<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'order_id', 'meta_vendor_id', 'user_id', 'star', 'review', 'reply'
    ];

    public function user() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

    public function order() {
        return $this->belongsTo('App\Model\Order', 'order_id');
    }
}
