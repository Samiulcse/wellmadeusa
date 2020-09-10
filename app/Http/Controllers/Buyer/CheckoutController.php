<?php

namespace App\Http\Controllers\Buyer;

use App\Enumeration\CouponType;
use App\Enumeration\OrderStatus;
use App\Enumeration\Role;
use App\Enumeration\VendorImageType;
use App\Model\AdminShipMethod;
use App\Model\AuthorizeLog;
use App\Model\BuyerShippingAddress;
use App\Model\BuyerBillingAddress;
use App\Model\CartItem;
use App\Model\Country;
use App\Model\Color;
use App\Model\Coupon;
use App\Model\MetaBuyer;
use App\Model\PointSystem;
use App\Model\MetaVendor;
use App\Model\Order;
use App\Model\ItemInv;
use App\Model\OrderItem;
use App\Model\Promotion;
use App\Model\ShippingMethod;
use App\Model\PromoCodes;
use App\Model\State;
use App\Model\StoreCredit;
use App\Model\User;
use App\Model\VendorImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Encryption\DecryptException;
use CreditCard;
use Illuminate\Support\Facades\DB;
use Validator;
use Mail;
use PDF;
use Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
// payment
use Omnipay\Omnipay;
use Omnipay\Common\CreditCard as NewCreditCard;

// PayPal
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\ShippingAddress;

use Stripe\Stripe;

// For Authorize Checkout
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Log;
use App\Mail\WelcomeMail;

class CheckoutController extends Controller
{
    public function create(Request $request) {
        $user = Auth::user();
        $user->load('buyer');

        $meta_vendor = MetaVendor::where('verified', 1)->where('active', 1)->get()->first();
        $minimum_order_amount = ( isset($meta_vendor->min_order) && $meta_vendor->min_order != '' ) ? $meta_vendor->min_order : 0;

        $storeCredit = 0;

        if ($request->storeCredit && is_numeric($request->storeCredit)) {
            $sc = StoreCredit::where('user_id', Auth::user()->id)->first();

            if (!$sc) {
                return response()->json(['success' => false, 'message' => 'Insufficient Store Credit.']);
            }

            if ($sc->amount < $request->storeCredit) {

                return response()->json(['success' => false, 'message' => 'Insufficient Store Credit.']);

            } else {

                $storeCredit = (float) $request->storeCredit;

            }

        }

        $order = Order::create([
            'status' => OrderStatus::$INIT,
            'user_id' => $user->id,
            'name' => $user->first_name.' '.$user->last_name,
            'company_name' => $user->buyer->company_name,
            'email' => $user->email,
            'billing_location' => $user->buyer->billing_location,
            'billing_address' => $user->buyer->billing_address,
            'billing_unit' => $user->buyer->billing_unit,
            'billing_city' => $user->buyer->billing_city,
            'billing_state' => ($user->buyer->billingState == null) ? $user->buyer->billing_state : $user->buyer->billingState->code,
            'billing_state_id' => $user->buyer->billing_state_id,
            'billing_state_text' => $user->buyer->billing_state,
            'billing_zip' => $user->buyer->billing_zip,
            'billing_country' => $user->buyer->billingCountry['name'],
            'billing_country_id' => $user->buyer->billing_country_id,
            'billing_phone' => $user->buyer->billing_phone,
        ]);

        // calculate total amount
        $cartItems = CartItem::where('user_id', $order->user_id)->get();
        $subTotal = 0;
        foreach ($cartItems as $cartItem) {
            $sizes = explode("-", $cartItem->item->pack->name);
            $pack = '';
            $itemInPack = 0;

            for ($i = 1; $i <= sizeof($sizes); $i++) {
                $var = 'pack' . $i;

                if ($cartItem->item->pack->$var != null) {
                    $pack .= $cartItem->item->pack->$var * $cartItem->quantity . '-';
                    $itemInPack += (int)$cartItem->item->pack->$var;
                } else {
                    $pack .= '0-';
                }
            }

            $subTotal += $itemInPack * $cartItem->quantity * $cartItem->item->price;
        }

        if ( $minimum_order_amount > $subTotal ) {
            return response()->json(['success' => false, 'message' => 'Minimum order amount is : ' . $minimum_order_amount]);
        }

        // Cart Items
        $cartItems = CartItem::where('user_id', $order->user_id)->get();

        $subTotal = 0;
        $orderNumber = $this->generateRandomString(13);

        foreach ($cartItems as $cartItem) {
            $sizes = explode("-", $cartItem->item->pack->name);
            $pack = '';

            $itemInPack = 0;

            for ($i = 1; $i <= sizeof($sizes); $i++) {
                $var = 'pack' . $i;

                if ($cartItem->item->pack->$var != null) {
                    $pack .= $cartItem->item->pack->$var * $cartItem->quantity . '-' ;
                    $itemInPack += (int)$cartItem->item->pack->$var;
                } else {
                    $pack .= '0-';
                }
            }

            $pack = rtrim($pack, "-");
            $subTotal += $itemInPack * $cartItem->quantity * $cartItem->item->price;

            OrderItem::create([
                'order_id' => $order->id,
                'item_id' => $cartItem->item_id,
                'style_no' => $cartItem->item->style_no,
                'color' => $cartItem->color->name,
                'size' => $cartItem->item->pack->name,
                'item_per_pack' => $itemInPack,
                'pack' => $pack,
                'qty' => $cartItem->quantity,
                'total_qty' => $itemInPack * $cartItem->quantity,
                'per_unit_price' => $cartItem->item->price,
                'amount' => number_format($itemInPack * $cartItem->quantity * $cartItem->item->price, 2, '.', ''),
            ]);
        }

        $order = $this->getMaximumDiscount($order->id, $subTotal);
        $orderNumber = $this->generateRandomString(13);
        $order->order_number = $orderNumber;
        $order->shipping_cost = 0;
        $order->total = number_format($subTotal - $storeCredit - $order->discount, 2, '.', '');

        if ($storeCredit > $subTotal)
            $storeCredit = $subTotal;

        $order->store_credit = $storeCredit;

        $order->save();

        return response()->json(['success' => true, 'message' => encrypt($order->id)]);
    }

