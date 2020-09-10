<?php

namespace App\Http\ViewComposers;

use App\Enumeration\VendorImageType;
use App\Model\Category;
use App\Model\AdminMessage;
use App\Model\DefaultCategory;
use App\Model\VendorImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Route;
use DB;

class AdminLayout
{
    public function __construct(Request $request)
    {

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
                $subIds = [];
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

                        $subIds[] = $item->id;
                        $subIds2 = [];
                        $data3 = [];
                        foreach($categoriesCollection as $item2) {
                            if ($item2->parent == $item->id) {
                                $subIds[] = $item2->id;
                                $subIds2[] = $item2->id;

                                $data3[] = [
                                    'id' => $item2->id,
                                    'name' => $item2->name
                                ];
                            }
                        }

                        $data2['subCategories'] = $data3;
                        $data2['subIds'] = $subIds2;
                        $subCategories[] = $data2;
                    }
                }

                $data['subCategories'] = $subCategories;
                $data['subIds'] = $subIds;
                $defaultCategories[] = $data;
            }
        }

        //dd($defaultCategories);

        // Logo
        $logo_path = '';
        
        

        /*$image = VendorImage::where('type', VendorImageType::$LOGO)
            ->where('status', 1)->first();

        if ($image)
            $logo_path = asset($image->image_path);*/

        $black = DB::table('settings')->where('name', 'logo-black')->first();
        if ($black)
            $logo_path = asset($black->value);
        $unread_messages = '';
        if(Auth::user()){
            $unread_messages = AdminMessage::where('reading_status', 0)
                ->count();
        }
        $defaultItemImage = DB::table('settings')->where('name', 'default-item-image')->first();
        // if (!empty($defaultItemImage)){
            $defaultItemImage_path = asset($defaultItemImage->value);
            $view->with([
                'categories' => $defaultCategories,
                'unread_messages' => $unread_messages,
                'logo_path' => $logo_path,
                'defaultItemImage_path' => $defaultItemImage_path
            ]);
        // }
       
    }
}
