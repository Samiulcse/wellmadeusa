<div class="my_account_menu for_desktop">
	<h2> <a href="{{ route('buyer_show_overview') }}">MY DASHBOARD</a></h2>
	<ul>
		<li><a href="{{ route('buyer_show_orders') }}">ORDERS</a></li>
		<li><a href="{{ route('buyer_show_rewards') }}">REWARD POINTS</a></li>
		<li><a href="{{ route('buyer_show_profile') }}">PROFILE </a></li>
		<li><a href="{{ route('buyer_show_address') }}">ADDRESS BOOK </a></li>
		<li><a href="{{ route('buyer_show_message') }}">MESSAGES <span class="badge badge-info" id="message_count">{{ $unread_messages }}</span></a></li>
		<li><a href="#" class="btnLogOut">LOG OUT </a></li>
	</ul>
</div>
<div class="buyer_deshboard_menu for_mobile">
	<div class="card">
		<div class="card-header" id="buyer_menu">
			<button class="btn-link collapsed" data-toggle="collapse" data-target="#buyerOne">
				SELECT
			</button>
		</div>
		<div id="buyerOne" class="collapse">
			<div class="card-body clearfix">
				<ul>
				    <li><a href="{{ route('buyer_show_overview') }}">MY DASHBOARD</a></li>
					<li><a href="{{ route('buyer_show_orders') }}">ORDERS</a></li>
					<li><a href="{{ route('buyer_show_rewards') }}">REWARD POINTS</a></li>
					<li><a href="{{ route('buyer_show_profile') }}">PROFILE </a></li>
					<li><a href="{{ route('buyer_show_address') }}">ADDRESS BOOK </a></li>
					<li><a href="{{ route('buyer_show_message') }}">MESSAGES <span class="badge badge-info" id="message_count">{{ $unread_messages }}</span></a></li>
					<li><a href="#" class="btnLogOut">LOG OUT </a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
