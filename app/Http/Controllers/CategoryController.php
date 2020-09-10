<?php

namespace App\Http\Controllers;

use App\Enumeration\Role;
use App\Enumeration\VendorImageType;
use App\Model\BodySize;
use App\Model\Category;
use App\Model\Item;
use App\Model\ItemCategory;
use App\Model\ItemView;
use App\Model\MasterColor;
use App\Model\MasterFabric;
use App\Model\MetaVendor;

use App\Model\ItemInv;
use App\Model\Pattern;
use App\Model\Pack;
use App\Model\Setting;
use App\Model\Style;
use App\Model\VendorImage;
use App\Model\Visitor;
use App\Model\WishListItem;
use App\Model\Page;
use App\Enumeration\PageEnumeration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;

use DrewM\MailChimp\MailChimp;
use Spatie\Newsletter\Newsletter;
use Spatie\Newsletter\NewsletterListCollection;
use Vinkla\Instagram\Instagram;
use App\Model\TopBanner;

class CategoryController extends Controller
{

    public function CategoryPage($category){ 
        $all = explode('-',$category);
        $showall = 0;
        if(end($all) == 'all'){
            $showall = 1;
            $category = substr($category, 0, -9);
        } 
        $c = null;
        $c = Category::where('slug', '=', $category)->first();

        if($c){

/* ----------------------- check category landing page ---------------------- */
            if($c->landing_page != null && $showall==0 ){
                $ids=[];
                $newin = VendorImage::where('type', VendorImageType::$FRONT_PAGE_RECOMMEND_BANNER)->where('cat_id',$c->id)->get();
                foreach($newin as $item){
                    $ids[]= $item->item_id;
                }
                
                $topimages = VendorImage::where('cat_id', $c->id)->where('type',VendorImageType::$LANDING_CATEGORY_TOP)->orderBy('sort', 'asc')->get();
                $bottomimages = VendorImage::where('cat_id', $c->id)->where('type',VendorImageType::$LANDING_CATEGORY_BOTTOM)->orderBy('sort', 'asc')->get(); 
                $newArrivalItems = Item::where('status', 1)
                ->whereIn('id',$ids)
                ->orderBy('sorting', 'asc')
                ->with('images')
                ->limit(40)
                ->get();
                $category = $c;
                return view('pages.category_landing_page', compact('newArrivalItems','topimages','bottomimages','category'));
            }

            // New Modify
            $category = $c;

            $category->load('subCategories', 'lengths', 'parentCategory');

            $patterns = Pattern::where('parent_category_id', $category->id)
                ->orderBy('name')
                ->get();

            $styles = Style::where('parent_category_id', $category->id)
                ->orderBy('name')
                ->get();

            $masterColors = MasterColor::orderBy('name')->get();
            $masterFabrics = MasterFabric::orderBy('name')->get();

            $items = [];

            // Wishlist
            $obj = new WishListItem();
            $wishListItems = $obj->getItemIds();
            $defaultItemImage = DB::table('settings')->where('name', 'default-item-image')->first();
            if ($defaultItemImage)
                $defaultItemImage_path = asset($defaultItemImage->value);
            $febric = MasterFabric::all();
            $packs = pack::all();
            $totalCount = Item::where('status', 1)->count();
            $banner = TopBanner::where('category_id',$category->id)->first();
            $categories = Category::where('parent', 0)->orderBy('sort')->get();

        /*Notification Banner module*/
            $top_notification_banner_module = DB::table('top_banners')->where('category_id', $category->id)->get();
            if(count($top_notification_banner_module) == 0) {
                $top_notification_banner_module = [];
            }

            return view('pages.category', compact('category','categories', 'masterColors',
                'masterFabrics', 'items', 'wishListItems', 'patterns', 'styles', 'packs','febric','defaultItemImage_path','totalCount','banner','top_notification_banner_module'));
        }else{
            return redirect()->route('home');
        }
    }

