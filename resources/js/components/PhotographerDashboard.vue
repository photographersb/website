<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b">
      <div class="container mx-auto px-3 sm:px-4 py-4 sm:py-6">
        <div class="flex items-center gap-3 sm:gap-4">
          <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full overflow-hidden bg-burgundy flex items-center justify-center text-white font-bold text-lg sm:text-2xl flex-shrink-0">
            <img 
              v-if="photographer?.profile_picture" 
              :src="`/storage/${photographer.profile_picture}`" 
              :alt="user?.name"
              class="w-full h-full object-cover"
            />
            <span v-else>{{ user?.name?.charAt(0).toUpperCase() }}</span>
          </div>
          <div class="min-w-0 flex-1">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold truncate">Photographer Dashboard</h1>
            <p class="text-sm sm:text-base text-gray-600 truncate">Welcome, {{ user?.name }}</p>
          </div>
          <!-- Notification Bell -->
          <div class="ml-4">
            <NotificationBell />
          </div>
        </div>
      </div>
    </div>

    <div class="container mx-auto px-3 sm:px-4 py-4 sm:py-6 md:py-8">
      <!-- Stats Overview -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 md:gap-6 mb-4 sm:mb-6 md:mb-8">
        <div class="bg-white rounded-lg shadow p-3 sm:p-4 md:p-6">
          <p class="text-xs sm:text-sm text-gray-600 mb-1 sm:mb-2">Total Bookings</p>
          <p class="text-xl sm:text-2xl md:text-3xl font-bold text-burgundy">{{ stats.total_bookings || 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-3 sm:p-4 md:p-6">
          <p class="text-xs sm:text-sm text-gray-600 mb-1 sm:mb-2">Pending Requests</p>
          <p class="text-xl sm:text-2xl md:text-3xl font-bold text-yellow-600">{{ stats.pending_bookings || 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-3 sm:p-4 md:p-6">
          <p class="text-xs sm:text-sm text-gray-600 mb-1 sm:mb-2">Average Rating</p>
          <p class="text-xl sm:text-2xl md:text-3xl font-bold text-green-600">{{ stats.average_rating || 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-3 sm:p-4 md:p-6">
          <p class="text-xs sm:text-sm text-gray-600 mb-1 sm:mb-2">Total Revenue</p>
          <p class="text-xl sm:text-2xl md:text-3xl font-bold text-burgundy">৳{{ stats.total_revenue || 0 }}</p>
        </div>
      </div>

      <!-- Share Profile -->
      <div class="bg-gradient-to-r from-burgundy to-[#8B1538] rounded-lg shadow-lg p-4 sm:p-6 mb-4 sm:mb-6 md:mb-8 text-white">
        <h3 class="text-base sm:text-lg font-bold mb-1 sm:mb-2">Share Your Profile</h3>
        <p class="text-xs sm:text-sm text-white/90 mb-3">Share your professional profile with clients</p>
        <div class="flex flex-col sm:flex-row gap-2">
          <input
            type="text"
            :value="profileUrl"
            readonly
            class="flex-1 px-3 sm:px-4 py-2 text-sm sm:text-base rounded-lg bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/30"
          />
          <div class="flex gap-2">
            <button
              @click="copyProfileLink"
              class="flex-1 sm:flex-none px-4 sm:px-6 py-2 bg-white text-burgundy rounded-lg font-medium hover:bg-gray-100 transition-colors flex items-center justify-center gap-2 text-sm sm:text-base min-h-[44px]"
            >
              <svg v-if="!copied" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
              <svg v-else class="w-4 h-4 sm:w-5 sm:h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
              <span class="hidden sm:inline">{{ copied ? 'Copied!' : 'Copy' }}</span>
            </button>
            <a
              :href="profileUrl"
              target="_blank"
              class="flex-1 sm:flex-none px-4 sm:px-6 py-2 bg-white/10 backdrop-blur-sm border border-white/20 text-white rounded-lg font-medium hover:bg-white/20 transition-colors flex items-center justify-center gap-2 text-sm sm:text-base min-h-[44px]"
            >
              <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
              </svg>
              View
            </a>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="bg-white rounded-lg shadow">
        <div class="border-b">
          <div class="overflow-x-auto scrollbar-hide">
            <div class="flex gap-3 sm:gap-6 px-3 sm:px-6 py-3 sm:py-4 min-w-max">
              <button
                @click="activeTab = 'bookings'"
                :class="`pb-2 font-medium whitespace-nowrap text-sm sm:text-base min-h-[44px] px-2 ${activeTab === 'bookings' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600'}`"
              >
                Bookings
              </button>
              <button
                @click="activeTab = 'profile'"
                :class="`pb-2 font-medium whitespace-nowrap text-sm sm:text-base min-h-[44px] px-2 ${activeTab === 'profile' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600'}`"
              >
                Profile
              </button>
              <button
                @click="activeTab = 'portfolio'"
                :class="`pb-2 font-medium whitespace-nowrap text-sm sm:text-base min-h-[44px] px-2 ${activeTab === 'portfolio' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600'}`"
              >
                Portfolio
              </button>
              <button
                @click="activeTab = 'packages'"
                :class="`pb-2 font-medium whitespace-nowrap text-sm sm:text-base min-h-[44px] px-2 ${activeTab === 'packages' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600'}`"
              >
                Packages
              </button>
              <button
                @click="activeTab = 'reviews'"
                :class="`pb-2 font-medium whitespace-nowrap text-sm sm:text-base min-h-[44px] px-2 ${activeTab === 'reviews' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600'}`"
              >
                Reviews
              </button>
              <button
                @click="activeTab = 'competitions'"
                :class="`pb-2 font-medium whitespace-nowrap text-sm sm:text-base min-h-[44px] px-2 ${activeTab === 'competitions' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600'}`"
              >
                Competitions
              </button>
              <button
                @click="activeTab = 'events'"
                :class="`pb-2 font-medium whitespace-nowrap text-sm sm:text-base min-h-[44px] px-2 ${activeTab === 'events' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600'}`"
              >
                Events
              </button>
              <button
                @click="activeTab = 'achievements'"
                :class="`pb-2 font-medium whitespace-nowrap text-sm sm:text-base min-h-[44px] px-2 ${activeTab === 'achievements' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600'}`"
              >
                🌟 Achievements
              </button>
              <button
                @click="activeTab = 'awards'"
                :class="`pb-2 font-medium whitespace-nowrap text-sm sm:text-base min-h-[44px] px-2 ${activeTab === 'awards' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600'}`"
              >
                🏆 Awards
              </button>
            </div>
          </div>
        </div>

        <div class="p-3 sm:p-4 md:p-6">
          <!-- Bookings Tab -->
          <div v-if="activeTab === 'bookings'">
            <h2 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4">Recent Bookings</h2>
            <EmptyState 
              v-if="bookings.length === 0"
              icon="calendar"
              title="No bookings yet"
              description="Your booking requests will appear here"
            />
            <div v-else class="space-y-3 sm:space-y-4">
              <div
                v-for="booking in bookings"
                :key="booking.id"
                class="border rounded-lg p-3 sm:p-4 hover:bg-gray-50 active:bg-gray-100"
              >
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 mb-2 sm:mb-3">
                  <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-sm sm:text-base truncate">{{ booking.client?.name || 'Unknown Client' }}</h3>
                    <p class="text-xs sm:text-sm text-gray-600 truncate">{{ booking.event_location }}</p>
                  </div>
                  <span
                    :class="`px-2 sm:px-3 py-1 rounded-full text-xs whitespace-nowrap ${getBookingStatusClass(booking.status)}`"
                  >
                    {{ booking.status }}
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
                <div v-if="booking.status === 'pending'" class="flex flex-col sm:flex-row gap-2">
                  <button 
                    @click="updateBookingStatus(booking.id, 'confirmed')"
                    class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium min-h-[44px] sm:min-h-0"
                  >
                    Accept
                  </button>
                  <button 
                    @click="updateBookingStatus(booking.id, 'rejected')"
                    class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-medium min-h-[44px] sm:min-h-0"
                  >
                    Decline
                  </button>
                </div>
                <div v-else-if="booking.status === 'confirmed'" class="flex flex-col sm:flex-row gap-2">
                  <button 
                    @click="updateBookingStatus(booking.id, 'completed')"
                    class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium min-h-[44px] sm:min-h-0"
                  >
                    Mark Completed
                  </button>
                  <button 
                    @click="updateBookingStatus(booking.id, 'cancelled')"
                    class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm font-medium min-h-[44px] sm:min-h-0"
                  >
                    Cancel
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Profile Tab -->
          <div v-if="activeTab === 'profile'">
            <h2 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4">Profile Settings</h2>
            <div class="space-y-4 sm:space-y-6">
              <div>
                <label class="block text-sm font-medium mb-2">Profile Picture</label>
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 mb-3">
                  <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center flex-shrink-0">
                    <img 
                      v-if="photographer?.profile_picture" 
                      :src="`/storage/${photographer.profile_picture}`" 
                      :alt="user?.name"
                      class="w-full h-full object-cover"
                    />
                    <svg v-else class="w-12 h-12 sm:w-14 sm:h-14 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                  </div>
                  <div class="flex-1 w-full">
                    <ImageUpload
                      label=""
                      placeholder="Upload your profile picture"
                      @upload="handleProfileImageUpload"
                    />
                    <p class="text-xs text-gray-500 mt-1">Recommended: Square image, at least 400x400px</p>
                  </div>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium mb-2">Bio</label>
                <textarea
                  v-model="profileForm.bio"
                  rows="4"
                  class="w-full border rounded px-3 sm:px-4 py-2 text-sm sm:text-base focus:ring-2 focus:ring-burgundy"
                  placeholder="Tell clients about yourself..."
                ></textarea>
              </div>
              <div>
                <label class="block text-sm font-medium mb-2">City/Location</label>
                <select
                  v-model="profileForm.city_id"
                  class="w-full border rounded px-3 sm:px-4 py-2 text-sm sm:text-base focus:ring-2 focus:ring-burgundy"
                >
                  <option value="">Select your city</option>
                  <option v-for="city in cities" :key="city.id" :value="city.id">
                    {{ city.name }}{{ city.state ? ', ' + city.state : '' }}
                  </option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Select your primary work location</p>
              </div>
              <div>
                <label class="block text-sm font-medium mb-2">Categories</label>
                <div class="border rounded px-3 sm:px-4 py-2 focus-within:ring-2 focus-within:ring-burgundy max-h-48 overflow-y-auto">
                  <div v-if="categories.length === 0" class="text-gray-400 text-sm">Loading categories...</div>
                  <label v-for="category in categories" :key="category.id" class="flex items-center gap-2 py-1.5 cursor-pointer hover:bg-gray-50 px-2 rounded">
                    <input
                      type="checkbox"
                      :value="category.id"
                      v-model="profileForm.category_ids"
                      class="rounded text-burgundy focus:ring-burgundy"
                    />
                    <span class="text-sm">{{ category.name }}</span>
                  </label>
                </div>
                <p class="text-xs text-gray-500 mt-1">Select photography types you specialize in</p>
              </div>
              <div>
                <label class="block text-sm font-medium mb-2">Favorite Hashtags</label>
                <div class="border rounded px-3 sm:px-4 py-2 focus-within:ring-2 focus-within:ring-burgundy max-h-48 overflow-y-auto">
                  <div v-if="hashtags.length === 0" class="text-gray-400 text-sm">Loading hashtags...</div>
                  <label v-for="hashtag in hashtags" :key="hashtag.id" class="flex items-center gap-2 py-1.5 cursor-pointer hover:bg-gray-50 px-2 rounded">
                    <input
                      type="checkbox"
                      :value="hashtag.name"
                      v-model="profileForm.favorite_hashtags"
                      class="rounded text-burgundy focus:ring-burgundy"
                    />
                    <span class="text-sm">#{{ hashtag.name }}</span>
                    <span v-if="hashtag.usage_count" class="text-xs text-gray-400 ml-auto">{{ hashtag.usage_count }}</span>
                  </label>
                </div>
                <p class="text-xs text-gray-500 mt-1">Select relevant hashtags for your photography style</p>
              </div>
              <div>
                <label class="block text-sm font-medium mb-2">Experience (Years)</label>
                <input
                  v-model.number="profileForm.experience_years"
                  type="number"
                  class="w-full border rounded px-3 sm:px-4 py-2 text-sm sm:text-base focus:ring-2 focus:ring-burgundy"
                />
              </div>

              <!-- Social Media Links Section -->
              <div class="border-t pt-4 mt-6">
                <h3 class="text-base sm:text-lg font-bold mb-3 sm:mb-4">Social Media Links</h3>
                <p class="text-xs sm:text-sm text-gray-600 mb-4">Add your social media profiles to help clients connect with you</p>
                <div class="space-y-3 sm:space-y-4">
                  <div>
                    <label class="block text-sm font-medium mb-2 flex items-center gap-2">
                      <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                      </svg>
                      Facebook
                    </label>
                    <input
                      v-model="profileForm.facebook_url"
                      type="url"
                      class="w-full border rounded px-3 sm:px-4 py-2 text-sm sm:text-base focus:ring-2 focus:ring-burgundy"
                      placeholder="https://facebook.com/yourpage"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium mb-2 flex items-center gap-2">
                      <svg class="w-4 h-4 text-pink-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                      </svg>
                      Instagram
                    </label>
                    <input
                      v-model="profileForm.instagram_url"
                      type="url"
                      class="w-full border rounded px-3 sm:px-4 py-2 text-sm sm:text-base focus:ring-2 focus:ring-burgundy"
                      placeholder="https://instagram.com/yourusername"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium mb-2 flex items-center gap-2">
                      <svg class="w-4 h-4 text-sky-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                      </svg>
                      Twitter / X
                    </label>
                    <input
                      v-model="profileForm.twitter_url"
                      type="url"
                      class="w-full border rounded px-3 sm:px-4 py-2 text-sm sm:text-base focus:ring-2 focus:ring-burgundy"
                      placeholder="https://twitter.com/yourusername"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium mb-2 flex items-center gap-2">
                      <svg class="w-4 h-4 text-blue-700" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                      </svg>
                      LinkedIn
                    </label>
                    <input
                      v-model="profileForm.linkedin_url"
                      type="url"
                      class="w-full border rounded px-3 sm:px-4 py-2 text-sm sm:text-base focus:ring-2 focus:ring-burgundy"
                      placeholder="https://linkedin.com/in/yourprofile"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium mb-2 flex items-center gap-2">
                      <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                      </svg>
                      YouTube
                    </label>
                    <input
                      v-model="profileForm.youtube_url"
                      type="url"
                      class="w-full border rounded px-3 sm:px-4 py-2 text-sm sm:text-base focus:ring-2 focus:ring-burgundy"
                      placeholder="https://youtube.com/@yourchannel"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium mb-2 flex items-center gap-2">
                      <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                      </svg>
                      Website
                    </label>
                    <input
                      v-model="profileForm.website_url"
                      type="url"
                      class="w-full border rounded px-3 sm:px-4 py-2 text-sm sm:text-base focus:ring-2 focus:ring-burgundy"
                      placeholder="https://yourwebsite.com"
                    />
                  </div>
                </div>
              </div>

              <button 
                @click="saveProfile"
                class="w-full sm:w-auto px-6 py-3 sm:py-2 bg-burgundy text-white rounded hover:bg-[#6F112D] font-medium min-h-[44px] sm:min-h-0"
              >
                Save Changes
              </button>
            </div>
          </div>

          <!-- Portfolio Tab -->
          <div v-if="activeTab === 'portfolio'">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
              <h2 class="text-lg sm:text-xl font-bold">Portfolio Albums</h2>
              <button @click="showAlbumModal = true" class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-burgundy text-white rounded hover:bg-[#6F112D] font-medium min-h-[44px] sm:min-h-0">
                + Add Album
              </button>
            </div>
            
            <EmptyState
              v-if="albums.length === 0"
              icon="camera"
              title="No albums yet"
              description="Create your first album to showcase your work!"
            />

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
              <div
                v-for="album in albums"
                :key="album.id"
                class="bg-white border rounded-lg overflow-hidden hover:shadow-lg transition-shadow"
              >
                <div class="h-48 bg-gray-200 relative">
                  <img
                    v-if="album.cover_photo"
                    :src="album.cover_photo"
                    :alt="album.title"
                    class="w-full h-full object-cover"
                  />
                  <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                    <button
                      @click="viewAlbum(album)"
                      class="px-4 py-2 bg-white text-burgundy rounded-lg font-medium"
                    >
                      View Album
                    </button>
                  </div>
                </div>
                <div class="p-4">
                  <h3 class="font-bold text-lg mb-1">{{ album.title }}</h3>
                  <p class="text-sm text-gray-600 mb-2">{{ album.description }}</p>
                  <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">{{ album.photo_count || 0 }} photos</span>
                    <span
                      :class="`px-2 py-1 rounded text-xs ${album.is_public ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`"
                    >
                      {{ album.is_public ? 'Public' : 'Private' }}
                    </span>
                  </div>
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
              <h3 class="text-xl font-bold mb-4">Create New Album</h3>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium mb-2">Album Name *</label>
                  <input
                    v-model="albumForm.name"
                    type="text"
                    class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                    placeholder="Wedding Photography"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium mb-2">Description</label>
                  <textarea
                    v-model="albumForm.description"
                    rows="3"
                    class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                    placeholder="Beautiful wedding moments captured..."
                  ></textarea>
                </div>
                <div class="flex items-center gap-2">
                  <input
                    v-model="albumForm.is_public"
                    type="checkbox"
                    id="is_public"
                    class="w-4 h-4 text-burgundy focus:ring-burgundy"
                  />
                  <label for="is_public" class="text-sm font-medium">Make this album public</label>
                </div>
              </div>
              <div class="flex gap-3 mt-6">
                <button
                  @click="createAlbum"
                  :disabled="!albumForm.name || creatingAlbum"
                  class="flex-1 px-4 py-2 bg-burgundy text-white rounded hover:bg-[#6F112D] disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {{ creatingAlbum ? 'Creating...' : 'Create Album' }}
                </button>
                <button
                  @click="showAlbumModal = false"
                  class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50"
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
              <h2 class="text-lg sm:text-xl font-bold">Service Packages</h2>
              <button @click="showPackageModal = true" class="w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-burgundy text-white rounded hover:bg-[#6F112D] font-medium min-h-[44px] sm:min-h-0">
                + Add Package
              </button>
            </div>
            
            <EmptyState
              v-if="packages.length === 0"
              icon="package"
              title="No packages yet"
              description="Create service packages to attract clients!"
            />

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
              <div
                v-for="pkg in packages"
                :key="pkg.id"
                class="bg-white border rounded-lg overflow-hidden hover:shadow-lg transition-shadow"
              >
                <!-- Package Cover Image -->
                <div v-if="pkg.cover_image" class="h-48 bg-gray-100">
                  <img
                    :src="pkg.cover_image"
                    :alt="pkg.name"
                    class="w-full h-full object-cover"
                  />
                </div>
                <div v-else class="h-48 bg-gradient-to-br from-burgundy/10 to-purple-100 flex items-center justify-center">
                  <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
                
                <div class="p-6">
                  <div class="flex items-start justify-between mb-4">
                    <div>
                      <h3 class="font-bold text-xl mb-1">{{ pkg.name }}</h3>
                      <p class="text-2xl font-bold text-burgundy">৳{{ pkg.price }}</p>
                    </div>
                    <span
                      :class="`px-3 py-1 rounded-full text-xs font-medium ${pkg.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`"
                    >
                      {{ pkg.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </div>
                  
                  <p class="text-sm text-gray-600 mb-4">{{ pkg.description }}</p>
                  
                  <div class="space-y-2 mb-4">
                    <div class="flex items-center gap-2 text-sm">
                      <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>{{ pkg.duration_hours }} hours coverage</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                      <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>{{ pkg.edited_photos }} edited photos</span>
                    </div>
                    <div v-if="pkg.raw_photos" class="flex items-center gap-2 text-sm">
                      <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>{{ pkg.raw_photos }} raw photos</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                      <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>{{ pkg.delivery_days }} days delivery</span>
                    </div>
                  </div>

                  <div class="flex gap-2">
                    <button
                      @click="editPackage(pkg)"
                      class="flex-1 px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                    >
                      Edit
                    </button>
                    <button
                      @click="deletePackage(pkg)"
                      class="px-3 py-2 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200"
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
              <h3 class="text-xl font-bold mb-4">{{ editingPackage ? 'Edit Package' : 'Create New Package' }}</h3>
              <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium mb-2">Package Name *</label>
                    <input
                      v-model="packageForm.name"
                      type="text"
                      class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                      placeholder="Basic Package"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-2">Price (৳) *</label>
                    <input
                      v-model.number="packageForm.price"
                      type="number"
                      class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                      placeholder="15000"
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium mb-2">Description *</label>
                  <textarea
                    v-model="packageForm.description"
                    rows="3"
                    class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                    placeholder="Perfect for small events and gatherings..."
                  ></textarea>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded p-4">
                  <p class="text-sm font-medium mb-2">📸 Package Images (Optional)</p>
                  <div class="space-y-3">
                    <div>
                      <label class="block text-xs text-gray-600 mb-1">Cover Image URL</label>
                      <input
                        v-model="packageForm.cover_image"
                        type="url"
                        class="w-full border rounded px-3 py-2 text-sm focus:ring-2 focus:ring-purple-600"
                        placeholder="https://images.pexels.com/photos/..."
                      />
                      <p class="text-xs text-gray-500 mt-1">Add a Pexels or external image URL</p>
                    </div>
                    <div v-if="packageForm.cover_image" class="mt-2">
                      <img :src="packageForm.cover_image" alt="Cover preview" class="w-32 h-32 object-cover rounded" />
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
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-2">Edited Photos *</label>
                    <input
                      v-model.number="packageForm.edited_photos"
                      type="number"
                      class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                      placeholder="50"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-2">Raw Photos</label>
                    <input
                      v-model.number="packageForm.raw_photos"
                      type="number"
                      class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                      placeholder="0"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-2">Delivery Days *</label>
                    <input
                      v-model.number="packageForm.delivery_days"
                      type="number"
                      class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                      placeholder="7"
                    />
                  </div>
                </div>

                <div class="flex items-center gap-2">
                  <input
                    v-model="packageForm.is_active"
                    type="checkbox"
                    id="pkg_is_active"
                    class="w-4 h-4 text-burgundy focus:ring-burgundy"
                  />
                  <label for="pkg_is_active" class="text-sm font-medium">Make this package active</label>
                </div>
              </div>
              
              <div class="flex gap-3 mt-6">
                <button
                  @click="savePackage"
                  :disabled="!packageForm.name || !packageForm.price || creatingPackage"
                  class="flex-1 px-4 py-2 bg-burgundy text-white rounded hover:bg-[#6F112D] disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {{ creatingPackage ? 'Saving...' : (editingPackage ? 'Update Package' : 'Create Package') }}
                </button>
                <button
                  @click="closePackageModal"
                  class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50"
                >
                  Cancel
                </button>
              </div>
            </div>
          </div>

          <!-- Reviews Tab -->
          <div v-if="activeTab === 'reviews'">
            <h2 class="text-xl font-bold mb-4">Client Reviews</h2>
            <EmptyState
              icon="star"
              title="No reviews yet"
              description="Your client reviews will appear here after completing bookings"
            />
          </div>

          <!-- Competitions Tab -->
          <div v-if="activeTab === 'competitions'">
            <div class="mb-6">
              <h2 class="text-xl font-bold">My Competition Submissions</h2>
              <p class="text-sm text-gray-600 mt-1">View and manage your submissions to photography competitions</p>
            </div>

            <!-- Loading State -->
            <div v-if="loadingSubmissions" class="grid grid-cols-1 gap-4">
              <LoadingSkeleton type="card" v-for="n in 3" :key="n" />
            </div>

            <!-- Submissions List -->
            <div v-else-if="submissions.length > 0" class="space-y-4">
              <div v-for="submission in submissions" :key="submission.id" class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                <div class="flex items-start gap-4">
                  <!-- Submission Image -->
                  <div class="w-32 h-32 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                    <img 
                      v-if="submission.photo_url" 
                      :src="submission.photo_url" 
                      :alt="submission.title"
                      class="w-full h-full object-cover"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center">
                      <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                    </div>
                  </div>

                  <!-- Submission Details -->
                  <div class="flex-1">
                    <h3 class="font-semibold text-lg mb-1">{{ submission.title }}</h3>
                    <p class="text-sm text-gray-600 mb-3">{{ submission.description }}</p>
                    
                    <!-- Competition Info -->
                    <div class="flex items-center gap-2 mb-2">
                      <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                      </svg>
                      <a 
                        :href="`/competitions/${submission.competition?.slug}`" 
                        class="text-sm text-burgundy hover:underline"
                      >
                        {{ submission.competition?.title }}
                      </a>
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
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                        </svg>
                        <span>{{ submission.votes_count || 0 }} votes</span>
                      </div>
                      <div class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Submitted {{ formatDate(submission.created_at) }}</span>
                      </div>
                    </div>
                  </div>

                  <!-- View Button -->
                  <a 
                    :href="`/competitions/${submission.competition?.slug}`"
                    class="px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors text-sm font-medium"
                  >
                    View Competition
                  </a>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="space-y-4">
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                  <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <div>
                    <h3 class="font-semibold text-blue-900">Participate in Competitions</h3>
                    <p class="text-sm text-blue-700 mt-1">Submit your best work to photography competitions and compete for prizes.</p>
                    <ul class="mt-2 text-sm text-blue-700 space-y-1">
                      <li>• Browse active competitions in the Competitions page</li>
                      <li>• Submit your best photographs</li>
                      <li>• Get public votes and judge ratings</li>
                      <li>• Win prizes and build your reputation</li>
                    </ul>
                    <a href="/competitions" class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                      Browse Competitions
                    </a>
                  </div>
                </div>
              </div>
              <div class="text-center py-12 text-gray-600">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                <p class="text-lg font-medium mb-2">No submissions yet</p>
                <p class="text-sm mb-4">Submit to a competition to see your entries here</p>
              </div>
            </div>
          </div>

          <!-- Events Tab -->
          <div v-if="activeTab === 'events'">
            <div class="mb-6">
              <h2 class="text-xl font-bold">My Events</h2>
              <p class="text-sm text-gray-600 mt-1">Events you've registered for or attended</p>
            </div>

            <!-- Events coming soon notice -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                  <h3 class="font-semibold text-blue-900">Participate in Events</h3>
                  <p class="text-sm text-blue-700 mt-1">Browse photography events, workshops, and meetups organized by admins.</p>
                  <a href="/events" class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    Browse Events
                  </a>
                </div>
              </div>
            </div>

            <!-- Create Event Form -->
            <div v-if="showEventForm" class="bg-white border rounded-lg p-6 mb-6">
              <h3 class="text-lg font-semibold mb-4">Create New Event</h3>
              <form @submit.prevent="createEvent" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Event Title *</label>
                  <input
                    v-model="eventForm.title"
                    type="text"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                    placeholder="Photography Workshop 2026"
                  />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Type *</label>
                    <select
                      v-model="eventForm.event_type"
                      required
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                    >
                      <option value="">Select type</option>
                      <option value="workshop">Workshop</option>
                      <option value="exhibition">Exhibition</option>
                      <option value="meetup">Meetup</option>
                      <option value="competition">Competition</option>
                      <option value="seminar">Seminar</option>
                      <option value="other">Other</option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Date *</label>
                    <input
                      v-model="eventForm.event_date"
                      type="datetime-local"
                      required
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                  <textarea
                    v-model="eventForm.description"
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                    placeholder="Describe your event..."
                  ></textarea>
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
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                    <select
                      v-model="eventForm.city_id"
                      required
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                    >
                      <option value="">Select city</option>
                      <option v-for="city in cities" :key="city.id" :value="city.id">
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
                    />
                    <p class="text-xs text-gray-500 mt-1">Max 500 for photographers</p>
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
                    />
                    <p class="text-xs text-gray-500 mt-1">Max ৳50,000</p>
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
                    />
                    <p class="text-xs text-gray-500 mt-1">Max 24 hours</p>
                  </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                  <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm text-yellow-800">
                      <p class="font-medium">Event Approval Required</p>
                      <p>Your event will be saved as a draft and requires admin approval before going live. You'll be notified once approved.</p>
                    </div>
                  </div>
                </div>

                <div class="flex justify-end gap-3">
                  <button
                    type="button"
                    @click="showEventForm = false"
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
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

            <div v-else class="space-y-4">
              <div
                v-for="event in events"
                :key="event.id"
                class="bg-white border rounded-lg p-6 hover:shadow-md transition-shadow"
              >
                <div class="flex justify-between items-start mb-3">
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                      <h3 class="text-lg font-bold">{{ event.title }}</h3>
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
                    <p class="text-sm text-gray-600">{{ event.event_type }} • {{ event.location }}</p>
                  </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-4">
                  <div>
                    <span class="text-gray-600">Date:</span>
                    <p class="font-medium">{{ formatDate(event.event_date) }}</p>
                  </div>
                  <div>
                    <span class="text-gray-600">RSVPs:</span>
                    <p class="font-medium">{{ event.rsvp_count || 0 }}</p>
                  </div>
                  <div>
                    <span class="text-gray-600">Views:</span>
                    <p class="font-medium">{{ event.view_count || 0 }}</p>
                  </div>
                  <div>
                    <span class="text-gray-600">Price:</span>
                    <p class="font-medium">{{ event.ticket_price ? `৳${event.ticket_price}` : 'Free' }}</p>
                  </div>
                </div>

                <div class="flex gap-2">
                  <button
                    @click="viewEvent(event)"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm"
                  >
                    View
                  </button>
                  <button
                    v-if="event.status === 'draft'"
                    @click="editEvent(event)"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm"
                  >
                    Edit
                  </button>
                  <button
                    v-if="event.status === 'published'"
                    @click="cancelEvent(event)"
                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm"
                  >
                    Cancel Event
                  </button>
                  <button
                    v-if="event.status === 'draft' && (event.rsvp_count === 0 || !event.rsvp_count)"
                    @click="deleteEvent(event)"
                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </div>

            <!-- Registered Events Section -->
            <div v-if="eventRsvps.length > 0" class="mt-8">
              <h3 class="text-lg font-bold mb-4">Registered Events</h3>
              <p class="text-sm text-gray-600 mb-4">Events you've registered to attend</p>
              
              <!-- Loading State -->
              <div v-if="loadingEventRsvps" class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-burgundy"></div>
              </div>

              <!-- RSVPs List -->
              <div v-else class="space-y-4">
                <div
                  v-for="rsvp in eventRsvps"
                  :key="rsvp.id"
                  class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
                >
                  <div class="flex items-start gap-4">
                    <!-- Event Banner -->
                    <div v-if="rsvp.event?.banner_image" class="w-24 h-24 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                      <img 
                        :src="rsvp.event.banner_image" 
                        :alt="rsvp.event.title"
                        class="w-full h-full object-cover"
                      />
                    </div>
                    
                    <!-- Event Details -->
                    <div class="flex-1">
                      <div class="flex items-start justify-between mb-2">
                        <div>
                          <h4 class="font-semibold text-lg mb-1">{{ rsvp.event?.title }}</h4>
                          <p class="text-sm text-gray-600">{{ rsvp.event?.location_text || 'Location TBA' }}</p>
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
                          <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          </svg>
                          <span class="text-gray-700">
                            {{ formatDate(rsvp.event?.event_date) }}
                          </span>
                        </div>
                        <div class="flex items-center gap-2">
                          <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                          </svg>
                          <span class="text-gray-700">{{ rsvp.event?.city?.name }}</span>
                        </div>
                      </div>

                      <div class="flex gap-2">
                        <a 
                          :href="`/events/${rsvp.event?.slug}`"
                          class="px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors text-sm font-medium"
                        >
                          View Event
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Achievements Tab -->
          <div v-if="activeTab === 'achievements'" class="p-4 sm:p-6">
            <div class="flex flex-col lg:flex-row gap-6">
              <div class="flex-1 bg-gradient-to-r from-burgundy to-[#8B1538] rounded-lg p-6 text-white">
                <h2 class="text-xl font-bold mb-2">Your Progress</h2>
                <p class="text-sm text-white/90 mb-4">Track your achievements, points, and level</p>

                <div v-if="loadingAchievementsSummary" class="text-sm text-white/80">Loading achievements...</div>
                <div v-else class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                  <div>
                    <p class="text-2xl font-bold">{{ achievementsSummary.stats?.level || 1 }}</p>
                    <p class="text-xs text-white/80">Level</p>
                  </div>
                  <div>
                    <p class="text-2xl font-bold">{{ achievementsSummary.stats?.total_points || 0 }}</p>
                    <p class="text-xs text-white/80">Total Points</p>
                  </div>
                  <div>
                    <p class="text-2xl font-bold">{{ achievementsSummary.unlocked_achievements || 0 }}/{{ achievementsSummary.total_achievements || 0 }}</p>
                    <p class="text-xs text-white/80">Unlocked</p>
                  </div>
                  <div>
                    <p class="text-2xl font-bold">{{ achievementsSummary.completion_percentage || 0 }}%</p>
                    <p class="text-xs text-white/80">Completion</p>
                  </div>
                </div>
              </div>

              <div class="w-full lg:w-80 bg-white border rounded-lg p-6">
                <h3 class="text-lg font-bold mb-2">Next Level</h3>
                <p class="text-sm text-gray-600 mb-4">Keep earning points to level up!</p>
                <div class="mb-4">
                  <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Points Needed</span>
                    <span class="font-semibold">{{ achievementsSummary.points_to_next_level || 0 }}</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div
                      class="bg-burgundy h-2 rounded-full"
                      :style="{ width: ((achievementsSummary.stats?.total_points || 0) % 100) + '%' }"
                    ></div>
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
          <div v-if="activeTab === 'awards'" class="p-4 sm:p-6">
            <div class="mb-6">
              <div class="flex items-center justify-between">
                <div>
                  <h2 class="text-xl font-bold">Awards & Achievements</h2>
                  <p class="text-sm text-gray-600 mt-1">Showcase your awards, certifications, and achievements</p>
                </div>
                <button
                  @click="showAddAwardModal = true"
                  class="px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors flex items-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                  Add Award
                </button>
              </div>
            </div>

            <!-- Loading State -->
            <div v-if="loadingAwards" class="text-center py-12">
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-burgundy"></div>
              <p class="text-sm text-gray-600 mt-2">Loading awards...</p>
            </div>

            <!-- Empty State -->
            <div v-else-if="awards.length === 0" class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed">
              <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
              </svg>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">No Awards Yet</h3>
              <p class="text-sm text-gray-600 mb-4">Start building your credibility by adding your awards and achievements</p>
              <button
                @click="showAddAwardModal = true"
                class="px-6 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors"
              >
                Add Your First Award
              </button>
            </div>

            <!-- Awards List -->
            <div v-else class="space-y-4">
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
                        <h3 class="font-semibold text-lg text-gray-900">{{ award.title }}</h3>
                        <p v-if="award.organization" class="text-sm text-gray-600 mt-1">{{ award.organization }}</p>
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

                    <p v-if="award.description" class="text-sm text-gray-600 mt-2">{{ award.description }}</p>

                    <!-- Certificate Link -->
                    <a
                      v-if="award.certificate_url"
                      :href="award.certificate_url"
                      target="_blank"
                      class="inline-flex items-center gap-1 text-sm text-burgundy hover:underline mt-2"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                      </svg>
                      View Certificate
                    </a>

                    <!-- Actions -->
                    <div class="flex gap-2 mt-3">
                      <button
                        @click="editAward(award)"
                        class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                      >
                        Edit
                      </button>
                      <button
                        @click="deleteAward(award.id)"
                        class="px-3 py-1 text-sm bg-red-50 text-red-600 rounded hover:bg-red-100"
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
              <h3 class="text-xl font-bold">{{ editingAward ? 'Edit Award' : 'Add New Award' }}</h3>
              <button @click="closeAwardModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <form @submit.prevent="saveAward" class="p-6 space-y-4">
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
                />
                <p v-if="awardErrors.title" class="text-sm text-red-600 mt-1">{{ awardErrors.title[0] }}</p>
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
                />
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
                  />
                  <p v-if="awardErrors.year" class="text-sm text-red-600 mt-1">{{ awardErrors.year[0] }}</p>
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
                    <option value="award">🏆 Award</option>
                    <option value="achievement">⭐ Achievement</option>
                    <option value="recognition">🎖️ Recognition</option>
                    <option value="certification">📜 Certification</option>
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
                ></textarea>
              </div>

              <!-- Certificate Upload -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Certificate (JPG, PNG, or PDF - Max 5MB)
                </label>
                <input
                  type="file"
                  @change="handleCertificateUpload"
                  accept="image/jpeg,image/jpg,image/png,application/pdf"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                />
                <p v-if="awardErrors.certificate_file" class="text-sm text-red-600 mt-1">{{ awardErrors.certificate_file[0] }}</p>
                <p v-if="editingAward && editingAward.certificate_url" class="text-sm text-gray-600 mt-1">
                  Current certificate: 
                  <a :href="editingAward.certificate_url" target="_blank" class="text-burgundy hover:underline">View</a>
                </p>
              </div>

              <!-- Error Message -->
              <div v-if="awardErrorMessage" class="p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-600">{{ awardErrorMessage }}</p>
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
                  @click="closeAwardModal"
                  class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium"
                >
                  Cancel
                </button>
              </div>
            </form>
          </div>
        </div>
      </teleport>

      <!-- Quick Links -->
      <div class="mt-4 sm:mt-6 md:mt-8 bg-white rounded-lg shadow p-4 sm:p-6">
        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Quick Links</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4">
          <button @click="$router.push('/competitions')" class="flex flex-col items-center p-3 sm:p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 active:bg-red-100 transition-all group min-h-[88px] sm:min-h-0">
            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-600 group-hover:text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
            <span class="text-xs sm:text-sm font-medium text-center leading-tight">Competitions</span>
          </button>

          <button @click="$router.push('/events')" class="flex flex-col items-center p-3 sm:p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 active:bg-red-100 transition-all group min-h-[88px] sm:min-h-0">
            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-600 group-hover:text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="text-xs sm:text-sm font-medium text-center leading-tight">Events</span>
          </button>

          <button @click="$router.push('/transactions')" class="flex flex-col items-center p-3 sm:p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 active:bg-red-100 transition-all group min-h-[88px] sm:min-h-0">
            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-600 group-hover:text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="text-xs sm:text-sm font-medium text-center leading-tight">Transactions</span>
          </button>

          <button @click="$router.push('/notifications')" class="flex flex-col items-center p-3 sm:p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 active:bg-red-100 transition-all group min-h-[88px] sm:min-h-0">
            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-600 group-hover:text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="text-xs sm:text-sm font-medium text-center leading-tight">Notifications</span>
          </button>

          <button @click="$router.push('/photographer')" class="flex flex-col items-center p-3 sm:p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 active:bg-red-100 transition-all group min-h-[88px] sm:min-h-0">
            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-600 group-hover:text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span class="text-xs sm:text-sm font-medium text-center leading-tight">Search</span>
          </button>

          <button @click="$router.push('/help')" class="flex flex-col items-center p-3 sm:p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 active:bg-red-100 transition-all group min-h-[88px] sm:min-h-0">
            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-600 group-hover:text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-xs sm:text-sm font-medium text-center leading-tight">Help</span>
          </button>

          <button @click="$router.push('/settings')" class="flex flex-col items-center p-3 sm:p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 active:bg-red-100 transition-all group min-h-[88px] sm:min-h-0">
            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-600 group-hover:text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="text-xs sm:text-sm font-medium text-center leading-tight">Settings</span>
          </button>

          <button @click="logout" class="flex flex-col items-center p-3 sm:p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 active:bg-red-100 transition-all group min-h-[88px] sm:min-h-0">
            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-600 group-hover:text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="text-xs sm:text-sm font-medium text-center leading-tight">Logout</span>
          </button>
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
        <svg v-if="toast.type === 'success'" class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <svg v-else-if="toast.type === 'error'" class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <svg v-else-if="toast.type === 'info'" class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        <svg v-else class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <p class="flex-1 text-sm font-medium">{{ toast.message }}</p>
        <button @click="removeToast(toast.id)" class="flex-shrink-0 hover:opacity-75">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import api from '../api';
import ImageUpload from './ImageUpload.vue';
import AlbumPhotoManager from './AlbumPhotoManager.vue';
import NotificationBell from './NotificationBell.vue';
import LoadingSkeleton from './ui/LoadingSkeleton.vue';
import EmptyState from './ui/EmptyState.vue';

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
  const slug = user.value?.photographer?.slug;
  if (!slug) return window.location.origin + '/photographer/your-username';
  return window.location.origin + '/photographer/' + slug;
});

const copyProfileLink = async () => {
  try {
    await navigator.clipboard.writeText(profileUrl.value);
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 2000);
  } catch (error) {
    console.error('Failed to copy:', error);
    // Fallback for older browsers
    const input = document.createElement('input');
    input.value = profileUrl.value;
    document.body.appendChild(input);
    input.select();
    document.execCommand('copy');
    document.body.removeChild(input);
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 2000);
  }
};

const fetchDashboardData = async () => {
  try {
    const { data: userData } = await api.get('/auth/me');
    if (userData.status === 'success') {
      user.value = userData.data;
      
      // Load photographer data into profile form
      if (user.value.photographer) {
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
    confirmed: 'bg-green-100 text-green-800',
    completed: 'bg-blue-100 text-blue-800',
    cancelled: 'bg-red-100 text-red-800',
    rejected: 'bg-gray-100 text-gray-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
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
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });
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
    const { data } = await api.get('/cities');
    cities.value = data.data || data;
  } catch (error) {
    console.error('Error fetching cities:', error);
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
    const { data } = await api.get('/photographer/submissions');
    if (data.status === 'success') {
      submissions.value = data.data || [];
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
const handleCertificateUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
      awardErrors.value.certificate_file = ['Certificate file must not exceed 5MB'];
      event.target.value = '';
      return;
    }
    certificateFile.value = file;
    delete awardErrors.value.certificate_file;
  }
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
