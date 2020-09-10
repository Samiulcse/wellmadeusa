@extends('admin.layouts.main')

@section('additionalCSS')
    <link href="{{ asset('plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <style type="text/css">
        .text-danger{
            color: red;
        }
    </style>
@stop

@section('content')
<div class="ly_accrodion">
    <div class="ly_accrodion_heading">
        <div id="btnAddNew" class="ly_accrodion_title {{ count($errors) > 0 ? ' open_acc' : ''}}" data-toggle="accordion" data-target="#addtasks" data-class="accordion">
            <span id="addEditTitle">{{ old('inputAdd') == '0' ? 'Edit Tasks' : 'Add Tasks' }}</span>
        </div>
    </div>
    <div class="accordion_body default_accrodion  {{ count($errors) > 0 ? ' open' : ''}}" id="addtasks" style="">
    <form action="{{ route('create_tasks') }}" method="post" class="form-horizontal">
        @csrf
        <input type="hidden" id="editid" name="id" value="0" /> <!--Use for Edit Task only-->
        <div class="form_row">
            <div class="label_inline required width_150p">
                Task Name:
            </div>
            <div class="form_inline">
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form_global"/>
                @if ($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
            </div>
        </div>
        <div class="form_row">
            <div class="label_inline required width_150p">
                Description:
            </div>
            <div class="form_inline">
                <input type="text" name="desc" id="desc" value="{{ old('desc') }}" class="form_global"/>
                @if ($errors->has('desc'))
                    <span class="text-danger">{{ $errors->first('desc') }}</span>
                @endif
            </div>
        </div>
        <div class="form_row">
            <div class="label_inline required width_150p">
                Start Date
            </div>
            <div class="form_inline">
                <input type="text" name="start_date" id="start_date" class="date form_global" value="{{ old('start_date') }}"/>
                @if ($errors->has('start_date'))
                    <span class="text-danger">{{ $errors->first('start_date') }}</span>
                @endif
            </div>
        </div>
        <div class="form_row">
            <div class="label_inline required width_150p">
                End Date
            </div>
            <div class="form_inline">
                <input type="text" name="end_date" id="end_date" class="date form_global" value="{{ old('end_date') }}"/>
                @if ($errors->has('end_date'))
                    <span class="text-danger">{{ $errors->first('end_date') }}</span>
                @endif
            </div>
        </div>
        <div class="form_row">
            <div class="ly-4">
                <div class="ly-row">
                <div class="label_inline required width_150p">
                    Text Color
                </div>
                <div class="form_inline">
                    <input value="" name="textcolor" id="textcolor" class="jscolor form-control">
                </div>
                </div>
            </div>
            <div class="ly-4">
                <div class="label_inline required width_150p">
                    Lable Color
                </div>
                <div class="form_inline">
                    <input value="" name="lablecolor" id="lablecolor" class="jscolor form-control">
                </div>
            </div>
        </div>
        <div class="form_row">
            <div class="form_inline text_right">
            <input type="submit" value="Save" class="ly_btn btn_blue btn-sm"/>
            </div>
        </div>

        </form>
    </div>
</div>

<div class="ly-row">
    <div class="ly-12">
        <table class="table table-striped" id="myTable">
            <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Action</th>
            </tr>
            </thead>

            <tbody>
            @foreach($tasks as $task)
                <tr>
                    <td>{{ $task->name }}</td>
                    <td>{{ $task->desc }}</td>
                    <td>{{ $task->start_date }}</td>
                    <td>{{ $task->end_date }}</td>
                    <td>
                        <a class="link taskedit" data-txtcolor="{{ $task->color }}" data-lablecolor="{{ $task->lable_bg }}" data-desc="{{ $task->desc }}" data-id="{{ $task->id }}" data-name="{{ $task->name }}" data-date="{{ $task->start_date }}" data-enddate="{{ $task->end_date }}"   role="button" style="color: blue">Edit</a> |
                        <a class="link taskDelete" data-id="{{ $task->id }}" role="button" style="color: red">Delete</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@stop

@section('additionalJS')
<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>
<script type="text/javascript" src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscolor/2.0.4/jscolor.js"></script>
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.date').datepicker({
            autoclose: true,
            dateFormat: "yy-mm-dd"
        });

        $('.taskedit').click(function(){
            var id = $(this).data('id');
            var name = $(this).data('name');
            var desc = $(this).data('desc');
            var txtcolor = $(this).data('txtcolor');
            var lablecolor = $(this).data('lablecolor');
            var start_date = $(this).data('date');
            var end_date = $(this).data('enddate');
            $('.ly_accrodion_title').addClass('open_acc');
            $('#addtasks').show();

            $('#addtasks').find('#name').val(name);
            $('#addtasks').find('#desc').val(desc);
            $('#addtasks').find('#start_date').val(start_date);
            $('#addtasks').find('#end_date').val(end_date);
            $('#addtasks').find('#textcolor').val(txtcolor);
            $('#addtasks').find('#lablecolor').val(lablecolor);
            $('#addtasks').find('#editid').val(id);
        });
        $('.taskDelete').click(function(){
            var id = $(this).data('id');
            $.ajax({
                method: "POST",
                url: "{{ route('delete_tasks') }}",
                data: { id: id }
            }).done(function( msg ) {
                location.reload();
            });
        });

    });
</script>
@stop
