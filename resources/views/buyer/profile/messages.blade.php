<?php use App\Enumeration\OrderStatus; ?>
@extends('layouts.home_layout')

@section('additionalCSS')
        <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
        <link href="{{ asset('themes/front/css/custom.css') }}" rel="stylesheet">
@stop

@section('content')
 


<!-- =========================
      START PAGE TITLE SECTION
  ============================== -->
  @if(Session::has('message'))
    <div id="cartSuccessMessage">
        <div class="success_message custom_message_row">
            <div id="cart_message"><p>{!! session('message') !!}</p></div>
        </div>
    </div>
{{--                    <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('message') !!}</em></div>--}}
@endif
<section class="my_account_area common_top_margin">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-lg-2">
                @include('buyer.profile.menu')
            </div>
            <div class="col-md-9 col-lg-8">

                <div class="table-responsive mt-5 custom_table">
                    <div class="data_table_main_heading clearfix">
                        <ul>
                            <li>Sl</li>
                            <li>Subject</li>
                            <li>Unread</li>
                            <li>Date</li>
                        </ul>
                    </div>
                    <div class="data_table">
                        @forelse($messages as $key=>$message)
                            <div class="data_table_inner">
                                <div class="data_table_inner_head clearfix">
                                    <ul id="{{$message->id}}">
                                        <li class="{{ ($message->reading_status == 0) ? 'bg-read' : '' }}"> {{ $key+1}}</li>
                                        <li class="{{ ($message->reading_status == 0) ? 'bg-read' : '' }}"> {{ $message->subject }}</li>
                                        <li class="{{ ($message->reading_status == 0) ? 'bg-read' : '' }}"> {{($message->reading_status == 0) ? 'Unread' : 'Read' }}</li>
                                        <li class="{{ ($message->reading_status == 0) ? 'bg-read' : '' }}"> {{ $message->created_at->diffForHumans() }}</li>
                                    </ul>
                                </div>
                                <div class="data_table_inner_body">
                                    <h4>Dear {{ $message->recipient}}</h4>
                                    <div class="data_table_inner_heading">
                                        {{ $message->message}}
                                    </div>

                                    <div class="data_table_inner">
                                        <h4>Attachment</h4>
                                        @if(empty($message->attachment1) && empty($message->attachment1) && empty($message->attachment1))
                                            <p class="pt-2">No Attachment</p>
                                        @else
                                            @if(!empty($message->attachment1))
                                                <a href="{{asset('/buyer_message_attachment').'/'.$message->attachment1}}" target="_blank">{{$message->attachment1}}</a><br>
                                            @endif
                                            @if(!empty($message->attachment2))
                                                <a href="{{asset('/buyer_message_attachment').'/'.$message->attachment2}}"  target="_blank">{{$message->attachment2}}</a><br>
                                            @endif
                                            @if(!empty($message->attachment3))
                                                <a href="{{ asset('/buyer_message_attachment').'/'.$message->attachment3}}"  target="_blank">{{$message->attachment3}}</a><br>
                                            @endif
                                        @endif
                                    </div>
                                    <br>
                                    <a
                                            class="btn btn-primary btnEdit"
                                            data-message_id="{{ $message->id }}"
                                            data-message_user_id="{{ $message->user_id }}"
                                            data-message_subject="{{ $message->subject }}"
                                            data-message_recipient="{!!  $message->recipient  !!}"
                                            data-message_sender="{{ $message->sender }}"
                                    >Reply Message
                                    </a>

                                </div>
                            </div>
                        @empty
                            <div>
                                <h5 class="text-center">No Messages</h5>
                            </div>
                        @endforelse
                    </div>

                </div>
                <div class="row">
                    <div class="col-5 col-sm-4 col-lg-5">
                    </div>
                    <div class="col-2 col-sm-2 col-lg-2">
                        <div class="pagination justify-content-center mb-pagination">
                            {{ $messages->links() }}
                        </div>
                    </div>
                    <div class="col-6 col-sm-6 col-lg-6">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="message-modal" tabindex="-1" role="dialog" aria-labelledby="modalLabelLarge"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalLabelLarge">Send Message To Buyer  </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="message-form" action="{{ route('send_message_admin') }}" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="message_user_id" id="message_user_id" value="">
                    <input type="hidden" name="message_id" id="message_id" value="">
                    <div class="form-group row">
                        <label class="col-2 col-form-label">From</label>
                        <div class="col-10">
                            <p id="sender"></p>
                            <input type="hidden" name="message_sender" id="message_sender" value="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">To</label>
                        <div class="col-10">
                            <p id="recipient"></p>
                            <input type="hidden" name="message_recipient" id="message_recipient" value="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2">Title</label>
                        <div class="col-10">
                            <input name="message_subject" id="message_subject" value="" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Message</label>
                        <div class="col-10">
                            <textarea name="message" class="form-control" cols="30" rows="10" placeholder="Write your message here ...." required></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label"></label>
                        <div class="col-10">
                            <p class="font-italic">File type allowed .jpg, .gif, .png, .pdf, .xls, .xlsx</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label"></label>
                        <div class="col-10">
                            <span> <i class="fa fa-paperclip" aria-hidden="true"></i>  File 1</span>
                            <input type="file" id="attachment1" name="attachment1">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label"></label>
                        <div class="col-10">
                            <span> <i class="fa fa-paperclip" aria-hidden="true"></i>  File 2</span>
                            <input type="file" id="attachment2" name="attachment2">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label"></label>
                        <div class="col-10">
                            <span><i class="fa fa-paperclip" aria-hidden="true"></i> File 3</span>
                            <input type="file" id="attachment3" name="attachment3">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="btnMessageSend" class="btn btn-outline-primary" >Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){

        setTimeout(function () {
            $('#cartSuccessMessage').slideUp('slow');
        }, 1500);

        $(".data_table .data_table_inner_head ul").click(function () {
            $(this).parent().parent().siblings().removeClass("active_desc");
            $(this).parent().parent().toggleClass("active_desc");
            $(this).find('li').removeClass('bg-read');
            var message_count = $('#message_count').text();
            if(message_count > 0){
                message_count--;
            }
            $('#message_count').text(message_count);
            var id = $(this).attr('id');
            $.ajax({
                type: "POST",
                url: "{{ route('buyer_unread_message_count') }}",
                data: {"_token": "{{ csrf_token() }}","id": id},
                success: function (response) {

                }
            });

        });
        $('.btnEdit').click(function () {
            var message_id = $(this).data('message_id');
            var message_user_id = $(this).data('message_user_id');
            var message_subject = $(this).data('message_subject');
            var message_recipient = $(this).data('message_recipient');
            var message_sender = $(this).data('message_sender');

            $('#message-modal').modal('show');
            $('#message_id').val(message_id);
            $('#message_user_id').val(message_user_id);
            $('#message_subject').val(message_subject);
            $('#recipient').html(message_sender);
            $('#message_recipient').val(message_sender);
            $('#sender').html(message_recipient);
            $('#message_sender').val(message_recipient);

        });
    });
</script>
@stop

