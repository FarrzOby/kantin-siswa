@extends('layouts.app')
@section('title', isset($user) ? 'Edit User' : 'Tambah User')
@section('page-title', isset($user) ? 'Edit User' : 'Tambah User')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="ks-card">
        <form method="POST"
              action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}"
              class="space-y-5">
            @csrf
            @if(isset($user)) @method('PUT') @endif

            @if($errors->any())
            <div class="p-3 bg-red-900/30 border border-red-700/40 rounded-lg">
                @foreach($errors->all() as $e)<p class="text-red-300 text-sm">{{ $e }}</p>@endforeach
            </div>
            @endif

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Nama Lengkap *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="ks-input" required>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Username *</label>
                    <input type="text" name="username" value="{{ old('username', $user->username ?? '') }}" class="ks-input" required>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="ks-input" required>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Role *</label>
                    <select name="role" class="ks-input" required>
                        <option value="siswa"  {{ old('role', $user->role ?? '') === 'siswa'  ? 'selected' : '' }}>Siswa</option>
                        <option value="kasir"  {{ old('role', $user->role ?? '') === 'kasir'  ? 'selected' : '' }}>Kasir</option>
                        <option value="admin"  {{ old('role', $user->role ?? '') === 'admin'  ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Kelas</label>
                    <input type="text" name="kelas" value="{{ old('kelas', $user->kelas ?? '') }}" class="ks-input" placeholder="XI IPA 1">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">NIS</label>
                    <input type="text" name="nis" value="{{ old('nis', $user->nis ?? '') }}" class="ks-input" placeholder="2024001">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">
                        Password {{ isset($user) ? '(kosongkan jika tidak diubah)' : '*' }}
                    </label>
                    <input type="password" name="password" class="ks-input" {{ isset($user) ? '' : 'required' }}>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-gold">
                    {{ isset($user) ? 'Simpan Perubahan' : 'Tambah User' }}
                </button>
                <a href="{{ route('admin.users') }}" class="btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
