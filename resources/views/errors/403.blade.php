<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>403 — Akses Ditolak</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@700&family=DM+Sans&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #080808; color: #F0EDE8; font-family: 'DM Sans', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center">
    <div class="text-center p-8">
        <div class="text-8xl mb-6">🚫</div>
        <h1 style="font-family:'Cormorant Garamond',serif; font-size:48px; color:#C8A96E;" class="mb-2">403</h1>
        <h2 class="text-gray-300 text-xl font-semibold mb-2">Akses Ditolak</h2>
        <p class="text-gray-500 mb-8 max-w-sm mx-auto">{{ $exception->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}</p>
        <a href="{{ url()->previous() }}" style="background: linear-gradient(135deg, #C8A96E, #A8893E); color: #080808; font-weight: 600; border-radius: 8px; padding: 10px 24px; text-decoration: none;">
            ← Kembali
        </a>
    </div>
</body>
</html>
