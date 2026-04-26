@extends('layouts.app')
@section('title', 'Kelola Menu')
@section('page-title', 'Kelola Menu')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <p class="text-gray-400 text-sm">{{ $items->count() }} item menu</p>
        <a href="{{ route('admin.menu.create') }}" class="btn-gold flex items-center gap-2">
            + Tambah Menu
        </a>
    </div>

    <div class="ks-card overflow-hidden p-0">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-ks-border">
                    <th class="text-left px-5 py-3 text-xs text-gray-500 uppercase tracking-wide">Menu</th>
                    <th class="text-left px-5 py-3 text-xs text-gray-500 uppercase tracking-wide">Kategori</th>
                    <th class="text-right px-5 py-3 text-xs text-gray-500 uppercase tracking-wide">Harga</th>
                    <th class="text-right px-5 py-3 text-xs text-gray-500 uppercase tracking-wide">Stok</th>
                    <th class="text-center px-5 py-3 text-xs text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ks-border">
                @forelse($items as $item)
                <tr class="hover:bg-ks-card/50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-ks-muted flex items-center justify-center text-xl flex-shrink-0 overflow-hidden">
                                @if($item->image) <img src="{{ $item->image_url }}" class="w-full h-full object-cover" alt=""> @else 🍽️ @endif
                            </div>
                            <div>
                                <p class="font-medium text-gray-200">{{ $item->name }}</p>
                                @if($item->is_featured)
                                <span class="text-xs text-gold">★ Unggulan</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-gray-400">{{ $item->category->name }}</td>
                    <td class="px-5 py-3 text-gold text-right font-semibold">{{ $item->formatted_price }}</td>
                    <td class="px-5 py-3 text-right {{ $item->stock < 5 ? 'text-red-400' : 'text-gray-300' }}">{{ $item->stock }}</td>
                    <td class="px-5 py-3 text-center">
                        @if($item->is_available)
                            <span class="text-xs bg-green-900/30 text-green-400 px-2 py-0.5 rounded-full">Tersedia</span>
                        @else
                            <span class="text-xs bg-red-900/30 text-red-400 px-2 py-0.5 rounded-full">Tidak Tersedia</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2 justify-end">
                            <a href="{{ route('admin.menu.edit', $item) }}" class="text-gray-500 hover:text-gold transition-colors text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.menu.destroy', $item) }}" onsubmit="return confirm('Hapus menu ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-600 hover:text-red-400 transition-colors text-xs">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-gray-500">
                        Belum ada menu. <a href="{{ route('admin.menu.create') }}" class="text-gold">Tambah sekarang →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