    public function getMaximumDiscount($orderId, $subTotal) {

        $maximum_discount = 0;
        $optimizez_order = null;

        $coupons = Promotion::where('hasCouponCode', 0)->where('status', 1)->get();


        if(count($coupons) == 0) {
            $order = Order::where('id', $orderId)->first();
            $order->free_shipping = 0;
            $order->subtotal = number_format($subTotal, 2, '.', '');
            $order->discount = 0.00;
            $order->promotion_details = '';
            $order->shipping_cost = 0.00;
            return $order;
        }


        foreach($coupons as $coupon) {

            $order = Order::where('id', $orderId)->first();

            $discountflash = 0.00;
            $discount = 0;

            $coupon_details = '';
            $free_shipping = 0;

            $valid = 1;

            if($coupon->is_permanent == 0) {


                $startDate = strtotime($coupon->valid_from);
                $endDate = strtotime($coupon->valid_to);

                $currentTime = time();

                if($currentTime > $startDate && $currentTime < $endDate) {

                } else {

                    $valid = 0;

                }

            }

            $alreadyUsed = 0;

            if ($coupon->multiple_use == 0) {

                $previous = Order::where('user_id', Auth::user()->id)
                    ->where('status', '!=', OrderStatus::$INIT)
                    ->where('default_coupon_id', $coupon->id)
                    ->first();

                if ($previous) {

                    $alreadyUsed = 1;

                }
            }

            if($valid == 1 && $alreadyUsed == 0) {

                if($coupon->to_price_1) {
                } else {
                    $coupon->to_price_1 = 1000000;
                }

                if($subTotal >= $coupon->from_price_1 && $subTotal <= $coupon->to_price_1) {

                    if($coupon->promotion_type == 'Percentage discount by order amount') {

                        $discount = number_format($discountflash + ($coupon->percentage_discount_1 / 100) * $subTotal, 2, '.', '');
                        $coupon_details = '["' . $coupon->title . '" - ' . $coupon->percentage_discount_1 . '%]';

                    } else {

                        $discount = number_format($discountflash + $coupon->unit_price_discount_1,2, '.', '');
                        $coupon_details = '["' . $coupon->title . '" - $' . $coupon->unit_price_discount_1 . ']';

                    }

                    if($coupon->free_shipping_1 == 1) {

                        $free_shipping = 1;

                    }

                } else {

                    if($coupon->to_price_2) {
                    } else {
                        $coupon->to_price_2 = 1000000;
                    }

                    if($subTotal >= $coupon->from_price_2 && $subTotal <= $coupon->to_price_2) {

                        if($coupon->promotion_type == 'Percentage discount by order amount') {

                            $discount = number_format($discountflash + ($coupon->percentage_discount_2 / 100) * $subTotal, 2, '.', '');
                            $coupon_details = '["' . $coupon->title . '" - ' . $coupon->percentage_discount_2 . '%]';

                        } else {

                            $discount = number_format($discountflash + $coupon->unit_price_discount_2, 2, '.', '');
                            $coupon_details = '["' . $coupon->title . '" - $' . $coupon->unit_price_discount_2 . ']';

                        }

                        if($coupon->free_shipping_2 == 1) {

                            $free_shipping = 1;

                        }

                    } else {

                        if($coupon->to_price_3) {
                        } else {
                            $coupon->to_price_3 = 1000000;
                        }

                        if($subTotal >= $coupon->from_price_3 && $subTotal <= $coupon->to_price_3) {

                            if($coupon->promotion_type == 'Percentage discount by order amount') {

                                $discount = number_format($discountflash + ($coupon->percentage_discount_3 / 100) * $subTotal, 2, '.', '');
                                $coupon_details = '["' . $coupon->title . '" - ' . $coupon->percentage_discount_3 . '%]';

                            } else {

                                $discount = number_format($discountflash + $coupon->unit_price_discount_3, 2, '.', '');
                                $coupon_details = '["' . $coupon->title . '" - $' . $coupon->unit_price_discount_3 . ']';

                            }

                            if($coupon->free_shipping_3 == 1) {

                                $free_shipping = 1;

                            }

                        } else {

                            if($coupon->to_price_4) {
                            } else {
                                $coupon->to_price_4 = 1000000;
                            }

                            if($subTotal >= $coupon->from_price_4 && $subTotal <= $coupon->to_price_4) {

                                if($coupon->promotion_type == 'Percentage discount by order amount') {

                                    $discount = number_format($discountflash + ($coupon->percentage_discount_4 / 100) * $subTotal, 2, '.', '');
                                    $coupon_details = '["' . $coupon->title . '" - ' . $coupon->percentage_discount_4 . '%]';

                                } else {

                                    $discount = number_format($discountflash + $coupon->unit_price_discount_4, 2, '.', '');
                                    $coupon_details = '["' . $coupon->title . '" - $' . $coupon->unit_price_discount_4. ']';

                                }

                                if($coupon->free_shipping_4 == 1) {

                                    $free_shipping = 1;

                                }

                            } else {

                                if($coupon->to_price_5) {
                                } else {
                                    $coupon->to_price_5 = 1000000;
                                }

                                if($subTotal >= $coupon->from_price_5 && $subTotal <= $coupon->to_price_5) {

                                    if($coupon->promotion_type == 'Percentage discount by order amount') {

                                        $discount = number_format($discountflash + ($coupon->percentage_discount_5 / 100) * $subTotal, 2, '.', '');
                                        $coupon_details = '["' . $coupon->title . '" - ' . $coupon->percentage_discount_5 . '%]';

                                    } else {

                                        $discount = number_format($discountflash + $coupon->unit_price_discount_5, 2, '.', '');
                                        $coupon_details = '["' . $coupon->title . '" - $' . $coupon->unit_price_discount_5 . ']';

                                    }

                                    if($coupon->free_shipping_5 == 1) {

                                        $free_shipping = 1;

                                    }

                                }

                            }
                        }
                    }

                }

                $order->default_coupon_id = $coupon->id;
                $order->free_shipping = $free_shipping;

            }

            $order->subtotal = number_format($subTotal, 2, '.', '');
            $order->discount = $discount;
            $order->promotion_details = $coupon_details;
            $order->shipping_cost = 0;

            if($optimizez_order == null) {
                $optimizez_order = $order;
            }

            if($discount > $maximum_discount) {

                $maximum_discount = $discount;
                $optimizez_order = $order;

            }

        }

        return $optimizez_order;

    }

