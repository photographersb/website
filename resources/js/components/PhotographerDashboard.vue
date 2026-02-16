<template>
  <div class="min-h-screen bg-gray-50 pt-2 sm:pt-3 md:pt-4">
    <!-- Header -->
    <div class="bg-gradient-to-r from-white via-white to-rose-50 border-b">
      <div class="container mx-auto px-3 sm:px-4 py-6 sm:py-7">
        <div class="flex items-center gap-3 sm:gap-4">
          <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full overflow-hidden bg-burgundy flex items-center justify-center text-white font-bold text-lg sm:text-2xl flex-shrink-0 shadow-md">
            <img
              v-if="profileAvatarUrl"
              :src="profileAvatarUrl"
              :alt="user?.name"
              class="w-full h-full object-cover"
              @error="handleAvatarError"
            >
            <span v-else>{{ user?.name?.charAt(0).toUpperCase() }}</span>
          </div>
          <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 text-[11px] uppercase tracking-[0.2em] text-burgundy bg-opacity-70">
              Photographer HQ
            </div>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold truncate">Welcome, {{ user?.name }}</h1>
            <p class="text-sm sm:text-base text-gray-600 truncate mt-1">Build momentum, respond fast, and grow bookings.</p>
          </div>
          <div class="flex items-center gap-3">
            <NotificationBell />
          </div>
        </div>
      </div>
    </div>

    <div class="container mx-auto px-3 sm:px-4 py-3 sm:py-5 md:py-6">
      <!-- Stats Overview -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 md:gap-6 mt-3 sm:mt-4 md:mt-5 mb-3 sm:mb-4 md:mb-6">
        <div class="bg-white rounded-xl border border-gray-200/70 shadow-sm p-3 sm:p-4 md:p-6">
          <p class="text-[11px] sm:text-xs uppercase tracking-wide text-gray-500 mb-1">Total Bookings</p>
          <p class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">{{ stats.total_bookings || 0 }}</p>
          <p class="text-xs text-gray-500 mt-1">All time</p>
        </div>
        <div class="bg-white rounded-xl border border-amber-200/70 shadow-sm p-3 sm:p-4 md:p-6">
          <p class="text-[11px] sm:text-xs uppercase tracking-wide text-amber-700 mb-1">Pending Requests</p>
          <p class="text-xl sm:text-2xl md:text-3xl font-bold text-amber-800">{{ stats.pending_bookings || 0 }}</p>
          <p class="text-xs text-amber-700 mt-1">Needs response</p>
        </div>
        <div class="bg-white rounded-xl border border-emerald-200/70 shadow-sm p-3 sm:p-4 md:p-6">
          <p class="text-[11px] sm:text-xs uppercase tracking-wide text-emerald-700 mb-1">Average Rating</p>
          <p class="text-xl sm:text-2xl md:text-3xl font-bold text-emerald-800">{{ stats.average_rating || 0 }}</p>
          <p class="text-xs text-emerald-700 mt-1">Last 90 days</p>
        </div>
        <div class="bg-white rounded-xl border border-rose-200/70 shadow-sm p-3 sm:p-4 md:p-6">
          <p class="text-[11px] sm:text-xs uppercase tracking-wide text-rose-700 mb-1">Total Revenue</p>
          <p class="text-xl sm:text-2xl md:text-3xl font-bold text-rose-800">৳{{ stats.total_revenue || 0 }}</p>
          <p class="text-xs text-rose-700 mt-1">Gross earnings</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-3 mb-3 sm:mb-4 md:mb-6">
        <div class="md:col-span-2 bg-white rounded-2xl border border-gray-200/80 shadow-sm p-4 sm:p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-base sm:text-lg font-bold">Quick Actions</h3>
            <span class="text-xs sm:text-sm text-gray-500">Keep momentum high</span>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div
              v-for="item in priorityActions"
              :key="item.key"
              class="rounded-xl border p-4 sm:p-5 flex flex-col justify-between gap-3"
              :class="item.cardClass"
            >
              <div>
                <div class="flex items-center justify-between">
                  <p class="text-xs uppercase tracking-wide text-gray-500">{{ item.title }}</p>
                  <span
                    v-if="item.badge"
                    class="px-2 py-1 rounded-full text-[10px] font-semibold"
                    :class="item.badgeClass"
                  >
                    {{ item.badge }}
                  </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 mt-2">
                  {{ item.value }}
                </p>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">
                  {{ item.description }}
                </p>
              </div>
              <button
                type="button"
                class="inline-flex items-center justify-center px-3 py-2 text-sm font-semibold rounded-lg"
                :class="item.buttonClass"
                @click="item.onClick"
              >
                {{ item.cta }}
              </button>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-4 sm:p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-base sm:text-lg font-bold">Profile Checklist</h3>
            <span class="text-xs text-burgundy font-semibold">{{ profileCompletionPercent }}% complete</span>
          </div>
          <div class="h-2 bg-gray-200 rounded-full overflow-hidden mb-4">
            <div
              class="h-full bg-burgundy"
              :style="{ width: profileCompletionPercent + '%' }"
            />
          </div>
          <div class="space-y-2">
            <div
              v-for="item in profileChecklist"
              :key="item.key"
              class="flex items-center gap-2 text-sm"
            >
              <span
                class="flex items-center justify-center w-5 h-5 rounded-full text-xs font-semibold"
                :class="item.done ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400'"
              >
                {{ item.done ? '✓' : '•' }}
              </span>
              <span :class="item.done ? 'text-gray-700' : 'text-gray-500'">{{ item.label }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-rose-100 bg-gradient-to-r from-rose-50 via-white to-amber-50 p-4 sm:p-5 mb-3 sm:mb-4 md:mb-6 shadow-sm">
        <div class="grid gap-4 md:grid-cols-2">
          <div class="rounded-xl border border-rose-100 bg-white/70 p-4">
            <p class="text-[11px] uppercase tracking-[0.2em] text-rose-600 mb-2">Share The Spotlight</p>
            <h3 class="text-base sm:text-lg font-semibold text-gray-900">
              Post your latest shots on Instagram and tag our handle to get featured.
            </h3>
            <p class="text-sm text-gray-600 mt-1">
              Tag <span class="font-semibold text-rose-700">@thephotographersbd</span> so we can celebrate your work with the community.
            </p>
            <div class="mt-3 flex flex-wrap items-center gap-2">
              <a
                href="https://www.instagram.com/thephotographersbd"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg border border-rose-200 text-rose-700 hover:bg-rose-100"
              >
                Open Instagram
              </a>
            </div>
          </div>
          <div class="rounded-xl border border-amber-100 bg-white/70 p-4">
            <p class="text-[11px] uppercase tracking-[0.2em] text-amber-700 mb-2">Share Your Profile</p>
            <h3 class="text-base sm:text-lg font-semibold text-gray-900">
              Put your work in front of the right people, fast.
            </h3>
            <p class="text-sm text-gray-600 mt-1">
              Share your profile like a digital business card so every click becomes a future booking.
            </p>
            <div class="mt-3 flex flex-col sm:flex-row sm:items-center gap-2">
              <input
                :value="profileUrl"
                readonly
                class="w-full sm:flex-1 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700"
                aria-label="Profile share link"
              >
              <button
                type="button"
                class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg border border-amber-200 text-amber-700 hover:bg-amber-100"
                @click="copyProfileLink"
              >
                {{ copied ? 'Copied' : 'Copy link' }}
              </button>
              <a
                :href="profileUrl"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg bg-amber-600 text-white hover:bg-amber-700"
                @click="openProfile"
              >
                Open profile
              </a>
            </div>
            <div class="mt-3 flex flex-wrap items-center gap-2">
              <button
                type="button"
                class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold rounded-lg border border-emerald-200 text-emerald-700 hover:bg-emerald-100"
                @click="shareProfile('whatsapp')"
              >
                WhatsApp
              </button>
              <button
                type="button"
                class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold rounded-lg border border-blue-200 text-blue-700 hover:bg-blue-100"
                @click="shareProfile('facebook')"
              >
                Facebook
              </button>
              <button
                type="button"
                class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold rounded-lg border border-indigo-200 text-indigo-700 hover:bg-indigo-100"
                @click="shareProfile('messenger')"
              >
                Messenger
              </button>
              <button
                type="button"
                class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold rounded-lg border border-sky-200 text-sky-700 hover:bg-sky-100"
                @click="shareProfile('telegram')"
              >
                Telegram
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Revenue Funnel -->
      <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-4 sm:p-6 mb-3 sm:mb-4 md:mb-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-base sm:text-lg font-bold">
            Revenue Funnel
          </h3>
          <span class="text-xs sm:text-sm text-gray-500">Views → Inquiries → Bookings → Revenue</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
          <div class="rounded-xl border border-gray-200/70 bg-gradient-to-b from-white to-gray-50 p-3 sm:p-4">
            <p class="text-[11px] uppercase tracking-wide text-gray-500 mb-1">
              Profile Views
            </p>
            <p class="text-lg sm:text-2xl font-bold text-gray-900">
              {{ funnelStats.views }}
            </p>
            <div class="h-2 bg-gray-200 rounded-full overflow-hidden mt-3">
              <div
                class="h-full bg-burgundy"
                :style="{ width: funnelStats.viewBar + '%' }"
              />
            </div>
          </div>
          <div class="rounded-xl border border-amber-200/70 bg-gradient-to-b from-white to-amber-50/60 p-3 sm:p-4">
            <p class="text-[11px] uppercase tracking-wide text-amber-700 mb-1">
              Inquiries
            </p>
            <p class="text-lg sm:text-2xl font-bold text-gray-900">
              {{ funnelStats.inquiries }}
            </p>
            <p class="text-xs text-gray-500">
              {{ funnelStats.inquiryRate }}% from views
            </p>
            <div class="h-2 bg-gray-200 rounded-full overflow-hidden mt-3">
              <div
                class="h-full bg-amber-500"
                :style="{ width: funnelStats.inquiryBar + '%' }"
              />
            </div>
          </div>
          <div class="rounded-xl border border-emerald-200/70 bg-gradient-to-b from-white to-emerald-50/60 p-3 sm:p-4">
            <p class="text-[11px] uppercase tracking-wide text-emerald-700 mb-1">
              Bookings
            </p>
            <p class="text-lg sm:text-2xl font-bold text-gray-900">
              {{ funnelStats.bookings }}
            </p>
            <p class="text-xs text-gray-500">
              {{ funnelStats.bookingRate }}% from inquiries
            </p>
            <div class="h-2 bg-gray-200 rounded-full overflow-hidden mt-3">
              <div
                class="h-full bg-emerald-500"
                :style="{ width: funnelStats.bookingBar + '%' }"
              />
            </div>
          </div>
          <div class="rounded-xl border border-rose-200/70 bg-gradient-to-b from-white to-rose-50/60 p-3 sm:p-4">
            <p class="text-[11px] uppercase tracking-wide text-rose-700 mb-1">
              Revenue
            </p>
            <p class="text-lg sm:text-2xl font-bold text-gray-900">
              ৳{{ funnelStats.revenue }}
            </p>
            <p class="text-xs text-gray-500">
              ৳{{ funnelStats.revenuePerBooking }} per booking
            </p>
            <div class="h-2 bg-gray-200 rounded-full overflow-hidden mt-3">
              <div
                class="h-full bg-rose-600"
                :style="{ width: funnelStats.revenueBar + '%' }"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Upcoming Summary -->
      <div class="bg-white rounded-lg shadow p-4 sm:p-6 mb-3 sm:mb-4 md:mb-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-base sm:text-lg font-bold">
            Upcoming
          </h3>
          <span class="text-xs sm:text-sm text-gray-500">Next 7 days</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
          <div class="rounded-xl border border-gray-200/70 bg-gradient-to-b from-white to-gray-50 p-3 sm:p-4">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm font-semibold text-gray-900">Bookings</p>
              <button
                type="button"
                class="text-xs font-semibold text-burgundy hover:text-burgundy-dark"
                @click="activeTab = 'bookings'"
              >
                View all
              </button>
            </div>
            <p class="text-xs text-gray-500">{{ upcomingBookings.length }} scheduled</p>
            <p class="text-sm text-gray-700 mt-2">
              {{ upcomingBookings[0]?.client?.name || 'No upcoming bookings' }}
            </p>
            <p class="text-xs text-gray-500">
              {{ upcomingBookings[0]?.event_date ? formatDate(upcomingBookings[0].event_date) : 'Add availability to get booked' }}
            </p>
          </div>
          <div class="rounded-xl border border-gray-200/70 bg-gradient-to-b from-white to-gray-50 p-3 sm:p-4">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm font-semibold text-gray-900">Events</p>
              <button
                type="button"
                class="text-xs font-semibold text-burgundy hover:text-burgundy-dark"
                @click="activeTab = 'events'"
              >
                View all
              </button>
            </div>
            <p class="text-xs text-gray-500">{{ upcomingEvents.length }} upcoming</p>
            <p class="text-sm text-gray-700 mt-2">
              {{ upcomingEvents[0]?.title || 'No upcoming events' }}
            </p>
            <p class="text-xs text-gray-500">
              {{ upcomingEvents[0]?.event_date ? formatDate(upcomingEvents[0].event_date) : 'Create an event to stay visible' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Response SLA -->
      <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-4 sm:p-6 mb-3 sm:mb-4 md:mb-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-base sm:text-lg font-bold">
            Response Time
          </h3>
          <span class="text-xs sm:text-sm text-gray-500">Faster replies win more bookings</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="rounded-xl border border-gray-200/70 bg-gradient-to-b from-white to-gray-50 p-4">
            <p class="text-[11px] uppercase tracking-wide text-gray-500 mb-1">
              Average response
            </p>
            <p class="text-2xl font-bold text-gray-900">
              {{ responseStats.average }} hrs
            </p>
            <p class="text-xs text-gray-500">
              Goal: under {{ responseStats.target }} hrs
            </p>
          </div>
          <div class="rounded-xl border border-gray-200/70 bg-gradient-to-b from-white to-gray-50 p-4">
            <p class="text-[11px] uppercase tracking-wide text-gray-500 mb-1">
              Performance
            </p>
            <p
              class="text-2xl font-bold"
              :class="responseStats.statusClass"
            >
              {{ responseStats.statusLabel }}
            </p>
            <div class="h-2 bg-gray-200 rounded-full overflow-hidden mt-3">
              <div
                class="h-full"
                :class="responseStats.barClass"
                :style="{ width: responseStats.progress + '%' }"
              />
            </div>
          </div>
          <div class="rounded-xl border border-rose-200/70 bg-gradient-to-b from-white to-rose-50/60 p-4 flex flex-col justify-between">
            <div>
              <p class="text-[11px] uppercase tracking-wide text-rose-700 mb-1">
                Tip
              </p>
              <p class="text-sm text-gray-700">
                Use quick replies to answer within 1 hour.
              </p>
            </div>
            <router-link
              to="/photographer/settings"
              class="mt-3 inline-flex items-center justify-center px-3 py-2 rounded-lg text-sm font-semibold bg-burgundy text-white hover:bg-burgundy-dark transition-colors"
            >
              Update account settings
            </router-link>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 bg-opacity-70 border-opacity-70">
          <div class="overflow-x-auto scrollbar-hide">
            <div class="flex gap-3 sm:gap-6 px-3 sm:px-6 py-3 sm:py-4 min-w-max">
              <button
                v-if="canViewBookings"
                :class="`pb-2 font-semibold whitespace-nowrap text-sm sm:text-base min-h-[44px] px-2 transition-colors ${activeTab === 'bookings' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600 hover:text-gray-900'}`"
                @click="activeTab = 'bookings'"
              >
                {{ isPhotographerRole ? 'Incoming Requests' : 'Bookings' }}
              </button>
              <button
                :class="`pb-2 font-semibold whitespace-nowrap text-sm sm:text-base min-h-[44px] px-2 transition-colors ${activeTab === 'portfolio' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600 hover:text-gray-900'}`"
                @click="activeTab = 'portfolio'"
              >
                Portfolio
              </button>
              <button
                :class="`pb-2 font-semibold whitespace-nowrap text-sm sm:text-base min-h-[44px] px-2 transition-colors ${activeTab === 'packages' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600 hover:text-gray-900'}`"
                @click="activeTab = 'packages'"
              >
                Packages
              </button>
              <button
                :class="`pb-2 font-semibold whitespace-nowrap text-sm sm:text-base min-h-[44px] px-2 transition-colors ${activeTab === 'reviews' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600 hover:text-gray-900'}`"
                @click="activeTab = 'reviews'"
              >
                Reviews
              </button>
              <button
                :class="`pb-2 font-semibold whitespace-nowrap text-sm sm:text-base min-h-[44px] px-2 transition-colors ${activeTab === 'competitions' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600 hover:text-gray-900'}`"
                @click="activeTab = 'competitions'"
              >
                Competitions
              </button>
              <button
                :class="`pb-2 font-semibold whitespace-nowrap text-sm sm:text-base min-h-[44px] px-2 transition-colors ${activeTab === 'events' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600 hover:text-gray-900'}`"
                @click="activeTab = 'events'"
              >
                Events
              </button>
              <button
                :class="`pb-2 font-semibold whitespace-nowrap text-sm sm:text-base min-h-[44px] px-2 transition-colors ${activeTab === 'achievements' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600 hover:text-gray-900'}`"
                @click="activeTab = 'achievements'"
              >
                🌟 Achievements
              </button>
              <button
                :class="`pb-2 font-semibold whitespace-nowrap text-sm sm:text-base min-h-[44px] px-2 transition-colors ${activeTab === 'awards' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600 hover:text-gray-900'}`"
                @click="activeTab = 'awards'"
              >
                🏆 Awards
              </button>
            </div>
          </div>
        </div>

        <div class="p-3 sm:p-4 md:p-6">
          <!-- Bookings Tab -->
          <div v-if="activeTab === 'bookings' && canViewBookings">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3 sm:mb-4">
              <h2 class="text-lg sm:text-xl font-bold">
                {{ isPhotographerRole ? 'Incoming Requests' : 'Recent Bookings' }}
              </h2>
              <router-link
                v-if="!isPhotographerRole"
                to="/bookings"
                class="text-sm font-semibold text-burgundy hover:underline"
              >
                View all bookings
              </router-link>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 mb-4">
              <div class="rounded-xl border border-gray-200/70 bg-gradient-to-b from-white to-gray-50 p-3">
                <p class="text-[11px] uppercase tracking-wide text-gray-500">Total</p>
                <p class="text-lg sm:text-xl font-bold text-gray-900">{{ incomingSummary.total }}</p>
              </div>
              <div class="rounded-xl border border-amber-200/70 bg-gradient-to-b from-white to-amber-50/60 p-3">
                <p class="text-[11px] uppercase tracking-wide text-amber-700">Pending</p>
                <p class="text-lg sm:text-xl font-bold text-amber-800">{{ incomingSummary.pending }}</p>
              </div>
              <div class="rounded-xl border border-orange-200/70 bg-gradient-to-b from-white to-orange-50/60 p-3">
                <p class="text-[11px] uppercase tracking-wide text-orange-700">Awaiting Payment</p>
                <p class="text-lg sm:text-xl font-bold text-orange-800">{{ incomingSummary.pendingPayment }}</p>
              </div>
              <div class="rounded-xl border border-green-200/70 bg-gradient-to-b from-white to-green-50/60 p-3">
                <p class="text-[11px] uppercase tracking-wide text-green-700">Confirmed</p>
                <p class="text-lg sm:text-xl font-bold text-green-800">{{ incomingSummary.confirmed }}</p>
              </div>
            </div>
            <EmptyState 
              v-if="bookings.length === 0"
              icon="calendar"
              :title="isPhotographerRole ? 'No incoming requests' : 'No bookings yet'"
              :description="isPhotographerRole ? 'New booking requests will appear here' : 'Your booking requests will appear here'"
            />
            <div
              v-else
              class="space-y-3 sm:space-y-4"
            >
              <div
                v-for="booking in bookings"
                :key="booking.id"
                class="rounded-xl border border-gray-200/70 bg-white p-3 sm:p-4 hover:bg-gray-50 active:bg-gray-100"
              >
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 mb-2 sm:mb-3">
                  <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-sm sm:text-base truncate">
                      <router-link
                        v-if="booking.id"
                        :to="`/bookings/${booking.id}/messages`"
                        class="hover:underline"
                      >
                        {{ booking.client?.name || 'Unknown Client' }}
                      </router-link>
                      <span v-else>{{ booking.client?.name || 'Unknown Client' }}</span>
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-600 truncate">
                      {{ booking.event_location }}
                    </p>
                  </div>
                  <span
                    :class="`px-2 sm:px-3 py-1 rounded-full text-xs whitespace-nowrap ${getBookingStatusClass(booking.status)}`"
                  >
                    {{ formatStatus(booking.status) }}
                  </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 text-xs sm:text-sm mb-3">
                  <div>
                    <span class="text-gray-600">Event Date:</span>
                    <span class="ml-2 font-semibold">{{ formatDate(booking.event_date) }}</span>
                  </div>
                  <div v-if="booking.budget_min || booking.budget_max">
                    <span class="text-gray-600">Budget:</span>
                    <span class="ml-2 font-semibold">
                      <template v-if="booking.budget_min && booking.budget_max">
                        ৳{{ booking.budget_min }} - ৳{{ booking.budget_max }}
                      </template>
                      <template v-else-if="booking.budget_min">
                        ৳{{ booking.budget_min }}+
                      </template>
                      <template v-else>
                        Up to ৳{{ booking.budget_max }}
                      </template>
                    </span>
                  </div>
                  <div v-else>
                    <span class="text-gray-600">Budget:</span>
                    <span class="ml-2 text-gray-500 italic">Not specified</span>
                  </div>
                </div>
                <div class="mb-3">
                  <div
                    v-if="isPhotographerRole"
                    class="rounded-lg border border-rose-100 bg-rose-50/70 p-3"
                  >
                    <p class="text-[11px] uppercase tracking-wide text-rose-700 mb-2">
                      Photographer Details
                    </p>
                    <div class="flex flex-wrap items-center gap-2">
                      <a
                        v-if="booking.id"
                        :href="`/bookings/${booking.id}`"
                        class="px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg border border-rose-200 text-rose-700 hover:bg-rose-100 min-h-[40px] sm:min-h-0"
                      >
                        View Details
                      </a>
                      <router-link
                        v-if="booking.id"
                        :to="`/bookings/${booking.id}/messages`"
                        class="px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg border border-burgundy text-burgundy hover:bg-burgundy hover:text-white min-h-[40px] sm:min-h-0"
                      >
                        Open Messages
                      </router-link>
                    </div>
                  </div>
                  <div
                    v-else
                    class="flex flex-wrap items-center gap-2"
                  >
                    <router-link
                      v-if="booking.id"
                      :to="`/bookings/${booking.id}/messages`"
                      class="px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg border border-burgundy text-burgundy hover:bg-burgundy hover:text-white min-h-[40px] sm:min-h-0"
                    >
                      Open Messages
                    </router-link>
                    <router-link
                      to="/bookings"
                      class="px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 min-h-[40px] sm:min-h-0"
                    >
                      View Details
                    </router-link>
                  </div>
                </div>
                <div
                  v-if="booking.status === 'pending'"
                  class="flex flex-col sm:flex-row gap-2"
                >
                  <button 
                    class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium min-h-[44px] sm:min-h-0"
                    @click="updateBookingStatus(booking.id, 'confirmed')"
                  >
                    Accept
                  </button>
                  <button 
                    class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium min-h-[44px] sm:min-h-0"
                    @click="updateBookingStatus(booking.id, 'rejected')"
                  >
                    Decline
                  </button>
                </div>
                <div
                  v-else-if="booking.status === 'confirmed'"
                  class="flex flex-col sm:flex-row gap-2"
                >
                  <button 
                    class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium min-h-[44px] sm:min-h-0"
                    @click="updateBookingStatus(booking.id, 'completed')"
                  >
                    Mark Completed
                  </button>
                  <button 
                    class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm font-medium min-h-[44px] sm:min-h-0"
                    @click="updateBookingStatus(booking.id, 'cancelled')"
                  >
                    Cancel
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Portfolio Tab -->
          <div v-if="activeTab === 'portfolio'">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
              <h2 class="text-lg sm:text-xl font-bold">
                Portfolio Albums
              </h2>
              <button
                class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-burgundy text-white rounded hover:bg-[#6F112D] font-medium min-h-[44px] sm:min-h-0"
                @click="showAlbumModal = true"
              >
                + Add Album
              </button>
            </div>
            
            <EmptyState
              v-if="albums.length === 0"
              icon="camera"
              title="No albums yet"
              description="Create your first album to showcase your work!"
            />

            <div
              v-else
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6"
            >
              <div
                v-for="album in albums"
                :key="album.id"
                class="bg-white border rounded-lg overflow-hidden hover:shadow-lg transition-shadow"
              >
                <div class="h-48 bg-gray-200 relative">
                  <img
                    v-if="album.cover_photo"
                    :src="album.cover_photo"
                    :alt="album.name"
                    class="w-full h-full object-cover"
                  >
                  <div class="absolute inset-0 bg-black/40 flex items-center justify-center gap-2 opacity-0 hover:opacity-100 transition-opacity">
                    <button
                      class="px-3 py-2 bg-white text-burgundy rounded-lg font-medium text-sm hover:bg-gray-100"
                      @click="viewAlbum(album)"
                    >
                      View Photos
                    </button>
                    <button
                      class="px-3 py-2 bg-blue-500 text-white rounded-lg font-medium text-sm hover:bg-blue-600"
                      @click="editAlbum(album)"
                    >
                      Edit
                    </button>
                  </div>
                </div>
                <div class="p-4">
                  <h3 class="font-bold text-lg mb-1">
                    {{ album.name }}
                  </h3>
                  <p class="text-sm text-gray-600 mb-3">
                    {{ album.description }}
                  </p>
                  <div class="flex items-center justify-between text-sm mb-3">
                    <span class="text-gray-500">{{ album.photos_count || 0 }} photos</span>
                    <span
                      :class="`px-2 py-1 rounded text-xs ${album.is_public ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`"
                    >
                      {{ album.is_public ? 'Public' : 'Private' }}
                    </span>
                  </div>
                  <button
                    class="w-full px-3 py-2 border border-red-300 text-red-600 rounded hover:bg-red-50 font-medium text-sm"
                    @click="deleteAlbum(album)"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Album Modal -->
          <div
            v-if="showAlbumModal"
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
            @click.self="showAlbumModal = false"
          >
            <div class="bg-white rounded-lg max-w-md w-full p-6">
              <h3 class="text-xl font-bold mb-4">
                {{ editingAlbumId ? 'Edit Album' : 'Create New Album' }}
              </h3>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium mb-2">Album Name *</label>
                  <input
                    v-model="albumForm.name"
                    type="text"
                    class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                    placeholder="Wedding Photography"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium mb-2">Description</label>
                  <textarea
                    v-model="albumForm.description"
                    rows="3"
                    class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                    placeholder="Beautiful wedding moments captured..."
                  />
                </div>
                <div class="flex items-center gap-2">
                  <input
                    id="is_public"
                    v-model="albumForm.is_public"
                    type="checkbox"
                    class="w-4 h-4 text-burgundy focus:ring-burgundy"
                  >
                  <label
                    for="is_public"
                    class="text-sm font-medium"
                  >Make this album public</label>
                </div>
              </div>
              <div class="flex gap-3 mt-6">
                <button
                  :disabled="!albumForm.name || creatingAlbum"
                  class="flex-1 px-4 py-2 bg-burgundy text-white rounded hover:bg-[#6F112D] disabled:opacity-50 disabled:cursor-not-allowed"
                  @click="editingAlbumId ? updateAlbum() : createAlbum()"
                >
                  {{ creatingAlbum ? 'Saving...' : (editingAlbumId ? 'Update Album' : 'Create Album') }}
                </button>
                <button
                  class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50"
                  @click="closeAlbumModal"
                >
                  Cancel
                </button>
              </div>
            </div>
          </div>

          <!-- Album Photo Manager -->
          <AlbumPhotoManager
            v-if="selectedAlbum"
            :album="selectedAlbum"
            @close="closePhotoManager"
            @updated="closePhotoManager"
          />

          <!-- Packages Tab -->
          <div v-if="activeTab === 'packages'">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
              <h2 class="text-lg sm:text-xl font-bold">
                Service Packages
              </h2>
              <button
                class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-burgundy text-white rounded hover:bg-[#6F112D] font-medium min-h-[44px] sm:min-h-0"
                @click="showPackageModal = true"
              >
                + Add Package
              </button>
            </div>
            
            <EmptyState
              v-if="packages.length === 0"
              icon="package"
              title="No packages yet"
              description="Create service packages to attract clients!"
            />

            <div
              v-else
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6"
            >
              <div
                v-for="pkg in packages"
                :key="pkg.id"
                class="bg-white border rounded-lg overflow-hidden hover:shadow-lg transition-shadow"
              >
                <!-- Package Cover Image -->
                <div
                  v-if="pkg.cover_image"
                  class="h-48 bg-gray-100"
                >
                  <img
                    :src="pkg.cover_image"
                    :alt="pkg.name"
                    class="w-full h-full object-cover"
                  >
                </div>
                <div
                  v-else
                  class="h-48 bg-gradient-to-br from-burgundy/10 to-purple-100 flex items-center justify-center"
                >
                  <svg
                    class="w-16 h-16 text-gray-400"
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
                
                <div class="p-6">
                  <div class="flex items-start justify-between mb-4">
                    <div>
                      <h3 class="font-bold text-xl mb-1">
                        {{ pkg.name }}
                      </h3>
                      <p class="text-2xl font-bold text-burgundy">
                        ৳{{ pkg.price }}
                      </p>
                    </div>
                    <span
                      :class="`px-3 py-1 rounded-full text-xs font-medium ${pkg.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`"
                    >
                      {{ pkg.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </div>
                  
                  <p class="text-sm text-gray-600 mb-4">
                    {{ pkg.description }}
                  </p>
                  
                  <div class="space-y-2 mb-4">
                    <div class="flex items-center gap-2 text-sm">
                      <svg
                        class="w-4 h-4 text-green-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M5 13l4 4L19 7"
                        />
                      </svg>
                      <span>{{ pkg.duration_hours }} hours coverage</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                      <svg
                        class="w-4 h-4 text-green-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M5 13l4 4L19 7"
                        />
                      </svg>
                      <span>{{ pkg.edited_photos }} edited photos</span>
                    </div>
                    <div
                      v-if="pkg.raw_photos"
                      class="flex items-center gap-2 text-sm"
                    >
                      <svg
                        class="w-4 h-4 text-green-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M5 13l4 4L19 7"
                        />
                      </svg>
                      <span>{{ pkg.raw_photos }} raw photos</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                      <svg
                        class="w-4 h-4 text-green-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M5 13l4 4L19 7"
                        />
                      </svg>
                      <span>{{ pkg.delivery_days }} days delivery</span>
                    </div>
                  </div>

                  <div class="flex gap-2">
                    <button
                      class="flex-1 px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                      @click="editPackage(pkg)"
                    >
                      Edit
                    </button>
                    <button
                      class="px-3 py-2 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200"
                      @click="deletePackage(pkg)"
                    >
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Package Modal -->
          <div
            v-if="showPackageModal"
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
            @click.self="closePackageModal"
          >
            <div class="bg-white rounded-lg max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
              <h3 class="text-xl font-bold mb-4">
                {{ editingPackage ? 'Edit Package' : 'Create New Package' }}
              </h3>
              <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium mb-2">Package Name *</label>
                    <input
                      v-model="packageForm.name"
                      type="text"
                      class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                      placeholder="Basic Package"
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-2">Price (৳) *</label>
                    <input
                      v-model.number="packageForm.price"
                      type="number"
                      class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                      placeholder="15000"
                    >
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium mb-2">Description *</label>
                  <textarea
                    v-model="packageForm.description"
                    rows="3"
                    class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                    placeholder="Perfect for small events and gatherings..."
                  />
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded p-4">
                  <p class="text-sm font-medium mb-2">
                    📸 Package Images (Optional)
                  </p>
                  <div class="space-y-3">
                    <div>
                      <label class="block text-xs text-gray-600 mb-1">Cover Image URL</label>
                      <input
                        v-model="packageForm.cover_image"
                        type="url"
                        class="w-full border rounded px-3 py-2 text-sm focus:ring-2 focus:ring-purple-600"
                        placeholder="https://images.pexels.com/photos/..."
                      >
                      <p class="text-xs text-gray-500 mt-1">
                        Add a Pexels or external image URL
                      </p>
                    </div>
                    <div>
                      <label class="block text-xs text-gray-600 mb-1">Cover Image Upload</label>
                      <input
                        type="file"
                        accept="image/*"
                        class="upload-input text-sm"
                        @change="onPackageCoverSelected"
                      >
                      <p class="text-xs text-gray-500 mt-1">
                        Upload a local image file (JPG/PNG/WEBP, max 10MB, 1200x800 px)
                      </p>
                    </div>
                    <div
                      v-if="packageForm.cover_image"
                      class="mt-2"
                    >
                      <img
                        :src="packageForm.cover_image"
                        alt="Cover preview"
                        class="w-32 h-32 object-cover rounded"
                      >
                    </div>
                    <div
                      v-if="packageCoverFile"
                      class="text-xs text-green-700"
                    >
                      ✅ Selected cover file: {{ packageCoverFile.name }}
                    </div>

                    <div class="border-t pt-3">
                      <label class="block text-xs text-gray-600 mb-1">Sample Images (URLs)</label>
                      <div class="flex gap-2">
                        <input
                          v-model="packageSampleUrlInput"
                          type="url"
                          class="flex-1 border rounded px-3 py-2 text-sm focus:ring-2 focus:ring-purple-600"
                          placeholder="https://images.pexels.com/photos/..."
                        >
                        <button
                          type="button"
                          class="px-3 py-2 text-sm bg-purple-600 text-white rounded hover:bg-purple-700"
                          @click="addSampleUrl"
                        >
                          Add
                        </button>
                      </div>
                      <div
                        v-if="packageForm.sample_images?.length"
                        class="mt-2 flex flex-wrap gap-2"
                      >
                        <span
                          v-for="(url, idx) in packageForm.sample_images"
                          :key="url + idx"
                          class="inline-flex items-center gap-2 px-2 py-1 bg-white border rounded text-xs"
                        >
                          {{ url.length > 40 ? url.slice(0, 40) + '…' : url }}
                          <button
                            type="button"
                            class="text-red-600"
                            @click="removeSampleUrl(idx)"
                          >×</button>
                        </span>
                      </div>
                    </div>

                    <div class="border-t pt-3">
                      <label class="block text-xs text-gray-600 mb-1">Sample Images (Upload)</label>
                      <input
                        type="file"
                        accept="image/*"
                        multiple
                        class="upload-input text-sm"
                        @change="onPackageSamplesSelected"
                      >
                      <p class="text-xs text-gray-500 mt-1">
                        Upload up to 10 images (JPG/PNG/WEBP, max 10MB each, 1600x1200 px)
                      </p>
                      <div
                        v-if="packageSampleFiles.length"
                        class="text-xs text-green-700 mt-1"
                      >
                        ✅ Selected {{ packageSampleFiles.length }} file(s)
                      </div>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <div>
                    <label class="block text-sm font-medium mb-2">Coverage Hours *</label>
                    <input
                      v-model.number="packageForm.duration_hours"
                      type="number"
                      class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                      placeholder="4"
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-2">Edited Photos *</label>
                    <input
                      v-model.number="packageForm.edited_photos"
                      type="number"
                      class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                      placeholder="50"
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-2">Raw Photos</label>
                    <input
                      v-model.number="packageForm.raw_photos"
                      type="number"
                      class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                      placeholder="0"
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-2">Delivery Days *</label>
                    <input
                      v-model.number="packageForm.delivery_days"
                      type="number"
                      class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                      placeholder="7"
                    >
                  </div>
                </div>

                <div class="flex items-center gap-2">
                  <input
                    id="pkg_is_active"
                    v-model="packageForm.is_active"
                    type="checkbox"
                    class="w-4 h-4 text-burgundy focus:ring-burgundy"
                  >
                  <label
                    for="pkg_is_active"
                    class="text-sm font-medium"
                  >Make this package active</label>
                </div>
              </div>
              
              <div class="flex gap-3 mt-6">
                <button
                  :disabled="!packageForm.name || !packageForm.price || creatingPackage"
                  class="flex-1 px-4 py-2 bg-burgundy text-white rounded hover:bg-[#6F112D] disabled:opacity-50 disabled:cursor-not-allowed"
                  @click="savePackage"
                >
                  {{ creatingPackage ? 'Saving...' : (editingPackage ? 'Update Package' : 'Create Package') }}
                </button>
                <button
                  class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50"
                  @click="closePackageModal"
                >
                  Cancel
                </button>
              </div>
            </div>
          </div>

          <!-- Reviews Tab -->
          <div v-if="activeTab === 'reviews'">
            <h2 class="text-xl font-bold mb-4">
              Client Reviews
            </h2>
            <EmptyState
              icon="star"
              title="No reviews yet"
              description="Your client reviews will appear here after completing bookings"
            />
          </div>

          <!-- Competitions Tab -->
          <div v-if="activeTab === 'competitions'">
            <div class="mb-6">
              <h2 class="text-xl font-bold">
                My Competition Submissions
              </h2>
              <p class="text-sm text-gray-600 mt-1">
                View and manage your submissions to photography competitions
              </p>
            </div>

            <!-- Loading State -->
            <div
              v-if="loadingSubmissions"
              class="grid grid-cols-1 gap-4"
            >
              <LoadingSkeleton
                v-for="n in 3"
                :key="n"
                type="card"
              />
            </div>

            <!-- Submissions List -->
            <div
              v-else-if="submissions.length > 0"
              class="space-y-4"
            >
              <div
                v-for="submission in submissions"
                :key="submission.id"
                class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
              >
                <div class="flex items-start gap-4">
                  <!-- Submission Image -->
                  <div class="w-32 h-32 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                    <img 
                      v-if="submission.thumbnail_url || submission.image_url || submission.photo_url" 
                      :src="submission.thumbnail_url || submission.image_url || submission.photo_url" 
                      :alt="submission.title"
                      class="w-full h-full object-cover"
                    >
                    <div
                      v-else
                      class="w-full h-full flex items-center justify-center"
                    >
                      <svg
                        class="w-12 h-12 text-gray-400"
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
                  </div>

                  <!-- Submission Details -->
                  <div class="flex-1">
                    <h3 class="font-semibold text-lg mb-1">
                      {{ submission.title }}
                    </h3>
                    <p class="text-sm text-gray-600 mb-3">
                      {{ submission.description }}
                    </p>
                    
                    <!-- Competition Info -->
                    <div class="flex items-center gap-2 mb-2">
                      <svg
                        class="w-4 h-4 text-gray-500"
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
                      <router-link
                        :to="`/competitions/${submission.competition?.slug}`"
                        class="text-sm text-burgundy hover:underline"
                      >
                        {{ submission.competition?.title }}
                      </router-link>
                      <span 
                        class="px-2 py-1 text-xs rounded-full"
                        :class="{
                          'bg-green-100 text-green-800': submission.competition?.status === 'active',
                          'bg-yellow-100 text-yellow-800': submission.competition?.status === 'voting',
                          'bg-blue-100 text-blue-800': submission.competition?.status === 'judging',
                          'bg-gray-100 text-gray-800': submission.competition?.status === 'completed'
                        }"
                      >
                        {{ submission.competition?.status }}
                      </span>
                    </div>

                    <!-- Stats -->
                    <div class="flex items-center gap-4 text-sm text-gray-600">
                      <div class="flex items-center gap-1">
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
                            d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"
                          />
                        </svg>
                        <span>{{ submission.votes_count || 0 }} votes</span>
                      </div>
                      <div class="flex items-center gap-1">
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
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                          />
                        </svg>
                        <span>Submitted {{ formatDate(submission.created_at) }}</span>
                      </div>
                    </div>
                  </div>

                  <!-- View Button -->
                  <router-link
                    :to="`/competitions/${submission.competition?.slug}`"
                    class="px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors text-sm font-medium"
                  >
                    View Competition
                  </router-link>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div
              v-else
              class="space-y-4"
            >
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                  <svg
                    class="w-5 h-5 text-blue-600 mt-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                  <div>
                    <h3 class="font-semibold text-blue-900">
                      Participate in Competitions
                    </h3>
                    <p class="text-sm text-blue-700 mt-1">
                      Submit your best work to photography competitions and compete for prizes.
                    </p>
                    <ul class="mt-2 text-sm text-blue-700 space-y-1">
                      <li>• Browse active competitions in the Competitions page</li>
                      <li>• Submit your best photographs</li>
                      <li>• Get public votes and judge ratings</li>
                      <li>• Win prizes and build your reputation</li>
                    </ul>
                    <router-link
                      to="/competitions"
                      class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium"
                    >
                      Browse Competitions
                    </router-link>
                  </div>
                </div>
              </div>
              <div class="text-center py-12 text-gray-600">
                <svg
                  class="w-16 h-16 mx-auto text-gray-400 mb-4"
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
                <p class="text-lg font-medium mb-2">
                  No submissions yet
                </p>
                <p class="text-sm mb-4">
                  Submit to a competition to see your entries here
                </p>
              </div>
            </div>
          </div>

          <!-- Events Tab -->
          <div v-if="activeTab === 'events'">
            <div class="mb-6">
              <h2 class="text-xl font-bold">
                My Events
              </h2>
              <p class="text-sm text-gray-600 mt-1">
                Events you've registered for or attended
              </p>
            </div>

            <!-- Events coming soon notice -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
              <div class="flex items-start gap-3">
                <svg
                  class="w-5 h-5 text-blue-600 mt-0.5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
                <div>
                  <h3 class="font-semibold text-blue-900">
                    Participate in Events
                  </h3>
                  <p class="text-sm text-blue-700 mt-1">
                    Browse photography events, workshops, and meetups organized by admins.
                  </p>
                  <router-link
                    to="/events"
                    class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium"
                  >
                    Browse Events
                  </router-link>
                </div>
              </div>
            </div>

            <!-- Create Event Form -->
            <div
              v-if="showEventForm"
              class="bg-white border rounded-lg p-6 mb-6"
            >
              <h3 class="text-lg font-semibold mb-4">
                Create New Event
              </h3>
              <form
                class="space-y-4"
                @submit.prevent="createEvent"
              >
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Event Title *</label>
                  <input
                    v-model="eventForm.title"
                    type="text"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                    placeholder="Photography Workshop 2026"
                  >
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Type *</label>
                    <select
                      v-model="eventForm.event_type"
                      required
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                    >
                      <option value="">
                        Select type
                      </option>
                      <option value="workshop">
                        Workshop
                      </option>
                      <option value="exhibition">
                        Exhibition
                      </option>
                      <option value="meetup">
                        Meetup
                      </option>
                      <option value="competition">
                        Competition
                      </option>
                      <option value="seminar">
                        Seminar
                      </option>
                      <option value="other">
                        Other
                      </option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Date *</label>
                    <input
                      v-model="eventForm.event_date"
                      type="datetime-local"
                      required
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                    >
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                  <textarea
                    v-model="eventForm.description"
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                    placeholder="Describe your event..."
                  />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                    <input
                      v-model="eventForm.location"
                      type="text"
                      required
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                      placeholder="123 Main St"
                    >
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                    <select
                      v-model="eventForm.city_id"
                      required
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                    >
                      <option value="">
                        Select location
                      </option>
                      <option
                        v-for="city in cities"
                        :key="city.id"
                        :value="city.id"
                      >
                        {{ city.name }}
                      </option>
                    </select>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Attendees</label>
                    <input
                      v-model.number="eventForm.max_attendees"
                      type="number"
                      min="1"
                      max="500"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                      placeholder="50"
                    >
                    <p class="text-xs text-gray-500 mt-1">
                      Max 500 for photographers
                    </p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ticket Price (৳)</label>
                    <input
                      v-model.number="eventForm.ticket_price"
                      type="number"
                      min="0"
                      max="50000"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                      placeholder="500"
                    >
                    <p class="text-xs text-gray-500 mt-1">
                      Max ৳50,000
                    </p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duration (hours)</label>
                    <input
                      v-model.number="eventForm.duration_hours"
                      type="number"
                      min="0.5"
                      max="24"
                      step="0.5"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                      placeholder="2.5"
                    >
                    <p class="text-xs text-gray-500 mt-1">
                      Max 24 hours
                    </p>
                  </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                  <div class="flex items-start gap-3">
                    <svg
                      class="w-5 h-5 text-yellow-600 mt-0.5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                    <div class="text-sm text-yellow-800">
                      <p class="font-medium">
                        Event Approval Required
                      </p>
                      <p>Your event will be saved as a draft and requires admin approval before going live. You'll be notified once approved.</p>
                    </div>
                  </div>
                </div>

                <div class="flex justify-end gap-3">
                  <button
                    type="button"
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
                    @click="showEventForm = false"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    :disabled="creatingEvent"
                    class="px-6 py-2 bg-burgundy text-white rounded-lg hover:bg-[#6F112D] disabled:opacity-50"
                  >
                    {{ creatingEvent ? 'Creating...' : 'Create Event' }}
                  </button>
                </div>
              </form>
            </div>

            <!-- Events List -->
            <EmptyState
              v-if="events.length === 0 && !showEventForm"
              icon="calendar"
              title="No events yet"
              description="Create your first event to get started"
            />

            <div
              v-else
              class="space-y-4"
            >
              <div
                v-for="event in events"
                :key="event.id"
                class="bg-white border rounded-lg p-6 hover:shadow-md transition-shadow"
              >
                <div class="flex justify-between items-start mb-3">
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                      <h3 class="text-lg font-bold">
                        {{ event.title }}
                      </h3>
                      <span
                        :class="{
                          'px-2 py-1 rounded text-xs font-medium': true,
                          'bg-yellow-100 text-yellow-800': event.status === 'draft',
                          'bg-green-100 text-green-800': event.status === 'published',
                          'bg-red-100 text-red-800': event.status === 'cancelled'
                        }"
                      >
                        {{ event.status }}
                      </span>
                    </div>
                    <p class="text-sm text-gray-600">
                      {{ event.event_type }} • {{ event.location }}
                    </p>
                  </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-4">
                  <div>
                    <span class="text-gray-600">Date:</span>
                    <p class="font-medium">
                      {{ formatDate(event.event_date) }}
                    </p>
                  </div>
                  <div>
                    <span class="text-gray-600">RSVPs:</span>
                    <p class="font-medium">
                      {{ event.rsvp_count || 0 }}
                    </p>
                  </div>
                  <div>
                    <span class="text-gray-600">Views:</span>
                    <p class="font-medium">
                      {{ event.view_count || 0 }}
                    </p>
                  </div>
                  <div>
                    <span class="text-gray-600">Price:</span>
                    <p class="font-medium">
                      {{ event.ticket_price ? `৳${event.ticket_price}` : 'Free' }}
                    </p>
                  </div>
                </div>

                <div class="flex gap-2">
                  <button
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm"
                    @click="viewEvent(event)"
                  >
                    View
                  </button>
                  <button
                    v-if="event.status === 'draft'"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm"
                    @click="editEvent(event)"
                  >
                    Edit
                  </button>
                  <button
                    v-if="event.status === 'published'"
                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm"
                    @click="cancelEvent(event)"
                  >
                    Cancel Event
                  </button>
                  <button
                    v-if="event.status === 'draft' && (event.rsvp_count === 0 || !event.rsvp_count)"
                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm"
                    @click="deleteEvent(event)"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </div>

            <!-- Registered Events Section -->
            <div
              v-if="eventRsvps.length > 0"
              class="mt-8"
            >
              <h3 class="text-lg font-bold mb-4">
                Registered Events
              </h3>
              <p class="text-sm text-gray-600 mb-4">
                Events you've registered to attend
              </p>
              
              <!-- Loading State -->
              <div
                v-if="loadingEventRsvps"
                class="flex items-center justify-center py-8"
              >
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-burgundy" />
              </div>

              <!-- RSVPs List -->
              <div
                v-else
                class="space-y-4"
              >
                <div
                  v-for="rsvp in eventRsvps"
                  :key="rsvp.id"
                  class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
                >
                  <div class="flex items-start gap-4">
                    <!-- Event Banner -->
                    <div
                      v-if="rsvp.event?.banner_image"
                      class="w-24 h-24 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100"
                    >
                      <img 
                        :src="rsvp.event.banner_image" 
                        :alt="rsvp.event.title"
                        class="w-full h-full object-cover"
                      >
                    </div>
                    
                    <!-- Event Details -->
                    <div class="flex-1">
                      <div class="flex items-start justify-between mb-2">
                        <div>
                          <h4 class="font-semibold text-lg mb-1">
                            {{ rsvp.event?.title }}
                          </h4>
                          <p class="text-sm text-gray-600">
                            {{ rsvp.event?.location_text || 'Location TBA' }}
                          </p>
                        </div>
                        <span 
                          class="px-3 py-1 text-xs rounded-full"
                          :class="{
                            'bg-green-100 text-green-800': rsvp.rsvp_status === 'going',
                            'bg-gray-100 text-gray-800': rsvp.rsvp_status === 'not_going'
                          }"
                        >
                          {{ rsvp.rsvp_status === 'going' ? 'Registered' : 'Cancelled' }}
                        </span>
                      </div>

                      <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                        <div class="flex items-center gap-2">
                          <svg
                            class="w-4 h-4 text-gray-500"
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
                          <span class="text-gray-700">
                            {{ formatDate(rsvp.event?.event_date) }}
                          </span>
                        </div>
                        <div class="flex items-center gap-2">
                          <svg
                            class="w-4 h-4 text-gray-500"
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
                          <span class="text-gray-700">{{ rsvp.event?.city?.name }}</span>
                        </div>
                      </div>

                      <div class="flex gap-2">
                        <router-link
                          :to="`/events/${rsvp.event?.slug}`"
                          class="px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors text-sm font-medium"
                        >
                          View Event
                        </router-link>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Achievements Tab -->
          <div
            v-if="activeTab === 'achievements'"
            class="p-4 sm:p-6"
          >
            <div class="flex flex-col lg:flex-row gap-6">
              <div class="flex-1 bg-gradient-to-r from-burgundy to-[#8B1538] rounded-lg p-6 text-white">
                <h2 class="text-xl font-bold mb-2">
                  Your Progress
                </h2>
                <p class="text-sm text-white/90 mb-4">
                  Track your achievements, points, and level
                </p>

                <div
                  v-if="loadingAchievementsSummary"
                  class="text-sm text-white/80"
                >
                  Loading achievements...
                </div>
                <div
                  v-else
                  class="grid grid-cols-2 sm:grid-cols-4 gap-4"
                >
                  <div>
                    <p class="text-2xl font-bold">
                      {{ achievementsSummary.stats?.level || 1 }}
                    </p>
                    <p class="text-xs text-white/80">
                      Level
                    </p>
                  </div>
                  <div>
                    <p class="text-2xl font-bold">
                      {{ achievementsSummary.stats?.total_points || 0 }}
                    </p>
                    <p class="text-xs text-white/80">
                      Total Points
                    </p>
                  </div>
                  <div>
                    <p class="text-2xl font-bold">
                      {{ achievementsSummary.unlocked_achievements || 0 }}/{{ achievementsSummary.total_achievements || 0 }}
                    </p>
                    <p class="text-xs text-white/80">
                      Unlocked
                    </p>
                  </div>
                  <div>
                    <p class="text-2xl font-bold">
                      {{ achievementsSummary.completion_percentage || 0 }}%
                    </p>
                    <p class="text-xs text-white/80">
                      Completion
                    </p>
                  </div>
                </div>
              </div>

              <div class="w-full lg:w-80 bg-white border rounded-lg p-6">
                <h3 class="text-lg font-bold mb-2">
                  Next Level
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                  Keep earning points to level up!
                </p>
                <div class="mb-4">
                  <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Points Needed</span>
                    <span class="font-semibold">{{ achievementsSummary.points_to_next_level || 0 }}</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div
                      class="bg-burgundy h-2 rounded-full"
                      :style="{ width: ((achievementsSummary.stats?.total_points || 0) % 100) + '%' }"
                    />
                  </div>
                </div>
                <a
                  href="/photographer/achievements"
                  class="block text-center px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors"
                >
                  View All Achievements
                </a>
              </div>
            </div>
          </div>

          <!-- Awards Tab -->
          <div
            v-if="activeTab === 'awards'"
            class="p-4 sm:p-6"
          >
            <div class="mb-6">
              <div class="flex items-center justify-between">
                <div>
                  <h2 class="text-xl font-bold">
                    Awards & Achievements
                  </h2>
                  <p class="text-sm text-gray-600 mt-1">
                    Showcase your awards, certifications, and achievements
                  </p>
                </div>
                <button
                  class="px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors flex items-center gap-2"
                  @click="showAddAwardModal = true"
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
                      d="M12 4v16m8-8H4"
                    />
                  </svg>
                  Add Award
                </button>
              </div>
            </div>

            <!-- Loading State -->
            <div
              v-if="loadingAwards"
              class="text-center py-12"
            >
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-burgundy" />
              <p class="text-sm text-gray-600 mt-2">
                Loading awards...
              </p>
            </div>

            <!-- Empty State -->
            <div
              v-else-if="awards.length === 0"
              class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed"
            >
              <svg
                class="w-16 h-16 mx-auto text-gray-400 mb-4"
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
              <h3 class="text-lg font-semibold text-gray-900 mb-2">
                No Awards Yet
              </h3>
              <p class="text-sm text-gray-600 mb-4">
                Start building your credibility by adding your awards and achievements
              </p>
              <button
                class="px-6 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors"
                @click="showAddAwardModal = true"
              >
                Add Your First Award
              </button>
            </div>

            <!-- Awards List -->
            <div
              v-else
              class="space-y-4"
            >
              <div
                v-for="award in awards"
                :key="award.id"
                class="bg-white border rounded-lg p-4 hover:shadow-md transition-shadow"
              >
                <div class="flex items-start gap-4">
                  <!-- Award Icon -->
                  <div class="flex-shrink-0">
                    <div :class="`w-12 h-12 rounded-full flex items-center justify-center ${getAwardColor(award.type)}`">
                      <span class="text-2xl">{{ getAwardIcon(award.type) }}</span>
                    </div>
                  </div>

                  <!-- Award Content -->
                  <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                      <div class="flex-1">
                        <h3 class="font-semibold text-lg text-gray-900">
                          {{ award.title }}
                        </h3>
                        <p
                          v-if="award.organization"
                          class="text-sm text-gray-600 mt-1"
                        >
                          {{ award.organization }}
                        </p>
                      </div>
                      <div class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-burgundy/10 text-burgundy rounded-full text-sm font-medium">
                          {{ award.year }}
                        </span>
                        <span :class="`px-3 py-1 rounded-full text-xs font-medium ${getTypeBadgeColor(award.type)}`">
                          {{ award.type }}
                        </span>
                      </div>
                    </div>

                    <p
                      v-if="award.description"
                      class="text-sm text-gray-600 mt-2"
                    >
                      {{ award.description }}
                    </p>

                    <!-- Certificate Link -->
                    <a
                      v-if="award.certificate_url"
                      :href="award.certificate_url"
                      target="_blank"
                      class="inline-flex items-center gap-1 text-sm text-burgundy hover:underline mt-2"
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

                    <!-- Actions -->
                    <div class="flex gap-2 mt-3">
                      <button
                        class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                        @click="editAward(award)"
                      >
                        Edit
                      </button>
                      <button
                        class="px-3 py-1 text-sm bg-red-50 text-red-600 rounded hover:bg-red-100"
                        @click="deleteAward(award.id)"
                      >
                        Delete
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Add/Edit Award Modal -->
      <teleport to="body">
        <div
          v-if="showAddAwardModal"
          class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
          @click.self="closeAwardModal"
        >
          <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between">
              <h3 class="text-xl font-bold">
                {{ editingAward ? 'Edit Award' : 'Add New Award' }}
              </h3>
              <button
                class="text-gray-400 hover:text-gray-600"
                @click="closeAwardModal"
              >
                <svg
                  class="w-6 h-6"
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
            </div>

            <form
              class="p-6 space-y-4"
              @submit.prevent="saveAward"
            >
              <!-- Title -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Award Title *
                </label>
                <input
                  v-model="awardForm.title"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                  placeholder="Best Wedding Photographer 2024"
                >
                <p
                  v-if="awardErrors.title"
                  class="text-sm text-red-600 mt-1"
                >
                  {{ awardErrors.title[0] }}
                </p>
              </div>

              <!-- Organization -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Organization / Issuer
                </label>
                <input
                  v-model="awardForm.organization"
                  type="text"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                  placeholder="Bangladesh Photography Association"
                >
              </div>

              <!-- Year and Type -->
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Year *
                  </label>
                  <input
                    v-model.number="awardForm.year"
                    type="number"
                    required
                    min="1950"
                    :max="currentYear + 1"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                  >
                  <p
                    v-if="awardErrors.year"
                    class="text-sm text-red-600 mt-1"
                  >
                    {{ awardErrors.year[0] }}
                  </p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Type *
                  </label>
                  <select
                    v-model="awardForm.type"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                  >
                    <option value="award">
                      🏆 Award
                    </option>
                    <option value="achievement">
                      ⭐ Achievement
                    </option>
                    <option value="recognition">
                      🎖️ Recognition
                    </option>
                    <option value="certification">
                      📜 Certification
                    </option>
                  </select>
                </div>
              </div>

              <!-- Description -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Description
                </label>
                <textarea
                  v-model="awardForm.description"
                  rows="3"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                  placeholder="Brief description of the award and what you achieved..."
                />
              </div>

              <!-- Certificate Upload -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Certificate (JPG, PNG, or PDF - Max 5MB)
                </label>
                <input
                  type="file"
                  accept="image/jpeg,image/jpg,image/png,application/pdf"
                  class="upload-input"
                  @change="handleCertificateUpload"
                >
                <p class="mt-1 upload-hint">Images: 2000x1400 px recommended. PDF accepted.</p>
                <p
                  v-if="awardErrors.certificate_file"
                  class="text-sm text-red-600 mt-1"
                >
                  {{ awardErrors.certificate_file[0] }}
                </p>
                <p
                  v-if="editingAward && editingAward.certificate_url"
                  class="text-sm text-gray-600 mt-1"
                >
                  Current certificate: 
                  <a
                    :href="editingAward.certificate_url"
                    target="_blank"
                    class="text-burgundy hover:underline"
                  >View</a>
                </p>
              </div>

              <!-- Error Message -->
              <div
                v-if="awardErrorMessage"
                class="p-3 bg-red-50 border border-red-200 rounded-lg"
              >
                <p class="text-sm text-red-600">
                  {{ awardErrorMessage }}
                </p>
              </div>

              <!-- Buttons -->
              <div class="flex gap-3 pt-4">
                <button
                  type="submit"
                  :disabled="savingAward"
                  class="flex-1 px-6 py-3 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark disabled:opacity-50 disabled:cursor-not-allowed font-medium"
                >
                  {{ savingAward ? 'Saving...' : (editingAward ? 'Update Award' : 'Add Award') }}
                </button>
                <button
                  type="button"
                  class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium"
                  @click="closeAwardModal"
                >
                  Cancel
                </button>
              </div>
            </form>
          </div>
        </div>
      </teleport>

      <!-- Quick Links -->
      <div class="mt-4 sm:mt-6 md:mt-8 bg-white rounded-2xl border border-gray-200/80 shadow-sm p-4 sm:p-6">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
          <h3 class="text-base sm:text-lg font-semibold">
            Quick Links
          </h3>
          <span class="text-xs sm:text-sm text-gray-500">Shortcuts</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5 sm:gap-3">
          <router-link
            to="/competitions"
            class="flex flex-col items-center p-2.5 sm:p-3 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 active:bg-red-100 transition-all group min-h-[72px] sm:min-h-0 hover:-translate-y-0.5 hover:shadow-md focus-visible:ring-2 focus-visible:ring-burgundy/30"
          >
            <span class="flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-burgundy/10 text-burgundy group-hover:bg-burgundy/15 mb-1.5">
              <svg
                class="w-4.5 h-4.5 sm:w-5 sm:h-5 shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
                />
              </svg>
            </span>
            <span class="text-[11px] sm:text-xs font-medium text-center leading-tight">Competitions</span>
          </router-link>

          <router-link
            to="/events"
            class="flex flex-col items-center p-2.5 sm:p-3 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 active:bg-red-100 transition-all group min-h-[72px] sm:min-h-0 hover:-translate-y-0.5 hover:shadow-md focus-visible:ring-2 focus-visible:ring-burgundy/30"
          >
            <span class="flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-burgundy/10 text-burgundy group-hover:bg-burgundy/15 mb-1.5">
              <svg
                class="w-4.5 h-4.5 sm:w-5 sm:h-5 shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                />
              </svg>
            </span>
            <span class="text-[11px] sm:text-xs font-medium text-center leading-tight">Events</span>
          </router-link>

          <router-link
            to="/transactions"
            class="flex flex-col items-center p-2.5 sm:p-3 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 active:bg-red-100 transition-all group min-h-[72px] sm:min-h-0 hover:-translate-y-0.5 hover:shadow-md focus-visible:ring-2 focus-visible:ring-burgundy/30"
          >
            <span class="flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-burgundy/10 text-burgundy group-hover:bg-burgundy/15 mb-1.5">
              <svg
                class="w-4.5 h-4.5 sm:w-5 sm:h-5 shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
                />
              </svg>
            </span>
            <span class="text-[11px] sm:text-xs font-medium text-center leading-tight">Transactions</span>
          </router-link>

          <router-link
            to="/notifications"
            class="flex flex-col items-center p-2.5 sm:p-3 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 active:bg-red-100 transition-all group min-h-[72px] sm:min-h-0 hover:-translate-y-0.5 hover:shadow-md focus-visible:ring-2 focus-visible:ring-burgundy/30"
          >
            <span class="flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-burgundy/10 text-burgundy group-hover:bg-burgundy/15 mb-1.5">
              <svg
                class="w-4.5 h-4.5 sm:w-5 sm:h-5 shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                />
              </svg>
            </span>
            <span class="text-[11px] sm:text-xs font-medium text-center leading-tight">Notifications</span>
          </router-link>

          <router-link
            to="/settings"
            class="flex flex-col items-center p-2.5 sm:p-3 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 active:bg-red-100 transition-all group min-h-[72px] sm:min-h-0 hover:-translate-y-0.5 hover:shadow-md focus-visible:ring-2 focus-visible:ring-burgundy/30"
          >
            <span class="flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-burgundy/10 text-burgundy group-hover:bg-burgundy/15 mb-1.5">
              <svg
                class="w-4.5 h-4.5 sm:w-5 sm:h-5 shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M11.25 3a.75.75 0 00-.75.75v1.142a7.5 7.5 0 00-1.77.738l-.807-.807a.75.75 0 00-1.06 0l-1.06 1.06a.75.75 0 000 1.06l.807.807a7.5 7.5 0 00-.738 1.77H3.75a.75.75 0 00-.75.75v1.5c0 .414.336.75.75.75h1.142a7.5 7.5 0 00.738 1.77l-.807.807a.75.75 0 000 1.06l1.06 1.06c.293.293.768.293 1.06 0l.807-.807a7.5 7.5 0 001.77.738v1.142c0 .414.336.75.75.75h1.5a.75.75 0 00.75-.75v-1.142a7.5 7.5 0 001.77-.738l.807.807c.293.293.768.293 1.06 0l1.06-1.06a.75.75 0 000-1.06l-.807-.807a7.5 7.5 0 00.738-1.77h1.142a.75.75 0 00.75-.75v-1.5a.75.75 0 00-.75-.75h-1.142a7.5 7.5 0 00-.738-1.77l.807-.807a.75.75 0 000-1.06l-1.06-1.06a.75.75 0 00-1.06 0l-.807.807a7.5 7.5 0 00-1.77-.738V3.75a.75.75 0 00-.75-.75h-1.5z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                />
              </svg>
            </span>
            <span class="text-[11px] sm:text-xs font-medium text-center leading-tight">Account Settings</span>
          </router-link>

          <router-link
            to="/verification"
            class="flex flex-col items-center p-2.5 sm:p-3 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 active:bg-red-100 transition-all group min-h-[72px] sm:min-h-0 hover:-translate-y-0.5 hover:shadow-md focus-visible:ring-2 focus-visible:ring-burgundy/30"
          >
            <span class="flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-burgundy/10 text-burgundy group-hover:bg-burgundy/15 mb-1.5">
              <svg
                class="w-4.5 h-4.5 sm:w-5 sm:h-5 shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </span>
            <span class="text-[11px] sm:text-xs font-medium text-center leading-tight">Verify Profile</span>
          </router-link>

          <router-link
            to="/help"
            class="flex flex-col items-center p-2.5 sm:p-3 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 active:bg-red-100 transition-all group min-h-[72px] sm:min-h-0 hover:-translate-y-0.5 hover:shadow-md focus-visible:ring-2 focus-visible:ring-burgundy/30"
          >
            <span class="flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-burgundy/10 text-burgundy group-hover:bg-burgundy/15 mb-1.5">
              <svg
                class="w-4.5 h-4.5 sm:w-5 sm:h-5 shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M18.364 5.636a9 9 0 11-12.728 0 9 9 0 0112.728 0z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M12 7v5l3 3"
                />
              </svg>
            </span>
            <span class="text-[11px] sm:text-xs font-medium text-center leading-tight">Support Center</span>
          </router-link>

          <router-link
            to="/photographer/achievements"
            class="flex flex-col items-center p-2.5 sm:p-3 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 active:bg-red-100 transition-all group min-h-[72px] sm:min-h-0 hover:-translate-y-0.5 hover:shadow-md focus-visible:ring-2 focus-visible:ring-burgundy/30"
          >
            <span class="flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-burgundy/10 text-burgundy group-hover:bg-burgundy/15 mb-1.5">
              <svg
                class="w-4.5 h-4.5 sm:w-5 sm:h-5 shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </span>
            <span class="text-[11px] sm:text-xs font-medium text-center leading-tight">Certificates</span>
          </router-link>
        </div>
      </div>

      <!-- Photography Resources -->
      <div class="mt-4 sm:mt-6 md:mt-8 bg-white rounded-lg shadow p-4 sm:p-6">
        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">
          Photography Resources
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4">
          <a
            class="flex flex-col items-center p-3 sm:p-4 border border-gray-200 rounded-lg hover:border-burgundy hover:bg-burgundy/5 active:bg-burgundy/10 transition-all min-h-[88px] sm:min-h-0"
            href="https://www.pexels.com"
            target="_blank"
            rel="noopener"
          >
            <img
              src="/images/pexels.webp"
              alt="Pexels"
              class="w-8 h-8 mb-2"
              loading="lazy"
            >
            <span class="text-sm font-semibold">Pexels</span>
            <span class="text-xs text-gray-500 text-center">Free stock photos</span>
          </a>
          <a
            class="flex flex-col items-center p-3 sm:p-4 border border-gray-200 rounded-lg hover:border-burgundy hover:bg-burgundy/5 active:bg-burgundy/10 transition-all min-h-[88px] sm:min-h-0"
            href="https://unsplash.com"
            target="_blank"
            rel="noopener"
          >
            <span class="text-sm font-semibold">Unsplash</span>
            <span class="text-xs text-gray-500 text-center">Editorial visuals</span>
          </a>
          <a
            class="flex flex-col items-center p-3 sm:p-4 border border-gray-200 rounded-lg hover:border-burgundy hover:bg-burgundy/5 active:bg-burgundy/10 transition-all min-h-[88px] sm:min-h-0"
            href="https://pixabay.com"
            target="_blank"
            rel="noopener"
          >
            <span class="text-sm font-semibold">Pixabay</span>
            <span class="text-xs text-gray-500 text-center">Free photos and videos</span>
          </a>
          <a
            class="flex flex-col items-center p-3 sm:p-4 border border-gray-200 rounded-lg hover:border-burgundy hover:bg-burgundy/5 active:bg-burgundy/10 transition-all min-h-[88px] sm:min-h-0"
            href="https://www.behance.net"
            target="_blank"
            rel="noopener"
          >
            <span class="text-sm font-semibold">Behance</span>
            <span class="text-xs text-gray-500 text-center">Portfolios and inspiration</span>
          </a>
        </div>
      </div>
    </div>

    <!-- Toast Notifications -->
    <div class="fixed top-4 right-4 z-50 space-y-2">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        class="flex items-center gap-3 min-w-[300px] max-w-md px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300"
        :class="{
          'bg-green-500 text-white': toast.type === 'success',
          'bg-red-500 text-white': toast.type === 'error',
          'bg-blue-500 text-white': toast.type === 'info',
          'bg-yellow-500 text-white': toast.type === 'warning'
        }"
      >
        <svg
          v-if="toast.type === 'success'"
          class="w-5 h-5 flex-shrink-0"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
            clip-rule="evenodd"
          />
        </svg>
        <svg
          v-else-if="toast.type === 'error'"
          class="w-5 h-5 flex-shrink-0"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
            clip-rule="evenodd"
          />
        </svg>
        <svg
          v-else-if="toast.type === 'info'"
          class="w-5 h-5 flex-shrink-0"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
            clip-rule="evenodd"
          />
        </svg>
        <svg
          v-else
          class="w-5 h-5 flex-shrink-0"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
            clip-rule="evenodd"
          />
        </svg>
        <p class="flex-1 text-sm font-medium">
          {{ toast.message }}
        </p>
        <button
          class="flex-shrink-0 hover:opacity-75"
          @click="removeToast(toast.id)"
        >
          <svg
            class="w-4 h-4"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import api from '../api';
import { validateUploadFile } from '../utils/imageValidation';
import ImageUpload from './ImageUpload.vue';
import AlbumPhotoManager from './AlbumPhotoManager.vue';
import NotificationBell from './NotificationBell.vue';
import LoadingSkeleton from './ui/LoadingSkeleton.vue';
import EmptyState from './ui/EmptyState.vue';
import { formatDate as formatDateValue } from '../utils/formatters';

const user = ref(null);
const activeTab = ref('bookings');
const stats = ref({});
const photographer = ref(null);
const bookings = ref([]);
const showEventForm = ref(false);
const creatingEvent = ref(false);
const events = ref([]);
const cities = ref([]);
const achievementsSummary = ref({
  stats: { level: 1, total_points: 0 },
  total_achievements: 0,
  unlocked_achievements: 0,
  completion_percentage: 0,
  points_to_next_level: 0,
});
const loadingAchievementsSummary = ref(false);
const showAlbumModal = ref(false);
const showPackageModal = ref(false);
const copied = ref(false);
const categories = ref([]);
const hashtags = ref([]);
const albums = ref([]);
const creatingAlbum = ref(false);
const selectedAlbum = ref(null);
const editingAlbumId = ref(null);
const packages = ref([]);
const creatingPackage = ref(false);
const editingPackage = ref(null);

// Competition submissions
const submissions = ref([]);
const loadingSubmissions = ref(false);

// Event RSVPs
const eventRsvps = ref([]);
const loadingEventRsvps = ref(false);

// Toast notifications
const toasts = ref([]);
let toastId = 0;

const disallowedBookingRoles = ['judge', 'admin', 'super_admin', 'moderator'];
const normalizeRole = (role) => String(role || '').toLowerCase().replace(/[\s-]+/g, '_');
const userRole = computed(() => {
  const storedUser = JSON.parse(localStorage.getItem('user') || '{}');
  return normalizeRole(localStorage.getItem('user_role') || storedUser.role || user.value?.role);
});
const canViewBookings = computed(() => !disallowedBookingRoles.includes(userRole.value));
const isPhotographerRole = computed(() => userRole.value === 'photographer');

const incomingSummary = computed(() => {
  const total = bookings.value.length;
  const pending = bookings.value.filter(b => b.status === 'pending').length;
  const pendingPayment = bookings.value.filter(b => b.status === 'pending_payment').length;
  const confirmed = bookings.value.filter(b => b.status === 'confirmed').length;
  return {
    total,
    pending,
    pendingPayment,
    confirmed,
  };
});

const showToast = (message, type = 'info') => {
  const id = toastId++;
  toasts.value.push({ id, message, type });
  setTimeout(() => removeToast(id), 5000);
};

const removeToast = (id) => {
  const index = toasts.value.findIndex(t => t.id === id);
  if (index > -1) toasts.value.splice(index, 1);
};

const albumForm = ref({
  name: '',
  description: '',
  is_public: true,
});

const packageForm = ref({
  name: '',
  description: '',
  price: 0,
  duration_hours: 0,
  edited_photos: 0,
  raw_photos: 0,
  delivery_days: 7,
  is_active: true,
  cover_image: '',
  sample_images: [],
});
const packageCoverFile = ref(null);
const packageSampleFiles = ref([]);
const packageSampleUrlInput = ref('');

const eventForm = ref({
  title: '',
  event_type: '',
  description: '',
  event_date: '',
  location: '',
  city_id: '',
  max_attendees: null,
  ticket_price: 0,
  duration_hours: null,
});

const profileForm = ref({
  username: '',
  bio: '',
  city_id: '',
  category_ids: [],
  favorite_hashtags: [],
  experience_years: 0,
  facebook_url: '',
  instagram_url: '',
  twitter_url: '',
  linkedin_url: '',
  youtube_url: '',
  website_url: '',
});

const profileUrl = computed(() => {
  const username = user.value?.username;
  const shareCode = photographer.value?.share_code || user.value?.photographer?.share_code;
  let baseUrl = '';

  if (username) {
    baseUrl = window.location.origin + '/@' + username;
  } else {
    // Fallback to slug if no username (shouldn't happen in production)
    const slug = photographer.value?.slug || user.value?.photographer?.slug;
    baseUrl = slug ? window.location.origin + '/photographer/' + slug : window.location.origin + '/@your-username';
  }

  if (shareCode) {
    return baseUrl + '?ref=' + encodeURIComponent(shareCode);
  }

  return baseUrl;
});

const shareLinks = computed(() => {
  const encodedUrl = encodeURIComponent(profileUrl.value);
  const message = 'Discover my work on Photographer SB - book trusted photographers across Bangladesh.';
  const encodedMessage = encodeURIComponent(message);
  return {
    whatsapp: `https://wa.me/?text=${encodedMessage}%20${encodedUrl}`,
    facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}&quote=${encodedMessage}`,
    messenger: `fb-messenger://share?link=${encodedUrl}&app_id=0&redirect_uri=${encodedUrl}`,
    telegram: `https://t.me/share/url?url=${encodedUrl}&text=${encodedMessage}`,
  };
});

const profileAvatarUrl = computed(() => {
  const raw = photographer.value?.profile_picture_url
    || photographer.value?.profile_picture
    || user.value?.photographer?.profile_picture
    || user.value?.profile_photo_url
    || '';

  if (!raw) return '';
  if (raw.startsWith('http://') || raw.startsWith('https://') || raw.startsWith('data:') || raw.startsWith('/storage/')) {
    return raw;
  }

  return `/storage/${raw.replace(/^\/+/, '')}`;
});

const handleAvatarError = (event) => {
  event.target.src = '/images/default-avatar.png';
};

watch(canViewBookings, (allowed) => {
  if (!allowed && activeTab.value === 'bookings') {
    activeTab.value = 'portfolio';
  }
}, { immediate: true });

const profileCompleteness = computed(() => {
  return Number(photographer.value?.profile_completeness ?? user.value?.photographer?.profile_completeness ?? 0);
});

const profileChecklist = computed(() => {
  const hasBio = Boolean(profileForm.value.bio && profileForm.value.bio.trim().length >= 30);
  const hasPhoto = Boolean(photographer.value?.profile_picture || user.value?.photographer?.profile_picture);
  const hasCity = Boolean(profileForm.value.city_id);
  const hasCategories = Array.isArray(profileForm.value.category_ids) && profileForm.value.category_ids.length > 0;
  const hasPortfolio = albums.value.length > 0;
  const hasPackages = packages.value.length > 0;

  return [
    { key: 'bio', label: 'Add a detailed bio', done: hasBio },
    { key: 'photo', label: 'Upload a profile photo', done: hasPhoto },
    { key: 'city', label: 'Select your city', done: hasCity },
    { key: 'categories', label: 'Pick specialties', done: hasCategories },
    { key: 'portfolio', label: 'Create at least 1 album', done: hasPortfolio },
    { key: 'packages', label: 'Add at least 1 package', done: hasPackages },
  ];
});

const profileCompletionPercent = computed(() => {
  if (profileCompleteness.value > 0) return Math.min(100, Math.max(0, Math.round(profileCompleteness.value)));
  const total = profileChecklist.value.length;
  const done = profileChecklist.value.filter(item => item.done).length;
  return total > 0 ? Math.round((done / total) * 100) : 0;
});

const funnelStats = computed(() => {
  const views = Number(stats.value?.profile_views || 0);
  const inquiries = Number(stats.value?.profile_clicks || 0);
  const bookingsCount = Number(stats.value?.total_bookings || 0);
  const revenue = Number(stats.value?.total_revenue || 0);

  const inquiryRate = views > 0 ? Math.round((inquiries / views) * 100) : 0;
  const bookingRate = inquiries > 0 ? Math.round((bookingsCount / inquiries) * 100) : 0;
  const revenuePerBooking = bookingsCount > 0 ? Math.round(revenue / bookingsCount) : 0;

  const viewBar = 100;
  const inquiryBar = Math.min(100, Math.round(inquiryRate));
  const bookingBar = Math.min(100, Math.round(bookingRate));
  const revenueBar = bookingsCount > 0 ? Math.min(100, Math.round((revenuePerBooking / 1000) * 100)) : 0;

  return {
    views,
    inquiries,
    bookings: bookingsCount,
    revenue,
    inquiryRate,
    bookingRate,
    revenuePerBooking,
    viewBar,
    inquiryBar,
    bookingBar,
    revenueBar,
  };
});

const upcomingBookings = computed(() => {
  const now = new Date();
  const end = new Date();
  end.setDate(now.getDate() + 7);

  return bookings.value
    .filter(booking => {
      if (!booking?.event_date) return false;
      const date = new Date(booking.event_date);
      return date >= now && date <= end;
    })
    .sort((a, b) => new Date(a.event_date) - new Date(b.event_date))
    .slice(0, 4);
});

const upcomingEvents = computed(() => {
  const now = new Date();
  const end = new Date();
  end.setDate(now.getDate() + 7);

  return events.value
    .filter(eventItem => {
      if (!eventItem?.event_date) return false;
      const date = new Date(eventItem.event_date);
      return date >= now && date <= end;
    })
    .sort((a, b) => new Date(a.event_date) - new Date(b.event_date))
    .slice(0, 4);
});

const responseStats = computed(() => {
  const average = Number(photographer.value?.response_time_avg || 0);
  const target = 2;
  const progress = target > 0 ? Math.max(0, Math.min(100, Math.round((target / Math.max(average, 0.5)) * 100))) : 0;

  let statusLabel = 'Needs work';
  let statusClass = 'text-red-600';
  let barClass = 'bg-red-500';

  if (average > 0 && average <= target) {
    statusLabel = 'Great';
    statusClass = 'text-green-600';
    barClass = 'bg-green-500';
  } else if (average > 0 && average <= target * 2) {
    statusLabel = 'Good';
    statusClass = 'text-yellow-600';
    barClass = 'bg-yellow-500';
  }

  return {
    average: average > 0 ? average.toFixed(1) : '0.0',
    target,
    progress,
    statusLabel,
    statusClass,
    barClass,
  };
});

const openQuickAction = (action) => {
  switch (action) {
    case 'album':
      activeTab.value = 'portfolio';
      showAlbumModal.value = true;
      break;
    case 'package':
      activeTab.value = 'packages';
      showPackageModal.value = true;
      break;
    case 'event':
      activeTab.value = 'events';
      showEventForm.value = true;
      break;
    case 'profile':
      window.location.href = '/photographer/settings';
      break;
    default:
      break;
  }
};

const trustStats = computed(() => {
  const rating = Number(stats.value?.average_rating || 0);
  const reviews = Number(photographer.value?.rating_count || stats.value?.rating_count || 0);
  const albumsCount = albums.value.length;
  const photos = albums.value.reduce((total, album) => total + (album.photos_count || 0), 0);

  return {
    verified: Boolean(photographer.value?.is_verified),
    rating: rating > 0 ? rating.toFixed(1) : '0.0',
    reviews,
    albums: albumsCount,
    photos,
    tipsEnabled: Boolean(photographer.value?.accept_tips),
  };
});

const priorityActions = computed(() => {
  const pendingRequests = Number(stats.value?.pending_bookings || 0);
  const completeness = profileCompleteness.value;
  const albumCount = albums.value.length;
  const packageCount = packages.value.length;

  return [
    {
      key: 'pending-requests',
      title: 'Pending Requests',
      value: pendingRequests,
      description: pendingRequests > 0
        ? 'Respond quickly to win bookings.'
        : 'No pending requests right now.',
      cta: pendingRequests > 0 ? 'Review bookings' : 'View bookings',
      onClick: () => {
        activeTab.value = 'bookings';
      },
      badge: pendingRequests > 0 ? 'Urgent' : null,
      cardClass: pendingRequests > 0 ? 'border-yellow-200 bg-yellow-50' : 'border-gray-200 bg-gray-50',
      badgeClass: pendingRequests > 0 ? 'bg-yellow-200 text-yellow-900' : '',
      buttonClass: pendingRequests > 0
        ? 'bg-yellow-600 text-white hover:bg-yellow-700'
        : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100',
    },
    {
      key: 'profile-completeness',
      title: 'Profile Completion',
      value: `${Math.min(100, Math.max(0, completeness))}%`,
      description: completeness < 80
        ? 'Complete your profile to rank higher.'
        : 'Great work - keep it updated.',
      cta: 'Account settings',
      onClick: () => {
        window.location.href = '/photographer/settings';
      },
      badge: completeness < 80 ? 'Boost' : null,
      cardClass: completeness < 80 ? 'border-blue-200 bg-blue-50' : 'border-gray-200 bg-gray-50',
      badgeClass: completeness < 80 ? 'bg-blue-200 text-blue-900' : '',
      buttonClass: 'bg-burgundy text-white hover:bg-burgundy-dark',
    },
    {
      key: 'portfolio',
      title: 'Portfolio Albums',
      value: albumCount,
      description: albumCount === 0
        ? 'Add your best work to build trust.'
        : 'Refresh albums with recent work.',
      cta: albumCount === 0 ? 'Add album' : 'Manage albums',
      onClick: () => {
        activeTab.value = 'portfolio';
        if (albumCount === 0) showAlbumModal.value = true;
      },
      badge: albumCount === 0 ? 'Missing' : null,
      cardClass: albumCount === 0 ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-gray-50',
      badgeClass: albumCount === 0 ? 'bg-red-200 text-red-900' : '',
      buttonClass: albumCount === 0
        ? 'bg-red-600 text-white hover:bg-red-700'
        : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100',
    },
    {
      key: 'packages',
      title: 'Service Packages',
      value: packageCount,
      description: packageCount === 0
        ? 'Packages help clients book faster.'
        : 'Review pricing for competitiveness.',
      cta: packageCount === 0 ? 'Create package' : 'Manage packages',
      onClick: () => {
        activeTab.value = 'packages';
        if (packageCount === 0) showPackageModal.value = true;
      },
      badge: packageCount === 0 ? 'Missing' : null,
      cardClass: packageCount === 0 ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-gray-50',
      badgeClass: packageCount === 0 ? 'bg-red-200 text-red-900' : '',
      buttonClass: packageCount === 0
        ? 'bg-red-600 text-white hover:bg-red-700'
        : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100',
    },
  ];
});

const copyProfileLink = async () => {
  try {
    trackProfileShare('copy');
    await navigator.clipboard.writeText(profileUrl.value);
    copied.value = true;
    showToast('Profile link copied to clipboard.', 'success');
    setTimeout(() => {
      copied.value = false;
    }, 2000);
  } catch (error) {
    console.error('Failed to copy:', error);
    showToast('Copy failed. Please try again.', 'error');
    // Fallback for older browsers
    const input = document.createElement('input');
    input.value = profileUrl.value;
    document.body.appendChild(input);
    input.select();
    document.execCommand('copy');
    document.body.removeChild(input);
    copied.value = true;
    showToast('Profile link copied to clipboard.', 'success');
    setTimeout(() => {
      copied.value = false;
    }, 2000);
  }
};

const openProfile = () => {
  trackProfileShare('open');
  showToast('Opening your public profile...', 'info');
};

const trackProfileShare = async (action) => {
  try {
    await api.post('/photographer/profile-share', { action });
  } catch (error) {
    console.warn('Profile share tracking failed:', error);
  }
};

const shareProfile = async (channel) => {
  const link = shareLinks.value[channel];
  if (!link) return;
  trackProfileShare(channel);
  window.open(link, '_blank', 'noopener');
  const labels = {
    whatsapp: 'WhatsApp',
    facebook: 'Facebook',
    messenger: 'Messenger',
    telegram: 'Telegram',
  };
  showToast(`Opening ${labels[channel] || 'share'}...`, 'info');
};

const fetchDashboardData = async () => {
  try {
    const { data: userData } = await api.get('/auth/me');
    if (userData.status === 'success') {
      user.value = userData.data;
      
      // Load photographer data into profile form
      if (user.value.photographer) {
        profileForm.value.username = user.value.username || '';
        profileForm.value.bio = user.value.photographer.bio || '';
        profileForm.value.city_id = user.value.photographer.city_id || '';
        profileForm.value.category_ids = user.value.photographer.categories?.map(c => c.id) || [];
        profileForm.value.favorite_hashtags = Array.isArray(user.value.photographer.favorite_hashtags) 
          ? user.value.photographer.favorite_hashtags 
          : [];
        profileForm.value.experience_years = user.value.photographer.experience_years || 0;
        profileForm.value.facebook_url = user.value.photographer.facebook_url || '';
        profileForm.value.instagram_url = user.value.photographer.instagram_url || '';
        profileForm.value.twitter_url = user.value.photographer.twitter_url || '';
        profileForm.value.linkedin_url = user.value.photographer.linkedin_url || '';
        profileForm.value.youtube_url = user.value.photographer.youtube_url || '';
        profileForm.value.website_url = user.value.photographer.website_url || '';
      }
    }

    // Fetch dashboard stats from API
    try {
      const { data: dashboardData } = await api.get('/photographer/dashboard');
      if (dashboardData.status === 'success') {
        stats.value = dashboardData.data.stats;
        bookings.value = dashboardData.data.bookings || [];
        photographer.value = dashboardData.data.photographer; // Store photographer data
      }
    } catch (error) {
      console.error('Error fetching dashboard stats:', error);
      // Fallback to basic bookings
      const { data: bookingsData } = await api.get('/bookings');
      if (bookingsData.status === 'success') {
        bookings.value = bookingsData.data;
      }

      // Fallback stats
      stats.value = {
        total_bookings: bookings.value.length,
        pending_bookings: bookings.value.filter(b => b.status === 'pending').length,
        average_rating: 0,
        total_revenue: 0,
        profile_views: 0,
        profile_clicks: 0,
        vote_count: 0,
        share_count: 0,
      };
    }
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
  }
};

const fetchAchievementsSummary = async () => {
  loadingAchievementsSummary.value = true;
  try {
    const { data } = await api.get('/photographer/achievements');
    if (data.status === 'success') {
      achievementsSummary.value = data.data;
    }
  } catch (error) {
    console.error('Error fetching achievements summary:', error);
  } finally {
    loadingAchievementsSummary.value = false;
  }
};

const getBookingStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    pending_payment: 'bg-amber-100 text-amber-800',
    confirmed: 'bg-green-100 text-green-800',
    completed: 'bg-blue-100 text-blue-800',
    cancelled: 'bg-red-100 text-red-800',
    rejected: 'bg-gray-100 text-gray-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const formatStatus = (status) => {
  if (!status) return 'Unknown';
  return status
    .toString()
    .replace(/_/g, ' ')
    .replace(/\b\w/g, (char) => char.toUpperCase());
};

const updateBookingStatus = async (bookingId, newStatus) => {
  try {
    const { data } = await api.patch(`/bookings/${bookingId}/status`, { status: newStatus });
    
    if (data.status === 'success') {
      // Update local booking list
      const booking = bookings.value.find(b => b.id === bookingId);
      if (booking) {
        booking.status = newStatus;
      }
      
      // Refresh stats
      stats.value.pending_bookings = bookings.value.filter(b => b.status === 'pending').length;
    }
  } catch (error) {
    console.error('Error updating booking status:', error);
    showToast('Failed to update booking status', 'error');
  }
};

const formatDate = (date) => {
  return formatDateValue(date);
};

const handleProfileImageUpload = async (file) => {
  const formData = new FormData();
  formData.append('avatar', file);

  try {
    const { data } = await api.post('/photographer/profile/avatar', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    if (data.status === 'success') {
      showToast('Profile picture updated successfully!', 'success');
      fetchDashboardData();
    }
  } catch (error) {
    console.error('Error uploading image:', error);
    showToast('Failed to upload image', 'error');
  }
};

const saveProfile = async () => {
  try {
    const { data } = await api.patch('/photographer/profile', profileForm.value);
    
    if (data.status === 'success') {
      showToast('Profile updated successfully!', 'success');
      // Update local photographer data with fresh data from server
      photographer.value = data.data;
      await fetchDashboardData();
    } else {
      showToast(data.message || 'Failed to save profile', 'error');
    }
  } catch (error) {
    console.error('Error saving profile:', error);
    
    // Handle validation errors (422)
    if (error.response?.status === 422 && error.response?.data?.errors) {
      const errors = error.response.data.errors;
      const firstError = Object.values(errors)[0];
      const errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
      showToast(errorMessage, 'error');
    } else {
      const errorMessage = error.response?.data?.message || error.response?.data?.error || 'Failed to save profile. Please try again.';
      showToast(errorMessage, 'error');
    }
  }
};

// Event Management Functions
const fetchCities = async () => {
  try {
    const { data } = await api.get('/locations');
    const locations = data.data || data || [];
    cities.value = locations.filter(location => location.type !== 'division');
  } catch (error) {
    console.error('Error fetching locations:', error);
  }
};

const fetchCategories = async () => {
  try {
    const { data } = await api.get('/categories');
    categories.value = data.data || data;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

const fetchHashtags = async () => {
  try {
    const { data } = await api.get('/hashtags');
    hashtags.value = data.data || data;
  } catch (error) {
    console.error('Error fetching hashtags:', error);
  }
};

const fetchEvents = async () => {
  try {
    const { data } = await api.get('/photographer/events');
    events.value = data.data || data;
  } catch (error) {
    console.error('Error fetching events:', error);
  }
};

// Fetch competition submissions
const fetchSubmissions = async () => {
  loadingSubmissions.value = true;
  try {
    const { data } = await api.get('/photographer/submissions', {
      params: { per_page: 1000 }
    });
    if (data.status === 'success') {
      submissions.value = data.data?.data || data.data || [];
    }
  } catch (error) {
    console.error('Error fetching submissions:', error);
    showToast('Failed to load competition submissions', 'error');
  } finally {
    loadingSubmissions.value = false;
  }
};

// Fetch event RSVPs
const fetchEventRsvps = async () => {
  loadingEventRsvps.value = true;
  try {
    const { data } = await api.get('/photographer/event-rsvps');
    if (data.status === 'success') {
      eventRsvps.value = data.data || [];
    }
  } catch (error) {
    console.error('Error fetching event RSVPs:', error);
    showToast('Failed to load event registrations', 'error');
  } finally {
    loadingEventRsvps.value = false;
  }
};

const createEvent = async () => {
  creatingEvent.value = true;
  try {
    // Get photographer ID from user
    const photographerId = user.value.photographer?.id;
    if (!photographerId) {
      showToast('Photographer profile not found. Please complete your profile first.', 'error');
      return;
    }

    const payload = {
      ...eventForm.value,
      organizer_id: photographerId,
    };

    const { data } = await api.post('/photographer/events', payload);
    
    if (data.status === 'success') {
      showToast('Event created successfully! It will be reviewed by admin before going live.', 'success');
      showEventForm.value = false;
      
      // Reset form
      eventForm.value = {
        title: '',
        event_type: '',
        description: '',
        event_date: '',
        location: '',
        city_id: '',
        max_attendees: null,
        ticket_price: 0,
        duration_hours: null,
      };
      
      fetchEvents();
    }
  } catch (error) {
    console.error('Error creating event:', error);
    showToast(error.response?.data?.message || 'Failed to create event', 'error');
  } finally {
    creatingEvent.value = false;
  }
};

const viewEvent = (event) => {
  window.open(`/events/${event.slug}`, '_blank');
};

const editEvent = (event) => {
  // Navigate to event edit page or open modal
  editingEvent.value = { ...event };
  creatingEvent.value = true; // Reuse create modal for editing
};

const cancelEvent = async (event) => {
  if (!confirm('Are you sure you want to cancel this event? Attendees will be notified.')) {
    return;
  }

  try {
    const { data } = await api.post(`/photographer/events/${event.id}/cancel`);
    
    if (data.status === 'success') {
      showToast('Event cancelled successfully', 'success');
      fetchEvents();
    }
  } catch (error) {
    console.error('Error cancelling event:', error);
    showToast(error.response?.data?.message || 'Failed to cancel event', 'error');
  }
};

const deleteEvent = async (event) => {
  if (!confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
    return;
  }

  try {
    const { data } = await api.delete(`/photographer/events/${event.id}`);
    
    if (data.status === 'success') {
      showToast('Event deleted successfully', 'success');
      fetchEvents();
    }
  } catch (error) {
    console.error('Error deleting event:', error);
    showToast(error.response?.data?.message || 'Failed to delete event', 'error');
  }
};

// Album Management Functions
const fetchAlbums = async () => {
  try {
    const { data } = await api.get('/photographer/albums');
    albums.value = data.data || [];
  } catch (error) {
    console.error('Error fetching albums:', error);
  }
};

const createAlbum = async () => {
  if (!albumForm.value.name) {
    showToast('Please enter an album title', 'warning');
    return;
  }

  creatingAlbum.value = true;
  try {
    const { data } = await api.post('/photographer/albums', albumForm.value);
    
    if (data.status === 'success') {
      showToast('Album created successfully!', 'success');
      showAlbumModal.value = false;
      
      // Reset form
      albumForm.value = {
        name: '',
        description: '',
        is_public: true,
      };
      
      fetchAlbums();
    }
  } catch (error) {
    console.error('Error creating album:', error);
    showToast(error.response?.data?.message || 'Failed to create album', 'error');
  } finally {
    creatingAlbum.value = false;
  }
};

const editAlbum = (album) => {
  editingAlbumId.value = album.id;
  albumForm.value = {
    name: album.name,
    description: album.description,
    is_public: album.is_public,
  };
  showAlbumModal.value = true;
};

const updateAlbum = async () => {
  if (!albumForm.value.name) {
    showToast('Please enter an album name', 'warning');
    return;
  }

  creatingAlbum.value = true;
  try {
    const { data } = await api.put(`/photographer/albums/${editingAlbumId.value}`, albumForm.value);
    
    if (data.status === 'success') {
      showToast('Album updated successfully!', 'success');
      closeAlbumModal();
      fetchAlbums();
    }
  } catch (error) {
    console.error('Error updating album:', error);
    showToast(error.response?.data?.message || 'Failed to update album', 'error');
  } finally {
    creatingAlbum.value = false;
  }
};

const deleteAlbum = async (album) => {
  if (!confirm(`Are you sure you want to delete "${album.name}"? This will also delete all photos in this album. This action cannot be undone.`)) {
    return;
  }

  try {
    const { data } = await api.delete(`/photographer/albums/${album.id}`);
    
    if (data.status === 'success') {
      showToast('Album deleted successfully', 'success');
      fetchAlbums();
    }
  } catch (error) {
    console.error('Error deleting album:', error);
    showToast(error.response?.data?.message || 'Failed to delete album', 'error');
  }
};

const closeAlbumModal = () => {
  showAlbumModal.value = false;
  editingAlbumId.value = null;
  albumForm.value = {
    name: '',
    description: '',
    is_public: true,
  };
};

const viewAlbum = (album) => {
  selectedAlbum.value = album;
};

const closePhotoManager = () => {
  selectedAlbum.value = null;
  fetchAlbums(); // Refresh album list to update photo counts
};

// Package Management Functions
const fetchPackages = async () => {
  try {
    const { data } = await api.get('/photographer/packages');
    packages.value = data.data || [];
  } catch (error) {
    console.error('Error fetching packages:', error);
  }
};

const savePackage = async () => {
  if (!packageForm.value.name || !packageForm.value.price) {
    showToast('Please fill in required fields (name and price)', 'warning');
    return;
  }

  creatingPackage.value = true;
  try {
    const endpoint = editingPackage.value 
      ? `/photographer/packages/${editingPackage.value.id}`
      : '/photographer/packages';
    
    const method = editingPackage.value ? 'put' : 'post';
    
    const { data } = await api[method](endpoint, packageForm.value);
    
    if (data.status === 'success') {
      const savedPackage = data.data || data.package || data;

      // Upload images if selected
      if ((packageCoverFile.value || packageSampleFiles.value.length) && savedPackage?.id) {
        const formData = new FormData();
        if (packageCoverFile.value) {
          formData.append('cover_image', packageCoverFile.value);
        }
        if (packageSampleFiles.value.length) {
          packageSampleFiles.value.forEach((file) => {
            formData.append('sample_images[]', file);
          });
        }

        await api.post(`/photographer/packages/${savedPackage.id}/images`, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
      }

      showToast(editingPackage.value ? 'Package updated successfully!' : 'Package created successfully!', 'success');
      closePackageModal();
      fetchPackages();
    }
  } catch (error) {
    console.error('Error saving package:', error);
    showToast(error.response?.data?.message || 'Failed to save package', 'error');
  } finally {
    creatingPackage.value = false;
  }
};

const editPackage = (pkg) => {
  editingPackage.value = pkg;
  packageForm.value = {
    name: pkg.name,
    description: pkg.description,
    price: pkg.price,
    duration_hours: pkg.duration_hours,
    edited_photos: pkg.edited_photos,
    raw_photos: pkg.raw_photos || 0,
    delivery_days: pkg.delivery_days,
    is_active: pkg.is_active,
    cover_image: pkg.cover_image || '',
    sample_images: pkg.sample_images || [],
  };
  packageCoverFile.value = null;
  packageSampleFiles.value = [];
  packageSampleUrlInput.value = '';
  showPackageModal.value = true;
};

const deletePackage = async (pkg) => {
  if (!confirm(`Are you sure you want to delete "${pkg.name}"?`)) {
    return;
  }

  try {
    const { data } = await api.delete(`/photographer/packages/${pkg.id}`);
    
    if (data.status === 'success') {
      showToast('Package deleted successfully', 'success');
      fetchPackages();
    }
  } catch (error) {
    console.error('Error deleting package:', error);
    showToast(error.response?.data?.message || 'Failed to delete package', 'error');
  }
};

const closePackageModal = () => {
  showPackageModal.value = false;
  editingPackage.value = null;
  packageForm.value = {
    name: '',
    description: '',
    price: 0,
    duration_hours: 0,
    edited_photos: 0,
    raw_photos: 0,
    delivery_days: 7,
    is_active: true,
    cover_image: '',
    sample_images: [],
  };
  packageCoverFile.value = null;
  packageSampleFiles.value = [];
  packageSampleUrlInput.value = '';
};

const onPackageCoverSelected = async (event) => {
  const file = event.target.files?.[0];
  if (!file) {
    packageCoverFile.value = null;
    return;
  }

  const validation = await validateUploadFile(file, {
    label: 'Cover image',
    maxBytes: 10 * 1024 * 1024,
    allowedTypes: ['image/jpeg', 'image/png', 'image/webp'],
    imageWidth: 1200,
    imageHeight: 800
  });

  if (!validation.ok) {
    showToast(validation.message, 'error');
    packageCoverFile.value = null;
    event.target.value = '';
    return;
  }

  packageCoverFile.value = file;
};

const onPackageSamplesSelected = async (event) => {
  const files = Array.from(event.target.files || []).slice(0, 10);
  const valid = [];

  for (const file of files) {
    const validation = await validateUploadFile(file, {
      label: 'Sample image',
      maxBytes: 10 * 1024 * 1024,
      allowedTypes: ['image/jpeg', 'image/png', 'image/webp'],
      imageWidth: 1600,
      imageHeight: 1200
    });

    if (!validation.ok) {
      showToast(validation.message, 'error');
      continue;
    }

    valid.push(file);
  }

  packageSampleFiles.value = valid;
};

const addSampleUrl = () => {
  const url = packageSampleUrlInput.value.trim();
  if (!url) return;
  if (!packageForm.value.sample_images) {
    packageForm.value.sample_images = [];
  }
  if (!packageForm.value.sample_images.includes(url)) {
    packageForm.value.sample_images.push(url);
  }
  packageSampleUrlInput.value = '';
};

const removeSampleUrl = (index) => {
  packageForm.value.sample_images.splice(index, 1);
};

// ==================== AWARDS FUNCTIONALITY ====================
const awards = ref([]);
const loadingAwards = ref(false);
const showAddAwardModal = ref(false);
const editingAward = ref(null);
const savingAward = ref(false);
const awardErrors = ref({});
const awardErrorMessage = ref('');
const currentYear = new Date().getFullYear();

const awardForm = ref({
  title: '',
  organization: '',
  year: currentYear,
  type: 'award',
  description: '',
});

const certificateFile = ref(null);

// Fetch awards
const fetchAwards = async () => {
  try {
    loadingAwards.value = true;
    const response = await api.get('/photographer/awards');
    awards.value = response.data.data;
  } catch (error) {
    console.error('Failed to fetch awards:', error);
  } finally {
    loadingAwards.value = false;
  }
};

// Award type styling
const getAwardIcon = (type) => {
  const icons = {
    award: '🏆',
    achievement: '⭐',
    recognition: '🎖️',
    certification: '📜'
  };
  return icons[type] || '🏆';
};

const getAwardColor = (type) => {
  const colors = {
    award: 'bg-yellow-100',
    achievement: 'bg-blue-100',
    recognition: 'bg-purple-100',
    certification: 'bg-green-100'
  };
  return colors[type] || 'bg-gray-100';
};

const getTypeBadgeColor = (type) => {
  const colors = {
    award: 'bg-yellow-100 text-yellow-800',
    achievement: 'bg-blue-100 text-blue-800',
    recognition: 'bg-purple-100 text-purple-800',
    certification: 'bg-green-100 text-green-800'
  };
  return colors[type] || 'bg-gray-100 text-gray-800';
};

// Handle certificate file upload
const handleCertificateUpload = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  const validation = await validateUploadFile(file, {
    label: 'Certificate',
    maxBytes: 5 * 1024 * 1024,
    allowedTypes: ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'],
    imageWidth: 2000,
    imageHeight: 1400
  });

  if (!validation.ok) {
    awardErrors.value.certificate_file = [validation.message];
    event.target.value = '';
    certificateFile.value = null;
    return;
  }

  certificateFile.value = file;
  delete awardErrors.value.certificate_file;
};

// Edit award
const editAward = (award) => {
  editingAward.value = award;
  awardForm.value = {
    title: award.title,
    organization: award.organization || '',
    year: award.year,
    type: award.type,
    description: award.description || '',
  };
  certificateFile.value = null;
  showAddAwardModal.value = true;
};

// Save award (create or update)
const saveAward = async () => {
  try {
    savingAward.value = true;
    awardErrors.value = {};
    awardErrorMessage.value = '';

    const formData = new FormData();
    Object.keys(awardForm.value).forEach(key => {
      if (awardForm.value[key]) {
        formData.append(key, awardForm.value[key]);
      }
    });

    if (certificateFile.value) {
      formData.append('certificate_file', certificateFile.value);
    }

    if (editingAward.value) {
      // Update existing award
      formData.append('_method', 'PUT');
      await api.post(`/photographer/awards/${editingAward.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    } else {
      // Create new award
      await api.post('/photographer/awards', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    }

    // Success
    closeAwardModal();
    fetchAwards();
    alert(editingAward.value ? 'Award updated successfully!' : 'Award added successfully!');
  } catch (error) {
    console.error('Failed to save award:', error);
    console.error('Error response:', error.response);
    console.error('Error data:', error.response?.data);
    
    if (error.response?.status === 422) {
      awardErrors.value = error.response.data.errors || {};
      awardErrorMessage.value = error.response.data.message;
    } else if (error.response?.data?.message) {
      awardErrorMessage.value = error.response.data.message;
    } else {
      awardErrorMessage.value = 'Failed to save award. Please try again.';
    }
  } finally {
    savingAward.value = false;
  }
};

// Delete award
const deleteAward = async (awardId) => {
  if (!confirm('Are you sure you want to delete this award?')) {
    return;
  }

  try {
    await api.delete(`/photographer/awards/${awardId}`);
    awards.value = awards.value.filter(a => a.id !== awardId);
    alert('Award deleted successfully!');
  } catch (error) {
    console.error('Failed to delete award:', error);
    alert('Failed to delete award. Please try again.');
  }
};

// Close modal
const closeAwardModal = () => {
  showAddAwardModal.value = false;
  editingAward.value = null;
  awardForm.value = {
    title: '',
    organization: '',
    year: currentYear,
    type: 'award',
    description: '',
  };
  certificateFile.value = null;
  awardErrors.value = {};
  awardErrorMessage.value = '';
};

// Watch for activeTab changes to lazy load data (skip initial call)
watch(activeTab, (newTab) => {
  // Profile tab dependencies
  if (newTab === 'profile') {
    if (cities.value.length === 0) fetchCities();
    if (categories.value.length === 0) fetchCategories();
    if (hashtags.value.length === 0) fetchHashtags();
  }
  // Portfolio tab
  if (newTab === 'portfolio' && albums.value.length === 0) fetchAlbums();
  // Packages tab
  if (newTab === 'packages' && packages.value.length === 0) fetchPackages();
  // Competitions tab
  if (newTab === 'competitions' && submissions.value.length === 0) fetchSubmissions();
  // Events tab
  if (newTab === 'events') {
    if (events.value.length === 0) fetchEvents();
    if (eventRsvps.value.length === 0) fetchEventRsvps();
  }
  // Achievements tab
  if (newTab === 'achievements' && achievementsSummary.value.total_achievements === 0) {
    fetchAchievementsSummary();
  }
  // Awards tab
  if (newTab === 'awards' && awards.value.length === 0) fetchAwards();
}, { immediate: false });

onMounted(() => {
  // Fetch only essential data on mount
  fetchDashboardData();
  
  // Lazy load other data when needed
  // Cities, categories, hashtags will load when profile tab is opened
  // Albums, packages, etc. will load when their tabs are opened
});
</script>
