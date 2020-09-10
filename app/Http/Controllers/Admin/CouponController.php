<?php

namespace App\Http\Controllers\Admin;

use App\Enumeration\CouponType;
use App\Model\Coupon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
class CouponController extends Controller
{
    public function index() {
        $coupons = Coupon::orderBy('created_at', 'desc')->where('user_id', Auth::user()->id)->paginate(10);
        $header_desc = DB::table('header_desc')->first();
        $minimum_amount_data = DB::table('bonus_amount')->first();
        // dd($coupons);
        return view('admin.dashboard.coupon.index', compact( 'coupons','header_desc','minimum_amount_data'))->with('page_title', 'Coupon');
    }

    public function addPost(Request $request) {
        $rules = [
            'name' => 'required|max:191',
            'description' => 'nullable|max:191',
        ];

        if ($request->type != CouponType::$FREE_SHIPPING)
            $rules['amount'] = 'required|numeric';

        $request->validate($rules);

        $amount = null;

        if ($request->type != CouponType::$FREE_SHIPPING)
            $amount = $request->amount;

        Coupon::create([
            'name' => $request->name,
            'type' => $request->type,
            'amount' => $amount,
            'user_id' => Auth::user()->id,
            'multiple_use' => $request->multipleUse,
            'description' => $request->description,
        ]);

        return redirect()->route('admin_coupon')->with('message', 'Coupon Added!');
    }

    public function editPost(Request $request) {
        $rules = [
            'name' => 'required|max:191',
            'description' => 'nullable|max:191',
        ];

        if ($request->type != CouponType::$FREE_SHIPPING)
            $rules['amount'] = 'required|numeric';

        $request->validate($rules);

        $coupon = Coupon::where('id', $request->couponId)->first();

        $amount = null;

        if ($request->type != CouponType::$FREE_SHIPPING)
            $amount = $request->amount;

        $coupon->name = $request->name;
        $coupon->type = $request->type;
        $coupon->amount = $amount;
        $coupon->multiple_use = $request->multipleUse;
        $coupon->description = $request->description;
        $coupon->save();

        return redirect()->route('admin_coupon')->with('message', 'Coupon Updated!');
    }

    public function delete(Request $request) {
        Coupon::where('id', $request->id)->delete();
    }

    public function add_header_short_desc(Request $request){
        $rules = [
            'short_desc' => 'required'
        ];
        $request->validate($rules);

        if($request->short_desc){
            DB::table('header_desc')->truncate();

            $result = DB::table('header_desc')->insert(['short_desc' => $request->short_desc]);
            if($result){
                return redirect()->back()->with('message','Description Updated');
            }
        }
    }
    public function update_minimum_bonus_amount(Request $request){
        $rules = [
            'minimum_amount' => 'required|numeric',
            'bonus_amount' => 'required|numeric',
        ];
        $request->validate($rules);
        
        if($request->minimum_amount){
            DB::table('bonus_amount')->truncate();
            $result = DB::table('bonus_amount')->insert(['minimum_amount' => $request->minimum_amount,'type'=>$request->type,'bonus_amount'=>$request->bonus_amount]);
            if($result){
                return redirect()->back()->with('message','Data Updated');
            }
        }
    }


}
