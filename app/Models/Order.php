<?php
// app/Models/Order.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'kasir_id', 'status', 'payment_method',
        'payment_status', 'subtotal', 'tax', 'total', 'amount_paid',
        'change_amount', 'qris_ref', 'notes', 'paid_at', 'completed_at',
    ];

    protected $casts = [
        'paid_at'      => 'datetime',
        'completed_at' => 'datetime',
        'subtotal'     => 'decimal:2',
        'tax'          => 'decimal:2',
        'total'        => 'decimal:2',
        'amount_paid'  => 'decimal:2',
        'change_amount'=> 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', today())->count() + 1;
        return 'KS-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function user()       { return $this->belongsTo(User::class); }
    public function kasir()      { return $this->belongsTo(User::class, 'kasir_id'); }
    public function items()      { return $this->hasMany(OrderItem::class); }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'    => 'Menunggu',
            'processing' => 'Diproses',
            'ready'      => 'Siap Diambil',
            'completed'  => 'Selesai',
            'cancelled'  => 'Dibatalkan',
            default      => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'    => 'yellow',
            'processing' => 'blue',
            'ready'      => 'green',
            'completed'  => 'gray',
            'cancelled'  => 'red',
            default      => 'gray',
        };
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }
}
