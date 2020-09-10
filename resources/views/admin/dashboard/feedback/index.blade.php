@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <style>
        @import url('//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css')
    </style>
@stop

@section('content')
    <form action="{{ route('admin_save_feedback') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Order Number</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Feedback</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($reviews as $review)
                            <tr>
                                <td>
                                    {{ date('F d, Y', strtotime($review->created_at)) }}
                                    <input type="hidden" name="ids[]" value="{{ $review->id }}">
                                </td>
                                <td>{{ $review->user->first_name.' '.$review->user->last_name }}</td>
                                <td>
                                    <a href="{{ route('admin_order_details', ['order' => $review->order->id]) }}" target="_blank">{{ $review->order->order_number }}</a>
                                </td>
                                <td>
                                    @for($i=0; $i<$review->star; $i++)
                                        <i class='fa fa-star'></i>
                                    @endfor
                                </td>
                                <td>
                                    {{ $review->review }}
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="feedback[]" value="{{ $review->reply }}">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-12 text-right">
                <input class="btn btn-primary" type="submit" value="SAVE">
            </div>
        </div>
    </form>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="pagination">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
@stop

@section('additionalJS')
    <script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script>
        $(function () {
            var message = '{{ session('message') }}';

            if (message != '')
                toastr.success(message);
        });
    </script>
@stop