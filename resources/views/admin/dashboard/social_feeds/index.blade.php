@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <style type="text/css">
        input.form-control {
            display: block;
            width: 100%;
            border: 0;
            background: #fff;
            border: 1px solid #999;
            padding: 4px 15px;
            font-size: 14px;
            line-height: 18px;
            color: #343434;
            -webkit-appearance: none;
            -moz-appearance: none;
            -ms-progress-appearance: none;
            border-radius: 0;
            margin-bottom: 5px;
            -webkit-border-radius: 0;
        }
    </style>
@stop

@section('content')
<div class="ly_accrodion">
    <div class="ly_accrodion_heading">
        <div id="btnAddNew" class="ly_accrodion_title open_acc" data-toggle="accordion" data-target="#addShipMethod" data-class="accordion">
            <span id="addEditTitle">Add / Update Social Feeds</span>
        </div>
    </div>
    <div class="accordion_body default_accrodion  open" id="addShipMethod" style="">
        
        @if ( session()->has('message') )
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
        @endif
        
        <form class="form-horizontal" id="form" method="post" action="{{ route('admin_social_feed_add_post') }}">
            @csrf


            <div class="form_row">
                <div class="label_inline required width_150p {{ $errors->has('name') ? ' has-danger' : '' }}">
                    <label for="courier" class="col-form-label">Instagram Feeds : </label>
                </div>
                <div class="form_inline">
                    <input type="hidden" name="instagram" value="instagram">
                    <input type="text" id="" class="form_global" placeholder="Access Token" name="instagram_access_token" value="<?php echo $data_array['instagram']['access_token']?>">
                </div>
            </div>

            <div class="text_right m15">
                <div class="display_inline mr_0">
                    <button type="submit" class="ly_btn  btn_blue min_width_100p " id="btnSubmit">Save or update</button>
                </div>
            </div>
        </form>

    </div>
</div>
@stop

@section('additionalJS')
    
@stop