<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterFabric extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name'
    ];
}
