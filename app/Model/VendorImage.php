<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorImage extends Model
{
    protected $fillable = [
        'type', 'image_path', 'status','item_id','slug', 'sort', 'url', 'head', 'color','details','cat_id'
    ];

   public function item() {
        return $this->hasOne('App\Model\Item', 'id', 'item_id');
   }
   public function category() {
        return $this->hasOne('App\Model\category', 'id', 'cat_id');
    }

   public function item_image() {
        return $this->hasMany('App\Model\ItemImages', 'item_id', 'url')->orderBy('sort','asc');
   }
}
