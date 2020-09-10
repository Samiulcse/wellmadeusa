<form class="form-horizontal" method="POST" id="form-settings">
    <div class="form_row">
        <div class="label_inline width_150p">
            <label for="cost_dollars">Cost Dollars:</label>             
        </div>
        <div class="form_inline">
            <input type="number" min="0" class="form_global" name="cost_dollars" value="{{ isset($pointSetting->cost_dollars) ? $pointSetting->cost_dollars : ''  }}" id="cost_dollars" placeholder="$">
        </div>
    </div>

    <div class="form_row">
        <div class="label_inline width_150p">
            <label for="point_rewards">Point Rewards: </label> 
        </div>
        <div class="form_inline">
            <input type="text" class="form_global" name="point_rewards" value="{{ isset($pointSetting->point_rewards) ? $pointSetting->point_rewards : ''  }}" id="point_rewards">
        </div>
    </div>

    <div class="text_right m15">
        <div class="display_inline mr_0">
            <button class="ly_btn  btn_blue min_width_100p " id="btnSaveSettings">SAVE</button>
        </div>
    </div>
</form>