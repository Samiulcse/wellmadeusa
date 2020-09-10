@extends('admin.layouts.main')

@section('additionalCSS')
    <style>
        .form-group label {
            padding-left: 0px !important;
        }
        .custom-radio [type="radio"]:checked, .custom-radio [type="radio"]:not(:checked) {

            position: absolute;
            left: -9999px;

        }
        .input[type="radio"], input[type="checkbox"] {

             box-sizing: border-box;
             padding: 0;

         }
        .custom-control-input {
            position: unset!important;

            z-index: -1;
        }
        .custom-radio {

            padding-left: 0;

        }
        .custom-control {

            position: relative;
            display: unset;
            min-height: 1.5rem;
            padding-left: 15px;

        }
        .custom-control-input {

            position: unset!important;

            z-index: -1;
            opacity: 1.5;

        }
    </style>
@stop

@section('content')
<section class="section main_content_sec">
    <div class="container">
        <div class="row">
            <div class="col-md-1 col-lg-1"></div>
            <div class="col-md-10 col-lg-10 col-sm-12">
                <div class="shipping_cart_area2 signup_form">
                    <form action="{{ route('admin_buyer_edit_post', ['buyer' => $buyer->id]) }}" method="POST">
                        @csrf
                        <div class="ly_card">
                            <div class="ly_card_heading">
                                <h5 class="mb-0"> Reward Points</h5>
                            </div>
                            <div class="ly_card_body">
                                <div class="ly-wrap-fluid">
                                    @if($buyer->points != 0)
                                        <div class="ly-row">
                                            <div class="ly-4">
                                                <div class="form_row">
                                                    <label for="small-rounded-input">Earned Points</label>
                                                    <input class="form-control{{ $errors->has('points') ? ' is-invalid' : '' }}"
                                                        type="number" id="points" name="points" min="0"
                                                        value="{{ empty(old('points')) ? ($errors->has('points') ? '' : $buyer->points) : old('points') }}" step="any" min="0">

                                                    @if ($errors->has('points'))
                                                        <div class="form-control-feedback">{{ $errors->first('points') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="ly-4">
                                                <div class="form_row">
                                                    <label for="small-rounded-input">Points Spent</label>
                                                    <input class="form-control{{ $errors->has('points_spent') ? ' is-invalid' : '' }}"
                                                        type="number" id="points_spent" name="points_spent" min="0"
                                                        value="{{ empty(old('points_spent')) ? ($errors->has('points_spent') ? '' : $buyer->points_spent) : old('points_spent') }}">

                                                    @if ($errors->has('points_spent'))
                                                        <div class="form-control-feedback">{{ $errors->first('points_spent') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="ly-4">
                                                <div class="form_row">
                                                    <label for="small-rounded-input">Remaining Points</label>
                                                    <input class="form-control" type="number" value="{{ $buyer->points - $buyer->points_spent }}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="ly-row">
                                            <div class="col-md-12">
                                                <h5>No reward points.</h5>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                            <div class="ly_card">
                                <div class="ly_card_heading">
                                    <h5 class="mb-0"> Customer Information</h5>
                                </div>
                                <div class="ly_card_body">
                                    <div class="ly-wrap-fluid">
                                        <div class="ly-row">
                                            <div class="ly-6 pl_0 pl_60">

                                                <div class="form_row {{ $errors->has('firstName') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        First Name
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="firstName" name="firstName" placeholder="First name"
                                                            value="{{ empty(old('firstName')) ? ($errors->has('firstName') ? '' : $buyer->user->first_name) : old('firstName') }}">
                                                        @if ($errors->has('firstName'))
                                                            <div class="form-control-feedback">{{ $errors->first('firstName') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form_row {{ $errors->has('email') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        Email
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="email" class="form_global" id="email" name="email" placeholder="email"
                                                            value="{{ empty(old('email')) ? ($errors->has('email') ? '' : $buyer->user->email) : old('email') }}">
                                                        @if ($errors->has('email'))
                                                            <div class="form-control-feedback">{{ $errors->first('email') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ly-6 pl_0 pr_60">
                                                <div class="form_row {{ $errors->has('lastName') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        Last Name
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="lastName" name="lastName" placeholder="Last name"
                                                            value="{{ empty(old('lastName')) ? ($errors->has('lastName') ? '' : $buyer->user->last_name) : old('lastName') }}">
                                                        @if ($errors->has('lastName'))
                                                            <div class="form-control-feedback">{{ $errors->first('lastName') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form_row {{ $errors->has('password') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        Password
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="password" name="password" placeholder="Password">
                                                        @if ($errors->has('password'))
                                                            <div class="form-control-feedback">{{ $errors->first('password') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ly_card">
                                <div class="ly_card_heading">
                                    <h5 class="mb-0"> My Company Information</h5>
                                </div>
                                <div class="ly_card_body">
                                    <div class="ly-wrap-fluid">
                                        <div class="ly-row">
                                            <div class="ly-6 pl_0 pl_60">
                                                <div class="form_row {{ $errors->has('companyName') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        Company Name
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="companyName" name="companyName" placeholder="Company Name"
                                                            value="{{ empty(old('companyName')) ? ($errors->has('companyName') ? '' : $buyer->company_name) : old('companyName') }}">
                                                        @if ($errors->has('companyName'))
                                                            <div class="form-control-feedback">{{ $errors->first('companyName') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ly-6 pl_0 pr_60">
                                                <div class="form_row {{ $errors->has('primaryCustomerMarket') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        Primary Customer Market
                                                    </div>
                                                    <div class="form_inline">
                                                        <div class="select">
                                                            <select class="form_global" id="primaryCustomerMarket" name="primaryCustomerMarket">
                                                                <option value="1"
                                                                        {{ empty(old('primaryCustomerMarket')) ? ($buyer->primary_customer_market == '1' ? 'selected' : '') :
                                                                            (old('primaryCustomerMarket') == '1' ? 'selected' : '') }}>All</option>
                                                                <option value="2"
                                                                        {{ empty(old('primaryCustomerMarket')) ? ($buyer->primary_customer_market == '2' ? 'selected' : '') :
                                                                            (old('primaryCustomerMarket') == '2' ? 'selected' : '') }}>African</option>
                                                                <option value="3"
                                                                        {{ empty(old('primaryCustomerMarket')) ? ($buyer->primary_customer_market == '3' ? 'selected' : '') :
                                                                            (old('primaryCustomerMarket') == '3' ? 'selected' : '') }}>Asian</option>
                                                                <option value="4"
                                                                        {{ empty(old('primaryCustomerMarket')) ? ($buyer->primary_customer_market == '4' ? 'selected' : '') :
                                                                            (old('primaryCustomerMarket') == '4' ? 'selected' : '') }}>Caucasian</option>
                                                                <option value="5"
                                                                        {{ empty(old('primaryCustomerMarket')) ? ($buyer->primary_customer_market == '5' ? 'selected' : '') :
                                                                            (old('primaryCustomerMarket') == '5' ? 'selected' : '') }}>Latino/Hispanic</option>
                                                                <option value="6"
                                                                        {{ empty(old('primaryCustomerMarket')) ? ($buyer->primary_customer_market == '6' ? 'selected' : '') :
                                                                            (old('primaryCustomerMarket') == '6' ? 'selected' : '') }}>Middle Eastern</option>
                                                                <option value="7"
                                                                        {{ empty(old('primaryCustomerMarket')) ? ($buyer->primary_customer_market == '7' ? 'selected' : '') :
                                                                            (old('primaryCustomerMarket') == '7' ? 'selected' : '') }}>Native American</option>
                                                                <option value="8"
                                                                        {{ empty(old('primaryCustomerMarket')) ? ($buyer->primary_customer_market == '8' ? 'selected' : '') :
                                                                            (old('primaryCustomerMarket') == '8' ? 'selected' : '') }}>Pacific Islander</option>
                                                                <option value="9"
                                                                        {{ empty(old('primaryCustomerMarket')) ? ($buyer->primary_customer_market == '9' ? 'selected' : '') :
                                                                            (old('primaryCustomerMarket') == '9' ? 'selected' : '') }}>Other</option>
                                                            </select>
                                                        </div>
                                                        @if ($errors->has('primaryCustomerMarket'))
                                                            <div class="form-control-feedback">{{ $errors->first('primaryCustomerMarket') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form_row">
                                                    <div class="label_inline required width_150p">
                                                        Do you sell online ?
                                                    </div>
                                                    <div class="form_inline">
                                                        <div class="custom_radio">
                                                            <input class="sellOnline" type="radio" id="sellOnlineYes" name="sellOnline" value="1"
                                                                {{ empty(old('sellOnline')) ? ($buyer->sell_online == "1" ? 'checked' : '') :
                                                                    (old('sellOnline') == '1' ? 'checked' : '') }}>
                                                            <label for="sellOnlineYes">Yes</label>
                                                        </div>
                                                        <div class="custom_radio">
                                                            <input class="sellOnline" type="radio" id="sellOnlineNo" name="sellOnline" value="0"
                                                                {{ empty(old('sellOnline')) ? ($buyer->sell_online == "0" ? 'checked' : '') :
                                                                    (old('sellOnline') == '0' ? 'checked' : '') }}>
                                                            <label for="sellOnlineNo">No</label>
                                                        </div>
                                                    </div>
                                                    <input class="form_global form-control-rounded form-control-sm" type="text" id="website"
                                                        name="website" placeholder="http://www.mywebsite.com"
                                                        value="{{ empty(old('website')) ? ($errors->has('website') ? '' : $buyer->website) : old('website') }}">
                                                    @if ($errors->has('website'))
                                                        <div class="form-control-feedback">{{ $errors->first('website') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ly_card">
                                <div class="ly_card_heading">
                                    <h5 class="mb-0"> Business Information</h5>
                                </div>
                                <div class="ly_card_body">
                                    <div class="ly-wrap-fluid">
                                        <div class="ly-row">
                                            <div class="ly-6 pl_0 pl_60">

                                                <div class="form_row {{ $errors->has('sellerPermitNumber') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        Seller Permit Number
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="sellerPermitNumber" name="sellerPermitNumber" placeholder="Seller Permit Number"
                                                            value="{{ empty(old('sellerPermitNumber')) ? ($errors->has('sellerPermitNumber') ? '' : $buyer->seller_permit_number) : old('sellerPermitNumber') }}">
                                                        @if ($errors->has('sellerPermitNumber'))
                                                            <div class="form-control-feedback">{{ $errors->first('sellerPermitNumber') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ly-6 pl_0 pr_60">
                                                <div class="form_row {{ $errors->has('ein') ? ' has-danger' : '' }}">
                                                    <div class="label_inline width_150p">
                                                        Business License, Federal Tax ID (EIN)
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="file" id="ein" name="ein">
                                                        @if ($errors->has('ein'))
                                                            <div class="form-control-feedback">{{ $errors->first('ein') }}</div>
                                                        @endif

                                                        <i>file must be less than 500kb</i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ly_card">
                                <div class="ly_card_heading">
                                    <h5 class="mb-0"> Shipping Address</h5>
                                </div>
                                <div class="ly_card_body">
                                    <div class="ly-wrap-fluid">
                                        <div class="ly-row">
                                            <div class="ly-12 pl_60 pr_60">
                                                <div class="form_row">
                                                    <div class="label_inline width_150p">
                                                        Location
                                                    </div>
                                                    <div class="form_inline">
                                                        <div class="custom_radio">
                                                            <input class="location" type="radio" id="locationUS" name="location"  value="US"
                                                                {{ empty(old('location')) ? ($buyerShippingAddress->location == "US" ? 'checked' : '') :
                                                                    (old('location') == 'US' ? 'checked' : '') }}>
                                                            <label for="locationUS">United States</label>
                                                        </div>
                                                        <div class="custom_radio">
                                                            <input class="location" type="radio" id="locationCA" name="location"  value="CA"
                                                            {{ empty(old('location')) ? ($buyerShippingAddress->location == "CA" ? 'checked' : '') :
                                                                    (old('location') == 'CA' ? 'checked' : '') }}>
                                                            <label for="locationCA">Canada</label>
                                                        </div>
                                                        <div class="custom_radio">
                                                            <input class="location" type="radio" id="locationInt" name="location"  value="INT"
                                                            {{ empty(old('location')) ? ($buyerShippingAddress->location == "INT" ? 'checked' : '') :
                                                                    (old('location') == 'INT' ? 'checked' : '') }}>
                                                            <label for="locationInt">International</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ly-row">
                                            <div class="ly-6 pl_0 pl_60">
                                                <div class="form_row {{ $errors->has('store_no') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        Store No.
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="store_no" name="store_no"  placeholder="Store No."
                                                            value="{{ empty(old('store_no')) ? ($errors->has('store_no') ? '' : $buyerShippingAddress->store_no) : old('store_no') }}">
                                                        @if ($errors->has('store_no'))
                                                            <div class="form-control-feedback">{{ $errors->first('store_no') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form_row">
                                                    <div class="label_inline required width_150p{{ $errors->has('address') ? ' has-danger' : '' }}">
                                                        Address
                                                    </div>
                                                    <div class="form_inline">
                                                        <div class="input_inline">
                                                            <div class="display_inline">
                                                                <input type="text" class="form_global width_350p" id="address" name="address" placeholder="Address"
                                                                    value="{{ empty(old('address')) ? ($errors->has('address') ? '' : $buyerShippingAddress->address) : old('address') }}">
                                                                @if ($errors->has('address'))
                                                                    <div class="form-control-feedback">{{ $errors->first('address') }}</div>
                                                                @endif
                                                            </div>
                                                            <div class="display_inline float_right mr_0{{ $errors->has('unit') ? ' has-danger' : '' }}">
                                                                <span class="mr_8">Unit #</span>
                                                                <div class="width_50p">
                                                                    <input type="text" class="form_global" id="unit" name="unit"
                                                                        value="{{ empty(old('unit')) ? ($errors->has('unit') ? '' : $buyerShippingAddress->unit) : old('unit') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form_row {{ $errors->has('state') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        State
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="state" name="state" placeholder="State"
                                                            value="{{ empty(old('state')) ? ($errors->has('state') ? '' : $buyerShippingAddress->state_text) : old('state') }}">
                                                        @if ($errors->has('state'))
                                                            <div class="form-control-feedback">{{ $errors->first('state') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form_row d_none {{ $errors->has('stateSelect') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        Select State
                                                    </div>
                                                    <div class="form_inline">
                                                        <div class="select">
                                                            <select class="form_global form-control-rounded form-control-sm" id="stateSelect" name="stateSelect">
                                                                <option value="">Select State</option>
                                                            </select>
                                                        </div>
                                                        @if ($errors->has('stateSelect'))
                                                            <div class="form-control-feedback">{{ $errors->first('stateSelect') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ly-6 pl_0 pr_60">
                                                <div class="form_row {{ $errors->has('attention') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        Attention
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="attention" name="attention" placeholder="Attention"
                                                            value="{{ empty(old('attention')) ? ($errors->has('attention') ? '' : $buyer->attention) : old('attention') }}">
                                                        @if ($errors->has('attention'))
                                                            <div class="form-control-feedback">{{ $errors->first('attention') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form_row {{ $errors->has('city') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        City
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="city" name="city" placeholder="City"
                                                            value="{{ empty(old('city')) ? ($errors->has('city') ? '' : $buyerShippingAddress->city) : old('city') }}">
                                                        @if ($errors->has('city'))
                                                            <div class="form-control-feedback">{{ $errors->first('city') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form_row {{ $errors->has('zipCode') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        Zip Code
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="zipCode" name="zipCode" placeholder="Zip Code"
                                                            value="{{ empty(old('zipCode')) ? ($errors->has('zipCode') ? '' : $buyerShippingAddress->zip) : old('zipCode') }}">
                                                        @if ($errors->has('zipCode'))
                                                            <div class="form-control-feedback">{{ $errors->first('zipCode') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ly-row">
                                            <div class="ly-7 pl_60 pr_60">
                                                <div class="form_row {{ $errors->has('country') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        Country
                                                    </div>
                                                    <div class="form_inline">
                                                        <div class="select">
                                                            <select class="form_global{{ $errors->has('country') ? ' is-invalid' : '' }}"
                                                                id="country" name="country">
                                                            <option value="">Select Country</option>
                                                            @foreach($countries as $country)
                                                                <option data-code="{{ $country->code }}" value="{{ $country->id }}"
                                                                        {{ empty(old('country')) ? ($errors->has('country') ? '' : ($buyerShippingAddress->country_id == $country->id ? 'selected' : '')) :
                                                                        (old('country') == $country->id ? 'selected' : '') }}>{{ $country->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        </div>
                                                        @if ($errors->has('country'))
                                                            <div class="form-control-feedback">{{ $errors->first('country') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>




                                        <div class="ly-row">
                                            <div class="ly-6 pl_0 pl_60">
                                                <div class="form_row {{ $errors->has('phone') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        Phone
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="phone" name="phone" placeholder="Phone"
                                                            value="{{ empty(old('phone')) ? ($errors->has('phone') ? '' : $buyerShippingAddress->phone) : old('phone') }}">
                                                        @if ($errors->has('phone'))
                                                            <div class="form-control-feedback">{{ $errors->first('phone') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ly-6 pl_0 pr_60">
                                                <div class="form_row {{ $errors->has('fax') ? ' has-danger' : '' }}">
                                                    <div class="label_inline width_150p">
                                                        Fax
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="fax" name="fax" placeholder="Fax"
                                                            value="{{ empty(old('fax')) ? ($errors->has('fax') ? '' : $buyerShippingAddress->fax) : old('fax') }}">
                                                        @if ($errors->has('fax'))
                                                            <div class="form-control-feedback">{{ $errors->first('fax') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ly-row">
                                            <div class="ly-12 pl_60 pr_60">
                                                <div class="form_row">
                                                    <div class="label_inline width_150p">

                                                    </div>
                                                    <div class="form_inline">
                                                        <div class="custom_checkbox">
                                                            <input type="checkbox" id="showroomCommercial" name="showroomCommercial" value="1"
                                                                {{ empty(old('showroomCommercial')) ? ($buyerShippingAddress->commercial == 1 ? 'checked' : '') :
                                                                    (old('showroomCommercial') ? 'checked' : '') }}>
                                                            <label for="showroomCommercial">This address is commercial.</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ly_card">
                                <div class="ly_card_heading">
                                    <h5 class="mb-0"> Billing Address
                                        <div class="form_inline">
                                            <div class="custom_checkbox">
                                                <input type="checkbox" id="sameAsShowroomAddress" name="sameAsShowroomAddress" value="1" {{ old('sameAsShowroomAddress') ? 'checked' : '' }}>
                                                <label for="sameAsShowroomAddress">Check here if same as shipping address.</label>
                                            </div>
                                        </div>
                                    </h5>
                                </div>
                                <div class="ly_card_body">
                                    <div class="ly-wrap-fluid">
                                        <div class="ly-row">
                                            <div class="ly-12 pl_60 pr_60">
                                                <div class="form_row">
                                                    <div class="label_inline width_150p">
                                                        Location
                                                    </div>
                                                    <div class="form_inline">
                                                        <div class="custom_radio">
                                                            <input class="factoryLocation" type="radio" id="factoryLocationUS" name="factoryLocation" value="US"
                                                                {{ empty(old('factoryLocation')) ? ($buyer->billing_location == "US" ? 'checked' : '') :
                                                                    (old('factoryLocation') == 'US' ? 'checked' : '') }}>
                                                            <label for="factoryLocationUS">United States</label>
                                                        </div>
                                                        <div class="custom_radio">
                                                            <input class="factoryLocation" type="radio" id="factoryLocationCA" name="factoryLocation" value="CA"
                                                                {{ empty(old('factoryLocation')) ? ($buyer->billing_location == "CA" ? 'checked' : '') :
                                                                    (old('factoryLocation') == 'CA' ? 'checked' : '') }}>
                                                            <label for="factoryLocationCA">Canada</label>
                                                        </div>
                                                        <div class="custom_radio">
                                                            <input class="factoryLocation" type="radio" id="factoryLocationInt" name="factoryLocation" value="INT"
                                                                {{ empty(old('factoryLocation')) ? ($buyer->billing_location == "INT" ? 'checked' : '') :
                                                                    (old('factoryLocation') == 'INT' ? 'checked' : '') }}>
                                                            <label for="factoryLocationInt">International</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ly-row">
                                            <div class="ly-6 pl_0 pl_60">
                                                <div class="form_row">
                                                    <div class="label_inline required width_150p{{ $errors->has('factoryAddress') ? ' has-danger' : '' }}">
                                                        Address
                                                    </div>
                                                    <div class="form_inline">
                                                        <div class="input_inline">
                                                            <div class="display_inline">
                                                                <input type="text" class="form_global width_350p" id="factoryAddress" name="factoryAddress" placeholder="Address"
                                                                    value="{{ empty(old('factoryAddress')) ? ($errors->has('factoryAddress') ? '' : $buyer->billing_address) : old('factoryAddress') }}">
                                                                @if ($errors->has('factoryAddress'))
                                                                    <div class="form-control-feedback">{{ $errors->first('factoryAddress') }}</div>
                                                                @endif
                                                            </div>
                                                            <div class="display_inline float_right mr_0{{ $errors->has('factoryUnit') ? ' has-danger' : '' }}">
                                                                <span class="mr_8">Unit #</span>
                                                                <div class="width_50p">
                                                                    <input type="text" class="form_global" id="factoryUnit" name="factoryUnit" value="{{ empty(old('factoryUnit')) ? ($errors->has('factoryUnit') ? '' : $buyer->billing_unit) : old('factoryUnit') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form_row {{ $errors->has('factoryState') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        State
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="factoryState" name="factoryState" placeholder="State"
                                                            value="{{ empty(old('factoryState')) ? ($errors->has('factoryState') ? '' : $buyer->billing_state) : old('factoryState') }}">
                                                        @if ($errors->has('factoryState'))
                                                            <div class="form-control-feedback">{{ $errors->first('factoryState') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form_row d_none {{ $errors->has('factoryStateSelect') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        Select State
                                                    </div>
                                                    <div class="form_inline">
                                                        <div class="select">
                                                            <select class="form_global{{ $errors->has('factoryStateSelect') ? ' is-invalid' : '' }}"
                                                                    id="factoryStateSelect" name="factoryStateSelect">
                                                                <option value="">Select State</option>
                                                            </select>
                                                        </div>
                                                        @if ($errors->has('factoryStateSelect'))
                                                            <div class="form-control-feedback">{{ $errors->first('factoryStateSelect') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ly-6 pl_0 pr_60">
                                                <div class="form_row {{ $errors->has('factoryCity') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        City
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="factoryCity" name="factoryCity" placeholder="City"
                                                            value="{{ empty(old('factoryCity')) ? ($errors->has('factoryCity') ? '' : $buyer->billing_city) : old('factoryCity') }}">
                                                        @if ($errors->has('factoryCity'))
                                                            <div class="form-control-feedback">{{ $errors->first('factoryCity') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form_row {{ $errors->has('factoryZipCode') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        Zip Code
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="factoryZipCode" name="factoryZipCode" placeholder="Zip Code"
                                                            value="{{ empty(old('factoryZipCode')) ? ($errors->has('factoryZipCode') ? '' : $buyer->billing_zip) : old('factoryZipCode') }}">
                                                        @if ($errors->has('factoryZipCode'))
                                                            <div class="form-control-feedback">{{ $errors->first('factoryZipCode') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ly-row">
                                            <div class="ly-7 pl_60 pr_60">
                                                <div class="form_row {{ $errors->has('factoryCountry') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        Country
                                                    </div>
                                                    <div class="form_inline">
                                                        <div class="select">
                                                            <select class="form_global{{ $errors->has('factoryCountry') ? ' is-invalid' : '' }}"
                                                                id="factoryCountry" name="factoryCountry">
                                                                <option value="">Select Country</option>
                                                                @foreach($countries as $country)
                                                                    <option data-code="{{ $country->code }}" value="{{ $country->id }}"
                                                                            {{ empty(old('factoryCountry')) ? ($errors->has('factoryCountry') ? '' : ($buyer->billing_country_id == $country->id ? 'selected' : '')) :
                                                                            (old('factoryCountry') == $country->id ? 'selected' : '') }}>{{ $country->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        @if ($errors->has('factoryCountry'))
                                                            <div class="form-control-feedback">{{ $errors->first('factoryCountry') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>




                                        <div class="ly-row">
                                            <div class="ly-6 pl_0 pl_60">
                                                <div class="form_row {{ $errors->has('factoryPhone') ? ' has-danger' : '' }}">
                                                    <div class="label_inline required width_150p">
                                                        Phone
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="factoryPhone" name="factoryPhone" placeholder="Phone"
                                                            value="{{ empty(old('factoryPhone')) ? ($errors->has('factoryPhone') ? '' : $buyer->billing_phone) : old('factoryPhone') }}">
                                                        @if ($errors->has('factoryPhone'))
                                                            <div class="form-control-feedback">{{ $errors->first('factoryPhone') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ly-6 pl_0 pr_60">
                                                <div class="form_row {{ $errors->has('factoryFax') ? ' has-danger' : '' }}">
                                                    <div class="label_inline width_150p">
                                                        Fax
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="factoryFax" name="factoryFax" placeholder="Fax"
                                                            value="{{ empty(old('factoryFax')) ? ($errors->has('factoryFax') ? '' : $buyer->billing_fax) : old('factoryFax') }}">
                                                        @if ($errors->has('factoryFax'))
                                                            <div class="form-control-feedback">{{ $errors->first('factoryFax') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ly-row">
                                            <div class="ly-12 pl_60 pr_60">
                                                <div class="form_row">
                                                    <div class="label_inline width_150p">
                                                    </div>
                                                    <div class="form_inline">
                                                        <div class="custom_checkbox">
                                                            <input type="checkbox" id="factoryCommercial" name="factoryCommercial"  value="1"
                                                                {{ empty(old('factoryCommercial')) ? ($buyer->billing_commercial == 1 ? 'checked' : '') :
                                                                    (old('factoryCommercial') ? 'checked' : '') }}>
                                                            <label for="factoryCommercial">This address is commercial.</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ly-row">
                                            <div class="ly-12 pl_60 pr_60">
                                                <div class="form_row">
                                                    <div class="label_inline width_150p">
                                                    </div>
                                                    <div class="form_inline">
                                                        <div class="custom_checkbox">
                                                            <input type="checkbox" id="receiveSpecialOffers" name="receiveSpecialOffers" value="1"
                                                            {{ ($buyer->receive_offers == 1) ? 'checked' : '' }}>
                                                            <label for="receiveSpecialOffers">Sign up to receive special offers and information.</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text_right m15">
                                <div class="display_inline mr_0">
                                    <input class="ly_btn  btn_blue min_width_100p " type="submit" value="UPDATE">
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@stop

@section('additionalJS')
    <script>
        var usStates = <?php echo json_encode($usStates); ?>;
        var caStates = <?php echo json_encode($caStates); ?>;
        var oldState = '{{ empty(old('stateSelect')) ? ($errors->has('stateSelect') ? '' : $buyerShippingAddress->state_id) : old('stateSelect') }}';
        var oldFactoryState = '{{ empty(old('factoryStateSelect')) ? ($errors->has('factoryStateSelect') ? '' : $buyer->billing_state_id) : old('factoryStateSelect') }}';

        $(function () {
            $('form').bind('submit', function () {
                $(this).find(':input').prop('disabled', false);
            });

            $('#address').keyup(function () {
                var text = $(this).val();
                if ($("#sameAsShowroomAddress").is(':checked'))
                    $('#factoryAddress').val(text);
            });

            $('#unit').keyup(function () {
                var text = $(this).val();
                if ($("#sameAsShowroomAddress").is(':checked'))
                    $('#factoryUnit').val(text);
            });

            $('#city').keyup(function () {
                var text = $(this).val();
                if ($("#sameAsShowroomAddress").is(':checked'))
                    $('#factoryCity').val(text);
            });

            $('#state').keyup(function () {
                var text = $(this).val();
                if ($("#sameAsShowroomAddress").is(':checked'))
                    $('#factoryState').val(text);
            });

            $('#zipCode').keyup(function () {
                var text = $(this).val();
                if ($("#sameAsShowroomAddress").is(':checked'))
                    $('#factoryZipCode').val(text);
            });

            $('#phone').keyup(function () {
                var text = $(this).val();
                if ($("#sameAsShowroomAddress").is(':checked'))
                    $('#factoryPhone').val(text);
            });

            $('#fax').keyup(function () {
                var text = $(this).val();
                if ($("#sameAsShowroomAddress").is(':checked'))
                    $('#factoryFax').val(text);
            });

            $('#sameAsShowroomAddress').change(function () {
                $('#address').trigger('keyup');
                $('#unit').trigger('keyup');
                $('#city').trigger('keyup');
                $('#state').trigger('keyup');
                $('#zipCode').trigger('keyup');
                $('#phone').trigger('keyup');
                $('#fax').trigger('keyup');

                var location = $('.location:checked').val();
                $('.factoryLocation[value=' + location + ']').prop('checked', true);
                $('.factoryLocation').trigger('change');

                $('#factoryCountry').val($('#country').val());
                $('#factoryState').val($('#state').val());
                $('#factoryStateSelect').val($('#stateSelect').val());
            });

            $('.location').change(function () {
                var location = $('.location:checked').val();

                if ($("#sameAsShowroomAddress").is(':checked')) {
                    $('.factoryLocation[value=' + location + ']').prop('checked', true);
                    $('.factoryLocation').trigger('change');
                }

                if (location == 'CA' || location == 'US') {
                    if (location == 'US')
                        $('#country').val('1');
                    else
                        $('#country').val('2');


                    $('#country').prop('disabled', 'disabled');
                    $('#form-group-state-select').show();
                    $('#stateSelect').val('');
                    $('#form-group-state').hide();

                    $('#stateSelect').html('<option value="">Select State</option>');

                    if (location == 'US') {
                        $.each(usStates, function (index, value) {
                            if (value.id == oldState)
                                $('#stateSelect').append('<option value="'+value.id+'" selected>'+value.name+'</option>');
                            else
                                $('#stateSelect').append('<option value="'+value.id+'">'+value.name+'</option>');
                        });
                    }

                    if (location == 'CA') {
                        $.each(caStates, function (index, value) {
                            if (value.id == oldState)
                                $('#stateSelect').append('<option value="'+value.id+'" selected>'+value.name+'</option>');
                            else
                                $('#stateSelect').append('<option value="'+value.id+'">'+value.name+'</option>');
                        });
                    }
                } else {
                    $('#country').prop('disabled', false);
                    $('#form-group-state-select').hide();
                    $('#form-group-state').show();
                }
            });

            $('.factoryLocation').change(function () {
                var location = $('.factoryLocation:checked').val();

                if (location == 'CA' || location == 'US') {
                    if (location == 'US')
                        $('#factoryCountry').val('1');
                    else
                        $('#factoryCountry').val('2');

                    $('#factoryCountry').prop('disabled', 'disabled');
                    $('#form-group-factory-state-select').show();
                    $('#factoryStateSelect').val('');
                    $('#form-group-factory-state').hide();

                    $('#factoryStateSelect').html('<option value="">Select State</option>');

                    if (location == 'US') {
                        $.each(usStates, function (index, value) {
                            if (value.id == oldFactoryState)
                                $('#factoryStateSelect').append('<option value="'+value.id+'" selected>'+value.name+'</option>');
                            else
                                $('#factoryStateSelect').append('<option value="'+value.id+'">'+value.name+'</option>');
                        });
                    }

                    if (location == 'CA') {
                        $.each(caStates, function (index, value) {
                            if (value.id == oldFactoryState)
                                $('#factoryStateSelect').append('<option value="'+value.id+'" selected>'+value.name+'</option>');
                            else
                                $('#factoryStateSelect').append('<option value="'+value.id+'">'+value.name+'</option>');
                        });
                    }
                } else {
                    $('#factoryCountry').prop('disabled', false);
                    $('#form-group-factory-state-select').hide();
                    $('#form-group-factory-state').show();
                }
            });

            $('#country').change(function () {
                var countryId = $(this).val();

                if (countryId == 1) {
                    $("#locationUS").prop("checked", true);
                    $('.location').trigger('change');
                } else if (countryId == 2) {
                    $("#locationCA").prop("checked", true);
                    $('.location').trigger('change');
                }
            });

            $('.sellOnline').change(function () {
                if ($('#sellOnlineYes').is(':checked')) {
                    $('#website').show();
                } else {
                    $('#website').hide();
                }
            });

            $('.location').trigger('change');
            $('.factoryLocation').trigger('change');
            $('.sellOnline').trigger('change');
        })
    </script>
@stop
