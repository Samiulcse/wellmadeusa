<?php

namespace App\Http\Controllers\Admin;

use App\Model\PromoCodes;
use PDF;
use Mail;
use Session;
use Validator;
use CreditCard;
use Stripe\Stripe;
use App\Model\Page;
use App\Model\User;
use App\Model\Order;
use App\Model\State;
use PayPal\Api\Item;
use App\Model\Coupon;
use PayPal\Api\Payer;
use App\Model\Country;
use App\Model\ItemInv;
use PayPal\Api\Amount;
use App\Model\CartItem;
use PayPal\Api\Details;
use PayPal\Api\Payment;
use App\Model\MetaBuyer;
use App\Model\OrderItem;
use PayPal\Api\ItemList;
use App\Enumeration\Role;
use App\Model\MetaVendor;
use App\Model\StoreCredit;
use App\Model\VendorImage;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use Illuminate\Http\Request;
use PayPal\Api\RedirectUrls;
use App\Model\ShippingMethod;
use App\Model\AdminShipMethod;
use Carbon\Carbon;

// PayPal
use App\Enumeration\CouponType;
use PayPal\Api\ShippingAddress;
use App\Enumeration\OrderStatus;
use PayPal\Api\PaymentExecution;
use Illuminate\Support\Facades\DB;
use App\Model\BuyerShippingAddress;
use Illuminate\Support\Facades\URL;
use App\Enumeration\PageEnumeration;
use App\Enumeration\VendorImageType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PayPal\Auth\OAuthTokenCredential;

// For Authorize Checkout
use Illuminate\Support\Facades\Redirect;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Illuminate\Contracts\Encryption\DecryptException;

