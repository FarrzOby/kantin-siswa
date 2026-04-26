@extends('layouts.auth')
@section('title', 'Login')

@section('form')
<div>
    <h2 style="font-family:'Cormorant Garamond',serif; font-size:30px; font-weight:700; color:#F0EDE8;" class="mb-1">Selamat Datang</h2>
    <p class="text-gray-500 text-sm mb-8">Masuk ke akun Kantin Siswa Anda</p>

    {{-- Validation errors --}}
    @if($errors->any())
    <div class="mb-5 p-3 bg-red-900/30 border border-red-700/40 rounded-lg">
        @foreach($errors->all() as $error)
            <p class="text-red-300 text-sm">{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs text-gray-400 mb-1.5 tracking-wide uppercase">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="ks-input" placeholder="email@sekolah.com" required autofocus>
        </div>

        <div>
            <input type="password" name="password" class="ks-input" placeholder="••••••••" required>
            <div class="flex justify-between items-center mb-1.5">
                <label class="text-xs text-gray-400 tracking-wide uppercase">Password</label>
                @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs text-gold hover:text-gold-light">Lupa password?</a>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 accent-gold rounded">
            <label for="remember" class="text-sm text-gray-400">Ingat saya</label>
        </div>

        <button type="submit" class="btn-gold mt-2">Masuk</button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-gold hover:text-gold-light font-medium">Daftar sekarang</a>
    </p>

    {{-- Demo accounts --}}
    <div class="mt-6 p-4 rounded-lg border border-[#222] bg-[#0A0A0A]">
        <p class="text-xs text-gray-500 mb-3 font-medium uppercase tracking-wide">Akun Demo</p>
        <div class="space-y-1.5 text-xs text-gray-400 font-mono">
            <div class="flex justify-between"><span class="text-red-300">Admin:</span><span>admin@kantinsiswa.com / password</span></div>
            <div class="flex justify-between"><span class="text-blue-300">Kasir:</span><span>kasir@kantinsiswa.com / password</span></div>
            <div class="flex justify-between"><span class="text-gold">Siswa:</span><span>siswa@kantinsiswa.com / password</span></div>
        </div>
    </div>
</div>
@endsection
