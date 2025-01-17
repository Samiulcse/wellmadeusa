<?php

namespace App\Model;

use App\Enumeration\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MetaBuyer extends Model
{
    use SoftDeletes;

    protected $table = 'meta_buyers';

    protected $fillable = [
        'verified', 'avatar', 'active', 'block', 'company_name', 'primary_customer_market', 'seller_permit_number', 'sell_online', 'website',
        'attention', 'billing_location', 'billing_address', 'billing_unit', 'billing_city', 'billing_state_id', 'billing_state',
        'billing_zip', 'billing_country_id', 'billing_phone', 'billing_fax', 'billing_commercial', 'hear_about_us',
        'hear_about_us_other', 'receive_offers', 'mailing_list' , 'user_id', 'ein_path', 'sales1_path', 'sales2_path', 'min_order'
    ];

    public function user() {
        return $this->hasOne('App\Model\User', 'buyer_meta_id');
    }

    public function billingState() {
        return $this->belongsTo('App\Model\State', 'billing_state_id');
    }

    public function billingCountry() {
        return $this->belongsTo('App\Model\Country', 'billing_country_id');
    }

    public function orders() {
        return $this->hasManyThrough('App\Model\Order', 'App\Model\User', 'id', 'user_id', 'user_id')
            ->where('status', '!=', OrderStatus::$INIT);
    }

    public function login() {
        return $this->hasManyThrough('App\Model\LoginHistory', 'App\Model\User', 'id', 'user_id', 'user_id');
    }
    
    public function userLastLogin() {
        return $this->hasOne('App\Model\LoginHistory', 'user_id', 'user_id')->orderBy('created_at', 'DESC');
    }
    
    public function state() {
        return $this->belongsTo('App\Model\State', 'billing_state_id');
    }

    public function country() {
        return $this->belongsTo('App\Model\Country', 'billing_country_id');
    }

    public static function avatar_path()
    {
        return 'images/avatar/';
    }
}
