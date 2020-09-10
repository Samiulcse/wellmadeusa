<?php use App\Enumeration\PageEnumeration; ?>

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=1228">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Lumiere USA') }}</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('themes/back/css/bootstrap.css') }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('themes/back/css/main.css') }}?id={{ rand() }}">
    <link rel="stylesheet" href="{{ asset('themes/back/css/custom.css') }}?id={{ rand() }}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
    @yield('additionalCSS')

    <style>
        .form-control-feedback{
            color: #d80808 !important;
        }
    </style>
    <script src="{{ asset('themes/back/js/vendor/jquery-3.3.1.min.js') }}"></script>

</head>

<body>
<!-- Header -->
<div class="header_area">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="header_right_menu">
                    <div class="header_nav pull-right">
                        <ul class="nav">
                            <li class="dropdown">
                                <a href="{{ route('admin_message') }}" title="" >  Messages <span class="badge badge-info" id="message_count">{{ $unread_messages }}</span></a>
                            </li>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">My Account</a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul>
                                        <li><a href="#" id="btnLogOut">Logout</a></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>

                        <form id="logoutForm" class="" action="{{ route('logout_admin') }}" method="post">
                            {{ csrf_field() }}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Header -->

<!-- Left Menu -->
<div class="nav-side-menu">
    <div class="brand">
       @if(isset($logo_path))
            @if ($logo_path != '')
                <a href="{{ route('admin_dashboard') }}">
                    <img src="{{ $logo_path }}" class="admin_img" alt="logo">
                </a>
            @endif
       @endif
    </div>
    <i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>

    <div class="menu-list">

        <ul id="menu-content" class="menu-content">
            <?php
                $menu_items = ['Create a New Item', 'Category', 'Color', 'Pack', 'Other: Fabric, Made In, Supplying Vendor, Default Item Setting', 'Item Edit',
                    'Edit All Items', 'Data Import', 'Master Color', 'Master Fabric', 'Sort Items'];
            ?>

            <li data-toggle="collapse" data-target="#Products">
                Products
            </li>
            <ul class="sub-menu collapse show" id="Products">
                <li class="{{ (isset($page_title) && $page_title == 'Data Import') ? 'active' : '' }}">
                    <a href="{{ route('admin_data_import') }}">Data Import</a>
                </li>

                <li class="{{ (isset($page_title) && $page_title == 'Create a New Item') ? 'active' : '' }}">
                    <a href="{{ route('admin_create_new_item') }}">New Products</a>
                </li>

