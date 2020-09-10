<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DB;

class WishListItem extends Model
{
    protected $fillable = [
        'user_id', 'item_id'
    ];

    public function getItemIds() {
        $ids = [];

        if (Auth::check()) {
            $items = DB::table('wish_list_items')->where('user_id', Auth::user()->id)->get();

            foreach ($items as $item)
                $ids[] = $item->item_id;
        }

        return $ids;
    }
}
