@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="content col-md-12 margin-bottom-1x">
            <h4>Notification</h4>
        </div>
    </div>
</div>
<div class="container content">
    <div class="table-responsive">
        <table class="table">
            @foreach($notifications as $notification)
                <tr class="{{ ($notification->view == 0) ? 'table-danger' : '' }}">
                    <th><a class="nav-link" href="{{ route('view_notification', ['id' => $notification->id, 'link' => $notification->link]) }}">
                            {{ $notification->text }}</a>
                    </th>
                    <td>{{ date('F d, Y g:h a') }}</td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
@stop