<link href="{{ asset('themes/admire/css/components.css') }}" rel="stylesheet">
<link href="{{ asset('themes/admire/css/custom.css') }}" rel="stylesheet">
<script type="text/javascript" src="{{ asset('themes/admire/js/components.js') }}"></script>
<style>
	.custom-column{
		border: 1px solid #ccc;
		padding-left: 0;
		padding-right: 0;
	}
	.nav-tabs .nav-item {
	    width: 50%;
	    border-right: 1px solid #ccc;
	    border-top: 1px solid #ccc;
	}
	.tab-content{
		padding: 20px 10px;
	}
	/* customer info css */
	.section-header:first-child {
	    margin-top: 0;
	}
	.section-header {
	    margin: 20px 0 14px;
	    padding-bottom: 8px;
	    border-bottom: 1px solid #999;
	}
	.section-header .section-title {
	    float: left;
	}
	.section-title {
	    font-size: 16px;
	    font-weight: 600;
	    line-height: 21px;
	}
	.section-header:after {
	    content: '';
	    clear: both;
	    display: block;
	    height: 0;
	    overflow: hidden;
	}
	.table-grid {
	    display: table;
	    width: 100%;
	    padding-left: 20px;
	}
	.table-grid__left--half {
	    width: 50%;
	}
	.table-grid__left {
	    display: table-cell;
	    text-align: left;
	}
	.row label {
	    display: inline-block;
	    font-weight: 600;
	}
	.width-125 {
	    width: 125px;
	}
	.text-left {
	    text-align: left;
	}
	.table-grid__right--half {
	    width: 50%;
	}
	.table-grid__right {
	    display: table-cell;
	    text-align: right;
	}
	.link {
	    color: #16325c !important;
	    text-decoration: underline !important;
	    cursor: pointer;
	}
	.link-light, .link-light i {
	    color: #3c74e7 !important;
	}
	.cursor-pointer {
	    cursor: pointer;
	}
	.margin-left-8 {
	    margin-left: 8px;
	}
	/* order history css */
	.success {
	    top: 50px;
	    right: 16px;
	    left: 268px;
	    background: #c1e3c6;
	    color: #229b33;
	    border: 1px solid #229b33;
	}
	.basic-table {
	    border: 1px solid #999;
	}
	.basic-table table {
	    width: 100%;
	    border: 0;
	    border-spacing: 0;
	    border-collapse: collapse;
	}
	.basic-table table tbody th {
	    text-align: left;
	    background: #f1f4f9;
	    font-weight: 600;
	}
	.basic-table table tbody td, .basic-table table tbody th {
	    line-height: 17px;
	    font-size: 14px;
	    font-weight: 600;
	    padding: 8px 6px;
	    border-right: 1px solid #999;
	    border-bottom: 1px solid #999;
	}
	.line-table table {
	    width: 100%;
	    border: 0;
	    border-spacing: 0;
	    border-collapse: collapse;
	}
	.line-table table tbody tr td, .line-table table tbody tr th, .line-table table tfoot tr td, .line-table table tfoot tr th, .line-table table thead tr td, .line-table table thead tr th {
	    height: 32px;
	    line-height: 17px;
	    padding: 8px 5px;
	    border-bottom: 1px solid #e3e3e3;
	    text-align: center;
	    font-size: 12px;
	    font-weight: 600;
	}
	.line-table:after {
	    content: '';
	    display: block;
	    height: 0;
	    overflow: hidden;
	    clear: both;
	}
