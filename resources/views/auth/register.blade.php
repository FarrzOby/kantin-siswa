@extends('layouts.auth')
@section('title', 'Daftar Akun')

@section('form')
<div>
    <h2 style="font-family:'Cormorant Garamond',serif; font-size:28px; font-weight:700; color:#F0EDE8;" class="mb-1">Buat Akun Baru</h2>
    <p class="text-gray-500 text-sm mb-7">Daftarkan diri sebagai siswa Kantin Siswa</p>

    @if($errors->any())
    <div class="mb-5 p-3 bg-red-900/30 border border-red-700/40 rounded-lg">
        @foreach($errors->all() as $error)
            <p class="text-red-300 text-sm">{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        {{-- Hidden: new users are always siswa --}}
        <input type="hidden" name="role" value="siswa">

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs text-gray-400 mb-1.5 tracking-wide uppercase">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" class="ks-input" placeholder="Nama kamu" required autofocus>
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1.5 tracking-wide uppercase">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" class="ks-input" placeholder="username" required>
            </div>
        </div>

        <div>
            <label class="block text-xs text-gray-400 mb-1.5 tracking-wide uppercase">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="ks-input" placeholder="email@sekolah.com" required>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs text-gray-400 mb-1.5 tracking-wide uppercase">NIS</label>
                <input type="text" name="nis" value="{{ old('nis') }}" class="ks-input" placeholder="Nomor Induk Siswa">
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1.5 tracking-wide uppercase">Kelas</label>
                <input type="text" name="kelas" value="{{ old('kelas') }}" class="ks-input" placeholder="XI IPA 1">
            </div>
        </div>

        <div>
            <label class="block text-xs text-gray-400 mb-1.5 tracking-wide uppercase">Password</label>
            <input type="password" name="password" class="ks-input" placeholder="Min. 8 karakter" required>
        </div>

        <div>
            <label class="block text-xs text-gray-400 mb-1.5 tracking-wide uppercase">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="ks-input" placeholder="Ulangi password" required>
        </div>

        <button type="submit" class="btn-gold mt-2">Buat Akun</button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-5">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-gold hover:text-gold-light font-medium">Masuk di sini</a>
    </p>
</div>
@endsection
