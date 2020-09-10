<?php use App\Enumeration\OrderStatus; ?>

@extends('layouts.home_layout')

@section('content')
    <section class="my_account_area common_top_margin">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-lg-2">
                    @include('buyer.profile.menu')
                </div>
                <div class="col-md-9 col-lg-8">
                    <div class="account_wrapper account_wrapper_b_t clearfix">
                        <div class="account_inner account_inner_b_t">
                            <h2>Order Details</h2>
                            <hr>
                            <h4>Vendor Order Details - {{ $order->order_number }}</h4>
                            <div class="row mb-5">
                                <div class="col-md-4 text-right offset-md-8">
                                    <table class="table table-bordered">
                                        <tbody><tr>
                                            <th>Order No.</th>
                                            <td>{{ $order->order_number }}</td>
                                        </tr>

                                        <tr>
                                            <th>Order Date</th>
                                            <td>{{ date('F d, Y', strtotime($order->created_at)) }}</td>
                                        </tr>

                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                {{ $order->statusText() }}
                                                @if ($order->status == OrderStatus::$BACK_ORDER && $order->rejected == 0)
                                                    <br>
                                                    <a href="" class="text-success" id="btnApprove">Approve</a>
                                                    <a href="" class="text-danger" id="btnDecline">Decline</a>
                                                @endif

                                            </td>
                                        </tr>
                                        </tbody></table>
                                </div>
                            </div>
                            <div class="row mb-5">
                                <div class="col-md-12">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Shipping Address</th>
                                            <th>Billing Address</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                         <tr>
                                            <td>{{ ($order->shipping_address) ? $order->shipping_address  : '' }}<br>
                                                {{ ($order->shipping_unit) ? $order->shipping_unit . ',' : '' }} {{ ($order->shipping_city) ? $order->shipping_city . ',' : '' }} {{ ($order->shipping_state) ? $order->shipping_state  : '' }}
                                                {{ ($order->shipping_zip) ?  ' - ' . $order->shipping_zip . ',' : '' }} {{ ($order->shipping_country) ? $order->shipping_country  : '' }}
                                            </td>
                                            <td>{{ ($order->billing_address) ? $order->billing_address  : '' }}<br>
                                                {{ ($order->billing_unit) ? $order->billing_unit . ',' : '' }} {{ ($order->billing_city) ? $order->billing_city . ',' : '' }} {{ ($order->billing_state) ? $order->billing_state  : '' }}
                                                {{ ($order->billing_zip) ?  ' - ' . $order->billing_zip . ',' : '' }} {{ ($order->billing_country) ? $order->billing_country  : '' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Phone: </b>{{ ($order->shipping_phone) ?   $order->shipping_phone  : '' }}</td>
                                            <td><b>Phone: </b>{{ ($order->billing_phone) ?  $order->billing_phone  : '' }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tbody><tr>
                                                <th>Shipping Method</th>
                                                <td>{{ $order->shipping }}</td>
                                                <th>Tracking Number</th>
                                                <td><a href="https://www.ups.com/track?tracknum={{ $order->tracking_number }}&requester=WT/trackdetails" target="_blank">{{ $order->tracking_number }}</a></td>
                                                <th>Invoice Number</th>
                                                <td>{{ $order->invoice_number }}</td>
                                            </tr>
                                            </tbody></table>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Image</th>
                                                <th>Style No.</th>
                                                <th class="text-center">Color</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-center">Dispatch</th>
                                                <th class="text-center">Unit Price</th>
                                                <th class="text-center">Amount</th>
                                            </tr>
                                            </thead>

                                            <tbody>
                                            @php ($totalQty = 0)
                                            @foreach($allItems as $item_id => $items)
                                                @php ($totalQty += $items[0]->total_qty)
                                            <tr>
                                                <td rowspan="{{ sizeof($items)  }}">
                                                    @if (sizeof($items[0]->item->images) > 0)
                                                        <img src="{{ asset($items[0]->item->images[0]->thumbs_image_path) }}" alt="Product" style="height: 100px;">
                                                    @else
                                                        <img src="{{ asset('images/no-image.png') }}" alt="Product" style="height: 100px;width: 100px;">
                                                    @endif
                                                </td>

                                                <td rowspan="{{ sizeof($items) }}" class="text-uppercase">
                                                    {{ $items[0]->item->style_no }}
                                                </td>

                                                <td>
                                                    {{ $items[0]->color }}
                                                </td>

                                                <td class="text-center">
                                                    {{ $items[0]->total_qty }}
                                                </td>

                                                <td class="text-center">
                                                    {{ $items[0]->dispatch }}
                                                </td>

                                                <td class="text-center">
                                                    ${{ sprintf('%0.2f', $items[0]->per_unit_price) }}
                                                </td>

                                                <td class="text-center">
                                                    <span>${{ sprintf('%0.2f', $items[0]->amount) }}</span>
                                                </td>
                                            </tr>
                                                @foreach($items as $item)
                                                    @if (!$loop->first)
                                                        <tr>
                                                            <td>
                                                                {{ $item->color }}
                                                            </td>

                                                            <td class="text-center">
                                                                {{ $item->total_qty }}
                                                            </td>

                                                            <td class="text-center">
                                                                {{ $item->dispatch }}
                                                            </td>

                                                            <td class="text-center">
                                                                ${{ sprintf('%0.2f', $item->per_unit_price) }}
                                                            </td>

                                                            <td class="text-center">
                                                                <span>${{ sprintf('%0.2f', $item->amount) }}</span>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="col-md-6 offset-md-6">
                                    <table class="table table-bordered">
                                        <tbody><tr>
{{--                                            <th rowspan="5">Qty Total</th>--}}
{{--                                            <td rowspan="5">{{$totalQty}}</td>--}}
                                            <th>Sub Total</th>
                                            <td>${{ sprintf('%0.2f', $order->subtotal) }}</td>
                                        </tr>

                                        <tr>
                                            <th>Discount</th>
                                            <td>${{ sprintf('%0.2f', $order->discount) }}</td>
                                        </tr>
                                        
                                        @if($order->reward_percantage)
                                        <tr>
                                            <th>Point Discount</th>
                                            <td>${{ sprintf('%0.2f', $order->reward_percantage) }}</td>
                                        </tr>
                                        @endif
                                        @if($order->reward_fixed)
                                        <tr>
                                            <th>Point Discount</th>
                                            <td>${{ sprintf('%0.2f', $order->reward_fixed) }}</td>
                                        </tr>
                                        @endif

                                        @if(!empty($order->store_credit))
                                            <tr>
                                                <th>Store Credit</th>
                                                <td>${{ sprintf('%0.2f', $order->store_credit) }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>Shipping Cost</th>
                                            <td>${{ sprintf('%0.2f', $order->shipping_cost) }}</td>
                                        </tr>


                                        <tr>
                                            <th>Total</th>
                                            <td><b>${{ sprintf('%0.2f', $order->total) }}</b></td>
                                        </tr>
                                        </tbody></table>
                                </div>
                            </div>
                            <div class="row mb-5">
                                <div class="col-md-12">
                                    <p>
                                        <b>Note: </b>
                                        {{ $order->note }}
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="content col-md-6 col-8 col-sm-8 margin-top-1x">
                                    <a class="order_details_btn" href="{{ route('buyer_show_orders') }}">Back To Order List</a>
                                </div>
                                <div class="content col-md-6 col-4 col-sm-4 margin-top-1x">
                                    <a class="order_details_btn float-right custom-float" role="button" data-toggle="modal" data-target="#print-modal">Print</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal" id="print-modal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header shipping_modal_header">
                    <h4 class="modal-title" id="modalLabelSmall">Print</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body shipping_modal_body">
                    <a class="add_cart_btn" href="{{ route('order_invoice_print_pdf', ['order' => $order->id]) }}" target="_blank">Print with Images</a><br><br>
                    <a class="add_cart_btn" href="{{ route('order_invoice_print_pdf_without_image', ['order' => $order->id]) }}" target="_blank">Print without Images</a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('additionalJS')
    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': '{!! csrf_token() !!}'
                }
            });

            $('#btnApprove').click(function (e) {
                e.preventDefault();

                $.ajax({
                    method: "POST",
                    url: "{{ route('order_reject_status_change') }}",
                    data: { id: '{{ $order->id }}', status: 2 },
                }).done(function( data ) {
                    window.location.reload(true);
                });
            });

            $('#btnDecline').click(function (e) {
                e.preventDefault();

                $.ajax({
                    method: "POST",
                    url: "{{ route('order_reject_status_change') }}",
                    data: { id: '{{ $order->id }}', status: 1 },
                }).done(function( data ) {
                    window.location.reload(true);
                });
            });
        });
    </script>
@stop
