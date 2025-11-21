<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerProduct extends Model
{
    use HasFactory;

    protected $table = 'partner_product';

    protected $fillable = [
        'product_id',
        'partner_id',
        'price'
    ];
}
