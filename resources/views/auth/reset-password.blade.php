@extends('layouts.auth')
@section('title', 'Reset Password')
@section('form')
<div>
    <h2 style="font-family:'Cormorant Garamond',serif; font-size:28px; font-weight:700; color:#F0EDE8;" class="mb-1">Reset Password</h2>
    <p class="text-gray-500 text-sm mb-6">Buat password baru untuk akunmu.</p>

    @if($errors->any())
    <div class="mb-4 p-3 bg-red-900/30 border border-red-700/40 rounded-lg">
        @foreach($errors->all() as $e)<p class="text-red-300 text-sm">{{ $e }}</p>@endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div>
            <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email', $request->email) }}" class="ks-input" required>
        </div>
        <div>
            <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Password Baru</label>
            <input type="password" name="password" class="ks-input" required>
        </div>
        <div>
            <label class="block text-xs text-gray-400 uppercase tracking-wide mb-1.5">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="ks-input" required>
        </div>
        <button type="submit" class="btn-gold">Reset Password</button>
    </form>
</div>
@endsection
