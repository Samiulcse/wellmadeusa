@extends('admin.layouts.main')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <table class="table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Style No</th>
                    <th>Status</th>
                </tr>
                </thead>

                <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->style_no }}</td>
                        <td class="status">Pending</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('additionalJS')
    <script>
        var items = <?php echo json_encode($items) ?>;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function asyncFunction(item, i) {
            var c = '{{ request()->get('c') }}';
            var v = '{{ request()->get('v') }}';

            item.c = c;
            item.v = v;

            return $.ajax({
                url: '{{ route('admin_export_to_sp_post') }}',
                type: 'POST',
                data : item
            }).then(function(data){
                if (data.success)
                    $('.status:eq('+i+')').html('<span class="text-success">'+data.message+'</span>');
                else
                    $('.status:eq('+i+')').html('<span class="text-danger">'+data.message+'</span>');
            });
        }

        function sequence(arr, callback) {
            var i=0;

            var request = function(item) {
                return callback(item, i).then(function(){
                    if (i < arr.length-1)
                        return request(arr[++i]);
                });
            }

            return request(arr[i]);
        }

        sequence(items, asyncFunction).then(function(){
            //console.log('ALl complete');
        });
    </script>
@stop