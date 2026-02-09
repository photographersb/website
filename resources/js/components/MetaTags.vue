<template>
  <!-- Meta tags are managed via document.title and meta elements -->
</template>

<script>
import { watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';

export default {
  name: 'MetaTags',
  setup() {
    const route = useRoute();

    const updateMetaTags = (meta) => {
      // Update title
      document.title = meta.title || 'Photographer SB - Find Perfect Photographers in Bangladesh';

      // Update or create meta tags
      updateMetaTag('description', meta.description || 'Discover verified professional photographers in Bangladesh. Book wedding photographers, event photography, portrait sessions and more. Trusted by thousands of clients.');
      updateMetaTag('keywords', meta.keywords || 'photographer bangladesh, wedding photographer dhaka, event photography, portrait photographer, professional photography');
      updateMetaTag('robots', meta.robots || 'index, follow');
      
      // Open Graph tags
      updateMetaTag('og:title', meta.title || 'Photographer SB - Find Perfect Photographers in Bangladesh', 'property');
      updateMetaTag('og:description', meta.description || 'Discover verified professional photographers in Bangladesh.', 'property');
      updateMetaTag('og:image', meta.image || `${window.location.origin}/images/og-default.svg`, 'property');
      updateMetaTag('og:url', window.location.href, 'property');
      updateMetaTag('og:type', meta.type || 'website', 'property');
      updateMetaTag('og:site_name', 'Photographer SB', 'property');
      
      // Twitter Card tags
      updateMetaTag('twitter:card', 'summary_large_image', 'name');
      updateMetaTag('twitter:title', meta.title || 'Photographer SB', 'name');
      updateMetaTag('twitter:description', meta.description || 'Find verified photographers in Bangladesh', 'name');
      updateMetaTag('twitter:image', meta.image || `${window.location.origin}/images/og-default.svg`, 'name');
      
      // Canonical URL
      updateLinkTag('canonical', meta.canonical || window.location.href);

      // Structured Data (JSON-LD)
      if (meta.structuredData) {
        updateStructuredData(meta.structuredData);
      }
    };

    const updateMetaTag = (name, content, attribute = 'name') => {
      let element = document.querySelector(`meta[${attribute}="${name}"]`);
      if (!element) {
        element = document.createElement('meta');
        element.setAttribute(attribute, name);
        document.head.appendChild(element);
      }
      element.setAttribute('content', content);
    };

    const updateLinkTag = (rel, href) => {
      let element = document.querySelector(`link[rel="${rel}"]`);
      if (!element) {
        element = document.createElement('link');
        element.setAttribute('rel', rel);
        document.head.appendChild(element);
      }
      element.setAttribute('href', href);
    };

    const updateStructuredData = (data) => {
      let script = document.getElementById('structured-data');
      if (!script) {
        script = document.createElement('script');
        script.id = 'structured-data';
        script.type = 'application/ld+json';
        document.head.appendChild(script);
      }
      script.textContent = JSON.stringify(data);
    };

    const toTitleCase = (value = '') => {
      return value
        .replace(/[-_]+/g, ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase());
    };

    const getRouteMetaTags = () => {
      const routeName = route.name;
      const params = route.params;
      const query = route.query;
      const origin = window.location.origin;
      const currentUrl = `${origin}${route.fullPath}`;

      const metaMap = {
        'home': {
          title: 'Photographer SB - Find Perfect Photographers in Bangladesh',
          description: 'Discover verified professional photographers in Bangladesh. Book wedding photographers, event photography, portrait sessions. Trusted platform with secure payments.',
          keywords: 'photographer bangladesh, wedding photographer dhaka, event photography, portrait photographer',
          structuredData: {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "name": "Photographer SB",
            "url": "https://photographersb.com",
            "potentialAction": {
              "@type": "SearchAction",
              "target": "https://photographersb.com/search?q={search_term_string}",
              "query-input": "required name=search_term_string"
            }
          }
        },
        'photographer-profile': {
          title: `${params.slug} - Professional Photographer | Photographer SB`,
          description: `View portfolio, packages, and reviews for ${params.slug}. Book verified professional photographer in Bangladesh.`,
          type: 'profile'
        },
        'photographers-by-category': {
          title: query.category
            ? `${toTitleCase(query.category)} Photographers in Bangladesh | Photographer SB`
            : 'Photographers by Category | Photographer SB',
          description: query.category
            ? `Browse verified ${toTitleCase(query.category)} photographers across Bangladesh. Compare portfolios, reviews, and pricing.`
            : 'Browse photographers by category. Discover wedding, portrait, event, and commercial photography specialists in Bangladesh.',
          image: `${origin}/images/og-categories.svg`,
          canonical: currentUrl,
          structuredData: {
            "@context": "https://schema.org",
            "@type": "WebPage",
            "name": query.category
              ? `${toTitleCase(query.category)} Photographers`
              : 'Photographers by Category',
            "url": currentUrl,
            "breadcrumb": {
              "@type": "BreadcrumbList",
              "itemListElement": [
                { "@type": "ListItem", "position": 1, "name": "Home", "item": origin },
                { "@type": "ListItem", "position": 2, "name": "Categories", "item": `${origin}/categories` },
                { "@type": "ListItem", "position": 3, "name": query.category ? toTitleCase(query.category) : 'Browse', "item": currentUrl }
              ]
            }
          }
        },
        'photographers-by-location': {
          title: query.city
            ? `${toTitleCase(query.city)} Photographers | Photographer SB`
            : 'Photographers by Location | Photographer SB',
          description: query.city
            ? `Find verified photographers in ${toTitleCase(query.city)}. Compare portfolios, reviews, and pricing.`
            : 'Browse photographers by location across Bangladesh. Discover local professionals near you.',
          image: `${origin}/images/og-locations.svg`,
          canonical: currentUrl,
          structuredData: {
            "@context": "https://schema.org",
            "@type": "WebPage",
            "name": query.city ? `${toTitleCase(query.city)} Photographers` : 'Photographers by Location',
            "url": currentUrl,
            "breadcrumb": {
              "@type": "BreadcrumbList",
              "itemListElement": [
                { "@type": "ListItem", "position": 1, "name": "Home", "item": origin },
                { "@type": "ListItem", "position": 2, "name": "Locations", "item": `${origin}/locations` },
                { "@type": "ListItem", "position": 3, "name": query.city ? toTitleCase(query.city) : 'Browse', "item": currentUrl }
              ]
            }
          }
        },
        'categories-landing': {
          title: 'Photography Categories in Bangladesh | Photographer SB',
          description: 'Browse photography categories to find the right specialist. Wedding, portrait, event, product, and more.',
          image: `${origin}/images/og-categories.svg`,
          canonical: currentUrl,
          structuredData: {
            "@context": "https://schema.org",
            "@type": "CollectionPage",
            "name": "Photography Categories",
            "url": currentUrl
          }
        },
        'locations-landing': {
          title: 'Photographers by City in Bangladesh | Photographer SB',
          description: 'Explore photographers by city across Bangladesh. Find professionals near you.',
          image: `${origin}/images/og-locations.svg`,
          canonical: currentUrl,
          structuredData: {
            "@context": "https://schema.org",
            "@type": "CollectionPage",
            "name": "Photography Locations",
            "url": currentUrl
          }
        },
        'events': {
          title: 'Photography Events in Bangladesh | Photographer SB',
          description: 'Discover photography workshops, exhibitions, networking events in Bangladesh. Register for photography events and competitions.',
          keywords: 'photography events bangladesh, photography workshop dhaka, photo exhibition'
        },
        'competitions': {
          title: 'Photography Competitions Bangladesh | Photographer SB',
          description: 'Join photography competitions in Bangladesh. Submit your best photos, compete with talented photographers, and win exciting prizes.',
          keywords: 'photography competition bangladesh, photo contest, photography awards'
        },
        'admin-competitions-index': {
          title: 'Manage Competitions | Admin | Photographer SB',
          description: 'Admin panel for managing photography competitions.',
          robots: 'noindex, nofollow'
        },
        'login': {
          title: 'Login | Photographer SB',
          description: 'Sign in to your Photographer SB account. Access your dashboard and manage your photography business.',
          robots: 'noindex, nofollow',
          canonical: currentUrl
        },
        'admin-login': {
          title: 'Admin Login | Photographer SB',
          description: 'Admin sign in to Photographer SB management panel.',
          robots: 'noindex, nofollow',
          canonical: currentUrl
        },
        'admin-dashboard': {
          title: 'Admin Dashboard | Photographer SB',
          description: 'Manage photographers, events, competitions, users, bookings, and platform analytics.',
          robots: 'noindex, nofollow',
          canonical: currentUrl
        },
        'verification-center': {
          title: 'Verification Center | Photographer SB',
          description: 'Manage your photographer verification documents and credentials.',
          robots: 'noindex, nofollow',
          canonical: currentUrl
        },
        'auth': {
          title: 'Login / Register | Photographar SB',
          description: 'Sign in to your account or create a new one. Join Bangladesh\'s leading photography marketplace.',
          robots: 'noindex, nofollow',
          canonical: currentUrl
        }
      };

      return metaMap[routeName] || {
        title: 'Photographar SB',
        description: 'Find professional photographers in Bangladesh'
      };
    };

    // Watch for route changes
    watch(() => route.fullPath, () => {
      const meta = getRouteMetaTags();
      updateMetaTags(meta);
    }, { immediate: true });

    onMounted(() => {
      const meta = getRouteMetaTags();
      updateMetaTags(meta);
    });

    return {};
  }
};
</script>
