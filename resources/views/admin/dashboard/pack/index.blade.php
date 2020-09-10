@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <style type="text/css">
        .checkbox-inline {
            display: inline-flex;
        }

        .d-none {
            display: none;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }
    </style>
@stop

@section('content')
    <div class="ly_page_wrapper {{ ($errors && sizeof($errors) > 0) ? '' : 'd-none' }}" id="addEditRow">
        <form class="form-horizontal" id="form" method="post" action="{{ (old('inputAdd') == '1') ? route('admin_pack_add_post') : route('admin_pack_edit_post') }}">
        @csrf
            <input type="hidden" name="inputAdd" id="inputAdd" value="{{ old('inputAdd') }}">
            <input type="hidden" name="packId" id="packId" value="{{ old('packId') }}">
            <div class="add_new_cat_color">
                    <h3 class="font_16p mb_15 item_change_title" id="addEditTitle">{{ old('inputAdd') == '0' ? 'Edit Pack' : 'Add Pack' }}</h3>
                <div class="form_row">
                    <div class="label_inline width_150p fw_500">
                        Status
                    </div>
                    <div class="form_inline">
                        <div class="custom_radio">
                            <input type="radio" id="statusActive" name="status" class="custom-control-input"
                                    value="1" {{ (old('status') == '1' || empty(old('status'))) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="statusActive">Active</label>
                        </div>
                        <div class="custom_radio">
                            <input type="radio" id="statusInactive" name="status" class="custom-control-input"
                                    value="0" {{ old('status') == '0' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="statusInactive">Inactive</label>
                        </div>
                    </div>
                </div>

                <div class="form_row">
                    <div class="label_inline required width_150p fw_500">
                        Size Details
                    </div>
                    <div class="form_inline">
                        <div class="display_inline width_60p">
                            <input type="text" id="s1" name="s1" class="form_global{{ $errors->has('s1') ? ' is-invalid' : '' }}"
                            placeholder="S1" value="{{ old('s1') }}">
                        </div>
                        <div class="display_inline width_60p">
                            <input type="text" id="s2" name="s2" class="form_global{{ $errors->has('s2') ? ' is-invalid' : '' }}"
                            placeholder="S2" value="{{ old('s2') }}">
                        </div>
                        <div class="display_inline width_60p">
                            <input type="text" id="s3" name="s3" class="form_global{{ $errors->has('s3') ? ' is-invalid' : '' }}"
                            placeholder="S3" value="{{ old('s3') }}">
                        </div>
                        <div class="display_inline width_60p">
                            <input type="text" id="s4" name="s4" class="form_global{{ $errors->has('s4') ? ' is-invalid' : '' }}"
                            placeholder="S4" value="{{ old('s4') }}">
                        </div>
                        <div class="display_inline width_60p">
                            <input type="text" id="s5" name="s5" class="form_global{{ $errors->has('s5') ? ' is-invalid' : '' }}"
                            placeholder="S5" value="{{ old('s5') }}">
                        </div>
                        <div class="display_inline width_60p">
                            <input type="text" id="s6" name="s6" class="form_global{{ $errors->has('s6') ? ' is-invalid' : '' }}"
                            placeholder="S6" value="{{ old('s6') }}">
                        </div>
                        <div class="display_inline width_60p">
                            <input type="text" id="s7" name="s7" class="form_global{{ $errors->has('s7') ? ' is-invalid' : '' }}"
                            placeholder="S7" value="{{ old('s7') }}">
                        </div>
                        <div class="display_inline width_60p">
                            <input type="text" id="s8" name="s8" class="form_global{{ $errors->has('s8') ? ' is-invalid' : '' }}"
                            placeholder="S8" value="{{ old('s8') }}">
                        </div>
                        <div class="display_inline width_60p">
                            <input type="text" id="s9" name="s9" class="form_global{{ $errors->has('s9') ? ' is-invalid' : '' }}"
                            placeholder="S9" value="{{ old('s9') }}">
                        </div>
                        <div class="display_inline width_60p">
                            <input type="text" id="s10" name="s10" class="form_global{{ $errors->has('s10') ? ' is-invalid' : '' }}"
                            placeholder="S10" value="{{ old('s10') }}">
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <div class="label_inline required width_150p fw_500">
                            Pack
                    </div>
                    <div class="form_inline">
                        <div class="display_inline width_60p">
                            <input type="text" id="p1" name="p1" class="form_global{{ $errors->has('p1') ? ' is-invalid' : '' }}"
                                    placeholder="P1" value="{{ old('p1') }}">
                        </div>

                        <div class="display_inline width_60p">
                            <input type="text" id="p2" name="p2" class="form_global{{ $errors->has('p2') ? ' is-invalid' : '' }}"
                                    placeholder="P2" value="{{ old('p2') }}">
                        </div>

                        <div class="display_inline width_60p">
                            <input type="text" id="p3" name="p3" class="form_global{{ $errors->has('p3') ? ' is-invalid' : '' }}"
                                    placeholder="P3" value="{{ old('p3') }}">
                        </div>

                        <div class="display_inline width_60p">
                            <input type="text" id="p4" name="p4" class="form_global{{ $errors->has('p4') ? ' is-invalid' : '' }}"
                                    placeholder="P4" value="{{ old('p4') }}">
                        </div>

                        <div class="display_inline width_60p">
                            <input type="text" id="p5" name="p5" class="form_global{{ $errors->has('p5') ? ' is-invalid' : '' }}"
                                    placeholder="P5" value="{{ old('p5') }}">
                        </div>

                        <div class="display_inline width_60p">
                            <input type="text" id="p6" name="p6" class="form_global{{ $errors->has('p6') ? ' is-invalid' : '' }}"
                                    placeholder="P6" value="{{ old('p6') }}">
                        </div>

                        <div class="display_inline width_60p">
                            <input type="text" id="p7" name="p7" class="form_global{{ $errors->has('p7') ? ' is-invalid' : '' }}"
                                    placeholder="P7" value="{{ old('p7') }}">
                        </div>

                        <div class="display_inline width_60p">
                            <input type="text" id="p8" name="p8" class="form_global{{ $errors->has('p8') ? ' is-invalid' : '' }}"
                                    placeholder="P8" value="{{ old('p8') }}">
                        </div>

                        <div class="display_inline width_60p">
                            <input type="text" id="p9" name="p9" class="form_global{{ $errors->has('p9') ? ' is-invalid' : '' }}"
                                    placeholder="P9" value="{{ old('p9') }}">
                        </div>

                        <div class="display_inline width_60p">
                            <input type="text" id="p10" name="p10" class="form_global{{ $errors->has('p10') ? ' is-invalid' : '' }}"
                                    placeholder="P10" value="{{ old('p10') }}">
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <div class="label_inline width_150p fw_500">
                        Pack Description
                    </div>
                    <div class="form_inline">
                        <input type="text" id="description" name="description" class="form_global{{ $errors->has('description') ? ' is-invalid' : '' }}"
                        placeholder="Pack Description" value="{{ old('description') }}">
                    </div>
                </div>
                <div class="form_row">
                    <div class="label_inline width_150p fw_500">
                            Default
                    </div>
                    <div class="form_inline">
                        <div class="custom_checkbox">
                            <input type="checkbox" value="1" name="default" id="default" {{ old('default') ? 'checked' : '' }}>
                            <label for="default"></label>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <div class="form_inline">
                        <div class="text_right">
                            <div class="display_inline">
                                <button class="ly_btn btn_grey min_width_100p close_item_color" id="btnCancel">Cancel</button>
                            </div>
                            <div class="display_inline mr_0">
                                <button type="submit" id="btnSubmit" class="ly_btn btn_blue min_width_100p">{{ old('inputAdd') == '0' ? 'Update' : 'Add' }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="item_color_heading m15">
        <div class="ly-wrap">
            <div class="ly-row">
                <div class="ly-6 pl_0">
                    <div class="item_color_heading_left">
                        <span class="{{ ($errors && sizeof($errors) > 0) ? 'd-none' : '' }}" id="addBtnRow">
                            <span class="link mr_20 item_color_btn" id="btnAddNewPack">+ Add New Pack</span>
                        </span>
                    </div>
                </div>
                <div class="ly-6 pr_0">
                    <div class="text_right">
                        <span class="font_italic color_grey_type2 font_12p">You currently have {{ count($packs) }} packs.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="item_size_content">
        <table class="table header-border">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pack Name</th>
                    <th>Pack Details</th>
                    <th>Pack Description</th>
                    <th>Created On</th>
                    <th class="text_center width_100p">Active</th>
                    <th class="text_center width_100p">Default</th>
                    <th class="width_100p">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($packs as $pack)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $pack->name }}</td>
                    <td>{{ $pack->pack1 }}{{ ($pack->pack2 != '') ? '-'.$pack->pack2 : '' }}{{ ($pack->pack3 != '') ? '-'.$pack->pack3 : '' }}{{ ($pack->pack4 != '') ? '-'.$pack->pack4 : '' }}{{ ($pack->pack5 != '') ? '-'.$pack->pack5 : '' }}{{ ($pack->pack6 != '') ? '-'.$pack->pack6 : '' }}{{ ($pack->pack7 != '') ? '-'.$pack->pack7 : '' }}{{ ($pack->pack8 != '') ? '-'.$pack->pack8 : '' }}{{ ($pack->pack9 != '') ? '-'.$pack->pack9 : '' }}{{ ($pack->pack10 != '') ? '-'.$pack->pack10 : '' }}</td>
                    <td>{{ $pack->description }}</td>
                    <td>{{ date('d/m/Y', strtotime($pack->created_at)) }}</td>
                    <td class="text_center">
                        <div class="custom_checkbox">
                            <input type="checkbox" id="pcb1_{{ $pack->id }}" data-id="{{ $pack->id }}" class="status" value="1" {{ $pack->status == 1 ? 'checked' : '' }}>
                            <label for="pcb1_{{ $pack->id }}" class="pr_0"></label>
                        </div>
                    </td>
                    <td class="text_center">
                        <div class="custom_radio">
                            <input type="radio" id="pcb2_{{ $pack->id }}" name="defaultTable" class="custom-control-input default" data-id="{{ $pack->id }}"
                                value="1" {{ $pack->default == 1 ? 'checked' : '' }}>
                            <label for="pcb2_{{ $pack->id }}" class="pr_0"></label>
                        </div>
                    </td>
                    <td>
                        <a class="btnEdit" data-id="{{ $pack->id }}" data-index="{{ $loop->index }}" role="button"><span class="color_blue item_setting_edit">Edit</span></a> |
                        <a class="btnDelete" data-id="{{ $pack->id }}" role="button"><span class="color_red item_size_delete">Delete</span></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination_wrapper p10 item_color_pagination_color">
        <ul class="pagination">
            <li><button class="ly_btn p1_first{{ $packs->currentPage() > 1 ?  ' btn_paginate' : ''}}"{{ $packs->currentPage() == 1 ?  ' disabled' : ''}}>| <i class="fas fa-chevron-left"></i></button></li>
            <li>
                <button class="ly_btn p1_prev{{ $packs->currentPage() > 1 ?  ' btn_paginate' : ''}}"{{ $packs->currentPage() == 1 ?  ' disabled' : ''}}> <i class="fas fa-chevron-left"></i> PREV</button>
            </li>
            <li>
                <div class="pagination_input">
                    <input type="number" min="1" max="{{ $packs->lastPage() }}" class="form_global p1" value="{{ $packs->currentPage() }}"> of {{ $packs->lastPage() }}
                </div>
                <div class="pagination_btn">
                    <button class="ly_btn switch_page">GO</button>
                </div>
            </li>
            <li><button class="ly_btn p1_next{{ $packs->currentPage() < $packs->lastPage() ?  ' btn_paginate' : ''}}"{{ $packs->currentPage() == $packs->lastPage() ?  ' disabled' : ''}}>  NEXT <i class="fas fa-chevron-right"></i></button></li>
            <li>
                <button class="ly_btn p1_last{{ $packs->currentPage() < $packs->lastPage() ?  ' btn_paginate' : ''}}"{{ $packs->currentPage() == $packs->lastPage() ?  ' disabled' : ''}}> <i class="fas fa-chevron-right"></i> |</button>
            </li>
        </ul>
    </div>

    <div id="deleteModal" class="modal" data-modal="deleteModal">
        <div class="modal_overlay" data-modal-close="deleteModal"></div>
        <div class="modal_inner">
            <div class="modal_wrapper modal_380p">
                <div class="item_list_popup">
                    <div class="modal_header display_table">
                        <span class="modal_header_title">Delete Confirmation</span>
                    </div>
                    <div class="modal_content pa15">
                        <p class="fw_500 ">Are you sure that you want to delete?</p>
                        <div class="form_row mb_0 pt_15">
                            <div class="form_inline">
                                <div class="text_right">
                                    <div class="display_inline mr_0">
                                        <button data-modal-close="deleteModal" class="ly_btn btn_grey min_width_100p close_item_color">Close</button>
                                    </div>
                                    <div class="display_inline mr_0">
                                        <button class="ly_btn btn_blue min_width_100p" id="modalBtnDelete">Yes</button>
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
    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.p1').keyup(function() {

                if($(this).val() > pageCount) {

                    $('.p1').val(pageCount);

                } else {

                    $('.p1').val($(this).val());

                }
                

            });

            $('.switch_page').click(function() {

                var p1 = $('.p1').val();
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

            });

            $('.p1_first').click(function() {

                var p1 = 1;
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

            });

            $('.p1_prev').click(function() {

                var p1 = <?php echo $packs->currentPage(); ?> - 1;
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

            });

            $('.p1_next').click(function() {

                var p1 = <?php echo $packs->currentPage(); ?> + 1;
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

            });

            $('.p1_last').click(function() {

                var p1 = <?php echo $packs->lastPage(); ?>;
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

            });

            var packsArray = <?php echo json_encode($packs); ?>;
            
            var packs = packsArray.data;
            
            var selectedId;
            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);


            $('#btnAddNewPack').click(function () {
                $('#addEditRow').removeClass('d-none');
                $('#addBtnRow').addClass('d-none');
                $('#addEditTitle').html('Add Pack');
                $('#btnSubmit').html('Add');
                $('#inputAdd').val('1');
                $('#form').attr('action', '{{ route('admin_pack_add_post') }}');
            });

            $('#btnCancel').click(function (e) {
                e.preventDefault();

                $('#addEditRow').addClass('d-none');
                $('#addBtnRow').removeClass('d-none');

                // Clear form
                $('#statusActive').prop('checked', true);
                $('#default').prop('checked', false);
                $('#s1').val('');
                $('#s2').val('');
                $('#s3').val('');
                $('#s4').val('');
                $('#s5').val('');
                $('#s6').val('');
                $('#s7').val('');
                $('#s8').val('');
                $('#s9').val('');
                $('#s10').val('');
                $('#p1').val('');
                $('#p2').val('');
                $('#p3').val('');
                $('#p4').val('');
                $('#p5').val('');
                $('#p6').val('');
                $('#p7').val('');
                $('#p8').val('');
                $('#p9').val('');
                $('#p10').val('');
                $('#description').val('');
                $('#size_description').val('');

                $('input').removeClass('is-invalid');
                $('.form-group').removeClass('has-danger');
            });

            $('.btnEdit').click(function () {
                var id = $(this).data('id');
                var index = $(this).data('index');

                $('#addEditRow').removeClass('d-none');
                $('#addBtnRow').addClass('d-none');
                $('#addEditTitle').html('Edit Pack');
                $('#btnSubmit').html('Update');
                $('#inputAdd').val('0');
                $('#form').attr('action', '{{ route('admin_pack_edit_post') }}');
                $('#packId').val(id);

                var pack = packs[index];

                if (pack.status == 1)
                    $('#statusActive').prop('checked', true);
                else
                    $('#statusInactive').prop('checked', true);

                if (pack.default == 1)
                    $('#default').prop('checked', true);
                else
                    $('#default').prop('checked', false);

                $('#p1').val(pack.pack1);
                $('#p2').val(pack.pack2);
                $('#p3').val(pack.pack3);
                $('#p4').val(pack.pack4);
                $('#p5').val(pack.pack5);
                $('#p6').val(pack.pack6);
                $('#p7').val(pack.pack7);
                $('#p8').val(pack.pack8);
                $('#p9').val(pack.pack9);
                $('#p10').val(pack.pack10);
                $('#description').val(pack.description);
                $('#size_description').val(pack.size_description);

                var sizes = pack.name.split('-')

                $.each(sizes, function (i, size) {
                    $('#s'+(i+1)).val(size);
                });
            });

            $('.btnDelete').click(function () {
                $('#deleteModal').addClass('open_modal');
                selectedId = $(this).data('id');
            });

            $('#modalBtnDelete').click(function () {
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_pack_delete') }}",
                    data: { id: selectedId }
                }).done(function( msg ) {
                    location.reload();
                });
            });

            $('.status').change(function () {
                var status = 0;
                var id = $(this).data('id');

                if ($(this).is(':checked'))
                    status = 1;

                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_pack_change_status') }}",
                    data: { id: id, status: status }
                }).done(function( msg ) {
                    toastr.success('Status Updated!');
                });
            });

            $('.default').change(function () {
                var id = $(this).data('id');

                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_pack_change_default') }}",
                    data: { id: id }
                }).done(function( msg ) {
                    toastr.success('Default Updated!');
                });
            });
        })
    </script>
@stop