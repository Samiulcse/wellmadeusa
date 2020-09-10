<?php

namespace App\Http\Controllers\Api;

use App\Enumeration\Availability;
use App\Enumeration\Role;
use App\Model\Category;
use App\Model\Color;
use App\Model\Item;
use App\Model\ItemCategory;
use App\Model\ItemImages;
use App\Model\MadeInCountry;
use App\Model\Pack;
use App\Model\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Exception\NotReadableException;
use Uuid;
use Image;
use File;
use ImageOptimizer;

use App\Model\ItemInv;
use Carbon\Carbon;

class ItemController extends Controller
{
    public function createItem(Request $request) {
        if (!isset($request->username) || !isset($request->password))
            return response()->json(['success' => false, 'message' => 'username & password parameter required.']);

        $user = User::where('user_id', $request->username)
            ->whereIn('role', [Role::$ADMIN, Role::$EMPLOYEE])
            ->with('vendor')->first();

        if (!$user)
            return response()->json(['success' => false, 'message' => 'Username not found.']);

        if ($user->vendor->active == 0)
            return response()->json(['success' => false, 'message' => 'Vendor is inactivate.']);

        if ($user->vendor->verified == 0)
            return response()->json(['success' => false, 'message' => 'Vendor is not verified.']);

        if (Hash::check($request->password, $user->password)) {
            $requiredParameters = ['styleno', 'defaultcategory', 'size', 'pack', 'unitprice', 'color'];

            foreach ($requiredParameters as $parameter) {
                if (!isset($request->$parameter) || $request->$parameter == '')
                    return response()->json(['success' => false, 'message' => 'These parameters required: '.implode(',', $requiredParameters)]);
            }

            $found = false;
            $item = Item::where('style_no', $request->styleno)
                ->first();

            if ($item) {
                $found = true;
            }

            // Default Category Check
            $dc = explode(',', $request->defaultcategory);

            $defaultCategory = Category::where('id', $dc[0])
                ->where('parent', 0)
                ->first();

            if (!$defaultCategory)
                return response()->json(['success' => false, 'message' => 'Default category not found.']);

            // Second default category check
            $defaultCategorySecondId = null;
            if (sizeof($dc) > 1) {
                $defaultCategorySecond = Category::where('id', $dc[1])
                    ->where('parent', $defaultCategory->id)
                    ->first();

                if (!$defaultCategorySecond)
                    return response()->json(['success' => false, 'message' => 'Sub category not found.']);
                else
                    $defaultCategorySecondId = $defaultCategorySecond->id;
            }

            // Third default category check
            $defaultCategoryThirdId = null;
            if (sizeof($dc) > 2) {
                $defaultCategoryThird = Category::where('id', $dc[2])
                    ->where('parent', $defaultCategorySecond->id)
                    ->first();

                if (!$defaultCategoryThird)
                    return response()->json(['success' => false, 'message' => 'Sub category not found.']);
                else
                    $defaultCategoryThirdId = $defaultCategoryThird->id;
            }


            // Size Check
            $packQuery = Pack::query();
            $packQuery->where('status', 1)
                ->where('name', $request->size);

            $packSizes = explode('-', $request->pack);

            for($i=1; $i <= sizeof($packSizes); $i++) {
                $var = 'pack'.$i;
                $packQuery->where('pack'.$i, (int) $packSizes[$i-1]);

                /*if ((int) $packSizes[$i-1] != $pack->$var) {
                    $pack = null;
                    return $packSizes;
                    break;
                }*/
            }

            $pack = $packQuery->first();


            if (!$pack) {
                $pack = new Pack;
                $pack->name = $request->size;
                $pack->status = 1;
                $pack->default = 0;

                $packSizes = explode('-', $request->pack);
                for($i=1; $i <= sizeof($packSizes); $i++) {
                    $var = 'pack'.$i;

                    $pack->$var = (int) $packSizes[$i-1];
                }

                $pack->save();
            }

            // Made In Country
            $madeInId = null;

            if ($request->madein != null && $request->madein != '') {
                $madeIn = MadeInCountry::where('status', 1)
                    ->where('name', $request->madein)
                    ->first();

                if ($madeIn)
                    $madeInId = $madeIn->id;
            }

            // Available On
            $date = '';

            if ($request->availableon != null || $request->availableon != '') {
                if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$request->availableon))

                    $date = $request->availableon;
            }

