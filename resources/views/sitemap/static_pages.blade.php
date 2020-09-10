<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('about_us') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1</priority>
    </url>

    <url>
        <loc>{{ route('contact_us') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1</priority>
    </url>
    
    <url>
        <loc>{{ route('look_book') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1</priority>
    </url>

    <url>
        <loc>{{ route('show_schedule') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1</priority>
    </url>

    <url>
        <loc>{{ route('return_info') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1</priority>
    </url>
 

    <url>
        <loc>{{ route('shipping') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1</priority>
    </url>

    <url>
        <loc>{{ route('terms_conditions') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1</priority>
    </url>

</urlset>