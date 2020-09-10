@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
@stop

@section('content')
    <form action="{{ route('admin_page_save', ['id' => $page->page_id]) }}" method="POST">
        @csrf

        <div class="form_row">
            <div class="label_inline required width_150p">
                Meta Title
            </div>
            <div class="form_inline">
                <input type="text" class="form_global{{ $errors->has('title') ? ' is-invalid' : '' }}"
                        placeholder="Title" name="title" value="{{ empty(old('title')) ? ($errors->has('title') ? '' : $meta->title) : old('title') }}">
            </div>
        </div>

        <div class="form_row">
            <div class="label_inline required width_150p">
                Meta Description
            </div>
            <div class="form_inline"> 
                <input type="text" class="form_global{{ $errors->has('description') ? ' is-invalid' : '' }}"
                    placeholder="Description" name="description" value="{{ empty(old('description')) ? ($errors->has('description') ? '' : $meta->description) : old('description') }}">
            </div>
        </div>

        <div class="form_row">
            <div class="form_inline">
                <textarea class="d-none" name="page_editor" id="page_editor" rows="2">{{ $page->content }}</textarea>
            </div>
        </div>
        
        @if($page_title == 'Page/Meta - Home Page Custome Section2')
        <div class="form_row">
            <div class="label_inline width_150p">
                Video URL:
            </div>
            <div class="form_inline">
                <input type="text" class="form_global{{ $errors->has('url') ? ' is-invalid' : '' }}"
                        placeholder="Video Url" name="url" value="{{ empty(old('url')) ? ($errors->has('url') ? '' : $page->url) : old('url') }}">
            </div>
        </div>
        @endif

        <br>
        <div class="create_item_bottom text_right m15">
            <div class="display_inline  mr_0">
                <input class="ly_btn  btn_blue min_width_100p" type="submit" value="SAVE">
            </div>
        </div>
    </form>
@stop

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    {{--<script type="text/javascript" src="{{ asset('plugins/ckeditor/ckeditor.js') }}?id={{ rand() }}"></script>--}}
    <script src="//cdn.ckeditor.com/4.6.2/full/ckeditor.js"></script>
    <script>
        $(function () {
            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);

        });

        //var pageEditor = CKEDITOR.replace( 'page_editor' );
        var options = {
            filebrowserImageBrowseUrl: '{{ url('laravel-filemanager') }}?type=Images',
            filebrowserImageUploadUrl: '{{ url('laravel-filemanager') }}/upload?type=Images&_token=',
            filebrowserBrowseUrl: '{{ url('laravel-filemanager') }}?type=Files',
            filebrowserUploadUrl: '{{ url('laravel-filemanager') }}?type=Files&_token=',
            height : 500 
        };

        CKEDITOR.config.allowedContent = true;
        CKEDITOR.replace('page_editor', options);
    </script>
@stop
