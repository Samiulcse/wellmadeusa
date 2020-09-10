@extends('layouts.app')

@section('content')
<div class="container content">
    <div class="row">
        <div class="col-md-12">
            <div class="checkout-steps">
                <a>5. Complete</a>
                <a><span class="angle"></span>4. Review</a>
                <a><span class="angle"></span>3. Payment</a>
                <a><span class="angle"></span>2. Shipping Method</a>
                <a class="active"><span class="angle"></span>1. Address</a>
            </div>
        </div>
    </div>

    <form action="{{ route('checkout_address_post') }}" method="post" id="form">
        @csrf
        <input type="hidden" name="orders" value="{{ request()->get('id') }}" id="orders">
        <input type="hidden" name="address_id" value="{{ ($address != null) ? $address->id : '' }}" id="address_id">

        <h4>Shipping Address</h4>
        <hr class="padding-bottom-1x">

        <p id="address_text">
            @if ($address != null)
                {{ $address->address }}, {{ $address->city }}, {{ ($address->state == null) ? $address->state_text : $address->state->name }},
                <br>
                {{ $address->country->name }} - {{ $address->zip }}
            @endif
        </p>

        <a class="text-info common-btn" role="button" id="btnChangeAddress">Change123</a> |
        <a class="text-info common-btn" role="button" id="btnAddShippingAddress">Add New Shipping Address</a>

        <div class="checkout-footer">
            <div class="column"><a class="btn btn-outline-secondary" href="{{ route('show_cart') }}"><i class="icon-arrow-left"></i><span class="hidden-xs-down">&nbsp;Back To Cart</span></a></div>
            <div class="column">
                <button class="btn btn-primary" id="btnContinue"><span class="hidden-xs-down">Continue&nbsp;</span><i class="icon-arrow-right"></i></button>
            </div>
        </div>
    </form>
