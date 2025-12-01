<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'partner_id',
        'quantity',
        'price',
    ];

    // 🟢 Карточка — бір өнімге тиесілі
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // 🟢 Карточка — бір партнерге тиесілі
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    // 🟢 Карточка — бір қолданушыға тиесілі
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
