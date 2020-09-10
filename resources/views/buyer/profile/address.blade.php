@extends('layouts.home_layout')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
@stop

@section('content')


    <!-- =========================
        START REGISTER SECTION
    ============================== -->
    <section class="my_account_area common_top_margin">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-lg-2">
                    @include('buyer.profile.menu')
                </div>
                <div class="col-md-9 col-lg-8">
                    <div class="account_wrapper account_wrapper_b_t clearfix">
                        <div class="account_inner account_inner_b_t">
                            <h2>Shipping Address <a href="#" id="btnAddShippingAddress" >Add New Shipping Address</a></h2>
                            <form action="{{ route('buyer_update_address') }}" method="post">
                                @csrf 
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <th>Address</th>
                                        <th class="text-center">Default</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                    @foreach($shippingAddress as $address)
                                    <tr>
                                        <td>
                                            <b>{{ $address->store_no }}</b>
                                            {{ $address->address }}, {{ $address->city }}, {{ ($address->state == null) ? $address->state_text : $address->state->name }},
                                            <br>
                                            {{ $address->country->name }} - {{ $address->zip }}
                                        </td>

                                        <td class="text-center align-middle">
                                            <div class="custom_radio">
                                                <input class="defaultAddress" type="radio" id="default_address_{{ $address->id }}"
                                                       name="defaultAddress" {{ ($address->default == 1) ? 'checked' : '' }} value="{{ $address->id }}">
                                                <label for="default_address_{{ $address->id }}"></label>
                                            </div>
                                        </td>

                                        <td class="text-center align-middle">
                                            <a class="text-info btnEdit" href="#" data-id="{{ $address->id }}" data-index="{{ $loop->index }}">Edit</a> |
                                            <a class="text-danger btnDelete" href="#" role="button" data-id="{{ $address->id }}">Delete</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <h2>Billing Information <a href="#" id="btnAddBillingAddress">Add New Billing Address</a></h2> 
                            <hr>
                                <div class="form-row">
                                    <div class="col-md-12 table-responsive">
                                        <table class="table table-bordered shipping_address_table">
                                            <tr>
                                                <th>Address</th>
                                                <th class="text-center">Default</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                            @foreach($billing_address as $billingaddress)
                                                <tr>
                                                    <td> 
                                                        {{ $billingaddress->billing_address }}, {{ $billingaddress->billing_city }},
                                                        {{ ($billingaddress->billing_state_id == null) ? $billingaddress->billing_state : $billingaddress->state->name }},
                                                        <br>
                                                        {{ $billingaddress->country->name }} - {{ $billingaddress->billing_zip }}
                                                    </td> 
                                                    <td class="text-center">
                                                        <div class="custom-control custom_radio custom-control-inline">
                                                            <input class="custom-control-input defaultBillAddress" type="radio" id="default_billing_address_{{ $billingaddress->id }}"
                                                                name="defaultBillAddress" {{ ($billingaddress->default == 1) ? 'checked' : '' }} value="{{ $billingaddress->id }}">
                                                            <label class="custom-control-label" for="default_billing_address_{{ $billingaddress->id }}"></label>
                                                        </div>
                                                    </td> 
                                                    <td class="text-center">
                                                        <a class="text-info billingAddressEdit"  href="#" role="button" data-id="{{ $billingaddress->id }}" data-index="{{ $loop->index }}">Edit</a> |
                                                        <a class="text-danger billingAddressDelete" href="#"  role="button" data-id="{{ $billingaddress->id }}">Delete</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
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
        END REGISTER SECTION
    ============================== -->

    <div class="modal shipping_modal" id="addEditShippingModal">
        <div class="modal-dialog modal-lg" role="document">
            <form id="modalForm">
                <input type="hidden" id="editAddressId" name="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Shipping Address</h4>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"> </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form_radio">
                                        <label for="small-rounded-input">Location</label><br>
                                        <div class="custom_radio">
                                            <input class="location" type="radio" id="locationUS" name="location" value="US" checked>
                                            <label for="locationUS">United States</label>
                                        </div>
                                        <div class="custom_radio">
                                            <input class="location" type="radio" id="locationCA" name="location" value="CA">
                                            <label for="locationCA">Canada</label>
                                        </div>
                                        <div class="custom_radio">
                                            <input class="location" type="radio" id="locationInt" name="location" value="INT">
                                            <label for="locationInt">International</label>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-lg-6">
                                            <label>Store No.</label>
                                            <input class="form-control " type="text" id="store_no" name="store_no">
                                        </div>
                                        <div class="form-group col-lg-6" id="form-group-address">
                                            <label >Address <span class="required text-danger">*</span></label>
                                            <input class="form-control" type="text" id="address" name="address">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-lg-6">
                                            <label>Unit #</label>
                                            <input class="form-control" type="text" id="unit" name="unit">
                                        </div>
                                        <div class="form-group col-lg-6" id="form-group-city">
                                            <label >City <span class="required">*</span></label>
                                            <input class="form-control" type="text" id="city" name="city">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-lg-6" id="form-group-state">
                                            <label>State <span class="required">*</span></label>
                                            <input class="form-control" type="text" id="state" name="state">
                                        </div>
                                        <div class="form-group col-lg-6" id="form-group-state-select">
                                            <label >State <span class="required">*</span></label>
                                            <select class="form-control"  id="stateSelect" name="stateSelect">
                                                <option value="">Select State</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group " id="form-group-country">
                                        <label>Country <span class="required">*</span></label>
                                        <select class="form-control" id="country" name="country">
                                            <option value="">Select Country</option>
                                            @foreach($countries as $country)
                                                <option data-code="{{ $country->code }}" value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-lg-6" id="form-group-zip">
                                            <label>Zip Code <span class="required">*</span></label>
                                            <input class="form-control" type="text" id="zipCode" name="zipCode">
                                        </div>
                                        <div class="form-group col-lg-6" id="form-group-phone">
                                            <label >Phone <span class="required">*</span></label>
                                            <input class="form-control" type="text" id="phone" name="phone">
                                        </div>
                                    </div>
                                    <div class="form-group form_checkbox">
                                        <div class="custom_checkbox ">
                                            <input type="checkbox" id="showroomCommercial" name="showroomCommercial" value="1">
                                            <label for="showroomCommercial">This address is commercial.</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                                    <button class="btn btn-primary btn-sm" type="button" id="modalBtnAdd">Add</button>
                                    <button class="btn btn-primary btn-sm" type="button" id="modalBtnUpdate">Update</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
