<template>
  <Toast
    v-if="toastVisible"
    :message="toastMessage"
    :type="toastType"
    @close="closeToast"
  />
  <div
    v-if="loading"
    class="min-h-screen flex items-center justify-center"
    role="status"
    aria-live="polite"
  >
    <div class="text-center">
      <div class="inline-block animate-spin rounded-full h-16 w-16 border-b-2 border-burgundy" />
      <p class="text-gray-600 mt-4">
        Loading event...
      </p>
    </div>
  </div>

  <div
    v-else-if="event"
    class="min-h-screen bg-[#f7f2ee] text-[#1d1014]"
  >
    <!-- Hero Banner -->
    <div class="relative h-80 sm:h-96 md:h-[34rem] overflow-hidden bg-[#1b0b12]">
      <picture v-if="heroImage && !heroImageError">
        <source
          v-if="getWebpSource(heroImage)"
          :srcset="getWebpSource(heroImage)"
          type="image/webp"
        >
        <img
          :src="heroImage"
          :alt="event.title"
          class="w-full h-full object-cover"
          decoding="async"
          loading="eager"
          fetchpriority="high"
          width="1600"
          height="900"
          @error="heroImageError = true"
        >
      </picture>
      <div
        v-else
        class="w-full h-full flex items-center justify-center"
      >
        <svg
          class="w-20 h-20 text-gray-500"
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
      </div>
      <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/35 to-transparent" />
      
      <div class="absolute inset-0">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 xl:px-12 max-w-7xl h-full flex flex-col justify-end py-8 sm:py-12 md:py-16">
          <div class="max-w-4xl">
            <div class="flex flex-wrap items-center gap-2 mb-2 sm:mb-3">
            <span
              class="px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-semibold uppercase"
              :class="eventTypeBadge.tone"
            >
              {{ eventTypeBadge.label }}
            </span>
            <span
              v-if="eventState"
              class="px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-semibold"
              :class="eventState.tone"
            >
              {{ eventState.label }}
            </span>
            <span
              v-if="featuredBadge"
              class="px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-semibold"
              :class="featuredBadge.tone"
            >
              {{ featuredBadge.label }}
            </span>
            <span
              v-if="isPaid"
              class="px-2 sm:px-3 py-1 bg-burgundy text-white rounded-full text-xs sm:text-sm font-semibold"
            >
              Paid
            </span>
            <span
              v-else
              class="px-2 sm:px-3 py-1 bg-green-600 text-white rounded-full text-xs sm:text-sm font-semibold"
            >
              Free
            </span>
            </div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-2">
              {{ event.title }}
            </h1>
            <p class="text-sm sm:text-base md:text-lg text-white/90">
              {{ formatDate(event.start_datetime || event.event_date) }} • {{ event.venue_name || event.venue || event.location_text || event.location || 'TBA' }}
            </p>
            <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
              <div class="bg-black/45 rounded-lg px-3 py-2">
                <p class="text-[10px] uppercase tracking-wide text-white/70">
                  Date
                </p>
                <p class="text-xs sm:text-sm font-semibold text-white">
                  {{ formatHeroDate(event.start_datetime || event.event_date) }}
                </p>
              </div>
              <div class="bg-black/45 rounded-lg px-3 py-2">
                <p class="text-[10px] uppercase tracking-wide text-white/70">
                  Time
                </p>
                <div class="text-xs sm:text-sm font-semibold text-white space-y-0.5">
                  <div>
                    <span class="text-white/70">Start:</span>
                    <span class="ml-1">{{ formatTimeLabel(event.start_time, event.start_datetime || event.event_date) }}</span>
                  </div>
                  <div>
                    <span class="text-white/70">End:</span>
                    <span class="ml-1">{{ formatTimeLabel(event.end_time, event.end_datetime || event.event_end_date) }}</span>
                  </div>
                </div>
              </div>
              <div class="bg-black/45 rounded-lg px-3 py-2">
                <p class="text-[10px] uppercase tracking-wide text-white/70">
                  Location
                </p>
                <p class="text-xs sm:text-sm font-semibold text-white truncate">
                  {{ event.venue_name || event.venue || event.location_text || event.location || 'TBA' }}
                </p>
              </div>
              <div class="bg-black/45 rounded-lg px-3 py-2">
                <p class="text-[10px] uppercase tracking-wide text-white/70">
                  Entry
                </p>
                <p class="text-xs sm:text-sm font-semibold text-white">
                  {{ isPaid ? formatPrice(priceValue, priceCurrency) : 'Free' }}
                </p>
              </div>
            </div>
            <p
              v-if="heroCredit"
              class="mt-2 text-xs text-white/80"
            >
              Photo by
              <a
                :href="heroCredit.url"
                target="_blank"
                rel="noopener"
                class="font-semibold text-white underline"
              >
                {{ heroCredit.name }}
              </a>
              on Pexels.
            </p>

            <div class="mt-5 flex flex-wrap items-center gap-2 sm:gap-3">
              <button
                :disabled="isEventFull"
                :class="[
                  'px-4 sm:px-5 py-2.5 rounded-full text-sm sm:text-base font-semibold transition-colors min-h-[42px]',
                  isEventFull
                    ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                    : 'bg-burgundy text-white hover:bg-rose-800'
                ]"
                @click="handleRsvp"
              >
                {{ isPaid ? 'Buy Tickets' : (isRsvped ? '✓ Registered' : 'Register') }}
              </button>
              <button
                class="px-4 sm:px-5 py-2.5 rounded-full text-sm sm:text-base font-semibold bg-white/10 border border-white/30 text-white hover:bg-white/20 transition-colors min-h-[42px]"
                @click="addToCalendar"
              >
                Add to calendar
              </button>
              <button
                class="px-4 sm:px-5 py-2.5 rounded-full text-sm sm:text-base font-semibold bg-white text-[#1b0b12] hover:bg-white/90 transition-colors min-h-[42px]"
                @click="copyLink"
              >
                Share event
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 xl:px-12 max-w-7xl py-8 sm:py-10 md:py-12">
      <div class="lg:grid lg:grid-cols-3 lg:gap-8">
        <!-- Left Column: Event Details -->
        <div class="lg:col-span-2 space-y-6 sm:space-y-8">
          <!-- Stats Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
            <div class="bg-white rounded-lg shadow-md p-3 sm:p-4 text-center">
              <div class="text-2xl sm:text-3xl font-bold text-burgundy">
                {{ event.registered_count || 0 }}
              </div>
              <div class="text-xs sm:text-sm text-gray-600 mt-1">
                Registrations
              </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-3 sm:p-4 text-center">
              <div class="text-2xl sm:text-3xl font-bold text-burgundy">
                {{ event.capacity_percent || 0 }}%
              </div>
              <div class="text-xs sm:text-sm text-gray-600 mt-1">
                Capacity Used
              </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-3 sm:p-4 text-center">
              <div class="text-2xl sm:text-3xl font-bold text-burgundy">
                {{ event.max_attendees || '∞' }}
              </div>
              <div class="text-xs sm:text-sm text-gray-600 mt-1">
                Capacity
              </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-3 sm:p-4 text-center">
              <div class="text-2xl sm:text-3xl font-bold text-burgundy">
                {{ isPaid ? formatPrice(priceValue, priceCurrency) : 'Free' }}
              </div>
              <div class="text-xs sm:text-sm text-gray-600 mt-1">
                Price
              </div>
            </div>
          </div>

          <!-- Description -->
          <div class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6">
            <h2 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4">
              About This Event
            </h2>
            <div class="prose prose-sm sm:prose max-w-none text-gray-700">
              <p class="whitespace-pre-wrap">
                {{ event.description }}
              </p>
            </div>
          </div>

          <!-- Event Essentials -->
          <div class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6">
            <h2 class="text-xl sm:text-2xl font-bold mb-4">
              Event Essentials
            </h2>
            <div class="space-y-3 sm:space-y-4">
              <!-- Date & Time -->
              <div class="flex items-start">
                <svg
                  class="w-5 h-5 sm:w-6 sm:h-6 text-burgundy mr-3 flex-shrink-0 mt-0.5"
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
                <div>
                  <p class="text-xs sm:text-sm text-gray-600">
                    Date & Time
                  </p>
                  <div class="font-semibold text-sm sm:text-base space-y-0.5">
                    <div>
                      <span class="text-gray-500">Date:</span>
                      <span class="ml-1">{{ formatDate(event.start_datetime || event.event_date) }}</span>
                    </div>
                    <div>
                      <span class="text-gray-500">Start:</span>
                      <span class="ml-1">{{ formatTimeLabel(event.start_time, event.start_datetime || event.event_date) }}</span>
                    </div>
                    <div>
                      <span class="text-gray-500">End:</span>
                      <span class="ml-1">{{ formatTimeLabel(event.end_time, event.end_datetime || event.event_end_date) }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Location -->
              <div class="flex items-start">
                <svg
                  class="w-5 h-5 sm:w-6 sm:h-6 text-burgundy mr-3 flex-shrink-0 mt-0.5"
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
                <div class="flex-1">
                  <p class="text-xs sm:text-sm text-gray-600">
                    Location
                  </p>
                  <button
                    type="button"
                    class="font-semibold text-sm sm:text-base text-left hover:text-burgundy focus:outline-none focus-visible:ring-2 focus-visible:ring-burgundy rounded"
                    @click="viewEventsByLocation"
                  >
                    {{ event.venue_name || event.venue || event.location_text || event.location || 'TBA' }}
                  </button>
                  <p
                    v-if="event.venue_address"
                    class="text-xs sm:text-sm text-gray-700 mt-1"
                  >
                    {{ event.venue_address }}
                  </p>
                  <p
                    v-else-if="event.address"
                    class="text-xs sm:text-sm text-gray-700 mt-1"
                  >
                    {{ event.address }}
                  </p>
                  <p
                    v-if="event.city"
                    class="text-xs sm:text-sm text-gray-600 mt-0.5"
                  >
                    {{ event.city.name }}
                  </p>
                </div>
              </div>

              <!-- Entry Type -->
              <div class="flex items-start">
                <svg
                  class="w-5 h-5 sm:w-6 sm:h-6 text-burgundy mr-3 flex-shrink-0 mt-0.5"
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
                <div>
                  <p class="text-xs sm:text-sm text-gray-600">
                    Entry Type
                  </p>
                  <p class="font-semibold text-sm sm:text-base">
                    {{ isPaid ? formatPrice(priceValue, priceCurrency) : 'Free' }}
                  </p>
                </div>
              </div>

              <!-- Seats Available -->
              <div class="flex items-start">
                <svg
                  class="w-5 h-5 sm:w-6 sm:h-6 text-burgundy mr-3 flex-shrink-0 mt-0.5"
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
                <div>
                  <p class="text-xs sm:text-sm text-gray-600">
                    Seats Available
                  </p>
                  <p class="font-semibold text-sm sm:text-base">
                    {{ spotsLeftText }}
                  </p>
                </div>
              </div>

              <!-- Duration -->
              <div
                v-if="event.duration_hours"
                class="flex items-start"
              >
                <svg
                  class="w-5 h-5 sm:w-6 sm:h-6 text-burgundy mr-3 flex-shrink-0 mt-0.5"
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
                <div>
                  <p class="text-xs sm:text-sm text-gray-600">
                    Duration
                  </p>
                  <p class="font-semibold text-sm sm:text-base">
                    {{ event.duration_hours }} hours
                  </p>
                </div>
              </div>

              <!-- Capacity -->
              <div
                v-if="event.max_attendees"
                class="flex items-start"
              >
                <svg
                  class="w-5 h-5 sm:w-6 sm:h-6 text-burgundy mr-3 flex-shrink-0 mt-0.5"
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
                <div>
                  <p class="text-xs sm:text-sm text-gray-600">
                    Capacity
                  </p>
                  <p class="font-semibold text-sm sm:text-base">
                    {{ event.max_attendees }} attendees
                  </p>
                  <p class="text-xs text-gray-500">
                    {{ event.registered_count || 0 }} registered
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Organizer -->
          <div
            v-if="event.organizer"
            class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6"
          >
            <h2 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4">
              Organizer
            </h2>
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 overflow-hidden">
                <img
                  v-if="event.organizer?.profile_image_url"
                  :src="event.organizer.profile_image_url"
                  :alt="event.organizer?.user?.name"
                  class="w-full h-full object-cover"
                  loading="lazy"
                  decoding="async"
                >
              </div>
              <div>
                <button
                  type="button"
                  class="font-semibold text-sm sm:text-base hover:text-burgundy focus:outline-none focus-visible:ring-2 focus-visible:ring-burgundy rounded"
                  @click="viewPhotographer"
                >
                  {{ event.organizer?.user?.name || event.organizer?.name || 'Organizer' }}
                </button>
                <p
                  v-if="event.organizer?.specialization"
                  class="text-xs sm:text-sm text-gray-600"
                >
                  {{ event.organizer.specialization }}
                </p>
              </div>
            </div>
          </div>

          <!-- Requirements (if any) -->
          <div
            v-if="event.requirements"
            class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6"
          >
            <h2 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4">
              Requirements
            </h2>
            <div class="prose prose-sm sm:prose max-w-none text-gray-700">
              <p class="whitespace-pre-wrap">
                {{ event.requirements }}
              </p>
            </div>
          </div>

          <!-- Schedule (if available) -->
          <div
            v-if="hasSchedule"
            class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6"
          >
            <h2 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4">
              Schedule
            </h2>
            <div
              v-if="scheduleItems.length"
              class="space-y-4 border-l border-gray-200 pl-4"
            >
              <div
                v-for="(item, index) in scheduleItems"
                :key="index"
                class="relative"
              >
                <span class="absolute -left-[9px] top-1.5 w-3 h-3 rounded-full bg-burgundy" />
                <p class="text-xs uppercase tracking-wide text-gray-500">
                  {{ item.time || 'Session' }}
                </p>
                <p class="font-semibold text-sm sm:text-base text-gray-900">
                  {{ item.title }}
                </p>
                <p
                  v-if="item.description"
                  class="text-xs sm:text-sm text-gray-600"
                >
                  {{ item.description }}
                </p>
              </div>
            </div>
            <p
              v-else
              class="text-sm text-gray-700 whitespace-pre-wrap"
            >
              {{ scheduleText }}
            </p>
          </div>

          <div
            v-if="event.mentors && event.mentors.length > 0"
            class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6"
          >
            <h2 class="text-xl sm:text-2xl font-bold mb-4">Mentors / Speakers</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div
                v-for="mentor in event.mentors"
                :key="mentor.id"
                class="border border-[#eadfd7] rounded-lg p-4 bg-[#fdf9f6]"
              >
                <div class="flex items-center gap-3">
                  <img
                    v-if="mentor.profile_photo_url"
                    :src="mentor.profile_photo_url"
                    :alt="mentor.name"
                    class="w-12 h-12 rounded-full object-cover"
                    loading="lazy"
                    decoding="async"
                  >
                  <div
                    v-else
                    class="w-12 h-12 rounded-full bg-[#efe5dc] flex items-center justify-center text-[#7a1f2b] font-bold"
                  >
                    {{ mentor.name?.charAt(0) || 'M' }}
                  </div>
                  <div>
                    <p class="font-semibold text-gray-900">{{ mentor.name || 'Mentor' }}</p>
                    <p class="text-xs text-gray-600">{{ mentor.title || mentor.expertise || 'Speaker' }}</p>
                  </div>
                </div>
                <p
                  v-if="mentor.bio"
                  class="text-sm text-gray-700 mt-3"
                >
                  {{ mentor.bio }}
                </p>
              </div>
            </div>
          </div>

          <div
            v-if="galleryImages.length > 0"
            class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6"
          >
            <h2 class="text-xl sm:text-2xl font-bold mb-4">Gallery</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
              <img
                v-for="(image, index) in galleryImages"
                :key="`${event.id}-gallery-${index}`"
                :src="image"
                :alt="`${event.title} gallery ${index + 1}`"
                class="w-full aspect-[4/3] object-cover rounded-lg"
                loading="lazy"
                decoding="async"
              >
            </div>
          </div>

          <div
            v-if="event.sponsors && event.sponsors.length > 0"
            class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6"
          >
            <h2 class="text-xl sm:text-2xl font-bold mb-4">Sponsors</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
              <component
                :is="sponsor.website_url || sponsor.website ? 'a' : 'div'"
                v-for="sponsor in event.sponsors"
                :key="sponsor.id"
                :href="sponsor.website_url || sponsor.website || undefined"
                target="_blank"
                rel="noopener"
                class="border border-[#eadfd7] rounded-lg p-3 bg-[#fdf9f6] flex flex-col items-center text-center"
              >
                <img
                  v-if="sponsor.logo_url || sponsor.logo"
                  :src="sponsor.logo_url || sponsor.logo"
                  :alt="sponsor.name"
                  class="w-full h-12 object-contain mb-2"
                  loading="lazy"
                  decoding="async"
                >
                <p class="text-sm font-semibold text-gray-900">{{ sponsor.name || 'Sponsor' }}</p>
              </component>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6">
            <h2 class="text-xl sm:text-2xl font-bold mb-4">Participants</h2>
            <div class="flex items-center justify-between mb-3">
              <p class="text-sm text-gray-600">Registered participants</p>
              <p class="text-lg font-semibold text-burgundy">{{ participantCount }}</p>
            </div>
            <div
              v-if="participantNames.length"
              class="flex flex-wrap gap-2"
            >
              <span
                v-for="(name, index) in participantNames"
                :key="`${name}-${index}`"
                class="px-3 py-1 rounded-full text-xs font-medium bg-[#efe5dc] text-[#7a1f2b]"
              >
                {{ name }}
              </span>
            </div>
            <p
              v-else
              class="text-sm text-gray-500"
            >
              Participants will appear as registrations are confirmed.
            </p>
          </div>

          <div class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6">
            <h2 class="text-xl sm:text-2xl font-bold mb-3">Certificate Availability</h2>
            <p
              :class="event.certificates_enabled ? 'text-emerald-700' : 'text-gray-600'"
              class="text-sm sm:text-base font-medium"
            >
              {{ event.certificates_enabled ? 'Digital participation certificates are available for attendees.' : 'Certificates are not enabled for this event.' }}
            </p>
          </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="mt-6 lg:mt-0 space-y-4 sm:space-y-6">
          <!-- RSVP Card -->
          <div class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6 sticky top-24">
            <div class="text-center mb-4 sm:mb-6">
              <div class="text-3xl sm:text-4xl font-bold text-burgundy mb-2">
                {{ isPaid ? formatPrice(priceValue, priceCurrency) : 'Free' }}
              </div>
              <p
                v-if="isPaid"
                class="text-xs sm:text-sm text-gray-600"
              >
                per person
              </p>
            </div>

            <button
              :disabled="isEventFull"
              :class="[
                'w-full py-3 sm:py-4 rounded-lg font-semibold text-base sm:text-lg transition-colors',
                isRsvped
                  ? 'bg-green-600 text-white hover:bg-green-700'
                  : isEventFull
                    ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                    : 'bg-burgundy text-white hover:bg-rose-800'
              ]"
              @click="handleRsvp"
            >
              {{
                isEventFull
                  ? 'Event Full'
                  : isPaid
                    ? 'Buy Tickets'
                    : isRsvped
                      ? '✓ Registered'
                      : 'Register Now'
              }}
            </button>

            <div
              v-if="!isEventFull"
              class="mt-3 sm:mt-4 text-center text-xs sm:text-sm text-gray-600"
            >
              {{ spotsLeftText }}
            </div>
          </div>

          <!-- Organizer Card -->
          <div class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6">
            <h3 class="text-base sm:text-lg font-bold mb-3 sm:mb-4">
              Organized By
            </h3>
            <div class="flex items-center gap-3 mb-3 sm:mb-4">
              <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full object-cover overflow-hidden bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center">
                <img
                  v-if="event.organizer?.profile_image_url"
                  :src="event.organizer?.profile_image_url"
                  :alt="event.organizer?.user?.name"
                  class="w-full h-full object-cover"
                  loading="lazy"
                  decoding="async"
                >
                <svg
                  v-else
                  class="w-6 h-6 sm:w-8 sm:h-8 text-gray-500"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div>
                <p class="font-semibold text-sm sm:text-base">
                  {{ event.organizer?.user?.name }}
                </p>
                <p
                  v-if="event.organizer?.specialization"
                  class="text-xs sm:text-sm text-gray-600"
                >
                  {{ event.organizer.specialization }}
                </p>
              </div>
            </div>
            <button
              class="w-full py-2 border-2 border-burgundy text-burgundy rounded-lg hover:bg-burgundy hover:text-white transition-colors text-sm sm:text-base font-medium"
              @click="viewPhotographer"
            >
              View Profile
            </button>
          </div>

          <!-- Map Card -->
          <div
            v-if="mapLink"
            class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6"
          >
            <h3 class="text-base sm:text-lg font-bold mb-3 sm:mb-4">
              Location Map
            </h3>
            <p class="text-xs sm:text-sm text-gray-600 mb-3">
              Open the venue in Google Maps for directions.
            </p>
            <a
              :href="mapLink"
              target="_blank"
              rel="noopener"
              class="inline-flex items-center justify-center w-full min-h-[44px] px-4 py-2 rounded-lg border border-burgundy text-burgundy font-semibold hover:bg-burgundy hover:text-white transition-colors"
            >
              Open Map
            </a>
          </div>

          <!-- Share Card -->
          <div class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6">
            <h3 class="text-base sm:text-lg font-bold mb-3 sm:mb-4">
              Share Event
            </h3>
            <div class="flex gap-2 sm:gap-3">
              <button
                class="flex-1 p-2 sm:p-3 border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-500 transition-colors"
                title="Share on Facebook"
                aria-label="Share this event on Facebook"
                @click="shareOnFacebook"
              >
                <svg
                  class="w-5 h-5 sm:w-6 sm:h-6 mx-auto text-blue-600"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                </svg>
              </button>
              <button
                class="flex-1 p-2 sm:p-3 border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-colors"
                title="Share on WhatsApp"
                aria-label="Share this event on WhatsApp"
                @click="shareOnWhatsApp"
              >
                <svg
                  class="w-5 h-5 sm:w-6 sm:h-6 mx-auto text-blue-400"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.174.199-.347.223-.645.075-.297-.15-1.255-.463-2.39-1.475-.883-.787-1.48-1.76-1.653-2.058-.174-.298-.018-.458.13-.606.134-.133.298-.348.446-.52.149-.174.198-.298.298-.497.099-.198.05-.372-.025-.521-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.372-.01-.57-.01-.198 0-.52.075-.792.372-.272.298-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.077 4.487.71.306 1.263.489 1.694.626.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a8.41 8.41 0 01-4.281-1.175l-.307-.182-3.183.833.85-3.102-.2-.318a8.377 8.377 0 01-1.281-4.446c.002-4.627 3.773-8.396 8.403-8.396 2.242 0 4.347.873 5.933 2.46a8.345 8.345 0 012.458 5.93c-.003 4.628-3.774 8.397-8.404 8.397m7.188-15.615A10.166 10.166 0 0012.05 3c-5.62 0-10.194 4.572-10.196 10.19a10.13 10.13 0 001.357 5.077L1 23l4.876-1.28a10.2 10.2 0 004.874 1.24h.004c5.62 0 10.194-4.572 10.197-10.19a10.132 10.132 0 00-2.958-7.2" />
                </svg>
              </button>
              <button
                class="flex-1 p-2 sm:p-3 border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-500 transition-colors"
                title="Share on LinkedIn"
                aria-label="Share this event on LinkedIn"
                @click="shareOnLinkedIn"
              >
                <svg
                  class="w-5 h-5 sm:w-6 sm:h-6 mx-auto text-blue-700"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path d="M20.447 20.452H16.89V14.87c0-1.33-.028-3.042-1.854-3.042-1.854 0-2.137 1.447-2.137 2.944v5.68H9.343V9h3.414v1.561h.049c.476-.9 1.637-1.85 3.37-1.85 3.605 0 4.271 2.372 4.271 5.456v6.285zM5.337 7.433a2.062 2.062 0 11.001-4.124 2.062 2.062 0 01-.001 4.124zM7.119 20.452H3.555V9h3.564v11.452z" />
                </svg>
              </button>
              <button
                class="flex-1 p-2 sm:p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                title="Copy link"
                aria-label="Copy event link"
                @click="copyLink"
              >
                <svg
                  class="w-5 h-5 sm:w-6 sm:h-6 mx-auto text-gray-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div
    v-else
    class="min-h-screen flex items-center justify-center"
  >
    <div class="text-center">
      <h2 class="text-2xl font-bold text-gray-800 mb-2">
        Event Not Found
      </h2>
      <p class="text-gray-600 mb-4">
        The event you're looking for doesn't exist
      </p>
      <button
        class="px-6 py-2 bg-burgundy text-white rounded-lg hover:bg-rose-800"
        @click="$router.push('/events')"
      >
        Back to Events
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import Toast from '../components/ui/Toast.vue';
import { useApiError } from '../composables/useApiError';
import api from '../api';
import {
  formatDate as formatDateValue,
  formatDateTime as formatDateTimeValue,
  formatNumber
} from '../utils/formatters';

