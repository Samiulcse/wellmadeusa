<?php use App\Enumeration\Role; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ $meta_title }}</title>
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta name="description" content="{{ $meta_description }}">
        <link rel="canonical" href="{{ url()->current() }}" />
        <!-- Favicon -->
        <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    <!-- Bootstrap CSS -->
        <link rel="stylesheet" type="text/css" href="{{asset('themes/front/css/bootstrap.css')}}">
        <link rel="stylesheet" href="{{ asset('themes/front/fonts/stylesheet.css') }}">
        <link rel="stylesheet" href="{{ asset('themes/front/css/jquery-ui.css') }}">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css">
        <link rel="stylesheet" href="{{asset('themes/front/css/slick.css')}}">
        <link rel="stylesheet" href="{{asset('themes/front/css/slick-theme.css')}}">
        <link rel="stylesheet" href="{{asset('themes/front/css/owl.carousel.css')}}">
        <link rel="stylesheet" href="{{asset('themes/front/css/swiper.css')}}">
        <link rel="stylesheet" href="{{asset('themes/front/css/main.css')}}">

        @yield('additionalCSS')
    <script src="{{ asset('themes/front/js/jquery-3.4.1.js') }}"></script>

</head>
    <body>

        @include('layouts.shared.header')
        <div id="cartSuccessMessage">
            <div class="success_message custom_message_row">
                <div id="message"></div>
            </div>
        </div>
        <!-- Content -->
        @yield('content')

        @include('layouts.shared.footer')


        <form id="logoutForm" class="" action="{{ route('logout_buyer') }}" method="post">
          {{ csrf_field() }}
        </form>


        <!-- JavaScript (jQuery) libraries, plugins and custom scripts-->

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="{{ asset('themes/front/js/bootstrap.js') }}"></script>
        <script src="{{ asset('themes/front/js/jquery.ui.touch-punch.js') }}"></script>
        <script  src="{{ asset('themes/front/js/bootstrap-select.js') }}"></script>
        <!-- <script defer src="https://use.fontawesome.com/releases/v5.1.0/js/all.js"></script> -->
        <script src="{{ asset('themes/front/js/instafeed.min.js') }}"></script>
        <script  src="{{ asset('themes/front/js/slick.js') }}"></script>
        <script  src="{{ asset('themes/front/js/owl.carousel.js') }}"></script>
        <script  src="{{ asset('themes/front/js/swiper.js') }}"></script>
        <script src="{{asset('themes/front/js/jquery.zoom.js')}}"></script>
        <script  src="{{ asset('themes/front/js/jquery-ui.js') }}"></script>
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-144349011-1"></script>
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-144349011-1"></script>
        <script src="{{ asset('themes/front/js/main.js') }}"></script>
        <script src="//code.tidio.co/5frysztat5qeur6flnaygtaazllcevht.js" async></script>
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
                      $('#mail_chimp_message').addClass('text-success');
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

    </body>

</html>

