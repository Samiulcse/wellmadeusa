<?php

namespace App\Http\Controllers\Admin;

use App\Enumeration\PageEnumeration;
use App\Enumeration\SliderType;
use App\Enumeration\VendorImageType;
use App\Model\Category;
use App\Model\Item;
use App\Model\Setting;
use GuzzleHttp\Client;
use App\Model\SliderItem;
use App\Model\TopBanner;
use App\Model\Banners;
use App\Model\Meta;
use App\Model\MetaVendor;
use App\Model\VendorImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use Uuid;
use File;
// use DB;
use Carbon\Carbon;
use ImageOptimizer;
use Illuminate\Support\Facades\DB;
class BannerController extends Controller
{
    public function index(Request $request) {
        $parameters = [];
        $appends = array();

        if ($request->type){
            $parameters [] = array('type', '=', $request->type);
            $appends['type'] = $request->type;
        }

        if ($request->status){
            $status = ($request->status == '2') ? 0 : 1;
            $parameters [] = array('status', '=', $status);
            $appends['status'] = $request->status;
        }

        $images = VendorImage::where($parameters)
                    ->whereIn('type', [VendorImageType::$BIDDING_BIG_BANNER, VendorImageType::$BIDDING_SMALL_BANNER, VendorImageType::$MOBILE_MAIN_BANNER,
                    VendorImageType::$LOGO, VendorImageType::$SMALL_AD_BANNER, VendorImageType::$HOME_PAGE_BANNER])
                    ->paginate(10);


        $white = DB::table('settings')->where('name', 'logo-white')->where('status',1)->first();
        $black = DB::table('settings')->where('name', 'logo-black')->where('status',1)->first();
        $defaultItemImage = DB::table('settings')->where('name', 'default-item-image')->where('status',1)->first();

        return view('admin.dashboard.marketing_tools.banner.index', compact('images', 'appends', 'white', 'black', 'defaultItemImage'))->with('page_title', 'Banner Manager');
    }

    public function addPost(Request $request) {
        $request->validate([
            'logo' => 'nullable|mimes:jpeg,jpg,png,svg',
            'home_page_banner' => 'nullable|mimes:jpeg,jpg,png|dimensions:width=1713,height=441',
            'small_ad_banner' => 'nullable|mimes:jpeg,jpg,png|dimensions:width=448,height=206',
            'mobile_main_banner' => 'nullable|mimes:jpeg,jpg,png|dimensions:width=593,height=400',
            'bidding_big_banner' => 'nullable|mimes:jpeg,jpg,png|dimensions:width=1400,height=400',
            'bidding_small_banner' => 'nullable|mimes:jpeg,jpg,png|dimensions:width=376,height=270',
        ]);

        if ($request->logo) {
            $file = $request->file('logo');
            $this->uploadFile($file, VendorImageType::$LOGO);
        }

        if ($request->home_page_banner) {
            $file = $request->file('home_page_banner');
            $this->uploadFile($file, VendorImageType::$HOME_PAGE_BANNER);
        }

        if ($request->small_ad_banner) {
            $file = $request->file('small_ad_banner');
            $this->uploadFile($file, VendorImageType::$SMALL_AD_BANNER);
        }

        if ($request->mobile_main_banner) {
            $file = $request->file('mobile_main_banner');
            $this->uploadFile($file, VendorImageType::$MOBILE_MAIN_BANNER);
        }

        if ($request->bidding_big_banner) {
            $file = $request->file('bidding_big_banner');
            $this->uploadFile($file, VendorImageType::$BIDDING_BIG_BANNER);
        }

        if ($request->bidding_small_banner) {
            $file = $request->file('bidding_small_banner');
            $this->uploadFile($file, VendorImageType::$BIDDING_SMALL_BANNER);
        }

        return redirect()->back();
    }

    public function delete(Request $request) {
        $image = VendorImage::where('id', $request->id)->first();
        File::delete(public_path($image->image_path));
        $image->delete();
    }

    public function active(Request $request) {
        VendorImage::where('type', $request->type)->update(['status' => 0]);
        VendorImage::where('id', $request->id)->update(['status' => 1]);
    }

    public function uploadFile($file, $type) {
        $filename = Uuid::generate()->string;
        $ext = $file->getClientOriginalExtension();

        $destinationPath = '/images/banner';
        $file->move(public_path($destinationPath), $filename.".".$ext);
        $imagePath = $destinationPath."/".$filename.".".$ext;

        VendorImage::create([
            'type' => $type,
            'image_path' => $imagePath,
            'status' => 0
        ]);
    }

