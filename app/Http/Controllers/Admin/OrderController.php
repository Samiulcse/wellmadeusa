<?php

namespace App\Http\Controllers\Admin;

use App\Enumeration\OrderStatus;
use App\Enumeration\Role;
use App\Enumeration\VendorImageType;
use App\Enumeration\PageEnumeration;
use App\Model\AdminShipMethod;
use App\Model\BuyerMessage;
use App\Model\CartItem;
use App\Model\Category;
use App\Model\User;
use App\Model\Color;
use App\Model\Item;
use App\Model\ItemInv;
use App\Model\Visitor;
use App\Model\MetaVendor;
use App\Model\MetaBuyer;
use App\Model\PointSystem;
use App\Model\Notification;
use App\Model\Order;
use App\Model\OrderItem;
use App\Model\Page;
use App\Model\Coupon;
use App\Model\PromoCodes;
use App\Model\Promotion;
use App\Model\Review;
use App\Model\StoreCredit;
use App\Model\StoreCreditTransection;
use App\Model\VendorImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use PDF;
use Uuid;
//use Auth;
use Validator;
use DB;
use Carbon\Carbon;
use Mail;
use DateTime;
use Maatwebsite\Excel\Facades\Excel;
class OrderController extends Controller
{
    public function allOrders(Request $request) {
        $parameters = [];
        $userIds = [];

        // Search
        if (isset($request->text) && $request->text !='') {
           if (isset($request->search) && $request->search == '4') {
               $metaBuyers = User::whereRaw('CONCAT(first_name, " ", last_name) LIKE ? ', '%' . $request->text . '%')->get();
                foreach ($metaBuyers as $buyer)
                    $userIds[] = $buyer->id;
            } else if (isset($request->search) && $request->search == '1') {
                $metaBuyers = MetaBuyer::where('company_name', 'like', '%' . $request->text . '%')->get();

                foreach ($metaBuyers as $buyer)
                    $userIds[] = $buyer->user_id;
            } else if (isset($request->search) && $request->search == '2') {
                $parameters[] = ['order_number', 'like', '%' . $request->text . '%'];
            } else if (isset($request->search) && $request->search == '3') {
                $parameters[] = ['tracking_number', 'like', '%' . $request->text . '%'];
            }
        }

        if (isset($request->ship) && $request->ship !='') {
            if ($request->ship == '1')
                $parameters[] = ['status', '=', OrderStatus::$PARTIALLY_SHIPPED_ORDER];
            else if ($request->ship == '2')
                $parameters[] = ['status', '=', OrderStatus::$FULLY_SHIPPED_ORDER];
        }


        if (isset($request->date) && $request->date !='') {
            if ($request->date == '1') {
                $parameters[] = ['created_at', '>', date('Y-m-d 23:59:59', strtotime('-1 days'))];
            } else if ($request->date == '2') {
                $day = date('w');
                $week_start = date('Y-m-d', strtotime('-'.$day.' days'));
                $parameters[] = ['created_at', '>=', $week_start];
            } else if ($request->date == '3') {
                $parameters[] = ['created_at', '>', date('Y-m-01')];
            } else if ($request->date == '5') {
                $parameters[] = ['created_at', '>', date('Y-01-01')];
            } else if ($request->date == '6') {
                $parameters[] = ['created_at', '>=', date('Y-m-d', strtotime('-1 days'))];
                $parameters[] = ['created_at', '<=', date('Y-m-d 23:59:59', strtotime('-1 days'))];
            } else if ($request->date == '8') {
                $month_ini = new DateTime("first day of last month");
                $month_end = new DateTime("last day of last month");

                $parameters[] = ['created_at', '>=', $month_ini->format('Y-m-d')];
                $parameters[] = ['created_at', '<=', $month_end->format('Y-m-d')];
            } else if ($request->date == '10') {
                $parameters[] = ['created_at', '>=', date("Y-m-d",strtotime("last year January 1st"))];
                $parameters[] = ['created_at', '<=', date("Y-m-d",strtotime("last year December 31st"))];
            } else if ($request->date == '13') {
                $parameters[] = ['created_at', '>=', date('Y-m-d', strtotime('-7 days'))];
            } else if ($request->date == '14') {
                $parameters[] = ['created_at', '>=', date('Y-m-d', strtotime('-30 days'))];
            } else if ($request->date == '15') {
                $parameters[] = ['created_at', '>=', date('Y-m-d', strtotime('-90 days'))];
            } else if ($request->date == '16') {
                $parameters[] = ['created_at', '>=', date('Y-m-d', strtotime('-365 days'))];
            } else if ($request->date == '0') {
                $starDate = DateTime::createFromFormat('m/d/Y', $request->startDate);
                $endDate = DateTime::createFromFormat('m/d/Y', $request->endDate);

                $parameters[] = ['created_at', '>=', $starDate->format('Y-m-d 00:00:00')];
                $parameters[] = ['created_at', '<=', $endDate->format('Y-m-d 23:59:59')];
            }
        } else {
            $parameters[] = ['created_at', '>=', date('Y-m-d', strtotime('-30 days'))];
        }

        $totals = [];

        // New Orders
        $newOrdersQuery = Order::query();
        $newOrdersQuery->where('status', OrderStatus::$NEW_ORDER)
            ->where($parameters)
            ->orderBy('created_at', 'desc');

        if (isset($request->text) && $request->text != '' && isset($request->search) && $request->search == '1') {
            $newOrdersQuery->whereIn('user_id', $userIds);
        }else if (isset($request->text) && $request->text != '' && isset($request->search) && $request->search == '4') {
            $newOrdersQuery->whereIn('user_id', $userIds);
        }

        $newOrders = $newOrdersQuery->paginate(10, ['*'], 'p1');
        $totals['new'] = $newOrdersQuery->sum('total');


        foreach($newOrders as &$order) {
            $count = Order::where('status', '!=', OrderStatus::$INIT)
                ->where('user_id', $order->user['id'])
                ->where('created_at', '<=', $order->created_at)
                ->count();
            // Get ship method name by order table's shipping_method_id
            $shipping_method = DB::table('admin_ship_methods')->select('name')->where('id', $order->shipping_method_id)->get();
            if ( count($shipping_method) > 0 ) {
                $shipping_method_name = $shipping_method[0]->name;
            }
            else {
                $shipping_method_name = '';
            }
            $order->shipping_method_name = $shipping_method_name;
            $order->count = $count;
            $order->aStatus = json_decode($order->authorize_info, true);
        }

        // Confirm Orders
        $confirmOrdersQuery = Order::query();
        $confirmOrdersQuery->where('status', OrderStatus::$CONFIRM_ORDER)
            ->where($parameters)
            ->orderBy('created_at', 'desc');

        if (isset($request->text) && $request->text != '' && isset($request->search) && $request->search == '1') {
            $confirmOrdersQuery->whereIn('user_id', $userIds);
        }else if (isset($request->text) && $request->text != '' && isset($request->search) && $request->search == '4') {
            $confirmOrdersQuery->whereIn('user_id', $userIds);
        }

        $confirmOrders = $confirmOrdersQuery->paginate(10, ['*'], 'p2');
        $totals['confirm'] = $confirmOrdersQuery->sum('total');

        foreach($confirmOrders as &$order) {
            $count = Order::where('status', '!=', OrderStatus::$INIT)
                ->where('user_id', $order->user['id'])
                ->where('created_at', '<=', $order->created_at)
                ->count();
            // Get ship method name by order table's shipping_method_id
            $shipping_method = DB::table('admin_ship_methods')->select('name')->where('id', $order->shipping_method_id)->get();
            if ( count($shipping_method) > 0 ) {
                $shipping_method_name = $shipping_method[0]->name;
            }
            else {
                $shipping_method_name = '';
            }
            $order->shipping_method_name = $shipping_method_name;
            $order->count = $count;
            $order->aStatus = json_decode($order->authorize_info, true);
        }


        // Back Orders
        $backOrdersQuery = Order::query();

        $backOrdersQuery->where('status', OrderStatus::$BACK_ORDER)
            ->where($parameters)
            ->orderBy('created_at', 'desc');

        if (isset($request->text) && $request->text != '' && isset($request->search) && $request->search == '1') {
            $backOrdersQuery->whereIn('user_id', $userIds);
        }else if (isset($request->text) && $request->text != '' && isset($request->search) && $request->search == '4') {
            $backOrdersQuery->whereIn('user_id', $userIds);
        }

        $backOrders = $backOrdersQuery->paginate(10, ['*'], 'p3');
        $totals['back'] = $backOrdersQuery->sum('total');

        foreach($backOrders as &$order) {
            $count = Order::where('status', '!=', OrderStatus::$INIT)
                ->where('user_id', $order->user['id'])
                ->where('created_at', '<=', $order->created_at)
                ->count();
            // Get ship method name by order table's shipping_method_id
            $shipping_method = DB::table('admin_ship_methods')->select('name')->where('id', $order->shipping_method_id)->get();
            if ( count($shipping_method) > 0 ) {
                $shipping_method_name = $shipping_method[0]->name;
            }
            else {
                $shipping_method_name = '';
            }
            $order->shipping_method_name = $shipping_method_name;
            $order->count = $count;
            $order->aStatus = json_decode($order->authorize_info, true);
        }

        // Shipped Orders
        $shippedOrdersQuery = Order::query();
        $shippedOrdersQuery->whereIn('status', [OrderStatus::$FULLY_SHIPPED_ORDER, OrderStatus::$PARTIALLY_SHIPPED_ORDER])
            ->where($parameters)
            ->orderBy('created_at', 'desc');

        if (isset($request->text) && $request->text != '' && isset($request->search) && $request->search == '1') {
            $shippedOrdersQuery->whereIn('user_id', $userIds);
        }else if (isset($request->text) && $request->text != '' && isset($request->search) && $request->search == '4') {
            $shippedOrdersQuery->whereIn('user_id', $userIds);
        }

        $shippedOrders = $shippedOrdersQuery->paginate(10, ['*'], 'p4');
        $totals['shipped'] = $shippedOrdersQuery->sum('total');

        foreach($shippedOrders as &$order) {
            $count = Order::where('status', '!=', OrderStatus::$INIT)
                ->where('user_id', $order->user['id'])
                ->where('created_at', '<=', $order->created_at)
                ->count();
            // Get ship method name by order table's shipping_method_id
            $shipping_method = DB::table('admin_ship_methods')->select('name')->where('id', $order->shipping_method_id)->get();
            if ( count($shipping_method) > 0 ) {
                $shipping_method_name = $shipping_method[0]->name;
            }
            else {
                $shipping_method_name = '';
            }
            $order->shipping_method_name = $shipping_method_name;
            $order->count = $count;
            $order->aStatus = json_decode($order->authorize_info, true);
        }

        // Cancel Orders
        $cancelOrdersQuery = Order::query();
        $cancelOrdersQuery->whereIn('status', [OrderStatus::$CANCEL_BY_AGREEMENT, OrderStatus::$CANCEL_BY_BUYER, OrderStatus::$CANCEL_BY_VENDOR])
            ->where($parameters)
            ->orderBy('created_at', 'desc');

        if (isset($request->text) && $request->text != '' && isset($request->search) && $request->search == '1') {
            $cancelOrdersQuery->whereIn('user_id', $userIds);
        }else if (isset($request->text) && $request->text != '' && isset($request->search) && $request->search == '4') {
            $cancelOrdersQuery->whereIn('user_id', $userIds);
        }

        $cancelOrders = $cancelOrdersQuery->paginate(10, ['*'], 'p5');
        $totals['cancel'] = $cancelOrdersQuery->sum('total');

        foreach($cancelOrders as &$order) {
            $count = Order::where('status', '!=', OrderStatus::$INIT)
                ->where('user_id', $order->user['id'])
                ->where('created_at', '<=', $order->created_at)
                ->count();
            // Get ship method name by order table's shipping_method_id
            $shipping_method = DB::table('admin_ship_methods')->select('name')->where('id', $order->shipping_method_id)->get();
            if ( count($shipping_method) > 0 ) {
                $shipping_method_name = $shipping_method[0]->name;
            }
            else {
                $shipping_method_name = '';
            }
            $order->shipping_method_name = $shipping_method_name;
            $order->count = $count;
            $order->aStatus = json_decode($order->authorize_info, true);
        }

        $appends = [
            'p1' => $newOrders->currentPage(),
            'p2' => $confirmOrders->currentPage(),
            'p3' => $confirmOrders->currentPage(),
            'p4' => $confirmOrders->currentPage(),
            'p5' => $confirmOrders->currentPage(),
        ];

        foreach ($request->all() as $key => $value) {
            if ($key != 'p1' && $key != 'p2' && $key != 'p3' && $key != 'p4')
                $appends[$key] = ($value == null) ? '' : $value;
        }

//        dd($newOrders->toArray());

        return view('admin.dashboard.orders.all_orders', compact('newOrders', 'confirmOrders', 'backOrders',
            'appends', 'shippedOrders', 'cancelOrders', 'totals'))
            ->with('page_title', 'All Orders');
    }