class AdminCheckoutController extends Controller
{
    public function create(Request $request) {

        $sessionUser = session('order_customer_id' , null);

        $user = User::find($sessionUser);

        $user->load('buyer');

        $meta_vendor = MetaVendor::where('verified', 1)->where('active', 1)->get()->first();
        $minimum_order_amount = ( isset($meta_vendor->min_order) && $meta_vendor->min_order != '' ) ? $meta_vendor->min_order : 0;

        $storeCredit = 0;

        // if ($request->storeCredit && is_numeric($request->storeCredit)) {
        //     $sc = StoreCredit::where('user_id', Auth::user()->id)->first();

        //     if (!$sc) {
        //         return response()->json(['success' => false, 'message' => 'Insufficient Store Credit.']);
        //     }

        //     if ($sc->amount < $request->storeCredit)
        //         return response()->json(['success' => false, 'message' => 'Insufficient Store Credit.']);
        //     else
        //         $storeCredit = (float) $request->storeCredit;
        // }

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
                    $pack .= $cartItem->item->pack->$var * $cartItem->quantity . '-';
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

        $descountflash=$order->discount;
        $promotion = PromoCodes::where('status',1)->where('status',1)->first();
        $getdescount=0;



        if(!empty($promotion)){
            if($subTotal >= $promotion->credit){
                if($promotion->type==2){
                    $descount=number_format($subTotal * $promotion->amount /100);
                    $getdescount = $descountflash + $descount;
                }else{
                    $getdescount = $descountflash + $promotion->amount;
                }
            }
        }

        $order->order_number = $orderNumber;
        $order->subtotal = number_format($subTotal, 2, '.', '');
        $order->discount = (string) $getdescount;
        $order->shipping_cost = 0;
        $order->total = number_format($subTotal-$storeCredit-$getdescount, 2, '.', '');

        if ($storeCredit > $subTotal)
            $storeCredit = $subTotal;

        $order->store_credit = $storeCredit;

        $order->save();

        return response()->json(['success' => true, 'message' => encrypt($order->id)]);
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

        //AuthorizeNet::authorizeCreditCard(10,true);

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

//	    AuthorizeNet::authorizeCreditCard(10,true);

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
            $preOrder = Order::where('order_number', 'like', 'CQ%')
                ->where('order_number', 'not like', '%BO%')
                ->orderBy('created_at', 'desc')
                ->first();

            $orderNumber = "CQ10001";

            if ($preOrder) {
                $tmp = (int) substr($preOrder->order_number, 2);
                $orderNumber = "CQ".($tmp+1);
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

    public function generateRandomString($length) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
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
        $vendorLogo = VendorImage::where('status', 1)
            ->where('type', VendorImageType::$LOGO)
            ->first();

        if ($vendorLogo)
            $logo_path = public_path($vendorLogo->image_path);

        $vendor = MetaVendor::where('id', 1)->first();
        $order->vendor = $vendor;

        $content = '';

        $page = Page::where('page_id', PageEnumeration::$RETURN_INFO)->first();
        if ($page)
            $content = $page->content;

        $data = [
            'all_items' => [$allItems],
            'orders' => [$order],
            'logo_paths' => [$logo_path],
            'return_policy_description' => $vendor->size_chart
        ];

        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('admin.dashboard.orders.pdf.with_image', $data);
        return $pdf->output();
    }

    /**
     * @param Request $request
     * Authorize and Capture Data From Vendor End.
     * @return \Illuminate\Http\JsonResponse
     */
    public function authorizeOnly($order) {

        //1. Style No: N234, 2. Style No: N4234
//    	$orderId = $request->order;
//    	$order = Order::where('id', $orderId)->with('user', 'items')->first();


        $orderDesc = '';
        $oInc = 1;
        foreach ($order->items as $oitem){
            $orderDesc.= $oInc.'. Style No: '. $oitem->style_no .', ';
            $oInc = $oInc + 1;
        }

        $invoiceId = $order->order_number;

//    	dd($order->toArray());
        $authorize_info = $order->authorize_info;

        $cardNumber = '';
        $cardFullName = '';
        $cardExpire = '';
        $cardCvc = '';

        try {
            $cardNumber = decrypt($order->card_number);
//			$cardNumber = str_repeat("*", (strlen($cardNumber) - 4)).substr($cardNumber,-4,4);
            $cardFullName = decrypt($order->card_full_name);
            $cardExpire = decrypt($order->card_expire);
            $cardCvc = decrypt($order->card_cvc);
        } catch (DecryptException $e) {

        }



        $fName =  $order->user->first_name;
        $lName =  $order->user->last_name;
        $b_address =  $order->billing_address;
        $b_city =  $order->billing_city;
        $b_state =  $order->billing_state;
        $b_zip =  $order->billing_zip;
        $b_country =  $order->billing_country;
        $user_id = $order->user->id;
        $user_email = $order->user->email;


        $s_address =  $order->shipping_address;
        $s_city =  $order->shipping_city;
        $s_state =  $order->shipping_state;
        $s_zip =  $order->shipping_zip;
        $s_country =  $order->shipping_country;



        $amount = $order->total;
        $expireData = explode('/', $cardExpire);
        $exYear = 2000 + intval($expireData[1]);
        $exMonth = $expireData[0];
        $expiry = $exYear.'-'.$exMonth;

//		dd($order->user->first_name);

        // Common setup for API credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(config('services.authorize.login'));
        $merchantAuthentication->setTransactionKey(config('services.authorize.key'));
        $refId = 'ref'.time();


        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($expiry);
        $creditCard->setCardCode($cardCvc);

        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);


        // Create order information
        $orderInfo = new AnetAPI\OrderType();
        $orderInfo->setInvoiceNumber($invoiceId);
        $orderInfo->setDescription($orderDesc);



        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName($fName);
        $customerAddress->setLastName($lName);
//		$customerAddress->setCompany("Souveniropolis");
        $customerAddress->setAddress($b_address);
        $customerAddress->setCity($b_city);
        $customerAddress->setState($b_state);
        $customerAddress->setZip($b_zip);
        $customerAddress->setCountry($b_country);


        // Set the customer's Shipping Address
        $customerSAddress = new AnetAPI\CustomerAddressType();
        $customerSAddress->setFirstName($fName);
        $customerSAddress->setLastName($lName);
//		$customerAddress->setCompany("Souveniropolis");
        $customerSAddress->setAddress($s_address);
        $customerSAddress->setCity($s_city);
        $customerSAddress->setState($s_state);
        $customerSAddress->setZip($s_zip);
        $customerSAddress->setCountry($s_country);

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setType("individual");
        $customerData->setId($user_id);
        $customerData->setEmail($user_email);

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
//		$transactionRequestType->setTransactionType("authOnlyTransaction");
        $transactionRequestType->setTransactionType("authOnlyTransaction");
//		$transactionRequestType->setShipping($amount);
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($orderInfo);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setShipTo($customerSAddress);
        $transactionRequestType->setCustomer($customerData);


        // Assemble the complete transaction request
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

        $status_avs_hint = array("A" => 'Address (street) matches, ZIP code does not', 'B' => 'Address information not provided for AVS check',
            'E' => 'AVS error', 'G' => 'Non-U.S. card issuing bank', 'N' => 'No match on address (street) and ZIP code',
            'P' => 'AVS not applicable for this transaction', 'R' => 'Retry – System unavailable or timed out', 'S' => 'Service not supported by issuer',
            'U' => 'Address information is unavailable', 'W' => '9 digit ZIP code matches, address (street) does not', 'Y' => 'Address (street) and 5 digit ZIP code match',
            'Z' => '5 digit ZIP matches, Address (Street) does not'
        );

        $status_cvv_hint = array('M' => 'Successful Match', 'N' => 'Does NOT Match', 'P'=> 'Is NOT Processed',
            'S' => 'Should be on card, but is not indicated', 'U' => 'Issuer is not certified or has not provided encryption key'
        );



        $transactionInfo = (object) array();

        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode()) {
//					pr($response);
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {
                    $transactionInfo->status = 'Success';
                    $transactionInfo->message = date('Y-m-d H:i:s') . " Authorized Only with ID: " . $tresponse->getTransId();
                    $transactionInfo->transaction_code = $tresponse->getTransId();
                    $transactionInfo->transaction_response_code = $tresponse->getResponseCode();
                    $transactionInfo->message_code = $tresponse->getMessages()[0]->getCode();
                    $transactionInfo->auth_code = $tresponse->getAuthCode();
                    $transactionInfo->avs_code = $tresponse->getAvsResultCode();
                    $transactionInfo->cvv_code = $tresponse->getCvvResultCode();
                    $transactionInfo->desc = $tresponse->getMessages()[0]->getDescription();
                    if($transactionInfo->avs_code){
                        $transactionInfo->avs_message = $status_avs_hint[$transactionInfo->avs_code];
                    }
                    if($transactionInfo->cvv_code){
                        $transactionInfo->cvv_message = $status_cvv_hint[$transactionInfo->cvv_code];
                    }

                } else {
                    $transactionInfo->status = 'Failed';
                    $transactionInfo->message = date('Y-m-d H:i:s') . ' Auth Failed';
                    $transactionInfo->error_code = $tresponse->getErrors()[0]->getErrorCode();
                    $transactionInfo->error_message = $tresponse->getErrors()[0]->getErrorText();
                }
                // Or, print errors if the API request wasn't successful
            } else {
                $transactionInfo->status = 'Failed';
                $transactionInfo->message = date('Y-m-d H:i:s') . ' Auth Failed';
                $tresponse = $response->getTransactionResponse();

                $transactionInfo->error_code = $tresponse->getErrors()[0]->getErrorCode();
                $transactionInfo->error_message = $tresponse->getErrors()[0]->getErrorText();
            }
        } else {
            $transactionInfo->status = 'Failed';
            $transactionInfo->message = 'No Response(Failed)';
        }

        $transactionInfo->captured = false;

        $tInfo = json_encode($transactionInfo);
        return $tInfo;
    }

    public function authorizeAndCapture(Request $request) {

        //1. Style No: N234, 2. Style No: N4234
        $orderId = $request->order;
        $order = Order::where('id', $orderId)->with('user', 'items')->first();

        $orderDesc = '';
        $oInc = 1;
        foreach ($order->items as $oitem){
            $orderDesc.= $oInc.'. Style No: '. $oitem->style_no .', ';
            $oInc = $oInc + 1;
        }

        $invoiceId = $order->order_number;

//    	dd($order->toArray());
        $authorize_info = $order->authorize_info;

        $cardNumber = '';
        $cardFullName = '';
        $cardExpire = '';
        $cardCvc = '';

        try {
            $cardNumber = decrypt($order->card_number);
//			$cardNumber = str_repeat("*", (strlen($cardNumber) - 4)).substr($cardNumber,-4,4);
            $cardFullName = decrypt($order->card_full_name);
            $cardExpire = decrypt($order->card_expire);
            $cardCvc = decrypt($order->card_cvc);
        } catch (DecryptException $e) {

        }



        $fName =  $order->user->first_name;
        $lName =  $order->user->last_name;
        $b_address =  $order->billing_address;
        $b_city =  $order->billing_city;
        $b_state =  $order->billing_state;
        $b_zip =  $order->billing_zip;
        $b_country =  $order->billing_country;
        $user_id = $order->user->id;
        $user_email = $order->user->email;


        $s_address =  $order->shipping_address;
        $s_city =  $order->shipping_city;
        $s_state =  $order->shipping_state;
        $s_zip =  $order->shipping_zip;
        $s_country =  $order->shipping_country;



        $amount = $order->total;
        $expireData = explode('/', $cardExpire);
        $exYear = 2000 + intval($expireData[1]);
        $exMonth = $expireData[0];
        $expiry = $exYear.'-'.$exMonth;

//		dd($order->user->first_name);

        // Common setup for API credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(config('services.authorize.login'));
        $merchantAuthentication->setTransactionKey(config('services.authorize.key'));
        $refId = 'ref'.time();


        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($expiry);
        $creditCard->setCardCode($cardCvc);

        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);


        // Create order information
        $orderInfo = new AnetAPI\OrderType();
        $orderInfo->setInvoiceNumber($invoiceId);
        $orderInfo->setDescription($orderDesc);



        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName($fName);
        $customerAddress->setLastName($lName);
