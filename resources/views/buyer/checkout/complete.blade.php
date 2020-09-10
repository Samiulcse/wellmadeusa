@extends('layouts.home_layout')

@section('content')

    <section class="static_page common_top_margin">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="static_page_wrapper clearfix">
                        <h2 class="text-center static_title">Thank you for placing an order!</h2>
                        <ul class="contact_list text-center">
                            <li>Your order number is: <a class="theme-anchor-color" href="{{ route('show_order_details', ['order' => $order->id]) }}">{{ $order->order_number }}</a></li>
                            <li>We will process your order soon. Please keep an eye out for a confirmation email.</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </section>
@stop
