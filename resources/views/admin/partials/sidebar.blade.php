<?php use App\Enumeration\PageEnumeration; ?>

<div class="brand">
    @if(isset($logo_path))
        @if ($logo_path != '')
            <a href="{{ route('admin_dashboard') }}">
                <img src="{{ $logo_path }}" class="admin_img" alt="logo">
            </a>
        @endif
    @endif
</div>
<div class="side_nav_list">
    <ul>
        <?php
            $menu_items = ['Create a New Item', 'Category', 'Color', 'Pack', 'Other: Fabric, Made In, Supplying Vendor, Default Item Setting', 'Item Edit',
                'Edit All Items', 'Data Import', 'Master Color', 'Master Fabric', 'Sort Items'];
        ?>
        <li data-toggle="accordion" data-target="#products" class="accordion_heading open_acc active {{ ((isset($page_title) && in_array($page_title, $menu_items)) || \Request::segment(3) == 'category') ? ' open_sec open_acc active' : '' }}" data-class="accordion">
            Products
        </li>
        <ul class="sub_accordion default_accrodion open {{ ((isset($page_title) && in_array($page_title, $menu_items)) || \Request::segment(3) == 'category') ? ' open' : '' }}" id="products">

            <li class="{{ (isset($page_title) && $page_title == 'Data Import') ? 'active' : '' }}">
                <a href="{{ route('admin_data_import') }}">Data Import</a>
            </li>
            <li class="{{ (isset($page_title) && $page_title == 'Create a New Item') ? 'active' : '' }}">
                <a href="{{ route('admin_create_new_item') }}">New Products</a>
            </li>

            <?php
                $sub_menu_items = ['Item Edit', 'Edit All Items'];
                if(isset($categories)){
                    foreach($categories as $category) {
                        $sub_menu_items[] = $category['name'];

                        if (sizeof($category['subCategories']) > 0) {
                            foreach ($category['subCategories'] as $sub) {
                                $sub_menu_items[] = $sub['name'];
                            }
                        }
                    }
                }

                $title = isset($page_title) ? $page_title : '';
            ?>
            <li class="sub_child_accordion {{ ((isset($page_title) && in_array($page_title, $sub_menu_items)) || \Request::segment(3) == 'category') ? ' active' : '' }}">
                <div data-toggle="accordion" data-target="#listProducts" class="sub_child_accordion_open accordion_heading  {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? ' open_sec' : '' }}" data-class="accordion"></div>
                <a href="{{ route('admin_item_list_all') }}">List Products</a>
            </li>

            <?php $sub_cat_id = Request::segment(4); ?>
            <ul class="sub_gchild_accordion default_accrodion {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? 'open' : '' }}" id="listProducts">
                @if(isset($categories))
                    @foreach($categories as $category)
                        <?php
                        $subCat = [];

                        foreach ($category['subCategories'] as $sub)
                            $subCat[] = $sub['name'];
                        ?>

                        @if (sizeof($category['subCategories']) > 0)

                            <li class="{{ (in_array($title, $subCat) || $sub_cat_id == $category['id'])  ? ' active' : '' }}">
                                <a href="{{ route('admin_item_list_by_category', ['category' => $category['id']]) }}">{{ $category['name'] }}</a> <span data-toggle="accordion" data-target="#listProductsSub_{{ $category['id'] }}" class=" sub_gchild_accordion_open accordion_heading {{ in_array($title, $subCat) ? ' open_sec' : '' }}" data-class="accordion"></span>
                            </li>
                            <ul class="sub_gchild_accordion sub_ggchild_accordion default_accrodion {{ in_array($title, $subCat) ? ' open' : '' }}" id="listProductsSub_{{ $category['id'] }}">

                                @foreach($category['subCategories'] as $sub)
                                    <li class="{{ (isset($page_title) && $title == $sub['name']) ? 'active' : '' }}">
                                        <a href="{{ route('admin_item_list_by_category', ['category' => $sub['id']]) }}"> {{ $sub['name'] }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <li class="{{ ((isset($page_title) && $title == $category['name']) || in_array($title, $subCat)) ? 'active' : '' }} {{ sizeof($category['subCategories']) > 0 ? 'has-sub-categories' : '' }}" data-id="{{ $category['id'] }}">
                                <a href="{{ (sizeof($category['subCategories']) > 0) ? 'javascript:;' : route('admin_item_list_by_category', ['category' => $category['id']]) }}"> {{ $category['name'] }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            </ul>

            <?php $sub_menu_items = ['Category', 'Color', 'Pack', 'Other: Fabric, Made In, Supplying Vendor, Default Item Setting', 'Master Color','Sort Items', 'Master Fabric'] ?>
            <li class="sub_child_accordion {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? ' active' : '' }}">
                <div data-toggle="accordion" data-target="#productSetting" class="sub_child_accordion_open accordion_heading  {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? ' open_sec' : '' }}" data-class="accordion"></div>
                <a data-toggle="accordion" data-target="#productSetting">Product Settings</a>
            </li>
            <ul class="sub_gchild_accordion default_accrodion {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? 'open' : '' }}" id="productSetting">
                <li class="{{ (isset($page_title) && $page_title == 'Category') ? 'active' : '' }}">
                    <a href="{{ route('admin_category') }}">Category</a>
                </li>

                <li class="{{ (isset($page_title) && $page_title == 'Master Color') ? 'active' : '' }}">
                    <a href="{{ route('admin_master_color') }}">Master Color</a>
                </li>
                <li class="{{ (isset($page_title) && $page_title == 'Color') ? 'active' : '' }}">
                    <a href="{{ route('admin_color') }}">Color</a>
                </li>
                <li class="{{ (isset($page_title) && $page_title == 'Pack') ? 'active' : '' }}">
                    <a href="{{ route('admin_pack') }}">Pack</a>
                </li>
                <li class="{{ (isset($page_title) && $page_title == 'Sort Items') ? 'active' : '' }}">
                    <a href="{{ route('admin_sort_items_view') }}">Sort Items</a>
                </li>

                <li class="{{ (isset($page_title) && $page_title == 'Master Fabric') ? 'active' : '' }}">
                    <a href="{{ route('admin_master_fabric') }}">Master Fabric</a>
                </li>

                <li class="{{ (isset($page_title) && $page_title == 'Other: Fabric, Made In, Supplying Vendor, Default Item Setting') ? 'active' : '' }}">
                    <a href="{{ route('admin_item_settings_others') }}">Others</a>
                </li>
            </ul>

            <?php $sub_menu_items = ['category landing'] ?>
            <li class="sub_child_accordion {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? ' active' : '' }}">
                <div data-toggle="accordion" data-target="#category_landing_page" class="sub_child_accordion_open accordion_heading {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? ' open_sec' : '' }} " data-class="accordion"></div>
                <a data-toggle="accordion" data-target="#category_landing_page">Category Landing Page</a>
            </li>
            <ul class="sub_gchild_accordion default_accrodion {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? 'open' : '' }}" id="category_landing_page">
                @foreach($categories as $d_cat)
                    <li class=" "><a href="{{ route('category_landing_page', ['category' => $d_cat['id']]) }}">{{$d_cat['name']}}</a></li>
                @endforeach
            </ul>


        </ul>
        <?php
            $menu_items = ['New Orders', 'Order Details', 'Confirmed Orders', 'Back Orders', 'Shipped Orders', 'Cancel Orders',
                'Return Orders', 'All Orders', 'Incomplete Checkouts'];
        ?>
        <li data-toggle="accordion" data-target="#orders" class="accordion_heading open_acc active {{ (isset($page_title) && in_array($page_title, $menu_items)) ? ' open_acc active' : '' }}" data-class="accordion">
            Orders
        </li>
        <ul class="sub_accordion default_accrodion open {{ (isset($page_title) && in_array($page_title, $menu_items)) ? ' open' : '' }}" id="orders">
            <?php
                $sub_menu_items = ['New Orders', 'Order Details', 'Confirmed Orders', 'Back Orders', 'Shipped Orders',
                    'Cancel Orders', 'Return Orders', 'All Orders'];
            ?>
            <li class="sub_child_accordion {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? ' active' : '' }}">
                <div data-toggle="accordion" data-target="#allOrders" class="sub_child_accordion_open accordion_heading  {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? ' open_sec' : '' }}" data-class="accordion"></div>
                <a href="{{ route('admin_all_orders') }}">All Orders</a>
            </li>
            <ul class="sub_gchild_accordion default_accrodion {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? 'open' : '' }}" id="allOrders">
                <li class="{{ (isset($page_title) && $page_title == 'New Orders') ? 'active' : '' }}">
                    <a href="{{ route('admin_new_orders') }}">New Orders</a>
                </li>

                <li class="{{ (isset($page_title) && $page_title == 'Confirmed Orders') ? 'active' : '' }}">
                    <a href="{{ route('admin_confirmed_orders') }}">Confirmed</a>
                </li>

                <li class="{{ (isset($page_title) && $page_title == 'Back Orders') ? 'active' : '' }}">
                    <a href="{{ route('admin_backed_orders') }}">Back Ordered</a>
                </li>

                <li class="{{ (isset($page_title) && $page_title == 'Shipped Orders') ? 'active' : '' }}">
                    <a href="{{ route('admin_shipped_orders') }}">Shipped</a>
                </li>

                <li class="{{ (isset($page_title) && $page_title == 'Cancel Orders') ? 'active' : '' }}">
                    <a href="{{ route('admin_cancelled_orders') }}">Cancelled</a>
                </li>

                <li class="{{ (isset($page_title) && $page_title == 'Return Orders') ? 'active' : '' }}">
                    <a href="{{ route('admin_returned_orders') }}">Returned</a>
                </li>
            </ul>
            <li class="{{ (isset($page_title) && $page_title == 'Create New Order') ? 'active' : '' }}">
                <a href="{{ route('admin_new_order_create') }}">Create New Order</a>
            </li>
            <li class="{{ (isset($page_title) && $page_title == 'Incomplete Checkouts') ? 'active' : '' }}">
                <a href="{{ route('admin_incomplete_orders') }}">Incomplete Checkouts</a>
            </li>
        </ul>
        <?php $menu_items = ['Header Top' ,'Logo', 'Main Banner', 'Section Two Banner', 'Section Three Banner', 'Best Seller','Recommend Item', 'Bottom Banner', 'Category Banner', 'Page/Meta - Mobile Home Page Custom Section', 'Page/Meta - Home Page Custome Section2'] ?>
        <li data-toggle="accordion" data-target="#bannerManager" class="accordion_heading {{ (isset($page_title) && in_array($page_title, $menu_items)) ? ' open_sec open_acc active' : '' }}" data-class="accordion">
            Banner Manager
        </li>
        <ul class="sub_accordion default_accrodion {{ (isset($page_title) && in_array($page_title, $menu_items)) ? ' open' : '' }}" id="bannerManager">
            <li class="{{ (isset($page_title) && $page_title == 'Logo') ? 'active' : '' }}">
                <a href="{{ route('admin_banner') }}">Logo</a>
            </li>

            <li class="{{ (isset($page_title) && $page_title == 'Main Banner') ? 'active' : '' }}">
                <a href="{{ route('admin_front_page_banner_items') }}">Main Banner</a>
            </li>

            <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Mobile Home Page Custom Section') ? 'active' : '' }}">
                <a href="{{ route('admin_page_view', ['id' => PageEnumeration::$HOME_PAGE_CUSTOM_SECTION]) }}">Mobile Home Page Custom Section</a>
            </li>
            <li class="{{ (isset($page_title) && $page_title == 'Recommend Item') ? 'active' : '' }}">
                <a href="{{ route('admin_front_recommend_banner') }}">Recommended Items</a>
            </li>

        </ul>
        <?php $menu_items = ['All Customer', 'Block Customers', 'Store Credit','Create Customer', 'Customer Registration Complete'] ?>
        <li data-toggle="accordion" data-target="#customers" class="accordion_heading {{ (isset($page_title) && in_array($page_title, $menu_items)) ? ' open_sec open_acc active' : '' }}" data-class="accordion">
            Customers
        </li>
        <ul class="sub_accordion default_accrodion {{ (isset($page_title) && in_array($page_title, $menu_items)) ? ' open' : '' }}" id="customers">
            <li class="{{ (isset($page_title) && $page_title == 'All Customer') ? 'active' : '' }}">
                <a href="{{ route('admin_all_buyer') }}">All Customers</a>
            </li>
            <li class="{{ (isset($page_title) && ($page_title == 'Create Customer' ||  $page_title == 'Customer Registration Complete')) ? 'active' : '' }}">
                <a href="{{ route('customer_create') }}">Create Customer</a>
            </li>
            <li class="{{ (isset($page_title) && $page_title == 'Store Credit') ? 'active' : '' }}">
                <a href="{{ route('admin_store_credit_view') }}">Store Credit</a>
            </li>
        </ul>


        <?php $menu_items = ['Vendor Information', 'Account Setting', 'Shipping Methods', 'Courier', 'Ship Method','Promotions', 'Point System', 'Social Links','Social Feeds', 'All Message','Buyer Home','Welcome Notification', 'Top Notification'] ?>
        <li data-toggle="accordion" data-target="#administration" class="accordion_heading {{ (isset($page_title) && in_array($page_title, $menu_items)) ? ' open_sec open_acc active' : '' }}" data-class="accordion">
            Administration
        </li>

        <ul class="sub_accordion default_accrodion {{ (isset($page_title) && in_array($page_title, $menu_items)) ? ' open' : '' }}" id="administration">
            <li class="{{ (isset($page_title) && $page_title == 'Vendor Information') ? 'active' : '' }}">
                <a href="{{ route('admin_admin_information') }}">Vendor Information</a>
            </li>
            <li class="{{ (isset($page_title) && $page_title == 'Account Setting') ? 'active' : '' }}">
                <a href="{{ route('admin_account_setting') }}">Account Setting</a>
            </li>
            <li class="{{ (isset($page_title) && $page_title == 'Courier') ? 'active' : '' }}">
                <a href="{{ route('admin_courier') }}">Courier</a>
            </li>
            <li class="{{ (isset($page_title) && $page_title == 'Ship Method') ? 'active' : '' }}">
                <a href="{{ route('admin_ship_method') }}">Ship Method</a>
            </li>
            <li class="{{ (isset($page_title) && $page_title == 'Promotions') ? 'active' : '' }}">
                <a href="{{ route('admin_promotions') }}">Promotions</a>
            </li>
            <li class="{{ (isset($page_title) && $page_title == 'Point System') ? 'active' : '' }}">
                <a href="{{ route('admin_point_system') }}">Point System</a>
            </li>
            <li class="{{ (isset($page_title) && $page_title == 'Social Links') ? 'active' : '' }}">
                <a href="{{ route('admin_social_link') }}">Social Links</a>
            </li>
            <li class="{{ (isset($page_title) && $page_title == 'Social Feeds') ? 'active' : '' }}">
                <a href="{{ route('admin_social_feed') }}">Social Feed</a>
            </li>

            <?php $sub_menu_items = ['Buyer Home','Welcome Notification', 'Top Notification'] ?>
            <li class="sub_child_accordion {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? ' active' : '' }}">
                <div data-toggle="accordion" data-target="#notification" class="sub_child_accordion_open accordion_heading  {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? ' open_sec' : '' }}" data-class="accordion"></div>
                <a href="javascript:void(0)" data-toggle="accordion" data-target="#notification">Notification</a>
            </li>

            <ul class="sub_gchild_accordion default_accrodion {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? 'open' : '' }}" id="notification">
                <li class="{{ (isset($page_title) && $page_title == 'Welcome Notification') ? 'active' : '' }}">
                    <a href="{{ route('admin_welcome_notification') }}">Welcome Notification</a>
                </li>

                <li class="{{ (isset($page_title) && $page_title == 'Top Notification') ? 'active' : '' }}">
                    <a href="{{ route('admin_top_notification') }}">Top Notification</a>
                </li>
                <li class="{{ (isset($page_title) && $page_title == 'Buyer Home') ? 'active' : '' }}">
                    <a href="{{ route('admin_buyer_home') }}">Buyer Home</a>
                </li>
            </ul>
        </ul>
        <?php $menu_items = ['Page/Meta - Home', 'Page/Meta - schedule','Page/Meta - Tasks','Page/Meta - Show Schedule','Appointment','Page/Meta - lookbook','Page/Meta - About Us', 'Page/Meta - Contact Us', 'Page/Meta - Privacy Policy', 'Page/Meta - Return Policy','Page/Meta - Terms and Conditions',
                    'Page/Meta - Size Guide',
                    'Page/Meta - Faqs'] ?>
        <li data-toggle="accordion" data-target="#pages" class="accordion_heading {{ (isset($page_title) && in_array($page_title, $menu_items)) ? ' open_sec open_acc active' : '' }}" data-class="accordion">
            Pages
        </li>
        <ul class="sub_accordion default_accrodion {{ (isset($page_title) && in_array($page_title, $menu_items)) ? ' open' : '' }}" id="pages">
            <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Home') ? 'active' : '' }}">
                <a href="{{ route('admin_page_view', ['id' => PageEnumeration::$HOME]) }}">Home</a>
            </li>
            <li class="{{ (isset($page_title) && $page_title == 'Appointment') ? 'active' : '' }}">
                <a href="{{ route('appointments') }}">Appointment</a> 
            </li> 
            <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - About Us') ? 'active' : '' }}">
                <a href="{{ route('admin_page_view', ['id' => PageEnumeration::$ABOUT_US]) }}">About Us</a>
            </li>
            <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Contact Us') ? 'active' : '' }}">
                <a href="{{ route('admin_page_view', ['id' => PageEnumeration::$CONTACT_US]) }}">Contact Us</a>
            </li>
            <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Privacy Policy') ? 'active' : '' }}">
                <a href="{{ route('admin_page_view', ['id' => PageEnumeration::$PRIVACY_POLICY]) }}">Privacy Policy</a>
            </li>
            <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Return Policy') ? 'active' : '' }}">
                <a href="{{ route('admin_page_view', ['id' => PageEnumeration::$RETURN_INFO]) }}">Returns</a>
            </li>
            <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Terms and Conditions') ? 'active' : '' }}">
                <a href="{{ route('admin_page_view', ['id' => PageEnumeration::$TERMS_AND_CONDIOTIONS]) }}">Terms & Conditions</a>
            </li>
            <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - shipping') ? 'active' : '' }}">
                <a href="{{ route('admin_page_view', ['id' => PageEnumeration::$SHIPPING]) }}">Shipping</a>
            </li>
            <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Size Guide') ? 'active' : '' }}">
                <a href="{{ route('admin_page_view', ['id' => PageEnumeration::$SIZE_GUIDE]) }}">Size Guide</a>
            </li>  
            <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - lookbook') ? 'active' : '' }}">
                <a href="{{ route('schedule_page') }}">LookBook</a> 
            </li> 

            <?php $sub_menu_items = ['category landing','Page/Meta - Show Schedule','Page/Meta - Tasks'] ?>
            <li class="sub_child_accordion {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? ' active' : '' }}">
                <div data-toggle="accordion" data-target="#schedulePage" class="sub_child_accordion_open accordion_heading {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? ' open_sec' : '' }} " data-class="accordion"></div>
                <a data-toggle="accordion" data-target="#schedulePage">Show Schedule Page</a>
            </li>
            <ul class="sub_gchild_accordion default_accrodion {{ (isset($page_title) && in_array($page_title, $sub_menu_items)) ? 'open' : '' }}" id="schedulePage"> 
                <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Show Schedule') ? 'active' : '' }}"><a href="{{ route('admin_page_view', ['id' => PageEnumeration::$SHOW_SCHEDULE]) }}">Schedule Page</a></li> 
                <li class="{{ (isset($page_title) && $page_title == 'Page/Meta - Tasks') ? 'active' : '' }}"><a href="{{ route('tasks') }}">Schedule Tasks</a></li> 
            </ul> 
        </ul>
        <?php $menu_items = ['Item Statistics'] ?>
        <li data-toggle="accordion" data-target="#statistics" class="accordion_heading {{ (isset($page_title) && in_array($page_title, $menu_items)) ? ' open_sec open_acc active' : '' }}" data-class="accordion">
            STATISTICS
        </li>
        <ul class="sub_accordion default_accrodion {{ (isset($page_title) && in_array($page_title, $menu_items)) ? ' open' : '' }}" id="statistics">
            <li class="{{ (isset($page_title) && $page_title == 'Item Statistics') ? 'active' : '' }}">
                <a href="{{ route('item_statistics') }}">Item Statistics</a>
            </li>
        </ul> 
    </ul>
</div>
