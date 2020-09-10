@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
@stop

@section('content')
    <div class="row {{ ($errors && sizeof($errors) > 0) ? 'd-none' : '' }}" id="addBtnRow">
        <div class="col-md-12">
            <button class="btn btn-primary" id="btnAddNew">Add New Item</button>
        </div>
    </div>

    <div class="row {{ ($errors && sizeof($errors) > 0) ? '' : 'd-none' }}" id="addEditRow">
        <div class="col-md-12" style="border: 1px solid black">
            <h3><span id="addEditTitle">Add New Image</span></h3>

            <form class="form-horizontal" enctype="multipart/form-data" id="form" method="post" action="{{ route('admin_main_slider_item_add') }}">
                @csrf

                <div class="form-group row{{ $errors->has('photo') ? ' has-danger' : '' }}">
                    <div class="col-lg-2">
                        <label for="code" class="col-form-label">Image/Video *</label>
                    </div>

                    <div class="col-lg-5">
                        <input class="form-control{{ $errors->has('photo') ? ' is-invalid' : '' }}"
                               type="file" id="photo" name="photo">
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

                <input type="hidden" name="color" value="Black">
                {{--  <div class="form-group row">
                    <div class="col-lg-2">
                        <label for="link" class="col-form-label">Color</label>
                    </div>

                    <div class="col-lg-5">
                        <select class="form-control" name="color">
                            <option value="black">Black</option>
                            <option value="white">White</option>
                        </select>
                    </div>
                </div>  --}}

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
                            @if (strpos($image->image_path, '.mp4') !== false)
                                <img src="{{ asset('images/video.png') }}">
                            @else
                                <img src="{{ asset($image->image_path) }}">
                            @endif

                            <a class="text-danger btnRemove" data-type="1" data-id="{{ $image->id }}">Remove</a> |
                            <a class="text-info btnEdit" data-id="{{ $image->id }}">Edit</a>
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
                    <input type="text" class="form-control" id="modal_url"><br>
                    <select class="form-control" id="modal-color">
                        <option value="black">Black</option>
                        <option value="white">White</option>
                    </select>
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
                selectedId = id;

                $.each(images, function (i, img) {
                    if (img.id == id)
                        image = img;
                });


                $('#modal-color').val(image.color);
                $('#modal_url').val(image.url);
                $('#editModal').modal('show');
            });

            $('#modalBtnEdit').click(function () {
                var url = $('#modal_url').val();
                var color = $('#modal-color').val();

                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_banner_edit_post') }}",
                    data: { id: selectedId, url: url, color: color }
                }).done(function( msg ) {
                    location.reload();
                });
            });
        })
    </script>
@stop
