@extends('buyer.layouts.profile')

@section('additionalCSS')
    <style>
        @import url('//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css')
    </style>
@stop

@section('profile_content')
    <div class="table-responsive">
        <form action="{{ route('buyer_feedback_post') }}" method="POST">
            @csrf

            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Date</th>
                        <th>Order Number</th>
                        <th>Rating</th>
                        <th>Comments</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input order_checkbox" type="checkbox" id="checkbox_{{ $order->id }}" name="ids[]" value="{{ $order->id }}">
                                    <label class="custom-control-label" for="checkbox_{{ $order->id }}"></label>
                                </div>
                            </td>
                            <td>{{ date('m/d/Y', strtotime($order->created_at)) }}</td>
                            <td>
                                <a href="{{ route('show_order_details', ['order' => $order->id]) }}">{{ $order->order_number }}</a>
                            </td>
                            <td>
                                <div class="row lead">
                                    <div class="starrr" data-rating='{{ $order->star }}'></div>
                                    <input type="hidden" name="star_{{ $order->id }}" class="star_input" value="{{ $order->star }}">
                                </div>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-rounded form-control-sm comment-input" name="comment_{{ $order->id }}" value="{{ $order->review }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <input class="btn btn-primary" type="submit" value="SUBMIT">
        </form>

        <div class="pagination">
            {{ $orders->links('others.pagination') }}
        </div>
    </div>
@stop

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('js/star.js') }}"></script>
    <script>
        $(function () {
            $('.starrr').on('starrr:change', function(e, value){
                var index = $('.starrr').index($(this));
                $('.order_checkbox:eq('+index+')').prop('checked', true);
                $('.star_input:eq('+index+')').val(value);

                $('#count').html(value);
            });

            $('.comment-input').keyup(function () {
                var index = $('.comment-input').index($(this));
                $('.order_checkbox:eq('+index+')').prop('checked', true);
            });
        });
    </script>
@stop