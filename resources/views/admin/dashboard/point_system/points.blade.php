<div class="row {{ ($errors && sizeof($errors) > 0) ? 'd-none' : '' }}" id="addBtnRow">
    <div class="col-md-12">
        <button class="btn btn-primary" id="btnAddNew">Add New Point</button>
    </div>
</div>
<div class="row {{ ($errors && sizeof($errors) > 0) ? '' : 'd-none' }}" id="addEditRow">
    <div class="col-md-12" style="border: 1px solid black">
        <h3><span id="addEditTitle">{{ old('inputAdd') == '0' ? 'Edit Point' : 'Add Point' }}</span></h3>
		<hr>
        <form class="form-horizontal" enctype="multipart/form-data" id="form"
              method="post" action="{{ (old('inputAdd') == '1') ? route('admin_points_add_post') : route('admin_points_edit_post') }}">
            @csrf

            <input type="hidden" name="inputAdd" id="inputAdd" value="{{ old('inputAdd') }}">
            <input type="hidden" name="pointId" id="pointId" value="{{ old('pointId') }}">

            <div class="form-group row">
                <div class="col-lg-2">
                    <label for="status" class="col-form-label">Status *</label>
                </div>

                <div class="col-lg-5">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" name="status" id="statusActive" name="status" class="custom-control-input" value="1" {{ (old('status') == '1' || empty(old('status'))) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="statusActive">Active</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="statusInactive" name="status" class="custom-control-input" value="0" {{ old('status') == '0' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="statusInactive">Inactive</label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-2">
                    <label for="point_type" class="col-form-label">Point Type</label>
                </div>

                <div class="col-lg-10">
                    <select class="form-control" name="point_type" id="point_type">
                         <option value="Percentage discount by order amount">Percentage discount by order amount</option>
                         <option value="Unit price discount by order amount">Unit price discount by order amount</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-2">
                    <label for="status" class="col-form-label">Discount Details</label>
                </div>

                <div class="col-lg-10">
                    <div class="point_table">
                        <table>
                            <tr>
                                <th>Requirement</th>
                                <th>Discount Options</th>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" id="from_price_1" name="from_price_1" size="8" class=""> point
                                </td>
                                <td>
                                    <div class="form-check custom_checkbox">
                                        <input class="form-check-input" type="checkbox" value="1" id="free_shipping_1" name="free_shipping_1">
                                        <label class="form-check-label" for="free_shipping_1">
                                            Free Shipping
                                        </label>
                                    </div>
                                    <span class="dollarSpan"></span>
                                    <input type="text" id="discount_1" name="discount_1" size="3" class=""> <span class="discountSpan"></span>
                                    <br>
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-lg-12 text-right">
                    <button class="btn grey_btn" id="btnCancel">Cancel</button>
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
                <th>Point</th>
                <th>Discount</th>
                <th>Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>
            @foreach($points as $point)
                <tr>
                    <td>{{ $point->from_price_1 }}</td>
                    <td>
                        @if($point->free_shipping_1 != 0)
                            Free Shipping
                            
                        @elseif($point->point_type === 'Unit price discount by order amount')
                            $ {{ $point->unit_price_discount_1 }}
                        @elseif($point->point_type === 'Percentage discount by order amount')
                            {{ $point->percentage_discount_1 }} %
                        @endif
                    </td>
                    <td>{{ $point->point_type }}</td>
                    <td>
                        @if ($point->status == 1)
                            Active
                        @else
                            Inactive
                        @endif
                    </td>
                    <td>
                        <a class="btnEdit" data-id="{{ $point->id }}" data-index="{{ $loop->index }}" role="button" style="color: blue">Edit</a> |
                        <a class="btnDelete" data-id="{{ $point->id }}" role="button" style="color: red">Delete</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
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