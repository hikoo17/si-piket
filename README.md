# SI-PIKET

Sistem Informasi Piket - Aplikasi presensi dan manajemen piket kelas berbasis web untuk SMAN 1 Tasikmalaya.

## Tech Stack

### Backend
- **Laravel 13** — Framework PHP (minimal PHP 8.3)
- **MySQL** — Database (via Laragon)
- **barryvdh/laravel-dompdf** — Export laporan PDF

### Frontend
- **Vite 8** — Build tool & dev server
- **Tailwind CSS 4** — Utility CSS framework
- **Blade Templates** — Server-side rendering (Laravel)
- **blade-ui-kit/blade-heroicons** — Komponen ikon Heroicons di Blade

### JavaScript Libraries
- **Leaflet 1.9.4** — Peta interaktif untuk konfirmasi lokasi sekolah
- **Lucide 1.30.0** — Ikon SVG di halaman login
- **SweetAlert2 11.26.25** — Notifikasi toast/alert yang menggantikan `alert()` bawaan

### Dev Tools
- **Laravel Pint** — Code style fixer
- **Laravel Pail** — Log viewer saat development
- **Laravel PAO** — Performance monitoring

---

## Arsitektur & Alur Project

### Role-Based Access Control

Aplikasi menggunakan 4 role pengguna:

| Role | Akses |
|------|-------|
| **admin** | Dashboard, Manajemen Pengguna, Manajemen Kelas, Pengaturan Sekolah, Manajemen Jadwal, Verifikasi, Laporan |
| **wali_kelas** | Verifikasi bukti piket, Laporan |
| **km** (Ketua Kelas) | Upload bukti piket, Manajemen Jadwal, Verifikasi, Laporan |
| **siswa** | Upload bukti piket |

### Alur Presensi Piket

```
1. Admin/KM membuat jadwal piket (pagi & pulang) untuk setiap siswa
2. Siswa/KM login → buka "Ambil Bukti" → upload foto + otomatis capture koordinat GPS
3. Sistem validasi:
   - Jarak dari lokasi sekolah (radius default 100m)
   - Waktu upload sesuai jadwal
4. Wali Kelas/Admin/KM verifikasi bukti piket
5. Laporan dapat dicetak PDF atau diexport CSV
```

### Struktur Folder

```
si-piket/
├── app/
│   ├── Http/Controllers/    # Controller aplikasi
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── PiketController.php
│   │   ├── ReportController.php
│   │   ├── ScheduleController.php
│   │   ├── SchoolController.php
│   │   ├── SchoolClassController.php
│   │   ├── StudentController.php
│   │   ├── UserController.php
│   │   └── VerificationController.php
│   └── Models/              # Model Eloquent
├── database/
│   ├── migrations/          # Skema tabel
│   └── seeders/             # Data awal (admin, wali kelas, siswa, jadwal)
├── resources/
│   ├── css/app.css          # Tailwind + Leaflet CSS
│   ├── js/
│   │   ├── app.js           # Logic global (map, SweetAlert, sidebar)
│   │   └── login.js         # Logic halaman login (toggle password, Lucide icons)
│   └── views/               # Template Blade
│       ├── layouts/app.blade.php   # Layout utama (sidebar + header)
│       ├── auth/login.blade.php    # Halaman login
│       ├── dashboard.blade.php     # Dashboard statistik
│       ├── schedules/              # Manajemen jadwal piket
│       ├── schools/                 # Pengaturan sekolah + peta Leaflet
│       ├── students/                # Manajemen siswa
│       └── ...
├── routes/web.php           # Definisi route + middleware role
├── vite.config.js           # Konfigurasi Vite (entry: app.css + app.js + login.js)
└── .env                     # Konfigurasi environment
```

### Alur Request

1. **Guest** → `/login` (halaman login dengan Lucide icons + toggle password)
2. **Auth** → Redirect ke `/dashboard` (statistik: piket hari ini, pending verifikasi, ringkasan per role)
3. **Siswa/KM** → Upload bukti piket via `PiketController` (form dengan capture foto + GPS)
4. **Verifikator** → `VerificationController` approve/reject bukti piket
5. **Admin** → `SchoolController` edit koordinat sekolah + radius pada peta Leaflet
6. **Admin/KM** → `ScheduleController` kelola jadwal pagi & pulang
7. **All auth** → `ReportController` lihat laporan + export PDF/CSV

### Asset Pipeline

```
resources/js/app.js      → public/build/assets/app-[hash].js
resources/js/login.js    → public/build/assets/login-[hash].js
resources/css/app.css    → public/build/assets/app-[hash].css
         ↓
    vite.config.js (Laravel Vite Plugin)
         ↓
    public/build/manifest.json
         ↓
    @vite() directive di Blade templates
```

- **Development**: `npm run dev` → Vite dev server (HMR) di `http://127.0.0.1:5173`
- **Production**: `npm run build` → Compile + minify + hash + simpan di `public/build/`

---

## Fitur

