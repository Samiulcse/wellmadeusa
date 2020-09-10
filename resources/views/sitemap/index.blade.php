<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?> 
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    <sitemap>
        <loc>{{ route('sitemap_static') }}</loc>
    </sitemap>

    <sitemap>
        <loc>{{ route('vendor_or_parent_category') }}</loc>
        @if ($categoriesModify != null)
            <lastmod>{{ $categoriesModify->updated_at->tz('UTC')->toAtomString() }}</lastmod>
        @endif
    </sitemap>
    <sitemap>
        <loc>{{ route('sitemap_items') }}</loc>
    </sitemap>
</sitemapindex>