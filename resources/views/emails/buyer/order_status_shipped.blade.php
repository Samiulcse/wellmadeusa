<b>Order ID: </b> {{ $order->order_number }} <br>
<b>Order Status: </b> {{ $order->statusText() }} <br>
@if(!empty($order->shipping_cost)) <b>Shipping Cost: </b> {{ $order->shipping_cost}} <br> @endif
@if(!empty($order->tracking_number)) <b>Tracking Nnumber: </b> {{ $order->tracking_number}} <br> @endif