<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') — Kantin Siswa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { gold: { DEFAULT: '#C8A96E', light: '#E2C98A', dark: '#A8893E' } },
                fontFamily: { display: ['"Cormorant Garamond"', 'serif'], body: ['"DM Sans"', 'sans-serif'] }
            }}
        }
    </script>
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        body { background: #080808; color: #F0EDE8; min-height: 100vh; }

        .auth-bg {
            background:
                radial-gradient(ellipse 60% 40% at 70% 50%, rgba(200,169,110,0.06) 0%, transparent 70%),
                radial-gradient(ellipse 40% 60% at 20% 80%, rgba(200,169,110,0.04) 0%, transparent 60%),
                #080808;
        }

        /* Animated grain overlay */
        .auth-bg::before {
            content: '';
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            opacity: 0.025;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            background-size: 128px;
        }

        .auth-card {
            background: #111; border: 1px solid #252525;
            border-radius: 16px; padding: 40px;
            position: relative; z-index: 1;
        }

        .ks-input {
            background: #1A1A1A; border: 1px solid #2A2A2A; color: #F0EDE8;
            border-radius: 8px; padding: 11px 14px; width: 100%;
            font-size: 14px; transition: border-color 0.2s; outline: none;
        }
        .ks-input:focus { border-color: #C8A96E; }
        .ks-input::placeholder { color: #444; }

        .btn-gold {
            background: linear-gradient(135deg, #C8A96E, #A8893E);
            color: #080808; font-weight: 600; border-radius: 8px;
            padding: 12px 20px; width: 100%; font-size: 15px;
            transition: all 0.2s; border: none; cursor: pointer;
            letter-spacing: 0.02em;
        }
        .btn-gold:hover { background: linear-gradient(135deg, #E2C98A, #C8A96E); transform: translateY(-1px); }

        .divider { display: flex; align-items: center; gap: 12px; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #252525; }

        @keyframes logoReveal {
            0%   { opacity:0; transform: scale(0.8) rotate(-5deg); }
            60%  { transform: scale(1.05) rotate(1deg); }
            100% { opacity:1; transform: scale(1) rotate(0deg); }
        }
        .logo-anim { animation: logoReveal 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) both; }

        @keyframes cardIn {
            from { opacity:0; transform: translateY(20px); }
            to   { opacity:1; transform: none; }
        }
        .card-in { animation: cardIn 0.5s ease 0.2s both; }
    </style>
</head>
<body class="auth-bg flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-4xl grid grid-cols-1 lg:grid-cols-2 gap-0 overflow-hidden rounded-2xl border border-[#222]">

        {{-- Left panel — branding --}}
        <div class="hidden lg:flex flex-col items-center justify-center p-12 relative"
             style="background: linear-gradient(145deg, #0D0D0D 0%, #151008 100%); border-right: 1px solid #252525;">

            {{-- Decorative rings --}}
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="w-72 h-72 rounded-full border border-gold/5"></div>
            </div>
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="w-48 h-48 rounded-full border border-gold/8"></div>
            </div>

            <div class="relative z-10 text-center">
                {{-- Logo --}}
                <div class="logo-anim w-24 h-24 rounded-full mx-auto mb-6 flex items-center justify-center"
                     style="background: radial-gradient(circle at 35% 35%, #E2C98A, #7A5520); box-shadow: 0 0 40px rgba(200,169,110,0.2);">
                    <span style="font-family:'Cormorant Garamond',serif; font-size:36px; font-weight:700; color:#080808;">KS</span>
                </div>

                <h1 style="font-family:'Cormorant Garamond',serif; font-size:36px; font-weight:700; color:#C8A96E; line-height:1.1;" class="mb-2">
                    Kantin Siswa
                </h1>
                <p class="text-gray-500 text-sm leading-relaxed max-w-52 mx-auto mt-3">
                    Platform pemesanan kantin sekolah yang modern dan efisien
                </p>

                <div class="mt-8 space-y-3 text-left">
                    @foreach(['Pesan makanan dengan mudah', 'Pembayaran tunai & QRIS', 'Lacak status pesanan realtime'] as $f)
                    <div class="flex items-center gap-3 text-sm text-gray-400">
                        <div class="w-5 h-5 rounded-full bg-gold/15 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-gold" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </div>
                        {{ $f }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right panel — form --}}
        <div class="bg-[#0E0E0E] p-10 flex flex-col justify-center card-in">
            {{-- Mobile logo --}}
            <div class="lg:hidden flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                     style="background: radial-gradient(circle, #E2C98A, #7A5520);">
                    <span style="font-family:'Cormorant Garamond',serif; font-weight:700; color:#080808;">KS</span>
                </div>
                <span style="font-family:'Cormorant Garamond',serif; font-size:22px; color:#C8A96E; font-weight:700;">Kantin Siswa</span>
            </div>

            @yield('form')
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
</body>
</html>
