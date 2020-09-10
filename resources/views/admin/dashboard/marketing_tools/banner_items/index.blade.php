<?php use App\Enumeration\SliderType; ?>

@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <h4>Home Page Slider</h4>
        </div>

        <div class="col-md-6 text-right">
            <button class="btn btn-primary" id="btnAddHomeSlider">Add Item</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="banner-item-container">
                <ul id="HomePageSliderItems">
                    @foreach($mainSliderItems as $i)
                        <li class="text-center" data-id="{{ $i->id }}">
                            @if (sizeof($i->item->images) > 0)
                                <img src="{{ asset($i->item->images[0]->image_path) }}" alt="{{ $i->item->style_no }}">
                            @else
                                <img src="{{ asset('images/no-image.png') }}" alt="{{ $i->item->style_no }}">
                            @endif
                            <a class="text-danger btnRemove" data-type="1" data-id="{{ $i->id }}">Remove</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{--Category Top Slider--}}
    <div class="row">
        <div class="col-md-6">
            <h4>Category Top Slider</h4>
        </div>

        <div class="col-md-6 text-right">
            <button class="btn btn-primary" id="btnAddCategoryTopSlider">Add Item</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="banner-item-container">
                <ul id="categoryTopSliderItems">
                    @foreach($categoryTopSliderItems as $i)
                        <li class="text-center" data-id="{{ $i->id }}">
                            @if (sizeof($i->item->images) > 0)
                                <img src="{{ asset($i->item->images[0]->image_path) }}" alt="{{ $i->item->style_no }}">
                            @else
                                <img src="{{ asset('images/no-image.png') }}" alt="{{ $i->item->style_no }}">
                            @endif
                            <a class="text-danger btnRemove" data-type="1" data-id="{{ $i->id }}">Remove</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{--Category Second Slider--}}

    <div class="row">
        <div class="col-md-6">
            <h4>Category Second Slider</h4>
        </div>

        <div class="col-md-6 text-right">
            <button class="btn btn-primary" id="btnAddCategorySecondSlider">Add Item</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="banner-item-container">
                <ul id="categorySecondSliderItems">
                    @foreach($categorySecondSliderItems as $i)
                        <li class="text-center" data-id="{{ $i->id }}">
                            @if (sizeof($i->item->images) > 0)
                                <img src="{{ asset($i->item->images[0]->image_path) }}" alt="{{ $i->item->style_no }}">
                            @else
                                <img src="{{ asset('images/no-image.png') }}" alt="{{ $i->item->style_no }}">
                            @endif
                            <a class="text-danger btnRemove" data-type="1" data-id="{{ $i->id }}">Remove</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{--New Arrivals Top Slider--}}
    <div class="row">
        <div class="col-md-6">
            <h4>New Arrival Top Slider</h4>
        </div>

        <div class="col-md-6 text-right">
            <button class="btn btn-primary" id="btnAddNewTopSlider">Add Item</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="banner-item-container">
                <ul id="newTopSliderItems">
                    @foreach($newTopSliderItems as $i)
                        <li class="text-center" data-id="{{ $i->id }}">
                            @if (sizeof($i->item->images) > 0)
                                <img src="{{ asset($i->item->images[0]->image_path) }}" alt="{{ $i->item->style_no }}">
                            @else
                                <img src="{{ asset('images/no-image.png') }}" alt="{{ $i->item->style_no }}">
                            @endif
                            <a class="text-danger btnRemove" data-type="1" data-id="{{ $i->id }}">Remove</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{--New Arrivals Second Slider--}}
    <div class="row">
        <div class="col-md-6">
            <h4>New Arrival Second Slider</h4>
        </div>

        <div class="col-md-6 text-right">
            <button class="btn btn-primary" id="btnAddNewSecondSlider">Add Item</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="banner-item-container">
                <ul id="newSecondSliderItems">
                    @foreach($newSecondSliderItems as $i)
                        <li class="text-center" data-id="{{ $i->id }}">
                            @if (sizeof($i->item->images) > 0)
                                <img src="{{ asset($i->item->images[0]->image_path) }}" alt="{{ $i->item->style_no }}">
                            @else
                                <img src="{{ asset('images/no-image.png') }}" alt="{{ $i->item->style_no }}">
                            @endif
                            <a class="text-danger btnRemove" data-type="1" data-id="{{ $i->id }}">Remove</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>



    <div class="modal fade" id="item-modal" tabindex="-1" role="dialog" aria-labelledby="modalLabelLarge"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabelLarge">Select Item</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    <ul class="modal-items">
                        @foreach($items as $item)
                            <li class="modal-item" data-id="{{ $item->id }}">
                                @if (sizeof($item->images) > 0)
                                    <img src="{{ asset($item->images[0]->list_image_path) }}" alt="{{ $item->style_no }}">
                                @else
                                    <img src="{{ asset('images/no-image.png') }}" alt="{{ $item->style_no }}">
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" role="dialog" aria-labelledby="deleteModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h4 class="modal-title text-white" id="deleteModal">Delete</h4>
                </div>
                <div class="modal-body">
                    <p>
                        Are you sure want to delete?
                    </p>
                </div>
                <div class="modal-footer">
                    <button class="btn  btn-default" data-dismiss="modal">Close</button>
                    <button class="btn  btn-danger" id="modalBtnDelete">Delete</button>
                </div>
            </div>
        </div>
        <!--- end modals-->
    </div>
