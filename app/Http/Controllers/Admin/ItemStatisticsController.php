<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Model\Item;
use App\Model\Category;
use DateTime;
use App\Model\OrderItem;
use App\Model\OrderTotal;
use App\Model\CartTotal;
use App\Model\Order;

class ItemStatisticsController extends Controller
{
    public function index()
    {
        $categories = Category::where('parent', 0)->orderBy('sort')->orderBy('name')->get();

//        $items = Item::with('itemcategory','orders','carts')->where('activated_at', '>=', date('Y-m-d', strtotime('-30 days')))->get();
        $items = Item::with('itemcategory','orders','carts')->get();


        foreach($items as $key=>$item){
            Item::where('id',$item->id)->update([
                'total_amount'=> 0,
                'total_order_qty'=> 0,
                'o_created_at'=>  ''
                ]);
        }
        
        foreach($items as $key=>$item){
            $data = [];

            $cart_item = 0;
            $id = $item->id;
            
            foreach($item->orders as $order){
                $data_item = Item::where('id','=',$order->item_id)->first();
                if( !empty($order->order) && ($order->order->status == 2 || $order->order->status == 3 || $order->order->status == 4 || $order->order->status == 5 || $order->order->status == 6 )){
                    $total_qty = $order->total_qty;
                    $amount = $order->amount;
                    $o_created_at = $order->created_at;
                    Item::where('id',$order->item_id)->update([
                        'total_amount'=> number_format(($data_item->total_amount + $amount), 2, '.', ''),
                        'total_order_qty'=>$data_item->total_order_qty + $total_qty,
                        'o_created_at'=> ($total_qty > 0) ? $o_created_at : ''
                        ]);
                }
            }

            foreach($item->carts as $cart){
                $cart_item += $cart->quantity;
                $c_created_at = $cart->created_at;
            }

            $data =[
                'total_in_cart'=>$cart_item,
                'c_created_at'=> ($cart_item > 0) ? $c_created_at : ''
            ];
            Item::where('id',$id)->update($data);
        } 

         
 
        return view('admin.dashboard.statistics.index',compact('categories'))->with('page_title', 'Item Statistics');
    }

     public function filter(Request $request)
    {
        $query = Item::query();

        if($request->type == 'item_activation'){
            if(!empty($request->period)){
                $query = $this->item_activation($request->period , $query);
            }else{
                $start_date = date("Y-m-d", strtotime($request->start_date));
                $end_date = date("Y-m-d", strtotime($request->end_date));
                $query = $this->item_activation_period($start_date,$end_date, $query);
            }
        }
        
        if($request->type == 'view'){
            if(!empty($request->period)){
                $query = $this->view($request->period , $query);
            }else{
                $start_date = date("Y-m-d", strtotime($request->start_date));
                $end_date = date("Y-m-d", strtotime($request->end_date));
                $query = $this->view_period($start_date,$end_date, $query);
            }
        }

        if($request->type == 'cart'){
            if(!empty($request->period)){
                $query = $this->cart($request->period , $query);
            }else{
                $start_date = date("Y-m-d", strtotime($request->start_date));
                $end_date = date("Y-m-d", strtotime($request->end_date));
                $query = $this->cart_period($start_date,$end_date, $query);
            }
        }

        if($request->type == 'order'){
            if(!empty($request->period)){
                $query = $this->order($request->period , $query);
            }else{
                $start_date = date("Y-m-d", strtotime($request->start_date));
                $end_date = date("Y-m-d", strtotime($request->end_date));
                $query = $this->order_period($start_date,$end_date, $query);
            }
        }

        if(!($request->status == 'all')){
            $query->where('status', $request->status);
        }

        if(!empty($request->style_no)){
            $query->where('style_no', $request->style_no);
        }

        if( $request->category != 'all' ){
            $query->where('default_parent_category', $request->category);
        }

        $query = $this->sort_by( $request->sort , $query);

        $items = $query->with('images')->paginate(15);

        $paginationView = $items->links('others.pagination');
        $paginationView = trim(preg_replace('/\r\n/', ' ', $paginationView));

        return ['items' => $items->toArray(), 'pagination' => $paginationView];
    }

    protected function sort_by( $sort , $query){
        if($sort == 0){
            return $query->orderBy('total_amount','desc');
        }else if($sort == 1){
            return $query->orderBy('total_amount','asc');
        }else if($sort == 2){
            return $query->orderBy('total_order_qty','desc');
        }else if($sort == 3){
            return $query->orderBy('total_order_qty','asc');
        }else if($sort == 4){
            return $query->orderBy('activated_at','desc');
        }else if($sort == 5){
            return $query->orderBy('activated_at','asc');
        }else if($sort == 6){
            return $query->orderBy('view','desc');
        }else if($sort == 7){
            return $query->orderBy('view','asc');
        }else if($sort == 8){
            return $query->orderBy('total_in_cart','desc');
        }else if($sort == 9){
            return $query->orderBy('total_in_cart','asc');
        }
    }

