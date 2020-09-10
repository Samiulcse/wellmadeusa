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
            <form class="form-horizontal" enctype="multipart/form-data" id="form" method="post" action="{{ route('admin_footer_banner_add_post') }}">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <textarea class="d-none" name="page_editor_1" id="page_editor_1" rows="2">{{$data['text_editor_1']}}</textarea>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-12">
                        <textarea class="d-none" name="page_editor_2" id="page_editor_2" rows="2">{{$data['text_editor_2']}}</textarea>
                    </div>
                </div>

                <br>

                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="sumbit" class="btn btn-primary" id="btnOrderNoticeSubmit">Save or Update</button>
                    </div>
                </div>
            </form>
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
            CKEDITOR.config.allowedContent = true;
            CKEDITOR.replace('page_editor_1', options);
            CKEDITOR.replace('page_editor_2', options);
    </script>
@stop
