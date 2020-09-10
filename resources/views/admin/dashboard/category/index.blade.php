@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/Nestable/style.css') }}" rel="stylesheet">
    <style type="text/css">
        .d-none {
            display: none !important;
        }
        .editdelete{
            text-align: right;
        }
    </style>
@stop

@section('content')
    <div class="item_category display_flex">
        <div class="item_category_inner">
            <div class="add_item_cat link m15 ">
                <span class="add_new_cat_open" id="btnAddNew">+ Add a New Category</span> <span class="float_right">Click Category From List To Edit.</span>
            </div>
            <div class="ly_page_wrapper">
                <h4 class="item_cat_heading mb_20">Category List <span class="float_right">Drag & Drop To Sort.</span></h4>
                <div class="dd" id="nestable">
                    <ol class="dd-list item_shortable_parent">
                        @foreach($categories as $category)
                            <li class="dd-item" data-id="{{ $category['id'] }}">
                                <div class="dd-handle dd3-handle">{{ $category['name'] }}
                                    
                                </div>
                                <div class="editdelete">
                                    <span>
                                        <a class="btnEdit color_blue" data-id="{{ $category['id'] }}" data-index="{{ $loop->index }}" data-image="{{ isset($category['image']) ? $category['image']: 'not_found' }}">Edit</a> |
                                        <span>&nbsp;<a  class="btnDelete color_red" data-id="{{ $category['id'] }}">Delete</a></span>
                                    </span>
                                </div>

                                @if (sizeof($category['subCategories']) > 0)
                                    <ol class="dd-list">
                                        @foreach($category['subCategories'] as $sub)
                                            <li class="dd-item" data-id="{{ $sub['id'] }}">
                                                <div class="dd-handle dd3-handle">{{ $sub['name'] }}</div>
                                                <div class="editdelete">
                                                    <span>
                                                        <a class="btnEdit color_blue" data-id="{{ $sub['id'] }}" data-index="{{ $loop->index }}" data-parent="{{ $loop->parent->index }}" data-image="{{ isset($sub['image']) ? $sub['image']: 'not_found' }}">Edit</a> |
                                                        <span>&nbsp;<a class="btnDelete color_red" data-id="{{ $sub['id'] }}">Delete</a></span>
                                                    </span>
                                                </div>
                                                @if (sizeof($sub['subCategories']) > 0)
                                                    <ol class="dd-list">
                                                        @foreach($sub['subCategories'] as $sub2)
                                                            <li class="dd-item" data-id="{{ $sub2['id'] }}">
                                                                <div class="dd-handle dd3-handle">{{ $sub2['name'] }}</div>
                                                                <div class="editdelete">
                                                                    <span>
                                                                        <a class="btnEdit color_blue" data-id="{{ $sub2['id'] }}" data-index="{{ $loop->index }}" data-parent="{{ $loop->parent->parent->index }}" data-secondary-parent="{{ $loop->parent->index }}" data-image="{{ isset($sub2['image']) ? $sub2['image']: 'not_found' }}">Edit</a> |
                                                                        <span>&nbsp;<a class="btnDelete color_red" data-id="{{ $sub2['id'] }}">Delete</a></span>
                                                                    </span>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ol>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ol>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
        <div class="add_new_cat pl_15{{ ($errors && sizeof($errors) > 0) ? '' : ' d-none' }} " @if($errors->has('image')) style="display:block" @endif id="addEditRow">
            <div class="m15 ">&nbsp;</div>
            <div class="ly_page_wrapper">
                <div class="float_right">
                    <span class="close_modal item_cat_close"></span>
                </div>
                <h4 id="addEditTitle" class="item_cat_heading mb_20">{{ old('inputAdd') == '0' ? 'Edit Category' : 'Add Category' }}</h4>

                <form class="form-horizontal" enctype="multipart/form-data" id="form" method="post" action="{{ (old('inputAdd') == '1') ? route('admin_category_add') : route('admin_category_update') }}">
                    @csrf
                    <input type="hidden" name="inputAdd" id="inputAdd" value="{{ old('inputAdd') }}">
                    <input type="hidden" name="categoryId" id="categoryId" value="{{ old('categoryId') }}">
                    <div class="add_new_cat_form ">
                        <div class="form_row">
                            <div class="label_inline width_150p fw_500">
                                Status
                            </div>
                            <div class="form_inline">
                                <div class="custom_radio">
                                    <input type="radio" id="statusActive" name="status" class="custom-control-input"
                                            value="1" {{ (old('status') == '1' || empty(old('status'))) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="statusActive">Active</label>
                                </div>
                                <div class="custom_radio">
                                    <input type="radio" id="statusInactive" name="status" class="custom-control-input"
                                            value="0" {{ old('status') == '0' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="statusInactive">Inactive</label>
                                </div>
                            </div>
                        </div>
                        <div class="form_row">
                            <div class="label_inline required width_150p fw_500">
                                <label for="parent_category">Parent</label>
                            </div>
                            <div class="form_inline">
                                <div class="select">
                                    <select class="form_global" name="parent_category" id="parent_category">
                                        <option value="0">Select Parent</option>

                                        @foreach($categories as $category)
                                            <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form_row">
                            <div class="label_inline required width_150p fw_500">
                                <label for="secondaryParent">Secondary Parent</label>
                            </div>
                            <div class="form_inline">
                                <div class="select">
                                    <select class="form_global" name="second_parent" id="secondaryParent">
                                        <option value="0">Select Secondary Parent</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form_row{{ $errors->has('category_name') ? ' has-danger' : '' }}">
                            <div class="label_inline required width_150p fw_500">
                                Category Name
                            </div>
                            <div class="form_inline">
                                <input type="text" id="category_name" class="form_global{{ $errors->has('category_name') ? ' is-invalid' : '' }}" name="category_name" value="{{ old('category_name') }}">
                            </div>
                        </div>
                        <div class="form_row">
                            <div class="label_inline required width_150p fw_500">
                                Meta Title
                            </div>
                            <div class="form_inline">
                                <input type="text" id="meta_title" class="form_global" name="meta_title" value="{{ old('meta_title') }}">
                            </div>
                        </div>
                        <div class="form_row">
                            <div class="label_inline required width_150p fw_500">
                                Meta Description
                            </div>
                            <div class="form_inline">
                                <textarea class="form_global" rows="5" id="meta_description" name="meta_description" value="{{ old('meta_description') }}"></textarea>
                            </div>
                        </div>

                        <div class="form_row">
                            <div class="label_inline required width_150p fw_500">
                                Image
                            </div>
                            <div class="form_inline">
                                @if ($errors->has('image'))
                                    <span class="color_red">{{ $errors->first('image') }}</span>
                                @endif
                                <input type="file" id="categoryImage" name="image" id="image" onchange="readURL(this);" class="form_global file-upload" accept="image/*">
                                <input type="hidden" id="image" name="xxxx" > 
                                <span class="file-name">
                                    <img id="categoryImage_show" src="{{ isset( $category['image']) ? asset($category['image']) : asset('images/no-image.png') }}" height="50" width="50">
                                </span>
                                <button class="file-delete-button" id="delete_image_btn"><i class="fa fa-times"></i></button>
                                <button class="file-revert-button" style="display:none"><i class="fa fa-undo"></i></button>
                            </div>
                        </div>
                        
                        <div class="text_right m15">
                            <div class="display_inline mr_0">
                               @if(old('inputAdd') == '0') <button id="deleteButton" class="ly_btn btn_danger min_width_100p btnDelete" data-id="">Delete</button> @endif
                            </div>
                            <div class="display_inline mr_0">
                                <button class="ly_btn btn_blue min_width_100p" id="btnCancel">Cancel</button>
                                <button type="submit" class="ly_btn btn_blue min_width_100p" id="btnSubmit">{{ old('inputAdd') == '0' ? 'Update' : 'Add' }}</button>
                            </div>
                        </div>
                    </div>
                     <input type="hidden" id="delete_image" name="delete_image" value="0"> 
                </form>
            </div>
        </div>
        <div class="item_cat_right_text float_right"> <span>You currently have   categories.</span> </div>
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
    <script type="text/javascript" src="{{ asset('plugins/Nestable/jquery.nestable.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {

            $('.file-delete-button').click(function (e) { 
                $('.file-delete-button').addClass('hide');
            });

            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);

            var categories = <?php echo json_encode($categories); ?>;
            var selectedID = 0;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#nestable').nestable({
                maxDepth: 3
            }).on('change', '.dd-item', function(e) {
                e.stopPropagation();

                var itemArray = $('.dd').nestable('serialize');

                var id = $(this).data('id'),
                    parentId = $(this).parents('.dd-item').data('id');

                if (parentId == undefined)
                    parentId = 0;

                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_sort_category') }}",
                    data: { itemArray: itemArray }
                }).done(function( msg ) {
                    toastr.success('Category Updated!');
                });
            });

            $('#btnAddNew').click(function () {
                $("#categoryImage_show").attr('src','');
                $('#btnAddNew').css('opacity' , '0');
                $('#addEditRow').removeClass('d-none');
                $('#addEditTitle').html('Add Category');
                $('#btnSubmit').html('Add');
                $('#inputAdd').val('1');
                $('#form').attr('action', '{{ route('admin_category_add') }}');
                $('#statusActive').prop('checked', true);
                $('#parent_category').val('0');
                $('#secondaryParent').html('<option value="0">Select Secondary Parent</option>');
                $('#category_name').val('');

                $('input').removeClass('is-invalid');
                $('.form-group').removeClass('has-danger');
            });

            $('.item_cat_close').click(function() {
                $('#addEditRow').addClass('d-none');
                $('#btnAddNew').css('opacity' , '1');
            });

            $('#btnCancel').click(function (e) {
                e.preventDefault();

                $('#addEditRow').addClass('d-none');
                $('#btnAddNew').css('opacity' , '1');
                $('#statusActive').prop('checked', true);
                $('#parent_category').val('');
                $('#secondaryParent').val('');
                $('#category_name').val('');

                $('input').removeClass('is-invalid');
                $('.form-group').removeClass('has-danger');
            });

            $('#parent_category').change(function () {
                var index = $(this).prop('selectedIndex');

                $('#secondaryParent').html('<option value="0">Select Secondary Parent</option>');

                if (index != 0) {
                    var subCategories = categories[index - 1].subCategories;

                    $.each(subCategories, function (index, value) {
                        $('#secondaryParent').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            });

            $(document).on('click', '.btnEdit',function () {
                $("html, body").animate({ scrollTop: 0 }, "slow");
                $('.file-name').show();
                $('.file-delete-button').show();
                var id = $(this).data('id');
                selectedID = $(this).data('id');
                var index = $(this).data('index');
                var parent = $(this).data('parent');
                var secondaryParent = $(this).data('secondary-parent');
                var image = $(this).data('image');
// console.log(image);
                var url = '{{URL::to('/')}}';
                if( image !== 'not_found'){
                    $('#categoryImage_show').attr('src', url + image);
                }else{
                    $('#categoryImage_show').attr('src', url +'/' + 'images/no-image.png');
                }

                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_category_detail') }}",
                    data: { id: id }
                }).done(function( data ) {
                    $('#btnAddNew').css('opacity' , '0');
                    $('.add_new_cat').css('display' , 'block');
                    $('#addEditRow').removeClass('d-none');
                    $('#addEditTitle').html('Edit Category');
                    $('#deleteButton').attr('data-id', id)
                    $('#btnSubmit').html('Update');
                    $('#inputAdd').val('0');
                    $('#form').attr('action', '{{ route('admin_category_update') }}');
                    $('#categoryId').val(id);

                    if (secondaryParent !== undefined)
                        var category = categories[parent].subCategories[secondaryParent].subCategories[index];
                    else if (parent !== undefined)
                        var category = categories[parent].subCategories[index];
                    else
                        var category = categories[index];

                    if (data.status == 1)
                        $('#statusActive').prop('checked', true);
                    else
                        $('#statusInactive').prop('checked', true);

                    $('#parent_category').val('0');
                    $('#secondaryParent').html('<option value="0">Select Secondary Parent</option>');

                    if (parent !== undefined) {
                        $('#parent_category').val(categories[parent].id);
                        var subCategories = categories[parent].subCategories;
                        var secondaryParentID = 0;

                        if (secondaryParent !== undefined)
                            secondaryParentID = categories[parent].subCategories[secondaryParent].id;

                        $.each(subCategories, function (index, value) {

                            if (secondaryParentID == value.id)
                                $('#secondaryParent').append('<option value="' + value.id + '" selected>' + value.name + '</option>');
                            else
                                $('#secondaryParent').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }

                    $('#category_name').val(data.name);
                    $('#meta_title').val(data.meta.title);
                    $('#meta_description').val(data.meta.description);
                    $('#image').val(data.image);
                });
            });

            $('.btnDelete').click(function (e) {
                e.preventDefault();
                $('#deleteModal').addClass('open_modal');
                selectedId = $(this).data('id');
            });

            $('#modalBtnDelete').click(function () {
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_category_delete') }}",
                    data: { id: selectedId }
                }).done(function( msg ) {
                    location.reload();
                });
            });
        });
    </script>

    <script>
        function readURL(input) {
        $('.file-name').addClass('show');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
            $('#categoryImage_show').attr('src', e.target.result);
            }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).on('change', '.file-upload', function(){
            $('.file-delete-button').trigger('click');
        })
        $('.file-revert-button').click(function(e){
            e.preventDefault();
            $('.is-file-delete').val(false);
            $('.file-name').css('display','inline');
            $('.file-delete-button').show();
            $(this).hide();
        })
        $('.file-delete-button').click(function(e){
            e.preventDefault();
            $('.is-file-delete').val(true);
            $('.file-name').css('display','none');
            $('.file-revert-button').show();
            $(this).hide();
        })
        $(document).on("click","#delete_image_btn",function() {
                $("#delete_image").val(1);
            });
    </script>
@stop