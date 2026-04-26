@extends('layouts.app')
@section('title', 'Menu')
@section('page-title', 'Menu Kantin')

@section('content')
<div class="max-w-7xl mx-auto" x-data="menuPage()">

    {{-- Search + Filter bar --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
            <form method="GET" action="{{ route('home') }}">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="ks-input pl-9" placeholder="Cari menu...">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
            </form>
        </div>

        {{-- Category pills --}}
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('home') }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition-all
                      {{ !request('category') ? 'bg-gold text-ks' : 'bg-ks-card border border-ks-border text-gray-400 hover:border-gold/50' }}">
                Semua
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('home', ['category' => $cat->slug]) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition-all
                      {{ request('category') === $cat->slug ? 'bg-gold text-ks' : 'bg-ks-card border border-ks-border text-gray-400 hover:border-gold/50' }}">
                {{ $cat->icon }} {{ $cat->name }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- Featured section --}}
    @if(!request('search') && !request('category') && $featured->count() > 0)
    <div class="mb-8">
        <h2 class="font-display text-gold text-2xl font-semibold mb-4">✨ Menu Unggulan</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($featured as $item)
            <div class="ks-card group cursor-pointer hover:border-gold/30 transition-all duration-200 relative overflow-hidden"
                 @click="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}')">
                <div class="absolute top-2 right-2 bg-gold/20 text-gold text-xs px-2 py-0.5 rounded-full">Unggulan</div>
                <div class="h-32 bg-ks-muted rounded-lg mb-3 flex items-center justify-center text-4xl overflow-hidden">
                    @if($item->image)
                        <img src="{{ $item->image_url }}" class="w-full h-full object-cover" alt="{{ $item->name }}">
                    @else
                        🍽️
                    @endif
                </div>
                <p class="font-medium text-sm text-gray-200 leading-tight">{{ $item->name }}</p>
                <p class="text-gold font-semibold text-sm mt-1">{{ $item->formatted_price }}</p>
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-xs text-gray-500">Stok: {{ $item->stock }}</span>
                    <button class="w-7 h-7 rounded-full bg-gold/10 hover:bg-gold text-gold hover:text-ks flex items-center justify-center transition-all text-lg font-bold leading-none">+</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- All menu items --}}
    <div>
        <h2 class="font-display text-gold text-2xl font-semibold mb-4">
            {{ request('category') ? $categories->firstWhere('slug', request('category'))?->name ?? 'Menu' : 'Semua Menu' }}
            <span class="text-gray-500 text-base font-sans font-normal ml-2">{{ $menuItems->count() }} item</span>
        </h2>

        @if($menuItems->isEmpty())
        <div class="ks-card text-center py-16">
            <div class="text-5xl mb-3">🔍</div>
            <p class="text-gray-400">Menu tidak ditemukan</p>
        </div>
        @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @foreach($menuItems as $item)
            <div class="ks-card group hover:border-gold/30 transition-all duration-200 fade-in"
                 style="animation-delay: {{ $loop->index * 30 }}ms">
                {{-- Image --}}
                <div class="h-28 bg-ks-muted rounded-lg mb-3 flex items-center justify-center text-4xl overflow-hidden">
                    @if($item->image)
                        <img src="{{ $item->image_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform" alt="{{ $item->name }}">
                    @else
                        🍽️
                    @endif
                </div>

                <div class="flex-1">
                    <p class="font-medium text-sm text-gray-200 leading-tight line-clamp-2">{{ $item->name }}</p>
                    @if($item->description)
                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $item->description }}</p>
                    @endif
                    <p class="text-gold font-semibold text-sm mt-1.5">{{ $item->formatted_price }}</p>
                </div>

                <div class="mt-3 flex items-center justify-between">
                    <span class="text-xs {{ $item->stock > 5 ? 'text-gray-500' : 'text-red-400' }}">
                        {{ $item->stock > 0 ? 'Stok: ' . $item->stock : 'Habis' }}
                    </span>
                    @if($item->stock > 0)
                    <button onclick="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}')"
                            class="w-8 h-8 rounded-full bg-gold/10 hover:bg-gold text-gold hover:text-ks flex items-center justify-center transition-all text-lg font-bold">
                        +
                    </button>
                    @else
                    <span class="text-xs text-gray-600 italic">Habis</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- Floating cart button --}}
<div id="floating-cart" class="no-print fixed bottom-6 right-6 z-50 hidden">
    <a href="{{ route('cart') }}" class="btn-gold flex items-center gap-2 px-5 py-3 rounded-full shadow-2xl" style="box-shadow: 0 8px 32px rgba(200,169,110,0.3);">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        <span id="cart-count-btn" class="font-bold">0</span> item
    </a>
</div>
@endsection

@push('scripts')
<script>
function menuPage() {
    return {};
}

async function addToCart(menuItemId, name) {
    try {
        const res = await apiFetch('{{ route('cart.add') }}', {
            method: 'POST',
            body: JSON.stringify({ menu_item_id: menuItemId, quantity: 1 })
        });
        const data = await res.json();
        if (data.success) {
            showToast(`${name} ditambahkan ke keranjang ✓`);
            updateCartCount(data.cart_count);
        } else {
            showToast(data.error || 'Gagal menambahkan', 'error');
        }
    } catch (e) {
        showToast('Terjadi kesalahan', 'error');
    }
}

async function updateCartCount(count) {
    const btn = document.getElementById('floating-cart');
    const countEl = document.getElementById('cart-count-btn');
    if (countEl) countEl.textContent = count;
    if (btn) btn.classList.toggle('hidden', count === 0);
}

// Init cart count
(async () => {
    try {
        const res = await fetch('{{ route('cart.count') }}');
        const data = await res.json();
        updateCartCount(data.count);
    } catch (e) {}
})();
</script>
@endpush
