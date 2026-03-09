<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <!-- Hero Section with Mobile-First Responsive Design -->
    <section class="relative overflow-hidden bg-gradient-to-br from-burgundy via-[#8E0E3F] to-[#6F112D] text-white">
      <!-- Decorative Background Elements (Hidden on Mobile, Visible on Desktop) -->
      <div class="absolute inset-0 overflow-hidden hidden sm:block">
        <div class="absolute top-0 left-0 w-48 h-48 sm:w-64 sm:h-64 md:w-96 md:h-96 bg-white bg-opacity-5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2" />
        <div class="absolute bottom-0 right-0 w-48 h-48 sm:w-64 sm:h-64 md:w-96 md:h-96 bg-white bg-opacity-5 rounded-full blur-3xl translate-x-1/2 translate-y-1/2" />
        <div class="absolute top-1/2 left-1/2 w-32 h-32 sm:w-48 sm:h-48 md:w-64 md:h-64 bg-white bg-opacity-5 rounded-full blur-2xl -translate-x-1/2 -translate-y-1/2" />
      </div>

      <!-- Mobile Background Gradient for Visibility -->
      <div class="absolute inset-0 sm:hidden opacity-40">
        <div class="absolute top-10 left-10 w-32 h-32 bg-white bg-opacity-10 rounded-full blur-2xl" />
        <div class="absolute bottom-10 right-5 w-24 h-24 bg-white bg-opacity-5 rounded-full blur-xl" />
      </div>

      <div class="relative z-10 w-full px-4 sm:px-6 md:px-8 lg:px-4 pt-8 pb-6 sm:pt-8 sm:pb-8 md:pt-8 md:pb-12 lg:pt-8 lg:pb-16">
        <div class="max-w-5xl mx-auto">
          <!-- Logo/Brand Section - Responsive Text -->
          <div class="text-center mt-5 mb-4 sm:mb-5 md:mb-6 lg:mb-8">
            <div class="inline-flex mb-2 sm:mb-3 px-3 sm:px-4 py-1 sm:py-1.5 bg-white bg-opacity-10 backdrop-blur-sm rounded-full border border-white border-opacity-20 hover:bg-opacity-20 transition-colors">
              <p class="text-xs sm:text-sm font-medium flex items-center gap-1 sm:gap-2 justify-center">
                <svg
                  class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z" />
                </svg>
                <span>A Project by</span>
                <a
                  href="https://somogrobangladesh.com/"
                  target="_blank"
                  rel="noopener"
                  class="underline hover:text-white/80 transition-colors font-semibold"
                >
                  Somogro Bangladesh
                </a>
              </p>
            </div>
          </div>

          <!-- Main Heading - Fluid Typography with clamp() -->
          <div class="text-center mb-5 sm:mb-6 md:mb-8 lg:mb-10">
            <h1
              class="font-bold mb-2 sm:mb-3 md:mb-4 tracking-tight leading-tight"
              style="font-size: clamp(1.75rem, 5vw, 4rem);"
            >
              <span class="inline-block animate-fade-in">Find Your Perfect</span>
              <br class="sm:hidden">
              <span class="block bg-gradient-to-r from-white via-gray-100 to-white bg-clip-text text-transparent animate-fade-in-delay">Photographer</span>
            </h1>
            <p
              class="font-medium text-gray-100 max-w-2xl mx-auto leading-relaxed animate-fade-in-delay-2"
              style="font-size: clamp(0.95rem, 2vw, 1.25rem);"
            >
              Connect with <span class="font-bold text-white">{{ totalPhotographers }}+ verified professionals</span> across Bangladesh for weddings, portraits & events.
            </p>
          </div>

          <!-- Search Bar - Fully Responsive -->
          <div class="max-w-3xl mx-auto animate-fade-in-delay-3">
            <!-- Search Container -->
            <div class="bg-white bg-opacity-10 backdrop-blur-md border border-white border-opacity-20 rounded-lg sm:rounded-xl md:rounded-2xl p-2.5 sm:p-3 md:p-4 shadow-2xl">
              <div class="flex flex-col gap-2.5 sm:gap-3 md:gap-3">
                <!-- Search Input Row -->
                <div class="flex flex-col sm:flex-row gap-2.5 sm:gap-3">
                  <div class="flex-1 relative">
                    <svg
                      class="absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 w-4 h-4 sm:w-5 sm:h-5 text-gray-400 flex-shrink-0"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                      />
                    </svg>
                    <input
                      v-model="searchQuery"
                      type="text"
                      placeholder="Search by name or specialty..."
                      class="w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-3 sm:py-3.5 md:py-4 rounded-lg sm:rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all bg-white/95 text-sm sm:text-base"
                      @input="debouncedSearch"
                      @keyup.enter="search"
                    >
                  </div>
                  <button
                    class="group px-4 sm:px-6 md:px-8 py-3 sm:py-3.5 md:py-4 bg-gradient-to-r from-burgundy to-[#6F112D] hover:from-[#6F112D] hover:to-burgundy text-white font-semibold rounded-lg sm:rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 whitespace-nowrap flex items-center justify-center gap-2 text-sm sm:text-base md:text-base w-full sm:w-auto"
                    @click="search"
                  >
                    <svg
                      class="w-4 h-4 sm:w-5 sm:h-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                      />
                    </svg>
                    <span class="hidden sm:inline">Search</span>
                  </button>
                </div>

                <!-- Popular Categories (Quick Filters) - Mobile Optimized -->
                <div class="flex flex-wrap justify-center gap-1.5 sm:gap-2 md:gap-2">
                  <button
                    v-for="category in popularCategories"
                    :key="category.slug"
                    class="px-2.5 sm:px-3 md:px-4 py-1 sm:py-1.5 md:py-2 bg-white bg-opacity-10 hover:bg-opacity-20 backdrop-blur-sm rounded-full text-xs sm:text-sm border border-white border-opacity-20 transition-all hover:scale-105 whitespace-nowrap font-medium"
                    @click="quickFilter(category.slug)"
                  >
                    <span class="mr-1">{{ category.icon }}</span>{{ category.name }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Quick Stats - Responsive Grid -->
            <div class="grid grid-cols-3 gap-2 sm:gap-3 md:gap-6 lg:gap-8 mt-5 sm:mt-6 md:mt-8 lg:mt-10 pb-16 sm:pb-20 md:pb-24 lg:pb-28">
              <div class="text-center px-1 sm:px-2">
                <div
                  class="font-bold mb-3 sm:mb-3 md:mb-4 leading-tight text-white"
                  style="font-size: clamp(1.75rem, 5vw, 3.5rem);"
                >
                  <span
                    v-if="totalPhotographers === '...'"
                    class="inline-block animate-pulse"
                  >...</span>
                  <span v-else>{{ totalPhotographers }}<span class="text-lg sm:text-2xl md:text-3xl">+</span></span>
                </div>
                <div
                  class="text-xs sm:text-sm font-bold text-white uppercase tracking-wide"
                  style="font-size: clamp(0.65rem, 1.5vw, 0.9rem);"
                >
                  Photographers
                </div>
              </div>
              <div class="text-center px-1 sm:px-2 border-x border-white border-opacity-40">
                <div
                  class="font-bold mb-3 sm:mb-3 md:mb-4 leading-tight text-white"
                  style="font-size: clamp(1.75rem, 5vw, 3.5rem);"
                >
                  <span
                    v-if="platformStats.cities === '...'"
                    class="inline-block animate-pulse"
                  >...</span>
                  <span v-else>{{ platformStats.cities }}<span class="text-lg sm:text-2xl md:text-3xl">+</span></span>
                </div>
                <div
                  class="text-xs sm:text-sm font-bold text-white uppercase tracking-wide"
                  style="font-size: clamp(0.65rem, 1.5vw, 0.9rem);"
                >
                  Locations Covered
                </div>
              </div>
              <div class="text-center px-1 sm:px-2">
                <div
                  class="font-bold mb-3 sm:mb-3 md:mb-4 leading-tight text-white"
                  style="font-size: clamp(1.75rem, 5vw, 3.5rem);"
                >
                  <span
                    v-if="platformStats.visitors === '...'"
                    class="inline-block animate-pulse"
                  >...</span>
                  <span v-else>{{ platformStats.visitors }}<span class="text-lg sm:text-2xl md:text-3xl">+</span></span>
                </div>
                <div
                  class="text-xs sm:text-sm font-bold text-white uppercase tracking-wide"
                  style="font-size: clamp(0.65rem, 1.5vw, 0.9rem);"
                >
                  Total Visitors
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Smooth Rounded Curve Separator -->
      <div class="absolute bottom-0 left-0 right-0">
        <svg
          viewBox="0 0 1440 100"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
          class="w-full"
        >
          <path
            d="M0 20 Q360 5 720 0 Q1080 5 1440 20 L1440 100 L0 100 Z"
            fill="#f9fafb"
          />
        </svg>
      </div>
    </section>

    <!-- Upcoming Events Section -->
    <section
      v-if="featuredEvents.length > 0"
      class="py-8 sm:py-12 md:py-16 px-4 sm:px-6 md:px-8 lg:px-4 bg-gradient-to-b from-white to-gray-50"
    >
      <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6 sm:mb-8">
          <div>
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-2">
              📅 Upcoming Events
            </h2>
            <p class="text-sm sm:text-base text-gray-600">
              Join photography workshops, photowalks & networking events
            </p>
          </div>
          <router-link
            to="/events"
            class="text-sm sm:text-base font-semibold text-burgundy hover:text-burgundy-dark flex items-center gap-2"
          >
            View All
            <svg
              class="w-4 h-4"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5l7 7-7 7"
              />
            </svg>
          </router-link>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
          <router-link
            v-for="event in featuredEvents.slice(0, 3)"
            :key="event.id"
            :to="`/events/${event.slug}`"
            class="group bg-white rounded-lg shadow hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100 hover:border-burgundy"
          >
            <!-- Event Image -->
            <div class="relative bg-gradient-to-br from-burgundy to-gray-800 overflow-hidden pt-[56.25%]">
              <picture v-if="getEventImage(event) && !hasFeaturedImageError(event.id)">
                <source
                  v-if="getWebpSource(getEventImage(event))"
                  :srcset="getWebpSource(getEventImage(event))"
                  type="image/webp"
                >
                <img
                  :src="getEventImage(event)"
                  :alt="event.title"
                  class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                  loading="lazy"
                  decoding="async"
                  width="1280"
                  height="720"
                  sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
                  @error="handleFeaturedImageError($event, event.id)"
                >
              </picture>
              <img
                v-else
                :src="imagePlaceholder"
                :alt="event.title || 'Event image placeholder'"
                class="absolute inset-0 w-full h-full object-cover"
                loading="lazy"
                decoding="async"
              >
              <div class="absolute inset-0 bg-gradient-to-t from-black/65 via-black/20 to-transparent" />
              
              <!-- Event Type Badge -->
              <div class="absolute top-2 left-2">
                <span
                  class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase"
                  :class="getEventTypeBadge(event).tone"
                >
                  {{ getEventTypeBadge(event).label }}
                </span>
              </div>

              <!-- Featured Badge -->
              <div
                v-if="getFeaturedBadge(event)"
                class="absolute top-2 right-2"
              >
                <span
                  class="px-2 py-0.5 rounded-full text-[10px] font-semibold"
                  :class="getFeaturedBadge(event)?.tone"
                >
                  {{ getFeaturedBadge(event).label }}
                </span>
              </div>

              <!-- Date Badge -->
              <div class="absolute bottom-2 left-2 bg-black/70 text-white rounded-full px-2 py-0.5 shadow text-[10px] font-semibold">
                {{ formatEventBadge(event.event_date) || 'TBA' }}
              </div>

              <!-- Price/Free Badge -->
              <div
                class="absolute bottom-2 right-2 px-2 py-0.5 rounded-full text-[10px] font-semibold"
                :class="getPriceBadge(event).tone"
              >
                {{ getPriceBadge(event).label }}
              </div>
            </div>

            <!-- Event Details -->
            <div class="p-3 sm:p-4">
              <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-1.5 group-hover:text-burgundy transition-colors">
                {{ event.title }}
              </h3>
              
              <p class="text-xs sm:text-sm text-gray-600 mb-3">
                {{ truncateText(event.description, 90) }}
              </p>

              <div class="space-y-1.5">
                <!-- Date & Time -->
                <div class="flex items-start gap-2 text-xs sm:text-sm text-gray-600 leading-tight">
                  <svg
                    class="w-4 h-4 flex-shrink-0 mt-0.5 text-burgundy"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                    />
                  </svg>
                  <div class="space-y-0.5">
                    <div>
                      <span class="text-gray-500">Date:</span>
                      <span class="ml-1 font-medium">{{ formatDateLabel(event.event_date || event.start_datetime) }}</span>
                    </div>
                    <div>
                      <span class="text-gray-500">Start:</span>
                      <span class="ml-1 font-medium">{{ formatTimeLabel(event.start_time, event.start_datetime) }}</span>
                    </div>
                    <div>
                      <span class="text-gray-500">End:</span>
                      <span class="ml-1 font-medium">{{ formatTimeLabel(event.end_time, event.end_datetime || event.event_end_date) }}</span>
                    </div>
                  </div>
                </div>

                <!-- Location -->
                <div
                  v-if="event.city || event.location"
                  class="flex items-center gap-2 text-xs sm:text-sm text-gray-600"
                >
                  <svg
                    class="w-4 h-4 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                    />
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                    />
                  </svg>
                  <button
                    type="button"
                    class="text-left hover:text-burgundy focus:outline-none focus-visible:ring-2 focus-visible:ring-burgundy rounded"
                    @click.stop.prevent="viewEventsByLocation(event)"
                  >
                    {{ event.city?.name || event.location }}
                  </button>
                </div>

                <!-- Price/Free -->
                <div class="flex items-center gap-2 text-xs sm:text-sm">
                  <svg
                    class="w-4 h-4 flex-shrink-0"
                    :class="isPaidEvent(event) ? 'text-burgundy' : 'text-green-600'"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                  <span
                    class="font-semibold"
                    :class="isPaidEvent(event) ? 'text-burgundy' : 'text-green-600'"
                  >
                    {{ getPriceBadge(event).label }}
                  </span>
                </div>

                <!-- Attendees -->
                <div
                  v-if="event.registrations_count"
                  class="flex items-center gap-2 text-xs sm:text-sm text-gray-600"
                >
                  <svg
                    class="w-4 h-4 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                    />
                  </svg>
                  <span>{{ event.registrations_count }} registered</span>
                </div>
              </div>

              <div class="mt-3 flex items-center justify-between text-xs sm:text-sm font-semibold text-burgundy">
                <span>View Details</span>
                <svg
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5l7 7-7 7"
                  />
                </svg>
              </div>
            </div>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Featured Competitions Section -->
    <section
      v-if="featuredCompetitions.length > 0"
      class="py-8 sm:py-12"
    >
      <div class="w-full px-4 sm:px-6 md:px-8 lg:px-4">
        <div class="max-w-5xl mx-auto">
          <!-- Header -->
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-3">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-2">
              🏆 Photography Contests
            </h2>
            <router-link
              to="/competitions"
              class="inline-flex items-center gap-2 text-burgundy hover:text-burgundy-dark font-semibold transition-colors group"
            >
              View All Competitions
              <svg
                class="w-5 h-5 group-hover:translate-x-1 transition-transform"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17 8l4 4m0 0l-4 4m4-4H3"
                />
              </svg>
            </router-link>
          </div>

          <!-- Competitions Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <router-link
              v-for="competition in featuredCompetitions.slice(0, 3)"
              :key="competition.id"
              :to="`/competitions/${competition.slug}`"
              class="group bg-white rounded-xl sm:rounded-2xl overflow-hidden border-2 border-gray-100 hover:border-burgundy hover:shadow-xl transition-all duration-300 flex flex-col"
            >
              <!-- Competition Image -->
              <div class="relative h-48 sm:h-56 overflow-hidden">
                <img
                  :src="competition.cover_image_url || imagePlaceholder"
                  :alt="competition.title"
                  class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                  @error="useImagePlaceholder"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent" />
                
                <!-- Status Badge -->
                <div class="absolute top-3 left-3">
                  <span
                    :class="{
                      'bg-green-500/90': competition.status === 'active',
                      'bg-blue-500/90': competition.status === 'published',
                      'bg-gray-500/90': competition.status === 'completed'
                    }"
                    class="px-3 py-1 rounded-full text-white text-xs font-semibold backdrop-blur-sm"
                  >
                    {{ competition.status.toUpperCase() }}
                  </span>
                </div>

                <!-- Featured Badge -->
                <div
                  v-if="competition.is_featured"
                  class="absolute top-3 right-3"
                >
                  <span class="px-3 py-1 bg-amber-500/90 rounded-full text-white text-xs font-semibold flex items-center gap-1 backdrop-blur-sm">
                    ⭐ Featured
                  </span>
                </div>

                <!-- Prize Pool Badge -->
                <div
                  v-if="competition.total_prize_pool"
                  class="absolute bottom-3 left-3"
                >
                  <div class="px-3 py-1.5 bg-white/90 rounded-lg backdrop-blur-sm">
                    <div class="text-xs text-gray-600 font-medium">Prize Pool</div>
                    <div class="text-sm font-bold text-burgundy">৳{{ formatPrize(competition.total_prize_pool) }}</div>
                  </div>
                </div>
              </div>

              <!-- Competition Details -->
              <div class="p-4 sm:p-5 flex-1 flex flex-col">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-2 group-hover:text-burgundy transition-colors line-clamp-2">
                  {{ competition.title }}
                </h3>

                <p
                  v-if="competition.theme"
                  class="text-sm text-gray-600 mb-3 line-clamp-2"
                >
                  Theme: {{ competition.theme }}
                </p>

                <!-- Meta Information -->
                <div class="mt-auto space-y-2 text-sm text-gray-600">
                  <!-- Submission Deadline -->
                  <div
                    v-if="competition.submission_deadline"
                    class="flex items-center gap-2"
                  >
                    <svg
                      class="w-4 h-4 text-burgundy flex-shrink-0"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                    <span>Deadline: {{ formatDeadline(competition.submission_deadline) }}</span>
                  </div>

                  <!-- Submissions Count -->
                  <div class="flex items-center gap-2">
                    <svg
                      class="w-4 h-4 text-burgundy flex-shrink-0"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                      />
                    </svg>
                    <span>{{ competition.submissions_count || 0 }} submissions</span>
                  </div>

                  <!-- Entry Fee -->
                  <div class="flex items-center gap-2">
                    <svg
                      class="w-4 h-4 text-burgundy flex-shrink-0"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
                      />
                    </svg>
                    <span v-if="competition.is_paid_competition" class="font-semibold text-burgundy">
                      Entry Fee Required
                    </span>
                    <span v-else class="font-semibold text-green-600">
                      Free Entry
                    </span>
                  </div>
                </div>
              </div>
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Photographers Section -->
    <section
      v-if="featuredPhotographers.length > 0"
      class="py-8 sm:py-12 bg-gradient-to-br from-burgundy-50 via-white to-burgundy-50 bg-opacity-30"
    >
      <div class="w-full px-4 sm:px-6 md:px-8 lg:px-4">
        <div class="max-w-5xl mx-auto">
          <!-- Header -->
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-3">
            <div>
              <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-2 mb-2">
                ⭐ Featured Photographers
              </h2>
              <p class="text-gray-600 text-sm sm:text-base">
                Top-rated professionals handpicked for excellence
              </p>
            </div>
            <router-link
              to="/?sort=rating&featured=true"
              class="inline-flex items-center gap-2 text-burgundy hover:text-burgundy-dark font-semibold transition-colors group"
            >
              Explore All
              <svg
                class="w-5 h-5 group-hover:translate-x-1 transition-transform"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17 8l4 4m0 0l-4 4m4-4H3"
                />
              </svg>
            </router-link>
          </div>

          <!-- Photographers Carousel/Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            <div
              v-for="photographer in featuredPhotographers.slice(0, 3)"
              :key="photographer.id"
              class="group bg-white rounded-xl sm:rounded-2xl overflow-hidden border-2 border-gray-100 hover:border-burgundy hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col"
            >
              <!-- Profile Picture -->
              <div class="relative">
                <div class="aspect-square overflow-hidden bg-gray-100">
                  <img
                    :src="getPhotographerAvatar(photographer)"
                    :alt="photographer.user?.name"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                    @error="useDefaultAvatar"
                  >
                </div>
                
                <!-- Featured Badge -->
                <div class="absolute top-3 right-3">
                  <span class="px-2.5 py-1 bg-amber-500 rounded-full text-white text-xs font-bold flex items-center gap-1 shadow-lg">
                    ⭐ Featured
                  </span>
                </div>

                <!-- Favorite Button -->
                <button
                  v-if="isClient"
                  class="absolute top-3 left-3 w-9 h-9 rounded-full flex items-center justify-center shadow-lg transition-all"
                  :class="favoriteIdSet.has(photographer.id) ? 'bg-[#7a1f2b] text-white' : 'bg-white text-[#7a1f2b] hover:scale-105'"
                  aria-label="Toggle favorite"
                  @click.stop="toggleFavorite(photographer.id)"
                >
                  <svg
                    class="w-4 h-4"
                    :fill="favoriteIdSet.has(photographer.id) ? 'currentColor' : 'none'"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3.172 5.172a4 4 0 015.656 0L12 8.343l3.172-3.171a4 4 0 115.656 5.656L12 21.343l-8.828-8.829a4 4 0 010-5.656z"
                    />
                  </svg>
                </button>

                <!-- Rating Badge -->
                <div
                  v-if="isValidRating(photographer.average_rating)"
                  class="absolute bottom-3 left-3"
                >
                  <div class="px-3 py-1.5 bg-white/95 rounded-lg backdrop-blur-sm shadow-lg flex items-center gap-1.5">
                    <svg
                      class="w-4 h-4 text-amber-400"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="text-sm font-bold text-gray-800">{{ formatRating(photographer.average_rating) }}</span>
                  </div>
                </div>
              </div>

              <!-- Photographer Details -->
              <div class="p-4 flex-1 flex flex-col">
                <h3 class="text-lg font-bold text-gray-800 mb-1 group-hover:text-burgundy transition-colors line-clamp-1">
                  {{ photographer.user?.name }}
                </h3>

                <!-- Location -->
                <div
                  v-if="photographer.city"
                  class="flex items-center gap-1.5 text-sm text-gray-600 mb-3"
                >
                  <svg
                    class="w-4 h-4 text-burgundy flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                    />
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                    />
                  </svg>
                  <span>{{ photographer.city.name }}</span>
                </div>

                <!-- Categories -->
                <div
                  v-if="photographer.categories && photographer.categories.length > 0"
                  class="flex flex-wrap gap-1.5 mb-3"
                >
                  <span
                    v-for="category in photographer.categories.slice(0, 2)"
                    :key="category.id"
                    class="px-2 py-0.5 bg-burgundy-50 text-burgundy text-xs font-medium rounded"
                  >
                    {{ category.name }}
                  </span>
                  <span
                    v-if="photographer.categories.length > 2"
                    class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded"
                  >
                    +{{ photographer.categories.length - 2 }}
                  </span>
                </div>

                <!-- CTA Button -->
                <router-link
                  :to="getPublicProfilePath(photographer)"
                  class="mt-auto w-full py-2.5 bg-burgundy text-white rounded-lg font-semibold text-center hover:bg-burgundy-dark transition-colors group-hover:shadow-lg"
                >
                  View Profile
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Popular Locations Section -->
    <section
      v-if="cities.length > 0"
      class="py-8 sm:py-12"
    >
      <div class="w-full px-4 sm:px-6 md:px-8 lg:px-4">
        <div class="max-w-5xl mx-auto">
          <!-- Header -->
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-3">
            <div>
              <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-2 mb-2">
                📍 Browse by Location
              </h2>
              <p class="text-gray-600 text-sm sm:text-base">
                Find talented photographers across Bangladesh
              </p>
            </div>
            <a
              href="/locations"
              class="inline-flex items-center gap-2 text-burgundy hover:text-burgundy-dark font-semibold transition-colors group"
            >
              View All Locations
              <svg
                class="w-5 h-5 group-hover:translate-x-1 transition-transform"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17 8l4 4m0 0l-4 4m4-4H3"
                />
              </svg>
            </a>
          </div>

          <!-- Locations Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
            <a
              v-for="city in cities.slice(0, 12)"
              :key="city.id"
              :href="`/locations/${city.slug}`"
              class="group bg-white rounded-xl overflow-hidden border-2 border-gray-100 hover:border-burgundy hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 p-4 sm:p-5 flex flex-col items-center text-center"
            >
              <!-- Location Icon -->
              <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-gradient-to-br from-burgundy/10 to-burgundy/5 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg
                  class="w-6 h-6 sm:w-7 sm:h-7 text-burgundy"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                  />
                </svg>
              </div>

              <!-- Location Name -->
              <h3 class="text-sm sm:text-base font-bold text-gray-800 mb-1 group-hover:text-burgundy transition-colors line-clamp-1">
                {{ city.name }}
              </h3>

              <!-- Photographer Count (if available) -->
              <p
                v-if="city.photographers_count !== undefined"
                class="text-xs text-gray-600"
              >
                {{ city.photographers_count }} photographers
              </p>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Photo Categories Section -->
    <section
      v-if="categories.length > 0"
      class="py-8 sm:py-12 bg-gradient-to-br from-gray-50 via-white to-gray-50"
    >
      <div class="w-full px-4 sm:px-6 md:px-8 lg:px-4">
        <div class="max-w-5xl mx-auto">
          <!-- Header -->
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-3">
            <div>
              <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-2 mb-2">
                📸 Photography Specializations
              </h2>
              <p class="text-gray-600 text-sm sm:text-base">
                Discover photographers by their expertise
              </p>
            </div>
            <a
              href="/categories"
              class="inline-flex items-center gap-2 text-burgundy hover:text-burgundy-dark font-semibold transition-colors group"
            >
              View All Categories
              <svg
                class="w-5 h-5 group-hover:translate-x-1 transition-transform"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17 8l4 4m0 0l-4 4m4-4H3"
                />
              </svg>
            </a>
          </div>

          <!-- Categories Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
            <a
              v-for="category in categories.slice(0, 12)"
              :key="category.id"
              :href="`/categories/${category.slug}`"
              class="group bg-white rounded-xl overflow-hidden border-2 border-gray-100 hover:border-burgundy hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 p-4 sm:p-5 flex flex-col items-center text-center"
            >
              <!-- Category Icon -->
              <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-gradient-to-br from-burgundy/10 to-burgundy/5 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <span
                  v-if="category.icon"
                  class="text-2xl sm:text-3xl"
                  v-html="category.icon"
                />
                <svg
                  v-else
                  class="w-6 h-6 sm:w-7 sm:h-7 text-burgundy"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                </svg>
              </div>

              <!-- Category Name -->
              <h3 class="text-sm sm:text-base font-bold text-gray-800 mb-1 group-hover:text-burgundy transition-colors line-clamp-2">
                {{ category.name }}
              </h3>

              <!-- Photographer Count (if available) -->
              <p
                v-if="category.photographers_count !== undefined"
                class="text-xs text-gray-600"
              >
                {{ category.photographers_count }} pros
              </p>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section
      v-if="testimonials.length > 0"
      class="py-8 sm:py-12 bg-gradient-to-br from-burgundy-50 via-white to-burgundy-50 bg-opacity-30"
    >
      <div class="w-full px-4 sm:px-6 md:px-8 lg:px-4">
        <div class="max-w-5xl mx-auto">
          <!-- Header -->
          <div class="text-center mb-8 sm:mb-10">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">
              💬 What Our Clients Say
            </h2>
            <p class="text-gray-600 text-sm sm:text-base">
              Real experiences from photographers and their clients
            </p>
          </div>

          <!-- Testimonials Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div
              v-for="testimonial in testimonials.slice(0, 3)"
              :key="testimonial.id"
              class="bg-white rounded-xl sm:rounded-2xl overflow-hidden border-2 border-gray-100 hover:border-burgundy hover:shadow-xl transition-all duration-300 p-5 sm:p-6 flex flex-col"
            >
              <!-- Rating Stars -->
              <div class="flex items-center gap-1 mb-3">
                <svg
                  v-for="star in 5"
                  :key="star"
                  :class="star <= testimonial.rating ? 'text-amber-400' : 'text-gray-300'"
                  class="w-5 h-5"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
              </div>

              <!-- Review Title -->
              <h3
                v-if="testimonial.title"
                class="text-base sm:text-lg font-bold text-gray-800 mb-2"
              >
                {{ testimonial.title }}
              </h3>

              <!-- Review Comment -->
              <p class="text-sm text-gray-600 mb-4 flex-1 line-clamp-4">
                "{{ testimonial.comment }}"
              </p>

              <!-- Reviewer Info -->
              <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <div
                  v-if="!testimonial.is_anonymous && testimonial.reviewer"
                  class="flex items-center gap-3 flex-1"
                >
                  <div class="w-10 h-10 rounded-full bg-gradient-to-br from-burgundy/20 to-burgundy/10 flex items-center justify-center overflow-hidden">
                    <img
                      v-if="testimonial.reviewer.avatar"
                      :src="testimonial.reviewer.avatar"
                      :alt="testimonial.reviewer.name"
                      class="w-full h-full object-cover"
                    >
                    <span
                      v-else
                      class="text-burgundy font-bold text-sm"
                    >
                      {{ testimonial.reviewer.name.charAt(0).toUpperCase() }}
                    </span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">
                      {{ testimonial.reviewer.name }}
                    </p>
                    <p class="text-xs text-gray-500">
                      {{ formatReviewDate(testimonial.created_at) }}
                    </p>
                  </div>
                </div>
                <div
                  v-else
                  class="flex items-center gap-3 flex-1"
                >
                  <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                    <svg
                      class="w-5 h-5 text-gray-400"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                      />
                    </svg>
                  </div>
                  <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-600">Anonymous</p>
                    <p class="text-xs text-gray-500">
                      {{ formatReviewDate(testimonial.created_at) }}
                    </p>
                  </div>
                </div>

                <!-- Verified Badge -->
                <div
                  v-if="testimonial.is_verified_purchase"
                  class="flex-shrink-0"
                >
                  <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-50 text-green-700 rounded text-xs font-medium">
                    <svg
                      class="w-3 h-3"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    Verified
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Trending Hashtags Section -->
    <section
      v-if="trendingHashtags.length > 0"
      class="py-8 sm:py-12"
    >
      <div class="w-full px-4 sm:px-6 md:px-8 lg:px-4">
        <div class="max-w-5xl mx-auto">
          <!-- Header -->
          <div class="text-center mb-6 sm:mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">
              # Trending Topics
            </h2>
            <p class="text-gray-600 text-sm sm:text-base">
              Explore popular photography trends and styles
            </p>
          </div>

          <!-- Hashtags Cloud -->
          <div class="flex flex-wrap justify-center gap-2 sm:gap-3">
            <router-link
              v-for="hashtag in trendingHashtags.slice(0, 15)"
              :key="hashtag.id"
              :to="`/hashtags/${hashtag.slug || hashtag.name.replace('#', '')}`"
              class="group inline-flex items-center gap-2 px-4 sm:px-5 py-2 sm:py-2.5 bg-white rounded-full border-2 border-gray-200 hover:border-burgundy hover:bg-burgundy hover:text-white transition-all duration-300 transform hover:scale-105 shadow-sm hover:shadow-md"
            >
              <span class="text-sm sm:text-base font-semibold">
                {{ hashtag.name }}
              </span>
              <span
                v-if="hashtag.usage_count"
                class="text-xs px-2 py-0.5 rounded-full bg-gray-100 group-hover:bg-white/20 text-gray-600 group-hover:text-white transition-colors"
              >
                {{ formatHashtagCount(hashtag.usage_count) }}
              </span>
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Platform Statistics Section -->
    <section class="py-12 sm:py-16 bg-gradient-to-br from-burgundy via-burgundy-dark to-burgundy">
      <div class="w-full px-4 sm:px-6 md:px-8 lg:px-4">
        <div class="max-w-5xl mx-auto">
          <!-- Header -->
          <div class="text-center mb-8 sm:mb-12">
            <h2 class="text-2xl sm:text-3xl font-bold text-white mb-2">
              📊 Platform at a Glance
            </h2>
            <p class="text-burgundy-100 text-sm sm:text-base">
              Join thousands of photographers and clients on Bangladesh's premier platform
            </p>
          </div>

          <!-- Statistics Grid -->
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Total Photographers -->
            <div class="bg-white backdrop-blur-sm rounded-xl sm:rounded-2xl p-5 sm:p-6 text-center hover:bg-white transition-all duration-300 transform hover:-translate-y-1 border border-white bg-opacity-10 hover:bg-opacity-20 border-opacity-20">
              <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg
                  class="w-6 h-6 sm:w-7 sm:h-7 text-white"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                </svg>
              </div>
              <div class="text-3xl sm:text-4xl font-bold text-white mb-1">
                {{ platformStats.photographers }}
              </div>
              <div class="text-sm sm:text-base text-burgundy-100 font-medium">
                Photographers
              </div>
            </div>

            <!-- Cities Covered -->
            <div class="bg-white/10 backdrop-blur-sm rounded-xl sm:rounded-2xl p-5 sm:p-6 text-center hover:bg-white/20 transition-all duration-300 transform hover:-translate-y-1 border border-white/20">
              <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg
                  class="w-6 h-6 sm:w-7 sm:h-7 text-white"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                </svg>
              </div>
              <div class="text-3xl sm:text-4xl font-bold text-white mb-1">
                {{ platformStats.cities }}
              </div>
              <div class="text-sm sm:text-base text-burgundy-100 font-medium">
                Cities Covered
              </div>
            </div>

            <!-- Total Visitors -->
            <div class="bg-white/10 backdrop-blur-sm rounded-xl sm:rounded-2xl p-5 sm:p-6 text-center hover:bg-white/20 transition-all duration-300 transform hover:-translate-y-1 border border-white/20">
              <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg
                  class="w-6 h-6 sm:w-7 sm:h-7 text-white"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                  />
                </svg>
              </div>
              <div class="text-3xl sm:text-4xl font-bold text-white mb-1">
                {{ platformStats.visitors }}
              </div>
              <div class="text-sm sm:text-base text-burgundy-100 font-medium">
                Happy Visitors
              </div>
            </div>

            <!-- Average Rating -->
            <div class="bg-white/10 backdrop-blur-sm rounded-xl sm:rounded-2xl p-5 sm:p-6 text-center hover:bg-white/20 transition-all duration-300 transform hover:-translate-y-1 border border-white/20">
              <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg
                  class="w-6 h-6 sm:w-7 sm:h-7 text-white"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
              </div>
              <div class="text-3xl sm:text-4xl font-bold text-white mb-1">
                {{ platformStats.rating }}<span class="text-2xl">/5</span>
              </div>
              <div class="text-sm sm:text-base text-burgundy-100 font-medium">
                Avg. Rating
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Smart Filters Section - Mobile Responsive -->
    <section class="bg-white border-b sticky top-16 sm:top-20 z-40 shadow-sm">
      <div class="w-full px-4 sm:px-6 md:px-8 lg:px-4 py-3 sm:py-4 md:py-5">
        <div class="max-w-5xl mx-auto">
          <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-2 sm:gap-3 md:gap-4">
            <!-- Category Filter -->
            <div class="relative col-span-1">
              <label class="block text-xs sm:text-sm font-semibold mb-1 sm:mb-2 text-gray-700">Category</label>
              <div class="relative">
                <svg
                  class="absolute left-2 sm:left-3 top-1/2 -translate-y-1/2 w-4 h-4 sm:w-5 sm:h-5 text-gray-400 pointer-events-none flex-shrink-0"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                  />
                </svg>
                <select
                  v-model="filters.category"
                  class="w-full pl-8 sm:pl-10 pr-2 sm:pr-3 py-2 sm:py-2.5 md:py-3 border-2 border-gray-200 rounded-lg sm:rounded-xl focus:border-burgundy focus:ring-2 focus:ring-burgundy/20 transition-all appearance-none bg-white cursor-pointer hover:border-gray-300 text-xs sm:text-sm font-medium"
                >
                  <option value="">
                    All
                  </option>
                  <option
                    v-for="cat in categories"
                    :key="cat.id"
                    :value="cat.slug"
                  >
                    {{ cat.name }}
                  </option>
                </select>
                <svg
                  class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 w-3 h-3 sm:w-4 sm:h-4 text-gray-400 pointer-events-none flex-shrink-0"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                  />
                </svg>
              </div>
            </div>
            
            <!-- City Filter -->
            <div class="relative col-span-1">
              <label class="block text-xs sm:text-sm font-semibold mb-1 sm:mb-2 text-gray-700">City</label>
              <div class="relative">
                <svg
                  class="absolute left-2 sm:left-3 top-1/2 -translate-y-1/2 w-4 h-4 sm:w-5 sm:h-5 text-gray-400 pointer-events-none flex-shrink-0"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                </svg>
                <select
                  v-model="filters.city"
                  class="w-full pl-8 sm:pl-10 pr-2 sm:pr-3 py-2 sm:py-2.5 md:py-3 border-2 border-gray-200 rounded-lg sm:rounded-xl focus:border-burgundy focus:ring-2 focus:ring-burgundy/20 transition-all appearance-none bg-white cursor-pointer hover:border-gray-300 text-xs sm:text-sm font-medium"
                >
                  <option value="">
                    All
                  </option>
                  <option
                    v-for="city in cities"
                    :key="city.id"
                    :value="city.slug"
                  >
                    {{ city.name }}
                  </option>
                </select>
                <svg
                  class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 w-3 h-3 sm:w-4 sm:h-4 text-gray-400 pointer-events-none flex-shrink-0"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                  />
                </svg>
              </div>
            </div>
            
            <!-- Rating Filter -->
            <div class="relative col-span-1 hidden sm:block">
              <label class="block text-xs sm:text-sm font-semibold mb-1 sm:mb-2 text-gray-700">Rating</label>
              <div class="relative">
                <svg
                  class="absolute left-2 sm:left-3 top-1/2 -translate-y-1/2 w-4 h-4 sm:w-5 sm:h-5 text-yellow-400 pointer-events-none flex-shrink-0"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <select
                  v-model="filters.rating"
                  class="w-full pl-8 sm:pl-10 pr-2 sm:pr-3 py-2 sm:py-2.5 md:py-3 border-2 border-gray-200 rounded-lg sm:rounded-xl focus:border-burgundy focus:ring-2 focus:ring-burgundy/20 transition-all appearance-none bg-white cursor-pointer hover:border-gray-300 text-xs sm:text-sm font-medium"
                >
                  <option value="">
                    All
                  </option>
                  <option value="5">
                    ★★★★★
                  </option>
                  <option value="4">
                    ★★★★+
                  </option>
                  <option value="3">
                    ★★★+
                  </option>
                </select>
                <svg
                  class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 w-3 h-3 sm:w-4 sm:h-4 text-gray-400 pointer-events-none flex-shrink-0"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                  />
                </svg>
              </div>
            </div>
            
            <!-- Sort By Filter -->
            <div class="relative col-span-1 hidden sm:block">
              <label class="block text-xs sm:text-sm font-semibold mb-1 sm:mb-2 text-gray-700">Sort</label>
              <div class="relative">
                <svg
                  class="absolute left-2 sm:left-3 top-1/2 -translate-y-1/2 w-4 h-4 sm:w-5 sm:h-5 text-gray-400 pointer-events-none flex-shrink-0"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"
                  />
                </svg>
                <select
                  v-model="filters.sort"
                  class="w-full pl-8 sm:pl-10 pr-2 sm:pr-3 py-2 sm:py-2.5 md:py-3 border-2 border-gray-200 rounded-lg sm:rounded-xl focus:border-burgundy focus:ring-2 focus:ring-burgundy/20 transition-all appearance-none bg-white cursor-pointer hover:border-gray-300 text-xs sm:text-sm font-medium"
                >
                  <option value="relevance">
                    Relevant
                  </option>
                  <option value="rating">
                    Rated
                  </option>
                  <option value="newest">
                    Newest
                  </option>
                </select>
                <svg
                  class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 w-3 h-3 sm:w-4 sm:h-4 text-gray-400 pointer-events-none flex-shrink-0"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                  />
                </svg>
              </div>
            </div>

            <!-- Mobile Additional Filter (shown only on very small screens) -->
            <div class="relative col-span-1 sm:hidden">
              <label class="block text-xs font-semibold mb-1 text-gray-700">More</label>
              <button class="w-full p-2 border-2 border-gray-200 rounded-lg bg-white text-gray-700 font-medium text-xs hover:border-burgundy transition-all">
                Filters
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Photographers Grid - Responsive Section -->
    <section class="py-6 sm:py-8 md:py-12 lg:py-16 px-4 sm:px-6 md:px-8 lg:px-4">
      <div class="max-w-5xl mx-auto">
        <!-- Loading State -->
        <div
          v-if="loading"
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 md:gap-6 lg:gap-8"
        >
          <LoadingSkeleton
            v-for="n in 8"
            :key="n"
            type="card"
          />
        </div>

        <!-- Empty State -->
        <EmptyState
          v-else-if="photographers.length === 0"
          icon="camera"
          :title="searchQuery || filters.category || filters.city || filters.rating ? 'No photographers found' : 'Building Our Community'"
          :description="searchQuery || filters.category || filters.city || filters.rating ? 'Try adjusting your filters or search terms to find more photographers.' : 'We\'re building our photographer community. Check back soon or be among the first to join!'"
          :action-text="searchQuery || filters.category || filters.city || filters.rating ? 'Reset Filters' : ''"
          @action="resetFilters"
        />

        <!-- Photographers Grid -->
        <div v-else>
          <!-- Results Header - Responsive Typography -->
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 mb-6 sm:mb-8 md:mb-10">
            <div>
              <h2
                class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 leading-tight"
                style="font-size: clamp(1.25rem, 3vw, 1.875rem);"
              >
                Found {{ photographers.length }} photographer{{ photographers.length !== 1 ? 's' : '' }}
              </h2>
              <p v-if="filters.city || searchQuery" class="text-sm sm:text-base text-gray-600 mt-1">
                <span v-if="filters.city && getCityName(filters.city)">
                  Found {{ photographers.length }} photographer{{ photographers.length !== 1 ? 's' : '' }} in <strong>{{ getCityName(filters.city) }}</strong>
                </span>
                <span v-else-if="searchQuery">
                  Searching for: <strong>{{ searchQuery }}</strong>
                </span>
              </p>
            </div>
            <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-600 font-medium whitespace-nowrap">
              <svg
                class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z"
                  clip-rule="evenodd"
                />
              </svg>
              <span>Verified</span>
            </div>
          </div>

          <!-- Cards Grid - Mobile Optimized -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 md:gap-6 lg:gap-7">
            <PhotographerCard
              v-for="photographer in photographers"
              :key="photographer.id"
              :photographer="photographer"
              cta-text="View Profile"
              :show-favorite="isClient"
              :is-favorite="favoriteIdSet.has(photographer.id)"
              @click="viewPhotographer(photographer)"
              @book="bookPhotographer(photographer)"
              @toggle-favorite="toggleFavorite"
            />
          </div>

          <!-- Pagination - Mobile Responsive -->
          <div
            v-if="totalPages > 1"
            class="flex flex-wrap justify-center items-center gap-2 mt-8 sm:mt-12 md:mt-16"
          >
            <button
              v-if="currentPage > 1"
              class="px-3 sm:px-4 md:px-6 py-2 sm:py-2.5 md:py-3 border-2 border-gray-200 rounded-lg sm:rounded-xl hover:border-burgundy hover:text-burgundy transition-all font-medium text-sm sm:text-base flex items-center gap-1 sm:gap-2"
              @click="previousPage"
            >
              <svg
                class="w-4 h-4 sm:w-5 sm:h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 19l-7-7 7-7"
                />
              </svg>
              <span class="hidden sm:inline">Previous</span>
            </button>
            
            <div class="flex items-center gap-1 sm:gap-2">
              <span
                v-for="page in visiblePages"
                :key="page" 
                :class="page === currentPage ? 'bg-burgundy text-white border-burgundy' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-200'"
                class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 flex items-center justify-center rounded-lg sm:rounded-xl cursor-pointer transition-all font-semibold border-2 text-xs sm:text-sm md:text-base"
                @click="goToPage(page)"
              >
                {{ page }}
              </span>
            </div>
            
            <button
              v-if="currentPage < totalPages"
              class="px-3 sm:px-4 md:px-6 py-2 sm:py-2.5 md:py-3 border-2 border-gray-200 rounded-lg sm:rounded-xl hover:border-burgundy hover:text-burgundy transition-all font-medium text-sm sm:text-base flex items-center gap-1 sm:gap-2"
              @click="nextPage"
            >
              <span class="hidden sm:inline">Next</span>
              <svg
                class="w-4 h-4 sm:w-5 sm:h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5l7 7-7 7"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api';
