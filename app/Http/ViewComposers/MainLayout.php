<?php

namespace App\Http\ViewComposers;

use App\Enumeration\PageEnumeration;
use App\Enumeration\Role;
use App\Enumeration\VendorImageType;
use App\Model\BuyerMessage;
use App\Model\CartItem;
use App\Model\Item;
use App\Model\Category;
use App\Model\DefaultCategory; 
use App\Model\Meta;
use App\Model\Pack;
use App\Model\MetaVendor;
use App\Model\Notification;
use App\Model\PromoCodes;
use App\Model\Setting;
use App\Model\VendorImage;
use App\Model\SocialLinks;
use App\Model\MetaSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Route;
use DB;

class MainLayout
{
    private $text;

    public function __construct(Request $request)
    {
        if (Route::currentRouteName() == 'category_page') {
            $this->text = $request->category;
        }
        if (Route::currentRouteName() == 'second_category') {
            $this->text = $request->category;
        }
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Default Categories
        $defaultCategories = [];
        $categoriesCollection = Category::orderBy('sort')->orderBy('name')->get();

        foreach($categoriesCollection as $cc) {
            if ($cc->parent == 0) {
                $data = [
                    'id' => $cc->id,
                    'name' => $cc->name,
                    'slug' => $cc->slug,
                    'image' => $cc->image
                ];

                $subCategories = [];
                foreach($categoriesCollection as $item) {
                    if ($item->parent == $cc->id) {
                        $data2 = [
                            'id' => $item->id,
                            'name' => $item->name,
                            'slug' => $item->slug,
                            'image' => $cc->image
                        ];

                        $data3 = [];
                        foreach($categoriesCollection as $item2) {
                            if ($item2->parent == $item->id) {
                                $data3[] = [
                                    'id' => $item2->id,
                                    'name' => $item2->name,
                                    'slug' => $item2->slug,
                                    'image' => $cc->image
                                ];
                            }
                        }

                        $data2['thirdcategory'] = $data3;
                        $subCategories[] = $data2;
                    }
                }

                $data['subCategories'] = $subCategories;
                $defaultCategories[] = $data;
            }
        }
 
        // Cart
        $cartItems['total'] = 0;
        $cartItems['empty'] = 0;

        if (Auth::check() && Auth::user()->role == Role::$BUYER) { 
            $items = CartItem::where('user_id', Auth::user()->id)
                ->with('item','color')->get();
            
                $totalPrice = 0;
                $totalQty = 0;
                $i=0;
                foreach($items as $item) { 
                    $pack = pack::where('id',$item->item->pack_id)->first();
                    $pack_size = $pack->pack1 + $pack->pack2+ $pack->pack3 + $pack->pack4 + $pack->pack5 + $pack->pack6 + $pack->pack7 + $pack->pack8 + $pack->pack9 + $pack->pack10;
                     
                $qty = $item->quantity;
                $totalPrice += $item->quantity * $item->item['price'] * $pack_size;
                $totalQty += $item->quantity; 
                $image_path = asset('images/no-image.png'); 
                if (!empty($item->item['images'])){ 
                    foreach($item->item['images'] as $image) 

                    if($image->color_id == $item->color_id){  
                        $image_path = asset($image->thumbs_image_path);
                    break;
                    }
                } 
                $cartItems['items'][$i] = [
                    'name' => $item->item['style_no'],
                    'qty' => $qty,
                    'color' => $item->color->name,
                    'image_path' => $image_path,
                    'price' => $item->item['price'] * $pack_size,
                    'details_url' => route('item_details_page', ['item' => $item->item['id']])
                ];
                $cartItems['empty'] = 1;
                $i++;
            } 
            $cartItems['total'] = [
                'total_price' => $totalPrice,
                'total_qty' => $totalQty
            ];
        }  
        // Notification
        $notifications = [];

        if (Auth::check() && Auth::user()->role == Role::$BUYER)
            $notifications = Notification::where('user_id', Auth::user()->id)
                ->where('view', 0)->get();

        // Meta
        $meta_title = config('app.name');
        $meta_description = '';
        $meta = null;
        $current_category = null;

        if (Route::currentRouteName() == 'home') {
            $meta = Meta::where('page', PageEnumeration::$HOME)->first();
        }  else if (Route::currentRouteName() == 'about_us') {
            $meta = Meta::where('page', PageEnumeration::$ABOUT_US)->first();
        } else if (Route::currentRouteName() == 'contact_us') {
            $meta = Meta::where('page', PageEnumeration::$CONTACT_US)->first();
        } else if (Route::currentRouteName() == 'privacy_policy') {
            $meta = Meta::where('page', PageEnumeration::$PRIVACY_POLICY)->first();
        } else if (Route::currentRouteName() == 'return_info') {
            $meta = Meta::where('page', PageEnumeration::$RETURN_INFO)->first();
        }else if (Route::currentRouteName() == 'shipping') {
            $meta = Meta::where('page', PageEnumeration::$SHIPPING)->first();
        }else if (Route::currentRouteName() == 'terms_conditions') {
            $meta = Meta::where('page', PageEnumeration::$TERMS_AND_CONDIOTIONS)->first();
        } else if (Route::currentRouteName() == 'billing_shipping') {
            $meta = Meta::where('page', PageEnumeration::$BILLING_SHIPPING_INFO)->first();
        } else if (Route::currentRouteName() == 'look_book') {
            $meta = Meta::where('page', PageEnumeration::$LOOK_BOOK)->first();
        } else if (Route::currentRouteName() == 'large_quantities') {
            $meta = Meta::where('page', PageEnumeration::$LARGE_QUANTITIES)->first();
        } else if (Route::currentRouteName() == 'refunds') {
            $meta = Meta::where('page', PageEnumeration::$REFUNDS)->first();
        } else if (Route::currentRouteName() == 'category_page') { 
            foreach ($defaultCategories as $cat) {
                if (changeSpecialChar($cat['slug']) == $this->text) {
                    $meta = Meta::where('category', $cat['id'])->first(); 
                    break;
                }
            }
        } else if (Route::currentRouteName() == 'second_category') { 
            if($this->text == "new"){
                $currenturl = $_SERVER['REQUEST_URI'];  
                $currenturl = explode('/', $currenturl); 
                if($currenturl[1] =='polagram-2'){
                    $meta_title =  'New Arrival - Polagram ' ; 
                    $meta_description = " Let's check the new arrival tops, bottoms, outerwear, dresses of Polagram, the fastest growing wholesale women's clothing store in the USA."; 
                }else if($currenturl[1] =='baevely'){  
                    $meta_title =  'New Arrival - Baevely' ; 
                    $meta_description = "  Let's check the new arrival tops, bottoms, outerwear, dresses of Baevely, the fastest growing wholesale women's clothing store in the USA."; 
                }else if($currenturl[1] =='curvy-2'){ 
                    $meta_title =  'New Arrival - Curvy ' ; 
                    $meta_description = "Let's check the new arrival curvy tops, bottoms, outerwear, dresses of Polagram & Baevely, the fastest growing wholesale women's clothing store in the USA."; 
                }
            }
            foreach ($defaultCategories as $cat) {
                foreach ($cat['subCategories'] as $d_sub) {
                    if (changeSpecialChar($d_sub['slug']) == $this->text) {
                        $meta = Meta::where('category', $d_sub['id'])->first(); 
                        break;
                    }
                }
            }
        }else if (Route::currentRouteName() == 'third_category') { 
            $currenturl = url()->current();
            $currenturl = explode('/',$currenturl); 
            $category = Category::where('slug',end($currenturl))->first(); 
            $meta = Meta::where('category', $category->id)->first();
        }else if (Route::currentRouteName() == 'product_single_page') { 
            $currenturl = $_SERVER['REQUEST_URI'];  
            $currenturl = explode('/', $currenturl);  
            $slugCheck = Item::where('slug', end($currenturl))->first(); 
            $meta_title =  $slugCheck ? ucwords($slugCheck->name) : NULL ; 
            $meta_description = $meta_title. ' is available with the best wholesale price. ';  
        }
        if ($meta) {
            if ($meta->title != NULL && $meta->title != '')
                $meta_title = $meta->title;

            if ($meta->title != NULL)
                $meta_description = $meta->description;
        }
  
        // Logo Path
        /*$logo_path = '';
        $vendorLogo = VendorImage::where('status', 1)
            ->where('type', VendorImageType::$LOGO)
            ->first();

        if ($vendorLogo)
            $logo_path = asset($vendorLogo->image_path);*/
        $black_logo_path = '';
        $white_logo_path = '';
        $top_notification = '';

        $settings = DB::table('settings')->get();

        foreach ($settings as $settings_item){
            if($settings_item->name == 'logo-white'){
                $white_logo_path = asset($settings_item->value);
            }

            if($settings_item->name == 'logo-black'){
                $black_logo_path = asset($settings_item->value);
            }


        }

        $white = DB::table('settings')->where('name', 'logo-white')->first();
        if ($white)
            $white_logo_path = asset($white->value);

        $black = DB::table('settings')->where('name', 'logo-black')->first();
        if ($black)
            $black_logo_path = asset($black->value);

        $defaultItemImage = DB::table('settings')->where('name', 'default-item-image')->first();
        if ($defaultItemImage)
            $defaultItemImage_path = asset($defaultItemImage->value);

        /*Notification Banner module*/
        $notification_banner_module = DB::table('vendor_images')->where('type', '9')->get();

        if(count($notification_banner_module) == 0)
        {
            $notification_banner_module = [];
        }

        // Get social links
        $socialLinks = SocialLinks::first();

        // Get header footer color
        $headerBGColor = MetaSettings::where('meta_key', 'header_color')->first();
        $headerFontColor = MetaSettings::where('meta_key', 'header_font_color')->first();
        $footerBGColor = MetaSettings::where('meta_key', 'footer_color')->first();
        $footerFontColor = MetaSettings::where('meta_key', 'footer_font_color')->first();
        $topnotification = Setting::where('name', 'top_notification')->first();

        // Promotion Title
        $promotionTitle = PromoCodes::where('id', 1)->where('status',1)->first();
        $unread_messages = '';
        if(Auth::user()){
            $unread_messages = BuyerMessage::where('user_id', Auth::user()->id)
                ->where('reading_status', 0)
                ->count();
        }
         
        $view->with([
            'default_categories' => $defaultCategories,
            'cart_items' => $cartItems,
            'notifications' => $notifications,
            'meta_title' => $meta_title,
            'promotionTitle' => $promotionTitle,
            'white_logo_path' => $white_logo_path,
            'top_notification' => $topnotification,
            'black_logo_path' => $black_logo_path,
            'unread_messages' => $unread_messages,
            'meta_description' => $meta_description, 
            'current_category' => $current_category,
            // 'defaultItemImage_path' => $defaultItemImage_path,
            'notification_banner_module' => $notification_banner_module,
            'social_links' => $socialLinks,
            'header_bg_color' => isset($headerBGColor->meta_value) ? $headerBGColor->meta_value : 'none',
            'header_font_color' => isset($headerFontColor->meta_value) ? $headerFontColor->meta_value : 'none',
            'footer_bg_color' => isset($footerBGColor->meta_value) ? $footerBGColor->meta_value : 'none',
            'footer_font_color' => isset($footerFontColor->meta_value) ? $footerFontColor->meta_value : 'none'
        ]);
    }
}
