$(document).ready(function() {
    var w = $(window).width();
    if (w > 1199) {
        $(window).on('scroll', function() {
            if ($(window).scrollTop() > 0) {
                $(".header_area").addClass("scrolled");
            } else {
                $(".header_area").removeClass("scrolled");
            }
        });
    }

    /*
    =========================================================================================
    2. MOBILE MENU
    =========================================================================================
    */

    $(".navbar-toggler").click(function() {
        $(".mobile_menu").addClass('menu_open');
        return false;
    });

    $('.navbar-toggler').click(function() {
        $('.mobile_menu').addClass('menu_open');
        $('.mobile_overlay').addClass('mobile_overlay_open');
        $('.ic_c_mb').addClass('ic_c_mb_show');
        return false;
    });
    $('.ic_c_mb').click(function() {
        $('.mobile_menu').removeClass('menu_open');
        $('.mobile_overlay').removeClass('mobile_overlay_open');
        $('.ic_c_mb').removeClass('ic_c_mb_show');
    });
    $('.mobile_overlay').click(function() {
        $('.mobile_menu').removeClass('menu_open');
        $('.mobile_overlay').removeClass('mobile_overlay_open');
        $('.ic_c_mb').removeClass('ic_c_mb_show');
    });

    $(window).resize(function() {
        var w = $(window).width();

        if (w > 1199) {
            $(window).on('scroll', function() {
                if ($(window).scrollTop() > 0) {

                    $(".header_area").addClass("scrolled");
                } else {
                    $(".header_area").removeClass("scrolled");
                }
            });
        }
        if (w < 1199) {
            $(".header_area").removeClass("scrolled");
        }


    });


    $(".nav-item.has_dropdown").hover(function() {

        $(this).find('.megamenu').addClass('megamenu_open');
        $('.main_header').addClass('has_submenu');

    }, function() {

        $(this).find('.megamenu').removeClass('megamenu_open');
        $('.main_header').removeClass('has_submenu');

    });


    // HEADER SEARCH
    $('.header_search_ic').click(function() {
        $('.header_search').toggleClass('search_show');
        return false;
    });


    /*
    =========================================================================================
    product slider
    =========================================================================================
    */
    $('.single_img_thumb').slick({
            vertical:true,
            verticalSwiping:true,
            slidesToShow: 4,
            slidesToScroll: 1,
            dots: false,
            arrow : false,
            focusOnSelect: false,
            infinite: false,
    });
    
    $('.product_main_slide_wrapper').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        dots: false,
        arrow : false,
        focusOnSelect: false,
        infinite: false,
        responsive: [
            {
              breakpoint: 1024,
              settings: {
                slidesToShow: 3,
                slidesToScroll: 1,
              }
            },
            {
              breakpoint: 600,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 2
              }
            },
            {
              breakpoint: 480,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 2
              }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
          ]
    });

    $('.single_img_thumb_mobile').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: false,
        arrow : false,
        focusOnSelect: false,
        infinite: false,
    });
      
    $(".single_img_thumb .slide").click(function() {
        var TargetId = $(this).attr('href');
        $('html, body').animate({
            scrollTop: $(TargetId).offset().top - 130
        }, 1000, 'swing');
        return false;
    });
    
    $('.s_on_hover').zoom();

    $('.s_p_size li').click(function() {
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
    });

    /*
    =========================================
    FIXED PRODUCT DESCRIPTION
    =========================================
    */
   var s_p_width = $(".single_product_description_wrapper").width();
    if ($(window).width() > 767) {
        productSinglefixed()
    } 

});




function productSinglefixed() {
    $(window).on('scroll', function() {
        var windowScroll = $(window).scrollTop();
        var windowHeight = $(window).height();
        var sidelength = $(".single_product_description").length;
        var s_p_width = $(".single_product_description_wrapper").width();
        if (sidelength == true) {
            offsetleftSize = $('.single_product_description').offset().top;
            offsetFitSize = $('#related_product_area').offset().top;
            descHeight = $('.single_product_description').outerHeight();
            transformHeight = offsetFitSize  - windowHeight;
            if (windowScroll > transformHeight) {
                $('.single_product_description').css({
                    'position': 'relative',
                    'z-index': 3,
                    '-webkit-transform': `translate3d(0px, ${transformHeight}px, 0px)`,
                    '-moz-transform': `translate3d(0px, ${transformHeight}px, 0px)`,
                    '-ms-transform': `translate3d(0px, ${transformHeight}px, 0px)`,
                    '-o-transform': `translate3d(0px, ${transformHeight}px, 0px)`,
                    'transform': `translate3d(0px, ${transformHeight}px, 0px)`
                });
            } else {
                $('.single_product_description').css({
                    'position': 'fixed',
                    'width' : `${s_p_width}px`,
                    '-webkit-transform': 'translate3d(0px, 0px, 0px)',
                    '-moz-transform': 'translate3d(0px, 0px, 0px)',
                    '-ms-transform': 'translate3d(0px, 0px, 0px)',
                    '-o-transform': 'translate3d(0px, 0px, 0px)',
                    'transform': 'translate3d(0px, 0px, 0px)'
                });
            }
        }
    
        var s_p_width = $(".single_img_thumb_wrap").width();
        if (sidelength == true) {
            offsetleftSize = $('.single_img_thumb_wrap').offset().top;
            offsetFitSize = $('#related_product_area').offset().top;
            descHeight = $('.single_img_thumb_wrap').outerHeight();
            headerHeight = $('.header_area').outerHeight();
            transformHeight = offsetFitSize - windowHeight;
            if (windowScroll > transformHeight) {
                $('.single_img_thumb_wrap').css({
                    'position': 'relative',
                    'z-index': 3,
                    '-webkit-transform': `translate3d(0px, ${transformHeight}px, 0px)`,
                    '-moz-transform': `translate3d(0px, ${transformHeight}px, 0px)`,
                    '-ms-transform': `translate3d(0px, ${transformHeight}px, 0px)`,
                    '-o-transform': `translate3d(0px, ${transformHeight}px, 0px)`,
                    'transform': `translate3d(0px, ${transformHeight}px, 0px)`
                });
            } else {
                $('.single_img_thumb_wrap').css({
                    'position': 'fixed',
                    '-webkit-transform': 'translate3d(0px, 0px, 0px)',
                    '-moz-transform': 'translate3d(0px, 0px, 0px)',
                    '-ms-transform': 'translate3d(0px, 0px, 0px)',
                    '-o-transform': 'translate3d(0px, 0px, 0px)',
                    'transform': 'translate3d(0px, 0px, 0px)'
                });
            }
        }
    });
}

// function productSingleMobile() {
//     $(window).on('scroll', function() {
//         var aclength = $('.add_to_cart_btn_mob').length;
//         var windowScroll = $(window).scrollTop();
//         var windowHeight = $(window).height();
//         if(aclength == true) {
//             var aclength = $('.add_to_cart_btn_mob').length;
//             var windowScroll = $(window).scrollTop();
//             var windowHeight = $(window).height();
//             var acoffset = $('.add_to_cart_btn_mob').offset().top;
//             if (windowScroll > acoffset - windowHeight) {
//                 $('.add_to_cart_btn_mob').removeClass('ac_fixed');
//             } else {
//                 $('.add_to_cart_btn_mob').addClass('ac_fixed');
//             }
//         }
//     }); 
// }


$(window).resize(function() {
    productSinglefixed();
    // productSingleMobile();
});