const router = useRouter();
const route = useRoute();

const {
  toastMessage,
  toastType,
  toastVisible,
  showToast,
  closeToast,
  handleApiError,
} = useApiError();

// State
const event = ref(null);
const loading = ref(true);
const isRsvped = ref(false);
const heroImageError = ref(false);

// Computed
const isEventFull = computed(() => {
  if (!event.value) return false;
  if (typeof event.value.capacity_full === 'boolean') return event.value.capacity_full;
  if (!event.value.max_attendees) return false;
  return (event.value.registered_count || 0) >= event.value.max_attendees;
});

const isPaid = computed(() => {
  if (!event.value) return false;
  if (typeof event.value.is_paid === 'boolean') return event.value.is_paid;
  if (event.value.event_mode) return event.value.event_mode === 'paid';
  if (event.value.event_type) return event.value.event_type === 'paid';
  if (typeof event.value.is_ticketed === 'boolean') return event.value.is_ticketed;
  if (typeof event.value.is_ticketed === 'number') return event.value.is_ticketed === 1;
  if (typeof event.value.is_ticketed === 'string') {
    const normalized = event.value.is_ticketed.trim().toLowerCase();
    return normalized === '1' || normalized === 'true';
  }

  const numeric = Number(
    event.value.ticket_price ?? event.value.price ?? event.value.base_price ?? 0
  );
  return Number.isFinite(numeric) && numeric > 0;
});

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

