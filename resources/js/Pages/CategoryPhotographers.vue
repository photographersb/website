<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 sm:py-10 lg:py-12 px-3 sm:px-4">
    <Toast
      v-if="toastVisible"
      :message="toastMessage"
      :type="toastType"
      @close="closeToast"
    />
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8 sm:mb-12">
        <div class="flex items-start sm:items-center gap-3 sm:gap-4 mb-4">
          <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-xl">
            <svg
              class="w-8 h-8 text-white"
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
          <div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-semibold text-gray-900">
              Find Photographers <span class="text-primary-700">by Category</span>
            </h1>
            <p class="text-sm sm:text-base lg:text-lg text-primary-700 font-medium mt-1">
              Browse by photography specialty
            </p>
          </div>
        </div>
        <div class="h-1 w-24 bg-gradient-to-r from-primary-500 to-primary-400 rounded-full" />
      </div>

      <!-- Category Grid -->
      <div
        v-if="!selectedCategory"
        class="mb-10 sm:mb-12"
      >
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-5 sm:mb-6">
          Photography Categories
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
          <button
            v-for="category in categories"
            :key="category.id"
            class="bg-white rounded-2xl shadow-lg p-5 sm:p-8 border border-gray-100 hover:shadow-2xl hover:border-primary-300 hover:-translate-y-2 transition-all text-left group"
            @click="selectedCategory = category.slug"
          >
            <div class="text-5xl mb-4 group-hover:scale-125 transition-transform">
              {{ category.icon }}
            </div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-2">
              {{ category.name }}
            </h3>
            <p class="text-gray-600 mb-4">
              {{ category.description }}
            </p>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-primary-700">{{ category.count }} photographers</span>
              <svg
                class="w-5 h-5 text-primary-600 group-hover:translate-x-2 transition-transform"
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
          </button>
        </div>
      </div>

      <!-- Back Button -->
      <button
        v-if="selectedCategory"
        class="mb-6 inline-flex items-center gap-2 px-4 py-2 text-primary-700 hover:text-primary-800 font-medium hover:bg-primary-100 rounded-lg transition-all"
        @click="selectedCategory = null"
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
            d="M15 19l-7-7 7-7"
          />
        </svg>
        Back to Categories
      </button>

      <div
        v-if="selectedCategory && loading"
        class="flex justify-center items-center py-20"
      >
        <div class="text-center">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-primary-200 border-t-primary-600 mx-auto mb-4" />
          <p class="text-primary-700 font-medium">
            Loading photographers...
          </p>
        </div>
      </div>

      <!-- Category View -->
      <div
        v-else-if="selectedCategory"
        class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8"
      >
        <!-- Filters Sidebar -->
        <div class="hidden lg:block lg:col-span-4">
          <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 sticky top-20">
            <!-- Selected Category Info -->
            <div class="mb-8 pb-8 border-b border-gray-200">
              <div class="text-4xl mb-3">
                {{ selectedCategoryData?.icon }}
              </div>
              <h3 class="text-xl font-semibold text-gray-900">
                {{ selectedCategoryData?.name }}
              </h3>
              <p class="text-sm text-gray-600 mt-2">
                {{ selectedCategoryData?.description }}
              </p>
            </div>

            <!-- Price Range Filter -->
            <div class="mb-8">
              <label class="block text-base font-medium text-gray-900 mb-4 flex items-center gap-2">
                <span class="text-2xl">💰</span>
                <span>Price Range</span>
              </label>
              <div class="space-y-2">
                <button
                  :class="[
                    'w-full text-left px-4 py-3 rounded-lg font-medium transition-all',
                    !selectedPrice
                      ? 'bg-primary-700 text-white shadow-md'
                      : 'bg-white text-gray-800 hover:bg-gray-50 border-2 border-gray-300 hover:border-primary-400'
                  ]"
                  @click="selectedPrice = null"
                >
                  All Prices
                </button>
                <button
                  v-for="price in priceRanges"
                  :key="price.label"
                  :class="[
                    'w-full text-left px-4 py-3 rounded-lg font-medium transition-all',
                    selectedPrice?.label === price.label
                      ? 'bg-primary-700 text-white shadow-md'
                      : 'bg-white text-gray-800 hover:bg-gray-50 border-2 border-gray-300 hover:border-primary-400'
                  ]"
                  @click="selectedPrice = price"
                >
                  {{ price.label }}
                </button>
              </div>
            </div>

            <!-- Rating Filter -->
            <div class="mb-8 pb-8 border-b border-gray-200">
              <label class="block text-base font-medium text-gray-900 mb-4 flex items-center gap-2">
                <span class="text-2xl">⭐</span>
                <span>Rating</span>
              </label>
              <div class="space-y-2">
                <button
                  :class="[
                    'w-full text-left px-4 py-2 rounded-lg text-sm font-medium transition-all',
                    !selectedRating
                      ? 'bg-primary-700 text-white shadow-md'
                      : 'bg-white text-gray-800 hover:bg-gray-50 border-2 border-gray-300'
                  ]"
                  @click="selectedRating = null"
                >
                  All Ratings
                </button>
                <button
                  v-for="rating in [5, 4, 3]"
                  :key="rating"
                  :class="[
                    'w-full text-left px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center justify-between',
                    selectedRating === rating
                      ? 'bg-primary-700 text-white shadow-md'
                      : 'bg-white text-gray-800 hover:bg-gray-50 border-2 border-gray-300'
                  ]"
                  @click="selectedRating = rating"
                >
                  <span>{{ rating }}+ Stars</span>
                  <div class="flex gap-0.5">
                    <span
                      v-for="i in 5"
                      :key="i"
                      class="text-yellow-400"
                    >⭐</span>
                  </div>
                </button>
              </div>
            </div>

            <!-- Sort Options -->
            <div>
              <label class="block text-base font-medium text-gray-900 mb-4 flex items-center gap-2">
                <span class="text-2xl">📊</span>
                <span>Sort By</span>
              </label>
              <select
                v-model="sortBy"
                class="w-full bg-white border-2 border-gray-200 rounded-lg px-4 py-3 text-gray-900 font-medium focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-600 focus:ring-opacity-30 transition-all"
              >
                <option value="recent">
                  Most Recent
                </option>
                <option value="rating">
                  Highest Rated
                </option>
                <option value="popular">
                  Most Popular
                </option>
                <option value="price-low">
                  Price: Low to High
                </option>
                <option value="price-high">
                  Price: High to Low
                </option>
              </select>
            </div>
          </div>
        </div>

        <!-- Photographers Grid -->
        <div class="lg:col-span-8">
          <!-- Mobile Filter + Sort Bar -->
          <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center justify-between gap-3">
              <p class="text-base sm:text-lg font-medium text-gray-900">
                Found <span class="text-primary-700">{{ filteredPhotographers.length }}</span> photographers
              </p>
              <button
                class="lg:hidden px-4 py-2 rounded-lg border-2 border-primary-300 text-primary-700 font-medium hover:bg-primary-50"
                @click="isFilterOpen = true"
              >
                Filters
              </button>
            </div>
            <div class="flex-1 sm:flex-none">
              <select
                v-model="sortBy"
                class="w-full sm:w-56 bg-white border-2 border-gray-200 rounded-lg px-4 py-2 text-gray-900 font-medium focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-600 focus:ring-opacity-30 transition-all"
              >
                <option value="recent">
                  Most Recent
                </option>
                <option value="rating">
                  Highest Rated
                </option>
                <option value="popular">
                  Most Popular
                </option>
                <option value="price-low">
                  Price: Low to High
                </option>
                <option value="price-high">
                  Price: High to Low
                </option>
              </select>
            </div>
          </div>

          <!-- Results Count -->
          <div class="mb-4 sm:mb-6 flex items-center justify-between">
            <p class="hidden sm:block text-lg font-medium text-gray-900">
              Found <span class="text-primary-700">{{ filteredPhotographers.length }}</span> photographers
            </p>
            <div class="flex gap-2">
              <button
                :class="[
                  'px-4 py-2 rounded-lg font-medium transition-all',
                  viewMode === 'grid'
                    ? 'bg-primary-700 text-white shadow-md'
                    : 'bg-white text-primary-700 hover:bg-primary-100 border-2 border-primary-300 hover:border-primary-500'
                ]"
                @click="viewMode = 'grid'"
              >
                Grid
              </button>
              <button
                :class="[
                  'px-4 py-2 rounded-lg font-medium transition-all',
                  viewMode === 'list'
                    ? 'bg-primary-700 text-white shadow-md'
                    : 'bg-white text-primary-700 hover:bg-primary-100 border-2 border-primary-300 hover:border-primary-500'
                ]"
                @click="viewMode = 'list'"
              >
                List
              </button>
            </div>
          </div>

          <!-- Empty State -->
          <div
            v-if="!loading && filteredPhotographers.length === 0"
            class="bg-gradient-to-br from-white via-primary-50 via-opacity-30 to-purple-50 to-opacity-40 rounded-2xl shadow-xl p-8 sm:p-16 border-2 border-primary-100 text-center"
          >
            <div class="max-w-md mx-auto">
              <!-- Icon -->
              <div class="mb-6 flex justify-center">
                <div class="bg-gradient-to-br from-primary-100 to-purple-100 rounded-full p-6 inline-block">
                  <svg class="w-16 h-16 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
              </div>
              
              <!-- Heading -->
              <h3 class="text-2xl sm:text-3xl font-semibold text-gray-900 mb-3">
                No Photographers Found
              </h3>
              
              <!-- Description -->
              <p class="text-base sm:text-lg text-gray-600 mb-8 leading-relaxed">
                <span v-if="selectedCategory">No photographers specialize in this category yet.</span>
                <span v-else>Try adjusting your filters or browse different categories.</span>
              </p>
              
              <!-- Actions -->
              <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button
                  class="px-6 py-3 bg-gradient-to-r from-primary-700 to-primary-800 text-white rounded-xl hover:from-primary-800 hover:to-primary-900 transition-all font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                  @click="selectedPrice = null; selectedRating = null; sortBy = 'recent'"
                >
                  Clear Filters
                </button>
                <router-link
                  to="/photographers/by-location"
                  class="px-6 py-3 bg-white text-primary-700 border-2 border-primary-200 rounded-xl hover:bg-primary-50 hover:border-primary-300 transition-all font-medium shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                >
                  Browse by Location
                </router-link>
              </div>
            </div>
          </div>

          <!-- Loading State -->
          <div
            v-else-if="loading"
            class="bg-white rounded-2xl shadow-lg p-12 border border-gray-100 text-center"
          >
            <div class="flex flex-col items-center gap-4">
              <div class="animate-spin rounded-full h-16 w-16 border-4 border-primary-200 border-t-primary-700"></div>
              <p class="text-lg font-medium text-gray-700">Loading photographers...</p>
            </div>
          </div>

          <!-- Grid View -->
          <div
            v-else-if="viewMode === 'grid'"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 sm:gap-6"
          >
            <router-link
              v-for="photographer in displayedPhotographers"
              :key="photographer.id"
              :to="getPhotographerProfilePath(photographer)"
              class="bg-white rounded-xl sm:rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all border border-gray-100"
            >
              <!-- Image -->
              <div class="relative h-56 sm:h-48 bg-gradient-to-br from-primary-100 via-primary-50 to-purple-100 overflow-hidden">
                <img
                  v-if="photographer.profile_photo"
                  :src="photographer.profile_photo"
                  :alt="photographer.name"
                  class="w-full h-full object-cover hover:scale-110 transition-transform"
                  @error="showImageFallback"
                  loading="lazy"
                >
                <div
                  class="fallback-icon w-full h-full flex flex-col items-center justify-center text-primary-700"
                  :style="{ display: photographer.profile_photo ? 'none' : 'flex' }"
                >
                  <svg class="w-16 h-16 sm:w-20 sm:h-20 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  <span class="text-xs font-medium opacity-60">No Photo</span>
                </div>
                <div class="absolute top-2 sm:top-3 right-2 sm:right-3 bg-white rounded-full p-1.5 sm:p-2 shadow-lg">
                  <svg
                    v-if="photographer.verified"
                    class="w-4 h-4 sm:w-5 sm:h-5 text-green-600"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 3.062v6.757a1 1 0 01-.940 1.017 48.412 48.412 0 01-7.125 0 1 1 0 01-.94-1.017v-6.757a3.066 3.066 0 012.812-3.062zM9 12a1 1 0 100-2 1 1 0 000 2z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>
                <button
                  v-if="isClient"
                  class="absolute top-2 sm:top-3 left-2 sm:left-3 w-9 h-9 rounded-full flex items-center justify-center shadow-lg transition-all"
                  :class="favoriteIdSet.has(photographer.id) ? 'bg-[#7a1f2b] text-white' : 'bg-white text-[#7a1f2b] hover:scale-105'"
                  aria-label="Toggle favorite"
                  @click.stop.prevent="toggleFavorite(photographer.id)"
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
              </div>

              <!-- Info -->
              <div class="p-3 sm:p-4 md:p-5">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1 truncate">
                  {{ photographer.name }}
                </h3>
                <router-link
                  v-if="photographer.city_slug"
                  :to="`/photographers/by-location?city=${photographer.city_slug}`"
                  class="inline-flex items-center px-2 py-1 bg-primary-100 text-primary-700 text-xs font-medium rounded-full mb-2 hover:bg-primary-200"
                >
                  {{ photographer.city_name }}
                </router-link>
                <span
                  v-else
                  class="inline-flex items-center px-2 py-1 bg-primary-100 text-primary-700 text-xs font-medium rounded-full mb-2"
                >
                  {{ photographer.city_name }}
                </span>
                <p class="text-sm text-gray-600 mb-2 sm:mb-3">
                  from ৳{{ photographer.min_price || 1000 }}/event
                </p>
                
                <!-- Rating -->
                <div class="flex items-center gap-1 sm:gap-2 mb-2 sm:mb-3">
                  <div class="flex gap-0.5">
                    <span
                      v-for="i in 5"
                      :key="i"
                      class="text-yellow-400 text-sm sm:text-base"
                    >
                      {{ i <= Math.floor(photographer.rating || 0) ? '⭐' : '☆' }}
                    </span>
                  </div>
                  <span class="text-xs sm:text-sm text-gray-600 font-medium">({{ photographer.reviews_count || 0 }})</span>
                </div>

                <!-- CTA -->
                <button class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-gradient-to-r from-primary-700 to-primary-800 text-white rounded-lg hover:from-primary-800 hover:to-primary-900 transition-all font-semibold text-xs sm:text-sm">
                  View Profile
                </button>
              </div>
            </router-link>
          </div>

          <!-- List View -->
          <div
            v-else
            class="space-y-3 sm:space-y-4"
          >
            <router-link
              v-for="photographer in displayedPhotographers"
              :key="photographer.id"
              :to="getPhotographerProfilePath(photographer)"
              class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-3 sm:p-4 md:p-6 border border-gray-100 hover:shadow-xl hover:border-primary-300 transition-all flex flex-col sm:flex-row gap-3 sm:gap-4 md:gap-6 hover:-translate-y-1"
            >
              <!-- Photo -->
              <div class="w-full sm:w-24 md:w-32 h-48 sm:h-24 md:h-32 rounded-lg sm:rounded-xl bg-gradient-to-br from-primary-100 via-primary-50 to-purple-100 flex-shrink-0 overflow-hidden relative">
                <img
                  v-if="photographer.profile_photo"
                  :src="photographer.profile_photo"
                  :alt="photographer.name"
                  class="w-full h-full object-cover"
                  @error="showImageFallback"
                  loading="lazy"
                >
                <div
                  class="fallback-icon absolute inset-0 flex flex-col items-center justify-center text-primary-700"
                  :style="{ display: photographer.profile_photo ? 'none' : 'flex' }"
                >
                  <svg class="w-12 h-12 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <button
                  v-if="isClient"
                  class="absolute top-2 left-2 w-8 h-8 rounded-full flex items-center justify-center shadow-lg transition-all"
                  :class="favoriteIdSet.has(photographer.id) ? 'bg-[#7a1f2b] text-white' : 'bg-white text-[#7a1f2b] hover:scale-105'"
                  aria-label="Toggle favorite"
                  @click.stop.prevent="toggleFavorite(photographer.id)"
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
                <div
                  v-if="photographer.verified"
                  class="absolute top-1 sm:top-1 right-1 sm:right-1 bg-green-500 rounded-full p-1 shadow-lg"
                >
                  <svg
                    class="w-3 h-3 sm:w-4 sm:h-4 text-white"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>
              </div>

              <!-- Details -->
              <div class="flex-1 min-w-0">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-2">
                  <div class="min-w-0">
                    <h3 class="text-lg sm:text-xl md:text-2xl font-semibold text-gray-900 truncate">
                      {{ photographer.name }}
                    </h3>
                    <router-link
                      v-if="photographer.city_slug"
                      :to="`/photographers/by-location?city=${photographer.city_slug}`"
                      class="inline-flex items-center gap-1 sm:gap-2 px-2 sm:px-3 py-1 bg-primary-100 text-primary-700 text-xs font-medium rounded-full hover:bg-primary-200"
                    >
                      <svg
                        class="w-3 h-3 sm:w-4 sm:h-4"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      {{ photographer.city_name }}
                    </router-link>
                    <p
                      v-else
                      class="inline-flex items-center gap-1 sm:gap-2 px-2 sm:px-3 py-1 bg-primary-100 text-primary-700 text-xs font-medium rounded-full"
                    >
                      <svg
                        class="w-3 h-3 sm:w-4 sm:h-4"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      {{ photographer.city_name }}
                    </p>
                  </div>
                  <div class="text-left sm:text-right flex-shrink-0">
                    <p class="text-xs sm:text-sm text-gray-500">
                      Starting from
                    </p>
                    <p class="text-xl sm:text-2xl font-medium text-primary-700">
                      ৳{{ photographer.min_price || 1000 }}
                    </p>
                  </div>
                </div>

                <!-- Rating -->
                <div class="flex items-center gap-4 mb-3">
                  <div class="flex gap-0.5">
                    <span
                      v-for="i in 5"
                      :key="i"
                      class="text-yellow-400"
                    >
                      {{ i <= Math.floor(photographer.rating || 0) ? '⭐' : '☆' }}
                    </span>
                  </div>
                  <span class="text-sm text-gray-600 font-medium">({{ photographer.reviews_count || 0 }} reviews)</span>
                </div>

                <!-- Description -->
                <p class="text-gray-600 text-sm mb-4">
                  {{ photographer.bio || 'Professional photographer' }}
                </p>

                <!-- CTAs -->
                <div class="flex flex-col sm:flex-row gap-2">
                  <button class="w-full sm:w-auto px-6 py-2 bg-gradient-to-r from-primary-700 to-primary-800 text-white rounded-lg hover:from-primary-800 hover:to-primary-900 transition-all font-semibold text-sm">
                    Book Now
                  </button>
                  <button class="w-full sm:w-auto px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all font-medium text-sm border border-gray-200">
                    Message
                  </button>
                </div>
              </div>
            </router-link>
          </div>

          <!-- Load More -->
          <div
            v-if="filteredPhotographers.length > itemsPerPage"
            class="mt-8 text-center"
          >
            <button
              class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-primary-700 to-primary-800 text-white rounded-lg hover:from-primary-800 hover:to-primary-900 transition-all font-semibold shadow-lg hover:shadow-xl"
              @click="displayLimit += itemsPerPage"
            >
              Load More Photographers
            </button>
          </div>
        </div>
      </div>
      <FilterOffcanvas
        v-model="isFilterOpen"
        title="Filter Photographers"
        :on-apply="applyFilters"
        :on-reset="resetFilters"
      >
        <div class="space-y-6">
          <div>
            <div class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">
              Category
            </div>
            <div class="flex items-center gap-3">
              <div class="text-2xl">
                {{ selectedCategoryData?.icon }}
              </div>
              <div>
                <p class="text-base font-medium text-gray-900">
                  {{ selectedCategoryData?.name }}
                </p>
                <p class="text-sm text-gray-600">
                  {{ selectedCategoryData?.description }}
                </p>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-base font-medium text-gray-900 mb-3 flex items-center gap-2">
              <span class="text-2xl">💰</span>
              <span>Price Range</span>
            </label>
            <div class="space-y-2">
              <button
                :class="[
                  'w-full text-left px-4 py-3 rounded-lg font-medium transition-all',
                  !selectedPrice
                    ? 'bg-primary-700 text-white shadow-md'
                    : 'bg-white text-gray-800 hover:bg-gray-50 border-2 border-gray-300 hover:border-primary-400'
                ]"
                @click="selectedPrice = null"
              >
                All Prices
              </button>
              <button
                v-for="price in priceRanges"
                :key="price.label"
                :class="[
                  'w-full text-left px-4 py-3 rounded-lg font-medium transition-all',
                  selectedPrice?.label === price.label
                    ? 'bg-primary-700 text-white shadow-md'
                    : 'bg-white text-gray-800 hover:bg-gray-50 border-2 border-gray-300 hover:border-primary-400'
                ]"
                @click="selectedPrice = price"
              >
                {{ price.label }}
              </button>
            </div>
          </div>

          <div>
            <label class="block text-base font-medium text-gray-900 mb-3 flex items-center gap-2">
              <span class="text-2xl">⭐</span>
              <span>Rating</span>
            </label>
            <div class="space-y-2">
              <button
                :class="[
                  'w-full text-left px-4 py-2 rounded-lg text-sm font-medium transition-all',
                  !selectedRating
                    ? 'bg-primary-700 text-white shadow-md'
                    : 'bg-white text-gray-800 hover:bg-gray-50 border-2 border-gray-300'
                ]"
                @click="selectedRating = null"
              >
                All Ratings
              </button>
              <button
                v-for="rating in [5, 4, 3]"
                :key="rating"
                :class="[
                  'w-full text-left px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center justify-between',
                  selectedRating === rating
                    ? 'bg-primary-700 text-white shadow-md'
                    : 'bg-white text-gray-800 hover:bg-gray-50 border-2 border-gray-300'
                ]"
                @click="selectedRating = rating"
              >
                <span>{{ rating }}+ Stars</span>
                <div class="flex gap-0.5">
                  <span
                    v-for="i in 5"
                    :key="i"
                    class="text-yellow-400"
                  >⭐</span>
                </div>
              </button>
            </div>
          </div>
        </div>
      </FilterOffcanvas>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../api'
