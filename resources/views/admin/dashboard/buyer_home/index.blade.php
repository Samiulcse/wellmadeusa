@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
@stop

@section('content')
    <div class="ly-row">
        <div class="ly-12">
            <form action="{{ route('admin_buyer_home_save') }}" method="POST">
                @csrf

                <div class="form_row">
                    <div class="ly-12">
                        <textarea class="ckeditor" name="buyer_home" id="buyer_home" rows="10" cols="80">{{ $setting->value}}</textarea>
                    </div>
                </div>

                <br>

                <div class="form_row">
                    <div class="ly-12 text_right">
                        <button class="ly_btn  btn_blue min_width_100p " id="btnOrderNoticeSubmit">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script src="//cdn.ckeditor.com/4.6.2/standard/ckeditor.js"></script>
    <script>
        $(function () {
            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);

        });

        var options = {
            filebrowserImageBrowseUrl: '{{ url('laravel-filemanager') }}?type=Images',
            filebrowserImageUploadUrl: '{{ url('laravel-filemanager') }}/upload?type=Images&_token=',
            filebrowserBrowseUrl: '{{ url('laravel-filemanager') }}?type=Files',
            filebrowserUploadUrl: '{{ url('laravel-filemanager') }}?type=Files&_token='
        };

        CKEDITOR.config.allowedContent = true;
        CKEDITOR.replace('buyer_home', options);
    </script>
@stop
