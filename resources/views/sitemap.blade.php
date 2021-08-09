<?php echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>

<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ $frontUrl }}</loc>

        <lastmod>{{ $date }}</lastmod>

        <changefreq>monthly</changefreq>

        <priority>1.0</priority>
    </url>

    @foreach ($profiles as $profileId)
        <url>
            <loc>{{ $frontUrl }}/profiles_list/{{ $profileId }}</loc>
            <lastmod>{{ $date }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
</urlset>
