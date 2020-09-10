@extends('layouts.home_layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="my_acc_page">
                <div class="row">
                    @include('buyer.profile.buyer_sidebar')
                    <div class="col-lg-10 col-md-8 col-sm-12 main_page_column">
                        <div class="my_page_content" style="background-image: url('../themes/front/images/bg.jpeg');">
                            <div class="row">
                                <div class="col-md-12  col-lg-offset-2 col-lg-8 col-sm-12">
                                    <div class="my_page_user_panel">
                                        <div class="card card-default custom_panel">
                                            <div class="card-heading custom_heading">Update Profile Image</div>
                                                <div class="card-body custom_content">
                                                <div class="check_out_form mt-5">
                                                    @if(Session::has('flash_message_success'))
                                                        <div class="alert alert-success background-success">
                                                            <button type="button" class="close" data-dismiss="alert">x</button>
                                                            <strong>{!! session('flash_message_success')!!}</strong>
                                                        </div>
                                                    @endif
                                                    <form class="account_form"  method="post" action="{{route('buyer_change_avatar')}}" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="form-group row">
                                                            <label for="changeImage" class="col-sm-12 col-form-label col-form-label-md">Image</label>
                                                            <div class="col-lg-8 col-md-12">
                                                                <div class="form-group">
                                                                  <label for="inputGroupFile01" class="sr-only">File</label>
                                                                  <div class="input-group">
                                                                    <input type="text" id="inputGroupFile01" name="avatar" class="form-control" placeholder="No file selected" readonly>
                                                                    <span class="input-group-btn">
                                                                      <div class="btn btn-default  custom-file-uploader">
                                                                        <input type="file" name="avatar" onchange="this.form.avatar.value = this.files.length ? this.files[0].name : ''" />
                                                                        Select a file
                                                                      </div>
                                                                    </span>
                                                                  </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 col-md-12">
                                                                @if(!empty($buyerAvatar->avatar))
                                                                <img id="blah" align='middle'  src="{{asset(\App\Model\MetaBuyer::avatar_path().$buyerAvatar->avatar)}}" class="img-fluid" alt="your image" title='' style="width: 100px;"/>
                                                                    @else
                                                                    <img id="blah" align='middle' src="{{asset('images/avatar/user.png')}}" class="img-fluid" alt="your image" style="width: 100px;" />
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <button type="submit" class="update_profile_btn" data-wow-delay="0.7s">Update</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
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
</div>
@endsection