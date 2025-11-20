<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerJoinRequest extends Model
{
    protected $fillable = ['partner_id', 'product_id', 'status'];

    public function partner() {
        return $this->belongsTo(Partner::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
