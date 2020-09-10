<?php use App\Enumeration\Role; ?>

<!-- =========================
        START HEADER SECTION
    ============================== -->
     
<header class="header_area fixed-top"><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@if(!empty($top_notification->value))
<div class="header_top" @if(!empty($top_notification->desc)) style="background: #{{$top_notification->desc}}" @endif>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="top_notification_content">
                {!! $top_notification->value !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endif
    <div class="main_header">
        <div class="container">
            <nav class="navbar navbar-expand-xl navbar-light">
                <a class="navbar-brand" href="{{ route('home') }}">
                    @if(!empty($black_logo_path))
                        <img class="desktop_logo" src="{{$black_logo_path}}" alt="">
                    @endif
                    
                    @if(!empty($white_logo_path))
                        <img class="mobile_logo" src="{{$white_logo_path}}" alt="">
                    @endif
                </a>
                
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span></span>
                </button>

                <div class="collapse navbar-collapse d-none d-xl-block" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        
                        @foreach($default_categories as $cat)

                            @if(!empty($cat['subCategories']))
                                <li class="nav-item has_dropdown">
                                    <a class="nav-link" href="{{ route('category_page', ['category' => $cat['slug']]) }}">{{ $cat['name'] }}</a>
                                    <div class="megamenu">
                                        <div class="megamenu_overlay"></div>
                                        <div class="magamenu_inner">
                                            <?php
                                            $subIds = [];
                                            foreach ($cat['subCategories'] as $d_sub)
                                                $subIds[] = $d_sub['id'];
                                            ?>
                                            <div class="megamenu_col">
                                                <h2>{{ $cat['name'] }}</h2>
                                                <ul class="main_menu_wrap">
                                                <li><a href="{{ route('category_page', ['category' => $cat['slug']]) }}-view-all">View All</a></li>
                                                <li><a href="{{ route('second_category', ['category' => 'new', 'parent' => $cat['slug']]) }}">New</a></li>
                                                    @foreach($cat['subCategories'] as $d_sub)
                                                    <li><a class="@if(strtolower($d_sub['name']) == 'sale') text-danger @endif" href="{{ route('second_category', ['category' => $d_sub['slug'], 'parent' =>$cat['slug']]) }}">{{ ucwords(strtolower($d_sub['name'])) }}</a> 
                                                        @if(!empty($d_sub['thirdcategory']))
                                                            <ul class="third_cat_div">
                                                                @foreach($d_sub['thirdcategory'] as $third_cat)
                                                                    <li><a href="{{ route('third_category', ['category' => $d_sub['slug'], 'parent' => $cat['slug'],'subcategory'=> $third_cat['slug']]) }}">{{ ucwords(strtolower($third_cat['name'])) }}</a></li> 
                                                                @endforeach 
                                                            </ul>
                                                        @endif
                                                    </li>
                                                    @endforeach 
                                                </ul>
                                            </div>
                                        </div>
                                        @if(!empty($cat['image']))
                                        <div class="magamenu_inner magamenu_inner_right">
                                            <div class="megamenu_right"> 
                                                <a href="{{ route('category_page', ['category' => $cat['slug']]) }}">
                                                    <img src="{{asset($cat['image'])}}" class="img-fluid" alt="">

                                                </a>

                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('category_page', ['category' => $cat['slug']]) }}">{{ $cat['name'] }}</a>
                                </li>
                            @endif
                        @endforeach
                        <!--<li class="nav-item"><a class="nav-link" href="{{route('show_schedule')}}">Show Schedule</a></li>-->
                        <!--<li class="nav-item"><a class="nav-link" href="{{route('look_book')}}">LookBook</a></li>-->
                    </ul>
                    <ul class="navbar-nav ml-auto nav_right">
                        <li class="nav-item">
                            <a class="nav-link header_search_ic" href="#"><svg id="icon-search" viewBox="0 0 26 26">
                            <path d="M18.2,3.9C14.3,0,7.8,0,3.9,3.9S0,14.3,3.9,18.2c3.7,3.7,9.7,3.9,13.6,0.6l6.3,6.3l1.2-1.2l-6.3-6.3C22.1,13.6,21.9,7.6,18.2,3.9z M16.9,16.9c-3.3,3.3-8.5,3.3-11.8,0s-3.3-8.5,0-11.8s8.5-3.3,11.8,0S20.2,13.7,16.9,16.9z"></path>
                        </svg></a>
                        </li>

                        <li class="nav-item">
                            @if (!Auth::check() || (Auth::check() && Auth::user()->role != Role::$BUYER))
                            <a href="{{ route('buyer_login') }}" class="nav-link">Sign in</a>
                            @else
                            <a href="#" class="nav-link btnLogOut">Sign Out</a>
                            @endif
                        </li>
                        <li class="nav-item">
                            @if (!Auth::check() || (Auth::check() && Auth::user()->role != Role::$BUYER))
                            <a class="nav-link" href="{{ route('buyer_register') }}">Register</a>
                            @else
                            <a class="nav-link" href="{{ route('buyer_show_overview') }}">My Account</a>
                            @endif
                        </li>
                        <li class="nav-item">
                            <div class="shopping_cart">
                                @if (Auth::check() || (Auth::check() && Auth::user()->role == Role::$BUYER))
                                    <a href="{{ route('show_cart') }}">
                                        <img src="{{asset('images/cart.png')}}" id="icon-bag" alt="">
                                        <span>@if(!empty($cart_items['items'])){{ count($cart_items['items']) }} @else 0 @endif</span>
                                </a>
                                @else
                                    <a href="{{ route('buyer_login') }}">
                                        <img src="{{asset('images/cart.png')}}" id="icon-bag" alt="">
                                        <span>0</span>
                                    </a>
                                @endif 
                                @if (Auth::check() || (Auth::check() && Auth::user()->role == Role::$BUYER))
                                <div class="mini-cart-sub">  
                                    @if($cart_items['empty']==1)
                                        <div class="cart-product @if(count($cart_items['items'])>4) over @endif">
                                            @foreach($cart_items['items'] as $key => $ci) 
                                                <div class="single-cart">
                                                    <div class="cart-img">
                                                        <a href="@if( !empty($ci['details_url'])) {{ $ci['details_url'] }} @endif"><img src="@if( !empty($ci['image_path'])){{ $ci['image_path'] }}@endif" /></a>
                                                    </div>
                                                    <div class="cart-info">
                                                        <h5><a href="@if( !empty($ci['details_url'])){{ $ci['details_url'] }}@endif">@if( !empty($ci['name'])){{ $ci['name'] }}@endif</a> </h5>
                                                        <p> <b>Qty: </b>@if( !empty($ci['qty'])){{ $ci['qty'] }}@endif  </p>
                                                        <p><b>Price: </b> @if( !empty($ci['price']))${{ sprintf('%0.2f', $ci['price']) }}@endif</p> 
                                                        <p><b>Color: </b> @if( !empty($ci['color'])){{ $ci['color'] }} @endif</p> 
                                                        <p><b>Total: </b> @if( !empty($ci['qty'])){{ $ci['qty'] * sprintf('%0.2f', $ci['price'])}} @endif</p> 
                                                    </div>
                                                </div> 
                                            @endforeach 
                                        </div>
                                        <div class="cart-totals">
                                            <ul>
                                                <li><p> Sub Total:  <span>$@if(isset($cart_items['total'])) {{ sprintf('%0.2f', $cart_items['total']['total_price']) }} @endif</span></p></li>
                                                <li>
                                                    <p> Total Qty: </b> <span>@if(isset($cart_items['total'])) {{ $cart_items['total']['total_qty'] }} @endif</span> </p>
                                                </li>
                                            </ul>  
                                        </div>
                                    @else 
                                        <div class="empty_cart text-center">
                                            <p>Cart Empty !!</p>
                                        </div>
                                    @endif 
                                    <div class="cart-bottom">
                                        @if (Auth::check() && Auth::user()->role == Role::$BUYER)
                                            <a href="{{ route('show_cart') }}">View Cart</a>
                                        @else
                                            <a href="{{ route('buyer_login') }}">View Cart</a>
                                        @endif
                                    </div>
                                </div>
                                @endif

                            </div>
                        </li>

                    </ul>
                    <div class="header_search">
                        <form method="get" action="{{ route('search') }}">
                            <input type="text" name="s" class="form-control" placeholder="Search">
                            <button class="btn"><svg id="icon-search" viewBox="0 0 26 26">
                            <path d="M18.2,3.9C14.3,0,7.8,0,3.9,3.9S0,14.3,3.9,18.2c3.7,3.7,9.7,3.9,13.6,0.6l6.3,6.3l1.2-1.2l-6.3-6.3C22.1,13.6,21.9,7.6,18.2,3.9z M16.9,16.9c-3.3,3.3-8.5,3.3-11.8,0s-3.3-8.5,0-11.8s8.5-3.3,11.8,0S20.2,13.7,16.9,16.9z"></path>
                        </svg></button>
                        </form>
                    </div>

                </div>
                <div class="shopping_cart shopping_cart_for_mob">
                    @if (Auth::check() || (Auth::check() && Auth::user()->role == Role::$BUYER))
                        <a href="{{ route('show_cart') }}">
                            <img src="{{asset('images/cart.png')}}" id="icon-bag" alt="">
                            <span>@if(!empty($cart_items['items'])){{ count($cart_items['items']) }} @else 0 @endif</span></a>
                    @else
                        <a href="{{ route('buyer_login') }}">
                            <img src="{{asset('images/cart.png')}}" id="icon-bag" alt="">
                            <span>0</span>
                        </a>
                    @endif
                </div>
            </nav>

        </div>
    </div>
    <div class="mobile_overlay"></div>
    <div class="mobile_menu">

        <div class="menu-list clearfix">
            <div class='l_f_top'>
                <div class='left_menu_logo'>
                    @if(!empty($black_logo_path))
                        <img class="mobile_logo" src="{{$black_logo_path}}" alt="">
                    @endif
                </div>
                

            </div>
            <ul id="menu-content" class="menu-content m_menu_content"> 
                @if($default_categories)
                    @foreach($default_categories as $cat)
                        <?php 
                            $currenturl = url()->current();
                            $currenturl = explode('/',$currenturl); 
                            $cat_name = strtolower($cat['slug']); 
                            $view_all = $cat_name."-view-all";
                            $show = 0; 
                            if(in_array($cat_name, $currenturl)){
                                $show = 1;
                            }else{ 
                                if(in_array($view_all, $currenturl)){
                                    $show = 1;
                                }
                            } 
                            array_push($currenturl, strtolower($cat_name.'-view-all')); 
                        ?>
                        @if(!empty($cat['subCategories']))
                            <li data-toggle="collapse" data-target="#cat_{{ $cat['id'] }}"  class="collapsed has_subcat">
                                <a href="{{ route('category_page', $cat['slug']) }}">{{ $cat['name'] }}  </a> <span ></span>
                            </li>
                            <ul class="sub-menu collapse clearfix @if($show == 1) {{'show'}} @endif" id="cat_{{ $cat['id'] }}">
                                <?php
                                $subIds = [];

                                foreach ($cat['subCategories'] as $d_sub)
                                    $subIds[] = $d_sub['id'];
                                ?>
                                
                                    <li><a href="{{ route('category_page', ['category' => $cat['slug']]) }}-view-all">View All</a></li>
                                    <li><a href="{{ route('second_category', ['category' => 'new', 'parent' => $cat['slug']]) }}">New</a></li>
                                @foreach($cat['subCategories'] as $d_sub)
                                    @if(!empty($d_sub['thirdcategory']))
                                    <?php 
                                        $currenturl = url()->current();
                                        $currenturl = explode('/',$currenturl); 
                                        $last_cat = strtolower($d_sub['slug']); 
                                        $view_all = $last_cat."-view-all"; 
                                        $third_menu_active = 0;
                                        if(in_array($last_cat, $currenturl)){
                                            $third_menu_active = 1;
                                        }else{ 
                                            if(in_array($view_all, $currenturl)){
                                                $third_menu_active = 1;
                                            }
                                        } 
                                        array_push($currenturl, strtolower($last_cat.'-view-all')); 
                                         
                                    ?>
                                        <li data-toggle="collapse" data-target="#cat_{{ $d_sub['id'] }}" class="collapsed has_subcat"><a href="{{ route('second_category', ['category' => $d_sub['slug'], 'parent' => $cat['slug']]) }}">{{ $d_sub['name'] }}</a></li>
                                        <ul class="third_menu collapse clearfix @if($third_menu_active == 1) {{'show'}}  @endif " id="cat_{{ $d_sub['id'] }}">
                                            @foreach($d_sub['thirdcategory'] as $third_cat)
                                                <li><a href="{{ route('third_category', ['category' =>$d_sub['slug'], 'parent' =>$cat['slug'],'subcategory'=> $third_cat['slug']]) }}">{{ ucwords(strtolower($third_cat['name'])) }}</a></li> 
                                            @endforeach 
                                        </ul>
                                    @else 
                                    <li><a class="@if(strtolower($d_sub['name']) == 'sale') text-danger @endif" href="{{ route('second_category', ['category' => $d_sub['slug'], 'parent' => $cat['slug']]) }}">{{ $d_sub['name'] }}</a></li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <li data-toggle="collapse" data-target="#{{ $cat['id'] }}" class="collapsed no_subcat">
                                <a href="{{ route('category_page', ['category' => $cat['slug']]) }}">{{ $cat['name'] }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
                <li class="collapsed no_subcat"><a href="{{route('show_schedule')}}">Show Schedule</a> </li> 
                <li class="collapsed no_subcat"><a href="{{route('look_book')}}">LookBook</a> </li>
            </ul>
            <div class="l_m_bottom">
                <div class="mobile_search">
                        <form method="get" action="{{ route('search') }}" class="search_form">
                            <input type="search" name="s" placeholder="Search" class="searchTerm" required="">
                        </form>
                        <button><img src="{{asset('images/icon-search.png')}}" alt=""></button>
                    </div>
                            <ul id="menu-content" class="menu-content mobile_signup">
                    @if (!Auth::check() || (Auth::check() && Auth::user()->role != Role::$BUYER))
                        <li  class="collapsed no_subcat"><a href="{{route('buyer_login')}}">SIGN IN</a> </li>
                        <li  class="collapsed no_subcat"><a href="{{route('buyer_register')}}">REGISTER</a> </li>
                    @else
                        <li  class="collapsed no_subcat"><a href="{{route('buyer_show_overview')}}">MY ACCOUNT</a> </li>
                        <li  class="collapsed no_subcat"><a href="#"  class="btnLogOut">LOG OUT</a> </li>
                    @endif
                        <form class="logoutForm" action="{{ route('logout_buyer') }}" method="post">
                            {{ csrf_field() }}
                        </form>
                </ul>
            </div>

            {{--            </div>--}}
        </div>
    </div>
</header>
  