{{--                <li class="{{ (isset($page_title) && $page_title == 'Sort Items') ? 'active' : '' }}">--}}
{{--                    <a href="{{ route('admin_sort_items_view') }}">Sort Items</a>--}}
{{--                </li>--}}

                <?php
                    $sub_menu_items = ['Item Edit', 'Edit All Items'];
                    if(isset($categories)){
                        foreach($categories as $category) {
                            $sub_menu_items[] = $category['name'];

                            if (sizeof($category['subCategories']) > 0) {
                                foreach ($category['subCategories'] as $sub) {
                                    $sub_menu_items[] = $sub['name'];
                                }
                            }
                        }
                    }

                    $title = isset($page_title) ? $page_title : '';

                    /*if (Route::currentRouteName() == 'vendor_edit_item') {
                        $title = request()->route()->parameters['item']->category->name;
                    }*/
                ?>

                <li data-toggle="collapse"
                    data-target="#listProducts"
                    class="{{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? '' : 'collapsed' }} sub_collapse"
                    aria-expanded="{{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? 'true' : 'false' }}">

                    <span id="btnMenuItemList">List Products</span>
                </li>

                <?php $sub_cat_id = Request::segment(4); ?>

                <ul class="sub-menu sub_child_collapse collapse {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? 'show' : '' }}" id="listProducts">
                    @if(isset($categories))
                        @foreach($categories as $category)
                            <?php
                            $subCat = [];

                            foreach ($category['subCategories'] as $sub)
                                $subCat[] = $sub['name'];
                            ?>

                            @if (sizeof($category['subCategories']) > 0)
                                <li data-toggle="collapse"
                                    data-target="#listProductsSub_{{ $category['id'] }}"
                                    class="sub_collapse_type_2 {{ in_array($title, $subCat) ? '' : 'collapsed' }}"
                                    aria-expanded="{{ in_array($title, $subCat) ? 'true' : 'false' }}">

                                    <span class="menu-category-item" data-id="{{ $category['id'] }}">
                                        {{ $category['name'] }}
                                    </span>
                                </li>
                                @if ( $sub_cat_id == $category['id'] )
                                <ul class="sub-menu {{ in_array($title, $subCat) ? 'show' : '' }}" id="listProductsSub_{{ $category['id'] }}">
                                @else
                                <ul class="sub-menu collapse sub_grand_child_collapse' {{ in_array($title, $subCat) ? 'show' : '' }}" id="listProductsSub_{{ $category['id'] }}">
                                @endif
                                    @foreach($category['subCategories'] as $sub)
                                        <li class="{{ (isset($page_title) && $title == $sub['name']) ? 'active' : '' }}">
                                            <a href="{{ route('admin_item_list_by_category', ['category' => $sub['id']]) }}"> {{ $sub['name'] }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <li class="{{ ((isset($page_title) && $title == $category['name']) || in_array($title, $subCat)) ? 'active' : '' }} {{ sizeof($category['subCategories']) > 0 ? 'has-sub-categories' : '' }}" data-id="{{ $category['id'] }}">
                                    <a href="{{ (sizeof($category['subCategories']) > 0) ? 'javascript:;' : route('admin_item_list_by_category', ['category' => $category['id']]) }}"> {{ $category['name'] }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                </ul>

                <?php $sub_menu_items = ['Category','Fit & Size', 'Color', 'Pack', 'Other: Fabric, Made In, Supplying Vendor, Default Item Setting', 'Master Color', 'Master Fabric'] ?>

                <li data-toggle="collapse" data-target="#productSettings" class="sub_collapse {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? '' : 'collapsed' }}">
                    Product Settings
                </li>
                <ul class="sub-menu sub_child_collapse collapse {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? 'show' : '' }}" id="productSettings">
                    <li class="{{ (isset($page_title) && $page_title == 'Category') ? 'active' : '' }}">
                        <a href="{{ route('admin_category') }}">Category</a>
                    </li>
                    <li class="{{ (isset($page_title) && $page_title == 'Master Color') ? 'active' : '' }}">
                        <a href="{{ route('admin_master_color') }}">Master Color</a>
                    </li>
                    <li class="{{ (isset($page_title) && $page_title == 'Color') ? 'active' : '' }}">
                        <a href="{{ route('admin_color') }}">Color</a>
                    </li>
                    <li class="{{ (isset($page_title) && $page_title == 'Pack') ? 'active' : '' }}">
                        <a href="{{ route('admin_pack') }}">Pack</a>
                    </li>

                    <li class="{{ (isset($page_title) && $page_title == 'Master Fabric') ? 'active' : '' }}">
                        <a href="{{ route('admin_master_fabric') }}">Master Fabric</a>
                    </li>

                    <li class="{{ (isset($page_title) && $page_title == 'Other: Fabric, Made In, Supplying Vendor, Default Item Setting') ? 'active' : '' }}">
                        <a href="{{ route('admin_item_settings_others') }}">Others</a>
                    </li>
                    <!--<li class="{{ (isset($page_title) && $page_title == 'Fit & Size') ? 'active' : '' }}">-->
                    <!--    <a href="{{ route('admin_item_fit_size') }}">Fit & Size</a>-->
                    <!--</li>-->
                </ul>
            </ul>

            <?php
                $menu_items = ['New Orders', 'Order Details', 'Confirmed Orders', 'Back Orders', 'Shipped Orders', 'Cancel Orders',
                    'Return Orders', 'All Orders', 'Incomplete Checkouts'];
            ?>

            <li data-toggle="collapse" data-target="#Orders">
                Orders
            </li>
            <ul class="sub-menu collapse show" id="Orders">
                <?php
                    $sub_menu_items = ['New Orders', 'Order Details', 'Confirmed Orders', 'Back Orders', 'Shipped Orders',
                        'Cancel Orders', 'Return Orders', 'All Orders'];
                ?>

                <li data-toggle="collapse" data-target="#allOrders" class="sub_collapse {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? '' : 'collapsed' }}">
                    <span id="btnMenuAllOrders">
                        All Orders
                    </span>
                </li>
                <ul class="sub-menu sub_child_collapse collapse {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? 'show' : '' }}" id="allOrders">
                    <li class="{{ (isset($page_title) && $page_title == 'New Orders') ? 'active' : '' }}">
                        <a href="{{ route('admin_new_orders') }}">New Orders</a>
                    </li>

                    <li class="{{ (isset($page_title) && $page_title == 'Confirmed Orders') ? 'active' : '' }}">
                        <a href="{{ route('admin_confirmed_orders') }}">Confirmed</a>
                    </li>

                    <li class="{{ (isset($page_title) && $page_title == 'Back Orders') ? 'active' : '' }}">
                        <a href="{{ route('admin_backed_orders') }}">Back Ordered</a>
                    </li>

                    <li class="{{ (isset($page_title) && $page_title == 'Shipped Orders') ? 'active' : '' }}">
                        <a href="{{ route('admin_shipped_orders') }}">Shipped</a>
                    </li>

                    <li class="{{ (isset($page_title) && $page_title == 'Cancel Orders') ? 'active' : '' }}">
                        <a href="{{ route('admin_cancelled_orders') }}">Cancelled</a>
                    </li>

                    <li class="{{ (isset($page_title) && $page_title == 'Return Orders') ? 'active' : '' }}">
                        <a href="{{ route('admin_returned_orders') }}">Returned</a>
                    </li>
                </ul>
                <li class="{{ (isset($page_title) && $page_title == 'Create New Order') ? 'active' : '' }}">
                    <a href="{{ route('admin_new_order_create') }}">Create New Order</a>
                </li>

                <li class="{{ (isset($page_title) && $page_title == 'Incomplete Checkouts') ? 'active' : '' }}">
                    <a href="{{ route('admin_incomplete_orders') }}">Incomplete Checkouts</a>
                </li>
            </ul>

            <?php $menu_items = ['Banner Manager','Top Banner', 'Banner Items', 'Main Slider', 'Main Banner','Recommend Item', 'Section Two', 'Home Page Banner','Section Three','Section Four','Notification Banner','Footer Banner'] ?>
            <li data-toggle="collapse" data-target="#bannerManager" class="{{ (isset($page_title) && in_array($page_title, $menu_items)) ? '' : 'collapsed' }}">
                Banner Manager
            </li>
            <ul class="sub-menu collapse {{ (isset($page_title) && in_array($page_title, $menu_items)) ? 'show' : '' }}" id="bannerManager">
                <li class="{{ (isset($page_title) && $page_title == 'Banner Manager') ? 'active' : '' }}">
                    <a href="{{ route('admin_banner') }}">Logo</a>
                </li> 

                <li class="{{ (isset($page_title) && $page_title == 'Main Banner') ? 'active' : '' }}">
                    <a href="{{ route('admin_front_page_banner_items') }}">Main Banner</a>
                </li>
                <li class="{{ (isset($page_title) && $page_title == 'Top Banner') ? 'active' : '' }}">
                    <a href="{{ route('admin_top_banners') }}">Category Top Banner</a>
                </li>

{{--                <li class="{{ (isset($page_title) && $page_title == 'Recommend Item') ? 'active' : '' }}">--}}
{{--                    <a href="{{ route('admin_front_recommend_banner') }}"> Recommend Item Banner</a>--}}
{{--                </li>--}}

                <li class="{{ (isset($page_title) && $page_title == 'Home Page Banner') ? 'active' : '' }}">
                    <a href="{{ route('admin_front_page_banner_two') }}">Home Page Banner</a>
                </li> 

            </ul>

            <?php $menu_items = ['All Customer', 'Block Customers', 'Store Credit','Create Customer'] ?>
            <li data-toggle="collapse" data-target="#customers" class="{{ (isset($page_title) && in_array($page_title, $menu_items)) ? '' : 'collapsed' }}">
                Customers
            </li>
            <ul class="sub-menu collapse {{ (isset($page_title) && in_array($page_title, $menu_items)) ? 'show' : '' }}" id="customers">
                <li class="{{ (isset($page_title) && $page_title == 'All Customer') ? 'active' : '' }}">
                    <a href="{{ route('admin_all_buyer') }}">All Customers</a>
                </li>
                <li class="{{ (isset($page_title) && $page_title == 'Create Customer') ? 'active' : '' }}">
                    <a href="{{ route('customer_create') }}">Create Customer</a>
                </li>
                <li class="{{ (isset($page_title) && $page_title == 'Store Credit') ? 'active' : '' }}">
                    <a href="{{ route('admin_store_credit_view') }}">Store Credit</a>
                </li>
            </ul>

            <?php $menu_items = ['Vendor Information','Admin Message', 'Account Setting', 'Shipping Methods', 'Courier', 'Ship Method','Promotions','Social Links','Social Feeds','Buyer Home','Welcome Notification','Point Syatem'] ?>
            <li data-toggle="collapse" data-target="#administration" class="{{ (isset($page_title) && in_array($page_title, $menu_items)) ? '' : 'collapsed' }}">
                Administration
            </li>
            <ul class="sub-menu collapse {{ (isset($page_title) && in_array($page_title, $menu_items)) ? 'show' : '' }}" id="administration">
                <li class="{{ (isset($page_title) && $page_title == 'Vendor Information') ? 'active' : '' }}">
                    <a href="{{ route('admin_admin_information') }}">Vendor Information</a>
                </li>
                <li class="{{ (isset($page_title) && $page_title == 'Admin Message') ? 'active' : '' }}">
                    <a href="{{ route('admin_message') }}">Admin Message <span class="badge badge-info" id="message_count">{{ $unread_messages }}</span></a>
                </li>
                <li class="{{ (isset($page_title) && $page_title == 'Account Setting') ? 'active' : '' }}">
                    <a href="{{ route('admin_account_setting') }}">Account Setting</a>
                </li>
                <li class="{{ (isset($page_title) && $page_title == 'Courier') ? 'active' : '' }}">
                    <a href="{{ route('admin_courier') }}">Courier</a>
                </li>
                <li class="{{ (isset($page_title) && $page_title == 'Ship Method') ? 'active' : '' }}">
                    <a href="{{ route('admin_ship_method') }}">Ship Method</a>
                </li>
                <li class="{{ (isset($page_title) && $page_title == 'Promotions') ? 'active' : '' }}">
                    <a href="{{ route('admin_promotions') }}">Promotions</a>
                </li>
                <li class="{{ (isset($page_title) && $page_title == 'Point Syatem') ? 'active' : '' }}">
                    <a href="{{ route('admin_point_system') }}">Point System</a>
                </li>
{{--                <li class="{{ (isset($page_title) && $page_title == 'Social Links') ? 'active' : '' }}">--}}
{{--                    <a href="{{ route('admin_social_link') }}">Social Links</a>--}}
{{--                </li>--}}
{{--                <li class="{{ (isset($page_title) && $page_title == 'Social Feeds') ? 'active' : '' }}">--}}
{{--                    <a href="{{ route('admin_social_feed') }}">Social Feed</a>--}}
{{--                </li>--}}
                <!-- <li class="{{ (isset($page_title) && $page_title == 'Header Footer Color') ? 'active' : '' }}">
                    <a href="{{ route('admin_header_footer_color') }}">Header Footer Color</a>
                </li> -->
                <li data-toggle="collapse" data-target="#notification" class="sub_collapse collapsed">
                    Notification
                </li>
                <ul class="sub-menu sub_child_collapse collapse {{ (isset($page_title) && in_array($page_title, $menu_items)) ? 'show' : '' }}" id="notification">
{{--                    <li class="{{ (isset($page_title) && $page_title == 'Buyer Home') ? 'active' : '' }}">--}}
{{--                        <a href="{{ route('admin_buyer_home') }}">Buyer Home Page</a>--}}
{{--                    </li>--}}

                    <li class="{{ (isset($page_title) && $page_title == 'Welcome Notification') ? 'active' : '' }}">
                        <a href="{{ route('admin_welcome_notification') }}">Welcome Notification</a>
                    </li>
                   <li>
                        <a href="{{ route('admin_top_notification') }}">Top Notification</a>
                   </li>
                </ul>
            </ul>

            <?php $menu_items = ['Page/Meta - Home', 'Page/Meta - About Us', 'Page/Meta - Contact Us', 'Page/Meta - Privacy Policy', 'Page/Meta - Return Info','Page/Meta - Terms and Conditions',
                    'Page/Meta - Cookies Policy', 'Page/Meta - Refunds & Replacements','Page/Meta - Size Guide','Page/Meta - Check Orders','Page/Meta - Return Policy','Page/Meta - Shipping',
                    'Page/Meta - Customer Care','Page/Meta - Faqs','Page/Meta - Lookbook','Page/Meta - Show Schedule'] ?>
            <li data-toggle="collapse" data-target="#pages" class="{{ (isset($page_title) && in_array($page_title, $menu_items)) ? '' : 'collapsed' }}">
                Pages
            </li>
            <ul class="sub-menu collapse {{ (isset($page_title) && in_array($page_title, $menu_items)) ? 'show' : '' }}" id="pages">
                <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Home') ? 'active' : '' }}">
                    <a href="{{ route('admin_page_view', ['id' => PageEnumeration::$HOME]) }}">Home</a>
                </li>
                <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Return Info') ? 'active' : '' }}"><a href="{{ route('admin_page_view', ['id' => PageEnumeration::$RETURN_INFO]) }}">Return Info</a></li>
                <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Shipping') ? 'active' : '' }}"><a href="{{ route('admin_page_view', ['id' => PageEnumeration::$SHIPPING]) }}">Shipping</a></li>
                <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Terms and Conditions') ? 'active' : '' }}"><a href="{{ route('admin_page_view', ['id' => PageEnumeration::$TERMS_AND_CONDIOTIONS]) }}">Terms & Conditions</a></li>
                <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - About Us') ? 'active' : '' }}"><a href="{{ route('admin_page_view', ['id' => PageEnumeration::$ABOUT_US]) }}">About Us</a></li>
                <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Contact Us') ? 'active' : '' }}"><a href="{{ route('admin_page_view', ['id' => PageEnumeration::$CONTACT_US]) }}">Contact Us</a></li>
                <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Lookbook') ? 'active' : '' }}"><a href="{{ route('admin_page_view', ['id' => PageEnumeration::$LOOK_BOOK]) }}">Lookbook</a></li>
                <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Show Schedule') ? 'active' : '' }}"><a href="{{ route('admin_page_view', ['id' => PageEnumeration::$SHOW_SCHEDULE]) }}">Show Schedule</a></li>
{{--                <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Customer Care') ? 'active' : '' }}"><a href="{{ route('admin_page_view', ['id' => PageEnumeration::$CUSTOMER_CARE]) }}">Customer Care</a></li>--}}
{{--                <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Privacy Policy') ? 'active' : '' }}"><a href="{{ route('admin_page_view', ['id' => PageEnumeration::$PRIVACY_POLICY]) }}">Privacy Policy</a></li>--}}
{{--                <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Cookies Policy') ? 'active' : '' }}"><a href="{{ route('admin_page_view', ['id' => PageEnumeration::$COOKIES_POLICY]) }}">Cookies Policy</a></li>--}}
{{--                <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Size Guide') ? 'active' : '' }}"><a href="{{ route('admin_page_view', ['id' => PageEnumeration::$SIZE_GUIDE]) }}">Size Guide</a></li>--}}
{{--                <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Check Orders') ? 'active' : '' }}"><a href="{{ route('admin_page_view', ['id' => PageEnumeration::$CHECK_ORDERS]) }}">Check Orders</a></li>--}}
{{--                <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Faqs') ? 'active' : '' }}"><a href="{{ route('admin_page_view', ['id' => PageEnumeration::$FAQ]) }}">FAQs</a></li>--}}
{{--                <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Return Policy') ? 'active' : '' }}"><a href="{{ route('admin_page_view', ['id' => PageEnumeration::$RETURN_POLICY]) }}">Return Policy</a></li>--}}
{{--                <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Refunds & Replacements') ? 'active' : '' }}"><a href="{{ route('admin_page_view', ['id' => PageEnumeration::$REFUNDS]) }}">Refunds & Replacements</a></li>--}}

            </ul>

            <?php $menu_items = ['Item Statistics'] ?>
            <li data-toggle="collapse" data-target="#statistics" class="{{ (isset($page_title) && in_array($page_title, $menu_items)) ? '' : 'collapsed' }}">
                STATISTICS
            </li>
            <ul class="sub-menu collapse {{ (isset($page_title) && in_array($page_title, $menu_items)) ? 'show' : '' }}" id="statistics">
                <li class="{{ (isset($page_title) && $page_title == 'Item Statistics') ? 'active' : '' }}">
                    <a href="{{ route('item_statistics') }}">Item Statistics</a>
                </li>
            </ul>

        </ul>
    </div>
</div>


 <!-- Left Menu -->

<div class="main">
    <div class="main_title">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 no-padding">
                    @if(isset($page_title))
                        <h2>{{ $page_title}}</h2>
                    @else
                        <h2></h2>
                    @endif
                </div>
                <div class="col-6 text-right no-padding">
{{--                    @if ( isset($prev_item) > 0 )--}}
{{--                        <a href="{{ route('admin_edit_item', ['item' => $prev_item]) }}" class="btn btn-secondary">Prev</a>--}}
{{--                    @endif--}}
{{--                    @if ( isset($next_item) > 0 )--}}
{{--                        <a href="{{ route('admin_edit_item', ['item' => $next_item]) }}" class="btn btn-secondary">Next</a>--}}
{{--                    @endif--}}
                </div>
            </div>
        </div>
    </div>

    <div>
        @yield('content')
    </div>
</div>


<!-- global scripts-->
<script src="https://code.jquery.com/jquery-1.12.1.min.js"></script>
<script src="{{ asset('themes/back/js/vendor/popper.min.js') }}"></script>
<script src="{{ asset('themes/back/js/vendor/bootstrap.js') }}"></script>
<script src="{{ asset('themes/back/js/main.js') }}?id={{ rand() }}"></script>
<!--end of global scripts-->
<script>
    $(function () {
        $('#btnLogOut').click(function () {
            $('#logoutForm').submit();
        });

        /*$('#btnMenuItemList').click(function () {
            if ($(this).closest('li').hasClass('active'))
                window.location.replace("{{ route('admin_item_list_all') }}");
        }).on('touchstart', function () {
            if (!$(this).closest('li').hasClass('active'))
                window.location.replace("{{ route('admin_item_list_all') }}");
        });*/

        $('#btnMenuItemList').click(function (event) {
            event.stopImmediatePropagation();
            window.location.replace("{{ route('admin_item_list_all') }}");
        });

        /*$('#btnMenuAllOrders').click(function () {
            if ($(this).closest('li').hasClass('active'))
                window.location.replace("{{ route('admin_all_orders') }}");
        }).on('touchstart', function () {
            if (!$(this).closest('li').hasClass('active'))
                window.location.replace("{{ route('admin_all_orders') }}");
        });*/

        $('#btnMenuAllOrders').click(function (event) {
            event.stopImmediatePropagation();
            window.location.replace("{{ route('admin_all_orders') }}");
        });

        $('.menu-category-item').click(function () {
            var id = $(this).data('id');
            var url = '{{ route('admin_item_list_by_category', ['category' => '']) }}';
            window.location.replace(url + '/' + id);
        });


    });
</script>
@yield('additionalJS')
</body>
</html>
