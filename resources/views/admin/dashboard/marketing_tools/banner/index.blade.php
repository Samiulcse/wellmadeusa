<?php use App\Enumeration\VendorImageType; ?>
@extends('admin.layouts.main')

@section('content')
    <div class="ly-row">
        <div class="ly-12">
            <form action="{{ route('admin_logo_add_post') }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
                @csrf 

                <div class="form_row">
                    <div class="label_inline">
                        <label class="col-form-label">Site Logo:</label>
                    </div>
                    <div class="form_inline">
                        <input type="file" class="form-control{{ $errors->has('logo2') ? ' is-invalid' : '' }}" name="logo2" accept="image/*">

                        @if ($errors->has('logo2'))
                            <div class="form-control-feedback">{{ $errors->first('logo2') }}</div>
                        @endif
                    </div>
                </div>
                <div class="form_row">
                    <div class="label_inline">
                        <label class="col-form-label">Site Logo For Small Device:</label>
                    </div>
                    <div class="form_inline">
                        <input type="file" class="form-control{{ $errors->has('logo') ? ' is-invalid' : '' }}" name="logo" accept="image/*">

                        @if ($errors->has('logo'))
                            <div class="form-control-feedback">{{ $errors->first('logo') }}</div>
                        @endif
                    </div>
                </div>
                <div class="form_row">
                    <div class="label_inline">
                        <label class="col-form-label">No Login Image:</label>
                    </div>
                    <div class="form_inline">
                        <input type="file" class="form-control{{ $errors->has('logo3') ? ' is-invalid' : '' }}" name="logo3" accept="image/*">

                        @if ($errors->has('logo3'))
                            <div class="form-control-feedback">{{ $errors->first('logo3') }}</div>
                        @endif
                    </div>
                </div>
                
                <div class="form_row">
                    <div class="form_inline text_right">
                        <input class="ly_btn  btn_blue min_width_100p" type="submit" value="Submit">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <hr>

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

    <div class="row">
        <table class="table logo_preview">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Preview</th>
                    <th>Upload Date</th> 
                    <th>Action</th> 
                </tr>
            </thead>
            <tbody> 
                @if(!empty($black))
                <tr>
                    <td>@if($black->name=='logo-black') Site Logo @endif</td>
                    <td><img width="200px" src="{{ asset($black->value) }}" alt=""></td>
                    <td>@if(!empty($black->created_at)) {{$black->created_at}} @endif</td>
                    <td><a href="#" class="text-danger btnRemove" data-type="1" data-id="{{ $black->id }}">Remove</a></td>
                </tr>
                @endif
                @if(!empty($white))
                <tr>
                    <td>@if($white->name=='logo-white') Site Logo For Mobile Device @endif</td>
                    <td><img width="200px" src="{{ asset($white->value) }}" alt=""></td>
                    <td>@if(!empty($white->created_at)) {{$white->created_at}} @endif</td>
                    <td><a href="#" class="text-danger btnRemove" data-type="1" data-id="{{ $white->id }}">Remove</a></td>
                </tr>
                @endif
                @if(!empty($defaultItemImage))
                <tr>
                    <td>@if($defaultItemImage->name=='default-item-image') Default Category Item Image @endif</td>
                    <td><img height="150px" width="" src="{{ asset($defaultItemImage->value) }}" alt=""></td>
                    <td>@if(!empty($defaultItemImage->created_at)) {{$defaultItemImage->created_at}} @endif </td>
                    <td><a href="#" class="text-danger btnRemove" data-id="{{ $defaultItemImage->id }}">Remove</a></td>
                </tr>
                @endif
            </tbody>
            
        </table>
    </div> 
@stop

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/sortable/js/Sortable.min.js') }}"></script>
    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var type = '';
            var id = '';

             

            // $('.btnRemove').click(function () {
            //     $('#deleteModal').modal('show'); 
            //     id = $(this).data('id');
            // });

            $('.btnRemove').click(function () {
                var id = $(this).data('id');
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_logo_item_remove') }}",
                    data: { type: type, id: id }
                }).done(function( msg ) {
                    location.reload();
                });
            });

            // ================Sortable==========

            

            function updateSort(ids) {
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_banner_item_sort') }}",
                    data: { ids: ids }
                }).done(function( msg ) {
                    toastr.success('Items sort updated!');
                });
            }

        });
    </script>
@stop