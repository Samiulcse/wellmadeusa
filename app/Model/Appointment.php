<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;  
class Appointment extends Model
{
    protected $table = 'appointments';  
    protected $fillable = ['name', 'user_id','start_date','end_date','desc','color','lable_bg'];
    
    public function user() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }
}
