@extends('layouts.app')
@section('title', 'Checkout')
@section('page-title', 'Checkout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Order items summary --}}
        <div class="ks-card">
            <h3 class="font-display text-gold text-xl mb-4">Detail Pesanan</h3>
            <div class="space-y-3">
                @foreach($cartItems as $item)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-ks-muted flex items-center justify-center text-xl flex-shrink-0 overflow-hidden">
                        @if($item->menuItem->image)
                            <img src="{{ $item->menuItem->image_url }}" class="w-full h-full object-cover" alt="">
                        @else
                            🍽️
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-200 truncate">{{ $item->menuItem->name }}</p>
                        <p class="text-xs text-gray-500">{{ $item->quantity }}× Rp {{ number_format($item->menuItem->price, 0, ',', '.') }}</p>
                    </div>
                    <p class="text-gold text-sm font-semibold flex-shrink-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>
            <div class="border-t border-ks-border mt-4 pt-4 flex justify-between">
                <span class="font-semibold text-gray-300">Total</span>
                <span class="font-display text-gold text-xl font-bold">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Payment form --}}
        <div class="ks-card">
            <h3 class="font-display text-gold text-xl mb-4">Pilih Pembayaran</h3>

            <form method="POST" action="{{ route('orders.store') }}" class="space-y-4">
                @csrf

                {{-- Payment method --}}
                <div class="space-y-2">
                    <label class="block text-xs text-gray-400 uppercase tracking-wide mb-2">Metode Pembayaran</label>

                    <label class="flex items-center gap-3 p-3 rounded-lg border border-ks-border hover:border-gold/40 cursor-pointer transition-all has-[:checked]:border-gold has-[:checked]:bg-gold/5">
                        <input type="radio" name="payment_method" value="cash" class="accent-gold" checked>
                        <div class="flex items-center gap-2">
                            <span class="text-xl">💵</span>
                            <div>
                                <p class="text-sm font-medium text-gray-200">Tunai</p>
                                <p class="text-xs text-gray-500">Bayar saat mengambil pesanan</p>
                            </div>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-lg border border-ks-border hover:border-gold/40 cursor-pointer transition-all has-[:checked]:border-gold has-[:checked]:bg-gold/5">
                        <input type="radio" name="payment_method" value="qris" class="accent-gold">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">📱</span>
                            <div>
                                <p class="text-sm font-medium text-gray-200">QRIS</p>
                                <p class="text-xs text-gray-500">Scan QR di kasir untuk bayar</p>
                            </div>
                        </div>
                    </label>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Catatan (opsional)</label>
                    <textarea name="notes" rows="2" class="ks-input resize-none" placeholder="Contoh: tanpa sambal, tidak pedas...">{{ old('notes') }}</textarea>
                </div>

                {{-- Customer info --}}
                <div class="p-3 bg-ks-surface rounded-lg border border-ks-border">
                    <p class="text-xs text-gray-500 mb-1">Pemesan</p>
                    <p class="text-sm font-medium text-gray-200">{{ auth()->user()->name }}</p>
                    @if(auth()->user()->kelas)
                    <p class="text-xs text-gray-500">{{ auth()->user()->kelas }}</p>
                    @endif
                </div>

                <button type="submit" class="btn-gold w-full text-base">
                    🎉 Buat Pesanan
                </button>
                <a href="{{ route('cart') }}" class="btn-outline w-full text-center block text-sm">← Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
