@extends('layouts.home_layout')
@section('additionalCSS')
    <style>
        .footer{
            margin-top: 0 !important;
        }
    </style>
@stop
@section('content')
    <!-- =========================
        START REGISTER SECTION
    ============================== -->
    <section class="register_area common_top_margin">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="signup_content">
                        <h2>CREATE AN ACCOUNT</h2>
                        <div class="form_global">
                            <form action="{{ route('buyer_register_post') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-row">
                                    <div class="form-group{{ $errors->has('firstName') ? ' has-danger' : '' }} col-md-6">
                                        <label>First Name<span class="required">*</span></label>
                                        <input type="text" class="form-control" id="firstName" name="firstName" value="{{ old('firstName') }}"  placeholder="">

                                        @if ($errors->has('firstName'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('firstName') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('lastName') ? ' has-danger' : '' }} col-md-6">
                                        <label>Last Name<span class="required">*</span></label>
                                        <input type="text" id="lastName" name="lastName" value="{{ old('lastName') }}" class="form-control" placeholder="">
                                        @if ($errors->has('lastName'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('lastName') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }} col-md-6">
                                        <label>Email Address<span class="required">*</span></label>
                                        <input type="text" id="email" name="email" value="{{ old('email') }}" class="form-control"  placeholder="">

                                        @if ($errors->has('email'))
                                            <div class="form-control-feedback form-error text-danger" >{{ $errors->first('email') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }} col-md-6">
                                        <label>Password<span class="required">*</span></label>
                                        <input type="password" id="password" name="password" class="form-control" placeholder="">
                                        @if ($errors->has('password'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('password') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group{{ $errors->has('companyName') ? ' has-danger' : '' }} col-md-6">
                                        <label>Company Name<span class="required">*</span></label>
                                        <input type="text" id="companyName" name="companyName" value="{{ old('companyName') }}" class="form-control"  placeholder="">
                                        @if ($errors->has('companyName'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('companyName') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('sellerPermitNumber') ? ' has-danger' : '' }} col-md-6">
                                        <label>Seller Permit Number<span class="required">*</span></label>
                                        <input type="text" id="sellerPermitNumber" name="sellerPermitNumber"
                                               value="{{ old('sellerPermitNumber') }}" class="form-control" placeholder="">
                                        @if ($errors->has('sellerPermitNumber'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('sellerPermitNumber') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('ein') ? ' has-danger' : '' }}">
                                    <label for="exampleFormControlFile1">Business License, Federal Tax ID (EIN) / Recent Sales Receipt<span class="required">*</span></label>
                                    <input type="file" id="ein" name="ein" class="form-control-file">

                                    @if ($errors->has('ein'))
                                        <div class="form-control-feedback form-error text-danger">{{ $errors->first('ein') }}</div>
                                    @endif
                                </div>
                                <p class="text-danger">
                                    For all International Customers, If you don't have a business License or a Federal Tax ID
                                    number, Please submit a recent sales receipt.
                                </p>
                                <h3>Shipping Address</h3>
                                <div class="form-group">
                                    <label >Location : </label>
                                    <div class="custom_radio">
                                        <input class="location" type="radio" id="locationUS"
                                               name="location"
                                               value="US" {{ (old('location') == 'US' || empty(old('location'))) ? 'checked' : '' }}  />
                                        <label for="locationUS">United States</label>
                                    </div>
                                    <div class="custom_radio">
                                        <input class="location" type="radio" id="locationCA"
                                               name="location"
                                               value="CA" {{ old('location') == 'CA' ? 'checked' : '' }}  />
                                        <label for="locationCA">Canada</label>
                                    </div>
                                    <div class="custom_radio">
                                        <input class="location" type="radio" id="locationInt"
                                               name="location"
                                               value="INT" {{ old('location') == 'INT' ? 'checked' : '' }} />
                                        <label for="locationInt">International</label>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group{{ $errors->has('store_no') ? ' has-danger' : '' }} col-md-6">
                                        <label>Store No.</label>
                                        <input type="text" class="form-control" id="store_no" name="store_no" value="{{ old('store_no') }}"  placeholder="">

                                        @if ($errors->has('store_no'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('store_no') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('attention') ? ' has-danger' : '' }} col-md-6">
                                        <label>Attention</label>
                                        <input type="text" class="form-control" id="attention" name="attention" value="{{ old('attention') }}" placeholder="">

                                        @if ($errors->has('attention'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('attention') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group{{ $errors->has('address') ? ' has-danger' : '' }} col-md-6">
                                        <label>Address <span class="required">*</span></label>
                                        <input type="text" id="address" name="address" value="{{ old('address') }}" class="form-control"  placeholder="">
                                        @if ($errors->has('address'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('address') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('unit') ? ' has-danger' : '' }} col-md-6">
                                        <label>Unit #</label>
                                        <input type="text" class="form-control" id="unit" name="unit" value="{{ old('unit') }}" placeholder="">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group{{ $errors->has('city') ? ' has-danger' : '' }} col-md-6">
                                        <label>City <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}" placeholder="">
                                        @if ($errors->has('city'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('city') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('state') ? ' has-danger' : '' }} col-md-6" id="form-group-state">
                                        <label>State <span class="required">*</span></label>
                                        <input type="text" class="form-control"  id="state" name="state" value="{{ old('state') }}" placeholder="">
                                        @if ($errors->has('state'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('state') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('stateSelect') ? ' has-danger' : '' }} col-md-6"  id="form-group-state-select">
                                        <label>State<span class="required">*</span></label>
                                        <select class="form-control" id="stateSelect" name="stateSelect">
                                            <option value="">Select State</option>
                                        </select>
                                        @if ($errors->has('stateSelect'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('stateSelect') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group{{ $errors->has('zipCode') ? ' has-danger' : '' }} col-md-6">
                                        <label>Zip Code <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="zipCode" name="zipCode" value="{{ old('zipCode') }}"  placeholder="">
                                        @if ($errors->has('zipCode'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('zipCode') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('country') ? ' has-danger' : '' }} col-md-6">
                                        <label>Country<span class="required">*</span></label>
                                        <select class="form-control" id="country" name="country">
                                            <option value="">Select Country</option>
                                            @foreach($countries as $country)
                                                <option data-code="{{ $country->code }}"
                                                        value="{{ $country->id }}" {{ old('country') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('country'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('country') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group{{ $errors->has('phone') ? ' has-danger' : '' }} col-md-6">
                                        <label>Phone <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}"  placeholder="">
                                        @if ($errors->has('phone'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('phone') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('fax') ? ' has-danger' : '' }} col-md-6">
                                        <label>Fax #</label>
                                        <input type="text" class="form-control" id="fax" name="fax" value="{{ old('fax') }}" placeholder="">
                                        @if ($errors->has('fax'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('fax') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom_checkbox">
                                        <input type="checkbox" id="showroomCommercial" name="showroomCommercial" value="1" {{ old('showroomCommercial') ? 'checked' : '' }}/>
                                        <label for="showroomCommercial">This address is commercial.</label>
                                    </div>
                                </div>
                                <h3>Billing Address</h3>
                                <div class="form-group">
                                    <div class="custom_checkbox">
                                        <input type="checkbox" id="sameAsShowroomAddress" name="sameAsShowroomAddress" {{ old('sameAsShowroomAddress') ? 'checked' : '' }} />
                                        <label for="sameAsShowroomAddress">Check here if same as shipping address.</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label >Location : </label>
                                    <div class="custom_radio">
                                        <input class="factoryLocation" type="radio" id="factoryLocationUS" name="factoryLocation"
                                               value="US" {{ (old('factoryLocation') == 'US' || empty(old('factoryLocation'))) ? 'checked' : '' }} />
                                        <label for="factoryLocationUS">United States</label>
                                    </div>
                                    <div class="custom_radio">
                                        <input class="factoryLocation" type="radio" id="factoryLocationCA" name="factoryLocation"
                                               value="CA" {{ old('factoryLocation') == 'CA' ? 'checked' : '' }}  />
                                        <label for="factoryLocationCA">Canada</label>
                                    </div>
                                    <div class="custom_radio">
                                        <input class="factoryLocation" type="radio" id="factoryLocationInt" name="factoryLocation"
                                               value="INT" {{ old('factoryLocation') == 'INT' ? 'checked' : '' }} />
                                        <label for="factoryLocationInt">International</label>
                                    </div>
                                </div>


                                <div class="form-row">
                                    <div class="form-group{{ $errors->has('factoryAddress') ? ' has-danger' : '' }} col-md-6">
                                        <label>Address <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="factoryAddress" name="factoryAddress"
                                               value="{{ old('factoryAddress') }}"  placeholder="">
                                        @if ($errors->has('factoryAddress'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('factoryAddress') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('factoryUnit') ? ' has-danger' : '' }} col-md-6">
                                        <label>Unit #</label>
                                        <input type="text" class="form-control"  id="factoryUnit" name="factoryUnit" value="{{ old('factoryUnit') }}" placeholder="">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group{{ $errors->has('factoryCity') ? ' has-danger' : '' }} col-md-6">
                                        <label>City <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="factoryCity" name="factoryCity" value="{{ old('factoryCity') }}"  placeholder="">
                                        @if ($errors->has('factoryCity'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('factoryCity') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('factoryState') ? ' has-danger' : '' }} col-md-6" id="form-group-factory-state">
                                        <label>State <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="factoryState" name="factoryState" value="{{ old('factoryState') }}"  placeholder="">
                                        @if ($errors->has('factoryState'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('factoryState') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('factoryStateSelect') ? ' has-danger' : '' }} col-md-6" id="form-group-factory-state-select">
                                        <label>State <span class="required">*</span></label>
                                        <select class="form-control" id="factoryStateSelect" name="factoryStateSelect">
                                            <option value="">Select State</option>
                                        </select>
                                        @if ($errors->has('factoryStateSelect'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('factoryStateSelect') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group{{ $errors->has('factoryZipCode') ? ' has-danger' : '' }} col-md-6">
                                        <label>Zip Code <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="factoryZipCode" name="factoryZipCode"
                                               value="{{ old('factoryZipCode') }}"  placeholder="">
                                        @if ($errors->has('factoryZipCode'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('factoryZipCode') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('factoryCountry') ? ' has-danger' : '' }} col-md-6">
                                        <label>Country<span class="required">*</span></label>
                                        <select class="form-control" id="factoryCountry" name="factoryCountry">
                                            <option value="">Select Country</option>
                                            @foreach($countries as $country)
                                                <option data-code="{{ $country->code }}"
                                                        value="{{ $country->id }}" {{ old('factoryCountry') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('factoryCountry'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('factoryCountry') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group{{ $errors->has('factoryPhone') ? ' has-danger' : '' }} col-md-6">
                                        <label>Phone <span class="required">*</span></label>
                                        <input type="text" class="form-control"  id="factoryPhone" name="factoryPhone" value="{{ old('factoryPhone') }}"  placeholder="">
                                        @if ($errors->has('factoryPhone'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('factoryPhone') }}</div>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('factoryFax') ? ' has-danger' : '' }} col-md-6">
                                        <label>Fax #</label>
                                        <input type="text" class="form-control" id="factoryFax" name="factoryFax" value="{{ old('factoryFax') }}" placeholder="">
                                        @if ($errors->has('factoryFax'))
                                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('factoryFax') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom_checkbox">
                                        <input type="checkbox" id="factoryCommercial" name="factoryCommercial" value="1" {{ old('factoryCommercial') ? 'checked' : '' }} />
                                        <label for="factoryCommercial">This address is commercial.</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom_checkbox">
                                        <input type="checkbox" id="receiveSpecialOffers" value="1" name="receiveSpecialOffers" checked />
                                        <label for="receiveSpecialOffers">Sign up to receive special offers and information.</label>
                                    </div>
                                </div>


                                <button type="submit" class="btn btn-default common_btn">Register</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =========================
        END REGISTER SECTION
    ============================== -->
@stop

@section('additionalJS')
    <script>

        $('#receiveSpecialOffers').click(function () {
            var  ischecked_signup = $("#receiveSpecialOffers").is(":checked");
            // console.log(ischecked_signup);
            if (ischecked_signup) {
                $('#receiveSpecialOffers').val(1);
            }
            else {
                $('#receiveSpecialOffers').val(0);
            }

        });
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
                                $('#stateSelect').append('<option value="' + value.id + '" selected>' + value.name + '</option>');
                            else
                                $('#stateSelect').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }

                    if (location == 'CA') {
                        $.each(caStates, function (index, value) {
                            if (value.id == oldState)
                                $('#stateSelect').append('<option value="' + value.id + '" selected>' + value.name + '</option>');
                            else
                                $('#stateSelect').append('<option value="' + value.id + '">' + value.name + '</option>');
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
                                $('#factoryStateSelect').append('<option value="' + value.id + '" selected>' + value.name + '</option>');
                            else
                                $('#factoryStateSelect').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }

                    if (location == 'CA') {
                        $.each(caStates, function (index, value) {
                            if (value.id == oldFactoryState)
                                $('#factoryStateSelect').append('<option value="' + value.id + '" selected>' + value.name + '</option>');
                            else
                                $('#factoryStateSelect').append('<option value="' + value.id + '">' + value.name + '</option>');
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