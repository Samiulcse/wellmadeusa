@extends('layouts.app')

@section('content')
<div class="container content">
    <div class="row">
        <div class="col-md-12">
            <div class="checkout-steps">
                <a>5. Complete</a>
                <a><span class="angle"></span>4. Review</a>
                <a><span class="angle"></span>3. Payment</a>
                <a class="active"><span class="angle"></span>2. Shipping Method</a>
                <a class="completed"><span class="angle"></span><span class="step-indicator icon-circle-check"></span>1. Address</a>
            </div>
        </div>
    </div>

    <form method="post" action="{{ route('show_shipping_method_post') }}">
        @csrf

        <input type="hidden" name="orderIds" value="{{ request()->get('id') }}">

        <h4>Shipping Method</h4>
        <hr class="padding-bottom-1x">
        <div class="table-responsive">
            <table class="table table-hover">
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
                            <div class="custom-control custom-radio mb-0">
                                <input class="custom-control-input" type="radio" id="{{ $shipping_method->id }}" name="shipping_method"
                                       value="{{ $shipping_method->id }}"
                                       {{ ($order->shipping_method_id == $shipping_method->id) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="{{ $shipping_method->id }}"></label>
                            </div>
                        </td>

                        <td class="align-middle">
                            <span class="text-medium">{{ $shipping_method->courier->name }}</span><br>
                            <span class="text-muted text-sm">{{ $shipping_method->name }}</span>
                        </td>

                        <td>
                            ${{ number_format($shipping_method->fee, 2, '.', '') }}
                        </td>

                    </tr>
                @endforeach
            </table>

            @if ($errors->has('shipping_method'))
                <div class="form-control-feedback text-danger">Select a shipping method</div>
            @endif
        </div>

        <div class="checkout-footer">
            <div class="column"><a class="btn btn-outline-secondary" href="{{ route('show_checkout') }}?id={{ request()->get('id') }}"><i class="icon-arrow-left"></i><span class="hidden-xs-down">&nbsp;Back</span></a></div>
            <div class="column"><button class="btn btn-primary" type="submit"><span class="hidden-xs-down">Continue&nbsp;</span><i class="icon-arrow-right"></i></button></div>
        </div>
    </form>
</div>
@stop