@extends('layouts.home_layout')
@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
@stop
@section('content')
<section class="my_save_section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="headline">
                    <h4>MY SAVES</h4>
                </div>
            </div>
            @if(!empty($items))
                @foreach ( $items as $product )
                    <div class="com-sm-6 col-6 col-md-4 col-lg-2">
                        <div class="save_list">
                            <img src="{{ asset('/' . $product->images[0]['image_path']) }}" alt="Avatar" class="save_list_image img-fluid">
                            <div class="middle">
                                <a  href="{{ route('product_single_page', $product->slug) }}" class="save_list_button">Add to Bag</a>
                                <button type="button" class="save_list_button remove_wishlist" id="remove_{{$product->id}}" title="Remove item" data-id="{{ $product->id }}">Remove</button>
                            </div>
                        </div>
                        <div class="save_prod_info">
                            <a href="{{ route('product_single_page', $product->slug) }}" class="product_name">{{ $product->name }}</a>
                            <p class="product_subtitle">{{ $product->style_no }}</p>
                            <p class="product_price">${{ $product->price }}</p>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="com-sm-4">
                    <div class="save_list">
                        <img src="{{asset('themes/front/images/blank_wish.jpg')}}" alt="Avatar" class="save_list_image">
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
@section('additionalJS')
<script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
<script>
    $(function () {
        $(".customer_rateYo").each(function(){
            var rating = $(this).attr('data-type');
            $(this).rateYo({
                starWidth: "17px",
                rating: rating,
                readOnly: true,
                normalFill: "#cfcfcf",
                ratedFill: "#000"
            });
        });
    });


    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': '{!! csrf_token() !!}'
            }
        });

        var message = '{{ session('message') }}';

        if (message != '')
            toastr.success(message);

        $('.add_to_cart').click(function (e) {
            e.preventDefault();

            var target_id = $(this).attr('id').split('_')[1];

            var ids = [];
            ids.push(target_id);

            if (ids.length > 0) {
                $.ajax({
                    method: "POST",
                    url: "{{ route('wishlist_item_details') }}",
                    data: {id: ids}
                }).done(function (products) {
                    $('#form_new_item').html('');

                    $.each(products, function (i, product) {
                        var html = $('#template-table').html();
                        var item = $(html);

                        if(product.min_qty)
                        item.find('.template-item-name').html(product.style_no + ' - Min Qty: ' + product.min_qty);
                        else
                            item.find('.template-item-name').html(product.style_no);

                        $.each(product.colors, function (ci, color) {
                            if (color.image == ''){
                                var pack_item = '', pack = 0;
                                for(pack=1; pack<=10 ; pack++){
                                    if(product.pack.hasOwnProperty('pack' +pack) && product.pack['pack' +pack] !== null ){
                                        pack_item += product.pack['pack' +pack] + ' '
                                    }
                                }
                                item.find('.item_colors_table').append('<tr><td>'+ pack_item +'</td><th>' + color.name + '</th><td><input name="colors[]" class="input_color" type="text"><input type="hidden" name="ids[]" value="' + product.id + '"><input type="hidden" name="colorIds[]" value="' + color.id + '"></td></tr>');
                            }
                            else
                                item.find('.item_colors_table').append('<tr><td><img src="' + color.image + '" width="30px"></td><th>' + color.name + '</th><td><input name="colors[]" class="input_color" type="text"><input type="hidden" name="ids[]" value="' + product.id + '"><input type="hidden" name="colorIds[]" value="' + color.id + '"></td></tr>');

                        });


                        $('#form_new_item').append(item);
                    });

                    $('#color-modal').modal('show');
                });
            }
        });

        $('#btnItemAdd').click(function () {
            var error = false;

            $('.input_color').each(function () {
                if (error)
                    return;

                var count = $(this).val();

                if (count != '' && !isInt(count)) {
                    error = true;
                    return alert('Invalid input.');
                }
            });
            console.log($('#form_new_item').serialize());


            if (!error) {
                $.ajax({
                    method: "POST",
                    url: "{{ route('wishlist_add_to_cart') }}",
                    data: $('#form_new_item').serialize()
                }).done(function (data) {
                    if (data.success)
                        location.reload();
                    else{
                        alert(data.message);
                    }
                });
            }
        });

        function isInt(value) {
            return !isNaN(value) && (function (x) {
                return (x | 0) === x;
            })(parseFloat(value))
        }

        $('.remove_wishlist').click(function (e) {
            e.preventDefault();
            var id = $(this).attr('id').split('_')[1];

            $.ajax({
                method: "POST",
                url: "{{ route('remove_from_wishlist') }}",
                data: { id: id }
            }).done(function( data ) {
                toastr.success('Removed from Wishlist.');
                setTimeout(function () {
                    location.reload();
                }, 1000);
            });
        });
    });
</script>
@stop