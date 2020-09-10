<?php

namespace App\Http\Controllers\Buyer;

use App\Enumeration\OrderStatus;
use App\Enumeration\VendorImageType;
use App\Model\AdminMessage;
use App\Model\BuyerMessage;
use App\Model\BuyerShippingAddress;
use App\Model\BuyerBillingAddress;
use App\Model\Country;
use App\Model\MetaBuyer;
use App\Model\MetaVendor;
use App\Model\Order;
use App\Model\Review;
use App\Model\Setting;
use App\Model\State;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Support\Facades\Redirect;
use Uuid;

class ProfileController extends Controller
{
    public function index() {
        $user = Auth::user();
        $user->load('buyer');
        $setting = Setting::where('name', 'buyer_home')->first();
        if ($setting)
            $setting= $setting->value;
        $unread_messages = '';
        if(Auth::user()){
            $unread_messages = BuyerMessage::where('user_id', Auth::user()->id)
                ->where('reading_status', 0)
                ->count();
        }
        return view('buyer.profile.index', compact('user','setting','unread_messages'))->with('page_title', 'My Profile');
    }

    public function rewardPoints(){
        $unread_messages = '';
        if(Auth::user()){
            $unread_messages = BuyerMessage::where('user_id', Auth::user()->id)
                ->where('reading_status', 0)
                ->count();
        }
        $profile_page = 'reward-points';
        $orders = Order::where('user_id', Auth::user()->id)->where('points','!=',0)->orderBy('created_at', 'desc')->paginate(10);
        $user_info = MetaBuyer::select('points','points_spent')->where('user_id',Auth::user()->id)->first();
        return view('buyer.profile.rewards', compact('profile_page','orders','user_info','unread_messages'))->with('page_title', 'Reward Points');
    }

    public function updateProfile(Request $request) {
        $rules  = [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
//            'company_name' => 'required|max:255',
        ];

        if ($request->password != '')
            $rules['password'] = 'string|min:6';

        $request->validate($rules);

        $user = Auth::user();
        $user->load('buyer');

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
//        $user->buyer->company_name = $request->company_name;

        if ($request->password != '')
            $user->password = Hash::make($request->password);

        $user->save();
//        $user->buyer->save();

        return redirect()->back()->with('message', 'Profile Updated!');
    }

    public function address() {
        $countries = Country::orderBy('name')->get();
        $usStates = State::where('country_id', 1)->orderBy('name')->get()->toArray();
        $caStates =State::where('country_id', 2)->orderBy('name')->get()->toArray();

        $buyer = MetaBuyer::where('id', Auth::user()->buyer_meta_id)->with('user')->first();
        $shippingAddress = BuyerShippingAddress::where('user_id', Auth::user()->id)
            ->with('state', 'country')->get();
        $unread_messages = '';
        if(Auth::user()){
            $unread_messages = BuyerMessage::where('user_id', Auth::user()->id)
                ->where('reading_status', 0)
                ->count();
        }
        $billing_address = BuyerBillingAddress::where('user_id', Auth::user()->id)->with('state', 'country')->get();
         
        return view('buyer.profile.address', compact('countries','unread_messages', 'usStates', 'caStates', 'buyer','billing_address', 'shippingAddress'))
            ->with('page_title', 'Addresses');
    }
    public function addressPost(Request $request) { 
        
        $messages = [
            'required' => 'This field is required.',
        ];
 

        if ($request->factoryLocation == "INT")
            $rules['factoryState'] = 'required|string|max:255';
        else
            $rules['factoryStateSelect'] = 'required';

        $request->validate($rules, $messages);

        $buyer = BuyerBillingAddress::where('id',$request->id)->first();
        if($request->defaultaddress == 1){
            BuyerBillingAddress::where('user_id', Auth::user()->id)->update(['default' => 0]);
        }
        $factory_state_id = null;
        $factory_state = null;

        if ($request->factoryLocation == "INT")
            $factory_state = $request->factoryState;
        else
            $factory_state_id = $request->factoryStateSelect;
 
        $buyer->billing_location = $request->factoryLocation;
        $buyer->billing_address = $request->factoryAddress;
        $buyer->billing_unit = $request->factoryUnit;
        $buyer->billing_city = $request->factoryCity;
        $buyer->billing_state_id = $factory_state_id;
        $buyer->billing_state = $factory_state;
        $buyer->billing_zip = $request->factoryZipCode;
        $buyer->billing_country_id = $request->factoryCountry;
        $buyer->billing_phone = $request->factoryPhone;
        $buyer->billing_fax = $request->factoryFax; 
        $buyer->default = $request->defaultaddress? $request->defaultaddress : 0; 

        $buyer->save();

        return redirect()->back()->with('message', 'Address Updated!');
    }

