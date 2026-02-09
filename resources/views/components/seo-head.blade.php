@props(['seoMeta'])

@php
    $title = $seoMeta->meta_title ?? config('app.name');
    $description = $seoMeta->meta_description ?? 'Photographer marketplace for Bangladesh';
    $canonical = $seoMeta->canonical_url ?? request()->url();
    $ogTitle = $seoMeta->og_title ?? $title;
    $ogDescription = $seoMeta->og_description ?? $description;
    $ogImage = $seoMeta->og_image ?? asset('images/og-default.jpg');
    $ogUrl = $seoMeta->og_url ?? request()->url();
    $robotsIndex = $seoMeta->robots_index !== false ? 'index' : 'noindex';
    $robotsFollow = $seoMeta->robots_follow !== false ? 'follow' : 'nofollow';
@endphp

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">

<!-- Primary Meta Tags -->
<title>{{ $title }}</title>
<meta name="title" content="{{ $title }}">
<meta name="description" content="{{ $description }}">
<meta name="robots" content="{{ $robotsIndex }}, {{ $robotsFollow }}">
<link rel="canonical" href="{{ $canonical }}">

<!-- Open Graph Tags -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ $ogUrl }}">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:site_name" content="Photographer SB">
<meta property="og:locale" content="en_BD">

<!-- Twitter Tags -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ $ogUrl }}">
<meta property="twitter:title" content="{{ $ogTitle }}">
<meta property="twitter:description" content="{{ $ogDescription }}">
<meta property="twitter:image" content="{{ $ogImage }}">

<!-- WhatsApp Share Preview -->
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">

@if(isset($seoMeta->schema_json))
<script type="application/ld+json">
{!! $seoMeta->schema_json !!}
</script>
@endif