    public function getItemsCategory(Request $request) { 
        $query = Item::query();
         if ($request->secondCategory && $request->secondCategory != '')
            $query->where('default_second_category', $request->secondCategory);

        if ($request->categories && sizeof($request->categories) > 0)
            $query->whereIn('default_parent_category', $request->categories);

        if ($request->vendors && sizeof($request->vendors) > 0)
            $query->whereIn('vendor_meta_id', $request->vendors);


        if ($request->masterCategory && sizeof($request->masterCategory) > 0)
            $query->whereIn('default_parent_category', $request->masterCategory);


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

        if ($request->bodySizes && sizeof($request->bodySizes) > 0)
            $query->whereIn('body_size_id', $request->bodySizes);

        if ($request->patterns && sizeof($request->patterns) > 0)
            $query->whereIn('pattern_id', $request->patterns);

        if ($request->lengths && sizeof($request->lengths) > 0)
            $query->whereIn('length_id', $request->lengths);

        if ($request->styles && sizeof($request->styles) > 0)
            $query->whereIn('style_id', $request->styles);

        if ($request->fabrics && sizeof($request->fabrics) > 0)
            $query->whereIn('master_fabric_id', $request->fabrics);

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

        // Sorting
        // $query->orderBy('activated_at', 'desc');


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
            $query->orderBy('sorting', 'asc');
        }

        // For On scroll loading

        if($request->limit == 0) {
            $limit = 40;
        }else {
            $limit = $request->limit;
        }

        // $items = $query->with('images', 'vendor', 'colors')->where('status',1)->paginate(50);
        $items = $query->with('images', 'vendor', 'colors')->where('status',1)->limit($limit)->get();


//        $paginationView = $items->onEachSide(1)->links('others.pagination');
//        $paginationView = trim(preg_replace('/\r\n/', ' ', $paginationView));

        // Wishlist
        $obj = new WishListItem();
        $wishListItems = $obj->getItemIds();
        foreach($items as &$item) {

            // Price
            $price = '';
            $colorsImages = [];

//            if (Auth::check() && Auth::user()->role == Role::$BUYER) {
                if ($item->orig_price != null)
//                    $price .= '<del>$' . number_format($item->orig_price, 2, '.', '') . '</del> ';

                $price .= '$' . number_format($item->price, 2, '.', '');
//            }

            $colorsImages = [];

            foreach($item->colors as $color) {
                if ($color->pivot->available ==1) {
                    $colorsImages[$color->name] = array([asset($color->image_path)],[$color->id]);
                }
//                foreach ($item->images as $image) {
//                    if ($image->color_id == $color->id) {
//                        $colorsImages[$color->name] = asset($image->compressed_image_path);
//                        break;
//                    }
//                }
            }

            $item->colorsImages = $colorsImages;

            // Image
            $imagePath = '';
            $imagePath2 = '';

            $imagePath = asset('images/no-image.png');
            if (Auth::check() && Auth::user()->role == Role::$BUYER) {
                if (sizeof($item->images) > 0){ 
                    $imagePath = asset($item->images[0]->list_image_path);
                } 
                if (sizeof($item->images) > 1){ 
                    $imagePath2 = asset($item->images[1]->list_image_path);
                }
            }else{
                $defaultItemImage = DB::table('settings')->where('name', 'default-item-image')->first();
                if ($defaultItemImage)
                    $defaultItemImage_path = asset($defaultItemImage->value);

                $imagePath = $defaultItemImage_path;
                $imagePath2 = $defaultItemImage_path;
            }

            $item->price = '$'.sprintf('%0.2f', $item->price);
            $item->imagePath = $imagePath;
            $item->imagePath2 = $imagePath2;
            $item->detailsUrl = route('item_details_page', ['item' => $item->id, 'name' => changeSpecialChar($item->name)]);

            $wishListButton = '';
            if (in_array($item->id, $wishListItems)) {
                $wishListButton = '<button class="btn btn-danger btn-sm btnRemoveWishList" data-id="'.$item->id.'"><i class="icon-heart"></i></button>';
            } else {
                $wishListButton = '<button class="btn btn-default btn-sm btnAddWishList" data-id="'.$item->id.'"><i class="icon-heart"></i></button>';
            }

            $item->wishListButton = $wishListButton;
            // $item->price = $price;
            $item->video = ($item->video) ? asset($item->video) : null;
            $inv = ItemInv::where('item_id',$item->id)->where('available_on','!=', null)->where('available_on','!=','null')->get();
            if(count($inv) > 0){
                $item->available_on = $inv[0]->available_on; 
            }
        }


