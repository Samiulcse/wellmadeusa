@extends('layouts.home_layout')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
@stop

@section('content')


    <section class="my_account_area common_top_margin">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-lg-2">
                    @include('buyer.profile.menu')
                </div>
                <div class="col-md-9 col-lg-8">
                    <div class="account_wrapper account_wrapper_b_t clearfix">
                        <div class="account_inner account_inner_b_t">
                            <h2>Hello, {{$user->first_name }} {{$user->last_name}}</h2>
                            <hr>
                            <form action="{{ route('buyer_update_profile') }}" method="post">
                                @csrf
                            <div class="form-group {{ $errors->has('first_name') ? ' has-danger' : '' }}" >
                                <label>First Name </label>
                                <input class="form-control" type="text" name="first_name"
                                       value="{{ empty(old('first_name')) ? ($errors->has('first_name') ? '' : $user->first_name) : old('first_name') }}">
                                @if ($errors->has('first_name'))
                                    <div class="form-control-feedback">{{ $errors->first('first_name') }}</div>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('last_name') ? ' has-danger' : '' }}" >
                                <label>Last Name </label>
                                <input class="form-control" type="text" name="last_name"
                                       value="{{ empty(old('last_name')) ? ($errors->has('last_name') ? '' : $user->last_name) : old('last_name') }}">
                                @if ($errors->has('last_name'))
                                    <div class="form-control-feedback">{{ $errors->first('last_name') }}</div>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('email') ? ' has-danger' : '' }}" >
                                <label>Email</label>
                                <input class="form-control" type="email" value="{{ $user->email }}" disabled>
                            </div>
                            <div class="form-group {{ $errors->has('password') ? ' has-danger' : '' }}" >
                                <label>Password </label>
                                <input class="form-control" type="password" name="password">
                                @if ($errors->has('password'))
                                    <div class="form-control-feedback">{{ $errors->first('password') }}</div>
                                @endif
                            </div>
{{--                            <div class="form-row">--}}
{{--                                <div class="form-group  {{ $errors->has('password') ? ' has-danger' : '' }}" >--}}
{{--                                    <label>Password </label>--}}
{{--                                    <input class="form-control" type="password" name="password">--}}
{{--                                    @if ($errors->has('password'))--}}
{{--                                        <div class="form-control-feedback">{{ $errors->first('password') }}</div>--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                                <div class="form-group col-lg-6" >--}}
{{--                                    <label >Confirm Password </label>--}}
{{--                                    <input class="form-control" type="password" name="password_confirmation">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="form-group form_checkbox">--}}
{{--                                <div class="custom_checkbox hasvalue">--}}
{{--                                    <input type="checkbox"  id="receive_offer" required>--}}
{{--                                    <label for="receive_offer">Sign up to receive special offers and information</label>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <div class="form-group">
                                    <button class="btn common_btn" type="submit" >Update Profile</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
 


     
@stop

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script>
        $(function () {
            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);
        });
    </script>
@stop