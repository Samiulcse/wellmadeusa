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
                                            <h3>Reset Password</h3>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="forgot_sub_title">
                                            <p>Enter your New Password</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(Session::has('flash_message_success'))
                                <div class="alert alert-success background-success">
                                    <button type="button" class="close" data-dismiss="alert">x</button>
                                    <strong>{!! session('flash_message_success')!!}</strong>
                                </div>
                            @endif
                            <form method="post" action="{{ route('new_password_post_buyer') }}">
                                @csrf 
                                <div class="form-row"> 
                                    <div class="form-group col-md-12">
                                        <label for="password">Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control {{ $errors->has('password') ? ' has-danger' : '' }}" name="password" id="password" placeholder="Password">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="password_confirmation">Confirmed Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control {{ $errors->has('password') ? ' has-danger' : '' }}" name="password_confirmation" id="password_confirmation" placeholder="Re-enter Password"> 
                                        @if ($errors->has('password'))
                                            <div class="has-error custom_form_feedback">{{ $errors->first('password') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <input type="hidden" name="token" value="{{ request()->get('token') }}">
                                <dov class="form-row">
                                    <div class="form-group col-md-12"> 
                                        <div class="reset_button">
                                            <button type="submit" class="btn sign_in_btn btn-default common_btn">New Password</button>
                                        </div> 
                                    </div>
                                </dov>
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
       
        });
</script>
@stop


 