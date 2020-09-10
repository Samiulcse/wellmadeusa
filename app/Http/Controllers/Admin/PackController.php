<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Pack;
use Auth;

class PackController extends Controller
{
    public function index() {
        $packs = Pack::paginate(10);

        return view('admin.dashboard.pack.index', compact('packs'))->with('page_title', 'Pack');
    }

    public function addPost(Request $request) {
        $rules = [
            's1' => 'required',
            'p1' => 'required|integer',
            'description' => 'nullable|max:151'
        ];

        for($i=3; $i<=10; $i++) {
            $tmp = 'p'.$i;

            if ($request->$tmp && $request->$tmp != "") {
                for ($j = 2; $j < $i; $j++) {
                    $rules['p' . $j] = 'required|integer';
                }
            }
        }

        for($i=3; $i<=10; $i++) {
            $tmp = 's'.$i;

            if ($request->$tmp && $request->$tmp != "") {
                for ($j = 2; $j < $i; $j++) {
                    $rules['s' . $j] = 'required';
                }
            }
        }

        $request->validate($rules);

        $packName = $request->s1;

        for($i=2; $i <= 10; $i++) {
            $tmp = 's'.$i;

            if ($request->$tmp != '')
                $packName .= '-'.$request->$tmp;
        }

        if ($request->default) {
            Pack::where([])->update([ 'default' => 0 ]);
        }

        $pack = Pack::create([
            'name' => $packName,
            'status' => $request->status,
            'default' => ($request->default) ? 1 : 0,
            'description' => $request->description,
            'pack1' => $request->p1,
            'pack2' => $request->p2,
            'pack3' => $request->p3,
            'pack4' => $request->p4,
            'pack5' => $request->p5,
            'pack6' => $request->p6,
            'pack7' => $request->p7,
            'pack8' => $request->p8,
            'pack9' => $request->p9,
            'pack10' => $request->p10,
        ]);

        return redirect()->route('admin_pack')->with('message', 'Pack Added!');
    }

    public function editPost(Request $request) {
        $rules = [
            's1' => 'required',
            'p1' => 'required|integer',
            'description' => 'nullable|max:151'
        ];

        for($i=3; $i<=10; $i++) {
            $tmp = 'p'.$i;

            if ($request->$tmp && $request->$tmp != "") {
                for ($j = 2; $j < $i; $j++) {
                    $rules['p' . $j] = 'required|integer';
                }
            }
        }

        for($i=3; $i<=10; $i++) {
            $tmp = 's'.$i;

            if ($request->$tmp && $request->$tmp != "") {
                for ($j = 2; $j < $i; $j++) {
                    $rules['s' . $j] = 'required';
                }
            }
        }

        $request->validate($rules);

        $packName = $request->s1;

        for($i=2; $i <= 10; $i++) {
            $tmp = 's'.$i;

            if ($request->$tmp != '')
                $packName .= '-'.$request->$tmp;
        }

        if ($request->default) {
            Pack::update([ 'default' => 0 ]);
        }

        $pack = Pack::where('id', $request->packId)->first();
        $pack->name = $packName;
        $pack->status = $request->status;
        $pack->default = ($request->default) ? 1 : 0;
        $pack->description = $request->description;
        $pack->pack1 = $request->p1;
        $pack->pack2 = $request->p2;
        $pack->pack3 = $request->p3;
        $pack->pack4 = $request->p4;
        $pack->pack5 = $request->p5;
        $pack->pack6 = $request->p6;
        $pack->pack7 = $request->p7;
        $pack->pack8 = $request->p8;
        $pack->pack9 = $request->p9;
        $pack->pack10 = $request->p10;
        $pack->save();

        return redirect()->route('admin_pack')->with('message', 'Pack Updated!');
    }

    public function delete(Request $request) {
        $pack = Pack::where('id', $request->id)->first();
        $pack->delete();
    }

    public function changeStatus(Request $request) {
        $pack = Pack::where('id', $request->id)->first();
        $pack->status = $request->status;
        $pack->save();
    }

    public function changeDefault(Request $request) {
        Pack::where([])->update([ 'default' => 0 ]);
        Pack::where('id', $request->id)->update([ 'default' => 1 ]);
    }
}
