<?php

namespace App\Http\Controllers\Admin;

use App\Enumeration\Role;
use App\Model\Order;
use App\Model\StoreCredit;
use App\Model\StoreCreditTransection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;

class StoreCreditController extends Controller
{
    public function add(Request $request) {
        $order_query = Order::query();

        $order_query->where('id', $request->order_id);

        $order = $order_query->first();

        if (!$order)
            return response()->json(['success' => false, 'message' => 'Order not found.']);


        $validator = Validator::make($request->all(),
            [
                'reason' => 'required',
                'amount' => 'required|numeric',
                're_amount' => 'required|numeric|same:amount',
            ]
        );

        if ($validator->fails())
        {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        StoreCreditTransection::create([
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'reason' => $request->reason,
            'amount'=> $request->amount,
        ]);

        $store = StoreCredit::where('user_id', $order->user_id)->first();

        if ($store) {
            $store->amount += $request->amount;
            $store->save();
            $store->touch();
        } else {
            StoreCredit::create([
                'user_id' => $order->user_id,
                'amount' => $request->amount
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Success']);
    }

    public function view() {
        $storeCredits = StoreCredit::with('user')->paginate(10);

        foreach ($storeCredits as &$storeCredit) {
            $items = StoreCreditTransection::where('user_id', $storeCredit->user_id)
                ->with('order')
                ->orderBy('created_at', 'desc')->get();

            $storeCredit->items = $items;
        }

        return view('admin.dashboard.customer.store_credit', compact('storeCredits'))->with('page_title', 'Store Credit');
    }
    public function update(Request $request) {

        $store_credit = StoreCredit::where('id', $request->store_credit_id)->first();
        $update_store_credit_amount = $store_credit->amount + ($request->StoreCreditAmount);

        if ($update_store_credit_amount < 0){
            return response()->json(['success' => false, 'message' => 'Amount Can Not be less than the Actual Amount']);
        }else {
            if ($store_credit) {
                $store_credit->amount = $update_store_credit_amount;
                $store_credit->save();
                $store_credit->touch();

                StoreCreditTransection::create([
                    'user_id' => $request->user_id,
                    'reason' => $request->StoreCreditReason,
                    'amount'=> $request->StoreCreditAmount,
                ]);
                return response()->json(['success' => true, 'message' => 'Success']);
            }
        }
    }
}
