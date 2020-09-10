@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
@stop

@section('content')

<div class="ly_accrodion">
    <div class="ly_accrodion_heading">
        <div id="btnAddNew" class="ly_accrodion_title {{ count($errors) > 0 ? ' open_acc' : ''}}" data-toggle="accordion" data-target="#addCourier" data-class="accordion">
            <span id="addEditTitle">{{ old('inputAdd') == '0' ? 'Edit Courier' : 'Add Courier' }}</span>
        </div>
    </div>
    <div class="accordion_body default_accrodion  {{ count($errors) > 0 ? ' open' : ''}}" id="addCourier" style="">
        <form class="form-horizontal" enctype="multipart/form-data" id="addCourierForm"
              method="post" action="{{ (old('inputAdd') == '0') ? route('admin_courier_update') : route('admin_courier_add') }}">
            @csrf
            
            <input type="hidden" name="inputAdd" id="inputAdd" value="{{ old('inputAdd') }}">
            <input type="hidden" name="courierId" id="courierId" value="{{ old('courierId') }}">            
                    
            <div class="form_row">
                <div class="label_inline required width_150p">
                    <label for="courier_name" class="col-form-label">Courier Name </label>
                </div>
                <div class="form_inline">
                    <input type="text" class="form_global{{ $errors->has('courier_name') ? ' is-invalid' : '' }}"
                        placeholder="Courier Name" id="courier_name" name="courier_name" value="{{ old('courier_name') }}">
                </div>
            </div>
        </form>
        <div class="create_item_color">
            <div class="float_right">
                <div class="display_inline">
                    <span id="btnCancel" data-toggle="accordion" data-target="#addCourier" data-class="accordion" class="accordion_heading" data-class="accordion" id="addCourierDismiss"><span class="ly_btn btn_danger width_80p " style="text-align:center">Close</span> </span>
                </div>
            </div>
            <div class="float_right">
                <div class="display_inline">
                    <a href="javascript:void(0)" onclick="document.getElementById('addCourierForm').submit();"><span class="ly_btn  btn_blue " id="btnSubmit">{{ old('inputAdd') == '0' ? 'Update' : 'Add' }}</span> </a>
                </div>
            </div>
        </div>
        <br>
    </div>
</div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                @foreach($couriers as $courier)
                    <tr>
                        <td>{{ $courier->name }}</td>
                        <td>
                            <a class="link btnEdit" data-id="{{ $courier->id }}" data-index="{{ $loop->index }}" role="button" style="color: blue">Edit</a> |
                            <a class="link btnDelete" data-id="{{ $courier->id }}" role="button" style="color: red">Delete</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal" data-modal="deleteModal">
        <div class="modal_overlay" data-modal-close="deleteModal"></div>
        <div class="modal_inner">
            <div class="modal_wrapper modal_470p">
                <div class="item_list_popup">
                    <div class="modal_header display_table">
                        <span class="modal_header_title">Are you sure want to delete?</span>
                        <div class="float_right">
                            <span class="close_modal" data-modal-close="deleteModal"></span>
                        </div>
                    </div>
                    <div class="modal_content">
                        <div class="ly-wrap-fluid">
                            <div class="ly-row">
                                <div class="ly-12">
                                    <div class="display_table m15">
                                        <div class="float_right">
                                            <button class="ly_btn btn_blue width_150p " data-modal-close="deleteModal">Close</button>
                                            <button class="ly_btn btn_danger width_150p" id="modalBtnDelete">Delete</button>
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

            var couriers = <?php echo json_encode($couriers->toArray()); ?>;
            var selectedId;
            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);

            $('#btnAddNew').click(function () {
                $('#addEditRow').removeClass('d-none');
                $('#addBtnRow').addClass('d-none');
                $('#addEditTitle').html('Add Courier');
                $('#btnSubmit').html('Add');
                $('#inputAdd').val('1');
                $('#addCourierForm').attr('action', '{{ route('admin_courier_add') }}');
            });

            $('#btnCancel').click(function (e) {
                e.preventDefault();

                $('#addEditTitle').html('Add Courier');
                $('#btnAddNew').removeClass('open_acc');
                $('#addEditRow').addClass('d-none');
                $('#addBtnRow').removeClass('d-none');

                // Clear form
                $('#courier_name').val('');

                $('input').removeClass('is-invalid');
                $('.form-group').removeClass('has-danger');
            });

            $('.btnEdit').click(function () {
                var id = $(this).data('id');
                var index = $(this).data('index');

                $('#addEditRow').removeClass('d-none');
                $('#addBtnRow').addClass('d-none');
                $('#addEditTitle').html('Edit Courier');
                $('#btnSubmit').html('Update');
                $('#inputAdd').val('0');
                $('#addCourierForm').attr('action', '{{ route('admin_courier_update') }}');
                $('#courierId').val(id);

                var courier = couriers[index];

                $('#courier_name').val(courier.name);

                if(!$('#addCourier').is(":visible")) {
                    let target = $('#addCourier');
                    $('.ly_accrodion_title').toggleClass('open_acc');
                    target.slideToggle();
                }
            });

            $('.btnDelete').click(function () {
                var targeted_modal_class = 'deleteModal';
                $('[data-modal="' + targeted_modal_class + '"]').addClass('open_modal');
                selectedId = $(this).data('id');
            });

            $('#modalBtnDelete').click(function () {
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_courier_delete') }}",
                    data: { id: selectedId }
                }).done(function( msg ) {
                    location.reload();
                });
            });
        })
    </script>
@stop