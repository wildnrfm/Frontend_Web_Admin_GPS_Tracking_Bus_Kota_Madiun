<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Analitik - {{ $date }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #1a73e8;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f0f0f0;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #ddd;
            font-weight: bold;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .section {
            page-break-inside: avoid;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1a73e8;
            margin: 15px 0 10px 0;
            border-left: 4px solid #1a73e8;
            padding-left: 10px;
        }
        .footer {
            text-align: center;
            font-size: 11px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Analitik</h1>
        <p>Tanggal: <strong>{{ $date }}</strong></p>
        <p>Admin: <strong>{{ $user['name'] ?? 'Administrator' }}</strong></p>
    </div>

    <div class="section">
        <div class="section-title">Ringkasan Data</div>
        <p style="color: #666; font-size: 12px;">
            Ringkasan data analitik untuk tanggal {{ $date }} akan ditampilkan di sini.
        </p>
        <table>
            <tr>
                <th>Metrik</th>
                <th>Nilai</th>
            </tr>
            <tr>
                <td>Total Bus</td>
                <td>-</td>
            </tr>
            <tr>
                <td>Total Siswa</td>
                <td>-</td>
            </tr>
            <tr>
                <td>Total Driver</td>
                <td>-</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Dokumen ini digenerate otomatis oleh Sistem GPS Tracking Bus Kota Madiun</p>
        <p>Waktu: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
