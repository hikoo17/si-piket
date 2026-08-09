# ERD dan DFD Sistem Informasi Piket

Dokumen ini menggambarkan struktur data dan aliran data aplikasi berdasarkan implementasi saat ini (`as-is`). Diagram menggunakan Mermaid dan dapat dirender langsung oleh GitHub atau editor yang mendukung Mermaid.

## 1. Ruang Lingkup

Sistem Informasi Piket digunakan untuk:

- mengelola sekolah, kelas, pengguna, dan jadwal piket;
- mengirim pengingat piket melalui WhatsApp;
- menerima foto bukti piket beserta koordinat GPS;
- memvalidasi jadwal, waktu unggah, akurasi GPS, dan geofence sekolah;
- memverifikasi bukti piket;
- menandai ketidakhadiran secara otomatis; dan
- menampilkan serta mengekspor laporan piket.

### Aktor

| Aktor | Peran utama |
|---|---|
| Admin | Mengelola master data, jadwal, verifikasi, dan laporan |
| Guru | Memverifikasi bukti dan melihat laporan |
| Ketua kelas (`km`) | Mengelola jadwal kelas, mengunggah bukti, memverifikasi bukti kelas, dan melihat laporan |
| Siswa | Melihat jadwal, mengunggah bukti, dan menerima pengingat |
| Scheduler | Memicu pengingat dan penandaan ketidakhadiran |
| Queue worker | Memproses antrean pengiriman WhatsApp |
| Provider WhatsApp | Mengirim pesan pengingat ke pengguna |
| GPS dan kamera | Menyediakan lokasi, akurasi, dan foto bukti |

## 2. Entity Relationship Diagram

### 2.1 ERD Domain

```mermaid
erDiagram
    SCHOOLS ||--o{ CLASSES : memiliki
    CLASSES o|--o{ USERS : menaungi
    USERS ||--o{ PIKET_SCHEDULES : mendapat
    PIKET_SCHEDULES ||--o{ PIKET_LOGS : menghasilkan
    USERS ||--o{ PIKET_LOGS : melaksanakan
    USERS o|--o{ PIKET_LOGS : memverifikasi
    PIKET_LOGS ||--o{ PIKET_LOG_ATTEMPTS : memiliki
    USERS ||--o{ NOTIFICATION_LOGS : menerima
    PIKET_SCHEDULES ||--o{ NOTIFICATION_LOGS : memicu
    USERS o|--o{ AUDIT_LOGS : melakukan

    SCHOOLS {
        bigint id PK
        string name
        string address "nullable"
        string google_place_id "nullable"
        decimal latitude
        decimal longitude
        integer radius_meters "default 100"
        time upload_start_time "default 05:00"
        time upload_deadline "default 17:00"
        timestamp created_at
        timestamp updated_at
    }

    CLASSES {
        bigint id PK
        bigint school_id FK
        string name
        timestamp created_at
        timestamp updated_at
    }

    USERS {
        bigint id PK
        bigint class_id FK "nullable"
        string name
        string email UK
        string phone "nullable"
        enum role "admin, wali_kelas, km, siswa"
        timestamp email_verified_at "nullable"
        string password
        string remember_token "nullable"
        timestamp created_at
        timestamp updated_at
    }

    PIKET_SCHEDULES {
        bigint id PK
        bigint user_id FK
        enum day_of_week "Monday-Sunday"
        timestamp created_at
        timestamp updated_at
    }

    PIKET_LOGS {
        bigint id PK
        bigint schedule_id FK
        bigint user_id FK
        date date
        string photo_path "nullable"
        decimal latitude "nullable"
        decimal longitude "nullable"
        decimal accuracy_meters "nullable"
        integer distance_meters "nullable"
        timestamp location_captured_at "nullable"
        timestamp photo_captured_at "nullable"
        string ip_address "nullable"
        text user_agent "nullable"
        boolean wa_notif_sent "default false"
        enum status "pending, approved, rejected, absent"
        bigint verified_by FK "nullable"
        timestamp verified_at "nullable"
        string rejection_reason "nullable"
        timestamp created_at
        timestamp updated_at
    }

    PIKET_LOG_ATTEMPTS {
        bigint id PK
        bigint piket_log_id FK
        string photo_path "nullable"
        decimal latitude "nullable"
        decimal longitude "nullable"
        decimal accuracy_meters "nullable"
        integer distance_meters "nullable"
        string status
        string rejection_reason "nullable"
        timestamp submitted_at "nullable"
        timestamp created_at
        timestamp updated_at
    }

    NOTIFICATION_LOGS {
        bigint id PK
        bigint user_id FK
        bigint schedule_id FK
        date date
        string channel "default whatsapp"
        string phone
        text message
        string status "queued, sent, failed"
        string provider_message_id "nullable"
        timestamp sent_at "nullable"
        timestamp failed_at "nullable"
        text error_message "nullable"
        timestamp created_at
        timestamp updated_at
    }

    AUDIT_LOGS {
        bigint id PK
        bigint user_id FK "nullable"
        string action
        string auditable_type "nullable"
        bigint auditable_id "nullable"
        json metadata "nullable"
        string ip_address "nullable"
        text user_agent "nullable"
        timestamp created_at
        timestamp updated_at
    }
```

