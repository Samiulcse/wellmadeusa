<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SliderItem extends Model
{
    protected $fillable = [
        'item_id', 'sort', 'type'
    ];

    public function item() {
        return $this->belongsTo('App\Model\Item')->with('images');
    }
}
