@extends('layouts.home_layout')

@section('additionalCSS')

@stop

@section('content')
    <!-- =========================
        START CART SECTION
    ============================== -->
    <section class="cart_area common_top_margin">
        <div class="cart_wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <?php $itemInPack  = $subTotal = 0; ?>
                        <div class="cart_inner clearfix">
                            <h2>Shopping Bag</h2>
                            <div class="cart_table for_desktop">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th scope="col">ITEM</th>
                                            <th scope="col">QUANTITY</th>
                                            <th scope="col">UNIT PRICE</th>
                                            <th scope="col">TOTAL</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(count($cartItems)>0)
                                            @foreach($cartItems as $item_index => $items)
                                        <tr>
                                            <td>
                                                <div class="cart_inner_content clearfix">
                                                    <div class="cart_inner_img">
                                                        @php $img_path = 0; $flash=0; @endphp
                                                        @if (sizeof($items->item->images) > 0)
                                                            @foreach($items->item->images as $image)
                                                                @if($image->color_id == $items->color_id)

                                                                    <?php
                                                                    $flash=1;
                                                                    $img_path = $image->list_image_path;
                                                                    ?>
                                                                    @break
                                                                @endif
                                                            @endforeach
                                                            @if($flash == 0)
                                                                    <a href="#"><img src="{{ asset($items->item->images[0]->image_path) }}" alt="" class="img-fluid"></a>
                                                            @else
                                                                    <a href="#"><img src="{{ asset($img_path) }}" alt="" class="img-fluid"></a>
                                                            @endif
                                                        @else
                                                            <a href="#"> <img src="{{ asset('images/no-image.png') }}" alt="" width="100px" class="img-fluid"></a>
                                                        @endif
                                                    </div>
                                                    <div class="cart_inner_text">
                                                        <h3><a href="{{ route('product_single_page', $items->item->slug) }}">{{ $items->item->style_no }}</a></h3>
                                                        <?php
                                                        $sizes = explode("-", $items->item->pack->name);
                                                        $itemInPack = 0;
                                                        for($i=1; $i <= sizeof($sizes); $i++) {
                                                            $var = 'pack'.$i;
                                                            if ($items->item->pack->$var != null)
                                                                $itemInPack += (int) $items->item->pack->$var;
                                                        }
                                                        ?>
                                                        <ul>
                                                            <li> Size: {{ join(' ', $sizes) }} /
                                                                @for ($i = 1; $i <= sizeof($sizes); $i++)
                                                                    <?php $p = 'pack'.$i; ?>
                                                                    {{ ($items->item->pack->$p != null) ? $items->item->pack->$p : 0 }}
                                                                @endfor </li>
                                                            <li>Color: {{$items->color->name}}</li>
                                                            <li>
                                                                @foreach($items->inventory as $inv)
                                                                    @if($inv->color_id == $items->color_id && $inv->item_id == $items->item_id)
                                                                        @if($inv->available_on != 'null')
                                                                        pre order: {{$inv->available_on}}
                                                                        @endif
                                                                    @break
                                                                    @endif
                                                                @endforeach
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <span><a class="text-danger remove-from-cart btnDelete" href="#"
                                                             data-toggle="tooltip"
                                                             title="Remove item" data-id="{{ $items->id }}">Remove</a></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="cart_number">
                                                    <input type="number"  class="input-number pack input_qty form-control" placeholder="1"
                                                           value="{{ $items->quantity }}"
                                                           data-per-pack="{{ $itemInPack }}"
                                                           data-price="{{ $items->item->price }}"
                                                           data-id="{{ $items->id }}"
                                                           min="0"
                                                           max="1000"
                                                           name="input-pack[{{ $items->id }}]">
                                                    <a href="#" id="btnUpdate" class="">Update</a>
                                                </div>
                                            </td>
                                            <td>  ${{ sprintf('%0.2f', $items->item->price) }}</td>
                                            <td class="total_amount">$<?php echo e(sprintf('%0.2f', $items->item->price * $itemInPack * $items->quantity)); ?></td>
                                            <?php $subTotal += $items->item->price * $itemInPack * $items->quantity; ?>

                                        </tr>

                                        @endforeach

                                        @else
                                            <tr><td>No Record Found</td></tr>
                                        @endif
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="3" >Subtotal:</td>
                                            <td colspan="4" class="total"></td>
                                        </tr>

                                        </tfoot>
                                    </table>
                                    <?php if ( Auth::user()->storeCredit() > 0 ) : ?>
                                    <span class="float-right mb-3 text-danger"> You have ${{ number_format(Auth::user()->storeCredit(), 2, '.', '') }} store credits.</span>
                                    <?php endif; ?>
                                </div>

                            </div>
                            <div class="cart_table for_mobile">
                                <div class="cart_mobile_wrapper clearfix">
                                    @if(count($cartItems)>0)
                                        @foreach($cartItems as $item_index => $items)
                                    <div class="cart_mobile clearfix">
                                        <div class="cart_mobile_img">
                                            @php $img_path = 0; $flash=0; @endphp
                                            @if (sizeof($items->item->images) > 0)
                                                @foreach($items->item->images as $image)
                                                    @if($image->color_id == $items->color_id)

                                                        <?php
                                                        $flash=1;
                                                        $img_path = $image->list_image_path;
                                                        ?>
                                                        @break
                                                    @endif
                                                @endforeach
                                                @if($flash == 0)
                                                   <img src="{{ asset($items->item->images[0]->image_path) }}" alt="" class="img-fluid">
                                                @else
                                                    <img src="{{ asset($img_path) }}" alt="" class="img-fluid">
                                                @endif
                                            @else
                                               <img src="{{ asset('images/no-image.png') }}" alt="" width="100px" class="img-fluid">
                                            @endif
                                        </div>
                                        <div class="cart_mobile_text">
                                            <ul class="cart_style">
                                                <li>Style No. {{ $items->item->style_no }}</li>
                                                <!--<li>Name: {{ $items->item->name }}</li>-->
                                                <li>color:  {{$items->color->name}}</li>
                                                <li>
                                                    @foreach($items->inventory as $inv)
                                                        @if($inv->color_id == $items->color_id && $inv->item_id == $items->item_id)
                                                            @if($inv->available_on != null)
                                                            pre order: {{$inv->available_on}}
                                                            @endif
                                                        @break
                                                        @endif
                                                    @endforeach
                                                </li>
                                            </ul>
                                            <ul class="cart_size">
                                                <?php
                                                $sizes = explode("-", $items->item->pack->name);
                                                $itemInPack = 0;
                                                for($i=1; $i <= sizeof($sizes); $i++) {
                                                    $var = 'pack'.$i;
                                                    if ($items->item->pack->$var != null)
                                                        $itemInPack += (int) $items->item->pack->$var;
                                                }
                                                ?>
                                                <li>Size:  <span> {{ join(' ', $sizes) }} /
                                                                @for ($i = 1; $i <= sizeof($sizes); $i++)
                                                            <?php $p = 'pack'.$i; ?>
                                                            {{ ($items->item->pack->$p != null) ? $items->item->pack->$p : 0 }}
                                                        @endfor</span>
                                                    <span><a class="text-danger remove-from-cart btnDelete" href="#"
                                                             data-toggle="tooltip"
                                                             title="Remove item" data-id="{{ $items->id }}">Remove</a></span></li>
                                                <li>quantity  <span>
                                                    <div class="cart_number">
                                                        <input type="number"  class="input-number pack input_qty_m form-control" placeholder="1"
                                                               value="{{ $items->quantity }}"
                                                               data-per-pack-m="{{ $itemInPack }}"
                                                               data-price-m="{{ $items->item->price }}"
                                                               data-id-m="{{ $items->id }}"
                                                               min="0"
                                                               max="1000"
                                                               name="input-pack-m[{{ $items->id }}]">
                                                        <a href="#" id="btnUpdateM">Update</a>
                                                    </div>
                                            </span> </li>
                                                <li>Total  <span class="total_amount total_amount_m">$<?php echo e(sprintf('%0.2f', $items->item->price * $itemInPack * $items->quantity)); ?></span>
                                                    <?php $subTotal += $items->item->price * $itemInPack * $items->quantity; ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                        @endforeach

                                    @else
                                        <tr><td>No Record Found</td></tr>
                                    @endif

                                    <div class="cart_subtotal text-right total">

                                    </div>

                                </div>

                            </div>

                            <div class="checkout_btn text-right">
                                <button type="submit" class="btn btn-default common_btn btnCheckout" >checkout</button>
                            </div>
{{--                            <div class="checkout_btn text-right">--}}
{{--                                <button type="submit" class="btn btn-default common_btn">continue shopping</button>--}}
{{--                            </div>--}}
{{--                            <div class="cart_bottom_list">--}}
{{--                                <ul>--}}
{{--                                    <li><a href="#">Shipping & Returns</a></li>--}}
{{--                                    <li><a href="#">Customer Service</a></li>--}}
{{--                                </ul>--}}
{{--                            </div>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================
        END CART SECTION
    ============================== -->
@endsection

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script>
        $(function () {
            $('#cartSuccessMessage').hide();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': '{!! csrf_token() !!}'
                }
            });


            // Quentity Increament decriment button start
             $('.btn-number').click(function(e){
                e.preventDefault();
                var itemInPack = {{ $itemInPack }};
                var perPrice =  parseFloat('<?php if(!empty($items->item->price)){  ?> {{ $items->item->price }} <?php } ?>') ;
                var totalQty = 0;
                var i=0;
                var itemId =  '<?php if(!empty($items->item->price)){ ?> {{ $items->item->id }} <?php }  ?>';

                fieldName = $(this).attr('data-field');
                type      = $(this).attr('data-type');
                var input = $("input[name='"+fieldName+"']");
                var currentVal = parseInt(input.val());
                if (!isNaN(currentVal)) {
                    if(type == 'minus') { 
                        if(currentVal > input.attr('min')) {
                            input.val(currentVal - 1).change();
                            i=currentVal - 1;
                        } 
                        if(parseInt(input.val()) == input.attr('min')) {
                            $(this).attr('disabled', true);
                        }  
                        calculate();

                        $(this).closest('tr').find('.total_qty').html(itemInPack * i);
                        $(this).closest('tr').find('.total_amount').html('$' + (itemInPack * i * perPrice).toFixed(2));
                        $(this).closest('tr').find('.sub_total').val(itemInPack * i * perPrice);

                    } else if(type == 'plus') { 
                        if(currentVal < input.attr('max')) {
                            input.val(currentVal + 1).change();
                            i=currentVal + 1;
                        }
                        if(parseInt(input.val()) == input.attr('max')) {
                            $(this).attr('disabled', true);
                        } 
                        calculate();
                        $(this).closest('tr').find('.total_qty').html(itemInPack * i);
                        $(this).closest('tr').find('.total_amount').html('$' + (itemInPack * i * perPrice).toFixed(2));
                        $(this).closest('tr').find('.sub_total').val(itemInPack * i * perPrice);
                    }

                    calculate(); 
                    var summation = 0;
                    $( ".price" ).each(function( index ) {
                        summation += parseFloat($(this).text().substring(1));
                    });
                    $("#total_sum").html('$' + summation.toFixed(2));

                    var qty_sum = 0;
                    $( ".row_qty" ).each(function( index ) {
                        qty_sum += parseInt($(this).text());
                    });
                    $("#total_qty").html(qty_sum.toFixed(2));

                    $(this).focus();
                }else{
                    input.val(0);
                }
            });
            // Quentity Increament decriment button exit

            var message = '{{ session('message') }}'; 
            if (message != '')
                toastr.success(message);

            $('.input_qty').keyup(function () {
                var index = $('.input_qty').index($(this));
                var perPack = parseInt($(this).data('per-pack'));
                var price = $(this).data('price');
                var i = 0;
                var val = $(this).val();

                if (isInt(val)) {
                    i = parseInt(val);

                    if (i < 0)
                        i = 0;
                }


                // $('.total_qty:eq('+index+')').html(perPack * i);
                $('.total_amount:eq('+index+')').html('$' + (perPack * i * price).toFixed(2));

                calculate();
            });
            $('.input_qty_m').keyup(function () {
                var index = $('.input_qty_m').index($(this));
                var perPack = parseInt($(this).data('per-pack-m'));
                var price = $(this).data('price-m');
                var i = 0;
                var val = $(this).val();

                if (isInt(val)) {
                    i = parseInt(val);

                    if (i < 0)
                        i = 0;
                }


                $('.total_qty:eq('+index+')').html(perPack * i);
                $('.total_amount_m:eq('+index+')').html('$' + (perPack * i * price).toFixed(2));

                calculateM();
            });

            $(document).on('click','#btnUpdate',function () {
                var ids = []; 
                var qty = [];

                var valid = true;
                $('.input_qty').each(function () {
                    var i = 0;
                    var val = $(this).val();

                    if (isInt(val)) {
                        i = parseInt(val);

                        if (i < 0)
                            return valid = false;
                    } else {
                        return valid = false;
                    }

                    ids.push($(this).data('id'));
                    qty.push(i);
                });

                if (!valid) {
                    alert('Invalid Quantity.');
                    return;
                }

                $.ajax({
                    method: "POST",
                    url: "{{ route('update_cart') }}",
                    data: { ids: ids, qty: qty }
                }).done(function( data ) {
                    var message = '<p>Cart Updated Successfully.</p>';
                    if (data.success) {
                        $('#cartSuccessMessage').slideDown('slow',function(){
                            $('#message').html(message);
                        });
                        setTimeout(function () {
                            $('#cartSuccessMessage').slideUp('slow');
                            location.reload();
                        }, 3000);
                        
                    } else {
                        alert(data.message);
                    }
                });
            });

            $(document).on('click','#btnUpdateM',function () {
                var ids = [];
                var qty = [];

                var valid = true;
                $('.input_qty_m').each(function () {
                    var i = 0;
                    var val = $(this).val();

                    if (isInt(val)) {
                        i = parseInt(val);

                        if (i < 0)
                            return valid = false;
                    } else {
                        return valid = false;
                    }

                    ids.push($(this).data('id-m'));
                    qty.push(i);
                });

                if (!valid) {
                    alert('Invalid Quantity.');
                    return;
                }

                $.ajax({
                    method: "POST",
                    url: "{{ route('update_cart') }}",
                    data: { ids: ids, qty: qty }
                }).done(function( data ) {
                    var message = '<p>Cart Updated Successfully.</p>';
                    if (data.success) {
                        $('#cartSuccessMessage').slideDown('slow',function(){
                            $('#message').html(message);
                        });
                        setTimeout(function () {
                            $('#cartSuccessMessage').slideUp('slow');
                            location.reload();
                        }, 3000);

                    } else {
                        alert(data.message);
                    }
                });
            });

            $('.btnDelete').click(function () {
                var id = $(this).data('id');

                $.ajax({
                    method: "POST",
                    url: "{{ route('delete_cart') }}",
                    data: { id: id }
                }).done(function( data ) {
                    location.reload();
                });
            });

            $('.btnCheckout').click(function (e) {
                e.preventDefault();
                var vendorId = [$(this).data('vendor-id')];
                var storeCredit = $('#discount_amount').val(); 
                 
                  
                $.ajax({
                    method: "POST",
                    url: "{{ route('create_checkout') }}",
                    data: { storeCredit: storeCredit },
                }).done(function( data ) {
                    if (data.success==true){ 
                        window.location.replace("{{ route('show_checkout') }}" + "?id=" + data.message);
                    } 
                    else{ 
                        alert(data.message);
                    }
                });
            });

            function calculate() {
                var subTotal = 0;

                $('.input_qty').each(function () {
                    var perPack = parseInt($(this).data('per-pack'));
                    var price = $(this).data('price');
                    var i = 0;
                    var val = $(this).val();

                    if (isInt(val)) {
                        i = parseInt(val);

                        if (i < 0)
                            i = 0;
                    }

                    subTotal += perPack * i * price;
                });

                var promotion = 0;
                var type = 0;
                var amount = 0;   
                promotion = $('#promotion').val(); 
                type =  $('#type').val(); 
                amount = $('#amount').val(); 

                var Tcredit=0;
                if(promotion != '' && subTotal >= promotion){
                   if(type==2){
                        Tcredit = parseFloat(subTotal * amount/100);
                   }else{
                        Tcredit = parseFloat(amount);
                   }
                }
                 

                if(isNaN(promotion))
                    promotion = 0;


                var total = subTotal-Tcredit;

                if (total < 0)
                    total = 0;

                $('#discount_amount').val('$' + Tcredit.toFixed(2));
                $('.discount').html('$' + Tcredit.toFixed(2));
                $('.sub_total').html('$' + addCommas(subTotal.toFixed(2)));
                $('.total').html('$' + addCommas(total.toFixed(2)));
            }

            function calculateM() {
                var subTotal = 0;

                $('.input_qty_m').each(function () {
                    var perPack = parseInt($(this).data('per-pack-m'));
                    var price = $(this).data('price-m');
                    var i = 0;
                    var val = $(this).val();

                    if (isInt(val)) {
                        i = parseInt(val);

                        if (i < 0)
                            i = 0;
                    }

                    subTotal += perPack * i * price;
                });

                var promotion = 0;
                var type = 0;
                var amount = 0;
                promotion = $('#promotion').val();
                type =  $('#type').val();
                amount = $('#amount').val();

                var Tcredit=0;
                if(promotion != '' && subTotal >= promotion){
                   if(type==2){
                        Tcredit = parseFloat(subTotal * amount/100);
                   }else{
                        Tcredit = parseFloat(amount);
                   }
                }


                if(isNaN(promotion))
                    promotion = 0;


                var total = subTotal-Tcredit;

                if (total < 0)
                    total = 0;

                $('#discount_amount').val('$' + Tcredit.toFixed(2));
                $('.discount').html('$' + Tcredit.toFixed(2));
                $('.sub_total').html('$' + addCommas(subTotal.toFixed(2)));
                $('.total').html('$' + addCommas(total.toFixed(2)));
            }

            calculate();
            calculateM();

            function isInt(value) {
                return !isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value))
            }

            $('.store_credit').keyup(function () {
                calculate();
            });
        });

        function addCommas(nStr) {
            nStr += '';
            x = nStr.split('.');
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            return x1 + x2;
        }
    </script>
@stop