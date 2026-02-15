<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="color-scheme" content="light">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @php
        $trackingSettings = cache()->remember('tracking_settings', 300, function () {
            return DB::table('settings')
                ->whereIn('key', [
                    'tracking.fb_domain_verification'
                ])
                ->pluck('value', 'key')
                ->toArray();
        });
        $fbDomainVerification = $trackingSettings['tracking.fb_domain_verification'] ?? env('FB_DOMAIN_VERIFICATION');
    @endphp
    
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
            <meta property="og:image" content="{{ $seoMeta->og_image ?: 'https://photographersb.com/images/PhotographerSB-OG.jpg' }}">
            <meta property="og:image:alt" content="{{ $seoMeta->meta_title }}">
            <meta property="og:site_name" content="PhotographerSB">
            
            <!-- Twitter Card Tags -->
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:url" content="{{ $seoMeta->canonical_url }}">
            <meta name="twitter:title" content="{{ $seoMeta->meta_title }}">
            <meta name="twitter:description" content="{{ $seoMeta->meta_description }}">
            <meta name="twitter:image" content="{{ $seoMeta->og_image ?: 'https://photographersb.com/images/PhotographerSB-OG.jpg' }}">
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
            <meta property="og:image" content="https://photographersb.com/images/PhotographerSB-OG.jpg">
            <meta property="og:image:alt" content="PhotographerSB">
            <meta property="og:site_name" content="PhotographerSB">
            
            <!-- Twitter Card Tags -->
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:url" content="https://photographersb.com/">
            <meta name="twitter:title" content="PhotographerSB | Book Professional Photographers in Bangladesh">
            <meta name="twitter:description" content="Explore portfolios and hire trusted photographers for weddings, events, corporate, and commercial projects across Bangladesh.">
            <meta name="twitter:image" content="https://photographersb.com/images/PhotographerSB-OG.jpg">
            <meta name="twitter:creator" content="@photographersb">
        @endif
    @show

    @if(!empty($fbDomainVerification))
    <meta name="facebook-domain-verification" content="{{ $fbDomainVerification }}">
    @endif
    
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
    
    <!-- Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-T3BW6WBM');
    </script>
    <!-- End Google Tag Manager -->

    <!-- Google Analytics 4 with Consent Management -->
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        
        // Set default consent to 'denied' for all categories
        gtag('consent', 'default', {
            'ad_user_data': 'denied',
            'ad_personalization': 'denied',
            'ad_storage': 'denied',
            'analytics_storage': 'denied',
            'wait_for_update': 500,
        });
        
        gtag('js', new Date());
        gtag('config', 'G-PYWLWNZR5K', {
            'anonymize_ip': true,
        });
    </script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-PYWLWNZR5K"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T3BW6WBM"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <div id="app">
        @yield('content')
    </div>
    
    <!-- Cookie Consent Banner Component -->
    <cookie-consent-banner></cookie-consent-banner>
</body>
</html>