import PhotographerCard from './ui/PhotographerCard.vue';
import LoadingSkeleton from './ui/LoadingSkeleton.vue';
import EmptyState from './ui/EmptyState.vue';
import {
  formatDate as formatDateValue,
  formatMonthShort,
  formatMonthYearShort
} from '../utils/formatters';

const router = useRouter();
const photographers = ref([]);
const featuredEvents = ref([]);
const featuredCompetitions = ref([]);
const featuredPhotographers = ref([]);
const testimonials = ref([]);
const trendingHashtags = ref([]);
const categories = ref([]);
const cities = ref([]);
const loading = ref(false);
const sectionsLoading = ref(true);
const searchQuery = ref('');
const currentPage = ref(1);
const totalPages = ref(1);
const totalPhotographers = ref('...');
const platformStats = ref({
  photographers: '...',
  cities: '...',
  visitors: '...',
  rating: '...'
});
const favoriteIds = ref([]);
const featuredImageErrors = ref({});
const imagePlaceholder = '/images/placeholder.svg';

const normalizeRole = (role) => String(role || '').toLowerCase().replace(/[\s-]+/g, '_');
const storedUserRole = computed(() => {
  const storedUser = JSON.parse(localStorage.getItem('user') || '{}');
  return normalizeRole(localStorage.getItem('user_role') || storedUser.role);
});
const isClient = computed(() => storedUserRole.value === 'client');
const favoriteIdSet = computed(() => new Set(favoriteIds.value));

