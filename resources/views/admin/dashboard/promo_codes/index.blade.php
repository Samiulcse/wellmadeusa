@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('themes/admire/css/components.css') }}" rel="stylesheet">

@stop

@section('content')
     

    <div class="row " id="addEditRow">
        <div class="col-md-12" style="border: 1px solid black"> 
            <br>
            <br>

            <form class="form-horizontal" enctype="multipart/form-data" id="form"
                  method="post" action="{{ route('admin_promo_codes_update') }}">
                @csrf 

                <div class="form-group row{{ $errors->has('name') ? ' has-danger' : '' }}">
                    <div class="col-lg-2">
                        <label for="name" class="col-form-label">Name *</label>
                    </div>
                    <input type="hidden" name="id" value="{{$promoCodes->id}}">
                    <div class="col-lg-5">
                        <input type="text" id="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                               placeholder="Store Credit Name" name="name" value="{{ $promoCodes->name }}">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-lg-2">
                        <label for="type" class="col-form-label">Type *</label>
                    </div>

                    <div class="col-lg-5">
                        <label for="typeFixed" class="custom-control custom-radio">
                            <input id="typeFixed" name="type" type="radio" class="custom-control-input"
                                   value="1" @if($promoCodes->type==1) {{'checked'}} @endif>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Fixed Price</span>
                        </label>
                        <label for="typePercentage" class="custom-control custom-radio">
                            <input id="typePercentage" name="type" type="radio" class="custom-control-input" value="2" @if($promoCodes->type==2) {{'checked'}} @endif>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Percentage</span>
                        </label> 
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('amount') ? ' has-danger' : '' }}">
                    <div class="col-lg-2">
                        <label for="amount" class="col-form-label">Credit Amount</label>
                    </div>

                    <div class="col-lg-5">
                        <input type="text" id="amount" class="form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}"
                               placeholder="Credit Amount" name="amount" value="@if(!empty($promoCodes->amount)) {{$promoCodes->amount}} @endif">
                    </div>
                </div> 

                <div class="form-group row{{ $errors->has('credit') ? ' has-danger' : '' }}">
                    <div class="col-lg-2">
                        <label for="credit" class="col-form-label">Amount</label>
                    </div>

                    <div class="col-lg-5">
                        <input type="text" id="credit" class="form-control{{ $errors->has('credit') ? ' is-invalid' : '' }}"
                               placeholder="Max Amount of Order" name="credit" value="@if(!empty($promoCodes->credit)) {{$promoCodes->credit}} @endif">
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('description') ? ' has-danger' : '' }}">
                    <div class="col-lg-2">
                        <label for="description" class="col-form-label">Description</label>
                    </div>

                    <div class="col-lg-5">
                        <input type="text" id="description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"
                               placeholder="Description" name="description" value=" @if(!empty($promoCodes->description)) {{$promoCodes->description}} @endif">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-lg-2">
                        <label for="status" class="col-form-label">Status *</label>
                    </div>

                    <div class="col-lg-5">
                        <label for="statusactive" class="custom-control custom-radio">
                            <input id="statusactive" name="status" type="radio" class="custom-control-input"
                                   value="1" @if($promoCodes->status==1) {{'checked'}} @endif>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Active</span>
                        </label>
                        <label for="statusdeactive" class="custom-control custom-radio">
                            <input id="statusdeactive" name="status" type="radio" class="custom-control-input" value="0" @if($promoCodes->status==0) {{'checked'}} @endif>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">DeActive</span>
                        </label> 
                    </div>
                </div>



                <div class="form-group row">
                    <div class="col-lg-12 text-right"> 
                        <input type="submit" id="btnSubmit" class="btn btn-primary" value="Update">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <br> 
@stop

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }); 

            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);
        })
    </script>
@stop