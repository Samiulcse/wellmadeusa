@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
@stop

@section('content')

<div class="ly_accrodion">
    <div class="ly_accrodion_heading">
        <div id="btnAddNew" class="ly_accrodion_title open_acc" data-toggle="accordion" data-target="#addShipMethod" data-class="accordion">
            <span id="addEditTitle">Add / Update Social links</span>
        </div>
    </div>
    <div class="accordion_body default_accrodion  open" id="addShipMethod" style="">
        
        @if ( session()->has('message') )
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
        @endif

        <form class="form-horizontal" id="form" method="post" action="{{ route('admin_social_links_add_post') }}">
            @csrf

            <div class="form_row">
                <div class="label_inline required width_150p {{ $errors->has('facebook') ? ' has-danger' : '' }}">
                    <label for="courier" class="col-form-label">Facebook Link</label>
                </div>
                <div class="form_inline">
                    <input type="text" id="facebook" class="form_global{{ $errors->has('facebook') ? ' is-invalid' : '' }}"
                       placeholder="https://facebook.com/YOUR_PROFILE_ID" name="facebook" value="{{ old('facebook') == '' ? isset($socialLinks[0]->facebook) ? $socialLinks[0]->facebook : '' : '' }}">               
                </div>
            </div> 

            {{-- <div class="form-group row{{ $errors->has('name') ? ' has-danger' : '' }}">
               <div class="col-lg-2">
                   <label for="name" class="col-form-label">Pinterest Link</label>
               </div>

               <div class="col-lg-5">
                   <input type="text" id="pinterest" class="form-control{{ $errors->has('pinterest') ? ' is-invalid' : '' }}"
                       placeholder="https://www.pinterest.com/YOUR_PROFILE_ID" name="pinterest" value="{{ old('pinterest') == '' ? isset($socialLinks[0]->pinterest) ? $socialLinks[0]->pinterest : '' : '' }}">
               </div>
            </div>     --}}
            
            <div class="form_row">
                <div class="label_inline required width_150p {{ $errors->has('twitter') ? ' has-danger' : '' }}">
                    <label for="courier" class="col-form-label">Twitter Link</label>
                </div>
                <div class="form_inline">
                    <input type="text" id="twitter" class="form_global{{ $errors->has('twitter') ? ' is-invalid' : '' }}"
                        placeholder="https://twitter.com/" name="twitter" value="{{ old('twitter') == '' ? isset($socialLinks[0]->twitter) ? $socialLinks[0]->twitter : '' : '' }}">
                </div>
            </div>

            <div class="form_row">
                <div class="label_inline required width_150p {{ $errors->has('instagram') ? ' has-danger' : '' }}">
                    <label for="courier" class="col-form-label">Polagram Instagram</label>
                </div>
                <div class="form_inline">
                    <input type="text" id="instagram" class="form_global{{ $errors->has('instagram') ? ' is-invalid' : '' }}"
                        placeholder="https://www.instagram.com/YOUR_PROFILE_ID" name="instagram" value="{{ old('instagram') == '' ? isset($socialLinks[0]->instagram) ? $socialLinks[0]->instagram : '' : '' }}">
                </div>
            </div>
            <div class="form_row">
                <div class="label_inline required width_150p {{ $errors->has('instagram_baevely') ? ' has-danger' : '' }}">
                    <label for="courier" class="col-form-label">Baevely Instagram</label>
                </div>
                <div class="form_inline">
                    <input type="text" id="instagram_baevely" class="form_global{{ $errors->has('instagram_baevely') ? ' is-invalid' : '' }}"
                        placeholder="https://www.instagram.com/YOUR_PROFILE_ID" name="instagram_baevely" value="{{ old('instagram_baevely') == '' ? isset($socialLinks[0]->instagram_baevely) ? $socialLinks[0]->instagram_baevely : '' : '' }}">
                </div>
            </div>

            <div class="text_right m15">
                <div class="display_inline mr_0">
                    <button type="submit" class="ly_btn  btn_blue min_width_100p " id="btnSubmit">Submit</button>
                </div>
            </div>
        </form>

    </div>
</div>
@stop

@section('additionalJS')
    
@stop