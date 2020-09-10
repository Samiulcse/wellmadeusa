<?php

namespace App\Model;

use App\Enumeration\Providers;
use Illuminate\Database\Eloquent\Model;

class ItemInv extends Model
{
   protected $table = 'item_inv';

   public function images() {
        return $this->hasMany('App\Model\ItemImages', 'item_id', 'item_id');
   }

    
}
