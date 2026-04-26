@extends('layouts.app')
@section('title', 'Semua Pesanan')
@section('page-title', 'Semua Pesanan')

@section('content')
<div class="space-y-4">

    {{-- Stats summary --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="ks-card text-center">
            <p class="font-display text-gold text-xl font-bold">Rp {{ number_format($stats['today_sales'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Penjualan Hari Ini</p>
        </div>
        <div class="ks-card text-center">
            <p class="font-display text-blue-400 text-xl font-bold">{{ $stats['today_orders'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Pesanan Hari Ini</p>
        </div>
        <div class="ks-card text-center">
            <p class="font-display text-yellow-400 text-xl font-bold">{{ $stats['pending_orders'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Masih Pending</p>
        </div>
        <div class="ks-card text-center">
            <p class="font-display text-green-400 text-xl font-bold">Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Penjualan</p>
        </div>
    </div>

    <div class="ks-card overflow-hidden p-0">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-ks-border">
                    <th class="text-left px-5 py-3 text-xs text-gray-500 uppercase tracking-wide">No. Pesanan</th>
                    <th class="text-left px-5 py-3 text-xs text-gray-500 uppercase tracking-wide">Pemesan</th>
                    <th class="text-left px-5 py-3 text-xs text-gray-500 uppercase tracking-wide">Item</th>
                    <th class="text-right px-5 py-3 text-xs text-gray-500 uppercase tracking-wide">Total</th>
                    <th class="text-center px-5 py-3 text-xs text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="text-center px-5 py-3 text-xs text-gray-500 uppercase tracking-wide">Bayar</th>
                    <th class="text-left px-5 py-3 text-xs text-gray-500 uppercase tracking-wide">Waktu</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ks-border">
                @forelse($orders as $order)
                <tr class="hover:bg-ks-card/50 transition-colors">
                    <td class="px-5 py-3 font-mono text-xs text-gold">{{ $order->order_number }}</td>
                    <td class="px-5 py-3">
                        <p class="text-gray-200 font-medium">{{ $order->user->name }}</p>
                        @if($order->user->kelas)
                        <p class="text-xs text-gray-500">{{ $order->user->kelas }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-400 text-xs max-w-[180px]">
                        <div class="truncate">
                            {{ $order->items->map(fn($i) => $i->quantity.'× '.$i->item_name)->join(', ') }}
                        </div>
                    </td>
                    <td class="px-5 py-3 text-gold font-semibold text-right">{{ $order->formatted_total }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="badge-{{ $order->status }} text-xs px-2 py-0.5 rounded-full">{{ $order->status_label }}</span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if($order->payment_status === 'paid')
                        <span class="text-xs text-green-400">✓ Lunas</span>
                        @else
                        <span class="text-xs text-yellow-400">Belum</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-500 text-xs">{{ $order->created_at->format('d/m H:i') }}</td>
                    <td class="px-5 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('orders.show', $order) }}" class="text-gray-500 hover:text-gold text-xs">Detail</a>
                            <a href="{{ route('orders.receipt', $order) }}" target="_blank" class="text-gray-500 hover:text-gold text-xs">🖨️</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-gray-500">Belum ada pesanan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $orders->links() }}</div>
</div>
@endsection
