<?php

namespace App\Http\Controllers\Admin;

use App\Model\AdminShipMethod;
use App\Model\Courier;
use App\Model\ShippingMethod;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CourierController extends Controller
{
    public function index() {
        $couriers = Courier::orderBy('name')->get();

        return view('admin.dashboard.courier.index', compact('couriers'))->with('page_title', 'Courier');
    }

    public function addPost(Request $request) {
        $request->validate([
            'courier_name' => 'required|unique:couriers,name',
        ]);

        Courier::create([
            'name' => $request->courier_name,
        ]);

        return redirect()->route('admin_courier')->with('message', 'Courier Added!');
    }

    public function delete(Request $request) {
        $courier = Courier::where('id', $request->id)->first();
        AdminShipMethod::where('courier_id', $courier->id)->delete();
        ShippingMethod::where('courier_id', $courier->id)->delete();
        $courier->delete();
    }

    public function update(Request $request) {
        $request->validate([
            'courier_name' => 'required|unique:couriers,name,'.$request->courierId,
        ]);

        $courier = Courier::where('id', $request->courierId)->first();
        $courier->name = $request->courier_name;
        $courier->save();

        return redirect()->route('admin_courier')->with('message', 'Courier Updated!');
    }
}
