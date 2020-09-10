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
                            @if(!empty($setting)) <div class="banner"> {!!$setting!!}</div> @endif

                            <h6>HELLO, {{strtoupper($user->first_name )}} {{strtoupper($user->last_name)}}</h6>
                            <h7>From your My Account Dashboard you have the ability to view a snapshot of your recent account activity and update your account information. Select a link below to view or edit information.
                            </h7>
                            </br></br></br>
                            <div class="my_account_right_heading">
                            <h6>RECENT ORDERS</h2>
                            </div>
                            <hr>
                            <div class="table-responsive">
                                @if ( $orders->count() > 0 )
                                    <table class="table table-striped">
                                        <tbody>
                                        <tr>
                                            <th>Date</th>
                                            <th>Order #</th>
                                            <th>Amount</th>
                                            <th >Status</th>
                                        </tr>
                                        @foreach($orders as $order)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d ')}}</td>
                                                <td>
                                                    <a class="theme-anchor-color" href="{{route('show_order_details',$order->id)}}">{{ $order->order_number }} </a>
                                                </td>
                                                <td>$ {{ $order->total }}</td>
                                                <td >
                                                    @if($order->status == 1)
                                                        <span class="label label-info">Init</span>
                                                    @elseif($order->status == 2)
                                                        <span class="label label-info">New</span>
                                                    @elseif($order->status == 3)
                                                        <span class="label label-info">Confirm</span>
                                                    @elseif($order->status == 4)
                                                        <span class="label label-info">Partially Shipped</span>
                                                    @elseif($order->status == 5)
                                                        <span class="label label-info">Fully Shipped</span>
                                                    @elseif($order->status == 6)
                                                        <span class="label label-info">Back Order</span>
                                                    @elseif($order->status == 7)
                                                        <span class="label label-info">Cancelled By Buyer</span>
                                                    @elseif($order->status == 8)
                                                        <span class="label label-info">Cancelled By R3</span>
                                                    @elseif($order->status == 9)
                                                        <span class="label label-info">Cancelled By Agrement</span>
                                                    @elseif($order->status == 10)
                                                        <span class="label label-info">Returned</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                @else
                                    <h5>No Order History Found!</h5>
                                @endif
                            </div>
                            </br></br>

                            <div class="my_account_right_heading">
                            <h6>ACCOUNT INFORMATION</h2>
                            <a href="{{ route('buyer_show_profile') }}">Edit</a>
                            </div>
                            <hr>
                            <div class="account_info_wrapper clearfix">
                                <div class="account_info_box">
                                    <div class="my_account_box_title">

                                    </div>
                                    <p>
                                        {{ $user->first_name.' '.$user->last_name }} <br>
                                        {{ $user->email }} <br>
                                        <a href="{{ route('buyer_show_profile') }}">Change Password</a>
                                    </p>
                                </div>
                            </div>
                            </br></br>
                            <div class="my_account_right_heading">
                            <h6>ADDRESS BOOK</h2>
                            <a href="{{ route('buyer_show_address') }}">Edit </a>
                            </div>
                            <hr>
                            <div class="account_info_wrapper clearfix">
                                <div class="account_info_box">
                                    <div class="my_account_box_title">
                                        <h7>DEFAULT BILLING ADDRESS</h7><br>
                                    </div>
                                    <p>
                                        {{ $user->first_name.' '.$user->last_name }} <br>
                                        {{ $buyer->billing_address }}, {{ $buyer->billing_city }} <br>
                                        {{ ($buyer->state == null) ? $buyer->state_text : $buyer->state->name }} <br>
                                        {{ $buyer->country['name'] }}- {{ $buyer->billing_zip }} <br>
                                        T: {{ $buyer->billing_phone }}
                                    </p>
                                </div>
                                <div class="account_info_box">
                                    <div class="my_account_box_title">
                                        <h7>DEFAULT SHIPPING ADDRESS</h7><br>
                                    </div>
                                    <p>
                                        {{ $user->first_name.' '.$user->last_name }} <br>
                                        {{ $shippingAddress->address }}, {{ $shippingAddress->city }} <br>
                                        {{ ($shippingAddress->state == null) ? $shippingAddress->state_text : $shippingAddress->state->name }} <br>
                                        {{ $shippingAddress->country->name }}- {{ $shippingAddress->zip }} <br>
                                        T: {{ $shippingAddress->shipping_phone }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