            // Availability
            $availability = Availability::$IN_STOCK;

            if ($date != '') {
                if(time() < strtotime($date)) {
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
            
            $time = Carbon::now();
            if($request->status == 1){
                $activation_date = $time->toDateTimeString();
            }else{
                $activation_date = null;
            }

            // Create Item

            if ($found) {
                $item->status = $request->status;
                $item->brand = $request->company_name;
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
                $item->labeled = $request->labeled;
                $item->made_in_id = $madeInId;
                $item->memo = $request->inhousememo;
                $item->activated_at = $activation_date;

                $item->save();
                $item->touch();

                $item->colors()->detach();
                $item->images()->delete();
            } else {
                // Create slug from categoryname
                $itemName = $request->itemname . '-' . $request->styleno;
                $slug = str_replace('/', '-', str_replace(' ', '-', str_replace('&', '', str_replace('?', '', strtolower($itemName)))));

                $slugCheck = Item::where('slug', $slug)->first();
                if ( $slugCheck != null ) {
                    // Check this category name already exists in category table
                    $duplicateNameCounter = Item::where('name', $itemName)->count();
                    // $slug .= '-' . time();
                    $slug .= '-' . ($duplicateNameCounter + 1);
                }
                $item = Item::create([
                    'status' => 0,
                    'style_no' => $request->styleno,
                    'brand' => $request->company_name,
                    'price' => $request->unitprice,
                    'orig_price' => $request->originalprice,
                    'pack_id' => $pack->id,
                    'sorting' => 1,
                    'description' => $request->productdescription,
                    'available_on' => $request->availableon,
                    'availability' => $availability,
                    'name' => $request->itemname,
                    'slug' => $slug,
                    'default_parent_category' => $defaultCategory->id,
                    'default_second_category' => $defaultCategorySecondId,
                    'default_third_category' => $defaultCategoryThirdId,
                    'min_qty' => $request->packqty,
                    'fabric' => $request->fabric,
                    'labeled' => $request->labeled,
                    'made_in_id' => $madeInId,
                    'memo' => $request->inhousememo,
                    'activated_at' => $activation_date
                ]);
            }

            $item->colors()->attach($colorIds);

            // Images
            if ($request->images != '') {
                $urls = explode(',', $request->images);
                $images_color = [];
                $colors = explode(',', $request->color);

                if ($request->images_color != '')
                    $images_color = explode(',', $request->images_color);

                $sort = 1;
                foreach ($urls as $url) {
                    $filename = Uuid::generate()->string;
                    $ext = pathinfo($url, PATHINFO_EXTENSION);

                    $listSavePath = 'images/item/list/' . $filename . '.' . $ext;
                    $originalSavePath = 'images/item/original/' . $filename . '.' . $ext;
                    $thumbsSavePath = 'images/item/thumbs/' . $filename . '.' . $ext;
                    $compressedSavePath = 'images/item/compressed/' . $filename . '.' . $ext;

                    // List Image
                    try
                    {
                        $img = Image::make($url)->resize(1000, 1500);
                        $img->save(public_path($listSavePath), 85);
                    }
                    catch(NotReadableException $e)
                    {
                        continue;
                    }


                    // Thumbs Image
                    $thumb = Image::make($url)->resize(100, 150);
                    $thumb->save(public_path($thumbsSavePath), 85);

                    // if you use a second parameter the package will not modify the original
                    // ImageOptimizer::optimize($url, public_path($compressedSavePath));
                    $filesize = $this->getImageSize($url); 
                    
                    if($filesize > 300){   
                        ImageOptimizer::optimize($url, public_path($compressedSavePath));
                    }else{   
                        $data = file_get_contents($url);
                        file_put_contents($compressedSavePath, $data); 
                    }
                    
                    File::copy($url, public_path($originalSavePath));
                    //File::copy($url, public_path('images/item/' . $filename . '.' . $ext));


                    // Color
                    $colorId = null;

                    if (sizeof($images_color) >= $sort) {
                        if (in_array($images_color[$sort-1], $colors)) {
                            if ($images_color[$sort-1] != null && $images_color[$sort-1] != '') {
                                $color = Color::where('status', 1)
                                    ->where('name', $images_color[$sort-1])
                                    ->first();

                                if ($color)
                                    $colorId = $color->id;
                            }
                        }
                    }

                    ItemImages::create([
                        'item_id' => $item->id,
                        'sort' => $sort,
                        'color_id' => $colorId,
                        'image_path' => $originalSavePath,
                        'list_image_path' => $listSavePath,
                        'thumbs_image_path' => $thumbsSavePath,
                        'compressed_image_path' => $compressedSavePath,
                    ]);

                    $sort++;
                }
            }
            
             //Inventory
            $itemId =  $item->id;
            $itemInvIds = [];
            if ($request->inventory_color_id != '') {
                $inventory_color_id = explode(',', $request->inventory_color_id);
                $inventory_color_name = explode(',', $request->inventory_color_name);
                $inventory_qty = explode(',', $request->inventory_color_qty);
                $inventory_threshold = explode(',', $request->inventory_color_threshold);
                $inventory_available_on = explode(',', $request->inventory_available_on);

                for ($i = 0; $i < sizeof($inventory_color_id);)
                {
                    $itemInvModel = new ItemInv();
                    $itemInvModel->item_id = $itemId;
                    $itemInvModel->color_id = $colorIds[$i];
                    $itemInvModel->color_name = $inventory_color_name[$i];
                    $itemInvModel->qty = $inventory_qty[$i];
                    $itemInvModel->threshold = $inventory_threshold[$i];
                    $itemInvModel->available_on = $inventory_available_on[$i] == '' ? 'null': $inventory_available_on[$i];
                    $itemInvModel->created_at = Carbon::now();
                    $itemInvModel->save();
                    $i++;
                    $itemInvIds[] = $itemInvModel->id;
                }
                $itemInvModel = new ItemInv();
                $itemInvModel->where('item_id', $item->id)->whereNotIn('id', $itemInvIds)->delete();

            }

            return response()->json(['success' => true, 'message' => 'Item added successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid Password.']);
    }

    public function statusChange(Request $request) {
        if (!isset($request->username) || !isset($request->password))
            return response()->json(['success' => false, 'message' => 'username & password parameter required.']);

        $user = User::where('user_id', $request->username)
            ->whereIn('role', [Role::$ADMIN, Role::$EMPLOYEE])
            ->first();

        if (!$user)
            return response()->json(['success' => false, 'message' => 'Username not found.']);

        if ($user->vendor->active == 0)
            return response()->json(['success' => false, 'message' => 'Vendor is inactivate.']);

        if ($user->vendor->verified == 0)
            return response()->json(['success' => false, 'message' => 'Vendor is not verified.']);

        if (Hash::check($request->password, $user->password)) {
            $requiredParameters = ['styleno', 'status'];

            foreach ($requiredParameters as $parameter) {
                if (!isset($request->$parameter) || $request->$parameter == '')
                    return response()->json(['success' => false, 'message' => 'These parameters required: '.implode(',', $requiredParameters)]);
            }

            if ($request->status != '0' && $request->status != '1')
                return response()->json(['success' => false, 'message' => 'Status should be 0 or 1']);

            $item = Item::where('style_no', $request->styleno)->first();

            if (!$item)
                return response()->json(['success' => false, 'message' => 'Item not found']);

            $item->status = $request->status;
            $item->save();

            return response()->json(['success' => true, 'message' => 'Item status changed.']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid Password.']);
    }
    
    public function getImageSize($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        $data = curl_exec($ch);
        $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close($ch);
        return $size / 1024;
    }
}
