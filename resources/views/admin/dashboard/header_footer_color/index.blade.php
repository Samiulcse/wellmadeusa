@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('themes/back/css/components.css') }}" rel="stylesheet">
@stop

@section('content')
    <div class="row">
        <div class="col-md-12" style="border: 1px solid black">
            <h3><span id="addEditTitle">Add / Update Color</span></h3>

            @if ( session()->has('message') )
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
            @endif

            <form class="form-horizontal" id="form" method="post" action="{{ route('admin_header_footer_color_post') }}">
                @csrf

                <div class="form-group row{{ $errors->has('name') ? ' has-danger' : '' }}">
                    <div class="col-lg-2">
                        <label for="name" class="col-form-label">Header Background Color</label>
                    </div>

                    <div class="col-lg-5">
                        <input type="text" id="header_color" class="jscolor form-control{{ $errors->has('header_color') ? ' is-invalid' : '' }}"
                            placeholder="#123456 or color name" name="header_color" value="{{ old('header_color') == '' ? isset($metaSettingsHeaderBGColor->meta_value) ? $metaSettingsHeaderBGColor->meta_value : '' : '' }}">
                    </div>
                </div>
                
                <div class="form-group row{{ $errors->has('name') ? ' has-danger' : '' }}">
                    <div class="col-lg-2">
                        <label for="name" class="col-form-label">Header Font Color</label>
                    </div>

                    <div class="col-lg-5">
                        <input type="text" id="header_color" class="jscolor form-control{{ $errors->has('header_font_color') ? ' is-invalid' : '' }}"
                            placeholder="#123456 or color name" name="header_font_color" value="{{ old('header_font_color') == '' ? isset($metaSettingsHeaderFontColor->meta_value) ? $metaSettingsHeaderFontColor->meta_value : '' : '' }}">
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('name') ? ' has-danger' : '' }}">
                    <div class="col-lg-2">
                        <label for="name" class="col-form-label">Footer Background Color</label>
                    </div>

                    <div class="col-lg-5">
                        <input type="text" id="footer_color" class="jscolor form-control{{ $errors->has('footer_color') ? ' is-invalid' : '' }}"
                            placeholder="#123456 or color name" name="footer_color" value="{{ old('footer_color') == '' ? isset($metaSettingsFooterBGColor->meta_value) ? $metaSettingsFooterBGColor->meta_value : '' : '' }}">
                    </div>
                </div>
                
                <div class="form-group row{{ $errors->has('name') ? ' has-danger' : '' }}">
                    <div class="col-lg-2">
                        <label for="name" class="col-form-label">Footer Font Color</label>
                    </div>

                    <div class="col-lg-5">
                        <input type="text" id="footer_font_color" class="jscolor form-control{{ $errors->has('footer_font_color') ? ' is-invalid' : '' }}"
                            placeholder="#123456 or color name" name="footer_font_color" value="{{ old('footer_font_color') == '' ? isset($metaSettingsFooterFontColor->meta_value) ? $metaSettingsFooterFontColor->meta_value : '' : '' }}">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-lg-12 text-right">
                        <input type="submit" id="btnSubmit" class="btn btn-primary" value="Submit">
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('additionalJS')
<script src="{{ asset('plugins/jscolor/jscolor.js') }}"></script>

@stop