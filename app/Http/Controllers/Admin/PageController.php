<?php

namespace App\Http\Controllers\Admin;

use App\Enumeration\PageEnumeration;
use App\Enumeration\VendorImageType;
use App\Model\Page;
use App\Model\Season;
use App\Model\Meta;
use App\Model\VendorImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DateTime; 
use App\Model\AppointmentTime; 
use App\Model\Appointment;

class PageController extends Controller
{
    public function index($id) {

        $meta = Meta::where('page', $id)->first();
        if (!$meta) {
            $meta = Meta::create([
                'page' => $id
            ]);
        }

        $page = Page::where('page_id', $id)->first();
        if (!$page) {
            $page = Page::create([
                'page_id' => $id,
            ]);
        }

        $title = 'Page/Meta - ';

        if ($id == PageEnumeration::$HOME)
            $title .= 'Home';
        if ($id == PageEnumeration::$ABOUT_US)
            $title .= 'About Us';
        else if ($id == PageEnumeration::$CONTACT_US)
            $title .= 'Contact Us';
        else if ($id == PageEnumeration::$PRIVACY_POLICY)
            $title .= 'Privacy Policy';
        else if ($id == PageEnumeration::$RETURN_INFO)
            $title .= 'Return Info';
        else if ($id == PageEnumeration::$BILLING_SHIPPING_INFO)
            $title .= 'Billing & Shipping Info';
        else if ($id == PageEnumeration::$SIZE_GUIDE)
            $title .= 'Size Guide';
        else if ($id == PageEnumeration::$CHECK_ORDERS)
            $title .= 'Check Orders';
        else if ($id == PageEnumeration::$RETURN_POLICY)
            $title .= 'Return Policy';
        else if ($id == PageEnumeration::$SHIPPING)
            $title .= 'Shipping';
        else if ($id == PageEnumeration::$TERMS_AND_CONDIOTIONS)
            $title .= 'Terms and Conditions';
        else if ($id == PageEnumeration::$COOKIES_POLICY)
            $title .= 'Cookies Policy';
        else if ($id == PageEnumeration::$FAQ)
            $title .= 'Faqs';
        else if ($id == PageEnumeration::$CUSTOMER_CARE)
            $title .= 'Customer Care';
        else if ($id == PageEnumeration::$LOOK_BOOK)
            $title .= 'Lookbook';
        else if ($id == PageEnumeration::$SHOW_SCHEDULE)
            $title .= 'Show Schedule';
        else if ($id == PageEnumeration::$HOME_PAGE_CUSTOM_SECTION)
            $title .= 'Mobile Home Page Custom Section';
        else if ($id == PageEnumeration::$APPOINTMENT)
            $title .= 'Appointment';
            
        if ($id == PageEnumeration::$SHOW_SCHEDULE){ 
            
        } 
        return view('admin.dashboard.page.index', compact('page', 'meta'))->with('page_title', $title);
    }

    public function show_schedule_page(){ 
        $seasons = Season::all();
        $items = VendorImage::where('type',VendorImageType::$SESON_BANNER)->orderBy('sort','asc')->get(); 
        $items = \DB::table('vendor_images')
                ->where('vendor_images.type',VendorImageType::$SESON_BANNER) 
                ->join('season', 'vendor_images.head', '=', 'season.id')
                ->select('season.name', 'vendor_images.*', 'season.name', 'season.id as seasonid')
                ->orderBy('vendor_images.sort','asc')
                ->get();  
        $title = 'Page/Meta - lookbook';
        return view('admin.dashboard.page.schedule', compact('items','seasons'))->with('page_title', $title);
    }
    
    public function Appointment(){
        $title = 'Appointment';
        $appointTime = AppointmentTime::orderBy('time','asc')->get(); 
        $appointmets = Appointment::with('user')->orderBy('id','desc')->paginate(40);  
        return view('admin.dashboard.page.appointment', compact('appointTime','appointmets'))->with('page_title', $title);
    }

    public function add_new_appoint_time(Request $request){ 
        $date = $request->time;    
        AppointmentTime::create([
            'time' => $date, 
            'note' => $request->note,
        ]); 
        return redirect()->back();
    }
    public function appointment_delete(Request $request){
        Appointment::where('id',$request->id)->delete(); 
    }
    public function appointTime_delete(Request $request){
        AppointmentTime::where('id',$request->id)->delete(); 
    } 

    public function add_new_season(Request $request){  
        $request->validate([
            'season_name' => 'required',
            'page_editor'=>'required'
        ]);  
        if(!empty($request->id)){
            $data = VendorImage::where('id', $request->id)->first();
            $data->details=$request->page_editor; 
            $data->head=$request->season_name;
            $data->save(); 
        }else{ 
            VendorImage::create([
                'type' => VendorImageType::$SESON_BANNER,
                'details' => $request->page_editor, 
                'head' =>$request->season_name
            ]); 
        }
         
        return redirect()->back()->with('message', 'Successfully Added!.');
    }
    public function admin_season_delete(Request $request){
        $i=0;
        $sesion = Season::find($request->id); 
        if($sesion->default ==1){
            $i=1;
        }
        Season::find($request->id)->delete();

        if($i==1){
            $season = Season::first();
            $season->default = 1;
            $season->update();
        }
        VendorImage::where('type',VendorImageType::$SESON_BANNER)->where('head',$request->id)->delete();
        return redirect()->back()->with('msg', 'Successfully Deleted!.');
    }

    public function set_default_season(Request $request){
        Season::where('default',1)->update([
            'default'=>0
        ]);

        Season::where('id',$request->id)->update([
            'default'=>1
        ]);
        return redirect()->back()->with('msg', 'Successfully Updated!.');
    }

    public function save(Request $request, $id) {
        Page::where('page_id', $id)->update([
            'content' => $request->page_editor,
        ]);

        Meta::where('page', $id)->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('message', 'Updated!');
    }

    public function new_lookbook_season(Request $request){
        $request->validate([
            'name' => 'required|unique:season,name'
        ]); 
        if(!empty($request->id)){
            $data = Season::where('id',$request->id)->first();
            $data->name=$request->name;
            $data->description=$request->description;
            $data->save();
        }else{
            Season::create([
                'name'=>$request->name,
                'description' => $request->description,
            ]);
        }
        
        return redirect()->back()->with('message', 'New Season Added!');
    }
}