const filters = ref({
  category: '',
  city: '',
  rating: '',
  sort: 'relevance',
});

const popularCategories = ref([
  { name: 'Wedding', slug: 'wedding-photography', icon: '💒' },
  { name: 'Portrait', slug: 'portrait-photography', icon: '📸' },
  { name: 'Event', slug: 'event-photography', icon: '🎉' },
  { name: 'Newborn', slug: 'newborn-photography', icon: '👶' },
]);

const quickFilter = (categorySlug) => {
  filters.value.category = categorySlug;
  currentPage.value = 1;
  window.scrollTo({ top: document.querySelector('.bg-white.border-b')?.offsetTop || 400, behavior: 'smooth' });
};

const visiblePages = computed(() => {
  const pages = [];
  const maxVisible = 5;
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
  let end = Math.min(totalPages.value, start + maxVisible - 1);
  
  if (end - start < maxVisible - 1) {
    start = Math.max(1, end - maxVisible + 1);
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  return pages;
});

const resetFilters = () => {
  filters.value = {
    category: '',
    city: '',
    rating: '',
    sort: 'relevance',
  };
  searchQuery.value = '';
  currentPage.value = 1;
  fetchPhotographers();
};

const goToPage = (page) => {
  currentPage.value = page;
  fetchPhotographers();
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const fetchPlatformStats = async () => {
  try {
    const { data } = await api.get('/platform/stats');
    if (data.status === 'success') {
      // Format numbers for display
      platformStats.value = {
        photographers: formatNumber(data.data.photographers),
        cities: data.data.cities,
        visitors: formatNumber(data.data.visitors),
        rating: data.data.rating
      };
    }
  } catch (error) {
    if (import.meta.env.DEV) console.error('Error fetching platform stats:', error);
    // Start from 0 if API fails
    platformStats.value = {
      photographers: '0',
      cities: '0',
      visitors: '0',
      rating: '0'
    };
  }
};

const formatNumber = (num) => {
  // Show exact count for numbers under 1000
  if (num < 1000) {
    return num.toString();
  }
  // Format thousands with K
  if (num >= 1000 && num < 1000000) {
    const thousands = num / 1000;
    return thousands % 1 === 0 ? thousands + 'K' : thousands.toFixed(1) + 'K';
  }
  // Format millions with M
  if (num >= 1000000) {
    const millions = num / 1000000;
    return millions % 1 === 0 ? millions + 'M' : millions.toFixed(1) + 'M';
  }
  return num.toString();
};

const fetchCategories = async () => {
  try {
    const { data } = await api.get('/categories');
    if (data.status === 'success') {
      categories.value = data.data;
    }
  } catch (error) {
    if (import.meta.env.DEV) console.error('Error fetching categories:', error);
  }
};

const fetchCities = async () => {
  try {
    const { data } = await api.get('/locations');
    if (data.status === 'success') {
      const locations = data.data || [];
      cities.value = locations.filter(location => location.type !== 'division');
    }
  } catch (error) {
    if (import.meta.env.DEV) console.error('Error fetching locations:', error);
  }
};

const fetchPhotographers = async () => {
  loading.value = true;
  try {
    const params = new URLSearchParams();
    params.append('page', currentPage.value);
    
    if (searchQuery.value) params.append('q', searchQuery.value);
    if (filters.value.category) params.append('category', filters.value.category);
    if (filters.value.city) params.append('city', filters.value.city);
    if (filters.value.rating) params.append('min_rating', filters.value.rating);
    if (filters.value.sort) params.append('sort', filters.value.sort);

    const { data } = await api.get(`/photographers?${params}`);

    if (data.status === 'success') {
      photographers.value = data.data;
      totalPages.value = data.meta?.last_page || 1;
      currentPage.value = data.meta?.current_page || 1;
      totalPhotographers.value = data.meta?.total || data.data?.length || 0;
    }
  } catch (error) {
    if (import.meta.env.DEV) console.error('Error fetching photographers:', error);
    photographers.value = [];
  } finally {
    loading.value = false;
  }
};

const loadFavorites = async () => {
  if (!isClient.value) return;
  try {
    const response = await api.get('/client/favorites');
    const items = response.data.data || [];
    favoriteIds.value = items.map((favorite) => favorite.photographer_id);
  } catch (error) {
    if (import.meta.env.DEV) console.error('Error fetching favorites:', error);
  }
};

const toggleFavorite = async (photographerId) => {
  if (!isClient.value) {
    router.push('/auth');
    return;
  }

  const isFavorite = favoriteIdSet.value.has(photographerId);
  try {
    if (isFavorite) {
      await api.delete(`/client/favorites/${photographerId}`);
      favoriteIds.value = favoriteIds.value.filter((id) => id !== photographerId);
    } else {
      await api.post(`/client/favorites/${photographerId}`);
      favoriteIds.value = [...favoriteIds.value, photographerId];
    }
  } catch (error) {
    if (import.meta.env.DEV) console.error('Error toggling favorite:', error);
  }
};

const search = () => {
  currentPage.value = 1;
  fetchPhotographers();
};

// Debounced search for AJAX live search
let searchTimeout = null;
const debouncedSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    fetchPhotographers();
  }, 500); // Wait 500ms after user stops typing
};

