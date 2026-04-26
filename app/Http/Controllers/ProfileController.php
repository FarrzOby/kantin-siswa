<?php
// app/Http/Controllers/ProfileController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    public function show()
    {
        $user   = auth()->user();
        $orders = $user->orders()->with('items')->orderByDesc('created_at')->take(5)->get();
        return view('pages.profile', compact('user', 'orders'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'kelas'    => 'nullable|string|max:100',
            'nis'      => 'nullable|string|max:50',
            'avatar'   => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);

        return redirect()->route('profile')->with('success', 'Password berhasil diubah!');
    }
}