    public function bannerItems() {
        $items = Item::where('status', 1)
            ->get();

        $mainSliderItems = SliderItem::where('type', SliderType::$MAIN_SLIDER)
            ->orderBy('sort')
            ->with('item')
            ->get();

        $categoryTopSliderItems = SliderItem::where('type', SliderType::$CATEGORY_TOP_SLIDER)
            ->orderBy('sort')
            ->with('item')
            ->get();

        $categorySecondSliderItems = SliderItem::where('type', SliderType::$CATEGORY_SECOND_SLIDER)
            ->orderBy('sort')
            ->with('item')
            ->get();

        $newTopSliderItems = SliderItem::where('type', SliderType::$NEW_ARRIVAL_TOP_SLIDER)
            ->orderBy('sort')
            ->with('item')
            ->get();

        $newSecondSliderItems = SliderItem::where('type', SliderType::$NEW_ARRIVAL_SECOND_SLIDER)
            ->orderBy('sort')
            ->with('item')
            ->get();

        return view('admin.dashboard.marketing_tools.banner_items.index',
            compact('items', 'mainSliderItems', 'categoryTopSliderItems', 'categorySecondSliderItems', 'newTopSliderItems', 'newSecondSliderItems'))
            ->with('page_title', 'Banner Items');
    }

    public function front_page_recommend_banner_add(Request $request) {
        $item = Item::where('id', $request->item)->with('images')->first();
        $img_path=$item->images[0]->compressed_image_path;
        $slug =$item->slug;

        $sort = VendorImage::where('type', VendorImageType::$FRONT_PAGE_RECOMMEND_BANNER)->max('sort');

        if ($sort == null || $sort == '')
            $sort = 0;

        $sort++; 
        $recommend_item = VendorImage::where('type', VendorImageType::$FRONT_PAGE_RECOMMEND_BANNER)
            ->where('item_id',$request->item)
            ->first();
        $count = VendorImage::where('type', VendorImageType::$FRONT_PAGE_RECOMMEND_BANNER)->where('cat_id',$item->default_parent_category)->count();
        if ($recommend_item){
            return redirect()->back()->with('message', 'Already Added!.');
        }else if($count >= 5){
            return redirect()->back()->with('message', 'Max 5 items allow for same category.');
        }else{
            VendorImage::create([
                'type' => VendorImageType::$FRONT_PAGE_RECOMMEND_BANNER,
                'image_path' => $img_path,
                'item_id' => $item->id,
                'slug' => $slug,
                'cat_id' => $item->default_parent_category,
//                'title' => $item->name,
//                'discription' => $item->description,
                'status' => 1,
                'sort' => $sort
            ]);
            return redirect()->back()->with('message', 'Successfully Added!.');
        }
    }

    public function top_front_slider_item_delete(Request $request) {
        $image = VendorImage::where('id', $request->id)->delete(); 
    }