const getCityName = (citySlug) => {
  const city = cities.value.find(c => c.slug === citySlug);
  return city ? city.name : '';
};

const fetchFeaturedEvents = async () => {
  try {
    const { data } = await api.get('/events/featured?limit=3');
    if (data.success) {
      featuredEvents.value = data.data;
    }
  } catch (error) {
    if (import.meta.env.DEV) console.error('Error fetching featured events:', error);
  }
};

const normalizeSlug = (value) => {
  return String(value || '')
    .trim()
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-|-$)/g, '');
};

const EVENT_TYPE_MAP = {
  photowalk: { label: 'PHOTOWALK', tone: 'bg-amber-100 text-amber-800' },
  workshop: { label: 'WORKSHOP', tone: 'bg-blue-100 text-blue-800' },
  expo: { label: 'EXPO', tone: 'bg-rose-100 text-rose-800' },
  exhibition: { label: 'EXPO', tone: 'bg-rose-100 text-rose-800' },
  seminar: { label: 'SEMINAR', tone: 'bg-emerald-100 text-emerald-800' },
  webinar: { label: 'WEBINAR', tone: 'bg-blue-100 text-blue-800' },
  meetup: { label: 'MEETUP', tone: 'bg-gray-100 text-gray-700' },
  competition: { label: 'COMPETITION', tone: 'bg-amber-100 text-amber-800' },
  other: { label: 'EVENT', tone: 'bg-gray-100 text-gray-700' }
};

