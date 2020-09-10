<form action="{{ route('administration_return_info') }}" method="POST">
    @csrf
    <div class="form_row">
        <div class="label_inline fw_500 required width_150p"> Description</div>
        <div class="form_inline">
            <textarea name="description" class="form_global" id="returndescription" rows="10" cols="80">{{ $user->vendor->return_policy }}</textarea>
        </div>
    </div>
    <br>
    <div class="form_row">
        <div class="text_right">
            <button class="ly_btn  btn_blue min_width_100p " id="btnSizeChartSubmit">Save</button>
        </div>
    </div>
</form>
