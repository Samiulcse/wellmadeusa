<?php use App\Enumeration\Role; ?>
@extends('layouts.home_layout')
@section('additionalCSS')

@stop 
@section('content') 
@if(count($HomeMainBanner) >0) 
    <section class="banner_area home_page_min_banner"> 
        <div  class="swiper-container">
            <div class="swiper-wrapper">
              
                  @foreach($HomeMainBanner as $slide)
                    @if(strpos($slide->image_path, '.mp4') !== false || strpos($slide->image_path, '.m4v') !== false)
                        <div class="banner_item swiper-slide"> 
                            <div class="video-wrapper">   
                                <video id='home-video' loop muted preload="metadata"width="100%" height="100%" class="embed-responsive-item" autoplay="autoplay"  playsinline>
                                    <source id='mp4' src="{{ asset($slide->image_path) }}" type='video/mp4'/> 
                                </video> 
                            </div>
                        </div>
                    @else
                    <div class="swiper-slide">
                        <a href="{{$slide->url}}"><img src="{{ asset($slide->image_path) }}" alt="" class="img-fluid "></a>
                    </div>
                    @endif
                    @endforeach 
            </div>
            
        </div>  
    </section>
@endif

<section class="homepage_mobile_section category_landing_page_area">
    @if(count($newArrivalItems)>0)
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="main_title text-center">
                        <h2>NEW ARRIVALS</h2> 
                    </div>
                </div>
            </div>
            <div class="row">
                <ul class="landing_page_newarrival_slider owl-carousel">
                    @foreach($newArrivalItems as $item)  
                        <li>
                            <a href="{{ route('product_single_page', $item->slug) }}">
                            @if((strpos($item->video, '.mp4') !== false || strpos($item->video, '.m4v') !== false) && $item->default==1) 
                                <video id='home-video' loop muted preload="metadata"width="100%" height="100%" class="embed-responsive-item" autoplay="autoplay"  playsinline>
                                    <source id='mp4' src="{{ asset($item->video) }}" type='video/mp4'/> 
                                </video> 
                            @else 
                            <img src="{{asset($item->images[0]->compressed_image_path)}}" alt="{{$item->name}}" class="img-fluid">
                            @endif
                            </a>
                            <h2><a href="{{ route('product_single_page', $item->slug) }}">@if(!empty($item->style_no)){{$item->style_no}} @endif</a></h2>
                            @if (Auth::check() && Auth::user()->role == Role::$BUYER)
                                <p class="price">@if(!empty($item->price))${{$item->price}} @endif</p>
                            @else
                                <a href="{{ route('buyer_login') }}">LOGIN TO SEE PRICE</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
            @if(!empty($page))
            <div class="row">
                <div class="main_product_area">
                    {!! $page->content !!}
                </div>
            </div>
            @endif 
        </div>
    @endif
</section>

 
    <!-- =========================
        END HOME PRODUCT SECTION
    ============================== -->
    <div class="modal fade" id="modalWelcome">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    {!! $welcome_msg !!}</div>
            </div>
        </div>
    </div>
@endsection

@section('additionalJS')

    <script>
        var msg = '{{ $welcome_msg  }}';
        if ( msg !="" ){
            setTimeout(function () {
                $('#modalWelcome').modal('show');
            }, 100);
        } 
        $(function () {    
            setTimeout(function() {
                $('.banner_area').css('min-height',"auto"); 
            }, 1000);
            
        });
    </script>
            <!-- Initialize Swiper -->
          <script>
            var swiper = new Swiper('.swiper-container', {
              loop: true
            });
          </script>
 
@endsection
