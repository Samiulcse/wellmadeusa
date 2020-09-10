<?php
use App\Enumeration\Availability;
use App\Enumeration\Role;
?>

@extends('layouts.app')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/fotorama.css') }}">
@stop

@section('content')
    <div class="container-fluid">
        {{--@if ($topBannerUrl != '')--}}
            {{--<div class="row">--}}
                {{--<div class="col-md-12">--}}
                    {{--<a href="{{ route('vendor_or_parent_category', ['text' => changeSpecialChar($topBannerVendor)]) }}">--}}
                        {{--<img src="{{ $topBannerUrl }}" width="100%">--}}
                    {{--</a>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--@endif--}}
        <div class="content col-md-12">
            <div class="row">
                <div class="col-md-8">
                    <h4>Best Seller</h4>
                    <div class="title-desc">
                        <span id="totalItem"></span>
                        <span>Items Found</span>
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    <div class="column">
                        <div class="shop-sorting">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{ Breadcrumbs::render('best_seller_page') }}
    </div>
    <div class="container-fluid">
        <div class="col-md-12">
            <div class="row">
                <div class="sidebar col-xl-2 col-lg-2 order-lg-1">
                    <button class="sidebar-toggle position-left" data-toggle="modal" data-target="#modalShopFilters"><i class="icon-layout"></i></button>
                    <aside class="sidebar-offcanvas">
                        <!-- Widget Categories-->
                        <section class="content row widget widget-categories">
                            <h3 class="widget-title">CATEGORY</h3>
                            <ul>
                                @foreach($categories as $cat)
                                    <li><a href="{{ route('category_page', ['category' => changeSpecialChar($cat->name)]) }}">{{ $cat->name }} ({{ $cat->count }})</a></li>
                                @endforeach
                            </ul>
                        </section>

                    </aside>
                </div>
                <div class="content col-xl-10 col-lg-10 order-lg-2">
                    @if (sizeof($bestItems) > 0)
                        <section class="section-new-arrival">
                            <ul class="product-container-5x">

    				            <?php $s = (sizeof($bestItems) >= 5 ? 5 : sizeof($bestItems)); ?>

                                @for($i=0; $i<$s; $i++)
    					            <?php $item = $bestItems[$i]; ?>
                                    <li>
                                        <div class="product-card reveal">
                                            <a class="product-thumb" href="{{ route('item_details_page', ['item' => $item->id]) }}">
                                                @if (sizeof($item->images) > 0)
                                                    <img src="{{ asset($item->images[0]->list_image_path) }}" alt="{{ $item->style_no }}">
                                                @else
                                                    <img src="{{ asset('images/no-image.png') }}" alt="Product">
                                                @endif
                                                    @if (sizeof($item->images) > 1)

                                                        <div class="hidden">
                                                            <img class="product-image2" src="{{ asset($item->images[1]->list_image_path) }}" alt="{{ $item->name }}">
                                                        </div><!-- end of .hidden -->
                                                    @endif
                                            </a>

                                            @if (Auth::check() && Auth::user()->role == Role::$BUYER)
                                                <div class="btnWishlist {{ in_array($item->id, $wishListItems) ? 'btnRemoveWishList' : 'btnAddWishList' }}" data-id="{{ $item->id }}">
                                                    @if (in_array($item->id, $wishListItems))
                                                        <i class="fas fa-heart"></i>
                                                    @else
                                                        <i class="far fa-heart"></i>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="caption">
                                                <div class="centered show_quick_view" data-pid="{{$item->id}}">
                                                    Quick View
                                                </div><!-- end of .centered -->
                                            </div><!-- end of .caption -->

                                            <div class="product-title">
                                                <a href="{{ route('item_details_page', ['item' => $item->id]) }}" class="vendor-name">{{ $item->name }}</a>
                                            </div>
                                            @if (Auth::check() && Auth::user()->role == Role::$BUYER)
                                            <h3 class="product-title">
                                                <a class="style-no" href="{{ route('item_details_page', ['item' => $item->id]) }}">{{ $item->style_no }}</a>
                                                    <span class="price">
                                                    @if ($item->orig_price != null)
                                                            <del>${{ number_format($item->orig_price, 2, '.', '') }}</del>
                                                        @endif
                                                        ${{ sprintf('%0.2f', $item->price) }}
                                                </span>
                                            </h3>
                                            @endif

                                            <div class="product-extra-info">
                                                @if (sizeof($item->colors) > 1)
                                                    <img class="multi-color" src="{{ asset('images/multi-color.png') }}" title="Multi Color Available">
                                                @endif

                                                @if ($item->availability == Availability::$ARRIVES_SOON && $item->available_on != null)
                                                    <span title="Available On">
                                                    <img class="calendar-icon" src="{{ asset('images/calendar-icon.png') }}"> {{ date('m/d/Y', strtotime($item->available_on)) }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endfor
                            </ul>

                            @if (sizeof($bestItems) > 5)
                                <ul class="product-container-5x">
                                    @for($i=6; $i<sizeof($bestItems); $i++)
    						            <?php $item = $bestItems[$i]; ?>
                                        <li>
                                            <div class="product-card reveal">
                                                <a class="product-thumb" href="{{ route('item_details_page', ['item' => $item->id]) }}">
                                                    @if (sizeof($item->images) > 0)
                                                        <img src="{{ (!auth()->user()) ? $defaultItemImage_path : asset($item->images[0]->list_image_path) }}" alt="Product">
                                                    @else
                                                        <img src="{{ asset('images/no-image.png') }}" alt="Product">
                                                    @endif
                                                        @if (sizeof($item->images) > 1)

                                                            <div class="hidden">
                                                                <img class="product-image2" src="{{ (!auth()->user()) ? $defaultItemImage_path : asset($item->images[1]->list_image_path) }}" alt="{{ (!auth()->user()) ? config('app.name') : $item->name }}">
                                                            </div><!-- end of .hidden -->
                                                        @endif
                                                </a>
                                                <div class="caption">
                                                    <div class="centered show_quick_view" data-pid="{{$item->id}}">
                                                        Quick View
                                                    </div><!-- end of .centered -->
                                                </div><!-- end of .caption -->

                                                <div class="product-title">
                                                    <a href="{{ route('item_details_page', ['item' => $item->id]) }}" class="vendor-name">{{ $item->name }}</a>
                                                </div>
                                                @if (Auth::check() && Auth::user()->role == Role::$BUYER)
                                                <h3 class="product-title">
                                                    <a class="style-no" href="{{ route('item_details_page', ['item' => $item->id]) }}">{{ $item->style_no }}</a>
                                                        <span class="price">
                                                        @if ($item->orig_price != null)
                                                                <del>${{ number_format($item->orig_price, 2, '.', '') }}</del>
                                                            @endif
                                                            ${{ sprintf('%0.2f', $item->price) }}
                                                    </span>
                                                </h3>
                                                @endif

                                                <div class="product-extra-info">
                                                    @if (sizeof($item->colors) > 1)
                                                        <img class="multi-color" src="{{ asset('images/multi-color.png') }}" title="Multi Color Available">
                                                    @endif

                                                    @if ($item->availability == Availability::$ARRIVES_SOON && $item->available_on != null)
                                                        <span title="Available On">
                                                        <img class="calendar-icon" src="{{ asset('images/calendar-icon.png') }}"> {{ date('m/d/Y', strtotime($item->available_on)) }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @endfor
                                </ul>
                            @endif
                        </section>
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop

@section('modal')
<div class="modal fade" id="quickViewArea" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="product-img-box">
                            <div id="fotormaPopup" class="fotorama" data-nav="thumbs" data-thumbwidth="80" data-thumbheight="120" data-width="100%"></div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="pt-1 mb-2"><span class="text-medium stock_item_preview">Stock:</span>

                        </div>
                        <div class="product-title"><h1></h1></div>
                        <div class="product-sku"><h2 class="text-normal"><span class="text-medium">Style#:</span><span class="product_style_sku"></span> </h2>
                        </div>
                        <div class="product_price"></div>

                        <div class="modal_color_table">

                        </div>

                        <div class="d-flex flex-wrap justify-content-between">
                            <div class="sp-buttons mt-2 mb-2">
                                @if (Auth::check() && Auth::user()->role == Role::$BUYER)
                                    <button class="btn btn-primary" id="btnAddToCart"><i class="icon-bag"></i> Add to Shopping Bag</button>

                                    <a href="{{ route('show_cart') }}" class="btn btn-secondary"> Checkout</a>
                                @else
                                    <a href="/login" class="btn btn-primary">Login to Add to Cart</a>
                                @endif
                            </div>
                        </div>

                        <input type="hidden" id="itemInPack">
                        <input type="hidden" id="itemPrice">
                        <input type="hidden" id="modalItemId">
                        @if (Auth::check() && Auth::user()->role == Role::$BUYER)
                            <input type="hidden" id="loggedIn" value="true">
                        @else
                            <input type="hidden" id="loggedIn" value="false">
                        @endif

                        <br>
                        <h4>Description</h4>
                        <p class="text-xs description"> Description</p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('mobile_filter')
    <div class="modal fade" id="modalShopFilters" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Filters</h4>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <section class="widget widget-categories">
                        <h3 class="widget-title">CATEGORY</h3>
                        <ul>
                            @foreach($categories as $cat)
                                <li><a href="{{ route('category_page', ['category' => changeSpecialChar($cat->name)]) }}">{{ $cat->name }} ({{ $cat->count }})</a></li>
                            @endforeach
                        </ul>
                    </section>

                    <!-- Widget Brand Filter-->
                    {{--<section class="widget widget-categories">--}}
                        {{--<h3 class="widget-title">{{ $category->name }} VENDOR</h3>--}}
                        {{--<ul>--}}
                            {{--@foreach($vendors as $vendor)--}}
                                {{--<li><a href="{{ route('vendor_or_parent_category', ['text' => changeSpecialChar($vendor->company_name)]) }}">{{ $vendor->company_name }}</a></li>--}}
                            {{--@endforeach--}}
                        {{--</ul>--}}
                    {{--</section>--}}
                </div>
            </div>
        </div>
    </div>
@stop

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/slider.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/fotorama.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var message = '{{ session('message') }}';

        if (message != '')
            toastr.success(message);

        // WishList
        $(document).on('click', '.btnAddWishList', function () {
            var id = $(this).data('id');
            $this = $(this);

            $.ajax({
                method: "POST",
                url: "{{ route('add_to_wishlist') }}",
                data: { id: id }
            }).done(function( data ) {
                toastr.success('Added to Wish List.');

                $this.removeClass('btnAddWishList');
                $this.addClass('btnRemoveWishList');
                $this.html('<i class="fas fa-heart"></i>');
            });
        });

        // Click to Show Quick View Functionalities
        $(document).on('click', '.show_quick_view', function () {
            var pid = $(this).attr('data-pid');
            $.ajax({
                method: "POST",
                url: "{{ route('quick_view_item') }}",
                data: {item: pid}
            }).done(function (data) {
                if(data.item){
                    var item = data.item;
                    // var $fotoramaDiv = $('.fotorama').fotorama();
                    // var fotorama = $fotoramaDiv.data('fotorama');
                    // if(fotorama){
                    //     fotorama.destroy();
                    // }
                    if(item && item.images && item.images.length){
                        $('#itemPrice').val(item.price);
                        $('#modalItemId').val(item.id);
                        var loggedIn = $('#loggedIn').val();
                        var lPriceRow, ltPriceRow;

                        if(loggedIn && loggedIn === 'true'){
                            lPriceRow = '<span class="price">$0.00</span> <input class="input-price" type="hidden" value="0">';
                            ltPriceRow = '<b><span id="totalPrice">$0.00</span></b>';
                        }else{
                            lPriceRow = '<span class="login">$xxx</span>';
                            ltPriceRow = '<b><span id="loginrequire">$xxx</span></b>';
                        }

                        // Table Maker
                        var cTable = '<table class="table table-bordered">\n' +
                            '<thead class="bgfame">\n' +
                            '<tr>' +
                            '<th></th><th>Color</th>';

                        cTable+='<th width="20%">Min Qty</th>' +
                            '<th class="hidden-sm-down" width="20%">Qty</th>' +
                            '<th width="20%">Amount</th>' +
                            '</tr>' +
                            '</thead>' +
                            '<tbody class="text-sm">';

                        $.each(item.colors, function (i, color) {
                            cTable+='<tr>';

                            if (color.image != '')
                                cTable+='<td><a href="" data-index="'+color.image_index+'" class="btnThumb"><img src="'+color.image+'" style="height: 50px"></a></td>';
                            else
                                cTable+='<td></td>';

                            cTable+='<td>'+color.name+'</td>';
                            cTable+='<td>'+item.min_qty+'</td>';
                            cTable+='<td><input class="form-control pack" data-color="'+color.id+'" name="input-pack[]" type="text"></td>';
                            cTable+='<td>'+ lPriceRow +'</td>';
                            cTable+='</tr>';
                        });

                        cTable+='<tr>';
                        cTable+='<td colspan="3"><b>Total</b></td>' +
                            '<td><b><span id="totalQty">0</span></b></td>' +
                            '<td>'+ltPriceRow+'</td>';
                        cTable+='</tr>';


                        cTable+='</tbody>' +
                            '</table>';

                        $('.modal_color_table').html(cTable);


                        $('.product_style_sku').html(item.style_no);

                        if (item.price != '') {
                            if (item.orig_price) {
                                $('.product_price').html('<span class="h2 d-block"> <del>$' + parseFloat(item.orig_price).toFixed(2) + '</del> $' + parseFloat(item.price).toFixed(2) + '</span>');
                            } else {
                                $('.product_price').html('<span class="h2 d-block">$' + parseFloat(item.price).toFixed(2) + '</span>');
                            }
                        }

                        var imgData = item.images.map(function (image) {
                            return {img: image.image_path, thumb: image.thumbs_image_path, full: image.image_path};
                        });

                        // new fotorama load
                        $imgSlider = '<div id="fotormaPopup" class="fotorama" data-nav="thumbs" data-thumbwidth="80" data-thumbheight="120" data-width="100%">';
                        item.images.forEach(function (image) {
                            $imgSlider+= '<a href="'+image.image_path+'"><img src="'+image.image_path+'"></a>';
                        });
                        $imgSlider+= '</div>';
                        $('.product-img-box').html($imgSlider);

                        $('#fotormaPopup').fotorama();

                        // new fotorama load


                        // setTimeout(function () {
                        //     $('.fotorama').fotorama().data('fotorama').resize({width: '100%'});
                        // }, 1000);

                        // $.each(item.images, function (index, image) {
                        //     $('.product-img-box .fotorama').html('<a href="'+image.image_path+'"><img src="'+image.list_image_path+'"></a>');
                        // });
                        $('.product-title h1').html(item.name);
                        var itemAvailability = ['Unspecified', 'Arrives soon / Back Order', 'In Stock'];
                        $('.stock_item_preview').html('Stock: ' + itemAvailability[item.availability])
                        $('#quickViewArea .description').html(item.description)
                    }
                    setTimeout(function () {
                        $('#quickViewArea').modal('show');
                    });
                }
            });

        });

        $(document).on('click', '.btnThumb', function (e) {
            e.preventDefault();
            var index = parseInt($(this).data('index'));

            var $fotoramaDiv = $('#fotormaPopup').fotorama();
            var fotorama = $fotoramaDiv.data('fotorama');

            fotorama.show(index);
        });

        var totalQty = 0;
        $(document).on('keyup', '.pack', function () {
            var i = 0;
            var val = $(this).val();

            if (isInt(val)) {
                i = parseInt(val);

                if (i < 0)
                    i = 0;
            }

            var perPrice = $('#itemPrice').val();

            $(this).closest('tr').find('.qty').html(i);
            $(this).closest('tr').find('.price').html('$' + (i * perPrice).toFixed(2));
            $(this).closest('tr').find('.input-price').val(i * perPrice);

            calculate();

            $(this).focus();
        });
        function isInt(value) {
            return !isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value))
        }
        function calculate() {
            totalQty = 0;
            var totalPrice = 0;

            $('.pack').each(function () {
                i = 0;
                var val = $(this).val();

                if (isInt(val)) {
                    i = parseInt(val);

                    if (i < 0)
                        i = 0;
                }

                totalQty += i;
            });

            $('.input-price').each(function () {
                totalPrice += parseFloat($(this).val());
            });

            $('#totalQty').html(totalQty);
            $('#totalPrice').html('$' + totalPrice.toFixed(2));
        }
        $(document).on('click', '#btnAddToCart', function () {
            var colors = [];
            var qty = [];
            var vendor_id = '';


            if (totalQty == 0) {
                alert('Please select an item.');
                return;
            }

            var valid = true;
            $('.pack').each(function () {
                var i = 0;
                var val = $(this).val();

                if (isInt(val)) {
                    i = parseInt(val);

                    if (i < 0)
                        return valid = false;
                } else {
                    if (val != '')
                        return valid = false;
                }

                if (i != 0) {
                    colors.push($(this).data('color'));
                    qty.push(i);
                }
            });

            if (!valid) {
                alert('Invalid Quantity.');
                return;
            }

            var itemId = $('#modalItemId').val();

            $.ajax({
                method: "POST",
                url: "{{ route('add_to_cart') }}",
                data: { itemId: itemId, colors: colors, qty: qty, vendor_id: vendor_id }
            }).done(function( data ) {
                if (data.success)
                    window.location.replace("{{ route('add_to_cart_success') }}");
                else
                    alert(data.message);
            });
        });

        $(document).on('click', '.btnRemoveWishList', function () {
            var id = $(this).data('id');
            $this = $(this);

            $.ajax({
                method: "POST",
                url: "{{ route('remove_from_wishlist') }}",
                data: { id: id }
            }).done(function( data ) {
                toastr.success('Remove from Wish List.');

                $this.removeClass('btnRemoveWishList');
                $this.addClass('btnAddWishList');

                $this.html('<i class="far fa-heart"></i>');
            });
        });
    </script>
@stop