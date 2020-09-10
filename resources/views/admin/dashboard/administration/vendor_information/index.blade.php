@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <style>
        .checkbox-inline {
            display: inline-flex;
        }
    </style>
@stop

@section('content')

<div class="vendor_info_content">
    <div class="tab_wrapper pa_0">
        <div class="ly_tab">
            <nav class="tabs tab_with_link">
                <div class="tab_three">
                    <ul>
                        <li href="#CompanyInfo" class="active" data-toggle="tab">Company Info</li>
                        <li href="#return" data-toggle="tab">Return Info</li>
                        <li href="#setting" data-toggle="tab">Setting</li>
                        <li href="#shipping" data-toggle="tab">Shipping</li>
                    </ul>
                </div>
            </nav>
            <div class="tab_content_wrapper">
                <div id="CompanyInfo" class="tab_content show">
                    <div class="fadein">
                        @include('admin.dashboard.administration.vendor_information.includes.company_info')
                    </div>
                </div>

                <div id="return" class="tab_content">
                    <div class="fadein">
                        @include('admin.dashboard.administration.vendor_information.includes.return_policy')
                    </div>
                </div>
                <div id="setting" class="tab_content">
                    <div class="fadein">
                        @include('admin.dashboard.administration.vendor_information.includes.settings')
                    </div>
                </div>
                <div id="shipping" class="tab_content">
                    <div class="fadein">
                        @include('admin.dashboard.administration.vendor_information.includes.shipping')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>

    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);

            var usStates = <?php echo json_encode($usStates); ?>;
            var caStates = <?php echo json_encode($caStates); ?>;
            var showroomStateId = '{{ $user->vendor->billing_state_id }}';
            var warehouseStateId = '{{ $user->vendor->factory_state_id }}';

            $('#showroom_country').change(function () {
                var countryId = $(this).val();
                $('#showroom_state').html('<option value="">Select State</option>');

                if (countryId == 1) {
                    $.each(usStates, function (index, value) {
                        if (value.id == showroomStateId)
                            $('#showroom_state').append('<option value="'+value.id+'" selected>'+value.name+'</option>');
                        else
                            $('#showroom_state').append('<option value="'+value.id+'">'+value.name+'</option>');
                    });
                } else if (countryId == 2) {
                    $.each(caStates, function (index, value) {
                        if (value.id == showroomStateId)
                            $('#showroom_state').append('<option value="'+value.id+'" selected>'+value.name+'</option>');
                        else
                            $('#showroom_state').append('<option value="'+value.id+'">'+value.name+'</option>');
                    });
                }
            });

            $('#warehouse_country').change(function () {
                var countryId = $(this).val();
                $('#warehouse_state').html('<option value="">Select State</option>');

                if (countryId == 1) {
                    $.each(usStates, function (index, value) {
                        if (value.id == warehouseStateId)
                            $('#warehouse_state').append('<option value="'+value.id+'" selected>'+value.name+'</option>');
                        else
                            $('#warehouse_state').append('<option value="'+value.id+'">'+value.name+'</option>');
                    });
                } else if (countryId == 2) {
                    $.each(caStates, function (index, value) {
                        if (value.id == warehouseStateId)
                            $('#warehouse_state').append('<option value="'+value.id+'" selected>'+value.name+'</option>');
                        else
                            $('#warehouse_state').append('<option value="'+value.id+'">'+value.name+'</option>');
                    });
                }
            });

            $('#showroom_country').trigger('change');
            $('#warehouse_country').trigger('change');

            var orderNotice = CKEDITOR.replace( 'returndescription' );
            var Shipping = CKEDITOR.replace( 'order_shipping_editors' );



            $('#btnOrderNoticeSubmit').click(function (e) {
                e.preventDefault();
                var description = orderNotice.getData();

                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_order_notice_post') }}",
                    data: { description: description }
                }).done(function( msg ) {
                    toastr.success("Order Notice Updated!");
                });
            });


            $('#btnSaveSettings').click(function (e) {
                var data = $('#setting_description').val();
                e.preventDefault();

                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_save_setting_post') }}",
                    data: {data: data},
                }).done(function( data ) {
                    if (data.success) {
                        toastr.success("Settings Saved!");
                    } else {
                        alert(data.message);
                    }
                });
            });

            $('#btnShippingSave').click(function (e) {

                e.preventDefault();
                var description = Shipping.getData();

                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_shipping_post') }}",
                    data: { description: description }
                }).done(function( msg ) {
                    toastr.success("Shipping Updated!");
                });
            });
        })
    </script>
@stop
