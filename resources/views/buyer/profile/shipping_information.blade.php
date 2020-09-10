<?php use App\Enumeration\OrderStatus; ?>
@extends('layouts.my_account')

@section('content')

<!-- =========================
    START APPOINMENT SECTION
============================== -->
<section class="appoinment_area common_content_area">
    <div class="container">
        <div class="row">
            <div class="col-md-2 custom_padding_9 for_desktop d-none d-lg-block">
                <div class="common_left_menu">
                    @include('buyer.profile.menu')
                </div>
            </div>
            <div class="col-lg-10 col-md-12">
                <div class="my_account_content">
                    <div class="clearfix">&nbsp;</div>
                    <div class="myaccount_title">
                        <h2>My Information</h2>
                    </div>
                    <div class="my_info_area">
                        <form method="post" action="{{route('buyer_update_shipping_info')}}">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group{{ $errors->has('first_name') ? ' has-danger' : '' }}">
                                        <label for="small-rounded-input">First name <span class="required">*</span></label>
                                        <input class="form-control" type="text" id="first_name" name="first_name" value="{{ empty(old('first_name')) ? ($errors->has('first_name') ? '' : auth()->user()->first_name) : old('first_name') }}">

                                        @if ($errors->has('first_name'))
                                            <div class="has-error form-control-feedback">{{ $errors->first('first_name') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group{{ $errors->has('last_name') ? ' has-danger' : '' }}">
                                        <label for="small-rounded-input">Last name <span class="required">*</span></label>
                                        <input class="form-control" type="text" id="last_name"  name="last_name" value="{{ empty(old('first_name')) ? ($errors->has('	last_name') ? '' : auth()->user()->last_name) : old('last_name') }}">

                                        @if ($errors->has('last_name'))
                                            <div class="has-error form-control-feedback">{{ $errors->first('last_name') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group{{ $errors->has('	address') ? ' has-danger' : '' }}">
                                        <label for="small-rounded-input">Address <span class="required">*</span></label>
                                        <input type="text" name="address" rows="5" id="address" class="form-control" value="{{ empty(old('address')) ? ($errors->has('address') ? '' : $editShippingInfo->address) : old('address') }}">

                                        @if ($errors->has('address'))
                                            <div class="has-error form-control-feedback">{{ $errors->first('address') }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group{{ $errors->has('city') ? ' has-danger' : '' }}">
                                        <label for="small-rounded-input">City <span class="required">*</span></label>
                                        <input class="form-control" type="text" id="city" name="city" value="{{ empty(old('city')) ? ($errors->has('city') ? '' : $editShippingInfo->city) : old('city') }}">
                                        @if ($errors->has('city'))
                                            <div class="has-error form-control-feedback">{{ $errors->first('city') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group" id="form-group-state-select">
                                        <label for="small-rounded-input">State <span class="required">*</span></label>
                                        <select class="form-control form-control-rounded form-control-sm" id="stateSelect" name="state">
                                            <option value="">Select State</option>
                                            @foreach($states as $state)
                                                <option data-code="{{ $state->code }}" value="{{ $state->id }}" {{ empty(old('state')) ? ($errors->has('state') ? '' : ($editShippingInfo->state_id == $state->id ? 'selected' : '')) :
                                                    ($state->code  == 'US' ? 'selected' : '') }}>{{ $state->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="form-group{{ $errors->has('factoryCountry') ? ' has-danger' : '' }}">
                                        <label for="small-rounded-input">Country <span class="required">*</span></label>
                                        <select class="form-control" id="country" name="country">
                                            <option value="">Select Country</option>
                                            @foreach($countries as $country)
                                                <option data-code="{{ $country->code }}" value="{{ $country->id }}" {{ empty(old('country')) ?      ($errors->has('country') ? '' : ($editShippingInfo->country_id == $country->id ? 'selected' : '')) :
                                                            ($country->code  == 'US' ? 'selected' : '') }}>{{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('country'))
                                            <div class="form-control-feedback">{{ $errors->first('country') }}</div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-sm-4">

                                    <div class="form-group{{ $errors->has('zip') ? ' has-danger' : '' }}">
                                        <label for="small-rounded-input">Zip <span class="required">*</span></label>
                                        <input class="form-control" type="text" id="zip" name="zip" value="{{ empty(old('zip')) ? ($errors->has('zip') ? '' : $editShippingInfo->zip) : old('zip') }}">
                                        @if ($errors->has('zip'))
                                            <div class="has-error form-control-feedback">{{ $errors->first('zip') }}</div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="form-group{{ $errors->has('phone') ? ' has-danger' : '' }}">
                                        <label for="small-rounded-input">Phone <span class="required">*</span></label>
                                        <input class="form-control" type="text" id="phone" name="phone" value="{{ empty(old('phone')) ? ($errors->has('phone') ? '' : $editShippingInfo->phone) : old('phone') }}">
                                        @if ($errors->has('phone'))
                                            <div class="has-error form-control-feedback">{{ $errors->first('phone') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group my_account_field">
                                        <button type="submit" class="btn_common float-right">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- =========================
    END APPOINMENT SECTION
============================== -->
@stop

@section('additionalJS')
    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': '{!! csrf_token() !!}'
                }
            });

            $('#btnApprove').click(function (e) {
                e.preventDefault();
                var id = $(this).data('id');

                $.ajax({
                    method: "POST",
                    url: "{{ route('order_reject_status_change') }}",
                    data: { id: id, status: 2 },
                }).done(function( data ) {
                    window.location.reload(true);
                });
            });

            $('#btnDecline').click(function (e) {
                e.preventDefault();
                var id = $(this).data('id');

                $.ajax({
                    method: "POST",
                    url: "{{ route('order_reject_status_change') }}",
                    data: { id: id, status: 1 },
                }).done(function( data ) {
                    window.location.reload(true);
                });
            });
        });
    </script>
@stop