    public function newOrders() {
        $orders = Order::with('shippingMethod')
            ->where('status', OrderStatus::$NEW_ORDER)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // dd($orders);

        foreach($orders as &$order) {
            $count = Order::where('status', '!=', OrderStatus::$INIT)
                ->where('user_id', $order->user['id'])
                ->where('created_at', '<=', $order->created_at)
                ->count();
            $order->count = $count;
        }

        return view('admin.dashboard.orders.new_orders', compact('orders'))->with('page_title', 'New Orders');
    }
    
    public function  admin_orders_according_type(Request $request){
        $type = $request->type;
        $parameters = [];
        $userIds = [];

        // Search
        if (isset($request->text) && $request->text !='') {
            if (isset($request->search) && $request->search == '4') {
                $metaBuyers = User::where('first_name', 'like', '%' . $request->text . '%')->get();

                foreach ($metaBuyers as $buyer)
                    $userIds[] = $buyer->id;
            } else if (isset($request->search) && $request->search == '1') {
                $metaBuyers = MetaBuyer::where('company_name', 'like', '%' . $request->text . '%')->get();

                foreach ($metaBuyers as $buyer)
                    $userIds[] = $buyer->user_id;
            } else if (isset($request->search) && $request->search == '2') {
                $parameters[] = ['order_number', 'like', '%' . $request->text . '%'];
            } else if (isset($request->search) && $request->search == '3') {
                $parameters[] = ['tracking_number', 'like', '%' . $request->text . '%'];
            }
        }

        if (isset($request->ship) && $request->ship !='') {
            if ($request->ship == '1')
                $parameters[] = ['status', '=', OrderStatus::$PARTIALLY_SHIPPED_ORDER];
            else if ($request->ship == '2')
                $parameters[] = ['status', '=', OrderStatus::$FULLY_SHIPPED_ORDER];
        }


        if (isset($request->date) && $request->date !='') {
            if ($request->date == '1') {
                $parameters[] = ['created_at', '>', date('Y-m-d 23:59:59', strtotime('-1 days'))];
            } else if ($request->date == '2') {
                $day = date('w');
                $week_start = date('Y-m-d', strtotime('-'.$day.' days'));
                $parameters[] = ['created_at', '>=', $week_start];
            } else if ($request->date == '3') {
                $parameters[] = ['created_at', '>', date('Y-m-01')];
            } else if ($request->date == '5') {
                $parameters[] = ['created_at', '>', date('Y-01-01')];
            } else if ($request->date == '6') {
                $parameters[] = ['created_at', '>=', date('Y-m-d', strtotime('-1 days'))];
                $parameters[] = ['created_at', '<=', date('Y-m-d 23:59:59', strtotime('-1 days'))];
            } else if ($request->date == '8') {
                $month_ini = new DateTime("first day of last month");
                $month_end = new DateTime("last day of last month");

                $parameters[] = ['created_at', '>=', $month_ini->format('Y-m-d')];
                $parameters[] = ['created_at', '<=', $month_end->format('Y-m-d')];
            } else if ($request->date == '10') {
                $parameters[] = ['created_at', '>=', date("Y-m-d",strtotime("last year January 1st"))];
                $parameters[] = ['created_at', '<=', date("Y-m-d",strtotime("last year December 31st"))];
            } else if ($request->date == '13') {
                $parameters[] = ['created_at', '>=', date('Y-m-d', strtotime('-7 days'))];
            } else if ($request->date == '14') {
                $parameters[] = ['created_at', '>=', date('Y-m-d', strtotime('-30 days'))];
            } else if ($request->date == '15') {
                $parameters[] = ['created_at', '>=', date('Y-m-d', strtotime('-90 days'))];
            } else if ($request->date == '16') {
                $parameters[] = ['created_at', '>=', date('Y-m-d', strtotime('-365 days'))];
            } else if ($request->date == '0') {
                $starDate = DateTime::createFromFormat('m/d/Y', $request->startDate);
                $endDate = DateTime::createFromFormat('m/d/Y', $request->endDate);

                $parameters[] = ['created_at', '>=', $starDate->format('Y-m-d 00:00:00')];
                $parameters[] = ['created_at', '<=', $endDate->format('Y-m-d 23:59:59')];
            }
        } else {
            $parameters[] = ['created_at', '>=', date('Y-m-d', strtotime('-30 days'))];
        }

        $totals = [];

        // New Orders
        $OrdersQuery = Order::query();
        if($type == OrderStatus::$FULLY_SHIPPED_ORDER) {
//            $shipped_types[] = array(OrderStatus::$PARTIALLY_SHIPPED_ORDER,OrderStatus::$FULLY_SHIPPED_ORDER);
            $OrdersQuery->whereIn('status', [OrderStatus::$PARTIALLY_SHIPPED_ORDER,OrderStatus::$FULLY_SHIPPED_ORDER])
                ->where($parameters)
                ->orderBy('created_at', 'desc');

        }else if($type == OrderStatus::$CANCEL_BY_AGREEMENT) {
            $OrdersQuery->whereIn('status', [OrderStatus::$CANCEL_BY_BUYER,OrderStatus::$CANCEL_BY_VENDOR,OrderStatus::$CANCEL_BY_AGREEMENT])
                ->where($parameters)
                ->orderBy('created_at', 'desc');

        }else {
        $OrdersQuery->where('status', $type)
            ->where($parameters)
            ->orderBy('created_at', 'desc');
        }

        if (isset($request->text) && $request->text != '' && isset($request->search) && $request->search == '1') {
            $OrdersQuery->whereIn('user_id', $userIds);
        }else if (isset($request->text) && $request->text != '' && isset($request->search) && $request->search == '4') {
            $OrdersQuery->whereIn('user_id', $userIds);
        }

        $orders = $OrdersQuery->paginate(10, ['*'], 'p1');
        $totals['new'] = $OrdersQuery->sum('total');


        foreach($orders as &$order) {
            $count = Order::where('status', '!=', OrderStatus::$INIT)
                ->where('user_id', $order->user['id'])
                ->where('created_at', '<=', $order->created_at)
                ->count();
            // Get ship method name by order table's shipping_method_id
            $shipping_method = DB::table('admin_ship_methods')->select('name')->where('id', $order->shipping_method_id)->get();
            if ( count($shipping_method) > 0 ) {
                $shipping_method_name = $shipping_method[0]->name;
            }
            else {
                $shipping_method_name = '';
            }
            $order->shipping_method_name = $shipping_method_name;
            $order->count = $count;
            $order->aStatus = json_decode($order->authorize_info, true);
        }

        $appends = [
            'p1' => $orders->currentPage()
        ];

        foreach ($request->all() as $key => $value) {
            if ($key != 'p1')
                $appends[$key] = ($value == null) ? '' : $value;
        }

         if($type == OrderStatus::$NEW_ORDER){
            return view('admin.dashboard.orders.new_orders', compact('orders', 'appends','totals'))
                ->with('page_title', 'New Orders');
        }else if($type == OrderStatus::$CONFIRM_ORDER){
            return view('admin.dashboard.orders.confirmed_orders', compact('orders', 'appends','totals'))
                ->with('page_title', 'Confirmed Orders');
        }else if($type == OrderStatus::$BACK_ORDER){
            return view('admin.dashboard.orders.back_orders', compact('orders', 'appends','totals'))
                ->with('page_title', 'Back Orders');
        }else if($type == OrderStatus::$FULLY_SHIPPED_ORDER){
            return view('admin.dashboard.orders.shipped_orders', compact('orders', 'appends','totals'))
                ->with('page_title', 'Shipped Orders');
        }else if($type == OrderStatus::$CANCEL_BY_AGREEMENT){
            return view('admin.dashboard.orders.cancel_orders', compact('orders', 'appends','totals'))
                ->with('page_title', 'Cancel Orders');
        }
    }

    public function confirmOrders() {
        $orders = Order::where('status', OrderStatus::$CONFIRM_ORDER)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        foreach($orders as &$order) {
            $count = Order::where('status', '!=', OrderStatus::$INIT)
                ->where('user_id', $order->user['id'])
                ->where('created_at', '<=', $order->created_at)
                ->count();
            $order->count = $count;
        }

        return view('admin.dashboard.orders.confirmed_orders', compact('orders'))->with('page_title', 'Confirmed Orders');
    }

    public function backedOrders() {
        $orders = Order::where('status', OrderStatus::$BACK_ORDER)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        foreach($orders as &$order) {
            $count = Order::where('status', '!=', OrderStatus::$INIT)
                ->where('user_id', (isset($order->user))? $order->user['id'] : null)
                ->where('created_at', '<=', $order->created_at)
                ->count();
            $order->count = $count;
        }

        return view('admin.dashboard.orders.back_orders', compact('orders'))->with('page_title', 'Back Orders');
    }

    public function shippedOrders() {
        $orders = Order::whereIn('status', [OrderStatus::$FULLY_SHIPPED_ORDER, OrderStatus::$PARTIALLY_SHIPPED_ORDER])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        foreach($orders as &$order) {
            $count = Order::where('status', '!=', OrderStatus::$INIT)
                ->where('user_id', $order->user['id'])
                ->where('created_at', '<=', $order->created_at)
                ->count();
            $order->count = $count;
        }


        return view('admin.dashboard.orders.shipped_orders', compact('orders'))->with('page_title', 'Shipped Orders');
    }

    public function cancelledOrders() {
        $orders = Order::whereIn('status', [OrderStatus::$CANCEL_BY_AGREEMENT, OrderStatus::$CANCEL_BY_BUYER, OrderStatus::$CANCEL_BY_VENDOR])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        foreach($orders as &$order) {
            $count = Order::where('status', '!=', OrderStatus::$INIT)
                ->where('user_id', $order->user['id'])
                ->where('created_at', '<=', $order->created_at)
                ->count();
            $order->count = $count;
        }

        return view('admin.dashboard.orders.cancel_orders', compact('orders'))->with('page_title', 'Cancel Orders');
    }

    public function returnedOrders() {
        $orders = Order::where('status', OrderStatus::$RETURNED)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        foreach($orders as &$order) {
            $count = Order::where('status', '!=', OrderStatus::$INIT)
                ->where('user_id', $order->user['id'])
                ->where('created_at', '<=', $order->created_at)
                ->count();
            $order->count = $count;
        }

        return view('admin.dashboard.orders.returned', compact('orders'))->with('page_title', 'Return Orders');
    }
    
