<div class="ly_accrodion">
    <div class="ly_accrodion_heading">
        <div  id="btnAddNewMadeInCountry"  class="ly_accrodion_title {{ count($errors) > 0 ? ' open_acc' : ''}}" data-toggle="accordion" data-target="#addNewMadeInCountry" data-class="accordion">
            <span id="addEditTitleMadeInCountry">Add a New Made In Country</span>
        </div>
    </div>
    <div class="accordion_body default_accrodion  {{ count($errors) > 0 ? ' open' : ''}}" id="addNewMadeInCountry" style="">            
        <div class="form_row">
            <div class="label_inline required width_150p">
                Status:
            </div>
            <div class="form_inline">
                <div class="custom_radio">
                    <input type="radio" id="statusActiveMadeInCountry" name="statusMadeInCountry" value="1"
                        {{ (old('status') == '1' || empty(old('status'))) ? 'checked' : '' }}>
                    <label for="statusActiveMadeInCountry">Active</label>
                </div>
                <div class="custom_radio">
                    <input type="radio" id="statusInactiveMadeInCountry" name="statusMadeInCountry" value="0" 
                        {{ old('status') == '0' ? 'checked' : '' }}>
                    <label for="statusInactiveMadeInCountry">Inactive</label>
                </div>
            </div>
        </div>   

        <div class="form_row">
            <div class="label_inline required width_150p">
                <label for="color_name" class="col-form-label">Made In Country </label>
            </div>
            <div class="form_inline">
                <input type="text" class="form_global"
                    placeholder="Enter Made In Country Name"  id="madeInCountryName">
            </div>
        </div>

        <div class="form_row">
            <div class="label_inline width_150p"></div>
            <div class="form_inline">
                <span class="mr_8" for="defaultMadeInCountry">Default</span>
                <div class="custom_checkbox">
                    <input type="checkbox" id="defaultMadeInCountry" name="checkbox" value="1" name="defaultMadeInCountry">
                    <label for="defaultMadeInCountry"></label>
                </div>
            </div>
        </div>
        <div class="create_item_color">
            <div class="float_right">
                <div class="display_inline">
                    <span id="btnCancelMadeInCountry" data-toggle="accordion" data-target="#addNewMadeInCountry" data-class="accordion" class="accordion_heading" data-class="accordion" id="addNewMadeInCountryDismiss"><span class="ly_btn btn_danger width_80p " style="text-align:center">Cancel</span> </span>
                    <button id="btnAddMadeInCountry" class="ly_btn  btn_blue ">Add</button>
                    <button id="btnUpdateMadeInCountry" class="ly_btn  btn_blue ">Update</button>
                </div>
            </div>
        </div>
        <br>
    </div>
</div>

<br>
<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Made In Country</th>
            <th>Active</th>
            <th>Default</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody id="madeInCountryTbody">
        @foreach($madeInCountries as $country)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><span class="madeInCountryName">{{ $country->name }}</span></td>
                <td>
                    <label class="custom-control custom-checkbox">
                        <input type="checkbox" data-id="{{ $country->id }}" class="custom-control-input statusMadeInCountry"
                               value="1" {{ $country->status == 1 ? 'checked' : '' }}>
                        <span class="custom-control-indicator"></span>
                    </label>
                </td>
                <td>
                    <label class="custom-control custom-radio">
                        <input type="radio" name="defaultMadeInCountryTable" class="custom-control-input defaultMadeInCountry" data-id="{{ $country->id }}"
                               value="1" {{ $country->default == 1 ? 'checked' : '' }}>
                        <span class="custom-control-indicator"></span>
                    </label>
                </td>
                <td>
                    <a class="link btnEditMadeInCountry cursor_pointer" data-id="{{ $country->id }}" data-index="{{ $loop->index }}" role="button" style="color: blue">Edit</a> |
                    <a class="link btnDeleteMadeInCountry cursor_pointer" data-id="{{ $country->id }}" data-index="{{ $loop->index }}" role="button" style="color: red">Delete</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<template id="madeInCountryTrTemplate">
    <tr>
        <td><span class="madeInCountryIndex"></span></td>
        <td><span class="madeInCountryName"></span></td>
        <td>
            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input statusMadeInCountry"
                       value="1">
                <span class="custom-control-indicator"></span>
            </label>
        </td>
        <td>
            <label class="custom-control custom-radio">
                <input type="radio" name="defaultMadeInCountryTable" class="custom-control-input defaultMadeInCountry"
                       value="1">
                <span class="custom-control-indicator"></span>
            </label>
        </td>
        <td>
            <a class="link btnEditMadeInCountry cursor_pointer" role="button" style="color: blue">Edit</a> |
            <a class="link btnDeleteMadeInCountry cursor_pointer" role="button" style="color: red">Delete</a>
        </td>
    </tr>
</template>


<div class="modal" data-modal="deleteModalMadeInCountry">
    <div class="modal_overlay" data-modal-close="deleteModalMadeInCountry"></div>
    <div class="modal_inner">
        <div class="modal_wrapper modal_470p">
            <div class="item_list_popup">
                <div class="modal_header display_table">
                    <span class="modal_header_title">Are you sure want to delete?</span>
                    <div class="float_right">
                        <span class="close_modal" data-modal-close="deleteModalMadeInCountry"></span>
                    </div>
                </div>
                <div class="modal_content">
                    <div class="ly-wrap-fluid">
                        <div class="ly-row">
                            <div class="ly-12">
                                <div class="display_table m15">
                                    <div class="float_right">
                                        <button class="ly_btn btn_blue width_150p " data-modal-close="deleteModalMadeInCountry">Close</button>
                                        <button class="ly_btn btn_danger width_150p" id="modalBtnDeleteMadeInCountry">Delete</button>
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