<?php

namespace App\Http\Controllers\Admin;

use App\Model\MasterFabric;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MasterFabricController extends Controller
{
    public function index() {
        $fabrics = MasterFabric::orderBy('name')->get();

        return view('admin.dashboard.master_fabric.index', compact('fabrics'))->with('page_title', 'Master Fabric');
    }

    public function addPost(Request $request) {
        MasterFabric::create([
            'name' => $request->name
        ]);

        return redirect()->route('admin_master_fabric')->with('message', 'Master Fabric Added!');
    }

    public function delete(Request $request) {
        $fabric = MasterFabric::where('id', $request->id)->first();
        $fabric->delete();
    }

    public function update(Request $request) {
        $fabric = MasterFabric::where('id', $request->id)->first();
        $fabric->name = $request->name;
        $fabric->save();

        return redirect()->route('admin_master_fabric')->with('message', 'Master Fabric Updated!');
    }
}
