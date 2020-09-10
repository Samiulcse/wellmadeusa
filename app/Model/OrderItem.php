<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';

    protected $fillable = [
        'order_id', 'item_id', 'color', 'size', 'item_per_pack', 'pack', 'qty', 'total_qty', 'per_unit_price', 'amount',
        'style_no', 'status','dispatch'
    ];

    public function item() {
        return $this->belongsTo('App\Model\Item')->withTrashed();
    }

    public function order() {
        return $this->belongsTo('App\Model\Order');
    }
    public function ItemInv() { 
        return $this->hasMany('App\Model\ItemInv','item_id','item_id');
    }
}