    public function frontRecommendBanner(Request $request) { 
        $activeItemsQuery = Item::query();
        
        $activeItemsQuery->where('status', 1)->with('itemcategory', 'images');

        // Search
        if (isset($request->text) && $request->text != '') {
            $activeItemsQuery->where(function($q) use ($request){
                if (isset($request->style) && $request->style == '1') {
                    $q->where('style_no', 'like', '%' . $request->text . '%');
                }

                if (isset($request->des) && $request->des == '1') {
                        $q->orWhere('description', 'like', '%' . $request->text . '%');
                }

                if (isset($request->name) && $request->name == '1') {
                        $q->orWhere('name', 'like', '%' . $request->text . '%');
                }
            }); 

        }

        // Active Items Order
        if (isset($request->s1) && $request->s1 != '') {
            if ($request->s1 == '4')
                $activeItemsQuery->orderBy('price');
            else if ($request->s1 == '1')
                $activeItemsQuery->orderBy('updated_at', 'desc');
            else if ($request->s1 == '2')
                $activeItemsQuery->orderBy('created_at', 'desc');
            else if ($request->s1 == '3')
                $activeItemsQuery->orderBy('activated_at', 'desc');
            else if ($request->s1 == '5')
                $activeItemsQuery->orderBy('price', 'desc');
            else if ($request->s1 == '6')
                $activeItemsQuery->orderBy('style_no');
            else if ($request->s1 == '0') {
                $activeItemsQuery->orderBy('sorting');
                $activeItemsQuery->orderBy('activated_at', 'desc');
            }
        } else {
            $activeItemsQuery->orderBy('sorting');
            $activeItemsQuery->orderBy('activated_at', 'desc');
        }

        $activeItems = $activeItemsQuery->paginate(12, ['*'], 'p1');

        // Inactive Items
        $inactiveItemsQuery = Item::query();
        $inactiveItemsQuery->where('status', 0)
            ->with('category', 'images');

        // Search
        if (isset($request->text) && $request->text != '') {
            if (isset($request->style) && $request->style == '1')
                $inactiveItemsQuery->where('style_no', 'like', '%' . $request->text . '%');

            if (isset($request->des) && $request->des == '1')
                $inactiveItemsQuery->where('description', 'like', '%' . $request->text . '%');

            if (isset($request->name) && $request->name == '1')
                $inactiveItemsQuery->where('name', 'like', '%' . $request->text . '%');
        }
 

        $appends = [
            'p1' => $activeItems->currentPage(), 
        ];

        foreach ($request->all() as $key => $value) {
            if ($key != 'p1')
                $appends[$key] = ($value == null) ? '' : $value;
        }

        $vendor = MetaVendor::where('id', 1)->first();

        // Default Categories
        $url = config('custom.sp_url');
        $client = new Client();
        $res = $client->get($url.'api/categories');

        $defaultCategories = json_decode($res->getBody()->getContents());

        // Vendor Categories
        $vendorCategories = [];

        $client = new Client();
        $res = $client->post($url.'api/vendor/categories', [
            'form_params' => [
                'username' => $vendor->sp_vendor,
                'password' => $vendor->sp_password,
            ]
        ]);

        $res = json_decode($res->getBody()->getContents());

        if ($res->success) {
            $vendorCategories = $res->items;
        } 
        $category = Category::where('parent',0)->select('id','name')->get();
        $images = []; 
        $i=0;
        foreach($category as $cat){
           $images[$i]['category'] = $cat;
           $images[$i]['items'] = VendorImage::where('type', VendorImageType::$FRONT_PAGE_RECOMMEND_BANNER)->where('cat_id',$cat->id)->with('item','item_image')->orderBy('cat_id')->get();
           $i++;
        }
           
        return view('admin.dashboard.marketing_tools.front_page_banner.recommend_banner', compact( 'appends','defaultCategories', 'vendorCategories','activeItems',  'images' ))->with('page_title', 'Recommend Item');
    }

    public function BannerAllSearchItem(Request $request) {
        $activeItemsQuery = Item::query();
        
        $activeItemsQuery->where('status', 1)->with('itemcategory', 'images');

        // Search
        if (isset($request->text) && $request->text != '') {
            $activeItemsQuery->where(function($q) use ($request){
                if (isset($request->style) && $request->style == '1') {
                    $q->where('style_no', 'like', '%' . $request->text . '%');
                }

                if (isset($request->des) && $request->des == '1') {
                        $q->orWhere('description', 'like', '%' . $request->text . '%');
                }

                if (isset($request->name) && $request->name == '1') {
                        $q->orWhere('name', 'like', '%' . $request->text . '%');
                }
            }); 

        }

        // Active Items Order
        if (isset($request->s1) && $request->s1 != '') {
            if ($request->s1 == '4')
                $activeItemsQuery->orderBy('price');
            else if ($request->s1 == '1')
                $activeItemsQuery->orderBy('updated_at', 'desc');
            else if ($request->s1 == '2')
                $activeItemsQuery->orderBy('created_at', 'desc');
            else if ($request->s1 == '3')
                $activeItemsQuery->orderBy('activated_at', 'desc');
            else if ($request->s1 == '5')
                $activeItemsQuery->orderBy('price', 'desc');
            else if ($request->s1 == '6')
                $activeItemsQuery->orderBy('style_no');
            else if ($request->s1 == '0') {
                $activeItemsQuery->orderBy('sorting');
                $activeItemsQuery->orderBy('activated_at', 'desc');
            }
        } else {
            $activeItemsQuery->orderBy('sorting');
            $activeItemsQuery->orderBy('activated_at', 'desc');
        }

        $activeItems = $activeItemsQuery->paginate(10, ['*'], 'p1');

        // Inactive Items
        $inactiveItemsQuery = Item::query();
        $inactiveItemsQuery->where('status', 0)
            ->with('category', 'images');

        // Search
        if (isset($request->text) && $request->text != '') {
            if (isset($request->style) && $request->style == '1')
                $inactiveItemsQuery->where('style_no', 'like', '%' . $request->text . '%');

            if (isset($request->des) && $request->des == '1')
                $inactiveItemsQuery->where('description', 'like', '%' . $request->text . '%');

            if (isset($request->name) && $request->name == '1')
                $inactiveItemsQuery->where('name', 'like', '%' . $request->text . '%');
        }

        // Inactive order
        if (isset($request->s2) && $request->s2 != '') {
            if ($request->s2 == '4')
                $inactiveItemsQuery->orderBy('price');
            else if ($request->s2 == '1')
                $inactiveItemsQuery->orderBy('updated_at', 'desc');
            else if ($request->s2 == '2')
                $inactiveItemsQuery->orderBy('created_at', 'desc');
            else if ($request->s2 == '3')
                $inactiveItemsQuery->orderBy('activated_at', 'desc');
            else if ($request->s2 == '5')
                $inactiveItemsQuery->orderBy('price', 'desc');
            else if ($request->s2 == '6')
                $inactiveItemsQuery->orderBy('style_no');
            else if ($request->s2 == '0') {
                $inactiveItemsQuery->orderBy('sorting');
                $inactiveItemsQuery->orderBy('activated_at', 'desc');
            }
        } else {
            $inactiveItemsQuery->orderBy('sorting');
            $inactiveItemsQuery->orderBy('created_at', 'desc');
        }

         

        $appends = [
            'p1' => $activeItems->currentPage(), 
        ];

        foreach ($request->all() as $key => $value) {
            if ($key != 'p1' && $key != 'p2')
                $appends[$key] = ($value == null) ? '' : $value;
        }

        $vendor = MetaVendor::where('id', 1)->first();

        // Default Categories
        $url = config('custom.sp_url');
        $client = new Client();
        $res = $client->get($url.'api/categories');

        $defaultCategories = json_decode($res->getBody()->getContents());

        // Vendor Categories
        $vendorCategories = [];

        $client = new Client();
        $res = $client->post($url.'api/vendor/categories', [
            'form_params' => [
                'username' => $vendor->sp_vendor,
                'password' => $vendor->sp_password,
            ]
        ]);

        $res = json_decode($res->getBody()->getContents());

        if ($res->success) {
            $vendorCategories = $res->items;
        }
        $images = VendorImage::where('type', VendorImageType::$FRONT_PAGE_RECOMMEND_BANNER)
            ->orderBy('sort')
            ->get(); 
        return view('admin.dashboard.marketing_tools.front_page_banner.recommend_banner', compact( 'activeItems','images',  'appends',
            'vendor', 'defaultCategories', 'vendorCategories'))
            ->with('page_title', 'Recommend Item');
    }


