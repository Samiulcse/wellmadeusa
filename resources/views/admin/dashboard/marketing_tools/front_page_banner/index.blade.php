@extends('admin.layouts.main')

<?php
    use App\Enumeration\VendorImageType;
?>

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <style type="text/css">
        .msg_txt
        {
            text-align: right;
            color: red;
            margin-bottom: 0px;
        }
        a{
            cursor: pointer;
        }
        .modal-items li {
            width: 100px;
            margin-right: 10px;
            float: left;
        }
        .modal-items li img{
            width: 100%;
        }
        .form-control-feedback{
            color:red;
        }
    </style>
@stop

@section('content')
    <div class="ly_accrodion">
        <div class="ly_accrodion_heading">
            <div class="ly_accrodion_title {{ count($errors) > 0 ? ' open_acc' : ''}}" data-toggle="accordion" data-target="#addNewImage" data-class="accordion">
                <span>Add New Image</span>
            </div>
        </div>
        <div class="accordion_body default_accrodion  {{ count($errors) > 0 ? ' open' : ''}}" id="addNewImage" style="">
            <form class="form-horizontal" enctype="multipart/form-data" method="post" id="addNewImageForm" action="{{ route('admin_front_page_banner_item_add') }}">
                @csrf
                <div class="form_row">
                    <div class="label_inline required width_150p">
                        <label for="code">Upload Image</label>
                    </div>
                    <div class="form_inline">
                        <input class="form_global{{ $errors->has('photo') ? ' is-invalid' : '' }}"
                            type="file" id="photo" name="photo" accept="image/*">
                    </div>
                </div>

                <div class="form_row">
                    <div class="label_inline required width_150p">
                        <label for="link" class="col-form-label">Link</label>
                    </div>
                    <div class="form_inline">
                        <input class="form_global{{ $errors->has('link') ? ' is-invalid' : '' }}"
                            type="text" id="link" name="link" value="{{ old('link') }}">
                    </div>
                </div>

                <div class="form_row">
                    <div class="label_inline required width_150p">
                        <label for="head" class="col-form-label">Description</label>
                    </div>
                    <div class="form_inline">
                        <input class="form_global{{ $errors->has('head') ? ' is-invalid' : '' }}"
                            type="text" id="head" name="head" value="{{ old('head') }}">
                    </div>
                </div>
                <input type="hidden" id="lgdevice" name="type" value="{{ VendorImageType::$FRONT_PAGE_BANNER }}" required="">
                <!-- <div class="form_row">
                    <div class="label_inline required width_150p">
                        Show on device:
                    </div>
                    <div class="form_inline">
                        <div class="custom_radio">
                            <input type="radio" id="lgdevice" name="type" value="{{ VendorImageType::$FRONT_PAGE_BANNER }}" required="">
                            <label for="lgdevice"> For Large Device</label>
                        </div>
                        <div class="custom_radio">
                            <input type="radio" id="smdevice" name="type" value="{{ VendorImageType::$MOBILE_MAIN_BANNER }}"  required="">
                            <label for="smdevice"> For Small Device</label>
                        </div>
                        <br/>
                        @if ($errors->has('type'))
                            <div class="form-control-feedback">{{ $errors->first('type') }}</div>
                        @endif
                    </div>
                </div> -->
            </form>
            <div class="create_item_color">
                <div class="float_right">
                    <div class="display_inline">
                        <span data-toggle="accordion" data-target="#addNewImage" data-class="accordion" class="accordion_heading" data-class="accordion"><span class="ly_btn btn_danger width_80p " style="text-align:center">Close</span> </span>
                    </div>
                </div>
                <div class="float_right">
                    <div class="display_inline">
                        <a href="javascript:void(0)" onclick="document.getElementById('addNewImageForm').submit();"><span class="ly_btn  btn_blue ">Add new image</span> </a>
                    </div>
                </div>
            </div>
            <br>
        </div>
    </div>

    <br>

    <p> Banner Item For Large Device</p>
    <div id="mainBanner">
        @if(!empty($images))
            @foreach($images as $image)
            <div data-id="{{ $image->id }}">
                <div class="ly_page_wrapper oneft_banner"  >
                    <div class="ly-wrap-fluid">
                        <div class="ly-row">
                            <div class="ly-2">
                                <div class="banner_img">
                                    @if(strpos($image->image_path, '.mp4') !== false || strpos($image->image_path, '.m4v') !== false)
                                        <video id='video' loop muted preload="metadata" width="100%" height="100%" autoplay="" class="embed-responsive-item" autoplay playsinline>
                                            <source id='mp4' src="{{ asset($image->image_path) }}" type='video/mp4'/>
                                        </video>
                                    @else
                                        <img src="{{ asset($image->image_path) }}" alt="" class="width_full">
                                    @endif
                                    <div class="banner_edit">
                                        <span class="color_blue btnEdit" data-head="{{ $image->head }}" data-id="{{ $image->id }}">Edit</span> |
                                        <span class="color_red btnRemove" data-type="1" data-id="{{ $image->id }}">Remove</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
            </div>
            @endforeach
        @endif

    </div>
    <br>

 

    <br>
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
    <div class="modal" data-modal="deleteItemModal">
        <div class="modal_overlay" data-modal-close="deleteItemModal"></div>
        <div class="modal_inner">
            <div class="modal_wrapper modal_470p">
                <div class="item_list_popup">
                    <div class="modal_header display_table">
                        <span class="modal_header_title">Are you sure want to delete?</span>
                        <div class="float_right">
                            <span class="close_modal" data-modal-close="deleteItemModal"></span>
                        </div>
                    </div>
                    <div class="modal_content">
                        <div class="ly-wrap-fluid">
                            <div class="ly-row">
                                <div class="ly-12">
                                    <div class="display_table m15">
                                        <div class="float_right">
                                            <button class="ly_btn btn_blue width_150p " data-modal-close="deleteItemModal">Close</button>
                                            <button class="ly_btn btn_danger width_150p" id="modalBtnItemDelete">Delete</button>
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

    <div class="modal" data-modal="editModal">
        <div class="modal_overlay" data-modal-close="editModal"></div>
        <div class="modal_inner">
            <div class="modal_wrapper modal_470p">
                <div class="item_list_popup">
                    <div class="modal_header display_table">
                        <span class="modal_header_title">Edit Link URL</span>
                        <div class="float_right">
                            <span class="close_modal" data-modal-close="editModal"></span>
                        </div>
                    </div>
                    <div class="modal_content">
                        <div class="ly-wrap-fluid">

                            <div id="htmlMsg"></div>
                            <div class="form_row">
                                <div class="label_inline required width_150p">
                                    <label for="link" class="col-form-label">Link</label>
                                </div>
                                <div class="form_inline">
                                    <input class="form_global" type="text" id="modal_url" required="">
                                </div>
                            </div>

                            <div class="form_row">
                                <div class="label_inline required width_150p">
                                    <label for="head" class="col-form-label">Description</label>
                                </div>
                                <div class="form_inline">
                                    <input type="text" class="form_global" id="modal_head">
                                </div>
                            </div>
                            <div class="ly-row">
                                <div class="ly-12">
                                    <div class="display_table m15">
                                        <div class="float_right">
                                            <button class="ly_btn btn_danger width_150p " data-modal-close="editModal">Close</button>
                                            <button class="ly_btn btn_blue width_150p" id="modalBtnEdit">Save</button>
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
    <script type="text/javascript" src="{{ asset('plugins/sortable/js/Sortable.min.js') }}"></script>
    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var selectedId;
            var type = '';
            var bannerId = '';
            var message = '{{ session('message') }}';
            var images = <?php echo json_encode($images); ?>;
            var images_mob ='';

            if (message != '')
                toastr.success(message);

            var el = document.getElementById('mainBanner');
            Sortable.create(el, {
                animation: 150,
                dataIdAttr: 'data-id',
                onEnd: function () {
                    updateSort(this.toArray());
                },
            });
            

            function updateSort(ids) {
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_main_slider_items_sort') }}",
                    data: { ids: ids }
                }).done(function( msg ) {
                    toastr.success('Items sort updated!');
                });
            }

            $('#btnAddNew').click(function () {
                $('#addEditRow').removeClass('d-none');
                $('#addBtnRow').addClass('d-none');
            });

            $('#btnCancel').click(function (e) {
                e.preventDefault();

                $('#addEditRow').addClass('d-none');
                $('#addBtnRow').removeClass('d-none');

                // Clear form
                $('input').removeClass('is-invalid');
                $('.form-group').removeClass('has-danger');
            });

            $('.btnRemove').click(function () {
                
                var targeted_modal_class = 'deleteModal';
                $('[data-modal="' + targeted_modal_class + '"]').addClass('open_modal');
                selectedId = $(this).data('id');
            });

            $('#modalBtnDelete').click(function () {
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_main_slider_item_delete') }}",
                    data: { id: selectedId }
                }).done(function( msg ) {
                    location.reload();
                });
            });

            // Edit
            $('.btnEdit').click(function () {
                 
                var type = 0;
                var id = parseInt($(this).data('id'));
                var head = $(this).data('head');
                    type = $(this).data('type');
                selectedId = id;

                if(type == 2){
                    $.each(images_mob, function (i, img) {
                        if (img.id == id)
                            image = img;
                    });
                }else{
                    $.each(images, function (i, img) {
                        if (img.id == id)
                            image = img;
                    });
                }
                $('#modal_url').val(image.url);
                $('#modal_head').val(head);
                var targeted_modal_class = 'editModal';
                $('[data-modal="' + targeted_modal_class + '"]').addClass('open_modal');
            });

            $('#modalBtnEdit').click(function () {
                var url = $('#modal_url').val();
                var head = $('#modal_head').val();
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_banner_edit_post') }}",
                    data: { id: selectedId, url: url, head: head }
                }).done(function( msg ) {
                    location.reload();
                });
            });

            $('.btnMainBannerRemove').click(function () {
                var targeted_modal_class = 'deleteItemModal';
                $('[data-modal="' + targeted_modal_class + '"]').addClass('open_modal');
                type = $(this).data('type');
                id = $(this).data('id');
            });
        })
    </script>
@stop
