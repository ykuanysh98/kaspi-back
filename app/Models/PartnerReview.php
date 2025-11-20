<?php

namespace App\Models;

use App\Models\User;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Model;


class PartnerReview extends Model
{
    protected $fillable = [
        'partner_id',
        'user_id',
        'rating',
        'comment'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
