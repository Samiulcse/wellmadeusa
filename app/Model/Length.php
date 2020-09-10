<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Length extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'sub_category_id'
    ];

    public function category() {
        return $this->belongsTo('App\Model\Category', 'sub_category_id')->with('parentCategory');
    }
}
