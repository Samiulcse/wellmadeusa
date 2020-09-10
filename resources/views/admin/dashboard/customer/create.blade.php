@extends('admin.layouts.main')

@section('additionalCSS')
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/themify/css/themify-icons.css') }}" />
    <style>
        .form-control-feedback{
            color: #ff0000;
        }

        .custom-control-input{
            display: none;
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
                    <form class="" action="{{ route('customer_register_post') }}" method="POST" enctype="multipart/form-data">
                        @csrf        
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
                                                        <input type="text" class="form_global" id="firstName" name="firstName" value="{{ old('firstName') }}" placeholder="First name">
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
                                                        <input type="email" class="form_global" id="email" name="email" value="{{ old('email') }}" placeholder="email">
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
                                                        <input type="text" class="form_global" id="lastName" name="lastName" value="{{ old('lastName') }}" placeholder="Last name">
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
                                                        <input type="text" class="form_global" id="password" name="password" value="{{ old('password') }}" placeholder="Password">
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
                                                        <input type="text" class="form_global" id="companyName" name="companyName" value="{{ old('companyName') }}" placeholder="Company Name">
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
                                                            <select class="form_global form-control-rounded form-control-sm" id="primaryCustomerMarket" name="primaryCustomerMarket">
                                                                <option value="1" {{ old('primaryCustomerMarket') == '1' ? 'selected' : '' }}>All</option>
                                                                <option value="2" {{ old('primaryCustomerMarket') == '2' ? 'selected' : '' }}>African</option>
                                                                <option value="3" {{ old('primaryCustomerMarket') == '3' ? 'selected' : '' }}>Asian</option>
                                                                <option value="4" {{ old('primaryCustomerMarket') == '4' ? 'selected' : '' }}>Caucasian</option>
                                                                <option value="5" {{ old('primaryCustomerMarket') == '5' ? 'selected' : '' }}>Latino/Hispanic</option>
                                                                <option value="6" {{ old('primaryCustomerMarket') == '6' ? 'selected' : '' }}>Middle Eastern</option>
                                                                <option value="7" {{ old('primaryCustomerMarket') == '7' ? 'selected' : '' }}>Native American</option>
                                                                <option value="8" {{ old('primaryCustomerMarket') == '8' ? 'selected' : '' }}>Pacific Islander</option>
                                                                <option value="9" {{ old('primaryCustomerMarket') == '9' ? 'selected' : '' }}>Other</option>
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
                                                            <input class="sellOnline" type="radio" id="sellOnlineYes" name="sellOnline" value="1" {{ old('sellOnline') == '1'  ? 'checked' : '' }}>
                                                            <label for="sellOnlineYes">Yes</label>
                                                        </div>
                                                        <div class="custom_radio">
                                                            <input class="sellOnline" type="radio" id="sellOnlineNo" name="sellOnline" value="0" {{ !old('sellOnline') || old('sellOnline') == '0'  ? 'checked' : '' }}>
                                                            <label for="sellOnlineNo">No</label>
                                                        </div>
                                                    </div>                                    
                                                    <input class="form_global form-control-rounded form-control-sm" type="text" id="website"
                                                        name="website" placeholder="http://www.mywebsite.com" value="{{ old('website') }}">
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
                                                        <input type="text" class="form_global" id="sellerPermitNumber" name="sellerPermitNumber" value="{{ old('sellerPermitNumber') }}" placeholder="Seller Permit Number">
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
                                                            <input class="location" type="radio" id="locationUS" name="location" value="US" {{ (old('location') == 'US' || empty(old('location'))) ? 'checked' : '' }}>
                                                            <label for="locationUS">United States</label>
                                                        </div>
                                                        <div class="custom_radio">
                                                            <input class="location" type="radio" id="locationCA" name="location" value="CA" {{ old('location') == 'CA'  ? 'checked' : '' }}>
                                                            <label for="locationCA">Canada</label>
                                                        </div>
                                                        <div class="custom_radio">
                                                            <input class="location" type="radio" id="locationInt" name="location" value="INT" {{ old('location') == 'INT'  ? 'checked' : '' }}>
                                                            <label for="locationInt">International</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ly-row">
                                            <div class="ly-6 pl_0 pl_60">   
                                                <div class="form_row {{ $errors->has('store_no') ? ' has-danger' : '' }}">
                                                    <div class="label_inline width_150p">
                                                        Store No.
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="store_no" name="store_no" value="{{ old('store_no') }}" placeholder="Store No.">
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
                                                                <input type="text" class="form_global width_350p" id="address" name="address" value="{{ old('address') }}" placeholder="Address">
                                                                @if ($errors->has('address'))
                                                                    <div class="form-control-feedback">{{ $errors->first('address') }}</div>
                                                                @endif
                                                            </div>
                                                            <div class="display_inline float_right mr_0{{ $errors->has('unit') ? ' has-danger' : '' }}">
                                                                <span class="mr_8">Unit #</span>
                                                                <div class="width_50p">
                                                                    <input type="text" class="form_global" id="unit" name="unit" value="{{ old('unit') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form_row d_none {{ $errors->has('state') ? ' has-danger' : '' }}" id="form-group-state">
                                                    <div class="label_inline required width_150p">
                                                        State
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="state" name="state" value="{{ old('state') }}" placeholder="State">
                                                        @if ($errors->has('state'))
                                                            <div class="form-control-feedback">{{ $errors->first('state') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form_row  {{ $errors->has('stateSelect') ? ' has-danger' : '' }}" id="form-group-state-select">
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
                                                    <div class="label_inline width_150p">
                                                        Attention
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="attention" name="attention" value="{{ old('attention') }}" placeholder="Attention">
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
                                                        <input type="text" class="form_global" id="city" name="city" value="{{ old('city') }}" placeholder="City">
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
                                                        <input type="text" class="form_global" id="zipCode" name="zipCode" value="{{ old('zipCode') }}" placeholder="Zip Code">
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
                                                            <select class="form_global form-control-rounded form-control-sm" id="country" name="country">
                                                                <option value="">Select Country</option>
                                                                @foreach($countries as $country)
                                                                    <option data-code="{{ $country->code }}" value="{{ $country->id }}" {{ old('country') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
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
                                                        <input type="text" class="form_global" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Phone">
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
                                                        <input type="text" class="form_global" id="fax" name="fax" value="{{ old('fax') }}" placeholder="Fax">
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
                                                            <input type="checkbox" id="showroomCommercial" name="showroomCommercial" value="1">
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
                                        </div></h5>
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
                                                            <input class="factoryLocation" type="radio" id="factoryLocationUS" name="factoryLocation" value="US" {{ (old('factoryLocation') == 'US' || empty(old('factoryLocation'))) ? 'checked' : '' }}>
                                                            <label for="factoryLocationUS">United States</label>
                                                        </div>
                                                        <div class="custom_radio">
                                                            <input class="factoryLocation" type="radio" id="factoryLocationCA" name="factoryLocation" value="CA" {{ old('factoryLocation') == 'CA'  ? 'checked' : '' }}>
                                                            <label for="factoryLocationCA">Canada</label>
                                                        </div>
                                                        <div class="custom_radio">
                                                            <input class="factoryLocation" type="radio" id="factoryLocationInt" name="factoryLocation" value="INT" {{ old('factoryLocation') == 'INT'  ? 'checked' : '' }}>
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
                                                                <input type="text" class="form_global width_350p" id="factoryAddress" name="factoryAddress" value="{{ old('factoryAddress') }}" placeholder="Address">
                                                                @if ($errors->has('factoryAddress'))
                                                                    <div class="form-control-feedback">{{ $errors->first('factoryAddress') }}</div>
                                                                @endif
                                                            </div>
                                                            <div class="display_inline float_right mr_0{{ $errors->has('factoryUnit') ? ' has-danger' : '' }}">
                                                                <span class="mr_8">Unit #</span>
                                                                <div class="width_50p">
                                                                    <input type="text" class="form_global" id="factoryUnit" name="factoryUnit" value="{{ old('factoryUnit') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form_row d_none {{ $errors->has('factoryState') ? ' has-danger' : '' }}" id="form-group-factory-state">
                                                    <div class="label_inline required width_150p">
                                                        State
                                                    </div>
                                                    <div class="form_inline">
                                                        <input type="text" class="form_global" id="factoryState" name="factoryState" value="{{ old('factoryState') }}" placeholder="State">
                                                        @if ($errors->has('factoryState'))
                                                            <div class="form-control-feedback">{{ $errors->first('factoryState') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form_row {{ $errors->has('factoryStateSelect') ? ' has-danger' : '' }}" id="form-group-factory-state-select">
                                                    <div class="label_inline required width_150p">
                                                        Select State
                                                    </div>
                                                    <div class="form_inline">
                                                        <div class="select">
                                                            <select class="form_global form-control-rounded form-control-sm" id="factoryStateSelect" name="factoryStateSelect">
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
                                                        <input type="text" class="form_global" id="factoryCity" name="factoryCity" value="{{ old('factoryCity') }}" placeholder="City">
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
                                                        <input type="text" class="form_global" id="factoryZipCode" name="factoryZipCode" value="{{ old('factoryZipCode') }}" placeholder="Zip Code">
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
                                                            <select class="form_global form-control-rounded form-control-sm" id="factoryCountry" name="factoryCountry">
                                                                <option value="">Select Country</option>
                                                                @foreach($countries as $country)
                                                                    <option data-code="{{ $country->code }}" value="{{ $country->id }}" {{ old('factoryCountry') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
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
                                                        <input type="text" class="form_global" id="factoryPhone" name="factoryPhone" value="{{ old('factoryPhone') }}" placeholder="Phone">
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
                                                        <input type="text" class="form_global" id="factoryFax" name="factoryFax" value="{{ old('factoryFax') }}" placeholder="Fax">
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
                                                            <input type="checkbox" id="factoryCommercial" name="factoryCommercial" value="1" {{ old('factoryCommercial') ? 'checked' : '' }}>
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
                                                            <input type="checkbox" id="receiveSpecialOffers" name="receiveSpecialOffers" value="1" checked>
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
                                    <input class="ly_btn  btn_blue min_width_100p " type="submit" value="REGISTER">
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
        $(function () {
            $('.btnShowHide').click(function () {
                if ($(this).hasClass('collapsed')) {
                    $(this).closest('.col-md-3').find('.span_icon').html('<i class="ti-arrow-down"></i>');
                } else {
                    $(this).closest('.col-md-3').find('.span_icon').html('<i class="ti-arrow-right"></i>');
                }
            });

            $('.span_icon').click(function () {
                $(this).siblings('.btnShowHide').trigger('click');
            });
        });
    </script>

<script>
    var usStates = <?php echo json_encode($usStates); ?>;
    var caStates = <?php echo json_encode($caStates); ?>;
    var oldState = '{{ old('stateSelect') }}';
    var oldFactoryState = '{{ old('factoryStateSelect') }}';

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

        $('#stateSelect').change(function () {
            var stateId = $(this).val();            
            if ($("#sameAsShowroomAddress").is(':checked'))
                $("#factoryStateSelect").val(stateId).trigger('change');
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
                $('#form-group-state-select').removeClass('d_none');
                $('#stateSelect').val('');
                $('#form-group-state').addClass('d_none');

                $('#stateSelect').html('<option value="">@lang('Select State')</option>');

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
                $('#country').val('');
                $('#country').prop('disabled', false);
                $('#form-group-state-select').addClass('d_none');
                $('#form-group-state').removeClass('d_none');
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
                $('#form-group-factory-state-select').removeClass('d_none');
                $('#factoryStateSelect').val('');
                $('#form-group-factory-state').addClass('d_none');

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
                 $('#factoryCountry').val('');
                $('#factoryCountry').prop('disabled', false);
                $('#form-group-factory-state-select').addClass('d_none');
                $('#form-group-factory-state').removeClass('d_none');
            }
        });

        $('#country').change(function () {
            var countryId = $(this).val();
            $("#factoryCountry").val(countryId).trigger('change');

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