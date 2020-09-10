<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BulletTwo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bullet_two_desc','status'
    ];
}
