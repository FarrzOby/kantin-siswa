<?php
// app/Http/Controllers/AdminController.php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'today_sales'    => Order::whereDate('created_at', today())->where('payment_status', 'paid')->sum('total'),
            'today_orders'   => Order::whereDate('created_at', today())->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_users'    => User::where('role', 'siswa')->count(),
            'total_menus'    => MenuItem::where('is_available', true)->count(),
            'monthly_sales'  => Order::whereMonth('created_at', now()->month)->where('payment_status', 'paid')->sum('total'),
        ];

        $recentOrders = Order::with('user', 'items')
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        $topItems = \App\Models\OrderItem::selectRaw('item_name, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('item_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return view('pages.admin.dashboard', compact('stats', 'recentOrders', 'topItems'));
    }

    public function users()
    {
        $users = User::orderBy('role')->orderBy('name')->paginate(20);
        return view('pages.admin.users', compact('users'));
    }

    public function createUser()
    {
        return view('pages.admin.user-form');
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'role'     => 'required|in:admin,kasir,siswa',
            'kelas'    => 'nullable|string|max:100',
            'nis'      => 'nullable|string|max:50',
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('admin.users')->with('success', 'User berhasil ditambahkan!');
    }

    public function editUser(User $user)
    {
        return view('pages.admin.user-form', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'role'     => 'required|in:admin,kasir,siswa',
            'kelas'    => 'nullable|string|max:100',
            'nis'      => 'nullable|string|max:50',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => Rules\Password::defaults()]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'User berhasil diperbarui!');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri!');
        }
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User berhasil dihapus!');
    }
}
