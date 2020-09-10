<?php
use App\Enumeration\Role;
use App\Enumeration\Availability;
?>

@extends('layouts.home_layout')

@section('additionalCSS')

@stop

@section('content')
{{--    @if(!empty($banner->image_path))--}}
{{--    <section class="section cateogry_section">--}}
{{--        <div class="banner-image-wrap">--}}
{{--            <img src="{{URL::to('/')}}/{{$banner->image_path}} " alt="{{$banner->url}}" class="img-fluid" width="100%;" max-height="400px;">--}}
{{--        </div>--}}
{{--    </section>--}}
{{--    @endif--}}
<!-- =========================
        START HOME PRODUCT SECTION
    ============================== -->
<section class="product_area common_top_margin">
    <div class="container">
        <div class="main_title text-center">
            <h2>Search Results For - {{ request()->get('s') }}</h2>
        </div>
    
        <!-- =========================
           PRODUCT FILTER
        ============================== -->
    
        <!-- ============================
           PRODUCT THUMBNAIL
        ============================== -->
        <div class="main_product_area"> 
                <div class="row product_custom_margin" id="product-container"> </div>
                <template id="template-product" >
                    <div class="custom_grid_child product_custom_padding">
                        <div class="product_wrapper">
                            <div class="pre_order">
                                <span><img src="{{ asset('images/pre_order.png') }}" height="100px" width="100px" alt=""></span>
                            </div>
                            <div class="main_product_img">
                                <a href="#">
                                    <img src="" class="img-fluid product-image" alt="" data-src="">
                                    <img src="" class="img_hover" alt="">
                                </a>
                            </div>
                            <div class="main_product_text">
                                <h2><a href="#" class="p_style title_for_lg trim-text"></a></h2>
                                <p class="price"></p>
                            </div>
                        </div>
                    </div>
                </template>
                <template id="template-product-video" >
                    <div class="custom_grid_child product_custom_padding">
                        <div class="product_wrapper">
                            <div class="pre_order">
                                <span><img src="{{ asset('images/pre_order.png') }}" height="100px" width="100px" alt=""></span>
                            </div>
                            <span class="product_grid_inner_video_grid">
                                <a class="video_url" href="">
                                <ul>
    
                                </ul>
                                </a>
                            </span>
                            <div class="main_product_text">
                                <h2><a href="#" class="p_style title_for_lg trim-text"></a></h2>
                                <p class="price"></p>
                            </div>
                        </div>
                    </div>
                </template> 
        </div>
    </div>
</section>
<!-- =========================
    END HOME PRODUCT SECTION
