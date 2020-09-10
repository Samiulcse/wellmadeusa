<?php

namespace App\Http\Controllers\Admin;

use App\Model\AdminShipMethod;
use App\Model\Courier;
use App\Model\ShippingMethod;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShipMethodController extends Controller
{
    public function index() {
        $shipMethods = AdminShipMethod::with('courier')->orderBy('name')->get();
        $couriers = Courier::orderBy('name')->get();

        return view('admin.dashboard.ship_method.index', compact('shipMethods', 'couriers'))->with('page_title', 'Ship Method');
    }

    public function addPost(Request $request) {
        $request->validate([
            'ship_method' => 'required|unique:admin_ship_methods,name',
            'courier' => 'required',
            'fee' => 'nullable|numeric'
        ]);

        AdminShipMethod::create([
            'name' => $request->ship_method,
            'courier_id' => $request->courier,
            'fee' => $request->fee,
        ]);

        return redirect()->route('admin_ship_method')->with('message', 'Ship Method Added!');
    }

    public function update(Request $request) {
        $request->validate([
            'ship_method' => 'required|unique:admin_ship_methods,name,'.$request->shipMethodId,
            'courier' => 'required',
            'fee' => 'nullable|numeric'
        ]);


        AdminShipMethod::where('id', $request->shipMethodId)->update([
            'name' => $request->ship_method,
            'courier_id' =>  $request->courier,
            'fee' => $request->fee,
        ]);

        return redirect()->route('admin_ship_method')->with('message', 'Ship Method Updated!');
    }

    public function delete(Request $request) {
        ShippingMethod::where('ship_method_id', $request->id)->delete();
        AdminShipMethod::where('id', $request->id)->delete();
    }
}
