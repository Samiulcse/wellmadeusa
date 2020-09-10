<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromoCodes extends Model
{
    protected $table = 'promo_codes';

    protected $fillable = [
         'name','amount','type','description','credit','status'
    ];
}
