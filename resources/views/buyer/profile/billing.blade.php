<?php use App\Enumeration\OrderStatus; ?>
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
                                <div class="col-md-12  col-lg-12 col-sm-12">
                                    <div class="my_page_user_panel">
                                        <div class="card card-default custom_panel">
                                            <div class="card-heading custom_heading">Billing Information</div>
                                            <div class="card-body custom_content">
                                                <div class="check_out_form mt-5">
                                                    @if(Session::has('flash_message_success'))
                                                        <div class="alert alert-success background-success">
                                                            <button type="button" class="close" data-dismiss="alert">x</button>
                                                            <strong>{!! session('flash_message_success')!!}</strong>
                                                        </div>
                                                    @endif
                                                    <form class="account_form" method="post" action="{{route('buyer_update_billing_info')}}">
                                                        @csrf
                                                        <div class="form-group">
                                                            <label for="first_name">First name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control {{ $errors->has('first_name') ? ' has-danger' : '' }}" name="first_name" id="first_name" value="{{ empty(old('first_name')) ? ($errors->has('first_name') ? '' : auth()->user()->first_name) : old('first_name') }}"> 
                                                            @if ($errors->has('first_name'))
                                                                <div class="has-error custom_form_feedback">{{ $errors->first('first_name') }}</div>
                                                            @endif
                                                        </div>
                                                        <div class="form-group ">
                                                            <label for="last_name">Last name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control {{ $errors->has('last_name') ? ' has-danger' : '' }}" name="last_name" id="last_name" value="{{ empty(old('last_name')) ? ($errors->has('last_name') ? '' : auth()->user()->last_name) : old('last_name') }}"> 
                                                            @if ($errors->has('last_name'))
                                                                <div class="has-error custom_form_feedback">{{ $errors->first('last_name') }}</div>
                                                            @endif
                                                        </div>
                                                        <div class="form-group ">
                                                            <label for="address">Address <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control {{ $errors->has('address') ? ' has-danger' : '' }}" name="address" id="address" value="{{ empty(old('address')) ? ($errors->has('address') ? '' : $buyerBillingInfo->billing_address) : old('address') }}"> 
                                                            @if ($errors->has('address'))
                                                                <div class="has-error custom_form_feedback">{{ $errors->first('address') }}</div>
                                                            @endif
                                                        </div>
                                                        <div class="form-group ">
                                                            <label for="city">City <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control {{ $errors->has('city') ? ' has-danger' : '' }}" name="city" id="city" value="{{ empty(old('city')) ? ($errors->has('city') ? '' : $buyerBillingInfo->billing_city) : old('city') }}"> 
                                                            @if ($errors->has('city'))
                                                                <div class="has-error custom_form_feedback">{{ $errors->first('city') }}</div>
                                                            @endif
                                                        </div>
                                                        <div class="form-group" id="form-group-state-select">
                                                            <label for="stateSelect">State <span class="text-danger">*</span></label>
                                                            <select class="form-control {{ $errors->has('state') ? ' has-danger' : '' }}" id="stateSelect" name="state">
                                                                <option value="">Select State</option>
                                                                @foreach($states as $state)
                                                                    <option data-code="{{ $state->code }}" value="{{ $state->id }}" {{ empty(old('state')) ? ($errors->has('state') ? '' : ($buyerBillingInfo->shipping_state_id == $state->id ? 'selected' : '')) :
                                                                        ($state->code  == 'US' ? 'selected' : '') }}>{{ $state->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @if ($errors->has('state'))
                                                                <div class="custom_form_feedback">{{ $errors->first('state') }}</div>
                                                            @endif
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="country">Country <span class="text-danger">*</span></label>
                                                            <select class="form-control {{ $errors->has('factoryCountry') ? ' has-danger' : '' }}" id="country" name="country">
                                                                <option value="">Select Country</option>
                                                                @foreach($countries as $country)
                                                                    <option data-code="{{ $country->code }}"
                                                                            value="{{ $country->id }}"
                                                                            {{ empty(old('country')) ? ($errors->has('country') ? '' : ($buyerBillingInfo->billing_country_id == $country->id ? 'selected' : '')) :
                                                                                ($country->code  == 'US' ? 'selected' : '') }}>{{ $country->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @if ($errors->has('country'))
                                                                <div class="custom_form_feedback">{{ $errors->first('country') }}</div>
                                                            @endif
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="zip">Zip <span class="text-danger">*</span></label>
                                                            <input class="form-control {{ $errors->has('zip') ? ' has-danger' : '' }}" type="text" id="zip" name="zip" value="{{ empty(old('zip')) ? ($errors->has('zip') ? '' : $buyerBillingInfo->billing_zip) : old('zip') }}">
                                                            @if ($errors->has('zip'))
                                                                <div class="has-error custom_form_feedback">{{ $errors->first('zip') }}</div>
                                                            @endif
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="phone">Phone <span class="required">*</span></label>
                                                            <input class="form-control {{ $errors->has('phone') ? ' has-danger' : '' }}" type="text" id="phone" name="phone" value="{{ empty(old('phone')) ? ($errors->has('phone') ? '' : $buyerBillingInfo->billing_phone) : old('phone') }}">
                                                            @if ($errors->has('phone'))
                                                                <div class="has-error custom_form_feedback">{{ $errors->first('phone') }}</div>
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