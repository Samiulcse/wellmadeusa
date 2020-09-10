<div class="ly_accrodion">
    <div class="ly_accrodion_heading">
        <div id="btnAddNewFabric" class="ly_accrodion_title {{ count($errors) > 0 ? ' open_acc' : ''}}" data-toggle="accordion" data-target="#addNewFabric" data-class="accordion">
            <span id="addEditTitleFabric">Add a New Fabric</span>
        </div>
    </div>
    <div class="accordion_body default_accrodion  {{ count($errors) > 0 ? ' open' : ''}}" id="addNewFabric" style="">            
        <div class="form_row">
            <div class="label_inline required width_150p">
                Status:
            </div>
            <div class="form_inline">
                <div class="custom_radio">
                    <input type="radio" id="statusActiveFabric" name="statusFabric" value="1"
                        {{ (old('status') == '1' || empty(old('status'))) ? 'checked' : '' }}>
                    <label for="statusActiveFabric">Active</label>
                </div>
                <div class="custom_radio">
                    <input type="radio" id="statusInactiveFabric" name="statusFabric" value="0" 
                        {{ old('status') == '0' ? 'checked' : '' }}>
                    <label for="statusInactiveFabric">Inactive</label>
                </div>
            </div>
        </div>   

        <div class="form_row">
            <div class="label_inline required width_150p">
                <label for="color_name" class="col-form-label">Fabric description </label>
            </div>
            <div class="form_inline">
                <input type="text" class="form_global{{ $errors->has('fabric_description') ? ' is-invalid' : '' }}"
                    placeholder="Fabric description"  id="fabric_description"  value="{{ old('fabric_description') }}">
            </div>
        </div>
        
        <div class="form_row">
            <div class="label_inline width_150p required">
                <label for="master_color">Master Fabric</label>
            </div>
            <div class="form_inline">
                <div class="select">
                    <select id="master_fabric" class="form_global{{ $errors->has('master_fabric') ? ' is-invalid' : '' }}">
                        <option value="">Select Master Fabric</option>
    
                        @foreach($masterFabrics as $fabric)
                            <option value="{{ $fabric->id }}">{{ $fabric->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="form_row">
            <div class="label_inline width_150p"></div>
            <div class="form_inline">
                <span class="mr_8" for="defaultFabric">Default</span>
                <div class="custom_checkbox">
                    <input type="checkbox" id="defaultFabric" name="checkbox" value="1" name="defaultFabric">
                    <label for="defaultFabric"></label>
                </div>
            </div>
        </div>
        <div class="create_item_color">
            <div class="float_right">
                <div class="display_inline">
                    <span id="btnCancelFabric" data-toggle="accordion" data-target="#addNewFabric" data-class="accordion" class="accordion_heading" data-class="accordion" id="addNewFabricDismiss"><span class="ly_btn btn_danger width_80p " style="text-align:center">Cancel</span> </span>
                    <button id="btnAddFabric" class="ly_btn  btn_blue ">Add</button>
                    <button id="btnUpdateFabric" class="ly_btn  btn_blue ">Update</button>
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
                    <th>#</th>
                    <th>Fabric Description</th>
                    <th>Master Fabric</th>
                    <th>Active</th>
                    <th>Default</th>
                    <th>Action</th>
                </tr>
            </thead>        

            <tbody id="fabricTbody">
                @foreach($fabrics as $fabric)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><span class="fabricName">{{ $fabric->name }}</span></td>
                        <td><span class="masterFabricName">{{ $fabric->masterFabric->name }}</span></td>
                        <td>
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" data-id="{{ $fabric->id }}" class="custom-control-input statusFabric"
                                       value="1" {{ $fabric->status == 1 ? 'checked' : '' }}>
                                <span class="custom-control-indicator"></span>
                            </label>
                        </td>
                        <td>
                            <label class="custom-control custom-radio">
                                <input type="radio" name="defaultFabricTable" class="custom-control-input defaultFabric" data-id="{{ $fabric->id }}"
                                       value="1" {{ $fabric->default == 1 ? 'checked' : '' }}>
                                <span class="custom-control-indicator"></span>
                            </label>
                        </td>
                        <td>
                            <a class="link btnEditFabric cursor_pointer" data-id="{{ $fabric->id }}" data-index="{{ $loop->index }}" role="button" style="color: blue">Edit</a> |
                            <a class="link btnDeleteFabric cursor_pointer" data-id="{{ $fabric->id }}" data-index="{{ $loop->index }}" role="button" style="color: red">Delete</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal" data-modal="deleteModalFabric">
    <div class="modal_overlay" data-modal-close="deleteModalFabric"></div>
    <div class="modal_inner">
        <div class="modal_wrapper modal_470p">
            <div class="item_list_popup">
                <div class="modal_header display_table">
                    <span class="modal_header_title">Are you sure want to delete?</span>
                    <div class="float_right">
                        <span class="close_modal" data-modal-close="deleteModalFabric"></span>
                    </div>
                </div>
                <div class="modal_content">
                    <div class="ly-wrap-fluid">
                        <div class="ly-row">
                            <div class="ly-12">
                                <div class="display_table m15">
                                    <div class="float_right">
                                        <button class="ly_btn btn_blue width_150p " data-modal-close="deleteModalFabric">Close</button>
                                        <button class="ly_btn btn_danger width_150p" id="modalBtnDeleteFabric">Delete</button>
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

<template id="fabricTrTemplate">
    <tr>
        <td><span class="fabricIndex"></span></td>
        <td><span class="fabricName"></span></td>
        <td><span class="masterFabricName"></span></td>
        <td>
            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input statusFabric"
                       value="1">
                <span class="custom-control-indicator"></span>
            </label>
        </td>
        <td>
            <label class="custom-control custom-radio">
                <input type="radio" name="defaultFabricTable" class="custom-control-input defaultFabric"
                       value="1">
                <span class="custom-control-indicator"></span>
            </label>
        </td>
        <td>
            <a class="btnEditFabric cursor_pointer link" role="button" style="color: blue">Edit</a> |
            <a class="btnDeleteFabric cursor_pointer link" role="button" style="color: red">Delete</a>
        </td>
    </tr>
</template>
