<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MasterColor extends Model
{
    protected $fillable = [
        'name', 'image_path'
    ];
}
