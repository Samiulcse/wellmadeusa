@extends('layouts.app')

@section('content')
<div class="container content">
    <div class="row">
        <div class="col-md-12">
            <div class="checkout-steps">
                <a>5. Complete</a>
                <a><span class="angle"></span>4. Review</a>
                <a class="active"><span class="angle"></span>3. Payment</a>
                <a class="completed"><span class="step-indicator icon-circle-check"></span><span class="angle"></span>2. Shipping Method</a>
                <a class="completed"><span class="step-indicator icon-circle-check"></span><span class="angle"></span>1. Address</a>
            </div>
        </div>
    </div>

    <form action="{{ route('checkout_payment_post') }}" method="post">
        @csrf
        <input type="hidden" name="id" value="{{ request()->get('id') }}">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group col-sm-6{{ $errors->has('name') ? ' has-danger' : '' }}">
                    <input class="form-control" type="text" name="name" placeholder="Full Name"
                           value="{{ empty(old('name')) ? ($errors->has('name') ? '' : $order->card_full_name) : old('name') }}">
                </div>
                <div class="form-group col-sm-6{{ $errors->has('number') ? ' has-danger' : '' }}">
                    <input class="form-control" type="text" name="number" placeholder="Card Number"
                           value="{{ empty(old('number')) ? ($errors->has('number') ? '' : $order->card_number) : old('number') }}">
                </div>
                <div class="form-group col-sm-3{{ $errors->has('expiry') ? ' has-danger' : '' }}">
                    <input class="form-control" type="text" name="expiry" placeholder="MM/YY"
                           data-inputmask="'mask': '99/99'" id="expiry"
                           value="{{ empty(old('expiry')) ? ($errors->has('expiry') ? '' : $order->card_expire) : old('expiry') }}">
                </div>
                <div class="form-group col-sm-3{{ $errors->has('cvc') ? ' has-danger' : '' }}">
                    <input class="form-control" type="text" name="cvc" placeholder="CVC"
                           value="{{ empty(old('cvc')) ? ($errors->has('cvc') ? '' : $order->card_cvc) : old('cvc') }}">
                </div>
            </div>
        </div>

        <div class="checkout-footer margin-top-1x">
            <div class="column"><a class="btn btn-outline-secondary" href="{{ route('show_shipping_method') }}?id={{ request()->get('id') }}"><i class="icon-arrow-left"></i><span class="hidden-xs-down">&nbsp;Back</span></a></div>
            <div class="column"><button class="btn btn-primary" type="submit"><span class="hidden-xs-down">Continue&nbsp;</span><i class="icon-arrow-right"></i></button></div>
        </div>
    </form>
</div>
@stop

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/inputmask/js/inputmask.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/inputmask/js/jquery.inputmask.js') }}"></script>
    <script>
        $(function () {
            $('#expiry').inputmask();
        });
    </script>
@stop