    public function bannerItemAdd(Request $request) {
        $query = SliderItem::query();

        if ($request->type == SliderType::$MAIN_SLIDER) {
            $query->where('type', SliderType::$MAIN_SLIDER);
            $count = $query->count();

            if ($count >= 8)
                return response()->json(['success' => false, 'message' => 'Already added 8 items']);
            else {
                $item = SliderItem::where([
                    ['item_id', $request->id],
                    ['type', SliderType::$MAIN_SLIDER]
                ])->first();

                if ($item)
                    return response()->json(['success' => false, 'message' => 'Already added this item']);

                $maxSort = SliderItem::where([
                    ['type', SliderType::$MAIN_SLIDER]
                ])->max('sort');

                if (!$maxSort)
                    $maxSort = 0;

                SliderItem::create([
                    'item_id' => $request->id,
                    'sort' => (int) $maxSort + 1,
                    'type' => SliderType::$MAIN_SLIDER
                ]);

                return response()->json(['success' => true]);
            }
        }
        else if ($request->type == SliderType::$CATEGORY_TOP_SLIDER) {
            $query->where('type', SliderType::$CATEGORY_TOP_SLIDER);
            $count = $query->count();

            if ($count >= 6)
                return response()->json(['success' => false, 'message' => 'Already added 6 items']);
            else {
                $item = SliderItem::where([
                    ['item_id', $request->id],
                    ['type', SliderType::$CATEGORY_TOP_SLIDER]
                ])->first();

                if ($item)
                    return response()->json(['success' => false, 'message' => 'Already added this item']);

                $maxSort = SliderItem::where([
                    ['type', SliderType::$CATEGORY_TOP_SLIDER]
                ])->max('sort');

                if (!$maxSort)
                    $maxSort = 0;

                SliderItem::create([
                    'item_id' => $request->id,
                    'sort' => (int) $maxSort + 1,
                    'type' => SliderType::$CATEGORY_TOP_SLIDER
                ]);

                return response()->json(['success' => true]);
            }
        }
        else if ($request->type == SliderType::$CATEGORY_SECOND_SLIDER) {
            $query->where('type', SliderType::$CATEGORY_SECOND_SLIDER);
            $count = $query->count();

            if ($count >= 6)
                return response()->json(['success' => false, 'message' => 'Already added 6 items']);
            else {
                $item = SliderItem::where([
                    ['item_id', $request->id],
                    ['type', SliderType::$CATEGORY_SECOND_SLIDER]
                ])->first();

                if ($item)
                    return response()->json(['success' => false, 'message' => 'Already added this item']);

                $maxSort = SliderItem::where([
                    ['type', SliderType::$CATEGORY_SECOND_SLIDER]
                ])->max('sort');

                if (!$maxSort)
                    $maxSort = 0;

                SliderItem::create([
                    'item_id' => $request->id,
                    'sort' => (int) $maxSort + 1,
                    'type' => SliderType::$CATEGORY_SECOND_SLIDER
                ]);

                return response()->json(['success' => true]);
            }
        }
        else if ($request->type == SliderType::$NEW_ARRIVAL_TOP_SLIDER) {
            $query->where('type', SliderType::$NEW_ARRIVAL_TOP_SLIDER);
            $count = $query->count();

            if ($count >= 6)
                return response()->json(['success' => false, 'message' => 'Already added 6 items']);
            else {
                $item = SliderItem::where([
                    ['item_id', $request->id],
                    ['type', SliderType::$NEW_ARRIVAL_TOP_SLIDER]
                ])->first();

                if ($item)
                    return response()->json(['success' => false, 'message' => 'Already added this item']);

                $maxSort = SliderItem::where([
                    ['type', SliderType::$NEW_ARRIVAL_TOP_SLIDER]
                ])->max('sort');

                if (!$maxSort)
                    $maxSort = 0;

                SliderItem::create([
                    'item_id' => $request->id,
                    'sort' => (int) $maxSort + 1,
                    'type' => SliderType::$NEW_ARRIVAL_TOP_SLIDER
                ]);

                return response()->json(['success' => true]);
            }
        }
        else if ($request->type == SliderType::$NEW_ARRIVAL_SECOND_SLIDER) {
            $query->where('type', SliderType::$NEW_ARRIVAL_SECOND_SLIDER);
            $count = $query->count();

            if ($count >= 6)
                return response()->json(['success' => false, 'message' => 'Already added 6 items']);
            else {
                $item = SliderItem::where([
                    ['item_id', $request->id],
                    ['type', SliderType::$NEW_ARRIVAL_SECOND_SLIDER]
                ])->first();

                if ($item)
                    return response()->json(['success' => false, 'message' => 'Already added this item']);

                $maxSort = SliderItem::where([
                    ['type', SliderType::$NEW_ARRIVAL_SECOND_SLIDER]
                ])->max('sort');

                if (!$maxSort)
                    $maxSort = 0;

                SliderItem::create([
                    'item_id' => $request->id,
                    'sort' => (int) $maxSort + 1,
                    'type' => SliderType::$NEW_ARRIVAL_SECOND_SLIDER
                ]);

                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false, 'message' => '']);
    }

