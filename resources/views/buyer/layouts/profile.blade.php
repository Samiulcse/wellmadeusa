@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-2">
            <div class="content">
                <aside class="user-info-wrapper">
                    <div class="user-info">
                        <div class="user-data">
                            <h4>
                                {{ Auth::user()->first_name.' '.Auth::user()->last_name }}</h4><span>Joined {{ date('F d, Y', strtotime(Auth::user()->created_at)) }}</span>
                        </div>
                    </div>
                </aside>
                <nav class="list-group">
                    <a class="list-group-item {{ (Request::route()->getName() == 'buyer_show_orders') ? 'active' : '' }}"
                       href="{{ route('buyer_show_orders') }}"><i class="icon-bag"></i>Orders</a>
                    <a class="list-group-item {{ (Request::route()->getName() == 'view_wishlist') ? 'active' : '' }}"
                       href="{{ route('view_wishlist') }}"><i class="icon-heart"></i>Wishlist</a>
                    <a class="list-group-item {{ (Request::route()->getName() == 'buyer_show_profile') ? 'active' : '' }}"
                       href="{{ route('buyer_show_profile') }}"><i class="icon-head"></i>Profile</a>
                    <a class="list-group-item {{ (Request::route()->getName() == 'buyer_show_address') ? 'active' : '' }}"
                       href="{{ route('buyer_show_address') }}"><i class="icon-map"></i>Addresses</a>
                </nav>
            </div>
        </div>

        <div class="col-lg-10">
            <div class="content">
                @yield('profile_content')
            </div>
        </div>
    </div>
</div>
@stop