import Toast from '../components/ui/Toast.vue'
import { useDevLogger } from '../composables/useDevLogger'
import { useApiError } from '../composables/useApiError'
import FilterOffcanvas from '../components/ui/FilterOffcanvas.vue'

const loading = ref(false)
const photographers = ref([])
const categories = ref([]) // Now loaded dynamically!
const selectedCategory = ref(null)
const selectedPrice = ref(null)
const selectedRating = ref(null)
const sortBy = ref('recent')
const viewMode = ref('grid')
const displayLimit = ref(12)
const itemsPerPage = 12
const isFilterOpen = ref(false)
const favoriteIds = ref([])

const route = useRoute()
const router = useRouter()
const { log } = useDevLogger()
const { toastMessage, toastType, toastVisible, handleApiError, closeToast } = useApiError()

const normalizeRole = (role) => String(role || '').toLowerCase().replace(/[\s-]+/g, '_')
const storedUserRole = computed(() => {
  const storedUser = JSON.parse(localStorage.getItem('user') || '{}')
  return normalizeRole(localStorage.getItem('user_role') || storedUser.role)
})
const isClient = computed(() => storedUserRole.value === 'client')
const favoriteIdSet = computed(() => new Set(favoriteIds.value))

const priceRanges = [
  { label: '৳0 - ৳1,000', min: 0, max: 1000 },
  { label: '৳1,000 - ৳3,000', min: 1000, max: 3000 },
  { label: '৳3,000 - ৳5,000', min: 3000, max: 5000 },
  { label: '৳5,000+', min: 5000, max: Infinity }
]

