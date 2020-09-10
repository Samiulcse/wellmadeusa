<?php use App\Enumeration\PageEnumeration; ?>

@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
@stop

@section('content')
    <div class="row {{ ($errors && sizeof($errors) > 0) ? 'd-none' : '' }}" id="addBtnRow">
        <div class="col-md-12">
            <button class="btn btn-primary" id="btnAddNew">Add</button>
        </div>
    </div>

    <div class="row {{ ($errors && sizeof($errors) > 0) ? '' : 'd-none' }}" id="addEditRow">
        <div class="col-md-12" style="border: 1px solid black">
            <h3><span id="addEditTitle">Add Category Banner Details</span></h3>

            <form class="form-horizontal" enctype="multipart/form-data" id="form" method="post" action="{{ route('admin_top_banner_add') }}">
                @csrf
{{--                <div class="form-group row{{ $errors->has('title') ? ' has-danger' : '' }}">--}}
{{--                    <div class="col-lg-2">--}}
{{--                        <label for="title" class="col-form-label">Title</label>--}}
{{--                    </div>--}}

{{--                    <div class="col-lg-10">--}}
{{--                        <input type="text" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}"--}}
{{--                               placeholder="Category heading" name="title" @isset($banners->title) value="{{ empty(old('title')) ? ($errors->has('title') ? '' : $banners->title) : old('title') }}"@endif>--}}
{{--                    </div>--}}
{{--                </div>--}}

                <div class="form-group row{{ $errors->has('description') ? ' has-danger' : '' }}">
                    <div class="col-lg-2">
                        <label for="description" class="col-form-label">Description</label>
                    </div>

                    <div class="col-lg-10">
                        <input type="text" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"
                               placeholder="Category Description" name="description" @isset($banners->description) value="{{ empty(old('description')) ? ($errors->has('description') ? '' : $banners->description) : old('description') }}" @endif>
                    </div>
                </div>
{{--                <div class="form-group row{{ $errors->has('photo') ? ' has-danger' : '' }}">--}}
{{--                    <div class="col-lg-2">--}}
{{--                        <label for="code" class="col-form-label">Image<span class="required"> </span> (width: 1250px X height: 172px)</label>--}}
{{--                    </div>--}}

{{--                    <div class="col-lg-5">--}}
{{--                        <input class="form-control{{ $errors->has('photo') ? ' is-invalid' : '' }}"--}}
{{--                               type="file" id="photo" name="photo" accept="image/*">--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                @if ($errors->has('photo'))--}}
{{--                    <div class="form-control-feedback">{{ $errors->first('photo') }}</div>--}}
{{--                @endif--}}

{{--                <div class="form-group row{{ $errors->has('link') ? ' has-danger' : '' }}">--}}
{{--                    <div class="col-lg-2">--}}
{{--                        <label for="link" class="col-form-label">Link </label>--}}
{{--                    </div>--}}

{{--                    <div class="col-lg-5">--}}
{{--                        <input class="form-control{{ $errors->has('link') ? ' is-invalid' : '' }}"--}}
{{--                               type="text" id="link" name="link" value="{{ old('link') }}">--}}
{{--                    </div>--}}
{{--                </div>--}}

                <div class="form-group row{{ $errors->has('page') ? ' has-danger' : '' }}">
                    <div class="col-lg-2">
                        <label for="page" class="col-form-label">Page<span class="required"> *</span></label>
                    </div>

                    <div class="col-lg-5">
                        <select class="form-control{{ $errors->has('page') ? ' is-invalid' : '' }}" name="page">
                            <option value="">Select Page</option>
                            {{--                            <option value="-1">New Arrival</option>--}}
                            {{--                            <option value="-2">Best Sellers</option>--}}
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">@if(!empty($category->name)){{ $category->name }} @endif</option>
                            @endforeach
                        </select>
                        @if ($errors->has('page'))
                            <div class="form-control-feedback form-error text-danger">{{ $errors->first('page') }}</div>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-lg-12 text-right">
                        <button class="btn btn-default" id="btnCancel">Cancel</button>
                        <input type="submit" id="btnSubmit" class="btn btn-primary" value="Add">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
{{--                        <th>Title</th>--}}
                        <th>Description</th>
                        <th>Page</th>
{{--                        <th>Banner</th>--}}
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($banners as $banner)
                        <tr>
{{--                            <td>{{$banner->title}}</td>--}}
                            <td>{{$banner->description}}</td>
                            <td>
                                @if ($banner->page == PageEnumeration::$NEW_ARRIVAL)
                                    New Arrival
                                @elseif ($banner->page == PageEnumeration::$BEST_SELLER)
                                    Best Seller
                                @else
                                    @if(!empty($banner->category->name)) {{ $banner->category->name }} @endif
                                @endif
                            </td>
{{--                            <td>--}}
{{--                                @if(!empty($banner->image_path))--}}
{{--                                    <img src="{{ asset($banner->image_path) }}" width="200px" height="100px">--}}
{{--                                @endif--}}
{{--                            </td>--}}
                            <td>
                                <a class="text-danger btnRemove" data-id="{{ $banner->id }}">Remove</a> |
                                <a class="text-info btnEdit" data-id="{{ $banner->id }}"
                                   data-title="{{ $banner['title'] }}"
                                   data-description="{{ $banner['description'] }}"
                                   data-link="{{ $banner['url'] }}"
                                   data-image="{{ isset($banner['image_path']) ? $banner['image_path']: 'not_found' }}"
                                >Edit</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br>
    @if(Session::has('flash_message_success'))
        <div class="alert alert-success background-success">
            <button type="button" class="close" data-dismiss="alert">x</button>
            <strong>{!! session('flash_message_success')!!}</strong>
        </div>
    @endif
    <form action="{{ route('admin_update_home_Settings') }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
        @csrf
        <input type="hidden" value="2" name="customize_title_desc">


        <div class="row">
            <div class="col-12">
                <h5>New Arrival Top Banner</h5>
                <hr>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-2">
                <label class="col-form-label">Description Two: </label>
            </div>
            <div class="col-4">
                <input type="text" class="form-control{{ $errors->has('new_desc_two') ? ' is-invalid' : '' }}" name="new_desc_two" value="{{ !empty($settings_data[0]->new_desc_two) ? $settings_data[0]->new_desc_two : '' }}">

                @if ($errors->has('new_desc_two'))
                    <div class="form-control-feedback">{{ $errors->first('new_desc_two') }}</div>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <div class="col-2">
                <label class="col-form-label">Text Color: </label>
            </div>
            <div class="col-4">
                <input type="color" class="form-control{{ $errors->has('new_desc_two_color') ? ' is-invalid' : '' }}" name="new_desc_two_color" value="{{ !empty($settings_data[0]->new_desc_two_color) ? $settings_data[0]->new_desc_two_color : '' }}">

                @if ($errors->has('new_desc_two_color'))
                    <div class="form-control-feedback">{{ $errors->first('new_desc_two_color') }}</div>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <div class="col-2">
                <label class="col-form-label">Text Font Size ( In px) : </label>
            </div>
            <div class="col-4">
                <input type="number" class="form-control{{ $errors->has('new_desc_two_font') ? ' is-invalid' : '' }}" name="new_desc_two_font" value="{{ !empty($settings_data[0]->new_desc_two_font) ? $settings_data[0]->new_desc_two_font : '' }}" min="0">

                @if ($errors->has('new_desc_two_font'))
                    <div class="form-control-feedback">{{ $errors->first('new_desc_two_font') }}</div>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <div class="col-2">
                <label class="col-form-label">Text Font Family : </label>
            </div>
            <div class="col-4">
                <select class="" id="new_desc_two_font_family" name="new_desc_two_font_family">
                    <option value="" disabled selected>Select Font Family</option>
                    <option value="Times New Roman, Times, serif" {{ $settings_data[0]->new_desc_two_font_family == "Times New Roman, Times, serif" ? 'selected' : '' }} >Times New Roman, Times, serif</option>
                    <option value="Arial, Helvetica, sans-serif" {{ $settings_data[0]->new_desc_two_font_family == "Arial, Helvetica, sans-serif" ? 'selected' : '' }} >Arial, Helvetica, sans-serif</option>
                    <option value="Georgia, serif" {{ $settings_data[0]->new_desc_two_font_family == "Georgia, serif" ? 'selected' : '' }}>Georgia, serif</option>
                    <option value="Arial Black, Gadget, sans-serif"{{ $settings_data[0]->new_desc_two_font_family == "Arial Black, Gadget, sans-serif" ? 'selected' : '' }} >Arial Black, Gadget, sans-serif</option>
                    <option value="Comic Sans MS, cursive, sans-serif"{{ $settings_data[0]->new_desc_two_font_family == "Comic Sans MS, cursive, sans-serif" ? 'selected' : '' }} >Comic Sans MS, cursive, sans-serif</option>
                    <option value="Verdana, Geneva, sans-serif"{{ $settings_data[0]->new_desc_two_font_family == "Verdana, Geneva, sans-serif" ? 'selected' : '' }} >Verdana, Geneva, sans-serif</option>
                    <option value="Tahoma, Geneva, sans-serif"{{ $settings_data[0]->new_desc_two_font_family == "Tahoma, Geneva, sans-serif" ? 'selected' : '' }} >Tahoma, Geneva, sans-serif</option>
                    <option value="Trebuchet MS, Helvetica, sans-serif" {{ $settings_data[0]->new_desc_two_font_family == "Trebuchet MS, Helvetica, sans-serif" ? 'selected' : '' }}>Trebuchet MS, Helvetica, sans-serif</option>
                    <option value="Impact, Charcoal, sans-serif"{{ $settings_data[0]->new_desc_two_font_family == "Impact, Charcoal, sans-serif" ? 'selected' : '' }} >Impact, Charcoal, sans-serif</option>
                    <option value="Courier New, Courier, monospace"{{ $settings_data[0]->new_desc_two_font_family == "Courier New, Courier, monospace" ? 'selected' : '' }} >Courier New, Courier, monospace</option>
                    <option value="Lucida Console, Monaco, monospac"{{ $settings_data[0]->new_desc_two_font_family == "Lucida Console, Monaco, monospac" ? 'selected' : '' }} >Lucida Console, Monaco, monospace</option>
                    <option value="Cormorant Garamond, serif" {{ $settings_data[0]->new_desc_two_font_family == "Cormorant Garamond, serif" ? 'selected' : '' }} >Cormorant Garamond, serif</option>
                    <option value="Source Sans Pro, sans-serif" {{ $settings_data[0]->new_desc_two_font_family == "Source Sans Pro, sans-serif" ? 'selected' : '' }} >Source Sans Pro, sans-serif</option>
                </select>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-2">
            </div>
            <div class="col-7">
                <input class="btn btn-primary" type="submit" value="Update">
            </div>
        </div>
    </form>
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
                    <h4 class="modal-title text-white" id="">Edit Category Banner Details</h4>
                </div>
                {{--                <div class="modal-body">--}}
                {{--                    <input type="text" class="form-control" id="modal_url">--}}
                {{--                </div>--}}
                <div class="modal-body">
                    <form class="form-horizontal" id="categoryBannerUpdateform" method="" action="" enctype="multipart/form-data">
                        @csrf
                        <fieldset>
{{--                            <div class="form-group row">--}}
{{--                                <div class="col-lg-5 text-lg-right">--}}
{{--                                    <label for="modalTitle" class="col-form-label">Title</label>--}}
{{--                                </div>--}}
{{--                                <div class="col-lg-6">--}}
{{--                                    <input type="text" id="modalTitle" name="modalTitle" class="form-control" placeholder=" " value=""  >--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <div class="form-group row">
                                <div class="col-lg-5 text-lg-right">
                                    <label for="modalDescription" class="col-form-label">Description</label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" id="modalDescription" name="modalDescription" class="form-control" placeholder="" value="">
                                </div>
                            </div>
{{--                            <div class="form-group row">--}}
{{--                                <div class="col-lg-5 text-lg-right">--}}
{{--                                    <label for="modalLink" class="col-form-label"> Link</label>--}}
{{--                                </div>--}}
{{--                                <div class="col-lg-6">--}}
{{--                                    <input type="text" id="modalLink" class="form-control" name="modalLink" placeholder="" value="">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="form-group row">--}}
{{--                                <div class="col-lg-5 text-lg-right">--}}
{{--                                    <label for="categoryImage" class="col-form-label">Category Banner Image</label>--}}
{{--                                </div>--}}
{{--                                <div class="col-lg-6">--}}
{{--                                    <input type="file" id="categoryImage" name="categoryImage" class="form-control file-upload" accept="image/*">--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <input type="hidden" id="category_page_id" name="category_page_id" value="">
                        </fieldset>
                        <div class="modal-footer">
                            <button class="btn  btn-default" data-dismiss="modal">Close</button>
                            <button class="btn  btn-default" id="modalBtnEdit">Save</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <!--- end modals-->
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
            var message = '{{ session('message') }}';
            var banners = <?php echo json_encode($banners); ?>;

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
                $('#deleteModal').modal('show');
                selectedId = $(this).data('id');
            });

            $('#modalBtnDelete').click(function () {
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_top_banner_delete') }}",
                    data: { id: selectedId }
                }).done(function( msg ) {
                    location.reload();
                });
            });

            // Edit
            $('.btnEdit').click(function () {
                var id = parseInt($(this).data('id'));
                selectedId = id;

                var edittitle = $(this).data('title');
                var editdesc = $(this).data('description');
                var editlink = $(this).data('link');
                // var image = $(this).data('image');
                var url = '{{URL::to('/')}}';
                // if( image !== 'not_found'){
                //     $('#categoryImage_show').attr('src', url + image);
                // }else{
                //     $('#categoryImage_show').attr('src', url +'/' + 'images/no-image.png');
                // }
                $.each(banners, function (i, img) {
                    if (img.id == id)
                        banner = img;
                });
                $('#modalDescription').val(editdesc);
                $('#modalTitle').val(edittitle);
                $('#modalLink').val(editlink);
                $('#category_page_id').val(selectedId);
                // $('#image').val(image);

                // $('#modal_url').val(banner.url);
                $('#editModal').modal('show');
            });

            $('#modalBtnEdit').click(function () {

                $("#categoryBannerUpdateform").attr("method", "post");
                $("#categoryBannerUpdateform").attr("action", "{{route('admin_top_banner_edit_post')}}");
                {{--var description = $('#modalDescription').val();--}}
                {{--var title = $('#modalTitle').val();--}}
                {{--var link = $('#modalLink').val();--}}
                {{--var image = $('#image').val();--}}

                {{--$.ajax({--}}
                {{--    method: "POST",--}}
                {{--    url: "{{ route('admin_top_banner_edit_post') }}",--}}
                {{--    data: { id: selectedId, url: url }--}}
                {{--}).done(function( msg ) {--}}
                {{--    location.reload();--}}
                {{--});--}}
            });
        })
    </script>
@stop