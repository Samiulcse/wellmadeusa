@extends('admin.layouts.main')

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
    </style>
@stop

@section('content')
<div class="ly_accrodion">
    <div class="ly_accrodion_heading">
        <div id="btnAddNewFabric" class="ly_accrodion_title {{ count($errors) > 0 ? ' open_acc' : ''}}" data-toggle="accordion" data-target="#addNewFabric" data-class="accordion">
            <span id="btnAddNew">Add New Image</span>
        </div>
    </div>
    <div class="accordion_body default_accrodion  {{ count($errors) > 0 ? ' open' : ''}}" id="addNewFabric" style="">            
        <div class="form_row">
            <div class="ly-row {{ ($errors && sizeof($errors) > 0) ? '' : 'd-none' }}" id="addEditRow">
                <div class="ly-12" style="border: 1px solid black">  
                    <form   enctype="multipart/form-data" id="form" method="post" action="{{ route('admin_front_page_banner_item_add') }}">
                        <div class="ly-wrap-fluid">
                        @csrf
                        <input type="hidden" name="cat_id" value="{{$id}}">
                        <div class="form_row{{ $errors->has('photo') ? ' has-danger' : '' }}">
                            <div class="label_inline  required">
                                <label for="photo" class="col-form-label">Image/Video (width: 1902px X height: 1070px)</label> 
                            </div>
                            <div class="form_inline">
                                <input class="form_global {{ $errors->has('photo') ? ' is-invalid' : '' }}" type="file" id="photo" name="photo"> 
                            </div>   
                        </div> 
                        <div class="form_row{{ $errors->has('link') ? ' has-danger' : '' }}">
                            <div class="label_inline  ">
                                <label for="link" class="col-form-label">Link </label>
                            </div> 
                            <div class="form_inline">
                                <input class="form_global {{ $errors->has('link') ? ' is-invalid' : '' }}" type="text" id="link" name="link" value="{{ old('link') }}">
                            </div>
                        </div> 

                        <div class="form_row {{ $errors->has('link') ? ' has-danger' : '' }}">
                            <div class="label_inline  required">
                                <label for="link" class="col-form-label">Use For? </label>
                            </div> 
                            <div class="form_inline">
                                <div class="custom_radio">
                                    <input type="radio" name="type"   id="lgdevice" value="15" checked required="">
                                    <label for="lgdevice"> Category Landing Page Top Banner</label>
                                </div>
                                <div class="custom_radio">
                                    <input type="radio" name="type"   id="smdevice" value="16" required="">
                                    <label for="smdevice">  Category Landing Page Bottom Banner</label>
                                </div> 
                            </div>
                        </div> 

                        <div class="form_row">
                            <div class=" text_right">
                                <button class="ly_btn  btn_blue min_width_100p" id="btnCancel">Cancel</button>
                                <input type="submit" id="btnSubmit" class="ly_btn  btn_blue min_width_100p" value="Add">
                            </div>
                        </div> 
                        </div> 
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 
 
    

    <br>

    <div class="ly-row">
        <div class="ly-12"> 
            <h3>Banner For Category Landing Page Top </h3> 
            <br>
        </div>
        <div class="ly-12">
            <div class="banner-item-container">
                <ul id="categorylandingtopbanner"  class="ly-row">
                    @foreach($images as $image)
                    @if($image->type == 15)
                        @php
                            $ext = pathinfo( url("$image->image_path"), PATHINFO_EXTENSION );
                        @endphp
                        <li class="text-center ly-2" data-id="{{ $image->id }}" >
                            @if ($ext == 'mp4')
                                <video height="56" controls="" autoplay="" width="100%">
                                    <source src="{{ asset($image->image_path) }}" type="video/mp4">
                                    Your browser does not support HTML5 video.
                                </video>
                            @else
                                <img src="{{ asset($image->image_path) }}" width="100%">
                            @endif
                            <a class="text-danger btnRemove" data-type="1" data-id="{{ $image->id }}">Remove</a> 
                            <!-- <a class="text-info btnEdit" data-head="{{ $image->head }}" data-id="{{ $image->id }}">Edit</a> -->
                        </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
 
