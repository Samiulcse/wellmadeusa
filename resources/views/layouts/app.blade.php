<?php use App\Enumeration\Role; ?>
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">
    <script src="{{asset('themes/front/js/lazysizes.min.js')}}"></script>
    @yield('additionalCSS')
    <style>
        .form-control-feedback{
            color: #d80808 !important;
        }
    </style>
</head>

<!-- Body-->
<body class="product_page">

    <!-- Header -->
    @include('layouts.shared.header')
    @include('layouts.shared.left_menu')
    <!-- Header -->

    @yield('filters')
    @yield('content')

    <!-- Footer -->
    @include('layouts.shared.footer')
    <!-- Footer -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="{{ asset('themes/cq/js/owl.carousel.js') }}"></script>
    <script src="{{ asset('themes/cq/js/jquery.magnific-popup.js') }}"></script>
    <script src="{{ asset('themes/cq/js/slick.js') }}"></script>
    <script src="{{ asset('themes/cq/js/main.js') }}?id={{ rand() }}"></script>
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
</body>
</html>