<?php

namespace App\Http\Controllers;

use App\Model\Item;
use App\Model\Category;
use App\Model\MetaVendor;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index()
    {
        $itemsLastModify = Item::where('status', 1)->orderBy('updated_at', 'desc')->limit(1)->first();
        $vendorsLastModify = MetaVendor::where('active', 1)->where('verified', 1)->orderBy('updated_at', 'desc')->limit(1)->first();
        $categoriesModify = Category::where('parent', 0)->orderBy('updated_at', 'desc')->limit(1)->first();

        return response()->view('sitemap.index', [
            'itemsLastModify' => $itemsLastModify,
            'vendorsLastModify' => $vendorsLastModify,
            'categoriesModify' => $categoriesModify,
        ])->header('Content-Type', 'text/xml');
    }

    public function staticPages() {
        return response()->view('sitemap.static_pages', [])->header('Content-Type', 'text/xml');
    }

    public function items() {
        $items = Item::where('status', 1)->get();

        return response()->view('sitemap.items', [
            'items' => $items,
        ])->header('Content-Type', 'text/xml');
    }

    public function vendors() {
        $vendors = MetaVendor::where('verified', 1)
            ->where('active', 1)
            ->get();

        return response()->view('sitemap.vendors', [
            'vendors' => $vendors,
        ])->header('Content-Type', 'text/xml');
    }

    public function categories() {
        $categories = Category::where('parent', 0)->get(); 
        
        foreach ($categories as &$category) {
            $category->subCategories = Category::where('parent', $category->id)->get(); 
            
            foreach ($category->subCategories as $sub) {
                $sub->thirdcategory = Category::where('parent', $sub->id)->get();  
            }
            
        } 
        return response()->view('sitemap.categories', [
            'categories' => $categories,
        ])->header('Content-Type', 'text/xml');
    }
}
