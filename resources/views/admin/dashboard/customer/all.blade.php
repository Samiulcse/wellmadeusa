@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.5.4/css/buttons.dataTables.min.css" rel="stylesheet">
@stop

@section('content')
<div class="outslider_loading" style="display: none;">
    <div class="la-ball-scale-ripple-multiple la-dark la-2x">
        <div></div>
        <div></div>
        <div></div>
    </div>    
</div> 
    <div class="ly-row">
        <div class="ly-12">
            <div class="form-group ly-row item_list_search">
                <form method="get" class="form-inline" role="form" style="display: contents">
                    <div class="ly-2"  >
                        <div class="form-group">
                            <select class="form-control" name="status">
                                <option value="all">All</option>
                                <option value="active">Acitve</option>
                                <option value="inactive">Inactive</option>
                                <option value="verified">Verified</option>
                                <option value="notverified">Not Verified</option>
                            </select>
                        </div>
                    </div>
                    <div class="ly-2">
                        <div class="form-group"> 
                            <input  value="{{ (request()->get('company_name') != null ) ? request()->get('company_name'): '' }}" type="text" class="form-control" name="company_name" placeholder="Company Name">
                        </div>
                    </div>
                    <div class="ly-2">
                        <div class="form-group">
                            <input  value="{{ (request()->get('customer_name') != null ) ? request()->get('customer_name'): '' }}" type="text" class="form-control" name="customer_name" placeholder="Customer Name">
                        </div>
                    </div>
                    <div class="ly-2">
                        <div class="form-group">
                            <button class="btn ly_btn btn_blue" type="submit"><i class="fa fa-search"></i> search</button> &nbsp;
                            <a class="ly_btn btn_blue btn-sm" href="{{route('admin_all_buyer')}}"> <i class="fa fa-arrow-left"></i> back </a>
                        </div>
                    </div>
                </form>
                <div class="ly-2">
                    <div class="form-group">
                        <a class="ly_btn btn_blue btn-sm" href="{{route('admin_all_buyer_export')}}"> <i class="fa fa-file-excel"></i> Export </a>
                    </div>
                </div>
                <!-- import customer -->
                <div class="ly-2">
                    <div class="form-group">
                        <button class="ly_btn  btn_blue min_width_100p" id="btnImport"> <i class="fas fa-file-import"></i> Import from Excel File</button>
                        <!-- <a class="ly_btn  btn_danger min_width_100p" href="{{ asset('files/sample-file.xlsx') }}" target="_blank">Download Sample File</a> -->

                        <form action="{{ route('adminImportCustomer') }}" id="form" method="post" enctype="multipart/form-data">
                            <input class="d_none" type="file" id="file" name="file">
                            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="ly-12">
            <table class="table" id="customer_off">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Company Name</th>
                    <th>Active</th>
                    <th>Verified</th>
                    <th>Block</th>
                    <th>Minimum Require</th>
                    <th>Created At</th>
                    <th>Files</th>
                    <th>Mailing List</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                    @foreach($buyers as $buyer)
                    <tr>
                        <td>@if($buyer->user) {{  $buyer->user->first_name .' '. $buyer->user->last_name  }}@endif</td>
                        <td>@if($buyer->user){{   $buyer->user->email }}@endif</td>
                        <td>@if($buyer->user){{    $buyer->company_name }}@endif</td>
                        <td>
                            <div class="form-check custom_checkbox">
                                <input class="form-check-input status" type="checkbox" id="checkbox-status-{{ $buyer->id }}" value="1" data-id="{{ $buyer->id }}" {{ $buyer->active == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="checkbox-status-{{ $buyer->id }}">
                                    &nbsp;
                                </label>
                            </div>

                            {{--<label class="custom-control custom-checkbox">
                                <input type="checkbox" data-id="{{ $buyer->id }}" class="custom-control-input status" value="1" {{ $buyer->active == 1 ? 'checked' : '' }}>
                                <span class="custom-control-indicator"></span>
                            </label>--}}
                        </td>
                        <td>
                            <div class="form-check custom_checkbox">
                                <input class="form-check-input verified" type="checkbox" id="checkbox-verified-{{ $buyer->id }}" value="1" data-id="{{ $buyer->id }}" {{ $buyer->verified == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="checkbox-verified-{{ $buyer->id }}">
                                    &nbsp;
                                </label>
                            </div>

                            {{--<label class="custom-control custom-checkbox">
                                <input type="checkbox" data-id="{{ $buyer->id }}" class="custom-control-input verified" value="1" {{ $buyer->verified == 1 ? 'checked' : '' }}>
                                <span class="custom-control-indicator"></span>
                            </label>--}}
                        </td>
                        <td>
                            <div class="form-check custom_checkbox">
                                <input class="form-check-input block" type="checkbox" id="checkbox-block-{{ $buyer->id }}" value="1" data-id="{{ $buyer->id }}" {{ $buyer->block == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="checkbox-block-{{ $buyer->id }}">
                                    &nbsp;
                                </label>
                            </div>

                            {{--<label class="custom-control custom-checkbox">
                                <input type="checkbox" data-id="{{ $buyer->id }}" class="custom-control-input block" value="1" {{ $buyer->block == 1 ? 'checked' : '' }}>
                                <span class="custom-control-indicator"></span>
                            </label>--}}
                        </td>
                        <td>
                            <div class="form-check custom_checkbox">
                                <input class="form-check-input minOrder" type="checkbox" id="checkbox-minOrder-{{ $buyer->id }}" value="1" data-id="{{ $buyer->id }}" {{ $buyer->min_order == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="checkbox-minOrder-{{ $buyer->id }}">
                                    &nbsp;
                                </label>
                            </div>

                            {{--<label class="custom-control custom-checkbox">
                                <input type="checkbox" data-id="{{ $buyer->id }}" class="custom-control-input minOrder" value="1" {{ $buyer->min_order == 1 ? 'checked' : '' }}>
                                <span class="custom-control-indicator"></span>
                            </label>--}}
                        </td>
                        <td>{{ date('m/d/Y g:i:s a', strtotime($buyer->created_at)) }}</td>
                        <td>
                            @if ($buyer->ein_path != null)
                                @if (pathinfo($buyer->ein_path, PATHINFO_EXTENSION) != 'pdf')
                                    <a class="show-image" data-href="{{ asset($buyer->ein_path) }}" href="#">EIN</a>
                                @else
                                    <a href="{{ asset($buyer->ein_path) }}" download>EIN</a> &nbsp;
                                @endif
                            @endif

                            @if ($buyer->sales1_path != null)
                                @if (pathinfo($buyer->sales1_path, PATHINFO_EXTENSION) != 'pdf')
                                    <a class="show-image" data-href="{{ asset($buyer->sales1_path) }}" href="#">Sales 1</a>
                                @else
                                    <a href="{{ asset($buyer->sales1_path) }}" download>Sales 1</a> &nbsp;
                                @endif
                            @endif

                            @if ($buyer->sales2_path != null)
                                @if (pathinfo($buyer->sales2_path, PATHINFO_EXTENSION) != 'pdf')
                                    <a class="show-image" data-href="{{ asset($buyer->sales2_path) }}" href="#">Sales 2</a>
                                @else
                                    <a href="{{ asset($buyer->sales2_path) }}" download>Sales 2</a> &nbsp;
                                @endif
                            @endif
                        </td>
                        <td>
                            <div class="form-check custom_checkbox">
                                <input class="form-check-input mailing_list" type="checkbox" id="checkbox-mailing-list-{{ $buyer->id }}" value="1" data-id="{{ $buyer->id }}" data-user_id="{{ $buyer->user_id }}" data-billing_phone="{{ $buyer->billing_phone }}" {{ $buyer->mailing_list == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="checkbox-mailing-list-{{ $buyer->id }}">
                                    &nbsp;
                                </label>
                            </div>
                        </td>
                        <td>
                            <a class="link btnEdit" href="{{ route('admin_buyer_edit', ['buyer' => $buyer->id]) }}" style="color: blue">Edit</a> |
                            <a class="link btnDelete" data-id="{{ $buyer->id }}" role="button" style="color: red">Delete</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="pagination"> 
            {!! $buyers->appends(request()->input())->links() !!}
            </div>
        </div>
    </div>

    <div class="modal" data-modal="modalShowImage">
        <div class="modal_overlay" data-modal-close="modalShowImage"></div>
        <div class="modal_inner">
            <div class="modal_wrapper modal_470p">
                <div class="item_list_popup">
                    <div class="modal_header display_table">
                        <span class="modal_header_title">Document</span>
                        <div class="float_right">
                            <span class="close_modal" data-modal-close="modalShowImage"></span>
                        </div>
                    </div>
                    <div class="modal_content">
                        <div class="ly-wrap-fluid">
                            <div class="ly-row">
                                <div class="ly-12">
                                    <img id="img" src="" width="100%">
                                </div>
                                <div class="ly-12">
                                    <div class="display_table m15">
                                        <div class="float_right">
                                            <button class="ly_btn btn_danger text_center width_150p " data-modal-close="modalShowImage">Close</button>
                                            <a class="ly_btn btn_blue width_150p text_center" id="btnDownload" download>Download</a>
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


    <div class="modal" data-modal="deleteModal">
        <div class="modal_overlay" data-modal-close="deleteModal"></div>
        <div class="modal_inner">
            <div class="modal_wrapper modal_470p">
                <div class="item_list_popup">
                    <div class="modal_header display_table">
                        <span class="modal_header_title">Are you sure want to delete?</span>
                        <div class="float_right">
                            <span class="close_modal" data-modal-close="deleteModal"></span>
                        </div>
                    </div>
                    <div class="modal_content">
                        <div class="ly-wrap-fluid">
                            <div class="ly-row">
                                <div class="ly-12">
                                    <div class="display_table m15">
                                        <div class="float_right">
                                            <button class="ly_btn btn_blue width_150p " data-modal-close="deleteModal">Close</button>
                                            <button class="ly_btn btn_danger width_150p" id="modalBtnDelete">Delete</button>
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
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.4/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.4/js/buttons.html5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#customer').DataTable( {
                dom: 'Bfrtip',
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ]
            } );
        } );
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // customer start
            var message = '{{ session('message') }}';
            var error = '{{ session('error') }}';

            if (message != '')
                toastr.success(message);

            if (error != '')
                toastr.error(error);

            $('#btnImport').click(function () {
                $('#file').click();
            });

            $('#file').change(function () {
                file = $(this).prop('files')[0];

                if (typeof file !== "undefined") {
                    setTimeout(() => {
                        $('.outslider_loading').css({"display": "block"});
                    }, 500);
                    $('#form').submit();
                }
            });

            //end import customer

            $('.status').change(function () {
                var status = 0;
                var id = $(this).data('id');

                if ($(this).is(':checked'))
                    status = 1;

                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_buyer_change_status') }}",
                    data: { id: id, status: status }
                }).done(function( msg ) {
                    toastr.success('Status Updated!');
                });
            });

            $('.verified').change(function () {
                var status = 0;
                var id = $(this).data('id');

                if ($(this).is(':checked'))
                    status = 1;

                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_buyer_change_verified') }}",
                    data: { id: id, status: status }
                }).done(function( msg ) {
                    toastr.success('Status Updated!');
                });
            });
            
            $('.mailing_list').change(function () {
                var mailing_list = 0;
                var id = $(this).data('id');
                var user_id = $(this).data('user_id');
                var billing_phone = $(this).data('billing_phone');

                if ($(this).is(':checked'))
                    mailing_list = 1;

                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_buyer_change_mailing_list') }}",
                    data: { id: id, user_id: user_id, billing_phone: billing_phone, mailing_list: mailing_list }
                }).done(function( msg ) {
                    toastr.success('Mailing List Updated!');
                });
            });

            $('.block').change(function () {
                var status = 0;
                var id = $(this).data('id');

                if ($(this).is(':checked'))
                    status = 1;

                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_buyer_change_block') }}",
                    data: { id: id, status: status }
                }).done(function( msg ) {
                    toastr.success('Status Updated!');
                });
            });

            $('.minOrder').change(function () {
                var status = 0;
                var id = $(this).data('id');

                if ($(this).is(':checked'))
                    status = 1;

                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_buyer_change_min_order') }}",
                    data: { id: id, status: status }
                }).done(function( msg ) {
                    toastr.success('Min. Order Updated!');
                });
            });

            $('.show-image').click(function (e) {
                e.preventDefault();

                var url = $(this).data('href');
                // alert(url);
                $('#img').attr('src', url);
                $('#btnDownload').attr('href', url);
                var targeted_modal_class = 'modalShowImage';
                $('[data-modal="' + targeted_modal_class + '"]').addClass('open_modal');
            });

            var selectedId;

            $('.btnDelete').click(function () {
                var targeted_modal_class = 'deleteModal';
                $('[data-modal="' + targeted_modal_class + '"]').addClass('open_modal');
                selectedId = $(this).data('id');
            });

            $('#modalBtnDelete').click(function () {
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_buyer_delete') }}",
                    data: { id: selectedId }
                }).done(function( msg ) {
                    location.reload();
                });
            });
        });

    </script>
@stop