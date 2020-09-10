<?php
use App\Enumeration\Role;
use App\Enumeration\Availability;
?>
@extends('layouts.home_layout')
@section('additionalCSS')
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
@stop

@section('content')
    <!-- =========================
        START REGISTER SECTION
    ============================== -->
    <section class="static_page common_top_margin">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="static_page_wrapper clearfix">
                        <h2 class="text-center static_title">{{ $title}}</h2> 
                        <p class="text-danger text-center">Note: Please select a future date or current date for get a appintment.   </p>
                    </div>
                </div>
            </div>
            <div class="row">  
                <div class="col-md-6 offset-md-3 col-12 appintment_calender_outer">
                    <h4>Select a date.</h4>
                    <div class="static_page_wrapper clearfix">
                        @if (\Session::has('success'))
                            <div class="alert alert-success">
                                <p>{{ \Session::get('success') }}</p>
                            </div><br />
                            @endif
                        <div class="panel panel-default">
                                <div class="panel-heading">
                                </div>
                                <div class="panel-body" >
                                    {!! $calendar->calendar() !!}
                                </div>
                            </div>
                        </div>

                        {!! $calendar->script() !!}
                    </div> 
                     
                </div>
            </div>
        </div>
    </section>

 

    <div class="modal fade" id="TimeSelected" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header"> 
                    <h6 class="modal-title" id="appint_date_time">  </h6>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="appinment_time">
                    <div class="row"> 
                        <div class="col-md-12"><h4>Select time.</h4>  <p class="error_msg text-danger"></p></div>
                            <ul class="pm col-md-12">  
                                <?php $i = 1;?>
                                @foreach($AppointmentTime as $date)
                                    <li class="{{$i%2 == 0 ? 'right': 'left'}}">
                                        <div class="custom_radio">
                                            <input class="time" type="radio" id="{{$date->time}}" name="time" value="{{date ('H:i',strtotime($date->time))}}" >
                                            <label for="{{$date->time}}">{{date ('H:i',strtotime($date->time))}}  </label>
                                        </div>
                                    </li>
                                    <?php $i++; ?>
                                @endforeach
                            </ul>
                             
                        </div>
                    </div>
                    <div class="form-group"> 
                        <label for="note"> About Appointment. </label>
                        <input class="form-control" type="text" name="appint_note" id="appint_note">
                        <input class="form-control" type="hidden" name="appoint_time" id="appoint_time">
                        <input class="form-control" type="hidden" name="appoint_date" id="appoint_date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default common_btn btnCheckout" type="button" data-dismiss="modal">Close</button> 
                    <button class="btn btn-default common_btn btnCheckout" id="confirm_appointment" type="button" disabled>Confirm Appointment</button> 
                </div>
            </div>
        </div>
    </div>
    <!-- =========================
        END REGISTER SECTION
    ============================== -->
@endsection
@section('additionalJS')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
<script src="{{asset('themes/front/js/fullcalendar.min.js')}}"></script>
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
            $(".fc-event-container").attr('rowspan', '1');
            $(".fc-past").prop('disabled', true);
            $(document).on('click', '.fc-day-number', function(){
                if('{{ (Auth::check() && Auth::user()->role == Role::$BUYER)}}'){ 
                    var today = $(".fc-day-number.fc-today").data('date') 
                    $(".fc-day-number").removeClass('selected');
                    $(this).addClass('selected'); 
                    
                    var date = $(this).data('date'); 
                    $("#appint_date_time").html();  
                    $("#appoint_time").val();  
                    $("#appoint_date").val(); 
                    $("#appint_note").val(); 

                      
                    $("#appoint_date").val(date); 
                    $("#appint_date_time").html("your Appintment Date and Time : " +date); 
                    $('#TimeSelected').modal('show'); 
                }else{
                    window.location = "{{ route('buyer_login') }}";
                }
                
                
            });
            
            $("#confirm_appointment").click(function(){
                var time = $("input[name='time']:checked").val();   
                var date = $("#appoint_date").val(); 
                var note = $("#appint_note").val(); 
                $.ajax({
                    method: "POST",
                    url: "{{ route('submit_appointment_schedule') }}",
                    data: { time: time, date: date, note: note },
                }).done(function( data ) { 
                    console.log(data)
                    if(data.already == 1){
                        $(".error_msg").html('Someone already got this appintment please select another time/date.'); 
                        toastr.success('Appointment submited Not Successfull.');
                    }else if(data.already == 0 && data.success == 1){
                        $('#TimeSelected').modal('hide');
                        toastr.success('Appointment submited successfully!');
                    }else{
                        toastr.success('Appointment submited Not Successfull.');
                    }
                     
                    
                });
            });

            $(".time").change(function() { 
                var date = $(".fc-day-number.selected").data('date') 

                if($(this).val()){
                    $("#appint_date_time").html("your Appintment Date and Time : " +date+" "+$(this).val()); 
                    $("#confirm_appointment").prop('disabled', false)
                }else{
                    $("#confirm_appointment").prop('disabled', true)
                }  
            });
            
        });
    </script>


@stop