    public function bannerItemRemove(Request $request) {
        SliderItem::where('id', $request->id)->delete();
    }

    public function LogoItemRemove(Request $request) { 

        Setting::where('id', $request->id)->update([
            'status' => 0, 
        ]);
    }

    public function bannerItemsSort(Request $request) {
        $sort = 1;

        foreach ($request->ids as $id) {
            SliderItem::where('id', $id)->update(['sort' => $sort]);
            $sort++;
        }
    }

    public function category_landing_page(Request $request){ 
        $id= $request->category;
        $category = Category::where('id', $id)->first(); 
        $images  = VendorImage::where('cat_id', $id)
            ->orderBy('sort') 
            ->where('cat_id',$request->category)
            ->get(); 
        return view('admin.dashboard.category.landing_page', compact('images','id','category'))->with('page_title', 'category landing');
    }

    public function mainSliderItems() {
        $images = VendorImage::where('type', VendorImageType::$MAIN_SLIDER)
            ->orderBy('sort')
            ->get();

        return view('admin.dashboard.marketing_tools.main_slider.index', compact('images'))->with('page_title', 'Main Slider');
    }

    public function mainSliderItemAdd(Request $request) {
        $request->validate([
            'photo' => 'required|mimes:jpg,jpeg,mp4,gif'
        ]);

        $filename = Uuid::generate()->string;
        $file = $request->file('photo');
        $ext = $file->getClientOriginalExtension();
 
        
        $compressedSavePath = 'images/banner/' . $filename . '.' . $ext;
        ImageOptimizer::optimize($file, public_path($compressedSavePath));
         


        $sort = VendorImage::where('type', VendorImageType::$MAIN_SLIDER)->max('sort');

        if ($sort == null || $sort == '')
            $sort = 0;

        $sort++;

        VendorImage::create([
            'type' => VendorImageType::$MAIN_SLIDER,
            'image_path' => $compressedSavePath,
            'status' => 1,
            'url' => $request->link,
            'sort' => $sort,
            'color' => $request->color
        ]);


        return redirect()->back()->with('message', 'Successfully Added!.');
    }

