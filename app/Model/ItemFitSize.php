<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemFitSize extends Model
{
//    use SoftDeletes;

    protected $table = 'item_fit_size';

    protected $fillable = [
        'text'
    ];


}