//		$customerAddress->setCompany("Souveniropolis");
        $customerAddress->setAddress($b_address);
        $customerAddress->setCity($b_city);
        $customerAddress->setState($b_state);
        $customerAddress->setZip($b_zip);
        $customerAddress->setCountry($b_country);


        // Set the customer's Shipping Address
        $customerSAddress = new AnetAPI\CustomerAddressType();
        $customerSAddress->setFirstName($fName);
        $customerSAddress->setLastName($lName);
//		$customerAddress->setCompany("Souveniropolis");
        $customerSAddress->setAddress($s_address);
        $customerSAddress->setCity($s_city);
        $customerSAddress->setState($s_state);
        $customerSAddress->setZip($s_zip);
        $customerSAddress->setCountry($s_country);

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setType("individual");
        $customerData->setId($user_id);
        $customerData->setEmail($user_email);

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
//		$transactionRequestType->setTransactionType("authOnlyTransaction");
        $transactionRequestType->setTransactionType("authCaptureTransaction");
//		$transactionRequestType->setShipping($amount);
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($orderInfo);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setShipTo($customerSAddress);
        $transactionRequestType->setCustomer($customerData);


        // Assemble the complete transaction request
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

        $status_avs_hint = array("A" => 'Address (street) matches, ZIP code does not', 'B' => 'Address information not provided for AVS check',
            'E' => 'AVS error', 'G' => 'Non-U.S. card issuing bank', 'N' => 'No match on address (street) and ZIP code',
            'P' => 'AVS not applicable for this transaction', 'R' => 'Retry – System unavailable or timed out', 'S' => 'Service not supported by issuer',
            'U' => 'Address information is unavailable', 'W' => '9 digit ZIP code matches, address (street) does not', 'Y' => 'Address (street) and 5 digit ZIP code match',
            'Z' => '5 digit ZIP matches, Address (Street) does not'
        );

        $status_cvv_hint = array('M' => 'Successful Match', 'N' => 'Does NOT Match', 'P'=> 'Is NOT Processed',
            'S' => 'Should be on card, but is not indicated', 'U' => 'Issuer is not certified or has not provided encryption key'
        );



        $transactionInfo = (object) array();

        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode()) {
//					pr($response);
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {
                    $transactionInfo->status = 'Success';
                    $transactionInfo->message = date('Y-m-d H:i:s') . " Authorized Only with ID: " . $tresponse->getTransId();
                    $transactionInfo->transaction_code = $tresponse->getTransId();
                    $transactionInfo->transaction_response_code = $tresponse->getResponseCode();
                    $transactionInfo->message_code = $tresponse->getMessages()[0]->getCode();
                    $transactionInfo->auth_code = $tresponse->getAuthCode();
                    $transactionInfo->avs_code = $tresponse->getAvsResultCode();
                    $transactionInfo->cvv_code = $tresponse->getCvvResultCode();
                    $transactionInfo->desc = $tresponse->getMessages()[0]->getDescription();
                    if($transactionInfo->avs_code){
                        $transactionInfo->avs_message = $status_avs_hint[$transactionInfo->avs_code];
                    }
                    if($transactionInfo->cvv_code){
                        $transactionInfo->cvv_message = $status_cvv_hint[$transactionInfo->cvv_code];
                    }

                } else {
                    $transactionInfo->status = 'Failed';
                    $transactionInfo->message = date('Y-m-d H:i:s') . ' Auth Failed';
                    $transactionInfo->error_code = $tresponse->getErrors()[0]->getErrorCode();
                    $transactionInfo->error_message = $tresponse->getErrors()[0]->getErrorText();
                }
                // Or, print errors if the API request wasn't successful
            } else {
                $transactionInfo->status = 'Failed';
                $transactionInfo->message = date('Y-m-d H:i:s') . ' Auth Failed';
                $tresponse = $response->getTransactionResponse();

                $transactionInfo->error_code = $tresponse->getErrors()[0]->getErrorCode();
                $transactionInfo->error_message = $tresponse->getErrors()[0]->getErrorText();
            }
        } else {
            $transactionInfo->status = 'Failed';
            $transactionInfo->message = 'No Response(Failed)';
        }

        $tInfo = json_encode($transactionInfo);

        if(!(isset($authorize_info))){
            DB::table('orders')->where('id', $orderId)->update(['authorize_info' => $tInfo]);
        }
