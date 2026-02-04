{{-- 
    Enhanced SEO Component - Photographer SB
    
    Usage:
    <x-seo 
        title="Page Title"
        description="Page description"
        :canonical="url('/path')"
        :image="asset('image.jpg')"
        type="website"
        :schema="$schemaArray"
    />
    
    Or pass full meta object:
    <x-seo :meta="$seoMeta" />
--}}

@props([
    'meta' => null,
    'title' => null,
    'description' => null,
    'canonical' => null,
    'image' => null,
    'type' => 'website',
    'schema' => null,
    'noindex' => false,
])

@php
    // If meta object provided, extract values
    if ($meta) {
        $title = $meta->meta_title ?? $title;
        $description = $meta->meta_description ?? $description;
        $canonical = $meta->canonical_url ?? $canonical;
        $image = $meta->og_image ?? $image;
        $type = $meta->og_type ?? $type;
        $schema = $meta->schema_json ?? $schema;
        $noindex = isset($meta->robots_index) ? !$meta->robots_index : $noindex;
    }
    
    // Fallbacks
    $title = $title ?? config('app.name');
    $description = $description ?? 'Photographer SB - Bangladesh\'s Premier Photography Marketplace';
    $canonical = $canonical ?? url()->current();
    $image = $image ?? asset('images/og-default.jpg');
    
    // Ensure image is absolute URL
    if ($image && !str_starts_with($image, 'http')) {
        $image = url($image);
    }
@endphp

{{-- Page Title --}}
<title>{{ $title }}</title>

{{-- Essential Meta Tags --}}
<meta name="description" content="{{ $description }}">

{{-- Canonical URL --}}
<link rel="canonical" href="{{ $canonical }}">

{{-- Robots Meta --}}
<meta name="robots" content="{{ $noindex ? 'noindex, nofollow' : 'index, follow' }}">

{{-- Open Graph Tags --}}
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:locale" content="en_US">

@if($image)
    <meta property="og:image" content="{{ $image }}">
    <meta property="og:image:secure_url" content="{{ $image }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $title }}">
@endif

{{-- Twitter Card Tags --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@thephotographersbd">
<meta name="twitter:creator" content="@thephotographersbd">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
@if($image)
    <meta name="twitter:image" content="{{ $image }}">
    <meta name="twitter:image:alt" content="{{ $title }}">
@endif

{{-- Schema.org JSON-LD --}}
@if($schema)
    <script type="application/ld+json">
        {!! json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endif

{{-- Additional Meta for Better Indexing --}}
<meta name="author" content="Photographer SB">
<meta name="publisher" content="Photographer SB">
<meta name="application-name" content="Photographer SB">
<meta name="apple-mobile-web-app-title" content="Photographer SB">

{{-- Geo Tags (for Bangladesh-wide service) --}}
<meta name="geo.region" content="BD">
<meta name="geo.placename" content="Bangladesh">

{{-- Additional OpenGraph Tags --}}
<meta property="og:locale:alternate" content="bn_BD">
