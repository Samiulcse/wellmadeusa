<?php

namespace App\Http\Controllers\Admin;

use Uuid;
use App\Model\Meta;
use App\Model\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ImageOptimizer;
use DB;
use File;
use Image;
use Validator;
class CategoryController extends Controller
{
    public function index() {
        $categories = [];
        $metadata = []; 
        $mname='';
        $mdesc=' ';
        $categoriesCollection = Category::orderBy('sort')->orderBy('name')->get(); 
        foreach($categoriesCollection as $cc) {
        $metadata = Meta::where('category',$cc->id)->first(); 
            if(!empty($metadata)){$mname=$metadata->title;$mdesc=$metadata->description;}  
            if ($cc->parent == 0) { 
                $data = [
                    'id' => $cc->id,
                    'name' => $cc->name, 
                    'image' => $cc->image,
                    'meta_title' => $mname,
                    'meta_desc' => $mdesc
                ]; 
                $subCategories = [];
                foreach($categoriesCollection as $item) { 
                    $metadata2 =  Meta::where('category',$item->id)->first(); 
                    if(!empty($metadata2)){$mname=$metadata2->title;$mdesc=$metadata2->description;}  
                    if ($item->parent == $cc->id) {
                        $data2 = [
                            'id' => $item->id,
                            'name' => $item->name, 
                            'image' => $item->image,
                            'meta_title' => $mname,
                            'meta_desc' => $mdesc
                        ];
                        $data3 = [];
                        foreach($categoriesCollection as $item2) {
                            $metadata3 =Meta::where('category',$item2->id)->first();
                            if(!empty($metadata3)){$mname=$metadata3->title;$mdesc=$metadata3->description;}  

                            if ($item2->parent == $item->id) {
                                $data3[] = [
                                    'id' => $item2->id,
                                    'name' => $item2->name, 
                                    'image' => $item2->image,
                                    'meta_title' => $mname,
                                    'meta_desc' => $mdesc
                                ];
                            }
                        }
                        $data2['subCategories'] = $data3;
                        $subCategories[] = $data2;
                    }
                }

                $data['subCategories'] = $subCategories;
                $categories[] = $data;
            }
        }

        

        return view('admin.dashboard.category.index', compact('categories'))->with('page_title', 'Category');
    }

    public function addCategory(Request $request) {
        if(!empty($request->image)){
            $image_info = getimagesize($request->image);
            $width = $image_info[0];
            $height = $image_info[1];
            $ratio = $width + ($width/2);
            if($ratio != $height){ 
                $rules = [
                    'image' => 'required', 
                ];
                $validator = Validator::make($request->all(), $rules);
                $validator->after(function ($validator) use($request) { 
                    $validator->errors()->add('image', 'Image Ratio Invalid Use "2:3"'); 
                });
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            } 
        }

        $request->validate([
            'category_name' => 'required'
        ]);

        $parentID  = 0;
        if ($request->second_parent != "0")
            $parentID = $request->second_parent;
        elseif ($request->parentID != "0")
            $parentID = $request->parent_category;
            
        $sort = 1;
        $category = Category::where('parent', $parentID)->orderBy('sort', 'desc')->first();

        if ($category)
            $sort = $category->sort + 1;

        // Create slug from categoryname
        $categoryName = $request->category_name;

        $string = trim($categoryName);
        $string = preg_replace('/[^\w-]/', '', $string);
        $string = str_replace(' ', '-', $string);
        $slug = strtolower($string);

        $slugCheck = Category::where('slug', $slug)->first();
        if ( $slugCheck != null ) {
            $duplicateNameCounter = Category::where('name', $categoryName)->count();
            $slug .= '-' . ($duplicateNameCounter + 1);
        }

        $imagePath = null;

        if (!empty($request->image) && $request->image != 'undefined') {

            $filename = Uuid::generate()->string;
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension(); 

            $destinationPath = '/images/category_images';

            $file->move(public_path($destinationPath), $filename.".".$ext);
            $imagePath = $destinationPath."/".$filename.".".$ext; 
        }

        $category = Category::create([
            'name' => $request->category_name,
            'slug' => $slug,
            'parent' => $parentID,
            'sort' => $sort,
            'image' => $imagePath,
            'status' => $request->status,
        ]);

        $meta = Meta::create([
            'category' => $category->id,
            'title' => $request->meta_title, 
            'description' => $request->meta_description
        ]);

        return redirect()->route('admin_category')->with('message', 'Category Added!');
    }

