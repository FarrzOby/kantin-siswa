@extends('layouts.app')
@section('title', 'Keranjang')
@section('page-title', 'Keranjang Belanja')

@section('content')
<div class="max-w-5xl mx-auto" x-data="cartPage()">
    @if($cartItems->isEmpty())
    <div class="ks-card text-center py-20">
        <div class="text-6xl mb-4">🛒</div>
        <h2 class="font-display text-gold text-2xl mb-2">Keranjang Kosong</h2>
        <p class="text-gray-500 mb-6">Belum ada item di keranjangmu</p>
        <a href="{{ route('home') }}" class="btn-gold inline-block">Lihat Menu</a>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Cart items --}}
        <div class="lg:col-span-2 space-y-3">
            <div class="flex items-center justify-between mb-2">
                <h2 class="font-display text-gold text-xl">{{ $cartItems->count() }} Item</h2>
            </div>

            @foreach($cartItems as $item)
            <div class="ks-card flex items-center gap-4 fade-in" id="cart-row-{{ $item->id }}">
                {{-- Image --}}
                <div class="w-16 h-16 rounded-lg bg-ks-muted flex items-center justify-center text-2xl flex-shrink-0 overflow-hidden">
                    @if($item->menuItem->image)
                        <img src="{{ $item->menuItem->image_url }}" class="w-full h-full object-cover" alt="">
                    @else
                        🍽️
                    @endif
                </div>

                {{-- Details --}}
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-200 truncate">{{ $item->menuItem->name }}</p>
                    <p class="text-gold text-sm">{{ $item->menuItem->formatted_price }}</p>
                    <p class="text-xs text-gray-500">{{ $item->menuItem->category->name }}</p>
                </div>

                {{-- Qty control --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button onclick="updateQty({{ $item->id }}, {{ $item->quantity - 1 }})"
                            class="w-7 h-7 rounded-full bg-ks-muted hover:bg-ks-border text-gray-300 flex items-center justify-center transition-all font-bold">−</button>
                    <span id="qty-{{ $item->id }}" class="w-8 text-center text-sm font-medium">{{ $item->quantity }}</span>
                    <button onclick="updateQty({{ $item->id }}, {{ $item->quantity + 1 }})"
                            class="w-7 h-7 rounded-full bg-ks-muted hover:bg-ks-border text-gray-300 flex items-center justify-center transition-all font-bold">+</button>
                </div>

                {{-- Subtotal --}}
                <div class="text-right flex-shrink-0 min-w-[80px]">
                    <p id="sub-{{ $item->id }}" class="text-gold font-semibold text-sm">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </p>
                </div>

                {{-- Remove --}}
                <button onclick="removeItem({{ $item->id }})"
                        class="text-gray-600 hover:text-red-400 transition-colors flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @endforeach
        </div>

        {{-- Order summary --}}
        <div class="space-y-4">
            <div class="ks-card">
                <h3 class="font-display text-gold text-xl mb-4">Ringkasan</h3>

                <div class="space-y-2 text-sm">
                    @foreach($cartItems as $item)
                    <div class="flex justify-between text-gray-400" id="summary-{{ $item->id }}">
                        <span class="truncate pr-2">{{ $item->menuItem->name }} ×{{ $item->quantity }}</span>
                        <span class="flex-shrink-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="border-t border-ks-border mt-4 pt-4">
                    <div class="flex justify-between font-semibold">
                        <span class="text-gray-300">Total</span>
                        <span id="grand-total" class="text-gold text-lg font-display">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <a href="{{ route('checkout') }}" class="btn-gold w-full text-center block mt-5">
                    Lanjut ke Pembayaran →
                </a>
                <a href="{{ route('home') }}" class="btn-outline w-full text-center block mt-2 text-sm">
                    + Tambah Item
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function cartPage() { return {}; }

async function updateQty(cartItemId, newQty) {
    if (newQty < 1) { removeItem(cartItemId); return; }

    try {
        const res = await apiFetch(`/cart/${cartItemId}`, {
            method: 'PATCH',
            body: JSON.stringify({ quantity: newQty })
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById(`qty-${cartItemId}`).textContent = newQty;
            document.getElementById(`sub-${cartItemId}`).textContent = data.item_subtotal;
            document.getElementById('grand-total').textContent = data.total;
        }
    } catch(e) { showToast('Gagal update', 'error'); }
}

async function removeItem(cartItemId) {
    try {
        await apiFetch(`/cart/${cartItemId}`, { method: 'DELETE' });
        const row = document.getElementById(`cart-row-${cartItemId}`);
        if (row) { row.style.opacity = '0'; row.style.transform = 'translateX(20px)'; row.style.transition = '0.3s'; setTimeout(() => location.reload(), 300); }
    } catch(e) { showToast('Gagal hapus', 'error'); }
}
</script>
@endpush