const normalizeSlug = (value) => {
  return String(value || '')
    .trim()
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-|-$)/g, '');
};

const eventTypeBadge = computed(() => {
  if (!event.value) return { label: 'EVENT', tone: 'bg-gray-100 text-gray-700' };
  const raw = event.value.type || event.value.event_type || event.value.event_mode || '';
  const key = normalizeSlug(raw);
  if (EVENT_TYPE_MAP[key]) return EVENT_TYPE_MAP[key];
  if (!raw) return { label: 'EVENT', tone: 'bg-gray-100 text-gray-700' };
  return { label: String(raw).replace(/[_-]/g, ' ').toUpperCase(), tone: 'bg-gray-100 text-gray-700' };
});

const priceValue = computed(() => {
  if (!event.value) return 0;

  if (Array.isArray(event.value.tickets) && event.value.tickets.length > 0) {
    const prices = event.value.tickets
      .map((ticket) => Number(ticket.price))
      .filter((amount) => Number.isFinite(amount));
    if (prices.length) return Math.min(...prices);
  }

  const direct = Number(
    event.value.ticket_price ?? event.value.price ?? event.value.base_price ?? 0
  );
  return Number.isFinite(direct) ? direct : 0;
});

const priceCurrency = computed(() => event.value?.currency || 'BDT');

