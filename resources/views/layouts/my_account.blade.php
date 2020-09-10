<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $meta_title }}</title>
    <meta name="description" content="{{ $meta_description }}" />
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    <!-- CSRF Token -->
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
      <link rel="apple-touch-icon" href="apple-touch-icon.png">
      <link rel="stylesheet" type="text/css" href="{{asset('themes/front/css/bootstrap.min.css')}}">
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
      <link rel="stylesheet" href="{{ asset('themes/front/fonts/stylesheet.css') }}">
      <link rel="stylesheet" href="{{ asset('themes/front/css/owl.theme.default.css') }}">
      <link rel="stylesheet" type="text/css" href="{{asset('themes/front/css/owl.carousel.css')}}">
       <link rel="stylesheet" type="text/css" href="{{asset('themes/front/css/magnific-popup.css')}}">
      <link rel="stylesheet" href="{{asset('themes/front/css/slick.css')}}">
      <link rel="stylesheet" href="{{asset('themes/front/css/main.css')}}">
    @yield('additionalCSS')
    <script src="{{ asset('themes/front/js/jquery-3.4.1.js') }}"></script>

</head>

<body class="product_page my_account_page">
  @include('layouts.shared.header')

  <div class="my_account_area">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-3 col-lg-2">
          @include('buyer.profile.menu')
        </div>
        <div class="col-md-9 col-lg-8">
            @yield('content')
        </div>

      </div>
    </div>
  </div>



  @include('layouts.shared.footer')
    <!-- Footer -->
    <a href="javascript:void(0);" title="Go To Top" id="scroll" style="display: none;"><span></span></a>
        <form id="logoutForm" class="" action="{{ route('logout_buyer') }}" method="post">
          {{ csrf_field() }}
      </form>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script  src="{{ asset('themes/front/js/bootstrap-select.js') }}"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.1.0/js/all.js"></script>
        <script src="{{ asset('themes/front/js/owl.carousel.js') }}"></script>
{{--        <script src="{{ asset('themes/front/js/instafeed.min.js') }}"></script>--}}
        <script src="{{ asset('themes/front/js/jquery.magnific-popup.js') }}"></script>
        <script  src="{{ asset('themes/front/js/slick.js') }}"></script>
        <script  src="{{ asset('themes/front/js/lazysizes.min.js') }}"></script>
        <script src="{{ asset('themes/front/js/main.js') }}"></script> 
        <script src="{{ asset('themes/front/js/custom.js') }}"></script> 
        
        <script>
          $(window).scroll(function(){
          if($(this).scrollTop() > 100){
            $('#scroll').show();
          } else{
            $('#scroll').hide();
          }
         });

         $('#scroll').click(function(){
             $("html, body").animate(
                {scrollTop: 0},600);
             return false;
         });
        </script>
        <script>
        $(function () {
            $('.btnLogOut').click(function () {
                $('#logoutForm').submit();
            });
        });
    </script>
        @yield('additionalJS')
        <script>
          $('#mail_chimp_message').hide();
          $('#mailchimp_add').click(function(e){
              e.preventDefault();
              $('#mail_chimp_message').hide();
              if(validateEmail(document.getElementById('mail_to_add').value)){
                  $.ajax({
                      method: "POST",
                      url:"{{ route('add_email_to_mailchimp') }}",
                      data: {
                          email: document.getElementById('mail_to_add').value,
                      },
                      headers: {
                          'X-CSRF-Token': '{!! csrf_token() !!}'
                      }
                  }).done(function( data ) {
                      $('#mail_chimp_message').show();
                      $('#mail_chimp_message').html('Successfully added to mail list.Please check your email inbox.');
                      $('#mail_chimp_message').removeClass("subs_faild");
                      $('#mail_chimp_message').addClass('subs_success');
                  })
              } else {
                  $('#mail_chimp_message').show();
                  $('#mail_chimp_message').html('Invalid Email or Your are allready subscribed. ');
                  $('#mail_chimp_message').removeClass("subs_success");
                  $('#mail_chimp_message').addClass('subs_faild');
              }
          });

          function validateEmail(email) {
              var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
              return re.test(String(email).toLowerCase());
          }
        </script>
{{--        <script type="text/javascript">--}}
{{--            var feed = new Instafeed({--}}
{{--                get: 'user',--}}
{{--                userId: '8649697948',--}}
{{--                accessToken: '8649697948.1677ed0.1190576726914b7f868c5f37ac1847a2',--}}
{{--                clientId: ' fcbf8d1c48b54d8f870743a17131a894',--}}
{{--                limit: '9',--}}
{{--                resolution: 'standard_resolution',--}}
{{--                template: '<div class="instagram_inner"><a style="cursor: pointer;" data-toggle="modal" data-target="#instaModal-@{{id}}"><img src="http:@{{image}}" class="img-fluid"></a></div><div class="modal" id="instaModal-@{{id}}"><div class="modal-dialog mw-100 w-75"> <div class="modal-content"> <div class="modal-header"> <h4 class="modal-title"></h4> <button type="button" class="close" data-dismiss="modal">X</button> </div><div class="modal-body"><div class="container-fluid"> <div class="row"> <div class="col-sm-6"> <div class="insta_pop_up_left"> <img src="https:@{{image}}" alt="" class="img-fluid"> </div></div><div class="col-sm-6"><div class="insta_pop_up_right" style="margin-top: 0px !important; text-align:left;">@{{caption}}<hr> <p style="text-align: right"> <a target="_blank" href="@{{link}}">Details</a> </p></div></div></div></div></div><div class="modal-footer"></div></div></div></div>'--}}


{{--        });--}}
{{--        feed.run();--}}
{{--        </script>--}}
    @yield('additionalJS')
    <div id="scrollToTop" onclick="goTop()" > <i class="fas fa-arrow-up" ></i> </div>
<script>
    function goTop(){
        $('html,body').animate({ scrollTop: 0 }, 500);
    }
    setTimeout(function(){
        $(function(){
            $(window).scroll(function(e){
                if(window.scrollY > 100){
                    $('#scrollToTop').fadeIn();
                } else {
                    $('#scrollToTop').fadeOut();
                }
            });
        })
    }, 1000);
</script>
</body>
</html>