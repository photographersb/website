<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    @section('meta')
        @if($seoMeta)
            <!-- Title -->
            <title>{{ $seoMeta->meta_title }}</title>
            
            <!-- Meta Description -->
            <meta name="description" content="{{ $seoMeta->meta_description }}">
            
            <!-- Canonical URL -->
            <link rel="canonical" href="{{ $seoMeta->canonical_url }}">
            
            <!-- OpenGraph Tags -->
            <meta property="og:type" content="website">
            <meta property="og:title" content="{{ $seoMeta->meta_title }}">
            <meta property="og:description" content="{{ $seoMeta->meta_description }}">
            <meta property="og:url" content="{{ $seoMeta->canonical_url }}">
            <meta property="og:image" content="{{ $seoMeta->og_image ?: 'https://photographersb.com/images/og-cover.jpg' }}">
            <meta property="og:image:alt" content="{{ $seoMeta->meta_title }}">
            <meta property="og:site_name" content="PhotographerSB">
            
            <!-- Twitter Card Tags -->
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:url" content="{{ $seoMeta->canonical_url }}">
            <meta name="twitter:title" content="{{ $seoMeta->meta_title }}">
            <meta name="twitter:description" content="{{ $seoMeta->meta_description }}">
            <meta name="twitter:image" content="{{ $seoMeta->og_image ?: 'https://photographersb.com/images/twitter-card.jpg' }}">
            <meta name="twitter:creator" content="@photographersb">
            
            <!-- Robots Meta -->
            <meta name="robots" content="{{ ($seoMeta->robots_index ?? true) ? 'index' : 'noindex' }}, {{ ($seoMeta->robots_follow ?? true) ? 'follow' : 'nofollow' }}">
            
            <!-- Schema.org JSON-LD -->
            @if($seoMeta->schema_json)
                <script type="application/ld+json">
                    {!! json_encode($seoMeta->schema_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}
                </script>
            @endif
        @else
            <!-- Title -->
            <title>PhotographerSB | Professional Photographers & Booking Platform in Bangladesh</title>
            
            <!-- Meta Description -->
            <meta name="description" content="Discover and book verified professional photographers across Bangladesh for weddings, events, corporate, commercial, and portrait photography.">
            
            <!-- Canonical URL -->
            <link rel="canonical" href="https://photographersb.com/">
            
            <!-- OpenGraph Tags -->
            <meta property="og:type" content="website">
            <meta property="og:title" content="PhotographerSB | Professional Photographers & Booking Platform in Bangladesh">
            <meta property="og:description" content="Discover and book verified professional photographers across Bangladesh for weddings, events, corporate, commercial, and portrait photography.">
            <meta property="og:url" content="https://photographersb.com/">
            <meta property="og:image" content="https://photographersb.com/images/og-cover.jpg">
            <meta property="og:image:alt" content="PhotographerSB">
            <meta property="og:site_name" content="PhotographerSB">
            
            <!-- Twitter Card Tags -->
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:url" content="https://photographersb.com/">
            <meta name="twitter:title" content="PhotographerSB | Book Professional Photographers in Bangladesh">
            <meta name="twitter:description" content="Explore portfolios and hire trusted photographers for weddings, events, corporate, and commercial projects across Bangladesh.">
            <meta name="twitter:image" content="https://photographersb.com/images/twitter-card.jpg">
            <meta name="twitter:creator" content="@photographersb">
        @endif
    @show
    
    <!-- Primary Meta Tags (defaults) -->
    <meta name="title" content="PhotographerSB - Find Professional Photographers in Bangladesh">
    <meta name="keywords" content="photographers in bangladesh, wedding photographer, professional photography, event photography">
    <meta name="author" content="Somogro Bangladesh">
    <meta name="robots" content="index, follow">
    <meta name="language" content="English">
    <meta name="theme-color" content="#8E0E3F">
    
    <!-- Preconnect for Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        @yield('content')
    </div>
</body>
</html>
