@extends('layouts.app')
@section('title', 'Profil')
@section('page-title', 'Profil Saya')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- Profile header card --}}
    <div class="ks-card relative overflow-hidden">
        {{-- Background bar --}}
        <div class="absolute top-0 left-0 right-0 h-24"
             style="background: linear-gradient(135deg, rgba(200,169,110,0.12), rgba(200,169,110,0.04));
                    border-bottom: 1px solid rgba(200,169,110,0.1);"></div>

        <div class="relative flex items-end gap-5 pt-14 pb-2">
            <div class="relative">
                <img src="{{ $user->avatar_url }}" class="w-20 h-20 rounded-full border-4 border-ks object-cover" alt="{{ $user->name }}">
                <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-green-500 border-2 border-ks"></div>
            </div>
            <div class="pb-1">
                <h2 class="font-display text-2xl text-gray-100 font-semibold">{{ $user->name }}</h2>
                <p class="text-gray-500 text-sm">@{{ $user->username ?? $user->email }}</p>
                <span class="text-xs px-2 py-0.5 rounded-full mt-1 inline-block
                    @if($user->isAdmin()) bg-red-900/30 text-red-300
                    @elseif($user->isKasir()) bg-blue-900/30 text-blue-300
                    @else bg-gold/10 text-gold @endif">
                    {{ ucfirst($user->role) }}
                </span>
            </div>
        </div>

        @if($user->isSiswa())
        <div class="flex gap-6 mt-4 pt-4 border-t border-ks-border text-sm">
            <div><p class="text-gray-500 text-xs">NIS</p><p class="text-gray-200">{{ $user->nis ?: '—' }}</p></div>
            <div><p class="text-gray-500 text-xs">Kelas</p><p class="text-gray-200">{{ $user->kelas ?: '—' }}</p></div>
            <div><p class="text-gray-500 text-xs">Total Pesanan</p><p class="text-gray-200">{{ $user->orders()->count() }}</p></div>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Edit profile form --}}
        <div class="ks-card">
            <h3 class="font-display text-gold text-lg mb-4">Edit Profil</h3>
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Foto Profil</label>
                    <input type="file" name="avatar" accept="image/*" class="ks-input text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="ks-input" required>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" class="ks-input">
                </div>
                @if($user->isSiswa())
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">NIS</label>
                        <input type="text" name="nis" value="{{ old('nis', $user->nis) }}" class="ks-input">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Kelas</label>
                        <input type="text" name="kelas" value="{{ old('kelas', $user->kelas) }}" class="ks-input">
                    </div>
                </div>
                @endif
                <button type="submit" class="btn-gold w-full">Simpan Perubahan</button>
            </form>
        </div>

        {{-- Change password --}}
        <div class="space-y-5">
            <div class="ks-card">
                <h3 class="font-display text-gold text-lg mb-4">Ubah Password</h3>
                <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Password Saat Ini</label>
                        <input type="password" name="current_password" class="ks-input" required>
                        @error('current_password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Password Baru</label>
                        <input type="password" name="password" class="ks-input" required>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="ks-input" required>
                    </div>
                    <button type="submit" class="btn-outline w-full">Ubah Password</button>
                </form>
            </div>

            {{-- Recent orders widget --}}
            <div class="ks-card">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-display text-gold text-lg">Pesanan Terakhir</h3>
                    <a href="{{ route('orders.my') }}" class="text-xs text-gray-500 hover:text-gold">Lihat semua →</a>
                </div>
                <div class="space-y-2">
                    @forelse($orders as $order)
                    <a href="{{ route('orders.show', $order) }}"
                       class="flex items-center justify-between hover:bg-ks-card/50 p-2 rounded-lg transition-colors">
                        <div>
                            <p class="font-mono text-xs text-gold">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="badge-{{ $order->status }} text-xs px-2 py-0.5 rounded-full">{{ $order->status_label }}</span>
                            <p class="text-gold text-xs font-semibold mt-0.5">{{ $order->formatted_total }}</p>
                        </div>
                    </a>
                    @empty
                    <p class="text-gray-500 text-xs text-center py-3">Belum ada pesanan</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
