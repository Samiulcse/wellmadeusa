<?php
    use App\Enumeration\Role;
    use App\Enumeration\Availability;
?>

@extends('layouts.home_layout')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/fotorama.css') }}">
    <style>
        .color-selected {
            border: 1px solid black !important;
        }
        .sub_category_menu
        {
            margin-left: 0px;
            padding-left: 5px;
            border-left: 1px solid #eee;
        }
    </style>
@stop

@section('breadcrumbs')
    {{-- {{ Breadcrumbs::render('parent_category_page', $category) }} --}}
@stop

@section('filters')
    <section class="product_filter_area">
        <div class="product_filter_wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <ul class="product_filter">
                            <li>VIEW</li>
                            <li class="two_grid active">2</li>
                            <li class="four_grid">4</li>
                            <li>|</li>
                            <li class="p_fiter">+Filters</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter_form">
            <div class="filter_form_inner">
                <div class="row">
                    <div class="col-md-3">
                        <div class="widget-categories">
                            <h6 class="widget-title">SUB CATEGORY</h6>

                            {{-- <ul>
                                @foreach($category->subCategories as $sub)
                                    <li>
                                        <a href="{{ route('second_category', ['category' => changeSpecialChar($sub->name), 'parent' => changeSpecialChar($category->name)]) }}">{{ $sub->name }}</a>
                                    </li>
                                @endforeach
                            </ul> --}}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <h6 class="widget-title">SEARCH</h6>

                        <div class="custom-control custom-radio custom-control-inline">
                            <input class="custom-control-input" type="radio" id="search-style-no" name="search-component" value="1"
                                    {{ (request()->get('search-by') == '') ? 'checked' : (request()->get('search-by') == 1 ? 'checked' : '') }}>
                            <label class="custom-control-label" for="search-style-no">Style No.</label>
                        </div>

                        <div class="custom-control custom-radio custom-control-inline">
                            <input class="custom-control-input" type="radio" id="search-description" name="search-component" value="2"
                                    {{ request()->get('search-by') == 2 ? 'checked' : '' }}>
                            <label class="custom-control-label" for="search-description">Description</label>
                        </div>

                        <div class="form-group">
                            <input class="form-control" id="search-input" type="text" placeholder="Search"
                                   value="{{ request()->get('search') }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <input class="form-control" id="search-price-min" type="text" placeholder="Price Min"
                                       value="{{ request()->get('price_min') }}">
                            </div>

                            <div class="col-md-6">
                                <input class="form-control" id="search-price-max" type="text" placeholder="Price Max"
                                       value="{{ request()->get('price_max') }}">
                            </div>
                        </div>

                        <button class="btn btn-secondary mt-3" id="btn-search">SEARCH</button>
                    </div>

                    <div class="col-md-3">
                        <h6 class="widget-title">COLORS</h6>

                        <div class="widget-categories">
                            <ul class="sidecolor">
                                @foreach($masterColors as $mc)
                                    <li class="item-color {{ request()->get('color') == $mc->id ? 'color-selected' : '' }}"
                                        style="position: relative; float: left; display: list-item; padding: 3px; border: 1px solid white"
                                        data-id="{{ $mc->id }}" title="{{ $mc->name }}">
                                        <img src="{{ asset($mc->image_path) }}" width="30px" height="20px">
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="filter_confirm text-right">
                    <a class="cancel_form" href="#">Cancel</a>
                </div>
            </div>
        </div>
    </section>
@stop

