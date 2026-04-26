@extends('layouts.app')
@section('title', 'Pesanan Saya')
@section('page-title', 'Pesanan Saya')

@section('content')
<div class="max-w-3xl mx-auto space-y-3">

    @forelse($orders as $order)
    <a href="{{ route('orders.show', $order) }}"
       class="ks-card flex items-center gap-4 hover:border-gold/30 transition-all block fade-in cursor-pointer">

        {{-- Status dot --}}
        <div class="w-2.5 h-2.5 rounded-full flex-shrink-0
            @if($order->status === 'pending')    bg-yellow-400
            @elseif($order->status === 'processing') bg-blue-400
            @elseif($order->status === 'ready')  bg-green-400
            @elseif($order->status === 'completed') bg-gray-500
            @else bg-red-400 @endif">
        </div>

        {{-- Order info --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <p class="font-mono text-xs text-gold font-bold">{{ $order->order_number }}</p>
                <span class="badge-{{ $order->status }} text-xs px-2 py-0.5 rounded-full">{{ $order->status_label }}</span>
                @if($order->payment_status === 'paid')
                <span class="text-xs text-green-400">✓ Lunas</span>
                @endif
            </div>
            <p class="text-gray-400 text-xs mt-1">
                {{ $order->items->map(fn($i) => $i->quantity.'× '.$i->item_name)->take(3)->join(', ') }}
                @if($order->items->count() > 3) +{{ $order->items->count() - 3 }} lagi @endif
            </p>
            <p class="text-gray-600 text-xs mt-0.5">{{ $order->created_at->diffForHumans() }}</p>
        </div>

        <div class="text-right flex-shrink-0">
            <p class="text-gold font-semibold font-display text-lg">{{ $order->formatted_total }}</p>
            <p class="text-xs text-gray-500">{{ $order->payment_method === 'qris' ? 'QRIS' : 'Tunai' }}</p>
        </div>

        <svg class="w-4 h-4 text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
    @empty
    <div class="ks-card text-center py-16">
        <div class="text-5xl mb-3">📋</div>
        <p class="text-gray-400 mb-4">Belum ada pesanan</p>
        <a href="{{ route('home') }}" class="btn-gold inline-block">Pesan Sekarang</a>
    </div>
    @endforelse

    @if($orders->hasPages())
    <div class="pt-2">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
