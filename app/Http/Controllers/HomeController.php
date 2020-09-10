<?php

namespace App\Http\Controllers;

use App\Enumeration\Role;
use App\Enumeration\VendorImageType;
use App\Model\BodySize;
use App\Model\Category;
use App\Model\Color;
use App\Model\Item;
use App\Model\ItemCategory;
use App\Model\ItemFitSize;
use App\Model\ItemView;
use App\Model\ItemInv;
use App\Model\MasterColor;
use App\Model\MasterFabric;
use App\Model\MetaBuyer;
use App\Model\MetaVendor;
use App\Model\Pattern;
use App\Model\ItemImages;
use App\Model\Setting;
use App\Model\Style;
use App\Model\User;
use App\Model\VendorImage;
use App\Model\ProductDetails;
use App\Model\BulletTwo;
use App\Model\BulletThree;
use App\Model\BulletFour;
use App\Model\Pack;
use App\Model\Visitor;
use App\Model\WishListItem;
use App\Model\CartItem;
use App\Model\Meta;
use App\Model\Page;
use App\Enumeration\PageEnumeration;
use Carbon\Carbon;
use Illuminate\Http\Request;
//use Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DrewM\MailChimp\MailChimp;
use Spatie\Newsletter\Newsletter;
use Spatie\Newsletter\NewsletterListCollection;
use Vinkla\Instagram\Instagram;
use Request as HttpRequest;
use View;

class HomeController extends Controller
{

    public function index() {
        $top_notification = Setting::where('name', 'top_notification')->first();
        $today = Carbon::today();
        $newArrivalItems = Item::where('status', 1)
            ->orderBy('activated_at', 'desc')
            ->with('images')
            ->limit(10)
            ->get();
        $main_slider_mob = VendorImage::where('type', VendorImageType::$MOBILE_MAIN_BANNER)
            ->orderBy('sort')
            ->get();
        // Banners
        $HomeMainBanner = VendorImage::where('type', VendorImageType::$FRONT_PAGE_BANNER)
            ->where('status', 1)
            ->orderBy('sort')
            ->get();
        // Visitor
        $visitor_url = HttpRequest::ip();
        Visitor::create([
            'user_id' => (Auth::check() && Auth::user()->role == Role::$BUYER) ? Auth::user()->id : null,
            'url' => $visitor_url,
            'ip' => $visitor_url
        ]);
        //Home Page content
        $page = Page::where('page_id', PageEnumeration::$HOME_PAGE_CUSTOM_SECTION)->first();
         
        // Welcome Notification
        $welcome_msg = '';
        if(!isset($_COOKIE['welcome_popup_fame'])) {
            $setting = Setting::where('name', 'welcome_notification')->first();
            if ($setting && $setting->value != null)
                $welcome_msg = $setting->value;
            setcookie("welcome_popup_fame", 'sessionexists', time()+3600*24);
        }
        return view('pages.home', compact('top_notification' ,'newArrivalItems','HomeMainBanner', 'welcome_msg', 'page','main_slider_mob'));
    }





