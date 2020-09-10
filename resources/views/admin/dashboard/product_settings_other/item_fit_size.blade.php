@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
@stop

@section('content')


        <div class="col-md-12" >
            <div id="cartSuccessMessage">
                <div class="success_message custom_message_row">
                    <div id="message" style="color: #0c951f" >{{ Session::get('message') }}</div>
                </div>
            </div>

            <br>

            <form class="form-horizontal" enctype="multipart/form-data" id="form"
                  method="post" action="{{ route('admin_item_fit_size_add') }}">
                @csrf
                <div class="form-group row">
                    <div class="col-lg-2">
                        <label for="fit_size_text" class="col-form-label">Text *</label>
                    </div>
                    <input type="hidden" value="{{$ItemFitSize->id}}" name="fit_size_id">

                    <div class="col-lg-5">
                        <textarea type="text" id="fit_size_text" class="form-control{{ $errors->has('fit_size_text') ? ' is-invalid' : '' }}"
                                  placeholder="Fit Size" name="fit_size_text"  required> {{ !empty($ItemFitSize->text) ? $ItemFitSize->text : '' }}</textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-lg-12 ">
                        <input type="submit" id="btnSubmit" class="btn btn-primary" value="Submit">
                    </div>
                </div>
            </form>
        </div>



@stop

@section('additionalJS')
    <script>
        $(document).ready(function() {

            setTimeout(function () {
                $('#message').slideUp('slow');
            }, 1500);
        });
        </script>
@stop