const spotsLeftText = computed(() => {
  if (!event.value) return '';

  if (Array.isArray(event.value.tickets) && event.value.tickets.length > 0) {
    const totalAvailable = event.value.tickets.reduce((sum, ticket) => {
      const available = Number(ticket.available_quantity ?? ticket.quantity ?? 0);
      return sum + (Number.isFinite(available) ? available : 0);
    }, 0);
    return `${totalAvailable} spots left`;
  }

  if (event.value.max_attendees) {
    const remaining = event.value.max_attendees - (event.value.registered_count || 0);
    return `${Math.max(remaining, 0)} spots left`;
  }

  return 'Unlimited spots';
});

const getGalleryImage = (eventValue) => {
  if (!eventValue) return null;
  const gallery = eventValue.gallery_images;
  if (Array.isArray(gallery)) return gallery[0] || null;
  if (typeof gallery === 'string') {
    try {
      const parsed = JSON.parse(gallery);
      return Array.isArray(parsed) ? (parsed[0] || null) : null;
    } catch {
      return null;
    }
  }
  return null;
};

const heroImage = computed(() => {
  if (!event.value) return null;
  return event.value.hero_image_url
    || event.value.banner_image
    || event.value.og_image
    || getGalleryImage(event.value)
    || null;
});

const galleryImages = computed(() => {
  if (!event.value?.gallery_images) return [];
  if (Array.isArray(event.value.gallery_images)) {
    return event.value.gallery_images.filter(Boolean).slice(0, 12);
  }
  if (typeof event.value.gallery_images === 'string') {
    try {
      const parsed = JSON.parse(event.value.gallery_images);
      return Array.isArray(parsed) ? parsed.filter(Boolean).slice(0, 12) : [];
    } catch {
      return [];
    }
  }
  return [];
});

