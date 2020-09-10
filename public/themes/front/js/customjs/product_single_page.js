$(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
     
    var auth = $("#user_login").val();
    if(auth ==1){
        $('.s_on_hover').zoom();
    }  
    var preorder = $(".insNoutStack").val();
    if (preorder == 'null') {
        $(".available_on_date").html('In Stock')
    } else {
        if (parseInt(preorder)) {
            $(".available_on_date").html('PRE ORDER: ' + preorder)
        } else {
            $(".available_on_date").html('Out of Stock')
        }
    }
    
    // var topmartin =  $(".header_area").outerHeight(); 
    // $("#cartSuccessMessage").css({
    //     top: `${topmartin}px`
    // })


    $(document).on('click', '#single_color_variation a', function() {
        jQuery('html,body').animate({ scrollTop: 0 });
        var itemid = $(this).data('itemid');
        var colorid = $(this).data('colorid');
        var color_name = $(this).data('color_name');
        var color_img = $(this).data('colorimg');
        $("#selectedcolor").val(colorid);
        $("#dropdownMenuButton img.img-fluid").attr('src', color_img);
        $('.active_colorname').html(color_name);
        AjaxItemSelect(itemid, colorid);
    });

    function AjaxItemSelect(itemid, colorid) {
        var url = $("#color_choose_url").val();
        $.ajax({
            method: "POST",
            url: url,
            data: {
                colorid: colorid,
                itemid: itemid
            }
        }).done(function(data) {
            var loginimage = data.defaultItemImage_path;
            var oLoc = document.location,
                sUrl = oLoc.protocol + oLoc.hostname;
            var imgurl = oLoc.origin + "/";
            var output = '';
            var counter = 1;
            var active = '';
            $(".single_img_thumb").empty();
            $(".single_img").empty();
            $(".single_img_popup").empty();
            if (data.inventory) {
                if (data.inventory.available_on == 'null') {
                    $(".available_on_date").html('In Stock')
                } else {
                    if (parseInt(data.inventory.available_on)) {
                        $(".available_on_date").html('PRE ORDER: ' + data.inventory.available_on)
                    } else {
                        $(".available_on_date").html("Out of Stock")
                    }
                }
            }

            let slider_main_wrap_desktop = document.getElementById("single_product_img");
            slider_main_wrap_desktop.outerHTML = '<div class="single_img_thumb_wrap" id="single_product_img"><div class="single_img_thumb"></div></div>';

            let slider_main_wrap_mobile = document.getElementById("single_product_img_mob");
            slider_main_wrap_mobile.outerHTML = '<div class="single_product_left below_mobile" id="single_product_img_mob"><div class="single_img_thumb_mobile"></div></div>';

            let popup_slider_zoom = document.getElementById("popup_slider_zoom");
            popup_slider_zoom.outerHTML = '<div class="modal-body" id="popup_slider_zoom"><div class="single_img_popup"></div></div>';

            $.each(data.image, function(i, e) { 
                if(loginimage== null){
                    var image_path_thumb = imgurl + e.thumbs_image_path;
                    var image_path = imgurl + e.image_path; 
                }else{ 
                    var image_path_thumb = loginimage;
                    var image_path = loginimage;
                }
                var data = `<div class="slide" href="#sp${e.id}" ><img src="${image_path_thumb}" alt="" class="img-fluid"></div>`;
                var mainslider = `<div class="s_on_hover" data-toggle="modal" data-target="#exampleModal" >
                <div id="sp${e.id}" data-id="{e.id}"><img src="${image_path}" alt="" class="img-fluid"></div></div> `;
                var mob_slider = `<div class="slide" href="#sp${e.id}"><img src="${image_path}" alt="" class="img-fluid"></div>  `;
                var popupslide = `<div class="slide" id="img_${e.id}}" data-slick_index="${e.id}"><img src="${image_path}" alt="" class="img-fluid"></div>`;
                $('.single_img_thumb_mobile').append(mob_slider);
                $('.single_img_thumb').append(data);
                $('.single_img').append(mainslider);
                $('.single_img_popup').append(popupslide);
            });


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
            $("#exampleModal").on("shown.bs.modal", function() {
                $('.single_img_popup').slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    dots: false,
                    navText: ["<i class='lni-chevron-left'></i>", "<i class='lni-chevron-right'></i>"],
                    focusOnSelect: false,
                    infinite: false,
                });
            });
            if(auth ==1){
                $('.s_on_hover').zoom();
            } 

        });
    }


    // Add to cart click function start
    $('.btnAddToCart').click(function() { 
        var selected_quantity = $(".select_quantity").val();
        var colors = $("#selectedcolor").val();
        var itemId = $('#single_item_name').data('itemid');
        var vendor_id = '';
        var valid = true;

        if (colors == 0) {
            alert('Please select item Color.');
            return;
        }

        if (selected_quantity == 0) {
            alert('Please select item quantity.');
            return;
        }
        var url = $("#add_cart_url").val();
        var check_login = $("#user_login").val();
        var login_url = $("#login_url").val();
        if (check_login == 1) {
            $.ajax({
                method: "POST",
                url: url,
                data: { itemId: itemId, colors: colors, qty: selected_quantity, vendor_id: vendor_id }
            }).done(function(data) {
                if (data.success) {
                    $(".shopping_cart .mini-cart-sub").load(location.href+" .shopping_cart .mini-cart-sub>*","");
                    $(".shopping_cart a span").html(data.qty) 
                    $('#cartSuccessMessage').slideDown('slow', function() {
                        $('#message').html('Add to Cart Successfully !!');
                    });
                    setTimeout(function() {
                        $('#cartSuccessMessage').slideUp('slow');
                        // location.reload();
                    }, 1500);
                } else {
                    alert(data.message);
                }
            });
        } else {
            window.location.href = login_url;
        } 
    });

    $(".p_size_guide span").click(function() {
        $('.sizeguide').toggleClass('hide');
    });

    $(".close_sizeguide_modal").click(function() {
        $('.sizeguide').addClass('hide');
    });

    $("#play_video").click(function() {
        $('.videoFrame').toggleClass('hide');
    });

    $(".close_playvideo_modal").click(function() {
        $('.videoFrame').addClass('hide');
    });



    $("#exampleModal").on("shown.bs.modal", function() {
        $('.single_img_popup').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: false,
            navText: ["<i class='lni-chevron-left'></i>", "<i class='lni-chevron-right'></i>"],
            focusOnSelect: false,
            infinite: false,
        });
    });
    
    // $(window).on('scroll', function() {
    //   var topmartin =  $(".header_area").outerHeight(); 
    //   $("#cartSuccessMessage").css({
    //         top: `${topmartin}px`
    //     });
    // });

});