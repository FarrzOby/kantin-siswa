@extends('layouts.app')
@section('title', isset($menuItem) ? 'Edit Menu' : 'Tambah Menu')
@section('page-title', isset($menuItem) ? 'Edit Menu' : 'Tambah Menu')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="ks-card">
        <form method="POST" action="{{ isset($menuItem) ? route('admin.menu.update', $menuItem) : route('admin.menu.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @if(isset($menuItem)) @method('PUT') @endif

            @if($errors->any())
            <div class="p-3 bg-red-900/30 border border-red-700/40 rounded-lg">
                @foreach($errors->all() as $e) <p class="text-red-300 text-sm">{{ $e }}</p> @endforeach
            </div>
            @endif

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Nama Menu *</label>
                    <input type="text" name="name" value="{{ old('name', $menuItem->name ?? '') }}" class="ks-input" required>
                </div>

                <div>
                    <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Kategori *</label>
                    <select name="category_id" class="ks-input" required>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $menuItem->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Harga (Rp) *</label>
                    <input type="number" name="price" value="{{ old('price', $menuItem->price ?? '') }}" class="ks-input" min="0" required>
                </div>

                <div>
                    <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Stok *</label>
                    <input type="number" name="stock" value="{{ old('stock', $menuItem->stock ?? 0) }}" class="ks-input" min="0" required>
                </div>

                <div>
                    <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Gambar</label>
                    <input type="file" name="image" accept="image/*" class="ks-input">
                    @if(isset($menuItem) && $menuItem->image)
                    <p class="text-xs text-gray-500 mt-1">Biarkan kosong untuk tidak mengubah gambar</p>
                    @endif
                </div>

                <div class="col-span-2">
                    <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Deskripsi</label>
                    <textarea name="description" rows="3" class="ks-input resize-none">{{ old('description', $menuItem->description ?? '') }}</textarea>
                </div>
            </div>

            <div class="flex gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_available" value="1" class="w-4 h-4 accent-gold"
                           {{ old('is_available', $menuItem->is_available ?? true) ? 'checked' : '' }}>
                    <span class="text-sm text-gray-300">Tersedia</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" class="w-4 h-4 accent-gold"
                           {{ old('is_featured', $menuItem->is_featured ?? false) ? 'checked' : '' }}>
                    <span class="text-sm text-gray-300">Menu Unggulan</span>
                </label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-gold">{{ isset($menuItem) ? 'Simpan Perubahan' : 'Tambah Menu' }}</button>
                <a href="{{ route('admin.menu.index') }}" class="btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
