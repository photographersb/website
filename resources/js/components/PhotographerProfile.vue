<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="relative h-80 bg-gradient-to-r from-burgundy via-[#8B1538] to-[#6F112D]">
      <div class="absolute inset-0 bg-black/20"></div>
      <div class="container mx-auto px-4 relative h-full flex items-end pb-8">
        <button
          @click="$router.back()"
          class="absolute top-6 left-4 text-white hover:text-gray-200 font-medium flex items-center gap-2 bg-black/30 px-4 py-2 rounded-lg backdrop-blur-sm"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Back
        </button>
      </div>
    </div>

    <div class="container mx-auto px-4 max-w-7xl -mt-32 relative z-10 pb-12">
      <div v-if="loading" class="text-center py-20 bg-white rounded-lg shadow-lg">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-burgundy mx-auto mb-4"></div>
        <p class="text-gray-600">Loading profile...</p>
      </div>

      <div v-else-if="!photographer" class="text-center py-20 bg-white rounded-lg shadow-lg">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-xl text-gray-600 mb-4">Photographer not found</p>
        <button @click="$router.push('/')" class="text-burgundy hover:underline font-medium">
          Return to Home
        </button>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Sidebar -->
        <div class="lg:col-span-1">
          <!-- Profile Card -->
          <div class="bg-white rounded-lg shadow-lg overflow-hidden sticky top-6">
            <div class="p-6 text-center">
              <img
                :src="photographer.profile_picture ? `/storage/${photographer.profile_picture}` : (photographer.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(photographer.user?.name || 'User')}&size=200&background=6c0b1a&color=fff`)"
                :alt="photographer.user?.name || 'Photographer'"
                class="w-32 h-32 rounded-full mx-auto mb-4 object-cover border-4 border-burgundy shadow-lg"
              />
              <h1 class="text-2xl font-bold text-gray-900 mb-2">
                {{ photographer.user?.name || photographer.business_name || 'Unknown' }}
              </h1>
              
              <!-- Location & Primary Category -->
              <div class="flex flex-wrap items-center justify-center gap-2 mb-3">
                <span v-if="photographer.location" class="inline-flex items-center gap-1 bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  {{ photographer.location }}
                </span>
                <span v-if="photographer.city?.name" class="inline-flex items-center gap-1 bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                  </svg>
                  {{ photographer.city.name }}
                </span>
                <span v-if="photographer.categories && photographer.categories.length > 0" class="inline-flex items-center gap-1 bg-burgundy/10 text-burgundy px-3 py-1 rounded-full text-xs font-bold">
                  <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                  </svg>
                  {{ photographer.categories[0].name }}
                  <span v-if="photographer.categories.length > 1" class="ml-0.5">+{{ photographer.categories.length - 1 }}</span>
                </span>
              </div>
              
              <!-- Level Badge -->
              <div v-if="photographer.achievements" class="flex items-center justify-center gap-2 mb-2">
                <div class="inline-flex items-center gap-1 bg-gradient-to-r from-purple-500 to-blue-500 text-white px-3 py-1 rounded-full text-sm font-bold shadow-md">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                  Level {{ photographer.achievements.level }} • {{ photographer.achievements.total_points }} pts
                </div>
              </div>
              
              <div class="flex items-center justify-center gap-2 mb-3">
                <div class="flex items-center gap-1">
                  <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                  </svg>
                  <span class="font-bold text-gray-900">{{ photographer.average_rating || '0.0' }}</span>
                  <span class="text-gray-600 text-sm">({{ photographer.rating_count || 0 }})</span>
                </div>
                <router-link
                  v-if="photographer.is_verified && photographer.slug"
                  :to="`/verify/${photographer.slug}`"
                  class="inline-flex items-center gap-1 bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium hover:bg-green-200 transition-colors"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  Verified
                </router-link>
                <span
                  v-else-if="photographer.is_verified"
                  class="inline-flex items-center gap-1 bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  Verified
                </span>
              </div>

              <p v-if="photographer.bio" class="text-gray-600 text-sm mb-4 line-clamp-3">{{ photographer.bio }}</p>

              <!-- Starting Price -->
              <div v-if="photographer.starting_price" class="mb-3 p-2 bg-green-50 rounded-lg border border-green-200">
                <p class="text-xs text-green-700 font-medium">Starting from</p>
                <p class="text-2xl font-bold text-green-700">৳{{ photographer.starting_price.toLocaleString() }}</p>
              </div>

              <!-- Response Time & Rate -->
              <div class="mb-3 space-y-2">
                <div v-if="photographer.average_response_time" class="flex items-center justify-center gap-2 text-sm">
                  <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                  </svg>
                  <span class="text-gray-700">
                    Usually responds in <strong>{{ photographer.average_response_time }}h</strong>
                  </span>
                </div>
                <div v-if="photographer.response_rate && photographer.response_rate >= 70" class="flex items-center justify-center gap-2 text-sm">
                  <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  <span class="text-gray-700">
                    <strong>{{ photographer.response_rate }}%</strong> response rate
                  </span>
                </div>
              </div>

              <!-- Profile Views -->
              <div v-if="photographer.profile_views" class="mb-4 text-xs text-gray-500">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                {{ photographer.profile_views.toLocaleString() }} profile views
              </div>

              <!-- Social Links -->
              <div v-if="photographer.instagram_url || photographer.facebook_url || photographer.twitter_url || photographer.linkedin_url || photographer.youtube_url || photographer.website_url" class="mb-4 flex items-center justify-center gap-2 flex-wrap">
                <a v-if="photographer.instagram_url" :href="photographer.instagram_url" target="_blank" class="p-2 bg-pink-100 text-pink-600 rounded-full hover:bg-pink-200 transition" title="Instagram">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                  </svg>
                </a>
                <a v-if="photographer.facebook_url" :href="photographer.facebook_url" target="_blank" class="p-2 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition" title="Facebook">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                  </svg>
                </a>
                <a v-if="photographer.twitter_url" :href="photographer.twitter_url" target="_blank" class="p-2 bg-sky-100 text-sky-600 rounded-full hover:bg-sky-200 transition" title="Twitter / X">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                  </svg>
                </a>
                <a v-if="photographer.linkedin_url" :href="photographer.linkedin_url" target="_blank" class="p-2 bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition" title="LinkedIn">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                  </svg>
                </a>
                <a v-if="photographer.youtube_url" :href="photographer.youtube_url" target="_blank" class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition" title="YouTube">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                  </svg>
                </a>
                <a v-if="photographer.website_url" :href="photographer.website_url" target="_blank" class="p-2 bg-gray-100 text-gray-600 rounded-full hover:bg-gray-200 transition" title="Website">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                  </svg>
                </a>
              </div>

              <!-- Contact Info -->
              <div class="space-y-2 mb-4 text-sm">
                <div v-if="photographer.user?.email" class="flex items-center justify-center gap-2 text-gray-600">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                  <span>{{ photographer.user.email }}</span>
                </div>
                <div v-if="photographer.phone" class="flex items-center justify-center gap-2 text-gray-600">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                  </svg>
                  <span>{{ photographer.phone }}</span>
                </div>
                <div v-if="photographer.location" class="flex items-center justify-center gap-2 text-gray-600">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  <span>{{ photographer.location }}</span>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="space-y-2">
                <!-- WhatsApp Button (Primary CTA for Bangladesh) -->
                <button
                  v-if="photographer.phone"
                  @click="contactWhatsApp"
                  class="w-full bg-green-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-600 transition shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                >
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                  </svg>
                  WhatsApp Chat
                </button>

                <!-- Call Button -->
                <button
                  v-if="photographer.phone"
                  @click="callPhotographer"
                  class="w-full bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                  </svg>
                  Call Now
                </button>

                <!-- Book Now Button -->
                <button
                  @click="startBooking"
                  class="w-full bg-burgundy text-white px-6 py-3 rounded-lg font-semibold hover:bg-[#8B1538] transition shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  Request Booking
                </button>

                <!-- Write Review Button -->
                <button
                  @click="writeReview"
                  class="w-full border-2 border-burgundy text-burgundy px-6 py-3 rounded-lg font-semibold hover:bg-burgundy hover:text-white transition"
                >
                  Write Review
                </button>
              </div>
            </div>

            <!-- Portfolio Completeness -->
            <div v-if="photographer.portfolio_completeness" class="px-6 pb-4">
              <div class="flex items-center justify-between text-xs mb-1">
                <span class="text-gray-600 font-medium">Profile Completeness</span>
                <span class="text-burgundy font-bold">{{ photographer.portfolio_completeness }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-burgundy to-pink-600 h-2 rounded-full transition-all" :style="{ width: photographer.portfolio_completeness + '%' }"></div>
              </div>
            </div>

            <!-- Member Since -->
            <div v-if="photographer.user?.created_at" class="px-6 pb-4 text-center">
              <p class="text-xs text-gray-500">
                <svg class="w-3.5 h-3.5 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                Member since {{ formatDate(photographer.user.created_at, 'MMMM yyyy') }}
              </p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-px bg-gray-200 border-t">
              <div class="bg-white p-4 text-center">
                <p class="text-2xl font-bold text-burgundy">{{ photographer.experience_years || 0 }}</p>
                <p class="text-gray-600 text-xs mt-1">Years Experience</p>
              </div>
              <div class="bg-white p-4 text-center">
                <p class="text-2xl font-bold text-burgundy">{{ photographer.total_bookings || 0 }}</p>
                <p class="text-gray-600 text-xs mt-1">Total Bookings</p>
              </div>
              <div class="bg-white p-4 text-center">
                <p class="text-2xl font-bold text-burgundy">{{ photographer.completed_bookings || 0 }}</p>
                <p class="text-gray-600 text-xs mt-1">Completed</p>
              </div>
              <div class="bg-white p-4 text-center">
                <p class="text-2xl font-bold text-burgundy">{{ photographer.trustScore?.overall_score || 0 }}</p>
                <p class="text-gray-600 text-xs mt-1">Trust Score</p>
              </div>
              <div class="bg-white p-4 text-center">
                <p class="text-2xl font-bold text-burgundy">{{ photographer.events_joined || 0 }}</p>
                <p class="text-gray-600 text-xs mt-1">Event Registrations</p>
              </div>
              <div class="bg-white p-4 text-center">
                <p class="text-2xl font-bold text-burgundy">{{ photographer.competitions_tried || 0 }}</p>
                <p class="text-gray-600 text-xs mt-1">Competitions Entered</p>
              </div>
              <div class="bg-white p-4 text-center">
                <p class="text-2xl font-bold text-burgundy">{{ photographer.awards_won || 0 }}</p>
                <p class="text-gray-600 text-xs mt-1">Awards Earned</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <!-- About Section -->
          <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">About</h2>
            <p v-if="photographer.bio" class="text-gray-700 leading-relaxed mb-4">{{ photographer.bio }}</p>
            <p v-else class="text-gray-500 italic">No bio available</p>
            
            <div v-if="photographer.specializations && photographer.specializations.length > 0" class="mt-4">
              <h3 class="font-semibold text-gray-900 mb-2">Specializations</h3>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="(spec, index) in photographer.specializations"
                  :key="index"
                  class="bg-burgundy/10 text-burgundy px-3 py-1.5 rounded-full text-sm font-medium"
                >
                  {{ spec }}
                </span>
              </div>
            </div>

            <div v-if="photographer.categories && photographer.categories.length > 0" class="mt-4">
              <h3 class="font-semibold text-gray-900 mb-2">Categories</h3>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="category in photographer.categories"
                  :key="category.id"
                  class="bg-gray-100 text-gray-700 px-3 py-1.5 rounded-full text-sm"
                >
                  {{ category.name }}
                </span>
              </div>
            </div>
          </div>

          <!-- Tab Navigation -->
          <div class="bg-white rounded-lg shadow-lg">
            <div class="border-b border-gray-200">
              <nav class="flex -mb-px overflow-x-auto scrollbar-hide">
                <button
                  @click="activeTab = 'portfolio'"
                  :class="[
                    'px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-all',
                    activeTab === 'portfolio'
                      ? 'border-burgundy text-burgundy'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  ]"
                >
                  Portfolio
                </button>
                <button
                  @click="activeTab = 'packages'"
                  :class="[
                    'px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-all',
                    activeTab === 'packages'
                      ? 'border-burgundy text-burgundy'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  ]"
                >
                  Packages
                </button>
                <button
                  @click="activeTab = 'reviews'"
                  :class="[
                    'px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-all',
                    activeTab === 'reviews'
                      ? 'border-burgundy text-burgundy'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  ]"
                >
                  Reviews
                </button>
                <button
                  @click="activeTab = 'awards'"
                  :class="[
                    'px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-all',
                    activeTab === 'awards'
                      ? 'border-burgundy text-burgundy'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  ]"
                >
                  Awards & Achievements
                </button>
              </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
              <!-- Portfolio Tab -->
              <div v-if="activeTab === 'portfolio'">
                <div v-if="albums && albums.length > 0">
                  <h2 class="text-2xl font-bold text-gray-900 mb-6">Portfolio</h2>
                  
                  <!-- Album Grid -->
                  <div v-for="album in albums" :key="album.id" class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                      <h3 class="text-xl font-semibold text-gray-900">{{ album.name }}</h3>
                      <span class="text-sm text-gray-500">{{ album.photos?.length || 0 }} photos</span>
                    </div>
                    
                    <div v-if="album.photos && album.photos.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                      <div
                        v-for="photo in album.photos"
                        :key="photo.id"
                        @click="openLightbox(photo)"
                        class="group relative aspect-square rounded-lg overflow-hidden cursor-pointer hover:shadow-xl transition-all"
                      >
                        <img
                          :src="photo.thumbnail_url || photo.url || 'https://via.placeholder.com/400'"
                          :alt="photo.title || album.name"
                          class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                          loading="lazy"
                        />
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-opacity flex items-center justify-center">
                          <svg class="w-10 h-10 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                          </svg>
                        </div>
                        <div v-if="photo.title" class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3 opacity-0 group-hover:opacity-100 transition-opacity">
                          <p class="text-white text-sm font-medium truncate">{{ photo.title }}</p>
                        </div>
                      </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-400">
                      No photos in this album yet
                    </div>
                  </div>
                </div>
                <div v-else class="text-center py-12">
                  <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <p class="text-gray-500">No portfolio albums yet</p>
                </div>
              </div>

              <!-- Packages Tab -->
              <div v-if="activeTab === 'packages'">
                <div v-if="packages && packages.length > 0">
                  <h2 class="text-2xl font-bold text-gray-900 mb-4">Packages</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                      v-for="pkg in packages"
                      :key="pkg.id"
                      class="border-2 border-gray-200 rounded-lg p-5 hover:border-burgundy hover:shadow-lg transition"
                    >
                      <div class="flex items-start justify-between mb-3">
                        <h3 class="font-bold text-lg text-gray-900">{{ pkg.name }}</h3>
                        <span class="bg-burgundy/10 text-burgundy px-2 py-1 rounded text-sm font-semibold">
                          ৳{{ pkg.base_price?.toLocaleString() }}
                        </span>
                      </div>
                      <p class="text-gray-600 text-sm mb-4">{{ pkg.description }}</p>
                      <button
                        @click="selectPackage(pkg)"
                        class="w-full bg-burgundy text-white py-2.5 rounded-lg font-semibold hover:bg-[#8B1538] transition"
                      >
                        Select Package
                      </button>
                    </div>
                  </div>
                </div>
                <div v-else class="text-center py-12">
                  <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                  </svg>
                  <p class="text-gray-500">No packages available yet</p>
                </div>
              </div>

              <!-- Reviews Tab -->
              <div v-if="activeTab === 'reviews'">
                <div v-if="reviews && reviews.length > 0">
                  <h2 class="text-2xl font-bold text-gray-900 mb-4">Reviews ({{ reviews.length }})</h2>
                  <div class="space-y-4">
                    <div
                      v-for="review in reviews"
                      :key="review.id"
                      class="border border-gray-200 rounded-lg p-4"
                    >
                      <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 rounded-full bg-burgundy text-white flex items-center justify-center font-semibold">
                            {{ review.is_anonymous ? 'A' : (review.reviewer?.name || 'A')[0].toUpperCase() }}
                          </div>
                          <div>
                            <p class="font-semibold text-gray-900">
                              {{ review.is_anonymous ? 'Anonymous' : (review.reviewer?.name || 'Anonymous') }}
                              <span v-if="review.is_verified_purchase" class="ml-2 text-xs text-green-600" title="Verified Purchase">✓ Verified</span>
                            </p>
                            <p class="text-xs text-gray-500">{{ formatDate(review.published_at) }}</p>
                          </div>
                        </div>
                        <div class="flex items-center gap-1 bg-yellow-50 px-2 py-1 rounded">
                          <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                          </svg>
                          <span class="font-semibold text-gray-900 text-sm">{{ review.rating }}</span>
                        </div>
                      </div>
                      <p class="text-gray-700">{{ review.comment }}</p>
                    </div>
                  </div>
                </div>
                <div v-else class="text-center py-12">
                  <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                  <p class="text-gray-500">No reviews yet</p>
                </div>
              </div>

              <!-- Awards Tab -->
              <div v-if="activeTab === 'awards'">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Awards & Achievements</h2>
                
                <!-- Competition Wins Section -->
                <div v-if="competitionWins && competitionWins.length > 0" class="mb-8">
                  <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    Competition Wins
                  </h3>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                      v-for="win in competitionWins"
                      :key="win.id"
                      class="bg-gradient-to-br from-yellow-50 to-orange-50 border-2 border-yellow-300 rounded-lg p-5 hover:shadow-xl transition"
                    >
                      <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                          <div class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-2xl font-bold text-white">{{ win.position === 1 ? '🥇' : win.position === 2 ? '🥈' : '🥉' }}</span>
                          </div>
                        </div>
                        <div class="flex-1 min-w-0">
                          <div class="flex items-start justify-between mb-2">
                            <div>
                              <div class="flex items-center gap-2 mb-1">
                                <span class="bg-yellow-500 text-white px-2 py-0.5 rounded text-xs font-bold">
                                  {{ win.position === 1 ? '1ST PLACE' : win.position === 2 ? '2ND PLACE' : '3RD PLACE' }}
                                </span>
                              </div>
                              <h4 class="font-bold text-lg text-gray-900 mb-1">{{ win.competition_title }}</h4>
                              <p v-if="win.submission_title" class="text-gray-700 text-sm mb-2">
                                <span class="font-medium">Entry:</span> {{ win.submission_title }}
                              </p>
                            </div>
                          </div>
                          <div class="flex items-center justify-between">
                            <div v-if="win.prize_amount" class="flex items-center gap-1">
                              <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                              </svg>
                              <span class="text-green-700 font-bold">৳{{ win.prize_amount.toLocaleString() }}</span>
                            </div>
                            <img v-if="win.image_url" :src="win.image_url" :alt="win.submission_title" class="w-16 h-16 object-cover rounded border-2 border-white shadow-sm" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Regular Awards Section -->
                <div v-if="awards && awards.length > 0" class="mb-8">
                  <h3 class="text-xl font-bold text-gray-900 mb-4">Professional Awards</h3>
                  <div class="space-y-4">
                    <div
                      v-for="award in awards"
                      :key="award.id"
                      class="border-2 border-gray-200 rounded-lg p-5 hover:border-burgundy hover:shadow-lg transition"
                    >
                    <div class="flex items-start gap-4">
                      <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-burgundy/10 rounded-lg flex items-center justify-center">
                          <svg class="w-8 h-8 text-burgundy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                          </svg>
                        </div>
                      </div>
                      <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between mb-2">
                          <div class="flex-1">
                            <h3 class="font-bold text-lg text-gray-900 mb-1">{{ award.title }}</h3>
                            <p v-if="award.organization" class="text-gray-600 text-sm mb-1">{{ award.organization }}</p>
                          </div>
                          <span class="flex-shrink-0 bg-burgundy/10 text-burgundy px-3 py-1 rounded-full text-sm font-semibold ml-3">
                            {{ award.year }}
                          </span>
                        </div>
                        <p v-if="award.description" class="text-gray-700 text-sm mb-2">{{ award.description }}</p>
                        <div class="flex items-center gap-2">
                          <span
                            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium"
                            :class="{
                              'bg-yellow-100 text-yellow-800': award.type === 'award',
                              'bg-blue-100 text-blue-800': award.type === 'achievement',
                              'bg-green-100 text-green-800': award.type === 'recognition',
                              'bg-purple-100 text-purple-800': award.type === 'certification'
                            }"
                          >
                            {{ award.type.charAt(0).toUpperCase() + award.type.slice(1) }}
                          </span>
                          <a
                            v-if="award.certificate_url"
                            :href="award.certificate_url"
                            target="_blank"
                            class="text-burgundy hover:underline text-xs font-medium flex items-center gap-1"
                          >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            View Certificate
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                  </div>
                </div>
                
                <!-- Empty State for Awards -->
                <div v-if="(!awards || awards.length === 0) && (!competitionWins || competitionWins.length === 0)" class="text-center py-12">
                  <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                  </svg>
                  <p class="text-gray-500 text-lg mb-2">No awards or achievements yet</p>
                  <p class="text-gray-400 text-sm">Competition wins and certificates will appear here</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Lightbox Modal -->
    <div
      v-if="selectedPhoto"
      @click="closeLightbox"
      class="fixed inset-0 z-50 bg-black bg-opacity-90 flex items-center justify-center p-4"
    >
      <button
        @click="closeLightbox"
        class="absolute top-4 right-4 text-white hover:text-gray-300 z-10"
      >
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
      
      <div @click.stop class="max-w-6xl w-full">
        <img
          :src="selectedPhoto.url || selectedPhoto.thumbnail_url"
          :alt="selectedPhoto.title"
          class="w-full h-auto max-h-[90vh] object-contain rounded-lg"
        />
        <div v-if="selectedPhoto.title || selectedPhoto.description" class="mt-4 text-white text-center">
          <h3 v-if="selectedPhoto.title" class="text-xl font-semibold mb-2">{{ selectedPhoto.title }}</h3>
          <p v-if="selectedPhoto.description" class="text-gray-300">{{ selectedPhoto.description }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api';

const route = useRoute();
const router = useRouter();
const photographer = ref(null);
const albums = ref([]);
const packages = ref([]);
const reviews = ref([]);
const awards = ref([]);
const competitionWins = ref([]);
const activeTab = ref('portfolio');
const loading = ref(true);
const selectedPhoto = ref(null);

const fetchPhotographer = async () => {
  try {
    const { data } = await api.get(`/photographers/${route.params.slug}`);
    
    if (data.status === 'success') {
      photographer.value = data.data;
      
      // Parse specializations if it's a JSON string
      if (typeof photographer.value.specializations === 'string') {
        try {
          photographer.value.specializations = JSON.parse(photographer.value.specializations);
        } catch (e) {
          photographer.value.specializations = [];
        }
      }
      
      albums.value = data.data.albums || [];
      packages.value = data.data.packages || [];
      reviews.value = data.data.reviews || [];
      awards.value = data.data.awards || [];
      competitionWins.value = data.data.competition_wins || [];
    }
  } catch (error) {
    console.error('Error fetching photographer:', error);
  } finally {
    loading.value = false;
  }
};

const startBooking = () => {
  router.push(`/booking/${photographer.value.id}`);
};

const writeReview = () => {
  const token = localStorage.getItem('auth_token');
  if (!token) {
    router.push('/auth');
    return;
  }
  router.push(`/review/${photographer.value.id}`);
};

const contactWhatsApp = () => {
  if (!photographer.value.phone) return;
  
  // Clean and format phone number for WhatsApp
  let phone = photographer.value.phone.replace(/[^0-9]/g, '');
  
  // Add Bangladesh country code if not present
  if (!phone.startsWith('880')) {
    if (phone.startsWith('0')) {
      phone = '880' + phone.substring(1);
    } else if (phone.startsWith('1')) {
      phone = '880' + phone;
    }
  }
  
  const message = encodeURIComponent(`Hi! I'm interested in your photography services. I found you on Photographer SB.`);
  window.open(`https://wa.me/${phone}?text=${message}`, '_blank');
};

const callPhotographer = () => {
  if (!photographer.value.phone) return;
  window.location.href = `tel:${photographer.value.phone}`;
};


const selectPackage = (pkg) => {
  router.push(`/booking/${photographer.value.id}?package=${pkg.id}`);
};

const formatDate = (date, format = 'default') => {
  if (!date) return '';
  const d = new Date(date);
  
  if (format === 'MMMM yyyy') {
    return d.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
  }
  
  return d.toLocaleDateString();
};

const openLightbox = (photo) => {
  selectedPhoto.value = photo;
  document.body.style.overflow = 'hidden';
};

const closeLightbox = () => {
  selectedPhoto.value = null;
  document.body.style.overflow = 'auto';
};

onMounted(() => {
  fetchPhotographer();
});
</script>
