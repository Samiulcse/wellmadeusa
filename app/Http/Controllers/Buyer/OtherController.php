<?php

namespace App\Http\Controllers\Buyer;

use App\Enumeration\PageEnumeration;
use App\Enumeration\VendorImageType;
use App\Model\BuyerMessage;
use App\Model\MetaVendor;
use App\Model\Notification;
use App\Model\Order;
use App\Model\Page;
use App\Model\VendorImage;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Uuid;

class OtherController extends Controller
{
    public function showOrderDetails(Order $order) {
        $allItems = [];
        $order->load( 'items');

        $vendor = MetaVendor::where('id', 1)->first();
        $order->vendor = $vendor;

        // Logo
        $vendor_logo_path = '';

        $black = \DB::table('settings')->where('name', 'logo-black')->first();
        if ($black)
            $vendor_logo_path = asset($black->value);

        foreach($order->items as $item)
            $allItems[$item->item_id][] = $item;
        $unread_messages = '';
        if(Auth::user()){
            $unread_messages = BuyerMessage::where('user_id', Auth::user()->id)
                ->where('reading_status', 0)
                ->count();
        }
        return view('buyer.order_details', compact('order', 'vendor_logo_path', 'allItems','unread_messages'))->with('page_title', 'Order Details: '.$order->order_number);
    }

    public function viewNotification(Request $request) {
        Notification::where('id', $request->id)->update(['view' => 1]);

        return redirect()->to($request->link);
    }

    public function allNotification() {
        $notifications = Notification::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();

        return view('buyer.notifications', compact('notifications'))->with('page_title', 'Notifications');
    }

    public function orderRejectStatusChange(Request $request) {
        $order = Order::where('id', $request->id)
            ->where('user_id', Auth::user()->id)
            ->first();

        if ($order) {
            $order->rejected = $request->status;
            $order->save();
        }
    }

    public function orderImages(Order $order) {
        if ($order->user_id != Auth::user()->id)
            abort(404);

        $zipFileName = Uuid::generate()->string.'.zip';
        $zip = new \ZipArchive();

        if ($zip->open(public_path('zip') . '/' . $zipFileName, \ZipArchive::CREATE) === TRUE) {
            foreach ($order->items as $item) {
                $count = 1;

                foreach ($item->item->images as $image) {
                    $filename = $item->style_no.'-'.$count.'.jpg';
                    $zip->addFile(public_path($image->image_path), $filename);
                    $count++;
                }
            }
        }

        $zip->close();

        $headers = array(
            'Content-Type' => 'application/octet-stream',
        );

        return response()->download(public_path('zip/'.$zipFileName), $order->order_number.'.zip',$headers);
    }
    public function printPdf(Request $request) {
        $orderIds = explode(',', $request->order);
        $orders = [];
        $items = [];
        $logo_paths = [];
        $vendor = MetaVendor::where('id', 1)->first();
        foreach ($orderIds as $orderId) {
            $order = Order::where('id', $orderId)->with('user', 'items')->first();
            $allItems = [];

            // Decrypt
            $cardNumber = '';
            $cardFullName = '';
            $cardExpire = '';
            $cardCvc = '';

            try {
                $cardNumber = ($order->card_number)?decrypt($order->card_number):'';
                $cardFullName = ($order->card_full_name)?decrypt($order->card_full_name):'';
                $cardExpire = ($order->card_expire)?decrypt($order->card_expire):'';
                $cardCvc = ($order->card_cvc)?decrypt($order->card_cvc):'';
            } catch (DecryptException $e) {

            }

            $order->card_number = $cardNumber;
            $order->card_full_name = $cardFullName;
            $order->card_expire = $cardExpire;
            $order->card_cvc = $cardCvc;

            foreach ($order->items as $item)
                $allItems[$item->item_id][] = $item;

            // Vendor Logo
            $logo_path = '';
            $black = \DB::table('settings')->where('name', 'logo-black')->first();
            if ($black)
                $logo_path = public_path($black->value);

            $order->vendor = $vendor;

            $orders[] = $order;
            $items[] = $allItems;
            $logo_paths[] = $logo_path;
        }

        $page = Page::where('page_id', PageEnumeration::$RETURN_INFO)->first();
        if ($page) {
            $content = $page->content;
        }else {
            $content = '';
        }

        $data = [
            'all_items' => $items,
            'orders' => $orders,
            'logo_paths' => $logo_paths,
            'return_policy_description' => $content
        ];

        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('buyer.profile.pdf.with_image', $data);
        return $pdf->stream('invoice.pdf');
    }
    public function printPdfWithOutImage(Request $request) {
        $orderIds = explode(',', $request->order);
        $orders = [];
        $items = [];
        $logo_paths = [];
        $vendor = MetaVendor::where('id', 1)->first();

        foreach ($orderIds as $orderId) {
            $order = Order::where('id', $orderId)->with('user', 'items')->first();
            $allItems = [];

            // Decrypt
            $cardNumber = '';
            $cardFullName = '';
            $cardExpire = '';
            $cardCvc = '';

            try {
                $cardNumber = ($order->card_number)?decrypt($order->card_number):'';
                $cardFullName = ($order->card_full_name)?decrypt($order->card_full_name):'';
                $cardExpire = ($order->card_expire)?decrypt($order->card_expire):'';
                $cardCvc = ($order->card_cvc)?decrypt($order->card_cvc):'';
            } catch (DecryptException $e) {

            }

            $order->card_number = $cardNumber;
            $order->card_full_name = $cardFullName;
            $order->card_expire = $cardExpire;
            $order->card_cvc = $cardCvc;

            foreach ($order->items as $item)
                $allItems[$item->item_id][] = $item;

            // Vendor Logo
            $logo_path = '';
            $black = \DB::table('settings')->where('name', 'logo-black')->first();
            if ($black)
                $logo_path = public_path($black->value);

            $order->vendor = $vendor;

            $orders[] = $order;
            $items[] = $allItems;
            $logo_paths[] = $logo_path;
        }
        $page = Page::where('page_id', PageEnumeration::$RETURN_INFO)->first();
        if ($page) {
            $content = $page->content;
        }else {
            $content = '';
        }

        $data = [
            'all_items' => $items,
            'orders' => $orders,
            'logo_paths' => $logo_paths,
            'return_policy_description' => $content
        ];
        $pdf = PDF::loadView('buyer.profile.pdf.without_image', $data);
        return $pdf->stream('invoice.pdf');
    }

}
