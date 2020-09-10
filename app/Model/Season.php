<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model; 

class Season extends Model
{
    //
    protected $table = 'season'; 
    protected $fillable = ['name', 'description','default'];
}
