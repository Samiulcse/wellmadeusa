<?php

namespace App\Http\Controllers;

use App\Enumeration\PageEnumeration;
use App\Enumeration\VendorImageType;
use App\Model\VendorImage;
use App\Model\Page;
use Illuminate\Http\Request;
use App\Model\Order;
use App\Model\Season;
use App\Enumeration\OrderStatus;
use Illuminate\Support\Facades\Auth;
use App\Model\AppointmentTime; 
use App\Model\Appointment;
use App\Model\Task;
use App\Model\Event;
use MaddHatter\LaravelFullcalendar\Facades\Calendar;
class StaticPageController extends Controller
{
    public function aboutUs() {
        $content = '';

        $page = Page::where('page_id', PageEnumeration::$ABOUT_US)->first();
        if ($page)
            $content = $page->content;


        return view('pages.static', compact('content'))->with('title', 'About Us');
    }

    public function contactUs() {
        $content = '';

        $page = Page::where('page_id', PageEnumeration::$CONTACT_US)->first();
        if ($page)
            $content = $page->content;


        return view('pages.static', compact('content'))->with('title', 'Contact Us');
    }

    public function privacyPolicy() {
        $content = '';

        $page = Page::where('page_id', PageEnumeration::$PRIVACY_POLICY)->first();
        if ($page)
            $content = $page->content;


        return view('pages.static', compact('content'))->with('title', 'Privacy Policy');
    }

    public function returnInfo() {
        $content = '';

        $page = Page::where('page_id', PageEnumeration::$RETURN_INFO)->first();
        if ($page)
            $content = $page->content;


        return view('pages.static', compact('content'))->with('title', 'Return Info');
    }

    public function billingShipping() {
        $content = '';

        $page = Page::where('page_id', PageEnumeration::$BILLING_SHIPPING_INFO)->first();
        if ($page)
            $content = $page->content;


        return view('pages.static', compact('content'))->with('title', 'Billing & Shipping Info');
    }

    public function largeQuantities() {
        $content = '';

        $page = Page::where('page_id', PageEnumeration::$LARGE_QUANTITIES)->first();
        if ($page)
            $content = $page->content;

        return view('pages.static', compact('content'))->with('title', 'Large Quantities / Pre Orders');
    }

    public function refunds() {
        $content = '';

        $page = Page::where('page_id', PageEnumeration::$REFUNDS)->first();
        if ($page)
            $content = $page->content;


        return view('pages.static', compact('content'))->with('title', 'Refunds & Replacements');
    }

    public function faq() {
        $content = '';

        $page = Page::where('page_id', PageEnumeration::$FAQ)->first();
        if ($page)
            $content = $page->content;


        return view('pages.static', compact('content'))->with('title', 'FAQ');
    }

    public function contactUsPost(Request $request) {
        return 'we are comming soon';
    }

    public function termsConditions() {
        $content = '';

        $page = Page::where('page_id', PageEnumeration::$TERMS_AND_CONDIOTIONS)->first();

        if ($page)
            $content = $page->content;


        return view('pages.static', compact('content'))->with('title', 'Terms & Conditions');
    }

    public function cookiesPolicy() {
        $content = '';

        $page = Page::where('page_id', PageEnumeration::$COOKIES_POLICY)->first();
        if ($page)
            $content = $page->content;


        return view('pages.static', compact('content'))->with('title', 'Cookies Policy');
    }

    public function sizeGuide()
    {
        $content = '';

        $page = Page::where('page_id', PageEnumeration::$SIZE_GUIDE)->first();
        if ($page)
            $content = $page->content;

        return view('pages.static', compact('content'))->with('title', 'Size Guide');
    }

    public function checkOrders()
    {
        $orders = Order::where('status', '!=', OrderStatus::$INIT)->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();

        return view('buyer.profile.orders', compact('orders'))->with('title', 'Check Orders');
    }

    public function returnPolicy()
    {
        $content = '';

        $page = Page::where('page_id', PageEnumeration::$RETURN_POLICY)->first();
        if ($page)
            $content = $page->content;

        return view('pages.static', compact('content'))->with('title', 'Return Policy');
    }