    public function generateRandomString($length) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function index(Request $request) {
        $user = Auth::user();
        $user->load('buyer');

        // Check Orders
        $id = '';

        try {
            $id = decrypt($request->id);
        } catch (DecryptException $e) {

        }

        if ($id == '')
            abort(404);

        $order = Order::where('id', $id)->first();

        $countries = Country::orderBy('name')->get();
        $usStates = State::where('country_id', 1)->orderBy('name')->get()->toArray();
        $caStates =State::where('country_id', 2)->orderBy('name')->get()->toArray();

        if ($order->shipping_address_id == null)
            $address = BuyerShippingAddress::where('user_id', Auth::user()->id)->where('default', 1)->first();
        else
            $address = BuyerShippingAddress::where('id', $order->shipping_address_id)->first();

        $shippingAddresses = BuyerShippingAddress::where('user_id', Auth::user()->id)->get();

        return view('buyer.checkout.index', compact('user', 'countries', 'usStates', 'caStates',
            'order', 'address', 'shippingAddresses'))
            ->with('page_title', 'Checkout');
    }

    public function addressPost(Request $request) {
        // Check Orders
        $id = '';

        try {
            $id = decrypt($request->orders);
        } catch (DecryptException $e) {

        }

        if ($id == '')
            abort(404);

        $shippingAddress = BuyerShippingAddress::where('id', $request->address_id)->with('state', 'country')->first();

        Order::where('id', $id)->update([
            'shipping_address_id' => $request->address_id,
            'shipping_location' => $shippingAddress->location,
            'shipping_address' => $shippingAddress->address,
            'shipping_city' => $shippingAddress->city,
            'shipping_state' => ($shippingAddress->state == null) ? $shippingAddress->state_text : $shippingAddress->state->name,
            'shipping_state_id' => $shippingAddress->state_id,
            'shipping_state_text' => $shippingAddress->state_text,
            'shipping_zip' => $shippingAddress->zipCode,
            'shipping_country' => $shippingAddress->country->name,
            'shipping_country_id' => $shippingAddress->country->id,
            'shipping_phone' => $shippingAddress->phone,
        ]);

        return redirect()->route('show_shipping_method', ['id' => $request->orders]);
    }

    public function shipping(Request $request) {
        // Check Orders
        $id = '';

        try {
            $id = decrypt($request->id);
        } catch (DecryptException $e) {

        }

        if ($id == '')
            abort(404);

        $order = Order::where('id', $id)->first();

        $shipping_methods = AdminShipMethod::with('courier')->get();

       AuthorizeNet::authorizeCreditCard(10,true);

        return view('buyer.checkout.shipping', compact( 'order', 'shipping_methods'))->with('page_title', 'Checkout');
    }

    public function shippingPost(Request $request) {
        // Check orders
        $id = [];

        try {
            $id = decrypt($request->orderIds);
        } catch (DecryptException $e) {

        }

        if ($id == '')
            abort(404);

        $request->validate([
            'shipping_method' => 'required'
        ]);


        $shippingMethod = AdminShipMethod::where('id', $request->shipping_method)->with('courier')->first();

        $order = Order::where('id', $id)->first();

        $order->shipping = $shippingMethod->name;
        $order->shipping_method_id = $shippingMethod->id;
        $order->shipping_cost = $shippingMethod->fee;
        $order->total = $order->total + $shippingMethod->fee;
        $order->save();

        return redirect()->route('show_payment', ['id' => $request->orderIds]);
    }

    public function payment(Request $request) {
        // Check orders
        $id = '';

        try {
            $id = decrypt($request->id);
        } catch (DecryptException $e) {

        }

        if ($id == '')
            abort(404);

        $order = Order::where('id', $id)->first();

        // Decrypt
        $cardNumber = '';
        $cardFullName = '';
        $cardExpire = '';
        $cardCvc = '';

        try {
            $cardNumber = decrypt($order->card_number);
            $cardFullName = decrypt($order->card_full_name);
            $cardExpire = decrypt($order->card_expire);
            $cardCvc = decrypt($order->card_cvc);
        } catch (DecryptException $e) {

        }

        $order->card_number = $cardNumber;
        $order->card_full_name = $cardFullName;
        $order->card_expire = $cardExpire;
        $order->card_cvc = $cardCvc;

        return view('buyer.checkout.payment', compact('user', 'order'))->with('page_title', 'Checkout');
    }