@section('content')
    <div class="container category-page">
        <!-- =========================
            START BANNER SECTION
        ============================== -->
        <section class="banner_area common_banner clearfix">
            <div class="row">
                <div class="col-md-12 custom_padding_9">
                    @if(count($top_notification_banner_module) != 0)
                    @php
                        $imageUrl = asset($top_notification_banner_module[0]->image_path);
                    @endphp
                    <div class="banner_top" style="background-image: url({{$imageUrl}});height: 15vh;background-size: 100% 100%;"></div>
                    @endif
                </div>
            </div>
        </section>
        <!-- =========================
            END BANNER SECTION
        ============================== -->
         <!-- =========================
            START BANNER SECTION
        ============================== -->
        <section class="banner_area common_banner clearfix">
            <div class="row">
                <div class="col-md-12 custom_padding_9">
                    @if( isset($notification_banner_module) && count($notification_banner_module) != 0)
                    @php
                        $imageUrl = asset($notification_banner_module[0]->image_path);
                    @endphp
                    <div class="banner_top" style="background-image: url({{$imageUrl}})">
                        <?php echo $notification_banner_module[0]->details; ?>
                    </div>
                    @endif
                </div>
            </div>
        </section>
        <!-- =========================
            END BANNER SECTION
        ============================== -->
        <!-- =========================
            START BREDCRUMS SECTION
        ============================== -->
        <section class="breadcrumbs_area">
            <div class="row">
                <div class="col-lg-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Best Selling</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </section>
        <!-- =========================
            START BREDCRUMS SECTION
        ============================== -->

        <!-- =========================
            START APPOINMENT SECTION
        ============================== -->
        <section class="appoinment_area common_content_area" id='main_app'>
            <div class="row">
                <div class="col-md-2 custom_padding_9 for_desktop d-none d-lg-block">
                    <div class="common_left_menu">
                        <ul>
                            <li><a href="{{ route('new_arrival_page') }}">New Arrival</a></li>
                            <li><a href="{{ route('best_selling_page') }}">Best Selling</a></li>
                            <ul>
                                @foreach($default_categories as $cat)
                                <li><a href="{{ route('category_page', ['category' => $cat['slug']]) }}">{{ $cat['name'] }}</a></li>
                                @if(count($cat['subCategories'])>0)
                                    @foreach($cat['subCategories'] as $d_sub)
                                        <li class="sub_category_menu"><a href="{{ route('second_category', ['category' => $d_sub['slug'], 'parent' => $cat['slug']]) }}">- {{ $d_sub['name'] }}</a></li>
                                    @endforeach
                                @endif
                                @endforeach
                            </ul>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-10 col-md-12" id='show_ajax_load_div' style="display: none">
                    <div class="row">
                        <div v-if='records.length > 0' class="col-md-12 category-custom_padding">
                            <div class="product_filter clearfix">
                                <div class="product_filter_left">
                                    <select class="form-control sort_by" id="sort_by_top" @change="sort_by_order('sort_by_top')">
                                        <option value="">Sort By</option>
                                        <option value="low_to_high">Price: Low - High</option>
                                        <option value="high_to_low">Price: High - Low</option>
                                    </select>
                                </div>
                                <div class="product_filter_right">
                                    <ul>
                                        <li @click="pre_page_load(currenct_pagination_index)"><a href="#"><span></span></a></li>
                                        <li>@{{currenct_pagination_index}}  of @{{last_pagination_index}}</li>
                                        <li @click="next_page_load(currenct_pagination_index)"><a href="#"><span></span></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div v-if='records.length > 0' v-for='record in records' class="col-6 col-md-4 col-lg-3 category-custom_padding">
                                    <div class="product_inner text-center">
                                        @if (Auth::check() && Auth::user()->role == Role::$BUYER)
                                        <a :href="'{{ URL::to('/') }}/product/' + record.slug">
                                            <img v-if='record.images.length == 0' src="{{asset('images/no-image.png')}}" alt="" class="img-fluid">
                                            {{-- <img v-if='record.images.length != 0' :src="record.images[0]['image_path']" alt="" class="img-fluid"> --}}
                                            <div class="owl-carousel owl-theme owl_product" v-bind:id="record.id" v-if='record.images.length != 0'>
                                                <div class="item" v-for='im in record.images'><img :src="'{{ URL::to('/') }}/'+im['image_path']" alt="" class="img-fluid"><div class="owl-overlay-text" v-if='record.default_parent_category==5'>Preorder</div></div>
                                            </div>
                                        </a>

                                        <h2><a :href="'{{ URL::to('/') }}/product/' + record.slug">@{{record.name}}</a></h2>
                                        <p>
                                            {{--  <span v-if='record.orig_price != null' style='text-decoration: line-through;'>$@{{record.orig_price}}</span>   --}}
                                            $@{{record.price}}
                                        </p>

                                        <div class="owl-dots" v-bind:id="'dotCustom'+record.id" v-if='record.images.length != 0'>
                                            <span v-for='im in record.images' v-if='im.color != null' @click="clickDot(record.id)"><img :src="'{{ URL::to('/') }}/'+im.color.image_path" v-bind:alt="im.color.name" class="dot-img"></span>
                                        </div>
                                        @else
                                        <a :href="'{{ URL::to('/') }}/product/' + record.slug">
                                            <img src="{{$defaultItemImage_path}}" alt="" class="img-fluid">
                                        </a>
                                        <h2><a :href="'{{ URL::to('/') }}/product/' + record.slug">@{{record.name}}</a></h2>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if='records.length > 0' class="col-md-12 category-custom_padding">
                            <div class="product_filter clearfix">
                                <div class="product_filter_left">
                                    <select class="form-control sort_by" id="sort_by_bottom" @change="sort_by_order('sort_by_bottom')">
                                        <option value="">Sort By</option>
                                        <option value="low_to_high">Price: Low - High</option>
                                        <option value="high_to_low">Price: High - Low</option>
                                    </select>
                                </div>
                                <div class="product_filter_right">
                                    <ul>
                                        <li @click="pre_page_load(currenct_pagination_index)"><a href="#"><span></span></a></li>
                                        <li>@{{currenct_pagination_index}}  of @{{last_pagination_index}}</li>
                                        <li @click="next_page_load(currenct_pagination_index)"><a href="#"><span></span></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- =========================
            END APPOINMENT SECTION
        ============================== -->
    </div>



    <div class="product_grid two_grid_area two_grid_show custom-pagination">
        <div class="container-fluid">
            <div class="row" id="product-container">

            </div>
            <div class="pagination justify-content-center"></div>
        </div>
    </div>

    <template id="template-product">
        <div class="single-product col-6 col-sm-6 col-md-6 product_custom_padding">
            <div class="product_grid_inner">
                <a href="#" class="product-thumb product_grid_inner_thumb">
                    <img src="{{ asset('images/no-image.png') }}" alt="" class="lazy product-image img-fluid">
                    @if(auth()->user())
                    <span class="product_grid_inner_thumb_grid">
                        <ul>

                        </ul>
                    </span>
                    @endif
                </a>
                <h2><a class="p_title" href="#"></a></h2>
                <h3 class="price"></h3>
            </div>
        </div>
    </template>

    <template id="template-product-video">
        <div class="single-product-video col-sm-6 col-md-12">
            <div class="product_grid_inner">
                <a href="#" class="product-thumb product_grid_inner_thumb">
                    <div class="video_grid">
                        <div class="video_wrapper">
                            <video  loop muted preload="metadata" autoplay>
                                <source class="product-video" src="" type="video/mp4">
                            </video>
                        </div>
                    </div>
                </a>
                <h2><a class="p_title" href="#"></a></h2>
                <h3 class="price"></h3>
            </div>
        </div>
    </template>
