<?php

namespace App\Http\Controllers;

use App\Enumeration\PageEnumeration;
use App\Enumeration\Role;
use App\Model\Category;
use App\Model\Item;
use App\Model\MasterColor;
use App\Model\MasterFabric;
use App\Model\Pack;
use App\Model\MetaVendor;
use App\Model\TopBanner;
use App\Model\WishListItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NewArrivalController extends Controller
{
    public function showItems(Request $request) { 
        $date = '';
        $cat = '';

        $query = Item::query();
        if ( Auth::guest() ) {
            $query->where('status', 1)->where('guest_image', 1);
        }
        else {
            $query->where('status', 1)->where('guest_image', 0);
        }

        if (isset($request->D)) {
            if ($request->D != 'A') {
                $date = date('Y-m-d', strtotime("-$request->D day"));
                $query->whereDate('created_at', $date);
            }
        }

        if (isset($request->C)) {
            $cat = $request->C;
            $query->where('default_parent_category', $request->C);
        }

        $query->orderBy('created_at', 'desc');
        //$items = $query->paginate(30);
        $items = [];

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

        $banner = TopBanner::whereNull('category_id')->where('page',10)->first(); 
        $category = Category::where('parent', '=', 0)->orderBy('sort')->get();

        $defaultItemImage = DB::table('settings')->where('name', 'default-item-image')->first();
        if ($defaultItemImage)
            $defaultItemImage_path = asset($defaultItemImage->value);

        $totalCount = Item::where('status', 1)->count();
        $febric = MasterFabric::all();
        $packs = pack::all();
        
        //get home page settings data
        $settings_data = DB::table('home_page_settings')->get();
        
        return view('pages.new_arrival', compact('items','febric','packs', 'byArrivalDate', 'categories', 'vendors',
            'masterColors', 'wishListItems', 'defaultItemImage_path','banner','category','totalCount','settings_data'));
    }


    public function getNewArrivalItems(Request $request) {
        $query = Item::query();

        // $query->where('status', 1);

        if ($request->D && $request->D != '') {
            if ($request->D != 'A') {
                $date = date('Y-m-d', strtotime("-$request->D day"));
                $query->whereDate('created_at', $date);
            }
        }
        if (isset($request->C)) {
            $cat = $request->C;
            $query->where('default_parent_category', $request->C);
        }

        if ($request->categories && sizeof($request->categories) > 0)
            $query->whereIn('default_second_category', $request->categories);

        if ($request->masterCategory && sizeof($request->masterCategory) > 0)
            $query->whereIn('default_parent_category', $request->masterCategory);


        if ($request->vendors && sizeof($request->vendors) > 0)
            $query->whereIn('vendor_meta_id', $request->vendors);


        //Master color fileter
        if ($request->masterColors && sizeof($request->masterColors) > 0) {
            $masterColors = $request->masterColors;

            $query->whereHas('colors', function ($query) use ($masterColors) {
                $query->whereIn('master_color_id', $masterColors);
            });
        }

        //master fabric filter 
        if ($request->masterfeb && sizeof($request->masterfeb) > 0) {
            $masterfeb = $request->masterfeb;

            $query->whereHas('fabric', function ($query) use ($masterfeb) {
                $query->whereIn('id', $masterfeb);
            });
        }

        //master packs filter 
        if ($request->packs && sizeof($request->packs) > 0) {
            $packs = $request->packs;

            $query->whereHas('pack_filter', function ($query) use ($packs) {
                $query->whereIn('id', $packs);
            });
        }

        // Search
        if ($request->searchText && $request->searchText != ''){
            if ($request->searchOption == '1')
                $query->where('style_no', 'like','%'.$request->searchText.'%');
            if ($request->searchOption == '2')
                $query->where('description', 'like','%'.$request->searchText.'%');
        }

        if ($request->priceMin && $request->priceMin != '')
            $query->where('price', '>=',$request->priceMin);

        if ($request->priceMax && $request->priceMax != '')
            $query->where('price', '<=',$request->priceMax);

        $query->where('created_at', '>=', date('Y-m-d', strtotime('-30 days')));

        // Sorting


        // Sorting
        if ($request->sorting){
            if ($request->sorting == '1') {
                $query->orderBy('activated_at', 'desc');
            } else if ($request->sorting == '2') {
                $query->orderBy('price', 'asc');
            }
            else if ($request->sorting == '3'){
                $query->orderBy('price', 'desc');
            }
            else if ($request->sorting == '4')
                $query->orderBy('style_no','desc');
        }
        else {
            $query->orderBy('activated_at', 'desc');
        }
        if($request->limit == 0) {
            $limit = 40;
        }else {
            $limit = $request->limit;
        }
        
        // $items = $query->with('images', 'vendor', 'colors')->where('status',1)->paginate(50);
        
        $items = $query->with('images', 'vendor', 'colors')->where('status',1)->limit($limit)->get();
        
        // $paginationView = $items->onEachSide(1)->links('others.pagination');
        // $paginationView = trim(preg_replace('/\r\n/', ' ', $paginationView));

        $blockedVendorIds = [];

        foreach($items as &$item) {
            $price = '';
            $colorsImages = [];

            foreach($item->colors as $color) {
                if ($color->pivot->available ==1) {
                    $colorsImages[$color->name] = array([asset($color->image_path)],[$color->id]);
                }
//                foreach ($item->images as $image) {
//                    if ($image->color_id == $color->id) {
//                        $colorsImages[$color->name] = asset($image->thumbs_image_path);
//                        break;
//                    }
//                }
            }

             
            if ($item->orig_price != null)
//                $price .= '<del>$' . number_format($item->orig_price, 2, '.', '') . '</del> ';

            $price .= '$' . number_format($item->price, 2, '.', '');
            

            $imagePath2 = '';
            $imagePath = asset('images/no-image.png');

            if (sizeof($item->images) > 0)
                $imagePath = asset($item->images[0]->compressed_image_path);

            if (sizeof($item->images) > 1)
                $imagePath2 = asset($item->images[1]->compressed_image_path);
                
            $item->price = '$'.sprintf('%0.2f', $item->price);
            $item->imagePath = $imagePath;
            $item->imagePath2 = $imagePath2;
            $item->detailsUrl = route('item_details_page', ['item' => $item->id, 'name' => changeSpecialChar($item->name)]);
            // $item->price = $price;
            $item->colorsImages = $colorsImages;
            $item->video = ($item->video) ? asset($item->video) : null;
            // Blocked Check
            if (in_array($item->vendor_meta_id, $blockedVendorIds)) {
                $item->imagePath = asset('images/blocked.jpg');
                $item->vendor->company_name = '';
                $item->vendorUrl = '';
                $item->style_no = '';
                $item->price = '';
                $item->available_on = '';
                $item->colors->splice(0);
            }
        } 
        return ['items' => $items->toArray()];
        // return ['items' => $items->toArray(), 'pagination' => $paginationView];
    }

    public function NewInSlug($slug)// New arrival arror ding to each category start
    { 
     
    $category=DB::table('categories')->where('slug','=', "$slug")->first();
    $id=$category->id; 
        $menus= $category=Category::where('parent', '=', 0)->orderBy('sort')->get();  
        $items = [];
         
        $today = Carbon::today();
        $query = Item::where('status', 1)->where('default_parent_category', $id)->with('images.color')->where('created_at', '>', $today->subDays(60));

        $total = Item::where('status', 1)->where('default_parent_category', $id)->where('created_at', '>', $today->subDays(60))->count();
        
        
        
         // Active Items Order
        if (isset($request->s1) && $request->s1 != '') {
            if ($request->s1 == '4')
                $query->orderBy('price');
            else if ($request->s1 == '1')
                $query->orderBy('updated_at', 'desc');
            else if ($request->s1 == '2')
                $query->orderBy('created_at', 'desc');
            else if ($request->s1 == '3')
                $query->orderBy('activated_at', 'desc');
            else if ($request->s1 == '5')
                $query->orderBy('price', 'desc');
            else if ($request->s1 == '6')
                $query->orderBy('style_no');
            else if ($request->s1 == '0') {
                $query->orderBy('sorting');
                $query->orderBy('activated_at', 'desc');
            }
        } else {
            $query->orderBy('sorting');
            $query->orderBy('activated_at', 'desc');
        } 
         
        $items = $query->paginate(40)->toArray();
        
        //get color details

       $selected_color = array();
        $i=0;
        foreach ($items['data'] as $item) {
            $id = $item['id'];
            //$item_images_sql = "SELECT * FROM `item_images` WHERE item_id = $id GROUP BY color_id ORDER BY min(sort)";
            $item_images_sql = "SELECT a.*".
                "FROM `item_images` a INNER JOIN".
                "(".
                    "SELECT  *, MIN(sort) AS minsort ".
                    "FROM  `item_images` ".
                    "WHERE item_id = $id ".
                    "GROUP BY color_id ".
                ") b ON a.color_id = b.color_id AND a.sort = b.minsort AND a.`item_id` = $id ".
                "ORDER BY a.sort";
            $items['data'][$i]['images'] = DB::select($item_images_sql);
            $color_sql = "SELECT `colors`.* FROM `colors`  " . 
                "INNER JOIN (SELECT color_id, min(sort) AS sort FROM `item_images` WHERE color_id IN (SELECT `color_id` FROM `color_item` WHERE item_id = $id and available = 1) and item_id = $id GROUP BY(color_id) ORDER BY min(sort)) AS `SelectedItem`" . 
                "ON `colors`.id = `SelectedItem`.color_id ORDER BY `SelectedItem`.sort ASC";
            $available_item = DB::select($color_sql);
            $items['data'][$i]['color_images'] = $available_item;
            $i++;
        }
                
        // $slug='1';
        
                
        // Wishlist
        $wishlistItem = [];
        if( Auth::check() && Auth::user() && Auth::user()->id){
            $obj = new WishListItem();
            $wishlistItem = $obj->getItemIds();
        }

        $defaultItemImage = DB::table('settings')->where('name','default-item-image')->first();
        if ($defaultItemImage)
            $defaultItemImage_path = asset($defaultItemImage->value);
        $banner = TopBanner::where('page',PageEnumeration::$NEW_ARRIVAL)->get();
        return view('pages.new_arrival', compact(
            'items', 'wishlistItem', 'menus', 'defaultItemImage_path','menus','category','slug','banner','total'));
}
// New arrival arror ding to each category exit

    public function get_new_arrival_items_load_ajax()
    {
        $today = Carbon::today();
        // New Arrivals
        $query = Item::where('status', 1)->with('images.color')->where('created_at', '>', $today->subDays(60));

        if ( isset($_GET['sort_by']) && $_GET['sort_by'] == 'low_to_high' ) {
            $query->orderBy('price', 'asc');
        }
        if ( isset($_GET['sort_by']) && $_GET['sort_by'] == 'high_to_low' ) {
            $query->orderBy('price', 'desc');
        }

        $query->orderBy('created_at', 'desc');

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