    public function deleteCategory(Request $request) {
        $category = Category::where('id', $request->id)->first();

        if ($category->image != null && File::exists($category->image))
                File::delete(public_path($category->image));

        $category->delete();
    }

    public function Category_Image_delete(Request $request){
        $id = $request->id;

        $data=[ 
              'image'=>  NULL
            ];  
        return DB::table('categories')->where('id','=',$id)->update($data);
    }

    public function updateCategory(Request $request) {
        
        $category = Category::where('id', $request->categoryId)->first();

        $parentID  = 0;
        if ($request->second_parent != "0")
            $parentID = $request->second_parent;
        elseif ($request->parent_category != "0")
            $parentID = $request->parent_category;

        // Create slug from categoryname
        if($category->name != $request->category_name){
            
            $categoryName = $request->category_name;

            $string = trim($categoryName);
            $string = preg_replace('/[^\w-]/', '', $string);
            $string = str_replace(' ', '-', $string);
            $slug = strtolower($string);

            $slugCheck = Category::where('slug', $slug)->first();

            if ( $slugCheck != null ) {
                $duplicateNameCounter = Category::where('name', $categoryName)->count();
                $slug .= '-' . ($duplicateNameCounter + 1);
            }
        }else{
            $slug = $category->slug;
        }

        $imagePath = null;

        if (!empty($request->image) && $request->image != 'undefined') {
            $image_info = getimagesize($request->image);
            $width = $image_info[0];
            $height = $image_info[1];
            $ratio = $width + ($width/2);
            if($ratio != $height){ 
                $rules = [
                    'image' => 'required', 
                ];
                $validator = Validator::make($request->all(), $rules);
                $validator->after(function ($validator) use($request) { 
                    $validator->errors()->add('image', 'Image Ratio Invalid Use "2:3"'); 
                });
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            } 
            
            $filename = Uuid::generate()->string;
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension(); 

            $destinationPath = '/images/category_images';

            $file->move(public_path($destinationPath), $filename.".".$ext);
            $imagePath = $destinationPath."/".$filename.".".$ext;

            $img = Image::make(public_path($imagePath));
            $img->save(public_path($imagePath), 85);
        }

        $category->parent = $parentID;
        $category->name = $request->category_name;
        $category->slug = $slug;
        if(!empty($imagePath)){
            $category->image = $imagePath;
        }
         if(empty($request->image) && $request->delete_image == 1){  
            $category->image = null;
        }
        $category->status = $request->status;
        $category->save();

        $data=[
              'title'=>  $request->meta_title,
              'description'=>  $request->meta_description
            ]; 
         
        $meta_check=Meta::where('category',$request->categoryId)->first();

        if(!empty($meta_check)){
            DB::table('metas')->where('category','=',$request->categoryId)->update($data);
        }else{
           $meta = Meta::create([
                'category' => $request->id,
                'title' => $request->meta_title, 
                'description' => $request->meta_description
            ]); 
        }

        return redirect()->route('admin_category')->with('message', 'Category Updated!');
    }

    public function updateCategoryParent(Request $request) {
        $category = Category::where('id', $request->id)->first();

        $category->parent = $request->parent;
        $category->save();
    }

    public function sortCategory(Request $request) {
        $parentSort = 1;

        foreach($request->itemArray as $parent) {
            Category::where('id', $parent['id'])->update(['sort' => $parentSort, 'parent' => 0]);

            if (isset($parent['children'])) {
                $children1 = 1;

                foreach($parent['children'] as $item) {
                    Category::where('id', $item['id'])->update(['sort' => $children1, 'parent' => $parent['id']]);

                    if (isset($item['children'])) {
                        $children2 = 1;

                        foreach($item['children'] as $item2) {
                            Category::where('id', $item2['id'])->update(['sort' => $children2, 'parent' => $item['id']]);
                            $children2++;
                        }

                    }

                    $children1++;
                }
            }

            $parentSort++;
        }
    }
    public function categoryDetail(Request $request) {
        $category = Category::where('id', $request->id)->with('meta')->first(); 
        return $category->toArray();
    }
}
