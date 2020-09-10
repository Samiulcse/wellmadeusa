<?php

namespace App\Http\Controllers\Buyer;

use App\Model\CartItem;
use App\Model\Item;
use App\Model\WishListItem;
use Illuminate\Http\Request;
use App\Model\MetaBuyer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WishListController extends Controller
{
    public function index() {
        $obj = new WishListItem();
        $items = Item::where('status', 1)
            ->whereIn('id', $obj->getItemIds())
            ->with('images')
            ->get();
        $data['buyerAvatar'] = MetaBuyer::where('user_id', auth()->user()->id)->first();
        return view('buyer.profile.wishlist', compact('items'),$data);
    }

    public function mysave(){
        $obj = new WishListItem();
        $items = Item::where('status', 1)
            ->whereIn('id', $obj->getItemIds())
            ->with('images')
            ->get();
        $data['buyerAvatar'] = MetaBuyer::where('user_id', auth()->user()->id)->first();
        return view('buyer.profile.mysave', compact('items'),$data);
    }

    public function addToWishList(Request $request) {
        $item = WishListItem::where('user_id', Auth::user()->id)
            ->where('item_id', $request->id)->first();

        if (!$item) {
            WishListItem::create([
                'user_id' => Auth::user()->id,
                'item_id' => $request->id,
            ]);
        }
    }

    public function removeWishListItem(Request $request) {
        WishListItem::where('item_id', $request->id)
            ->where('user_id', Auth::user()->id)
            ->delete();
    }

    public function itemDetails(Request $request) {
        $items = Item::where('status', 1)
            ->whereIn('id', $request->id)
            ->with('colors','pack')
            ->get();

        //$item->load('images', 'pack', 'colors');

        foreach ($items as $item) {
            foreach ($item->colors as &$color) {
                $thumb = null;
                $index = 0;

                for ($i = 0; $i < sizeof($item->images); $i++) {
                    if ($item->images[$i]->color_id == $color->id) {
                        $thumb = $item->images[$i];
                        $index = $i;
                        break;
                    }
                }

                if ($thumb) {
                    $color->image = asset($thumb->list_image_path);
                    $color->image_index = $index;
                } else {
                    $color->image = '';
                }
            }
        }

        return response()->json($items->toArray());
    }

    public function addToCart(Request $request) {
        //dd($request->all());

        $data = [];
        for($i=0; $i < sizeof($request->ids); $i++) {
            if ($request->colors[$i] != null) {
                $c = 0;

                if (isset($data[$request->ids[$i]]))
                    $c = (int)$data[$request->ids[$i]];

                $data[$request->ids[$i]] = (int)($request->colors[$i]) + $c;
            }
        }



        foreach ($data as $itemId => $q) {
            $item = Item::where('id', $itemId)->first();

            if ($item->min_qty > $q)
                return response()->json(['success' => false, 'message' => $item->style_no.' minimum order qty is '. $item->min_qty]);
        }


        for($i=0; $i < sizeof($request->colors); $i++) {
            if ($request->colors[$i] != null || $request->colors[$i] != '') {
                $count = (int) $request->colors[$i];

                $previous = CartItem::where('item_id', $request->ids[$i])
                    ->where('color_id', $request->colorIds[$i])
                    ->first();

                if ($previous) {
                    $previous->quantity = $previous->quantity + $count;
                    $previous->save();
                } else {
                    CartItem::create([
                        'user_id' => Auth::user()->id,
                        'item_id' => $request->ids[$i],
                        'color_id' => $request->colorIds[$i],
                        'quantity' => $count,
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Success']);
    }
}
