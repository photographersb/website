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
            <meta property="og:type" content="profile">
            <meta property="og:title" content="{{ $seoMeta->meta_title }}">
            <meta property="og:description" content="{{ $seoMeta->meta_description }}">
            <meta property="og:url" content="{{ $seoMeta->canonical_url }}">
            @if($seoMeta->og_image)
                <meta property="og:image" content="{{ $seoMeta->og_image }}">
                <meta property="og:image:alt" content="{{ $seoMeta->meta_title }}">
            @endif
            <meta property="og:site_name" content="Photographer SB">
            
            <!-- Twitter Card Tags -->
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" content="{{ $seoMeta->meta_title }}">
            <meta name="twitter:description" content="{{ $seoMeta->meta_description }}">
            @if($seoMeta->og_image)
                <meta name="twitter:image" content="{{ $seoMeta->og_image }}">
            @endif
            
            <!-- Robots Meta -->
            <meta name="robots" content="{{ ($seoMeta->robots_index ?? true) ? 'index' : 'noindex' }}, {{ ($seoMeta->robots_follow ?? true) ? 'follow' : 'nofollow' }}">
            
            <!-- Schema.org JSON-LD -->
            @if($seoMeta->schema_json)
                <script type="application/ld+json">
                    {!! json_encode($seoMeta->schema_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}
                </script>
            @endif
        @endif
    @show
    
    <!-- Primary Meta Tags (defaults) -->
    <meta name="title" content="Photographer SB - Find Professional Photographers in Bangladesh">
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
