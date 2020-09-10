@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <style>
        .table .table {
            background-color: white;
        }
    </style>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <a class="ly_btn btn_blue" href="{{ url()->previous() }}">Back to List</a>
        </div>
    </div>

    <br>

    <div class="table-responsive">
        <table class="table table-bordered">
            <tr>
                <th class="product-thumbnail"><b>Image</b></th>
                <th><b>Style No.</b></th>
                <th class="text-center"><b>Color</b></th>
                <th class="text-center" colspan="10"><b>Size</b></th>
                <th class="text-center"><b>Pack</b></th>
                <th class="text-center"><b>Total Qty</b></th>
                <th class="text-center"><b>Unit Price</b></th>
                <th class="text-center"><b>Amount</b></th>
            </tr>

            <tbody>
            <?php
            $totalItem = 0;
            $total = 0;
            ?>
            @foreach($allItems as $item_id => $items)
                @if(!empty($items[0]->item))
                <tr>
                    <td rowspan="{{ sizeof($items)+1  }}"> 
                        @if (sizeof($items[0]->item->images) > 0)
                            <img src="{{ asset($items[0]->item->images[0]->image_path) }}" alt="Product" height="100px">
                        @else
                            <img src="{{ asset('images/no-image.png') }}" alt="Product" height="100px">
                        @endif
                    </td>

                    <td rowspan="{{ sizeof($items)+1 }}">
                        {{ $items[0]->item->style_no }}
                    </td>

                    <td class="text-center text-lg text-medium">
                        &nbsp;
                    </td>

                    <?php
                    $sizes = explode("-", $items[0]->item->pack->name);
                    $itemInPack = 0;

                    for($i=1; $i <= sizeof($sizes); $i++) {
                        $var = 'pack'.$i;

                        if ($items[0]->item->pack->$var != null)
                            $itemInPack += (int) $items[0]->item->pack->$var;
                    }
                    ?>

                    @foreach($sizes as $size)
                        <th colspan="{{ $loop->last ? 10-sizeof($sizes) +1 : '' }}"><b>{{ $size }}</b></th>
                    @endforeach

                    <td>
                        &nbsp;
                    </td>

                    <td>
                        &nbsp;
                    </td>

                    <td>
                        &nbsp;
                    </td>

                    <td>
                        &nbsp;
                    </td>
                </tr>
                

                @foreach($items as $item)
                    <tr>
                        <td>
                            {{ $item->color->name }}
                        </td>

                        @for($i=1; $i <= sizeof($sizes); $i++)
                            <?php $p = 'pack'.$i; ?>
                            <td colspan="{{ $i == sizeof($sizes) ? 10-$i +1 : '' }}">{{ ($items[0]->item->pack->$p == null) ? '0' : $items[0]->item->pack->$p }}</td>
                        @endfor

                        <td>
                            {{ $item->quantity }}
                        </td>

                        <td>
                            <?php $totalItem += $itemInPack * $item->quantity; ?>
                            <span class="total_qty">{{ $itemInPack * $item->quantity }}</span>
                        </td>

                        <td>
                            ${{ sprintf('%0.2f', $item->item->price) }}
                        </td>

                        <td>
                            <?php $total += $item->item->price * $itemInPack * $item->quantity; ?>
                            <span class="total_amount">${{ sprintf('%0.2f', $item->item->price * $itemInPack * $item->quantity) }}</span>
                        </td>
                    </tr>
                @endforeach
                @endif
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="ly-row" style="margin-top: 20px">
        <div class="ly-9"></div>
        <div class="ly-3">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>Total Item</th>
                        <td>{{ $totalItem }}</td>
                    </tr>

                    <tr>
                        <th>Total</th>
                        <td>${{ number_format($total, 2, '.', '') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@stop