<?php

namespace App\Http\Controllers\Admin;

use App\Model\Fabric;
use App\Model\Item;
use App\Model\MadeInCountry;
use App\Model\MasterColor;
use App\Model\MasterFabric;
use App\Model\ProductDetails;
use App\Model\BulletTwo;
use App\Model\BulletThree;
use App\Model\BulletFour;
use App\Model\ItemFitSize;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class ItemSettingsOthersController extends Controller
{
    public function index() {
        $madeInCountries = MadeInCountry::orderBy('name')->get();
        $fabrics = Fabric::orderBy('name')->with('masterFabric')->get();
        $masterFabrics = MasterFabric::orderBy('name')->get();
        $productDetails = ProductDetails::get();
        $bulletTwoDetails = BulletTwo::get();
        $bulletThreeDetails = BulletThree::get();
        $bulletFourDetails = BulletFour::get();

        return view('admin.dashboard.product_settings_other.index', compact('madeInCountries', 'masterFabrics', 'fabrics','productDetails','bulletTwoDetails','bulletThreeDetails','bulletFourDetails'))
            ->with('page_title', 'Other: Fabric, Made In, Supplying Vendor, Default Item Setting','Product details');
    }

    public function madeInCountryAdd(Request $request) {
        if ($request->defaultVal == '1')
            MadeInCountry::where('vendor_meta_id', Auth::user()->vendor_meta_id)->update([ 'default' => 0 ]);

        $country = MadeInCountry::create([
            'name' => $request->name,
            'status' => $request->status,
            'default' => $request->defaultVal,
            'vendor_meta_id' => Auth::user()->vendor_meta_id,
        ]);

        return $country->toArray();
    }

    public function madeInCountryUpdate(Request $request) {
        $country = MadeInCountry::where('id', $request->id)->first();

        if ($request->defaultVal == '1')
            MadeInCountry::where([])->update([ 'default' => 0 ]);

        $country->name = $request->name;
        $country->status = $request->status;
        $country->default = $request->defaultVal;
        $country->save();

        return $country->toArray();
    }

    public function madeInCountryDelete(Request $request) {
        $country = MadeInCountry::where('id', $request->id)->first();
        $country->delete();
    }

    public function madeInCountryChangeStatus(Request $request) {
        $country = MadeInCountry::where('id', $request->id)->first();
        $country->status = $request->status;
        $country->save();
    }

    public function madeInCountryChangeDefault(Request $request) {
        MadeInCountry::where([])->update([ 'default' => 0 ]);
        $country = MadeInCountry::where('id', $request->id)->first();
        $country->default = 1;
        $country->save();
    }

    public function fabricAdd(Request $request) {
        if ($request->defaultVal == '1')
            Fabric::where([])->update([ 'default' => 0 ]);

        $fabric = Fabric::create([
            'name' => $request->name,
            'status' => $request->status,
            'default' => $request->defaultVal,
            'master_fabric_id' => $request->masterFabricId,
            'vendor_meta_id' => Auth::user()->vendor_meta_id,
        ]);

        $fabric->load('masterFabric');

        return $fabric->toArray();
    }

    public function fabricUpdate(Request $request) {
        $fabric = Fabric::where('id', $request->id)->first();

        if ($request->defaultVal == '1')
            Fabric::where([])->update([ 'default' => 0 ]);

        $fabric->name = $request->name;
        $fabric->status = $request->status;
        $fabric->default = $request->defaultVal;
        $fabric->master_fabric_id = $request->masterFabricId;
        $fabric->save();

        $fabric->load('masterFabric');

        return $fabric->toArray();
    }

    public function fabricDelete(Request $request) {
        $fabric = Fabric::where('id', $request->id)->first();
        $fabric->delete();
    }

    public function fabricChangeStatus(Request $request) {
        $fabric = Fabric::where('id', $request->id)->first();
        $fabric->status = $request->status;
        $fabric->save();
    }

    public function fabricChangeDefault(Request $request) {
        Fabric::where([])->update([ 'default' => 0 ]);
        $fabric = Fabric::where('id', $request->id)->first();
        $fabric->default = 1;
        $fabric->save();
    }

    //material and care
    public function materialAdd(Request $request) {

        $material = ProductDetails::create([
            'material_desc' => $request->materialDescription,
            'status' => $request->status
        ]);

        return $material->toArray();
    }

    public function materialUpdate(Request $request) {
        $material = ProductDetails::where('id', $request->id)->first();

        $material->material_desc = $request->materialDescription;
        $material->status = $request->status;
        $material->save();

        return $material->toArray();
    }

    public function materialDelete(Request $request) {
        $material = ProductDetails::where('id', $request->id)->first();
        $material->delete();
    }

    public function materialChangeStatus(Request $request) {
        $material = ProductDetails::where('id', $request->id)->first();
        $material->status = $request->status;
        $material->save();
    }

    //bullet two
    public function bulletTwoAdd(Request $request) {

        $bulletTwo = BulletTwo::create([
            'bullet_two_desc' => $request->bulletTwoDescription,
            'status' => $request->status
        ]);

        return $bulletTwo->toArray();
    }

    public function bulletTwoUpdate(Request $request) {
        $bulletTwo = BulletTwo::where('id', $request->id)->first();

        $bulletTwo->bullet_two_desc = $request->bulletTwoDescription;
        $bulletTwo->status = $request->status;
        $bulletTwo->save();

        return $bulletTwo->toArray();
    }

    public function bulletTwoDelete(Request $request) {
        $bulletTwo = BulletTwo::where('id', $request->id)->first();
        $bulletTwo->delete();
    }

    public function bulletTwoChangeStatus(Request $request) {
        $bulletTwo = BulletTwo::where('id', $request->id)->first();
        $bulletTwo->status = $request->status;
        $bulletTwo->save();
    }
     //bullet Three
    public function bulletThreeAdd(Request $request) {

        $bulletThree = BulletThree::create([
            'bullet_three_desc' => $request->bulletThreeDescription,
            'status' => $request->status
        ]);

        return $bulletThree->toArray();
    }

    public function bulletThreeUpdate(Request $request) {
        $bulletThree = BulletThree::where('id', $request->id)->first();

        $bulletThree->bullet_three_desc = $request->bulletThreeDescription;
        $bulletThree->status = $request->status;
        $bulletThree->save();

        return $bulletThree->toArray();
    }

    public function bulletThreeDelete(Request $request) {
        $bulletThree = BulletThree::where('id', $request->id)->first();
        $bulletThree->delete();
    }

    public function bulletThreeChangeStatus(Request $request) {
        $bulletThree = BulletThree::where('id', $request->id)->first();
        $bulletThree->status = $request->status;
        $bulletThree->save();
    }
     //bullet Four
    public function bulletFourAdd(Request $request) {

        $bulletFour = BulletFour::create([
            'bullet_four_desc' => $request->bulletFourDescription,
            'status' => $request->status
        ]);

        return $bulletFour->toArray();
    }

    public function bulletFourUpdate(Request $request) {
        $bulletFour = BulletFour::where('id', $request->id)->first();

        $bulletFour->bullet_four_desc = $request->bulletFourDescription;
        $bulletFour->status = $request->status;
        $bulletFour->save();

        return $bulletFour->toArray();
    }

    public function bulletFourDelete(Request $request) {
        $bulletFour = BulletFour::where('id', $request->id)->first();
        $bulletFour->delete();
    }

    public function bulletFourChangeStatus(Request $request) {
        $bulletFour = BulletFour::where('id', $request->id)->first();
        $bulletFour->status = $request->status;
        $bulletFour->save();
    }

    public function item_fit_size() {
        $ItemFitSize = ItemFitSize::first();


        return view('admin.dashboard.product_settings_other.item_fit_size', compact('ItemFitSize'))->with('page_title', 'Fit & Size');
    }
    public function item_fit_size_add(Request $request) {

        $ItemFitSize = ItemFitSize::where('id', $request->fit_size_id)->first();
        $ItemFitSize->text = $request->fit_size_text;
        $ItemFitSize->save();
        return redirect()->route('admin_item_fit_size')->with('message', 'Fit and Size Updated!');

    }
}
