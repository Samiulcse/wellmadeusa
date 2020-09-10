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
                            <h2>Reward Points</h2>
                            <hr>
                            <div class="table-responsive">
                                @if ( $orders->count() > 0 )
                                <table class="table table-striped">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Sub Total</th>
                                        <th>Total</th>
                                        <th>Earned Points</th>
                                        <th>Used Points</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                    <?php
                                        $total = 0;
                                        $subTotal = 0;
                                        $pointTotal = 0;
                                        $usedPoint = 0;
                                    ?>
                                    
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>
                                                <a href="{{route('show_order_details',$order->id)}}">{{ $order->order_number }} </a>
                                            </td>
                                            <td>$ {{ number_format($order->subtotal, 2, '.', '') }}</td>
                                            <td>$ {{ number_format($order->total, 2, '.', '') }}</td>
                                            <td>{{ $order->points }}</td>
                                            <td>{{ $order->used_point }}</td>
                                            <td>
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
                                            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('F d ,Y')}}</td>
                                        </tr>
                                        <?php
                                            $total += $order->total;
                                            $subTotal += $order->subtotal;
                                            $pointTotal += $order->points;
                                            $usedPoint += $order->used_point;
                                        ?>
                                    @endforeach
                                        <tr>
                                        	<td>Total</td>
                                        	<td>$ {{ number_format($subTotal, 2, '.', '') }}</td>
                                        	<td>$ {{ number_format($total, 2, '.', '') }}</td>
                                        	<td>{{ $pointTotal }}</td>
                                        	<td colspan="3">{{ $usedPoint }}</td>
                                        </tr>
                                        <tr>
                                        	<td colspan="4">Remaining Points</td>
                                        	<td colspan="2">{{ abs($pointTotal - $usedPoint) }}</td>
                                        	<td colspan="2"></td>
                                        </tr>
                                </table>
                                @else
                                    <h5>Please Order and Get Your Reward Points.</h5>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
 
@endsection