@extends('layouts.app')

@section('additionalCSS')
    <style>
        .btnApplyCoupon {
            margin-top: 0px;
        }
    </style>
@stop

@section('content')
<form action="{{ route('checkout_complete') }}" method="post">
    @csrf
    <div class="container content">
        <div class="row">
            <div class="col-md-12">
                <div class="checkout-steps">
                    <a>5. Complete</a>
                    <a class="active"><span class="angle"></span>4. Review</a>
                    <a class="completed"><span class="step-indicator icon-circle-check"></span><span class="angle"></span>3. Payment</a>
                    <a class="completed"><span class="step-indicator icon-circle-check"></span><span class="angle"></span>2. Shipping Method</a>
                    <a class="completed"><span class="step-indicator icon-circle-check"></span><span class="angle"></span>1. Address</a>
                </div>
            </div>
        </div>

        <div class="table-responsive shopping-cart">
            <table class="table">
                <thead>
                <tr>
                    <th>Style No.</th>
                    <th class="text-center">Color</th>
                    <th class="text-center">Size</th>
                    <th class="text-center">Pack</th>
                    <th class="text-center">Total Qty</th>
                    <th class="text-center">Unit Price</th>
                    <th class="text-center">Amount</th>
                </tr>
                </thead>

                <tbody>
                <?php $subTotal = 0; ?>
                @foreach($order->cartItems as $item_index => $items)
                    <tr>
                        <td>
                            <div class="product-item">
                                <a class="product-thumb" href="{{ route('item_details_page', ['item' => $items[0]->item->id]) }}">
                                    @if (sizeof($items[0]->item->images) > 0)
                                        <img src="{{ asset($items[0]->item->images[0]->image_path) }}" alt="Product">
                                    @else
                                        <img src="{{ asset('images/no-image.png') }}" alt="Product">
                                    @endif
                                </a>

                                <div class="product-info">
                                    <h4 class="product-title"><a href="{{ route('item_details_page', ['item' => $items[0]->item->id]) }}">{{ $items[0]->item->style_no }}</a></h4>
                                </div>
                            </div>
                        </td>
                        <td class="text-center text-lg text-medium">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th style="border-top: none">&nbsp;</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>{{ $item->color->name }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </td>
                        <td class="text-center text-lg text-medium">
                            <?php
                            $sizes = explode("-", $items[0]->item->pack->name);
                            $itemInPack = 0;

                            for($i=1; $i <= sizeof($sizes); $i++) {
                                $var = 'pack'.$i;

                                if ($items[0]->item->pack->$var != null)
                                    $itemInPack += (int) $items[0]->item->pack->$var;
                            }
                            ?>
                            <table class="table">
                                <thead>
                                <tr>
                                    @foreach($sizes as $size)
                                        <th style="border-top: none">{{ $size }}</th>
                                    @endforeach
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        @for($i=1; $i <= sizeof($sizes); $i++)
                                            <?php $p = 'pack'.$i; ?>
                                            <td>{{ ($items[0]->item->pack->$p == null) ? '0' : $items[0]->item->pack->$p }}</td>
                                        @endfor
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </td>

                        <td class="text-center text-lg text-medium">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th style="border-top: none">&nbsp;</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>
                                            {{ $item->quantity }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </td>

                        <td class="text-center text-lg text-medium">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th style="border-top: none">&nbsp;</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>
                                            <span class="total_qty">{{ $itemInPack * $item->quantity }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </td>
                        <td class="text-center text-lg text-medium">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th style="border-top: none">&nbsp;</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>
                                            ${{ sprintf('%0.2f', $item->item->price) }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </td>
                        <td class="text-center text-lg text-medium">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th style="border-top: none">&nbsp;</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>
                                            <?php $subTotal += $item->item->price * $itemInPack * $item->quantity; ?>
                                            <span class="total_amount">${{ sprintf('%0.2f', $item->item->price * $itemInPack * $item->quantity) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <h4>Note</h4>

        <textarea class="form-control" placeholder="Note" name="note"></textarea>

        <br>

        <div class="shopping-cart-footer">
            <div class="column"></div>
            <div class="column text-lg">
                Subtotal: <span class="text-medium"><b>${{ sprintf('%0.2f', $order->subtotal) }}</b></span><br>
                Shipping Cost: <span class="text-medium"><b>${{ sprintf('%0.2f', $order->shipping_cost) }}</b></span><br>
                Discount: <span class="text-medium"><b>${{ sprintf('%0.2f', $order->discount) }}</b></span><br>
                Store Credit: <span class="text-medium"><b>${{ sprintf('%0.2f', $order->store_credit) }}</b></span><br>
                Total: <span class="text-medium"><b>${{ sprintf('%0.2f', $order->total) }}</b></span><br>
            </div>
        </div>


        <div class="row padding-top-1x mt-3">
            <div class="col-sm-5">
                <h5>Shipping to:</h5>
                <ul class="list-unstyled">
                    <li><span class="text-muted">Client:</span>{{ $order->user->first_name.' '.$order->user->last_name }}</li>
                    <li><span class="text-muted">Address:</span>
                        {{ $order->shipping_address }}, {{ $order->shipping_state }}, {{ $order->shipping_city }}, {{ $order->shipping_country }} - {{ $order->shipping_zip }}<br>
                    </li>
                    <li><span class="text-muted">Phone:</span>{{ $order->shipping_phone }}</li>
                </ul>
            </div>
            <div class="col-sm-5">
                <h5>Payment method:</h5>
                <ul class="list-unstyled">
                    <li><span class="text-muted">Credit Card:</span>{{ $order->card_number }}</li>
                </ul>
            </div>
            <div class="col-sm-2">
                <h4>Total: ${{ sprintf('%0.2f', $order->total) }}</h4>
            </div>
        </div>


        <div class="checkout-footer margin-top-1x">
            <div class="column hidden-xs-down"><a class="btn btn-outline-secondary" href="{{ route('show_payment', ['id' => request()->get('id')]) }}"><i class="icon-arrow-left"></i>&nbsp;Back</a></div>


            <div class="column">
                    <input type="hidden" name="id" value="{{ request()->id }}">
                    <button class="btn btn-primary">Complete Order</button>
            </div>
        </div>
    </div>
</form>
@stop

@section('additionalJS')
    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.btnApplyCoupon').click(function (e) {
                e.preventDefault();

                var orderId = $(this).data('order-id');
                var coupon = $('#coupon_'+orderId).val();

                $.ajax({
                    method: "POST",
                    url: "#",
                    data: { id: orderId, coupon: coupon }
                }).done(function( data ) {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
            });
        });
    </script>
@stop
