<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($categories as $category)
        <url>
            <loc>{{ route('vendor_or_parent_category', ['text' => $category->slug]) }}</loc>
            <lastmod>{{ $category->updated_at->tz('UTC')->toAtomString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.6</priority>
        </url>
        
        <url>
            <loc>{{ route('category_page', ['category' => $category['slug']]) }}</loc>
            <lastmod>{{ $category->updated_at->tz('UTC')->toAtomString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.6</priority>
        </url>

        @if (sizeof($category->subCategories) > 0)
            @foreach ($category->subCategories as $sub)
                <url>
                    <loc>{{ route('second_category', ['category' => $sub->slug, 'parent' => $category->slug]) }}</loc>
                    <lastmod>{{ $sub->updated_at->tz('UTC')->toAtomString() }}</lastmod>
                    <changefreq>monthly</changefreq>
                    <priority>0.6</priority>
                </url>

                @if (sizeof($sub->thirdcategory) > 0)
                    @foreach ($sub->thirdcategory as $sub2)
                        <url>
                            <loc>{{ route('third_category', ['category' => $sub['slug'], 'parent' => $category['slug'],'subcategory'=> $sub2->slug]) }}</loc>
                            <lastmod>{{ $sub2->updated_at->tz('UTC')->toAtomString() }}</lastmod>
                            <changefreq>monthly</changefreq>
                            <priority>0.6</priority>
                        </url>
                    @endforeach
                @endif
            @endforeach
        @endif
    @endforeach
</urlset>