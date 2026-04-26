@extends('layouts.app')
@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <p class="text-gray-400 text-sm">{{ $users->total() }} user terdaftar</p>
        <a href="{{ route('admin.users.create') }}" class="btn-gold">+ Tambah User</a>
    </div>

    <div class="ks-card overflow-hidden p-0">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-ks-border">
                    <th class="text-left px-5 py-3 text-xs text-gray-500 uppercase tracking-wide">User</th>
                    <th class="text-left px-5 py-3 text-xs text-gray-500 uppercase tracking-wide">Role</th>
                    <th class="text-left px-5 py-3 text-xs text-gray-500 uppercase tracking-wide">Kelas / Info</th>
                    <th class="text-left px-5 py-3 text-xs text-gray-500 uppercase tracking-wide">Bergabung</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ks-border">
                @foreach($users as $user)
                <tr class="hover:bg-ks-card/50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-full flex-shrink-0" alt="">
                            <div>
                                <p class="font-medium text-gray-200">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full
                            @if($user->role === 'admin') bg-red-900/30 text-red-300
                            @elseif($user->role === 'kasir') bg-blue-900/30 text-blue-300
                            @else bg-gold/10 text-gold @endif">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-400 text-xs">
                        @if($user->kelas) {{ $user->kelas }} @endif
                        @if($user->nis) · NIS: {{ $user->nis }} @endif
                    </td>
                    <td class="px-5 py-3 text-gray-500 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-3">
                        <div class="flex gap-2 justify-end">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-gray-500 hover:text-gold text-xs">Edit</a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus user ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-600 hover:text-red-400 text-xs">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
</div>
@endsection
