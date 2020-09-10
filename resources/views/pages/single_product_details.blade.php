<?php
use App\Enumeration\Availability;
use App\Enumeration\Role;
?>

@extends('layouts.home_layout')
@section('additionalCSS')
    <link href="https://cdn.lineicons.com/1.0.1/LineIcons.min.css" rel="stylesheet">
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
    <link rel="stylesheet" href=" {{asset('themes/front/css/magnific-popup.css')}}">

    <link rel="stylesheet" href="{{ asset('css/fotorama.css') }}">
@stop
@section('content')
 
<div class="product_single_area">
        <div class="container">
            <div class="row">
                <div class="col-md-10 offset-lg-1">
                    <div class="filter_heading single_page_bradcrumb">
                        <ul>
                            <!--<li><a href="{{ route('home') }}">Home</a></li>-->
                            @if(!empty($parent_category))<li> <a href="{{ route('category_page', ['category' => changeSpecialChar($parent_category['name'])]) }}">{{ $parent_category['name'] }}</a></li>@endif
                            @if(!empty($second_category))<li> <a href="{{ route('second_category', ['category' => changeSpecialChar($second_category['name']), 'parent' => changeSpecialChar($parent_category['name'])]) }}">{{ ucwords(strtolower($second_category['name'])) }}</a></li>@endif
                            @if(!empty($item->name))<li>{{$item->name}} </li> @endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-6 offset-lg-1 col-sm-6">
                    <div class="single_product_left above_mobile" id="single_product_img2">
                        <div class="single_img_thumbnail">
                            <div class="single_img_thumb_wrap" id="single_product_img">
                                <div class="single_img_thumb">
                                    @foreach($color_images as $k => $images)
                                    <div class="slide" href="#sp{{$images->id}}">
                                        @if (Auth::check() && Auth::user()->role == Role::$BUYER)  
                                            <img src="{{ asset($images->thumbs_image_path) }}" alt="{{$item->name}}" class="img-fluid">
                                        @else 
                                            <img src="{{$defaultItemImage_path}}" alt="{{$item->name}}" class="img-fluid ">
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="single_img">
                            @foreach($color_images as $k => $images)
                            <div class="s_on_hover" data-toggle="modal" data-target="#exampleModal">
                                <div id="sp{{$images->id}}" data-id="{{$images->id}}">
                                    @if (Auth::check() && Auth::user()->role == Role::$BUYER)  
                                        <img src="{{ asset($images->image_path) }}" alt="{{$item->name}}" class="img-fluid">
                                    @else 
                                        <img src="{{$defaultItemImage_path}}" alt="{{$item->name}}" class="img-fluid ">
                                    @endif 
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @if(!empty($item->video && $item->default == 1))
                        <div class="video_wrapper">
                            <video  width="100%"  loop muted preload="metadata" autoplay><source class="product-video" src="{{asset($item->video)}}" type="video/mp4"></video>
                        </div>
                        @endif
                    </div>
                    <div class="single_product_left below_mobile" id="single_product_img_mob">
                        <div class="single_img_thumb_mobile">
                            @foreach($color_images as $k => $images)
                            <div class="slide" href="#sp{{$images->id}}">
                                @if (Auth::check() && Auth::user()->role == Role::$BUYER)  
                                    <img src="{{ asset($images->image_path) }}" alt="" class="img-fluid">
                                @else 
                                    <img src="{{$defaultItemImage_path}}" alt="" class="img-fluid ">
                                @endif 
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>
                <div class="col-md-5 col-lg-5   col-sm-6">
                    <div class="single_product_description_wrapper  ">
                        <div class="single_product_description">
                            <p class="available_on_date"> </p>
                            <h2 id="single_item_name" data-itemid="{{$item->id}}">@if(!empty($item->name)){{$item->name}} @endif</h2>
                            <p class="item_syle"><b>Style:</b>#{{$item->style_no}}</p>
                            @if (Auth::check() && Auth::user()->role == Role::$BUYER)
                                
                                <h3 class="price @if(!empty($item->orig_price)) text-danger @endif"> @if($item->orig_price) <del>${{$item->orig_price}}</del> @endif @if(!empty($item->price))${{$item->price}} @endif</h3>
                            @else
                                <h3><a href="{{ route('buyer_login') }}">LOGIN TO SEE PRICE</a></h3>
                            @endif
                            @if(!empty($item->brand))<h4 class="brand"> <b>Brand:</b> {{$item->brand}}<h4> @endif
                            <div class="qty_and_color_wrap"> 
                                <div class="s_p_color">
                                    <p>COLOUR</p>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            @foreach($itemInventories as $preorder)
    
                                                @if($admin_first_color_id == $preorder->color_id)
                                                    @if(!empty($item->colors))
                                                        @foreach($item->colors as $color)
                                                            @if ($color->pivot->available ==1)
                                                                @if($color->id == $admin_first_color_id)
                                                                    <img src="@if(!empty($color->image_path)) {{ asset($color->image_path) }} @else {{ asset('images/no-image.png') }} @endif" alt="" class="img-fluid ">
                                                                    <input type="hidden" class="insNoutStack" data-qty="{{$preorder->qty}}" value="{{$preorder->available_on}}">
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                <span class="active_colorname"> {{ $preorder->color_name }} </span>@endif
                                            @endforeach
                                        </button>
    
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" id="single_color_variation">
                                            @if(!empty($item->colors))
                                                @foreach($item->colors as $color)
                                                    @if ($color->pivot->available ==1)
                                                        @if($color->id == $admin_first_color_id)
                                                            <a class="dropdown-item" href="#" data-colorimg="@if(!empty($color->image_path)) {{ asset($color->image_path) }} @else {{ asset('images/no-image.png') }} @endif" data-colorId = "{{ $color->id}}" data-itemId="{{$item->id}}" data-color_name="{{$color->name}}">
                                                                <img src="@if(!empty($color->image_path)) {{ asset($color->image_path) }} @else {{ asset('images/no-image.png') }} @endif" alt="" class="img-fluid ">
                                                                <span>{{$color->name}}</span>
                                                            </a>
                                                        @else
                                                            <a class="dropdown-item" href="#" data-colorimg="@if(!empty($color->image_path)) {{ asset($color->image_path) }} @else {{ asset('images/no-image.png') }} @endif" data-colorId = "{{ $color->id}}" data-itemId="{{$item->id}}" data-color_name="{{$color->name}}">
                                                                <img src="@if(!empty($color->image_path)) {{ asset($color->image_path) }} @else {{ asset('images/no-image.png') }} @endif" alt="" class="img-fluid">
                                                                <span>{{$color->name}} </span>
                                                            </a>
    
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                        <input type="hidden" id="selectedcolor" value="{{$admin_first_color_id}}">
                                      </div>
                                </div> 
                                
                                <div class="single_page_qty">
                                    <p>Quantity</p>
                                    <div class="num_count">
                                        <input id="select_quantity" type="number" id="quantity" class="qty select_quantity" min="1" value="1" max="5">
                                    </div>
                                </div>
                            </div>
                                <div class="add_to_cart_btn">
                                    <button class="btnAddToCart btn" id="btnAddToCart">Add to Bag</button>
                                </div>
                                <div class="p_size_guide">
                                    <span>SIZE GUIDE</span>
                                </div>
                            <div class="product_description">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="contact-tab" data-toggle="tab" href="#Returns" role="tab" aria-controls="contact" aria-selected="false">DETAILS</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">SHIPPING</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#SizeChart" role="tab" aria-controls="profile" aria-selected="false">RETURN INFO</a>
                                    </li>
                                    
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade " id="home" role="tabpanel" aria-labelledby="home-tab">
                                        @if(!empty($metavendor->shipping))<p>{!! $metavendor->shipping !!}</p>@endif
                                    </div>
                                    <div class="tab-pane fade" id="SizeChart" role="tabpanel" aria-labelledby="profile-tab">
                                        @if(!empty($metavendor->return_policy))<p>{!! $metavendor->return_policy !!}</p>@endif
                                    </div>
                                    <div class="tab-pane fade show active" id="Returns" role="tabpanel" aria-labelledby="contact-tab">
                                        <p>@if(!empty($item->description)){{$item->description}} @endif</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>

    </div>
    <!-- =========================
        END PRODUCT SINGLE SECTION
	============================== -->

