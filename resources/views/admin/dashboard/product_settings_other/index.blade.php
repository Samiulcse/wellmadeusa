@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
@stop

@section('content')

    <div class="vendor_info_content">
        <div class="tab_wrapper pa_0">
            <div class="ly_tab">
                <nav class="tabs">
                    <ul class="tab_four">
                        <li href="#fabric" class="active">Fabric</li>
                        <li href="#madeIn">Made in</li>
                    </ul>   
                </nav>
                <div class="tab_content_wrapper">
                    <div id="fabric" class="tab_content show">
                        <div class="fadein">      
                            <br>
                            @include('admin.dashboard.product_settings_other.includes.fabric')       
                        </div>
                    </div>

                    <div id="madeIn" class="tab_content">
                        <div class="fadein">
                            @include('admin.dashboard.product_settings_other.includes.made_in_country')
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

            // Made in country
            var madeInCountries = <?php echo json_encode($madeInCountries->toArray()); ?>;
            var selectedMadeInCountryId;
            var selectedMadeInCountryIndex;

            $('#btnAddNewMadeInCountry').click(function () {
                $('#addEditRowMadeInCountry').removeClass('d-none');
                $('#btnAddNewMadeInCountry').addClass('d-none');
                $('#addEditTitleMadeInCountry').html('Add a New Made In Country');

                $('#btnAddMadeInCountry').show();
                $('#btnUpdateMadeInCountry').hide();
            });

            $('#btnCancelMadeInCountry').click(function () {
                $('#addEditRowMadeInCountry').addClass('d-none');
                $('#btnAddNewMadeInCountry').removeClass('d-none');

                // Clear form
                $('#statusActiveMadeInCountry').prop('checked', true);
                $('#addEditTitleMadeInCountry').html('Add a New Made In Country');
                $('#madeInCountryName').val('');
                $('#defaultMadeInCountry').prop('checked', false);

                $('#madeInCountryName').removeClass('is-invalid');
            });
            
            $('#btnAddMadeInCountry').click(function () {
                var name = $('#madeInCountryName').val();
                var status = 0;
                var defaultVal = 0;

                if (name == '') {
                    $('#madeInCountryName').addClass('is-invalid');
                } else {
                    if ($('#statusActiveMadeInCountry').is(':checked'))
                        status = 1;

                    if ($('#defaultMadeInCountry').is(':checked'))
                        defaultVal = 1;

                    $.ajax({
                        method: "POST",
                        url: "{{ route('admin_made_in_country_add') }}",
                        data: { name: name, status: status, defaultVal: defaultVal }
                    }).done(function( country ) {
                        madeInCountries.push(country);

                        var index = madeInCountries.length-1;

                        var html = $('#madeInCountryTrTemplate').html();
                        var row = $(html);
                        row.find('.madeInCountryIndex').html(index+1);
                        row.find('.madeInCountryName').html(name);

                        if (status == 1)
                            row.find('.statusMadeInCountry').prop('checked', true);

                        if (defaultVal == 1) {
                            $('.defaultMadeInCountry').prop('checked', false);
                            row.find('.defaultMadeInCountry').prop('checked', true);
                        }

                        row.find('.statusMadeInCountry').attr("data-id", country.id);
                        row.find('.defaultMadeInCountry').attr("data-id", country.id);
                        row.find('.btnEditMadeInCountry').attr("data-id", country.id);
                        row.find('.btnEditMadeInCountry').attr("data-index", index);
                        row.find('.btnDeleteMadeInCountry').attr("data-index", index);
                        row.find('.btnDeleteMadeInCountry').attr("data-id", country.id);

                        $('#madeInCountryTbody').append(row);

                        toastr.success('Made In Country Added!');
                        $('#btnCancelMadeInCountry').trigger('click');
                    });
                }
            });

            $('body').on('click', '.btnEditMadeInCountry', function () {
                var id = $(this).data('id');
                var index = $(this).data('index');
                var country = madeInCountries[index];
                selectedMadeInCountryId = id;
                selectedMadeInCountryIndex = index;

                $('#addEditRowMadeInCountry').removeClass('d-none');
                $('#btnAddNewMadeInCountry').addClass('d-none');
                $('#addEditTitleMadeInCountry').html('Edit Made In Country');

                if (country.status == 1)
                    $('#statusActiveMadeInCountry').prop('checked', true);
                else
                    $('#statusInactiveMadeInCountry').prop('checked', true);

                if (country.default == 1)
                    $('#defaultMadeInCountry').prop('checked', true);
                else
                    $('#defaultMadeInCountry').prop('checked', false);

                $('#madeInCountryName').val(country.name);

                $('#btnAddMadeInCountry').hide();
                $('#btnUpdateMadeInCountry').show();
                
                if(!$('#addNewMadeInCountry').is(":visible")) {
                    let target = $('#addNewMadeInCountry');
                    $('.ly_accrodion_title').toggleClass('open_acc');
                    target.slideToggle();
                }
            });
            
            $('#btnUpdateMadeInCountry').click(function () {
                var name = $('#madeInCountryName').val();
                var status = 0;
                var defaultVal = 0;

                if (name == '') {
                    $('#madeInCountryName').addClass('is-invalid');
                } else {
                    if ($('#statusActiveMadeInCountry').is(':checked'))
                        status = 1;

                    if ($('#defaultMadeInCountry').is(':checked'))
                        defaultVal = 1;

                    $.ajax({
                        method: "POST",
                        url: "{{ route('admin_made_in_country_update') }}",
                        data: { id: selectedMadeInCountryId, name: name, status: status, defaultVal: defaultVal }
                    }).done(function( country ) {
                        madeInCountries[selectedMadeInCountryIndex] = country;

                        $('.madeInCountryName:eq('+selectedMadeInCountryIndex+')').html(name);

                        if (status == 1)
                            $('.statusMadeInCountry:eq('+selectedMadeInCountryIndex+')').prop('checked', true);
                        else
                            $('.statusMadeInCountry:eq('+selectedMadeInCountryIndex+')').prop('checked', false);

                        if (defaultVal == 1) {
                            $('.defaultMadeInCountry').prop('checked', false);
                            $('.defaultMadeInCountry:eq('+selectedMadeInCountryIndex+')').prop('checked', true);
                        }

                        toastr.success('Made In Country Updated!');
                        $('#btnCancelMadeInCountry').trigger('click');
                    });
                }
                
            });

            $('body').on('click', '.btnDeleteMadeInCountry', function () {
                var id = $(this).data('id');
                var index = $(".btnDeleteMadeInCountry").index(this);
                selectedMadeInCountryId = id;
                selectedMadeInCountryIndex = index;
                var targeted_modal_class = 'deleteModalMadeInCountry';
                $('[data-modal="' + targeted_modal_class + '"]').addClass('open_modal');
            });
            
            $('#modalBtnDeleteMadeInCountry').click(function () {
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_made_in_country_delete') }}",
                    data: { id: selectedMadeInCountryId }
                }).done(function( country ) {
                    $('#madeInCountryTbody tr:eq('+selectedMadeInCountryIndex+')').remove();
                    var targeted_modal_class = 'deleteModalMadeInCountry';
                    $('[data-modal="' + targeted_modal_class + '"]').removeClass('open_modal');
                    toastr.success('Made In Country Deleted!');
                });

            });
            
            $('body').on('change', '.statusMadeInCountry', function () {
                var status = 0;
                var id = $(this).data('id');

                if ($(this).is(':checked'))
                    status = 1;

                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_made_in_country_change_status') }}",
                    data: { id: id, status: status }
                }).done(function( msg ) {
                    toastr.success('Status Updated!');
                });
            });

            $('body').on('change', '.defaultMadeInCountry', function () {
                var id = $(this).data('id');

                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_made_in_country_change_default') }}",
                    data: { id: id }
                }).done(function( msg ) {
                    toastr.success('Default Made In Country Updated!');
                });
            });

            // Fabric
            var fabrics = <?php echo json_encode($fabrics->toArray()); ?>;
            var selectedFabricId;
            var selectedFabricIndex;

            $('#btnAddNewFabric').click(function () {
                $('#addEditRowFabric').removeClass('d-none');
                $('#btnAddNewFabric').addClass('d-none');
                $('#addEditTitleFabric').html('Add a New Fabric');

                $('#btnAddFabric').show();
                $('#btnUpdateFabric').hide();
            });

            $('#btnCancelFabric').click(function () {
                $('#addEditRowFabric').addClass('d-none');
                $('#btnAddNewFabric').removeClass('d-none');

                // Clear form
                $('#statusActiveFabric').prop('checked', true);
                $('#addEditTitleFabric').html('Add a New Fabric');
                $('#master_fabric').val('');
                $('#fabric_description').val('');
                $('#defaultFabric').prop('checked', false);

                $('#fabric_description').removeClass('is-invalid');
                $('#master_fabric').removeClass('is-invalid');
            });

            $('#btnAddFabric').click(function () {
                $('#fabric_description').removeClass('is-invalid');
                $('#master_fabric').removeClass('is-invalid');

                var description = $('#fabric_description').val();
                var status = 0;
                var defaultVal = 0;
                var masterFabricId = $('#master_fabric').val();

                var error = false;

                if (description == '') {
                    $('#fabric_description').addClass('is-invalid');
                    error = true;
                }

                if (masterFabricId == '') {
                    $('#master_fabric').addClass('is-invalid');
                    error = true;
                }

                if (!error) {
                    if ($('#statusActiveFabric').is(':checked'))
                        status = 1;

                    if ($('#defaultFabric').is(':checked'))
                        defaultVal = 1;

                    $.ajax({
                        method: "POST",
                        url: "{{ route('admin_fabric_add') }}",
                        data: { name: description, status: status, defaultVal: defaultVal, masterFabricId: masterFabricId }
                    }).done(function( fabric ) {
                        fabrics.push(fabric);

                        var index = fabrics.length-1;

                        var html = $('#fabricTrTemplate').html();
                        var row = $(html);
                        row.find('.fabricIndex').html(index+1);
                        row.find('.fabricName').html(description);
                        row.find('.masterFabricName').html(fabric.master_fabric.name);

                        if (status == 1)
                            row.find('.statusFabric').prop('checked', true);

                        if (defaultVal == 1) {
                            $('.defaultFabric').prop('checked', false);
                            row.find('.defaultFabric').prop('checked', true);
                        }

                        row.find('.statusFabric').attr("data-id", fabric.id);
                        row.find('.defaultFabric').attr("data-id", fabric.id);
                        row.find('.btnEditFabric').attr("data-id", fabric.id);
                        row.find('.btnEditFabric').attr("data-index", index);
                        row.find('.btnDeleteFabric').attr("data-index", index);
                        row.find('.btnDeleteFabric').attr("data-id", fabric.id);

                        $('#fabricTbody').append(row);

                        toastr.success('Fabric Added!');
                        $('#btnCancelFabric').trigger('click');
                    });
                }
            });

            $('body').on('click', '.btnEditFabric', function () {
                var id = $(this).data('id');
                var index = $(this).data('index');
                var fabric = fabrics[index];
                selectedFabricId = id;
                selectedFabricIndex = index;

                $('#addEditRowFabric').removeClass('d-none');
                $('#btnAddNewFabric').addClass('d-none');
                $('#addEditTitleFabric').html('Edit Fabric');

                if (fabric.status == 1)
                    $('#statusActiveFabric').prop('checked', true);
                else
                    $('#statusInactiveFabric').prop('checked', true);

                if (fabric.default == 1)
                    $('#defaultFabric').prop('checked', true);
                else
                    $('#defaultFabric').prop('checked', false);

                $('#fabric_description').val(fabric.name);
                $('#master_fabric').val(fabric.master_fabric_id);

                $('#btnAddFabric').hide();
                $('#btnUpdateFabric').show();

                if(!$('#addNewFabric').is(":visible")) {
                    let target = $('#addNewFabric');
                    $('.ly_accrodion_title').toggleClass('open_acc');
                    target.slideToggle();
                }
            });

            $('#btnUpdateFabric').click(function () {
                $('#fabric_description').removeClass('is-invalid');
                $('#master_fabric').removeClass('is-invalid');

                var description = $('#fabric_description').val();
                var status = 0;
                var defaultVal = 0;
                var masterFabricId = $('#master_fabric').val();
                var error = false;

                if (description == '') {
                    $('#fabric_description').addClass('is-invalid');
                    error = true;
                }

                if (masterFabricId == '') {
                    $('#master_fabric').addClass('is-invalid');
                    error = true;
                }

                if (!error) {
                    if ($('#statusActiveFabric').is(':checked'))
                        status = 1;

                    if ($('#defaultFabric').is(':checked'))
                        defaultVal = 1;

                    $.ajax({
                        method: "POST",
                        url: "{{ route('admin_fabric_update') }}",
                        data: {id: selectedFabricId, name: description, status: status, defaultVal: defaultVal, masterFabricId: masterFabricId}
                    }).done(function (fabric) {
                        fabrics[selectedFabricIndex] = fabric;

                        $('.fabricName:eq(' + selectedFabricIndex + ')').html(description);
                        $('.masterFabricName:eq(' + selectedFabricIndex + ')').html(fabric.master_fabric.name);

                        if (status == 1)
                            $('.statusFabric:eq(' + selectedFabricIndex + ')').prop('checked', true);
                        else
                            $('.statusFabric:eq(' + selectedFabricIndex + ')').prop('checked', false);

                        if (defaultVal == 1) {
                            $('.defaultFabric').prop('checked', false);
                            $('.defaultFabric:eq(' + selectedFabricIndex + ')').prop('checked', true);
                        }

                        toastr.success('Fabric Updated!');
                        $('#btnCancelFabric').trigger('click');
                    });
                }
            });

            $('body').on('click', '.btnDeleteFabric', function () {
                var id = $(this).data('id');
                var index = $(".btnDeleteFabric").index(this);
                selectedFabricId = id;
                selectedFabricIndex = index;
                var targeted_modal_class = 'deleteModalFabric';
                $('[data-modal="' + targeted_modal_class + '"]').addClass('open_modal');
            });

            $('#modalBtnDeleteFabric').click(function () {
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_fabric_delete') }}",
                    data: { id: selectedFabricId }
                }).done(function( country ) {
                    $('#fabricTbody tr:eq('+selectedFabricIndex+')').remove();                    
                    var targeted_modal_class = 'deleteModalFabric';
                    $('[data-modal="' + targeted_modal_class + '"]').removeClass('open_modal');
                    toastr.success('Fabric Deleted!');
                });

            });

            $('body').on('change', '.statusFabric', function () {
                var status = 0;
                var id = $(this).data('id');

                if ($(this).is(':checked'))
                    status = 1;

                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_fabric_change_status') }}",
                    data: { id: id, status: status }
                }).done(function( msg ) {
                    toastr.success('Status Updated!');
                });
            });

            $('body').on('change', '.defaultFabric', function () {
                var id = $(this).data('id');

                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_fabric_change_default') }}",
                    data: { id: id }
                }).done(function( msg ) {
                    toastr.success('Default Fabric Updated!');
                });
            });
        })
    </script>
@stop