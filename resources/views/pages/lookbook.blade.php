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
            @if(count($items)>0)
            <div class="row"> 
                <div class="col-md-12"> 
                    <div class="static_page_wrapper clearfix">  
                        <div class="change_season">
                           <select name="season" id="seasonTriger">
                               @foreach($seasons as $season) 
                                   <option value="{{$season->id}}" @if($season->default ==1) selected @endif>{{$season->name}}</option>
                               @endforeach
                           </select>
                        </div>
                        <div class="scheduler_outer lookbook_slider" id="single_product_img">
                            <div class=" LookBook owl-carousel"> </div>
                        </div> 
                    </div> 
                </div>
                @endif
                 
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
            var defaultseason = $("#seasonTriger").val();
            slidercall(defaultseason)
            $("#seasonTriger").change(function(){
                var id = $(this).val(); 
                slidercall(id)
            });

           function slidercall(id){
            $.ajax({
                method: "POST",
                url: "{{ route('select_lookbook_slider') }}",
                data: { id: id },
            }).done(function( data ) {
                console.log(data)
                let lookbookslider_id = document.getElementById("single_product_img");
                lookbookslider_id.outerHTML = '<div class="scheduler_outer lookbook_slider" id="single_product_img"><div class=" LookBook owl-carousel"></div></div>';

                $.each(data.items, function(i, e) {   
                    var mob_slider = `<div class="scheduler_inner">${e.details}</div> `; 
                    $('.LookBook').append(mob_slider); 
                });
                var LookBook = jQuery(".LookBook");
                LookBook.owlCarousel({
                    loop: true,
                    margin: 10,
                    nav:true,
                    navText : ["<i class='fas fa-angle-left'></i>","<i class='fas fa-angle-right'></i>"],
                    responsive: {

                        0: {

                            items: 1
                        },
                        600: {

                            items: 1
                        },
                        768: {
                            items: 1
                        },
                        1000: {

                            items: 1
                        }
                    }
                });
            });
           }
        });
    </script>


@stop
