<?php
// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'username', 'role', 'kelas', 'nis', 'avatar', 'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // Role helpers
    public function isAdmin(): bool  { return $this->role === 'admin'; }
    public function isKasir(): bool  { return $this->role === 'kasir'; }
    public function isSiswa(): bool  { return $this->role === 'siswa'; }

    public function canManageMenu(): bool   { return in_array($this->role, ['admin']); }
    public function canProcessOrder(): bool { return in_array($this->role, ['admin', 'kasir']); }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=c8a96e&color=000&bold=true';
    }
}