- Manajemen pengguna (Administrator, Wali Kelas, Ketua Kelas, Siswa)
- Manajemen kelas
- Jadwal piket pagi & pulang
- Upload bukti piket dengan verifikasi lokasi
- Verifikasi bukti piket oleh wali kelas
- Laporan piket
- Pengingat otomatis WhatsApp
- Peta lokasi sekolah

## Persiapan Software

Sebelum menginstall aplikasi, pastikan 3 software berikut sudah terinstall:

1. **Laragon** (include Apache, MySQL, PHP)
2. **Composer** (untuk library PHP)
3. **Node.js** (untuk assets/frontend)

---

### Langkah 1 — Install Laragon

Laragon adalah software yang menyediakan Apache (web server), MySQL (database), dan PHP dalam satu paket.

1. Download Laragon di: https://sourceforge.net/projects/laragon/files/releases/laragon-wamp.exe/download
2. Klik **Download** dan tunggu sampai file terunduh
3. Buka file installer yang sudah terunduh
4. Klik **Next**, lalu pilih folder instalasi (default: `C:\laragon`)
5. Klik **Next**, lalu centang semua opsi yang tersedia
6. Klik **Install** dan tunggu sampai instalasi selesai
7. Setelah selesai, buka **Laragon** dari Start Menu
8. Klik tombol **Start All** di pojok kiri bawah
9. Pastikan status **Apache** dan **MySQL** berwarna **hijau**

