<?php use App\Enumeration\Role; ?>
@extends('layouts.home_layout')
@section('additionalCSS')

@stop
@section('content')
 
@if(count($topimages) >0)
    <section class="banner_area category_landing_page_main_slider">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div  class="banner_slider owl-carousel" id="landing_page_top">
                        @foreach($topimages as $slide)
                            @if($slide->type == 15)
                                @if(strpos($slide->image_path, '.mp4') !== false || strpos($slide->image_path, '.m4v') !== false)
                                    <div class="banner_item">
                                        <div class="video-wrapper">  
                                            <video id='home-video' loop muted preload="metadata"width="100%" height="100%" class="embed-responsive-item" autoplay="autoplay"  playsinline>
                                                <source id='mp4' src="{{ asset($slide->image_path) }}" type='video/mp4'/> 
                                            </video> 
                                        </div>
                                    </div>
                                @else
                                    <div class="banner_item">
                                    <a href="{{$slide->url}}"><img src="{{ asset($slide->image_path) }}" alt="" class="img-fluid "></a>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
@if(count($newArrivalItems)>0)
<div class="main_product_area category_landing_page_area @if(count($topimages) >0) @else category_landing_page_main_slider @endif">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="main_title text-center">
                    <h2>NEW ARRIVALS</h2>
                    <p>STAY UP TO DATE IN OUR NEWEST ARRIVALS IN TOPS , DRESSES , BOTTOMS AND MORE!</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="main_product_area"> 
                    <div class="row product_custom_margin"> 
                        @foreach($newArrivalItems as $item)
                            <div class=" custom_grid_child product_custom_padding">
                                <div class="product_wrapper">  
                                    <div class="main_product_img">
                                        <a href="{{ route('product_single_page', $item->slug) }}">
                                            @if((strpos($item->video, '.mp4') !== false || strpos($item->video, '.m4v') !== false) && $item->default==1) 
                                                <video id='home-video' loop muted preload="metadata"width="100%" height="100%" class="embed-responsive-item" autoplay="autoplay"  playsinline>
                                                    <source id='mp4' src="{{ asset($item->video) }}" type='video/mp4'/> 
                                                </video> 
                                            @else
                                            <img src="{{asset($item->images[0]->compressed_image_path)}}" alt="{{strtolower($item->name)}}" class="img-fluid">
                                            @endif
                                        </a>
                                    </div>
                                    <div class="main_product_text">
                                        <h2><a href="{{ route('product_single_page', $item->slug) }}" class="p_style title_for_lg trim-text">@if(!empty($item->style_no)){{$item->style_no}} @endif</a></h2> 
                                        <p class="price @if(!empty($item->orig_price)) text-danger @endif">
                                            @if (Auth::check() && Auth::user()->role == Role::$BUYER) 
                                                @if($item->orig_price) <del>${{$item->orig_price}}</del> @endif @if(!empty($item->price))${{$item->price}} @endif
                                            @else
                                            <a href="{{ route('buyer_login') }}">LOGIN TO SEE PRICE</a>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>  
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@if(count($bottomimages) >0)
    <section class="main_product_area landing_category_bottom_slider">
        <div  class="container">
            <div class="row">

                    @foreach($bottomimages as $slide)
                        @if($slide->type == 16)
                        <div class="col-md-4">
                            <a href="{{$slide->url}}"><img src="{{ asset($slide->image_path) }}" alt="" class="img-fluid "></a>
                        </div>
                        @endif
                    @endforeach
            </div>
        </div>
    </section>
@endif

@if(!empty($category->details))
<section class="main_product_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="cat_landing_bottom_section">
                    {!! $category->details !!}
                </div>
            </div>
        </div>
    </div>
</section>
@endif



@endsection

@section('additionalJS')
@endsection
