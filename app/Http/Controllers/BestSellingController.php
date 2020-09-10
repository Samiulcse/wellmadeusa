<?php

namespace App\Http\Controllers;

use App\Enumeration\PageEnumeration;
use App\Enumeration\Role;
use App\Model\Category;
use App\Model\Item;
use App\Model\MasterColor;
use App\Model\MetaVendor;
use App\Model\TopBanner;
use App\Model\WishListItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BestSellingController extends Controller
{
    public function showItems()
    {
        $date = '';
        $cat = '';

        // Category
        $categories = Category::where('parent', 0)
            ->orderBy('sort')
            ->get();

        foreach ($categories as &$category) {
            $categoryCountQuery = Item::query();
            $categoryCountQuery->where('status', 1)->where('default_parent_category', $category->id);

            if ($date == '') {
                $category->count = $categoryCountQuery->count();
            } else {
                $category->count = $categoryCountQuery->whereDate('created_at', $date)->count();
            }
        }

        $default_categories = $categories;

        // Arrival Dates
        $totalCount = Item::where('status', 1)->count();
        $byArrivalDate = [];
        $byArrivalDate[] = [
            'name' => 'All',
            'count' => number_format($totalCount),
            'day' => 'A'
        ];

        for($i=0; $i <= 6; $i++) {
            $count = Item::where('status', 1)
                ->whereDate('created_at', date('Y-m-d', strtotime("-$i day")))
                ->count();

            $byArrivalDate[] = [
                'name' => date('F j', strtotime("-$i day")),
                'count' => number_format($count),
                'day' => $i
            ];
        }

        // Vendors
        $vendors = MetaVendor::where('verified', 1)
            ->where('active', 1)
            ->orderBy('company_name')->get();

        // Wishlist
        $obj = new WishListItem();
        $wishListItems = $obj->getItemIds();

        // Master Colors
        $masterColors = MasterColor::orderBy('name')->get();

        $defaultItemImage = DB::table('settings')->where('name', 'default-item-image')->first();
        if ($defaultItemImage)
            $defaultItemImage_path = asset($defaultItemImage->value);

        // Best Selling
        // $bestSellingItems = Item::where('items.status', 1)
        // ->with('images')
        // ->select(DB::raw('COUNT(order_items.total_qty) AS maxSale'), 'items.*')
        // ->join('order_items', 'items.id', '=', 'order_items.item_id')
        // ->join('orders', 'orders.id', '=', 'order_items.order_id')
        // ->groupBy('items.id')
        // ->orderBy(DB::raw('COUNT(order_items.total_qty)'), 'DESC')
        // ->limit(12)
        // ->get();


         /*Notification Banner module*/
        $top_notification_banner_module = DB::table('top_banners')->where('page', '11')->get();

        if(count($top_notification_banner_module) == 0)
        {
            $top_notification_banner_module = [];
        }

        return view('pages.best_selling', compact('items', 'bestSellingItems', 'byArrivalDate', 'categories', 'default_categories', 'vendors',
            'masterColors', 'wishListItems', 'defaultItemImage_path', 'top_notification_banner_module'));
    }

    public function get_best_selling_items_load_ajax()
    {
        // Best Selling
        $query = Item::where('items.status', 1);
        $query->with('images.color');
        $query->select(DB::raw('COUNT(order_items.total_qty) AS maxSale'), 'items.*');
        $query->join('order_items', 'items.id', '=', 'order_items.item_id');
        $query->join('orders', 'orders.id', '=', 'order_items.order_id');
        $query->groupBy('items.id');

        if ( isset($_GET['sort_by']) && $_GET['sort_by'] == 'low_to_high' ) {
            $query->orderBy('items.price', 'asc');
        }
        if ( isset($_GET['sort_by']) && $_GET['sort_by'] == 'high_to_low' ) {
            $query->orderBy('items.price', 'desc');
        }
        $query->orderBy(DB::raw('COUNT(order_items.total_qty)'), 'DESC');

        $total_record = $query->get();

        $offset = (int) $_GET['offset'];
        $limit = $_GET['limit'];
        $skip = ($offset - 1) * $limit;

        $query->skip($skip)->take($limit);
        $query = $query->get();
        $last_pagination_index = ceil(count($total_record) / $limit);

        $data = array();
        $data['total_record'] = count($total_record);
        $data['records'] = $query;
        $data['offset'] = $offset;

        $data['last_pagination_index'] = $last_pagination_index;

        return json_encode($data);
    }
}
