<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TopBanner extends Model
{
    protected $table = 'top_banners';

    protected $fillable = [
        'page', 'title', 'description', 'category_id', 'url', 'image_path'
    ];

 	public function category() {
     return $this->belongsTo('App\Model\Category', 'category_id');
 	}
 	
}
