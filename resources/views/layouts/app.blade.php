<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kantin Siswa') — KS POS</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    {{-- Tailwind via CDN (replace with compiled in production) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold:  { DEFAULT: '#C8A96E', light: '#E2C98A', dark: '#A8893E', pale: '#F5EDD8' },
                        ks:    { DEFAULT: '#0A0A0A', surface: '#141414', card: '#1C1C1C', border: '#2A2A2A', muted: '#3A3A3A' },
                    },
                    fontFamily: {
                        display: ['"Cormorant Garamond"', 'serif'],
                        body:    ['"DM Sans"', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        * { font-family: 'DM Sans', sans-serif; }
        h1,h2,.font-display { font-family: 'Cormorant Garamond', serif; }

        body { background: #0A0A0A; color: #F0EDE8; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: #141414; }
        ::-webkit-scrollbar-thumb { background: #C8A96E; border-radius: 2px; }

        /* Sidebar */
        .sidebar { background: #0F0F0F; border-right: 1px solid #2A2A2A; }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 16px; border-radius: 8px;
            color: #9A9A9A; font-size: 14px; font-weight: 500;
            transition: all 0.2s; text-decoration: none;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: #1C1C1C; color: #C8A96E;
        }
        .sidebar-link.active { border-left: 3px solid #C8A96E; padding-left: 13px; }

        /* Cards */
        .ks-card {
            background: #141414; border: 1px solid #2A2A2A;
            border-radius: 12px; padding: 20px;
        }

        /* Buttons */
        .btn-gold {
            background: linear-gradient(135deg, #C8A96E, #A8893E);
            color: #0A0A0A; font-weight: 600; border-radius: 8px;
            padding: 10px 20px; transition: all 0.2s; border: none; cursor: pointer;
        }
        .btn-gold:hover { background: linear-gradient(135deg, #E2C98A, #C8A96E); transform: translateY(-1px); }
        .btn-outline {
            border: 1px solid #2A2A2A; color: #C8A96E; background: transparent;
            border-radius: 8px; padding: 10px 20px; font-weight: 500;
            transition: all 0.2s; cursor: pointer;
        }
        .btn-outline:hover { border-color: #C8A96E; background: #1C1C1C; }

        /* Inputs */
        .ks-input {
            background: #1C1C1C; border: 1px solid #2A2A2A; color: #F0EDE8;
            border-radius: 8px; padding: 10px 14px; width: 100%;
            font-size: 14px; transition: border-color 0.2s; outline: none;
        }
        .ks-input:focus { border-color: #C8A96E; }
        .ks-input::placeholder { color: #555; }

        /* Status badges */
        .badge-pending    { background: #2D2500; color: #F59E0B; border: 1px solid #78450020; }
        .badge-processing { background: #0D1F3C; color: #60A5FA; border: 1px solid #1E40AF20; }
        .badge-ready      { background: #052E16; color: #34D399; border: 1px solid #06673520; }
        .badge-completed  { background: #1C1C1C; color: #9CA3AF; border: 1px solid #37415120; }
        .badge-cancelled  { background: #2D0000; color: #F87171; border: 1px solid #7F1D1D20; }

        /* Animations */
        @keyframes fadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:none; } }
        .fade-in { animation: fadeIn 0.3s ease forwards; }
        @keyframes slideIn { from { transform:translateX(-10px); opacity:0; } to { transform:none; opacity:1; } }
        .slide-in { animation: slideIn 0.25s ease forwards; }

        /* Toast */
        #toast-container { position:fixed; bottom:24px; right:24px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
        .toast { background:#1C1C1C; border:1px solid #2A2A2A; border-left:3px solid #C8A96E;
            padding:12px 18px; border-radius:8px; font-size:13px; min-width:240px;
            animation: fadeIn 0.3s ease; }

        /* Print only */
        @media print { .no-print { display:none !important; } }
    </style>

    @stack('styles')
</head>
<body class="h-full flex" x-data="{ sidebarOpen: true }">

    {{-- ── Sidebar ── --}}
    @auth
    <aside class="sidebar no-print w-56 flex-shrink-0 flex flex-col h-screen sticky top-0"
           :class="sidebarOpen ? 'w-56' : 'w-16'"
           style="transition: width 0.25s ease;">

        {{-- Logo --}}
        <div class="flex items-center gap-3 p-5 border-b border-ks-border">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gold to-gold-dark flex items-center justify-center flex-shrink-0">
                <span class="font-display font-bold text-ks text-sm">KS</span>
            </div>
            <div x-show="sidebarOpen" class="overflow-hidden">
                <div class="font-display text-gold font-bold text-lg leading-none">Kantin</div>
                <div class="text-xs text-gray-500 tracking-widest uppercase">Siswa</div>
            </div>
        </div>

        {{-- Role badge --}}
        <div class="px-4 pt-4 pb-2" x-show="sidebarOpen">
            <div class="text-xs px-2 py-1 rounded-full inline-block
                @if(auth()->user()->isAdmin()) bg-red-900/40 text-red-300
                @elseif(auth()->user()->isKasir()) bg-blue-900/40 text-blue-300
                @else bg-gold/10 text-gold @endif">
                {{ ucfirst(auth()->user()->role) }}
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-2 space-y-1 overflow-y-auto">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span x-show="sidebarOpen">Dashboard</span>
                </a>
                <a href="{{ route('admin.orders') }}" class="sidebar-link {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span x-show="sidebarOpen">Semua Pesanan</span>
                </a>
                <a href="{{ route('admin.menu.index') }}" class="sidebar-link {{ request()->routeIs('admin.menu*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span x-show="sidebarOpen">Kelola Menu</span>
                </a>
                <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                    <span x-show="sidebarOpen">Manajemen User</span>
                </a>
            @elseif(auth()->user()->isKasir())
                <a href="{{ route('cashier.orders') }}" class="sidebar-link {{ request()->routeIs('cashier.orders') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span x-show="sidebarOpen">Antrian Pesanan</span>
                </a>
            @else
                <a href="{{ route('home') }}" class="sidebar-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span x-show="sidebarOpen">Menu</span>
                </a>
                <a href="{{ route('cart') }}" class="sidebar-link {{ request()->routeIs('cart') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span x-show="sidebarOpen">Keranjang</span>
                    @php $cartCount = \App\Models\CartItem::where('user_id', auth()->id())->sum('quantity') @endphp
                    @if($cartCount > 0)
                        <span class="ml-auto bg-gold text-ks text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold">{{ $cartCount }}</span>
                    @endif
                </a>
                <a href="{{ route('orders.my') }}" class="sidebar-link {{ request()->routeIs('orders.my') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-show="sidebarOpen">Pesanan Saya</span>
                </a>
            @endif

            {{-- Common --}}
            <div class="border-t border-ks-border my-2"></div>
            <a href="{{ route('profile') }}" class="sidebar-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                <img src="{{ auth()->user()->avatar_url }}" class="w-4 h-4 rounded-full flex-shrink-0" alt="">
                <span x-show="sidebarOpen">Profil</span>
            </a>
        </nav>

        {{-- Collapse toggle --}}
        <div class="p-3 border-t border-ks-border">
            <button @click="sidebarOpen = !sidebarOpen" class="sidebar-link w-full justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="sidebarOpen"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!sidebarOpen"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
            </button>
        </div>

        {{-- Logout --}}
        <div class="p-3 border-t border-ks-border" x-show="sidebarOpen">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link w-full text-red-400 hover:text-red-300 hover:bg-red-900/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>
    @endauth

    {{-- ── Main Content ── --}}
    <div class="flex-1 flex flex-col min-h-screen overflow-hidden">

        {{-- Top bar --}}
        @auth
        <header class="no-print h-14 border-b border-ks-border flex items-center justify-between px-6 bg-ks-surface sticky top-0 z-40">
            <div class="flex items-center gap-3">
                <h1 class="font-display text-gold text-xl font-semibold">@yield('page-title', 'Kantin Siswa')</h1>
            </div>
            <div class="flex items-center gap-4 text-sm text-gray-400">
                <span id="clock" class="font-mono text-xs"></span>
                <div class="flex items-center gap-2">
                    <img src="{{ auth()->user()->avatar_url }}" class="w-7 h-7 rounded-full border border-ks-border" alt="">
                    <span class="text-gray-300">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </header>
        @endauth

        {{-- Flash messages --}}
        @if(session('success') || session('error'))
        <div class="no-print px-6 pt-4">
            @if(session('success'))
                <div class="flex items-center gap-3 p-3 bg-green-900/30 border border-green-700/40 rounded-lg text-green-300 text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 p-3 bg-red-900/30 border border-red-700/40 rounded-lg text-red-300 text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    {{ session('error') }}
                </div>
            @endif
        </div>
        @endif

        <main class="flex-1 p-6 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    {{-- Toast container --}}
    <div id="toast-container"></div>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        // Clock
        function updateClock() {
            const el = document.getElementById('clock');
            if (el) el.textContent = new Date().toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit',second:'2-digit'});
        }
        updateClock();
        setInterval(updateClock, 1000);

        // Toast helper
        function showToast(msg, type = 'success') {
            const c = document.getElementById('toast-container');
            const t = document.createElement('div');
            t.className = 'toast';
            t.style.borderLeftColor = type === 'success' ? '#C8A96E' : '#F87171';
            t.textContent = msg;
            c.appendChild(t);
            setTimeout(() => t.remove(), 3500);
        }

        // CSRF helper for fetch
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        function apiFetch(url, options = {}) {
            return fetch(url, {
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', ...options.headers },
                ...options
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
