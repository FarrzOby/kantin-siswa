@extends('layouts.app')
@section('title', 'Pesanan ' . $order->order_number)
@section('page-title', 'Detail Pesanan')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">

    {{-- Status card --}}
    <div class="ks-card">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Nomor Pesanan</p>
                <h2 class="font-display text-gold text-2xl font-bold">{{ $order->order_number }}</h2>
                <p class="text-xs text-gray-500 mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>

            <div class="text-right">
                <span class="badge-{{ $order->status }} px-3 py-1.5 rounded-full text-sm font-medium">
                    {{ $order->status_label }}
                </span>
                <p class="text-xs text-gray-500 mt-2">
                    @if($order->payment_status === 'paid')
                        <span class="text-green-400">✓ Lunas</span>
                    @else
                        <span class="text-yellow-400">⏳ Belum dibayar</span>
                    @endif
                    · {{ $order->payment_method === 'qris' ? 'QRIS' : 'Tunai' }}
                </p>
            </div>
        </div>

        {{-- Progress steps --}}
        <div class="mt-6 flex items-center gap-0">
            @php
                $steps = ['pending' => 'Menunggu', 'processing' => 'Diproses', 'ready' => 'Siap', 'completed' => 'Selesai'];
                $stepKeys = array_keys($steps);
                $currentIdx = array_search($order->status, $stepKeys) ?? 0;
            @endphp
            @foreach($steps as $key => $label)
                @php $idx = array_search($key, $stepKeys); @endphp
                <div class="flex items-center flex-1">
                    <div class="flex flex-col items-center">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                            {{ $idx <= $currentIdx ? 'bg-gold text-ks' : 'bg-ks-muted text-gray-500' }}">
                            {{ $idx < $currentIdx ? '✓' : ($idx + 1) }}
                        </div>
                        <p class="text-xs mt-1 {{ $idx <= $currentIdx ? 'text-gold' : 'text-gray-600' }}">{{ $label }}</p>
                    </div>
                    @if(!$loop->last)
                    <div class="flex-1 h-0.5 mx-1 {{ $idx < $currentIdx ? 'bg-gold' : 'bg-ks-border' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Items --}}
    <div class="ks-card">
        <h3 class="font-display text-gold text-lg mb-4">Item Pesanan</h3>
        <div class="space-y-3">
            @foreach($order->items as $item)
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-ks-muted flex items-center justify-center text-xl flex-shrink-0">🍽️</div>
                    <div>
                        <p class="text-sm font-medium text-gray-200">{{ $item->item_name }}</p>
                        <p class="text-xs text-gray-500">{{ $item->quantity }}× Rp {{ number_format($item->item_price, 0, ',', '.') }}</p>
                        @if($item->notes)
                        <p class="text-xs text-gold italic">📝 {{ $item->notes }}</p>
                        @endif
                    </div>
                </div>
                <p class="text-gold font-semibold text-sm">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>

        <div class="border-t border-ks-border mt-4 pt-4 space-y-1">
            <div class="flex justify-between text-sm text-gray-400">
                <span>Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($order->tax > 0)
            <div class="flex justify-between text-sm text-gray-400">
                <span>Pajak</span><span>Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between font-bold text-base pt-1">
                <span class="text-gray-200">Total</span>
                <span class="text-gold font-display text-xl">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
            @if($order->amount_paid)
            <div class="flex justify-between text-sm text-gray-400">
                <span>Bayar</span><span>Rp {{ number_format($order->amount_paid, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm text-green-400">
                <span>Kembalian</span><span>Rp {{ number_format($order->change_amount, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Notes --}}
    @if($order->notes)
    <div class="ks-card">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Catatan</p>
        <p class="text-gray-300 text-sm">{{ $order->notes }}</p>
    </div>
    @endif

    {{-- Actions --}}
    <div class="flex gap-3 flex-wrap">
        @if($order->status === 'completed' || $order->payment_status === 'paid')
        <a href="{{ route('orders.receipt', $order) }}" target="_blank"
           class="btn-gold flex items-center gap-2">
            🖨️ Cetak Struk
        </a>
        @endif

        @if(auth()->user()->canProcessOrder() && $order->payment_method === 'qris' && $order->payment_status === 'unpaid')
        <a href="{{ route('cashier.qris', $order) }}" class="btn-outline flex items-center gap-2">
            📱 Scan QRIS
        </a>
        @endif

        <a href="{{ route('orders.my') }}" class="btn-outline">← Pesanan Saya</a>
    </div>
</div>
@endsection
