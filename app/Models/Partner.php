<?php

namespace App\Models;

use App\Models\PartnerReview;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Partner extends Authenticatable
{
    use HasApiTokens, Notifiable ;

    protected $fillable = [
        'company_name',
        'email',
        'password',
        'phone',
        'address',
        'role',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function reviews()
    {
        return $this->hasMany(PartnerReview::class);
    }

    public function getRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'partner_product');
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
