<div class="header_menu_item">
    <div class="header_menu_item_inner">
        <div class="main_cat">
            <ul>
                <li><a href="{{ route('new_arrival_page') }}">NEW IN</a></li>

                @foreach($default_categories as $cat)
                    <li><a href="{{ route('category_page', ['category' => changeSpecialChar($cat['name'])]) }}">{{ $cat['name'] }}</a></li>

                    <?php
                        $subIds = [];

                        foreach ($cat['subCategories'] as $d_sub)
                            $subIds[] = $d_sub['id'];
                    ?>
                    @if (in_array(Request::route()->getName(), ['category_page', 'second_category']) && isset($category) && ($category->id == $cat['id'] || in_array($category->id, $subIds)))
                        <div class="sub_cat">
                            <ul>
                                @foreach($cat['subCategories'] as $d_sub)
                                    <li><a href="{{ route('second_category', ['category' => changeSpecialChar($d_sub['name']), 'parent' => changeSpecialChar($cat['name'])]) }}">{{ $d_sub['name'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</div>