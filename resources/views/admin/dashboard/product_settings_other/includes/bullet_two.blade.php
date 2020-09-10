<div class="row">
    <div class="col-md-12">
        <button class="btn btn-primary" id="btnAddNewbulletTwo">Add a New Details Option</button>
    </div>
</div>

<div class="row d-none" id="addEditRowbulletTwo">
    <div class="col-md-12">
        <h3><span id="addEditTitlebulletTwo"></span></h3>

        <div class="form-group row">
            <div class="col-lg-2">
                <label for="status" class="col-form-label">Status</label>
            </div>

            <div class="col-lg-5">
                <label for="statusActivebulletTwo" class="custom-control custom-radio inline_radio">
                    <input id="statusActivebulletTwo" name="statusbulletTwo" type="radio" class="custom-control-input"
                           value="1" checked>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">Active</span>
                </label>
                <label for="statusInactivebulletTwo" class="custom-control custom-radio signin_radio4 inline_radio">
                    <input id="statusInactivebulletTwo" name="statusbulletTwo" type="radio" class="custom-control-input" value="0">
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
                <input type="text" id="bulletTwoDescription" class="form-control">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-lg-12 text-right">
                <button class="btn btn-secondary" id="btnCancelbulletTwo">Cancel</button>
                <button id="btnAddbulletTwo" class="btn btn-primary">Add</button>
                <button id="btnUpdatebulletTwo" class="btn btn-primary">Update</button>
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

    <tbody id="bulletTwoTbody">
        @foreach($bulletTwoDetails as $prod)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><span class="bulletTwoDescription">{{ $prod->bullet_two_desc }}</span></td>
                <td>
                    <label class="custom-control custom-checkbox">
                        <input type="checkbox" data-id="{{ $prod->id }}" class="custom-control-input statusbulletTwo"
                               value="1" {{ $prod->status == 1 ? 'checked' : '' }}>
                        <span class="custom-control-indicator"></span>
                    </label>
                </td>
                <td>
                    <a class="btnEditbulletTwo cursor_pointer" data-id="{{ $prod->id }}" data-index="{{ $loop->index }}" role="button" style="color: blue;">Edit</a> |
                    <a class="btnDeletebulletTwo cursor_pointer" data-id="{{ $prod->id }}" data-index="{{ $loop->index }}" role="button" style="color: red;">Delete</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<template id="bulletTwoTrTemplate">
    <tr>
        <td><span class="bulletTwoIndex"></span></td>
        <td><span class="bulletTwoDescription"></span></td>
        <td>
            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input statusbulletTwo"
                       value="1">
                <span class="custom-control-indicator"></span>
            </label>
        </td>
        <td>
            <a class="btnEditbulletTwo cursor_pointer" role="button" style="color: blue">Edit</a> |
            <a class="btnDeletebulletTwo cursor_pointer" role="button" style="color: red">Delete</a>
        </td>
    </tr>
</template>

<div class="modal fade" id="deleteModalbulletTwo" role="dialog" aria-labelledby="deleteModalbulletTwo">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h4 class="modal-title text-white" id="deleteModalbulletTwo">Delete</h4>
            </div>
            <div class="modal-body">
                <p>
                    Are you sure want to delete?
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn  btn-default" data-dismiss="modal">Close</button>
                <button class="btn  btn-danger" id="modalBtnDeletebulletTwo">Delete</button>
            </div>
        </div>
    </div>
    <!--- end modals-->
</div>