### 2.2 Kardinalitas dan Aturan Relasi

| Relasi | Kardinalitas | Aturan |
|---|---|---|
| `schools` ke `classes` | 1 : 0..N | Kelas wajib berada di satu sekolah; penghapusan sekolah menghapus kelas |
| `classes` ke `users` | 1 : 0..N | Kelas pengguna opsional pada database; penghapusan kelas membuat `class_id` bernilai `NULL` |
| `users` ke `piket_schedules` | 1 : 0..N | Satu pengguna dapat memiliki beberapa jadwal pada hari berbeda |
| `piket_schedules` ke `piket_logs` | 1 : 0..N | Jadwal berulang dapat menghasilkan log pada banyak tanggal |
| `users` ke `piket_logs` | 1 : 0..N | Setiap log memiliki satu pelaksana |
| `users` ke `piket_logs.verified_by` | 1 : 0..N | Verifikator bersifat opsional |
| `piket_logs` ke `piket_log_attempts` | 1 : 0..N | Satu log dapat memiliki beberapa percobaan unggah |
| `users` ke `notification_logs` | 1 : 0..N | Setiap notifikasi memiliki satu penerima |
| `piket_schedules` ke `notification_logs` | 1 : 0..N | Jadwal dapat memicu notifikasi pada tanggal berbeda |
| `users` ke `audit_logs` | 1 : 0..N | Aktor audit bersifat opsional setelah pengguna dihapus |

### 2.3 Unique Constraint

| Tabel | Constraint | Tujuan |
|---|---|---|
| `classes` | `UNIQUE(school_id, name)` | Nama kelas unik dalam satu sekolah |
| `users` | `UNIQUE(email)` | Email akun tidak boleh sama |
| `piket_schedules` | `UNIQUE(user_id, day_of_week)` | Pengguna hanya memiliki satu jadwal per hari |
| `piket_logs` | `UNIQUE(schedule_id, date)` | Satu jadwal hanya memiliki satu log per tanggal |
| `notification_logs` | `UNIQUE(user_id, schedule_id, date, channel)` | Mencegah notifikasi ganda |

### 2.4 Catatan ERD

- `audit_logs.auditable_type` dan `audit_logs.auditable_id` adalah referensi polymorphic logis, bukan foreign key database.
- `piket_logs.user_id` semestinya sama dengan pengguna pada `piket_schedules.user_id`, tetapi konsistensi ini tidak dijamin oleh constraint database.
- Tabel `piket_log_attempts` sudah tersedia, tetapi alur unggah saat ini masih memperbarui `piket_logs` secara langsung dan belum menyimpan riwayat ke tabel tersebut.
- Tabel infrastruktur Laravel seperti `sessions`, `cache`, `jobs`, dan `failed_jobs` tidak dimasukkan ke ERD domain agar diagram tetap fokus.

## 3. DFD Diagram Konteks

```mermaid
flowchart LR
    Admin[Admin]
    Guru[Guru]
    KM[Ketua Kelas]
    Siswa[Siswa]
    Scheduler[Scheduler]
    Worker[Queue Worker]
    WA[Provider WhatsApp]
    Device[GPS dan Kamera]
    Sistem((Sistem Informasi Piket))

    Admin -->|kredensial, master data, jadwal, verifikasi, filter| Sistem
    Sistem -->|status login, data master, dashboard, bukti, laporan| Admin

    Guru -->|kredensial, keputusan verifikasi, filter| Sistem
    Sistem -->|dashboard, daftar bukti, laporan| Guru

    KM -->|kredensial, jadwal kelas, bukti, verifikasi, filter| Sistem
    Sistem -->|jadwal, status bukti, bukti kelas, laporan| KM

    Siswa -->|kredensial, foto, koordinat, akurasi| Sistem
    Sistem -->|jadwal, hasil validasi, status bukti| Siswa

    Device -->|foto dan data lokasi| Sistem
    Scheduler -->|trigger waktu| Sistem
    Sistem -->|job notifikasi| Worker
    Worker -->|hasil pemrosesan| Sistem
    Sistem -->|nomor dan pesan| WA
    WA -->|ID pesan atau error| Sistem
```

