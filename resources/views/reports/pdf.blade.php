<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Piket</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 20px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .subtitle {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #f59e0b;
            color: #fff;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background: #fffbeb;
        }
    </style>
</head>
<body>
    <div class="title">Laporan Piket</div>
    <div class="subtitle">SMAN 1 Tasikmalaya</div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Siswa</th>
                <th>Kelas</th>
                <th>Jenis Piket</th>
                <th>Status</th>
                <th>Jarak</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->date->locale('id')->translatedFormat('j F Y') }}</td>
                    <td>{{ ($log->photo_captured_at ?? $log->created_at)?->format('H:i') ?? '-' }} WIB</td>
                    <td>{{ $log->user->name ?? '-' }}</td>
                    <td>{{ $log->user->schoolClass?->name ?? '-' }}</td>
                    <td>{{ $log->schedule?->shift_label ?? '-' }}</td>
                    <td>{{ ucfirst($log->status) }}</td>
                    <td>{{ $log->distance_meters !== null ? $log->distance_meters . ' m' : '-' }}</td>
                    <td>{{ $log->description ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
