<?php

namespace App\Http\Controllers\Admin;

use App\Enumeration\OrderStatus;
use App\Model\Item;
use App\Model\ItemImages;
use App\Model\Order;
    use App\Model\User;
use App\Model\Visitor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
 

class DashboardController extends Controller
{
    public function index() {

        $homePageVisitor = Visitor::where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-30 days')))->distinct()->count('ip');


        $homePageVisitorUnique = Visitor::where('created_at', '>=', date('Y-m-d H:i:s', strtotime('today')))
            ->distinct()
            ->count('ip');

        $totalSaleAmount = Order::whereIn('status', [OrderStatus::$FULLY_SHIPPED_ORDER])
            ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-30 days')))
            ->sum('total');

        $totalPendingOrder = Order::whereIn('status', [OrderStatus::$PARTIALLY_SHIPPED_ORDER, OrderStatus::$BACK_ORDER, OrderStatus::$CONFIRM_ORDER, OrderStatus::$NEW_ORDER])
            ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-30 days')))
            ->sum('total');

        $todayOrderAmount = Order::where('status', '!=', OrderStatus::$INIT)
            ->whereDate('created_at', date('Y-m-d'))
            ->sum('total');
        $total_customer = 0;
        $total_customer = User::with('buyer')->where('active',1)->count();
        
        $orders = Order::with('shippingMethod')
            ->where('status', OrderStatus::$NEW_ORDER)
            ->orderBy('created_at', 'desc')
            ->get(); 
            foreach($orders as &$order) {
                $count = Order::where('status', '!=', OrderStatus::$INIT)
                    ->where('user_id', $order->user['id'])
                    ->where('created_at', '<=', $order->created_at)
                    ->count();
                $order->count = $count;
            }
        // $yesterdayVisitor = Visitor::where('url', route('home'))
        //     ->whereDate('created_at', date('Y-m-d', strtotime('-1 days')))
        //     ->count();

        $yesterdayVisitor = Visitor::where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-1 days')))->distinct()->count('ip');
        
        $yesterdayOrderAmount = Order::where('status', '!=', OrderStatus::$INIT)
            ->whereDate('created_at', date('Y-m-d', strtotime('-1 days')))
            ->sum('total');

        $itemUploadedThisMonth = Item::where('status', 1)
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->count();

        $itemUploadedLastMonth = Item::where('status', 1)
            ->where('created_at', '>=', new Carbon('first day of last month'))
            ->where('created_at', '<=', new Carbon('last day of last month'))
            ->count();

        // Best Selling Items
        $sql = "SELECT items.id, items.name, items.style_no, items.price, t.count
                FROM items
                LEFT JOIN (SELECT item_id, SUM(total_qty) count FROM order_items GROUP BY item_id) t ON items.id = t.item_id
                WHERE items.deleted_at IS NULL AND items.status = 1
                ORDER BY count DESC
                LIMIT 6";

        $bestItems = DB::select($sql);

        foreach ($bestItems as &$item) {
            $image = ItemImages::where('item_id', $item->id)
                ->orderBy('sort')
                ->first();

            $imagePath = asset('images/no-image.png');

            if ($image)
                $imagePath = asset($image->image_path);

            $item->image_path = $imagePath;
        }

        // Order Count By Month
        $startDate = [];
        $endDate = [];
        $orderCountLabel = [];
        $orderCount = [];
        $returnOrder = [];

        for($i=5; $i >= 0; $i--) {
            $orderCountLabel[] = date('Y/m', strtotime("-$i month"));
            $startDate[] = date('Y-m-01', strtotime("-$i month"));
            $endDate[] = date('Y-m-t', strtotime("-$i month"));
        }

        for($i=0; $i < 6; $i++) {
            $orderCount[] = Order::where('status', '!=', OrderStatus::$INIT)
                ->where('status', '!=', OrderStatus::$RETURNED)
                ->where('status', '!=', OrderStatus::$CANCEL_BY_VENDOR)
                ->where('status', '!=', OrderStatus::$CANCEL_BY_BUYER)
                ->where('status', '!=', OrderStatus::$CANCEL_BY_AGREEMENT)
                ->where('created_at', '>=', $startDate[$i])
                ->where('created_at', '<=', $endDate[$i])
                ->count();

            $returnOrder[] = Order::where('status', '=', OrderStatus::$RETURNED)
                ->where('created_at', '>=', $startDate[$i])
                ->where('created_at', '<=', $endDate[$i])
                ->count();
        }

        // Item Upload chart
        $uploadCount = [];

        for($i=0; $i < 6; $i++) {
            $uploadCount[] = Item::where('status', 1)
                ->where('created_at', '>=', $startDate[$i])
                ->where('created_at', '<=', $endDate[$i])
                ->count();
        }

        $data = [
            'homePageVisitor' => $homePageVisitor,
            'homePageVisitorUnique' => $homePageVisitorUnique,
            'totalSaleAmount' => $totalSaleAmount,
            'totalPendingOrder' => $totalPendingOrder,
            'todayOrderAmount' => $todayOrderAmount,
            'yesterdayVisitor' => $yesterdayVisitor,
            'yesterdayOrderAmount' => $yesterdayOrderAmount,
            'itemUploadedThisMonth' => $itemUploadedThisMonth,
            'itemUploadedLastMonth' => $itemUploadedLastMonth,
            'orderCountLabel' => json_encode($orderCountLabel),
            'orderCount' => json_encode($orderCount),
            'returnOrder' => json_encode($returnOrder),
            'uploadCount' => json_encode($uploadCount),
            'total_customer' => $total_customer,
            'orders' => $orders,
        ];

        return view('admin.dashboard.index', compact('data', 'bestItems'))->with('page_title', 'Dashboard');
    }
}
