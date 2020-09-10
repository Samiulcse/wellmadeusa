<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BuyerBillingAddress extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'default', 'store_no', 'billing_location', 'billing_address', 'billing_unit', 'billing_city', 'billing_state_id', 'billing_state', 'billing_zip', 'billing_country_id',
        'billing_phone', 'billing_fax', 'billing_commercial'
    ];

    public function state() {
        return $this->belongsTo('App\Model\State', 'billing_state_id');
    }

    public function country() {
        return $this->belongsTo('App\Model\Country', 'billing_country_id');
    }
}
