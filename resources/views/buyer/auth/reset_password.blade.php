<?php use App\Enumeration\Role; ?>
@extends('layouts.home_layout')
@section('additionalCSS')

@stop
@section('content')

    <section class="register_area common_top_margin">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="register_wrapper clearfix">
                        <div class="register_inner">
                            <div class="register_head text-center">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="forgot_title">
                                            <h3>Forgot Password</h3>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="forgot_sub_title">
                                            <p>Enter your email address below and we'll send you a link to reset your password</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <form method="post" action="{{ route('password_reset__buyer_post') }}">
                                @csrf
                            <div class="common_form">
                                <div class="form-group ">
                                    <input type="email" name="email" id="resetEmail" value="{{ old('email') }}" class="form-control common_input  {{ $errors->has('email') ? ' has-danger' : '' }}" placeholder="EMAIL ADDRESS">
                                    @if ($errors->has('email'))
                                        <div class="has-error custom_form_feedback text-danger">{{ $errors->first('email') }}</div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <div class="form-control-feedback alert-info">{{ session('message') }}</div>
                                </div>
                                <button type="submit" class="btn btn-default common_btn">Reset Password</button>
                            </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>




@endsection

@section('additionalJS')
<script>
    $(document).ready(function(){
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
                   window.location.replace('{{route("buyer_show_profile")}}');    
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