</div>
@stop
<div class="modal fade" id="addEditShippingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form id="modalForm">
            <input type="hidden" id="editAddressId" name="id">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Shipping Address </h4>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="small-rounded-input">Location</label><br>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input location" type="radio" id="locationUS" name="location" value="US" checked>
                                    <label class="custom-control-label" for="locationUS">United States</label>
                                </div>

                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input location" type="radio" id="locationCA" name="location" value="CA">
                                    <label class="custom-control-label" for="locationCA">Canada</label>
                                </div>

                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input location" type="radio" id="locationInt" name="location" value="INT">
                                    <label class="custom-control-label" for="locationInt">International</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Store No.</label>
                                <input class="form-control form-control-rounded form-control-sm" type="text" id="store_no" name="store_no">
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div class="form-group" id="form-group-address">
                                <label for="small-rounded-input">Address <span class="required">*</span></label>
                                <input class="form-control form-control-rounded form-control-sm" type="text" id="address" name="address">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="small-rounded-input">Unit #</label>
                                <input class="form-control form-control-rounded form-control-sm" type="text" id="unit" name="unit">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" id="form-group-city">
                                <label for="small-rounded-input">City <span class="required">*</span></label>
                                <input class="form-control form-control-rounded form-control-sm" type="text" id="city" name="city">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group" id="form-group-state">
                                <label for="small-rounded-input">State <span class="required">*</span></label>
                                <input class="form-control form-control-rounded form-control-sm" type="text" id="state" name="state">
                            </div>

                            <div class="form-group" id="form-group-state-select">
                                <label for="small-rounded-input">State <span class="required">*</span></label>
                                <select class="form-control form-control-rounded form-control-sm" id="stateSelect" name="stateSelect">
                                    <option value="">Select State</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" id="form-group-country">
                                <label for="small-rounded-input">Country <span class="required">*</span></label>
                                <select class="form-control form-control-rounded form-control-sm" id="country" name="country">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option data-code="{{ $country->code }}" value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group" id="form-group-zip">
                                <label for="small-rounded-input">Zip Code <span class="required">*</span></label>
                                <input class="form-control form-control-rounded form-control-sm" type="text" id="zipCode" name="zipCode">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" id="form-group-phone">
                                <label for="small-rounded-input">Phone <span class="required">*</span></label>
                                <input class="form-control form-control-rounded form-control-sm" type="text" id="phone" name="phone">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="small-rounded-input">Fax</label>
                                <input class="form-control form-control-rounded form-control-sm" type="text" id="fax" name="fax">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="showroomCommercial" name="showroomCommercial" value="1">
                                    <label class="custom-control-label" for="showroomCommercial">This address is commercial.</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary btn-sm" type="button" id="modalBtnAdd">Add</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="selectShippingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Select Shipping Address</h4>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table class="table table-bordered">
                            @foreach($shippingAddresses as $address)
                                <tr>
                                    <td>
                                        <b>{{ $address->store_no }}</b><br>
                                        {{ $address->address }}, {{ $address->city }}, {{ ($address->state == null) ? $address->state_text : $address->state->name }},
                                        <br>
                                        {{ $address->country->name }} - {{ $address->zip }}
                                    </td>

                                    <td class="text-center">
                                        <button class="btn btn-primary btnSelectAddress" data-index="{{ $loop->index }}" data-id="{{ $address->id }}">Select</button>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script>
        $(function () {
            var usStates = <?php echo json_encode($usStates); ?>;
            var caStates = <?php echo json_encode($caStates); ?>;
            var shippingAddresses = <?php echo json_encode($shippingAddresses); ?>;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': '{!! csrf_token() !!}'
                }
            });

            $('#btnContinue').click(function (e) {
                e.preventDefault();

                if ($('#address_id').val() == '') {
                    alert ('Select a Shipping Address.');
                    return;
                } else {
                    $('#form').submit();
                }
            });

            $('#btnChangeAddress').click(function () {
                $('#selectShippingModal').modal('show');
            });

            $('.btnSelectAddress').click(function () {
                var id = $(this).data('id');
                var index = $(this).data('index');

                $('#address_id').val(id);

                var address = shippingAddresses[index];

                $('#address_text').html(address.address + ', ' + address.city + ', ');

                if (address.state == null) {
                    $('#address_text').append(address.state_text + ', ');
                } else {
                    $('#address_text').append(address.state.name + ', ');
                }

                $('#address_text').append('<br>' + address.country.name + ' - ' + address.zip);

                $('#selectShippingModal').modal('hide');
            });

            // Add Shipping Address
            $('#btnAddShippingAddress').click(function () {
                $('#addEditShippingModal').modal('show');
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
                        setAddressId(data.id);
                    });

                    $('#country').prop('disabled', true);
                }
            });

            function setAddressId(id) {
                var orders = $('#orders').val();

                $.ajax({
                    method: "POST",
                    url: "{{ route('checkout_address_select') }}",
                    data: { shippingId: id, id: orders },
                }).done(function( data ) {
                    window.location.reload(true);
                });
            }

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

            function shippingAddressValidate() {
                var error = false;
                var location = $('.location:checked').val();

                clearModalForm();

                if ($('#address').val() == '') {
                    $('#form-group-address').addClass('has-danger');
                    error = true;
                }

                if ($('#city').val() == '') {
                    $('#form-group-city').addClass('has-danger');
                    error = true;
                }

                if ((location == 'US' || location == 'CA') && $('#stateSelect').val() == '') {
                    $('#form-group-state-select').addClass('has-danger');
                    error = true;
                }

                if (location == 'INT' && $('#state').val() == '') {
                    $('#form-group-state').addClass('has-danger');
                    error = true;
                }

                if ($('#country').val() == '') {
                    $('#form-group-country').addClass('has-danger');
                    error = true;
                }

                if ($('#zipCode').val() == '') {
                    $('#form-group-zip').addClass('has-danger');
                    error = true;
                }

                if ($('#phone').val() == '') {
                    $('#form-group-phone').addClass('has-danger');
                    error = true;
                }

                return error;
            }
        });
    </script>
@stop