## 4. DFD Level 0

### Data Store

| Kode | Data store | Implementasi |
|---|---|---|
| D1 | Pengguna | `users` |
| D2 | Sekolah dan konfigurasi geofence | `schools` |
| D3 | Kelas | `classes` |
| D4 | Jadwal piket | `piket_schedules` |
| D5 | Pelaksanaan dan verifikasi piket | `piket_logs` |
| D6 | Riwayat percobaan unggah | `piket_log_attempts` |
| D7 | Riwayat notifikasi | `notification_logs` |
| D8 | Audit aktivitas | `audit_logs` |
| D9 | Session autentikasi | `sessions` |
| D10 | Foto bukti | `storage/app/public/piket` |
| D11 | Antrean pekerjaan | `jobs`, `failed_jobs`, `job_batches` |

```mermaid
flowchart TB
    Admin[Admin]
    Guru[Guru]
    KM[Ketua Kelas]
    Siswa[Siswa]
    Scheduler[Scheduler]
    WA[Provider WhatsApp]
    Device[GPS dan Kamera]

    P1((1.0 Autentikasi dan Otorisasi))
    P2((2.0 Kelola Master Data))
    P3((3.0 Kelola Jadwal Piket))
    P4((4.0 Rekam dan Validasi Bukti))
    P5((5.0 Verifikasi Bukti))
    P6((6.0 Reminder dan Otomatisasi))
    P7((7.0 Dashboard dan Laporan))

    D1[(D1 Pengguna)]
    D2[(D2 Sekolah)]
    D3[(D3 Kelas)]
    D4[(D4 Jadwal Piket)]
    D5[(D5 Log Piket)]
    D6[(D6 Percobaan Unggah)]
    D7[(D7 Notifikasi)]
    D8[(D8 Audit)]
    D9[(D9 Session)]
    D10[(D10 Foto Bukti)]
    D11[(D11 Queue)]

    Admin -->|login| P1
    Guru -->|login| P1
    KM -->|login| P1
    Siswa -->|login| P1
    P1 <--> D1
    P1 <--> D9

    Admin -->|data sekolah, kelas, pengguna| P2
    P2 <--> D1
    P2 <--> D2
    P2 <--> D3
    P2 -->|hasil pengelolaan| Admin

    Admin -->|data jadwal| P3
    KM -->|jadwal kelas| P3
    P3 <--> D1
    P3 <--> D3
    P3 <--> D4
    P3 -->|daftar dan status jadwal| Admin
    P3 -->|jadwal kelas| KM

    Siswa -->|foto dan lokasi| P4
    KM -->|foto dan lokasi| P4
    Device -->|foto, koordinat, akurasi| P4
    D1 --> P4
    D2 --> P4
    D3 --> P4
    D4 --> P4
    P4 <--> D5
    P4 -. belum digunakan .-> D6
    P4 --> D8
    P4 <--> D10
    P4 -->|hasil validasi dan status| Siswa
    P4 -->|hasil validasi dan status| KM

    Admin -->|approve atau reject| P5
    Guru -->|approve atau reject| P5
    KM -->|verifikasi kelas| P5
    P5 <--> D5
    D1 --> P5
    P5 --> D8
    P5 -->|hasil verifikasi| Admin
    P5 -->|hasil verifikasi| Guru
    P5 -->|hasil verifikasi| KM

    Scheduler -->|trigger 06:00 dan 17:01| P6
    D1 --> P6
    D4 --> P6
    P6 <--> D5
    P6 <--> D7
    P6 <--> D11
    P6 -->|nomor dan pesan| WA
    WA -->|ID pesan atau error| P6

    Admin -->|filter dan ekspor| P7
    Guru -->|filter dan ekspor| P7
    KM -->|filter dan ekspor| P7
    D1 --> P7
    D3 --> P7
    D4 --> P7
    D5 --> P7
    P7 -->|dashboard, CSV, PDF| Admin
    P7 -->|dashboard, CSV, PDF| Guru
    P7 -->|dashboard, CSV, PDF| KM
```

## 5. DFD Level 1 - Unggah Bukti Piket

