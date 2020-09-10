<?php

namespace App\Http\Controllers\Admin;

use App\Model\BuyerShippingAddress;
use App\Model\Country;
use App\Model\MetaBuyer;
use App\Model\State;
use App\Model\StoreCredit;
use App\Model\StoreCreditTransection;
use App\Model\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Uuid;
use Illuminate\Support\Facades\App;
use App\Enumeration\Role;
use Spatie\Newsletter\NewsletterFacade as Newsletter;
use App\Events\UserRegistered;
use App\Model\BuyerBillingAddress;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class BuyerController extends Controller
{
    protected $allCustomerLists;

    public function __construct(MetaBuyer $allCustomerLists)
    {
        $this->allCustomerLists = $allCustomerLists;
    }
    public function allBuyer(Request $request) {
        $data['page_title'] = 'All Customer';
        $all=$this->allCustomerLists;

        if($request->has('status') && $request->status != null)
        {
            if($request->status == 'all'){
                $all=$all->orderBy('created_at', 'desc');
            }

            if($request->status == 'verified' || $request->status == 'notverified'){
                $statusVarified = ($request->status == 'verified') ? 1:0;
                $all=$all->where('verified','=',$statusVarified);
            }
            if($request->status == 'active' || $request->status == 'inactive'){
                $statusActive = ($request->status == 'active') ? 1:0;
                $all=$all->where('active','=',$statusActive);
            }
        }
        
        if($request->has('company_name')&& $request->company_name!=null){
            $all= $all->where('company_name','like','%' .$request->company_name. '%');

        }
        if($request->has('customer_name')&& $request->customer_name!=null){

            $get_user_name_id = User::Select('id')->whereRaw('CONCAT(first_name, " ", last_name) LIKE ? ', '%' . $request->customer_name . '%')->get();
            $all= $all->whereIn('user_id',$get_user_name_id);

        }
        if (isset($request->sort_by)) {
            if ($request->sort_by=='name_asc') {
                $all = $all->leftJoin('users', 'meta_buyers.id', '=', 'users.buyer_meta_id');
                $all =  $all->select('meta_buyers.*', 'users.buyer_meta_id', 'users.first_name')->orderBy('users.first_name', 'asc');
            }elseif($request->sort_by=='name_desc'){
                $all = $all->leftJoin('users', 'meta_buyers.id', '=', 'users.buyer_meta_id');
                $all =  $all->select('meta_buyers.*', 'users.buyer_meta_id', 'users.first_name')->orderBy('users.first_name', 'desc');
            }elseif ($request->sort_by=='email_asc') {
                $all = $all->leftJoin('users', 'meta_buyers.id', '=', 'users.buyer_meta_id');
                $all =  $all->select('meta_buyers.*', 'users.buyer_meta_id', 'users.email')->orderBy('users.email', 'asc');
            }elseif($request->sort_by=='email_desc'){
                $all = $all->leftJoin('users', 'meta_buyers.id', '=', 'users.buyer_meta_id');
                $all =  $all->select('meta_buyers.*', 'users.buyer_meta_id', 'users.email')->orderBy('users.email', 'desc');
            }elseif ($request->sort_by=='company_name_asc') {
                $all= $all->orderBy('company_name', 'asc');
            }elseif($request->sort_by=='company_name_desc'){
                $all= $all->orderBy('company_name', 'desc');
            }elseif ($request->sort_by=='created_at_asc') {
                $all= $all->orderBy('created_at', 'asc');
            }elseif($request->sort_by=='created_at_desc'){
                $all= $all->orderBy('created_at', 'desc');
            }elseif ($request->sort_by=='last_login_asc') {
                $all = $all->leftJoin('users', 'meta_buyers.id', '=', 'users.buyer_meta_id');
                $all =  $all->select('meta_buyers.*', 'users.buyer_meta_id', 'users.last_login')->orderBy('users.last_login', 'asc');
            }elseif($request->sort_by=='last_login_desc'){
                $all = $all->leftJoin('users', 'meta_buyers.id', '=', 'users.buyer_meta_id');
                $all =  $all->select('meta_buyers.*', 'users.buyer_meta_id', 'users.last_login')->orderBy('users.last_login', 'desc');
            }else{
                $all=$all->orderBy('created_at', 'desc');
            }
        }else{
            $all=$all->orderBy('created_at', 'desc');
        }
        
        $data['buyers'] = $all->where('meta_buyers.deleted_at', '=', null)->paginate(20);
        return view('admin.dashboard.customer.all', $data);
    }
    // import customer
    public function importCustomer(Request $request) {
        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();

        if (in_array($ext, ['xlsx', 'csv', 'xls'])) {
            $excel = Excel::load($file->getRealPath(), function ($reader) {
                $content = $reader->get();
            });

            $customers = $excel->get()->toArray();

            if (sizeof($customers) == 0)
                return redirect()->back()->with('error', 'Invalid file');
            DB::beginTransaction();
            try {
                foreach ($customers as $key => $customer) {
                    $billing_address = isset($customer['address1']) ? (isset($customer['address2']) ? $customer['address1'].' ' . $customer['address2'] : $customer['address1']) : '';
                    $billing_zip = isset($customer['zip']) ? (isset(explode("'",$customer['zip'])[1]) ? explode("'",$customer['zip'])[1] : '') : '';
                    $billing_country_id = Country::where('code',  $customer['country_code'] ??'')->first();
                    $meta = MetaBuyer::create([
                        'verified' => 1,
                        'active' => 1,
                        'user_id' => 0,
                        'company_name' => $customer['company'] ?? '',
                        'primary_customer_market' => 1,
                        'seller_permit_number' => '',
                        'sell_online' => null,
                        'website' => null,
                        'attention' => null,
                        'billing_location' => $customer['country_code'] ?? '',
                        'billing_address' => $billing_address,
                        'billing_unit' => null,
                        'billing_city' => $customer['city'] ?? '',
                        'billing_state_id' => null,
                        'billing_state' => null,
                        'billing_zip' => $billing_zip,
                        'billing_country_id' => $billing_country_id ? $billing_country_id->id : 0,
                        'billing_phone' => isset($customer['phone']) ? explode('.',$customer['phone'])[0] : 0,
                        'billing_fax' => null,
                        'billing_commercial' => null,
                        'hear_about_us' => null,
                        'hear_about_us_other' => null,
                        'receive_offers' => 1,
                        'mailing_list' => 1,
                        'ein_path' => null,
                    ]);
                    $customerEmail = $customer['email'] ?? ''; 
                    $userExist = User::where('email',$customerEmail)->first();
                    if($userExist){
                        $meta->forceDelete();
                    }else{
                        $user = User::create([
                            'first_name' => $customer['first_name'] ?? '',
                            'last_name' =>  $customer['last_name'] ?? '',
                            'email' => $customerEmail,
                            'password' => Hash::make(123456),
                            'role' => Role::$BUYER,
                            'buyer_meta_id' => $meta->id,
                        ]);

                        // update meta buyer
                        $meta->user_id = $user->id;
                        $meta->save();

                        $existShippingAddress = BuyerShippingAddress::where('user_id', $user->id)->first();

                        if(!$existShippingAddress){
                            BuyerShippingAddress::create([
                                'user_id' => $user->id,
                                'default' => 1,
                                'store_no' => null,
                                'location' => $customer['country_code'] ?? '',
                                'address' => $billing_address,
                                'unit' => null,
                                'city' => $customer['city'] ?? '',
                                'state_id' => null,
                                'state_text' => null,
                                'zip' => $billing_zip,
                                'country_id' => $meta->billing_country_id,
                                'phone' => isset($customer['phone']) ? explode('.',$customer['phone'])[0] : 0,
                                'fax' => null,
                                'commercial' => isset($customer['commercial']) ? 1 : 0,
                            ]);
                        }

                        $existBillingAddress = BuyerBillingAddress::where('user_id', $user->id)->first();
                        if(!$existBillingAddress){
                            BuyerBillingAddress::create([
                                'user_id' => $user->id, 
                                'billing_location' => $customer['country_code'] ?? '',
                                'billing_address' => $billing_address,
                                'billing_unit' => null,
                                'billing_city' => $customer['city'] ?? '',
                                'billing_state_id' => null,
                                'billing_state' => null,
                                'billing_zip' => $billing_zip,
                                'billing_country_id' => $meta->billing_country_id,
                                'billing_phone' => isset($customer['phone']) ? explode('.',$customer['phone'])[0] : 0, 
                                'billing_fax' => null, 
                                'default' => 1, 
                            ]);
                        }
                    }
                }
            } catch(\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('error', 'Somthing Wrong!');
            }
            DB::commit();
            return redirect()->back()->with('message', 'Imported Customer successfully!');
        } else {
            return redirect()->back()->with('error', 'Invalid file');
        }
    }
    // import customer

    public function changeStatus(Request $request) {
        $buyers = MetaBuyer::where('id', $request->id)->first();
        $buyers->active = $request->status;
        $buyers->save();
    }

    public function changeVerified(Request $request) {
        $buyers = MetaBuyer::where('id', $request->id)->first();
        $buyers->verified = $request->status;
        $buyers->save();
        
        $user = User::where('id', $buyers->user_id)->first();
        Mail::send('emails.buyer.verified', [], function ($message) use ($user) {
            $message->subject('Verified Complete');
            $message->to($user->email, $user->first_name.' '.$user->last_name);
        });
    }

    public function changeMailingList(Request $request) {
        $buyers = MetaBuyer::where('id', $request->id)->first();
        $buyers->mailing_list = $request->mailing_list;
        $buyers->save();

        // Get user data by id
        $user = User::find($request->user_id);
        $user->phone = $request->billing_phone;
        
        // Trigger event for mailchimp
        event(new UserRegistered($user));
    }

    public function changeBlock(Request $request) {
        $buyers = MetaBuyer::where('id', $request->id)->first();
        $buyers->block = $request->status;
        $buyers->save();
    }

    public function changeMinOrder(Request $request) {
        $buyers = MetaBuyer::where('id', $request->id)->first();
        $buyers->min_order = $request->status;
        $buyers->save();
    }

    public function edit(MetaBuyer $buyer) {
        $buyer->load('user');
        $countries = Country::orderBy('name')->get();
        $usStates = State::where('country_id', 1)->orderBy('name')->get()->toArray();
        $caStates =State::where('country_id', 2)->orderBy('name')->get()->toArray();
        $buyerShippingAddress=BuyerShippingAddress::where('user_id', $buyer->user->id)->where('default', 1)->first();

        // dd($buyerShippingAddress);

        return view('admin.dashboard.customer.edit', compact('countries', 'usStates', 'caStates', 'buyer','buyerShippingAddress'))->with('page_title', 'Edit Customer');
    }

    public function allBuyerExport() {
        $allCustomer = User::with('buyer')->get();
        $customersArray = [];
        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        $stateID=null;
        foreach ($allCustomer as $customer) {
            $sid=\App\Model\State::where('id',(isset($customer->buyer->billing_state_id)) ? $customer->buyer->billing_state_id:null)->first();
            if($sid){
                $stateID =$sid->code;
            }
            $customersArray[] = ['Business Name' => (isset($customer->buyer->company_name)) ? $customer->buyer->company_name : null,
                'Name' => $customer->first_name . ' ' . $customer->last_name,
                'Email' => $customer->email,
                'Address' => (isset($customer->buyer->billing_address)) ? $customer->buyer->billing_address : null,
                'Unit #' => (isset($customer->buyer->billing_unit)) ? $customer->buyer->billing_unit : null,
                'Phone' => (isset($customer->buyer->billing_phone)) ? $customer->buyer->billing_phone : null,
                'City' => (isset($customer->buyer->billing_city)) ? $customer->buyer->billing_city : null,
                'State' => (isset($stateID)) ? $stateID : null,
                'Zipcode' => (isset($customer->buyer->billing_zip)) ? $customer->buyer->billing_zip : null,
                'Fax' => (isset($customer->buyer->billing_fax)) ? $customer->buyer->billing_fax : null,
                'Website' => (isset($customer->buyer->website)) ? $customer->buyer->website : null,
                'Approved?' => ($customer->active == 1) ? 'Y' : 'N',
                'Approved At' => $customer->updated_at,
                'Created At' => $customer->created_at,
                'Orders' => $customer->order_count,
                'Last Login' => $customer->last_login];
//            $customersArray[] = $customer->toArray();
        }

        // Generate and return the spreadsheet
        $excel = App::make('excel');
        $excel->create('customers', function($excel) use ($customersArray) {
            // Set the spreadsheet title, creator, and description
            //$excel->setTitle('Customer');
           // $excel->setCreator('CustomersLists')->setCompany('CQBYCQ');
            //$excel->setDescription('customer lists file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('CustomerLists', function($sheet) use ($customersArray) {
           $sheet->fromArray($customersArray);
            });

        })->download('xlsx');
        return redirect()->back();
    }

    public function editPost(MetaBuyer $buyer, Request $request) {
 
        $messages = [
            'required' => 'This field is required.',
        ];

        $rules = [
            'companyName' => 'required|string|max:255',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$buyer->user->id,
            'sellerPermitNumber' => 'required|string|max:255',
            'address' => 'required|string|max:255',
             'unit' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'zipCode' => 'required|string|max:255',
            'country' => 'required',
            'phone' => 'required|max:255',
            'fax' => 'nullable|max:255',
            'factoryAddress' => 'required|string|max:255',
            'factoryUnit' => 'nullable|string|max:255',
            'factoryCity' => 'required|string|max:255',
            'factoryZipCode' => 'required|string|max:255',
            'factoryCountry' => 'required',
            'factoryPhone' => 'required|max:255',
            'factoryFax' => 'nullable|max:255' 
        ];

        if ($request->location == "INT")
            $rules['state'] = 'required|string|max:255';
        else
            $rules['stateSelect'] = 'required';

        if ($request->factoryLocation == "INT")
            $rules['factoryState'] = 'required|string|max:255';
        else
            $rules['factoryStateSelect'] = 'required';
 

        if ($request->sellOnline && $request->sellOnline == '1')
            $rules['website'] = 'required|string|max:255';

        if ($request->password != '')
            $rules['password'] = 'required|string|min:6';

        $request->validate($rules, $messages);

        $state_id = null;
        $state = null;
        $factory_state_id = null;
        $factory_state = null;
        $hearFromOtherText = null;

        if ($request->location == "INT")
            $state = $request->state;
        else
            $state_id = $request->stateSelect;

        if ($request->factoryLocation == "INT")
            $factory_state = $request->factoryState;
        else
            $factory_state_id = $request->factoryStateSelect; 

        $buyer->company_name = $request->companyName;
        $buyer->points = $request->points;
        $buyer->points_spent = $request->points_spent;
        $buyer->primary_customer_market = $request->primaryCustomerMarket;
        $buyer->seller_permit_number = $request->sellerPermitNumber;
        $buyer->sell_online = $request->sellOnline;
        $buyer->website = $request->website;
        /*$buyer->shipping_location = $request->location;
        $buyer->store_no = $request->store_no;*/
        $buyer->attention = $request->attention;
        /* $buyer->shipping_address = $request->address;
        $buyer->shipping_unit = $request->unit;
        $buyer->shipping_city = $request->city;
        $buyer->shipping_state_id = $state_id;
        $buyer->shipping_state = $state;
        $buyer->shipping_zip = $request->zipCode;
        $buyer->shipping_country_id = $request->country;
        $buyer->shipping_phone = $request->phone;
        $buyer->shipping_fax = $request->fax;
        $buyer->shipping_commercial = ($request->showroomCommercial == null) ? 0 : 1;*/
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
        $buyer->billing_commercial = ($request->factoryCommercial == null) ? 0 : 1; 
//        $buyer->hear_about_us_other = $hearFromOtherText;
        $buyer->receive_offers = $request->receiveSpecialOffers;



        $buyer->user->first_name = $request->firstName;
        $buyer->user->last_name = $request->lastName;
        $buyer->user->email = $request->email;
        $checkBuyerShippingAddress = BuyerShippingAddress::where('user_id', $buyer->user->id)->where('default', 1)->first();
        if(isset($checkBuyerShippingAddress)){

        $buyerShipping = BuyerShippingAddress::find($checkBuyerShippingAddress->id);
            $buyerShipping->default = 1;
            $buyerShipping->store_no = $request->store_no;
            $buyerShipping->location = $request->location;
            $buyerShipping->address = $request->address;
            $buyerShipping->unit = $request->unit;
            $buyerShipping->city = $request->city;
            $buyerShipping->state_id = $state_id;
            $buyerShipping->state_text = $state;
            $buyerShipping->zip = $request->zipCode;
            $buyerShipping->country_id = $request->country;
            $buyerShipping->phone = $request->phone;
            $buyerShipping->fax = $request->fax;
            $buyerShipping->commercial = ($request->showroomCommercial == null) ? 0 : 1;
            $buyerShipping->save();
        }
        if ($request->password != '')
            $buyer->user->password = Hash::make($request->password);

        $buyer->save();
        $buyer->user->save();

        return redirect()->route('admin_all_buyer')->with('message', 'Updated!');
    }

    public function delete(Request $request) {
        
        $meta = MetaBuyer::where('id', $request->id)->first();
        $user = User::where('id', $meta->user->id)->first();
        $user->email = $user->email.'-deleted'."-".$user->id;
        $user->save();
        
        StoreCredit::where('user_id', $user->id)->delete();
        StoreCreditTransection::where('user_id', $user->id)->delete();

        $user->delete();
        $meta->delete();
    }

    public function customerCreate(){
        $countries = Country::orderBy('name')->get();
        $usStates = State::where('country_id', 1)->orderBy('name')->get()->toArray();
        $caStates = State::where('country_id', 2)->orderBy('name')->get()->toArray();

        return view('admin.dashboard.customer.create', compact('countries', 'usStates', 'caStates'))->with('page_title', 'Create Customer');
    }

    public function customerPost(Request $request){
        $messages = [
            'required' => 'This field is required.',
            'required_without_all' => 'The :attribute field is required when none of :values are present.',
        ];

        $rules = [
            'companyName' => 'required|string|max:255',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'email' => 'required|string|email|max:255|unique:users',
            'sellerPermitNumber' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'unit' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'zipCode' => 'required|string|max:255',
            'country' => 'required',
            'phone' => 'required|max:255',
            'fax' => 'nullable|max:255',
            'factoryAddress' => 'required|string|max:255',
            'factoryUnit' => 'nullable|string|max:255',
            'factoryCity' => 'required|string|max:255',
            'factoryZipCode' => 'required|string|max:255',
            'factoryCountry' => 'required',
            'factoryPhone' => 'required|max:255',
            'factoryFax' => 'nullable|max:255',
            'ein' => 'required|mimes:jpeg,jpg,png,pdf',
        ];
          

        if ($request->location == "INT")
            $rules['state'] = 'required|string|max:255';
        else
            $rules['stateSelect'] = 'required';

        if ($request->sellOnline && $request->sellOnline == '1')
            $rules['website'] = 'required|string|max:255';
            
         

        $request->validate($rules, $messages);

        $state_id = null;
        $state = null;
        $factory_state_id = null;
        $factory_state = null;
        $hearFromOtherText = null;

        if ($request->location == "INT")
            $state = $request->state;
        else
            $state_id = $request->stateSelect;

        if ($request->factoryLocation == "INT")
            $factory_state = $request->factoryState;
        else
            $factory_state_id = $request->factoryStateSelect;

        $einPath = null;
        $destinationPath = '/files/buyer';
        if ($request->ein && $request->ein != null) {

            $filename = Uuid::generate()->string;
            $file = $request->file('ein');
            $ext = $file->getClientOriginalExtension();
            $file->move(public_path($destinationPath), $filename . "." . $ext);
            $einPath = $destinationPath . "/" . $filename . "." . $ext;
        }

        if(!empty($request->receiveSpecialOffers)){ 
            $mailing=$request->receiveSpecialOffers;
        }else{$mailing=0;}
        $meta = MetaBuyer::create([
            'verified' => 0,
            'active' => 1,
            'user_id' => 0,
            'company_name' => $request->companyName,
            'primary_customer_market' => $request->primaryCustomerMarket,
            'seller_permit_number' => $request->sellerPermitNumber,
            'sell_online' => $request->sellOnline,
            'website' => $request->website,
            'attention' => $request->attention,
            'billing_location' => $request->factoryLocation,
            'billing_address' => $request->factoryAddress,
            'billing_unit' => $request->factoryUnit,
            'billing_city' => $request->factoryCity,
            'billing_state_id' => $factory_state_id,
            'billing_state' => $factory_state,
            'billing_zip' => $request->factoryZipCode,
            'billing_country_id' => $request->factoryCountry,
            'billing_phone' => $request->factoryPhone,
            'billing_fax' => $request->factoryFax,
            'billing_commercial' => ($request->factoryCommercial == null) ? 0 : 1,
            'receive_offers' => $request->receiveSpecialOffers,
            'ein_path' => $einPath,
            'mailing_list' =>$mailing ,
        ]);

        $user = User::create([
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => Role::$BUYER,
            'buyer_meta_id' => $meta->id,
        ]);

        BuyerShippingAddress::create([
            'user_id' => $user->id,
            'default' => 1,
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

        $meta->user_id = $user->id;
        $meta->save();
        
        // Merge address and phone into user object
        $user->phone = $request->phone;
        $user->local_address = $request->address;
         
        // Trigger event for mailchimp 
        // if(! Newsletter::isSubscribed($user->email)){
        //     Newsletter::subscribePending($user->email);  
        // }
         
        // event(new UserRegistered($user));

        // Send Mail to User
        Mail::send('emails.buyer.registration_complete', [], function ($message) use ($request) {
            $message->subject('Registration Complete');
            $message->to($request->email, $request->firstName.' '.$request->lastName);
        });

        return redirect()->route('customer_register_complete');
    }
    
    public function customerComplete() {
        return view('admin.dashboard.customer.complete')->with('page_title', 'Customer Registration Complete');
    }
}
