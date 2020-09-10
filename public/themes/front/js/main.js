// $(window).on("load", function() {
//     $('body,html').animate({
//         scrollTop: 0
//     }, 500);
// });
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
    $('#return-to-top').click(function() {
        $('body,html').animate({
            scrollTop: 0
        }, 500);
    });

    /*
   =========================================================================================
       WINDOW SCROOL
   =========================================================================================

   */
    (function() {
        $(window).on('scroll', function() {

            if ($(this).scrollTop() >= 50) {
                $('#return-to-top').fadeIn(200);
            } else {
                $('#return-to-top').fadeOut(200);
            }
        });
    }());



    $(".produc_shorting span").click(function() {
        $(this).toggleClass('active');
        $(this).parent().find('ul').slideToggle();
    });
    /*
    =========================================================================================
    2. MOBILE MENU
    =========================================================================================
    */

    winheight = $(window).height();
    mtheight = $('.l_f_top').outerHeight();
    mbheight = $('.l_m_bottom').outerHeight();
    mallHeight = mtheight + mbheight;
    menuOverFlowHeight = winheight - mallHeight;
    mobileMenuHeight = $('.m_menu_content').outerHeight(menuOverFlowHeight);
    // if (mobileMenuHeight > menuOverFlowHeight) {
    //     $('.m_menu_content').css({
    //         'overflow-y':'scroll',
    //         'height' : `${mobileMenuHeight}px`
    //     });
    // } else {
    //     $('.m_menu_content').css({
    //         'overflow-y':'hidden',
    //         'height' : `${mobileMenuHeight}px`
    //     });
    // }

    

    


    $(".navbar-toggler").click(function() {
        $(".mobile_menu").addClass('menu_open');
        return false;
    });
    // $('.mobile_menu #menu-content>li.has_subcat span').click(function() {
    //     return false;
    // });

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
    // if (w < 1199) {
    //     $(".navbar-toggler").click(function() {
    //         $(".mobile_menu").addClass('menu_open');
    //         return false;
    //     });
    //
    //     $('.m_menu_close').click(function() {
    //         $('.mobile_menu').removeClass('menu_open');
    //     });
    //     $(".header_area").removeClass("scrolled");
    // }


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


    // HEADER SEARCH
    $('.customer_care_ic a').hover(function() {
        $('.customer_care').addClass('customer_care_show');
        return false;
    });

    $('.close_c_care').click(function() {
        $('.customer_care').removeClass('customer_care_show');
    });


    /*=========================================================================================
        PRODUCT IMAGE CHANGE ON HOVER
    =========================================================================================
    */
    $(".main_product_area .product_wrapper ,.related_product_img_wrapper").hover(function() {
        $(this).addClass('on_hover');
    }, function() {
        $(this).removeClass('on_hover');
    });

    /*=========================================================================================
        ADD ACTIVE CLASS WHEN SELECTED
    =========================================================================================
    */
    $('.product_grid li , .color_variation li').click(function() {
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
    });

    /*=========================================================================================
        PRODUCT GRID SHOW
    =========================================================================================
    */
    $('.navbar-nav li').click(function() {
        localStorage.removeItem('HiddenGrid');

    });

    $('#two-col').click(function() {
        localStorage.setItem('HiddenGrid', 2);
        $('.product_custom_padding').addClass('col-md-4');
        $('.product_custom_padding').removeClass('grid_custom_wide');
    });
    $('#grid').click(function() {
        localStorage.setItem('HiddenGrid', 4);
        $('.product_custom_padding').removeClass('col-md-4');
        $('.product_custom_padding').addClass('grid_custom_wide');
    });


    /*=========================================================================================
         CART INCREASE DECREASE VALUE
    =========================================================================================
    */
    $(function() {
        $('.add').on('click', function() {
            var $qty = $(this).closest('.num_count').find('.qty');
            var currentVal = parseInt($qty.val());
            if (!isNaN(currentVal)) {
                $qty.val(currentVal + 1);
            }
        });
        $('.minus').on('click', function() {
            var $qty = $(this).closest('.num_count').find('.qty');
            var currentVal = parseInt($qty.val());
            if (!isNaN(currentVal) && currentVal > 0) {
                $qty.val(currentVal - 1);
            }
        });
    });

    /*
    =========================================================================================
    Footer Menu Triger
    =========================================================================================
    */
    $('.footer_menu_ p').click(function() {
        $(this).toggleClass('active')
        $(this).parent().find('ul').slideToggle();
    });


    //     $('.filter_heading ul li ').click(function() {
    // // alert('hi');
    //         if ($('.filter_submenu').hasClass( "show" ) ) {
    //            $('.main_product_area').addClass('no_padding_top')
    //         } else {
    //             $('.main_product_area').removeClass('no_padding_top')
    //         }


    //     });
    /*
    ===============================================
        MODAL
    ===============================================
    */


    $(function() {
        //----- OPEN
        $('[data-modal-open]').on('click', function(e) {
            var targeted_modal_class = jQuery(this).attr('data-modal-open');
            $('[data-modal="' + targeted_modal_class + '"]').addClass('open_modal');

            e.preventDefault();
        });

        //----- CLOSE
        $('[data-modal-close]').on('click', function(e) {
            var targeted_modal_class = jQuery(this).attr('data-modal-close');
            $('[data-modal="' + targeted_modal_class + '"]').removeClass('open_modal');

            e.preventDefault();
        });
    });

    $(function() {
        $("#draggable").draggable({
            axis: "y"
        });
    });

    /*
    =========================================================================================
    5.  product slider
    =========================================================================================
    */
    // $('.product_main_slide').slick({
    //  slidesToScroll: 1,
    //  slidesToShow: 1,
    //  rows: 0,
    //  dots: true,
    //  prevArrow: false,
    //  nextArrow: false,
    //  responsive: [{
    //      breakpoint: 1200,
    //      settings: {
    //          slidesToScroll: 1,
    //          slidesToShow: 1
    //      }
    //  }, {
    //      breakpoint: 1024,
    //      settings: {
    //          slidesToScroll: 1,
    //          slidesToShow: 1
    //      }
    //  }, {
    //      breakpoint: 768,
    //      settings: {
    //          slidesToScroll: 1,
    //          slidesToShow: 1
    //      }
    //  }]
    //  });

    // $('.product_main_slide').owlCarousel({
    
    

    var product_main_slide = jQuery(".product_main_slide");
    product_main_slide.owlCarousel({
        stagePadding: 20,
        loop: true,
        margin: 0,
        nav: false,
        responsive: {

            0: {

                items: 1
            },
            600: {

                items: 1
            },
            768: {
                items: 1
            },
            1000: {

                items: 2
            }
        }
    });
    
    
    var landing_page_top = jQuery("#landing_page_top");
    landing_page_top.owlCarousel({
        stagePadding: 20,
        loop: true,
        margin: 0,
        nav: false,
        responsive: {

            0: {

                items: 1
            },
            600: {

                items: 1
            },
            768: {
                items: 1
            },
            1000: {

                items: 1
            }
        }
    });


    var landing_page_newarrival_slider = jQuery(".landing_page_newarrival_slider");
    landing_page_newarrival_slider.owlCarousel({
        loop: true,
        margin: 10,
        nav: false,
        responsive: {

            0: {

                items: 2
            },
            600: {

                items: 2
            },
            768: {
                items: 3
            },
            1000: {

                items: 5
            }
        }
    });



    var banner_slider = jQuery(".banner_slider");
    banner_slider.owlCarousel({
        loop: true,
        margin: 10,
        nav: false,
        responsive: {

            0: {

                items: 1
            },
            600: {

                items: 1
            },
            768: {
                items: 1
            },
            1000: {

                items: 1
            }
        }
    });

    var LookBook = jQuery(".LookBook");
    LookBook.owlCarousel({
        loop: true,
        margin: 10,
        nav:true,
        navText : ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
        responsive: {

            0: {

                items: 1
            },
            600: {

                items: 1
            },
            768: {
                items: 1
            },
            1000: {

                items: 1
            }
        }
    });


    /*
    =========================================================================================
    5.  product slider
    =========================================================================================
    */
    $('.product_related_slide').slick({
        slidesToScroll: 1,
        slidesToShow: 2,
        centerMode: true,
        dots: true,
        prevArrow: false,
        nextArrow: false,
        responsive: [{
            breakpoint: 1200,
            settings: {
                slidesToScroll: 1,
                slidesToShow: 2
            }
        }, {
            breakpoint: 1024,
            settings: {
                slidesToScroll: 1,
                slidesToShow: 2
            }
        }, {
            breakpoint: 768,
            settings: {
                slidesToScroll: 1,
                slidesToShow: 2
            }
        }]
    });
    /*
    =========================================================================================
    5.  product slider
    =========================================================================================
    */
    $('.product_related_slide2').slick({
        slidesToScroll: 1,
        slidesToShow: 2,
        rows: 0,
        centerMode: true,
        dots: true,
        prevArrow: false,
        nextArrow: false,
        responsive: [{
            breakpoint: 1200,
            settings: {
                slidesToScroll: 1,
                slidesToShow: 2
            }
        }, {
            breakpoint: 1024,
            settings: {
                slidesToScroll: 1,
                slidesToShow: 2
            }
        }, {
            breakpoint: 768,
            settings: {
                slidesToScroll: 1,
                slidesToShow: 2
            }
        }]
    });

    $('.checkout_inner input ,.checkout_inner select').click(function() {
        $(this).parent().siblings().removeClass('show');
        $(this).parent().addClass('show');
    });


    $('.checkout_inner input').each(function() {
        var dInput = this.value;
        if (dInput) {
            $(this).parent().addClass('hasvalue');
            if ($(this).val() == '') { // check if value changed
                $(this).parent().removeClass('hasvalue');
            }
        } else {
            $(this).parent().removeClass('hasvalue');
        }
    });
    $('.checkout_inner input').keyup(function() {
        var dInput = this.value;
        if (dInput) {
            $(this).parent().addClass('hasvalue');
            if ($(this).val() == '') { // check if value changed
                $(this).parent().removeClass('hasvalue');
            }
        } else {
            $(this).parent().removeClass('hasvalue');
        }
    });

    // $(".checkout_inner .custom_checkbox label").click(function(){
    //     $(".billing_address").toggle();
    // });


    /*
    =========================================================================================
    product slider
    =========================================================================================
    */
    $('.single_img_thumb').slick({
        vertical: true,
        verticalSwiping: true,
        slidesToShow: 4,
        slidesToScroll: 1,
        dots: false,
        arrow: false,
        focusOnSelect: false,
        infinite: false,
    });

    $('.product_main_slide_wrapper').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        dots: false,
        arrow: false,
        focusOnSelect: false,
        infinite: false,
        responsive: [{
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
        arrow: false,
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


/*
        =========================================================================================
        5.  BANNER SLIDER
        =========================================================================================
        */


$('.banner_slider').on('init', function(ev, el) {
    $('video').each(function() {
        this.play();
    });
});


var slideWrapper = $(".banner_slider"),
    iframes = slideWrapper.find('.embed-player'),
    lazyImages = slideWrapper.find('.slide-image'),
    lazyCounter = 0;

// POST commands to YouTube or Vimeo API
function postMessageToPlayer(player, command) {
    if (player == null || command == null) return;
    player.contentWindow.postMessage(JSON.stringify(command), "*");
}

// When the slide is changing
function playPauseVideo(slick, control) {
    var currentSlide, slideType, startTime, player, video;

    currentSlide = slick.find(".slick-current");
    slideType = currentSlide.attr("class").split(" ")[1];
    player = currentSlide.find("iframe").get(0);
    startTime = currentSlide.data("video-start");

    if (slideType === "video") {
        video = currentSlide.children("video").get(0);
        if (video != null) {
            if (control === "play") {
                video.play();
            } else {
                video.pause();
            }
        }
    }
}

// Resize player
function resizePlayer(iframes, ratio) {
    if (!iframes[0]) return;
    var win = $(".banner_slider"),
        width = win.width(),
        playerWidth,
        height = win.height(),
        playerHeight,
        ratio = ratio || 16 / 9;

    iframes.each(function() {
        var current = $(this);
        if (width / ratio < height) {
            playerWidth = Math.ceil(height * ratio);
            current.width(playerWidth).height(height).css({
                left: (width - playerWidth) / 2,
                top: 0
            });
        } else {
            playerHeight = Math.ceil(width / ratio);
            current.width(width).height(playerHeight).css({
                left: 0,
                top: (height - playerHeight) / 2
            });
        }
    });
}

// DOM Ready
$(function() {
    // $(".banner_slider").not('.slick-initialized').slick({
    //     slidesToShow: 1,
    //     slidesToScroll: 1,
    //     autoplay: false,
    //     arrows: false,
    //     dots: false,
    //     prevArrow: false,
    //     nextArrow: false,
    //     pauseOnHover: false,
    //     autoplaySpeed: 5000,
    // });



    $('.newarrivalitem_slider').slick({
        dots: true,
        infinite: false,
        speed: 300,

        padding: 15,
        slidesToShow: 5,
        slidesToScroll: 5,
        responsive: [{
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    infinite: true,
                    dots: true
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
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });
    var el = document.getElementById('home-video');
    if (el) {
        document.getElementById('home-video').addEventListener('ended', function() {
            $(".banner_slider").not('.slick-initialized').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: true,
                arrows: true,
                dots: true,
                prevArrow: false,
                nextArrow: false,
                pauseOnHover: false,
                autoplaySpeed: 5000,
            });
            $(".banner_slider").slick('slickNext');
        });

    }


});

// Resize event
$(window).on("resize.slickVideoPlayer", function() {
    resizePlayer(iframes, 16 / 9);
});

function productSinglefixed() {
    $(window).on('scroll', function() {
        var windowScroll = $(window).scrollTop();
        var windowHeight = $(window).height();
        var s_p_length = $(".single_img_thumb_wrap").length;
        if (s_p_length == true) {
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
function productSingleright() {
    $(window).on('scroll', function() {
        var windowScroll = $(window).scrollTop();
        var windowHeight = $(window).height();
        var sidelength = $(".single_product_description").length;
        var s_p_width = $(".single_product_description_wrapper").width();
        if (sidelength == true) {
            offsetleftSize = $('.single_product_description').offset().top;
            offsetFitSize = $('#related_product_area').offset().top;
            descHeight = $('.single_product_description').outerHeight();
            transformHeight = offsetFitSize - windowHeight;
            if (windowScroll > transformHeight) {
                $('.s_p_d_fixed').css({
                    'position': 'relative',
                    'z-index': 3,
                    '-webkit-transform': `translate3d(0px, ${transformHeight}px, 0px)`,
                    '-moz-transform': `translate3d(0px, ${transformHeight}px, 0px)`,
                    '-ms-transform': `translate3d(0px, ${transformHeight}px, 0px)`,
                    '-o-transform': `translate3d(0px, ${transformHeight}px, 0px)`,
                    'transform': `translate3d(0px, ${transformHeight}px, 0px)`
                });
            } else {
                $('.s_p_d_fixed').css({
                    'position': 'fixed',
                    'width': `${s_p_width}px`,
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

function productSinglerightmobile() {
        var sidelength = $(".single_product_description_wrapper").width();
        if (sidelength == true) {
            offsetleftSize = $('.single_product_description').offset().top;
            offsetFitSize = $('#related_product_area').offset().top;
            descHeight = $('.single_product_description').outerHeight();
            transformHeight = offsetFitSize - windowHeight;
            if (windowScroll > transformHeight) {
                $('.single_product_description').css({
                    'position': 'relative!important',
                    'z-index': 3,
                    'width': '100%!important',
                    '-webkit-transform': 'translate3d(0px, 0px, 0px)',
                    '-moz-transform': 'translate3d(0px, 0px, 0px)',
                    '-ms-transform': 'translate3d(0px, 0px, 0px)',
                    '-o-transform': 'translate3d(0px, 0px, 0px)',
                    'transform': 'translate3d(0px, 0px, 0px)'
                });
            } else {
                $('.single_product_description').css({
                    'position': 'relative!important',
                    'z-index': 3,
                    'width': '100%!important',
                    '-webkit-transform': 'translate3d(0px, 0px, 0px)',
                    '-moz-transform': 'translate3d(0px, 0px, 0px)',
                    '-ms-transform': 'translate3d(0px, 0px, 0px)',
                    '-o-transform': 'translate3d(0px, 0px, 0px)',
                    'transform': 'translate3d(0px, 0px, 0px)'
                });
            }
        }
}


function resize() {
    if ($(window).width() > 767) {
     $('.single_product_description').addClass('s_p_d_fixed');
     productSingleright();
    }
    else {
        productSinglerightmobile();
        $('.single_product_description').removeClass('s_p_d_fixed');
        
    }
}

$(document).ready( function() {
    $(window).resize(resize);
    resize();
    productSinglefixed();
    productSingleright();
    productSinglerightmobile()
});



