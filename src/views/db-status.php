<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Status Koneksi Database</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            background: #f3f4f6;
            color: #111827;
            font: 16px/1.5 system-ui, sans-serif;
        }
        main {
            width: min(100%, 440px);
            padding: 32px;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 12px 32px #11182714;
        }
        h1 { margin: 0 0 8px; font-size: 1.5rem; }
        p { margin: 8px 0; color: #4b5563; }
        .status {
            margin: 24px 0 16px;
            padding: 16px;
            border-radius: 10px;
            font-weight: 700;
        }
        .connected { background: #dcfce7; color: #166534; }
        .disconnected { background: #fee2e2; color: #991b1b; }
        a { color: #2563eb; }
    </style>
</head>
<body>
<main>
    <h1>Status Koneksi Database</h1>
    <p>Halaman ini memeriksa koneksi aplikasi ke database.</p>
    <div class="status <?= $connected ? 'connected' : 'disconnected' ?>">
        <?= $connected ? '✓ Koneksi database berhasil' : '✕ Koneksi database gagal' ?>
    </div>
    <p>Pemeriksaan terakhir: <?= htmlspecialchars($checkedAt, ENT_QUOTES, 'UTF-8') ?></p>
    <p><a href="/db-status">Periksa ulang</a></p>
</main>
</body>
</html>
