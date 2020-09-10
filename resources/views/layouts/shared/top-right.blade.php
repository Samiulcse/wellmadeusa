<?php use App\Enumeration\Role; ?>

<ul>
    @if (Auth::check() && Auth::user()->role == Role::$BUYER)
        <li style="padding: 0px 10px">
            <div class="btn-group profile-btn-group">
                <a href="{{ route('buyer_show_overview') }}" type="button" class="btn btn-secondary btn-transparent">
                    <i class="fa fa-user"></i>
                    {{ Auth::user()->first_name.' '.Auth::user()->last_name }}
                </a>
                <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{{ route('buyer_show_orders') }}">Orders List</a>
                    <a class="dropdown-item" href="{{ route('view_wishlist') }}">Wishlist</a>
                    <a class="dropdown-item" href="{{ route('buyer_show_profile') }}">My Profile</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item btnLogOut" href="#"> <i class="icon-unlock"></i>Logout</a>
                </div>
            </div>
        </li>


        <li id="shopping_bag">
            <a href='#'>
                0
            </a>
        </li>

        <form id="logoutForm" class="" action="{{ route('logout_buyer') }}" method="post">
            {{ csrf_field() }}
        </form>
    @else
        <li>
            <a href='{{ route('buyer_login') }}'>Login</a>
        </li>
        <li>
            <a href='{{ route('buyer_register') }}'>Register</a>
        </li>
    @endif
    <li>
        <div class="input_box_wrapper">
            <i id="serach_button_icon" class="fas fa-search"></i>
            <div class='input_box'>
                <input autocomplete="off" name="search" id="search_input" type='text'/>
                <button>Search</button>
            </div>
            <!-- /.input_box -->
        </div>
    </li>
</ul>