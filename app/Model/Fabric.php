<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fabric extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'status', 'default', 'master_fabric_id'
    ];

    public function masterFabric() {
        return $this->belongsTo('App\Model\MasterFabric');
    }
}
