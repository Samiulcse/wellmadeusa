<?php

namespace App\Http\Controllers\Admin;

use App\Enumeration\Availability;
use App\Model\CartItem;
use App\Model\Category;
use App\Model\Color;
use App\Model\Fabric;
use App\Model\Item;
use App\Model\ItemImages;
use App\Model\ItemInv;
use App\Model\ItemView;
use App\Model\MadeInCountry;
use App\Model\SliderItem;
use App\Model\ProductDetails;
use App\Model\VendorImage;
use App\Model\BulletTwo;
use App\Model\BulletThree;
use App\Model\BulletFour;
use App\Model\MasterColor;
use App\Model\MetaVendor;
use App\Model\Pack;
use App\Model\WishListItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Uuid;
use DateTime;
use Image;
use File;
use Carbon\Carbon;
use Excel;
use GuzzleHttp\Client;
use URL;
use DB;
use ImageOptimizer;

class ItemController extends Controller
{
    public function createNewItemIndex() {
        $packs = Pack::where('status', 1)->orderBy('name')->get();
        $fabrics = Fabric::where('status', 1)->orderBy('name')->get();
        $madeInCountries = MadeInCountry::where('status', 1)->orderBy('name')->get();
        $colors = Color::where('status', 1)->orderBy('name')->get();
        $ProductDetails = ProductDetails::where('status', 1)->get();
        $bulletTwoDetails = BulletTwo::where('status', 1)->get();
        $bulletThreeDetails = BulletThree::where('status', 1)->get();
        $bulletFourDetails = BulletFour::where('status', 1)->get();

        // Default Categories
        $defaultCategories = [];

        $categoriesCollection = Category::orderBy('sort')->orderBy('name')->get();

        foreach($categoriesCollection as $cc) {
            if ($cc->parent == 0) {
                $data = [
                    'id' => $cc->id,
                    'name' => $cc->name
                ];

                $subCategories = [];
                foreach($categoriesCollection as $item) {
                    if ($item->parent == $cc->id) {
                        $data2 = [
                            'id' => $item->id,
                            'name' => $item->name
                        ];

                        $data3 = [];
                        foreach($categoriesCollection as $item2) {
                            if ($item2->parent == $item->id) {
                                $data3[] = [
                                    'id' => $item2->id,
                                    'name' => $item2->name
                                ];
                            }
                        }

                        $data2['subCategories'] = $data3;
                        $subCategories[] = $data2;
                    }
                }

                $data['subCategories'] = $subCategories;
                $defaultCategories[] = $data;
            }
        }

        // Master Color
        $masterColors = MasterColor::orderBy('name')->get();

        return view('admin.dashboard.create_new_item.index', compact( 'packs', 'fabrics', 'madeInCountries',
            'defaultCategories', 'colors',  'masterColors','ProductDetails','bulletTwoDetails','bulletThreeDetails','bulletFourDetails'))
            ->with('page_title', 'Create a New Item');
    }

    public function createNewItemPost(Request $request) { 
         
        ini_set('upload_max_filesize', '5M');
        ini_set('post_max_size', '5M'); 
        $request->validate([
            'style_no' => 'required|max: 255|unique:items,style_no',
            'item_name' => 'required|max: 255',
            'price' => 'required|regex:/^-?[0-9]+(?:\.[0-9]{1,2})?$/',
            'orig_price' => 'nullable|numeric|min:'.$request->price,
            'size' => 'required',
            'sorting' => 'nullable|integer',
            'description' => 'nullable|max:500',
            'd_parent_category' => 'required',
            'min_qty' => 'nullable|integer',
            'memo' => 'nullable|max:255',
            'colors' => 'required',
            'video' => 'nullable|mimes:mp4',
        ]); 
        $videoPath = null;
        if ($request->video) {
            $filename = Uuid::generate()->string;
            $file = $request->file('video');
            $ext = $file->getClientOriginalExtension();

            $destinationPath = 'videos';
            $file->move(public_path($destinationPath), $filename.".".$ext);
            $videoPath = $destinationPath."/".$filename.".".$ext;
        } 
        $availableDate = null;
        if ($request->available_on) {
            //$availableOn = DateTime::createFromFormat('m/d/Y', $request->available_on);
            $availableDate = date('Y-m-d', strtotime($request->available_on));
            // $availableDate = $availableOn->format('Y-m-d');
        } 
        // Create slug from categoryname
        $itemName = $request->item_name."-".$request->style_no;
        $slug = str_replace('/', '-', str_replace(' ', '-', str_replace('&', '', str_replace('?', '', strtolower($itemName)))));

        $slugCheck = Item::where('slug', $slug)->first();
        if ( $slugCheck != null ) {
            // Check this category name already exists in category table
            $duplicateNameCounter = Item::where('name', $itemName)->count();
            // $slug .= '-' . time();
            $slug .= '-' . ($duplicateNameCounter + 1);
        }

        if($request->status == '1') {
            $activated_at = Carbon::now();
        }else {
            $activated_at = NULL;
        }
        // dd($request->all());
 
        $item = Item::create([
            'status' => $request->status,
            'style_no' => $request->style_no,
            'fit_size' => $request->fit_size,
            'price' => $request->price,
            'orig_price' => $request->orig_price,
            'pack_id' => $request->size,
            'sorting' => 1,
            'brand' => $request->brand,
            'description' => $request->description,
            'guest_image' => isset($request->guest_image) ? $request->guest_image : 1,
            'available_on' => $availableDate,
            'availability' => $request->availability,
            'name' => $request->item_name, 
            'slug' => $slug,
            'default' => $request->default_video_img ? 1:0,
            'default_parent_category' => $request->d_parent_category,
            'default_second_category' => $request->d_second_parent_category,
            'default_third_category' => $request->d_third_parent_category,
            'min_qty' => $request->min_qty,
            'fabric' => $request->fabric,
            'made_in_id' => $request->made_n,
            'material_one' => $request->material_one,
            'material_two' => $request->material_two,
            'material_three' => $request->material_three,
            'material_four' => $request->material_four,
            'material_five' => $request->material_five,
            'labeled' => $request->labeled,
            'activated_at' => $activated_at,
            'memo' => $request->memo,
            'video' => $videoPath,
            'youtube_url' => $request->youtube_url,
        ]);

        $colorAttach = [];
        foreach ($request->colors as $color) {
            $var = 'color_available_'.$color;

            $colorAttach[$color] = [
                'available' => ($request->$var ? 1 : 0)
            ];
        }

        $item->colors()->attach($colorAttach);

        if ($request->imagesId) {
            for ($i = 0; $i < sizeof($request->imagesId); $i++) {
                $image = ItemImages::where('id', $request->imagesId[$i])->first();

                $filename = Uuid::generate()->string;
                $ext = pathinfo($image->image_path, PATHINFO_EXTENSION);

                $listSavePath = 'images/item/list/' . $filename . '.' . $ext;
                $originalSavePath = 'images/item/original/' . $filename . '.' . $ext;
                $compressedSavePath = 'images/item/compressed/' . $filename . '.' . $ext;
                $thumbsSavePath = 'images/item/thumbs/' . $filename . '.' . $ext;

                // List Image
                if ($ext == 'gif') {
                    File::copy(public_path($image->image_path), public_path($listSavePath));
                } else {
                    $img = Image::make(public_path($image->image_path))->resize(1000,1500);
                    $img->save(public_path($listSavePath), 85);
                }
                // Thumbs Image
                if ($ext == 'gif') {
                    File::copy(public_path($image->image_path), public_path($thumbsSavePath));
                } else {
                    $thumb = Image::make(public_path($image->image_path))->resize(100, 150);
                    $thumb->save(public_path($thumbsSavePath), 85);
                }
                // if you use a second parameter the package will not modify the original
                ImageOptimizer::optimize(public_path($image->image_path), public_path($compressedSavePath));

                File::move(public_path($image->image_path), public_path($originalSavePath));

                $image->item_id = $item->id;
                $image->color_id = $request->imageColor[$i];
                $image->sort = $i + 1;
                $image->image_path = $originalSavePath;
                $image->list_image_path = $listSavePath;
                $image->thumbs_image_path = $thumbsSavePath;
                $image->compressed_image_path = $compressedSavePath;
                $image->save();
            }
        }
        $itemInvIds = [];
        if(isset($request->inv) && count($request->inv) > 0){  
            foreach ($request->inv as $inv){
                $available = '';
                if($inv['availability_inv']==1){
                    $available = 'null';
                }else if($inv['availability_inv']==2){
                    $available = $inv['available_on']; 
                }else if($inv['availability_inv']==3){
                    $available = "Out of Stock"; 
                }else{
                    $available = null;
                }  
                $itemInvModel = new ItemInv();
                $itemInvModel->item_id = $item->id;
                $itemInvModel->color_id = $inv['color_id'];
                $itemInvModel->color_name = $inv['color_name'];
                $itemInvModel->qty = $inv['qty'];
                $itemInvModel->threshold = $inv['threshold'];
                $itemInvModel->available_on = $available;
                $itemInvModel->created_at = Carbon::now();
                $itemInvModel->save();
                $itemInvIds[] = $itemInvModel->id;
            }
        }
        $itemInvModel = new ItemInv();
        $itemInvModel->where('item_id', $item->id)->whereNotIn('id', $itemInvIds)->delete();

        //return redirect()->route('vendor_item_list_by_category', ['category' => $item->category_id])->with('message', 'Item Added!');
        return redirect()->route('admin_item_list_all');
    }

