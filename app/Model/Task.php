<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    protected $table = 'tasks';
    use SoftDeletes;
    protected $fillable = ['name', 'description', 'task_date'];
}