    public function mainSliderItemsSort(Request $request) {
        $sort = 1;

        foreach ($request->ids as $id) {
            VendorImage::where('id', $id)->update(['sort' => $sort]);
            $sort++;
        }
    }

    public function mainSliderItemDelete(Request $request) {
        $image = VendorImage::where('id', $request->id)->first();

        if ($image->image_path != null)
            File::delete(public_path($image->image_path));

        $image->delete();
    }

    public function frontPageBannerItems() {
        $images = VendorImage::where('type', VendorImageType::$FRONT_PAGE_BANNER)
            ->orderBy('sort')
            ->get();
        $images_mob = VendorImage::where('type', VendorImageType::$MOBILE_MAIN_BANNER)
            ->orderBy('sort')
            ->get();

        return view('admin.dashboard.marketing_tools.front_page_banner.index', compact('images','images_mob'))->with('page_title', 'Main Banner');
    }

    public function frontPageBannerItemAdd(Request $request) {

        $request->validate([
            'photo' => 'required|mimes:jpg,jpeg,mp4,gif'
        ]); 
        $filename = Uuid::generate()->string;
        $file = $request->file('photo');
        $ext = $file->getClientOriginalExtension(); 
        $compressedSavePath = '/images/banner/' . $filename . '.' . $ext; 
        ImageOptimizer::optimize($file, public_path($compressedSavePath));  
        $sort = VendorImage::where('type', VendorImageType::$FRONT_PAGE_BANNER)->max('sort');
 
        if ($sort == null || $sort == '')
            $sort = 0; 
        $sort++;  
        VendorImage::create([
            'type' => $request->type,
            'status' => 1,
            'image_path' => $compressedSavePath,
            'sort' => $sort,
            'url' => $request->link,
            'cat_id' => $request->cat_id,
            'discription' => $request->head,
        ]);
        if(isset($request->cat_id)){
            $category = Category::where('id', $request->cat_id)->first(); 
            $category->landing_page = 1;
            $category->details = $request->details;
            $category->save();
        }
        return redirect()->back()->with('message', 'Successfully Added!.');
    }
    public function category_landing_page_custom_content(Request $request){
        if(isset($request->cat_id)){
            $category = Category::where('id', $request->cat_id)->first();  
            $category->details = $request->details;
            $category->save();
        }
        return redirect()->back()->with('message', 'Successfully Updated!.');
    }

    public function frontPageBannerItemAddSecond(Request $request) { 
        $sidebar_banner = VendorImage::where('type', VendorImageType::$FRONT_PAGE_BANNERSEC)
            ->orderBy('sort') 
            ->get();  
        $item = Item::where('id', $request->item)->with('images')->first(); 
        $img_path=$item->images[0]->compressed_image_path;  
        $newArrivalItems = Item::where('id', $request->item) 
            ->with('images') 
            ->first(); 
        $slug =$item->slug;
        
        $sort = VendorImage::where('type', VendorImageType::$FRONT_PAGE_BANNERSEC)->max('sort');

        if ($sort == null || $sort == '')
            $sort = 0;

        $sort++; 
        
        VendorImage::create([
            'type' => VendorImageType::$FRONT_PAGE_BANNERSEC,
            'image_path' => $img_path,
            'url' => $item->id,
            'slug' => $slug,
            'title' => $item->name,
            'discription' => $item->description,
            'status' => 1,
            'sort' => $sort
        ]); 
        return redirect()->back()->with('message', 'Successfully Added!.'); 
    }

    public function editPost(Request $request) {
        VendorImage::where('id', $request->id)->update([
            'url' => $request->url,
            'head' => $request->head,
            'details' => $request->details,
            'color' => $request->color,
        ]);
    }

    // Section 2
    public function frontPageBannerTwo() {
        $images = VendorImage::where('type', VendorImageType::$FRONT_PAGE_BANNER_THREE)
            ->orderBy('sort')
            ->get();

        $images_mob = VendorImage::where('type', VendorImageType::$MOME_SECOND_SLIDER_MOB)
            ->orderBy('sort')
            ->get();

        return view('admin.dashboard.marketing_tools.front_page_banner.index_two', compact('images','images_mob'))->with('page_title', 'Home Page Banner');
    }

