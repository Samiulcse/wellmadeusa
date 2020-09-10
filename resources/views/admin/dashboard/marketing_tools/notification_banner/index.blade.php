<?php use App\Enumeration\PageEnumeration; ?>

@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <style type="text/css">
        .banner-item-container ul li {
            width: 50%;
            margin-right: 1%;
            float: left;
        }
        .common_banner .banner_top p {
    margin-bottom: 10px;
}
.banner_top p {
    font-weight: 700;
    color: #ffffff;
    font-size: 24px;
    letter-spacing: .25em;
    line-height: 1.5;
    vertical-align: middle;
    text-transform: uppercase;
    width: 100%;
    padding: 35px 0px;
}
.common_banner .banner_top p {
    margin-bottom: 10px;
}
.banner_top p {
    font-weight: 700;
    color: #ffffff;
    font-size: 24px;
    letter-spacing: .25em;
    line-height: 1.5;
    vertical-align: middle;
    text-transform: uppercase;
    width: 100%;
    padding: 35px 0px;
}
.banner_top_black p {
    font-weight: 700;
    color: black;
    font-size: 24px;
    letter-spacing: .25em;
    line-height: 1.5;
    vertical-align: middle;
    text-transform: uppercase;
    width: 100%;
    padding: 35px 0px;
    text-align: center;
}
.common_banner .banner_top {
    margin: 0px 0px 0px;
}
.banner_top {
    background: url(../images/banner-top.jpg) no-repeat;
    background-size: cover;
    text-align: center;
    margin: 0px 0px 30px;
}
.container {
    width: 100%;
    padding-right: 5px;
    padding-left: 5px;
    margin-right: 0px;
    margin-left: 0px;
}
.container {
    max-width: 100%;
}
.p
{
    black;
}
    </style>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <form class="form-horizontal" enctype="multipart/form-data" id="form" method="post" action="{{ route('admin_notification_banner_add_post') }}">
                @csrf
                <div class="row">
                    <div class="col-6">
                        <label class="text-center">Status:</label>
                         @if(count($records) > 0)
                            @if($records[0]->status == 0)
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="statusActive" name="status" class="custom-control-input" value="1">
                                    <label class="custom-control-label" for="statusActive">Active</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="statusInactive" name="status" class="custom-control-input" value="0" checked="">
                                    <label class="custom-control-label" for="statusInactive">Inactive</label>
                                </div>
                            @else
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="statusActive" name="status" class="custom-control-input" value="1" checked="">
                                <label class="custom-control-label" for="statusActive">Active</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="statusInactive" name="status" class="custom-control-input" value="0">
                                <label class="custom-control-label" for="statusInactive">Inactive</label>
                            </div>
                            @endif
                         @else
                             <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="statusActive" name="status" class="custom-control-input" value="1">
                                <label class="custom-control-label" for="statusActive">Active</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="statusInactive" name="status" class="custom-control-input" value="0" checked="">
                                <label class="custom-control-label" for="statusInactive">Inactive</label>
                            </div>
                         @endif
                    </div>
                    <div class="col-md-12">
                        @if(count($records) > 0)
                            @if($records[0]->details == '')
                                <textarea class="d-none" name="link" id="page_editor" rows="2"></textarea>
                            @else
                                <textarea class="d-none" name="link" id="page_editor" rows="2">{{$records[0]->details}}</textarea>
                            @endif
                        @else
                        <textarea class="d-none" name="link" id="page_editor" rows="2"></textarea>
                        @endif
                    </div>
                    <div class="col-md-12" style="margin-top: 10px;">
                        <div class="form-group row{{ $errors->has('photo') ? ' has-danger' : '' }}">
                            <div class="col-lg-2">
                                <label for="code" class="col-form-label">Upload Banner Image *</label>
                            </div>

                            <div class="col-lg-5">
                                <input class="form-control{{ $errors->has('photo') ? ' is-invalid' : '' }}"
                                       type="file" id="photo" name="photo" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

                <br>

                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="sumbit" class="btn btn-primary" id="btnOrderNoticeSubmit">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <div class="row" style="margin-top: 50px;">
        <div class="col-md-12">
            @if(count($records) == 0)
            <p>No Natification Bannar Added</p>
            @endif
            @foreach($records as $record)
            <section class="banner_area common_banner clearfix banner-item-container">
                <div class="container container_full_width_mobile">
                    <div class="row">
                        <div class="col-md-12 custom_padding_9">
                            @if($record->image_path == '')
                                <div class="banner_top_black" style="color: white;">
                                @if($record->details == '')
                                <p></p>
                                @else
                                <?php echo $record->details; ?>
                                @endif
                                </div>
                            @else
                            <div class="banner_top" style="background-image: url('{{asset($record->image_path)}}')">
                                @if($record->details == '')
                                <p></p>
                                @else
                                <?php echo $record->details; ?>
                                @endif
                            </div>
                                <a class="text-danger btnRemove" data-type="1" data-id="{{ $record->id }}">Remove</a>
                            @endif

                        </div>
                    </div>
                </div>
            </section>
            @endforeach
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
                    <h4 class="modal-title text-white" id="editModal">Edit Notification Bannar</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" enctype="multipart/form-data" id="form" method="post" action="{{ route('admin_notification_banner_add_post') }}">
                        @csrf

                        <div class="form-group row{{ $errors->has('photo') ? ' has-danger' : '' }}">
                            <div class="col-lg-4">
                                <label for="code" class="col-form-label">Image *</label>
                            </div>

                            <div class="col-lg-8">
                                <input class="form-control{{ $errors->has('photo') ? ' is-invalid' : '' }}"
                                       type="file" id="photo" name="photo" accept="image/*" required="">
                            </div>
                        </div>

                        @if ($errors->has('photo'))
                            <div class="form-control-feedback">{{ $errors->first('photo') }}</div>
                        @endif

                        <div class="form-group row{{ $errors->has('link') ? ' has-danger' : '' }}">
                            <div class="col-lg-4">
                                <label for="link" class="col-form-label">Notification Massage *</label>
                            </div>

                            <div class="col-lg-8">
                                @if(count($records) > 0)
                                <textarea class="d-none" name="link" id="page_editor_2" rows="2">{{json_decode($records)[0]->details}}</textarea>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <input type="submit" class="btn btn-primary" value="Update">
                            </div>
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
    <script src="//cdn.ckeditor.com/4.6.2/standard/ckeditor.js"></script>
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
                    url: "{{ route('admin_notification_banner_delete') }}",
                    data: { id: selectedId }
                }).done(function( msg ) {
                    location.reload();
                });
            });

            // Edit
            $('.btnEdit').click(function () {
                var id = parseInt($(this).data('id'));
                // selectedId = id;

                // $.each(banners, function (i, img) {
                //     if (img.id == id)
                //         banner = img;
                // });

                // $('#modal_url').val(banner.url);
                $('#editModal').modal('show');
            });

            $('#modalBtnEdit').click(function () {
                var url = $('#modal_url').val();

                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_top_banner_edit_post') }}",
                    data: { id: selectedId, url: url }
                }).done(function( msg ) {
                    location.reload();
                });
            });
        })
        var options = {
            filebrowserImageBrowseUrl: '{{ url('laravel-filemanager') }}?type=Images',
            filebrowserImageUploadUrl: '{{ url('laravel-filemanager') }}/upload?type=Images&_token=',
            filebrowserBrowseUrl: '{{ url('laravel-filemanager') }}?type=Files',
            filebrowserUploadUrl: '{{ url('laravel-filemanager') }}?type=Files&_token='
        };
        CKEDITOR.replace('page_editor', options);
        CKEDITOR.replace('page_editor_2', options);
    </script>
@stop
