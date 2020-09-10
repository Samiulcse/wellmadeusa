<?php

namespace App\Http\Controllers\Buyer;

use App\Model\CartItem;
use App\Model\Item;
use App\Enumeration\Role;
use App\Model\MetaVendor;
use App\Model\ItemInv;
use App\Model\Order;
use App\Model\PromoCodes;
use App\Model\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Session;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
 
        $item = Item::where('status', 1)->where('id', $request->itemId)->first();
        $itemOrder = 0;
        $itemOrder = (int) $request->qty;
        if ( $request->qty) {
            $itemOrder=$request->qty;
        }
        if ($request->colors) {
            $itemInventory = ItemInv::where('item_id', $request->itemId)->where('color_id', $request->colors)->first();
            if (isset($itemInventory) && empty($itemInventory->qty))
                return response()->json(['success' => false, 'message' => 'Item is sold out']);
        }
        $cartItem = CartItem::where([
            ['user_id', Auth::user()->id],
            ['item_id', $request->itemId],
            ['color_id', $request->colors]
        ])->first();

        if ($cartItem) {
            $cartItem->quantity += (int) $request->qty;
            $cartItem->save();
        } else {
            CartItem::create([
                'user_id' => Auth::user()->id,
                'item_id' => $request->itemId,
                'color_id' => $request->colors,
                'size' => $request->size,
                'quantity' => $request->qty,
            ]);
        }
    $count = CartItem::where('user_id',Auth::user()->id)->count();

        return response()->json(['success' => true, 'message' => 'Success','qty'=>$count]);
    }

    public function addToCartSuccess() {
        return back()->with('message', 'Added to cart.');
    }

    public function showCart()
    {    
        $sessionUser = null;
        if (Auth::check() && Auth::user()->role == Role::$BUYER) {
            $sessionUser = Auth::user()->id;
        } else {
            $sessionUser = session('guestKey')[0];
        }

         $temp = [];
        $cartItems = [];
        $vendor = MetaVendor::where('id', 1)->first();  
        $cartItems = CartItem::where('user_id', $sessionUser) 
            ->with('item', 'color','inventory')
            ->get(); 
         
        $PromoCodes = PromoCodes::where('status',1)->first(); 
        return view('pages.cart', compact('cartItems', 'vendor','PromoCodes'))->with('page_title', 'Cart');
    }

    public function updateCart(Request $request) {  
        $data = [];
        for($i=0; $i < sizeof($request->ids); $i++) {
            $ci = CartItem::where('id', $request->ids[$i])->first();

            $c = 0;

            if (isset($data[$ci->item->id]))
                $c = $data[$ci->item->id];

            $data[$i]['id'] = $ci->item->id;
            $data[$i]['color'] = $ci->color_id;
            $data[$i]['qty'] = (int) ($request->qty[$i]) + $c;
        }
        foreach ($data as $arr) {
            $item = Item::where('id', $arr['id'])->first();

            if ($item->min_qty > $arr['qty'])
                return response()->json(['success' => false, 'message' => $item->style_no.' minimum order qty is '. $item->min_qty]);

            $itemInventory = ItemInv::where('item_id', $arr['id'])->where('color_id', $arr['color'])->first();

            if (isset($itemInventory) && $arr['qty'] > $itemInventory->qty)
                return response()->json(['success' => false, 'message' => $item->style_no.'maximum quantity is : '. $itemInventory->qty]);
            
            if (isset($itemInventory) && empty($itemInventory->qty))
                return response()->json(['success' => false, 'message' => 'Item is sold out']);
    
        }

        for($i=0; $i < sizeof($request->ids); $i++) {
            if ($request->qty[$i] == '0')
                CartItem::where('id', $request->ids[$i])->delete();
            else {
                CartItem::where('id', $request->ids[$i])->update(['quantity' => $request->qty[$i]]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Success']);
    }

    public function updateCartSuccess() {
        return back()->with('message', 'Cart Updated!');
    }

    public function deleteCart(Request $request) {
        CartItem::where('id', $request->id)->delete();
    }

    public function deleteCartAll(Request $request) {
        CartItem::where([])->delete();
    }

    public function productCart(){
        return view('pages.cart');
    }
}
