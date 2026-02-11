<template>
  <div class="min-h-screen bg-[#f7f2ee] text-[#1d1014]">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-[#1b0b12]">
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(245,158,11,0.28)_0,_transparent_55%)]" />
      <div class="absolute -top-24 -right-24 h-72 w-72 rounded-full bg-[#f3b35a]/30 blur-3xl" />
      <div class="absolute -bottom-28 -left-24 h-80 w-80 rounded-full bg-[#c46b7a]/20 blur-3xl" />
      <div class="container mx-auto px-4 relative pt-8 pb-24">
        <button
          class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-medium text-white/90 transition hover:bg-white/20"
          @click="$router.back()"
        >
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
              d="M10 19l-7-7m0 0l7-7m-7 7h18"
            />
          </svg>
          Back
        </button>

        <div class="mt-10 max-w-4xl text-white">
          <p class="text-xs uppercase tracking-[0.35em] text-white/70">
            {{ photographer?.is_featured ? 'Featured Photographer' : 'Photographer' }}
          </p>
          <h1 class="mt-4 text-4xl md:text-5xl font-semibold font-serif tracking-tight">
            {{ photographer?.user?.name || photographer?.business_name || 'Photographer' }}
          </h1>
          <p class="mt-3 text-base text-white/80">
            {{ shortBio }}
          </p>
          <div class="mt-6 flex flex-wrap gap-3 text-sm">
            <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-white/90">
              <svg class="w-4 h-4 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
              </svg>
              {{ photographer?.average_rating || '0.0' }} rating
            </span>
            <span
              v-if="photographer?.is_verified"
              class="inline-flex items-center gap-2 rounded-full bg-emerald-400/20 px-4 py-2 text-emerald-200"
            >
              Verified
            </span>
            <span
              v-if="photographer?.response_rate"
              class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-white/80"
            >
              {{ photographer.response_rate }}% response rate
            </span>
            <span
              v-if="photographer?.completed_bookings"
              class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-white/80"
            >
              {{ formatNumber(photographer.completed_bookings) }} completed
            </span>
          </div>
        </div>
      </div>
    </div>

    <div class="container mx-auto px-4 max-w-7xl -mt-20 relative z-10 pb-12">
      <div
        v-if="loading"
        class="text-center py-20 bg-white rounded-lg shadow-lg"
      >
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-burgundy mx-auto mb-4" />
        <p class="text-gray-600">
          Loading profile...
        </p>
      </div>

      <div
        v-else-if="!photographer"
        class="text-center py-20 bg-white rounded-lg shadow-lg"
      >
        <svg
          class="w-16 h-16 text-gray-400 mx-auto mb-4"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>
        <p class="text-xl text-gray-600 mb-4">
          Photographer not found
        </p>
        <button
          class="text-burgundy hover:underline font-medium"
          @click="$router.push('/')"
        >
          Return to Home
        </button>
      </div>

      <div
        v-else
        class="grid grid-cols-1 lg:grid-cols-3 gap-6"
      >
        <!-- Left Sidebar -->
        <div class="lg:col-span-1">
          <!-- Profile Card -->
          <div class="bg-white/90 backdrop-blur rounded-2xl shadow-xl border border-[#eadfd7] overflow-hidden sticky top-6 transition-transform duration-300 hover:-translate-y-1">
            <div class="p-6 text-center">
              <p class="text-[11px] uppercase tracking-[0.4em] text-[#7a1f2b] mb-4">
                Profile Snapshot
              </p>
              <img
                :src="profileImage"
                :alt="photographer.user?.name || 'Photographer'"
                class="w-28 h-28 rounded-full mx-auto mb-3 object-cover border-4 border-[#7a1f2b]/60 shadow-lg"
                loading="lazy"
                decoding="async"
              >
              <h2 class="text-xl font-semibold font-serif text-gray-900 mb-2">
                {{ photographer.user?.name || photographer.business_name || 'Unknown' }}
              </h2>
              
              <!-- Location & Category Snapshot -->
              <div class="flex flex-wrap items-center justify-center gap-2 mb-3">
                <span
                  v-if="locationLabel"
                  class="inline-flex items-center gap-1 bg-[#1b0b12]/10 text-[#1b0b12] px-3 py-1 rounded-full text-xs font-semibold"
                >
                  <svg
                    class="w-3.5 h-3.5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path d="M5.05 3.05a7 7 0 019.9 9.9L10 18l-4.95-5.05a7 7 0 010-9.9z" />
                  </svg>
                  {{ locationLabel }}
                </span>
                <span
                  v-if="photographer.categories && photographer.categories.length > 0"
                  class="inline-flex items-center gap-1 bg-[#7a1f2b]/10 text-[#7a1f2b] px-3 py-1 rounded-full text-xs font-bold"
                >
                  <svg
                    class="w-3.5 h-3.5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                  </svg>
                  {{ photographer.categories[0].name }}
                  <span
                    v-if="photographer.categories.length > 1"
                    class="ml-0.5"
                  >+{{ photographer.categories.length - 1 }}</span>
                </span>
                <span
                  v-if="serviceRadiusLabel"
                  class="inline-flex items-center gap-1 bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-xs font-semibold"
                >
                  <svg
                    class="w-3.5 h-3.5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 8.414l2.95 2.95-1.414 1.414-3.536-3.536V5h2v5.414z" />
                  </svg>
                  {{ serviceRadiusLabel }}
                </span>
              </div>
              
              <!-- Level Badge -->
              <div
                v-if="photographer.achievements"
                class="flex items-center justify-center gap-2 mb-2"
              >
                <div class="inline-flex items-center gap-1 bg-gradient-to-r from-purple-500 to-blue-500 text-white px-3 py-1 rounded-full text-sm font-bold shadow-md">
                  <svg
                    class="w-4 h-4"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  Level {{ photographer.achievements.level }} • {{ photographer.achievements.total_points }} pts
                </div>
              </div>
              
              <div class="flex items-center justify-center gap-2 mb-3">
                <div class="flex items-center gap-1">
                  <svg
                    class="w-5 h-5 text-yellow-400 fill-current"
                    viewBox="0 0 20 20"
                  >
                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                  </svg>
                  <span class="font-bold text-gray-900">{{ photographer.average_rating || '0.0' }}</span>
                  <span class="text-gray-600 text-sm">({{ photographer.rating_count || 0 }})</span>
                </div>
                <router-link
                  v-if="photographer.is_verified && photographer.slug"
                  :to="`/verify/${photographer.slug}`"
                  class="inline-flex items-center gap-1 bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium hover:bg-green-200 transition-colors"
                >
                  <svg
                    class="w-4 h-4"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Verified
                </router-link>
                <span
                  v-else-if="photographer.is_verified"
                  class="inline-flex items-center gap-1 bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium"
                >
                  <svg
                    class="w-4 h-4"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Verified
                </span>
              </div>

              <div
                v-if="photographer.instagram_url || photographer.website_url || photographer.pexels_url"
                class="mb-4 flex items-center justify-center gap-3"
              >
                <a
                  v-if="photographer.instagram_url"
                  :href="photographer.instagram_url"
                  target="_blank"
                  class="p-2 rounded-full bg-white shadow-sm border border-[#eadfd7] text-[#7a1f2b] hover:bg-[#f7f2ee] transition"
                  title="Instagram"
                >
                  <svg
                    class="w-5 h-5"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                  </svg>
                </a>
                <a
                  v-if="photographer.website_url"
                  :href="photographer.website_url"
                  target="_blank"
                  class="p-2 rounded-full bg-white shadow-sm border border-[#eadfd7] text-[#7a1f2b] hover:bg-[#f7f2ee] transition"
                  title="Website"
                >
                  <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"
                    />
                  </svg>
                </a>
                <a
                  v-if="photographer.pexels_url"
                  :href="photographer.pexels_url"
                  target="_blank"
                  class="p-2 rounded-full bg-white shadow-sm border border-[#eadfd7] text-[#7a1f2b] hover:bg-[#f7f2ee] transition"
                  title="Pexels"
                >
                  <img
                    src="/images/pexels.webp"
                    alt="Pexels"
                    class="w-5 h-5"
                    loading="lazy"
                  >
                </a>
              </div>

              <p
                v-if="photographer.bio"
                class="text-gray-600 text-sm mb-4 line-clamp-3"
              >
                {{ photographer.bio }}
              </p>

              <!-- Starting Price -->
              <div
                v-if="photographer.starting_price"
                class="mb-3 p-2 bg-green-50 rounded-lg border border-green-200"
              >
                <p class="text-xs text-green-700 font-medium">
                  Starting from
                </p>
                <p class="text-2xl font-bold text-green-700">
                  ৳{{ formatNumber(photographer.starting_price) }}
                </p>
              </div>

              <!-- Response Time & Rate -->
              <div class="mb-3 space-y-2">
                <div
                  v-if="photographer.average_response_time"
                  class="flex items-center justify-center gap-2 text-sm"
                >
                  <svg
                    class="w-4 h-4 text-blue-600"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-gray-700">
                    Usually responds in <strong>{{ photographer.average_response_time }}h</strong>
                  </span>
                </div>
                <div
                  v-if="photographer.response_rate && photographer.response_rate >= 70"
                  class="flex items-center justify-center gap-2 text-sm"
                >
                  <svg
                    class="w-4 h-4 text-green-600"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-gray-700">
                    <strong>{{ photographer.response_rate }}%</strong> response rate
                  </span>
                </div>
              </div>

              <!-- Profile Views -->
              <div
                v-if="photographer.profile_views"
                class="mb-4 text-xs text-gray-500"
              >
                <svg
                  class="w-4 h-4 inline mr-1"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                  />
                </svg>
                {{ formatNumber(photographer.profile_views) }} profile views
              </div>

              <!-- Action Buttons -->
              <div class="space-y-2">
                <!-- Book Now Button -->
                <button
                  :disabled="isSelfBooking"
                  :class="[
                    'w-full px-6 py-3 rounded-xl font-semibold shadow-md flex items-center justify-center gap-2 transition',
                    isSelfBooking
                      ? 'bg-gray-400 text-gray-200 cursor-not-allowed'
                      : 'bg-[#7a1f2b] text-white hover:bg-[#5f1421] hover:shadow-lg'
                  ]"
                  @click="startBooking"
                >
                  <svg
                    class="w-5 h-5"
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
                  {{ isSelfBooking ? 'Self Booking Not Allowed' : 'Request Booking' }}
                </button>

                <!-- Write Review Button -->
                <button
                  class="w-full border-2 border-[#7a1f2b] text-[#7a1f2b] px-6 py-3 rounded-xl font-semibold hover:bg-[#7a1f2b] hover:text-white transition"
                  @click="writeReview"
                >
                  Write Review
                </button>
              </div>

              <p
                v-if="photographer.average_response_time"
                class="text-xs text-gray-500 text-center mt-2"
              >
                Typically replies within {{ photographer.average_response_time }}h
              </p>
            </div>

            <!-- Portfolio Completeness -->
            <div
              v-if="photographer.portfolio_completeness"
              class="px-6 pb-4"
            >
              <div class="flex items-center justify-between text-xs mb-1">
                <span class="text-gray-600 font-medium">Profile Completeness</span>
                <span class="text-burgundy font-bold">{{ photographer.portfolio_completeness }}%</span>
              </div>
              <div class="w-full bg-[#efe5dc] rounded-full h-2 overflow-hidden">
                <div
                  class="bg-gradient-to-r from-[#7a1f2b] to-[#c75d5d] h-2 rounded-full transition-all"
                  :style="{ width: photographer.portfolio_completeness + '%' }"
                />
              </div>
            </div>

            <!-- Member Since -->
            <div
              v-if="photographer.user?.created_at"
              class="px-6 pb-4 text-center"
            >
              <p class="text-xs text-gray-500">
                <svg
                  class="w-3.5 h-3.5 inline mr-1"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                    clip-rule="evenodd"
                  />
                </svg>
                Member since {{ formatDate(photographer.user.created_at, 'MMMM yyyy') }}
              </p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-px bg-[#eadfd7] border-t border-[#eadfd7]">
              <div class="bg-white/80 p-4 text-center">
                <p class="text-2xl font-bold text-burgundy">
                  {{ photographer.experience_years || 0 }}
                </p>
                <p class="text-gray-600 text-xs mt-1">
                  Years Experience
                </p>
              </div>
              <div class="bg-white/80 p-4 text-center">
                <p class="text-2xl font-bold text-burgundy">
                  {{ photographer.total_bookings || 0 }}
                </p>
                <p class="text-gray-600 text-xs mt-1">
                  Total Bookings
                </p>
              </div>
              <div class="bg-white/80 p-4 text-center">
                <p class="text-2xl font-bold text-burgundy">
                  {{ photographer.completed_bookings || 0 }}
                </p>
                <p class="text-gray-600 text-xs mt-1">
                  Completed
                </p>
              </div>
              <div class="bg-white/80 p-4 text-center">
                <p class="text-2xl font-bold text-burgundy">
                  {{ photographer.trustScore?.overall_score || 0 }}
                </p>
                <p class="text-gray-600 text-xs mt-1">
                  Trust Score
                </p>
              </div>
              <div class="bg-white/80 p-4 text-center">
                <p class="text-2xl font-bold text-burgundy">
                  {{ photographer.events_joined || 0 }}
                </p>
                <p class="text-gray-600 text-xs mt-1">
                  Event Registrations
                </p>
              </div>
              <div class="bg-white/80 p-4 text-center">
                <p class="text-2xl font-bold text-burgundy">
                  {{ photographer.competitions_tried || 0 }}
                </p>
                <p class="text-gray-600 text-xs mt-1">
                  Competitions Entered
                </p>
              </div>
              <div class="bg-white/80 p-4 text-center">
                <p class="text-2xl font-bold text-burgundy">
                  {{ photographer.awards_won || 0 }}
                </p>
                <p class="text-gray-600 text-xs mt-1">
                  Awards Earned
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <!-- About Section - Consolidated -->
          <div class="bg-white/90 rounded-2xl border border-[#eadfd7] shadow-lg overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
            <!-- Header -->
            <div class="bg-gradient-to-r from-[#7a1f2b] to-[#c75d5d] px-8 py-6">
              <h2 class="text-2xl font-serif font-bold text-white">About {{ photographer.business_name || photographer.user?.name }}</h2>
              <p class="text-white/80 text-sm mt-1">Professional photography & specializations</p>
            </div>

            <div class="p-8 space-y-6">
              <!-- Short Introduction -->
              <div>
                <p class="text-lg text-gray-700 leading-relaxed font-light border-l-4 border-[#7a1f2b] pl-4">
                  {{ shortBio }}
                </p>
              </div>

              <!-- Key Stats Grid -->
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-[#7a1f2b]/5 rounded-lg p-4 text-center">
                  <p class="text-2xl font-bold text-[#7a1f2b]">{{ photographer.experience_years || 0 }}</p>
                  <p class="text-xs uppercase text-gray-600 font-medium mt-1">Yrs Exp</p>
                </div>
                <div class="bg-amber-50/70 rounded-lg p-4 text-center border border-amber-200/50">
                  <p class="text-2xl font-bold text-amber-700">{{ (photographer.average_rating || 0) }}</p>
                  <p class="text-xs uppercase text-gray-600 font-medium mt-1">Rating</p>
                </div>
                <div class="bg-emerald-50/70 rounded-lg p-4 text-center border border-emerald-200/50">
                  <p class="text-2xl font-bold text-emerald-700">{{ formatNumber(photographer.completed_bookings || 0) }}</p>
                  <p class="text-xs uppercase text-gray-600 font-medium mt-1">Bookings</p>
                </div>
                <div class="bg-blue-50/70 rounded-lg p-4 text-center border border-blue-200/50">
                  <p class="text-2xl font-bold text-blue-700">{{ photographer.is_verified ? '✓' : '–' }}</p>
                  <p class="text-xs uppercase text-gray-600 font-medium mt-1">{{ photographer.is_verified ? 'Verified' : 'Not Verified' }}</p>
                </div>
              </div>

              <!-- Full Bio -->
              <div v-if="hasLongBio" class="py-6 border-y border-[#eadfd7]">
                <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
                  <span class="inline-block w-1 h-6 bg-[#7a1f2b] rounded-full" />
                  Background
                </h3>
                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ photographer.bio }}</p>
              </div>

              <!-- Location & Service Area -->
              <div v-if="locationLabel || serviceRadiusLabel" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div v-if="locationLabel" class="bg-blue-50/70 rounded-lg p-4 border border-blue-200/50">
                  <p class="flex items-center gap-2 text-blue-900 font-semibold text-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M5.05 3.05a7 7 0 019.9 9.9L10 18l-4.95-5.05a7 7 0 010-9.9z" />
                    </svg>
                    Location
                  </p>
                  <p class="text-blue-700 mt-2 font-medium">{{ locationLabel }}</p>
                </div>
                <div v-if="serviceRadiusLabel" class="bg-amber-50/70 rounded-lg p-4 border border-amber-200/50">
                  <p class="flex items-center gap-2 text-amber-900 font-semibold text-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 8.414l2.95 2.95-1.414 1.414-3.536-3.536V5h2v5.414z" />
                    </svg>
                    Service Radius
                  </p>
                  <p class="text-amber-700 mt-2 font-medium">{{ serviceRadiusLabel }}</p>
                </div>
              </div>

              <!-- Specializations -->
              <div v-if="photographer.categories && photographer.categories.length > 0">
                <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
                  <span class="inline-block w-1 h-6 bg-purple-500 rounded-full" />
                  Specializations
                </h3>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="category in photographer.categories"
                    :key="category.id"
                    class="px-4 py-2 rounded-full bg-gradient-to-r from-[#7a1f2b]/10 to-[#c75d5d]/10 border border-[#7a1f2b]/20 text-[#7a1f2b] font-semibold text-sm"
                  >
                    📷 {{ category.name }}
                  </span>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-[#eadfd7]">
                <button
                  :disabled="isSelfBooking"
                  :class="[
                    'px-6 py-2.5 rounded-full font-semibold whitespace-nowrap transition-all duration-200 flex-1 sm:flex-initial',
                    isSelfBooking
                      ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                      : 'bg-[#7a1f2b] text-white hover:bg-[#5f1421]'
                  ]"
                  @click="startBooking"
                >
                  🎯 Request Booking
                </button>
                <button
                  class="px-6 py-2.5 rounded-full border border-[#7a1f2b] text-[#7a1f2b] font-semibold hover:bg-[#7a1f2b] hover:text-white transition-all duration-200 flex-1 sm:flex-initial"
                  @click="activeTab = 'portfolio'"
                >
                  📸 Portfolio
                </button>
                <button
                  class="px-6 py-2.5 rounded-full border border-[#eadfd7] text-gray-700 hover:border-[#7a1f2b] hover:text-[#7a1f2b] transition-all duration-200 flex-1 sm:flex-initial"
                  @click="activeTab = 'packages'"
                >
                  📋 Packages
                </button>
                <button
                  class="px-6 py-2.5 rounded-full border border-[#eadfd7] text-gray-700 hover:border-[#7a1f2b] hover:text-[#7a1f2b] transition-all duration-200 flex-1 sm:flex-initial"
                  @click="activeTab = 'reviews'"
                >
                  ⭐ Reviews
                </button>
              </div>
            </div>
          </div>

          <BuyMeCoffeeButton
            v-if="photographer?.id"
            :photographerId="photographer.id"
          />

          <!-- Tab Navigation -->
          <div class="bg-white/90 rounded-2xl border border-[#eadfd7] shadow-lg transition-transform duration-300 hover:-translate-y-1">
            <div class="border-b border-[#eadfd7]">
              <nav class="flex -mb-px overflow-x-auto scrollbar-hide">
                <button
                  :class="[
                    'px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-all',
                    activeTab === 'portfolio'
                      ? 'border-[#7a1f2b] text-[#7a1f2b]'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  ]"
                  @click="activeTab = 'portfolio'"
                >
                  Portfolio
                </button>
                <button
                  :class="[
                    'px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-all',
                    activeTab === 'packages'
                      ? 'border-[#7a1f2b] text-[#7a1f2b]'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  ]"
                  @click="activeTab = 'packages'"
                >
                  Packages
                </button>
                <button
                  :class="[
                    'px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-all',
                    activeTab === 'reviews'
                      ? 'border-[#7a1f2b] text-[#7a1f2b]'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  ]"
                  @click="activeTab = 'reviews'"
                >
                  Reviews
                </button>
                <button
                  :class="[
                    'px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-all',
                    activeTab === 'awards'
                      ? 'border-[#7a1f2b] text-[#7a1f2b]'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  ]"
                  @click="activeTab = 'awards'"
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
                  <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    Portfolio
                  </h2>
                  
                  <!-- Album Grid -->
                  <div
                    v-for="album in albums"
                    :key="album.id"
                    class="mb-8"
                  >
                    <div class="flex items-center justify-between mb-4">
                      <h3 class="text-xl font-semibold text-gray-900">
                        {{ album.name }}
                      </h3>
                      <span class="text-sm text-gray-500">{{ album.photos?.length || 0 }} photos</span>
                    </div>
                    
                    <div
                      v-if="album.photos && album.photos.length > 0"
                      class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4"
                    >
                      <div
                        v-for="photo in album.photos"
                        :key="photo.id"
                        class="group relative aspect-square rounded-lg overflow-hidden cursor-pointer hover:shadow-xl transition-all"
                        @click="openLightbox(photo)"
                      >
                        <img
                          :src="photo.thumbnail_url || photo.url || '/images/placeholder.svg'"
                          :alt="photo.title || album.name"
                          class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                          loading="lazy"
                          decoding="async"
                        >
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-opacity flex items-center justify-center">
                          <svg
                            class="w-10 h-10 text-white opacity-0 group-hover:opacity-100 transition-opacity"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                            />
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                            />
                          </svg>
                        </div>
                        <div
                          v-if="photo.title"
                          class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3 opacity-0 group-hover:opacity-100 transition-opacity"
                        >
                          <p class="text-white text-sm font-medium truncate">
                            {{ photo.title }}
                          </p>
                        </div>
                      </div>
                    </div>
                    <div
                      v-else
                      class="text-center py-8 text-gray-400"
                    >
                      No photos in this album yet
                    </div>
                  </div>
                </div>
                <div
                  v-else
                  class="text-center py-12"
                >
                  <svg
                    class="w-16 h-16 text-gray-300 mx-auto mb-4"
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
                  <p class="text-gray-500">
                    No portfolio albums yet
                  </p>
                </div>
              </div>

              <!-- Packages Tab -->
              <div v-if="activeTab === 'packages'">
                <div v-if="packages && packages.length > 0">
                  <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    Packages
                  </h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                      v-for="pkg in packages"
                      :key="pkg.id"
                      class="border-2 border-gray-200 rounded-lg p-5 hover:border-[#7a1f2b] hover:shadow-lg transition"
                    >
                      <div class="flex items-start justify-between mb-3">
                        <h3 class="font-bold text-lg text-gray-900">
                          {{ pkg.name }}
                        </h3>
                        <span class="bg-[#7a1f2b]/10 text-[#7a1f2b] px-2 py-1 rounded text-sm font-semibold">
                          ৳{{ formatNumber(pkg.base_price) }}
                        </span>
                      </div>
                      <p class="text-gray-600 text-sm mb-4">
                        {{ pkg.description }}
                      </p>
                      <button
                        class="w-full bg-[#7a1f2b] text-white py-2.5 rounded-lg font-semibold hover:bg-[#5f1421] transition"
                        @click="selectPackage(pkg)"
                      >
                        Select Package
                      </button>
                    </div>
                  </div>
                </div>
                <div
                  v-else
                  class="text-center py-12"
                >
                  <svg
                    class="w-16 h-16 text-gray-300 mx-auto mb-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                    />
                  </svg>
                  <p class="text-gray-500">
                    No packages available yet
                  </p>
                </div>
              </div>

              <!-- Reviews Tab -->
              <div v-if="activeTab === 'reviews'">
                <div v-if="reviews && reviews.length > 0">
                  <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    Reviews ({{ reviews.length }})
                  </h2>
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
                              <span
                                v-if="review.is_verified_purchase"
                                class="ml-2 text-xs text-green-600"
                                title="Verified Purchase"
                              >✓ Verified</span>
                            </p>
                            <p class="text-xs text-gray-500">
                              {{ formatDate(review.published_at) }}
                            </p>
                          </div>
                        </div>
                        <div class="flex items-center gap-1 bg-yellow-50 px-2 py-1 rounded">
                          <svg
                            class="w-4 h-4 text-yellow-400 fill-current"
                            viewBox="0 0 20 20"
                          >
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                          </svg>
                          <span class="font-semibold text-gray-900 text-sm">{{ review.rating }}</span>
                        </div>
                      </div>
                      <p class="text-gray-700">
                        {{ review.comment }}
                      </p>
                    </div>
                  </div>
                </div>
                <div
                  v-else
                  class="text-center py-12"
                >
                  <svg
                    class="w-16 h-16 text-gray-300 mx-auto mb-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                    />
                  </svg>
                  <p class="text-gray-500">
                    No reviews yet
                  </p>
                </div>
              </div>

              <!-- Awards Tab -->
              <div v-if="activeTab === 'awards'">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                  Awards & Achievements
                </h2>
                
                <!-- Competition Wins Section -->
                <div
                  v-if="competitionWins && competitionWins.length > 0"
                  class="mb-8"
                >
                  <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg
                      class="w-6 h-6 text-yellow-500"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
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
                              <h4 class="font-bold text-lg text-gray-900 mb-1">
                                {{ win.competition_title }}
                              </h4>
                              <p
                                v-if="win.submission_title"
                                class="text-gray-700 text-sm mb-2"
                              >
                                <span class="font-medium">Entry:</span> {{ win.submission_title }}
                              </p>
                            </div>
                          </div>
                          <div class="flex items-center justify-between">
                            <div
                              v-if="win.prize_amount"
                              class="flex items-center gap-1"
                            >
                              <svg
                                class="w-5 h-5 text-green-600"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                              >
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                <path
                                  fill-rule="evenodd"
                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                  clip-rule="evenodd"
                                />
                              </svg>
                              <span class="text-green-700 font-bold">৳{{ formatNumber(win.prize_amount) }}</span>
                            </div>
                            <img
                              v-if="win.image_url"
                              :src="win.image_url"
                              :alt="win.submission_title"
                              class="w-16 h-16 object-cover rounded border-2 border-white shadow-sm"
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Regular Awards Section -->
                <div
                  v-if="awards && awards.length > 0"
                  class="mb-8"
                >
                  <h3 class="text-xl font-bold text-gray-900 mb-4">
                    Professional Awards
                  </h3>
                  <div class="space-y-4">
                    <div
                      v-for="award in awards"
                      :key="award.id"
                      class="border-2 border-gray-200 rounded-lg p-5 hover:border-burgundy hover:shadow-lg transition"
                    >
                      <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                          <div class="w-16 h-16 bg-burgundy/10 rounded-lg flex items-center justify-center">
                            <svg
                              class="w-8 h-8 text-burgundy"
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
                              />
                            </svg>
                          </div>
                        </div>
                        <div class="flex-1 min-w-0">
                          <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                              <h3 class="font-bold text-lg text-gray-900 mb-1">
                                {{ award.title }}
                              </h3>
                              <p
                                v-if="award.organization"
                                class="text-gray-600 text-sm mb-1"
                              >
                                {{ award.organization }}
                              </p>
                            </div>
                            <span class="flex-shrink-0 bg-burgundy/10 text-burgundy px-3 py-1 rounded-full text-sm font-semibold ml-3">
                              {{ award.year }}
                            </span>
                          </div>
                          <p
                            v-if="award.description"
                            class="text-gray-700 text-sm mb-2"
                          >
                            {{ award.description }}
                          </p>
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
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
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
                <div
                  v-if="(!awards || awards.length === 0) && (!competitionWins || competitionWins.length === 0)"
                  class="text-center py-12"
                >
                  <svg
                    class="w-20 h-20 text-gray-300 mx-auto mb-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
                    />
                  </svg>
                  <p class="text-gray-500 text-lg mb-2">
                    No awards or achievements yet
                  </p>
                  <p class="text-gray-400 text-sm">
                    Competition wins and certificates will appear here
                  </p>
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
      class="fixed inset-0 z-50 bg-black bg-opacity-90 flex items-center justify-center p-4"
      @click="closeLightbox"
    >
      <button
        class="absolute top-4 right-4 text-white hover:text-gray-300 z-10"
        @click="closeLightbox"
      >
        <svg
          class="w-8 h-8"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M6 18L18 6M6 6l12 12"
          />
        </svg>
      </button>
      
      <div
        class="max-w-6xl w-full"
        @click.stop
      >
        <img
          :src="selectedPhoto.url || selectedPhoto.thumbnail_url"
          :alt="selectedPhoto.title"
          class="w-full h-auto max-h-[90vh] object-contain rounded-lg"
          decoding="async"
        >
        <div
          v-if="selectedPhoto.title || selectedPhoto.description"
          class="mt-4 text-white text-center"
        >
          <h3
            v-if="selectedPhoto.title"
            class="text-xl font-semibold mb-2"
          >
            {{ selectedPhoto.title }}
          </h3>
          <p
            v-if="selectedPhoto.description"
            class="text-gray-300"
          >
            {{ selectedPhoto.description }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api';
import BuyMeCoffeeButton from './BuyMeCoffeeButton.vue';
import {
  formatDate as formatDateValue,
  formatMonthYearLong,
  formatNumber
} from '../utils/formatters';

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
const currentUser = ref(null);

const shortBio = computed(() => {
  const bio = photographer.value?.bio || ''
  if (!bio) return 'Experienced photographer available for bookings and collaborations.'
  return bio.length > 180 ? `${bio.slice(0, 177).trim()}...` : bio
})

const hasLongBio = computed(() => {
  const bio = photographer.value?.bio || ''
  return bio.length > 180
})

const locationLabel = computed(() => {
  return photographer.value?.location
    || photographer.value?.city?.name
    || photographer.value?.district?.name
    || null
})

const serviceRadiusLabel = computed(() => {
  const radius = Number(photographer.value?.service_area_radius || 0)
  return radius > 0 ? `${radius} km service radius` : null
})


const profileImage = computed(() => {
  const raw = photographer.value?.profile_picture
    || photographer.value?.profile_picture_url
    || photographer.value?.avatar
  if (raw) {
    if (raw.startsWith('http') || raw.startsWith('/storage/')) return raw
    return `/storage/${raw.replace(/^\/+/, '')}`
  }
  const name = photographer.value?.user?.name || photographer.value?.business_name || 'User'
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&size=200&background=6c0b1a&color=fff`
})

const isSelfBooking = computed(() => {
  if (!currentUser.value || !photographer.value) return false;
  return currentUser.value.photographer?.id === photographer.value.id;
});

const fetchPhotographer = async () => {
  try {
    const { data } = await api.get(`/photographers/${route.params.slug}`);
    
    if (data.status === 'success') {
      photographer.value = data.data;
      
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
  if (isSelfBooking.value) {
    alert('You cannot book your own profile');
    return;
  }
  router.push(`/booking/${photographer.value.id}`);
};

const writeReview = () => {
  if (!localStorage.getItem('user')) {
    router.push('/auth');
    return;
  }
  router.push(`/review/${photographer.value.id}`);
};


const selectPackage = (pkg) => {
  router.push(`/booking/${photographer.value.id}?package=${pkg.id}`);
};

const formatDate = (date, format = 'default') => {
  if (!date) return '';
  if (format === 'MMMM yyyy') {
    return formatMonthYearLong(date);
  }

  return formatDateValue(date);
};

const openLightbox = (photo) => {
  selectedPhoto.value = photo;
  document.body.style.overflow = 'hidden';
};

const closeLightbox = () => {
  selectedPhoto.value = null;
  document.body.style.overflow = 'auto';
};

const trackShareVisit = async () => {
  const refParam = typeof route.query.ref === 'string' ? route.query.ref : null;
  if (!refParam) return;

  try {
    await api.post('/photographers/profile-share-visit', { ref: refParam });
  } catch (error) {
    console.warn('Profile share visit tracking failed:', error);
  }
};

onMounted(() => {
  const userStr = localStorage.getItem('user');
  if (userStr) {
    try {
      currentUser.value = JSON.parse(userStr);
    } catch (e) {
      console.error('Error parsing user from localStorage:', e);
    }
  }
  trackShareVisit();
  fetchPhotographer();
});
</script>