    public function addColor(Request $request) {
        if ($request->id == '' || $request->name == '')
            return response()->json(['success' => false, 'message' => 'Invalid parameters.']);

        $mc = Color::where('name', $request->name)->first();

        if ($mc)
            return response()->json(['success' => false, 'message' => 'Already have this color.']);

        $mc = Color::create([
            'name' => $request->name,
            'status' => 1,
            'master_color_id' => $request->id,
        ]);

        return response()->json(['success' => true, 'color' => $mc->toArray()]);
    }

    public function uploadImage(Request $request) {
        $filename = Uuid::generate()->string;
        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();
        $destinationPath = '/images/item';

        $file->move(public_path($destinationPath), $filename.".".$ext);

        //Make item directory...
        if (!is_dir(public_path().$destinationPath.'/list')) {
            mkdir(public_path().$destinationPath.'/list');
            mkdir(public_path().$destinationPath.'/original');
            mkdir(public_path().$destinationPath.'/thumbs');
        }

        $imagePath = $destinationPath."/".$filename.".".$ext;

        $image = ItemImages::create([
            'image_path' => $imagePath
        ]);

        $image->fullPath = asset($imagePath);

        return response()->json(['success' => true, 'data' => $image->toArray()]);
    }

    public function itemListAll(Request $request) {
        $activeItemsQuery = Item::query();
        $activeItemsQuery->where('status', 1)
            ->with('category', 'images');

        //Active Item Search
        if ($request->text){
            $data = explode(',', $request->text);
            $activeItemsQuery->where(function ( $q) use ($data, $request){
                if (isset($request->style) && $request->style == '1') {
                    foreach ($data as $value){
                        $q->orWhere('style_no', 'like', '%' . ltrim($value, ' ') . '%');
                    }
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

        // History
        DB::table('item_list_history')->delete();
        $historyData = [];
        $q = clone $activeItemsQuery;
        $q->select('id')->orderBy('id', 'asc');
        $tmp = $q->pluck('id')->toArray();

        foreach ($tmp as $t)
            $historyData[] = [
                'item_id' => $t,
                'status' => 1
            ];
        $activeItemsCount = $activeItemsQuery->count();
        $activeItems = $activeItemsQuery->paginate(50, ['*'], 'p1');

        // Inactive Items
        $inactiveItemsQuery = Item::query();
        $inactiveItemsQuery->where('status', 0)
            ->with('category', 'images');

        //Inactive Item Search
        if ($request->text){
            $data = explode(',', $request->text);
            $inactiveItemsQuery->where(function ( $q) use ($data, $request){
                if (isset($request->style) && $request->style == '1') {
                    foreach ($data as $value){
                        $q->orWhere('style_no', 'like', '%' . ltrim($value, ' ') . '%');
                    }
                }

                if (isset($request->des) && $request->des == '1') {
                    $q->orWhere('description', 'like', '%' . $request->text . '%');
                }

                if (isset($request->name) && $request->name == '1') {
                    $q->orWhere('name', 'like', '%' . $request->text . '%');
                }
            });
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

        // History
        $q = clone $inactiveItemsQuery;
        $q->select('id');
        $tmp = $q->pluck('id')->toArray();

        foreach ($tmp as $t)
            $historyData[] = [
                'item_id' => $t,
                'status' => 0
            ];

        DB::table('item_list_history')->insert($historyData);
        $inactiveItemsCount = $inactiveItemsQuery->count();
        $inactiveItems = $inactiveItemsQuery->paginate(50, ['*'], 'p2');

        $appends = [
            'p1' => $activeItems->currentPage(),
            'p2' => $inactiveItems->currentPage(),
        ];

        foreach ($request->all() as $key => $value) {
            if ($key != 'p1' && $key != 'p2')
                $appends[$key] = ($value == null) ? '' : $value;
        }

        $vendor = MetaVendor::where('id', 1)->first();

        // Default Categories
        $defaultCategories = []; 

        // Vendor Categories
        $vendorCategories = []; 
        $categories = Category::where('parent', 0)->orderBy('sort')->orderBy('name')->get();

        // Url history
        DB::table('item_list_url_history')->delete();
        DB::table('item_list_url_history')->insert([
            'url' => url()->full()
        ]);

        return view('admin.dashboard.item_list.index', compact( 'activeItems','activeItemsCount', 'inactiveItems','inactiveItemsCount', 'appends',
            'vendor', 'defaultCategories', 'vendorCategories', 'categories'))
            ->with('page_title', 'Edit All Items');
    }

    public function categoryMove(Request $request) {

        $items = Item::whereIn('id', $request->ids)
            ->with('categories')->get();

        $active_cat = Category::where('id', $request->cat_id)->first();
        if ($active_cat->parent == 0){
            foreach ($items as $item) {
                $item->update(['default_parent_category' => $request->cat_id,'default_second_category' => 0]);
            }
        }else {
            foreach ($items as $item) {
                $item->update(['default_parent_category' => $active_cat->parent,'default_second_category' => $request->cat_id]);
            }
        }

    }

    public function itemsChangeToInactive(Request $request) {
        Item::whereIn('id', $request->ids)->update(['status' => 0]);
    }

    public function itemsChangeToActive(Request $request) {
        $time = Carbon::now();

        Item::whereIn('id', $request->ids)->update([
            'status' => 1,
            'sorting' => null,
            'activated_at' => $time
        ]);
    }

    public function itemsDelete(Request $request) { 
        $images = ItemImages::whereIn('item_id',$request->ids)->get(); 
        foreach ($images as $image) {  
            if (\File::exists(public_path().'/'.$image->image_path)) {  
                \File::delete(public_path().'/'.$image->image_path); 
            } 
            if (\File::exists(public_path().'/'.$image->list_image_path)) {  
                \File::delete(public_path().'/'.$image->list_image_path); 
            } 
            if (\File::exists(public_path().'/'.$image->compressed_image_path)) {  
                \File::delete(public_path().'/'.$image->compressed_image_path); 
            }  
            $custompath = explode('/',$image->image_path);   
            if (\File::exists(public_path().'/images/item/'.end($custompath))) {  
                \File::delete(public_path().'/images/item/'.end($custompath)); 
            } 
            $image->image_path = null;
            $image->list_image_path = null;
            $image->compressed_image_path = null;
            $image->save();
        }    
         
        \DB::table('color_item')->whereIn('item_id',$request->ids)->delete(); 
        CartItem::whereIn('item_id', $request->ids)->delete();
        WishListItem::whereIn('item_id', $request->ids)->delete();
        ItemView::whereIn('item_id', $request->ids)->delete();
        SliderItem::whereIn('item_id', $request->ids)->delete();
        VendorImage::whereIn('item_id', $request->ids)->delete(); 

        $items = Item::whereIn('id', $request->ids)->get(); 
        foreach ($items as $item) {
            if (\File::exists(public_path().'/'.$item->video)) {  
                \File::delete(public_path().'/'.$item->video); 
            } 
            $item->style_no = $item->style_no.'-delete-'.rand();
            $item->save();
            $item->delete();
        }
    }

    public function removeVideo(Request $request) 
    {
        Item::where('id', $request->id)->update(['video' => NULL]);
        return response()->json(['success' => true, 'data' => 'Video remoted!']);
    }

    public function editItem(Item $item) 
    {
        $item->load('colors', 'images');

        $packs = Pack::where('status', 1)->orderBy('name')->get();
        $madeInCountries = MadeInCountry::where('status', 1)->orderBy('name')->get();
        $colors = Color::where('status', 1)->orderBy('name')->get();
        $ProductDetails = ProductDetails::where('status', 1)->get();
        $bulletTwoDetails = BulletTwo::where('status', 1)->get();
        $bulletThreeDetails = BulletThree::where('status', 1)->get();
        $bulletFourDetails = BulletFour::where('status', 1)->get();

        // Images color id
        $imagesColorIds = [];
        foreach($item->images as $img)
            $imagesColorIds[] = $img->color_id;

        // Default Categories
        $defaultCategories = [];

        $categoriesCollection = Category::orderBy('sort')->orderBy('name')->get();

        foreach($categoriesCollection as $cc) {
            if ($cc->parent == 0) {
                $data = [
                    'id' => $cc->id,
                    'name' => $cc->name,
                    'slug' => $cc->slug
                ];

                $subCategories = [];
                foreach($categoriesCollection as $cat) {
                    if ($cat->parent == $cc->id) {
                        $data2 = [
                            'id' => $cat->id,
                            'name' => $cat->name,
                            'slug' => $cat->slug
                        ];

                        $data3 = [];
                        foreach($categoriesCollection as $item2) {
                            if ($item2->parent == $cat->id) {
                                $data3[] = [
                                    'id' => $item2->id,
                                    'name' => $item2->name,
                                    'slug' => $item2->slug
                                ];
                            }
                        }

                        $data2['subCategories'] = $data3;
                        $subCategories[] = $data2;
                    }
                }

                $data['subCategories'] = $subCategories;
                $defaultCategories[] = $data;
            }
        }

        // Master Color
        $masterColors = MasterColor::orderBy('name')->get();

        if (session('message') == null) {
            session(['back_url' => URL::previous()]);
        }

        $item_id = $item->id;
        // Get previous item by this item id
        $prev_item = DB::table('items')->where('id', '<', $item_id)->where('deleted_at', null)->orderBy('id', 'DESC')->first();
        $prev_item = isset($prev_item->id) ? $prev_item->id : 0;
        // Get next item by this item id
        $next_item = DB::table('items')->where('id', '>', $item_id)->where('deleted_at', null)->first();
        $next_item = isset($next_item->id) ? $next_item->id : 0;

        // Next-Previous items
        $history = DB::table('item_list_history')->select('item_id')
            ->where('status', $item->status)
            ->orderBy('id')
            ->pluck('item_id')->toArray();

        $nextItemId = null;
        $prevItemId = null;

        $currentPosition = array_search($item->id, $history);
//        dd($currentPosition);

        if ($currentPosition != 0)
            $prevItemId = isset($history[$currentPosition-1]) ? $history[$currentPosition-1] : null;

        if ($currentPosition < count($history)-1)
            $nextItemId = isset($history[$currentPosition+1]) ?  $history[$currentPosition+1] : null;

        // Back Url
//        $backUrl = null;

        // Url history
        DB::table('item_list_url_history')->delete();
        DB::table('item_list_url_history')->insert([
            'url' => url()->previous()
        ]);
        $tmp = DB::table('item_list_url_history')->first();
        if ($tmp)
            $backUrl = $tmp->url;

        return view('admin.dashboard.item_list.edit_item', compact( 'prev_item', 'next_item','nextItemId', 'prevItemId', 'backUrl', 'packs', 'madeInCountries',
            'defaultCategories', 'colors','item', 'imagesColorIds', 'masterColors','ProductDetails','bulletTwoDetails','bulletThreeDetails','bulletFourDetails'))
            ->with('page_title', 'Item Edit');
    }

    public function editItemPost(Item $item, Request $request) { 
        $request->validate([
            'style_no' => 'required|max: 255|unique:items,style_no,'.$item->id,
            'price' => 'required|regex:/^-?[0-9]+(?:\.[0-9]{1,2})?$/',
            'orig_price' => 'nullable|numeric|min:'.$request->price,
            'size' => 'required',
            'sorting' => 'nullable|integer',
            'item_name' => 'required|max: 255',
            'description' => 'nullable|max:500',
            'd_parent_category' => 'required',
            'min_qty' => 'nullable|integer',
            'memo' => 'nullable|max:255',
            'colors' => 'required',
            'video' => 'nullable|mimes:mp4',
        ]);
        $availableDate = null;
        if ($request->available_on) {
            //$availableOn = DateTime::createFromFormat('m/d/Y', $request->available_on);
            $availableDate = date('Y-m-d', strtotime($request->available_on));
            // $availableDate = $availableOn->format('Y-m-d');
        }
        $videoPath = 0; 
        if ($request->video) { 
            $filename = Uuid::generate()->string;
            $file = $request->file('video');
            $ext = $file->getClientOriginalExtension();

            $destinationPath = 'videos';
            $file->move(public_path($destinationPath), $filename.".".$ext);
            $videoPath = $destinationPath."/".$filename.".".$ext;
            $item->video = $videoPath;
        }
        if($item->status == '0' && $request->status == '1') {
            $item->activated_at = Carbon::now();
        }elseif($item->status == '1' && $request->status == '1') {
            $item->activated_at = Carbon::now();
        }else {
            $item->activated_at = NULL;
        }

        $item->status = $request->status;
        $item->style_no = $request->style_no;
        $item->fit_size = $request->fit_size;
        $item->price = $request->price;
        $item->orig_price = $request->orig_price;
        $item->pack_id = $request->size;
        $item->sorting = $request->sorting;
        $item->brand = $request->brand;
        $item->default = $request->default_video_img ? 1:0;
        $item->description = $request->description;
        $item->guest_image = isset($request->guest_image) ? $request->guest_image: 0;
        $item->available_on = $availableDate;
        $item->availability = $request->availability;
        $item->name = $request->item_name; 
        $item->default_parent_category = $request->d_parent_category;
        $item->default_second_category = $request->d_second_parent_category;
        $item->default_third_category = $request->d_third_parent_category;
        $item->min_qty = $request->min_qty;
        $item->fabric = $request->fabric;
        $item->made_in_id = $request->made_n;
        $item->material_one = $request->material_one;
        $item->material_two = $request->material_two;
        $item->material_three = $request->material_three;
        $item->material_four = $request->material_four;
        $item->material_five = $request->material_five;
        $item->labeled = $request->labeled;
        $item->memo = $request->memo; 
        $item->youtube_url = $request->youtube_url;

        $item->save();
        $item->touch();

        $colorAttach = [];
        foreach ($request->colors as $color) {
            $var = 'color_available_'.$color;

            $colorAttach[$color] = [
                'available' => ($request->$var ? 1 : 0)
            ];
        }

        $item->colors()->sync($colorAttach);

        if ($request->imagesId) {
            for ($i = 0; $i < sizeof($request->imagesId); $i++) {
                $image = ItemImages::where('id', $request->imagesId[$i])->first();

                if ($image->list_image_path == null) {
                    $filename = Uuid::generate()->string;
                    $ext = pathinfo($image->image_path, PATHINFO_EXTENSION);

                    $listSavePath = 'images/item/list/' . $filename . '.' . $ext;
                    $originalSavePath = 'images/item/original/' . $filename . '.' . $ext;
                    $thumbsSavePath = 'images/item/thumbs/' . $filename . '.' . $ext;
                    $compressedSavePath = 'images/item/compressed/' . $filename . '.' . $ext;

                    // List Image
                    if ($ext == 'gif') {
                        File::copy(public_path($image->image_path), public_path($listSavePath));
                    } else {
                        $img = Image::make(public_path($image->image_path))->resize(1000, 1500);
                        $img->save(public_path($listSavePath), 85);
                    }

                    // Thumbs Image
                    if ($ext == 'gif') {
                        File::copy(public_path($image->image_path), public_path($thumbsSavePath));
                    } else {
                        $thumb = Image::make(public_path($image->image_path))->resize(100, 150);
                        $thumb->save(public_path($thumbsSavePath), 85);
                    }

                    // if you use a second parameter the package will not modify the original
                    ImageOptimizer::optimize(public_path($image->image_path), public_path($compressedSavePath));

                    File::move(public_path($image->image_path), public_path($originalSavePath));

                    $image->image_path = $originalSavePath;
                    $image->list_image_path = $listSavePath;
                    $image->thumbs_image_path = $thumbsSavePath;
                    $image->compressed_image_path = $compressedSavePath;

                }

                $image->sort = $i + 1;
                $image->color_id = $request->imageColor[$i];
                $image->item_id = $item->id;
                $image->save();
            }

            $images = ItemImages::where('item_id', $item->id)
                ->whereNotIn('id', $request->imagesId)
                ->get();

            foreach ($images as $image) {
                if (file_exists($image->image_path)) {
                    unlink($image->image_path);
                }

                if (file_exists($image->list_image_path)) {
                    unlink($image->list_image_path);
                }

                if (file_exists($image->thumbs_image_path)) {
                    unlink($image->thumbs_image_path);
                }

                $image->delete();
            }
        }
 
        $itemInvIds = [];
        if(isset($request->inv) && count($request->inv) > 0){
            foreach ($request->inv as $inv){
                $available = '';
                if($inv['availability_inv']==1){
                    $available = 'null';
                }else if($inv['availability_inv']==2){
                    $available = $inv['available_on']; 
                }else if($inv['availability_inv']==3){
                    $available = "Out of Stock"; 
                }else{
                    $available = null;
                } 

                if($inv['id'] == 0){
                    $itemInvModel = new ItemInv();
                    $itemInvModel->item_id = $item->id;
                    $itemInvModel->color_id = $inv['color_id'];
                    $itemInvModel->color_name = $inv['color_name'];
                    $itemInvModel->qty = $inv['qty'];
                    $itemInvModel->threshold = $inv['threshold'];
                    $itemInvModel->available_on = $available;
                    $itemInvModel->created_at = Carbon::now();
                    $itemInvModel->save();
                    $itemInvIds[] = $itemInvModel->id;
                } else {
                    $itemInvModel = new ItemInv();
                    $cCheck = $itemInvModel->where('item_id', $item->id)->where('id', $inv['id'])->get()->first();
                    if($cCheck != null){
                        $cCheck->qty = $inv['qty'];
                        $cCheck->threshold = $inv['threshold'];
                        $cCheck->available_on = $available;
                        $cCheck->updated_at = Carbon::now();
                        $cCheck->save();
                    }
                    $itemInvIds[] = $inv['id'];
                }

            }
        }
        $itemInvModel = new ItemInv();
        $itemInvModel->where('item_id', $item->id)->whereNotIn('id', $itemInvIds)->delete();

        //return redirect()->route('vendor_item_list_by_category', ['category' => $item->category_id])->with('message', 'Item Updated!');
        //return redirect()->route('admin_item_list_all');
        return redirect()->back()->with('message', 'Item Updated!');
    }
public function cloneMultiItems(Request $request) {
        $items = Item::select('id','style_no')
                            ->whereIn('id', $request->ids)
                            ->orderBy('updated_at', 'desc')
                            ->orderBy('id','desc')
                            ->get();
        if(count($items) > 0){
            $items = array_reverse($items->toArray());
        }
        foreach ($items as $itemD) {
            $item = Item::where('id', $itemD['id'])
                ->with('images', 'colors')->get()->first();
            $new = $item->replicate();
            // return $new;

            $videoPath = null;
            if ($new->video) {
                $filename = Uuid::generate()->string;
                // $file = $request->file('video');
                $arr = explode('.',$new->video);
                $ext = end($arr);

                $destinationPath = 'videos';

                File::copy(public_path($new->video), public_path($destinationPath."/".$filename.".".$ext));
                $videoPath = $destinationPath."/".$filename.".".$ext;
            }


            $availableDate = null;
            if ($new->available_on) {
                $availableDate = date('Y-m-d', strtotime($new->available_on));
            }

            // Create slug from categoryname
            $itemName = $new->item_name."-".$new->style_no;
            $slug = str_replace('/', '-', str_replace(' ', '-', str_replace('&', '', str_replace('?', '', strtolower($itemName)))));

            $slugCheck = Item::where('slug', $slug)->first();
            if ( $slugCheck != null ) {
                $duplicateNameCounter = Item::where('name', $itemName)->count();
                $slug .= '-' . ($duplicateNameCounter + 1);
            }

            if ($new->status == '1'){
                $activated_at = Carbon::now()->toDateTimeString();
            }else{
                $activated_at = null;
            }

            $cCheck = Item::select('id','style_no')->where('style_no','Like', '%'.$item->style_no.'.%')->orderBy('id', 'desc')->take(1)->get()->toArray();
            if(count($cCheck) > 0){
                $cCheck = $cCheck[0];
                $new->style_no = $cCheck['style_no'].'.';
            } else {
                $new->style_no .= '-clone';
            }

            $new->available_on = $availableDate;
            $new->slug = $slug;
            $new->video = $videoPath;
            $new->activated_at = $activated_at;
            $new->updated_at = Carbon::now();
            $new->save();
            $newItemId = $new->id;

            // Colors
            $colorIds = $item->colors->pluck('id')->toArray();
            $new->colors()->attach($colorIds);

            // images
            foreach ($item->images as $image) {
                $tmp = $image->replicate();

                $filename = Uuid::generate()->string;
                $ext = pathinfo($image->image_path, PATHINFO_EXTENSION);


                $listSavePath = 'images/item/list/' . $filename . '.' . $ext;
                $originalSavePath = 'images/item/original/' . $filename . '.' . $ext;
                $thumbsSavePath = 'images/item/thumbs/' . $filename . '.' . $ext;
                $compressedSavePath = 'images/item/compressed/' . $filename . '.' . $ext;

                // List Image
                try {
                    File::copy($tmp->list_image_path, public_path($listSavePath));
                    File::copy($tmp->image_path, public_path($originalSavePath));
                    File::copy($tmp->thumbs_image_path, public_path($thumbsSavePath));
                    File::copy($tmp->compressed_image_path, public_path($compressedSavePath));

                    $tmp->item_id = $new->id;
                    $tmp->list_image_path = $listSavePath;
                    $tmp->image_path = $originalSavePath;
                    $tmp->thumbs_image_path = $thumbsSavePath;
                    $tmp->compressed_image_path = $compressedSavePath;
                    $tmp->save();
                } catch (\Exception $exception) {

                }
            }

            $itemInvModel = new ItemInv();
            $itemInv = $itemInvModel->where('item_id', $item->id)->get()->toArray();
            if(count($itemInv) > 0){
                foreach ($itemInv as $inv){
                    $itemInvModel = new ItemInv();
                    $itemInvModel->item_id = $newItemId;
                    $itemInvModel->color_id = $inv['color_id'];
                    $itemInvModel->color_name = $inv['color_name'];
                    $itemInvModel->qty = $inv['qty'];
                    $itemInvModel->threshold = $inv['threshold'];
                    $itemInvModel->available_on = $inv['available_on'];
                    $itemInvModel->created_at = Carbon::now();
                    $itemInvModel->save();
                }
            }
        }
    }
    public function cloneItem(Item $item) {
        $item->load('colors', 'images');

        $packs = Pack::where('status', 1)->orderBy('name')->get();
        $madeInCountries = MadeInCountry::where('status', 1)->orderBy('name')->get();
        $colors = Color::where('status', 1)->orderBy('name')->get();
        $ProductDetails = ProductDetails::where('status', 1)->get();
        $bulletTwoDetails = BulletTwo::where('status', 1)->get();
        $bulletThreeDetails = BulletThree::where('status', 1)->get();
        $bulletFourDetails = BulletFour::where('status', 1)->get();

        // Images color id
        $imagesColorIds = [];
        foreach($item->images as $img)
            $imagesColorIds[] = $img->color_id;

        // Default Categories
        $defaultCategories = [];

        $categoriesCollection = Category::orderBy('sort')->orderBy('name')->get();

        foreach($categoriesCollection as $cc) {
            if ($cc->parent == 0) {
                $data = [
                    'id' => $cc->id,
                    'name' => $cc->name,
                    'slug' => $cc->slug
                ];

                $subCategories = [];
                foreach($categoriesCollection as $cat) {
                    if ($cat->parent == $cc->id) {
                        $data2 = [
                            'id' => $cat->id,
                            'name' => $cat->name,
                            'slug' => $cat->slug
                        ];

                        $data3 = [];
                        foreach($categoriesCollection as $item2) {
                            if ($item2->parent == $cat->id) {
                                $data3[] = [
                                    'id' => $item2->id,
                                    'name' => $item2->name,
                                    'slug' => $item2->slug
                                ];
                            }
                        }

                        $data2['subCategories'] = $data3;
                        $subCategories[] = $data2;
                    }
                }

                $data['subCategories'] = $subCategories;
                $defaultCategories[] = $data;
            }
        }

        // Master Color
        $masterColors = MasterColor::orderBy('name')->get();

        return view('admin.dashboard.item_list.clone_item', compact('packs', 'madeInCountries',
            'defaultCategories', 'colors', 'item', 'imagesColorIds', 'masterColors','ProductDetails','bulletTwoDetails','bulletThreeDetails','bulletFourDetails'))
            ->with('page_title', 'Create a New Item');
    }

    public function cloneItemPost(Item $old_item, Request $request) { 
        $request->validate([
            'style_no' => 'required|max: 255|unique:items,style_no',
            'item_name' => 'required|max: 255',
            'price' => 'required|numeric',
            'orig_price' => 'nullable|numeric',
            'size' => 'required',
            'sorting' => 'nullable|integer',
            'description' => 'nullable|max:500',
            'd_parent_category' => 'required',
            'min_qty' => 'nullable|integer',
            'memo' => 'nullable|max:255',
            'video' => 'nullable|mimes:mp4',
        ]);

        

        $videoPath = null;
        if ($request->video) {
            $filename = Uuid::generate()->string;
            $file = $request->file('video');
            $ext = $file->getClientOriginalExtension();

            $destinationPath = 'videos';
            $file->move(public_path($destinationPath), $filename.".".$ext);
            $videoPath = $destinationPath."/".$filename.".".$ext;
        }

        $availableDate = null;
        if ($request->available_on) {
            //$availableOn = DateTime::createFromFormat('m/d/Y', $request->available_on);
            $availableDate = date('Y-m-d', strtotime($request->available_on));
            // $availableDate = $availableOn->format('Y-m-d');
        }

        if($request->status == '1') {
            $activated_at = Carbon::now();
        }else {
            $activated_at = NULL;
        }

        // Create slug from categoryname
        $itemName = $request->item_name."-".$request->style_no;
        $slug = str_replace('/', '-', str_replace(' ', '-', str_replace('&', '', str_replace('?', '', strtolower($itemName)))));

        $slugCheck = Item::where('slug', $slug)->first();
        if ( $slugCheck != null ) {
            // Check this category name already exists in category table
            $duplicateNameCounter = Item::where('name', $itemName)->count();
            // $slug .= '-' . time();
            $slug .= '-' . ($duplicateNameCounter + 1);
        }
        $item = Item::create([
            'status' => $request->status,
            'style_no' => $request->style_no,
            'fit_size' => $request->fit_size,
            'price' => $request->price,
            'orig_price' => $request->orig_price,
            'pack_id' => $request->size,
            'sorting' =>1,
            'brand' =>$request->brand,
            'description' => $request->description,
            'guest_image' => isset($request->guest_image) ? $request->guest_image : 1,
            'available_on' => $availableDate,
            'availability' => $request->availability,
            'name' => $request->item_name,
            'slug' => $slug,
            'default' => $request->default_video_img ? 1:0,
            'default_parent_category' => $request->d_parent_category,
            'default_second_category' => $request->d_second_parent_category,
            'default_third_category' => $request->d_third_parent_category,
            'min_qty' => $request->min_qty,
            'fabric' => $request->fabric,
            'made_in_id' => $request->made_n,
            'material_one' => $request->material_one,
            'material_two' => $request->material_two,
            'material_three' => $request->material_three,
            'material_four' => $request->material_four,
            'material_five' => $request->material_five,
            'labeled' => $request->labeled,
            'activated_at' => $activated_at,
            'memo' => $request->memo,
            'video' => $videoPath,
            'youtube_url' => $request->youtube_url,
            ]);
            
            $colorAttach = [];
            foreach ($request->colors as $color) {
                $var = 'color_available_'.$color;
                
                $colorAttach[$color] = [
                    'available' => ($request->$var ? 1 : 0)
                ];
            } 
            if ($request->video) {
                $filename = Uuid::generate()->string;
                $file = $request->file('video');
                $ext = $file->getClientOriginalExtension();
    
                $destinationPath = 'videos';
                $file->move(public_path($destinationPath), $filename.".".$ext);
                $videoPath = $destinationPath."/".$filename.".".$ext;
                $item->video = $videoPath;
            } else if ($old_item->video != null) {
                $filename = Uuid::generate()->string;
                $destinationPath = 'videos/'.$filename.'.mp4';
    
                File::Copy(public_path($old_item->video), public_path($destinationPath));
    
                $item->video = $destinationPath;
            }
            
        $item->colors()->attach($colorAttach);

        if ($request->imagesId) {
            for ($i = 0; $i < sizeof($request->imagesId); $i++) {
                $tmp = ItemImages::where('id', $request->imagesId[$i])->first();

                if (is_null($tmp->item_id)) {
                    $filename = Uuid::generate()->string;
                    $ext = pathinfo($tmp->image_path, PATHINFO_EXTENSION);

                    $listSavePath = 'images/item/list/' . $filename . '.' . $ext;
                    $originalSavePath = 'images/item/original/' . $filename . '.' . $ext;
                    $thumbsSavePath = 'images/item/thumbs/' . $filename . '.' . $ext;
                    $compressedSavePath = 'images/item/compressed/' . $filename . '.' . $ext;

                    // List Image
                    $img = Image::make(public_path($tmp->image_path))->resize(1000, 1500);
                    $img->save(public_path($listSavePath), 85);

                    // Thumbs Image
                    $thumb = Image::make(public_path($tmp->image_path))->resize(100, 150);
                    $thumb->save(public_path($thumbsSavePath), 85);

                    // if you use a second parameter the package will not modify the original
                    ImageOptimizer::optimize(public_path($tmp->image_path), public_path($compressedSavePath));

                    File::move(public_path($tmp->image_path), public_path($originalSavePath));

                    $tmp->item_id = $item->id;
                    $tmp->color_id = $request->imageColor[$i];
                    $tmp->sort = $i + 1;
                    $tmp->image_path = $originalSavePath;
                    $tmp->list_image_path = $listSavePath;
                    $tmp->thumbs_image_path = $thumbsSavePath;
                    $tmp->compressed_image_path = $compressedSavePath;
                    $tmp->save();

                    /*$tmp->item_id = $item->id;
                    $tmp->color_id = $request->imageColor[$i];
                    $tmp->sort = $i + 1;
                    $tmp->save();*/
                } else {
                    $filename = Uuid::generate()->string;
                    $ext = pathinfo($tmp->image_path, PATHINFO_EXTENSION);

                    $listSavePath = 'images/item/list/' . $filename . '.' . $ext;
                    $originalSavePath = 'images/item/original/' . $filename . '.' . $ext;
                    $thumbsSavePath = 'images/item/thumbs/' . $filename . '.' . $ext;
                    $compressedSavePath = 'images/item/compressed/' . $filename . '.' . $ext;

                    // List Image
                    $img = Image::make(public_path($tmp->image_path))->resize(1000, 1500);
                    $img->save(public_path($listSavePath), 85);

                    // Thumbs Image
                    $thumb = Image::make(public_path($tmp->image_path))->resize(100, 150);
                    $thumb->save(public_path($thumbsSavePath), 85);

                    File::copy(public_path($tmp->image_path), public_path($originalSavePath));

                    // if you use a second parameter the package will not modify the original
                    ImageOptimizer::optimize(public_path($tmp->image_path), public_path($compressedSavePath));

                    ItemImages::create([
                        'item_id' => $item->id,
                        'color_id' => $request->imageColor[$i],
                        'sort' => $i+1,
                        'image_path' => $originalSavePath,
                        'list_image_path' => $listSavePath,
                        'thumbs_image_path' => $thumbsSavePath,
                        'compressed_image_path' => $compressedSavePath,
                    ]);
                }

            }
        }

        $item->save();

        $itemInvIds = [];
        if(isset($request->inv) && count($request->inv) > 0){
            foreach ($request->inv as $inv){
                $available = '';
                if($inv['availability_inv']==1){
                    $available = 'null';
                }else if($inv['availability_inv']==2){
                    $available = $inv['available_on']; 
                }else if($inv['availability_inv']==3){
                    $available = "Out of Stock"; 
                }else{
                    $available = null;
                } 
                $itemInvModel = new ItemInv();
                $itemInvModel->item_id = $item->id;
                $itemInvModel->color_id = $inv['color_id'];
                $itemInvModel->color_name = $inv['color_name'];
                $itemInvModel->qty = $inv['qty'];
                $itemInvModel->threshold = $inv['threshold'];
                $itemInvModel->available_on = $available;
                $itemInvModel->created_at = Carbon::now();
                $itemInvModel->save();
                $itemInvIds[] = $itemInvModel->id;
            }
        }
        $itemInvModel = new ItemInv();
        $itemInvModel->where('item_id', $item->id)->whereNotIn('id', $itemInvIds)->delete();
        //return redirect()->route('vendor_item_list_by_category', ['category' => $item->category_id])->with('message', 'Item Added!');
        return redirect()->route('admin_item_list_all');
    }

    public function itemListByCategory(Category $category, Request $request) {
        // Category Level Check
        $catLvl = 1;
        if ($category->parent != 0) {
            if ($category->parentCategory->parent == 0)
                $catLvl = 2;
            else
                $catLvl = 3;
        }


        // Active Items
        $activeItemsQuery = Item::query();

        if ($catLvl == 1)
            $activeItemsQuery->where('default_parent_category', $category->id);
        elseif ($catLvl == 2)
            $activeItemsQuery->where('default_second_category', $category->id);
        else
            $activeItemsQuery->where('default_third_category', $category->id);

        $activeItemsQuery->where('status', 1)
            ->with('category', 'images');

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
            
            /*if (isset($request->style) && $request->style == '1')
                $activeItemsQuery->where('style_no', 'like', '%' . $request->text . '%');

            if (isset($request->des) && $request->des == '1')
                $activeItemsQuery->where('description', 'like', '%' . $request->text . '%');

            if (isset($request->name) && $request->name == '1')
                $activeItemsQuery->where('name', 'like', '%' . $request->text . '%');*/
        }

        // Order
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
        $activeItemsCount = $activeItemsQuery->count();
        $activeItems = $activeItemsQuery->paginate(50, ['*'], 'p1');

        // Inactive Items
        $inactiveItemsQuery = Item::query();

        if ($catLvl == 1)
            $inactiveItemsQuery->where('default_parent_category', $category->id);
        elseif ($catLvl == 2)
            $inactiveItemsQuery->where('default_second_category', $category->id);
        else
            $inactiveItemsQuery->where('default_third_category', $category->id);

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

        // Order
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
            else if ($request->s2 == '0')
                $inactiveItemsQuery->orderBy('sorting');
        } else {
            $inactiveItemsQuery->orderBy('created_at', 'desc');
            $inactiveItemsQuery->orderBy('sorting');
        }
        $inactiveItemsCount = $inactiveItemsQuery->count();
        $inactiveItems = $inactiveItemsQuery->paginate(50, ['*'], 'p2');

        $appends = [
            'p1' => $activeItems->currentPage(),
            'p2' => $inactiveItems->currentPage(),
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
        $categories = Category::where('parent', 0)->orderBy('sort')->orderBy('name')->get();

 
        return view('admin.dashboard.item_list.index', compact('category','categories', 'activeItems','inactiveItemsCount', 'inactiveItems','activeItemsCount',
            'appends', 'vendor', 'defaultCategories', 'vendorCategories'))
            ->with('page_title', $category->name);
    }

    public function dataImportView() {
        return view('admin.dashboard.data_import')->with('page_title', 'Data Import');
    }

    public function dataImportReadFile(Request $request) {
        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();

        if (in_array($ext, ['xlsx', 'csv', 'xls'])) {
            $excel = Excel::load($file->getRealPath(), function ($reader) {
                $content = $reader->get();
            });

            $data = $excel->get()->toArray();

            if (sizeof($data) == 0)
                return redirect()->back()->with('error', 'Invalid file');

            $item = $data[0];
            $items = [];

            if (sizeof($item) >= 2 && sizeof($item) <= 3 && array_key_exists("styleno", $item) && array_key_exists("images", $item)) {
                foreach ($data as $item)
                    $items [] = $item;

                return view('admin.dashboard.image_import_stats', compact('items'));
            }

            if (!array_key_exists("styleno", $item) ||
                !array_key_exists("itemname", $item) ||
                !array_key_exists("defaultcategory", $item) ||
                !array_key_exists("size", $item) ||
                !array_key_exists("pack", $item) ||
                !array_key_exists("packqty", $item) ||
                !array_key_exists("unitprice", $item) ||
                !array_key_exists("originalprice", $item) ||
                !array_key_exists("availableon", $item) ||
                !array_key_exists("productdescription", $item) ||
                !array_key_exists("fabric", $item) ||
                !array_key_exists("color", $item)) {

                return redirect()->back()->with('error', 'Invalid file');
            }

            foreach ($data as &$item) {
                if ($item['styleno'] != null || $item['styleno'] != '') {
                    // Available On
                    $date = '';

                    if ($item['availableon'] != null || $item['availableon'] != '') {
                        try {
                            $date = $item['availableon']->format('Y-m-d');
                        } catch (\Throwable $e) {

                        }

                        $item['availableon'] = $date;
                    }

                    $items[] = $item;
                }
            }

            return view('admin.dashboard.data_import_stats', compact('items'));
        } else {
            return redirect()->back()->with('error', 'Invalid file');
        }
    }

    public function dataImportUpload(Request $request) {
        // Style no check
        $found = false;

        $item = Item::where('style_no', $request->styleno)->first();

        if ($item) {
            $found = true;
        }

        // Default Category Check
        if ($request->defaultcategory != null && $request->defaultcategory == '')
            return response()->json(['success' => false, 'message' => 'Default category required.']);

        $dc = explode(',', $request->defaultcategory);
        $defaultCategory = Category::where('name', $dc[0])
            ->where('parent', 0)
            ->first();

        if (!$defaultCategory)
            return response()->json(['success' => false, 'message' => 'Default category not found.']);

        // Second default category check
        $defaultCategorySecondId = null;
        if (sizeof($dc) > 1) {
            $defaultCategorySecond = Category::where('name', $dc[1])
                ->where('parent', $defaultCategory->id)
                ->first();

            if (!$defaultCategorySecond)
                return response()->json(['success' => false, 'message' => 'Invalid Default Sub category.']);
            else
                $defaultCategorySecondId = $defaultCategorySecond->id;
        }

        // Third default category check
        $defaultCategoryThirdId = null;
        if (sizeof($dc) > 2) {
            $defaultCategoryThird = Category::where('name', $dc[2])
                ->where('parent', $defaultCategorySecond->id)
                ->first();

            if (!$defaultCategoryThird)
                return response()->json(['success' => false, 'message' => 'Invalid Default Sub category.']);
            else
                $defaultCategoryThirdId = $defaultCategoryThird->id;
        }

        // Size Check
        if ($request->size != null && $request->size == '')
            return response()->json(['success' => false, 'message' => 'Size is required.']);

        $pack = Pack::where('status', 1)
            ->where('name', $request->size)
            ->first();

        if (!$pack)
            return response()->json(['success' => false, 'message' => 'Size not found.']);


        // Made In Country
        $madeInId = null;

        if ($request->madein != null && $request->madein != '') {
            $madeIn = MadeInCountry::where('status', 1)
                ->where('name', $request->madein)
                ->first();

            if ($madeIn)
                $madeInId = $madeIn->id;
        }

        // Availability
        $availability = Availability::$IN_STOCK;

        if ($request->availableon != null) {
            if(time() < strtotime($request->availableon)) {
                $availability = Availability::$ARRIVES_SOON;
            }
        }

        // Colors check
        if ($request->color != null && $request->color == '')
            return response()->json(['success' => false, 'message' => 'Color is required.']);

        $colorIds = [];
        $colors = explode(',', $request->color);

        foreach ($colors as $color) {
            $c = Color::where('status', 1)
                ->where('name', $color)
                ->first();

            if (!$c) {
                $c = Color::create([
                    'name' => $color,
                    'status' => 1,
                ]);
            }

            $colorIds[] = $c->id;
        }

        if (sizeof($colorIds) == 0)
            return response()->json(['success' => false, 'message' => 'Color(s) not found.']);

        // Create Item
        if ($found) {
            $item->price = $request->unitprice;
            $item->orig_price = $request->originalprice;
            $item->pack_id = $pack->id;
            $item->description = $request->productdescription;
            $item->available_on = $request->availableon;
            $item->availability = $availability;
            $item->name = $request->itemname;
            $item->default_parent_category = $defaultCategory->id;
            $item->default_second_category = $defaultCategorySecondId;
            $item->default_third_category = $defaultCategoryThirdId;
            $item->min_qty = $request->packqty;
            $item->fabric = $request->fabric;
            $item->made_in_id = $madeInId;
            $item->memo = $request->inhousememo;

            $item->save();
            $item->touch();

            $item->colors()->detach();
            foreach ($item->images as $image)
                $image->delete();
        } else {
            $item = Item::create([
                'status' => 0,
                'style_no' => $request->styleno,
                'price' => $request->unitprice,
                'orig_price' => $request->originalprice,
                'pack_id' => $pack->id,
                'description' => $request->productdescription,
                'available_on' => $request->availableon,
                'availability' => $availability,
                'name' => $request->itemname,
                'default_parent_category' => $defaultCategory->id,
                'default_second_category' => $defaultCategorySecondId,
                'default_third_category' => $defaultCategoryThirdId,
                'min_qty' => $request->packqty,
                'fabric' => $request->fabric,
                'made_in_id' => $madeInId,
                'memo' => $request->inhousememo,
            ]);
        }

        $item->colors()->attach($colorIds);

        // Images
        if ($request->images != '') {
            $images_color = [];
            $urls = explode(',', $request->images);
            $colors = explode(',', $request->color);

            if ($request->images_color && $request->images_color != '')
                $images_color = explode(',', $request->images_color);

            $sort = 1;
            foreach ($urls as $url) {
                $filename = Uuid::generate()->string;
                $ext = pathinfo($url, PATHINFO_EXTENSION);




                $listSavePath = 'images/item/list/' . $filename . '.' . $ext;
                $originalSavePath = 'images/item/original/' . $filename . '.' . $ext;
                $thumbsSavePath = 'images/item/thumbs/' . $filename . '.' . $ext;

                // List Image
                $img = Image::make($url)->resize(1000, 1500);
                $img->save(public_path($listSavePath), 85);

                // Thumbs Image
                $thumb = Image::make($url)->resize(100, 150);
                $thumb->save(public_path($thumbsSavePath), 85);

                File::copy($url, public_path($originalSavePath));
                //File::copy($url, public_path('images/item/' . $filename . '.' . $ext));

                // Color
                $colorId = null;

                if (isset($colors[$sort-1])) {
                    $colorName = $colors[$sort - 1];

                    $color = Color::where('status', 1)
                        ->where('name', $colorName)
                        ->first();

                    if ($color)
                        $colorId = $color->id;
                }


                ItemImages::create([
                    'item_id' => $item->id,
                    'sort' => $sort,
                    'color_id' => $colorId,
                    'image_path' => $originalSavePath,
                    'list_image_path' => $listSavePath,
                    'thumbs_image_path' => $thumbsSavePath,
                ]);

                $sort++;
            }
        }

        return response()->json(['success' => true, 'message' => 'Completed']);
    }

    public function dataImportImage(Request $request) {
        $item = Item::where('style_no', $request->styleno)->first();

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Style No. not found.']);
        }

        foreach ($item->images as $image)
            $image->delete();

        // Images
        if ($request->images != '') {
            $urls = explode(',', $request->images);

            $sort = 1;
            foreach ($urls as $url) {
                $filename = Uuid::generate()->string;
                $ext = pathinfo($url, PATHINFO_EXTENSION);

                $listSavePath = 'images/item/list/' . $filename . '.' . $ext;
                $originalSavePath = 'images/item/original/' . $filename . '.' . $ext;
                $thumbsSavePath = 'images/item/thumbs/' . $filename . '.' . $ext;

                // List Image
                $img = Image::make($url)->resize(1000, 15000);
                $img->save(public_path($listSavePath), 85);

                // Thumbs Image
                $thumb = Image::make($url)->resize(100, 150);
                $thumb->save(public_path($thumbsSavePath), 85);

                File::copy($url, public_path($originalSavePath));
                //File::copy($url, public_path('images/item/' . $filename . '.' . $ext));

                ItemImages::create([
                    'item_id' => $item->id,
                    'sort' => $sort,
                    'image_path' => $originalSavePath,
                    'list_image_path' => $listSavePath,
                    'thumbs_image_path' => $thumbsSavePath,
                ]);

                $sort++;
            }
        }

        return response()->json(['success' => true, 'message' => 'Completed']);
    }
}
