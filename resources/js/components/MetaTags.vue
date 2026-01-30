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
      document.title = meta.title || 'Photographar SB - Find Perfect Photographers in Bangladesh';

      // Update or create meta tags
      updateMetaTag('description', meta.description || 'Discover verified professional photographers in Bangladesh. Book wedding photographers, event photography, portrait sessions and more. Trusted by thousands of clients.');
      updateMetaTag('keywords', meta.keywords || 'photographer bangladesh, wedding photographer dhaka, event photography, portrait photographer, professional photography');
      
      // Open Graph tags
      updateMetaTag('og:title', meta.title || 'Photographar SB - Find Perfect Photographers in Bangladesh', 'property');
      updateMetaTag('og:description', meta.description || 'Discover verified professional photographers in Bangladesh.', 'property');
      updateMetaTag('og:image', meta.image || `${window.location.origin}/images/og-default.jpg`, 'property');
      updateMetaTag('og:url', window.location.href, 'property');
      updateMetaTag('og:type', meta.type || 'website', 'property');
      updateMetaTag('og:site_name', 'Photographar SB', 'property');
      
      // Twitter Card tags
      updateMetaTag('twitter:card', 'summary_large_image', 'name');
      updateMetaTag('twitter:title', meta.title || 'Photographar SB', 'name');
      updateMetaTag('twitter:description', meta.description || 'Find verified photographers in Bangladesh', 'name');
      updateMetaTag('twitter:image', meta.image || `${window.location.origin}/images/og-default.jpg`, 'name');
      
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

    const getRouteMetaTags = () => {
      const routeName = route.name;
      const params = route.params;

      const metaMap = {
        'home': {
          title: 'Photographar SB - Find Perfect Photographers in Bangladesh',
          description: 'Discover verified professional photographers in Bangladesh. Book wedding photographers, event photography, portrait sessions. Trusted platform with secure payments.',
          keywords: 'photographer bangladesh, wedding photographer dhaka, event photography, portrait photographer',
          structuredData: {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "name": "Photographar SB",
            "url": "https://photographersb.com",
            "potentialAction": {
              "@type": "SearchAction",
              "target": "https://photographersb.com/search?q={search_term_string}",
              "query-input": "required name=search_term_string"
            }
          }
        },
        'photographer-profile': {
          title: `${params.slug} - Professional Photographer | Photographar SB`,
          description: `View portfolio, packages, and reviews for ${params.slug}. Book verified professional photographer in Bangladesh.`,
          type: 'profile'
        },
        'events': {
          title: 'Photography Events in Bangladesh | Photographar SB',
          description: 'Discover photography workshops, exhibitions, networking events in Bangladesh. Register for photography events and competitions.',
          keywords: 'photography events bangladesh, photography workshop dhaka, photo exhibition'
        },
        'competitions': {
          title: 'Photography Competitions Bangladesh | Photographar SB',
          description: 'Join photography competitions in Bangladesh. Submit your best photos, compete with talented photographers, and win exciting prizes.',
          keywords: 'photography competition bangladesh, photo contest, photography awards'
        },
        'admin-dashboard': {
          title: 'Admin Dashboard | Photographar SB',
          description: 'Manage photographers, events, competitions, and users.',
          robots: 'noindex, nofollow'
        },
        'admin-competitions-index': {
          title: 'Manage Competitions | Admin | Photographar SB',
          description: 'Admin panel for managing photography competitions.',
          robots: 'noindex, nofollow'
        }
      };

      return metaMap[routeName] || {
        title: 'Photographar SB',
        description: 'Find professional photographers in Bangladesh'
      };
    };

    // Watch for route changes
    watch(() => route.path, () => {
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
