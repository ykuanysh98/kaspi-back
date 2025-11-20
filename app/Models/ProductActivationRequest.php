<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductActivationRequest extends Model
{
    protected $fillable = [
        'product_id',
        'partner_id',
        'status'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function partner()
    {
        return $this->belongsTo(User::class);
    }
}
