<?php use App\Enumeration\OrderStatus; ?>
@extends('layouts.home_layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="my_acc_page">
                <div class="row">
                    @include('buyer.profile.buyer_sidebar')
                    <div class="col-md-8 col-lg-10 col-sm-12 main_page_column">
                        <div  style="background-image: url('../themes/front/images/bg.jpeg');">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-sm-12">
                                    <div class="buyer_dashboard">
                                        <div class="welcome_title">
                                            <p>{{ $user->first_name.' '.$user->last_name }} , Welcome to Your Dashboard ! </p>
                                        </div>
                                        {!! $buyer_home !!}
                                    </div>
                                    <div class="my_page_user_panel m-5 ">
                                        <div class="card card-default custom_panel">
                                            <div class="card-heading custom_heading">Order History</div>
                                            <div class="card-body custom_content">
                                                <div class="table-responsive">
                                                    @if ( $orders->count() > 0 )
                                                    <table class="table table-striped">
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Order #</th>
                                                            <th>Amount</th>
                                                            <th class="text-right">Status</th>
                                                        </tr>
                                                        @foreach($orders as $order)
                                                            <tr>
                                                                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d ')}}</td>
                                                                <td>
                                                                    <a class="theme-anchor-color" href="{{route('show_order_details',$order->id)}}">{{ $order->order_number }} </a>
                                                                </td>
                                                                <td>$ {{ $order->total }}</td>
                                                                <td class="text-right">
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
                                                    </table>
                                                    @else
                                                        <h5>No Order History Found!</h5>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection