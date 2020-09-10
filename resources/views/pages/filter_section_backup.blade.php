<div  class="product_filter clearfix filter_desktop">
            <div id="filter" class="filter_by">
                <div class="filter_heading">
                    <ul>
                        <li data-toggle="collapse" data-target="#SORT" class="collapsed">
                            SORT BY
                        </li>
                        <li data-toggle="collapse" data-target="#FILTER" class="collapsed">
                            FILTER BY
                        </li>
                        <li data-toggle="collapse" data-target="#CATEGORY" class="collapsed">
                            CATEGORY
                        </li>
                        <li data-toggle="collapse" data-target="#COLOR" class="collapsed">
                            COLOR
                        </li>
                        <li data-toggle="collapse" data-target="#SIZE" class="collapsed">
                            SIZE
                        </li>
                    </ul>
                </div>
                <div class="filter_content">
                    <div class="collapse filter_submenu clearfix" id="SORT" data-parent="#filter">
                        <div class="close_filter" data-toggle="collapse" data-target="#SORT">
                            <span></span>
                            <span></span>
                        </div>
                        <div class="filter_content_col">
                            <h2>SORT BY</h2>
                            <ul>
                                <input type="hidden" id="sorting" value="">
                                <li class="sorting" data-type="1">Newest to Oldest</li>
                                <li class="sorting" data-type="2">Lowest to Highest Price</li>
                                <li class="sorting" data-type="3">Highest to Lowest Price</li>
                                <li class="sorting" data-type="4">Style </li>
                            </ul>
                        </div>
    
                    </div>
                    <div class="collapse filter_submenu clearfix" id="FILTER" data-parent="#filter">
                        <div class="close_filter" data-toggle="collapse" data-target="#FILTER">
                            <span></span>
                            <span></span>
                        </div>
                        <div class="filter_content_col">
                            <h2>CATEGORY</h2>
                            <ul>
                                @foreach($categories as $Single_category)
                                    <li><a href="#" id="{{$Single_category->slug}}" class="item-category" data-id="{{$Single_category->id}}">{{$Single_category->name}}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="filter_content_col">
                            <h2>COLOR</h2>
                            <ul>
                                @php
                                    $totalcolor = count($masterColors);
                                @endphp
                                @foreach($masterColors as $color)
                                    <li><a href="#" id="{{$color->name}}" class="item-color" data-id="{{$color->id}}">{{$color->name}}</a></li>
                                @endforeach
                            </ul>
    
                        </div>
                        <div class="filter_content_col">
                            <h2>SIZE</h2>
                            <ul>
                                @foreach($packs as $pack)
                                    <li><a href="#" id="{{$pack->name}}" class="packid" data-id="{{$pack->id}}">{{$pack->name}}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="collapse filter_submenu clearfix" id="CATEGORY" data-parent="#filter">
                        <div class="close_filter" data-toggle="collapse" data-target="#CATEGORY">
                            <span></span>
                            <span></span>
                        </div>
                        <div class="filter_content_col">
                            <h2>CATEGORY</h2>
                            <ul>
                                @foreach($categories as $Single_category)
                                    <li><a href="#" id="{{$Single_category->slug}}" class="item-category" data-id="{{$Single_category->id}}">{{$Single_category->name}}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="collapse filter_submenu clearfix" id="COLOR" data-parent="#filter">
                        <div class="close_filter" data-toggle="collapse" data-target="#COLOR">
                            <span></span>
                            <span></span>
                        </div>
                        <div class="filter_content_col">
                            <h2>COLOR</h2>
                            <ul>
                                @foreach($masterColors as $color)
                                    <li><a href="#" id="{{$color->name}}" class="item-color" data-id="{{$color->id}}">{{$color->name}}</a></li>
                                @endforeach
                            </ul>
    
                        </div>
                    </div>
                    <div class="collapse filter_submenu clearfix" id="SIZE" data-parent="#filter">
                        <div class="close_filter" data-toggle="collapse" data-target="#SIZE">
                            <span></span>
                            <span></span>
                        </div>
                        <div class="filter_content_col">
                            <h2>SIZE</h2>
                            <ul>
                                @foreach($packs as $pack)
                                    <li><a href="#" id="{{$pack->name}}" class="packid" data-id="{{$pack->id}}">{{$pack->name}}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="product_grid">
                <ul>
                    <li>
                        <svg id="grid" viewBox="0 0 26 26">
                            <path class="st0" d="M0,0v11.4h11.4V0H0z M9.7,9.7h-8v-8h8V9.7z"></path>
                            <path class="st0" d="M14.6,0v11.4H26V0H14.6z M24.3,9.7h-8v-8h8V9.7z"></path>
                            <path class="st0" d="M0,14.6V26h11.4V14.6H0z M9.7,24.3h-8v-8h8V24.3z"></path>
                            <path class="st0" d="M14.6,14.6V26H26V14.6H14.6z M24.3,24.3h-8v-8h8V24.3z"></path>
                        </svg>
                    </li>
                    <li>
                        <svg id="two-col" viewBox="0 0 26 26">
                            <path class="st0" d="M0,0v26h11.4V0H0z M9.8,24.3H1.7v-6.6v-8v-8h8h0.1V24.3z"></path>
                            <path class="st0" d="M14.6,0v26H26V0H14.6z M24.4,24.3h-8.1v-6.6v-8v-8h8h0.1V24.3z"></path>
                        </svg>
                    </li>
                </ul>
            </div>
        </div>
        <div  class="product_filter clearfix filter_moblie">
            <div id="sfm" class="filter_by">
                <div class="filter_heading">
                    <ul>
                        <li data-toggle="collapse" data-target="#SORTmb" class="collapsed">
                            SORT BY
                        </li>
                        <li data-toggle="collapse" data-target="#FILTERmb" class="collapsed">
                            FILTER BY
                        </li>
                    </ul>
                </div>
                <div class="collapse filter_submenu clearfix" id="SORTmb" data-parent="#sfm">
                    <div class="filter_content_col">
                        <ul>
                            <input type="hidden" id="sorting" value="">
                            <li class="sorting" data-type="1" data-name="Newest to Oldest">Newest to Oldest</li>
                            <li class="sorting" data-type="2" data-name="Lowest to Highest Price">Lowest to Highest Price</li>
                            <li class="sorting" data-type="3" data-name="Highest to Lowest Price">Highest to Lowest Price</li>
                            <li class="sorting" data-type="4" data-name="Style">Style </li>
                        </ul>
                    </div>
                </div>
                <div id="FILTERmb" class="collapse filter_submenu " data-parent="#sfm">
                    <div id="csc" class="filter_child_wrapper">
                        <div data-toggle="collapse" data-target="#CATEGORYmb" class="collapsed filter_child" >
                            CATEGORY
                        </div>
                        <div class="collapse" id="CATEGORYmb" data-parent="#csc">
                            <div class="filter_content_col_mb">
                                <ul>
                                    @foreach($categories as $Single_category)
                                        <li><a href="#" id="{{$Single_category->slug}}" class="item-category" data-id="{{$Single_category->id}}">{{$Single_category->name}}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div data-toggle="collapse" data-target="#COLORmb" class="collapsed filter_child" >
                            COLOR
                        </div>
                        <div class="collapse" id="COLORmb" data-parent="#csc">
                            <div class="filter_content_col_mb">
                                <ul>
                                    @foreach($masterColors as $color)
                                        <li><a href="#" id="{{$color->name}}" class="item-color" data-id="{{$color->id}}">{{$color->name}}</a></li>
                                    @endforeach
                                </ul>
    
                            </div>
                        </div>
                        <div data-toggle="collapse" data-target="#SIZEmb" class="collapsed filter_child" >
                            SIZE
                        </div>
                        <div class="collapse" id="SIZEmb" data-parent="#csc">
                            <div class="filter_content_col_mb">
                                <ul>
                                    @foreach($packs as $pack)
                                        <li><a href="#" id="{{$pack->name}}" class="packid" data-id="{{$pack->id}}">{{$pack->name}}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>