@if (sizeof($relatedItem) > 0)
    <section id="related_product_area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="r_p_title">
                        <h2>You may also like…</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="product_main_slide_wrapper">
                    @foreach($relatedItem as $simelar_item)
                        <div class="main_product_inner">
                            <div class="main_product_inner_img clearfix">
                                <a href="{{ route('product_single_page', $simelar_item->slug) }} ">
                                    @if (Auth::check() && Auth::user()->role == Role::$BUYER)   
                                        <img src="{{asset('/'.$simelar_item->images[0]->list_image_path)}}" alt="{{$simelar_item->name}}" class="img-fluid ">
                                    @else 
                                        <img src="{{$defaultItemImage_path}}" alt="{{$simelar_item->name}}" class="img-fluid ">
                                    @endif 
                                </a>
                            </div>
                            <div class="main_product_inner_text">
                                <h2><a href="{{ route('product_single_page', $simelar_item->slug) }}">@if(!empty($simelar_item->style_no)){{$simelar_item->style_no}} @endif</a></h2>
                                @if (Auth::check() && Auth::user()->role == Role::$BUYER)
                                    <p>@if(!empty($simelar_item->price))${{$simelar_item->price}} @endif</p>
                                @else
                                    <a href="{{ route('buyer_login') }}">LOGIN TO SEE PRICE</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
    <div class="sizeguide hide" >
        <div class="size_guide_content">
            <span class="close_sizeguide_modal">X</span>
            {!! $sizeguide->content !!}
        </div>
    </div>
    <div class="videoFrame hide" >
        <div class="modal-content">
            <span class="close_playvideo_modal">X</span>
            <video  width="100%"  loop muted preload="metadata" autoplay><source class="product-video" src="{{asset($item->video)}}" type="video/mp4"></video>
        </div>
    </div>
    <!-- Modal -->
    <div class="single_page_popup modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <span class="close_modal" data-dismiss="modal" aria-label="Close"></span>
    <div class="modal-dialog modal-dialog-centerd" role="document">
        <div class="modal-content">
            <div class="modal-body" id="popup_slider_zoom">
                <div class="single_img_popup" >
                    @if(!empty($color_images))
                        @foreach($color_images as $k => $images)
                            <div class="slide" id="img_{{$images->id}}" data-slick_index="{{$k}}">
                                @if (Auth::check() && Auth::user()->role == Role::$BUYER)   
                                    <img src="{{ asset($images->image_path) }}" alt="" class="img-fluid">
                                @else 
                                    <img src="{{$defaultItemImage_path}}" alt="" class="img-fluid ">
                                @endif 
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


<input type="hidden" id="color_choose_url" value="{{ route('get_items_info') }}">
<input type="hidden" id="add_cart_url" value="{{ route('add_to_cart') }}">
<input type="hidden" id="user_login" value="@if (Auth::check() && Auth::user()->role == Role::$BUYER) 1 @else 0 @endif">
<input type="hidden" id="login_url" value="{{ route('buyer_login') }}">


@endsection

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mobile-detect/1.4.3/mobile-detect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
    <script src="{{asset('themes/front/js/customjs/product_single_page.js')}}"></script>
@stop
