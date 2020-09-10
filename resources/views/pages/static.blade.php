@extends('layouts.home_layout')

{{--@section('breadcrumbs')--}}
{{--    @if (request()->route()->getName() == 'about_us')--}}
{{--        {{ Breadcrumbs::render('about_us') }}--}}
{{--    @elseif (request()->route()->getName() == 'contact_us')--}}
{{--        {{ Breadcrumbs::render('contact_us') }}--}}
{{--    @elseif (request()->route()->getName() == 'privacy_policy')--}}
{{--        {{ Breadcrumbs::render('privacy_policy') }}--}}
{{--    @elseif (request()->route()->getName() == 'return_info')--}}
{{--        {{ Breadcrumbs::render('return_info') }}--}}
{{--    @elseif (request()->route()->getName() == 'shipping')--}}
{{--        {{ Breadcrumbs::render('shipping') }}--}}
{{--    @elseif (request()->route()->getName() == 'billing_shipping')--}}
{{--        {{ Breadcrumbs::render('billing_shipping') }}--}}
{{--    @elseif (request()->route()->getName() == 'faq')--}}
{{--        {{ Breadcrumbs::render('faq') }}--}}
{{--    @endif--}}
{{--@stop--}}

@section('content')
@if(request()->route()->getName() == 'contact_us' || request()->route()->getName() == 'return_info')
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
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="static_page_wrapper clearfix"> 
                                {!! $content !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="contact_us_form static_page_wrapper clearfix"> 
                                <div class="form-horizontal">
                                     <span id="mailsenderrormassage"></span>
                                    <div class="form-group">
                                        <label for="name" class="control-label">Your Name <span>*</span></label> 
                                        <input type="text" class="form-control" id="name" name="name"> 
                                        <span id="nameerror"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="company" class="control-label">Company Name</label> 
                                        <input type="text" class="form-control" id="company"  name="company" "> 
                                    </div>
                                    <div class="form-group">
                                        <label for="email" class="control-label">Email Address <span>*</span></label>
                                        <input type="email" class="form-control" id="email" name="email"  >
                                        <span id="emailerror"></span>
                                    </div> 
                                    <div class="form-group">
                                        <label for="subject" class="control-label">Subject <span>*</span></label>
                                         <input type="text" class="form-control" id="subject" name="subject"  >
                                         <span id="subjecterror"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="subject"  class="control-label">Message <span>*</span></label>
                                        <textarea class="form-control" id="message" name="message"  rows="10"></textarea>
                                        <span id="messageerror"></span>
                                    </div> 
                                    <div class="form-group"> 
                                        <button type="submit" class="btn btn-default messagesend">Submit</button> 
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </section> 

@else 
    <section class="static_page common_top_margin">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="static_page_wrapper clearfix">
                        <h2 class="text-center static_title">{{ $title}}</h2>
                        {!! $content !!}
                    </div>

                </div>
            </div>
        </div>
    </section>
@endif  

@endsection

@section('additionalJS') 
    <script> 
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $(".messagesend").click(function(){
                var name = $.trim($("#name").val());
                var company = $.trim($("#company").val());
                var email = $.trim($("#email").val());
                var subject = $.trim($("#subject").val()); 
                var message = $('textarea#message').val();
                
                if(name == ''){
                    $('#nameerror').html('Please Fill Your Name Field.');
                    return false;
                }
                if(name == ''){
                    $('#nameerror').html('Please Fill Your Name Field.');
                    return false;
                }if(email == ''){
                    $('#emailerror').html('Please Enter a Valid Email Address. ');
                    return false;
                }if(subject == ''){
                    $('#subjecterror').html('Please Enter a Subject.');
                    return false;
                }
                
                if(message == ''){
                     $('#messageerror').html('Please Write Something To Send Mail.');
                     return false;
                }
                $.ajax({
                    method: "POST",
                    url: "{{ route('send_customer_email_to_admin') }}",
                    data: {  
                        name: name,
                        company: company,
                        email: email, 
                        subject: subject,
                        message: message 
                    }
                }).done(function (data) {
                    if(data.success){
                        $('#mailsenderrormassage').html(data.message);
                    }
                });
            });
        });
    </script>
@stop