@stop

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/sortable/js/Sortable.min.js') }}"></script>
    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var type = '';
            var id = '';

            $('#btnAddHomeSlider').click(function () {
                type = '{{ SliderType::$MAIN_SLIDER }}';
                $('#item-modal').modal('show');
            });

            $('#btnAddCategoryTopSlider').click(function () {
                type = '{{ SliderType::$CATEGORY_TOP_SLIDER }}';
                $('#item-modal').modal('show');
            });

            $('#btnAddCategorySecondSlider').click(function () {
                type = '{{ SliderType::$CATEGORY_SECOND_SLIDER }}';
                $('#item-modal').modal('show');
            });

            $('#btnAddNewTopSlider').click(function () {
                type = '{{ SliderType::$NEW_ARRIVAL_TOP_SLIDER }}';
                $('#item-modal').modal('show');
            });

            $('#btnAddNewSecondSlider').click(function () {
                type = '{{ SliderType::$NEW_ARRIVAL_SECOND_SLIDER }}';
                $('#item-modal').modal('show');
            });

            
            $('.modal-item').click(function () {
                var id = $(this).data('id');

                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_banner_item_add') }}",
                    data: { id: id, type: type }
                }).done(function( data ) {
                    if (data.success)
                        location.reload();
                    else
                        alert(data.message);
                });
            });

            $('.btnRemove').click(function () {
                $('#deleteModal').modal('show');
                type = $(this).data('type');
                id = $(this).data('id');
            });

            $('#modalBtnDelete').click(function () {
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_banner_item_remove') }}",
                    data: { type: type, id: id }
                }).done(function( msg ) {
                    location.reload();
                });
            });

            // ================Sortable==========

            // Homepage Slider
            var el = document.getElementById('HomePageSliderItems');
            Sortable.create(el, {
                animation: 150,
                dataIdAttr: 'data-id',
                onEnd: function () {
                    updateSort(this.toArray());
                },
            });

            // Category Top Slider
            var el2 = document.getElementById('categoryTopSliderItems');
            Sortable.create(el2, {
                animation: 150,
                dataIdAttr: 'data-id',
                onEnd: function () {
                    updateSort(this.toArray());
                },
            });

            // Category Second Slider
            var el3 = document.getElementById('categorySecondSliderItems');
            Sortable.create(el3, {
                animation: 150,
                dataIdAttr: 'data-id',
                onEnd: function () {
                    updateSort(this.toArray());
                },
            });

            // New Arrival Top Slider
            var el4 = document.getElementById('newTopSliderItems');
            Sortable.create(el4, {
                animation: 150,
                dataIdAttr: 'data-id',
                onEnd: function () {
                    updateSort(this.toArray());
                },
            });

            // New Arrival Second Slider
            var el5 = document.getElementById('newSecondSliderItems');
            Sortable.create(el5, {
                animation: 150,
                dataIdAttr: 'data-id',
                onEnd: function () {
                    updateSort(this.toArray());
                },
            });

            function updateSort(ids) {
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_banner_item_sort') }}",
                    data: { ids: ids }
                }).done(function( msg ) {
                    toastr.success('Items sort updated!');
                });
            }

        });
    </script>
@stop