const getEventTypeBadge = (event) => {
  const raw = event?.event_type || event?.type || event?.event_mode || '';
  const key = normalizeSlug(raw);
  if (EVENT_TYPE_MAP[key]) return EVENT_TYPE_MAP[key];
  if (!raw) return { label: 'EVENT', tone: 'bg-gray-100 text-gray-700' };
  return { label: String(raw).replace(/[_-]/g, ' ').toUpperCase(), tone: 'bg-gray-100 text-gray-700' };
};

const isPaidEvent = (event) => {
  if (!event) return false;
  if (typeof event.is_ticketed === 'boolean') return event.is_ticketed;
  if (typeof event.is_ticketed === 'number') return event.is_ticketed === 1;
  if (typeof event.is_ticketed === 'string') {
    const normalized = event.is_ticketed.trim().toLowerCase();
    return normalized === '1' || normalized === 'true';
  }
  if (event.event_mode) return event.event_mode === 'paid';
  if (event.event_type) return event.event_type === 'paid';
  const numeric = Number(event.ticket_price ?? event.price ?? event.base_price ?? 0);
  return Number.isFinite(numeric) && numeric > 0;
};

const isLimitedSeats = (event) => {
  if (!event) return false;
  if (event.is_limited_seats) return true;
  if (!event.max_attendees) return false;
  const remaining = event.max_attendees - (event.registered_count || 0);
  return remaining > 0 && remaining <= 10;
};

