@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <style>
    .short_product_inner {
        margin-left: 0.5%;
        margin-right: 0.5%;
        float: left;
        margin-bottom: 15px;
        padding: 5px;
        border: 1px solid #e5e5e5;
        text-align: center;
    }
    .short_product_inner img{
        width: 100%;
    }
    input.form-control {
        display: block;
        width: 100%;
        border: 0;
        background: #fff;
        border: 1px solid #999;
        padding: 2px 15px;
        font-size: 14px;
        line-height: 18px;
        color: #343434;
        -webkit-appearance: none;
        -moz-appearance: none;
        -ms-progress-appearance: none;
        border-radius: 0;
        -webkit-border-radius: 0;
    }
    #form-sort,.form-control{
        width:100%;
    }
    .short_product_inner p{
        font-size:10px;
    }
    #SortItems{
        display: flex;
        flex-wrap: wrap;
    }
    #SortItems li{
        width: 7%;
        margin-left: 5px;
        margin-right: 5px;
    }
    </style>
@stop

@section('content')
    <div class="ly-row">
            <div class="ly-12">
                <div class="ly-row">
                    <div class="ly-2">
                        <label>Order By</label>
                    </div>

                    <div class="ly-1">
                        <label>Type</label>
                    </div>

                    <div class="ly-2">
                        <label>Category</label>
                    </div>

                    <div class="ly-2">
                    </div>

                    <div class="ly-2">
                    </div>

                    <div class="ly-1">
                        <label>Show Per Page</label>
                    </div>
                </div>
            </div>
            <div class="ly-12 m15">
                <div class="ly-row mb5">
                    <div class="ly-2">
                        <div class="form_row">
                            <div class="form_inline">
                                <div class="select">
                                    <select class="form_global form-control-rounded form-control-sm" id="sort">
                                        <option value="1" {{ request()->get('sort') == '1' ? 'selected' : '' }}>Sort No</option>
                                        <option value="2" {{ request()->get('sort') == '2' ? 'selected' : '' }}>Activation Date</option>
                                        <option value="3" {{ request()->get('sort') == '3' ? 'selected' : '' }}>Modification Date</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ly-1">
                        <div class="form_row">
                            <div class="form_inline">
                                <div class="select">
                                    <select class="form_global form-control-rounded form-control-sm" id="active">
                                        <option value="1" {{ request()->get('a') == '1' ? 'selected' : '' }}>All</option>
                                        <option value="2" {{ request()->get('a') == null ? 'selected' : (request()->get('a') == '2' ? 'selected' : '') }}>Active</option>
                                        <option value="3" {{ request()->get('a') == '3' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ly-2">
                        <div class="form_row">
                            <div class="form_inline">
                                <div class="select">
                                    <select class="form_global form-control-rounded form-control-sm" id="d_parent_category">
                                        <option value="0">All Category</option>
                                        @foreach($defaultCategories as $item)
                                            <option value="{{ $item['id'] }}" data-index="{{ $loop->index }}" {{ request()->get('c1') == $item['id'] ? 'selected' : '' }}>{{ $item['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ly-2">
                        <div class="form_row">
                            <div class="form_inline">
                                <div class="select">
                                    <select class="form_global form-control-rounded form-control-sm" id="d_second_parent_category">
                                        <option value="0">All Category</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ly-2">
                        <div class="form_row">
                            <div class="form_inline">
                                <div class="select">
                                    <select class="form_global form-control-rounded form-control-sm" id="d_third_parent_category">
                                        <option value="0">All Category</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ly-1">
                        <div class="form_row">
                            <div class="form_inline">
                                <div class="select">
                                    <select class="form_global form-control-rounded form-control-sm" id="showPerPage">
                                        <option value="1" {{ request()->get('p') == '1' ? 'selected' : '' }}>50</option>
                                        <option value="2" {{ request()->get('p') == '2' ? 'selected' : '' }}>100</option>
                                        <option value="3" {{ request()->get('p') == '3' ? 'selected' : '' }}>150</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ly-2 ">
                        <button class="ly_btn  btn_blue" id="btnFilter">Filter</button>
                        <button class="ly_btn  btn_blue" id="btnSave">Save</button>
                    </div>
                </div>
            </div>

            <div class="ly-12 m15">
                <div class="ly-row">
                <form action="{{ route('admin_sort_items_save') }}" method="POST" id="form-sort">
                    @csrf
                    <ul id="SortItems">
                    @foreach($items as $item)
                        <li class="short_item"  >
                            <div class="short_product_inner">
                                <a href="{{ route('admin_edit_item', ['item' => $item->id]) }}">
                                    @if (sizeof($item->images) > 0)
                                        <img src="{{ asset($item->images[0]->thumbs_image_path) }}" alt="{{ $item->style_no }}" class="img-fluid">
                                    @else
                                        <img src="{{ asset('images/no-image.png') }}" alt="{{ $item->style_no }}" class="img-fluid">
                                    @endif
                                </a>
                                <p><a href="{{ route('admin_edit_item', ['item' => $item->id]) }}">{{ $item->style_no }}</a></p>
                                <input type="text" name="sort[]" class="form-control input_sort" value="{{ $item->sorting }}">
                                <input type="hidden" name="ids[]" value="{{ $item->id }}"> 
                            </div>
                        </li>
                    @endforeach
                    </ul>
                </form>
                </div>
            </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="pagination">
                {{ $items->links() }}
            </div>
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

            let searchParams = new URLSearchParams(window.location.search)
           var page= searchParams.has('page')
           page = searchParams.get('page')
            if(page == null || page == 1){
                page=0;
            }  else{
                page = page-1;
            }


            var defaultCategories = <?php echo json_encode($defaultCategories); ?>;
            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);

            var el = document.getElementById('SortItems');
            Sortable.create(el, {
                animation: 150,
                dataIdAttr: 'data-id',
                onEnd: function (th) { 
                    var i =1;
                    $('.short_item').each(function () {  
                        var sort = (((page)*2)+i); 
                        console.log(sort)
                        $(this).find('.input_sort').val(sort);
                        i++;
                    }); 
                },
            });

            $('#btnFilter').click(function () {
                filter();
            });

            $('#btnSave').click(function () {
                $('#form-sort').submit();
            });

            function filter() {
                var url = '{{ route("admin_sort_items_view") }}' + '?sort=';
                url += $('#sort').val();
                url += '&a=' + $('#active').val();
                url += '&c1=' + $('#d_parent_category').val();
                url += '&c2=' + $('#d_second_parent_category').val();
                url += '&c3=' + $('#d_third_parent_category').val();
                url += '&p=' + $('#showPerPage').val();

                window.location.replace(url);
            }


            // Category
            var d_parent_index;
            var d_second_id = '{{ request()->get('c2') }}';
            var d_third_id = '{{ request()->get('c3') }}';

            $('#d_parent_category').change(function () {
                $('#d_second_parent_category').html('<option value="0">All Category</option>');
                $('#d_third_parent_category').html('<option value="0">All Category</option>');
                var parent_id = $(this).val();

                if ($(this).val() != '0') {
                    var index = $(this).find(':selected').data('index');
                    d_parent_index = index;

                    var childrens = defaultCategories[index].subCategories;

                    $.each(childrens, function (index, value) {
                        if (value.id == d_second_id)
                            $('#d_second_parent_category').append('<option data-index="' + index + '" value="' + value.id + '" selected>' + value.name + '</option>');
                        else
                            $('#d_second_parent_category').append('<option data-index="' + index + '" value="' + value.id + '">' + value.name + '</option>');
                    });
                }

                $('#d_second_parent_category').trigger('change');
            });

            $('#d_parent_category').trigger('change');

            $('#d_second_parent_category').change(function () {
                $('#d_third_parent_category').html('<option value="0">All Category</option>');

                if ($(this).val() != '0') {
                    var index = $(this).find(':selected').attr('data-index');

                    var childrens = defaultCategories[d_parent_index].subCategories[index].subCategories;

                    $.each(childrens, function (index, value) {
                        if (value.id == d_third_id)
                            $('#d_third_parent_category').append('<option data-index="' + index + '" value="' + value.id + '" selected>' + value.name + '</option>');
                        else
                            $('#d_third_parent_category').append('<option data-index="' + index + '" value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            });

            $('#d_second_parent_category').trigger('change');
        });
    </script>
@stop