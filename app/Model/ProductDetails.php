<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDetails extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'material_desc','status'
    ];
}
