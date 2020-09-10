<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use App\Model\PromoCodes;

class PromoCodesController extends Controller
{
    public function index() 
    {
        $promoCodes = DB::table('promo_codes')->first(); 

        return view('admin.dashboard.promo_codes.index', compact('promoCodes'))->with('page_title', 'Promotion');
    }

    public function addPost(Request $request) {
        $request->validate([
            'title' => 'required',
            'code' => 'required',
            'discount' => 'required|integer',
            'start_date' => 'required',
            'end_date' => 'required'
        ]);

        $title = $request->title;
        $code = $request->code;
        $discount = $request->discount;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $status = isset($request->status) ? $request->status: 0;

        $insertData = [
            'title' => $title,
            'code' => $code,
            'discount' => $discount,
            'start_date' => date('Y-m-d', strtotime($start_date)),
            'end_date' => date('Y-m-d', strtotime($end_date)),
            'status' => $status,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];

        DB::table('promo_codes')->insert($insertData);

        return redirect()->route('admin_promo_codes')->with('message', 'Promo Code Added!');
    }

    public function update(Request $request) {
        $rules = [
            'type' => 'required',
            'status' => 'required',
        ];  
        $request->validate($rules);

        PromoCodes::where('id', $request->id)->update([
            'name' => $request->name,
            'type' => $request->type,
            'amount' => $request->amount,
            'credit' => $request->credit,
            'status' => $request->status,
            'description' => $request->description,
        ]);

 
        return redirect()->route('admin_promo_codes')->with('message', 'Promotion Updated!'); 
    }

    public function delete(Request $request) {
        DB::table('promo_codes')->where('id', $request->id)->delete();
    }
}