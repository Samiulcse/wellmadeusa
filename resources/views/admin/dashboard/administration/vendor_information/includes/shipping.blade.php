<form class="form-horizontal" method="POST" id="form-shipping">
    @csrf

    <div class="form_row">
        <div class="label_inline fw_500 required width_150p  align_top">
            Description
        </div>
        <div class="form_inline">
            <textarea class="form_global" name="shipping" id="order_shipping_editors" rows="10" cols="80">{{ $user->vendor->shipping }}</textarea>
        </div>
    </div>

    <br>
    <div class="text_right m15">
        <div class="display_inline mr_0">
            <button class="ly_btn  btn_blue min_width_100p " id="btnShippingSave">Save</button>
        </div>
    </div>
</form>