![Laragon Start All](https://i.imgur.com/8JcVQzH.png)

---

### Langkah 2 — Install Composer

Composer adalah tool untuk menginstall library PHP yang dibutuhkan aplikasi.

1. Download Composer di: https://getcomposer.org/Composer-Setup.exe
2. Buka file installer yang sudah terunduh
3. Klik **Next**, lalu centang opsi **"Add this directory to your PATH"**
4. Klik **Next**, lalu klik **Install**
5. Tunggu sampai instalasi selesai, lalu klik **Finish**

Setelah instalasi, buka **Command Prompt** (CMD) dan ketik:

```bash
composer --version
```

Jika muncul tulisan versi Composer (misal: `Composer version 2.x.x`), berarti Composer berhasil diinstall.

---

### Langkah 3 — Install Node.js

Node.js dibutuhkan untuk menginstall assets/frontend aplikasi (CSS, JavaScript).

1. Download Node.js di: https://nodejs.org/en/download (pilih versi **LTS**)
2. Buka file installer yang sudah terunduh
3. Klik **Next**, lalu **Next** lagi
4. Klik **I Agree** untuk menyetujui license
5. Klik **Next**, lalu **Next** lagi (gunakan default instalasi)
6. Klik **Install** dan tunggu sampai selesai
7. Klik **Finish**

Setelah instalasi, buka **Command Prompt** (CMD) dan ketik:

```bash
node --version
npm --version
```

Jika muncul tulisan versi (misal: `v20.x.x` dan `10.x.x`), berarti Node.js berhasil diinstall.

---

## Instalasi Aplikasi

### Langkah 4 — Salin Folder Proyek

Salin folder `si-piket` ke lokasi yang diinginkan, misalnya:
- `D:\Projects\si-piket`
- `C:\Users\NamaAnda\Documents\si-piket`

### Langkah 5 — Buka Terminal di Folder Proyek

1. Buka folder `si-piket`
2. Klik pada address bar (tempat lokasi folder ditampilkan)
3. Ketik `cmd` lalu tekan **Enter**
4. Sebuah jendela terminal akan muncul

![Open Terminal di Folder](https://i.imgur.com/5YzJvQK.png)

### Langkah 6 — Install Dependensi PHP

Di terminal yang sudah dibuka, ketik:

```bash
composer install
```

Tunggu sampai proses selesai. Proses ini akan mengunduh dan menginstall library-library PHP yang dibutuhkan.

### Langkah 7 — Setup File Konfigurasi

Ketik perintah berikut di terminal:

```bash
copy .env.example .env
php artisan key:generate
```

Perintah ini akan:
- Menyalin file contoh konfigurasi menjadi file konfigurasi asli
- Membuat kunci aplikasi yang dibutuhkan untuk berjalan

### Langkah 8 — Buat Database

1. Buka browser (Chrome/Firefox/Edge)
2. Ketik di address bar: http://localhost/phpmyadmin
3. Klik tab **Databases**
4. Di kolom **Create database**, ketik: `si_piket`
5. Klik tombol **Create**

![Create Database](https://i.imgur.com/3kLmZ9P.png)

### Langkah 9 — Atur Koneksi Database

1. Buka folder `si-piket`
2. Cari file bernama `.env`
3. Klik kanan pada file `.env`, pilih **Open with** → **Notepad**
4. Cari baris-baris berikut:

```env
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

5. Ubah menjadi:

```env
DB_DATABASE=si_piket
DB_USERNAME=root
DB_PASSWORD=
```

6. Klik **File** → **Save** untuk menyimpan perubahan
7. Tutup Notepad

### Langkah 10 — Jalankan Migrasi & Seeder

Ketik perintah berikut di terminal:

```bash
php artisan migrate --seed
```

Perintah ini akan:
- Membuat tabel-tabel di database
- Mengisi data awal (admin, wali kelas, siswa, kelas, jadwal)

Tunggu sampai proses selesai.

### Langkah 11 — Build Assets

Ketik perintah berikut di terminal:

```bash
npm install
npm run build
```

Perintah ini akan menginstall dan mengcompile assets (CSS, JavaScript) aplikasi.

---

## Menjalankan Aplikasi

### Langkah 12 — Pastikan Laragon Berjalan

1. Buka **Laragon**
2. Klik **Start All**
3. Pastikan **Apache** dan **MySQL** berwarna **hijau**

### Langkah 13 — Jalankan Server Aplikasi

Di terminal yang sudah dibuka sebelumnya, ketik:

```bash
php artisan serve
```

Tunggu sampai muncul tulisan:

```
Server running on [http://127.0.0.1:8000]
```

### Langkah 14 — Buka di Browser

1. Buka browser (Chrome/Firefox/Edge)
2. Ketik di address bar: http://localhost:8000
3. Aplikasi SI-PIKET akan terbuka

---

## Login

Setelah aplikasi terbuka, login menggunakan akun berikut:

| Email | Password | Role |
|-------|----------|------|
| admin@si-piket.test | password | Administrator |
| wali.kelas@si-piket.test | password | Wali Kelas XII-4 |
| km@si-piket.test | password | Ketua Kelas XII-4 |
| siswa@si-piket.test | password | Siswa XII-4 |

> Password untuk semua akun: **password**

---

## Menjalankan Aplikasi Setiap Hari

Setiap kali ingin membuka aplikasi, ikuti langkah berikut:

1. Buka **Laragon** → klik **Start All**
2. Buka folder `si-piket` → klik address bar → ketik `cmd` → tekan **Enter**
3. Buka **3 terminal** pada folder project yang sama.
4. Pada terminal pertama, jalankan server aplikasi:
   ```bash
   php artisan serve
   ```
5. Pada terminal kedua, jalankan pemeriksa jadwal otomatis:
   ```bash
   php artisan schedule:work
   ```
6. Pada terminal ketiga, jalankan proses antrean pengiriman WhatsApp:
   ```bash
   php artisan queue:work --queue=notifications
   ```
7. Buka browser ke: http://localhost:8000

> `schedule:work` diperlukan untuk memeriksa jadwal pengiriman setiap menit. `queue:work` diperlukan agar pesan yang sudah masuk antrean benar-benar dikirim melalui WhatsApp.

### Zona Waktu Pengiriman

Pengiriman otomatis menggunakan zona waktu Indonesia Barat (WIB/Asia Jakarta), sesuai pengaturan aplikasi. Jadi, jika jam pengiriman diatur ke `20:11`, sistem akan menjalankannya pada pukul 20:11 WIB.

Pastikan pengaturan berikut tersedia di file `.env`:

```env
APP_TIMEZONE=Asia/Jakarta
```

Jika baru mengubah file `.env`, jalankan:

```bash
php artisan config:clear
php artisan cache:clear
```

### Tes Pengiriman WhatsApp Manual

Untuk menguji pengingat tanpa menunggu jam yang telah diatur, jalankan:

```bash
php artisan piket:send-reminders --date=YYYY-MM-DD
```

Contoh:

```bash
php artisan piket:send-reminders --date=2026-08-09
```

Setelah itu, pastikan terminal `queue:work` tetap berjalan agar pesan dalam antrean dapat dikirim.

Untuk **menutup aplikasi**:
- Tekan **Ctrl+C** di terminal
- Tutup jendela terminal

---

## Troubleshooting

### Port 8000 sudah digunakan

Jika muncul error "Address already in use", jalankan perintah:

```bash
php artisan serve --port=8001
```

Kemudian buka browser ke: http://localhost:8001

### Error cache

Jika aplikasi tidak berjalan normal, jalankan perintah berikut di terminal:

```bash
php artisan view:clear
php artisan config:clear
php artisan cache:clear
```

### Database error

Jika terjadi error koneksi database, pastikan:
1. Laragon sudah berjalan (Apache + MySQL hijau)
2. Database `si_piket` sudah dibuat di phpMyAdmin
3. File `.env` sudah diubah dengan benar

### Reset database

Jika ingin mereset database (menghapus semua data dan membuat ulang):

```bash
php artisan migrate:fresh --seed
```

---

## Struktur Folder

```
si-piket/
├── app/
│   ├── Http/Controllers/    # Controller aplikasi
│   └── Models/              # Model Eloquent
├── database/
│   ├── migrations/          # File migrasi database
│   └── seeders/             # Data awal (seeder)
├── resources/
│   └── views/               # Template Blade
├── routes/
│   └── web.php              # Definisi route
├── .env                     # Konfigurasi aplikasi
└── artisan                  # CLI Laravel
```

## License

Proyek ini adalah aplikasi proprietary untuk SMAN 1 Tasikmalaya.
