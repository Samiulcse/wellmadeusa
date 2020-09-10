<?php use App\Enumeration\Role; ?>
@extends('layouts.home_layout')
@section('additionalCSS')

@stop
@section('content')


    <!-- =========================
        START Login SECTION
    ============================== -->
    <section class="register_area common_top_margin">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="register_wrapper clearfix">
                        <div class="register_inner">
                            <div class="register_head text-center">
                                <h2>Log in</h2>
                            </div>
                            <div class="common_form cart-login">
                                <form action="{{ route('buyer_login_post') }}" method="post" id="login">
                                    @csrf
                                    <input type="hidden" value="1" name="login_page">
                                    <input type="hidden" value="{{ URL::previous() }}" name="url_previous">
                                    <div class="form-row ">
                                        <div class="form-group col-md-12">
                                            <div id="errorMessage" class="text-danger"></div>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                    <input type="email" value="" name="email" class="form-control common_input" placeholder="EMAIL ADDRESS" id="cartEmail">
                                </div>
                                <div class="form-group ">
                                    <input type="password" name="password" value="" class="form-control common_input" placeholder="PASSWORD" id="cartPassword">
                                </div>
                                <div class="has-danger">
                                    <div class="form-control-feedback">{{ session('message') }}</div>
                                </div>
                                <button type="submit" class="btn btn-default common_btn" >Sign In</button>
                                <a href="{{ route('buyer_register') }}">Don't have an account create one?</a>
                                <a href="{{ route('password_reset_buyer') }}">FORGOT YOUR PASSWORD?</a>
                            </form>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =========================
        END Login SECTION
    ============================== -->

  
@endsection

@section('additionalJS')
<script>
    $(document).ready(function(){
        $('.header_area').addClass('login_page');
        $('#buyerPageLogin').click(function(event){ 
            event.preventDefault();
            var form = $('.cart-login form'),
                url  = form.attr('action'),
                method = $('input[name=_method]').val() == undefined ? 'POST' : 'PUT';

                form.find('.login_block').remove();
                form.find('.form-group').removeClass('login_error');

            $.ajax({
                url : url,
                method:method,
                data : form.serialize(),
                headers: {
                          'X-CSRF-Token': '{!! csrf_token() !!}'
                        },
                success: function(response){
                  if(response.custom_error){
                    $('#errorMessage').html('<p class="custom_mail_error">'+response.custom_error+'</p>');
                  }
                  if(response.login_success){
                   window.location.replace('{{route("home")}}');
                  }
                },
                error: function(xhr){
                  var res = xhr.responseJSON;
                  if($.isEmptyObject(res) == false){
                    $.each(res.errors, function (key, value){
                      $('.' + key)
                            .closest('.form-group')
                            .addClass('login_error')
                            .append('<span class="login_block"><strong>'+ value +'</strong></span>') 
                    });
                  }
                }
              });    
            });
        });
</script>
@stop