    protected function item_activation($period , $query){
        if($period == 'yesterday'){
          return $query->whereDate('activated_at', Carbon::now()->subDays(1));
        }else if($period == 'this_week'){
          return $query->whereBetween('activated_at',[Carbon::parse('last monday')->startOfDay(),Carbon::now()->endOfDay()]);
        }else if($period == 'this_month'){
            return $query->whereBetween('activated_at',[Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()]);
        }else if($period == 'this_year'){
            return $query->whereYear('activated_at', Carbon::now()->year);
        }else if($period == 'last_week'){
            return $query->whereBetween('activated_at', [Carbon::now()->subWeek()->startOfWeek(),Carbon::now()->subWeek()->endOfWeek()]);
        }else if($period == 'last_month'){
            return $query->whereMonth('activated_at', Carbon::now()->month-1);
        }else if($period == 'last_year'){
            return $query->whereYear('activated_at', Carbon::now()->year-1);
        }else if($period == 'last_7_days'){
          return $query->where('activated_at',  '>' , Carbon::now()->subDays(7)->toDateTimeString());
        }else if($period == 'last_30_days'){
            return $query->where('activated_at',  '>' , Carbon::now()->subDays(30)->toDateTimeString());
        }else if($period == 'last_90_days'){
            return $query->where('activated_at',  '>' , Carbon::now()->subDays(90)->toDateTimeString());
        }else if($period == 'last_365_days'){
            return $query->where('activated_at',  '>' , Carbon::now()->subDays(365)->toDateTimeString());
        }
    }

    protected function item_activation_period($start_date, $end_date , $query){
       if(empty($start_date) && empty($end_date)){
           return $query;
       }else{
           $start_date = $start_date ? $start_date." 00:00:00": Carbon::now();
           $end_date = $end_date ? $end_date." 23:59:59": Carbon::now();
           return $query->whereBetween('activated_at', [$start_date, $end_date]);
       }
    }

    protected function view($period , $query){
        if($period == 'yesterday'){
            return $query->whereDate('v_created_at', Carbon::now()->subDays(1));
        }else if($period == 'this_week'){
            return $query->whereBetween('v_created_at',[Carbon::parse('last monday')->startOfDay(),Carbon::now()->endOfDay()]);
        }else if($period == 'this_month'){
            return $query->whereBetween('v_created_at',[Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()]);
        }else if($period == 'this_year'){
            return $query->whereYear('v_created_at', Carbon::now()->year);
        }else if($period == 'last_week'){
            return $query->whereBetween('v_created_at', [Carbon::now()->subWeek()->startOfWeek(),Carbon::now()->subWeek()->endOfWeek()]);
        }else if($period == 'last_month'){
            return $query->whereMonth('v_created_at', Carbon::now()->month-1);
        }else if($period == 'last_year'){
            return $query->whereYear('v_created_at', Carbon::now()->year-1);
        }else if($period == 'last_7_days'){
            return $query->where('v_created_at',  '>' , Carbon::now()->subDays(7)->toDateTimeString());
        }else if($period == 'last_30_days'){
            return $query->where('v_created_at',  '>' , Carbon::now()->subDays(30)->toDateTimeString());
        }else if($period == 'last_90_days'){
            return $query->where('v_created_at',  '>' , Carbon::now()->subDays(90)->toDateTimeString());
        }else if($period == 'last_365_days'){
            return $query->where('v_created_at',  '>' , Carbon::now()->subDays(365)->toDateTimeString());
        }
    }

    protected function view_period($start_date, $end_date , $query){
       if(empty($start_date) && empty($end_date)){
           return $query;
       }else{
           $start_date = $start_date ? $start_date." 00:00:00": Carbon::now();
           $end_date = $end_date ? $end_date." 23:59:59": Carbon::now();
           return $query->whereBetween('v_created_at', [$start_date, $end_date]);
       }
    }