<br>
<br>
    <div class="ly-row">
       <div class="ly-12"> 
           <h3>Banner For Category Landing Page Bottom </h3> 
           <br>
        </div>
       <div class="ly-12">
           <div class="banner-item-container">
               <ul id="categorylandingbottombanner" class="ly-row">
                   @foreach($images as $image)
                    
                    @if($image->type == 16)
                       <li class="text-center ly-2" data-id="{{ $image->id }}">
                           <img src="{{ asset($image->image_path) }}" width="100%">
                           <a class="text-danger btnRemove" data-type="1" data-id="{{ $image->id }}">Remove</a> 
                           <!-- <a class="text-info btnEdit" data-head="{{ $image->head }}" data-type ='2' data-id="{{ $image->id }}">Edit</a> -->
                       </li>
                       @endif
                   @endforeach
               </ul>
           </div>
       </div>
    </div>
<br>
<br>
<br>
    <div class="ly_accrodion">
    <div class="ly_accrodion_heading">
        <div id="btnAddNewFabric" class="ly_accrodion_title {{ count($errors) > 0 ? ' open_acc' : ''}}" data-toggle="accordion" data-target="#customcontent" data-class="accordion">
            <span id="btnAddNew">Category Landing Page Bottom Custom Section</span>
        </div>
    </div>
    <div class="accordion_body default_accrodion  open" id="customcontent" style="">            
        <div class="form_row">
            <div class="ly-row {{ ($errors && sizeof($errors) > 0) ? '' : 'd-none' }}" id="addEditRow">
                <div class="ly-12" style="border: 1px solid black"> 
                    <form   enctype="multipart/form-data" id="form" method="post" action="{{ route('category_landing_page_custom_content') }}">
                        <div class="ly-wrap-fluid">
                        @csrf
                        <input type="hidden" name="cat_id" value="{{$id}}"> 
                        <div class="form_row{{ $errors->has('content') ? ' has-danger' : '' }}"> 
                            <div class="form_inline ly-10">
                                <br>
                                <br>
                                <textarea name="details" id="details"   class="form_global" > @if($category->details) {!! $category->details !!} @endif</textarea>
                            </div>
                        </div> 

                        <div class="form_row">
                            <div class=" text_right">  
                                <input type="submit" id=" " class="ly_btn  btn_blue min_width_100p" value="Update">
                            </div>
                        </div> 
                        </div> 
                    </form>
                </div>
            </div>
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

    <div class="modal fade" id="editModal" role="dialog" aria-labelledby="editModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h4 class="modal-title text-white" id="editModal">Edit Link URL</h4>
                </div>
                <div class="modal-body">
                    <div id="htmlMsg"></div>
                    <div class="form-group">
                        <label>Url</label>
                        <input type="url" class="" id="modal_url" required="">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" class="" id="modal_head">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn  btn-default" data-dismiss="modal">Close</button>
                    <button class="btn  btn-primary" id="modalBtnEdit">Save</button>
                </div>
            </div>
        </div>
        <!--- end modals-->
    </div>
@stop

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/sortable/js/Sortable.min.js') }}"></script>
    <script src="//cdn.ckeditor.com/4.6.2/full/ckeditor.js"></script>
    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var options = {
            filebrowserImageBrowseUrl: '{{ url('laravel-filemanager') }}?type=Images',
            filebrowserImageUploadUrl: '{{ url('laravel-filemanager') }}/upload?type=Images&_token=',
            filebrowserBrowseUrl: '{{ url('laravel-filemanager') }}?type=Files',
            filebrowserUploadUrl: '{{ url('laravel-filemanager') }}?type=Files&_token=',
            height : 500 
        };

        CKEDITOR.config.allowedContent = true;
        CKEDITOR.replace('details', options);

            var selectedId;
            var message = '{{ session('message') }}';
            var images = <?php echo json_encode($images); ?>; 

            if (message != '')
                toastr.success(message);

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
                selectedId = $(this).data('id');
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_main_slider_item_delete') }}",
                    data: { id: selectedId }
                }).done(function( msg ) {
                    location.reload();
                });
            });
 

            var el = document.getElementById('categorylandingtopbanner');
            Sortable.create(el, {
                animation: 150,
                dataIdAttr: 'data-id',
                onEnd: function () {
                    updateSort(this.toArray());
                },
            });

            var el = document.getElementById('categorylandingbottombanner');
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
                $('#editModal').modal('show');
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
        })
    </script>
@stop