```mermaid
flowchart TB
    Pelaksana[Siswa atau Ketua Kelas]
    Device[GPS dan Kamera]

    P41((4.1 Validasi Payload dan Role))
    P42((4.2 Cari Sekolah dan Jadwal))
    P43((4.3 Validasi Waktu Unggah))
    P44((4.4 Periksa Log Hari Ini))
    P45((4.5 Hitung dan Validasi Geofence))
    P46((4.6 Validasi dan Simpan Foto))
    P47((4.7 Buat atau Perbarui Log))
    P48((4.8 Catat Audit dan Kirim Hasil))

    D1[(D1 Pengguna)]
    D2[(D2 Sekolah)]
    D3[(D3 Kelas)]
    D4[(D4 Jadwal Piket)]
    D5[(D5 Log Piket)]
    D8[(D8 Audit)]
    D10[(D10 Foto Bukti)]

    Pelaksana -->|foto, latitude, longitude, akurasi| P41
    Device -->|hasil capture| P41
    D1 -->|role dan class_id| P41
    P41 -->|payload valid| P42
    P41 -->|pesan kesalahan| Pelaksana

    D1 --> P42
    D3 --> P42
    D2 --> P42
    D4 --> P42
    P42 -->|sekolah dan jadwal ditemukan| P43
    P42 -->|tidak ada sekolah atau jadwal| Pelaksana

    D2 -->|waktu mulai dan deadline| P43
    P43 -->|masih dalam jendela waktu| P44
    P43 -->|di luar waktu unggah| Pelaksana

    D5 -->|log tanggal berjalan| P44
    P44 -->|baru atau dapat dikirim ulang| P45
    P44 -->|sudah disetujui| Pelaksana

    D2 -->|titik pusat dan radius| P45
    P45 -->|lokasi valid| P46
    P45 -->|penolakan geofence| D8
    P45 -->|di luar radius| Pelaksana

    P46 <--> D10
    P46 -->|path foto dan metadata| P47
    P46 -->|foto tidak valid| Pelaksana

    P47 <--> D5
    P47 -->|submission atau resubmission| P48
    P48 --> D8
    P48 -->|status pending dan pesan sukses| Pelaksana
```

### Aturan Proses Unggah

1. Hanya pengguna dengan role `siswa` atau `km` yang dapat mengunggah bukti.
2. Pengguna harus memiliki kelas, sekolah, dan jadwal pada hari berjalan.
3. Akurasi GPS maksimum adalah 300 meter.
4. Waktu unggah mengikuti `upload_start_time` dan `upload_deadline` sekolah.
5. Jarak dihitung dari koordinat pengguna ke titik sekolah menggunakan rumus Haversine.
6. Jarak harus berada dalam `radius_meters` sekolah.
7. Foto harus berupa JPEG, PNG, atau WebP dengan ukuran maksimum 5 MB.
8. Log yang sudah `approved` tidak dapat dikirim ulang.
9. Submission baru atau resubmission disimpan dengan status `pending`.

## 6. DFD Level 1 - Verifikasi Bukti

```mermaid
flowchart LR
    Verifikator[Admin, Guru, atau Ketua Kelas]
    P51((5.1 Ambil Daftar Log))
    P52((5.2 Otorisasi Objek))
    P53((5.3 Validasi Status))
    P54((5.4 Terapkan Keputusan))
    P55((5.5 Catat Audit))
    D1[(D1 Pengguna)]
    D5[(D5 Log Piket)]
    D8[(D8 Audit)]

    Verifikator -->|permintaan daftar| P51
    D5 --> P51
    P51 -->|daftar bukti| Verifikator
    Verifikator -->|ID log dan keputusan| P52
    D1 -->|role dan kelas| P52
    D5 -->|pemilik log| P52
    P52 -->|berhak memverifikasi| P53
    P52 -->|akses ditolak| Verifikator
    D5 -->|status log| P53
    P53 -->|status pending| P54
    P53 -->|status tidak valid| Verifikator
    P54 -->|approved atau rejected, verifier, waktu| D5
    P54 --> P55
    P55 --> D8
    P55 -->|hasil verifikasi| Verifikator
```

## 7. DFD Level 1 - Reminder dan Ketidakhadiran

