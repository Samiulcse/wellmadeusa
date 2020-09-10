<?php $url = url()->current();?>
<div class="col-md-4 col-lg-2 col-sm-12 sidebar_column">
    <div class="row">
        <div class="col-sm-12">
            <div class="my_page_username">
                <p>Hey, {{ auth()->user()->first_name }}</p>
            </div>
            <div class="my_page_member_time">
                <p>{{ auth()->user()->email }}</p>
                <p>Member since {{ date('d M Y', strtotime(auth()->user()->created_at)) }}</p>
            </div>
        </div>
    </div>
    <div class="my_page_sidebar_mobile">
        <select class="form-control sub_cat_select" onchange="location = this.value;">
            <option <?php if(preg_match("/dashboard/i", $url)):?> selected <?php endif;?> value="{{route('buyer_show_dashboard')}}">My Account</option>
            <option <?php if(preg_match("/overview/i", $url)):?> selected <?php endif;?> value="{{route('buyer_show_overview')}}">Contact Information</option>
            <option <?php if(preg_match("/new/i", $url)):?> selected <?php endif;?> value="{{route('new_password_buyer_panel')}}">Update Password</option>
            <option <?php if(preg_match("/billing/i", $url)):?> selected <?php endif;?> value="{{route('buyer_billing')}}">Billing Information</option>
            <option <?php if(preg_match("/orders/i", $url)):?> selected <?php endif;?> value="{{route('buyer_show_orders')}}">Order History</option>
        </select>
    </div>
    <div class="my_page_sidebar">
        <nav class="sidebar" role="navigation">
            <ul class="sideNav nav navbar">
                <li <?php if(preg_match("/dashboard/i", $url)):?> class="my_page_active" <?php endif;?>><a href="{{route('buyer_show_dashboard')}}">My Account</a></li>
                <li <?php if(preg_match("/overview/i", $url)):?> class="my_page_active" <?php endif;?>><a href="{{route('buyer_show_overview')}}">Contact Information</a></li>
                <li <?php if(preg_match("/new/i", $url)):?> class="my_page_active" <?php endif;?>><a href="{{route('new_password_buyer_panel')}}">Update Password</a></li>       
                <li <?php if(preg_match("/billing/i", $url)):?> class="my_page_active" <?php endif;?>><a href="{{route('buyer_billing')}}">Billing Information</a></li>        
                <li <?php if(preg_match("/orders/i", $url)):?> class="my_page_active" <?php endif;?>><a href="{{route('buyer_show_orders')}}">Order History</a></li>
                <li><form style="float: left;padding-left: 10px;" action="{{ route('logout_buyer') }}" method="POST">@csrf<input class="my_page_sign_out" type="submit" value="Sign Out"></form></li>
            </ul>
        </nav>
    </div>
</div>