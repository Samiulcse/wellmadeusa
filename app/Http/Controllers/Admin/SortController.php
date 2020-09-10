<?php

namespace App\Http\Controllers\Admin;

use App\Model\Category;
use App\Model\Item;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SortController extends Controller
{
    public function index(Request $request) {
        $query = Item::query();

        // Sort
        if ($request->sort) {
            if ($request->sort == '2')
                $query->orderBy('activated_at', 'desc');
            else if ($request->sort == '3')
                $query->orderBy('updated_at', 'desc');
            else {
                $query->orderBy('sorting', 'asc');
                $query->orderBy('activated_at', 'desc');
            }
        } else {
            $query->orderBy('sorting', 'asc');
            $query->orderBy('activated_at', 'desc');
        }

        // Type
        if ($request->a) {
            if ($request->a == '2')
                $query->where('status', 1);
            else if ($request->a == '3')
                $query->where('status', 0);
            else if ($request->a == '1')
                $query->whereIn('status', [0, 1]);
            else
                $query->where('status', 1);
        } else {
            $query->where('status', 1);
        }

        // Category
        if ($request->c1) {
            if ($request->c1 != '0')
                $query->where('default_parent_category', $request->c1);
        }

        if ($request->c2) {
            if ($request->c2 != '0')
                $query->where('default_second_category', $request->c2);
        }

        if ($request->c3) {
            if ($request->c3 != '0')
                $query->where('default_third_category', $request->c3);
        }

        // Per page
        if ($request->p) {
            if ($request->p == '1')
                $items = $query->paginate(50);
            else if ($request->p == '2')
                $items = $query->paginate(100);
            else if ($request->p == '3')
                $items = $query->paginate(150);
            else
                $items = $query->paginate(50);
        } else {
            $items = $query->paginate(50);
        }



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
       
        return view('admin.dashboard.sort_items.index', compact('items', 'defaultCategories'))->with('page_title', 'Sort Items');
    }

    public function save(Request $request) { 
        for($i=0; $i < count($request->ids); $i++) {  
            Item::where('id', $request->ids[$i])->update([
                'sorting' => (int) $request->sort[$i]
            ]);
        } 
        return redirect()->back()->with('message', 'Updated!');
    }
}
