@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        @foreach([
            ['Penjualan Hari Ini', 'Rp ' . number_format($stats['today_sales'], 0, ',', '.'), '💰', 'text-gold'],
            ['Pesanan Hari Ini',  $stats['today_orders'], '📦', 'text-blue-400'],
            ['Pesanan Pending',   $stats['pending_orders'], '⏳', 'text-yellow-400'],
            ['Total Siswa',       $stats['total_users'], '👤', 'text-purple-400'],
            ['Menu Aktif',        $stats['total_menus'], '🍽️', 'text-green-400'],
            ['Penjualan Bulan',  'Rp ' . number_format($stats['monthly_sales'], 0, ',', '.'), '📊', 'text-pink-400'],
        ] as [$label, $value, $icon, $color])
        <div class="ks-card text-center">
            <div class="text-2xl mb-1">{{ $icon }}</div>
            <p class="font-display font-bold text-xl {{ $color }}">{{ $value }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $label }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Recent orders --}}
        <div class="ks-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display text-gold text-xl">Pesanan Terbaru</h3>
                <a href="{{ route('admin.orders') }}" class="text-xs text-gray-500 hover:text-gold">Lihat semua →</a>
            </div>
            <div class="space-y-3">
                @forelse($recentOrders as $order)
                <div class="flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-mono text-xs text-gray-500">{{ $order->order_number }}</p>
                        <p class="text-sm text-gray-200 truncate">{{ $order->user->name }}</p>
                    </div>
                    <span class="badge-{{ $order->status }} text-xs px-2 py-0.5 rounded-full">{{ $order->status_label }}</span>
                    <p class="text-gold text-sm font-semibold flex-shrink-0">{{ $order->formatted_total }}</p>
                </div>
                @empty
                <p class="text-gray-500 text-sm text-center py-4">Belum ada pesanan</p>
                @endforelse
            </div>
        </div>

        {{-- Top menu items --}}
        <div class="ks-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display text-gold text-xl">Menu Terlaris</h3>
            </div>
            <div class="space-y-3">
                @forelse($topItems as $i => $item)
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-full bg-gold/10 flex items-center justify-center text-gold font-bold text-sm flex-shrink-0">
                        {{ $i + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-200 truncate">{{ $item->item_name }}</p>
                        <div class="w-full bg-ks-muted rounded-full h-1 mt-1">
                            <div class="bg-gold h-1 rounded-full"
                                 style="width: {{ min(100, ($item->total_qty / max(1, $topItems->max('total_qty'))) * 100) }}%"></div>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-gold font-semibold text-sm">{{ $item->total_qty }} terjual</p>
                        <p class="text-xs text-gray-500">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-sm text-center py-4">Belum ada data penjualan</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('admin.menu.create') }}" class="ks-card hover:border-gold/30 transition-all text-center py-5 cursor-pointer">
            <div class="text-3xl mb-2">➕</div>
            <p class="text-sm font-medium text-gray-300">Tambah Menu</p>
        </a>
        <a href="{{ route('admin.users.create') }}" class="ks-card hover:border-gold/30 transition-all text-center py-5 cursor-pointer">
            <div class="text-3xl mb-2">👤</div>
            <p class="text-sm font-medium text-gray-300">Tambah User</p>
        </a>
        <a href="{{ route('admin.orders') }}" class="ks-card hover:border-gold/30 transition-all text-center py-5 cursor-pointer">
            <div class="text-3xl mb-2">📋</div>
            <p class="text-sm font-medium text-gray-300">Semua Pesanan</p>
        </a>
        <a href="{{ route('cashier.orders') }}" class="ks-card hover:border-gold/30 transition-all text-center py-5 cursor-pointer">
            <div class="text-3xl mb-2">🏪</div>
            <p class="text-sm font-medium text-gray-300">Mode Kasir</p>
        </a>
    </div>
</div>
@endsection
