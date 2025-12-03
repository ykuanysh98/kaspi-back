<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'status',
        'is_approved',
        'article',
        'category_id'
    ];

    protected $casts = [
        'images' => 'array',
        'is_favorite' => 'boolean',
    ];


    // Артикул автоматты түрде генерациялау (мысалы, ART-000123)
    protected static function booted()
    {
        static::creating(function ($product) {
            if (!$product->article) {
                $product->article = 'ART-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function partners()
    {
        return $this->belongsToMany(Partner::class, 'partner_product')
            ->withPivot('price', 'quantity');
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

}
