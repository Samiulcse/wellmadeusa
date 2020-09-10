<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;  
class AppointmentTime extends Model
{
    protected $table = 'appointment_times';  
    protected $fillable = ['time', 'ampm','note'];
}
