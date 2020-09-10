@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <style>
        table tbody tr td p img{
            width:200px !important;
        }
    </style>
@stop

@section('content')
<div class="ly_accrodion">
    <div class="ly_accrodion_heading">
        <div id="btnAddNew" class="ly_accrodion_title {{ count($errors) > 0 ? ' open_acc' : ''}}" data-toggle="accordion" data-target="#addschedule" data-class="accordion">
            <span id="addEditTitle">Add New Season</span>
        </div>
    </div>
    <div class="accordion_body default_accrodion  {{ count($errors) > 0 ? ' open' : ''}}" id="addschedule" style="">
        <form action="{{ route('add_new_seson') }}" method="POST" enctype="multipart/form-data">
            @csrf  
            <input type="hidden" id="id" name='id' value="">
            <div class="form_row">
                <div class="label_inline required width_150p">
                    Season Name
                </div>
                <div class="form_inline"> 
                    <select   id="seasonselect" name="season_name" class="form_global">
                        <option value="">Select One</option>
                        @foreach($seasons as $season)
                        <option value="{{$season->id}}">{{$season->name}}</option>
                        @endforeach
                        <option value="add-new">Add New Season</option>
                    </select> 

                    @if ($errors->has('season_name'))
                        <div class="form-control-feedback">{{ $errors->first('season_name') }}</div>
                    @endif
                </div>
            </div>

            <div class="form_row">
                <div class="form_inline">
                    <textarea class="d-none {{ $errors->has('page_editor') ? ' is-invalid' : '' }}" name="page_editor" id="page_editor" rows="2"> </textarea>
                    @if ($errors->has('page_editor'))
                            <div class="form-control-feedback">{{ $errors->first('page_editor') }}</div>
                        @endif
                </div>
            </div> 
            <br>
            <div class="create_item_bottom text_right m15">
                <div class="display_inline  mr_0">
                    <input class="ly_btn  btn_blue min_width_100p" type="submit" value="SAVE">
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal @if ($errors->has('name')) open_modal  @endif" data-modal="modalSendNotification" id="sessonmodal" >
        <div class="modal_overlay" data-modal-close="modalSendNotification"></div>
        <div class="modal_inner">
            <input type="hidden" id="userid" value="">
            <div class="modal_wrapper modal_650p">
                <div class="item_list_popup">
                    <div class="modal_header display_table">
                        <span class="modal_header_title">Add New Season</span>
                        <div class="float_right">
                            <span class="close_modal" data-modal-close="modalSendNotification"></span>
                        </div>
                    </div>
                    <div class="modal_content">
                        <div class="ly-wrap-fluid"> 
                            <div class="ly-row">
                                <div class="ly-12">   
                                    <form action="{{ route('add_new_lookbook_season') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" id="seasonid" name="id">
                                        <div class="form_row">
                                            <div class="label_inline required width_150p">
                                                Season Name
                                            </div>
                                            <div class="form_inline">
                                                <input name="name" id="subject" value="" class="form_global {{ $errors->has('name') ? ' is-invalid' : '' }}">
                                                @if ($errors->has('name'))
                                                    <div class="form-control-feedback">{{ $errors->first('name') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form_row">
                                            <div class="label_inline required width_150p">
                                                Season Description
                                            </div>
                                            <div class="form_inline">
                                                <textarea name="description" id="seseandesc" cols="30" rows="10"  class="form_global"></textarea>
                                            </div>
                                        </div>
                                        <div class="form_row"> 
                                            <div class="form_inline">
                                                <input type="submit" value="Submit" name="submit" class="ly_btn btn_blue width_150p text_center float_right">
                                            </div>
                                        </div>
                                    </form> 
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<br>
<br>
<div class="ly-row">
    <div class="ly-5">
        <h2>Season Table</h2>
        <br>
        <table class="table table-striped" id="myTable">
            <thead>
            <tr>
                <th>Name</th>
                <th>Content</th> 
                <th>Current Season</th> 
                <th>Action</th>
            </tr>
            </thead> 
            <tbody id="HomePageSliderItems">
            @foreach($seasons as $season)
                <tr data-id="{{ $season->id }}">
                    <td>{{ $season->name }}</td>
                    <td>{{$season->description}}</td> 
                    <td><input @if($season->default==1) checked @endif  type="radio" name="defaultseason" class="selectDefaultseason" value="{{ $season->id }}"></td>
                    <td> 
                         <a class="link seasonedit" data-name="{{ $season->name }}"    data-id="{{ $season->id }}" data-desc="{{ $season->description }}"   role="button" style="color: blue">Edit</a> |
                        <a class="link SeasonDelete" data-id="{{ $season->id }}" role="button" style="color: red">Delete</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="ly-7"> 
    <h2>Season Events Table</h2>
    <br>
        <table class="table table-striped" id="myTable">
            <thead>
            <tr>
                <th>#</th>
                <th>Season</th>
                <th>Content</th> 
                <th>Action</th>
            </tr>
            </thead> 
            <tbody id="HomePageSliderItems">
            @foreach($items as $item)
                <tr data-id="{{ $item->id }}">
                    <td>{{ $loop->index+1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{!!$item->details!!}</td> 
                    <td>
                        <a class="link taskedit" data-name="{{ $item->name }}" data-seasonid="{{ $item->head }}" data-details="{{ $item->details }}"  data-id="{{ $item->id }}"   role="button" style="color: blue">Edit</a> | 
                        <a class="link taskDelete" data-id="{{ $item->id }}" role="button" style="color: red">Delete</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    
</div>


    
@stop

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script> 
    <script type="text/javascript" src="{{ asset('plugins/sortable/js/Sortable.min.js') }}"></script>
    <script src="//cdn.ckeditor.com/4.6.2/full/ckeditor.js"></script>
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


            $('.taskedit').click(function(){  
                var id = $(this).data('id');
                var name = $(this).data('name');
                var desc = $(this).data('details');  
                var seasonid = $(this).data('seasonid');  
                $('#addschedule').show(); 

                $('#addschedule').find('#season_name').val(name); 
                $('#addschedule').find('#id').val(id); 
                $('#addschedule').find('#seasonselect').append('<option value="'+seasonid+'" selected>'+name+'</option>'); 
                
                
                 
                var oEditor = CKEDITOR.instances.page_editor;  
                var newElement = CKEDITOR.dom.element.createFromHtml( desc, oEditor.document ); 
                oEditor.insertElement( newElement );
    
            }); 

            $('.seasonedit').click(function(){
                var id = $(this).data('id');
                var name = $(this).data('name'); 
                var desc = $(this).data('desc'); 
                $("#sessonmodal").addClass('open_modal');
                $('#seseandesc').val(desc);
                $('#subject').val(name);
                $('#seasonid').val(id);
    
            });

            

        $('.taskDelete').click(function () {
            var id = $(this).data('id');
            $.ajax({
                method: "POST",
                url: "{{ route('admin_banner_delete') }}",
                data: {  id: id }
            }).done(function( msg ) {
                location.reload();
            });
        });

        $(".selectDefaultseason").click(function(){
            var id = $(this).val();
            $.ajax({
                method: "POST",
                url: "{{ route('set_default_season') }}",
                data: {  id: id }
            }).done(function( msg ) {
                toastr.success('Current Season Updated!');
                location.reload();
            });
        });

        $('.SeasonDelete').click(function () {
            var id = $(this).data('id');
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_season_delete') }}",
                    data: {  id: id }
                }).done(function( msg ) {
                    toastr.success('Season Delete Successfully');
                    location.reload();
                });
        });

            var el = document.getElementById('HomePageSliderItems');
            Sortable.create(el, {
                animation: 150,
                dataIdAttr: 'data-id',
                onEnd: function () {
                    updateSort(this.toArray());
                },
            });

            function updateSort(ids) {
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_main_slider_items_sort') }}",
                    data: { ids: ids }
                }).done(function( msg ) {
                    toastr.success('Items sort updated!');
                });
            }

            $("#seasonselect").change(function(){
                var id = $(this).val();
                if(id=='add-new'){
                    var targeted_modal_class = 'modalSendNotification';
                    $('[data-modal="' + targeted_modal_class + '"]').addClass('open_modal');
                }
            });

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