    public function CategoryPage($category){
        $categories = Category::where('parent', '=', 0)->get();

        $c = null;

        foreach ($categories as $cat) {
            if (changeSpecialChar($cat->name) == $category) {
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
            /*$items = Item::where('status', 1)
                ->where('default_third_category', $category->id)
                ->orderBy('activated_at', 'desc')
                ->with('vendor', 'images', 'colors')
                ->paginate(30);*/

            // Wishlist
            $obj = new WishListItem();
            $wishListItems = $obj->getItemIds();
            $defaultItemImage = DB::table('settings')->where('name', 'default-item-image')->first();
            if ($defaultItemImage)
                $defaultItemImage_path = asset($defaultItemImage->value);


            /*Notification Banner module*/
            $top_notification_banner_module = DB::table('top_banners')->where('category_id', $category->id)->get();

            if(count($top_notification_banner_module) == 0)
            {
                $top_notification_banner_module = [];
            }


            return view('pages.catalog_category', compact('category', 'masterColors',
                'masterFabrics', 'items', 'wishListItems', 'patterns', 'styles', 'defaultItemImage_path','top_notification_banner_module'));
            // ****************************************** //
        }else{
            abort(404);
        }
    }

    public function secondCategory($parent, $category){
        $categories = Category::where('parent', '!=', 0)->get();
        $c = null;
        foreach ($categories as $cat) {
            if (changeSpecialChar($cat->name) == $category && $cat->parentCategory && changeSpecialChar($cat->parentCategory->name) == $parent) {
                $c = $cat;
                break;
            }
        }

        if ($c) {
            return $this->subCategoryPage($c);
        }

        abort(404);
    }

    public function thirdCategory($parent, $second, $category){

        $categories = Category::where('parent', '!=', 0)
            ->get();

        foreach ($categories as $cat) {
            if (changeSpecialChar($cat->name) == $category) {
                $category = $cat;
                break;
            }
        }

        if ($category) {
            return $this->catalogPage($category);
        }

        abort(404);

    }

    public function getItemsSubCategory(Request $request) {  
        $new = $request->new;
        $query = Item::query();

        if ($request->secondCategory && $request->secondCategory != '')
            $query->where('default_second_category', $request->secondCategory);

        if (!empty($request->thirdcat )){  
            $query->where('default_third_category', $request->thirdcat);
        }

//      if ($request->vendors && sizeof($request->vendors) > 0)
//          $query->whereIn('vendor_meta_id', $request->vendors);

        if ($request->masterCategory )
            $query->where('default_parent_category', $request->masterCategory);

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
        if ($request->sorting){
            if ($request->sorting == '1') {
                $query->orderBy('sorting');
                $query->orderBy('activated_at', 'desc');
            } else if ($request->sorting == '2')
                $query->orderBy('price');
            else if ($request->sorting == '3')
                $query->orderBy('price', 'desc');
            else if ($request->sorting == '4')
                $query->orderBy('style_no','desc');
        }else{
            $query->orderBy('sorting','asc'); 
        }
        if($request->limit == 0) {
            $limit = 40;
        }else {
            $limit = $request->limit;
        }
        if($new == 1){
            $query->orderBy('activated_at','desc');
        }
        $items = $query->with('images.color', 'vendor', 'colors')->where('status',1)->limit($limit)->get();

        // $paginationView = $items->links('others.pagination');
        // $paginationView = trim(preg_replace('/\r\n/', ' ', $paginationView));


        // Wishlist
        $obj = new WishListItem();
        $wishListItems = $obj->getItemIds();

//      if(Auth::user()){
//          $blockedVendorIds = Auth::user()->blockedVendorIds();
//      }

        foreach($items as &$item) {
            // Price
            $price = '';
            $colorsImages = [];

            // if (Auth::check() && Auth::user()->role == Role::$BUYER) {
                if ($item->orig_price != null)
                    // $price .= '<del>$' . number_format($item->orig_price, 2, '.', '') . '</del> ';

                $price .= '$' . number_format($item->price, 2, '.', '');
            // }

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

            foreach($item->colors as $color) {
                foreach ($item->images as $image) {
                    if ($image->color_id == $color->id) {
                        $colorsImages[$color->name] = asset($image->thumbs_image_path);
                        break;
                    }
                }
            }

            $item->imagePath = $imagePath;
            $item->imagePath2 = $imagePath2;
            $item->detailsUrl = route('item_details_page', ['item' => $item->id, 'name' => changeSpecialChar($item->name)]);
//          $item->vendorUrl = route('vendor_or_parent_category', ['vendor' => changeSpecialChar($item->vendor->company_name)]);
            $item->colorsImages = $colorsImages;
            $item->video = ($item->video) ? asset($item->video) : null;

            $wishListButton = '';
            if (in_array($item->id, $wishListItems)) {
                $wishListButton = '<button class="btn btn-danger btn-sm btnRemoveWishList" data-id="'.$item->id.'"><i class="icon-heart"></i></button>';
            } else {
                $wishListButton = '<button class="btn btn-default btn-sm btnAddWishList" data-id="'.$item->id.'"><i class="icon-heart"></i></button>';
            }

            $item->wishListButton = $wishListButton;
            $inv = ItemInv::where('item_id',$item->id)->where('available_on','!=', null)->where('available_on','!=','null')->get();
            if(count($inv) > 0){
                $item->available_on = $inv[0]->available_on; 
            }
            // $item->price = $price;

            // Blocked Check
//          if (in_array($item->vendor_meta_id, $blockedVendorIds)) {
//              $item->imagePath = asset('images/blocked.jpg');
//              $item->vendor->company_name = '';
//              $item->vendorUrl = '';
//              $item->style_no = '';
//              $item->price = '';
//              $item->available_on = '';
//              $item->colors->splice(0);
//          }
        }

//      return ['items' => [], 'pagination' => 3];

        return ['items' => $items->toArray()];
        // return ['items' => $items->toArray(), 'pagination' => $paginationView];
    }

    public function getItemsCategory(Request $request) {

        $query = Item::query();

//      if ($request->secondCategory && $request->secondCategory != '')
//          $query->where('default_second_category', $request->secondCategory);

        if ($request->categories && sizeof($request->categories) > 0)
            $query->whereIn('default_parent_category', $request->categories);

//      if ($request->vendors && sizeof($request->vendors) > 0)
//          $query->whereIn('vendor_meta_id', $request->vendors);


        if ($request->masterColors && sizeof($request->masterColors) > 0) {
            $masterColors = $request->masterColors;

            $query->whereHas('colors', function ($query) use ($masterColors) {
                $query->whereIn('master_color_id', $masterColors);
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
        $query->orderBy('activated_at', 'desc');

        if ($request->sorting){
            if ($request->sorting == '1') {
                $query->orderBy('sorting');
                $query->orderBy('activated_at', 'desc');
            } else if ($request->sorting == '2')
                $query->orderBy('price');
            else if ($request->sorting == '3')
                $query->orderBy('price', 'desc');
            else if ($request->sorting == '4')
                $query->orderBy('style_no');
        }

        /*$query = Item::where('status', 1)
            ->whereIn('default_third_category', $request->categories)
            ->whereIn('vendor_meta_id', $request->vendors)
            ->whereHas('colors', function ($query) use($masterColors) {
                $query->whereIn('master_color_id', $masterColors);
            })
            ->with('images', 'vendor')
            ->query();*/

        // $items = $query->with('images', 'vendor', 'colors')->paginate(55);
        $items = $query->with('images.color', 'vendor', 'colors')->where('status',1)->paginate(40);

        $paginationView = $items->links('others.pagination');

        // Wishlist
        $obj = new WishListItem();
        $wishListItems = $obj->getItemIds();

        //if(Auth::user()){
        //$blockedVendorIds = Auth::user()->blockedVendorIds();
        //}

        foreach($items as &$item) {
            // Price
            $price = '';
            $colorsImages = [];

            if (Auth::check() && Auth::user()->role == Role::$BUYER) {
                if ($item->orig_price != null)
                    $price .= '<del>$' . number_format($item->orig_price, 2, '.', '') . '</del> ';

                $price .= '$' . number_format($item->price, 2, '.', '');
            }

            $colorsImages = [];

            foreach($item->colors as $color) {
                foreach ($item->images as $image) {
                    if ($image->color_id == $color->id) {
                        $colorsImages[$color->name] = asset($image->thumbs_image_path);
                        break;
                    }
                }
            }

            $item->colorsImages = $colorsImages;

            // Image
            $imagePath = '';
            $imagePath2 = '';

            $imagePath = asset('images/no-image.png');


            if (sizeof($item->images) > 0)
                $imagePath = asset($item->images[0]->list_image_path);

            if (sizeof($item->images) > 1)
                $imagePath2 = asset($item->images[1]->list_image_path);

            $item->price = '$'.sprintf('%0.2f', $item->price);

            $item->imagePath = $imagePath;
            $item->imagePath2 = $imagePath2;
            $item->detailsUrl = route('item_details_page', ['item' => $item->id, 'name' => changeSpecialChar($item->name)]);
            //$item->vendorUrl = route('vendor_or_parent_category', ['vendor' => changeSpecialChar($item->vendor->company_name)]);

            $wishListButton = '';
            if (in_array($item->id, $wishListItems)) {
                $wishListButton = '<button class="btn btn-danger btn-sm btnRemoveWishList" data-id="'.$item->id.'"><i class="icon-heart"></i></button>';
            } else {
                $wishListButton = '<button class="btn btn-default btn-sm btnAddWishList" data-id="'.$item->id.'"><i class="icon-heart"></i></button>';
            }

            $item->wishListButton = $wishListButton;
            $item->price = $price;
            $item->video = ($item->video) ? asset($item->video) : null;

            // Blocked Check
            //if (in_array($item->vendor_meta_id, $blockedVendorIds)) {
            //$item->imagePath = asset('images/blocked.jpg');
            //$item->vendor->company_name = '';
            //$item->vendorUrl = '';
            //$item->style_no = '';
            //$item->price = '';
            //$item->available_on = '';
            //$item->colors->splice(0);
            //}
        }

//return ['items' => [], 'pagination' => 3];
        $paginationView = trim(preg_replace('/\r\n/', ' ', $paginationView));
        return ['items' => $items->toArray(), 'pagination' => $paginationView];
    }

    public function get_items_category_load_ajax()
    {
       $query = Item::where('status', 1)->whereIn('default_parent_category',$_GET['categories_ids'])->with('images.color');
       if($_GET['sort_by'] == 'low_to_high')
       {
            $query->orderBy('price', 'asc');
       }
       if($_GET['sort_by'] == 'high_to_low')
       {
            $query->orderBy('price', 'desc');
       }

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

    public function get_items_sub_category_load_ajax()
    {
       $query = Item::whereIn('default_second_category',$_GET['categories_ids'])->with('images.color');
       if($_GET['sort_by'] == 'low_to_high')
       {
            $query->orderBy('price', 'asc');
       }
       if($_GET['sort_by'] == 'high_to_low')
       {
            $query->orderBy('price', 'desc');
       }

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
    public function category_single_page(Request $request, $itemSlug)
    {
        // Get category slug by category id
        $itemId = 0;
        $metavendor = MetaVendor::first();
        $slugCheck = Item::where('slug', $itemSlug)->first();
        $idtem=Item::where('slug', $itemSlug)->first();
        if(auth()->user()){
            Item::where('id',$idtem->id)->update(['view'=> $idtem->view + 1, 'v_created_at' =>Carbon::now() ]);
        }
        if($slugCheck){
            if ( $slugCheck != null ) {
                $itemId = $slugCheck->id;
                $default_parent_category=$slugCheck->default_parent_category;
                $default_second_category=$slugCheck->default_second_category;
                $default_third_category=$slugCheck->default_third_category;
            }

            $item = Item::with('images', 'pack', 'colors')->where('id', $itemId)->first();

            if($item){

                $sizes = explode("-", $item->pack->name);
                $second_category= '';
                $second_category_id = $item->default_second_category;
                if($second_category_id){
                    $second_category = Category::where('id', $second_category_id)->first();
                }

                $parent_category_id = $item->default_parent_category;
                $parent_category='';
                if($parent_category_id){
                    $parent_category = Category::where('id', $parent_category_id)->first();

                }

                $itemInPack = 0;

                for($i=1; $i <= sizeof($sizes); $i++) {
                    $var = 'pack'.$i;

                    if ($item->pack->$var != null)
                        $itemInPack += (int) $item->pack->$var;
                }

                // dd($item_color);
                $current_color = ItemImages::where('sort',1)->where('item_id',$itemId)->first();

                $admin_first_color_id = $current_color->color_id;
                $color_images = DB::table('item_images')
                    ->select('item_images.*')
                    ->orderBy('sort', 'asc')
                    ->where([
                        ['item_images.item_id',$itemId],
                         ['item_images.color_id',$admin_first_color_id]
                    ])
                    ->get();
                // Image
                $item->imagePathOne = asset($color_images[0]->compressed_image_path);

                //get recently viewed items

                $clientIP = request()->ip();

                $recentlyViewItems = [];
                if (Auth::check()) {

//                    ItemView::where('ip',$clientIP)->where('item_id', $item->id)->delete();
                    ItemView::where('user_id',Auth::user()->id)->where('item_id', $item->id)->delete();

                    ItemView::create([
                        'user_id' => Auth::user()->id,
                        'ip' => $clientIP,
                        'item_id' => $item->id,
                    ]);
                    $recentlyViewItems = DB::table('item_views')
                        ->join('items','item_views.item_id','=','items.id')
                        ->join('item_images', function ($join) {
                            $join->on('item_images.id', '=', DB::raw('(SELECT id FROM item_images WHERE item_images.item_id = item_views.item_id ORDER BY sort ASC LIMIT 1)'));
                        })
                        ->select('items.id','items.style_no','items.price','items.orig_price','items.slug','items.name','item_images.list_image_path')
                        ->where('item_views.item_id', '!=', $item->id)
                        ->where('item_views.user_id', '=', Auth::user()->id)
//                        ->where('item_views.ip', '=', $clientIP)
                        ->where('items.status', '=', 1)
                        ->orderBy('item_views.id', 'desc')
                        ->limit(5)
                        ->get();
//                      dd($recentlyViewItems);

                    $recentlyViewItems = $recentlyViewItems != null ? json_decode($recentlyViewItems) : [];
                    if(count($recentlyViewItems) == 4 ){
                    $recentlyViewItems_half = array_chunk($recentlyViewItems, ceil(count($recentlyViewItems)/2));
                    }else{
                        $recentlyViewItems_half = '';
                    }
                }else{
                    $recentlyViewItems_half = '';
                }

                //get similar random item from database

                if($default_parent_category != null && $default_second_category == null && $default_third_category == null){
                    $randoms = Item::with('images', 'colors', 'pack')->where('status', 1)->where('default_parent_category', $default_parent_category)->inRandomOrder()->limit(5)->get();
                }elseif($default_second_category !=null && $default_third_category == null){
                    $randoms = Item::with('images', 'colors', 'pack')->where('status', 1)->where('default_second_category', $default_second_category)->inRandomOrder()->limit(5)->get();
                }else{
                    $randoms = Item::with('images', 'colors', 'pack')->where('status', 1)->where('default_third_category', $default_third_category)->inRandomOrder()->limit(5)->get();
                };

                $relatedItem = $randoms != null ? json_decode($randoms) : [];

                $defaultItemImage_path = DB::table('settings')->where('name', 'default-item-image')->first();
                if ($defaultItemImage_path)
                    $defaultItemImage_path  = asset($defaultItemImage_path->value);

                $itemInventories = ItemInv::where('item_id', $itemId)->orderBy('id')->get();
                $item_details = DB::select("SELECT DISTINCT color_item.color_id, color_item.item_id,item_images.sort,item_inv.color_name as color_name,item_inv.qty,item_inv.available_on,
                                                    item_images.list_image_path,item_inv.id as inv_id
                                    FROM `color_item`
                                    LEFT JOIN item_images ON color_item.item_id = item_images.item_id AND color_item.color_id = item_images.color_id
                                    LEFT JOIN item_inv ON color_item.item_id = item_inv.item_id AND color_item.color_id = item_inv.color_id
                                    JOIN colors ON colors.id = color_item.color_id
                                    WHERE color_item.item_id = '$itemId' AND color_item.available = '1'
                                    ORDER BY item_images.sort ASC");
                foreach ($item_details as $single_item) {
                    if($single_item->sort == null) {
                        array_shift($item_details);
                        array_push($item_details, $single_item );
                    }

                }
                $new_item_details = array();
                $used = array();

                foreach ( $item_details AS $key => $val ) {
                    if ( !in_array($val->color_id, $used) ) {
                        $used[] = $val->color_id;
                        $new_item_details[] = $val;
                    }
                }
                $ItemFitSize = ItemFitSize::first();
                $sizeguide = Page::where('page_id', PageEnumeration::$SIZE_GUIDE)->first();
                return view('pages.single_product_details', compact('item','admin_first_color_id','ItemFitSize', 'metavendor','second_category','recentlyViewItems','new_item_details',
                    'color_images','relatedItem','parent_category','current_color', 'sizes','sizeguide', 'itemInPack', 'defaultItemImage_path' ,'itemInventories'));
            }else{
                abort(404);
            }
        }else{
            abort(404);
        }
    }



    public function get_items_info(Request $request){
        $item =  Item::where('id', $request->itemid)->first();
        $image = DB::table('item_images')
            ->join('colors','item_images.color_id','=','colors.id')
            ->select('item_images.*','colors.name')
            ->where([
                ['item_images.item_id',$request->itemid],
                ['item_images.color_id',$request->colorid]
            ])
            ->orderBy('sort','asc')
            ->get();

        if($image->count() == 0 ){
            $color_name = Color::where('id',$request->colorid)->first();
            $item = Item::where('id',$request->itemid)->with('images')->first();
            $image[0] = [
                'color_id' => $color_name->id,
                'id' => $item->images[0]->id,
                'compressed_image_path' => $item->images[0]->compressed_image_path,
                'image_path' => $item->images[0]->compressed_image_path,
                'item_id'=> $item->id,
                'list_image_path' => $item->images[0]->list_image_path,
                'name' => $color_name->name,
                'status' => "1",
                'thumbs_image_path' => $item->images[0]->thumbs_image_path
            ];

        }
        $available_color = DB::table('color_item')
            ->join('colors', 'color_item.color_id', '=', 'colors.id')
            ->where('color_item.available',1)
            ->where('color_item.item_id',$request->itemid)
            ->get();
        $itemInventories = ItemInv::where('item_id', $request->itemid)->where('color_id',$request->colorid)->first();
        $defaultItemImage_path = DB::table('settings')->where('name', 'default-item-image')->first();
        

        if( Auth::check() && Auth::user() && Auth::user()->id){
            $defaultItemImage_path = null;
        }else{ 
            if ($defaultItemImage_path){ 
                $defaultItemImage_path  = asset($defaultItemImage_path->value);
            }
        }
        return $data = [
            'item'=> $item,
            'image'=> $image,
            'available_color' => $available_color,
            'inventory' => $itemInventories,
            'defaultItemImage_path' => $defaultItemImage_path,
        ];
    }

     //method for get matched image
    public function get_matched_image(Request $request){
        $item_id = $request->input('itemId');
        $color_id = $request->input('colorId');
        $matched_images = DB::table('item_images')
                        ->join('colors','item_images.color_id','=','colors.id')
                        ->select('item_images.*','colors.name')
                        ->where([
                            ['item_images.item_id',$item_id],
                            ['item_images.color_id',$color_id]
                        ])
                        ->orderBy('sort')
                        ->get();
            return  $matched_images;

        // if(sizeof($matched_images)>0){
        //     return $matched_images;
        // }else{
        //     $matched_images = DB::table('colors')
        //                       ->select('colors.*')
        //                       ->where('colors.id',$color_id)
        //                       ->get();
        //     return  $matched_images;
        // }
    }

    //method for get matched preorder image
    public function get_matched_preorder_date(Request $request){
        $item_id = $request->input('itemId');
        $color_id = $request->input('colorId');

        $matched_images = DB::table('item_inv')
                        ->select('available_on','qty')
                        ->where([
                            ['item_inv.item_id',$item_id],
                            ['item_inv.color_id',$color_id]
                        ])
                        ->get();
        return $matched_images;
    }

    public function storeRating(Request $request)
    {
        $title = $request->title;
        $comment = $request->comment;
        $rate = is_null($request->rate_value)? 0 : $request->rate_value;
        $product_id = $request->product_id;

        // Check this customer rate it before it or not
        $get_customer_info = DB::table('product_reviews')
                                ->where('customer_id', Auth::user()->id)
                                ->where('product_id', $product_id)
                                ->get()
                                ->toArray();

        $data = [
            'title' => $title,
            'comment' => $comment,
            'rating' => $rate,
            'product_id' => $product_id,
            'customer_id' => Auth::user()->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];

        if ( count($get_customer_info) > 0 ) {
            // Update rating
            DB::table('product_reviews')
                ->where('product_id', $product_id)
                ->update($data);
        }
        else {
            // Insert new rating
            DB::table('product_reviews')->insert($data);
        }

        return redirect()->back();
    }

    public function subCategoryPage($category) {

        $category->load('subCategories', 'parentCategory', 'lengths');

        $vendors = MetaVendor::where('verified', 1)
            ->where('active', 1)
            ->orderBy('company_name')->get();

        $bodySizes = BodySize::where('parent_category_id', $category->parentCategory->id)
            ->orderBy('name')
            ->get();

        $patterns = Pattern::where('parent_category_id', $category->parentCategory->id)
            ->orderBy('name')
            ->get();

        $styles = Style::where('parent_category_id', $category->parentCategory->id)
            ->orderBy('name')
            ->get();

        $masterColors = MasterColor::orderBy('name')->get();
        $masterFabrics = MasterFabric::orderBy('name')->get();

        $items = [];
        /* $items = Item::where('status', 1)
            ->where('default_second_category', $category->id)
            ->orderBy('activated_at', 'desc')
            ->with('images', 'colors')
            ->paginate(54);
        */

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

        return view('pages.sub_category', compact('category', 'vendors', 'masterColors', 'masterFabrics',
            'items', 'wishListItems', 'bodySizes', 'patterns', 'styles', 'defaultItemImage_path'));
    }

    public function catalogPage($category) {
        $category->load('parentCategory');
        $parentCategory = $category->parentCategory;
        $parentCategory->load('subCategories', 'lengths', 'parentCategory');


        $patterns = Pattern::where('parent_category_id', $parentCategory->parentCategory->id)
            ->orderBy('name')
            ->get();

        $styles = Style::where('parent_category_id', $parentCategory->parentCategory->id)
            ->orderBy('name')
            ->get();

        $masterColors = MasterColor::orderBy('name')->get();
        $masterFabrics = MasterFabric::orderBy('name')->get();

        $items = [];
        /*$items = Item::where('status', 1)
            ->where('default_third_category', $category->id)
            ->orderBy('activated_at', 'desc')
            ->with('vendor', 'images', 'colors')
            ->paginate(30);*/

        // Wishlist
        //$obj = new WishListItem();
        //$wishListItems = $obj->getItemIds();


        $wishListItems = [];
        if( Auth::check() && Auth::user() && Auth::user()->id){
            $obj = new WishListItem();
            $wishListItems = $obj->getItemIds();
        }

        return view('pages.catalog', compact('category', 'parentCategory', 'masterColors',
            'masterFabrics', 'items', 'wishListItems', 'patterns', 'styles'));
    }

    public function bestSellerPage(Request $request){

        // Best Selling Items
        $sql = "SELECT items.id, t.count
                FROM items
                LEFT JOIN (SELECT item_id, SUM(total_qty) count FROM order_items
                JOIN orders ON orders.id = order_items.order_id WHERE orders.status != 1 GROUP BY item_id) t ON items.id = t.item_id
                WHERE items.deleted_at IS NULL AND items.status = 1
                ORDER BY count DESC
                LIMIT 60";


        $bestItems = DB::select($sql);

        $bestItemIds = [];
        foreach ($bestItems as $item)
            $bestItemIds[] = $item->id;



        $bestItems = [];
        if (sizeof($bestItemIds) > 0) {
            $bestItems = Item::whereIn('id', $bestItemIds)
                ->with('images', 'vendor', 'colors')
                ->orderByRaw(\DB::raw("FIELD(id, " . implode(',', $bestItemIds) . " )"))
                ->get();
        }

        //dd($bestItems->toArray());
        $categories = Category::where('parent', '=', 0)->get();
        $activeItemIds = Item::where('status', 1)->pluck('id')->toArray();

        foreach($categories as &$category) {
            $category->count = ItemCategory::whereIn('item_id', $activeItemIds)
                ->where('default_parent_category', $category->id)
                ->distinct('item_id')
                ->count();
        }

        // Wishlist
        $obj = new WishListItem();
        $wishListItems = $obj->getItemIds();

        return view('pages.best_seller', compact('bestItems', 'categories', 'wishListItems'));
    }

    public function searchPage(Request $request) {
        $categories = Category::where('parent', 0)
            ->orderBy('sort')
            ->get();

        foreach ($categories as &$category) {
            $categoryCountQuery = Item::query();
            $categoryCountQuery->where('status', 1)->where('default_parent_category', $category->id);

            $category->count = $categoryCountQuery->count();
        }

        $default_categories = $categories;
        $febric = MasterFabric::all();
        $packs = pack::all();
        // Master Colors
        $masterColors = MasterColor::orderBy('name')->get();

        // Wishlist
        $obj = new WishListItem();
        $wishListItems = $obj->getItemIds();


        if (Auth::check() && Auth::user()->role == Role::$BUYER) {
            $items = Item::where('status', 1)
                ->where(function ($query) use ($request) {
                    $query->where('style_no', 'like', '%' . $request->s . '%')
                        ->orWhere('name', 'like', '%' . $request->s . '%')
                        ->orWhere('fabric', 'like', '%' . $request->s . '%')
                        ->orWhere('description', 'like', '%' . $request->s . '%');
                })
                // ->with('images')->paginate(30);
                ->with('images.color')->orderBy('sorting','asc')->paginate(40);
        } else {
            $items = Item::where('status', 1)
                ->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->s . '%')
                        ->orWhere('style_no', 'like', '%' . $request->s . '%')
                        ->orWhere('fabric', 'like', '%' . $request->s . '%')
                        ->orWhere('description', 'like', '%' . $request->s . '%');
                })
                ->with('images.color')->orderBy('sorting','asc')->paginate(40);
        }

        $i=0;
        foreach($items as $item){
           $id = $item->id;
           //available color
            $available_color = DB::table('color_item')
                                   ->select('color_item.*')
                                   ->where('color_item.item_id','=',$id)
                                   ->where('color_item.available','=',1)
                                   ->get();
            $items[$i]['available_color'] = $available_color;
            $i++;
        }
        $wishlistItem=[];
        if (Auth::user()) {
            $wishlistItem = WishListItem::where('user_id', Auth::user()->id)->get();
        }
        $wishArr = [];
        for($i = 0 ; $i < count($wishlistItem); $i++){
            $wishArr[$i] = $wishlistItem[$i]['item_id'];
        }


        $defaultItemImage = DB::table('settings')->where('name', 'default-item-image')->first();
        if ($defaultItemImage)
            $defaultItemImage_path = asset($defaultItemImage->value);


        return view('pages.search', compact('items','febric','packs', 'categories',  'default_categories', 'masterColors',  'defaultItemImage_path','wishArr'));
    }

    public function searchAjax(Request $request)
    {
        $query = Item::query();
         if ($request->secondCategory && $request->secondCategory != '')
            $query->where('default_second_category', $request->secondCategory);

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

        //master Size filter

        if ($request->masterSizes && sizeof($request->masterSizes) > 0) {
            $masterSizes = $request->masterSizes;
            $query->whereHas('pack_filter', function ($query) use ($masterSizes) {
                $query->whereIn('id', $masterSizes);
            });
        }

        if ($request->s) {
//            $query->where('name', 'like', '%' . $request->s . '%')
//                        ->orWhere('style_no', 'like', '%' . $request->s . '%')
//                        ->orWhere('fabric', 'like', '%' . $request->s . '%')
//                        ->orWhere('description', 'like', '%' . $request->s . '%');

            $query->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->s . '%');
                $query->where('status', 1);
            })->orWhere(function ($query) use ($request) {
                $query->where('style_no', 'like', '%' . $request->s . '%');
                $query->where('status', 1);
            })->orWhere(function ($query) use ($request) {
                $query->where('fabric', 'like', '%' . $request->s . '%');
                $query->where('status', 1);
            })->orWhere(function ($query) use ($request) {
                $query->where('description', 'like', '%' . $request->s . '%');
                $query->where('status', 1);
            });

        }

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

        $items = $query->with('images', 'vendor', 'colors')->where('status',1)->limit($limit)->get();

        // $paginationView = $items->onEachSide(1)->links('others.pagination');
        // $paginationView = trim(preg_replace('/\r\n/', ' ', $paginationView));

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
                $imagePath2 = $defaultItemImage_path;
                $imagePath = $defaultItemImage_path;
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
        }

        return ['items' => $items->toArray()];
        // return ['items' => $items->toArray(), 'pagination' => $paginationView];
    }

