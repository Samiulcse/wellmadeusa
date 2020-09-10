<?php
    use App\Enumeration\Role;
use App\Enumeration\Availability;
?>

@extends('layouts.home_layout')

@section('additionalCSS')

@stop

@section('content') 
 
<!-- =========================
        START HOME PRODUCT SECTION
    ============================== -->
<section class="product_area common_top_margin single_page_bradcrumb">
    <div class="container">
        <div class="main_title  ">
            <div class="row">  
            <div class="col-md-6"> 
                    <h2>@php echo $category->name; @endphp</h2> 
                </div> 
            <div class="col-md-6">  
                    <div class="filter_heading  category_page_bredcumb">
                        <ul>
                            <li><a href="{{ route('home') }}">HOME</a></li> 
                            <li>{{$category->name}}</li> 
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- =========================
           PRODUCT FILTER
        ============================== -->
        <div  class="product_filter clearfix filter_desktop">
            <div class="product_grid">
                <ul>
                    <!--<li>-->
                    <!--    <svg id="grid" viewBox="0 0 26 26">-->
                    <!--        <path class="st0" d="M0,0v11.4h11.4V0H0z M9.7,9.7h-8v-8h8V9.7z"></path>-->
                    <!--        <path class="st0" d="M14.6,0v11.4H26V0H14.6z M24.3,9.7h-8v-8h8V9.7z"></path>-->
                    <!--        <path class="st0" d="M0,14.6V26h11.4V14.6H0z M9.7,24.3h-8v-8h8V24.3z"></path>-->
                    <!--        <path class="st0" d="M14.6,14.6V26H26V14.6H14.6z M24.3,24.3h-8v-8h8V24.3z"></path>-->
                    <!--    </svg>-->
                    <!--</li>-->
                    <!--<li>-->
                    <!--    <svg id="two-col" viewBox="0 0 26 26">-->
                    <!--        <path class="st0" d="M0,0v26h11.4V0H0z M9.8,24.3H1.7v-6.6v-8v-8h8h0.1V24.3z"></path>-->
                    <!--        <path class="st0" d="M14.6,0v26H26V0H14.6z M24.4,24.3h-8.1v-6.6v-8v-8h8h0.1V24.3z"></path>-->
                    <!--    </svg>-->
                    <!--</li>-->
                </ul>
            </div>
            <div class="produc_shorting">
                <span class="" role="button"  >Sort By</span>
                <ul>
                    <input type="hidden" id="sorting" value="">
                    <li class="sorting" data-type="1" data-name="Newest to Oldest">Newest to Oldest</li>
                    <li class="sorting" data-type="2" data-name="Lowest to Highest Price">Lowest to Highest Price</li>
                    <li class="sorting" data-type="3" data-name="Highest to Lowest Price">Highest to Lowest Price</li>
                    <li class="sorting" data-type="4" data-name="Style">Style </li>
                </ul>
            </div>
        </div> 
        <!-- ============================
           PRODUCT THUMBNAIL
        ============================== -->
        <div class="main_product_area"> 
                <div class="row product_custom_margin" id="product-container"> </div>
                <template id="template-product" >
                    <div class=" custom_grid_child product_custom_padding">
                        <div class="product_wrapper">
                            <div class="pre_order">
                                <span><img src="{{ asset('images/pre_order.png') }}" height="100px" width="100px" alt=""></span>
                            </div>
                            <div class="main_product_img">
                                <a href="#">
                                <img src="" class="img-fluid product-image " alt="" data-src="">
                                <img src="" class="img_hover " alt="">
                                </a>
                            </div>
                            <div class="main_product_text">
                                 <h2><a href="#" class="p_style title_for_lg trim-text"></a></h2>
                                <p class="price"></p>
                                <p class="pre_order_date"></p>
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
                                <p class="pre_order_date"></p>
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
        var url_loader = "{{URL::to('/')}}/images/preloader.gif";
        var url_login = "{{URL::to('/')}}/login/";
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var auth = "@if (Auth::check() && Auth::user()->role == Role::$BUYER) 1 @else 0 @endif"
             

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

            //Sort Filtering
            $('.sorting').click(function () {
                var name = $(this).data('name'); 
                $(this).parent().parent().find('span').html("Sort By/"+name); 
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
                if ($(this).hasClass('active'))
                    $(this).removeClass('active');
                else
                    $(this).addClass('active');
                filterItem();
            });

            $('#btn-search').click(function () {
                search_text = $('#search-input').val();
                search_option = $('input[name=search-component]:checked').val();
                search_price_min = $('#search-price-min').val();
                search_price_max = $('#search-price-max').val();

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
                var categories = ['{{ $category->id }}'];
                var vendors = [];
                var masterColors = [];
                var masterCategory = [];
                var bodySizes = [];
                var patterns = [];
                var lengths = [];
                var styles = [];
                var masterfeb = [];
                var packs = [];
                var fabrics = [];
                var sorting = $('#sorting').val();
                var looged_in = 0;  
                var limit_f = limit;
                var start_f = start;

                // Vendor
                $('.vendor-checkbox').each(function () {
                    if ($(this).is(':checked'))
                        vendors.push($(this).data('id'));
                });

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



                // Master Febric
                $('.febricid').each(function () {
                    if ($(this).hasClass('feb-selected'))
                        masterfeb.push($(this).data('id'));
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
                $.ajax({
                    method: "POST",
                    url: "{{ route('get_items_category') }}",
                    
                    data: {
                        categories: categories,
                        vendors: vendors,
                        masterColors: masterColors,
                        masterCategory: masterCategory,
                        bodySizes: bodySizes,
                        patterns: patterns, 
                        masterfeb: masterfeb, 
                        packs: packs,
                        lengths: lengths,
                        styles: styles, 
                        fabrics: fabrics,
                        sorting: sorting,
                        searchText: search_text,
                        searchOption: search_option,
                        priceMin: search_price_min,
                        priceMax: search_price_max,
                        limit: limit_f,
                        start: start_f,
                        page: page
                    }
                }).done(function (data) {
                    var products = data.items;
                    $('.pagination').html(data.pagination);
                    var defaultImagePath = "{{$defaultItemImage_path}}";
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
                            
                            
                    if(product.available_on != null){ 
                        row.find('.pre_order_date').html("pre order: "+product.available_on);
                    }else{
                        row.find('.pre_order_date').hide();
                    }
                    if (product.default_parent_category != 6) {
                        row.find('.pre_order').css('display','none');
                        row.find('.main_product_img a').attr('href', url+''+product.slug);
                       
                        row.find('.p_style').attr('href', url+''+product.slug); 
                        if(auth == 1)
                            if(product.orig_price){ 
                                row.find('.price').addClass('text-danger');
                                row.find('.price').html( "<del>$"+product.orig_price+"</del> "+product.price );
                            }else{
                                row.find('.price').html(product.price); 
                            }
                        else
                            row.find('.price').html('<a href="{{ route('buyer_login') }}">Login to See Price </a>');
                                               
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
                            if(auth==1){
                                row.find('.pre_order').css('display','block');
                                row.find('.main_product_img a').attr('href', url + '' + product.slug);
                                row.find('.p_style').attr('href', url + '' + product.slug);
                                row.find('.video_url').attr('href', url + '' + product.slug);
                                row.find('.price').html(product.price);

                                if (product.video == null && product.default ==1) {
                                    row.find('.product-image').attr('src', product.imagePath);
                                    row.find('.product-image').attr('data-src', product.imagePath);
                                    row.find('.product-image').addClass('item_src_' + product.id);
                                    row.find('.img_hover').attr('src', product.imagePath2);
                                } else {
                                    row.find('.product_grid_inner_video_grid').find('ul').append('<li><video  width="100%"  loop muted preload="metadata" autoplay><source class="product-video" src="' + product.video + '" type="video/mp4"></video></li>');
                                }
                            }
                            else{
                                row.find('.pre_order').css('display','block');
                                row.find('.main_product_img a').attr('href', url_login);
                                row.find('.price').html('<a href="{{ route('buyer_login') }}">Login to See Price </a>');
                                row.find('.product-image').attr('src', defaultImagePath);
                            }
                            
                        }
                        row.find('.product-image').attr('alt', product.name.toLowerCase());
                        row.find('.img_hover').attr('alt', product.name.toLowerCase());
                        $('#product-container').append(row);
                          // let HiddenGrid = localStorage.getItem("HiddenGrid");
                        // if(HiddenGrid == 4){
                        //     $('#product-container .product_custom_padding').removeClass('col-md-4');
                        //     $('#product-container .product_custom_padding').addClass('grid_custom_wide');
                        // }if(HiddenGrid == 2) {
                        //     $('#product-container .product_custom_padding').addClass('col-md-4');
                        //     $('#product-container .product_custom_padding').removeClass('grid_custom_wide');
                        // }
                       
                    });
                    //   if(data == '')
                    // {
                    //     action = 'active';
                    // }
                    // else
                    // {
                    //     action = "inactive";
                    // }

                    if ($.cookie('cq-view')!=="undefined") {
                        $('.four_grid').trigger('click');
                    }

    

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
                if($(window).scrollTop() + $(window).height() > $("#product-container").height() && action == 'inactive')
                {
                    action = 'active';
                    start = start + limit;
                    setTimeout(function(){
                        filterItem(limit, start);

                    }, 200);

                }
            });
            // category item ajax query exit

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