const getFeaturedBadge = (event) => {
  if (!event) return null;
  const isSponsored = Boolean(event.is_sponsored || event.sponsored);
  const isPromoted = Boolean(event.is_promoted || event.promoted);
  const isAdminFeatured = Boolean(event.is_admin_featured || event.admin_featured);
  const limited = isLimitedSeats(event);

  if (!isSponsored && !isPromoted && !isAdminFeatured && !limited) return null;
  if (limited) return { label: 'Limited Seats', tone: 'bg-amber-100 text-amber-800' };
  return { label: 'Featured', tone: 'bg-yellow-400 text-yellow-900' };
};

const getPriceBadge = (event) => {
  if (isPaidEvent(event)) {
    const amount = Number(event.ticket_price ?? event.price ?? event.base_price ?? 0);
    if (!Number.isFinite(amount) || amount <= 0) {
      return { label: 'Paid', tone: 'bg-burgundy text-white' };
    }
    const formatted = new Intl.NumberFormat('en-BD').format(amount);
    return { label: `৳${formatted}`, tone: 'bg-burgundy text-white' };
  }
  return { label: 'Free', tone: 'bg-green-600 text-white' };
};

const formatEventBadge = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  if (Number.isNaN(date.getTime())) return '';
  const month = formatMonthShort(dateString, true);
  return `${month} ${date.getDate()}`;
};

