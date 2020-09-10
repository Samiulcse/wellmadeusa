<div class="row">
    <div class="col-md-12">
        <button class="btn btn-primary" id="btnAddNewMaterial">Add a New Details Option</button>
    </div>
</div>

<div class="row d-none" id="addEditRowMaterial">
    <div class="col-md-12">
        <h3><span id="addEditTitleMaterial"></span></h3>

        <div class="form-group row">
            <div class="col-lg-2">
                <label for="status" class="col-form-label">Status</label>
            </div>

            <div class="col-lg-5">
                <label for="statusActiveMaterial" class="custom-control custom-radio inline_radio">
                    <input id="statusActiveMaterial" name="statusMaterial" type="radio" class="custom-control-input"
                           value="1" checked>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">Active</span>
                </label>
                <label for="statusInactiveMaterial" class="custom-control custom-radio signin_radio4 inline_radio">
                    <input id="statusInactiveMaterial" name="statusMaterial" type="radio" class="custom-control-input" value="0">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">Inactive</span>
                </label>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-2">
                <label for="color_name"  class="col-form-label">Description *</label>
            </div>

            <div class="col-lg-5">
                <input type="text" id="materialDescription" class="form-control">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-lg-12 text-right">
                <button class="btn btn-secondary" id="btnCancelMaterial">Cancel</button>
                <button id="btnAddMaterial" class="btn btn-primary">Add</button>
                <button id="btnUpdateMaterial" class="btn btn-primary">Update</button>
            </div>
        </div>
    </div>
</div>

<br>

<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Description</th>
            <th>Active</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody id="materialTbody">
        @foreach($productDetails as $prod)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><span class="materialDescription">{{ $prod->material_desc }}</span></td>
                <td>
                    <label class="custom-control custom-checkbox">
                        <input type="checkbox" data-id="{{ $prod->id }}" class="custom-control-input statusMaterial"
                               value="1" {{ $prod->status == 1 ? 'checked' : '' }}>
                        <span class="custom-control-indicator"></span>
                    </label>
                </td>
                <td>
                    <a class="btnEditMaterial cursor_pointer" data-id="{{ $prod->id }}" data-index="{{ $loop->index }}" role="button" style="color: blue;">Edit</a> |
                    <a class="btnDeleteMaterial cursor_pointer" data-id="{{ $prod->id }}" data-index="{{ $loop->index }}" role="button" style="color: red;">Delete</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<template id="materialTrTemplate">
    <tr>
        <td><span class="materialIndex"></span></td>
        <td><span class="materialDescription"></span></td>
        <td>
            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input statusMaterial"
                       value="1">
                <span class="custom-control-indicator"></span>
            </label>
        </td>
        <td>
            <a class="btnEditMaterial cursor_pointer" role="button" style="color: blue">Edit</a> |
            <a class="btnDeleteMaterial cursor_pointer" role="button" style="color: red">Delete</a>
        </td>
    </tr>
</template>

<div class="modal fade" id="deleteModalMaterial" role="dialog" aria-labelledby="deleteModalMaterial">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h4 class="modal-title text-white" id="deleteModalMaterial">Delete</h4>
            </div>
            <div class="modal-body">
                <p>
                    Are you sure want to delete?
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn  btn-default" data-dismiss="modal">Close</button>
                <button class="btn  btn-danger" id="modalBtnDeleteMaterial">Delete</button>
            </div>
        </div>
    </div>
    <!--- end modals-->
</div>