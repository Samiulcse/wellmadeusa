@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <form class="form-horizontal" method="post" action="{{ route('admin_meta_save') }}">
                @csrf
                <input type="hidden" name="meta_id" value="{{ $meta->id }}">

                <div class="form-group row{{ $errors->has('title') ? ' has-danger' : '' }}">
                    <div class="col-lg-2">
                        <label for="title" class="col-form-label">Title</label>
                    </div>

                    <div class="col-lg-5">
                        <input type="text" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}"
                               placeholder="Title" name="title" value="{{ empty(old('title')) ? ($errors->has('title') ? '' : $meta->title) : old('title') }}">
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('description') ? ' has-danger' : '' }}">
                    <div class="col-lg-2">
                        <label for="description" class="col-form-label">Description</label>
                    </div>

                    <div class="col-lg-5">
                        <input type="text" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"
                               placeholder="Description" name="description" value="{{ empty(old('description')) ? ($errors->has('description') ? '' : $meta->description) : old('description') }}">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-lg-12 text-right">
                        <input type="submit" class="btn btn-primary" value="SAVE">
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script>
        $(function () {
            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);
        });
    </script>
@stop