<?php
// app/Models/CartItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['user_id', 'menu_item_id', 'quantity', 'notes'];

    public function user()     { return $this->belongsTo(User::class); }
    public function menuItem() { return $this->belongsTo(MenuItem::class); }

    public function getSubtotalAttribute(): float
    {
        return $this->quantity * $this->menuItem->price;
    }
}
