<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\User;

class CartItem extends Model
{
    protected $fillable = [
        'user_id', 'item_id', 'color_id', 'quantity','status'
    ];

    public function item() {
        return $this->belongsTo('App\Model\Item')->with('images', 'vendor');
    }

    public function color() {
        return $this->belongsTo('App\Model\Color');
    }
    public function inventory(){
        return $this->hasMany('App\Model\ItemInv','item_id','item_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Model\User');
    }
}
