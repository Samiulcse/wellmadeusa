@extends('layouts.home_layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="my_acc_page">
                <div class="row">
                    @include('buyer.profile.buyer_sidebar')
                    <div class="col-md-10 col-sm-12 main_page_column">
                        <div class="my_page_content" style="background-image: url('themes/front/images/bg.jpeg');">
                            <div class="row">
                                <div class="col-md-offset-2 col-md-8 col-sm-12">
                                    <div class="my_page_user_panel">
                                        <div class="panel panel-default custom_panel">
                                            <div class="panel-heading custom_heading">My Account</div>
                                            <div class="panel-body custom_content">
                                                <div class="check_out_form mt-5">
                                                <form class="account_form">
                                                    <div class="form-group ">
                                                        <input type="text" class="form-control" id="exampleInputName" placeholder="First Name"> 
                                                    </div>
                                                    <div class="form-group"> 
                                                        <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Last Name">
                                                    </div>
                                                    <div class="form-group checkout_country_select mb-5">
                                                        <select class="form-control" >
                                                            <option  value="">Gender</option>
                                                            <option value="" >Female</option>
                                                            <option value="" >Male</option>
                                                            <option  value="">I'd rather not say</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="button" class="update_profile_btn" data-wow-delay="0.7s">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-default custom_panel">
                                            <div class="panel-heading custom_heading">Checkout Info</div>
                                            <div class="panel-body custom_content">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection