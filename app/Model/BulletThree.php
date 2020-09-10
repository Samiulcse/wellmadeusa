<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BulletThree extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bullet_three_desc','status'
    ];
}