    public function addShippingAddress(Request $request) {
        $state_id = null;
        $state = null;

        if ($request->location == "INT")
            $state = $request->state;
        else
            $state_id = $request->stateSelect;

        $address = BuyerShippingAddress::create([
            'user_id' => Auth::user()->id,
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

    public function defaultShippingAddress(Request $request) {
        BuyerShippingAddress::where('user_id', Auth::user()->id)->update(['default' => 0]); 
        BuyerShippingAddress::where('id', $request->id)->update(['default' => 1]);
    }

    public function defaultbillingAddress(Request $request) {
        BuyerBillingAddress::where('user_id', Auth::user()->id)->update(['default' => 0]); 
        BuyerBillingAddress::where('id', $request->id)->update(['default' => 1]);
    }

    
    public function deleteShippingAddress(Request $request) {
        BuyerShippingAddress::where('id', $request->id)->delete();
    }
    public function deletebillingAddress(Request $request) {
        BuyerBillingAddress::where('id', $request->id)->delete();
    }

    public function editShippingAddress(Request $request) {
        $state_id = null;
        $state = null;

        if ($request->location == "INT")
            $state = $request->state;
        else
            $state_id = $request->stateSelect;

        BuyerShippingAddress::where('id', $request->id)
            ->update([
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
    }

    public function feedback() {
        $orders = DB::table('orders')
            ->select('orders.id', 'order_number', 'review', 'star', 'orders.created_at')
            ->leftJoin('reviews', 'orders.id', '=', 'reviews.order_id')
            ->where('orders.user_id', Auth::user()->id)
            ->where('deleted_at', NULL)
            ->orderBy('orders.created_at', 'desc')
            ->paginate(10);

        return view('buyer.profile.feedback', compact('orders'))->with('page_title', 'Feedback');
    }

    public function feedbackPost(Request $request) {
        if (isset($request->ids)) {
            foreach ($request->ids as $id) {
                $review = Review::where('user_id', Auth::user()->id)
                    ->where('order_id', $id)
                    ->first();

                $starVar = 'star_' . $id;
                $commentVar = 'comment_' . $id;
                $star = 0;

                if ($request->$starVar != null || $request->$starVar != '')
                    $star = (int)$request->$starVar;

                if ($star > 5)
                    $star = 5;

                if ($review) {
                    $review->review = $request->$commentVar;
                    $review->star = $star;
                    $review->save();
                } else {
                    $order = Order::where('id', $id)->first();

                    Review::create([
                        'order_id' => $order->id,
                        'user_id' => Auth::user()->id,
                        'star' => $star,
                        'review' => $request->$commentVar,
                    ]);
                }
            }
        }

        return redirect()->back();
    }

    public function buyerDashboard(){
        $user = Auth::user();
        $buyer_home = '';
        $setting = Setting::where('name', 'buyer_home')->first();
        if ($setting)
            $buyer_home = $setting->value;

        $orders = $orders = Order::where('status', '!=', OrderStatus::$INIT)
            ->where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')->limit(4)->get();


        return view('buyer.profile.buyer_dashboard',compact('user', 'buyer_home', 'orders'));
    }

    public function overview()
    {
        $user = Auth::user();
        $user->load('buyer');
        $unread_messages = '';
        if(Auth::user()){
            $unread_messages = BuyerMessage::where('user_id', Auth::user()->id)
                ->where('reading_status', 0)
                ->count();
        }
        $setting = Setting::where('name', 'buyer_home')->first();
        if ($setting)
            $setting= $setting->value;
        $orders = Order::where('user_id', Auth::user()->id)->where('status', '<>',  OrderStatus::$INIT)->orderBy('created_at', 'desc')->limit(4)->get();
        $buyer = MetaBuyer::where('id', Auth::user()->buyer_meta_id)->with('user','state', 'country')->first();
        $shippingAddress = BuyerShippingAddress::where('user_id', Auth::user()->id)->where('default',1)->with('state', 'country')->first();
        if(empty($shippingAddress)){
            $shippingAddress = BuyerShippingAddress::where('user_id', Auth::user()->id)->with('state', 'country')->first();
        }

        $data['user'] = $user;
        $data['unread_messages'] = $unread_messages;
        $data['orders'] = $orders;
        $data['buyer'] = $buyer;
        $data['setting'] = $setting;
        $data['shippingAddress'] = $shippingAddress;
        $data['countries'] = Country::orderBy('name')->get();
        $data['states'] = State::orderBy('name')->get();
        $data['editShippingInfo'] = BuyerShippingAddress::with('state', 'country')->where('user_id', auth()->user()->id)->first();
        $data['buyerAvatar'] = MetaBuyer::where('user_id', auth()->user()->id)->first();

        return view('buyer.profile.buyer_overview',$data);
    }
    public function editShippingInfo()
    {
        $data['countries'] = Country::orderBy('name')->get();
        $data['states'] = State::orderBy('name')->get();
        $data['editShippingInfo'] = BuyerShippingAddress::with('state', 'country')->where('user_id', auth()->user()->id)->first();
        return view('buyer.profile.shipping_information', $data);
    }

    public function updateShippingInfo(Request $request)
    {
        $rules  = [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'country' => 'required',
            'phone' => 'required',
        ];
        $request->validate($rules);

        $user = Auth::user();
        $user->load('buyerShipping');

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->buyerShipping->address = $request->address;
        $user->buyerShipping->city = $request->city;
        $user->buyerShipping->state_text = $request->state_text;
        $user->buyerShipping->state_id = $request->state;
        $user->buyerShipping->zip = $request->zip;
        $user->buyerShipping->country_id = $request->country;
        $user->buyerShipping->phone = $request->phone;
        $user->save();
        $user->buyerShipping->save();

        return redirect()->back()->with('flash_message_success', 'Profile Updated Successfully.');
    }

    public function myInformation()
    {
        $data['buyerInfo'] = MetaBuyer::where('user_id',auth()->user()->id)->first();
        return view('buyer.profile.information',$data);
    }

    public function getChangeAvatar()
    {
        $data['title'] = "Change Avatar";
        $data['buyerAvatar'] = MetaBuyer::where('user_id', auth()->user()->id)->first();
        return view('buyer.profile.change_avatar', $data);
    }

    public function changeAvatar(Request $request)
    {

        $user_id = auth()->user()->id;

        if ( $request->hasFile('avatar') ) {
            $image          = $request->file('avatar');
            $filename       = Uuid::generate()->string.$image->getClientOriginalName();
            $destination    = MetaBuyer::avatar_path();
            $successUpload = $image->move($destination, $filename);

            $customer = MetaBuyer::where('user_id', $user_id)->firstOrFail();
            $customer->avatar = $filename;
            $customer->save();

            return redirect()->back()->with('flash_message_success', 'Profile Image Updated Successfully.');;
        }
        else{
            return redirect()->back();
        }
    }



    public function buyerBilling()
    {
        $data['title'] = "Buyer Billing Information Update";
        $data['countries'] = Country::orderBy('name')->get();
        $data['states'] = State::orderBy('name')->get();

        $data['buyerBillingInfo'] = MetaBuyer::where('user_id', auth()->user()->id)->first();
        $data['buyerAvatar'] = MetaBuyer::where('user_id', auth()->user()->id)->first();
        return view('buyer.profile.billing', $data);
    }

    public function editBillingAddress(Request $request)
    {
        $rules  = [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'address' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'phone' => 'required',

        ];
        $request->validate($rules);

        $buyer = Auth::user();
        $buyer->first_name = $request->first_name;
        $buyer->last_name = $request->last_name;
        $buyer->save();

        $buyer = MetaBuyer::where('id', Auth::user()->buyer_meta_id)->first();
        // $buyer->billing_location = $request->factoryLocation;
        $buyer->billing_address = $request->address;
        $buyer->billing_city = $request->city;
        $buyer->billing_state = $request->state_text;
        $buyer->shipping_state_id = $request->state;
        $buyer->billing_zip = $request->zip;
        $buyer->billing_country_id = $request->country;
        $buyer->billing_phone = $request->phone;
        $buyer->billing_state_id = $request->state;
        // $buyer->billing_unit = $request->factoryUnit;
        // $buyer->billing_fax = $request->factoryFax;
        // $buyer->billing_commercial = ($request->factoryCommercial == null) ? 0 : 1;
        $buyer->save();

        return redirect()->back()->with('flash_message_success', 'Billing Information Updated!');
    }

    public function orders()
    {
        $orders = Order::where('status', '!=', OrderStatus::$INIT)->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        $unread_messages = '';
        if(Auth::user()){
            $unread_messages = BuyerMessage::where('user_id', Auth::user()->id)
                ->where('reading_status', 0)
                ->count();
        }
        $data['buyerAvatar'] = MetaBuyer::where('user_id', auth()->user()->id)->first();
        return view('buyer.profile.orders', compact('orders','unread_messages'),$data)->with('page_title', 'My Orders');
    }

    public function message()
    {
        $messages = BuyerMessage::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'asc')
            ->paginate(5);
        if(Auth::user()){
            $unread_messages = BuyerMessage::where('user_id', Auth::user()->id)
                ->where('reading_status', 0)
                ->count();
        }


        return view('buyer.profile.messages', compact('messages','unread_messages'))->with('profile_page', 'My All Messages');
    }

    public function message_unread_count(Request $request)
    {
        BuyerMessage::where('id', $request->id)
            ->update(['reading_status' => 1]);
    }

    public function send_message_admin(Request $request)
    {
        if(!empty($request->file('attachment1'))){
            $attachment1 = $this->attachmentUpload($request->file('attachment1'));
        }

        if(!empty($request->file('attachment2'))){
            $attachment2 = $this->attachmentUpload($request->file('attachment2'));
        }

        if(!empty($request->file('attachment3'))){
            $attachment3 = $this->attachmentUpload($request->file('attachment3'));
        }

        AdminMessage::create([
            'user_id' => $request->message_user_id,
            'sender' => $request->message_sender,
            'recipient' => $request->message_recipient,
            'subject' => $request->message_subject,
            'message' => $request->message,
            'attachment1' => $attachment1 ?? '',
            'attachment2' => $attachment2 ?? '',
            'attachment3' => $attachment3 ?? '',
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);

        $request->session()->flash('message', 'Message Send To '.$request->message_recipient);
        return  Redirect::back();
    }

    public function attachmentUpload( $attachment = null)
    {
        $filename = Uuid::generate()->string;
        $file = $attachment;
        $ext = $file->getClientOriginalExtension();

        $destinationPath = '/buyer_message_attachment';
        $file->move(public_path($destinationPath), $filename.".".$ext);
        $imagePath = $filename.".".$ext;

        return $imagePath;
    }
    
    public function addbillingaddress(Request $request) { 
        $state_id = null;
        $state = null;
        if($request->defaultaddress == 1){
            BuyerBillingAddress::where('user_id', Auth::user()->id)->update(['default' => 0]);
        }
        if ($request->factoryLocation == "INT")
            $state = $request->factoryState;
        else
            $state_id = $request->factoryStateSelect;

        $default_billingaddress = BuyerBillingAddress::create([
            'user_id' => Auth::user()->id, 
            'billing_location' => $request->factoryLocation,
            'billing_address' => $request->factoryAddress,
            'billing_unit' => $request->factoryUnit,
            'billing_city' => $request->factoryCity,
            'billing_state_id' => $state_id,
            'billing_state' => $state,
            'billing_zip' => $request->factoryZipCode,
            'billing_country_id' => $request->factoryCountry,
            'billing_phone' => $request->factoryPhone, 
            'billing_fax' => $request->factoryfax, 
            'default' => $request->defaultaddress ? $request->defaultaddress : 0, 
        ]);

        return response()->json($default_billingaddress->toArray());
    }
}