    public function frontPageBannerTwoAdd(Request $request) { 
        $request->validate([
            'photo' => 'required|mimes:jpg,jpeg,png',
        ]);

        $filename = Uuid::generate()->string;
        $file = $request->file('photo');
        $ext = $file->getClientOriginalExtension(); 
        $destinationPath = '/images/banner'; 

        $compressedSavePath = 'images/banner/' . $filename . '.' . $ext;
        ImageOptimizer::optimize($file, public_path($compressedSavePath));

        $sort = VendorImage::where('type', VendorImageType::$FRONT_PAGE_BANNER_THREE)->max('sort'); 
        if ($sort == null || $sort == '')
            $sort = 0; 
        $sort++; 
        VendorImage::create([
            'type' => $request->type,
            'image_path' => $compressedSavePath,
            'url' => $request->link,
            'details' => $request->details,
            'head' => $request->head,
            'status' => 1,
            'sort' => $sort
        ]); 
        return redirect()->back()->with('message', 'Successfully Added!.');
    }

    public function frontPageBannerTwoEdit(Request $request) {
        VendorImage::where('id', $request->id)->update([
            'url' => $request->url,
            'head' => $request->head,
            // 'color' => $request->color,
        ]);
    }
    // Section 2 end

    // Section 3
    public function frontPageBannerThree() {
        $images = VendorImage::where('type', VendorImageType::$FRONT_PAGE_BANNER_THREE)
            ->orderBy('sort')
            ->get();

        return view('admin.dashboard.marketing_tools.front_page_banner.index_three', compact('images'))->with('page_title', 'Section Three');
    }

    public function frontPageBannerThreeAdd(Request $request) {
        $request->validate([
            'photo' => 'required|mimes:jpg,jpeg'
        ]);

        $filename = Uuid::generate()->string;
        $file = $request->file('photo');
        $ext = $file->getClientOriginalExtension(); 

        $compressedSavePath = 'images/banner/' . $filename . '.' . $ext;
        ImageOptimizer::optimize($file, public_path($compressedSavePath));

        $sort = VendorImage::where('type', VendorImageType::$FRONT_PAGE_BANNER_THREE)->max('sort');

        if ($sort == null || $sort == '')
            $sort = 0;

        $sort++;

        VendorImage::create([
            'type' => VendorImageType::$FRONT_PAGE_BANNER_THREE,
            'image_path' => $compressedSavePath,
            'url' => $request->link,
            'head' => $request->head,
            'status' => 1,
            'sort' => $sort
        ]);


        return redirect()->back()->with('message', 'Successfully Added!.');
    }

    public function frontPageBannerThreeEdit(Request $request) {
        VendorImage::where('id', $request->id)->update([
            'url' => $request->url,
            'head' => $request->head,
            'color' => $request->color,
        ]);
    }
    // Section 3 end

    // Section 4
    public function frontPageBannerFour() {
        $images = VendorImage::where('type', VendorImageType::$FRONT_PAGE_BANNER_FOUR)
            ->orderBy('sort')
            ->get();

        return view('admin.dashboard.marketing_tools.front_page_banner.index_four', compact('images'))->with('page_title', 'Section Four');
    }

    public function frontPageBannerFourAdd(Request $request) {
        $request->validate([
            'photo' => 'required|mimes:jpg,jpeg'
        ]);

        $filename = Uuid::generate()->string;
        $file = $request->file('photo');
        $ext = $file->getClientOriginalExtension();

        $compressedSavePath = '/images/banner/' . $filename . '.' . $ext; 
        ImageOptimizer::optimize($file, public_path($compressedSavePath)); 

        $sort = VendorImage::where('type', VendorImageType::$FRONT_PAGE_BANNER_FOUR)->max('sort');

        if ($sort == null || $sort == '')
            $sort = 0;

        $sort++;

        VendorImage::create([
            'type' => VendorImageType::$FRONT_PAGE_BANNER_FOUR,
            'image_path' => $compressedSavePath,
            'url' => $request->link,
            'head' => $request->head,
            'status' => 1,
            'sort' => $sort
        ]);


        return redirect()->back()->with('message', 'Successfully Added!.');
    }

    public function frontPageBannerFourEdit(Request $request) {
        VendorImage::where('id', $request->id)->update([
            'url' => $request->url,
            'head' => $request->head,
            'color' => $request->color,
        ]);
    }
    // Section 3 end

    public function topBanner() {
        $categories = Category::where('parent', 0)->get();
        $banners = TopBanner::all();
        $categoryName = '';

        foreach ($banners as $banner) {
            $categoryName = Category::where('id',$banner->category_id)->first();
        }
        $settings_data = DB::table('home_page_settings')->get();

        return view('admin.dashboard.marketing_tools.top_banner.index', compact('categories','settings_data', 'banners','categoryName'))->with('page_title', 'Top Banner');
    }

