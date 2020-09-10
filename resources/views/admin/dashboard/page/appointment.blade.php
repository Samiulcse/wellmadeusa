@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet"> 
    <link href="https://cdn.jsdelivr.net/timepicker.js/latest/timepicker.min.css" rel="stylesheet"/>
@stop

@section('content')
<div class="ly_accrodion">
    <div class="ly_accrodion_heading">
        <div id="btnAddNew" class="ly_accrodion_title {{ count($errors) > 0 ? ' open_acc' : ''}}" data-toggle="accordion" data-target="#addPromotion" data-class="accordion">
            <span id="addEditTitle">Add Appointment Time</span>
        </div>
    </div>
    <div class="accordion_body default_accrodion  {{ count($errors) > 0 ? ' open' : ''}}" id="addPromotion" style="">
        <div class="ly-row">
            <div class="ly-6">   
                <form class="form-horizontal" enctype="multipart/form-data" id="form"
                    method="post" action="{{ route('add_new_appoint_time') }}">
                    @csrf 

                    <div class="form_row">
                        <div class="label_inline required width_150p">
                            <label for="promotion_type" class="col-form-label">Time </label>
                        </div>
                        <div class="form_inline"> 
                            <input type="text" name="time"  id="time" class="form_global" placeholder="Time">
                        </div>
                    </div>
                    <div class="form_row">
                        <div class="label_inline required width_150p">
                            <label for="promotion_type" class="col-form-label">note </label>
                        </div>
                        <div class="form_inline">
                            <input type="text" name="note"  id="note" class="form_global" value="{{ old('title') }}"> 
                        </div>
                    </div>
                    <div class="form_row">
                        <div class="label_inline required width_150p"> 
                        </div>
                        <div class="form_inline">
                            <input type="submit" id="submittime" name="Add" class="ly_btn  btn_blue float_right" value="add"> 
                        </div>
                    </div> 
                </form>
            </div>
            <div class="ly-6">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Time</th> 
                        <th>Note</th> 
                        <th>Actions</th>
                    </tr>
                    </thead>

                    <tbody>
                        <?php $i = 1; ?>
                        @foreach($appointTime as $time)
                            <tr>
                                <td>{{$i}}</td> 
                                <td>{{ $time->time}}</td>  
                                <td>{{ $time->note}}</td>  
                                <td><a class="link TimeDelete" data-id="{{ $time->id }}" role="button" style="color: red">Delete</a></td> 
                            </tr>
                            <?php $i++ ?>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="create_item_color">
            <div class="float_right">
                <div class="display_inline">
                    <span id="btnCancel" data-toggle="accordion" data-target="#addPromotion" data-class="accordion" class="accordion_heading" data-class="accordion" id="addPromotionDismiss"><span class="ly_btn btn_danger width_80p " style="text-align:center">Close</span> </span>
                </div>
            </div> 
        </div>
        <br>
    </div>
</div>
<br>
<br>
<div class="ly-row">
        <div class="ly-12">
            <table class="table table-striped">
                <thead>
                <tr> 
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Company</th>
                    <th>Appointment Date</th>
                    <th>Appointment Time</th>
                    <th>Note</th> 
                    <th>Actions</th>
                </tr>
                </thead>

                <tbody> 
                @foreach($appointmets as $appointment)
                    @if(!empty($appointment->user))
                        <tr> 
                            <td>@if(!empty($appointment->user)){{$appointment->user->first_name}} {{ $appointment->user->last_name}} @endif </td>
                            <td>@if(!empty($appointment->user)) {{ $appointment->user->email }} @endif</td>
                            <td>@if(!empty($appointment->user->buyer)) {{ $appointment->user->buyer->company_name }} @endif </td>
                            <td>{{  $appointment->start_date }} </td> 
                            <td>{{  $appointment->desc }}</td> 
                            <td>{{  $appointment->name }}</td> 
                            <td><a class="link apointDelete" data-id="{{ $appointment->id }}" role="button" style="color: red">Delete</a></td> 
                        </tr> 
                    @endif
                @endforeach
                </tbody>
            </table>

            <div class="pagination">
            {{ $appointmets->links() }}
            </div>
        </div>
    </div>
@stop

@section('additionalJS') 
<script src="https://cdn.jsdelivr.net/timepicker.js/latest/timepicker.min.js"></script>
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script> 
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

                var timepicker = new TimePicker('time', {
                lang: 'en',
                theme: 'dark'
                });
                timepicker.on('change', function(evt) {
                
                var value = (evt.hour || '00') + ':' + (evt.minute || '00');
                evt.element.value = value;

                });
            $(".apointDelete").click(function(){
                var id = $(this).data('id');
                $.ajax({
                    method: "POST",
                    url: "{{ route('appointment_delete') }}",
                    data: {  id: id }
                }).done(function( msg ) {
                    location.reload();
                });
            });

            $(".TimeDelete").click(function(){
                var id = $(this).data('id');
                $.ajax({
                    method: "POST",
                    url: "{{ route('appointTime_delete') }}",
                    data: {  id: id }
                }).done(function( msg ) {
                    location.reload();
                });
            });
            

        });

       
    </script>
@stop