const participantCount = computed(() => {
  if (!event.value) return 0;
  const total = Number(event.value.registered_count);
  if (Number.isFinite(total)) return total;
  return Array.isArray(event.value.registrations) ? event.value.registrations.length : 0;
});

const participantNames = computed(() => {
  if (!Array.isArray(event.value?.registrations)) return [];
  return event.value.registrations
    .map((registration) => registration?.user?.name || registration?.name || registration?.participant_name)
    .filter(Boolean)
    .slice(0, 10);
});

const eventLocationSlug = computed(() => {
  if (!event.value) return '';
  return event.value.city?.slug
    || event.value.city_slug
    || normalizeSlug(
      event.value.city?.name
      || event.value.location_text
      || event.value.location
      || event.value.venue_name
      || event.value.venue
      || ''
    );
});

const heroCredit = computed(() => {
  if (!event.value) return null;
  if (event.value.hero_image_url && event.value.hero_image_credit_name) {
    return {
      name: event.value.hero_image_credit_name,
      url: event.value.hero_image_credit_url || 'https://www.pexels.com',
    };
  }
  if (!event.value.hero_image_url && event.value.banner_image && event.value.banner_image_credit_name) {
    return {
      name: event.value.banner_image_credit_name,
      url: event.value.banner_image_credit_url || 'https://www.pexels.com',
    };
  }
  return null;
});

