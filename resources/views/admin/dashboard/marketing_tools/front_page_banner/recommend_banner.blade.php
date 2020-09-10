<?php
use App\Enumeration\Availability;
?>

@extends('admin.layouts.main')
@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <style>
        #HomePageSliderItems li img{
            width: 100%;
        }
    </style>
@stop

@section('content')
    <div class="item_border">
        <div class="container-fluid">
            <div class="ly-row mb_15">
                <div class="ly-12">
                    <div class="form_row">
                        <div class="form_inline">
                            <div class="form-check custom_checkbox">
                            <input class="form_global" type="checkbox" id="searchStyleNo"{{ (request()->get('style') == '1' || request()->get('style') == null) ? 'checked' : '' }}>
                                <label for="searchStyleNo">Style No.</label>
                            </div>
                        </div>

                        <div class="form_inline">
                            <div class="form-check custom_checkbox">
                                <input class="form_global" type="checkbox" id="searchDescription" {{ (request()->get('des') == '1') ? 'checked' : '' }}>
                                <label for="searchDescription">Full Description</label>
                            </div>
                        </div>

                        <div class="form_inline">
                            <div class="form-check custom_checkbox">
                                <input class="form_global" type="checkbox" id="searchItemName" {{ (request()->get('name') == '1') ? 'checked' : '' }}>
                                <label for="searchItemName">Item Name</label>
                            </div>
                        </div>
                        <div class="form_inline">
                            <input type="text" class="form_global" placeholder="(Use commas(,) for multiple style search)" id="inputText" value="{{ request()->get('text') }}">
                        </div>
                        <div class="form_inline">
                            <div class="select">
                                <select class="form_global" id="selectSortActiveItems">
                                    <option value="3" {{ request()->get('s1') == '3' ? 'selected' : '' }}>Activation Date</option>
                                    <option value="0" {{ request()->get('s1') == '0' ? 'selected' : '' }}>Sort Number</option>
                                    <option value="1" {{ request()->get('s1') == '1' ? 'selected' : '' }}>Last Update</option>
                                    <option value="2" {{ request()->get('s1') == '2' ? 'selected' : '' }}>Upload Date</option>
                                    <option value="4" {{ request()->get('s1') == '4' ? 'selected' : '' }}>Price Low to High</option>
                                    <option value="5" {{ request()->get('s1') == '5' ? 'selected' : '' }}>Price High to Low</option>
                                    <option value="6" {{ request()->get('s1') == '6' ? 'selected' : '' }}>Style No.</option>
                                </select>
                            </div>
                        </div>
                        <div class="form_inline text_right">
                            <button class="ly_btn btn_blue" id="btnSearch">search</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ly-row mb_15">
                <div class="ly-10">
                    <div class="form_row">
                        <div class="form_inline">
                            Active Items - {{ sizeof($activeItems) }} Items
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="global_accordion mb_15">
        <div class="container-fluid no-padding mb_15">
            <div class="accordion" id="accordionExample">
                <div class="card">
                    <div id="collapseOne" class="collapse show">
                        <div class="card-body">
                            <div class="accordion_content">
                                <div class="container-fluid no-padding">
                                    <div class="ly-row">
                                        @if(count($activeItems)>0)
                                            @foreach($activeItems as $item)
                                                <div class="ly-1" >
                                                    <a href=" {{ route('front_page_recommend_banner_add', ['item' => $item->id]) }}">
                                                        <img width="100%" src="{{(!auth()->user()) ? $defaultItemImage_path : asset($item->images[0]->image_path) }}" alt="{{ (!auth()->user()) ? config('app.name') : $item->style_no }}">
                                                    </a>
                                                    <br>
                                                    <span>@if(!empty($item->itemcategory)) {{$item->itemcategory->name}} @endif</span>
                                                    <span>{{ $item->style_no }}</span>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="ly-12"><span>No Item found according to search content.</span></div>
                                        @endif
                                    </div>
                                    <div class="ly-row accordion_filter accordion_filter_bottom">
                                        <div class="ly-12 text_center mt_15">
                                            {{ $activeItems->appends($appends)->links() }}
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
    <div class="ly-row mt_15">
        <div class="ly-12 mt_15">
            @foreach($images as $banneritems)
                <div class="banner-item-container">
                    <h3 class="mt_15 mb_15">Selected Items for " @if(!empty($banneritems['category'])) {{$banneritems['category']->name}} @endif"</h3>
                    @if(!empty($banneritems['items'])  )
                    <ul id="HomePageSliderItems" class="ly-row">
                        @foreach($banneritems['items'] as $image)
                            <li class="text_center ly-1" data-id="{{ $image->id }}">
                                <img src="{{ asset($image->image_path) }}" >
                                @if(!empty($image->item))<span>{{$image->item->style_no}}</span></br> @endif
                                <a href="#" class="color_red  removeitem" data-type="1" data-delid="{{ $image->id }}">Remove</a>
                            </li>
                        @endforeach
                    </ul>
                    @else
                    <ul>
                        <li> Empty List</li>
                    </ul>
                    @endif
                </div>
                </br>
            @endforeach
        </div>
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
            var message = '{{ session('message') }}';
            if (message != '')
                toastr.success(message);

            $('#selectSortActiveItems').change(function () {
                checkParameters();
            });

            $('#btnSearch').click(function () {
                search();
            });

            $('#inputText').keypress(function(e) {
                if(e.which == 13) {
                    search();
                }
            });


            $('.removeitem').click(function () {
                var id = parseInt($(this).data('delid'));
                $.ajax({
                    method: "POST",
                    url: "{{ route('top_front_slider_item_delete') }}",
                    data: { id: id, "_token": "{{ csrf_token() }}" }
                }).done(function( msg ) {
                    location.reload();
                });
            });

            function checkParameters() {
                var s1 = $('#selectSortActiveItems').val();
                var s2 = $('#selectSortInactiveItems').val();

                var parameters = <?php echo json_encode(request()->all()); ?>;
                var url = '{{ route('BannerAllSearchItem') }}' + '?s1=' + s1 ;

                $.each(parameters, function (key, value) {
                    if (key != 's1'  && key != 'p1' ) {
                        var val = '';

                        if (value != null)
                            val = value;

                        url += '&' + key + '=' + val;
                    }
                });
                window.location.replace(url);
            }

            function search() {
                var s1 = $('#selectSortActiveItems').val();
                var text = $('#inputText').val();
                var searchStyleNo = ($('#searchStyleNo').is(':checked')) ? 1 : 0;
                var description = ($('#searchDescription').is(':checked')) ? 1 : 0;
                var name = ($('#searchItemName').is(':checked')) ? 1 : 0;

                var url = '{{ route('BannerAllSearchItem') }}' + '?s1=' + s1  + '&text=' + text + '&style=' + searchStyleNo +
                    '&des=' + description + '&name=' + name;

                window.location.replace(url);
            }

            var el = document.getElementById('HomePageSliderItems');
            Sortable.create(el, {
                animation: 150,
                dataIdAttr: 'data-id',
                onEnd: function () {
                    updateSort(this.toArray());
                },
            });

            function updateSort(ids) {
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_main_slider_items_sort') }}",
                    data: { ids: ids }
                }).done(function( msg ) {
                    toastr.success('Items sort updated!');
                });
            }


        });
    </script>
@stop
