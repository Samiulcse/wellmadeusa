@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <style type="text/css">

        .d-none {
            display: none !important;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

    </style>
@stop

@section('content')
    <div class="ly_page_wrapper{{ ($errors && sizeof($errors) > 0) ? '' : ' d-none' }}" id="addEditRow">
        <form class="form-horizontal" enctype="multipart/form-data" id="form"
                method="post" action="{{ (old('inputAdd') == '1') ? route('admin_master_color_add') : route('admin_master_color_update') }}">
            @csrf
            <input type="hidden" name="inputAdd" id="inputAdd" value="{{ old('inputAdd') }}">
            <input type="hidden" name="colorId" id="colorId" value="{{ old('colorId') }}">
            <div class="add_new_cat_color">
                <h3 class="font_16p mb_15 item_change_title" id="addEditTitle">{{ old('inputAdd') == '0' ? 'Edit Color' : 'Add Color' }}</h3>

                <div class="form_row">
                    <div class="label_inline required width_150p">
                            Color Name
                    </div>
                    <div class="form_inline display_inline">
                        <input type="text" id="color_name" class="form_global{{ $errors->has('color_name') ? ' is-invalid' : '' }}"
                        placeholder="Color Name" name="color_name" value="{{ old('color_name') }}">
                    </div>
                </div>
                <div class="form_row{{ $errors->has('photo') ? ' has-danger' : '' }}">
                    <div class="label_inline required width_150p align_middle fw_500">
                        Upload Color Image
                    </div>
                    <div class="form_inline align_middle">
                        <input type="file" class="form-control{{ $errors->has('photo') ? ' is-invalid' : '' }}" name="photo" accept="image/*">
                        <span class="font_italic color_grey_type2 ml_20 font_12p"> Required Size: 20x20px .jpg, .gif or .png Accepted.</span>
                    </div>
                    <div class="form_inline">
                        <div class="text_right">
                            <div class="display_inline">
                                <button class="ly_btn btn_grey min_width_100p close_item_color" id="btnCancel">Cancel</button>
                            </div>
                            <div class="display_inline mr_0">
                                <button type="submit" class="ly_btn btn_blue min_width_100p" id="btnSubmit">{{ old('inputAdd') == '0' ? 'Update' : 'Add' }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="item_color_heading m15">
        <div class="ly-wrap">
            <div class="ly-row">
                <div class="ly-6 pl_0">
                    <div class="item_color_heading_left">
                        <span class="{{ ($errors && sizeof($errors) > 0) ? 'd-none' : '' }}" id="addBtnRow">
                            <span class="link mr_20 item_color_btn" id="btnAddNew">+ Add New Color</span>
                        </span>
                        <span class="font_italic color_grey_type2">You currently have {{ $masterColors->total() }} colors.</span>
                    </div>
                </div>
                <div class="ly-6 pr_0">
                    <div class="item_color_heading_right text_right">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="item_color_search p15">
        <div class="ly-wrap">
            <div class="ly-row">
                <form action="{{ route('admin_master_color') }}">
                    <div class="ly-12 pl_0">
                        <div class="display_inline width_350p">
                            <input type="text" class="form_global" placeholder="Search" name="s" value="{{ request()->get('s') }}">
                        </div>
                        <div class="display_inline">
                            <button type="submit" class="ly_btn btn_blue width_100p toggle_item_search">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="item_color_option">
        <div class="item_color_option_inner">
            <ul>
                @foreach($masterColors as $color)
                <li>
                    <div class="item_color_list">
                        <img src="{{ ($color->image_path) ? asset($color->image_path) : asset('images/no-image.png') }}" height="50px" width="50px">
                        <div class="item_color_text">
                            <p>{{ $color->name }}</p>
                            <div class="text-left">
                                <a class="btnEdit" data-id="{{ $color->id }}" data-index="{{ $loop->index }}" role="button"><span class="color_blue item_setting_edit">Edit</span></a> |
                        <a class="btnDelete" data-id="{{ $color->id }}" role="button"><span class="color_red item_color_delete">Delete</span></a>
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="pagination_wrapper p10 item_color_pagination_color">
        <ul class="pagination">
            <li><button class="ly_btn p1_first{{ $masterColors->currentPage() > 1 ?  ' btn_paginate' : ''}}"{{ $masterColors->currentPage() == 1 ?  ' disabled' : ''}}>| <i class="fas fa-chevron-left"></i></button></li>
            <li>
                <button class="ly_btn p1_prev{{ $masterColors->currentPage() > 1 ?  ' btn_paginate' : ''}}"{{ $masterColors->currentPage() == 1 ?  ' disabled' : ''}}> <i class="fas fa-chevron-left"></i> PREV</button>
            </li>
            <li>
                <div class="pagination_input">
                    <input type="number" min="1" max="{{ $masterColors->lastPage() }}" class="form_global p1" value="{{ $masterColors->currentPage() }}"> of {{ $masterColors->lastPage() }}
                </div>
                <div class="pagination_btn">
                    <button class="ly_btn switch_page">GO</button>
                </div>
            </li>
            <li><button class="ly_btn p1_next{{ $masterColors->currentPage() < $masterColors->lastPage() ?  ' btn_paginate' : ''}}"{{ $masterColors->currentPage() == $masterColors->lastPage() ?  ' disabled' : ''}}>  NEXT <i class="fas fa-chevron-right"></i></button></li>
            <li>
                <button class="ly_btn p1_last{{ $masterColors->currentPage() < $masterColors->lastPage() ?  ' btn_paginate' : ''}}"{{ $masterColors->currentPage() == $masterColors->lastPage() ?  ' disabled' : ''}}> <i class="fas fa-chevron-right"></i> |</button>
            </li>
        </ul>
    </div>

    <div id="deleteModal" class="modal" data-modal="deleteModal">
        <div class="modal_overlay" data-modal-close="deleteModal"></div>
        <div class="modal_inner">
            <div class="modal_wrapper modal_380p">
                <div class="item_list_popup">
                    <div class="modal_header display_table">
                        <span class="modal_header_title">Delete Confirmation</span>
                    </div>
                    <div class="modal_content pa15">
                        <p class="fw_500 ">Are you sure that you want to delete?</p>
                        <div class="form_row mb_0 pt_15">
                            <div class="form_inline">
                                <div class="text_right">
                                    <div class="display_inline mr_0">
                                        <button data-modal-close="deleteModal" class="ly_btn btn_grey min_width_100p close_item_color">Close</button>
                                    </div>
                                    <div class="display_inline mr_0">
                                        <button class="ly_btn btn_blue min_width_100p" id="modalBtnDelete">Yes</button>
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

            var pageCount = <?php echo $masterColors->lastPage(); ?>;

            $('.p1').keyup(function() {

                if($(this).val() > pageCount) {

                    $('.p1').val(pageCount);

                } else {

                    $('.p1').val($(this).val());

                }
                
            });

            $('.switch_page').click(function() {

                var p1 = $('.p1').val();
                var currentLocation = String(window.location);
                
                var switchPageUrl = currentLocation.split('?')[0] + '?page=' + p1;

                if((currentLocation.split('?')[1])) {

                    if((currentLocation.split('?')[1]).search('page=') >= 0) {
                        
                    } else {

                        switchPageUrl = currentLocation.split('?')[0] + '?' + currentLocation.split('?')[1] + '&page=' + p1;

                    }

                    if((currentLocation.split('?')[1]).search('&page=') > 0) {

                        switchPageUrl = currentLocation.split('?')[0] + '?' + (currentLocation.split('?')[1]).split('&page=')[0] + '&page=' + p1;

                    }
                }

                window.location = switchPageUrl;

            });

            $('.p1_first').click(function() {

                var p1 = 1;
                var currentLocation = String(window.location);
                
                var switchPageUrl = currentLocation.split('?')[0] + '?page=' + p1;

                if((currentLocation.split('?')[1])) {

                    if((currentLocation.split('?')[1]).search('page=') >= 0) {
                        
                    } else {

                        switchPageUrl = currentLocation.split('?')[0] + '?' + currentLocation.split('?')[1] + '&page=' + p1;

                    }

                    if((currentLocation.split('?')[1]).search('&page=') > 0) {

                        switchPageUrl = currentLocation.split('?')[0] + '?' + (currentLocation.split('?')[1]).split('&page=')[0] + '&page=' + p1;

                    }
                    

                }
                
                window.location = switchPageUrl;

            });

            $('.p1_prev').click(function() {

                var p1 = <?php echo $masterColors->currentPage(); ?> - 1;
                var currentLocation = String(window.location);
                
                var switchPageUrl = currentLocation.split('?')[0] + '?page=' + p1;

                if((currentLocation.split('?')[1])) {

                    if((currentLocation.split('?')[1]).search('page=') >= 0) {
                        
                    } else {

                        switchPageUrl = currentLocation.split('?')[0] + '?' + currentLocation.split('?')[1] + '&page=' + p1;

                    }

                    if((currentLocation.split('?')[1]).search('&page=') > 0) {

                        switchPageUrl = currentLocation.split('?')[0] + '?' + (currentLocation.split('?')[1]).split('&page=')[0] + '&page=' + p1;

                    }
                }
                
                window.location = switchPageUrl;

            });

            $('.p1_next').click(function() {

                var p1 = <?php echo $masterColors->currentPage(); ?> + 1;
                var currentLocation = String(window.location);
                
                var switchPageUrl = currentLocation.split('?')[0] + '?page=' + p1;

                if((currentLocation.split('?')[1])) {

                    if((currentLocation.split('?')[1]).search('page=') >= 0) {
                        
                    } else {

                        switchPageUrl = currentLocation.split('?')[0] + '?' + currentLocation.split('?')[1] + '&page=' + p1;

                    }

                    if((currentLocation.split('?')[1]).search('&page=') > 0) {

                        switchPageUrl = currentLocation.split('?')[0] + '?' + (currentLocation.split('?')[1]).split('&page=')[0] + '&page=' + p1;

                    }
                }
                
                window.location = switchPageUrl;

            });

            $('.p1_last').click(function() {

                var p1 = <?php echo $masterColors->lastPage(); ?>;
                var currentLocation = String(window.location);
                
                var switchPageUrl = currentLocation.split('?')[0] + '?page=' + p1;

                if((currentLocation.split('?')[1])) {

                    if((currentLocation.split('?')[1]).search('page=') >= 0) {
                        
                    } else {

                        switchPageUrl = currentLocation.split('?')[0] + '?' + currentLocation.split('?')[1] + '&page=' + p1;

                    }

                    if((currentLocation.split('?')[1]).search('&page=') > 0) {

                        switchPageUrl = currentLocation.split('?')[0] + '?' + (currentLocation.split('?')[1]).split('&page=')[0] + '&page=' + p1;

                    }
                }
                
                window.location = switchPageUrl;

            });

            var colors = <?php echo json_encode($masterColors->toArray()); ?>;
            colors = colors.data;
            var selectedId;
            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);

            $('#btnAddNew').click(function () {
                $('#addEditRow').removeClass('d-none');
                $('#addBtnRow').addClass('d-none');
                $('#addEditTitle').html('Add Color');
                $('#btnSubmit').html('Add');
                $('#inputAdd').val('1');
                $('#form').attr('action', '{{ route('admin_master_color_add') }}');
            });

            $('#btnCancel').click(function (e) {
                e.preventDefault();

                $('#addEditRow').addClass('d-none');
                $('#addBtnRow').removeClass('d-none');

                // Clear form
                $('#color_name').val('');
                $('input').removeClass('is-invalid');
            });

            $('.btnEdit').click(function () {
                var id = $(this).data('id');
                var index = $(this).data('index');

                $('#addEditRow').removeClass('d-none');
                $('#addBtnRow').addClass('d-none');
                $('#addEditTitle').html('Edit Color');
                $('#btnSubmit').html('Update');
                $('#inputAdd').val('0');
                $('#form').attr('action', '{{ route('admin_master_color_update') }}');
                $('#colorId').val(id); 
                var color = colors[index]; 
                $('#color_name').val(color.name);  
            });

            $('.btnDelete').click(function () {
                $('#deleteModal').addClass('open_modal');
                selectedId = $(this).data('id');
            });

            $('#modalBtnDelete').click(function () {
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_master_color_delete') }}",
                    data: { id: selectedId }
                }).done(function( msg ) {
                    location.reload();
                });
            });
        })
    </script>
@stop