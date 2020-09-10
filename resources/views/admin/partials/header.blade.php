<header class="header_area">
    <ul>
        <li>Welcome {{auth()->user()->name}}!</li>
        <li class="header_msg"><a href="{{ route('all_message') }}"><span class="red_bg">{{ $unread_messages }}</span> MESSAGES</a></li>
        <li class="header_account"> more <span></span>
            <ul class="header_account_inner">
                <li>
                    <a href="#" onclick="document.getElementById('logoutForm').submit()"><i class="fas fa-sign-out-alt"></i>  Sign Out</a>
                    <form id="logoutForm" class="" action="{{ route('logout_admin') }}" method="post">
                        {{ csrf_field() }}
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</header>