    public function shipping()
    {
        $content = '';
        $page = Page::where('page_id', PageEnumeration::$SHIPPING)->first();
        if ($page)
            $content = $page->content;
        return view('pages.static', compact('content'))->with('title', 'Shipping');
    }
    public function customer_care()
    {
        $content = '';
        $page = Page::where('page_id', PageEnumeration::$CUSTOMER_CARE)->first();
        if ($page)
            $content = $page->content;
        return view('pages.static', compact('content'))->with('title', 'Customer Care');
    }

    public function look_book()
    {
        $content = '';
        $page = Page::where('page_id', PageEnumeration::$LOOK_BOOK)->first();
        if ($page)
            $content = $page->content;
        $seasons = Season::all();
        $items = VendorImage::where('type',VendorImageType::$SESON_BANNER)->orderBy('sort','asc')->get();
        return view('pages.lookbook', compact('content','items','seasons'))->with('title', 'Lookbook');
    }
    public function select_lookbook_slider(Request $request){
        $items = VendorImage::where('type',VendorImageType::$SESON_BANNER)->where('head',$request->id)->orderBy('sort','asc')->get();
        return response()->json(['items'=>$items],200);
    }
    public function appointment() {
        $AppointmentTime = AppointmentTime::all(); 
        
        $events = [];
        $data = [];
        if(Auth::user()){
            $data = Appointment::Where('user_id',Auth::user()->id)->get();
        } 
        if(count($data)>0)
            {
            foreach ($data as $key => $value)
            {
                $events[] = Calendar::event(
                    $value->name,
                    $value->desc,
                    true,
                    new \DateTime($value->start_date),
                    new \DateTime($value->end_date.'+1 day'),
                    null,
                    [
                        'color' => $value->lable_bg?  "#".$value->lable_bg: '#000000',
                        'textColor' => $value->color?  "#".$value->color: '#ffffff',
                    ]
                );
            }
        }
         
        $calendar = Calendar::addEvents($events);
        $content = '';
        $page = Page::where('page_id', PageEnumeration::$SHOW_SCHEDULE)->first();
        if ($page)
            $content = $page->content;  
        return view('pages.appoinment', compact('calendar','content','AppointmentTime' ))->with('title', 'Book Your Virtual Appointment');
    }
    public function aubmitappontment(Request $request){
        $count = Appointment::where('desc',$request->time)->where('start_date',$request->date)->count();
        if($count > 0){
            return response()->json(['already'=>1,'success'=>0],200);
        }else{ 
            $data = Appointment::create([
                'name'=>$request->note,
                'user_id'=> Auth::user()->id, 
                'desc'=>$request->time,   
                'start_date'=>$request->date,
            ]);
            if($data){
                return response()->json(['already'=>0,'success'=>1],200);
            }else{
                return response()->json(['already'=>0,'success'=>0],200);
            }
        }
    }
    public function show_schedule()
    { 
        $events = [];
        $data = Event::all();
        if($data->count())
            {
            foreach ($data as $key => $value)
            {
                $events[] = Calendar::event(
                    $value->name,
                    $value->desc,
                    true,
                    new \DateTime($value->start_date),
                    new \DateTime($value->end_date.'+1 day'),
                    null,
                    [
                        'color' => $value->lable_bg?  "#".$value->lable_bg: '#000000',
                        'textColor' => $value->color?  "#".$value->color: '#ffffff',
                    ]
                );
            }
        }
        $calendar = Calendar::addEvents($events);
        $content = '';
        $page = Page::where('page_id', PageEnumeration::$SHOW_SCHEDULE)->first();
        if ($page)
            $content = $page->content;

        
       
        return view('pages.task_schedule', compact('calendar','content', ))->with('title', 'Show Schedule');
    }

    public function myaccount(){
        return view('pages.myaccount');
    }
    public function mysave(){
        return view('pages.mysave');
    }
    public function complete(){
        return view('buyer.checkout.complete');
    }
}