const eventState = computed(() => {
  if (!event.value) return null;
  if (event.value.status === 'cancelled') {
    return { label: 'Cancelled', tone: 'bg-red-100 text-red-800' };
  }

  const sourceDate = event.value.start_datetime || event.value.event_date;
  if (!sourceDate) {
    return null;
  }

  const toDateKey = (value) => {
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return null;
    return date.toISOString().slice(0, 10);
  };

  const todayKey = toDateKey(new Date());
  const eventKey = toDateKey(sourceDate);
  if (!todayKey || !eventKey) return null;

  if (eventKey === todayKey) {
    return { label: 'Ongoing', tone: 'bg-green-100 text-green-800' };
  }
  if (eventKey > todayKey) {
    return { label: 'Upcoming', tone: 'bg-blue-100 text-blue-800' };
  }
  return { label: 'Ended', tone: 'bg-gray-100 text-gray-700' };
});

const featuredBadge = computed(() => {
  if (!event.value) return null;
  const isSponsored = Boolean(event.value.is_sponsored || event.value.sponsored);
  const isPromoted = Boolean(event.value.is_promoted || event.value.promoted);
  const isAdminFeatured = Boolean(event.value.is_admin_featured || event.value.admin_featured);
  const limitedSeats = isLimitedSeats(event.value);

  if (!isSponsored && !isPromoted && !isAdminFeatured && !limitedSeats) return null;
  if (limitedSeats) return { label: 'Limited Seats', tone: 'bg-amber-100 text-amber-800' };
  return { label: 'Featured', tone: 'bg-yellow-400 text-yellow-900' };
});

const scheduleItems = computed(() => {
  if (!event.value) return [];
  const items = event.value.schedule_items || event.value.schedule || event.value.timeline || [];
  if (Array.isArray(items)) {
    return items
      .map((item) => {
        if (typeof item === 'string') {
          return { title: item.trim() };
        }
        if (item && typeof item === 'object') {
          return {
            time: item.time || item.start_time || item.slot || '',
            title: item.title || item.name || item.topic || 'Session',
            description: item.description || item.details || ''
          };
        }
        return null;
      })
      .filter(Boolean);
  }
  return [];
});

