<?php use App\Enumeration\Availability; ?>
@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/ezdz/jquery.ezdz.min.css') }}" rel="stylesheet">

    <style>
        .block__list_tags {
            text-align: left;
        }

        .block__list_tags li{
                display: inline-block;
                width: 9%;
                margin-right: 1%;
                margin-bottom: 1%;
                vertical-align: top;
                min-height: 212px;
                position: relative;
        }
        .block__list_tags li img{
            width: 100%;
        }

        .block__list_tags li:hover span {
            display: block;
        }

        .block__list_tags li span {
            display: none;
            position: absolute;
            top: -7px;
            right: -7px;
            width: 21px;
            height: 21px;
            padding: 5px;
            background: #999;
            border-radius: 50%;
            cursor: pointer;
            z-index: 101;
            text-align: center;
            color: #fff;
            font-size: 11px;
        }

        .d-none {
            display: none!important;
        }

        .btnRemoveImage {
            color: red !important;
        }
    </style>
@stop

@section('content')

    <form class="form-horizontal" method="post" action="{{ route('admin_create_new_item_post') }}" id="form" enctype="multipart/form-data">
        @csrf
        <div class="text_right m15">
            <div class="display_inline mr_0">
                <button type="submit" class="ly_btn  btn_blue min_width_100p ">Save</button>
            </div>
        </div>

        <div class="ly_accrodion">
            <div class="ly_accrodion_heading">
                <div class="ly_accrodion_title open_acc" data-toggle="accordion" data-target="#ItemInfo" data-class="accordion">
                    <span>Item Info</span>
                </div>
            </div>
            <div class="accordion_body default_accrodion open" id="ItemInfo">
                <div class="ly-wrap-fluid">
                    <div class="ly-row">
                        <div class="ly-6 pl_0 pr_60">
                            <div class="form_row">
                                <div class="label_inline width_150p">
                                    Status
                                </div>
                                <div class="form_inline">
                                    <div class="custom_radio">
                                        <input type="radio" id="statusActive" name="status" value="1" {{ old('status') == '1' ? 'checked' : '' }}>
                                        <label for="statusActive">Active</label>
                                    </div>
                                    <div class="custom_radio">
                                        <input type="radio" id="statusInactive" name="status" value="0" {{ (old('status') == '0' || empty(old('status'))) ? 'checked' : '' }}>
                                        <label for="statusInactive">Inactive</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form_row">
                                <div class="label_inline required width_150p">
                                    Style No.
                                </div>
                                <div class="form_inline">
                                    <input type="text" id="style_no" class="form_global{{ $errors->has('style_no') ? ' is-invalid' : '' }}"
                                           name="style_no" value="{{ old('style_no') }}">
                                </div>
                            </div>
                            <div class="form_row">
                                <div class="label_inline required width_150p">
                                    Price
                                </div>
                                <div class="form_inline">
                                    <div class="input_inline">
                                        <div class="display_inline">
                                            <div class="input_number plc_fixed_left">
                                                <input type="text" id="price" class="form_global text_right{{ $errors->has('price') ? ' is-invalid' : '' }}"
                                                       placeholder="$" name="price" value="{{ old('price') }}">
                                            </div>
                                        </div>
                                        <div class="display_inline float_right mr_0">
                                            <span class="mr_8">Orig. Price</span>
                                            <div class="input_number plc_fixed_left">
                                                <input type="text" id="orig_price" class="form_global text_right{{ $errors->has('orig_price') ? ' is-invalid' : '' }}"
                                                       placeholder="$" name="orig_price" value="{{ old('orig_price') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form_row">
                                <div class="label_inline required width_150p">
                                    Size
                                </div>
                                <div class="form_inline">
                                    <div class="select">
                                        <select class="form_global{{ $errors->has('size') ? ' is-invalid' : '' }}" name="size" id="size">
                                            <option value="">Select Size</option>

                                            @foreach($packs as $pack)
                                                <option value="{{ $pack->id }}" data-index="{{ $loop->index }}" {{ old('size') == $pack->id ? 'selected' : '' }}>{{ $pack->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form_row">
                                <div class="label_inline width_150p align_top">
                                    Description
                                </div>
                                <div class="form_inline">
                                    <textarea class="form_global{{ $errors->has('description') ? ' is-invalid' : '' }}" name="description" rows="8"
                                        placeholder="Max. 500 letters allowed. The following special characters are not allowed: <, >, {, }, ^, [, ], =, @, ;">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="ly-6 pr_0 pl_60">
                            <div class="form_row">
                                <div class="label_inline required width_150p">
                                    Item Name
                                </div>
                                <div class="form_inline">
                                    <input type="text" id="item_name" class="form_global{{ $errors->has('item_name') ? ' is-invalid' : '' }}"
                                           name="item_name" value="{{ old('item_name') }}">
                                </div>
                            </div>
                            <div class="form_row">
                                <div class="label_inline required width_150p">
                                    Category
                                </div>
                                <div class="form_inline display_inline pr_8">
                                    <div class="select">
                                        <select class="form_global{{ $errors->has('d_parent_category') ? ' is-invalid' : '' }}" name="d_parent_category" id="d_parent_category">
                                            <option value="">Select Category</option>
                                            @foreach($defaultCategories as $item)
                                                <option value="{{ $item['id'] }}" data-index="{{ $loop->index }}" {{ old('d_parent_category') == $item['id'] ? 'selected' : '' }}>{{ $item['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form_inline display_inline pr_8">
                                    <div class="select">
                                        <select class="form_global{{ $errors->has('d_second_parent_category') ? ' is-invalid' : '' }}" name="d_second_parent_category" id="d_second_parent_category">
                                            <option value="">Sub Category</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form_inline display_inline">
                                    <div class="select">
                                        <select class="form_global{{ $errors->has('d_third_parent_category') ? ' is-invalid' : '' }}" name="d_third_parent_category" id="d_third_parent_category">
                                            <option value="">Sub Category</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form_row">
                                <div class="label_inline width_150p">
                                    Pack
                                </div>
                                <div class="form_inline">
                                    <label class="col-form-label" id="pack_details">Pack Details</label>
                                </div>
                            </div>

                            <div class="form_row">
                                <div class="label_inline width_150p">
                                    Details
                                </div>
                                <div class="form_inline display_inline pr_8">
                                    <div class="select">
                                        <select class="form_global" name="made_in" id="made_in">
                                            <option value="">Select Made In</option>

                                            @foreach($madeInCountries as $country)
                                                <option value="{{ $country->id }}" {{ old('made_in') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form_inline display_inline">
                                    <div class="select">
                                        <select class="form_global" name="labeled" id="labeled">
                                            <option value="">Select Labeled</option>
                                            <option value="labeled" {{ old('labeled') == 'labeled' ? 'selected' : '' }}>Labeled</option>
                                            <option value="printed" {{ old('labeled') == 'printed' ? 'selected' : '' }}>Printed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form_row">
                                <div class="label_inline width_150p">Memo</div>
                                <div class="form_inline">
                                    <input type="text" id="memo" class="form_global{{ $errors->has('memo') ? ' is-invalid' : '' }}"
                                           placeholder="Internal use only" name="memo" value="{{ old('memo') }}">
                                </div>
                            </div>
                            <div class="form_row">
                                <div class="label_inline width_150p">Fabric</div>
                                <div class="form_inline">
                                    <input type="text" id="memo" class="form_global{{ $errors->has('fabric') ? ' is-invalid' : '' }}"
                                           placeholder="Internal use only" name="fabric" value="{{ old('fabric') }}">
                                </div>
                            </div>

                            <div class="form_row">
                                <div class="label_inline width_150p">
                                    Brand
                                </div>
                                <div class="form_inline">
                                    <select class="form_global{{ $errors->has('brand') ? ' is-invalid' : '' }}" name="brand" id="brand">
                                        <option value="">Select Brand</option>
                                        @foreach($defaultCategories as $item)
                                            <option value="{{ $item['name'] }}" data-index="{{ $loop->index }}" {{ old('brand') == $item['id'] ? 'selected' : '' }}>{{ $item['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ly_accrodion">
            <div class="ly_accrodion_heading">
                <div class="ly_accrodion_title open_acc" data-toggle="accordion" data-target="#Video" class="accordion_heading" data-class="accordion">
                    <span> Video</span>
                </div>
            </div>
            <div class="accordion_body default_accrodion open" id="Video">
                <div class="create_images_area">
                    <br>
                    <div class="ly-row">
                        <div class="ly-6">
                            <input type="file" class="form_global" name="video" id="input-video">
                            @if ($errors->has('video'))
                                <span class="text-danger">{{ $errors->first('video') }}</span>
                            @endif
                        </div>
                        <div class="ly-6">
                            <div class="label_inline width_150p">
                                Default Video
                            </div>
                            <div class="form_inline"> 
                                <input type="checkbox" name="default_video_img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ly_accrodion">
            <div class="ly_accrodion_heading">
                <div class="ly_accrodion_title open_acc" data-toggle="accordion" data-target="#Colors" data-class="accordion">
                    <span>Colors</span>
                </div>
            </div>
            <div class="accordion_body default_accrodion open" id="Colors" style="">
                <div class="create_item_color">
                    <div class="display_inline">
                        <div class="plc_fixed_left_search">
                            <input class="form_global ui-autocomplete-input" type="text" id="color_search" placeholder="Type Color">
                            @if ($errors->has('colors'))
                                <span class="text_danger" style="color: red;">Color(s) is required.</span>
                            @endif
                        </div>
                    </div>
                    <div class="display_inline width_250p d_none">
                        <div class="select">
                            <select class="form_global" id="select_master_color">
                                <option value="">Select Master Color</option>
                                @foreach($masterColors as $mc)
                                    <option value="{{ $mc->id }}">{{ $mc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="display_inline">
                        <a class="ly_btn btn_blue" id="btnAddColor">Add Color</a>
                    </div>
                    <br>
                    <br>
                    <div class="ly-row">
                        <div class="ly-12 create_color_list" style="height:auto; overflow: unset;">
                            <ul class="colors-ul">
                                @if (old('colors') != null)
                                    @foreach($colors as $color)
                                        @if (in_array($color->id, old('colors')))
                                            <li>
                                                <div class="input-group">
                                                    <div class="form-check custom_checkbox">
                                                        <input class="form-check-input" type="checkbox" value="1" checked="" name="color_available_{{ $color->id }}" id="color_available_{{ $color->id }}">
                                                        <label class="form-check-label color-available" name="color_available_{{ $color->id }}" id="color_available_{{ $color->id }}" for="color_available_{{ $color->id }}">
                                                            <span class="name">{{ $color->name }}</span>
                                                        </label>
                                                    </div>
                                                    <a class="color-remove">X</a>
                                                    <input class="templateColor" type="hidden" name="colors[]" value="{{ $color->id }}">
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ly_accrodion">
            <div class="ly_accrodion_heading">
                <div class="ly_accrodion_title open_acc" data-toggle="accordion" data-target="#Inventory" class="accordion_heading" data-class="accordion">
                    <span> Inventory</span>
                </div>
            </div>
            <div class="accordion_body default_accrodion open" id="Inventory">
                <div class="inventory">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Color</th>
                                <th>
                                    <div class="display_inline">
                                        Qty
                                    </div>
                                </th>
                                <th>
                                    <div class="display_inline">
                                        Threshold
                                        <div class="tooltip display_inline mr_0">
                                            <i class="fas fa-info-circle"></i>
                                            <span>The threshold is the quantity at which the item will appear out of stock.</span>
                                        </div>
                                    </div>
                                </th>

                                <th>
                                    <div class="display_t_cell_50 pr_8">
                                        <div class="select">
                                            <select class="form_global availability_inv_glob" name="availability_inv"> 
                                                <option value="1" {{old('availability_inv') == '1' ? 'checked' : ''}}>In Stock</option>
                                                <option value="2" {{old('availability_inv') == '2' ? 'checked' : ''}}>Arrives Soon / Back Order</option>
                                                <option value="3" {{old('availability_inv') == '3' ? 'checked' : ''}}>Out of Stock</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="display_t_cell_50">
                                        <div class="datepicker_wrapper">
                                            <input class="datepicker form_global available_on_inv_glob" autocomplete="off" placeholder="Available On" value="{{old('available_on')}}">
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="itemInv">
                            @php($inventories = old('inv') ?? [])
                            @php($availability = old('availability') ?? [])
                            @php($i=0)
                            @foreach($inventories as $inventory)
                            <tr class="inv_{{$inventory['color_id']}}">
                                <td class="text-left">{{$inventory['color_name']}}
                                    <input type="hidden" name="inv[{{$i}}][id]" value="0">
                                    <input type="hidden" name="inv[{{$i}}][color_id]" value="{{$inventory['color_id']}}">
                                    <input type="hidden" name="inv[{{$i}}][color_name]" value="{{$inventory['color_name']}}">
                                </td>
                                <td class="text-center" style="padding: 0">
                                    <input class="text-center form-control" type="number" name="inv[{{$i}}][qty]" value="{{$inventory['qty']}}" autocomplete="off" placeholder="Qty">
                                </td>
                                <td class="text-center" style="padding: 0">
                                    <input class="text-center form-control" type="number" name="inv[{{$i}}][threshold]" value="{{$inventory['threshold']}}" autocomplete="off" placeholder="Threshold">
                                </td>
                                <td>
                                    <div class="display_t_cell_50 pr_8">
                                        <div class="select">
                                            <select class="form_global availability_inv" name="inv[{{$i}}][availability_inv]"> 
                                                <option value="1" {{$inventory['availability_inv'] == '1' ? 'checked' : ''}}>In Stock</option>
                                                <option value="2" {{$inventory['availability_inv'] == '2' ? 'checked' : ''}}>Arrives Soon / Back Order</option>
                                                <option value="3" {{$inventory['availability_inv'] == '3' ? 'checked' : ''}}>Out of Stock</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="display_t_cell_50">
                                        <div class="datepicker_wrapper">
                                            <input class="text-center form_global available_on_inv" name="inv[{{$i}}][available_on]"  value="{{$inventory['available_on']}}" autocomplete="off" placeholder="Available On">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @php($i++)
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="ly_accrodion">
            <div class="ly_accrodion_heading">
                <div class="ly_accrodion_title open_acc" data-toggle="accordion" data-target="#Images" class="accordion_heading" data-class="accordion">
                    <span> Images</span>
                </div>
                <span class="float_right display_inline pt_5"><span id="uploadedImagesCount">0</span> of 20 images added to this item.</span>
            </div>
            <div class="accordion_body default_accrodion open" id="Images">
                <div class="create_images_area images">
                    <div class="create_images">
                        <div class="create_images_inner">
                            <label class="ly_btn btn_blue_hover" for="upload_image" id="btnUploadImages">Upload Images</label>
                            <input type="file" class="d-none" multiple id="inputImages">
                        </div>
                        <p class="ml_20">Upload up to 20 images and hit the 'SAVE' button. Max. allowed image file size is 1MB per image. File name should not exceed 50 char. in length, otherwise it will be automatically truncated and/or assigned a unique name.</p>
                    </div>
                    <div class="ly-wrap mb_25">
                        <div class="ly-row" id="images">
                            <ul id="image-container" class="block__list block__list_tags width_full ">
                                @if (old('imagesId') != null && sizeof(old('imagesId')) > 0)
                                    @foreach(old('imagesId') as $img)
                                        <li>
                                            <div class="image-item" >
                                                <div class="custom_list_img">
                                                    <img class="img-thumbnail img" style="margin-bottom: 10px"
                                                        src="{{ asset(old('imagesSrc.'.$loop->index)) }}">
                                                </div>
                                                <br>
                                                <select class="form_global image-color" name="imageColor[]">
                                                <option value="">Color [Default]</option>
                                                </select><br>
                                                <a class="btnRemoveImage"><span class="remove"> <i class="fas fa-times"></i> </span></a>

                                                <input class="inputImageId" type="hidden" name="imagesId[]" value="{{ $img }}">
                                                <input class="inputImageSrc" type="hidden" name="imagesSrc[]" value="{{ old('imagesSrc.'.$loop->index) }}">
                                            </div>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="ly-wrap pt_15">
                        <div class="ly-row">
                            <div class="create_images_drag">
                                <div class="images">
                                    <span>Drag & Drop Images from your computer</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text_right m15">
            <div class="display_inline mr_0">
                <button type="submit" class="ly_btn  btn_blue min_width_100p ">Save</button>
            </div>
        </div>
    </form>

    <template id="imageTemplate">
        <li>
            <div class="image-item">
                <div class="custom_list_img">
                    <img class="img-thumbnail img">
                </div>
                <br>
                <select class="form_global image-color" name="imageColor[]">
                    <option value="">Color [Default]</option>
                </select><br>
                <a class="btnRemoveImage"><span class="remove"> <i class="fas fa-times"></i> </span></a>

                <input class="inputImageId" type="hidden" name="imagesId[]">
                <input class="inputImageSrc" type="hidden" name="imagesSrc[]">
            </div>
        </li>
    </template>

    <template id="colorItemTemplate">
        <li>
            <div class="input-group">
                <div class="form-check custom_checkbox">
                    <input class="form-check-input"
                           type="checkbox"
                           value="1" checked>
                    <label class="form-check-label color-available">
                        <span class="name"></span>
                    </label>
                </div>
                <a class="color-remove">X</a>
            </div>
            <input class="templateColor" type="hidden" name="colors[]" value="">
        </li>
    </template>
@stop

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/sortable/js/Sortable.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/ezdz/jquery.ezdz.min.js') }}"></script>
    <script type="text/javascript" src="https://momentjs.com/downloads/moment.js"></script>
    <script>
        $(function () {
            var defaultCategories = <?php echo json_encode($defaultCategories); ?>;
            var packs = <?php echo json_encode($packs->toArray()); ?>;
            var colors = <?php echo json_encode($colors->toArray()); ?>;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);

            $('#form').bind('submit', function () {
                $(this).find(':input').prop('disabled', false);
            });

            // Video
            $('#input-video').ezdz({
                previewImage: false
            });

            // Color select
            var availableColors = [];

            $.each(colors, function (i, color) {
                availableColors.push(color.name);
            });

            $('#color_search').autocomplete({
                source: function (request, response) {
                    var results = $.map(availableColors, function (tag) {
                        if (tag.toUpperCase().indexOf(request.term.toUpperCase()) === 0) {
                            return tag;
                        }
                    });
                    response(results);
                },
                response: function(event, ui) {
                    if (ui.content.length === 0) {
                        $('#select_master_color').val('');
                        $('#select_master_color').closest('.display_inline').removeClass('d_none');
                    } else {
                        $('#select_master_color').closest('.display_inline').addClass('d_none');
                    }
                }
            });

            $('#color_search').keydown(function (e){
                if(e.keyCode == 13){
                    e.preventDefault();
                    addColor();
                }
            });

            $('#color_search').keyup(function (e) {
                if ($('#color_search').val().length == 0)
                    $('#select_master_color').closest('.display_inline').addClass('d_none');
            });

            $('#btnAddColor').click(function () {
                if ($('#select_master_color').closest('.display_inline').hasClass('d_none')) {
                    addColor();
                } else {
                    var id = $('#select_master_color').val();
                    var name = $('#color_search').val();

                    if (id == '')
                        return alert('Select Master Color.');

                    if (name == '')
                        return alert('Enter color name.');

                    $.ajax({
                        method: "POST",
                        url: "{{ route('admin_item_add_color') }}",
                        data: { id: id, name: name }
                    }).done(function( data ) {
                        if (data.success) {
                            availableColors.push(data.color.name);
                            colors.push(data.color);

                            $('#select_master_color').closest('.display_inline').addClass('d_none');
                            $('#color_search').val(data.color.name);
                            addColor();
                        } else {
                            alert(data.message);
                        }
                    });
                }
            });

            function addColor() {
                var text = $('#color_search').val();

                if (text != '') {
                    var color = '';

                    $.each(colors, function (i, c) {
                        if (c.name == text)
                            color = c;
                    });

                    if (color != '') {
                        var found = false;
                        $( "input[name*='colors']" ).each(function () {
                            if ($(this).val() == color.id)
                                found = true;
                        });

                        if (!found) {
                            var html = $('#colorItemTemplate').html();
                            row = $(html);

                            row.find('.name').html(color.name);
                            row.find('.templateColor').val(color.id);
                            row.find('.color-available').attr('name', 'color_available_'+color.id);
                            row.find('.color-available').attr('id', 'color_available_'+color.id);
                            row.find('.color-available').attr('for', 'color_available_'+color.id);
                            row.find('.custom-checkbox').attr('for', 'color_available_'+color.id);
                            row.find('.form-check-input').attr('name', 'color_available_'+color.id);
                            row.find('.form-check-input').attr('id', 'color_available_'+color.id);
                            var availability_inv_glob = $('.availability_inv_glob').val();
                            var available_on_inv_glob = $('.available_on_inv_glob').val();
                            $('.colors-ul').append(row);
                            var invCount = $('#itemInv').find('tr').length;
                            var inv = `<tr class="inv_`+color.id+`">
                                        <td class="text-left">`+color.name+`
                                            <input type="hidden" name="inv[`+invCount+`][id]" value="0">
                                            <input type="hidden" name="inv[`+invCount+`][color_id]" value="`+color.id+`">
                                            <input type="hidden" name="inv[`+invCount+`][color_name]" value="`+color.name+`">
                                        </td>
                                        <td class="text-center" style="padding: 0">
                                            <input class="text-center form_global" type="number" name="inv[`+invCount+`][qty]" value="999" autocomplete="off" placeholder="Qty" style="height: 45px;border: none;">
                                        </td>
                                        <td class="text-center" style="padding: 0">
                                            <input class="text-center form_global" type="number" name="inv[`+invCount+`][threshold]" value="0" autocomplete="off" placeholder="Threshold" style="height: 45px;border: none;">
                                        </td>
                                        <td>
                                            <div class="display_t_cell_50 pr_8">
                                                <div class="select">
                                                    <select class="form_global availability_inv" name="inv[`+invCount+`][availability_inv]"> 
                                                        <option value="1">In Stock</option>
                                                        <option value="2">Arrives Soon / Back Order</option>
                                                        <option value="3">Out of Stock</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="display_t_cell_50">
                                                <div class="datepicker_wrapper">
                                                    <input class="text-center form_global available_on_inv" name="inv[`+invCount+`][available_on]"  value="" autocomplete="off" placeholder="Available On" disabled>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>`;
                            $('#itemInv').append(inv);
                            setTimeout(function(){
                                $('.availability_inv').val(availability_inv_glob);
                                $('.available_on_inv').val(available_on_inv_glob);
                                $('.available_on_inv').datepicker({
                                    autoclose: true,
                                    format: 'yyyy-mm-dd',
                                    orientation: "bottom left"
                                });
                            }, 500);

                            updateImageColors();
                        }
                        $('#color_search').val('');
                    } else {
                        $('#select_master_color').closest('.display_inline').removeClass('d_none');
                    }
                }
            }

            $(document).on('click', '.color-remove', function () {
                var target = $(this).closest('li');
                var color_id = target.find('.templateColor').val();
                $("#itemInv").find("tr.inv_"+color_id).remove();
                $(this).closest('li').remove();
            });

            $('.available_on_inv_glob').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                orientation: "bottom left"
            });

            $('body').on('change','.availability_inv_glob', function(){
                var availability_inv = $(this).val();
                $(this).closest('table').find('.availability_inv').val(availability_inv);
                if(availability_inv === ''){
                    $(this).closest('tr').find('.available_on_inv_glob').val('');
                    $(this).closest('table').find('.available_on_inv').val('');
                } else if(availability_inv === '1' || availability_inv === '3'){
                    $(this).closest('tr').find('.available_on_inv_glob').val('');
                    $(this).closest('tr').find('.available_on_inv_glob').attr('disabled', true);
                    $(this).closest('table').find('.available_on_inv').val('');
                    $(this).closest('table').find('.available_on_inv').attr('disabled', true);
                } else if(availability_inv === '2'){
                    $(this).closest('tr').find('.available_on_inv_glob').val('');
                    $(this).closest('tr').find('.available_on_inv_glob').removeAttr('disabled');
                    $(this).closest('table').find('.available_on_inv').val('');
                    $(this).closest('table').find('.available_on_inv').removeAttr('disabled');
                }
                 
            });

            $('body').on('change','.availability_inv', function(){
                $('.availability_inv_glob').val('');
                $('.available_on_inv_glob').val('');
                var availability_inv = $(this).val();
                if(availability_inv === ''){
                    $(this).closest('tr').find('.available_on_inv').val('');
                } else if(availability_inv === '1' || availability_inv === '3'){
                    $(this).closest('tr').find('.available_on_inv').val('');
                    $(this).closest('tr').find('.available_on_inv').attr('disabled', true);
                } else if(availability_inv === '2'){
                    $(this).closest('tr').find('.available_on_inv').val('');
                    $(this).closest('tr').find('.available_on_inv').removeAttr('disabled');
                }
            });

            $('body').on('change','.available_on_inv', function(){
                $('.availability_inv_glob').val('');
                $('.available_on_inv_glob').val('');
                var setDate = $(this).val();
            });

            $('body').on('change','.available_on_inv_glob', function(){
                var setDate = $(this).val();
                $('.available_on_inv').val(setDate);
            });

            function updateImageColors() {
                var ids = [];

                $( "input[name*='colors']" ).each(function () {
                    ids.push($(this).val());
                });

                $('.image-color').each(function () {
                    var selected = $(this).val();

                    $(this).html('<option value="">Color [Default]</option>');
                    $this = $(this);

                    $.each(ids, function (index, id) {
                        var color = colors.filter(function( obj ) {
                            return obj.id == id;
                        });
                        color = color[0];

                        if (color.id == selected)
                            $this.append('<option value="'+color.id+'" selected>'+color.name+'</option>');
                        else
                            $this.append('<option value="'+color.id+'">'+color.name+'</option>');
                    });
                });
            }
            updateImageColors();

            // Images
            var el = document.getElementById('image-container');
            Sortable.create(el, {
                group: "words",
                animation: 150,
                onEnd: function (evt) {
                    var old = old_colors[evt.oldIndex];
                    old_colors[evt.oldIndex] = old_colors[evt.newIndex];
                    old_colors[evt.newIndex] = old;
                },
            });

            $('.create_images_drag').on({
                'dragover dragenter': function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                },
                'drop': function(e) {
                    var dataTransfer =  e.originalEvent.dataTransfer;
                    if( dataTransfer && dataTransfer.files.length) {
                        e.preventDefault();
                        e.stopPropagation();
                        $.each( dataTransfer.files, function(i, file) {
                            if (file.size > 3072000) {
                                alert('Max allowed image size is 3MB per image.')
                            } else if (file.type != 'image/jpeg' && file.type != 'image/png') {
                                alert('Only jpg and png file allowed.');
                            } else if ($(".image-container").length > 2) {
                                alert('Maximum 20 photos allows');
                            } else {
                                var xmlHttpRequest = new XMLHttpRequest();
                                xmlHttpRequest.open("POST", '{{ route('admin_item_upload_image') }}', true);
                                var formData = new FormData();
                                formData.append("file", file);
                                xmlHttpRequest.send(formData);

                                xmlHttpRequest.onreadystatechange = function() {
                                    if (xmlHttpRequest.readyState == XMLHttpRequest.DONE) {
                                        var response = JSON.parse(xmlHttpRequest.responseText);

                                        if (response.success) {
                                            var html = $('#imageTemplate').html();
                                            var item = $(html);
                                            item.find('.img').attr('src', response.data.fullPath);
                                            item.find('.inputImageId').val(response.data.id);
                                            item.find('.inputImageSrc').val(response.data.image_path);

                                            $('#image-container').append(item);
                                            $('#uploadedImagesCount').html($('.inputImageId').length);
                                            updateImageColors();
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
            });

            $('body').on('click', '.btnRemoveImage', function () {
                $(this).closest('li').remove();
                $('#uploadedImagesCount').html($('.inputImageId').length);
                $('#btnUploadImages').prop('disabled', false);
            });

            var d_parent_index;
            var d_second_id = '{{ old('d_second_parent_category') }}';
            var d_third_id = '{{ old('d_third_parent_category') }}';

            $('#d_parent_category').change(function () {
                $('#d_second_parent_category').html('<option value="">Sub Category</option>');
                $('#d_third_parent_category').html('<option value="">Sub Category</option>');
                var parent_id = $(this).val();

                if ($(this).val() != '') {
                    var index = $(this).find(':selected').data('index');
                    d_parent_index = index;

                    var childrens = defaultCategories[index].subCategories;

                    $.each(childrens, function (index, value) {
                        if (value.id == d_second_id)
                            $('#d_second_parent_category').append('<option data-index="' + index + '" value="' + value.id + '" selected>' + value.name + '</option>');
                        else
                            $('#d_second_parent_category').append('<option data-index="' + index + '" value="' + value.id + '">' + value.name + '</option>');
                    });
                }

                $('#d_second_parent_category').trigger('change');
            });

            $('#d_parent_category').trigger('change');

            $('#d_second_parent_category').change(function () {
                $('#d_third_parent_category').html('<option value="">Sub Category</option>');

                if ($(this).val() != '') {
                    var index = $(this).find(':selected').attr('data-index');

                    var childrens = defaultCategories[d_parent_index].subCategories[index].subCategories;

                    $.each(childrens, function (index, value) {
                        if (value.id == d_third_id)
                            $('#d_third_parent_category').append('<option data-index="' + index + '" value="' + value.id + '" selected>' + value.name + '</option>');
                        else
                            $('#d_third_parent_category').append('<option data-index="' + index + '" value="' + value.id + '">' + value.name + '</option>');
                    });
                }

                var id = $(this).val();

                // Size
                $('#size').change(function () {
                    var index = $(this).find(':selected').data('index');

                    if (typeof index !== "undefined") {
                        var pack = packs[index];
                        var packDetails = pack.pack1;

                        if (pack.pack2 != null)
                            packDetails += '-' + pack.pack2;

                        if (pack.pack3 != null)
                            packDetails += '-' + pack.pack3;

                        if (pack.pack4 != null)
                            packDetails += '-' + pack.pack4;

                        if (pack.pack5 != null)
                            packDetails += '-' + pack.pack5;

                        if (pack.pack6 != null)
                            packDetails += '-' + pack.pack6;

                        if (pack.pack7 != null)
                            packDetails += '-' + pack.pack7;

                        if (pack.pack8 != null)
                            packDetails += '-' + pack.pack8;

                        if (pack.pack9 != null)
                            packDetails += '-' + pack.pack9;

                        if (pack.pack10 != null)
                            packDetails += '-' + pack.pack10;

                        $('#pack_details').html(packDetails);
                    } else {
                        $('#pack_details').html('Pack Details');
                    }
                });
            });

            $('#size').trigger('change');
            $('#d_second_parent_category').trigger('change');

            // Upload images button
            $('#btnUploadImages').click(function (e) {
                e.preventDefault();

                $('#inputImages').click();
            });

            $('#inputImages').change(function (e) {
                $.each(e.target.files, function (index, file) {
                    // if (file.size > 614400) {
                    if (file.size > 2097152) {
                        alert('Max allowed image size is 2MB per image.')
                    } else if (file.type != 'image/jpeg' && file.type != 'image/png' && file.type != 'image/gif') {
                        alert('Only jpg and png file allowed.');
                    } else if ($(".image-container").length > 2) {
                        alert('Maximum 20 photos allows');
                    } else {
                        var xmlHttpRequest = new XMLHttpRequest();
                        xmlHttpRequest.open("POST", '{{ route('admin_item_upload_image') }}', true);
                        var formData = new FormData();
                        formData.append("file", file);
                        xmlHttpRequest.send(formData);

                        xmlHttpRequest.onreadystatechange = function() {
                            if (xmlHttpRequest.readyState == XMLHttpRequest.DONE) {
                                var response = JSON.parse(xmlHttpRequest.responseText);

                                if (response.success) {
                                    // if ($(".image-container").length == 0)
                                    //     $('#images').html('');

                                    var html = $('#imageTemplate').html();
                                    var item = $(html);
                                    item.find('.img').attr('src', response.data.fullPath);
                                    item.find('.inputImageId').val(response.data.id);
                                    item.find('.inputImageSrc').val(response.data.image_path);

                                    $('#image-container').append(item);
                                    updateImageColors();
                                }
                            }
                        }
                    }
                });

                $(this).val('');
            });

            $('#btnSave').click(function (e) {
                e.preventDefault();

                $('#form').submit();
                /*var style_no = $('#style_no').val();

                $.ajax({
                    method: "POST",
                    url: "#",
                    data: { style_no: style_no }
                }).done(function( data ) {
                    if (data.success)
                        $('#form').submit();
                    else
                        alert(data.message);
                });*/
            });

            window.addEventListener("dragover",function(e){
                e = e || event;
                e.preventDefault();
            },false);
            window.addEventListener("drop",function(e){
                e = e || event;
                e.preventDefault();
            },false);
        });
    </script>
@stop
