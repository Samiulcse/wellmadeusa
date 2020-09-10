<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'status', 'style_no','brand', 'price', 'orig_price', 'pack_id', 'sorting', 'description', 'guest_image', 'available_on','fit_size',
        'availability', 'name', 'slug', 'default_parent_category', 'default_second_category', 'default_third_category', 'exclusive','default',
        'min_qty', 'made_in_id','material_one','material_two','material_three','material_four','material_five', 'labeled', 'memo', 'activated_at', 'fabric', 'video', 'youtube_url', 'min_order'
    ];

    public function colors() {
        return $this->belongsToMany('App\Model\Color')->distinct('item_id')->withPivot('available');
    }



    public function category() {
        return $this->belongsTo('App\Model\Category', 'default_parent_category', 'id');
    }

    public function images() {
        return $this->hasMany('App\Model\ItemImages')->orderBy('sort');
    }

    public function itemcategory() {
        return $this->belongsTo('App\Model\Category','default_parent_category','id');
    }

    public function vendor() {
        return $this->belongsTo('App\Model\MetaVendor', 'vendor_meta_id', 'id');
    }

    public function fabric() {
        return $this->belongsTo('App\Model\MasterFabric', 'fabric', 'id');
    }

    public function pack() {
        return $this->belongsTo('App\Model\Pack');
    }

    public function pack_filter() {
        return $this->belongsTo('App\Model\Pack','pack_id','id');
    }

    public function madeInCountry() {
        return $this->belongsTo('App\Model\MadeInCountry', 'made_in_id');
    }

    public function bodySize() {
        return $this->belongsTo('App\Model\BodySize', 'body_size_id');
    }

    public function pattern() {
        return $this->belongsTo('App\Model\Pattern', 'pattern_id');
    }

    public function length() {
        return $this->belongsTo('App\Model\Length', 'length_id');
    }

    public function style() {
        return $this->belongsTo('App\Model\Style', 'style_id');
    }

    public function carts() {
        return $this->hasMany('App\Model\CartItem','item_id', 'id');
    }

    public function categories() {
        return $this->belongsToMany('App\Model\Category');
    }

    protected static function boot() {
        parent::boot();
        static::deleting(function(Item $item) {
            // foreach ($item->images as $image)
            //     $image->delete();
        });
    }

    public function orders() {
        return $this->hasMany('App\Model\OrderItem','item_id','id')->select('id','item_id');
    }
}
