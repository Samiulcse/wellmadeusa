@extends('admin.layouts.main')

@section('additionalCSS')

@stop

@section('content')
    <div class="ly_card">
        <div class="ly_card_heading">
            <h1>My Performance</h1>
        </div>

        <div class="ly_card_body">
            <div class="ly-wrap-fluid">
                <div class="ly-row">
                    <div class="ly">
                        <div class="additional_stats_wrapper center_text">
                            <p>Last 30 Days</p>
                            <div class="additional_stats_content">
                                <h2>{{ number_format($data['homePageVisitor']) }}</h2>
                                <p>Home Page Visitor </p>
                            </div>
                        </div>
                    </div>
                    <div class="ly">
                        <div class="additional_stats_wrapper center_text">
                            <p>Today(Unique)</p>
                            <div class="additional_stats_content">
                                <h2>{{ number_format($data['homePageVisitorUnique']) }}</h2>
                                <p>Home Page Visitor </p>
                            </div>
                        </div>
                    </div>
                    <div class="ly">
                        <div class="additional_stats_wrapper center_text">
                            <p>Last 30 Days</p>
                            <div class="additional_stats_content">
                                <h2>${{ number_format($data['totalSaleAmount'], 2, '.', ',') }}</h2>
                                <p>Total Sale Amount</p>
                            </div>
                        </div>
                    </div>
                    <div class="ly">
                        <div class="additional_stats_wrapper center_text">
                            <p>Last 30 Days</p>
                            <div class="additional_stats_content">
                                <h2>${{ number_format($data['totalPendingOrder'], 2, '.', ',') }}</h2>
                                <p>Total Pending Order</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ly_card">
        <div class="ly_card_heading">
            <h1>Additional Stats</h1>
        </div>
        <div class="ly_card_body">
            <div class="ly-wrap-fluid">
                <div class="ly-row">
                    <div class="ly">
                        <div class="additional_stats_wrapper center_text">
                            <p>Today</p>
                            <div class="additional_stats_content">
                                <h2>${{ number_format($data['todayOrderAmount'], 2, '.', ',') }}</h2>
                                <p>Total Order Amt</p>
                            </div>
                        </div>
                    </div>
                    <div class="ly-5">
                        <div class="additional_stats_wrapper a_stats_two_row clearfix center_text">
                            <p>Yesterday</p>

                            <div class="additional_stats_content">
                                <h2>{{ number_format($data['yesterdayVisitor']) }}</h2>
                                <p>Visitors</p>
                            </div>
                            <div class="additional_stats_content">
                                <h2>${{ number_format($data['yesterdayOrderAmount'], 2, '.', ',') }}</h2>
                                <p>Total Order Amt</p>
                            </div>
                        </div>
                    </div>
                    <div class="ly">
                        <div class="additional_stats_wrapper center_text">
                            <p>This Month</p>
                            <div class="additional_stats_content">
                                <h2>{{ number_format($data['itemUploadedThisMonth']) }}</h2>
                                <p>Items Uploaded</p>
                            </div>
                        </div>
                    </div>
                    <div class="ly">
                        <div class="additional_stats_wrapper no_border center_text">
                            <p>Total Customer</p>
                            <div class="additional_stats_content">
                                <h2>{{ number_format($data['total_customer']) }}</h2>
                                <a href="{{ route('admin_all_buyer') }}"><p>Total User</p></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ly_card">
        <div class="ly_card_heading">
            <h1>Top 5 Styles Sold</h1>
        </div>
        <div class="ly_card_body">
            <div class="ly-wrap-fluid">
                <div class="ly-row">   
                    @php($counter = 1)                         
                    @foreach($bestItems as $item)
                    <div class="ly">
                        <div class="home_product">
                            <span class="product_serial">{{$counter++}}.</span>
                            <a href="{{ route('admin_edit_item', ['item' => $item->id]) }}">
                                <img src="{{ $item->image_path }}" alt="" class="img_mx_width">
                            </a>
                            <a href="{{ route('admin_edit_item', ['item' => $item->id]) }}">{{ $item->style_no }}</a>
                            <a href="#">COLOR BLOCK BACK HI LOW PULLOVER T</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="ly_card">
        <div class="ly_card_heading">
            <h1>Total Order</h1>
        </div>
        <div class="ly_card_body">
            
        <div class="ly-row">
            <div class="ly-6">
                <h4>Total Order Count by Month</h4>
                <hr>

                <canvas id="orderCount" height="200"></canvas>
            </div>

            <div class="ly-6">
                <h4>Item Uploaded by Month</h4>
                <hr>

                <canvas id="myChart2" height="200"></canvas>
            </div>
        </div>
        </div>
    </div>
@stop


@section('additionalJS')
    <script src="{{ asset('plugins/Chart.js/Chart.min.js') }}"></script>
    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var selectedId;

            $('.btnDelete').click(function () {
                $('#deleteModal').modal('show');
                selectedId = $(this).data('id');
            });

            $('#modalBtnDelete').click(function () {
                $.ajax({
                    method: "POST",
                    url: "{{ route('admin_delete_order') }}",
                    data: { id: selectedId }
                }).done(function( msg ) {
                    location.reload();
                });
            });
        });
    </script>
    <script>
        var orderCountLabel = <?php echo $data['orderCountLabel']; ?>;
        var orderCount = <?php echo $data['orderCount']; ?>;
        var returnOrder = <?php echo $data['returnOrder']; ?>;
        var uploadCount = <?php echo $data['uploadCount']; ?>;

        var chartOrderCount = document.getElementById("orderCount").getContext('2d');
        var chartOrderCount = new Chart(chartOrderCount, {
            type: 'bar',
            data: {
                labels: orderCountLabel,
                datasets: [{
                    label: 'New',
                    data: orderCount,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor:  'rgba(255,99,132,1)',
                    borderWidth: 1
                },
                    {
                        label: 'Return',
                        data: returnOrder,
                        backgroundColor: 'rgba(255, 206, 86, 0.2)',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 1
                    }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true,
                            max: Math.max(Math.max(...orderCount) + 5, Math.max(...returnOrder) + 5),
        }
        }]
        }
        }
        });

        var ctx = document.getElementById("myChart2").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: orderCountLabel,
                datasets: [{
                    data: uploadCount,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor:  'rgba(255,99,132,1)',
                    borderWidth: 1
                }]
            },
            options: {
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true,
                            max: Math.max(...uploadCount) + 5
        }
        }]
        }
        }
        });
    </script>
@stop