```mermaid
flowchart TB
    Scheduler[Scheduler]
    Worker[Queue Worker]
    WA[Provider WhatsApp]
    P61((6.1 Pilih Jadwal Hari Ini))
    P62((6.2 Buat Notifikasi Idempoten))
    P63((6.3 Antrekan Job))
    P64((6.4 Kirim WhatsApp))
    P65((6.5 Simpan Hasil Pengiriman))
    P66((6.6 Periksa Bukti Setelah Deadline))
    P67((6.7 Buat Log Absent))
    D1[(D1 Pengguna)]
    D4[(D4 Jadwal Piket)]
    D5[(D5 Log Piket)]
    D7[(D7 Notifikasi)]
    D11[(D11 Queue)]

    Scheduler -->|trigger 06:00| P61
    D4 --> P61
    D1 --> P61
    P61 -->|jadwal dan penerima bernomor telepon| P62
    P62 <--> D7
    P62 -->|notifikasi baru atau gagal| P63
    P63 --> D11
    D11 -->|job| Worker
    Worker --> P64
    P64 -->|nomor dan pesan| WA
    WA -->|ID pesan atau error| P64
    P64 --> P65
    P65 -->|sent atau failed| D7

    Scheduler -->|trigger 17:01| P66
    D4 -->|jadwal hari ini| P66
    D5 -->|log tanggal berjalan| P66
    P66 -->|jadwal tanpa log| P67
    P67 -->|status absent| D5
```

### Aturan Otomatisasi

- Reminder dijalankan setiap pukul 06:00.
- Pengguna tanpa nomor telepon dilewati.
- Notifikasi dibuat secara idempoten untuk kombinasi pengguna, jadwal, tanggal, dan kanal.
- Pengiriman gagal dapat dicoba ulang oleh queue worker.
- Pemeriksaan ketidakhadiran dijalankan pukul 17:01.
- Status `absent` dibuat hanya jika belum ada log untuk jadwal dan tanggal tersebut.
- Log berstatus `pending` atau `rejected` tetap dianggap sudah melakukan submission sehingga tidak diubah menjadi `absent`.

## 8. Matriks Proses dan Hak Akses

| Proses | Admin | Guru | Ketua kelas | Siswa | Sistem |
|---|:---:|:---:|:---:|:---:|:---:|
| Login dan dashboard | Ya | Ya | Ya | Ya | - |
| Kelola sekolah | Ya | - | - | - | - |
| Kelola kelas | Ya | - | - | - | - |
| Kelola pengguna | Ya | - | - | - | - |
| Kelola jadwal | Ya | - | Ya, sesuai kelas | - | - |
| Unggah bukti | - | - | Ya | Ya | - |
| Verifikasi bukti | Ya | Ya | Ya, sesuai kelas | - | - |
| Lihat dan ekspor laporan | Ya | Ya | Ya | - | - |
| Kirim reminder | - | - | - | - | Ya |
| Tandai absent | - | - | - | - | Ya |

## 9. Catatan Implementasi As-Is

- Riwayat `piket_log_attempts` belum diisi oleh proses unggah atau kirim ulang.
- Pembatasan laporan berdasarkan kelas untuk role ketua kelas belum diterapkan secara otomatis pada query laporan.
- Pesan reminder menyebut deadline pukul 17:00 secara statis, sedangkan setiap sekolah mempunyai konfigurasi `upload_deadline`.
- Route unggah `POST /piket/upload` terdaftar lebih dari satu kali pada konfigurasi route saat ini.
- Relasi audit ke objek yang diaudit bersifat logis melalui `auditable_type` dan `auditable_id`.

## 10. Traceability

| Proses | Implementasi utama |
|---|---|
| Autentikasi | `app/Http/Controllers/AuthController.php` |
| Otorisasi role | `app/Http/Middleware/EnsureUserHasRole.php` |
| Master sekolah | `app/Http/Controllers/SchoolController.php` |
| Master kelas | `app/Http/Controllers/SchoolClassController.php` |
| Master pengguna | `app/Http/Controllers/UserController.php` |
| Jadwal piket | `app/Http/Controllers/ScheduleController.php` |
| Unggah dan geofence | `app/Http/Controllers/PiketController.php`, `app/Services/GeoService.php` |
| Verifikasi | `app/Http/Controllers/VerificationController.php` |
| Dashboard | `app/Http/Controllers/DashboardController.php` |
| Laporan | `app/Http/Controllers/ReportController.php` |
| Reminder | `app/Console/Commands/SendPiketReminders.php` |
| Pengiriman WhatsApp | `app/Jobs/SendPiketReminderJob.php`, `app/Services/WhatsAppService.php` |
| Penandaan absent | `app/Console/Commands/MarkPiketAbsent.php` |
| Jadwal otomatis | `bootstrap/app.php` |
| Route aplikasi | `routes/web.php` |
| Struktur database | `database/migrations` |
