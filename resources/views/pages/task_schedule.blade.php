@extends('layouts.home_layout')
@section('additionalCSS')
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
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
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-md-6">
                    <div class="static_page_wrapper clearfix"> 
                        @if(!empty($content)) {!! $content !!} @endif
                    </div>
                </div>
                <div class="col-md-6">
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
    <!-- =========================
        END REGISTER SECTION
    ============================== -->
@endsection
@section('additionalJS')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
<script src="{{asset('themes/front/js/fullcalendar.min.js')}}"></script>
    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>


@stop
