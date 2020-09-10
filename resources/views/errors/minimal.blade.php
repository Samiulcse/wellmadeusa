<?php use App\Enumeration\Role; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=windows-1252"> 
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no  user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title')</title>
        <!-- Favicon -->
        <link rel="icon" href="{{ asset('images/favicon.jpg') }}" type="image/x-icon">
        <link rel="stylesheet" href="{{asset('themes/front/css/bootstrap.min.css')}}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css">
        <link rel="stylesheet" href="{{asset('themes/front/css/style.css')}}">    
        <link rel="stylesheet" href="{{asset('themes/front/css/font-awesome5.8-all.css')}}">
        @yield('additionalCSS')
    </head>
    <body>
        @include('layouts.shared.header')

        <!-- Content -->
        @yield('content')

        @include('layouts.shared.footer')

        <!-- JavaScript (jQuery) libraries, plugins and custom scripts-->
        <a href="javascript:void(0);" title="Go To Top" id="scroll" style="display: none;"><span></span></a>
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="crossorigin="anonymous"></script>
        <script src="{{asset('themes/front/js/bootstrap.min.js')}}" type="text/javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
        <script src="{{asset('themes/front/js/custom.js')}}"></script> 
        <script src="{{asset('themes/front/js/screen-scroll.js')}}"></script> 
        <script>
            function openNav() {
            document.getElementById("mySidepanel").style.display = "block";
            } 
            function closeNav() {
            document.getElementById("mySidepanel").style.display = "none";
            }
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

          document.addEventListener('touchmove', function(event) {
                event = event.originalEvent || event;
                if(event.scale > 1) {
                event.preventDefault();
                }
            }, false);
        </script>
    </body>

</html>