    public function add_to_mailchimp(Request $request){

        if(auth()->user()){
            $mailChimp = new MailChimp(env('MAILCHIMP_APIKEY'));

            $config['defaultListName'] = env('MAILCHIMP_EMAIL_LIST');
            $config['lists'] = [
                env('MAILCHIMP_EMAIL_LIST') => [
                    'id' => env('MAILCHIMP_LIST_ID'),
                ],
            ];
            $list = NewsletterListCollection::createFromConfig($config);

            $NewsLetter = new Newsletter($mailChimp , $list);

            if($request->email) {
                $NewsLetter->subscribe($request->email);
                $signup_email = User::where('email', $request->email)->first();

                if($signup_email){
                    $buyers = MetaBuyer::where('user_id', $signup_email->id)->first();
                    $buyers->mailing_list = 1;
                    $buyers->save();
                }
            $metaBuyer = DB::table('meta_buyers')->where('user_id', auth()->user()->id)->first();

            if($metaBuyer->mailing_list == 0){
                DB::table('meta_buyers')
                    ->where('user_id', auth()->user()->id)
                    ->update(['mailing_list' => 1]);
            }
            }

            return 'added';
        }
        else{
            $mailChimp = new MailChimp(env('MAILCHIMP_APIKEY'));

            $config['defaultListName'] = env('MAILCHIMP_EMAIL_LIST');
            $config['lists'] = [
                env('MAILCHIMP_EMAIL_LIST') => [
                    'id' => env('MAILCHIMP_LIST_ID'),
                ],
            ];
            $list = NewsletterListCollection::createFromConfig($config);

            $NewsLetter = new Newsletter($mailChimp , $list);

            if($request->email) {
                $NewsLetter->subscribe($request->email);
                $signup_email = User::where('email', $request->email)->first();

                if($signup_email){
                    $buyers = MetaBuyer::where('user_id', $signup_email->id)->first();
                    $buyers->mailing_list = 1;
                    $buyers->save();
                }
            }
            return 'added';
        }
    }

    public function itemDetailsStatic(){
        return view('pages.item_details');
    }
}
