<?php
// app/Models/MenuItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'category_id', 'name', 'description', 'price',
        'image', 'stock', 'is_available', 'is_featured',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_featured'  => 'boolean',
        'price'        => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/food-placeholder.jpg');
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
