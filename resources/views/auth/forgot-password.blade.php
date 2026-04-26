{{-- resources/views/auth/forgot-password.blade.php --}}
@extends('layouts.auth')
@section('title', 'Lupa Password')
@section('form')
<div>
    <h2 style="font-family:'Cormorant Garamond',serif; font-size:28px; font-weight:700; color:#F0EDE8;" class="mb-1">Lupa Password?</h2>
    <p class="text-gray-500 text-sm mb-6">Masukkan email dan kami kirim link reset.</p>

    @if (session('status'))
    <div class="mb-4 p-3 bg-green-900/30 border border-green-700/40 rounded-lg text-green-300 text-sm">{{ session('status') }}</div>
    @endif

    @if($errors->any())
    <div class="mb-4 p-3 bg-red-900/30 border border-red-700/40 rounded-lg">
        @foreach($errors->all() as $e)<p class="text-red-300 text-sm">{{ $e }}</p>@endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="ks-input" required autofocus>
        </div>
        <button type="submit" class="btn-gold">Kirim Link Reset</button>
    </form>
    <p class="text-center text-sm text-gray-500 mt-5">
        <a href="{{ route('login') }}" class="text-gold hover:text-gold-light">← Kembali ke Login</a>
    </p>
</div>
@endsection
