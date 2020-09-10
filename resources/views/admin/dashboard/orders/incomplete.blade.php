@extends('admin.layouts.main')

@section('content')
<style>
.gmail_quote .content_body{
    margin:0px auto;
}
.cke_contents.cke_reset{
    min-height:500px;
}
</style>
    <div class="table-responsive" xmlns:http="http://www.w3.org/1999/xhtml">
        <table class="table">
            <thead>
            <tr>
                <th>Last Updated</th>
                <th>Company Name</th>
                <th>Email Address</th>
                <th>Details</th>
                <th>Notification</th>
                <th>Amount</th>
            </tr>
            </thead>

            <tbody>
            @foreach($orders as $order)  
                <tr>
                    <td>{{ date('F d, Y g:i:s a', strtotime($order['updated_at'])) }}</td>
                    <td>{{ $order['company']}}</td>
                    <td>{{ $order['email']}}  </td>
                    <td>
                        <a class="text-primary"
                           href="{{ route('admin_incomplete_order_detail', ['order' => $order['user_id']]) }}">View
                            Detail</a>
                    </td>
                    <td>
                        <div id="mailbody{{$order['user_id']}}" style="display: none">

                            <blockquote class="gmail_quote" style="margin:0px 0px 0px 0.8ex;border-left:1px solid rgb(204,204,204);padding-left:1ex"><u></u>
                                <div>
                                    <div style=" " class="content_body">
                                        <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:854px">
                                            <tbody>
                                                <tr>
                                                    <td style="direction:ltr;font-size:0px;padding:0px 34.1563px;text-align:center;vertical-align:top">
                                                        <div style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:785.688px; margin:0px auto;">
                                                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="vertical-align:top;padding:0px; text-align:center;">
                                                                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td align="center" style="font-size:0px;padding:100px 0px 10px;word-break:break-word">
                                                                                            <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0px">
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td style="width:149px">
                                                                                                            <div><img width="150px" src="{{ asset($black_logo_path) }}">
                                                                                                                <br>
                                                                                                            </div>
                                                                                                            <br>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td align="center"  style="font-size:0px;padding:0px;word-break:break-word">
                                                                                            <div style="font-weight:bold;font-family:Arial;color:rgb(0,0,0);font-size:20px;line-height:20px;margin-top:50px">Incomplete Order</div>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size:0px;padding:20px 0px 0px;word-break:break-word">
                                                                                            <p style="border-top:1px solid rgb(221,221,221);margin:0px auto;width:785px">Hi {{$order['company']}}</p>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size:0px;padding:20px 0px 0px;word-break:break-word">
                                                                                            <p style="border-top:1px solid rgb(221,221,221);margin:0px auto;width:785px">Do you still want these?</p>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div style=" ">
                                        <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:854px">
                                            <tbody>
                                                <tr>
                                                    <td style="direction:ltr;font-size:0px;padding:0px 34.1563px;text-align:center;vertical-align:top">
                                                        <div  style="font-size:13px;text-align:center;direction:ltr;display:inline-block;vertical-align:top;width:785.688px">
                                                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="vertical-align:top;padding:0px">
                                                                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td align="center"  style="font-size:0px;margin:0px auto;padding:0px;word-break:break-word">
                                                                                            <div style="font-family:Arial;font-size:14px;color:rgb(0,0,0);line-height:19px">Check out your shopping cart &nbsp;
                                                                                                <br>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td align="center"  style="font-size:0px;margin:0px auto;padding:0px;word-break:break-word">
                                                                                            <div style="font-family:Arial;font-size:14px;color:rgb(0,0,0);line-height:19px">*Offer valid for a limited time. Visit us at wellmadeusa.com for more details.&nbsp;
                                                                                                <br>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td align="center"  style="font-size:0px;padding:0px;word-break:break-word">
                                                                                            <div style=" margin:0px auto;font-family:Arial;font-size:14px;color:rgb(0,0,0);line-height:19px">If you have any questions, feel free to contact me. Thanks you.&nbsp;
                                                                                                <br>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </blockquote>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family:Roboto,RobotoDraft,Helvetica,Arial,sans-serif;font-size:13px">
                                <tbody>
                                    @foreach($cartItems[$order['user_id']] as $item)
                                    <tr>
                                        <td style="text-align:center">
                                            @if (sizeof($item->item->images) > 0)
                                                <img height="auto" src="{{ asset($item->item->images[0]->image_path) }}" width="240" style="outline:none;object-fit:contain;border:0px;display:block;height:auto;width:240px; margin-left: auto; margin-right: auto;" tabindex="0">
                                            @else
                                                <img height="auto" src="{{ asset('images/no-image.png') }}" width="240" style="outline:none;object-fit:contain;border:0px;display:block;height:auto;width:240px; margin-left: auto; margin-right: auto;" tabindex="0">
                                            @endif
                                            <div class="a6S" style="text-align: center; margin-top: 50px;">
                                                <div style="font-family:Arial;font-size:14px;line-height:20px;text-transform:uppercase;color:rgb(0,0,0)">
                                                    {{$item->item->name}}
                                                </div>
                                                <div style="font-family:Arial;font-size:10px;line-height:20px;text-transform:uppercase;color:rgb(0,0,0)">
                                                    {{ $item->item->style_no }}
                                                </div>
                                                <div style="font-family:Arial;font-size:10px;line-height:20px;text-transform:uppercase;color:rgb(0,0,0)">
                                                    ${{ sprintf('%0.2f', $item->item->price) }}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tbody>
                                    <tr>
                                        <td style="vertical-align:top;padding:0px">
                                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                <tbody>
                                                    <tr>
                                                        <td align="center"  style="text-align:center;font-size:0px;padding:0px;word-break:break-word;margin:0px !important">
                                                            <p>from wellmadeusa result@wellmadeusa.com</p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center"  style="text-align:center;font-size:0px;padding:0px;word-break:break-word;margin:0px !important">
                                                            <p>wellmadeusa</p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center"  style="text-align:center;font-size:0px;padding:0px;word-break:break-word;margin:0px !important">
                                                            <p>wellmadeusa.com</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>



                        </div>
                        <a  data-userid="{{$order['user_id']}}" class="btnSend" href="#" data-recipient="{{ $order['email']}}" data-name="{{$order['company']}}"  >
                            <i class="fa fa-bell"></i> 
                            @if($order['status'] == 1) Sent @else Send @endif
                        </a>
                    </td>
                    <td>${{ sprintf('%0.2f', $order['total']) }}</td>
                    
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination_wrapper p10 pt_0">
        <ul class="pagination">
            <li><button class="ly_btn p1_first{{ $result->currentPage() > 1 ?  ' btn_paginate' : ''}}"{{ $result->currentPage() == 1 ?  ' disabled' : ''}}>| <i class="fas fa-chevron-left"></i></button></li>
            <li>
                <button class="ly_btn p1_prev{{ $result->currentPage() > 1 ?  ' btn_paginate' : ''}}"{{ $result->currentPage() == 1 ?  ' disabled' : ''}}> <i class="fas fa-chevron-left"></i> PREV</button>
            </li>
            <li>
                <div class="pagination_input">
                    <input type="number" min="1" max="{{ $result->lastPage() }}" class="form_global p1" value="{{ $result->currentPage() }}"> of {{ $result->lastPage() }}
                </div>
                <div class="pagination_btn">
                    <button class="ly_btn switch_page">GO</button>
                </div>
            </li>
            <li><button class="ly_btn p1_next{{ $result->currentPage() < $result->lastPage() ?  ' btn_paginate' : ''}}"{{ $result->currentPage() == $result->lastPage() ?  ' disabled' : ''}}>  NEXT <i class="fas fa-chevron-right"></i></button></li>
            <li>
                <button class="ly_btn p1_last{{ $result->currentPage() < $result->lastPage() ?  ' btn_paginate' : ''}}"{{ $result->currentPage() == $result->lastPage() ?  ' disabled' : ''}}> <i class="fas fa-chevron-right"></i> |</button>
            </li>
        </ul>
    </div>

    <div class="modal" data-modal="modalSendNotification">
        <div class="modal_overlay" data-modal-close="modalSendNotification"></div>
        <div class="modal_inner">
            <input type="hidden" id="userid" value="">
            <div class="modal_wrapper modal_650p">
                <div class="item_list_popup">
                    <div class="modal_header display_table">
                        <span class="modal_header_title">Mail Send</span>
                        <div class="float_right">
                            <span class="close_modal" data-modal-close="modalSendNotification"></span>
                        </div>
                    </div>
                    <div class="modal_content">
                        <div class="ly-wrap-fluid">
                            <div class="ly-row">
                                <div class="ly-12">
                                    <div class="form_row">
                                        <div class="form_inline">
                                            <textarea rows="2" id="page_editor" class="d-none form_global mailbody"></textarea>
                                        </div>
                                    </div>

                                    <div class="form_row">
                                        <div class="label_inline required width_150p">
                                            Sender
                                        </div>
                                        <div class="form_inline">
                                            <input type="email" id="sender" value="info@wellmadeusa.com" name="sender" class="form_global">
                                        </div>
                                    </div>
                                    <div class="form_row">
                                        <div class="label_inline required width_150p">
                                            Recipient
                                        </div>
                                        <div class="form_inline">
                                            <input type="email" id="recipient" value="" name="recipient" class="form_global">
                                        </div>
                                    </div>
                                    <div class="form_row">
                                        <div class="label_inline required width_150p">
                                            Subject
                                        </div>
                                        <div class="form_inline">
                                            <input name="subject" id="subject" value="[wellmadeusa] You have items in your cart." class="form_global">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ly-row">
                                <div class="ly-12">
                                    <div class="display_table m15">
                                        <div class="float_right">
                                            <button class="ly_btn btn_danger width_150p " data-modal-close="modalSendNotification">Close</button>
                                            <a id="modalBtnSendMail" class="ly_btn btn_blue width_150p text_center" download>Send Confirm</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    {{--<script type="text/javascript" src="{{ asset('plugins/ckeditor/ckeditor.js') }}?id={{ rand() }}"></script>--}}
    <script src="//cdn.ckeditor.com/4.6.2/full/ckeditor.js"></script>

    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var selectedId;

            $('.btnSend').click(function () {
                var userId = $(this).data('userid');
                $("#userid").val(userId);
                var mailBody = $('#mailbody'+userId).html();
                CKEDITOR.instances['page_editor'].setData(mailBody);
                $('#recipient').val($(this).data('recipient'));
                var targeted_modal_class = 'modalSendNotification';
                $('[data-modal="' + targeted_modal_class + '"]').addClass('open_modal');
                selectedId = $(this).data('id');
            });

            $('#modalBtnSendMail').click(function () {
                var recipient =  $('#recipient').val();
                var subject =  $('#subject').val();
                var sender =  $('#sender').val();
                var userid =  $("#userid").val();
                var mailbody =   CKEDITOR.instances['page_editor'].getData();
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_incomplete_order_send_mail') }}",
                    data: {sender: sender,recipient:recipient,subject:subject,mailbody:mailbody,userid:userid}
                }).done(function (msg) {
                    var targeted_modal_class = 'modalSendNotification';
                    $('[data-modal="' + targeted_modal_class + '"]').removeClass('open_modal');

                    // location.reload();
                });
            });
        });
    </script>
    <script>
        $(function () {
            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);

            $('.switch_page').click(function() {
                paginate_change('p1', Number($('.p1').val()))
            });

            $('.p1_first').click(function() {
                paginate_change('p1', 1);
            });

            $('.p1_prev').click(function() {
                paginate_change('p1', Number('{{ $result->currentPage() }}') - 1);
            });

            $('.p1_next').click(function() {
                paginate_change('p1', Number('{{ $result->currentPage() }}') + 1);
            });

            $('.p1_last').click(function() {
                paginate_change('p1', Number('{{ $result->lastPage() }}'));
            });
        });

        function paginate_change(key = '', value = 1) {
            var p1 = $('.p1').val();

            switch (key) {
                case 'p1':
                    p1 = value;
                    break;
            }

            var currentLocation = String(window.location);
            var switchPageUrl = currentLocation.split('?')[0] + '?page=' + p1;

            if((currentLocation.split('?')[1])) {

                if((currentLocation.split('?')[1]).search('page=') >= 0) {
                } else {
                    switchPageUrl = currentLocation.split('?')[0] + '?' + currentLocation.split('?')[1] + '&page=' + p1;
                }
                if((currentLocation.split('?')[1]).search('&page=') > 0) {
                    switchPageUrl = currentLocation.split('?')[0] + '?' + (currentLocation.split('?')[1]).split('&page=')[0] + '&page=' + p1;
                }
            }
            window.location = switchPageUrl;
        }
        //var pageEditor = CKEDITOR.replace( 'page_editor' );

        var options = {
            filebrowserImageBrowseUrl: '{{ url('laravel-filemanager') }}?type=Images',
            filebrowserImageUploadUrl: '{{ url('laravel-filemanager') }}/upload?type=Images&_token=',
            filebrowserBrowseUrl: '{{ url('laravel-filemanager') }}?type=Files',
            filebrowserUploadUrl: '{{ url('laravel-filemanager') }}?type=Files&_token='
        };

        CKEDITOR.replace('page_editor', options);
    </script>
@stop