const scheduleText = computed(() => {
  if (!event.value) return '';
  const schedule = event.value.schedule_text || event.value.schedule_notes || event.value.schedule;
  if (Array.isArray(schedule)) return '';
  return schedule ? String(schedule) : '';
});

const hasSchedule = computed(() => scheduleItems.value.length > 0 || Boolean(scheduleText.value));

const mapLink = computed(() => {
  if (!event.value) return '';
  const parts = [
    event.value.venue_name,
    event.value.venue,
    event.value.venue_address,
    event.value.address,
    event.value.city?.name,
    event.value.location_text,
    event.value.location
  ].filter(Boolean);
  if (!parts.length) return '';
  return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(parts.join(', '))}`;
});

// Methods
const fetchEvent = async () => {
  loading.value = true;
  try {
    const slug = route.params.slug;
    const { data } = await api.get(`/events/${slug}`);

    if (data.event || data.data) {
      event.value = data.event || data.data;
      heroImageError.value = false;
      const registration = event.value?.user_registration;
      isRsvped.value = registration && registration.rsvp_status === 'going';
      updateSeoMeta(event.value);
    }
  } catch (error) {
    handleApiError(error, 'Failed to load event');
    event.value = null;
  } finally {
    loading.value = false;
  }
};

const handleRsvp = async () => {
  if (!localStorage.getItem('user')) {
    router.push('/auth');
    return;
  }

  if (isEventFull.value) return;

  if (isPaid.value) {
    router.push(`/events/${event.value?.slug}/tickets`);
    return;
  }

  try {
    const rsvpStatus = isRsvped.value ? 'not_going' : 'going';
    const { data } = await api.post(`/events/${event.value.id}/rsvp`);

    if (data.status === 'success') {
      isRsvped.value = !isRsvped.value;
      if (isRsvped.value) {
        event.value.registered_count = (event.value.registered_count || 0) + 1;
      } else {
        event.value.registered_count = Math.max((event.value.registered_count || 1) - 1, 0);
      }
    }
  } catch (error) {
    handleApiError(error, 'Failed to update RSVP');
  }
};

const viewPhotographer = () => {
  if (event.value?.organizer?.slug) {
    router.push(`/photographers/${event.value.organizer.slug}`);
  }
};

const viewEventsByLocation = () => {
  if (!eventLocationSlug.value) return;
  router.push({ path: '/events', query: { location: eventLocationSlug.value } });
};

const shareOnFacebook = () => {
  const url = encodeURIComponent(window.location.href);
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
  trackShareLog('facebook');
};

const shareOnWhatsApp = () => {
  const url = encodeURIComponent(window.location.href);
  const text = encodeURIComponent(event.value?.title || 'Check out this event');
  window.open(`https://wa.me/?text=${text}%20${url}`, '_blank');
  trackShareLog('whatsapp');
};

const shareOnLinkedIn = () => {
  const url = encodeURIComponent(window.location.href);
  window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank');
  trackShareLog('linkedin');
};

const copyLink = () => {
  navigator.clipboard.writeText(window.location.href);
  showToast('Link copied to clipboard!', 'success');
  trackShareLog('copy');
};

const trackShareLog = async (platform) => {
  try {
    await api.post('/growth/share-log', {
      entity_type: 'event',
      entity_id: event.value?.id || null,
      platform,
    });
  } catch (error) {
    console.warn('Share log failed:', error);
  }
};

