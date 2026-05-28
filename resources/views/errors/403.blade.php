<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 700: '#097946', 800: '#065f46' },
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans h-screen flex items-center justify-center">
    <div class="text-center">
        <div class="w-20 h-20 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v.01M12 9v2m0-6a9 9 0 110 18 9 9 0 010-18z"></path></svg>
        </div>
        <h1 class="text-6xl font-bold text-gray-900 mb-2">403</h1>
        <h2 class="text-xl font-bold text-gray-700 mb-2">Akses Ditolak</h2>
        <p class="text-gray-500 mb-8 max-w-md mx-auto">Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi admin jika Anda merasa ini kesalahan.</p>
        <a href="/" class="inline-flex items-center gap-2 bg-brand-700 text-white px-6 py-3 rounded-xl font-bold hover:bg-brand-800 transition-colors shadow-lg shadow-brand-700/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
