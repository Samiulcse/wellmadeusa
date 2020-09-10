<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'user_id', 'name', 'type', 'amount', 'multiple_use', 'description'
    ];
}
