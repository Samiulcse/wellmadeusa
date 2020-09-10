@extends('layouts.home_layout')

@section('additionalCSS')

@stop

@section('content')

    <!-- =========================
        START CHECKOUT SECTION
    ============================== -->
    <section class="checkout_area common_top_margin">
        <div class="checkout_wrapper">
            <div class="container-fluid">
                <form action="{{ route('single_checkout_post') }}" method="POST">
                <div class="row">
                    @csrf
                    <input type="hidden" name="id" value="{{ request()->get('id') }}" id="orders">
                    <div class="col-xl-4">
                        <div class="checkout_inner">
                            <h2>Shipping Options</h2>
                            <input type="hidden" id="free_shipping" name="free_shipping" value="{{ $order->free_shipping }}">
                            <p>Select the address that matches your card or payment method.</p>
                            <div class="checkout_tab mb-3">
                                <table class="table">
                                    <thead class="thead-default">
                                    <tr>
                                        <th></th>
                                        <th>Shipping method</th>
                                        <th>Fee</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($shipping_methods as $shipping_method)
                                    <tr>
                                        <td class="align-middle">
                                            <div class="custom_radio">
                                                <input class=" shipping_method" type="radio"
                                                       id="{{ $shipping_method->id }}" name="shipping_method"
                                                       value="{{ $shipping_method->id }}" data-index="{{ $loop->index }}"
                                                        {{ old('shipping_method') == $shipping_method->id ? 'checked' : '' }}>
                                                <label class=""
                                                       for="{{ $shipping_method->id }}"></label>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            @if(!empty($shipping_method->courier->name))
                                                <span class="text-medium">{{ $shipping_method->courier->name }}</span><br>
                                            @endif
                                            <span class="text-muted text-sm">{{ $shipping_method->name }}</span>
                                        </td>
                                        <td>
                                            @if ($shipping_method->fee === null)
                                                Actual Rate
                                            @else
                                                ${{ number_format($shipping_method->fee, 2, '.', '') }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                @if ($errors->has('shipping_method'))
                                    <div class="form-control-feedback text-danger">Select a shipping method</div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="checkout_inner ">
                            <p class="text-muted">
                                Flat rate prices are for Continental US ONLY <br>
                                Prices for Expedited shipping will be determined by weight, dimensions, and shipping address
                            </p>
                            <textarea name="order_note" class="form-control" id="" cols="30" rows="10"
                                      placeholder="Order Notes"></textarea>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="checkout_inner shipping_address">
                            <h2>Shipping Address</h2>
                            <div class="row">
                                <div class="col-12 col-sm-12">
                                    <p id="address_text">
                                        @if ($address != null)
                                            {{ $address->address }}, {{ $address->city }}, {{ ($address->state == null) ? $address->state_text : $address->state->name }},
                                            <br>
                                            {{ $address->country->name }} - {{ $address->zip }}
                                        @endif
                                    </p>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <button class="btn common_btn" id="btnAddShippingAddress">Add New Shipping Address</button>
                                    <input type="hidden" name="address_id" value="{{ ($address != null) ? $address->id : '' }}" id="address_id">
                                </div>
                                <div class="col-12 col-sm-6 text-right">
                                    <button class="btn common_btn"  id="btnChangeAddress">Change</button>
                                </div>
                            </div>
                        </div>
                        <div   div class="checkout_inner">
                            <h2>Billing Address</h2>
                            <input type="hidden" name="billing_address_id" value="{{ ($default_billingaddress != null) ? $default_billingaddress->id : '' }}" id="billing_address_id">
                            @if ($errors->has('billing_address_id'))
                                <div class="form-control-feedback">Select a billing address.</div>
                            @endif
                            <div class="row">
                                <div class="col-12 col-sm-12">
                                    <p id="billing_address_text">
                                        @if ($default_billingaddress != null)
                                            {{ $default_billingaddress->billing_address }}, 
                                            {{ $default_billingaddress->billing_city }}, 
                                            {{ ($default_billingaddress->billing_state_id == null) ? $default_billingaddress->billing_state : $default_billingaddress->state->name }},
                                            <br>
                                            {{ $default_billingaddress->country ? $default_billingaddress->country->name : ''}} - {{ $default_billingaddress->billing_zip }}
                                        @endif
                                    </p>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <a class="btn common_btn" data-toggle="modal" data-target="#btnAddBillingAddress">Add New Billing Address</a>
                                </div>
                                <div class="col-12 col-sm-6 text-right">
                                    <button class="btn common_btn"  id="btnChangeBillingAddress">Change</button>
                                </div>
                            </div> 

                        </div>
                        <div class="checkout_inner checkout_product">
                            <h2>Payment Method</h2>
                            <p>All transactions are secure and encrypted.</p>
                            <div class="payment_method form_global __stripre">
                                <div class="form-group">
                                    <div class="checkout_radio_btn">
                                        <img src="{{asset('images/visa.svg')}}" alt="" class="img-fluid">
                                        <img src="{{asset('images/master-card.svg')}}" alt="" class="img-fluid">
                                        <img src="{{asset('images/american_express.svg')}}" alt="" class="img-fluid">
                                        <img src="{{asset('images/discover.svg')}}" alt="" class="img-fluid">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" id="paymentMethod" name="paymentMethod" value="2">
                                    @if ($errors->has('number'))
                                        <div class="form-control-feedback text-danger">Invalid card number.</div>
                                    @endif
                                    @if ($errors->has('name'))
                                        <div class="form-control-feedback text-danger">Your name is required.</div>
                                    @endif
                                    @if ($errors->has('expiry'))
                                        <div class="form-control-feedback text-danger">Invalid Expiry</div>
                                    @endif
                                    @if ($errors->has('cvc'))
                                        <div class="form-control-feedback text-danger">Invalid CVC</div>
                                    @endif
                                </div>
                                <div class="form-group {{ $errors->has('name') ? ' has-danger' : '' }}">
                                    <label>Card Holder's Name:</label>
                                    <input class="form-control" type="text" name="name" placeholder="Full Name" value="{{ empty(old('name')) ? ($errors->has('name') ? '' : $order->card_full_name) : old('name') }}">
                                </div>
                                <div class="form-group {{ $errors->has('number') ? ' has-danger' : '' }}">
                                    <label>Card Number:</label>
                                    <input class="form-control" type="text" name="number" placeholder="Card Number" value="{{ empty(old('number')) ? ($errors->has('number') ? '' : $order->card_number) : old('number') }}">
                                </div>
                                <div class="form-row mb_25">
                                    <div class="form-group col-lg-6 {{ $errors->has('expiry') ? ' has-danger' : '' }}">
                                        <label>Expiration Date:</label>
                                        <input class="form-control" type="text" name="expiry" placeholder="MM/YY" data-inputmask="'mask': '99/99'" id="expiry" value="{{ empty(old('expiry')) ? ($errors->has('expiry') ? '' : $order->card_expire) : old('expiry') }}">
                                    </div>
                                    <div class="form-group col-lg-6  {{ $errors->has('cvc') ? ' has-danger' : '' }}">
                                        <label> Secure Code (CVV): <span><i class="fas fa-question"></i></span></label>
                                        <input class="form-control" type="text" name="cvc" placeholder="CVC"  value="{{ empty(old('cvc')) ? ($errors->has('cvc') ? '' : $order->card_cvc) : old('cvc') }}">
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <p>CVV2 is that extra set of numbers after the normal 16 or 14 digits of the account usually printed on the back of the credit card. The "CVV2 security code", as it is formally referred to, provides an extra measure of security and we require it on all transactions.</p>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-xl-4">
                        <div class="checkout_inner table-responsive checkout_product">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>price</th>
                                    <th>Pack</th>
                                    <th>Qty</th>
                                    <th class="text-right">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                    
                                     <?php $total_pack = $total_qty = 0;?>
                                @foreach($order->items as $item) 
                                    <?php
                                    $thumb = null;
                                    for($i=0; $i < sizeof($item->item->images); $i++) {
                                        if ($item->item->images[$i]->color != null) {
                                            if ($item->item->images[$i]->color->name == $item->color) {
                                                $thumb = $item->item->images[0]->compressed_image_path;
                                                break;
                                            }
                                        }
                                    }
                                    if($thumb == null){
                                        $thumb = $item->item->images[0]->compressed_image_path;
                                    }else{
                                        $thumb = $thumb;
                                    }
                                    ?>
                                <tr>
                                    <td>
                                        @if ($thumb)
                                            <img src="{{ asset($thumb) }}" alt="" class="img-fluid">
                                        @else
                                            <img src="{{ asset('images/no-image.png') }}" alt="Product" width="100px">
                                        @endif
                                    </td>
                                    <td>
                                        <span >${{ number_format($item->per_unit_price, 2, '.', '') }} </span>
                                    </td>
                                    <td>{{$item->qty}} <?php $total_pack += $item->qty; $total_qty += $item->total_qty;?></td>
                                    <td>{{ $item->total_qty }}</td>
                                    <td class="text-right ng-binding">${{ number_format($item->per_unit_price * $item->total_qty, 2, '.', '') }}</td>
                                </tr>
                                @endforeach
                                    <tr>
                                        <td> <b>Total</b></td>
                                        <td></td>
                                        <td ><b>{{$total_pack}}</b></td>
                                        <td><b>{{$total_qty}}</b></td>
                                        <td class="text-right"><b>${{ number_format($order->total, 2, '.', '') }}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="checkout_inner order_amounts">
                            <h2>Promotion</h2>
                            <h4>Coupon Code</h4>
                            <div class="promo_code">
                                <input class="form-control promote_input" placeholder="Enter Promo code" id="coupon_{{ $order->id }}" value="{{ $order->coupon }}" name="code">
                                <input type="submit" class="btn  promo_code_btn btnApplyCoupon" id="promo_code_btn" data-order-id="{{ $order->id }}" disabled="disabled" value="Apply">
                            </div>
                            @if(isset($rewardOffer) && count($rewardOffer) > 0)
                                <div class="content margin-bottom-1x">
                                    <h4>Reward Point</h4>
                                    <select name="reward_point" id="reward_point" class="grid_point_select form-control mb-3">
                                        <option disabled="" selected="">Select points</option>
                                        @foreach($rewardOffer as $reward)
                                            <option value="{{$reward->id}}" data-index="{{ $loop->index }}" data-freeship="{{$reward->free_shipping_1}}">
                                                @if($reward->free_shipping_1 != 0)
                                                    {{ $reward->from_price_1 }} Points - Free Shipping 
                                                @elseif($reward->point_type === 'Unit price discount by order amount')
                                                    {{ $reward->from_price_1 }} Points - $ {{ $reward->unit_price_discount_1 }} discount
                                                @elseif($reward->point_type === 'Percentage discount by order amount')
                                                    {{ $reward->from_price_1 }} Points - {{ $reward->percentage_discount_1 }} % discount
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <?php if ( Auth::user()->storeCredit() > 0 ) : ?>
                            <h4>Store Credit</h4>
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td>
                                        <span >Store Credit - ${{ number_format(Auth::user()->storeCredit(), 2, '.', '') }}:</span>
                                        <input type="hidden" name="max_store_credit" value="{{ number_format(Auth::user()->storeCredit(), 2, '.', '') }}"><br>
                                        <span class="max_credit_limit"> </span>
                                        <span style="color:red;font-size:11px;">{{ $errors->has('store_credit') ? 'Invalid Stoer Credit.' : '' }}</span>
                                    </td>
                                    <td >
                                        <input type="text" class="form-control store_credit_input store_credit" placeholder="$" name="store_credit" value="{{ empty(old('store_credit')) ? ($errors->has('store_credit') ? '' : $order->store_credit) : old('store_credit') }}">
                                    </td>

                                </tr>
                                </tbody>
                            </table>
                            <?php endif; ?>
                        </div>
                        <div class="checkout_inner checkout_payment">
                            <h2>Payment</h2>
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td>
                                        <span >Subtotal:</span>

                                    </td>
                                    <td class="text-right">${{ number_format($order->subtotal, 2, '.', '') }}</td>
                                </tr>
                                @if ($order->discount != null || $order->discount != 0)
                                    @if(!empty($order->discount))
                                    <tr>
                                        <td>
                                            <span >Discount:</span>

                                        </td>
                                        <td class="text-right" >-$<span id="order-discount">{{ number_format($order->discount, 2, '.', '') }}</span></td>
                                    </tr>
                                    @endif
                                @endif
                                <tr id="rewardDiscountTd"></tr>
                                <tr>
                                    <td>
                                        <span >Shipping Cost:</span>
                                    </td>
                                    <td class="text-right" id="shippingCost">${{ number_format($order->shipping_cost, 2, '.', '') }}</td>
                                </tr>

                                <tr class="total_price">
                                    <td>
                                        <span class="checkout_payment_total "><b>Total:</b> </span>
                                    </td>
                                    <td class="text-right" id="total">${{ number_format($order->total, 2, '.', '') }}</td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="form-group form_checkbox">
                                <div class="custom_checkbox">
                                    <input type="checkbox" name="checkbox-agree" id="checkbox-agree"  checked >
                                    <label for="checkbox-agree">By Selecting this box and clicking the "Place My Order button", I agree that I have read the Policy. Your order may not be complete! Would you like us to contact you before shipping your order?</label>
                                </div>
                            </div>
                            <div class="continue_checkout">
                                <button type="submit" class="btn btn-default common_btn" id="btnSubmit">PLACE MY ORDER </button>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </section>
    <!-- =========================
        END CHECKOUT SECTION
    ============================== -->

    <!-- add new billng address modal start -->
<div class="modal shipping_modal" id="btnAddBillingAddress">
        <div class="modal-dialog modal-lg" role="document">
            <form id="newbillingaddress">
                <input type="hidden" id="editAddressId" name="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Billing Address</h4>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"> </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-row">
                                        <div class="form-group form_radio">
                                            <label for="small-rounded-input">United States</label><br>
                                            <div class="custom_radio">
                                                <input class="custom-control-input factoryLocation" type="radio" id="factoryLocationUS" name="factoryLocation" value="US" {{ empty(old('factoryLocation')) ? ($buyerBillingInfo->billing_location == "US" ? 'checked' : '') :
                                                (old('factoryLocation') == 'US' ? 'checked' : '') }}>
                                                <label class="custom-control-label" for="factoryLocationUS">United States</label>
                                            </div>

                                            <div class="custom_radio">
                                                <input class="custom-control-input factoryLocation" type="radio" id="factoryLocationCA" name="factoryLocation" value="CA" {{ empty(old('factoryLocation')) ? ($buyerBillingInfo->billing_location == "CA" ? 'checked' : '') :
                                                        (old('factoryLocation') == 'CA' ? 'checked' : '') }}>
                                                <label class="custom-control-label" for="factoryLocationCA">Canada</label>
                                            </div>

                                            <div class="custom_radio">
                                                <input class="custom-control-input factoryLocation" type="radio" id="factoryLocationInt" name="factoryLocation" value="INT" {{ empty(old('factoryLocation')) ? ($buyerBillingInfo->billing_location == "INT" ? 'checked' : '') :
                                                        (old('factoryLocation') == 'INT' ? 'checked' : '') }}>
                                                <label class="custom-control-label" for="factoryLocationInt">International</label>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="form-row">
                                        <div class="form-group hasvalue col-md-6" id="form-group-factoryAddress">
                                            <label for="factoryAddress">Address</label>
                                            <input class="form-control  " type="text" id="factoryAddress" name="factoryAddress" value="{{ empty(old('factoryAddress')) ? ($errors->has('factoryAddress') ? '' : $buyerBillingInfo->billing_address) : old('factoryAddress') }}" placeholder="Address">
                                            @if ($errors->has('factoryAddress'))
                                                <div class="form-control-feedback">{{ $errors->first('factoryAddress') }}</div>
                                            @endif
                                        </div>
                                    
                                        <div class="form-group hasvalue col-md-6" id="form-group-factoryUnit">
                                            <label for="factoryUnit">Unit</label>
                                            <input class="form-control" type="text" id="factoryUnit" name="factoryUnit" value="{{ empty(old('factoryUnit')) ? ($errors->has('factoryUnit') ? '' : $buyerBillingInfo->billing_unit) : old('factoryUnit') }}" placeholder="Unit">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group hasvalue col-md-6" id="form-group-factoryCity">
                                            <label for="factoryCity">City</label>
                                            <input class="form-control" type="text" id="factoryCity" name="factoryCity" value="{{ empty(old('factoryCity')) ? ($errors->has('factoryCity') ? '' : $buyerBillingInfo->billing_city) : old('factoryCity') }}" placeholder="City">
                                            @if ($errors->has('factorycity'))
                                                <p class="text-danger">{{ $errors->first('factorycity') }}</p>
                                            @endif
                                        </div>
                                    
                                        <div class="form-group col-md-6" id="form-group-factory-state" style="display: none;">
                                            <label for="factoryState">State</label>
                                            <input class="form-control" type="text" id="factoryState" name="factoryState" value="{{ empty(old('factoryState')) ? ($errors->has('factoryState') ? '' : $buyerBillingInfo->billing_state) : old('factoryState') }}" placeholder="Enter state">

                                            @if ($errors->has('factoryState'))
                                                <div class="form-control-feedback">{{ $errors->first('factoryState') }}</div>
                                            @endif
                                        </div> 
                                        <div class="form-group col-md-6" id="form-group-factory-state-select" >
                                            <label for="factoryStateSelect">Select State</label>
                                            <select class="form-control " id="factoryStateSelect" name="factoryStateSelect">
                                                <option value="">Select State</option>
                                            </select>

                                            @if ($errors->has('factoryStateSelect'))
                                                <div class="form-control-feedback">{{ $errors->first('factoryStateSelect') }}</div>
                                            @endif
                                        </div>
                                        </div>
                                        <div class="form-row">
                                        <div class="form-group col-md-6" id="form-group-factory-factoryCountry">
                                            <label>Country</label>
                                            <select class="form-control " id="factoryCountry" name="factoryCountry">
                                                <option value="">Select Country </option>
                                                @foreach($countries as $country)
                                                    <option data-code="{{ $country->code }}" value="{{ $country->id }}"  {{ empty(old('factoryCountry')) ? ($errors->has('factoryCountry') ? '' : ($buyerBillingInfo->billing_country_id == $country->id ? 'selected' : '')) :
                                                            (old('factoryCountry') == $country->id ? 'selected' : '') }}>{{ $country->name }}</option>
                                                @endforeach
                                            </select>
    
                                            @if ($errors->has('factoryCountry'))
                                                <div class="form-control-feedback">{{ $errors->first('factoryCountry') }}</div>
                                            @endif
                                        </div>
                                    
                                        <div class="form-group hasvalue col-md-6" id="form-group-factoryZipCode">
                                            <label for="factoryZipCode">ZIP / Postal Code</label>
                                            <input class="form-control " type="text" id="factoryZipCode" name="factoryZipCode" value="{{ empty(old('factoryZipCode')) ? ($errors->has('factoryZipCode') ? '' : $buyerBillingInfo->billing_zip) : old('factoryZipCode') }}"  placeholder="Enter zip code">
                                            @if ($errors->has('factoryZipCode'))
                                                <p class="text-danger">{{ $errors->first('factoryZipCode') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group hasvalue col-md-6" id="form-group-factoryPhone">
                                            <label for="factoryPhone">Phone Number</label>
                                            <input class="form-control" type="text" id="factoryPhone" name="factoryPhone" value="{{ empty(old('factoryPhone')) ? ($errors->has('factoryPhone') ? '' : $buyerBillingInfo->billing_phone) : old('factoryPhone') }}" placeholder="Enter phone">
                                            @if ($errors->has('factoryPhone'))
                                                <p class="text-danger">{{ $errors->first('factoryPhone') }}</p>
                                            @endif
                                        </div>
                                    
                                        <div class="form-group hasvalue col-md-6" id="form-group-fax">
                                            <label for="factoryfax">Fax</label>
                                            <input class="form-control " type="text" id="factoryfax" name="factoryfax" value="{{ empty(old('factoryfax')) ? ($errors->has('factoryfax') ? '' : $buyerBillingInfo->billing_fax) : old('factoryfax') }}"  placeholder="Enter Fax">
                                            @if ($errors->has('factoryfax'))
                                                <p class="text-danger">{{ $errors->first('factoryfax') }}</p>
                                            @endif
                                        </div> 
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <div class="custom_checkbox ">
                                                <input type="checkbox" id="defaultaddress" name="defaultaddress" value="1" checked>
                                                <label for="defaultaddress">This address use as Default.</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-6 text-right">
                                    <button class="btn common_btn" type="button" data-dismiss="modal">Close</button> 
                                </div>
                                <div class="col-md-6 text-right"> 
                                    <button class="btn common_btn" type="button" id="AddNewBillingAddress">Add</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- bililng address model end -->
         <!-- slect billing address modal start -->
         <div class="modal shipping_modal" id="selectbillingModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Select Shipping Address</h4>
                    <button class="close  modal_close_btn" type="button" data-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                @foreach($billingaddress as $bill_address)
                                    <tr>
                                        <td> 
                                            {{ $bill_address->billing_address }}, {{ $bill_address->billing_city }}, 
                                            {{ ($bill_address->billing_state_id == null) ? $bill_address->billing_state : $bill_address->state->name }},
                                            <br>
                                            {{ $bill_address->country ? $bill_address->country->name : ''}} - {{ $bill_address->billing_zip }}
                                        </td> 
                                        <td class="text-center lign-middle">
                                            <button class="btn common_btn SelectbillingAddress" data-index="{{ $loop->index }}" data-id="{{ $bill_address->id }}">Select</button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- slect billing address modal exit -->

    <div class="modal shipping_modal" id="addEditShippingModal">
        <div class="modal-dialog modal-lg" role="document">
            <form id="modalForm">
                <input type="hidden" id="editAddressId" name="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Shipping Address</h4>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"> </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form_radio">
                                        <label for="small-rounded-input">Location</label><br>
                                        <div class="custom_radio">
                                            <input type="radio" class="location" id="locationUS" name="location" value="US" checked>
                                            <label for="locationUS">United States</label>
                                        </div>
                                        <div class="custom_radio">
                                            <input type="radio" class="location" id="locationCA" name="location" value="CA">
                                            <label for="locationCA">Canada</label>
                                        </div>
                                        <div class="custom_radio">
                                            <input type="radio" class="location" id="locationInt" name="location" value="INT">
                                            <label for="locationInt">International</label>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-lg-6">
                                            <label>Store No.</label>
                                            <input class="form-control " type="text" id="store_no" name="store_no">
                                        </div>
                                        <div class="form-group col-lg-6" id="form-group-address">
                                            <label >Address <span class="required">*</span></label>
                                            <input class="form-control" type="text" id="address" name="address">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-lg-6">
                                            <label>Unit #</label>
                                            <input class="form-control" type="text" id="unit" name="unit">
                                        </div>
                                        <div class="form-group col-lg-6" id="form-group-city">
                                            <label >City <span class="required">*</span></label>
                                            <input class="form-control" type="text" id="city" name="city">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-lg-6" id="form-group-state">
                                            <label>State <span class="required">*</span></label>
                                            <input class="form-control" type="text" id="state" name="state">
                                        </div>
                                        <div class="form-group col-lg-6" id="form-group-state-select">
                                            <label >State <span class="required">*</span></label>
                                            <select class="form-control" type="text" id="stateSelect" name="stateSelect">
                                                <option value="">Select State</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group " id="form-group-country">
                                        <label>Country <span class="required">*</span></label>
                                        <select class="form-control" id="country" name="country">
                                            <option value="">Select Country</option>
                                            @foreach($countries as $country)
                                                <option data-code="{{ $country->code }}" value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-lg-6" id="form-group-zip">
                                            <label>Zip Code <span class="required">*</span></label>
                                            <input class="form-control" type="text" id="zipCode" name="zipCode">
                                        </div>
                                        <div class="form-group col-lg-6" id="form-group-phone">
                                            <label >Phone <span class="required">*</span></label>
                                            <input class="form-control" type="text" id="phone" name="phone">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-lg-6" id="form-group-phone">
                                            <label >Fax </label>
                                            <input class="form-control" type="text" id="fax" name="fax">
                                        </div>
                                    </div>
                                    <div class="form-group form_checkbox">
                                        <div class="custom_checkbox ">
                                            <input type="checkbox" id="showroomCommercial" name="showroomCommercial" value="1">
                                            <label for="showroomCommercial">This address is commercial.</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                                    <button class="btn btn-primary btn-sm" type="button" id="modalBtnAdd">Add</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal shipping_modal" id="selectShippingModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Select Shipping Address</h4>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                @foreach($shippingAddresses as $address)
                                <tr>
                                    <td>
                                        <b>{{ $address->store_no }}</b><br>
                                        {{ $address->address }}, {{ $address->city }}, {{ ($address->state == null) ? $address->state_text : $address->state->name }},
                                        <br>
                                        {{ $address->country->name }} - {{ $address->zip }}
                                    </td>

                                    <td class="text-center align-middle">
                                        <button class="btn common_btn btnSelectAddress"data-index="{{ $loop->index }}" data-id="{{ $address->id }}">Select</button>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/inputmask/js/inputmask.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/inputmask/js/jquery.inputmask.js') }}"></script>
    <script>
        var oldFactoryState = '{{ empty(old('factoryStateSelect')) ? ($errors->has('factoryStateSelect') ? '' : $buyerBillingInfo->billing_state_id) : old('factoryStateSelect') }}';

        $(function () {
            /*Add new shipping address text change*/
            if ( $(window).width() <= 480 ) {
                $('#btnAddShippingAddress').text("Add New Shipping");

                // Open credit card by default
                $('.credit-card-collapse').removeClass('collapse');
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': '{!! csrf_token() !!}'
                }
            });

            var shippingAddresses = <?php echo json_encode($shippingAddresses); ?>;
            var BillingAddresses = <?php echo json_encode($billingaddress); ?>;
            var shippingMethods = <?php echo json_encode($shipping_methods); ?>;
            var usStates = <?php echo json_encode($usStates); ?>;
            var caStates = <?php echo json_encode($caStates); ?>;
            var rewardPoint = <?php echo json_encode($rewardOffer); ?>;

            $('#expiry').inputmask();


             
            // =================================================================
            // Change Shipping Method
            // ================================================================= 
            $('.shipping_method').change(function () {
                // var index = parseInt($(".shipping_method:checked").data('index'));
                // var storeCredit = parseFloat($('.store_credit').val());
                
                var index = parseInt($(".shipping_method:checked").data('index'));
                if($("input[name='store_credit']").val()) {
                    var  storeCredit =$("input[name='store_credit']").val();
                }else{
                    var storeCredit = 0;
                }

                if (!isNaN(index)) {
                    var subTotal = parseFloat('{{ $order->subtotal }}');

                    var discount = parseFloat($('#order-discount').html());
                    
                    var sm = shippingMethods[index];
    
                    if (sm.fee === null)
                        shipmentFee = 0;
                    else
                        shipmentFee = parseFloat(sm.fee);

                    var free_shipping = $('#free_shipping').val();  
                    if(free_shipping == 1) {
                        
                        $('#total').html('$' + ((subTotal - discount) - storeCredit).toFixed(2));
                        $('#shippingCost').html('$' + shipmentFee.toFixed(2) + ' (Free By Promotion)');
                        
                    } else if ($('#reward_point option:selected')) {
                        var reIndex = $('select[name="reward_point"] option:selected').data('index');
                            if(reIndex != undefined){
                                var rewardCriteria = rewardPoint[reIndex];

                            if(rewardCriteria.free_shipping_1 == 1){
                                $('#total').html('$' + ((subTotal - discount) - storeCredit).toFixed(2));
                                $('#shippingCost').html('$' + shipmentFee.toFixed(2) + ' (Free By Point)');

                            }else if(rewardCriteria.percentage_discount_1 != 0){
                                var rewardDiscount = subTotal * ( rewardCriteria.percentage_discount_1 / 100 );
                                var discountAll = discount + rewardDiscount ;
                                $('#total').html('$' + ((subTotal - discountAll) - storeCredit + shipmentFee).toFixed(2));
                                $('#shippingCost').html('$' + shipmentFee.toFixed(2));
                            }else if(rewardCriteria.unit_price_discount_1 != 0){
                                var rewardDiscount = parseFloat(rewardCriteria.unit_price_discount_1);
                                var discountAll = discount + rewardDiscount ; 
                                $('#total').html('$' + ((subTotal - discountAll) - storeCredit + shipmentFee).toFixed(2));
                                $('#shippingCost').html('$' + shipmentFee.toFixed(2));
                            }
                        }else {
                            $('#total').html('$' + ((subTotal - discount)+ shipmentFee - storeCredit).toFixed(2));
                            $('#shippingCost').html('$' + shipmentFee.toFixed(2));
                        }
                    }
                }
            });
            // =================================================================
            // Apply Promotion code
            // =================================================================


            //apply point start
            $('#reward_point').change(function () {

                var index = parseInt($(this).find(':selected').data('index'));
                var storeCredit = parseFloat($('.store_credit').val());
                
                if (!isNaN(index)) {

                    var rewardShip = parseInt($(this).find(':selected').data('freeship'));
                    var free_shipping = $('#free_shipping').val();
                    var subTotal = parseFloat('{{ $order->subtotal }}');
                    var discount = parseFloat($('#order-discount').html());

                    var rewardCriteria = rewardPoint[index];

                    //check shipping method is checked or not
                    if ($(".shipping_method").is(":checked")) {
                       var shipIndex = $('input[name=shipping_method]:checked').data('index');
                       var sm =  shippingMethods[shipIndex];
                       if (sm.fee === null)
                            sm = 0;
                        else
                            sm = parseFloat(sm.fee);
                    }else{
                        var sm = $('#shippingCost').html();
                        sm = sm.replace(/[^\d.-]/g,'');; 
                    }

                    if(free_shipping == 1 && rewardShip == 1) {
                        $('#shippingCost').html('$ 0.00');
                        alert('Free shipping already applied by Promotion.');
                        $('select option:nth-child(1)').prop("selected", true);
                        $('#total').html('$' + ((subTotal - discount) - storeCredit).toFixed(2));
                        $('#shippingCost').html('$' + sm + ' (Free By Promotion)');
                        return;
                        
                    } else if(rewardShip == 1) {
                        $('#rewardDiscountTd').html('');
                        $('#total').html('$' + ((subTotal - discount) - storeCredit).toFixed(2));
                        $('#shippingCost').html('$' + sm + ' (Free By Point)');
                        
                    }else{
                        if(rewardCriteria.percentage_discount_1 != 0){
                            var rewardDiscount = subTotal * ( rewardCriteria.percentage_discount_1 / 100 );
                            
                            var output = '';
                            output += '<td>Point Discount</td>';
                            output += '<td class="text-right" id="rewardDiscountTotal"> -$ '+rewardDiscount.toFixed(2)+'</td>';
                            $('#rewardDiscountTd').html(output);
                            $('#shippingCost').html('$ '+ sm);

                            var discountAll = discount + rewardDiscount ; 
                           
                            var total = ((subTotal - discountAll) - storeCredit );
                            if(free_shipping == 1 ) {
                                $('#total').html('$' + ( total).toFixed(2));
                            }else{
                                $('#total').html('$' + ( total + parseFloat(sm)).toFixed(2));
                            }
                        }
                        if(rewardCriteria.unit_price_discount_1 != 0){
                            var rewardDiscount = parseFloat(rewardCriteria.unit_price_discount_1);
                            var output = '';
                            output += '<td>Point Discount</td>';
                            output += '<td class="text-right" id="rewardDiscountTotal"> -$ '+ rewardDiscount.toFixed(2)+'</td>';
                            $('#rewardDiscountTd').html(output);
                            $('#shippingCost').html('$ '+ sm);

                            var discountAll = discount + rewardDiscount ; 
                            var total = ((subTotal - discountAll) - storeCredit );
                            if(free_shipping == 1 ) {
                                $('#total').html('$' + ( total).toFixed(2));
                            }else{
                                $('#total').html('$' + ( total + parseFloat(sm)).toFixed(2));
                            }
                        }
                    }
                }
            });
            // apply point end
            
            $('#btnSubmit').click(function () {
                var agree =   $('#checkbox-agree:checked').val();
                if(agree == 'on'){
                    $(this).parent().siblings().removeClass('has-danger');
                    var total_amount = document.getElementById ( "total" ).innerText;
                    total_amount = total_amount.replace('$', '');
                    total_amount = parseInt(total_amount);
                    if(total_amount <  0){
                        alert('Amount can not be less than 0!')
                        return false;
                    }
                }else{
                    $(this).parent().siblings().addClass('has-danger');
                    $("#policy_agree").show();
                    $("#policy_agree").html('Please Checkmark this box.');
                    return false;
                }
            });

            $('.store_credit').keyup(function () {
                var index = parseInt($(".shipping_method:checked").data('index'));
                var max_credit = parseFloat($("input[name='max_store_credit']").val());
                if($("input[name='store_credit']").val()) {
                    var  storeCredit =$("input[name='store_credit']").val();
                }else{
                    var storeCredit = 0;
                }
                if(storeCredit > max_credit){
                    $('.max_credit_limit').html('Max Store Credit ' + max_credit);
                    return false;
                }
                var subTotal = parseFloat('{{ $order->subtotal }}');
                var discount = parseFloat($('#order-discount').html());
                var rewardDiscount = 0;
                var reIndex = $('select[name="reward_point"] option:selected').data('index');
                
                if(reIndex != undefined){
                    var rewardCriteria = rewardPoint[reIndex];

                    if(rewardCriteria.percentage_discount_1 != 0){
                        var rewardDiscount = subTotal * ( rewardCriteria.percentage_discount_1 / 100 );
                    }else if(rewardCriteria.unit_price_discount_1 != null){
                        var rewardDiscount = parseFloat(rewardCriteria.unit_price_discount_1);
                    }else{
                        var rewardDiscount = 0;
                    }
                }
                
                
                if (!isNaN(index)) {
                    var sm = shippingMethods[index];
                    if (sm.fee === null) {
                        shipmentFee = 0;}
                    else{
                        shipmentFee = parseFloat(sm.fee);}

                            {{--var free_shipping = parseInt('{{ $order->free_shipping }}');--}}
                    var free_shipping = $('#free_shipping').val();

                    if(free_shipping == 1) {

                        $('#total').html('$' + ((subTotal - discount - rewardDiscount) - storeCredit).toFixed(2));
                        $('#shippingCost').html('$' + shipmentFee.toFixed(2) + ' (Free By Promotion)');

                    } else {
                        $('#total').html('$' + ((subTotal - discount- rewardDiscount)+ shipmentFee - storeCredit).toFixed(2));
                        $('#shippingCost').html('$' + shipmentFee.toFixed(2));
                    }
                }else{
                    $('#total').html('$' + ((subTotal - storeCredit-discount - rewardDiscount)).toFixed(2));
                }

            });
            // =================================================================
            // Apply Promotion code
            // =================================================================
            $('.btnApplyCoupon').click(function (e) {
                e.preventDefault();

                var orderId = $(this).data('order-id');
                var coupon = $('#coupon_'+orderId).val();

                $.ajax({
                    method: "POST",
                    url: "{{ route('buyer_apply_coupon') }}",
                    data: { id: orderId, coupon: coupon }
                }).done(function( data ) {
                    console.log(data)
                    if (data.success) {
                        console.log(data.discount);
                        console.log(data.free_shipping);
                        $('#order-discount').html(data.discount);
                        $('#total').html('$' + data.total);
                        $('#free_shipping').val(data.free_shipping);
                        $('.shipping_method').trigger('change');
                    } else {
                        alert(data.message);
                    }
                });
            });

            $('.shipping_method').trigger('change');

            $('.btnPM').click(function () {
                var id = $(this).data('id');

                if($(this).attr('aria-expanded') == 'true') {
                    $('#paymentMethod').val('');
                } else {
                    $('#paymentMethod').val(id);
                }

            });

            // $('#checkbox-agree').change(function () {
            //     if ($(this).is(':checked')) {
            //         $('#btnSubmit').prop('disabled', false);
            //     } else {
            //         $('#btnSubmit').prop('disabled', true);
            //     }
            // });

            $('#checkbox-agree').trigger('change');

            // Shipping Address
            $('#btnChangeAddress').click(function (e) {
                e.preventDefault();
                $('#selectShippingModal').modal('show');
            });

            // select billing  Address modal show
            $('#btnChangeBillingAddress').click(function (e) {
                e.preventDefault();
                $('#selectbillingModal').modal('show');
            });

            $('.btnSelectAddress').click(function () {
                var id = $(this).data('id');
                var index = $(this).data('index');

                $('#address_id').val(id);

                var address = shippingAddresses[index];

                $('#address_text').html(address.address + ', ' + address.city + ', ');

                if (address.state == null) {
                    $('#address_text').append(address.state_text + ', ');
                } else {
                    $('#address_text').append(address.state.name + ', ');
                }

                $('#address_text').append('<br>' + address.country.name + ' - ' + address.zip);

                $('#selectShippingModal').modal('hide');
            });

            // sellect new billing address click functin
            $('.SelectbillingAddress').click(function () {
                var id = $(this).data('id');
                var index = $(this).data('index');

                $('#billing_address_id').val(id);

                var address = BillingAddresses[index];

                $('#billing_address_text').html(address.billing_address + ', ' + address.billing_city + ', ');

                if (address.billing_state_id == null) {
                    $('#billing_address_text').append(address.billing_state + ', ');
                } else {
                    $('#billing_address_text').append(address.state.name + ', ');
                }

                $('#billing_address_text').append('<br>' + address.country.name + ' - ' + address.billing_zip);

                $('#selectbillingModal').modal('hide');
                $(".modal-backdrop").hide();
            });

            $('#btnAddShippingAddress').click(function (e) {
                e.preventDefault();
                $('#addEditShippingModal').modal('show');
            });

            $('.location').change(function () {
                var location = $('.location:checked').val();

                if (location == 'CA' || location == 'US') {
                    if (location == 'US')
                        $('#country').val('1');
                    else
                        $('#country').val('2');

                    $('#country').prop('disabled', 'disabled');
                    $('#form-group-state-select').show();
                    $('#stateSelect').val('');
                    $('#form-group-state').hide();

                    $('#stateSelect').html('<option value="">Select State</option>');

                    if (location == 'US') {
                        $.each(usStates, function (index, value) {
                            $('#stateSelect').append('<option value="'+value.id+'">'+value.name+'</option>');
                        });
                    }

                    if (location == 'CA') {
                        $.each(caStates, function (index, value) {
                            $('#stateSelect').append('<option value="'+value.id+'">'+value.name+'</option>');
                        });
                    }
                } else {
                    $('#country').prop('disabled', false);
                    $('#form-group-state-select').hide();
                    $('#form-group-state').show();
                    $('#country').val('');
                }
            });

            $('.location').trigger('change');

            $('#country').change(function () {
                var countryId = $(this).val();

                if (countryId == 1) {
                    $("#locationUS").prop("checked", true);
                    $('.location').trigger('change');
                } else if (countryId == 2) {
                    $("#locationCA").prop("checked", true);
                    $('.location').trigger('change');
                }
            });

            $('#modalBtnAdd').click(function () {
                if (!shippingAddressValidate()) {
                    $('#country').prop('disabled', false);

                    $.ajax({
                        method: "POST",
                        url: "{{ route('buyer_add_shipping_address') }}",
                        data: $('#modalForm').serialize(),
                    }).done(function( data ) {
                        setAddressId(data.id);
                    });

                    $('#country').prop('disabled', true);
                }
            });
            $('#AddNewBillingAddress').click(function () {
                
                if (!billingsAddressValidate()) {
                    $('#factoryCountry').prop('disabled', false); 
                    $.ajax({
                        method: "POST",
                        url: "{{ route('buyer_add_billing_address') }}",
                        data: $('#newbillingaddress').serialize(),
                    }).done(function( data ) {
                        setAddressId(data.id);
                    });

                    $('#country').prop('disabled', true);
                }
            });

            function setAddressId(id) {
                var orders = $('#orders').val();

                $.ajax({
                    method: "POST",
                    url: "{{ route('checkout_address_select') }}",
                    data: { shippingId: id, id: orders },
                }).done(function( data ) {
                    window.location.reload(true);
                });
            }

            $('#addEditShippingModal').on('hide.bs.modal', function (event) {
                $("#locationUS").prop("checked", true);
                $('.location').trigger('change');

                $('#store_no').val('');
                $('#address').val('');
                $('#unit').val('');
                $('#city').val('');
                $('#stateSelect').val('');
                $('#state').val('');
                $('#zipCode').val('');
                $('#phone').val('');
                $('#fax').val('');
                $('#showroomCommercial').prop('checked', false);

                clearModalForm();
            });

            function clearModalForm() {
                $('#form-group-address').removeClass('has-danger');
                $('#form-group-city').removeClass('has-danger');
                $('#form-group-state-select').removeClass('has-danger');
                $('#form-group-state').removeClass('has-danger');
                $('#form-group-country').removeClass('has-danger');
                $('#form-group-zip').removeClass('has-danger');
                $('#form-group-phone').removeClass('has-danger');
            }

            function billingsAddressValidate() {
                var error = false; 
                var factorylocation = $('.factoryLocation:checked').val(); 
                clearModalForm(); 
                if ($('#factoryAddress').val() == '') {
                    $('#form-group-factoryAddress').addClass('has-danger');
                    error = true;
                }

                if ($('#factoryCity').val() == '') {
                    $('#form-group-factoryCity').addClass('has-danger');
                    error = true;
                }

                if ((factorylocation == 'US' || factorylocation == 'CA') && $('#factoryStateSelect').val() == '') {
                    $('#form-group-factory-state-select').addClass('has-danger');
                    error = true;
                }

                if (factorylocation == 'INT' && $('#factoryState').val() == '') {
                    $('#form-group-factory-state').addClass('has-danger');
                    error = true;
                }

                if ($('#factoryCountry').val() == '') {
                    $('#form-group-factory-factoryCountry').addClass('has-danger');
                    error = true;
                }

                if ($('#factoryZipCode').val() == '') {
                    $('#form-group-factoryZipCode').addClass('has-danger');
                    error = true;
                }

                if ($('#factoryPhone').val() == '') {
                    $('#form-group-factoryPhone').addClass('has-danger');
                    error = true;
                }

                return error;
            }

            function shippingAddressValidate() {
                var error = false;
                var location = $('.location:checked').val();

                clearModalForm();

                if ($('#address').val() == '') {
                    $('#form-group-address').addClass('has-danger');
                    error = true;
                }

                if ($('#city').val() == '') {
                    $('#form-group-city').addClass('has-danger');
                    error = true;
                }

                if ((location == 'US' || location == 'CA') && $('#stateSelect').val() == '') {
                    $('#form-group-state-select').addClass('has-danger');
                    error = true;
                }

                if (location == 'INT' && $('#state').val() == '') {
                    $('#form-group-state').addClass('has-danger');
                    error = true;
                }

                if ($('#country').val() == '') {
                    $('#form-group-country').addClass('has-danger');
                    error = true;
                }

                if ($('#zipCode').val() == '') {
                    $('#form-group-zip').addClass('has-danger');
                    error = true;
                }

                if ($('#phone').val() == '') {
                    $('#form-group-phone').addClass('has-danger');
                    error = true;
                }

                return error;
            }
            $('.factoryLocation').change(function () {
                var location = $('.factoryLocation:checked').val();

                if (location == 'CA' || location == 'US') {
                    if (location == 'US')
                        $('#factoryCountry').val('1');
                    else
                        $('#factoryCountry').val('2');

                    $('#factoryCountry').prop('disabled', 'disabled');
                    $('#form-group-factory-state-select').show();
                    $('#factoryStateSelect').val('');
                    $('#form-group-factory-state').hide();

                    $('#factoryStateSelect').html('<option value="">Select State</option>');

                    if (location == 'US') {
                        $.each(usStates, function (index, value) {
                            if (value.id == oldFactoryState)
                                $('#factoryStateSelect').append('<option value="' + value.id + '" selected>' + value.name + '</option>');
                            else
                                $('#factoryStateSelect').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }

                    if (location == 'CA') {
                        $.each(caStates, function (index, value) {
                            if (value.id == oldFactoryState)
                                $('#factoryStateSelect').append('<option value="' + value.id + '" selected>' + value.name + '</option>');
                            else
                                $('#factoryStateSelect').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                } else {
                    $('#factoryCountry').prop('disabled', false);
                    $('#form-group-factory-state-select').hide();
                    $('#form-group-factory-state').show();
                }
            });
            $('.factoryLocation').trigger('change');

            $('.promo_code :text').keyup(function() {
                if($('#promo_input').val() != "") {
                    $('#promo_code_btn').removeAttr('disabled');
                } else {
                    $('#promo_code_btn').attr('disabled', true);
                }
            });
        });
    </script>
@stop