//		 DB::table('orders')->where('id', $orderId)->update(['authorize_info' => $tInfo]);

        $redirectUrl = route('admin_order_details', ['order' => $orderId]);
        return response()->json(['success' => true, 'url' => $redirectUrl, 'transaction' => $transactionInfo]);
    }

    public function captureAuthorizedAmount(Request $request) {

        //1. Style No: N234, 2. Style No: N4234
        $orderId = $request->order;
        $order = Order::where('id', $orderId)->with('user', 'items')->first();

        $orderDesc = '';
        $transactionid = '';
        $oInc = 1;
        foreach ($order->items as $oitem){
            $orderDesc.= $oInc.'. Style No: '. $oitem->style_no .', ';
            $oInc = $oInc + 1;
        }

        $invoiceId = $order->order_number;

        $authorize_info = json_decode($order->authorize_info, true);
        $transactionid = $authorize_info['transaction_code'];



        $amount = $order->total;

        $cardNumber = '';
        $cardFullName = '';
        $cardExpire = '';
        $cardCvc = '';

        try {
            $cardNumber = decrypt($order->card_number);
//			$cardNumber = str_repeat("*", (strlen($cardNumber) - 4)).substr($cardNumber,-4,4);
            $cardFullName = decrypt($order->card_full_name);
            $cardExpire = decrypt($order->card_expire);
            $cardCvc = decrypt($order->card_cvc);
        } catch (DecryptException $e) {

        }



        $amount = $order->total;
        $expireData = explode('/', $cardExpire);
        $exYear = 2000 + intval($expireData[1]);
        $exMonth = $expireData[0];
        $expiry = $exYear.'-'.$exMonth;

//		dd($order->user->first_name);

        // Common setup for API credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(config('services.authorize.login'));
        $merchantAuthentication->setTransactionKey(config('services.authorize.key'));
        $refId = 'ref'.time();


        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("priorAuthCaptureTransaction");
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setRefTransId($transactionid);

        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setTransactionRequest( $transactionRequestType);
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

        $transactionInfo = (object) array();


        $status_avs_hint = array("A" => 'Address (street) matches, ZIP code does not', 'B' => 'Address information not provided for AVS check',
            'E' => 'AVS error', 'G' => 'Non-U.S. card issuing bank', 'N' => 'No match on address (street) and ZIP code',
            'P' => 'AVS not applicable for this transaction', 'R' => 'Retry – System unavailable or timed out', 'S' => 'Service not supported by issuer',
            'U' => 'Address information is unavailable', 'W' => '9 digit ZIP code matches, address (street) does not', 'Y' => 'Address (street) and 5 digit ZIP code match',
            'Z' => '5 digit ZIP matches, Address (Street) does not'
        );

        $status_cvv_hint = array('M' => 'Successful Match', 'N' => 'Does NOT Match', 'P'=> 'Is NOT Processed',
            'S' => 'Should be on card, but is not indicated', 'U' => 'Issuer is not certified or has not provided encryption key'
        );

        if ($response != null) {

//			dd($response);

            // Check to see if the API request was successfully received and acted upon
            // $response->getMessages()->getResultCode() == "Ok"
            if ($response->getMessages()->getResultCode()== "Ok" ) {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {
                    $transactionInfo->status = 'Success';
                    $transactionInfo->message = date('Y-m-d H:i:s') . " Authorize and Captured with ID: " . $tresponse->getTransId();
                    $transactionInfo->transaction_code = $tresponse->getTransId();
                    $transactionInfo->transaction_response_code = $tresponse->getResponseCode();
                    $transactionInfo->message_code = $tresponse->getMessages()[0]->getCode();
                    $transactionInfo->auth_code = $tresponse->getAuthCode();
                    $transactionInfo->avs_code = $tresponse->getAvsResultCode();
                    $transactionInfo->cvv_code = $tresponse->getCvvResultCode();
                    $transactionInfo->desc = $tresponse->getMessages()[0]->getDescription();
                    $transactionInfo->avs_message = $status_avs_hint[$transactionInfo->avs_code];
                    $transactionInfo->cvv_message = $status_cvv_hint[$transactionInfo->cvv_code];
                    $transactionInfo->captured = true;
                } else {
                    $transactionInfo->status = 'Failed';
                    $transactionInfo->message = date('Y-m-d H:i:s') . ' Auth Failed';
                    $transactionInfo->error_code = $tresponse->getErrors()[0]->getErrorCode();
                    $transactionInfo->error_message = $tresponse->getErrors()[0]->getErrorText();
                }
//				dd($transactionInfo);
                // Or, print errors if the API request wasn't successful
            } else {
                $transactionInfo->status = 'Failed';
                $transactionInfo->message = date('Y-m-d H:i:s') . ' Auth Failed';
                $tresponse = $response->getTransactionResponse();

                $transactionInfo->error_code = $tresponse->getErrors()[0]->getErrorCode();
                $transactionInfo->error_message = $tresponse->getErrors()[0]->getErrorText();
//				dd($transactionInfo);
            }
        } else {
//			dd($transactionInfo);
            $transactionInfo->status = 'Failed';
            $transactionInfo->message = 'No Response(Failed)';
        }

        $tInfo = json_encode($transactionInfo);

        if(!(isset($authorize_info))){
//			DB::table('orders')->where('id', $orderId)->update(['authorize_info' => $tInfo]);
        }
        DB::table('orders')->where('id', $orderId)->update(['authorize_info' => $tInfo]);

        $redirectUrl = route('admin_order_details', ['order' => $orderId]);
        return response()->json(['success' => true, 'url' => $redirectUrl, 'transaction' => $transactionInfo]);
    }

    // public function applyCoupon(Request $request) {
    //     $order = Order::where('id', $request->id)->where('user_id', Auth::user()->id)->first();

    //     if (!$order)
    //         return response()->json(['success' => false, 'message' => 'Invalid Order.']);

    //     $coupon = Coupon::where('vendor_meta_id', $order->vendor_meta_id)
    //         ->where('name', $request->coupon)
    //         ->first();

    //     if (!$coupon)
    //         return response()->json(['success' => false, 'message' => 'Invalid Coupon.']);

    //     if ($coupon->multiple_use == 0) {
    //         $previous = Order::where('user_id', Auth::user()->id)
    //             ->where('status', '!=', OrderStatus::$INIT)
    //             ->where('vendor_meta_id', $coupon->vendor_meta_id)
    //             ->where('coupon', $coupon->name)
    //             ->first();

    //         if ($previous)
    //             return response()->json(['success' => false, 'message' => 'Already used this coupon.']);
    //     }

    //     $subTotal = $order->subtotal;
    //     $discount = 0;

    //     if ($coupon->type == CouponType::$FIXED_PRICE)
    //         $discount = $coupon->amount;
    //     else if ($coupon->type == CouponType::$PERCENTAGE){
    //         $discount = ($coupon->amount / 100) * $subTotal;
    //     } else if ($coupon->type == CouponType::$FREE_SHIPPING){
    //         $discount = 0;
    //     }

    //     if ($discount > $subTotal)
    //         $discount = $subTotal;

    //     $order->discount = $discount;
    //     $order->total = $subTotal - $discount;
    //     $order->coupon = $coupon->name;
    //     $order->coupon_type = $coupon->type;
    //     $order->coupon_amount = $coupon->amount;
    //     $order->coupon_description = $coupon->description;
    //     $order->save();

    //     return response()->json(['success' => true, 'message' => 'Success.']);
    // }
 
    public function singlePageCheckout(Request $request){
        $sessionUser = session('order_customer_id' , null);

        $user = User::find($sessionUser);

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
            $address = BuyerShippingAddress::where('user_id', $user->id)->where('default', 1)->first();
        else
            $address = BuyerShippingAddress::where('id', $order->shipping_address_id)->first();

        $shippingAddresses = BuyerShippingAddress::where('user_id', $user->id)->with('state', 'country')->get();
        $shipping_methods = AdminShipMethod::with('courier')->get();

        return view('admin.dashboard.checkout.single', compact('address', 'shippingAddresses', 'user',
            'shipping_methods', 'order', 'countries', 'usStates', 'caStates'))->with('page_title', 'Checkout');
    }

    public function addShippingAddress(Request $request) {

        $sessionUser = session('order_customer_id' , null);

        $user = User::find($sessionUser);

        $state_id = null;
        $state = null;

        if ($request->location == "INT")
            $state = $request->state;
        else
            $state_id = $request->stateSelect;

        $address = BuyerShippingAddress::create([
            'user_id' => $user->id,
            'store_no' => $request->store_no,
            'location' => $request->location,
            'address' => $request->address,
            'unit' => $request->unit,
            'city' => $request->city,
            'state_id' => $state_id,
            'state_text' => $state,
            'zip' => $request->zipCode,
            'country_id' => $request->country,
            'phone' => $request->phone,
            'fax' => $request->fax,
            'commercial' => ($request->showroomCommercial == null) ? 0 : 1,
        ]);

        return response()->json($address->toArray());
    }

    public function applyCoupon(Request $request) {
        $sessionUser = session('order_customer_id' , null);

        $user = User::find($sessionUser);

        $order = Order::where('id', $request->id)->where('user_id', $user->id)->first();

        if (!$order)
            return response()->json(['success' => false, 'message' => 'Invalid Order.']);

        $coupon = Coupon::where('name', trim($request->coupon))->first();

        if (!$coupon)
            return response()->json(['success' => false, 'message' => 'Invalid Coupon.']);

        if ($coupon->multiple_use == 0) {
            $previous = Order::where('user_id', $user->id)
                ->where('status', '!=', OrderStatus::$INIT)
                ->where('coupon', $coupon->name)
                ->first();

            if ($previous)
                return response()->json(['success' => false, 'message' => 'Already used this coupon.']);
        }

        $subTotal = $order->subtotal;
        $discount = 0;

        if ($coupon->type == CouponType::$FIXED_PRICE)
            $discount = $coupon->amount;
        else if ($coupon->type == CouponType::$PERCENTAGE){
            $discount = ($coupon->amount / 100) * $subTotal;
        } else if ($coupon->type == CouponType::$FREE_SHIPPING){
            $discount = 0;
        }

        if ($discount > $subTotal) {
            $discount = $subTotal; }

        $descountflash=$order->discount;
        if ($descountflash){
            $Finaldiscount = $discount + $descountflash;
        }
        else {
            $Finaldiscount = $discount;
        }


        $order->discount = $Finaldiscount;
        $order->total = $subTotal - $Finaldiscount;
        $order->coupon = $coupon->name;
        $order->coupon_type = $coupon->type;
        $order->coupon_amount = $coupon->amount;
        $order->coupon_description = $coupon->description;
        $order->save();

        return response()->json(['success' => true, 'message' => 'Success.']);
    }

    public function singlePageCheckoutPost(Request $request) {

        $sessionUser = session('order_customer_id' , null);

        $user = User::find($sessionUser);

        $rules = [
            'address_id' => 'required',
            'paymentMethod' => 'required|integer|min:1|max:3',
            'shipping_method' => 'required',
        ];

        if ($request->paymentMethod == '2') {
            $rules['number'] = 'required|max:191|min:6';
            $rules['name'] = 'required|max:191';
            $rules['expiry'] = 'required|date_format:"m/y"';
            $rules['cvc'] = 'required';
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

        $order->user_id = $user->id;
        $order->email = $user->email;
        $order->company_name = $user->buyer->company_name;
        $order->shipping_method_id = $shipmentMethod->id;
        $order->shipping = $shipmentMethod->name;
        $order->can_call = $request->can_call;

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
        $order->total = number_format(($order->subtotal - $order->discount) + ($shipmentMethod->fee - $order->store_credit), 2, '.', '');
        if ($request->paymentMethod == '2') {
            $order->card_number = encrypt($request->number);
            $order->card_full_name = encrypt($request->name);
            $order->card_expire = encrypt($request->expiry);
            $order->card_cvc = encrypt($request->cvc);

            $order->payment_type = 'Credit Card';
            $card = CreditCard::validCreditCard($request->number);
            $order->payment_type = $card['type'];

            //$authorize = $this->authorizeOnly($order);
            // $order->authorize_info = $authorize;

        } else if ($request->paymentMethod == '1') {
            $order->payment_type = 'Wire Transfer';
        } else if ($request->paymentMethod == '3') {
            $order->payment_type = 'PayPal';
        }

        $order->note = $request->order_note;
        $order->save();

        $user = $user;
        $user->increment('order_count');


        if ($request->paymentMethod == '3') {
            $paypal_conf = \Config::get('paypal');
            $this->_api_context = new ApiContext(new OAuthTokenCredential(
                    $paypal_conf['client_id'],
                    $paypal_conf['secret'])
            );
            $this->_api_context->setConfig($paypal_conf['settings']);

            $payer = new Payer();
            $payer->setPaymentMethod('paypal');

            $items = [];
            $item_list = new ItemList();

            foreach ($order->items as $i) {
                $item = new Item();
                $item->setName($i->style_no.' - '.$i->color)
                    ->setCurrency('USD')
                    ->setQuantity($i->total_qty)
                    ->setPrice($i->per_unit_price);

                $items[] = $item;
            }

            $itemTmp = new \PayPal\Api\Item();
            $itemTmp->setName('Store Credit')
                ->setCurrency('USD')
                ->setQuantity(1)
                ->setPrice('-'.number_format($order->store_credit, 2, '.', ''));

            $items[] = $itemTmp;

            $item_list->setItems($items);

            $details = new Details();
            $details->setShipping($order->shipping_cost)
                ->setTax(0)
                ->setSubtotal(($order->subtotal - $order->discount)- $order->store_credit);

            $itemTmp->setName('Discount')
                ->setCurrency('USD')
                ->setQuantity(1)
                ->setPrice('-'.number_format($order->discount, 2, '.', ''));

            $amount = new Amount();
            $amount->setCurrency('USD')
                ->setTotal( ($order->subtotal - $order->discount) + $order->shipping_cost - $order->store_credit)
                ->setDetails($details);

            $transaction = new Transaction();
            $transaction->setAmount($amount)
                ->setItemList($item_list)
                ->setDescription('https://www.fameaccoessories.com')
                ->setInvoiceNumber($order->order_number);

            $redirect_urls = new RedirectUrls();
            $redirect_urls->setReturnUrl(route('checkout_complete', ['id' => $request->id]))
                ->setCancelUrl(route('show_cart'));
            $payment = new Payment();
            $payment->setIntent('Sale')
                ->setPayer($payer)
                ->setRedirectUrls($redirect_urls)
                ->setTransactions(array($transaction));

            try {
                $payment->create($this->_api_context);
            } catch (\PayPal\Exception\PPConnectionException $ex) {
                if (\Config::get('app.debug')) {
                    \Session::put('error', 'Connection timeout');
                    return Redirect::to('/');
                } else {
                    \Session::put('error', 'Some error occur, sorry for inconvenient');
                    return Redirect::to('/');
                }
            }
            foreach ($payment->getLinks() as $link) {
                if ($link->getRel() == 'approval_url') {
                    $redirect_url = $link->getHref();
                    break;
                }
            }

            /** add payment ID to session **/
            Session::put('paypal_payment_id', $payment->getId());
            if (isset($redirect_url)) {
                /** redirect to paypal **/
                return Redirect::away($redirect_url);
            }
            \Session::put('error', 'Unknown error occurred');
            return Redirect::to('/');
        }

        $cartItems = CartItem::where('user_id', $user->id)->get();
        foreach ($cartItems as $cartItem) {
            $itemInventory = ItemInv::where('item_id', $cartItem->item_id)->where('color_id', $cartItem->color->id)->first();
            if(isset($itemInventory)){
                ItemInv::where('item_id', $cartItem->item_id)->where('color_id', $cartItem->color->id)->update([
                    'qty' => $itemInventory->qty - $cartItem->quantity
                ]);
            }
        }
        CartItem::where([])->where('user_id', $user->id)->delete();

        $pdfData = $this->getPdfData($order);

        try {
            $order_time = Carbon::parse($order->created_at)->format('F d ,Y h:i:s A ');
            $orderItem = OrderItem::where('order_id', $order->id)->count('item_id');
            // Send Mail to Buyer
            Mail::send('emails.buyer.order_confirmation', ['order' => $order, 'order_time' => $order_time, 'order_item' => $orderItem], function ($message) use ($order, $pdfData) {
                $message->subject('Order Confirmed');
                $message->to($order->email, $order->name);
                $message->attachData($pdfData, $order->order_number . '.pdf');
            });

            // Send Mail to Vendor
            $user = User::where('role', Role::$EMPLOYEE)->first();

//            Mail::send('emails.vendor.new_order', ['order' => $order], function ($message) use ($order, $pdfData, $user) {
//                $message->subject('New Order - '.$order->order_number);
//                $message->to($user->email, $user->first_name.' '.$user->last_name);
//                $message->attachData($pdfData, $order->order_number.'.pdf');
//            });
        } catch (\Exception $exception) {

        }

        return view('admin.dashboard.checkout.complete', compact('order'))->with('page_title', 'Checkout Complete');
    }
}
