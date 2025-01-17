<?php

namespace App\Model;

use App\Enumeration\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'status', 'user_id', 'order_number', 'shipping_method_id', 'shipping_address', 'shipping_city', 'shipping_state',
        'shipping_zip', 'shipping_country', 'shipping_phone', 'billing_address', 'billing_city', 'billing_state', 'billing_zip',
        'billing_country', 'billing_phone', 'card_number', 'card_full_name', 'card_expire', 'card_cvc', 'subtotal',
        'discount', 'shipping_cost', 'total', 'shipping_location', 'shipping_state_id', 'shipping_state_text', 'billing_location',
        'billing_state_id', 'billing_state_text', 'shipping_country_id', 'billing_country_id', 'tracking_number',
        'company_name', 'email', 'name', 'shipping', 'note','admin_note', 'shipping_address_id', 'coupon', 'coupon_type', 'coupon_amount',
        'coupon_description','promo_ammount','promo_type','promo_percent', 'store_credit', 'authorize_info', 'payment_type', 'can_call', 'paypal_payment_id', 'paypal_token',
        'paypal_payer_id', 'shipping_unit', 'billing_unit', 'confirm_at', 'partially_shipped_at', 'fully_shipped_at',
        'back_ordered_at', 'cancel_at', 'return_at', 'invoice_number', 'updated_by', 'rejected','default_coupon_id',   'free_shipping', 'promotion_details'
    ];

    public function user() {
        return $this->belongsTo('App\Model\User')->with('buyer');
    }

    public function items() {
        return $this->hasMany('App\Model\OrderItem')->with('item','ItemInv');
    }

    public function shippingMethod() {
        return $this->belongsTo('App\Model\AdminShipMethod')->with('courier');
    }

    public function review() {
        return $this->hasMany('App\Model\Review', 'order_id', 'id');
    }

    public function storeCreditTransections() {
        return $this->hasMany('App\Model\StoreCreditTransection', 'order_id', 'id');
    }

    public function notifications() {
        return $this->hasMany('App\Model\Notification', 'order_id', 'id');
    }

    public function updatedBy() {
        return $this->belongsTo('App\Model\User', 'updated_by');
    }
    
    public function authorizeHistory() {
        return $this->hasMany('App\Model\AuthorizeLog','order_id','id');
    }
    
    public function visitors(){
        return $this->hasMany('App\Model\Visitor', 'user_id', 'user_id');
    }

    public function statusText() {
        if ($this->status == OrderStatus::$NEW_ORDER)
            return 'New Order';
        elseif ($this->status == OrderStatus::$CONFIRM_ORDER)
            return 'Confirmed Orders';
        elseif ($this->status == OrderStatus::$PARTIALLY_SHIPPED_ORDER)
            return 'Partially Shipped Orders';
        elseif ($this->status == OrderStatus::$FULLY_SHIPPED_ORDER)
            return 'Fully Shipped Orders';
        elseif ($this->status == OrderStatus::$BACK_ORDER) {
            if ($this->rejected == 0)
                return 'Back Ordered (Pending)';
            else if ($this->rejected == 1)
                return 'Back Ordered (Rejected)';
            else
                return 'Back Ordered';
        } elseif ($this->status == OrderStatus::$CANCEL_BY_BUYER)
            return 'Cancelled by Buyer';
        elseif ($this->status == OrderStatus::$CANCEL_BY_VENDOR)
            return 'Cancelled by Vendor';
        elseif ($this->status == OrderStatus::$CANCEL_BY_AGREEMENT)
            return 'Cancelled by Agreement';
        elseif ($this->status == OrderStatus::$RETURNED)
            return 'Returned';
    }
}