    protected function cart($period , $query){
        if($period == 'yesterday'){
            return $query->whereDate('c_created_at', Carbon::now()->subDays(1));
        }else if($period == 'this_week'){
           return $query->whereBetween('c_created_at',[Carbon::parse('last monday')->startOfDay(),Carbon::now()->endOfDay()]);
        }else if($period == 'this_month'){
           return $query->whereBetween('c_created_at',[Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()]);
        }else if($period == 'this_year'){
           return $query->whereYear('c_created_at', Carbon::now()->year);
        }else if($period == 'last_week'){
           return $query->whereBetween('c_created_at', [Carbon::now()->subWeek()->startOfWeek(),Carbon::now()->subWeek()->endOfWeek()]);
        }else if($period == 'last_month'){
           return $query->whereMonth('c_created_at', Carbon::now()->month-1);
        }else if($period == 'last_year'){
           return $query->whereYear('c_created_at', Carbon::now()->year-1);
        }else if($period == 'last_7_days'){
           return $query->where('c_created_at',  '>' , Carbon::now()->subDays(7)->toDateTimeString());
        }else if($period == 'last_30_days'){
           return $query->where('c_created_at',  '>' , Carbon::now()->subDays(30)->toDateTimeString());
        }else if($period == 'last_90_days'){
           return $query->where('c_created_at',  '>' , Carbon::now()->subDays(90)->toDateTimeString());
        }else if($period == 'last_365_days'){
           return $query->where('c_created_at',  '>' , Carbon::now()->subDays(365)->toDateTimeString());
        }
    }

    protected function cart_period($start_date, $end_date , $query){
       if(empty($start_date) && empty($end_date)){
           return $query;
       }else{
           $start_date = $start_date ? $start_date." 00:00:00": Carbon::now();
           $end_date = $end_date ? $end_date." 23:59:59": Carbon::now();
           return $query->whereBetween('c_created_at', [$start_date, $end_date]);
       }
    }

    protected function order($period , $query){
        if($period == 'yesterday'){
            return $query->whereDate('o_created_at', Carbon::now()->subDays(1));
        }else if($period == 'this_week'){
           return $query->whereBetween('o_created_at',[Carbon::parse('last monday')->startOfDay(),Carbon::now()->endOfDay()]);
        }else if($period == 'this_month'){
           return $query->whereBetween('o_created_at',[Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()]);
        }else if($period == 'this_year'){
           return $query->whereYear('o_created_at', Carbon::now()->year);
        }else if($period == 'last_week'){
           return $query->whereBetween('o_created_at', [Carbon::now()->subWeek()->startOfWeek(),Carbon::now()->subWeek()->endOfWeek()]);
        }else if($period == 'last_month'){
           return $query->whereMonth('o_created_at', Carbon::now()->month-1);
        }else if($period == 'last_year'){
           return $query->whereYear('o_created_at', Carbon::now()->year-1);
        }else if($period == 'last_7_days'){
           return $query->where('o_created_at',  '>' , Carbon::now()->subDays(7)->toDateTimeString());
        }else if($period == 'last_30_days'){
           return $query->where('o_created_at',  '>' , Carbon::now()->subDays(30)->toDateTimeString());
        }else if($period == 'last_90_days'){
           return $query->where('o_created_at',  '>' , Carbon::now()->subDays(90)->toDateTimeString());
        }else if($period == 'last_365_days'){
           return $query->where('o_created_at',  '>' , Carbon::now()->subDays(365)->toDateTimeString());
        }
    }

    protected function order_period($start_date, $end_date , $query){
       if(empty($start_date) && empty($end_date)){
           return $query;
       }else{
           $start_date = $start_date ? $start_date." 00:00:00": Carbon::now();
           $end_date = $end_date ? $end_date." 23:59:59": Carbon::now();
          return $query->whereBetween('o_created_at', [$start_date, $end_date]);
       }
    }

    function get_quantity_of_color_product(Request $request){
        $data = OrderItem::where('item_id',$request->item_id)->groupBy('color')->get();
        $items = [];
        $OrderItems =[];
        foreach ($data as $key => $value) {
            $total_qty = 0;
            $amount = 0;
            $OrderItems[$value->color] = OrderItem::where('color', $value->color)
                                        ->where('item_id',$request->item_id)->get();

            foreach ($OrderItems[$value->color] as $item) {
                $total_qty +=  $item->total_qty;
                $amount +=  $item->amount;
            }

            $items [] = [
                'color' => $OrderItems[$value->color][0]->color,
                'total_qty' => $total_qty,
                'amount' =>  sprintf('%0.2f', $amount)
            ];
        }
        return $items;
    }

    public function stylenoSearch(Request $request){

        $search = $request->search;
        $items = Item::where('style_no', 'like', '%' .$search . '%')->get();
        $total = $items->count();
        $response = [];
        foreach($items as $item){
            $response[] = ["value"=>$item->style_no , 'total'=>$total];
        }
        echo json_encode($response);
    }
}
