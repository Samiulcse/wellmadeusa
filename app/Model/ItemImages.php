<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use File;

class ItemImages extends Model
{
    protected $fillable = [
        'item_id', 'image_path', 'color_id', 'sort', 'list_image_path', 'thumbs_image_path','compressed_image_path'
    ];

    public function color() {
        return $this->belongsTo('App\Model\Color', 'color_id');
    }

    public function ItemInv() {
        return $this->hasMany('App\Model\ItemInv', 'item_id', 'item_id');
    }

    protected static function boot() {
        parent::boot();
        static::deleting(function(ItemImages $image) {
            if ($image->image_path != null && File::exists($image->image_path))
                File::delete(public_path($image->image_path));

            if ($image->list_image_path != null && File::exists($image->list_image_path))
                File::delete(public_path($image->list_image_path));

            if ($image->thumbs_image_path != null && File::exists($image->thumbs_image_path))
                File::delete(public_path($image->thumbs_image_path));
            
            if ($image->compressed_image_path != null && File::exists($image->compressed_image_path))
                File::delete(public_path($image->compressed_image_path));
        });
    }
}