    public function send_message_buyer(Request $request)
    {
        $request->validate([
            'topics' => 'required',
            'message' => 'required',
        ]);

        if(!empty($request->file('attachment1'))){
            $attachment1 = $this->attachmentUpload($request->file('attachment1'));
        }

        if(!empty($request->file('attachment2'))){
            $attachment2 = $this->attachmentUpload($request->file('attachment2'));
        }

        if(!empty($request->file('attachment3'))){
            $attachment3 = $this->attachmentUpload($request->file('attachment3'));
        }

        BuyerMessage::create([
            'user_id' => $request->user_id,
            'sender' => $request->sender,
            'recipient' => $request->recipient,
            'subject' => $request->topics .' ' .$request->order,
            'message' => $request->message,
            'attachment1' => $attachment1 ?? '',
            'attachment2' => $attachment2 ?? '',
            'attachment3' => $attachment3 ?? '',
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);

        $request->session()->flash('message', 'Message Send To '.$request->recipient);
        return  Redirect::back();
    }

    // public function send_message_buyer(Request $request)
    // {
    
    //     if($request->admin_back_msg == 1) {
    //     $request->validate([
    //         'topics' => 'required',
    //         'order' => 'required',
    //         'message' => 'required',
    //     ]);

    //     if(!empty($request->file('attachment1'))){
    //         $attachment1 = $this->attachmentUpload($request->file('attachment1'));
    //     }

    //     if(!empty($request->file('attachment2'))){
    //         $attachment2 = $this->attachmentUpload($request->file('attachment2'));
    //     }

    //     if(!empty($request->file('attachment3'))){
    //         $attachment3 = $this->attachmentUpload($request->file('attachment3'));
    //     }


    //         BuyerMessage::create([
    //             'user_id' => $request->user_id,
    //             'sender' => $request->sender,
    //             'recipient' => $request->recipient,
    //             'subject' => $request->topics .' ' .$request->order,
    //             'message' => $request->message,
    //             'attachment1' => $attachment1 ?? '',
    //             'attachment2' => $attachment2 ?? '',
    //             'attachment3' => $attachment3 ?? '',
    //             'created_at' => Carbon::now()->toDateTimeString(),
    //         ]);
    //         $request->session()->flash('message', 'Message Send To '.$request->recipient);
    //         return  Redirect::back();
    //     }else {
    //         if(!empty($request->file('attachment1'))){
    //             $attachment1 = $this->attachmentUpload($request->file('attachment1'));
    //         }

    //         if(!empty($request->file('attachment2'))){
    //             $attachment2 = $this->attachmentUpload($request->file('attachment2'));
    //         }

    //         if(!empty($request->file('attachment3'))){
    //             $attachment3 = $this->attachmentUpload($request->file('attachment3'));
    //         }
    //         dd($request->all());
    //         BuyerMessage::create([
    //             'user_id' => $request->message_user_id,
    //             'sender' => $request->message_sender,
    //             'recipient' => $request->message_recipient,
    //             'subject' => $request->message_subject,
    //             'message' => $request->message,
    //             'attachment1' => $attachment1 ?? '',
    //             'attachment2' => $attachment2 ?? '',
    //             'attachment3' => $attachment3 ?? '',
    //             'created_at' => Carbon::now()->toDateTimeString(),
    //         ]);
    //         $request->session()->flash('message', 'Message Send To '.$request->message_recipient);
    //         return  Redirect::back();
    //     }

    // }

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

    public function incompleteOrders() {
        $result = CartItem::select(DB::raw('user_id','status'))
            ->groupBy('user_id')
            // ->where('updated_at', '>=', date('Y-m-d H:i:s', strtotime('-15 days')))
            ->orderByDesc('updated_at')
            ->paginate(10);
        $black_logo_path='';
        $black = DB::table('settings')->where('name', 'logo-black')->first();
        if ($black)
            $black_logo_path = asset($black->value);
        $orders = [];
        $cartItems = [];

        foreach ($result as $r) {

            $total = 0;

            $cartItems[$r->user_id] = CartItem::where('user_id', $r->user_id)
                // ->where('updated_at', '>=', date('Y-m-d H:i:s', strtotime('-15 days')))
                ->with('item', 'user')
                ->get();
            foreach ($cartItems[$r->user_id] as $item) {
                $sizes = explode("-", $item->item->pack->name);
                $itemInPack = 0;

                for($i=1; $i <= sizeof($sizes); $i++) {
                    $var = 'pack'.$i;

                    if ($item->item->pack->$var != null)
                        $itemInPack += (int) $item->item->pack->$var;
                }

                $total += $itemInPack * $item->quantity * $item->item->price;
            }
            $orders [] = [
                'user_id' => $r->user_id,
                'company' => (isset($cartItems[$r->user_id][0]->user)? $cartItems[$r->user_id][0]->user->buyer->company_name : 'N/A'),
                'email' => (isset($cartItems[$r->user_id][0]->user) ? $cartItems[$r->user_id][0]->user->email : 'N/A'),
                'total' => $total,
                'status' => $r->status,
                'updated_at' => $cartItems[$r->user_id][0]->updated_at
            ];
        }
        return view('admin.dashboard.orders.incomplete', compact('orders', 'result','cartItems','black_logo_path'))->with('page_title', 'Incomplete Checkouts');
    }

    public function incompleteOrderDetails($user) {
        /*$order->load('user', 'items');
        $allItems = [];

        foreach($order->items as $item)
            $allItems[$item->item_id][] = $item;*/

        $cartItems = CartItem::where('user_id', $user)
            ->with('item', 'color')
            ->get();

        //$order->load('user', 'items');
        $allItems = [];

        foreach($cartItems as $item)
            $allItems[$item->item_id][] = $item;
        return view('admin.dashboard.orders.incomplete_details', compact(  'allItems'))->with('page_title', 'Incomplete Checkouts');
    }

    public function orderDetails(Order $order) {
        $allItems = [];
        $order->load('user', 'items', 'shippingMethod','authorizeHistory');
        $itemIds = [];

        $coupon = null;
        if($order->coupon != null && $order->coupon != '') {
            $coupon = Promotion::where('coupon_code', $order->coupon)->first();
        }

        //$coupon = Coupon::where('name', $order->coupon)->first();
        $promotion = Promotion::where('id', $order->default_coupon_id)->where('status', 1)->first();
        $shippingMethods = AdminShipMethod::orderBy('name')->get();

        //dd($promotion);

        $count = Order::where('status', '!=', OrderStatus::$INIT)
            ->where('user_id', (isset($order->user))? $order->user['id'] : null)
            ->where('created_at', '<=', $order->created_at)
            ->count();

        $countText = 'This is the '.$this->addOrdinalNumberSuffix($count).' order.';

        foreach($order->items as $item) {
            $allItems[$item->item_id][] = $item;
            $itemIds[] = $item->id;
        }

        $products = Item::where('status', 1)
            ->orderBy('activated_at', 'desc')
            ->paginate(21);

        $categories = Category::where('parent', 0)->orderBy('sort')->get();

        $authorize_info = json_decode($order->authorize_info, true);
        $order->authorize_info = $authorize_info;

        $UserInfoData = null;
        $sessionUser = null;
        if (Auth::check() && Auth::user()->role == Role::$BUYER) {
            $UserInfoData = Auth::user();
            $sessionUser = Auth::user()->id;
        } else {
            $sessionUser = session('guestKey')[0];
        }

        $order = Order::where('id', $order->id)->first();

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
        $get_storeCredit_amount = StoreCredit::where('user_id', $order->user_id)->first();
        
        
        //Point system consideration
        $rewardData = [];
        if($order->reward_point){
            $rewardData = PointSystem::where('id',$order->reward_point)->first();
        }

        return view('admin.dashboard.orders.order_details', compact('order', 'authorize_info', 'allItems', 'countText','cardNumber','cardFullName','cardExpire','cardCvc',
            'itemIds', 'shippingMethods', 'products', 'categories','UserInfoData','coupon','promotion','get_storeCredit_amount','rewardData'))
            ->with('page_title', 'Order Details');
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

            foreach ($order->items as $item)
                $allItems[$item->item_id][] = $item;

            // Vendor Logo
            $logo_path = '';
            /* $vendorLogo = VendorImage::where('status', 1)
                 ->where('type', VendorImageType::$LOGO)
                 ->first();

             if ($vendorLogo)
                 $logo_path = public_path($vendorLogo->image_path);*/

            $black = DB::table('settings')->where('name', 'logo-black')->first();
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

        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('admin.dashboard.orders.pdf.with_image', $data);
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

            foreach ($order->items as $item)
                $allItems[$item->item_id][] = $item;

            // Vendor Logo
            $logo_path = '';
            /*$vendorLogo = VendorImage::where('status', 1)
                ->where('type', VendorImageType::$LOGO)
                ->first();

            if ($vendorLogo)
                $logo_path = asset($vendorLogo->image_path);*/

            $black = DB::table('settings')->where('name', 'logo-black')->first();
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

        $pdf = PDF::loadView('admin.dashboard.orders.pdf.without_image', $data);
        return $pdf->stream('invoice.pdf');
    }

    public function checkPassword(Request $request) {
        $password = $request->password;

        if (Hash::check($password, Auth::user()->password)) {
            $order = Order::where('id', $request->orderID)->first();

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

            return response()->json([
                'success' => true,
                'fullName' => $order->card_full_name,
                'number' => $order->card_number,
                'cvc' => $order->card_cvc,
                'expire' => $order->card_expire,
            ]);
        }

        return response()->json(['success' => false]);
    }

    public function maskCardNumber(Request $request) {
        $order = Order::where('id', $request->id)->first();

        // Decrypt
        $cardNumber = '';

        try {
            $cardNumber = decrypt($order->card_number);
        } catch (DecryptException $e) {

        }

        $order->card_number = $cardNumber;

        $mask = str_repeat("*", (strlen($order->card_number) - 4)).substr($order->card_number,-4,4);
        $order->card_number = encrypt($mask);
        $order->save();

        return response()->json(['success' => true, 'mask' => $mask]);
    }

    public function itemDetails(Request $request) {
        $item = Item::where('id', $request->id)
            ->where('status', 1)
            ->with('colors')
            ->first();

        return response()->json($item->toArray());
    }

    public function addItem(Request $request) {
        $item = Item::where('id', $request->item_id)
            ->where('status', 1)
            ->with('pack')
            ->first();

        $newAmount = 0;

        foreach ($request->colors as $color_id => $count) {
            if ($count != null || $count != '') {
                $count = (int) $count;
                $color = Color::where('id', $color_id)->first();

                $previous = OrderItem::where('order_id', $request->order_id)
                    ->where('item_id', $item->id)
                    ->where('color', $color->name)
                    ->first();

                if ($previous) {
                    $sizes = explode("-", $previous->size);
                    $pack = '';
                    $itemInPack = 0; 
                    for ($i = 1; $i <= sizeof($sizes); $i++) {
                        $var = 'pack' . $i; 
                        if ($item->pack->$var != null) {
                            $pack .= $item->pack->$var * ($previous->qty + $count) . '-';
                            $itemInPack += (int)$item->pack->$var;
                        } else {
                            $pack .= '0-';
                        }
                    } 
                    $pack = rtrim($pack, "-"); 
                    $previous->qty = $previous->qty + $count;
                    $previous->total_qty = $previous->total_qty + ($previous->item_per_pack * $count);
                    $previous->amount = $previous->total_qty * $previous->per_unit_price;
                    $previous->pack = $pack;

                    $newAmount += ($previous->item_per_pack * $count) * $previous->per_unit_price;
                    $previous->save();
                } else {
                    $tmp = OrderItem::where('order_id', $request->order_id)
                        ->where('item_id', $item->id)
                        ->first();

                    if ($tmp) {
                        $sizes = explode("-", $tmp->size);
                    } else {
                        $sizes = explode("-", $item->pack->name);
                    }

                    $pack = '';
                    $itemInPack = 0;

                    for ($i = 1; $i <= sizeof($sizes); $i++) {
                        $var = 'pack' . $i;

                        if ($item->pack->$var != null) {
                            $pack .= $item->pack->$var * $count . '-';
                            $itemInPack += (int)$item->pack->$var;
                        } else {
                            $pack .= '0-';
                        }
                    }

                    $pack = rtrim($pack, "-");

                    OrderItem::create([
                        'order_id' => $request->order_id,
                        'item_id' => $item->id,
                        'style_no' => $item->style_no,
                        'color' => $color->name,
                        'size' => $item->pack->name,
                        'item_per_pack' => $itemInPack,
                        'pack' => $pack,
                        'qty' => $count,
                        'total_qty' => $itemInPack * $count,
                        'per_unit_price' => $item->price,
                        'amount' => number_format($item->price * $itemInPack * $count, 2, '.', '')
                    ]);

                    $newAmount += number_format($item->price * $itemInPack * $count, 2, '.', '');
                }
            }
        }

        $order = Order::where('id', $request->order_id)->first();
        $order->subtotal = $order->subtotal + $newAmount;
        $order->total = $order->total + $newAmount;

        $coupon = Promotion::where('coupon_code', $order->coupon)->first();
        $promotion = Promotion::where('id', $order->default_coupon_id)->where('status', 1)->first();

        $subTotal = $order->subtotal;

        $promotion_id = 0;
        $coupon_code = '';

        $discountFlash = 0;
        $coupon_discount = 0;
        $coupon_details = '';
        $free_shipping = 0;

        if(!empty($coupon)) {

            $coupon_code = $coupon->coupon_code;

            if($coupon->to_price_1) {
            } else {
                $coupon->to_price_1 = 1000000;
            }

            if($subTotal >= $coupon->from_price_1 && $subTotal <= $coupon->to_price_1) {

                if($coupon->promotion_type == 'Percentage discount by order amount') {

                    $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_1 / 100) * $subTotal, 2, '.', '');
                    $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_1 . '%]';

                } else {

                    $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_1, 2, '.', '');
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

                        $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_2 / 100) * $subTotal, 2, '.', '');
                        $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_2 . '%]';

                    } else {

                        $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_2, 2, '.', '');
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

                            $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_3 / 100) * $subTotal, 2, '.', '');
                            $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_3 . '%]';

                        } else {

                            $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_3, 2, '.', '');
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

                                $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_4 / 100) * $subTotal, 2, '.', '');
                                $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_4 . '%]';

                            } else {

                                $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_4, 2, '.', '');
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

                                    $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_5 / 100) * $subTotal, 2, '.', '');
                                    $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_5 . '%]';

                                } else {

                                    $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_5, 2, '.', '');
                                    $coupon_details = '["' . $coupon->coupon_code . '" - $' . $coupon->unit_price_discount_5 . ']';

                                }

                            }

                            if($coupon->free_shipping_5 == 1) {

                                $free_shipping = 1;

                            }

                        }
                    }
                }

            }

            $order->discount = $coupon_discount;
            $order->coupon = $coupon->coupon_code;
            $order->promotion_details = $coupon_details;
            $order->free_shipping = $free_shipping;

        }else{
             $coupon_discount = $order->discount;
        }

        $discountflash = 0;
        $promotion_discount = 0;

        $promotion_details = '';
        $free_shipping = 0;

        if(!empty($promotion)) {

            $promotion_id = $promotion->id;

            if($promotion->to_price_1) {
            } else {
                $promotion->to_price_1 = 1000000;
            }

            if($subTotal >= $promotion->from_price_1 && $subTotal <= $promotion->to_price_1) {

                if($promotion->promotion_type == 'Percentage discount by order amount') {

                    $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_1 / 100) * $subTotal, 2, '.', '');
                    $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_1 . '%]';

                } else {

                    $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_1,2, '.', '');
                    $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_1 . ']';

                }

                if($promotion->free_shipping_1 == 1) {

                    $free_shipping = 1;

                }

            } else {

                if($promotion->to_price_2) {
                } else {
                    $promotion->to_price_2 = 1000000;
                }

                if($subTotal >= $promotion->from_price_2 && $subTotal <= $promotion->to_price_2) {

                    if($promotion->promotion_type == 'Percentage discount by order amount') {

                        $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_2 / 100) * $subTotal, 2, '.', '');
                        $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_2 . '%]';

                    } else {

                        $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_2, 2, '.', '');
                        $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_2 . ']';

                    }

                } else {

                    if($promotion->to_price_3) {
                    } else {
                        $promotion->to_price_3 = 1000000;
                    }

                    if($subTotal >= $promotion->from_price_3 && $subTotal <= $promotion->to_price_3) {

                        if($promotion->promotion_type == 'Percentage discount by order amount') {

                            $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_3 / 100) * $subTotal, 2, '.', '');
                            $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_3 . '%]';

                        } else {

                            $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_3, 2, '.', '');
                            $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_3 . ']';

                        }

                    } else {

                        if($promotion->to_price_4) {
                        } else {
                            $promotion->to_price_4 = 1000000;
                        }

                        if($subTotal >= $promotion->from_price_4 && $subTotal <= $promotion->to_price_4) {

                            if($promotion->promotion_type == 'Percentage discount by order amount') {

                                $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_4 / 100) * $subTotal, 2, '.', '');
                                $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_4 . '%]';

                            } else {

                                $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_4, 2, '.', '');
                                $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_4. ']';

                            }

                        } else {

                            if($promotion->to_price_5) {
                            } else {
                                $promotion->to_price_5 = 1000000;
                            }

                            if($subTotal >= $promotion->from_price_5 && $subTotal <= $promotion->to_price_5) {

                                if($promotion->promotion_type == 'Percentage discount by order amount') {

                                    $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_5 / 100) * $subTotal, 2, '.', '');
                                    $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_5 . '%]';

                                } else {

                                    $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_5, 2, '.', '');
                                    $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_5 . ']';

                                }

                            }

                        }
                    }
                }

            }

            $order->discount = $promotion_discount;
            $order->default_coupon_id = $promotion->id;
            $order->promotion_details = $promotion_details;
            $order->free_shipping = $free_shipping;


        }
        
        //point system
        
        $pointDiscount = 0;
        if($order->reward_fixed){
            $pointDiscount = $order->reward_fixed;
        }
        if($order->reward_percantage){
            $pointDiscount = $order->reward_percantage;
        }
        $order->total = number_format($order->subtotal + $order->shipping_cost - $order->store_credit - $order->discount  - $pointDiscount, 2, '.', '');

        $order->save();
    }

    public function createBackorder(Request $request) {
        $orderItem = OrderItem::where('id', $request->ids[0])->first();
        $backOrderSubTotal = OrderItem::whereIn('id', $request->ids)->sum('amount');
        $order = $orderItem->order;

        $previousCount = Order::where('order_number', 'like', explode('-', $order->order_number)[0].'%')->count();

        $subTotal = $order->subtotal;

        $coupon = Promotion::where('coupon_code', $order->coupon)->first();
        $promotion = Promotion::where('id', $order->default_coupon_id)->where('status', 1)->first();

        $promotion_id = 0;
        $coupon_code = '';

        $discountFlash = 0;
        $coupon_discount = 0;
        $coupon_details = '';

        if(!empty($coupon)) {

            $coupon_code = $coupon->coupon_code;

            if($coupon->to_price_1) {
            } else {
                $coupon->to_price_1 = 1000000;
            }

            if($subTotal >= $coupon->from_price_1 && $subTotal <= $coupon->to_price_1) {

                if($coupon->promotion_type == 'Percentage discount by order amount') {

                    $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_1 / 100) * $subTotal, 2, '.', '');
                    $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_1 . '%]';

                } else {

                    $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_1, 2, '.', '');
                    $coupon_details = '["' . $coupon->coupon_code . '" - $' . $coupon->unit_price_discount_1 . ']';

                }

            } else {

                if($coupon->to_price_2) {
                } else {
                    $coupon->to_price_2 = 1000000;
                }

                if($subTotal >= $coupon->from_price_2 && $subTotal <= $coupon->to_price_2) {

                    if($coupon->promotion_type == 'Percentage discount by order amount') {

                        $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_2 / 100) * $subTotal, 2, '.', '');
                        $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_2 . '%]';

                    } else {

                        $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_2, 2, '.', '');
                        $coupon_details = '["' . $coupon->coupon_code . '" - $' . $coupon->unit_price_discount_2 . ']';

                    }

                } else {

                    if($coupon->to_price_3) {
                    } else {
                        $coupon->to_price_3 = 1000000;
                    }

                    if($subTotal >= $coupon->from_price_3 && $subTotal <= $coupon->to_price_3) {

                        if($coupon->promotion_type == 'Percentage discount by order amount') {

                            $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_3 / 100) * $subTotal, 2, '.', '');
                            $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_3 . '%]';

                        } else {

                            $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_3, 2, '.', '');
                            $coupon_details = '["' . $coupon->coupon_code . '" - $' . $coupon->unit_price_discount_3 . ']';

                        }

                    } else {

                        if($coupon->to_price_4) {
                        } else {
                            $coupon->to_price_4 = 1000000;
                        }

                        if($subTotal >= $coupon->from_price_4 && $subTotal <= $coupon->to_price_4) {

                            if($coupon->promotion_type == 'Percentage discount by order amount') {

                                $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_4 / 100) * $subTotal, 2, '.', '');
                                $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_4 . '%]';

                            } else {

                                $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_4, 2, '.', '');
                                $coupon_details = '["' . $coupon->coupon_code . '" - $' . $coupon->unit_price_discount_4 . ']';

                            }

                        } else {

                            if($coupon->to_price_5) {
                            } else {
                                $coupon->to_price_5 = 1000000;
                            }

                            if($subTotal >= $coupon->from_price_5 && $subTotal <= $coupon->to_price_5) {

                                if($coupon->promotion_type == 'Percentage discount by order amount') {

                                    $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_5 / 100) * $subTotal, 2, '.', '');
                                    $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_5 . '%]';

                                } else {

                                    $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_5, 2, '.', '');
                                    $coupon_details = '["' . $coupon->coupon_code . '" - $' . $coupon->unit_price_discount_5 . ']';

                                }

                            }

                        }
                    }
                }

            }
        }

        $discountflash = 0;
        $promotion_discount = 0;

        $promotion_details = '';

        if(!empty($promotion)) {

            $promotion_id = $promotion->id;

            if($promotion->to_price_1) {
            } else {
                $promotion->to_price_1 = 1000000;
            }

            if($subTotal >= $promotion->from_price_1 && $subTotal <= $promotion->to_price_1) {

                if($promotion->promotion_type == 'Percentage discount by order amount') {

                    $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_1 / 100) * $subTotal, 2, '.', '');
                    $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_1 . '%]';

                } else {

                    $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_1,2, '.', '');
                    $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_1 . ']';

                }

            } else {

                if($promotion->to_price_2) {
                } else {
                    $promotion->to_price_2 = 1000000;
                }

                if($subTotal >= $promotion->from_price_2 && $subTotal <= $promotion->to_price_2) {

                    if($promotion->promotion_type == 'Percentage discount by order amount') {

                        $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_2 / 100) * $subTotal, 2, '.', '');
                        $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_2 . '%]';

                    } else {

                        $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_2, 2, '.', '');
                        $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_2 . ']';

                    }

                } else {

                    if($promotion->to_price_3) {
                    } else {
                        $promotion->to_price_3 = 1000000;
                    }

                    if($subTotal >= $promotion->from_price_3 && $subTotal <= $promotion->to_price_3) {

                        if($promotion->promotion_type == 'Percentage discount by order amount') {

                            $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_3 / 100) * $subTotal, 2, '.', '');
                            $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_3 . '%]';

                        } else {

                            $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_3, 2, '.', '');
                            $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_3 . ']';

                        }

                    } else {

                        if($promotion->to_price_4) {
                        } else {
                            $promotion->to_price_4 = 1000000;
                        }

                        if($subTotal >= $promotion->from_price_4 && $subTotal <= $promotion->to_price_4) {

                            if($promotion->promotion_type == 'Percentage discount by order amount') {

                                $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_4 / 100) * $subTotal, 2, '.', '');
                                $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_4 . '%]';

                            } else {

                                $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_4, 2, '.', '');
                                $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_4. ']';

                            }

                        } else {

                            if($promotion->to_price_5) {
                            } else {
                                $promotion->to_price_5 = 1000000;
                            }

                            if($subTotal >= $promotion->from_price_5 && $subTotal <= $promotion->to_price_5) {

                                if($promotion->promotion_type == 'Percentage discount by order amount') {

                                    $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_5 / 100) * $subTotal, 2, '.', '');
                                    $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_5 . '%]';

                                } else {

                                    $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_5, 2, '.', '');
                                    $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_5 . ']';

                                }

                            }

                        }
                    }
                }

            }


        }
        $promotion_text = $promotion_details . ' ' . $coupon_details;


        $back_order_promotion_discount = number_format(($backOrderSubTotal / $order->subtotal) * $promotion_discount, 2, '.', '');
        $back_order_coupon_discount = number_format(($backOrderSubTotal / $order->subtotal) * $coupon_discount, 2, '.', '');
        $back_order_discount = number_format($back_order_promotion_discount + $back_order_coupon_discount, 2, '.', '');

        $new_order_discount = number_format($order->discount - $back_order_discount, 2, '.', '');


        $newOrder = Order::create([
            'status' => OrderStatus::$BACK_ORDER,
            'user_id' => $order->user_id,
            'order_number' => $order->order_number.'-BO'.$previousCount,
            'company_name' => $order->company_name,
            'email' => $order->email,
            'name' => $order->name,
            'shipping' => $order->shipping,
            'shipping_method_id' => $order->shipping_method_id,
            'shipping_address' => $order->shipping_address,
            'shipping_city' => $order->shipping_city,
            'shipping_state' => $order->shipping_state,
            'shipping_zip' => $order->shipping_zip,
            'shipping_country' => $order->shipping_country,
            'shipping_phone' => $order->shipping_phone,
            'billing_address' => $order->billing_address,
            'billing_city' => $order->billing_city,
            'billing_state' => $order->billing_state,
            'billing_zip' => $order->billing_zip,
            'billing_country' => $order->billing_country,
            'billing_phone' => $order->billing_phone,
            'card_number' => $order->card_number,
            'card_full_name' => $order->card_full_name,
            'card_expire' => $order->card_expire,
            'card_cvc' => $order->card_cvc,
            'note' => $order->note,
            'admin_note' => $order->admin_note,
            'can_call' => $order->can_call,
            'authorize_info' => $order->authorize_info,
            'paypal_payment_id' => $order->paypal_payment_id,
            'paypal_token' => $order->paypal_token,
            'paypal_payer_id' => $order->paypal_payer_id,
            'payment_type' => $order->payment_type,
            'subtotal' => $backOrderSubTotal,
            'discount' => 0,
            'shipping_cost' => 0,
            'total' => $backOrderSubTotal,
            'discount' => $back_order_discount,
            'shipping_cost' => 0,
            'total' => number_format($backOrderSubTotal - $back_order_discount, 2, '.', ''),
            'default_coupon_id' => $order->default_coupon_id,
            'coupon' => $order->coupon,
            'promotion_details' => $order->promotion_details,
            'free_shipping' => $order->free_shipping
        ]);

        $redirectUrl = '';

        if (sizeof($order->items) == sizeof($request->ids)) {
            $order->review()->delete();
            $order->notifications()->delete();
            $order->storeCreditTransections()->delete();

            $newOrder->discount = $order->discount;
            $newOrder->store_credit = $order->store_credit;
            $newOrder->shipping_cost = $order->shipping_cost;
            $newOrder->total = $order->total;
            $newOrder->save();

            $order->delete();
            $redirectUrl = route('admin_all_orders');
        } else {
            
            // point system
            $pointDiscount = 0;
            if($order->reward_fixed){
                $pointDiscount = $order->reward_fixed;
            }
            if($order->reward_percantage){
                $pointDiscount = $order->reward_percantage;
            }
            
            $order->subtotal = number_format($order->subtotal - $backOrderSubTotal, 2, '.', '');
            //$discount = number_format($coupon_discount + $promotion_discount, 2, '.', '');
            $order->discount = $new_order_discount;
            $order->total = number_format($order->subtotal + $order->shipping_cost - $order->store_credit - $new_order_discount - $pointDiscount, 2, '.', '');
            $order->promotion_details = $promotion_details;
            $order->save();


            /*$order->total = $order->total - $subTotal;
            $order->subtotal = $order->subtotal - $subTotal;
            $order->save();*/
            $redirectUrl = route('admin_order_details', ['order' => $order->id]);
        }

        OrderItem::whereIn('id', $request->ids)->update(['order_id' => $newOrder->id]);

        Notification::create([
            'user_id' => $newOrder->user_id,
            'text' => $newOrder->order_number." need approval.",
            'link' => route('show_order_details', ['order' => $newOrder->id]),
            'view' => 0
        ]);

        return response()->json(['success' => true, 'url' => $redirectUrl]);
    }

    public function outOfStock(Request $request) {
        $orderItem = OrderItem::where('id', $request->ids[0])->first();
        $outOfStockSubTotal = OrderItem::whereIn('id', $request->ids)->sum('amount');
        $order = $orderItem->order;

        $order->subtotal = number_format($order->subtotal - $outOfStockSubTotal, 2, '.', '');

        $coupon = Promotion::where('coupon_code', $order->coupon)->first();
        $promotion = Promotion::where('id', $order->default_coupon_id)->where('status', 1)->first();

        $subTotal = $order->subtotal;

        $promotion_id = 0;
        $coupon_code = '';

        $discountFlash = 0;
        $coupon_discount = 0;
        $coupon_details = '';
        $free_shipping = 0;

        if(!empty($coupon)) {

            $coupon_code = $coupon->coupon_code;

            if($coupon->to_price_1) {
            } else {
                $coupon->to_price_1 = 1000000;
            }

            if($subTotal >= $coupon->from_price_1 && $subTotal <= $coupon->to_price_1) {

                if($coupon->promotion_type == 'Percentage discount by order amount') {

                    $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_1 / 100) * $subTotal, 2, '.', '');
                    $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_1 . '%]';

                } else {

                    $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_1, 2, '.', '');
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

                        $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_2 / 100) * $subTotal, 2, '.', '');
                        $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_2 . '%]';

                    } else {

                        $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_2, 2, '.', '');
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

                            $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_3 / 100) * $subTotal, 2, '.', '');
                            $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_3 . '%]';

                        } else {

                            $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_3, 2, '.', '');
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

                                $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_4 / 100) * $subTotal, 2, '.', '');
                                $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_4 . '%]';

                            } else {

                                $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_4, 2, '.', '');
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

                                    $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_5 / 100) * $subTotal, 2, '.', '');
                                    $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_5 . '%]';

                                } else {

                                    $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_5, 2, '.', '');
                                    $coupon_details = '["' . $coupon->coupon_code . '" - $' . $coupon->unit_price_discount_5 . ']';

                                }

                            }

                            if($coupon->free_shipping_5 == 1) {

                                $free_shipping = 1;

                            }

                        }
                    }
                }

            }

            $order->discount = $coupon_discount;
            $order->coupon = $coupon->coupon_code;
            $order->promotion_details = $coupon_details;
            $order->free_shipping = $free_shipping;

        }else{
            $coupon_discount = $order->discount;
        }

        $discountflash = 0;
        $promotion_discount = 0;

        $promotion_details = '';
        $free_shipping = 0;

        if(!empty($promotion)) {

            $promotion_id = $promotion->id;

            if($promotion->to_price_1) {
            } else {
                $promotion->to_price_1 = 1000000;
            }

            if($subTotal >= $promotion->from_price_1 && $subTotal <= $promotion->to_price_1) {

                if($promotion->promotion_type == 'Percentage discount by order amount') {

                    $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_1 / 100) * $subTotal, 2, '.', '');
                    $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_1 . '%]';

                } else {

                    $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_1,2, '.', '');
                    $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_1 . ']';

                }

                if($promotion->free_shipping_1 == 1) {

                    $free_shipping = 1;

                }

            } else {

                if($promotion->to_price_2) {
                } else {
                    $promotion->to_price_2 = 1000000;
                }

                if($subTotal >= $promotion->from_price_2 && $subTotal <= $promotion->to_price_2) {

                    if($promotion->promotion_type == 'Percentage discount by order amount') {

                        $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_2 / 100) * $subTotal, 2, '.', '');
                        $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_2 . '%]';

                    } else {

                        $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_2, 2, '.', '');
                        $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_2 . ']';

                    }

                } else {

                    if($promotion->to_price_3) {
                    } else {
                        $promotion->to_price_3 = 1000000;
                    }

                    if($subTotal >= $promotion->from_price_3 && $subTotal <= $promotion->to_price_3) {

                        if($promotion->promotion_type == 'Percentage discount by order amount') {

                            $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_3 / 100) * $subTotal, 2, '.', '');
                            $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_3 . '%]';

                        } else {

                            $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_3, 2, '.', '');
                            $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_3 . ']';

                        }

                    } else {

                        if($promotion->to_price_4) {
                        } else {
                            $promotion->to_price_4 = 1000000;
                        }

                        if($subTotal >= $promotion->from_price_4 && $subTotal <= $promotion->to_price_4) {

                            if($promotion->promotion_type == 'Percentage discount by order amount') {

                                $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_4 / 100) * $subTotal, 2, '.', '');
                                $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_4 . '%]';

                            } else {

                                $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_4, 2, '.', '');
                                $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_4. ']';

                            }

                        } else {

                            if($promotion->to_price_5) {
                            } else {
                                $promotion->to_price_5 = 1000000;
                            }

                            if($subTotal >= $promotion->from_price_5 && $subTotal <= $promotion->to_price_5) {

                                if($promotion->promotion_type == 'Percentage discount by order amount') {

                                    $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_5 / 100) * $subTotal, 2, '.', '');
                                    $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_5 . '%]';

                                } else {

                                    $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_5, 2, '.', '');
                                    $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_5 . ']';

                                }

                            }

                        }
                    }
                }

            }

            $order->discount = $promotion_discount;
            $order->default_coupon_id = $promotion->id;
            $order->promotion_details = $promotion_details;
            $order->free_shipping = $free_shipping;


        }
        // point system
        $pointDiscount = 0;
        if($order->reward_fixed){
            $pointDiscount = $order->reward_fixed;
        }
        if($order->reward_percantage){
            $pointDiscount = $order->reward_percantage;
        }

        $order->total = number_format($order->subtotal + $order->shipping_cost - $order->store_credit - $order->discount - $pointDiscount, 2, '.', '');

        $order->save();

        OrderItem::whereIn('id', $request->ids)->update([
            'qty' => 0,
            'total_qty' => 0,
            'amount' => 0,
        ]);
    }

    public function deleteOrderItem(Request $request) {
        $orderItem = OrderItem::where('id', $request->ids[0])->first();
        $deletedSubTotal = OrderItem::whereIn('id', $request->ids)->sum('amount');
        $order = $orderItem->order;

        $order->subtotal = number_format($order->subtotal - $deletedSubTotal, 2, '.', '');

        $coupon = Promotion::where('coupon_code', $order->coupon)->first();
        $promotion = Promotion::where('id', $order->default_coupon_id)->where('status', 1)->first();

        $subTotal = $order->subtotal;

        $promotion_id = 0;
        $coupon_code = '';

        $discountFlash = 0;
        $coupon_discount = 0;
        $coupon_details = '';
        $free_shipping = 0;

        if(!empty($coupon)) {

            $coupon_code = $coupon->coupon_code;

            if($coupon->to_price_1) {
            } else {
                $coupon->to_price_1 = 1000000;
            }

            if($subTotal >= $coupon->from_price_1 && $subTotal <= $coupon->to_price_1) {

                if($coupon->promotion_type == 'Percentage discount by order amount') {

                    $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_1 / 100) * $subTotal, 2, '.', '');
                    $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_1 . '%]';

                } else {

                    $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_1, 2, '.', '');
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

                        $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_2 / 100) * $subTotal, 2, '.', '');
                        $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_2 . '%]';

                    } else {

                        $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_2, 2, '.', '');
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

                            $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_3 / 100) * $subTotal, 2, '.', '');
                            $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_3 . '%]';

                        } else {

                            $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_3, 2, '.', '');
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

                                $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_4 / 100) * $subTotal, 2, '.', '');
                                $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_4 . '%]';

                            } else {

                                $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_4, 2, '.', '');
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

                                    $coupon_discount = number_format($discountFlash + ($coupon->percentage_discount_5 / 100) * $subTotal, 2, '.', '');
                                    $coupon_details = '["' . $coupon->coupon_code . '" - ' . $coupon->percentage_discount_5 . '%]';

                                } else {

                                    $coupon_discount = number_format($discountFlash + $coupon->unit_price_discount_5, 2, '.', '');
                                    $coupon_details = '["' . $coupon->coupon_code . '" - $' . $coupon->unit_price_discount_5 . ']';

                                }

                            }

                            if($coupon->free_shipping_5 == 1) {

                                $free_shipping = 1;

                            }

                        }
                    }
                }

            }

            $order->discount = $coupon_discount;
            $order->coupon = $coupon->coupon_code;
            $order->promotion_details = $coupon_details;
            $order->free_shipping = $free_shipping;

        }

        $discountflash = 0;
        $promotion_discount = 0;

        $promotion_details = '';
        $free_shipping = 0;

        if(!empty($promotion)) {

            $promotion_id = $promotion->id;

            if($promotion->to_price_1) {
            } else {
                $promotion->to_price_1 = 1000000;
            }

            if($subTotal >= $promotion->from_price_1 && $subTotal <= $promotion->to_price_1) {

                if($promotion->promotion_type == 'Percentage discount by order amount') {

                    $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_1 / 100) * $subTotal, 2, '.', '');
                    $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_1 . '%]';

                } else {

                    $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_1,2, '.', '');
                    $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_1 . ']';

                }

                if($promotion->free_shipping_1 == 1) {

                    $free_shipping = 1;

                }

            } else {

                if($promotion->to_price_2) {
                } else {
                    $promotion->to_price_2 = 1000000;
                }

                if($subTotal >= $promotion->from_price_2 && $subTotal <= $promotion->to_price_2) {

                    if($promotion->promotion_type == 'Percentage discount by order amount') {

                        $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_2 / 100) * $subTotal, 2, '.', '');
                        $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_2 . '%]';

                    } else {

                        $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_2, 2, '.', '');
                        $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_2 . ']';

                    }

                } else {

                    if($promotion->to_price_3) {
                    } else {
                        $promotion->to_price_3 = 1000000;
                    }

                    if($subTotal >= $promotion->from_price_3 && $subTotal <= $promotion->to_price_3) {

                        if($promotion->promotion_type == 'Percentage discount by order amount') {

                            $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_3 / 100) * $subTotal, 2, '.', '');
                            $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_3 . '%]';

                        } else {

                            $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_3, 2, '.', '');
                            $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_3 . ']';

                        }

                    } else {

                        if($promotion->to_price_4) {
                        } else {
                            $promotion->to_price_4 = 1000000;
                        }

                        if($subTotal >= $promotion->from_price_4 && $subTotal <= $promotion->to_price_4) {

                            if($promotion->promotion_type == 'Percentage discount by order amount') {

                                $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_4 / 100) * $subTotal, 2, '.', '');
                                $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_4 . '%]';

                            } else {

                                $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_4, 2, '.', '');
                                $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_4. ']';

                            }

                        } else {

                            if($promotion->to_price_5) {
                            } else {
                                $promotion->to_price_5 = 1000000;
                            }

                            if($subTotal >= $promotion->from_price_5 && $subTotal <= $promotion->to_price_5) {

                                if($promotion->promotion_type == 'Percentage discount by order amount') {

                                    $promotion_discount = number_format($discountflash + ($promotion->percentage_discount_5 / 100) * $subTotal, 2, '.', '');
                                    $promotion_details = '["' . $promotion->title . '" - ' . $promotion->percentage_discount_5 . '%]';

                                } else {

                                    $promotion_discount = number_format($discountflash + $promotion->unit_price_discount_5, 2, '.', '');
                                    $promotion_details = '["' . $promotion->title . '" - $' . $promotion->unit_price_discount_5 . ']';

                                }

                            }

                        }
                    }
                }

            }

            $order->discount = $promotion_discount;
            $order->default_coupon_id = $promotion->id;
            $order->promotion_details = $promotion_details;
            $order->free_shipping = $free_shipping;


        }
        
        // point system
        $pointDiscount = 0;
        if($order->reward_fixed){
            $pointDiscount = $order->reward_fixed;
        }
        if($order->reward_percantage){
            $pointDiscount = $order->reward_percantage;
        }

        $order->total = number_format($order->subtotal + $order->shipping_cost - $order->store_credit - $order->discount - $pointDiscount, 2, '.', '');

        $order->save();

        foreach ($request->ids as $itemId) {
            $orderItem = OrderItem::where('id', $itemId)->first();
            $color = Color::where('name', $orderItem->color)->first();
            $itemInventory = ItemInv::where('item_id', $orderItem->item_id)->where('color_id', $color->id)->first();
            if(isset($itemInventory)){
                ItemInv::where('item_id', $orderItem->item_id)->where('color_id', $color->id)->update([
                    'qty' => $itemInventory->qty + $orderItem->qty
                ]);
            }
        }

        OrderItem::whereIn('id', $request->ids)->delete();
    }


    public function addOrdinalNumberSuffix($num) {
        if (!in_array(($num % 100),array(11,12,13))){
            switch ($num % 10) {
                // Handle 1st, 2nd, 3rd
                case 1:  return $num.'st';
                case 2:  return $num.'nd';
                case 3:  return $num.'rd';
            }
        }
        return $num.'th';
    }

    public function orderDetailsPost(Order $order, Request $request) { 
        $order->load('items');

        $rules = [
            'order_discount' => 'required|numeric|min:0',
            'input_shipping_cost' => 'required|numeric|min:0',
            'tracking_number' => 'nullable|max:191',
            'shipping_method_id' => 'required'
        ];

        foreach ($order->items as $item) {
            $rules['size_'.$item->id.'.*'] = 'required|integer|min:0';
            $rules['pack_'.$item->id] = 'required|integer|min:0';
            $rules['unit_price_'.$item->id] = 'required|regex:/^[0-9]+(?:\.[0-9]{1,2})?$/';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $moreStoreCredit = $request->input_store_credit - $order->store_credit;
        $sc = StoreCredit::where('user_id', $order->user_id)->first();

        $validator->after(function ($validator) use($request, $moreStoreCredit, $order, $sc) {
            if ($moreStoreCredit > 0) {
                if (!$sc) {
                    $validator->errors()->add('input_store_credit', 'Insufficient Balance.');
                } else {
                    if ($moreStoreCredit > $sc->amount) {
                        $validator->errors()->add('input_store_credit', 'Insufficient Balance.');
                    }
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($sc) {
            $sc->amount -= $moreStoreCredit;
            $sc->save();
            $sc->touch();
        }

        if ($request->input_store_credit > 0) {
            $transection = StoreCreditTransection::where('order_id', $order->id)->where('amount', '<', 0)->first();

            if (!$transection) {
                StoreCreditTransection::create([
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'reason' => 'Used',
                    'amount' => $request->input_store_credit * (-1),
                ]);
            } else {
                $transection->amount = $request->input_store_credit * (-1);
                $transection->save();
                $transection->touch();
            }
        } else {
            StoreCreditTransection::where('order_id', $order->id)->where('amount', '<', 0)->delete();
        }

        $subTotal = 0.0;
        //dd($order->items);

        foreach ($order->items as $item) {
            $sizeVar = 'size_'.$item->id;
            $packVar = 'pack_'.$item->id;
            $perUnitPriceVar = 'unit_price_'.$item->id;
            $itemPerPack = 0;
            $size = '';

            foreach ($request->$sizeVar as $s) {
                $size .= $s.'-';
                $itemPerPack += (int) $s;
            }

            $size = rtrim($size, "-");

            $itemamount = number_format($request->$perUnitPriceVar * $itemPerPack, 2, '.', '');
 
            $item->pack = $size;
            $item->qty = $request->$packVar;
            $item->item_per_pack = $itemPerPack;
            $item->total_qty = $itemPerPack ;
            $item->per_unit_price = $request->$perUnitPriceVar; 
            $item->amount = number_format($request->$perUnitPriceVar * $itemPerPack, 2, '.', '');
            $item->save();

            $subTotal += $itemamount;
        }
        
        
        //point system
        $pointDiscount = 0;
        if($request->reward_fixed){
            $pointDiscount = $request->reward_fixed;
        }
        if($request->reward_percantage){
            $pointDiscount = $request->reward_percantage;
        }

        $order->admin_note = $request->admin_note;
        $order->invoice_number = $request->invoice_number;
        $order->store_credit = number_format($request->input_store_credit, 2, '.', '');
        $order->subtotal = number_format($subTotal, 2, '.', '');
        $order->discount = $request->order_discount;
        $order->shipping_cost = number_format($request->input_shipping_cost, 2, '.', '');
        $order->total = number_format($order->subtotal + (float) $request->input_shipping_cost - (float) $request->order_discount - (float) $request->input_store_credit - (float) $pointDiscount , 2, '.', '');
        $order->status = $request->order_status;
        $order->tracking_number = $request->tracking_number;
        $order->shipping_method_id = $request->shipping_method_id;

        if ($request->order_status == OrderStatus::$CONFIRM_ORDER)
            $order->confirm_at = Carbon::now()->toDateTimeString();
        else if ($request->order_status == OrderStatus::$PARTIALLY_SHIPPED_ORDER)
            $order->partially_shipped_at = Carbon::now()->toDateTimeString();
        else if ($request->order_status == OrderStatus::$FULLY_SHIPPED_ORDER)
            $order->fully_shipped_at = Carbon::now()->toDateTimeString();
        else if ($request->order_status == OrderStatus::$BACK_ORDER)
            $order->back_ordered_at = Carbon::now()->toDateTimeString();
        else if (in_array($request->order_status, [OrderStatus::$CANCEL_BY_AGREEMENT, OrderStatus::$CANCEL_BY_BUYER, OrderStatus::$CANCEL_BY_VENDOR]))
            $order->cancel_at = Carbon::now()->toDateTimeString();
        else if ($request->order_status == OrderStatus::$RETURNED)
            $order->return_at = Carbon::now()->toDateTimeString();

        $order->updated_by = Auth::user()->id;

        $order->touch();
        $order->save();

        if($request->notify_user && $request->notify_user) {
            Notification::create([
                'user_id' => $order->user_id,
                'text' => 'Order no. '.$order->order_number.' updated.',
                'link' => route('show_order_details', ['order' => $order->id]),
                'view' => 0
            ]);
        }
        try {
            // Send Mail to Buyer
            Mail::send('emails.buyer.order_modified', ['order' => $order], function ($message) use ($order) {
                $message->subject('Order Confirmed');
                $message->to($order->email, $order->name);
            });

            // Send Mail to Vendor
            $user = User::where('role', Role::$EMPLOYEE)->first();

            Mail::send('emails.vendor.new_order', ['order' => $order], function ($message) use ($order, $user) {
                $message->subject('New Order - '.$order->order_number);
                $message->to($user->email, $user->first_name.' '.$user->last_name);
            });
        } catch (\Exception $exception) {

        }

        return redirect()->back()->with('message', 'Order Updated!');
    }

    public function deleteOrder(Request $request) {
        $order = Order::where('id', $request->id)->first();

        $orderItems = OrderItem::where('order_id', $request->id)->get();
        foreach ($orderItems as $item) {
            $color = Color::where('name', $item->color)->first();
            $itemInventory = ItemInv::where('item_id', $item->item_id)->where('color_id', $color->id)->first();
            if(isset($itemInventory)){
                ItemInv::where('item_id', $item->item_id)->where('color_id', $color->id)->update([
                    'qty' => $itemInventory->qty + $item->qty
                ]);
            }
        }
        $order->review()->delete();
        $order->notifications()->delete();
        $order->storeCreditTransections()->delete();

        $order->delete();
    }

    public function printPacklist(Request $request) {


        $orderIds = explode(',', $request->order);
        $orders = [];
        $items = [];
        $final_qty = [];
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

            foreach ($order->items as $item)
            
            $allItems[$item->item_id][] = $item;

            // Vendor Logo
            $logo_path = '';


            $black = DB::table('settings')->where('name', 'logo-black')->first();
            if ($black)
                $logo_path = public_path($black->value);

            $order->vendor = $vendor;

            $orders[] = $order;
            $items[] = $allItems;
            $logo_paths[] = $logo_path;
        }


        $data = [
            'all_items' => $items,
            'final_qty' => $final_qty,
            'orders' => $orders,
            'logo_paths' => $logo_paths,
        ];

        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('admin.dashboard.orders.pdf.packlist', $data);

        return $pdf->stream('Picklist.pdf');
    }

    public function changeStatus(Request $request) {
        $orders = Order::whereIn('id', $request->id)->get();

        foreach ($orders as $order) {
            $order->status = $request->status;
            $order->updated_by = Auth::user()->id;

            if ($order->status == OrderStatus::$CONFIRM_ORDER){
                $order->confirm_at = Carbon::now()->toDateTimeString();
            }
            else if ($order->status == OrderStatus::$PARTIALLY_SHIPPED_ORDER){
                $order->partially_shipped_at = Carbon::now()->toDateTimeString();
            }
            else if ($order->status == OrderStatus::$FULLY_SHIPPED_ORDER){
                $order->fully_shipped_at = Carbon::now()->toDateTimeString();
            }
            else if ($order->status == OrderStatus::$BACK_ORDER){
                $order->back_ordered_at = Carbon::now()->toDateTimeString();
            }
            else if (in_array($order->status, [OrderStatus::$CANCEL_BY_AGREEMENT, OrderStatus::$CANCEL_BY_BUYER, OrderStatus::$CANCEL_BY_VENDOR])){
                $order->cancel_at = Carbon::now()->toDateTimeString();
                //new point system start
                if($order->points || $order->used_dollar_point){
                    $oldPoints = MetaBuyer::select('points','points_spent')->where('user_id',$order->user_id)->first();
                    $deletedPoints = $oldPoints->points - $order->points;
                    $storedPoints = $oldPoints->points_spent - $order->used_dollar_point;
                    MetaBuyer::where('user_id',$order->user_id)->update(['points'=> number_format($deletedPoints, 2, '.', ''),'points_spent'=>number_format($storedPoints, 2, '.', '')]);
                }
                //new point system end
            }
            else if ($order->status == OrderStatus::$RETURNED){
                $order->return_at = Carbon::now()->toDateTimeString();
            }

            $order->touch();
            $order->save();

            $this->sendOrderStatusMail($order);
        }
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

    public function sendOrderStatusMail($order) {
        
        $order_time = Carbon::parse($order->created_at)->format('F d ,Y h:i:s A ');
        $orderItem = OrderItem::where('order_id', $order->id)->count('item_id');
        $black_logo_path='';
        $black = DB::table('settings')->where('name', 'logo-black')->first();
        if ($black)
            $black_logo_path = asset($black->value);
         
        // Back order
        if ($order->status == OrderStatus::$BACK_ORDER) {
            
            Notification::create([
                'user_id' => $order->user_id,
                'text' => $order->order_number." need approval.",
                'link' => route('show_order_details', ['order' => $order->id]),
                'view' => 0
            ]);

            $order->rejected = 0;
            $order->save();
            // Send Mail to Buyer
            Mail::send('emails.buyer.back_order', ['order' => $order, 'order_time' => $order_time, 'order_item' => $orderItem,'logo'=>$black_logo_path], function ($message) use ($order) {
                $message->subject('Back Order');
                $message->to($order->email, $order->name);
            });

        }

        //Confirm Order
        if ($order->status == OrderStatus::$CONFIRM_ORDER) {
            // Send Mail to Buyer
            Mail::send('emails.buyer.order_confirmed', ['order' => $order, 'order_time' => $order_time, 'order_item' => $orderItem,'logo'=>$black_logo_path], function ($message) use ($order) {
                $message->subject('Order Confirmation');
                $message->to($order->email, $order->name);
            });
        }
        //Partially Shipped Order
        if ($order->status == OrderStatus::$PARTIALLY_SHIPPED_ORDER) {
            // Send Mail to Buyer
            Mail::send('emails.buyer.partially_shipped_order', ['order' => $order, 'order_time' => $order_time, 'order_item' => $orderItem,'logo'=>$black_logo_path], function ($message) use ($order) {
                $message->subject('Partially Shipped Order');
                $message->to($order->email, $order->name);
            });
        }
        //Fully Shipped Order
        if ($order->status == OrderStatus::$FULLY_SHIPPED_ORDER) {
            // Send Mail to Buyer
            Mail::send('emails.buyer.fully_shipped_order', ['order' => $order, 'order_time' => $order_time, 'order_item' => $orderItem, 'logo'=>$black_logo_path], function ($message) use ($order) {
                $message->subject('Shipping Confirmation');
                $message->to($order->email, $order->name);
            });
        }
        //Cancelled Order
        if ($order->status == OrderStatus::$CANCEL_BY_AGREEMENT || $order->status == OrderStatus::$CANCEL_BY_BUYER || $order->status == OrderStatus::$CANCEL_BY_VENDOR) {
            // Send Mail to Buyer
            Mail::send('emails.buyer.cancelled_order', ['order' => $order, 'order_time' => $order_time, 'order_item' => $orderItem, 'logo'=>$black_logo_path], function ($message) use ($order) {
                $message->subject('Cancelled Order');
                $message->to($order->email, $order->name);
            });
        }
    }
    //saif added new for incomplete order send mail
    public function incompleteOrderSendMail(Request $request){
        $mailBody= $request->all();
        // Send Mail to Buyer
        Mail::send('emails.buyer.order_incomplete',$mailBody, function ($message) use ($request) {

            $sendTo = $request->sender;
            $subject = $request->subject;
            $recipient = $request->recipient;

            $message->subject($subject);
            $message->to($recipient, 'wellmadeusa');
        });

        \DB::table('cart_items')->where('user_id', $request->userid)->update(['status' => 1]);
        return response()->json(['success' => $request->all()]);
    }

    public function exportShipping(Request $request){

        $ids = $request->id;
        $exportData = [];

        foreach ($ids as $id){
            $exportData[] = DB::table('orders')
                ->join('order_items','orders.id','=','order_items.order_id')
                ->join('items','order_items.item_id','=','items.id')
                ->select('orders.*','order_items.*', 'items.available_on')
                ->where('orders.id',$id)
                ->whereIn('orders.status', [OrderStatus::$FULLY_SHIPPED_ORDER, OrderStatus::$PARTIALLY_SHIPPED_ORDER])
                ->get();
        }
        /*$export_array[] = array('#','Brand','Order Number','Order Date', 'Ship Date Start','Ship Date End','Customer Code','Order Total','Style Number','Color','Size','Quantity','Price Per','Shipping Line 1','Shipping City','Shipping State','Shipping Zip','Shipping Country','Billing Line 1','Billing City','Billing State','Billing Zip','Billing Country','Order Notes');

        $i = 1;
        foreach ($exportData as $data) {
            foreach($data as $item){
                $export_array[] = array(
                    '#' => $i++,
                    'Brand' =>'Main Office',
                    'Order Number' =>$item->order_number,
                    'Order Date' =>date('d-m-y', strtotime($item->created_at)),
                    'Ship Date Start' =>date('d-m-y', strtotime($item->confirm_at)),
                    'Ship Date End' =>date('d-m-y', strtotime($item->fully_shipped_at)),
                    'Customer Code' =>$item->user_id,
                    'Order Total' =>$item->total,
                    'Style Number' =>$item->style_no,
                    'Color' =>$item->color,
                    'Size' =>$item->size,
                    'Quantity' =>$item->total_qty,
                    'Price Per' =>$item->per_unit_price,
                    'Shipping Line 1' =>$item->shipping_address,
                    'Shipping City' =>$item->shipping_city,
                    'Shipping State' =>$item->shipping_state,
                    'Shipping Zip' =>$item->shipping_zip,
                    'Shipping Country' =>$item->shipping_country,
                    'Billing Line 1' =>$item->billing_address,
                    'Billing City' =>$item->billing_city,
                    'Billing State' =>$item->billing_state,
                    'Billing Zip' =>$item->billing_zip,
                    'Billing Country' =>$item->billing_country,
                    'Order Notes' =>$item->admin_note,
                );
            }
        }*/



        $i = 1;
        foreach ($exportData as $data) {

            $totalAmount = 0;

            foreach($data as $item){
                $totalAmount += $item->total_qty * $item->per_unit_price;
            }

            foreach($data as $item){

                if($item->status = 4) {
                    $orderStatus = 'PARTIALLY SHIPPED';
                }

                if($item->status = 5) {
                    $orderStatus = 'FULLY SHIPPED';
                }

                $orderDiscount = number_format((($item->total_qty * $item->per_unit_price) / $totalAmount) * $item->discount, 2, '.', '');

                $cardNumber = decrypt($item->card_number);


                $export_array[] = array(
                    'orderDetailId' => $i++,
                    'orderId' =>$item->order_number,
                    'orderDate' =>date('d-m-y', strtotime($item->created_at)),
                    'poNumber' =>$item->order_number,
                    'companyName' =>$item->company_name,
                    'totalAmount' =>$item->total_qty * $item->per_unit_price,
                    'discount' => $orderDiscount ? $orderDiscount : '0',
                    'couponAmount' =>$item->coupon_amount ? $item->coupon_amount : '0.00',
                    'creditUsed' =>$item->store_credit ? $item->store_credit : '0.00',
                    'additionaldiscount' =>'0',
                    'HandlingFee' =>'0',
                    'shippingCharge' =>$item->shipping_cost ? $item->shipping_cost : '0.00',
                    'orderStatus' => $orderStatus,
                    'confirmDate' =>($item->confirm_at != null) ? date('d-m-y', strtotime($item->confirm_at)) : '',
                    'shipDate' =>($item->fully_shipped_at != null) ? date('d-m-y', strtotime($item->fully_shipped_at)) : '',
                    'payment' => '****' . substr($cardNumber, -4),
                    'shipment' =>$item->shipping,
                    'phoneNumber' =>$item->billing_phone,
                    'fax' =>'',
                    'billingStreet' =>$item->billing_address,
                    'billingCity' =>$item->billing_city,
                    'billingState' =>$item->billing_state,
                    'billingZipcode' =>$item->billing_zip,
                    'billingCountry' =>$item->billing_country,
                    'shippingStreet' =>$item->shipping_address,
                    'shippingCity' =>$item->shipping_city,
                    'shippingState' =>$item->shipping_state,
                    'shippingZipcode' =>$item->shipping_zip,
                    'shippingCountry' =>$item->shipping_country,
                    'styleNo' =>$item->style_no,
                    'vendorStyleNo' =>explode(' ',$item->style_no)[0],
                    'color' =>$item->color,
                    'size' =>$item->size,
                    /*'pack' =>str_replace('-', '  ', $item->pack),*/
                    'pack' =>$item->pack,
                    'totalQty' =>$item->total_qty,
                    'unitPrice' =>$item->per_unit_price,
                    'subTotal' => number_format(($item->total_qty * $item->per_unit_price) - $orderDiscount, 2, '.', ''),
                    'stockAvailability' => $item->available_on ? 'Arriving Soon' : 'In Stock'
                );
            }
        }
        //create excel
        Excel::create('Shipped Orders Data', function($excel) use($export_array) {
            $excel->sheet('Shipped Orders Data', function($sheet) use($export_array) {
                $sheet->fromArray($export_array);
            });
        })->download('xlsx');
    }

    public function exportBack(Request $request){

        $ids = $request->id;
        $exportData = [];

        foreach ($ids as $id){
            $exportData[] = DB::table('orders')
                ->join('order_items','orders.id','=','order_items.order_id')
                ->join('items','order_items.item_id','=','items.id')
                ->select('orders.*','order_items.*', 'items.available_on')
                ->where('orders.id',$id)
                ->where('orders.status', OrderStatus::$BACK_ORDER)
                ->get();
        }

        $i = 1;
        foreach ($exportData as $data) {

            $totalAmount = 0;

            foreach($data as $item){
                $totalAmount += $item->total_qty * $item->per_unit_price;
            }

            foreach($data as $item){

                $orderDiscount = number_format((($item->total_qty * $item->per_unit_price) / $totalAmount) * $item->discount, 2, '.', '');

                $cardNumber = decrypt($item->card_number);


                $export_array[] = array(
                    'orderDetailId' => $i++,
                    'orderId' =>$item->order_number,
                    'orderDate' =>date('d-m-y', strtotime($item->created_at)),
                    'poNumber' =>$item->order_number,
                    'companyName' =>$item->company_name,
                    'totalAmount' =>$item->total_qty * $item->per_unit_price,
                    'discount' => $orderDiscount ? $orderDiscount : '0',
                    'couponAmount' =>$item->coupon_amount ? $item->coupon_amount : '0.00',
                    'creditUsed' =>$item->store_credit ? $item->store_credit : '0.00',
                    'additionaldiscount' =>'0',
                    'HandlingFee' =>'0',
                    'shippingCharge' =>$item->shipping_cost ? $item->shipping_cost : '0.00',
                    'orderStatus' => 'BACKED',
                    'confirmDate' =>($item->confirm_at != null) ? date('d-m-y', strtotime($item->confirm_at)) : '',
                    'shipDate' =>($item->fully_shipped_at != null) ? date('d-m-y', strtotime($item->fully_shipped_at)) : '',
                    'payment' => '****' . substr($cardNumber, -4),
                    'shipment' =>$item->shipping,
                    'phoneNumber' =>$item->billing_phone,
                    'fax' =>'',
                    'billingStreet' =>$item->billing_address,
                    'billingCity' =>$item->billing_city,
                    'billingState' =>$item->billing_state,
                    'billingZipcode' =>$item->billing_zip,
                    'billingCountry' =>$item->billing_country,
                    'shippingStreet' =>$item->shipping_address,
                    'shippingCity' =>$item->shipping_city,
                    'shippingState' =>$item->shipping_state,
                    'shippingZipcode' =>$item->shipping_zip,
                    'shippingCountry' =>$item->shipping_country,
                    'styleNo' =>$item->style_no,
                    'vendorStyleNo' =>explode(' ',$item->style_no)[0],
                    'color' =>$item->color,
                    'size' =>$item->size,
                    /*'pack' =>str_replace('-', '  ', $item->pack),*/
                    'pack' =>$item->pack,
                    'totalQty' =>$item->total_qty,
                    'unitPrice' =>$item->per_unit_price,
                    'subTotal' => number_format(($item->total_qty * $item->per_unit_price) - $orderDiscount, 2, '.', ''),
                    'stockAvailability' => $item->available_on ? 'Arriving Soon' : 'In Stock'
                );
            }
        }
        //create excel
        Excel::create('Backed Orders Data', function($excel) use($export_array) {
            $excel->sheet('Backed Orders Data', function($sheet) use($export_array) {
                $sheet->fromArray($export_array);
            });
        })->download('xlsx');
    }

    public function exportNew(Request $request){

        $ids = $request->id;
        $exportData = [];

        foreach ($ids as $id){
            $exportData[] = DB::table('orders')
                ->join('order_items','orders.id','=','order_items.order_id')
                ->join('items','order_items.item_id','=','items.id')
                ->select('orders.*','order_items.*', 'items.available_on')
                ->where('orders.id',$id)
                ->where('orders.status', OrderStatus::$NEW_ORDER)
                ->get();
        }



        $i = 1;
        foreach ($exportData as $data) {


            $totalAmount = 0;

            foreach($data as $item){
                $totalAmount += $item->total_qty * $item->per_unit_price;
            }

            foreach($data as $item){
                $orderDiscount = number_format((($item->total_qty * $item->per_unit_price) / $totalAmount) * $item->discount, 2, '.', '');

                $cardNumber = decrypt($item->card_number);


                $export_array[] = array(
                    'orderDetailId' => $i++,
                    'orderId' =>$item->order_number,
                    'orderDate' =>date('d-m-y', strtotime($item->created_at)),
                    'poNumber' =>$item->order_number,
                    'companyName' =>$item->company_name,
                    'totalAmount' =>$item->total_qty * $item->per_unit_price,
                    'discount' => $orderDiscount ? $orderDiscount : '0',
                    'couponAmount' =>$item->coupon_amount ? $item->coupon_amount : '0.00',
                    'creditUsed' =>$item->store_credit ? $item->store_credit : '0.00',
                    'additionaldiscount' =>'0',
                    'HandlingFee' =>'0',
                    'shippingCharge' =>$item->shipping_cost ? $item->shipping_cost : '0.00',
                    'orderStatus' => 'NEW',
                    'confirmDate' =>($item->confirm_at != null) ? date('d-m-y', strtotime($item->confirm_at)) : '',
                    'shipDate' =>($item->fully_shipped_at != null) ? date('d-m-y', strtotime($item->fully_shipped_at)) : '',
                    'payment' => '****' . substr($cardNumber, -4),
                    'shipment' =>$item->shipping,
                    'phoneNumber' =>$item->billing_phone,
                    'fax' =>'',
                    'billingStreet' =>$item->billing_address,
                    'billingCity' =>$item->billing_city,
                    'billingState' =>$item->billing_state,
                    'billingZipcode' =>$item->billing_zip,
                    'billingCountry' =>$item->billing_country,
                    'shippingStreet' =>$item->shipping_address,
                    'shippingCity' =>$item->shipping_city,
                    'shippingState' =>$item->shipping_state,
                    'shippingZipcode' =>$item->shipping_zip,
                    'shippingCountry' =>$item->shipping_country,
                    'styleNo' =>$item->style_no,
                    'vendorStyleNo' =>explode(' ',$item->style_no)[0],
                    'color' =>$item->color,
                    'size' =>$item->size,
                    /*'pack' =>str_replace('-', '  ', $item->pack),*/
                    'pack' =>$item->pack,
                    'totalQty' =>$item->total_qty,
                    'unitPrice' =>$item->per_unit_price,
                    'subTotal' => number_format(($item->total_qty * $item->per_unit_price) - $orderDiscount, 2, '.', ''),
                    'stockAvailability' => $item->available_on ? 'Arriving Soon' : 'In Stock'
                );
            }
        }
        //create excel
        Excel::create('New Orders Data', function($excel) use($export_array) {
            $excel->sheet('New Orders Data', function($sheet) use($export_array) {
                $sheet->fromArray($export_array);
            });
        })->download('xlsx');
    }

    public function exportConfirm(Request $request){

        $ids = $request->id;
        $exportData = [];

        foreach ($ids as $id){
            $exportData[] = DB::table('orders')
                ->join('order_items','orders.id','=','order_items.order_id')
                ->join('items','order_items.item_id','=','items.id')
                ->select('orders.*','order_items.*', 'items.available_on')
                ->where('orders.id',$id)
                ->where('orders.status', OrderStatus::$CONFIRM_ORDER)
                ->get();
        }

        $i = 1;
        foreach ($exportData as $data) {

            $totalAmount = 0;

            foreach($data as $item){
                $totalAmount += $item->total_qty * $item->per_unit_price;
            }

            foreach($data as $item){

                $orderDiscount = number_format((($item->total_qty * $item->per_unit_price) / $totalAmount) * $item->discount, 2, '.', '');

                $cardNumber = decrypt($item->card_number);


                $export_array[] = array(
                    'orderDetailId' => $i++,
                    'orderId' =>$item->order_number,
                    'orderDate' =>date('d-m-y', strtotime($item->created_at)),
                    'poNumber' =>$item->order_number,
                    'companyName' =>$item->company_name,
                    'totalAmount' =>$item->total_qty * $item->per_unit_price,
                    'discount' => $orderDiscount ? $orderDiscount : '0',
                    'couponAmount' =>$item->coupon_amount ? $item->coupon_amount : '0.00',
                    'creditUsed' =>$item->store_credit ? $item->store_credit : '0.00',
                    'additionaldiscount' =>'0',
                    'HandlingFee' =>'0',
                    'shippingCharge' =>$item->shipping_cost ? $item->shipping_cost : '0.00',
                    'orderStatus' => 'CONFIRMED',
                    'confirmDate' =>($item->confirm_at != null) ? date('d-m-y', strtotime($item->confirm_at)) : '',
                    'shipDate' =>($item->fully_shipped_at != null) ? date('d-m-y', strtotime($item->fully_shipped_at)) : '',
                    'payment' => '****' . substr($cardNumber, -4),
                    'shipment' =>$item->shipping,
                    'phoneNumber' =>$item->billing_phone,
                    'fax' =>'',
                    'billingStreet' =>$item->billing_address,
                    'billingCity' =>$item->billing_city,
                    'billingState' =>$item->billing_state,
                    'billingZipcode' =>$item->billing_zip,
                    'billingCountry' =>$item->billing_country,
                    'shippingStreet' =>$item->shipping_address,
                    'shippingCity' =>$item->shipping_city,
                    'shippingState' =>$item->shipping_state,
                    'shippingZipcode' =>$item->shipping_zip,
                    'shippingCountry' =>$item->shipping_country,
                    'styleNo' =>$item->style_no,
                    'vendorStyleNo' =>explode(' ',$item->style_no)[0],
                    'color' =>$item->color,
                    'size' =>$item->size,
                    /*'pack' =>str_replace('-', '  ', $item->pack),*/
                    'pack' =>$item->pack,
                    'totalQty' =>$item->total_qty,
                    'unitPrice' =>$item->per_unit_price,
                    'subTotal' => number_format(($item->total_qty * $item->per_unit_price) - $orderDiscount, 2, '.', ''),
                    'stockAvailability' => $item->available_on ? 'Arriving Soon' : 'In Stock'
                );
            }
        }
        //create excel
        Excel::create('Confirmed Orders Data', function($excel) use($export_array) {
            $excel->sheet('Confirmed Orders Data', function($sheet) use($export_array) {
                $sheet->fromArray($export_array);
            });
        })->download('xlsx');
    }
    public function exportCancel(Request $request){

        $ids = $request->id;
        $exportData = [];

        foreach ($ids as $id){
            $exportData[] = DB::table('orders')
                ->join('order_items','orders.id','=','order_items.order_id')
                ->join('items','order_items.item_id','=','items.id')
                ->select('orders.*','order_items.*', 'items.available_on')
                ->where('orders.id',$id)
                ->whereIn('orders.status', [OrderStatus::$CANCEL_BY_BUYER, OrderStatus::$CANCEL_BY_VENDOR, OrderStatus::$CANCEL_BY_AGREEMENT])
                ->get();
        }

        $i = 1;
        foreach ($exportData as $data) {

            $totalAmount = 0;

            foreach($data as $item){
                $totalAmount += $item->total_qty * $item->per_unit_price;
            }

            foreach($data as $item){

                $orderDiscount = number_format((($item->total_qty * $item->per_unit_price) / $totalAmount) * $item->discount, 2, '.', '');

                $cardNumber = decrypt($item->card_number);

                if($item->status == 7) {
                    $orderStatus = 'CANCELLED BY BUYER';
                }

                if($item->status == 8) {
                    $orderStatus = 'CANCELLED BY VENDOR';
                }

                if($item->status == 9) {
                    $orderStatus = 'CANCELLED BY AGGREEMENT';
                }

                $export_array[] = array(
                    'orderDetailId' => $i++,
                    'orderId' =>$item->order_number,
                    'orderDate' =>date('d-m-y', strtotime($item->created_at)),
                    'poNumber' =>$item->order_number,
                    'companyName' =>$item->company_name,
                    'totalAmount' =>$item->total_qty * $item->per_unit_price,
                    'discount' => $orderDiscount ? $orderDiscount : '0',
                    'couponAmount' =>$item->coupon_amount ? $item->coupon_amount : '0.00',
                    'creditUsed' =>$item->store_credit ? $item->store_credit : '0.00',
                    'additionaldiscount' =>'0',
                    'HandlingFee' =>'0',
                    'shippingCharge' =>$item->shipping_cost ? $item->shipping_cost : '0.00',
                    'orderStatus' => $orderStatus,
                    'confirmDate' =>($item->confirm_at != null) ? date('d-m-y', strtotime($item->confirm_at)) : '',
                    'shipDate' =>($item->fully_shipped_at != null) ? date('d-m-y', strtotime($item->fully_shipped_at)) : '',
                    'payment' => '****' . substr($cardNumber, -4),
                    'shipment' =>$item->shipping,
                    'phoneNumber' =>$item->billing_phone,
                    'fax' =>'',
                    'billingStreet' =>$item->billing_address,
                    'billingCity' =>$item->billing_city,
                    'billingState' =>$item->billing_state,
                    'billingZipcode' =>$item->billing_zip,
                    'billingCountry' =>$item->billing_country,
                    'shippingStreet' =>$item->shipping_address,
                    'shippingCity' =>$item->shipping_city,
                    'shippingState' =>$item->shipping_state,
                    'shippingZipcode' =>$item->shipping_zip,
                    'shippingCountry' =>$item->shipping_country,
                    'styleNo' =>$item->style_no,
                    'vendorStyleNo' =>explode(' ',$item->style_no)[0],
                    'color' =>$item->color,
                    'size' =>$item->size,
                    /*'pack' =>str_replace('-', '  ', $item->pack),*/
                    'pack' =>$item->pack,
                    'totalQty' =>$item->total_qty,
                    'unitPrice' =>$item->per_unit_price,
                    'subTotal' => number_format(($item->total_qty * $item->per_unit_price) - $orderDiscount, 2, '.', ''),
                    'stockAvailability' => $item->available_on ? 'Arriving Soon' : 'In Stock'
                );
            }
        }
        //create excel
        Excel::create('Cancelled Orders Data', function($excel) use($export_array) {
            $excel->sheet('Cancelled Orders Data', function($sheet) use($export_array) {
                $sheet->fromArray($export_array);
            });
        })->download('xlsx');
    }
    
        // order for individual company
    public function companyOrder($id){
        $user_info = User::where('id',$id)->with('buyer','buyerShipping')->first();

        $all_orders = Order::where('user_id',$id)->with('visitors')->get();

        $customerOrders = Order::where('user_id',$id)->get();

        $customerVisits = Visitor::where('user_id',$id)->get();

        $customer_last_visit = Visitor::where('user_id',$id)->latest('created_at')->first();
        
        $all_order_data = [];
        $customer_order_data = [];
        $all_total = 0;
        $customer_total = 0; 
        $all_visit = 0;
        $customer_visit = 0;


        //all orders
        $all_order_data['user_all_order'] = count($all_orders);
        $all_order_data['user_created'] = date('m/d/Y', strtotime($user_info->created_at));

        foreach ($all_orders as $order) {
            $all_total = $all_total + $order->total; 
        }
        $all_order_data['total'] = $all_total;
        $all_order_data['all_visit'] = count($all_orders[0]->visitors);
        
        if(!empty($customer_last_visit->created_at)){
            $all_order_data['last_visit'] = date('m/d/Y', strtotime($customer_last_visit->created_at));
        }else{
            $all_order_data['last_visit'] = null;
        }

        //customer orders
        $customer_order_data['customer_all_order'] = count($customerOrders);
        
        if(!empty($customer_last_visit->created_at)){
            $customer_order_data['last_visit'] = date('m/d/Y', strtotime($customer_last_visit->created_at));
        }else{
            $customer_order_data['last_visit'] = null;
        }

        foreach ($customerOrders as $order) {
            $customer_total = $customer_total + $order->total; 
        }
        $customer_order_data['total'] = $customer_total;
        $customer_order_data['customer_visit'] = count($customerVisits);
        
        //get monthly statistics
        $stat_all_order = DB::select('select count(id) as order_count, year(created_at) as year, MONTHNAME(created_at) as month, sum(total) as total_amount from orders where user_id = '.$id.' and deleted_at is null group by year(created_at), MONTHNAME(created_at)');

        $stat_customer_order = DB::select('select count(id) as order_count, year(created_at) as year, MONTHNAME(created_at) as month, sum(total) as total_amount from orders where user_id = '.$id.' and deleted_at is null group by year(created_at), MONTHNAME(created_at)');

        return view('admin.dashboard.orders.company_view_orders',compact('user_info','all_order_data','customer_order_data','stat_all_order','stat_customer_order'));
    }

}