<!-- add/edit buyer billing address modal start -->
<div class="modal fade" id="addEditBillingModal" tabindex="9999" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form id="newbillingaddress">
            <input type="hidden" id="editbillingAddressId" name="id">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Billing Address</h4>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span> </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-row">
                                    <div class="form-group form_radio">
                                        <label for="small-rounded-input">United States</label><br>
                                        <div class="custom_radio">
                                            <input class="custom-control-input factoryLocation" type="radio" id="factoryLocationUS" name="factoryLocation" value="US" checked >
                                            <label class="custom-control-label" for="factoryLocationUS">United States</label>
                                        </div>

                                        <div class="custom_radio">
                                            <input class="custom-control-input factoryLocation" type="radio" id="factoryLocationCA" name="factoryLocation" value="CA" >
                                            <label class="custom-control-label" for="factoryLocationCA">Canada</label>
                                        </div>

                                        <div class="custom_radio">
                                            <input class="custom-control-input factoryLocation" type="radio" id="factoryLocationInt" name="factoryLocation" value="INT" >
                                            <label class="custom-control-label" for="factoryLocationInt">International</label>
                                        </div>
                                    </div>

                                </div>
                                <div class="form-row">
                                    <div class="form-group hasvalue col-md-6" id="form-group-factoryAddress">
                                        <label for="factoryAddress">Address</label>
                                        <input class="form-control  " type="text" id="factoryAddress" name="factoryAddress" value="" placeholder="Address">
                                        @if ($errors->has('factoryAddress'))
                                            <div class="form-control-feedback">{{ $errors->first('factoryAddress') }}</div>
                                        @endif
                                    </div>
                                
                                    <div class="form-group hasvalue col-md-6" id="form-group-factoryUnit">
                                        <label for="factoryUnit">Unit</label>
                                        <input class="form-control" type="text" id="factoryUnit" name="factoryUnit" value="" placeholder="Unit">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group hasvalue col-md-6" id="form-group-factoryCity">
                                        <label for="factoryCity">City</label>
                                        <input class="form-control" type="text" id="factoryCity" name="factoryCity" value="" placeholder="City">
                                        @if ($errors->has('factorycity'))
                                            <p class="text-danger">{{ $errors->first('factorycity') }}</p>
                                        @endif
                                    </div>
                                
                                    <div class="form-group col-md-6" id="form-group-factory-state" style="display: none;">
                                        <label for="factoryState">State</label>
                                        <input class="form-control" type="text" id="factoryState" name="factoryState" value="" placeholder="Enter state">

                                        @if ($errors->has('factoryState'))
                                            <div class="form-control-feedback">{{ $errors->first('factoryState') }}</div>
                                        @endif
                                    </div> 
                                    <div class="form-group col-md-6" id="form-group-factory-state-select" >
                                        <label for="factoryStateSelect">Select State</label>
                                        <select class="form-control " id="factoryStateSelect" name="factoryStateSelect">
                                            <option value="">Select State</option>
                                        </select>

                                        @if ($errors->has('factoryStateSelect'))
                                            <div class="form-control-feedback">{{ $errors->first('factoryStateSelect') }}</div>
                                        @endif
                                    </div>
                                    </div>
                                    <div class="form-row">
                                    <div class="form-group col-md-6" id="form-group-factory-factoryCountry">
                                        <label>Country</label>
                                        <select class="form-control " id="factoryCountry" name="factoryCountry">
                                            <option value="">Select Country </option>
                                            @foreach($countries as $country)
                                                <option data-code="{{ $country->code }}" value="{{ $country->id }}" >{{ $country->name }}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('factoryCountry'))
                                            <div class="form-control-feedback">{{ $errors->first('factoryCountry') }}</div>
                                        @endif
                                    </div>
                                
                                    <div class="form-group hasvalue col-md-6" id="form-group-factoryZipCode">
                                        <label for="factoryZipCode">ZIP / Postal Code</label>
                                        <input class="form-control " type="text" id="factoryZipCode" name="factoryZipCode" value=""  placeholder="Enter zip code">
                                        @if ($errors->has('factoryZipCode'))
                                            <p class="text-danger">{{ $errors->first('factoryZipCode') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group hasvalue col-md-6" id="form-group-factoryPhone">
                                        <label for="factoryPhone">Phone Number</label>
                                        <input class="form-control" type="text" id="factoryPhone" name="factoryPhone" value="" placeholder="Enter phone">
                                        @if ($errors->has('factoryPhone'))
                                            <p class="text-danger">{{ $errors->first('factoryPhone') }}</p>
                                        @endif
                                    </div>
                                
                                    <div class="form-group hasvalue col-md-6" id="form-group-fax">
                                        <label for="factoryfax">Fax</label>
                                        <input class="form-control " type="text" id="factoryfax" name="factoryfax" value=""  placeholder="Enter Fax">
                                        @if ($errors->has('factoryfax'))
                                            <p class="text-danger">{{ $errors->first('factoryfax') }}</p>
                                        @endif
                                    </div> 
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <div class="custom_checkbox ">
                                            <input type="checkbox" id="defaultaddress" name="defaultaddress" value="1">
                                            <label for="defaultaddress">This address use as Default.</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6 text-left">
                                <button class="btn btn-primary btn-sm " type="button" data-dismiss="modal">Close</button> 
                            </div>
                            <div class="col-md-6 text-right"> 
                                <button class="btn btn-primary btn-sm" type="button" id="AddNewBillingAddress">Add</button>
                                <button class="btn btn-primary btn-sm" type="button" id="updateBillingAddress">Update</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
<!-- add/edit buyer billing addres modal exit -->
<div class="modal fade" id="deletebillingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Delete</h4>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p>
                    Are you sure want to delete?
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn btn_common btn-sm" type="button" data-dismiss="modal">Close</button>
                <button class="btn btn_common btn-sm" type="button" id="modalBtnDeletebilling">Delete</button>
            </div>
        </div>
    </div>
</div>

@stop

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script>
        var usStates = <?php echo json_encode($usStates); ?>;
        var caStates = <?php echo json_encode($caStates); ?>;
        var billingAddress = <?php echo json_encode($billing_address); ?>;
        var oldFactoryState = '{{ empty(old('factoryStateSelect')) ? ($errors->has('factoryStateSelect') ? '' : $buyer->billing_state_id) : old('factoryStateSelect') }}';
        var shippingAddresses = <?php echo json_encode($shippingAddress); ?>;

        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': '{!! csrf_token() !!}'
                }
            });

            $('form').bind('submit', function () {
                $(this).find(':input').prop('disabled', false);
            });

            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);


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

            $('.factoryLocation').trigger('change');
            // Billing address  
            $('#btnAddBillingAddress').click(function (e) {
                e.preventDefault();
                $('#addEditBillingModal').modal('show');
                $('#AddNewBillingAddress').show();
                $('#updateBillingAddress').hide();
            });

            $('#AddNewBillingAddress').click(function () { 
                if (!billingsAddressValidate()) {
                    $('#factoryCountry').prop('disabled', false); 
                    $.ajax({
                        method: "POST",
                        url: "{{ route('buyer_add_billing_address') }}",
                        data: $('#newbillingaddress').serialize(),
                    }).done(function( data ) {
                        window.location.reload(true);
                    });

                    $('#country').prop('disabled', true);
                }
            });
            function billingsAddressValidate() {
                var error = false; 
                var factorylocation = $('.factoryLocation:checked').val(); 
                clearModalForm(); 
                if ($('#factoryAddress').val() == '') {
                    $('#form-group-factoryAddress').addClass('has-danger');
                    error = true;
                }

                if ($('#factoryCity').val() == '') {
                    $('#form-group-factoryCity').addClass('has-danger');
                    error = true;
                }

                if ((factorylocation == 'US' || factorylocation == 'CA') && $('#factoryStateSelect').val() == '') {
                    $('#form-group-factory-state-select').addClass('has-danger');
                    error = true;
                }

                if (factorylocation == 'INT' && $('#factoryState').val() == '') {
                    $('#form-group-factory-state').addClass('has-danger');
                    error = true;
                }

                if ($('#factoryCountry').val() == '') {
                    $('#form-group-factory-factoryCountry').addClass('has-danger');
                    error = true;
                }

                if ($('#factoryZipCode').val() == '') {
                    $('#form-group-factoryZipCode').addClass('has-danger');
                    error = true;
                }

                if ($('#factoryPhone').val() == '') {
                    $('#form-group-factoryPhone').addClass('has-danger');
                    error = true;
                }

                return error;
            }

            // Shipping Address
            $('#btnAddShippingAddress').click(function (e) {
                e.preventDefault();
                $('#addEditShippingModal').modal('show');
                $('#modalBtnAdd').show();
                $('#modalBtnUpdate').hide();
            });

            $('.location').change(function () {
                var location = $('.location:checked').val();

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
                            $('#stateSelect').append('<option value="'+value.id+'">'+value.name+'</option>');
                        });
                    }

                    if (location == 'CA') {
                        $.each(caStates, function (index, value) {
                            $('#stateSelect').append('<option value="'+value.id+'">'+value.name+'</option>');
                        });
                    }
                } else {
                    $('#country').prop('disabled', false);
                    $('#form-group-state-select').hide();
                    $('#form-group-state').show();
                    $('#country').val('');
                }
            });

            $('.location').trigger('change');

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

            $('#modalBtnAdd').click(function () {
                if (!shippingAddressValidate()) {
                    $('#country').prop('disabled', false);

                    $.ajax({
                        method: "POST",
                        url: "{{ route('buyer_add_shipping_address') }}",
                        data: $('#modalForm').serialize(),
                    }).done(function( data ) {
                        window.location.reload(true);
                    });

                    $('#country').prop('disabled', true);
                }
            });

            $('#addEditShippingModal').on('hide.bs.modal', function (event) {
                $("#locationUS").prop("checked", true);
                $('.location').trigger('change');

                $('#store_no').val('');
                $('#address').val('');
                $('#unit').val('');
                $('#city').val('');
                $('#stateSelect').val('');
                $('#state').val('');
                $('#zipCode').val('');
                $('#phone').val('');
                $('#fax').val('');
                $('#showroomCommercial').prop('checked', false);

                clearModalForm();
            });

            function clearModalForm() {
                $('#form-group-address').removeClass('has-danger');
                $('#form-group-city').removeClass('has-danger');
                $('#form-group-state-select').removeClass('has-danger');
                $('#form-group-state').removeClass('has-danger');
                $('#form-group-country').removeClass('has-danger');
                $('#form-group-zip').removeClass('has-danger');
                $('#form-group-phone').removeClass('has-danger');
            }

            // Default Address
            $('.defaultAddress').change(function () {
                var id = $('.defaultAddress:checked').val();

                $.ajax({
                    method: "POST",
                    url: "{{ route('buyer_default_shipping_address') }}",
                    data: { id: id },
                }).done(function( data ) {
                    toastr.success('Default Shipping Address Changed!');
                });
            });
            // Default billing Address
            $('.defaultBillAddress').change(function () {
                var id = $('.defaultBillAddress:checked').val(); 
                $.ajax({
                    method: "POST",
                    url: "{{ route('buyer_default_billing_address') }}",
                    data: { id: id },
                }).done(function( data ) {
                    toastr.success('Default Billing Address Changed!');
                });
            });

            // Delete Address
            var selectedId = '';

            $('.btnDelete').click(function () {
                $('#deleteModal').modal('show');
                selectedId = $(this).data('id');
            });

            $('#modalBtnDelete').click(function () {
                $.ajax({
                    method: "POST",
                    url: "{{ route('buyer_delete_shipping_address') }}",
                    data: { id: selectedId }
                }).done(function( msg ) {
                    window.location.reload(true);
                });
            });

            // Delete billling Address
            var billingaddressId = '';

            $('.billingAddressDelete').click(function () {
                $('#deletebillingModal').modal('show');
                billingaddressId = $(this).data('id');
            });

            $('#modalBtnDeletebilling').click(function () {
                $.ajax({
                    method: "POST",
                    url: "{{ route('buyer_delete_billing_address') }}",
                    data: { id: billingaddressId }
                }).done(function( msg ) {
                    window.location.reload(true);
                });
            });

            // Edit Shipping Address
            $('.btnEdit').click(function () {
                var id = $(this).data('id');
                var index = $(this).data('index');
                $('#editAddressId').val(id);

                var address = shippingAddresses[index];

                if (address.location == 'US')
                    $("#locationUS").prop("checked", true);
                else if (address.location == 'CA')
                    $("#locationCA").prop("checked", true);
                else
                    $('#locationInt').prop("checked", true);

                $('.location').trigger('change');

                $('#store_no').val(address.store_no);
                $('#address').val(address.address);
                $('#unit').val(address.unit);
                $('#city').val(address.city);
                $('#stateSelect').val(address.state_id);
                $('#state').val(address.state_text);
                $('#zipCode').val(address.zip);
                $('#country').val(address.country_id);
                $('#phone').val(address.phone);
                $('#fax').val(address.fax);

                if (address.commercial == 1)
                    $('#showroomCommercial').prop('checked', true);

                $('#addEditShippingModal').modal('show');
                $('#modalBtnAdd').hide();
                $('#modalBtnUpdate').show();
            });

            // Edit billing Address
            $('.billingAddressEdit').click(function () {
                var id = $(this).data('id');
                var index = $(this).data('index');
                $('#editbillingAddressId').val(id);

                var address = billingAddress[index];
 
                if (address.billing_location == 'US')
                    $("#factoryLocationUS").prop("checked", true);
                else if (address.billing_location == 'CA')
                    $("#factoryLocationCA").prop("checked", true);
                else
                    $('#factoryLocationInt').prop("checked", true);

                $('.factoryLocation').trigger('change');
 
                $('#factoryAddress').val(address.billing_address);
                $('#factoryUnit').val(address.billing_unit);
                $('#factoryCity').val(address.billing_city);
                $('#factoryStateSelect').val(address.billing_state_id);
                $('#factoryState').val(address.billing_state);
                $('#factoryZipCode').val(address.billing_zip);
                $('#factoryCountry').val(address.billing_country_id);
                $('#factoryPhone').val(address.billing_phone);
                $('#factoryfax').val(address.billing_fax);
 

                $('#addEditBillingModal').modal('show'); 
                $('#AddNewBillingAddress').hide();
                $('#updateBillingAddress').show();
            });

            $('#modalBtnUpdate').click(function () {
                if (!shippingAddressValidate()) {
                    $('#country').prop('disabled', false);

                    $.ajax({
                        method: "POST",
                        url: "{{ route('buyer_edit_shipping_address') }}",
                        data: $('#modalForm').serialize(),
                    }).done(function( data ) {
                        window.location.reload(true);
                    });

                    $('#country').prop('disabled', true);
                }
            });
            // billing address edit post
            $('#updateBillingAddress').click(function () {
                if (!billingsAddressValidate()) {
                    $('#factoryCountry').prop('disabled', false);

                    $.ajax({
                        method: "POST",
                        url: "{{ route('buyer_update_address') }}",
                        data: $('#newbillingaddress').serialize(),
                    }).done(function( data ) {
                        window.location.reload(true);
                    });

                    $('#factoryCountry').prop('disabled', true);
                }
            });

            function shippingAddressValidate() {
                var error = false;
                var location = $('.location:checked').val();

                clearModalForm();

                if ($('#address').val() == '') {
                    $('#form-group-address').addClass('text-danger');
                    $("#form-group-address").prop('required',true);
                    error = true;
                }

                if ($('#city').val() == '') {
                    $('#form-group-city').addClass('text-danger');
                    $("#city").prop('required',true);
                    error = true;
                }

                if ((location == 'US' || location == 'CA') && $('#stateSelect').val() == '') {
                    $('#form-group-state-select').addClass('text-danger');
                    $("#stateSelect").prop('required',true);
                    error = true;
                }

                if (location == 'INT' && $('#state').val() == '') {
                    $('#form-group-state').addClass('text-danger');
                    $("#state").prop('required',true);
                    error = true;
                }

                if ($('#country').val() == '') {
                    $('#form-group-country').addClass('text-danger');
                    $("#country").prop('required',true);
                    error = true;
                }

                if ($('#zipCode').val() == '') {
                    $('#form-group-zip').addClass('text-danger');
                    $("#zipCode").prop('required',true);

                    error = true;
                }

                if ($('#phone').val() == '') {
                    $('#form-group-phone').addClass('text-danger');
                    $("#phone").prop('required',true);
                    error = true;
                }

                return error;
            }
        })
    </script>
@stop