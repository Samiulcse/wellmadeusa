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
    <div class="row {{ ($errors && sizeof($errors) > 0) ? 'd-none' : '' }}" id="addBtnRow">
        <div class="col-md-6">
            <button class="btn btn-primary" id="btnAddNew">Add New Image</button>
        </div>
        <div class="col-md-6">
            <p class="msg_txt">Per Row 2 Image</p>
        </div>
    </div>

    <div class="row {{ ($errors && sizeof($errors) > 0) ? '' : 'd-none' }}" id="addEditRow">
        <div class="col-md-12" style="border: 1px solid black">
            <h3><span id="addEditTitle">Add New Image</span></h3>

            <form class="form-horizontal" enctype="multipart/form-data" id="form" method="post" action="{{ route('admin_front_page_banner_three_add') }}">
                @csrf

                <div class="form-group row{{ $errors->has('photo') ? ' has-danger' : '' }}">
                    <div class="col-lg-2">
                        <label for="photo" class="col-form-label">Image *</label>
                    </div>

                    <div class="col-lg-5">
                        <input class="form-control{{ $errors->has('photo') ? ' is-invalid' : '' }}"
                               type="file" id="photo" name="photo" accept="image/*" required="">
                    </div>
                </div>

                @if ($errors->has('photo'))
                    <div class="form-control-feedback">{{ $errors->first('photo') }}</div>
                @endif

                <div class="form-group row{{ $errors->has('link') ? ' has-danger' : '' }}">
                    <div class="col-lg-2">
                        <label for="link" class="col-form-label">Link</label>
                    </div>

                    <div class="col-lg-5">
                        <input class="form-control{{ $errors->has('link') ? ' is-invalid' : '' }}"
                               type="text" id="link" name="link" value="{{ old('link') }}">
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('head') ? ' has-danger' : '' }}">
                    <div class="col-lg-2">
                        <label for="head" class="col-form-label">Description</label>
                    </div>

                    <div class="col-lg-5">
                        <input class="form-control{{ $errors->has('head') ? ' is-invalid' : '' }}"
                               type="text" id="head" name="head" value="{{ old('head') }}">
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
            <div class="banner-item-container">
                <ul id="HomePageSliderItems">
                    @foreach($images as $image)
                        <li class="text-center" data-id="{{ $image->id }}">
                            <img src="{{ asset($image->image_path) }}">
                            <a class="text-danger btnRemove" data-type="1" data-id="{{ $image->id }}">Remove</a> |
                            <a class="text-info btnEdit" data-head="{{ $image->head }}" data-id="{{ $image->id }}">Edit</a>
                        </li>
                    @endforeach
                </ul>
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
                        <input type="url" class="form-control" id="modal_url" required="">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" class="form-control" id="modal_head">
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
    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

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
                $('#deleteModal').modal('show');
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

            var el = document.getElementById('HomePageSliderItems');
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
                var id = parseInt($(this).data('id'));
                var head = $(this).data('head');
                selectedId = id;

                $.each(images, function (i, img) {
                    if (img.id == id)
                        image = img;
                });

                $('#modal_url').val(image.url);
                $('#modal_head').val(head);
                $('#editModal').modal('show');
            });

            $('#modalBtnEdit').click(function () {
                var url = $('#modal_url').val();
                var head = $('#modal_head').val();

                if (url!='' && head!='') {
                    $.ajax({
                        method: "POST",
                        url: "{{ route('admin_banner_edit_post') }}",
                        data: { id: selectedId, url: url, head: head }
                    }).done(function( msg ) {
                        location.reload();
                    });
                } else {
                    $('#htmlMsg').html('<p class="text-danger">URL & Description is required!</p>')
                }
            });
        })
    </script>
@stop
