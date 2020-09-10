@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <style>
        .checkbox-inline {
            display: inline-flex;
        }
    </style>
@stop

@section('content')
    <style>
        .bg-read{
            background: #FCF7F2;
        }
        .data_table_main_heading {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }
        .data_table_main_heading ul li{
            float: left;
            padding: .75rem;
            background: #fff;
            font-weight: bold;
            cursor: pointer;
        }
        .data_table_main_heading ul li:nth-of-type(1){
            width: 10%;
        }
        .data_table_main_heading ul li:nth-of-type(2){
            width: 40%;
        }
        .data_table_main_heading ul li:nth-of-type(3){
            width: 25%;
        }
        .data_table_main_heading ul li:nth-of-type(4){
            width: 25%;
        }
        .data_table {}
        .data_table .data_table_inner{}

        .data_table .data_table_inner{}
        .data_table .data_table_inner_head{
            border-bottom: 1px solid #dee2e6;
        }
        .data_table .data_table_inner.active_desc .data_table_inner_head {
            background: rgb(255, 204, 204, .4);
        }
        .data_table .data_table_inner_head ul li{
            float: left;
            padding: .75rem;
            cursor: pointer;
        }
        .data_table .data_table_inner_head ul li:nth-of-type(1){
            width: 10%;
        }
        .data_table .data_table_inner_head ul li:nth-of-type(2){
            width: 40%;
        }
        .data_table .data_table_inner_head ul li:nth-of-type(3){
            width: 25%;
        }
        .data_table .data_table_inner_head ul li:nth-of-type(4){
            width: 25%;
        }
        .data_table .data_table_inner_body {
            display: none;
        }
        .data_table_inner.active_desc .data_table_inner_body{
            display: block;
            border: 1px solid #ddd;
            padding: 15px;
        }

        .custom_table{
            overflow: hidden !important;
        }

        .pagination{
            padding-top: 10px;
        }
    </style>
    <!-- =========================
      START PAGE TITLE SECTION
  ============================== -->

    <section class="my_account_area">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    @if(Session::has('message'))
                        <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('message') !!}</em></div>
                    @endif
                    <div class="table-responsive mt-5 custom_table">
                        <div class="data_table_main_heading clearfix">
                            <ul>
                                <li>Sl No.</li>
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
                                            <li id="reading_status_{{$message->id}}" class="{{ ($message->reading_status == 0) ? 'bg-read' : '' }}"> {{($message->reading_status == 0) ? 'Unread' : 'Read' }}</li>
                                            <li class="{{ ($message->reading_status == 0) ? 'bg-read' : '' }}"> {{ $message->created_at->diffForHumans() }}</li>
                                        </ul>
                                    </div>
                                    <div class="data_table_inner_body">
                                        <h4>Dear {{ $message->recipient}}</h4>
                                        <div class="data_table_inner_heading">
                                            {{ $message->message}}
                                        </div>
                                        <br>
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

                                        <a class="btn btn-secondary btnReplay" role="button" data-modal-open="message_replay_modal" data-message_id="{{ $message->id }}" data-message_user_id="{{ $message->user_id }}"
                                            data-message_subject="{{ $message->subject }}" data-message_recipient="{{ $message['user']? $message['user']->first_name : '' }}&nbsp; ({{ $message['user'] ? $message['user']['buyer']->company_name : '' }})"
                                           >Reply Message
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div>
                                    <h3 class="text-center">No Messages</h3>
                                </div>
                            @endforelse
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-5 col-sm-4 col-lg-5">
                        </div>
                        <div class="ly-2 col-sm-2 col-lg-2">
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
    <!-- <div class="modal open_modal" id="message-replay-modal" tabindex="-1" role="dialog" aria-labelledby="modalLabelLarge"  aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabelLarge">Send Message To Buyer  </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

            </div>
        </div>
    </div> -->

    <div class="modal" data-modal="message_replay_modal" id="message-replay-modal">
    <div class="modal_overlay" data-modal-close="message_replay_modal"></div>
    <div class="modal_inner">
        <div class="modal_wrapper modal_600p">
            <div class="modal_header display_table white_bg   pa15 pb_0">
                <span class="modal_header_title" id="modalLabelLarge">Send Message To Buyer </span>
                <div class="float_right">
                    <span class="close_modal" data-modal-close="message_replay_modal"></span>
                </div>
            </div>
            <div class="modal_content pl_15 pr_15">
            <form id="message-form" action="{{ route('send_message_buyer') }}" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="user_id" id="message_user_id" value="">
                    <div class="ly-row mb_5">
                        <label class="ly-2 label_inline">From</label>
                        <div class="ly-10">
                            <p id="sender">Well-Made</p>
                            <input type="hidden" name="sender" value="Well-Made">
                        </div>
                    </div>
                    <div class="ly-row mb_5">
                        <label class="ly-2 label_inline">To</label>
                        <div class="ly-10">
                            <p id="recipient"></p>
                            <input type="hidden" name="recipient" id="message_recipient" value="">
                        </div>
                    </div>

                    <div class="ly-row mb_5">
                        <label class="ly-2">Title</label>
                        <div class="ly-10">
                            <input name="topics" id="message_subject" value="" class="form_global">
                        </div>
                    </div>

                    <div class="ly-row mb_5">
                        <label class="ly-2 label_inline">Message</label>
                        <div class="ly-10">
                            <textarea name="message" class="form_global" cols="30" rows="10" placeholder="Write your message here ...." required></textarea>
                        </div>
                    </div>
                    <div class="ly-row mb_5 ">
                        <label class="ly-2 label_inline"></label>
                        <div class="ly-10">
                            <p class="font-italic">File type allowed .jpg, .gif, .png, .pdf, .xls, .xlsx</p>
                        </div>
                    </div>
                    <div class="ly-row mb_5">
                        <label class="ly-2 label_inline"></label>
                        <div class="ly-10">
                            <span> <i class="fa fa-paperclip" aria-hidden="true"></i>  File 1</span>
                            <input type="file" id="attachment1" name="attachment1">
                        </div>
                    </div>
                    <div class="ly-row mb_5">
                        <label class="ly-2 label_inline"></label>
                        <div class="ly-10">
                            <span> <i class="fa fa-paperclip" aria-hidden="true"></i>  File 2</span>
                            <input type="file" id="attachment2" name="attachment2">
                        </div>
                    </div>
                    <div class="ly-row mb_5">
                        <label class="ly-2 label_inline"></label>
                        <div class="ly-10">
                            <span><i class="fa fa-paperclip" aria-hidden="true"></i> File 3</span>
                            <input type="file" id="attachment3" name="attachment3">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="ly-row mb_5">
                     <label class="ly-8 "></label>
                        <div class="ly-4"><button type="submit" id="btnMessageSend" class="ly_btn btn_blue float_right" >Send Message</button></div>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
    @stop
    @section('additionalJS')
    <!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> -->
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script>
        $(function (){

            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);

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
                    url: "{{ route('all_message_status') }}",
                    data: {"_token": "{{ csrf_token() }}","id": id},
                }).done(function( response ) {
                    if (response.success) {
                        $("#reading_status_"+id+"").html('Read')
                    } else {
                        alert(response.message);
                    }
                });
            });

            $('.btnReplay').click(function () {
                var message_id = $(this).data('message_id');
                var message_user_id = $(this).data('message_user_id');
                var message_subject = $(this).data('message_subject');
                var message_recipient = $(this).data('message_recipient');

                $('#message_id').val(message_id);
                $('#message_user_id').val(message_user_id);
                $('#message_subject').val(message_subject);
                $('#recipient').html(message_recipient);
                $('#message_recipient').val(message_recipient);
            });
        });
    </script>
@stop
