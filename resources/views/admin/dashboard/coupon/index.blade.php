@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('themes/admire/css/components.css') }}" rel="stylesheet">

@stop

@section('content')
    <div class="row {{ ($errors && sizeof($errors) > 0) ? 'd-none' : '' }}" id="addBtnRow">
        <div class="col-md-12">
            <button class="btn btn-primary" id="btnAddNew">Add New Promo Code</button>
        </div>
    </div>

    <div class="row {{ ($errors && sizeof($errors) > 0) ? '' : 'd-none' }}" id="addEditRow">
        <div class="col-md-12" style="border: 1px solid black">
            <h3><span id="addEditTitle">{{ old('inputAdd') == '0' ? 'Edit Promo Code' : 'Add Promo Code' }}</span></h3>

            <form class="form-horizontal" enctype="multipart/form-data" id="form"
                  method="post" action="{{ (old('inputAdd') == '1') ? route('admin_coupon_add_post') : route('admin_coupon_edit_post') }}">
                @csrf

                <input type="hidden" name="inputAdd" id="inputAdd" value="{{ old('inputAdd') }}">
                <input type="hidden" name="couponId" id="couponId" value="{{ old('couponId') }}">

                <div class="form-group row{{ $errors->has('name') ? ' has-danger' : '' }}">
                    <div class="col-lg-2">
                        <label for="name" class="col-form-label">Name *</label>
                    </div>

                    <div class="col-lg-5">
                        <input type="text" id="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                               placeholder="Promo Code Name" name="name" value="{{ old('name') }}">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-lg-2">
                        <label for="type" class="col-form-label">Type *</label>
                    </div>

                    <div class="col-lg-5">
                        <label for="typeFixed" class="custom-control custom-radio">
                            <input id="typeFixed" name="type" type="radio" class="custom-control-input"
                                   value="1" {{ (old('type') == '1' || empty(old('type'))) ? 'checked' : '' }}>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Fixed Price</span>
                        </label>
                        <label for="typePercentage" class="custom-control custom-radio">
                            <input id="typePercentage" name="type" type="radio" class="custom-control-input" value="2" {{ old('type') == '2' ? 'checked' : '' }}>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Percentage</span>
                        </label>
                        <label for="typeFreeShipping" class="custom-control custom-radio">
                            <input id="typeFreeShipping" name="type" type="radio" class="custom-control-input" value="3" {{ old('type') == '3' ? 'checked' : '' }}>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Free Shipping</span>
                        </label>
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('amount') ? ' has-danger' : '' }}">
                    <div class="col-lg-2">
                        <label for="amount" class="col-form-label">Amount</label>
                    </div>

                    <div class="col-lg-5">
                        <input type="text" id="amount" class="form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}"
                               placeholder="Amount" name="amount" value="{{ old('amount') }}">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-lg-2">
                        <label for="multipleUse" class="col-form-label">Multiple Use *</label>
                    </div>

                    <div class="col-lg-5">
                        <label for="multipleUseYes" class="custom-control custom-radio">
                            <input id="multipleUseYes" name="multipleUse" type="radio" class="custom-control-input"
                                   value="1" {{ (old('multipleUse') == '1' || empty(old('multipleUse'))) ? 'checked' : '' }}>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Yes</span>
                        </label>
                        <label for="multipleUseNo" class="custom-control custom-radio">
                            <input id="multipleUseNo" name="multipleUse" type="radio" class="custom-control-input" value="0" {{ old('multipleUse') == '0' ? 'checked' : '' }}>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">No</span>
                        </label>
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('description') ? ' has-danger' : '' }}">
                    <div class="col-lg-2">
                        <label for="description" class="col-form-label">Description</label>
                    </div>

                    <div class="col-lg-5">
                        <input type="text" id="description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"
                               placeholder="Description" name="description" value="{{ old('description') }}">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-lg-12 text-right">
                        <button class="btn btn-default" id="btnCancel">Cancel</button>
                        <input type="submit" id="btnSubmit" class="btn btn-primary" value="{{ old('inputAdd') == '0' ? 'Update' : 'Add' }}">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Multiple Use</th>
                    <th>Description</th>
                    <th></th>
                </tr>
                </thead>

                <tbody>
                @foreach($coupons as $coupon)
                    <tr>
                        <td>{{ $coupon->name }}</td>
                        <td>
                            @if ($coupon->type == 1)
                                Fixed Price
                            @elseif ($coupon->type == 2)
                                Percentage
                            @elseif ($coupon->type == 3)
                                Free Shipping
                            @endif
                        </td>
                        <td>{{ number_format($coupon->amount, 2, '.', '') }}</td>
                        <td>
                            @if ($coupon->multiple_use == 1)
                                Yes
                            @else
                                No
                            @endif
                        </td>
                        <td>{{ $coupon->description }}</td>
                        <td>
                            <a class="btnEdit" data-id="{{ $coupon->id }}" data-index="{{ $loop->index }}" role="button" style="color: blue">Edit</a> |
                            <a class="btnDelete" data-id="{{ $coupon->id }}" role="button" style="color: red">Delete</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="pagination">
                {{ $coupons->links() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" role="dialog" aria-labelledby="deleteModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h4 class="modal-title text-white" id="deleteModal">Delete</h4>
                </div>
                <div class="modal-body">
                    <p>
                        Are you sure want to delete?
                    </p>
                </div>
                <div class="modal-footer">
                    <button class="btn  btn-default" data-dismiss="modal">Close</button>
                    <button class="btn  btn-danger" id="modalBtnDelete">Delete</button>
                </div>
            </div>
        </div>
        <!--- end modals-->
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

            var coupons = <?php echo json_encode($coupons->toArray()); ?>;
            coupons = coupons.data;
            var selectedId;
            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);

            $('#btnAddNew').click(function () {
                $('#addEditRow').removeClass('d-none');
                $('#addBtnRow').addClass('d-none');
                $('#addEditTitle').html('Add Promo Code');
                $('#btnSubmit').val('Add');
                $('#inputAdd').val('1');
                $('#form').attr('action', '{{ route('admin_coupon_add_post') }}');
            });

            $('#btnCancel').click(function (e) {
                e.preventDefault();

                $('#addEditRow').addClass('d-none');
                $('#addBtnRow').removeClass('d-none');

                // Clear form
                $('#typeFixed').prop('checked', true);
                $('#multipleUseYes').prop('checked', true);
                $('#name').val('');
                $('#amount').val('');
                $('#description').val('');

                $('input').removeClass('is-invalid');
                $('.form-group').removeClass('has-danger');
            });

            $('.btnEdit').click(function () {
                var id = $(this).data('id');
                var index = $(this).data('index');

                $('#addEditRow').removeClass('d-none');
                $('#addBtnRow').addClass('d-none');
                $('#addEditTitle').html('Edit Promo Code');
                $('#btnSubmit').val('Update');
                $('#inputAdd').val('0');
                $('#form').attr('action', '{{ route('admin_coupon_edit_post') }}');
                $('#couponId').val(id);

                var coupon = coupons[index];

                if (coupon.type == 1)
                    $('#typeFixed').prop('checked', true);
                else if (coupon.type == 2)
                    $('#typePercentage').prop('checked', true);
                else if (coupon.type == 3)
                    $('#typeFreeShipping').prop('checked', true);

                if (coupon.multiple_use == 1)
                    $('#multipleUseYes').prop('checked', true);
                else
                    $('#multipleUseNo').prop('checked', true);

                $('#name').val(coupon.name);
                $('#amount').val(coupon.amount);
                $('#description').val(coupon.description);
            });

            $('.btnDelete').click(function () {
                $('#deleteModal').modal('show');
                selectedId = $(this).data('id');
            });

            $('#modalBtnDelete').click(function () {
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_coupon_delete') }}",
                    data: { id: selectedId }
                }).done(function( msg ) {
                    location.reload();
                });
            });
        })
    </script>
@stop