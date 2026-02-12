<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Primary Meta Tags -->
    <title>Photographer SB - Find Professional Photographers in Bangladesh | A Somogro Bangladesh Project</title>
    <meta name="title" content="Photographer SB - Find Professional Photographers in Bangladesh | A Somogro Bangladesh Project">
    <meta name="description" content="Bangladesh's premier photography marketplace by Somogro Bangladesh. Connect with 500+ verified professional photographers for weddings, events, portraits & more. Book now with secure payments.">
    <meta name="keywords" content="photographers in bangladesh, wedding photographer dhaka, professional photography, event photography, portrait photographer, photography competition, hire photographer bangladesh, somogro bangladesh">
    <meta name="author" content="Somogro Bangladesh">
    @if(strpos(request()->path(), 'admin') === 0)
    <meta name="robots" content="noindex, nofollow">
    @else
    <meta name="robots" content="index, follow">
    @endif
    @php
        $trackingSettings = cache()->remember('tracking_settings', 300, function () {
            return DB::table('settings')
                ->whereIn('key', [
                    'tracking.enable',
                    'tracking.ga4_measurement_id',
                    'tracking.gtm_id',
                    'tracking.fb_pixel_id',
                    'tracking.gsc_verification'
                ])
                ->pluck('value', 'key')
                ->toArray();
        });
        $trackingEnabled = filter_var($trackingSettings['tracking.enable'] ?? env('ANALYTICS_ENABLED', true), FILTER_VALIDATE_BOOLEAN);
        $ga4Id = $trackingSettings['tracking.ga4_measurement_id'] ?? env('GA4_MEASUREMENT_ID');
        $gtmId = $trackingSettings['tracking.gtm_id'] ?? env('GTM_ID');
        $fbPixelId = $trackingSettings['tracking.fb_pixel_id'] ?? env('FB_PIXEL_ID');
        $gscVerification = $trackingSettings['tracking.gsc_verification'] ?? env('GSC_VERIFICATION');
    @endphp
    <meta name="language" content="English">
    <meta name="revisit-after" content="7 days">
    @if(!empty($gscVerification))
    <meta name="google-site-verification" content="{{ $gscVerification }}">
    @endif
    
    <!-- Open Graph / Facebook Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://photographersb.com/">
    <meta property="og:title" content="Photographer SB - Professional Photographers Across Bangladesh">
    <meta property="og:description" content="A Somogro Bangladesh project connecting you with 500+ verified professional photographers for weddings, events, portraits & more.">
    <meta property="og:image" content="https://photographersb.com/images/og-image.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="Photographer SB - A Somogro Bangladesh Project">
    <meta property="og:locale" content="en_US">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://photographersb.com/">
    <meta name="twitter:title" content="Photographer SB - Find Professional Photographers in Bangladesh">
    <meta name="twitter:description" content="Bangladesh's premier photography marketplace. Connect with 500+ verified professional photographers.">
    <meta name="twitter:image" content="https://photographersb.com/images/twitter-card.jpg">
    <meta name="twitter:creator" content="@photographersb">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="https://photographersb.com/">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/images/Fev.png">
    <link rel="shortcut icon" type="image/png" href="/images/Fev.png">
    <link rel="apple-touch-icon" href="/images/Fev.png">
    
    <!-- Progressive Web App Manifest -->
    <link rel="manifest" href="/manifest.json">
    
    <!-- PWA Meta Tags -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Photographer SB">
    
    <!-- Theme Color -->
    <meta name="theme-color" content="#8E0E3F">
    <meta name="msapplication-TileColor" content="#8E0E3F">
    
    <!-- Dynamic SEO Meta Tags (Page-specific) -->
    @yield('meta')
    
    @if(config('app.env') !== 'production' && config('app.debug') === true)
    <!-- Anti-caching meta tags (DEV MODE ONLY) -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    @endif
    
    <!-- Preconnect for Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Photographer SB",
        "alternateName": "Photographers Across Bangladesh - A Somogro Bangladesh Project",
        "url": "https://photographersb.com",
        "description": "Bangladesh's premier photography marketplace by Somogro Bangladesh, connecting professional photographers with clients nationwide",
        "creator": {
            "@type": "Organization",
            "name": "Somogro Bangladesh",
            "url": "https://somogrobangladesh.com"
        },
        "potentialAction": {
            "@type": "SearchAction",
            "target": "https://photographersb.com/?search={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>

    @if($trackingEnabled && !empty($gtmId))
    <!-- Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ $gtmId }}');
    </script>
    <!-- End Google Tag Manager -->
    @endif

    @if($trackingEnabled && !empty($ga4Id))
    <!-- Google Analytics 4 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $ga4Id }}');
    </script>
    @endif

    @if($trackingEnabled && !empty($fbPixelId))
    <!-- Meta Pixel -->
    <script>
        !(function(f,b,e,v,n,t,s){
            if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)
        })(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $fbPixelId }}');
        fbq('track', 'PageView');
    </script>
    @endif
    
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Photographer SB",
        "alternateName": "A Somogro Bangladesh Project",
        "url": "https://photographersb.com",
        "logo": "https://photographersb.com/images/logo.svg",
        "description": "Bangladesh's premier photography marketplace, a project by Somogro Bangladesh",
        "parentOrganization": {
            "@type": "Organization",
            "name": "Somogro Bangladesh",
            "url": "https://somogrobangladesh.com"
        },
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Dhaka",
            "addressCountry": "Bangladesh"
        },
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+880-1XXXXXXXXX",
            "contactType": "Customer Service",
            "email": "support@photographersb.com",
            "availableLanguage": ["English", "Bengali"]
        },
        "sameAs": [
            "https://www.facebook.com/photographersb",
            "https://twitter.com/photographersb",
            "https://www.instagram.com/photographersb",
            "https://t.me/photographersb"
        ]
    }
    </script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @if($trackingEnabled && !empty($gtmId))
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    @endif

    @if($trackingEnabled && !empty($fbPixelId))
    <!-- Meta Pixel (noscript) -->
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ $fbPixelId }}&ev=PageView&noscript=1"/></noscript>
    @endif

    <div id="app"></div>

    <!-- Service Worker Registration -->
    <script>
        // Register service worker for offline functionality and PWA features
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(registration => {
                        console.log('Service Worker registered:', registration);
                    })
                    .catch(error => {
                        console.log('Service Worker registration failed:', error);
                    });
            });
        }
    </script>
</body>
</html>
