<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    //
    protected $table = 'tasks';
    use SoftDeletes;
    protected $fillable = ['name', 'start_date','end_date','desc','color','lable_bg'];
}