        return ['items' => $items->toArray()]; 
    }

    public function Category($category)
    {

        $categories = Category::where('parent', '=', 0)->get();

        $c = null;

        foreach ($categories as $cat) {
            if (changeSpecialChar($cat->slug) == $category) {
                $c = $cat;
                break;
            }
        }
        if($c){

            // New Modify
            $category = $c;


            $category->load('subCategories', 'lengths', 'parentCategory');

            $patterns = Pattern::where('parent_category_id', $category->id)
                ->orderBy('name')
                ->get();

            $styles = Style::where('parent_category_id', $category->id)
                ->orderBy('name')
                ->get();

            $masterColors = MasterColor::orderBy('name')->get();
            $masterFabrics = MasterFabric::orderBy('name')->get();

            $items = [];

            // Wishlist
            $obj = new WishListItem();
            $wishListItems = $obj->getItemIds();
            $defaultItemImage = DB::table('settings')->where('name', 'default-item-image')->first();
            if ($defaultItemImage)
                $defaultItemImage_path = asset($defaultItemImage->value);

            $totalCount = Item::where('status', 1)->count();

            return view('pages.category', compact('category', 'masterColors',
                'masterFabrics', 'items', 'wishListItems', 'patterns', 'styles', 'defaultItemImage_path','totalCount'));
        }else{
            abort(404);
        }
    }

    public function secondCategory($parent, $category){
         
        $parent_cat = str_replace('-', ' ', $parent); 
        $slugCheck = Category::where('slug', $parent)->first();
        $patterns = Category::where('slug', $parent)->first(); 
        $categories = Category::where('parent', '!=', 0)->get();
        $c = null;

        foreach ($categories as $cat) {
            if ($cat->slug == $category && $cat->parentCategory && $cat->parentCategory->slug == $parent) {
                $c = $cat;
                break;
            }
        }

        if ($c) {
            return $this->subCategoryPage($c,$slugCheck,null);
        }
        if($category== 'new'){
            $wishListItems = [];
            if( Auth::check() && Auth::user() && Auth::user()->id){
                $obj = new WishListItem();
                $wishListItems = $obj->getItemIds();
            }
            $defaultItemImage = DB::table('settings')->where('name','default-item-image')->first();
            if ($defaultItemImage){
                $defaultItemImage_path = asset($defaultItemImage->value);
            }
            $category = $slugCheck;
            $new = 1;
            $category->name="New";
            return view('pages.sub_category', compact('category','wishListItems','defaultItemImage_path','new','patterns','parent_cat'));
        }
        return redirect()->route('home');
    }
    public function thirdCategory($parent, $category, $third){ 
        // $parent_cat = str_replace('-', ' ', $parent); 
        $slugCheck = Category::where('slug', $parent)->first();
        $patterns = Category::where('slug', $parent)->first(); 
        $categories = Category::where('parent', '!=', 0)->get();
        $c = null;
        $third_cat = null;

        foreach ($categories as $cat) {
            if ($cat->slug == $category && $cat->parentCategory && $cat->parentCategory->slug == $parent) {
                $c = $cat;
                break;
            }
        }
        foreach ($categories as $cat) {
            if ($cat->slug == $third && $cat->parentCategory && $cat->parentCategory->slug == $category) {
                $third_cat = $cat;
                break;
            }
        }
 
        if ($third_cat) {
            return $this->subCategoryPage($c,$slugCheck,$third_cat);
        } 
        return redirect()->route('home');

    }

    public function subCategoryPage($category ,$slugCheck, $thirdcat ) {  
        $category->load('subCategories', 'parentCategory', 'lengths');
        $vendors = MetaVendor::where('verified', 1)
            ->where('active', 1)
            ->orderBy('company_name')->get();

        $bodySizes = BodySize::where('parent_category_id', $category->parentCategory->id)
            ->orderBy('name')
            ->get();

        $patterns = $slugCheck;

        $styles = Style::where('parent_category_id', $category->parentCategory->id)
            ->orderBy('name')
            ->get();

        $masterColors = MasterColor::orderBy('name')->get();
        $masterFabrics = MasterFabric::orderBy('name')->get();

        $items = [];

        foreach($category->subCategories as &$cat) {
            $count = Item::where('status', 1)->where('default_third_category', $cat->id)->count();
            $cat->totalItem = $count;
        }

        foreach($vendors as &$vendor) {
            $count = Item::where('status', 1)
                ->where('default_second_category', $category->id)->count();
            $vendor->totalItem = $count;
        }

        // Wishlist
        $wishListItems = [];
        if( Auth::check() && Auth::user() && Auth::user()->id){
            $obj = new WishListItem();
            $wishListItems = $obj->getItemIds();
        }

        $defaultItemImage = DB::table('settings')->where('name','default-item-image')->first();
        if ($defaultItemImage)
            $defaultItemImage_path = asset($defaultItemImage->value);

        $totalCount = Item::where('status', 1)->count();
        $banner = TopBanner::where('category_id',$slugCheck->id)->first();
        $febric = MasterFabric::all();
        $packs = pack::all();
        $categories = Category::where('parent', 0)->orderBy('sort')->get();
        $new = 0;
 
        return view('pages.sub_category', compact('thirdcat','category','categories','banner','packs','febric', 'vendors', 'masterColors', 'masterFabrics','items', 'wishListItems', 'bodySizes', 'patterns', 'styles', 'new','defaultItemImage_path','totalCount','slugCheck'));
    }

    public function newArrival(Request $request)
    {
        $today = Carbon::today();
        $query = Item::where('status', 1)->with('images.color')->where('created_at', '>', $today->subDays(60));

        if (isset($request->sort_by)) {
            if ($request->sort_by=='low_to_high') {
                $query->orderBy('price', 'asc');
            } elseif ($request->sort_by=='high_to_low') {
                $query->orderBy('price', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        $items = $query->paginate(40)->toArray();
        //get color details

        $selected_color = array();
        $i=0;
        foreach ($items['data'] as $item) {
            $id = $item['id'];
            $color_sql = "SELECT `colors`.* FROM `colors`  " .
                "INNER JOIN (SELECT color_id, sort FROM `item_images` WHERE color_id IN (SELECT `color_id` FROM `color_item` WHERE item_id = $id and available = 1) and item_id = $id GROUP BY(color_id)) AS `SelectedItem`" .
                "ON `colors`.id = `SelectedItem`.color_id ORDER BY `SelectedItem`.sort ASC";
            $available_item = DB::select($color_sql);
             $items['data'][$i]['color_images'] = $available_item;
             $i++;
        }
        // dd($items);
        // Default Image
        $defaultItemImage = DB::table('settings')->where('name', 'default-item-image')->first();
        if ($defaultItemImage)
            $defaultItemImage_path = asset($defaultItemImage->value);

        /*Notification Banner module*/
        $top_notification_banner_module = DB::table('top_banners')->where('page', '10')->get();
        if(count($top_notification_banner_module) == 0) {
            $top_notification_banner_module = [];
        }

        $category = (object) [
            'name' => 'New Arrival'
        ];
        $slug = 'new_in';
        return view('pages.category', compact('category', 'items', 'defaultItemImage_path','top_notification_banner_module','selected_color','slug'))->with('url', 'new_in');
    }

    public function bestSelling(Request $request)
    {
        $query = Item::where('items.status', 1);
        $query->with('images.color');
        $query->select(DB::raw('COUNT(order_items.total_qty) AS maxSale'), 'items.*');
        $query->join('order_items', 'items.id', '=', 'order_items.item_id');
        $query->join('orders', 'orders.id', '=', 'order_items.order_id');
        $query->groupBy('items.id');

        if (isset($request->sort_by)) {
            if ($request->sort_by=='low_to_high') {
                $query->orderBy('items.price', 'asc');
            } elseif ($request->sort_by=='high_to_low') {
                $query->orderBy('items.price', 'desc');
            }
        } else {
            $query->orderBy('items.created_at', 'desc');
        }
        $items = $query->paginate(40)->toArray();

        $selected_color = array();
        $i=0;
        foreach ($items['data'] as $item) {
            $id = $item['id'];
            $color_sql = "SELECT `colors`.* FROM `colors`  " .
                "INNER JOIN (SELECT color_id, sort FROM `item_images` WHERE color_id IN (SELECT `color_id` FROM `color_item` WHERE item_id = $id and available = 1) and item_id = $id GROUP BY(color_id)) AS `SelectedItem`" .
                "ON `colors`.id = `SelectedItem`.color_id ORDER BY `SelectedItem`.sort ASC";
            $available_item = DB::select($color_sql);
             $items['data'][$i]['color_images'] = $available_item;
             $i++;
        }

        // Default Image
        $defaultItemImage = DB::table('settings')->where('name', 'default-item-image')->first();
        if ($defaultItemImage)
            $defaultItemImage_path = asset($defaultItemImage->value);

        /*Notification Banner module*/
        $top_notification_banner_module = DB::table('top_banners')->where('page', '11')->get();
        if(count($top_notification_banner_module) == 0) {
            $top_notification_banner_module = [];
        }

        $category = (object) [
            'name' => 'Best Selling'
        ];
        $slug = 'new_in';
        return view('pages.category', compact('category', 'items', 'defaultItemImage_path','top_notification_banner_module','slug'))->with('url', 'best_selling');
    }
}