============================== -->
@endsection

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/fotorama.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/jqueryCookie/jquery.cookie-1.4.1.min.js') }}"></script>
    <script>
        var url = "{{URL::to('/')}}/product/";
        var url_login = "{{URL::to('/')}}/login/";
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            var page = 1;
            var search_text = '{{ request()->get('s') }}';  
            var search_option = '';
            var search_price_min = '';
            var search_price_max = '';
            var wishlist_ids = '';

            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);



            $('.checkbox-category, .vendor-checkbox, .checkbox-body-size, .checkbox-pattern, .checkbox-length, .checkbox-style, .checkbox-fabric').change(function () {
                filterItem();
            });

            //Sort Filtering
            $('.sorting').click(function () {
                // $('.filter_mobile_inner').css('display','none');
                // $('.for_sm_screen').removeClass('filter_mobile_fixed');
                // if ($(this).hasClass('active'))
                //     $(this).removeClass('active');
                // else
                //     $(this).addClass('active');
                var type = $(this).data('type');
                $('#sorting').val(type);
                filterItem();
            });

            //Color Filtering
            $('.item-color').click(function () {
                if ($(this).parent().hasClass('active'))
                    $(this).parent().removeClass('active');
                else
                    $(this).parent().addClass('active');

                filterItem();
            });

            //Category Filtering
            $('.item-category').click(function () {
                if ($(this).parent().hasClass('active'))
                    $(this).parent().removeClass('active');
                else
                    $(this).parent().addClass('active');

                filterItem();
            });

            //Pack Filtering
            $('.packid').click(function () {
                if ($(this).parent().hasClass('active'))
                    $(this).parent().removeClass('active');
                else
                    $(this).parent().addClass('active');
                filterItem();
            });


            //Fabric Filtering
            $('.febricid').click(function () {
                if ($(this).hasClass('feb-selected'))
                    $(this).removeClass('feb-selected');
                else
                    $(this).addClass('feb-selected'); 
                filterItem();
            });
 

            $('.febricid_reset').click(function () {
                $('.dropdown-menu').removeClass('show'); 
            });

            $('#clear_all').click(function () {
                location.reload();
            });
            var limit = 40; //The number of records to display per request
            var start = 0; //The starting pointer of the data
            var action = 'inactive'; //Check if current action is going on or not. If not then inactive otherwise active


            function filterItem(page,limit,start) {

                page = typeof page !== 'undefined' ? page : 1; 
                var masterColors = [];
                var masterCategory = [];
                var bodySizes = []; 
                var masterfeb = []; 
                var masterSizes = [];
                var fabrics = [];
                var packs = [];
                var limit_f = limit;
                var start_f = start;
                var sorting = $('#sorting').val();
                var looged_in = 0;

                // Master Color
                $('.item-color').each(function () {
                    if ($(this).parent().hasClass('active'))
                        masterColors.push($(this).data('id'));
                });

                // Master Category
                $('.item-category').each(function () {
                    if ($(this).parent().hasClass('active'))
                        masterCategory.push($(this).data('id'));
                });

                // Pack filter
                $('.packid').each(function () {
                    if ($(this).parent().hasClass('active'))
                        packs.push($(this).data('id'));
                });

                // Body Size
                $('.checkbox-body-size').each(function () {
                    if ($(this).is(':checked'))
                        bodySizes.push($(this).data('id'));
                }); 

                // Master Febric
                $('.febricid').each(function () {
                    if ($(this).hasClass('feb-selected'))
                        masterfeb.push($(this).data('id'));
                });  
                $.ajax({
                    method: "POST",
                    url: "{{ route('get_search_items_load_ajax') }}",
                    data: {  
                        masterColors: masterColors,
                        masterCategory: masterCategory,
                        bodySizes: bodySizes, 
                        masterfeb: masterfeb,
                        masterSizes: masterSizes,
                        sorting: sorting,
                        s: search_text, 
                        limit: limit_f,
                        start: start_f,
                        page: page
                    }
                }).done(function (data) { 
                    var products = data.items;
                    var no_result = products.length;
                    if(0 >= no_result){
                        $(".main_title").empty();
                        $('.main_title').html('<h2 class="no_result">Search Result Not Found For - '+search_text+'</h2>');
                    }
                    $('.pagination').html(data.pagination);
                    // $('#totalItem').html(data.items.total);

                    $('#product-container').html('');
                    var backOrder = '{{ Availability::$ARRIVES_SOON }}';
                    
                    $.each(products, function (index, product) {
                        var default_video = product.default;
                        if (product.video != null && default_video == 1) {
                            var html = $('#template-product-video').html();
                        } else{
                            var html = $('#template-product').html(); 
                        }

                        var row = $(html);
                        var productStyleNo = product.style_no;

                        if (product.style_no == null || product.style_no == '')
                            row.find('.p_style').html('&nbsp;');
                        else
                            row.find('.p_style').html(productStyleNo);
                          
                        // row.find('.main_product_img a').attr('href', url+''+product.slug);
                        // row.find('.p_style').attr('href', url+''+product.slug);
                        // row.find('.video_url').attr('href', url+''+product.slug);
                        @if(auth()->user())
                            if(product.orig_price){ 
                                row.find('.price').addClass('text-danger');
                                row.find('.price').html( "<del>$"+product.orig_price+"</del> "+product.price );
                            }else{
                                row.find('.price').html(product.price); 
                            }
                        @else
                        row.find('.price').html('<a href="{{ route('buyer_login') }}">Login to See Price </a>');
                        @endif
                       
                        
                        if (product.default_parent_category != 6) {
                        row.find('.pre_order').css('display','none');
                        row.find('.main_product_img a').attr('href', url+''+product.slug);
                        row.find('.p_style').attr('href', url+''+product.slug);
                        row.find('.video_url').attr('href', url+''+product.slug);
                        if (product.video != null && default_video == 1) { 
                            var defaultImagePath = "{{$defaultItemImage_path}}";
                            row.find('.product_grid_inner_video_grid').find('ul').append('<li><video  width="100%"  loop muted preload="metadata" autoplay><source class="product-video" src="'+product.video+'" type="video/mp4"></video></li>');
                        } else {  
                            var defaultImagePath = "{{$defaultItemImage_path}}";
                            row.find('.product-image').attr('src', product.imagePath);
                            row.find('.product-image').attr('data-src', product.imagePath);
                            row.find('.product-image').addClass('item_src_'+product.id);
                            row.find('.img_hover').attr('src', product.imagePath2);
                        }
                        }else if(product.default_parent_category == 6){
                        var defaultImagePath = "{{$defaultItemImage_path}}";
                        @if(auth()->user())
                        row.find('.pre_order').css('display','block');
                        if (product.video == null && product.default ==1) {
                            var defaultImagePath = "{{$defaultItemImage_path}}";
                            row.find('.product-image').attr('src', product.imagePath);
                            row.find('.product-image').attr('data-src', product.imagePath);
                            row.find('.product-image').addClass('item_src_'+product.id);
                            row.find('.img_hover').attr('src', product.imagePath2);
                            // Colors
                            
                        } else {
                            var defaultImagePath = "{{$defaultItemImage_path}}";
                            row.find('.product_grid_inner_video_grid').find('ul').append('<li><video  width="100%"  loop muted preload="metadata" autoplay><source class="product-video" src="'+product.video+'" type="video/mp4"></video></li>');
                        }
                        @else
                        row.find('.pre_order').css('display','block');
                        row.find('.main_product_img a').attr('href', url_login);
                        row.find('.product-image').attr('src', defaultImagePath);
                        @endif
                    }

                        $('#product-container').append(row);
                        // let HiddenGrid = localStorage.getItem("HiddenGrid");
                        // if(HiddenGrid == 4){
                        //     $('#product-container .product_custom_padding').removeClass('col-md-6');
                        //     $('#product-container .product_custom_padding').addClass('col-md-3');
                        // }if(HiddenGrid == 2) {
                        //     $('#product-container .product_custom_padding').addClass('col-md-6');
                        //     $('#product-container .product_custom_padding').removeClass('col-md-3');
                        // }
                        // if ($.cookie('cq-view')!=="undefined") {
                        //     $('.four_grid').trigger('click');
                        // }
                        row.find('.product-image').attr('alt', product.name.toLowerCase());
                        row.find('.img_hover').attr('alt', product.name.toLowerCase());
                    });
                    // if(data == '')
                    // {
                    //     action = 'active';
                    // }
                    // else
                    // {
                    //     action = "inactive";
                    // }
                   

                    //on hover img chg

                    $(".main_product_area .product_wrapper").hover(function() {
                        $(this).addClass('on_hover');
                    }, function() {
                        $(this).removeClass('on_hover');
                    });
                });
            }
            if(action == 'inactive')
            {
                action = 'active';
                filterItem(limit, start);
            }
            $(window).scroll(function(){
                if($(window).scrollTop() + $(window).height() > $(".lazy").height() && action == 'inactive')
                {
                    action = 'active';
                    start = start + limit;
                    setTimeout(function(){
                        filterItem(limit, start);

                    }, 200);

                }
            });
            // Search item ajax query exit
            // Pagination
            $(document).on('click', '.page-link', function (e) {
                e.preventDefault();
                var url = $(this).attr('href');
                page = getURLParameter(url, 'page');

                filterItem(page);
            });

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

    <script>
        $('.dropdown-menu').click(function(event){
             event.stopPropagation();
         });
        </script>
@stop