    public function paymentPost(Request $request) {
        $validator = Validator::make($request->all(), [
            'number' => 'required|max:191|min:6',
            'name' => 'required|max:191',
            'expiry' => 'required|date_format:"m/y"',
            'cvc' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validator->after(function ($validator) use($request) {
            // Card Number Check
            $card = CreditCard::validCreditCard($request->number);

            if (!$card['valid'])
                $validator->errors()->add('number', 'Invalid Card Number');

            // CVC Check
            $validCvc = CreditCard::validCvc($request->cvc, $card['type']);
            if (!$validCvc)
                $validator->errors()->add('cvc', 'Invalid CVC');

            // Expiry Check
            $tmp  = explode('/', $request->expiry);
            $validDate = CreditCard::validDate('20'.$tmp[1], $tmp[0]);
            if (!$validDate)
                $validator->errors()->add('expiry', 'Invalid Expiry');
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check orders
        $id = '';

        try {
            $id = decrypt($request->id);
        } catch (DecryptException $e) {

        }

        if ($id == '')
            abort(404);

        Order::where('id', $id)->update([
            'card_number' => encrypt($request->number),
            'card_full_name' => encrypt($request->name),
            'card_expire' => encrypt($request->expiry),
            'card_cvc' => encrypt($request->cvc),
        ]);

     AuthorizeNet::authorizeCreditCard(10,true);

        return redirect()->route('show_checkout_review', ['id' => $request->id]);
    }

    public function review(Request $request) {
        // Check orders
        $id = '';

        try {
            $id = decrypt($request->id);
        } catch (DecryptException $e) {

        }

        if ($id == '')
            abort(404);


        $order = Order::where('id', $id)->with('user')->first();

        $temp = [];
        $cartItems = [];

        $cartObjs = CartItem::where('user_id', $order->user_id)
            ->with('item', 'color')
            ->get()
            ->sortBy(function ($useritem, $key) {
                return $useritem->item->vendor_meta_id;
            });

        foreach ($cartObjs as $obj) {
            $temp[$obj->item->id][] = $obj;
        }

        $itemCounter = 0;
        foreach ($temp as $itemId => $item) {
            $cartItems[$itemCounter] = $item;
            $itemCounter++;
        }

        $order->cartItems = $cartItems;
        $orders[] = $order;

        // Decrypt
        $cardNumber = '';
        $cardFullName = '';
        $cardExpire = '';
        $cardCvc = '';

        try {
            $cardNumber = decrypt($order->card_number);
            $cardNumber = str_repeat("*", (strlen($cardNumber) - 4)).substr($cardNumber,-4,4);
            $cardFullName = decrypt($order->card_full_name);
            $cardExpire = decrypt($order->card_expire);
            $cardCvc = decrypt($order->card_cvc);
        } catch (DecryptException $e) {

        }

        $order->card_number = $cardNumber;
        $order->card_full_name = $cardFullName;
        $order->card_expire = $cardExpire;
        $order->card_cvc = $cardCvc;

        return view('buyer.checkout.review', compact('order'))->with('page_title', 'Checkout');
    }

    public function complete(Request $request) {
        // Check orders
        $id = '';

        try {
            $id = decrypt($request->id);
        } catch (DecryptException $e) {

        }

        if ($id == '')
            abort(404);

        $order = Order::where('id', $id)->first();

        if ($order->status == OrderStatus::$INIT) {
            $preOrder = Order::where('order_number', 'like', 'WLM%')
                ->where('order_number', 'not like', '%BO%')
                ->orderBy('created_at', 'desc')
                ->first();

            $orderNumber = "WLM10001";

            if ($preOrder) {
                $tmp = (int) substr($preOrder->order_number, 2);
                $orderNumber = "WLM".($tmp+1);
            }

            $order->order_number = $orderNumber;
            $order->status = OrderStatus::$NEW_ORDER;

            if ($order->payment_type == 'PayPal') {
                $order->paypal_payment_id = $request->paymentId;
                $order->paypal_token = $request->token;
                $order->paypal_payer_id = $request->PayerID;
            }
            //$order->note = $request->note;
            $order->save();
            $user = Auth::user();
            $user->increment('order_count');

            $pdfData = $this->getPdfData($order);

            // Send Mail to Buyer
            Mail::send('emails.buyer.order_confirmation', ['order' => $order], function ($message) use ($order, $pdfData) {
                $message->subject('Order Confirmed');
                $message->to($order->email, $order->name);
                $message->attachData($pdfData, $order->order_number.'.pdf');
            });

            // Send Mail to Vendor
            /*$user = User::where('role', Role::$VENDOR)
                ->where('vendor_meta_id', $order->vendor_meta_id)->first();

            Mail::send('emails.vendor.new_order', ['order' => $order], function ($message) use ($order, $pdfData, $user) {
                $message->subject('New Order - '.$order->order_number);
                $message->to($user->email, $user->first_name.' '.$user->last_name);
                $message->attachData($pdfData, $order->order_number.'.pdf');
            });*/

            CartItem::where([])->delete();
        }

        //return redirect()->route('checkout_complete_view', ['id' => $request->id]);
        return view('buyer.checkout.complete', compact('order'));
    }

    public function completeView(Request $request) {
        // Check orders
        $id = [];

        try {
            $id = decrypt($request->id);
        } catch (DecryptException $e) {

        }

        if ($id == '')
            abort(404);

        $order = Order::where('id', $id)->first();

        return view('buyer.checkout.complete', compact('order'));
    }

    public function addressSelect(Request $request) {
        try {
            $id = decrypt($request->id);
        } catch (DecryptException $e) {
            $id = '';
        }

        Order::where('id', $id)->update(['shipping_address_id' => $request->shippingId]);
    }



    public function getPdfData($order) {
        $allItems = [];
        $order->load('user', 'items');

        // Decrypt
        $cardNumber = '';
        $cardFullName = '';
        $cardExpire = '';
        $cardCvc = '';

        try {
            $cardNumber = decrypt($order->card_number);
            $cardFullName = decrypt($order->card_full_name);
            $cardExpire = decrypt($order->card_expire);
            $cardCvc = decrypt($order->card_cvc);
        } catch (DecryptException $e) {

        }

        $order->card_number = $cardNumber;
        $order->card_full_name = $cardFullName;
        $order->card_expire = $cardExpire;
        $order->card_cvc = $cardCvc;

        foreach($order->items as $item)
            $allItems[$item->item_id][] = $item;

        // Vendor Logo
        $logo_path = '';
        // $vendorLogo = VendorImage::where('status', 1)
        //     ->where('type', VendorImageType::$LOGO)
        //     ->first();

        // if ($vendorLogo)
        //     $logo_path = public_path($vendorLogo->image_path);

        $vendor = MetaVendor::where('id', 1)->first();
        $order->vendor = $vendor;

        $black = DB::table('settings')->where('name', 'logo-black')->first();
            if ($black)
                $logo_path = public_path($black->value);

        $data = [
            'all_items' => [$allItems],
            'orders' => [$order],
            'logo_paths' => [$logo_path],
            'return_policy_description' => $vendor->size_chart
        ];

        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('admin.dashboard.orders.pdf.with_image', $data);
        return $pdf->output();
    }

    //Promo code apply buyer (saif added)
    public function applyCoupon(Request $request) {

        $order = Order::where('id', $request->id)->where('user_id', Auth::user()->id)->first();

        if (!$order)
            return response()->json(['success' => false, 'message' => 'Invalid Order.']);

        $previous_discount = $order->discount;
        $discountFlash = 0.00;
        $coupon = Promotion::where('coupon_code', strtoupper(trim($request->coupon)))->where('status', 1)->first();

        if (!$coupon)
            return response()->json(['success' => false, 'message' => 'Invalid Coupon.']);

        if($order->coupon != null)
            return response()->json(['success' => false, 'message' => 'You have already used "' . $order->coupon .'" in this order.']);

        $previous = Order::where('user_id', Auth::user()->id)
            ->where('id', $request->id)
            ->where('status', 1)
            ->first();

        if ($previous->coupon == strtoupper(trim($request->coupon)))
            return response()->json(['success' => false, 'message' => 'Coupon code has already been used once.']);

        if($coupon->is_permanent == 0) {


            $startDate = strtotime($coupon->valid_from);
            $endDate = strtotime($coupon->valid_to);

            $currentTime = time();

            if($currentTime > $startDate && $currentTime < $endDate) {

            } else {

                return response()->json(['success' => false, 'message' => 'Coupon Expired.']);

            }

        }

        if ($coupon->multiple_use == 0) {
            $previous = Order::where('user_id', Auth::user()->id)
                ->where('status', '!=', OrderStatus::$INIT)
                ->where('coupon', $coupon->coupon_code)
                ->first();

            if ($previous)
                return response()->json(['success' => false, 'message' => 'Coupon code has already been used once.']);
        }

        $subTotal = $order->subtotal;
        $discount = 0;
        $coupon_details = '';
        $free_shipping = 0;

        if($coupon->to_price_1) {
        } else {
            $coupon->to_price_1 = 1000000;
        }

        if($subTotal >= $coupon->from_price_1 && $subTotal <= $coupon->to_price_1) {

            if($coupon->promotion_type == 'Percentage discount by order amount') {

                $discount = number_format($discountFlash + ($coupon->percentage_discount_1 / 100) * $subTotal, 2, '.', '');
                $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_1 . '%]';

            } else {

                $discount = number_format($discountFlash + $coupon->unit_price_discount_1, 2, '.', '');
                $coupon_details = '["' . $coupon->coupon_code . '" - $' . $coupon->unit_price_discount_1 . ']';

            }

            if($coupon->free_shipping_1 == 1) {
                $free_shipping = 1;
            }

        } else {

            if($coupon->to_price_2) {
            } else {
                $coupon->to_price_2 = 1000000;
            }

            if($subTotal >= $coupon->from_price_2 && $subTotal <= $coupon->to_price_2) {

                if($coupon->promotion_type == 'Percentage discount by order amount') {

                    $discount = number_format($discountFlash + ($coupon->percentage_discount_2 / 100) * $subTotal, 2, '.', '');
                    $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_2 . '%]';

                } else {

                    $discount = number_format($discountFlash + $coupon->unit_price_discount_2, 2, '.', '');
                    $coupon_details = '["' . $coupon->coupon_code . '" - $' . $coupon->unit_price_discount_2 . ']';

                }

                if($coupon->free_shipping_2 == 1) {
                    $free_shipping = 1;
                }

            } else {

                if($coupon->to_price_3) {
                } else {
                    $coupon->to_price_3 = 1000000;
                }

                if($subTotal >= $coupon->from_price_3 && $subTotal <= $coupon->to_price_3) {

                    if($coupon->promotion_type == 'Percentage discount by order amount') {

                        $discount = number_format($discountFlash + ($coupon->percentage_discount_3 / 100) * $subTotal, 2, '.', '');
                        $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_3 . '%]';

                    } else {

                        $discount = number_format($discountFlash + $coupon->unit_price_discount_3, 2, '.', '');
                        $coupon_details = '["' . $coupon->coupon_code . '" - $' . $coupon->unit_price_discount_3 . ']';

                    }

                    if($coupon->free_shipping_3 == 1) {
                        $free_shipping = 1;
                    }

                } else {

                    if($coupon->to_price_4) {
                    } else {
                        $coupon->to_price_4 = 1000000;
                    }

                    if($subTotal >= $coupon->from_price_4 && $subTotal <= $coupon->to_price_4) {

                        if($coupon->promotion_type == 'Percentage discount by order amount') {

                            $discount = number_format($discountFlash + ($coupon->percentage_discount_4 / 100) * $subTotal, 2, '.', '');
                            $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_4 . '%]';

                        } else {

                            $discount = number_format($discountFlash + $coupon->unit_price_discount_4, 2, '.', '');
                            $coupon_details = '["' . $coupon->coupon_code . '" - $' . $coupon->unit_price_discount_4 . ']';

                        }

                        if($coupon->free_shipping_4 == 1) {
                            $free_shipping = 1;
                        }

                    } else {

                        if($coupon->to_price_5) {
                        } else {
                            $coupon->to_price_5 = 1000000;
                        }

                        if($subTotal >= $coupon->from_price_5 && $subTotal <= $coupon->to_price_5) {

                            if($coupon->promotion_type == 'Percentage discount by order amount') {

                                $discount = number_format($discountFlash + ($coupon->percentage_discount_5 / 100) * $subTotal, 2, '.', '');
                                $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_5 . '%]';

                            } else {

                                $discount = number_format($discountFlash + $coupon->unit_price_discount_5, 2, '.', '');
                                $coupon_details = '["' . $coupon->coupon_code . '" - $' . $coupon->unit_price_discount_5 . ']';

                            }

                            if($coupon->free_shipping_5 == 1) {
                                $free_shipping = 1;
                            }

                        }

                    }
                }
            }

        }

        if($discount > $previous_discount) {

            $order->discount = $discount;
            $order->total = number_format($subTotal - $order->store_credit - $discount, 2, '.', '');
            $order->coupon = $coupon->coupon_code;
            $order->default_coupon_id = null;
            $order->promotion_details = $coupon_details;
            $order->free_shipping = $free_shipping;

            $order->save();

            return response()->json(['success' => true, 'message' => 'Success.', 'discount' => $order->discount, 'total' => $order->total, 'free_shipping' => $order->free_shipping]);

        } else {

            return response()->json(['success' => false, 'message' => 'You can use only one promotion in a single order.']);

        }

    }

    public function singlePageCheckout(Request $request) {

        $user = Auth::user();
        $user->load('buyer');

        // Check Orders
        $id = '';

        try {
            $id = decrypt($request->id);
        } catch (DecryptException $e) {

        }

        if ($id == '')
            abort(404);

        $order = Order::where('id', $id)->first();


        $countries = Country::orderBy('name')->get();
        $usStates = State::where('country_id', 1)->orderBy('name')->get()->toArray();
        $caStates =State::where('country_id', 2)->orderBy('name')->get()->toArray();

        if ($order->shipping_address_id == null)
            $address = BuyerShippingAddress::where('user_id', Auth::user()->id)->where('default', 1)->first();
        else
            $address = BuyerShippingAddress::where('id', $order->shipping_address_id)->first();

        $shippingAddresses = BuyerShippingAddress::where('user_id', Auth::user()->id)->with('state', 'country')->get();
        $shipping_methods = AdminShipMethod::with('courier')->get();
        // Vendor Logo
        $logo_path = '';
        $black = DB::table('settings')->where('name', 'logo-black')->first();
        if ($black){
            $logo_path = asset($black->value);
        }

        $buyerBillingInfo = MetaBuyer::where('user_id', $user->id)->first();
        $default_billingaddress = BuyerBillingAddress::where('user_id', Auth::user()->id)->where('default', 1)->first();
        if(empty($default_billingaddress)){
            $default_billingaddress = BuyerBillingAddress::where('user_id', Auth::user()->id)->first();
        }
        $billingaddress = BuyerBillingAddress::where('user_id', Auth::user()->id)->with('state', 'country')->get();
        $buyerBillingInfo = MetaBuyer::where('user_id', $user->id)->first(); 

        //point system
        // compare user points with admin setting points
        $buyerPoints = MetaBuyer::select('points','points_spent')->where('user_id', Auth::user()->id)->first();

        $pointExist = $buyerPoints->points - $buyerPoints->points_spent;

        $rewardOffer = PointSystem::where('status','=',1)->where('from_price_1', '<=', $pointExist)->get();
 
        return view('buyer.checkout.single', compact('address', 'shippingAddresses', 'user','logo_path','default_billingaddress', 'billingaddress','buyerBillingInfo',
            'shipping_methods', 'order', 'countries', 'usStates', 'caStates','buyerBillingInfo','rewardOffer'));
    }

     public function singlePageCheckoutPost(Request $request) {  
        $rules = [
            'address_id' => 'required',
            'paymentMethod' => 'required|integer|min:1|max:3',
            'shipping_method' => 'required',
            'billing_address_id' => 'required', 
        ];

        if ($request->paymentMethod == '2') {
            $rules['number'] = 'required|max:191|min:6';
            $rules['name'] = 'required|max:191';
            $rules['expiry'] = 'required|date_format:"m/y"';
            $rules['cvc'] = 'required';
        }

        if ($request->store_credit>$request->max_store_credit) {
            return redirect()->back()
                ->withErrors($validator=['store_credit'=>'sdf'])
                ->withInput();
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        if ($request->paymentMethod == '2') {
            $validator->after(function ($validator) use($request) {
                // Card Number Check
                $card = CreditCard::validCreditCard($request->number);
                if (!$card['valid'])
                    $validator->errors()->add('number', 'Invalid Card Number');

                // CVC Check
                $validCvc = CreditCard::validCvc($request->cvc, $card['type']);
                if (!$validCvc)
                    $validator->errors()->add('cvc', 'Invalid CVC');

                // Expiry Check
                $tmp  = explode('/', $request->expiry);
                $validDate = CreditCard::validDate('20'.$tmp[1], $tmp[0]);
                if (!$validDate)
                    $validator->errors()->add('expiry', 'Invalid Expiry');
            });

                if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        // Check Orders
        $id = '';
        try {
            $id = decrypt($request->id);
        } catch (DecryptException $e) {

        }

        if ($id == '')
            abort(404);

        $order = Order::where('id', $id)->where('status', OrderStatus::$INIT)->first();

        if (!$order)
            abort(404);

        $shipmentMethod = AdminShipMethod::where('id', $request->shipping_method)->first();
        $shippingAddress = BuyerShippingAddress::where('id', $request->address_id)->with('state', 'country')->first();

        if ($shipmentMethod->fee === null)
            $shipmentMethod->fee = 0;

        if($order->free_shipping == 1) {
            $shipmentMethod->fee = 0;
        }


        // Point system start

        $pointSetting = DB::table('point_system_settings')->first();
        //calculate point for order
        if(!empty($pointSetting)){
            $newPoints = ($pointSetting->point_rewards * $order->subtotal) / $pointSetting->cost_dollars;
            $newPoints = (int) floor($newPoints);

            $oldPoints = MetaBuyer::select('points')->where('user_id',Auth::user()->id)->first();
            $oldPoints =  $oldPoints->points;
            $totalPoints = $oldPoints + $newPoints;

            $order->points = $newPoints;
            MetaBuyer::where('user_id',Auth::user()->id)->update(['points'=> $totalPoints]);
        }
        $percentageTotal = 0;
        if($request->reward_point){
            $rewardData = PointSystem::where('id',$request->reward_point)->first();
            if($rewardData->free_shipping_1 == 1){
                $shipmentMethod->fee = 0;
            }
            if($rewardData->percentage_discount_1 != 0){
                $percentageTotal = $order->subtotal * ($rewardData->percentage_discount_1 / 100);
                $order->reward_percantage = number_format($percentageTotal, 2, '.', '');
            }
            if($rewardData->unit_price_discount_1 != 0){

                $percentageTotal = $rewardData->unit_price_discount_1;
                $order->reward_fixed = number_format($percentageTotal, 2, '.', '');
            }
            $oldSpentPoints = MetaBuyer::select('points','points_spent')->where('user_id',Auth::user()->id)->first();
            $totalSpentPoints = $oldSpentPoints->points_spent + $rewardData->from_price_1;
            MetaBuyer::where('user_id',Auth::user()->id)->update(['points_spent'=> $totalSpentPoints]);

            $order->reward_point = $request->reward_point;
            $order->used_point = $rewardData->from_price_1;
        }

        // Point system end

        if ($request->paymentMethod != '3') {
            $preOrder = Order::where('order_number', 'like', 'WLM%')
                ->where('order_number', 'not like', '%BO%')
                ->orderBy('created_at', 'desc')
                ->first();
            $orderNumber = "WLM10001";
            if ($preOrder) {
                $tmp = (int) substr($preOrder->order_number, 2);
            }
            order_number_create:{
                if ($preOrder) {
                    $orderNumber = "WLM".($tmp+1);

                    $preOrder_new = Order::where('order_number', $orderNumber)->get();

                    if( $preOrder_new->count() > 0){
                        $tmp++;
                        goto order_number_create;
                    }

                }
            }

            $order->status = OrderStatus::$NEW_ORDER;
            $order->order_number = $orderNumber;
        }

        $order->user_id = Auth::user()->id;
        $order->email = Auth::user()->email;
        $order->company_name = Auth::user()->buyer->company_name;
        $order->shipping_method_id = $shipmentMethod->id;
        $order->shipping = $shipmentMethod->name;
        $order->can_call = $request->can_call;
        $order->store_credit = $request->store_credit;


        // Billing and Shipping state Information 
        $country = Country::where('id',$request->factoryCountry)->first();
        $billingaddress = BuyerBillingAddress::where('id',$request->billing_address_id)->first();

        $order->shipping_address_id = $request->address_id;
        $order->shipping_location = $shippingAddress->location;
        $order->shipping_address = $shippingAddress->address;
        $order->shipping_unit = $shippingAddress->unit;
        $order->shipping_city = $shippingAddress->city;
        $order->shipping_state = ($shippingAddress->state == null) ? $shippingAddress->state_text : $shippingAddress->state->code;
        $order->shipping_state_id = $shippingAddress->state_id;
        $order->shipping_state_text = $shippingAddress->state_text;
        $order->shipping_zip = $shippingAddress->zip;
        $order->shipping_country = $shippingAddress->country->name;
        $order->shipping_country_id = $shippingAddress->country->id;
        $order->shipping_phone = $shippingAddress->phone;
        $order->shipping_cost = $shipmentMethod->fee;

        // Order billing Information insert
        $order->billing_location = $billingaddress->billing_location;
        $order->billing_address = $billingaddress->billing_address;
        $order->billing_unit =  $billingaddress->billing_unit;
        $order->billing_city = $billingaddress->billing_city;
        $order->billing_state = $billingaddress->billing_state;
        $order->billing_state_id = $billingaddress->billing_state_id;
        $order->billing_zip = $billingaddress->billing_zip;
        $order->billing_country = $billingaddress->billing_country_id ? $billingaddress->country->name : null;
        $order->billing_country_id = $billingaddress->billing_country_id ;
        $order->billing_phone = $billingaddress->billing_phone;

        $order->total = number_format(($order->subtotal - $order->discount  - $percentageTotal) + ($shipmentMethod->fee - $request->store_credit), 2, '.', '');
 
        if ($request->paymentMethod == '2') {
            $order->card_number = encrypt($request->number);
            $order->card_full_name = encrypt($request->name);
            $order->card_expire = encrypt($request->expiry);
            $order->card_cvc = encrypt($request->cvc);
            $order->token    = $request->stripeToken;

            $order->payment_type = 'Credit Card';
            $card = CreditCard::validCreditCard($request->number);
            $order->payment_type = $card['type'];

        } else if ($request->paymentMethod == '1') {
            $order->payment_type = 'Wire Transfer';
        } else if ($request->paymentMethod == '3') {
            $order->payment_type = 'PayPal';
        }

        $order->note = $request->order_note;
        $order->save();

        $final_store_credit=0;
        //Get the order ID created just now
        $last_order_id = $order->id;
        $store_credit_amount = StoreCredit::where('user_id', Auth::user()->id)->first();
     
        if(!empty($store_credit_amount)){
            if ($store_credit_amount['amount'] !== '0.00' || $store_credit_amount->amount !== null){
                $final_store_credit = $store_credit_amount['amount'] - ($request->store_credit);
            }else{
                $final_store_credit = '';
            }
        }
         
        DB::table('store_credits')->where('user_id', Auth::user()->id)->update(['amount' => $final_store_credit]);

        $timestr =  Carbon::now();
        $timestr =  $timestr->toDateTimeString();

        DB::table('store_credit_transections')->insert(
            ['user_id' => Auth::user()->id, 'order_id' => $last_order_id,'reason' => 'Used','amount' => (-$request->store_credit), 'created_at' => $timestr,'updated_at' => $timestr]
        );

 
        $user = Auth::user();
        $user->increment('order_count'); 


        $userShippingAddressD= BuyerShippingAddress::where('id',$request->address_id)->where('user_id',$user->id)->update(['default'=> 1]);
        $userShippingAddress= BuyerShippingAddress::where('id','<>',$request->address_id)->where('user_id',$user->id)->update(['default'=> 0]);

        $pdfData = $this->getPdfData($order);
        $logo_path = '';
        $black = DB::table('settings')->where('name', 'logo-black')->first();
        if ($black)
            $logo_path = asset($black->value);
        // Send Mail to Buyer
        $order_time = Carbon::parse($order->created_at)->format('F d ,Y h:i:s A ');
        $orderItem = OrderItem::where('order_id', $order->id)->count('item_id');
        Mail::send('emails.buyer.order_confirmation', ['order' => $order, 'order_time' => $order_time, 'order_item' => $orderItem,'logo'=>$logo_path], function ($message) use ($order, $pdfData) {
            $message->subject('Order Confirmed');
            $message->to($order->email, $order->name);
            $message->attachData($pdfData, $order->order_number . '.pdf');
        });

        //Send Mail to Vendor
        // $user = User::where('role', Role::$EMPLOYEE)->first();
        // Mail::send('emails.vendor.new_order', ['order' => $order, 'order_time' => $order_time], function ($message) use ($order, $pdfData, $user) {
        //     $message->subject('New Order - '.$order->order_number);
        //     $message->to($user->email, $user->first_name.' '.$user->last_name);
        //     $message->attachData($pdfData, $order->order_number.'.pdf');
        // });


        $cartItems = CartItem::where('user_id', Auth::user()->id)->get();
        foreach ($cartItems as $cartItem) {
            $itemInventory = ItemInv::where('item_id', $cartItem->item_id)->where('color_id', $cartItem->color->id)->first();
            if(isset($itemInventory)){
                ItemInv::where('item_id', $cartItem->item_id)->where('color_id', $cartItem->color->id)->update([
                    'qty' => $itemInventory->qty - $cartItem->quantity
                ]);
            }
        }

        CartItem::where([])->where('user_id',auth()->user()->id)->delete();

        return view('buyer.checkout.complete', compact('order'));
    }

    /* Elavon Payment Gateway*/
    public function authorizeOnly(Request $request) {
        
        $orderId = $request->order;
        $order = Order::where('id', $orderId)->with('user', 'items')->first();
        
        $orderDesc = '';
        $oInc = 1;
        foreach ($order->items as $oitem){
            $orderDesc.= $oInc.'. Style No: '. $oitem->style_no .', ';
            $oInc = $oInc + 1;
        }
        
        $invoiceId = $order->order_number;
        $date = decrypt($order->card_expire);
        $new_date = explode('/',$date);
        
        $gateway = \Omnipay\Omnipay::create('Elavon_Converge')->initialize([
            'merchantId' => '2118425',
            'username' => 'web',
            'password' => 'MYC9Q8PZF0FJAFRFE5OK06D6XUS1XL36PPMIT2CCU4SGYFBNLGQXITTKS4BNTT64',
            'testMode' => false
        ]);
        
        $card = new NewCreditCard(array(
            'firstName'             => decrypt($order->card_full_name),
            'number'                => decrypt($order->card_number),
            'expiryMonth'           => $new_date[0],
            'expiryYear'            => $new_date[1],
            'cvv'                   => decrypt($order->card_cvc),
        ));
        
        try {
            $transaction = $gateway->authorize(array(
                'amount'                =>  $order->total,
                'currency'              => 'USD',
                'description'           => 'Your WellMade Authorize request for order# ' . $order->id . 'is successful.',
                'card'                  => $card,
            ));
             
            $response = $transaction->send();
             
            $data = $response->getData();
            
            $transactionReference = $response->getTransactionReference();

            $transactionInfo = (object) array();
            // dd($response);
            if ($response->isSuccessful()) {
                $transactionInfo->status = 'Success';
                $transactionInfo->message = date('Y-m-d H:i:s') . " Authorized Only with ID: " . $data['ssl_approval_code'];
                $transactionInfo->transaction_code = $data['ssl_approval_code'];
                $transactionInfo->transaction_type = $data['ssl_transaction_type'];
                $transactionInfo->txn_id = $data['ssl_txn_id'];
                $transactionInfo->transaction_reference = $transactionReference;
                
                $transactionInfo->captured = false;
            }else {
                $transactionInfo->status = 'Failed';
                $transactionInfo->message = date('Y-m-d H:i:s') . ' Auth Failed';
            }
             
            $tInfo = json_encode($transactionInfo);
             
            DB::table('orders')->where('id', $orderId)->update(['authorize_info' => $tInfo,'aStatus'=>$transactionInfo->status]);
            
            $redirectUrl = route('admin_order_details', ['order' => $orderId]);
            return response()->json(['success' => true, 'url' => $redirectUrl, 'transaction' => $transactionInfo]);

        } catch (\Exception $e) {
             return response()->json(['success' => false, 'message' => 'Exception caught while attempting authorize. Error Message: ' . $e->getMessage(), 'exception_type' => get_class($e)]);
        }

    }
    
    public function captureAuthorizedAmount(Request $request) {
        
        $orderId = $request->order;
        $order = Order::where('id', $orderId)->with('user', 'items')->first();
        
        $orderDesc = '';
        $oInc = 1;
        foreach ($order->items as $oitem){
            $orderDesc.= $oInc.'. Style No: '. $oitem->style_no .', ';
            $oInc = $oInc + 1;
        }
        
        $authorizeInfo = json_decode($order->authorize_info, TRUE);
        
        $invoiceId = $order->order_number;
        $date = decrypt($order->card_expire);
        $new_date = explode('/',$date);
  
        $gateway = \Omnipay\Omnipay::create('Elavon_Converge')->initialize([
            'merchantId' => '2118425',
            'username' => 'web',
            'password' => 'MYC9Q8PZF0FJAFRFE5OK06D6XUS1XL36PPMIT2CCU4SGYFBNLGQXITTKS4BNTT64',
            'testMode' => false
        ]);
        
        $card = new NewCreditCard(array(
            'firstName'             => decrypt($order->card_full_name),
            'number'                => decrypt($order->card_number),
            'expiryMonth'           => $new_date[0],
            'expiryYear'            => $new_date[1],
            'cvv'                   => decrypt($order->card_cvc),
        ));
         
        try {
            
            $transaction = $gateway->capture(array(
                'amount'                =>  $order->total,
                'currency'              => 'USD',
                'description'           => 'Your WellMade Captured request for order# ' . $order->id . 'is successful.',
                'card'                  => $card,
                'transactionReference' => $authorizeInfo['transaction_reference']
            ));
            
            $response = $transaction->send();
            
            $data = $response->getData();
            
            $transactionInfo = (object) array();
             
            if (!empty($data)) {
                $transactionInfo->status = 'Success';
                $transactionInfo->message = date('Y-m-d H:i:s') . "Captured with ID: " . $data['ssl_approval_code'];
                $transactionInfo->transaction_code = $data['ssl_approval_code'];
                $transactionInfo->card_number = $data['ssl_card_number'];
                $transactionInfo->card_type = $data['ssl_card_type'];
                $transactionInfo->transaction_type = $data['ssl_transaction_type'];
                $transactionInfo->txn_id = $data['ssl_txn_id'];
                $transactionInfo->txn_time = $data['ssl_txn_time'];
                $transactionInfo->captured = true;
            }else {
                $transactionInfo->status = 'Failed';
                $transactionInfo->message = date('Y-m-d H:i:s') . ' Auth Failed';
            }
             
            $tInfo = json_encode($transactionInfo);
             
            DB::table('orders')->where('id', $orderId)->update(['authorize_info' => $tInfo,'aStatus'=>$transactionInfo->status]);
            
            $redirectUrl = route('admin_order_details', ['order' => $orderId]);
            return response()->json(['success' => true, 'url' => $redirectUrl, 'transaction' => $transactionInfo]);

        } catch (\Exception $e) {
             return response()->json(['success' => false, 'message' => 'Exception caught while attempting capture. Error Message: ' . $e->getMessage(), 'exception_type' => get_class($e)]);
        }

    }
    
    public function authorizeAndCapture(Request $request) {
        $orderId = $request->order;
        $order = Order::where('id', $orderId)->with('user', 'items')->first();
        $orderDesc = '';
        $oInc = 1;
        foreach ($order->items as $oitem){
            $orderDesc.= $oInc.'. Style No: '. $oitem->style_no .', ';
            $oInc = $oInc + 1;
        }
        $invoiceId = $order->order_number;
        $date = decrypt($order->card_expire);
        $new_date = explode('/',$date);
  
        $gateway = Omnipay::create('Elavon_Converge')->initialize([
            'merchantId' => env('MERCHANT_ID'),
            'username' => env('MERCHANT_USER'),
            'password' => env('MERCHANT_PASS'),
            'testMode' => false
        ]);
 
        $card = new NewCreditCard(array(
            'firstName'             => decrypt($order->card_full_name),
            'number'                => decrypt($order->card_number),
            'expiryMonth'           => $new_date[0],
            'expiryYear'            => $new_date[1],
            'cvv'                   => decrypt($order->card_cvc),
        ));
         
        try {
             $transaction = $gateway->purchase(array(
                'amount'                =>  $order->total,
                'currency'              => 'USD',
                'description'           => 'Your WellMade purchase request for order# ' . $order->id . 'is successful.',
                'card'                  => $card,
             ));
             
             $response = $transaction->send();
             $data = $response->getData();
              
             if ($response->isSuccessful()) {
                 $order->tracking_number = $data['ssl_approval_code'];
                 $order->invoice_number = $invoiceId;
                 $order->authorize_info = json_encode(['status' => 'Success', 'message' => 'Purchase transaction was successful!']);
                 $order->save();
                 return response()->json(['success' => true, 'message' => 'Purchase transaction was successful!', 'response_data' => $data]);
             } else {
                $order->authorize_info = json_encode(['status' => 'Failed', 'message' => 'Purchase transaction was successful!']);
                $order->save();
                return response()->json(['success' => false, 'message' => 'Purchase transaction was Unsuccessful!', 'response_data' => $data]);
             }

        } catch (\Exception $e) {
             return response()->json(['success' => false, 'message' => 'Exception caught while attempting purchase. Error Message: ' . $e->getMessage(), 'exception_type' => get_class($e)]);
        }

    }

    public function checkoutStatic(){
        return view('buyer.checkout.single');
    }

}