const formatDateLabel = (value) => {
  if (!value) return 'TBA';
  return formatDateValue(value) || 'TBA';
};

const formatTimeOnly = (value) => {
  if (!value) return '';
  if (typeof value === 'string') {
    const match = value.match(/^(\d{2}):(\d{2})/);
    if (match) return `${match[1]}:${match[2]}`;
  }
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return '';
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${hours}:${minutes}`;
};

const isMidnight = (value) => {
  if (!value || Number.isNaN(value.getTime())) return false;
  return value.getHours() === 0 && value.getMinutes() === 0 && value.getSeconds() === 0;
};

const formatTimeLabel = (timeValue, dateValue) => {
  const timeOnly = formatTimeOnly(timeValue);
  if (timeOnly) return timeOnly;
  if (!dateValue) return 'TBA';
  const raw = String(dateValue);
  if (!raw.includes(':') && raw.length <= 10) return 'TBA';
  const date = new Date(dateValue);
  if (Number.isNaN(date.getTime()) || isMidnight(date)) return 'TBA';
  return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
};

const truncateText = (value, maxLength = 110) => {
  if (!value) return '';
  const clean = String(value).replace(/\s+/g, ' ').trim();
  if (clean.length <= maxLength) return clean;
  const trimmed = clean.slice(0, maxLength);
  const safe = trimmed.slice(0, trimmed.lastIndexOf(' ') > 50 ? trimmed.lastIndexOf(' ') : trimmed.length);
  return `${safe}...`;
};

const getWebpSource = (url) => {
  if (!url || typeof url !== 'string') return '';
  if (url.startsWith('data:')) return '';
  const match = url.match(/\.(jpg|jpeg|png)(\?.*)?$/i);
  if (!match) return '';
  return url.replace(/\.(jpg|jpeg|png)(\?.*)?$/i, '.webp$2');
};

const getGalleryImage = (event) => {
  const gallery = event?.gallery_images;
  if (Array.isArray(gallery)) return gallery[0] || '';
  if (typeof gallery === 'string') {
    try {
      const parsed = JSON.parse(gallery);
      return Array.isArray(parsed) ? (parsed[0] || '') : '';
    } catch {
      return '';
    }
  }
  return '';
};

const getEventImage = (event) => {
  return event?.hero_image_url
    || event?.banner_image
    || event?.og_image
    || getGalleryImage(event)
    || '';
};

const hasFeaturedImageError = (id) => Boolean(featuredImageErrors.value[id]);

const markFeaturedImageError = (id) => {
  if (!id) return;
  featuredImageErrors.value = { ...featuredImageErrors.value, [id]: true };
};

const handleFeaturedImageError = (event, id) => {
  markFeaturedImageError(id);
  if (event?.target) {
    event.target.src = imagePlaceholder;
  }
};

const viewEventsByLocation = (event) => {
  const slug = event?.city?.slug || event?.city_slug || normalizeSlug(event?.city?.name || event?.location || '');
  if (!slug) return;
  router.push({ path: '/events', query: { location: slug } });
};

const fetchFeaturedCompetitions = async () => {
  try {
    const { data } = await api.get('/competitions?per_page=3&sort=featured-newest');
    if (data.status === 'success') {
      featuredCompetitions.value = data.data;
    }
  } catch (error) {
    if (import.meta.env.DEV) console.error('Error fetching featured competitions:', error);
  }
};

const formatPrize = (amount) => {
  if (!amount) return '0';
  if (amount >= 100000) {
    return (amount / 100000).toFixed(1) + 'L';
  }
  if (amount >= 1000) {
    return (amount / 1000).toFixed(1) + 'K';
  }
  return amount.toString();
};

const formatDeadline = (dateString) => {
  if (!dateString) return 'TBA';
  const date = new Date(dateString);
  const now = new Date();
  const diffTime = date - now;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  if (diffDays < 0) return 'Ended';
  if (diffDays === 0) return 'Today';
  if (diffDays === 1) return 'Tomorrow';
  if (diffDays <= 7) return `${diffDays} days left`;
  
  return formatDateValue(dateString);
};

const fetchFeaturedPhotographers = async () => {
  try {
    const { data } = await api.get('/featured-photographers?limit=3');
    if (data.status === 'success') {
      featuredPhotographers.value = data.data || [];
    }
  } catch (error) {
    if (import.meta.env.DEV) console.error('Error fetching featured photographers:', error);
  }
};

const isValidRating = (value) => {
  const numeric = Number(value)
  return Number.isFinite(numeric) && numeric > 0
}

const fallbackAvatar = imagePlaceholder;

const getPhotographerAvatar = (photographer) => {
  const raw = photographer?.profile_picture_url
    || photographer?.profile_picture
    || photographer?.avatar
    || '';
  if (!raw) return fallbackAvatar;
  if (raw.startsWith('http') || raw.startsWith('/') || raw.startsWith('data:')) return raw;
  const normalized = String(raw).replace(/^\/+/, '');
  if (normalized.startsWith('storage/')) {
    return `/${normalized}`;
  }
  return `/storage/${normalized}`;
};

const useDefaultAvatar = (event) => {
  event.target.src = fallbackAvatar;
};

const useImagePlaceholder = (event) => {
  event.target.src = imagePlaceholder;
};

const formatRating = (value) => {
  const numeric = Number(value)
  return Number.isFinite(numeric) ? numeric.toFixed(1) : '0.0'
}

const fetchTestimonials = async () => {
  try {
    // Fetch featured testimonials (public endpoint)
    const { data } = await api.get('/reviews/featured');
    if (data.status === 'success') {
      testimonials.value = data.data || [];
    }
  } catch (error) {
    if (import.meta.env.DEV) console.error('Error fetching testimonials:', error);
  }
};

const formatReviewDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const now = new Date();
  const diffTime = Math.abs(now - date);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  if (diffDays === 0) return 'Today';
  if (diffDays === 1) return 'Yesterday';
  if (diffDays < 7) return `${diffDays} days ago`;
  if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
  if (diffDays < 365) return `${Math.floor(diffDays / 30)} months ago`;
  
  return formatMonthYearShort(dateString);
};

const fetchTrendingHashtags = async () => {
  try {
    const { data } = await api.get('/hashtags/featured');
    if (data.status === 'success') {
      trendingHashtags.value = data.data || [];
    }
  } catch (error) {
    if (import.meta.env.DEV) console.error('Error fetching trending hashtags:', error);
  }
};

const formatHashtagCount = (count) => {
  if (!count) return '0';
  if (count >= 1000) {
    return (count / 1000).toFixed(1) + 'k';
  }
  return count.toString();
};

const getPublicSlug = (photographer) => {
  // Prefer username over slug for clean URLs
  return photographer?.user?.username || photographer?.username || photographer?.slug || '';
};

const getPublicProfilePath = (photographer) => {
  const username = photographer?.user?.username || photographer?.username;
  if (username) {
    return `/@${username}`;
  }
  // Fallback to slug if no username
  const slug = photographer?.slug;
  return slug ? `/photographer/${slug}` : '/photographers';
};

const viewPhotographer = (photographer) => {
  // Use public profile route with slug/username fallback
  window.location.href = getPublicProfilePath(photographer);
};

const bookPhotographer = (photographer) => {
  router.push(`/booking/${photographer.id}`);
};

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    fetchPhotographers();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
    fetchPhotographers();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

// Watch filters for changes (skip initial call since onMounted handles it)
watch(filters, () => {
  currentPage.value = 1;
  fetchPhotographers();
}, { deep: true, immediate: false });

onMounted(async () => {
  // Set SEO meta tags
  document.title = 'Find Professional Photographers in Bangladesh | PhotographerSB';
  const metaDescription = document.querySelector('meta[name="description"]');
  if (metaDescription) {
    metaDescription.setAttribute('content', 'Discover and hire professional photographers across Bangladesh. Browse portfolios, read reviews, and book photographers for weddings, events, portraits, and more.');
  }
  
  // Load essential data first
  await Promise.all([
    fetchPlatformStats(),
    fetchCategories(),
    fetchCities(),
    loadFavorites()
  ]);
  
  // Load main photographer list
  fetchPhotographers();
  
  // Load all featured sections in parallel (non-blocking)
  Promise.all([
    fetchFeaturedEvents(),
    fetchFeaturedCompetitions(),
    fetchFeaturedPhotographers(),
    fetchTestimonials(),
    fetchTrendingHashtags()
  ]).finally(() => {
    sectionsLoading.value = false;
  });
});
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slide-up {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.6s ease-out;
}

.animate-fade-in-delay {
  animation: fade-in 0.6s ease-out 0.2s both;
}

.animate-fade-in-delay-2 {
  animation: fade-in 0.6s ease-out 0.4s both;
}

.animate-fade-in-delay-3 {
  animation: fade-in 0.6s ease-out 0.6s both;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Mobile-Optimized Styles */
@media (max-width: 640px) {
  /* Reduce padding on very small screens */
  :deep() {
    .container {
      padding-left: 0.5rem;
      padding-right: 0.5rem;
    }
  }
  
  /* Optimize button touch targets */
  button {
    min-height: 44px;
    min-width: 44px;
  }

  /* Smooth scroll for better mobile UX */
  * {
    scroll-behavior: smooth;
  }
}

/* Accessibility: Focus states */
button:focus-visible {
  outline: 2px solid #6c0b1a;
  outline-offset: 2px;
}

select:focus-visible {
  outline: 2px solid #6c0b1a;
  outline-offset: 2px;
}

input:focus-visible {
  outline: 2px solid #6c0b1a;
  outline-offset: 2px;
}
</style>