// Icon mapping for categories
const categoryIcons = {
  'wedding-photography': '💒',
  'portrait-photography': '👤',
  'event-photography': '🎉',
  'product-photography': '📦',
  'corporate-photography': '🏢',
  'fashion-model-photography': '👗',
  'default': '📷'
}

const getIconForCategory = (slug) => {
  return categoryIcons[slug] || categoryIcons['default']
}

const slugify = (value = '') => {
  return value
    .toString()
    .trim()
    .toLowerCase()
    .replace(/\s+/g, '-')
    .replace(/[^a-z0-9-]/g, '')
}

const getCitySlug = (photographer) => {
  if (photographer?.city?.slug) return photographer.city.slug
  if (typeof photographer?.city === 'string') return slugify(photographer.city)
  if (typeof photographer?.city?.name === 'string') return slugify(photographer.city.name)
  return ''
}

const resetFilters = () => {
  selectedPrice.value = null
  selectedRating.value = null
}

const applyFilters = () => {
  isFilterOpen.value = false
}

const selectedCategoryData = computed(() => {
  return categories.value.find(cat => cat.slug === selectedCategory.value)
})

const filteredPhotographers = computed(() => {
  if (!Array.isArray(photographers.value)) return []
  
  let filtered = [...photographers.value]

  // Filter by price
  if (selectedPrice.value) {
    filtered = filtered.filter(p => {
      const price = p.starting_price || p.min_price || 1000
      return price >= selectedPrice.value.min && price <= selectedPrice.value.max
    })
  }

  // Filter by rating
  if (selectedRating.value) {
    filtered = filtered.filter(p => (p.average_rating || 0) >= selectedRating.value)
  }

  // Sort
  switch (sortBy.value) {
    case 'rating':
      filtered.sort((a, b) => (b.average_rating || 0) - (a.average_rating || 0))
      break
    case 'popular':
      filtered.sort((a, b) => (b.rating_count || 0) - (a.rating_count || 0))
      break
    case 'price-low':
      filtered.sort((a, b) => (a.starting_price || a.min_price || 1000) - (b.starting_price || b.min_price || 1000))
      break
    case 'price-high':
      filtered.sort((a, b) => (b.starting_price || b.min_price || 1000) - (a.starting_price || a.min_price || 1000))
      break
    default:
      filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
  }

  return filtered
})