</style>
<div class="inner bg-container">
	<div class="row">
	    <div class="col-md-12 custom-column">
	        <ul class="nav nav-tabs" id="myTab" role="tablist">
	            <li class="nav-item">
	                <a class="nav-link active" id="customerProfileTab" data-toggle="tab" href="#customerProfile" role="tab" aria-controls="customerProfile" aria-selected="true">Customer Profile</a>
	            </li>
	            <li class="nav-item">
	                <a class="nav-link" id="orderHistoryTab" data-toggle="tab" href="#orderHistory" role="tab" aria-controls="orderHistory" aria-selected="false">Order History</a>
	            </li>
	        </ul>

	        <div class="tab-content" id="myTabContent">
	            <div class="tab-pane fade show active" id="customerProfile" role="tabpanel" aria-labelledby="customerProfileTab">
	            	@if(!empty($user_info))
		                <div class="section-header">
				          <h3 class="section-title">Customer Information</h3>
				        </div>
				        <div class="table-grid">
				          	<div class="table-grid__left table-grid__left--half">
					            <div class="row">
                                    <label class="width-125">Customer Status:</label>
									@if($user_info->buyer->verified == 1)	
						              	<span>Verified by Document <a href="{{ asset($user_info->buyer->ein_path) }}" class="margin-left-8 link link-light cursor-pointer" onclick="viewDocument(); return false;">+ View Document</a></span>
									@else
										<span>Not Verified Yet.</span>
									@endif
					            </div>
					            <div class="row">
					              <label class="width-125">Company:</label>
					              <span>
					                  @if(!empty($user_info->buyer->company_name))
					                    {{ $user_info->buyer->company_name }}
					                  @endif
					              </span>
					            </div>
					            <div class="row">
					              <label class="width-125">Name:</label>
					              <span>
					                  @if(!empty($user_info->first_name))
					                    {{ $user_info->first_name .' '. $user_info->last_name}}
					                  @endif
					              </span>
					            </div>
					            <div class="row">
					              <label class="width-125">Email:</label>
					              <span>
					                  @if(!empty($user_info->email))
					                    {{ $user_info->email}}
					                  @endif
					              </span>
					            </div>
				          	</div>
				          	<div class="table-grid__right table-grid__right--half text-left">
				          		<div class="row">
					              <label class="width-125">Seller Permit:</label>
					              <span>
					                  @if(!empty($user_info->buyer->seller_permit_number))
					                    {{ $user_info->buyer->seller_permit_number }}
					                  @endif
					              </span>
					            </div>
					            <div class="row">
					              <label class="width-125">Website:</label>
					              <span>
					                  @if(!empty($user_info->buyer->website))
					                    {{ $user_info->buyer->website }}
					                  @endif
					              </span>
					            </div>
					            <div class="row">
					              <label class="width-125">Major Market:</label>
					              <span>
					              	@if($user_info->buyer->primary_customer_market == 1)
					              		{{ 'All' }}
					              	@elseif($user_info->buyer->primary_customer_market == 2)
					              		{{ 'African' }}
					              	@elseif($user_info->buyer->primary_customer_market == 3)
					              		{{ 'Asian' }}
					              	@elseif($user_info->buyer->primary_customer_market == 4)
					              		{{ 'Caucasian' }}
					              	@elseif($user_info->buyer->primary_customer_market == 5)
					              		{{ 'Latino/Hispanic' }}
					              	@elseif($user_info->buyer->primary_customer_market == 6)
					              		{{ 'Middle Eastern' }}
					              	@elseif($user_info->buyer->primary_customer_market == 7)
					              		{{ 'Native American' }}
					              	@elseif($user_info->buyer->primary_customer_market == 8)
					              		{{ 'Pacific Islander' }}
					              	@elseif($user_info->buyer->primary_customer_market == 9)
					              		{{ 'Other' }}
					              	@endif	
					              </span>
					            </div>
					            <div class="row">
					              <label class="width-125">Date Started:</label>
					              <span>
					                  @if(!empty($user_info->created_at))
					                    {{ date('m/d/Y', strtotime($user_info->created_at))}}
					                  @endif
					              </span>
					            </div>
				          	</div>
				        </div>
				        <div class="section-header">
				          <h3 class="section-title">Shipping Information</h3>
				        </div>
				        <div class="table-grid">
				          	<div class="table-grid__left table-grid__left--half">
					            <div class="row">
					              <label class="width-125">Location:</label>
					              <span>
					                  @if(!empty($user_info->buyerShipping->country->name))
					                    {{ $user_info->buyerShipping->country->name }}
					                  @endif
					              </span>
					            </div>
					            <div class="row">
					              <label class="width-125">Address:</label>
					              <span>
					                  @if(!empty($user_info->buyerShipping->address))
					                    {{ $user_info->buyerShipping->address }}
					                  @endif
					              </span>
					            </div>
					            <div class="row">
					              <label class="width-125">State:</label>
					              <span>
					                  @if(!empty($user_info->buyerShipping->state->name))
					                    {{ $user_info->buyerShipping->state->name }}
					                  @endif
					              </span>
					            </div>
				          	</div>
					        <div class="table-grid__right table-grid__right--half text-left">
					            <div class="row">
					              <label class="width-125">City:</label>
					              <span>
					                  @if(!empty($user_info->buyerShipping->city))
					                    {{ $user_info->buyerShipping->city }}
					                  @endif
					              </span>
					            </div>
					            <div class="row">
					              <label class="width-125">Zip Code:</label>
					              <span>
					                  @if(!empty($user_info->buyerShipping->zip))
					                    {{ $user_info->buyerShipping->zip }}
					                  @endif
					              </span>
					            </div>
					            <div class="row">
					              <label class="width-125">Phone:</label>
					              <span>
					                  @if(!empty($user_info->buyerShipping->phone))
					                    {{ $user_info->buyerShipping->phone }}
					                  @endif
					              </span>
					            </div>
					        </div>
				        </div>
				        <div class="section-header">
				          <h3 class="section-title">Billing Information</h3>
				        </div>
				        <div class="table-grid">
				          	<div class="table-grid__left table-grid__left--half">
					            <div class="row">
					              <label class="width-125">Location:</label>
					              <span>
					                  @if(!empty($user_info->buyer->billingCountry->name))
					                    {{ $user_info->buyer->billingCountry->name }}
					                  @endif
					              </span>
					            </div>
					            <div class="row">
					              <label class="width-125">Address:</label>
					              <span>
					                  @if(!empty($user_info->buyer->billing_address))
					                    {{ $user_info->buyer->billing_address }}
					                  @endif
					               </span>
					            </div>
					            <div class="row">
					              <label class="width-125">State:</label>
					              <span>
					                  @if(!empty($user_info->buyer->billingState->name))
					                    {{ $user_info->buyer->billingState->name }}
					                  @endif
					              </span>
					            </div>
				          	</div>
				          	<div class="table-grid__right table-grid__right--half text-left">
					            <div class="row">
					              <label class="width-125">City:</label>
					              <span>
					                  @if(!empty($user_info->buyer->billing_city))
					                    {{ $user_info->buyer->billing_city }}
					                  @endif
					              </span>
					            </div>
					            <div class="row">
					              <label class="width-125">Zip Code:</label>
					              <span>
					                  @if(!empty($user_info->buyer->billing_zip))
					                    {{ $user_info->buyer->billing_zip }}
					                  @endif
					              </span>
					            </div>
					            <div class="row">
					              <label class="width-125">Phone:</label>
					              <span>
					                  @if(!empty($user_info->buyer->billing_phone))
					                    {{ $user_info->buyer->billing_phone }}
					                  @endif
					              </span>
					            </div>
				          	</div>
				        </div>
			        @endif
	            </div>

	            <div class="tab-pane fade" id="orderHistory" role="tabpanel" aria-labelledby="orderHistoryTab">
	            	@if(!empty($customer_order_data))
                        <p class="success success-top text-s padding-3">
                            This customer has ordered from your company before.
                        </p>
			        @endif
			        <div class="section-header">
			          <h3 class="section-title">Statistics for Orders</h3>
			        </div>
			        <div class="basic-table">
			        	@if(!empty($all_order_data))
				          	<table>
					            <colgroup>
					              <col width="20%">
					              <col width="30%">
					              <col width="20%">
					              <col width="10%">
					              <col width="10%">
					            </colgroup>
					            <tbody>
					              <tr>
					                <th scope="row">No. of total orders</th>
					                <td>{{ $all_order_data['user_all_order'] }}</td>
					                <th scope="row">Total order Amounts</th>
					                <td>${{ $all_order_data['total'] }}</td>
					              </tr>
					              <tr>
					                <th scope="row">No. of visits</th>
					                <td>{{ $all_order_data['all_visit'] }}</td>
					                <th scope="row">Registered On</th>
                                    <td>{{ $all_order_data['user_created'] }}</td>
                                    <th scope="row">Last visited date</th>
					                <td>
                                        @if(!empty($all_order_data['last_visit']))
                                            {{ $all_order_data['last_visit'] }}
                                        @endif
                                    </td>
					              </tr>
					            </tbody>
				          	</table>
			        	@else
			        		<h5 class="p-3">No order from Stylepick.</h5>
			        	@endif
			        </div>

			        <div class="section-header">
			          <h3 class="section-title">Statistics for Orders [Per Month]</h3>
			        </div>
			        <div class="line-table line-table--order">
			          	<table>
				            <colgroup>
				              <col width="25%">
				              <col width="25%">
				              <col width="25%">
				              <col width="25%">
				            </colgroup>
				            <thead>
				              <tr>
				                <th class="line-table__date" scope="col">Month</th>
				                <th class="line-table__date" scope="col">Year</th>
				                <th class="line-table__date" scope="col">Total Orders (#)</th>
				                <th class="line-table__price" scope="col">Order amount ($)</th>
				              </tr>
				            </thead>
				            <tbody>
				            	<?php 
				            		$counted_total = 0; 
				            		$counted_amount = 0; 
				            	?>
								@if(!empty($stat_customer_order))
									@foreach($stat_customer_order as $order)
					              	<tr>
						                <td class="line-table__date">{{ $order->month }}</td>
						                <td class="line-table__date">{{ $order->year }}</td>
						                <td class="line-table__date">{{ $order->order_count }}</td>
						                <td class="line-table__price">${{ $order->total_amount }}</td>
						                <?php 
						                	$counted_total += $order->order_count;
						                	$counted_amount += $order->total_amount;
						                ?>
					             	</tr>
					             	@endforeach
								@endif
				            </tbody>
				            <tfoot>
				              <tr>
				                <th class="line-table__total" scope="row">Total</th>
				                <td class="line-table__date"></td>
				                <td class="line-table__date">{{ $counted_total }}</td>
				                <td class="line-table__cancel-n">${{ $counted_amount }}</td>
				              </tr>
				            </tfoot>
			          	</table>
			        </div>	
	            </div>
	        </div>
	    </div>
	</div>
</div>

<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img src="" class="imagepreview" style="width: 100%;" >
      </div>
    </div>
  </div>
</div>

<script>
	function viewDocument(){
		var href = event.currentTarget.getAttribute('href')
		$('.imagepreview').attr('src', href);
		$('#imagemodal').modal('show');
	}
</script>