@props(['meta'])

@if($meta)
    {{-- Essential Meta Tags --}}
    <meta name="description" content="{{ $meta->meta_description ?? '' }}">
    @if($meta->meta_keywords)
        <meta name="keywords" content="{{ $meta->meta_keywords }}">
    @endif
    
    {{-- Canonical URL --}}
    @if($meta->canonical_url)
        <link rel="canonical" href="{{ $meta->canonical_url }}">
    @endif
    
    {{-- Robots Meta --}}
    <meta name="robots" content="{{ $meta->getRobotsMetaTag() }}">
    
    {{-- Open Graph Tags --}}
    <meta property="og:title" content="{{ $meta->og_title ?? $meta->meta_title ?? config('app.name') }}">
    <meta property="og:description" content="{{ $meta->og_description ?? $meta->meta_description ?? '' }}">
    <meta property="og:url" content="{{ $meta->canonical_url ?? url()->current() }}">
    @if($meta->og_image)
        <meta property="og:image" content="{{ $meta->og_image }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    
    {{-- Twitter Card Tags --}}
    @if($meta->twitter_card)
        <meta name="twitter:card" content="{{ $meta->twitter_card }}">
        <meta name="twitter:title" content="{{ $meta->twitter_title ?? $meta->meta_title ?? config('app.name') }}">
        <meta name="twitter:description" content="{{ $meta->twitter_description ?? $meta->meta_description ?? '' }}">
        @if($meta->twitter_image)
            <meta name="twitter:image" content="{{ $meta->twitter_image }}">
        @endif
    @endif
    
    {{-- Schema.org JSON-LD --}}
    @if($meta->schema_json)
        <script type="application/ld+json">
            {!! json_encode($meta->schema_json) !!}
        </script>
    @endif
@else
    {{-- Default Meta Tags --}}
    <meta name="description" content="{{ config('app.description', 'Photographer SB - Connect with Professional Photographers') }}">
    <meta name="robots" content="index, follow">
@endif
