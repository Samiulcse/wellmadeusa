@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <link href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
@stop

@section('content')
<div class="ly_accrodion">
    <div class="ly_accrodion_heading">
        <div id="btnAddNew" class="ly_accrodion_title {{ count($errors) > 0 ? ' open_acc' : ''}}" data-toggle="accordion" data-target="#addShipMethod" data-class="accordion">
            <span id="addEditTitle">{{ old('inputAdd') == '0' ? 'Edit Ship Method' : 'Add Ship Method' }}</span>
        </div>
    </div>
    <div class="accordion_body default_accrodion  {{ count($errors) > 0 ? ' open' : ''}}" id="addShipMethod" style="">
        <form class="form-horizontal" enctype="multipart/form-data" id="addShipMethodForm"
              method="post" action="{{ (old('inputAdd') == '0') ? route('admin_ship_method_update') : route('admin_ship_method_add') }}">
            @csrf
            
            <input type="hidden" name="inputAdd" id="inputAdd" value="{{ old('inputAdd') }}">
            <input type="hidden" name="shipMethodId" id="shipMethodId" value="{{ old('shipMethodId') }}">      
                    
            <div class="form_row">
                <div class="label_inline required width_150p">
                    <label for="ship_method" class="col-form-label">Ship Method  </label>
                </div>
                <div class="form_inline">
                    <input type="text" id="ship_method" class="form_global{{ $errors->has('ship_method') ? ' is-invalid' : '' }}"
                           placeholder="Ship Method" name="ship_method" value="{{ old('ship_method') }}">
                </div>
            </div>


            <div class="form_row">
                <div class="label_inline required width_150p">
                    <label for="courier" class="col-form-label">Courier  </label>
                </div>
                <div class="form_inline">
                    <div class="select">
                        <select class="form_global{{ $errors->has('courier') ? ' is-invalid' : '' }}" id="courier"  name="courier">
                            <option value="">Select Courier</option>
                            @foreach($couriers as $courier)
                                <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form_row">
                <div class="label_inline width_150p">
                    <label for="fee" class="col-form-label">Fee  </label>
                </div>
                <div class="form_inline">
                    <input type="text" id="fee" class="form-control{{ $errors->has('fee') ? ' is-invalid' : '' }}"
                           placeholder="Fee" name="fee" value="{{ old('fee') }}">
                </div>
            </div>
        </form>
        <div class="create_item_color">
            <div class="float_right">
                <div class="display_inline">
                    <span id="btnCancel" data-toggle="accordion" data-target="#addShipMethod" data-class="accordion" class="accordion_heading" data-class="accordion" id="addShipMethodDismiss"><span class="ly_btn btn_danger width_80p " style="text-align:center">Close</span> </span>
                </div>
            </div>
            <div class="float_right">
                <div class="display_inline">
                    <a href="javascript:void(0)" onclick="document.getElementById('addShipMethodForm').submit();"><span class="ly_btn  btn_blue " id="btnSubmit">{{ old('inputAdd') == '0' ? 'Update' : 'Add' }}</span> </a>
                </div>
            </div>
        </div>
        <br>
    </div>
</div>
    <br>

    <div class="ly-row">
        <div class="ly-12">
            <table class="table table-striped" id="myTable">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Courier</th>
                    <th>Fee</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                @foreach($shipMethods as $sm)
                    <tr>
                        <td>{{ $sm->name }}</td>
                        <td>{{ $sm->courier->name }}</td>
                        <td>
                            @if ($sm->fee === null)
                                Actual Rate
                            @else
                                ${{ number_format($sm->fee, 2, '.', '') }}
                            @endif
                        </td>
                        <td>
                            
                            <a class="link btnEdit" data-id="{{ $sm->id }}" data-index="{{ $loop->index }}" role="button" style="color: blue">Edit</a> |
                            <a class="link btnDelete" data-id="{{ $sm->id }}" role="button" style="color: red">Delete</a>
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
    <script type="text/javascript" src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready( function () {
            $('#myTable').DataTable({
                'searching' : false,
                'paging' : false
            });
        });

        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var shipMethods = <?php echo json_encode($shipMethods->toArray()); ?>;
            var selectedId;
            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);

            $('#btnAddNew').click(function () {
                $('#addEditRow').removeClass('d-none');
                $('#addBtnRow').addClass('d-none');
                $('#addEditTitle').html('Add Ship Method');
                $('#btnSubmit').html('Add');
                $('#inputAdd').val('1');
                $('#addShipMethodForm').attr('action', '{{ route('admin_ship_method_add') }}');
            });

            $('#btnCancel').click(function (e) {
                e.preventDefault();

                $('#addEditTitle').html('Add Ship Method');
                $('#btnAddNew').removeClass('open_acc');
                $('#addEditRow').addClass('d-none');
                $('#addBtnRow').removeClass('d-none');

                // Clear form
                $('#ship_method').val('');
                $('#courier').val('');

                $('input').removeClass('is-invalid');
                $('.form-group').removeClass('has-danger');
            });

            $('.btnEdit').click(function () {
                var id = $(this).data('id');
                var index = $(this).data('index');

                $('#addEditRow').removeClass('d-none');
                $('#addBtnRow').addClass('d-none');
                $('#addEditTitle').html('Edit Ship Method');
                $('#btnSubmit').html('Update');
                $('#inputAdd').val('0');
                $('#addShipMethodForm').attr('action', '{{ route('admin_ship_method_update') }}');
                $('#shipMethodId').val(id);

                var shipMethod = shipMethods[index];

                $('#ship_method').val(shipMethod.name);
                $('#courier').val(shipMethod.courier_id);
                $('#fee').val(shipMethod.fee);
                
                if(!$('#addShipMethod').is(":visible")) {
                    let target = $('#addShipMethod');
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
                    url: "{{ route('admin_ship_method_delete') }}",
                    data: { id: selectedId }
                }).done(function( msg ) {
                    location.reload();
                });
            });
        })
    </script>
@stop