const displayedPhotographers = computed(() => {
  return filteredPhotographers.value.slice(0, displayLimit.value)
})

// Load all categories with counts
const loadCategories = async () => {
  try {
    const response = await api.get('/categories')
    const allCategories = response.data.data || response.data || []
    categories.value = allCategories
      .map((cat) => ({
        ...cat,
        count: Number(cat.photographers_count || cat.count || 0),
        icon: cat.icon || getIconForCategory(cat.slug),
        description: cat.description || `Browse ${cat.name} photographers`
      }))
      .filter(c => c.count > 0)
  } catch (error) {
    handleApiError(error, 'Failed to load categories')
    categories.value = []
  }
}

const fetchPhotographers = async () => {
  if (!selectedCategory.value) return
  
  loading.value = true
  try {
    const response = await api.get('/photographers', {
      params: {
        per_page: 100,
        category: selectedCategory.value
      }
    })
    
    // Handle nested data structure from API
    const data = response.data?.data || response.data || []
    log('API Response for category:', selectedCategory.value, 'Count:', data.length)
    
    // Transform photographer data to ensure consistent structure
    photographers.value = data.map(p => ({
      ...p,
      // Ensure city is accessible as both object and string
      city_name: p.city?.name || p.city || 'Unknown',
      city_slug: p.city?.slug || slugify(p.city?.name || p.city || ''),
      // Ensure categories array exists
      categories: Array.isArray(p.categories) ? p.categories : [],
      // Add convenient accessors
      min_price: p.starting_price || p.min_price || 1000,
      rating: p.average_rating || 0,
      reviews_count: p.rating_count || 0,
      name: p.user?.name || 'Unknown',
      profile_photo: p.profile_picture_url || p.profile_picture || null,
      has_photo: !!(p.profile_picture_url || p.profile_picture)
    }))
  } catch (error) {
    handleApiError(error, 'Failed to fetch photographers')
    photographers.value = []
  } finally {
    loading.value = false
  }
}