const addToCalendar = () => {
  if (!event.value) return;

  const dateFrom = event.value.start_datetime || event.value.event_date;
  const dateTo = event.value.end_datetime || event.value.event_end_date || dateFrom;
  if (!dateFrom) return;

  const formatForGoogle = (value) => {
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '';
    return date.toISOString().replace(/[-:]/g, '').replace(/\.\d{3}Z$/, 'Z');
  };

  const start = formatForGoogle(dateFrom);
  const end = formatForGoogle(dateTo);
  const details = encodeURIComponent(event.value.description || 'Photography event');
  const title = encodeURIComponent(event.value.title || 'Event');
  const location = encodeURIComponent(event.value.venue_name || event.value.venue || event.value.location_text || event.value.location || '');

  const calendarUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${title}&details=${details}&location=${location}&dates=${start}/${end}`;
  window.open(calendarUrl, '_blank');
};

const formatDate = (date) => {
  if (!date) return 'TBA';
  return formatDateValue(date);
};

const formatHeroDate = (date) => {
  if (!date) return 'TBA';
  const value = new Date(date);
  if (Number.isNaN(value.getTime())) return 'TBA';
  return value.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
};

const formatTimeLabel = (timeValue, dateValue) => {
  const timeOnly = formatTimeOnly(timeValue);
  if (timeOnly) return timeOnly;
  if (!dateValue) return 'TBA';
  const raw = String(dateValue);
  if (!raw.includes(':') && raw.length <= 10) return 'TBA';
  const value = new Date(dateValue);
  if (Number.isNaN(value.getTime()) || isMidnight(value)) return 'TBA';
  return value.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
};

const formatDateTime = (date) => {
  if (!date) return 'TBA';
  return formatDateTimeValue(date);
};

const formatDateTimeRange = (start, end, startTime, endTime) => {
  if (!start && !end && !startTime && !endTime) return 'TBA';

  const dateValue = start || end;
  const datePart = formatDateValue(dateValue) || 'TBA';
  const startPart = formatTimeOnly(startTime);
  const endPart = formatTimeOnly(endTime);

  if (startPart && endPart) {
    return `${datePart} ${startPart} - ${endPart}`;
  }
  if (startPart) {
    return `${datePart} ${startPart}`;
  }
  if (start && end) {
    if (isMidnight(new Date(start)) && isMidnight(new Date(end))) {
      return datePart;
    }
    return `${formatDateTime(start)} - ${formatDateTime(end)}`;
  }
  if (dateValue && isMidnight(new Date(dateValue))) {
    return datePart;
  }
  return formatDateTime(start || end);
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

const formatPrice = (price, currency = 'BDT') => {
  if (price === null || price === undefined) return `৳0`;
  const amount = Number(price) || 0;
  if (currency === 'BDT') {
    return `৳${formatNumber(amount)}`;
  }
  return `${currency} ${formatNumber(amount)}`;
};

const isLimitedSeats = (eventValue) => {
  if (!eventValue) return false;
  if (eventValue.is_limited_seats) return true;
  if (!eventValue.max_attendees) return false;
  const remaining = eventValue.max_attendees - (eventValue.registered_count || 0);
  return remaining > 0 && remaining <= 10;
};

const getWebpSource = (url) => {
  if (!url || typeof url !== 'string') return '';
  if (url.startsWith('data:')) return '';
  const match = url.match(/\.(jpg|jpeg|png)(\?.*)?$/i);
  if (!match) return '';
  return url.replace(/\.(jpg|jpeg|png)(\?.*)?$/i, '.webp$2');
};

const truncateText = (value, maxLength = 155) => {
  if (!value) return '';
  const clean = String(value).replace(/\s+/g, ' ').trim();
  if (clean.length <= maxLength) return clean;
  const trimmed = clean.slice(0, maxLength);
  const safe = trimmed.slice(0, trimmed.lastIndexOf(' ') > 60 ? trimmed.lastIndexOf(' ') : trimmed.length);
  return `${safe}...`;
};

const resolveUrl = (value) => {
  if (!value) return '';
  if (/^https?:\/\//i.test(value)) return value;
  return `${window.location.origin}${value.startsWith('/') ? '' : '/'}${value}`;
};

const setMetaTag = (attribute, key, content) => {
  if (!content) return;
  let tag = document.head.querySelector(`meta[${attribute}="${key}"]`);
  if (!tag) {
    tag = document.createElement('meta');
    tag.setAttribute(attribute, key);
    document.head.appendChild(tag);
  }
  tag.setAttribute('content', content);
};

const setLinkTag = (rel, href) => {
  if (!href) return;
  let tag = document.head.querySelector(`link[rel="${rel}"]`);
  if (!tag) {
    tag = document.createElement('link');
    tag.setAttribute('rel', rel);
    document.head.appendChild(tag);
  }
  tag.setAttribute('href', href);
};

const setJsonLd = (data) => {
  if (!data) return;
  let script = document.head.querySelector('script[data-event-schema="true"]');
  if (!script) {
    script = document.createElement('script');
    script.type = 'application/ld+json';
    script.setAttribute('data-event-schema', 'true');
    document.head.appendChild(script);
  }
  script.textContent = JSON.stringify(data);
};

const updateSeoMeta = (eventValue) => {
  if (!eventValue || typeof window === 'undefined') return;
  const title = eventValue.title || 'Photography Event';
  const description = truncateText(eventValue.description || 'Join this photography event.');
  const canonicalUrl = `${window.location.origin}${route.fullPath}`;
  const imageUrl = resolveUrl(heroImage.value || eventValue.banner_image || '/images/placeholder.svg');

  document.title = `${title} | Photography Events`;
  setMetaTag('name', 'description', description);
  setMetaTag('property', 'og:title', title);
  setMetaTag('property', 'og:description', description);
  setMetaTag('property', 'og:image', imageUrl);
  setMetaTag('property', 'og:url', canonicalUrl);
  setMetaTag('property', 'og:type', 'event');
  setMetaTag('property', 'og:site_name', 'Photographar');
  setMetaTag('name', 'twitter:card', 'summary_large_image');
  setMetaTag('name', 'twitter:title', title);
  setMetaTag('name', 'twitter:description', description);
  setMetaTag('name', 'twitter:image', imageUrl);
  setLinkTag('canonical', canonicalUrl);

  const locationName = eventValue.venue_name || eventValue.venue || eventValue.location_text || eventValue.location || 'TBA';
  const schema = {
    '@context': 'https://schema.org',
    '@type': 'Event',
    name: title,
    startDate: eventValue.start_datetime || eventValue.event_date || undefined,
    endDate: eventValue.end_datetime || eventValue.event_end_date || undefined,
    eventStatus: eventValue.status === 'cancelled'
      ? 'https://schema.org/EventCancelled'
      : 'https://schema.org/EventScheduled',
    eventAttendanceMode: 'https://schema.org/OfflineEventAttendanceMode',
    eventType: eventTypeBadge.value.label,
    image: imageUrl,
    location: {
      '@type': 'Place',
      name: locationName,
      address: eventValue.venue_address || eventValue.address || eventValue.city?.name || undefined
    },
    organizer: {
      '@type': 'Organization',
      name: eventValue.organizer?.user?.name || eventValue.organizer?.name || 'Organizer',
      url: eventValue.organizer?.slug
        ? `${window.location.origin}/photographers/${eventValue.organizer.slug}`
        : undefined
    },
    offers: {
      '@type': 'Offer',
      price: isPaid.value ? String(priceValue.value || 0) : '0',
      priceCurrency: priceCurrency.value,
      availability: isEventFull.value
        ? 'https://schema.org/SoldOut'
        : 'https://schema.org/InStock'
    }
  };

  setJsonLd(schema);
};

// Lifecycle
onMounted(() => {
  fetchEvent();
});
</script>
