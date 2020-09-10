$(function () {
    $('.new_vendor_item').click(function () {
        var id = $(this).data('id');
        var open = 1;

        if ($('#new_vendor_products_'+id).hasClass('d-none'))
            open = 0;

        $('.new_vendor_products').addClass('d-none');
        $('.new_vendor_item').find('img').removeClass('nv-active');


        if (open == 0) {
            $('#new_vendor_products_' + id).removeClass('d-none');
            $(this).find('img').addClass('nv-active');
        }
    });

    // Main Slider
    var current = 0;
    var total = $('.ms-vendor').length;
    var interval;
    var duration = 5000;

    $('.ms-vendor').click(function () {
        var id = $(this).data('id');
        var index = $('.ms-vendor').index($(this));
        current = index;

        $('.ms-banner').removeClass('ms-banner-active');
        $('#ms_banner_'+id).addClass('ms-banner-active');
        $('.ms-products').removeClass('ms-product-ul-active');
        $('#ms_products_'+id).addClass('ms-product-ul-active');
        $('.ms-vendor').removeClass('active');
        $(this).addClass('active');
    });

    if ($('.ms-vendor').length > 0) {
        var random = Math.floor((Math.random() * total));
        current = random;
        $('.ms-vendor:eq('+random+')').trigger('click');
        interval = setInterval(function() {
                $('.ms-next').trigger('click');
            }
        , duration);
    }

    $('.main-slider').mouseenter(function () {
        $('.ms-next, .ms-previous').addClass('ms-control-active');
        clearInterval(interval);
    });

    $('.main-slider').mouseleave(function () {
        $('.ms-next, .ms-previous').removeClass('ms-control-active');

        interval = setInterval(function() {
                $('.ms-next').trigger('click');
            }
            , duration);
    });

    $('.ms-next').click(function () {
        current++;

        if (current == total)
            current = 0;

        $('.ms-vendor:eq('+current+')').trigger('click');
    });

    $('.ms-previous').click(function () {
        current--;

        if (current < 0)
            current = total -1;

        $('.ms-vendor:eq('+current+')').trigger('click');
    });

    // Category Top Slider

    var cts_current = 0;
    var cts_total = $('.cts_nav').length;
    var cts_interval;
    var cts_duration = 5000;

    $('.cts_nav').click(function () {
        var id = $(this).data('id');
        var index = $('.cts_nav').index($(this));
        cts_current = index;

        $('.cts_title').addClass('d-none');
        $('#cts_title_'+id).removeClass('d-none');
        $('.cts_items_ul').addClass('d-none');
        $('#cts_items_ul_'+id).removeClass('d-none');
        $('.cts_nav').removeClass('active');
        $(this).addClass('active');
    });

    if ($('.cts_nav').length > 0) {
        var random = Math.floor((Math.random() * cts_total));
        cts_current = random;
        $('.cts_nav:eq('+random+')').trigger('click');

        cts_interval = setInterval(function() {
            ctsNext();
        }, cts_duration);
    }

    $('.category-top-slider').mouseenter(function () {
        clearInterval(cts_interval);
    });

    $('.category-top-slider').mouseleave(function () {
        cts_interval = setInterval(function() {
            ctsNext();
        }, cts_duration);
    });

    function ctsNext() {
        cts_current++;
        if (cts_current == cts_total)
            cts_current = 0;

        $('.cts_nav:eq('+cts_current+')').trigger('click');
    }

    // Category Second Slider

    var cs2_current = 0;
    var cs2_total = $('.cs2_nav').length;
    var cs2_interval;
    var cs2_duration = 5000;

    $('.cs2_nav').click(function () {
        var id = $(this).data('id');
        var index = $('.cs2_nav').index($(this));
        cs2_current = index;

        $('.cs2_title').addClass('d-none');
        $('#cs2_title_'+id).removeClass('d-none');
        $('.cs2_items_ul').addClass('d-none');
        $('#cs2_items_ul_'+id).removeClass('d-none');
        $('.cs2_nav').removeClass('active');
        $(this).addClass('active');
    });

    if ($('.cs2_nav').length > 0) {
        var random = Math.floor((Math.random() * cs2_total));
        cs2_current = random;
        $('.cs2_nav:eq('+random+')').trigger('click');

        cs2_interval = setInterval(function() {
            cs2Next();
        }, cs2_duration);
    }

    $('.category-slider-2').mouseenter(function () {
        clearInterval(cs2_interval);
    });

    $('.category-slider-2').mouseleave(function () {
        cs2_interval = setInterval(function() {
            cs2Next();
        }, cs2_duration);
    });

    function cs2Next() {
        cs2_current++;
        if (cs2_current == cs2_total)
            cs2_current = 0;

        $('.cs2_nav:eq('+cs2_current+')').trigger('click');
    }

    // New Vendor Slider
    var nvs_current = 0;
    var nvs_total = $('.nvs-tab-item').length;
    var nvs_interval;
    var nvs_duration = 5000;

    $('.nvs-tab-item').mouseover(function () {
        var id = $(this).data('id');
        var index = $('.nvs-tab-item').index($(this));
        nvs_current = index;

        $('.nvs-items-container').addClass('d-none');
        $('#nvs-items-container-'+id).removeClass('d-none');
        $('.nvs-tab-item').removeClass('active');
        $(this).addClass('active');
    });

    if ($('.nvs-tab-item').length > 0) {
        var random = Math.floor((Math.random() * nvs_total));
        nvs_current = random;
        $('.nvs-tab-item:eq('+random+')').trigger('mouseover');

        nvs_interval = setInterval(function() {
            nvsNext();
        }, nvs_duration);
    }

    $('.new-vendor-slider').mouseenter(function () {
        clearInterval(nvs_interval);
    });

    $('.new-vendor-slider').mouseleave(function () {
        nvs_interval = setInterval(function() {
            nvsNext();
        }, nvs_duration);
    });

    function nvsNext() {
        nvs_current++;
        if (nvs_current == nvs_total)
            nvs_current = 0;

        $('.nvs-tab-item:eq('+nvs_current+')').trigger('mouseover');
    }

    // Mobile Main Slider
    var mobile_current = 0;
    var mobile_total = $('.mobile-main-slider-item').length;
    var mobile_interval;
    var mobile_duration = 5000;

    if (mobile_total > 0) {
        var random = Math.floor((Math.random() * mobile_total));
        mobile_current = random;

        $('.mobile-main-slider-item:eq('+random+')').removeClass('d-none');

        mobile_interval = setInterval(function() {
            mobileNext();
        }, mobile_duration);
    }

    function mobileNext() {
        mobile_current++;
        if (mobile_current == mobile_total)
            mobile_current = 0;

        $('.mobile-main-slider-item').addClass('d-none');
        $('.mobile-main-slider-item:eq('+mobile_current+')').removeClass('d-none');
    }
});