const loadFavorites = async () => {
  if (!isClient.value) return
  try {
    const response = await api.get('/client/favorites')
    const items = response.data.data || []
    favoriteIds.value = items.map((favorite) => favorite.photographer_id)
  } catch (error) {
    console.error('Failed to load favorites:', error)
  }
}

const toggleFavorite = async (photographerId) => {
  if (!isClient.value) {
    router.push('/auth')
    return
  }

  const isFavorite = favoriteIdSet.value.has(photographerId)
  try {
    if (isFavorite) {
      await api.delete(`/client/favorites/${photographerId}`)
      favoriteIds.value = favoriteIds.value.filter((id) => id !== photographerId)
    } else {
      await api.post(`/client/favorites/${photographerId}`)
      favoriteIds.value = [...favoriteIds.value, photographerId]
    }
  } catch (error) {
    console.error('Failed to toggle favorite:', error)
  }
}

const getPhotographerProfilePath = (photographer) => {
  const username = photographer?.user?.username
  if (username) {
    return `/@${username}`
  }

  const slugOrId = photographer?.slug || photographer?.id
  return slugOrId ? `/photographer/${slugOrId}` : '/photographers'
}

const showImageFallback = (event) => {
  const imageEl = event?.target
  if (!imageEl) return

  imageEl.style.display = 'none'

  const fallback = imageEl.parentElement?.querySelector('.fallback-icon')
  if (fallback) {
    fallback.style.display = 'flex'
  }
}

// Watch for category changes
watch(selectedCategory, (newCategory) => {
  if (newCategory) {
    router.replace({ query: { ...route.query, category: newCategory } })
    fetchPhotographers()
  } else {
    const { category, ...rest } = route.query
    router.replace({ query: rest })
    photographers.value = []
  }
})

onMounted(async () => {
  await loadCategories()
  await loadFavorites()
  const initialCategory = route.query.category
  if (typeof initialCategory === 'string') {
    selectedCategory.value = initialCategory
  }
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
