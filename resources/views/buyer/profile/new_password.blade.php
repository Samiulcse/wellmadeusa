@extends('layouts.home_layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="my_acc_page">
                <div class="row">
                    @include('buyer.profile.buyer_sidebar')
                    <div class="col-lg-10 col-md-8 col-sm-12 main_page_column">
                        <div class="my_page_content" style="background-image: url('../themes/front/images/bg.jpeg');">
                            <div class="row">
                                <div class="col-md-12  col-lg-12  col-sm-12">
                                    <div class="my_page_user_panel">
                                        <div class="card card-default custom_panel">
                                            <div class="card-heading custom_heading">Update Password</div>
                                            <div class="card-body custom_content">
                                                <div class="check_out_form mt-5">
                                                    @if(Session::has('flash_message_success'))
                                                        <div class="alert alert-success background-success">
                                                            <button type="button" class="close" data-dismiss="alert">x</button>
                                                            <strong>{!! session('flash_message_success')!!}</strong>
                                                        </div>
                                                    @endif
                                                    <form class="account_form" method="post" action="{{route('new_password_post_buyer_panel')}}">
                                                        @csrf
                                                        <div class="form-group">
                                                            <label for="password">Password <span class="text-danger">*</span></label>
                                                            <input type="password" class="form-control {{ $errors->has('password') ? ' has-danger' : '' }}" name="password" id="password" placeholder="Password">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="password_confirmation">Confirmed Password <span class="text-danger">*</span></label>
                                                            <input type="password" class="form-control {{ $errors->has('password') ? ' has-danger' : '' }}" name="password_confirmation" id="password_confirmation" placeholder="Re-enter Password"> 
                                                            @if ($errors->has('password'))
                                                                <div class="has-error custom_form_feedback">{{ $errors->first('password') }}</div>
                                                            @endif
                                                        </div>
                                                        <div class="form-group">
                                                            <button type="submit" class="update_profile_btn" data-wow-delay="0.7s">Update</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection