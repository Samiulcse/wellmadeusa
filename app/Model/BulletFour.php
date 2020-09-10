<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BulletFour extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bullet_four_desc','status'
    ];
}
