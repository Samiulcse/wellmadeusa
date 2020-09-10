<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemCategory extends Model
{
    use SoftDeletes;

    protected $table = 'item_category';

    protected $fillable = [
        'item_id', 'default_parent_category', 'default_second_category', 'default_third_category'
    ];

    public function parent_category () {
        return $this->belongsTo('App\Model\Category', 'default_parent_category');
    }
}