@stop

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/fotorama.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/jqueryCookie/jquery.cookie-1.4.1.min.js') }}"></script>
    <script src="{{ asset('js/vue.js') }}"></script>
    <script src="{{ asset('js/axios.js') }}"></script>
    <script>

        var load_per_page = 8;
        var start_page_index = 1;
        var url = "{{ route('get_best_selling_items_load_ajax') }}";

        // start search for search sorting
        var app = new Vue({
            el: '#main_app',
            data: {
                records: [],
                currenct_pagination_index : 0,
                last_pagination_index : 0,
            },
            created: function() {
                this.get_all_data($('.sort_by').val(), start_page_index, load_per_page, 'next');
            },
            methods:
            {
                clickDot: function(id) {
                    $('#dotCustom'+id).on('click', 'span', function(e) {
                        $('#'+id).trigger('to.owl.carousel', [$(this).index(), 300]);
                    });
                },
                get_all_data: function(sort_by, offset, limit, action)
                {
                    axios.get(url,{
                        params: {
                            // categories_ids: categories,
                            sort_by : sort_by,
                            offset: offset,
                            limit : limit
                        }
                    })
                    .then((response) => {
                        this.records = response.data.records;
                        this.last_pagination_index = response.data.last_pagination_index;
                        this.currenct_pagination_index = parseInt(response.data.offset);
                        $('#show_ajax_load_div').show();

                        $( document ).ready(function() {
                            $(".owl_product").owlCarousel({
                                loop:true,
                                nav:false,
                                margin:10,
                                //dotsContainer: '.owl-dots',
                                responsive:{
                                    0:{
                                        items:1
                                    },
                                    600:{
                                        items:1
                                    },
                                    1000:{
                                        items:1
                                    }
                                }
                            });
                        });

                    })
                    .catch((err) => {
                        console.log(err);
                    });
                },
                sort_by_order: function(value)
                {
                    this.get_all_data($('#'+value).val(), start_page_index, load_per_page, 'next');
                    if(value == 'sort_by_bottom'){
                        $('#sort_by_top').val($('#'+value).val());
                    } else {
                        $('#sort_by_bottom').val($('#'+value).val());
                    }
                },
                next_page_load: function(offset_value)
                {
                    if ( offset_value != this.last_pagination_index )
                    {
                        this.get_all_data($('.sort_by').val(), offset_value + 1, load_per_page, 'next');
                    }
                },
                pre_page_load: function(offset_value)
                {
                    if ( offset_value != 1 )
                    {
                        this.get_all_data($('.sort_by').val(), offset_value - 1, load_per_page, 'Pre');
                    }
                }
            }
        });

        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var page = 1;
            var search_text = '';
            var search_option = '';
            var search_price_min = '';
            var search_price_max = '';
            var wishlist_ids = <?php echo json_encode($wishListItems); ?>;

            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);

            $('.checkbox-category, .vendor-checkbox, .checkbox-body-size, .checkbox-pattern, .checkbox-length, .checkbox-style, .checkbox-fabric').change(function () {
                filterItem();
            });

            $('#sorting').change(function () {
                filterItem();
            });

            $('.item-color').click(function () {
                if ($(this).hasClass('color-selected'))
                    $(this).removeClass('color-selected');
                else
                    $(this).addClass('color-selected');

                filterItem();
            });

            $('#btn-search').click(function () {
                search_text = $('#search-input').val();
                search_option = $('input[name=search-component]:checked').val();
                search_price_min = $('#price-range-min').val();
                search_price_max = $('#search-price-max').val();

                filterItem();
            });

            function filterItem(page) {
                page = typeof page !== 'undefined' ? page : 1;

                var vendors = [];
                var masterColors = [];
                var bodySizes = [];
                var patterns = [];
                var lengths = [];
                var styles = [];
                var fabrics = [];
                var sorting = $('#sorting').val();
                var looged_in = 0;

                // Vendor
                $('.vendor-checkbox').each(function () {
                    if ($(this).is(':checked'))
                        vendors.push($(this).data('id'));
                });

                // Master Color
                $('.item-color').each(function () {
                    if ($(this).hasClass('color-selected'))
                        masterColors.push($(this).data('id'));
                });

                // Body Size
                $('.checkbox-body-size').each(function () {
                    if ($(this).is(':checked'))
                        bodySizes.push($(this).data('id'));
                });

                // Pattern
                $('.checkbox-pattern').each(function () {
                    if ($(this).is(':checked'))
                        patterns.push($(this).data('id'));
                });

                // Length
                $('.checkbox-length').each(function () {
                    if ($(this).is(':checked'))
                        lengths.push($(this).data('id'));
                });

                // Style
                $('.checkbox-style').each(function () {
                    if ($(this).is(':checked'))
                        styles.push($(this).data('id'));
                });

                // Master Fabric
                $('.checkbox-fabric').each(function () {
                    if ($(this).is(':checked'))
                        fabrics.push($(this).data('id'));
                });

                // $.ajax({
                //     method: "GET",
                //     url: "{{ route('get_best_selling_items_filter_load_ajax') }}",
                //     data: {
                //         // categories: categories,
                //         vendors: vendors,
                //         masterColors: masterColors,
                //         bodySizes: bodySizes,
                //         patterns: patterns,
                //         lengths: lengths,
                //         styles: styles,
                //         fabrics: fabrics,
                //         sorting: sorting,
                //         searchText: search_text,
                //         searchOption: search_option,
                //         priceMin: search_price_min,
                //         priceMax: search_price_max,
                //         page: page
                //     }
                // }).done(function (data) {
                //     var product_details_clone = $('#product_details_clone');

                //     if(data.items.data == 0)
                //     {
                //         $('.sorting_div').hide();
                //     }
                //     else
                //     {
                //         $('.sorting_div').show();
                //     }

                //     // console.log(data.items.data);
                //     for(var i=0;i<data.items.data.length;i++)
                //     {
                //         product_details_clone.find('img').attr('src',data.items.data[i]['imagePath']);
                //         product_details_clone.find('h2 > a').html(data.items.data[i]['name']);
                //         product_details_clone.find('p').html(data.items.data[i]['price']);
                //         $('#product_details_show').append(product_details_clone.clone());
                //     }
                // });
            }

            // Pagination
            $(document).on('click', '.page-link', function (e) {
                e.preventDefault();
                var url = $(this).attr('href');
                page = getURLParameter(url, 'page');

                filterItem(page);
            });

            // Click to Show Quick View Functionalities

            function getURLParameter(url, name) {
                return (RegExp(name + '=' + '(.+?)(&|$)').exec(url) || [, null])[1];
            }

            // Hold Position
            $(window).bind('beforeunload', function(){
                localStorage['previous_page'] = page;
                localStorage['previous_position'] = $(document).scrollTop()+'';
            });

            var changePage = localStorage['change_page'];
            if (changePage) {
                localStorage.removeItem('change_page');

                page = parseInt(localStorage.getItem('previous_page'));
            }


            filterItem(page);

            // 2/4 View
            $('.four_grid').click(function () {
                $.cookie('cq-view', 4);
            });

            $('.two_grid').click(function () {
                $.cookie('cq-view', 2);
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
        var lazyImages = [].slice.call(document.querySelectorAll("img.lazy"));;

        if ("IntersectionObserver" in window && "IntersectionObserverEntry" in window && "intersectionRatio" in window.IntersectionObserverEntry.prototype) {
            let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                let lazyImage = entry.target;
                lazyImage.src = lazyImage.dataset.src;
                lazyImage.srcset = lazyImage.dataset.srcset;
                lazyImage.classList.remove("lazy");
                lazyImageObserver.unobserve(lazyImage);
                }
            });
            });

            lazyImages.forEach(function(lazyImage) {
            lazyImageObserver.observe(lazyImage);
            });
        }
        });
    </script>
@stop