    public function topBannerAdd(Request $request) {


//        $request->validate([
//            'photo' => 'required|mimes:jpg,jpeg',
//            'page' => 'required'
//        ]);

        $messages = [
            'required' => 'The category has already been taken.',
        ];

        $rules = [
            'page' => 'required|unique:top_banners,category_id',
        ];

        $request->validate($rules, $messages);


        $filename = Uuid::generate()->string;
        $file = $request->file('photo');
        if ($file){
        $ext = $file->getClientOriginalExtension();
        $compressedSavePath = '/images/banner/' . $filename . '.' . $ext;
        ImageOptimizer::optimize($file, public_path($compressedSavePath));
        }
        else {
            $compressedSavePath = null;
        }
        $page = null;
        $category = null;
        $previous = null;

        if ($request->page == '-1') {
            $page = PageEnumeration::$NEW_ARRIVAL;
            $previous = TopBanner::where('page', $page)->first();
        } else if ($request->page == '-2') {
            $page = PageEnumeration::$BEST_SELLER;

            $previous = TopBanner::where('page', $page)->first();
        } else {
            $category = $request->page;

            $previous = TopBanner::where('category_id', $category)->first();
        }

//        if ($previous) {
//            unlink(ltrim($previous->image_path, '/'));
//            $previous->delete();
//        }

        TopBanner::create([
            'title' => $request->title,
            'description' => $request->description,
            'page' => $page,
            'category_id' => $category,
            'url' => $request->link,
            'image_path' => $compressedSavePath
        ]);

        return redirect()->back();
    }

    public function topBannerDelete(Request $request) {
        TopBanner::where('id', $request->id)->delete();
    }

    public function topBannerEditPost(Request $request) {
        TopBanner::where('id', $request->category_page_id)->update([
            'description' => $request->modalDescription
        ]);
        return redirect()->back();
    }

    public function logoPost(Request $request) {
        $request->validate([
            'logo' => 'nullable|mimes:jpeg,jpg,png,svg',
            'logo2' => 'nullable|mimes:jpeg,jpg,png,svg',
            'logo3' => 'nullable|mimes:jpeg,jpg,png,svg',

        ]);
        if ($request->logo) {
            $file = $request->file('logo');
            $this->uploadLogo($file, 'logo-white');
        }

        if ($request->logo2) {
            $file = $request->file('logo2');
            $this->uploadLogo($file, 'logo-black');
        }
        if ($request->logo3) {
            $file = $request->file('logo3');
            $this->uploadLogo($file, 'default-item-image');
        }
        return redirect()->route('admin_banner');
    }

     

    public function uploadLogo($file, $type) {
        $filename = Uuid::generate()->string;
        $ext = $file->getClientOriginalExtension();
        $destinationPath = 'images/logo';
        $file->move(public_path($destinationPath), $filename.".".$ext);
        $imagePath = $destinationPath."/".$filename.".".$ext;

        DB::table('settings')->where('name', $type)->update(['value' => $imagePath,'status' => 1, 'created_at'=>Carbon::now()]);
    }
    
    // Admin New Arrival Section
    public function NewArrivalPageBanner() {
        $settings_data = DB::table('home_page_settings')->get();
        return view('admin.dashboard.marketing_tools.home_page.admin_new_arrival', compact('settings_data'))->with('page_title', 'New Arrival');
    }

    public function update_home_page_settings( Request $request){

        $customize_title_desc = $request->input('customize_title_desc');

        if ($customize_title_desc == 1){
            $data = [
                'new_title' => $request->input('new_title'),
                'new_color' => $request->input('new_color'),
                'new_font' => $request->input('new_font'),
                'new_font_family' => $request->input('new_font_family'),
                'new_desc' => $request->input('new_desc'),
                'new_desc_color' => $request->input('new_desc_color'),
                'new_desc_font' => $request->input('new_desc_font'),
                'new_desc_font_family' => $request->input('new_desc_font_family'),
            ];
        }
        if ($customize_title_desc == 2){
            $data = [
                'new_desc_two' => $request->input('new_desc_two'),
                'new_desc_two_color' => $request->input('new_desc_two_color'),
                'new_desc_two_font' => $request->input('new_desc_two_font'),
                'new_desc_two_font_family' => $request->input('new_desc_two_font_family'),
            ];
        }

        $table_id = 1;
        DB::table('home_page_settings')
            ->where('id', $table_id)
            ->update($data);

        return redirect()->back()->with('flash_message_success','Data Updated Successfully.');
    }
}
