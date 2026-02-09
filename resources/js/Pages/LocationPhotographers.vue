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
                d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
              />
            </svg>
          </div>
          <div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-semibold text-gray-900">
              Find Photographers <span class="text-primary-700">by Location</span>
            </h1>
            <p class="text-sm sm:text-base lg:text-lg text-primary-700 font-medium mt-1">
              Discover talented photographers near you
            </p>
          </div>
        </div>
        <div class="h-1 w-24 bg-gradient-to-r from-primary-500 to-primary-400 rounded-full" />
      </div>

      <div
        v-if="loading"
        class="flex justify-center items-center py-20"
      >
        <div class="text-center">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-primary-200 border-t-primary-600 mx-auto mb-4" />
          <p class="text-primary-700 font-semibold">
            Loading photographers...
          </p>
        </div>
      </div>

      <div
        v-else
        class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8"
      >
        <!-- Filters Sidebar -->
        <div class="hidden lg:block lg:col-span-4">
          <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 sticky top-20">
            <!-- Location Filter -->
            <div class="mb-8">
              <label class="block text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <span class="text-2xl">📍</span>
                <span>Location</span>
              </label>
              <div class="space-y-2">
                <button
                  :class="[
                    'w-full text-left px-4 py-3 rounded-lg font-semibold transition-all',
                    !selectedCity
                      ? 'bg-primary-700 text-white shadow-md'
                      : 'bg-white text-gray-800 hover:bg-gray-50 border-2 border-gray-300 hover:border-primary-400'
                  ]"
                  @click="selectedCity = null"
                >
                  All Locations
                </button>
                <button
                  v-for="city in cityData"
                  :key="city.slug"
                  :class="[
                    'w-full text-left px-4 py-3 rounded-lg font-semibold transition-all',
                    selectedCity === city.slug
                      ? 'bg-primary-700 text-white shadow-md'
                      : 'bg-white text-gray-800 hover:bg-gray-50 border-2 border-gray-300 hover:border-primary-400'
                  ]"
                  @click="selectedCity = city.slug"
                >
                  {{ city.display }}
                </button>
              </div>
            </div>

            <!-- Rating Filter -->
            <div class="mb-8 pb-8 border-b border-gray-200">
              <label class="block text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <span class="text-2xl">⭐</span>
                <span>Rating</span>
              </label>
              <div class="space-y-2">
                <button
                  :class="[
                    'w-full text-left px-4 py-2 rounded-lg text-sm font-semibold transition-all',
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
                    'w-full text-left px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center justify-between',
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
              <label class="block text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <span class="text-2xl">📊</span>
                <span>Sort By</span>
              </label>
              <select
                v-model="sortBy"
                class="w-full bg-white border-2 border-gray-200 rounded-lg px-4 py-3 text-gray-900 font-medium focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-600/30 transition-all"
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
                <option value="reviews">
                  Most Reviews
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
              <p class="text-base sm:text-lg font-semibold text-gray-900">
                Found <span class="text-primary-700">{{ filteredPhotographers.length }}</span> photographers
              </p>
              <button
                class="lg:hidden px-4 py-2 rounded-lg border-2 border-primary-300 text-primary-700 font-semibold hover:bg-primary-50"
                @click="isFilterOpen = true"
              >
                Filters
              </button>
            </div>
            <div class="flex-1 sm:flex-none">
              <select
                v-model="sortBy"
                class="w-full sm:w-56 bg-white border-2 border-gray-200 rounded-lg px-4 py-2 text-gray-900 font-medium focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-600/30 transition-all"
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
                <option value="reviews">
                  Most Reviews
                </option>
              </select>
            </div>
          </div>

          <!-- Results Count -->
          <div class="mb-4 sm:mb-6 flex items-center justify-between">
            <p class="hidden sm:block text-lg font-semibold text-gray-900">
              Found <span class="text-primary-700">{{ filteredPhotographers.length }}</span> photographers
              <span
                v-if="selectedCityName"
                class="text-gray-600"
              >in {{ selectedCityName }}</span>
            </p>
            <div class="flex gap-2">
              <button
                :class="[
                  'px-4 py-2 rounded-lg font-semibold transition-all',
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
                  'px-4 py-2 rounded-lg font-semibold transition-all',
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
            class="bg-gradient-to-br from-white via-primary-50/30 to-purple-50/40 rounded-2xl shadow-xl p-8 sm:p-16 border-2 border-primary-100 text-center"
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
                <span v-if="selectedCityName">No photographers available in <strong class="text-primary-700">{{ selectedCityName }}</strong> at the moment.</span>
                <span v-else>Try adjusting your filters or browse all photographers.</span>
              </p>
              
              <!-- Actions -->
              <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button
                  class="px-6 py-3 bg-gradient-to-r from-primary-700 to-primary-800 text-white rounded-xl hover:from-primary-800 hover:to-primary-900 transition-all font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                  @click="selectedCity = null; selectedRating = null"
                >
                  Clear Filters
                </button>
                <router-link
                  to="/photographers/by-category"
                  class="px-6 py-3 bg-white text-primary-700 border-2 border-primary-200 rounded-xl hover:bg-primary-50 hover:border-primary-300 transition-all font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                >
                  Browse All Categories
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
              :to="`/photographer/${photographer.slug}`"
              class="bg-white rounded-xl sm:rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all border border-gray-100"
            >
              <!-- Image -->
              <div class="relative h-56 sm:h-48 bg-gradient-to-br from-primary-100 via-primary-50 to-purple-100 overflow-hidden">
                <img
                  v-if="photographer.profile_photo"
                  :src="photographer.profile_photo"
                  :alt="photographer.name"
                  class="w-full h-full object-cover hover:scale-110 transition-transform"
                  @error="$event.target.style.display='none'; $event.target.parentElement.querySelector('.fallback-icon').style.display='flex'"
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
                    class="w-4 h-4 sm:w-5 sm:h-5 text-primary-700"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z" />
                  </svg>
                </div>
              </div>

              <!-- Info -->
              <div class="p-3 sm:p-4 md:p-5">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1 truncate">
                  {{ photographer.name }}
                </h3>
                <router-link
                  v-if="photographer.city_slug"
                  :to="`/photographers/by-location?city=${photographer.city_slug}`"
                  class="text-xs sm:text-sm text-primary-700 font-medium mb-2 sm:mb-3 inline-flex"
                >
                  {{ photographer.city_name }}
                </router-link>
                <p
                  v-else
                  class="text-xs sm:text-sm text-primary-700 font-medium mb-2 sm:mb-3"
                >
                  {{ photographer.city_name }}
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

                <!-- Categories -->
                <div class="flex flex-wrap gap-1.5 sm:gap-2 mb-3 sm:mb-4">
                  <router-link
                    v-for="category in photographer.categories?.slice(0, 2)"
                    :key="category"
                    :to="`/photographers/by-category?category=${slugify(category)}`"
                    class="px-2 py-1 bg-primary-100 text-primary-700 text-xs font-semibold rounded-full hover:bg-primary-200"
                  >
                    {{ category }}
                  </router-link>
                  <span
                    v-if="photographer.categories?.length > 2"
                    class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full"
                  >
                    +{{ photographer.categories.length - 2 }}
                  </span>
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
              :to="`/photographer/${photographer.slug}`"
              class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-3 sm:p-4 md:p-6 border border-gray-100 hover:shadow-xl hover:border-primary-300 transition-all flex flex-col sm:flex-row gap-3 sm:gap-4 md:gap-6 hover:-translate-y-1"
            >
              <!-- Photo -->
              <div class="w-full sm:w-24 md:w-32 h-48 sm:h-24 md:h-32 rounded-lg sm:rounded-xl bg-gradient-to-br from-primary-100 via-primary-50 to-purple-100 flex-shrink-0 overflow-hidden relative">
                <img
                  v-if="photographer.profile_photo"
                  :src="photographer.profile_photo"
                  :alt="photographer.name"
                  class="w-full h-full object-cover"
                  @error="$event.target.style.display='none'; $event.target.parentElement.querySelector('.fallback-icon').style.display='flex'"
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
              </div>

              <!-- Details -->
              <div class="flex-1 min-w-0">
                <h3 class="text-lg sm:text-xl md:text-2xl font-semibold text-gray-900 mb-2 truncate">
                  {{ photographer.name }}
                </h3>
                <router-link
                  v-if="photographer.city_slug"
                  :to="`/photographers/by-location?city=${photographer.city_slug}`"
                  class="text-sm sm:text-base text-primary-700 font-medium mb-2 flex items-center gap-1 sm:gap-2 hover:text-primary-800"
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
                  class="text-sm sm:text-base text-primary-700 font-medium mb-2 flex items-center gap-1 sm:gap-2"
                >
                  <svg
                    class="w-4 h-4"
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

                <!-- Rating & Categories -->
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

                <!-- Categories -->
                <div class="flex flex-wrap gap-2 mb-4">
                  <router-link
                    v-for="category in photographer.categories"
                    :key="category"
                    :to="`/photographers/by-category?category=${slugify(category)}`"
                    class="px-3 py-1 bg-primary-100 text-primary-700 text-xs font-semibold rounded-full hover:bg-primary-200"
                  >
                    {{ category }}
                  </router-link>
                </div>

                <!-- CTA -->
                <div class="flex flex-col sm:flex-row gap-2">
                  <button class="w-full sm:w-auto px-6 py-2 bg-gradient-to-r from-primary-700 to-primary-800 text-white rounded-lg hover:from-primary-800 hover:to-primary-900 transition-all font-semibold text-sm shadow-md">
                    View Profile
                  </button>
                  <button class="w-full sm:w-auto px-6 py-2 bg-primary-100 text-primary-700 rounded-lg hover:bg-primary-200 transition-all font-medium text-sm border-2 border-primary-200">
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
            <label class="block text-base font-semibold text-gray-900 mb-3 flex items-center gap-2">
              <span class="text-2xl">📍</span>
              <span>City</span>
            </label>
            <div class="space-y-2">
              <button
                :class="[
                  'w-full text-left px-4 py-3 rounded-lg font-semibold transition-all',
                  !selectedCity
                    ? 'bg-primary-700 text-white shadow-md'
                    : 'bg-white text-gray-800 hover:bg-gray-50 border-2 border-gray-300 hover:border-primary-400'
                ]"
                @click="selectedCity = null"
              >
                All Cities
              </button>
              <button
                v-for="city in cityData"
                :key="city.slug"
                :class="[
                  'w-full text-left px-4 py-3 rounded-lg font-semibold transition-all',
                  selectedCity === city.slug
                    ? 'bg-primary-700 text-white shadow-md'
                    : 'bg-white text-gray-800 hover:bg-gray-50 border-2 border-gray-300 hover:border-primary-400'
                ]"
                @click="selectedCity = city.slug"
              >
                {{ city.display }}
              </button>
            </div>
          </div>

          <div>
            <label class="block text-base font-semibold text-gray-900 mb-3 flex items-center gap-2">
              <span class="text-2xl">⭐</span>
              <span>Rating</span>
            </label>
            <div class="space-y-2">
              <button
                :class="[
                  'w-full text-left px-4 py-2 rounded-lg text-sm font-semibold transition-all',
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
                  'w-full text-left px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center justify-between',
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
import FilterOffcanvas from '../components/ui/FilterOffcanvas.vue'
import Toast from '../components/ui/Toast.vue'
import { useDevLogger } from '../composables/useDevLogger'
import { useApiError } from '../composables/useApiError'

const loading = ref(true)
const photographers = ref([])
const locations = ref([])
const selectedCity = ref(null)
const selectedRating = ref(null)
const sortBy = ref('recent')
const viewMode = ref('grid')
const displayLimit = ref(12)
const itemsPerPage = 12
const isFilterOpen = ref(false)

const route = useRoute()
const router = useRouter()
const { log } = useDevLogger()
const { toastMessage, toastType, toastVisible, handleApiError, closeToast } = useApiError()

const cityData = computed(() => {
  if (Array.isArray(locations.value) && locations.value.length > 0) {
    return locations.value
      .filter(city => city.count > 0)
      .sort((a, b) => a.name.localeCompare(b.name))
  }

  if (!Array.isArray(photographers.value)) return []
  // Extract unique cities from photographer city objects
  const uniqueCities = []
  const seenSlugs = new Set()
  
  for (const p of photographers.value) {
    if (p.city?.slug && !seenSlugs.has(p.city.slug)) {
      seenSlugs.add(p.city.slug)
      uniqueCities.push({
        slug: p.city.slug,
        name: p.city.name,
        display: p.city.name
      })
    }
  }
  
  return uniqueCities.sort((a, b) => a.name.localeCompare(b.name))
})

const selectedCityName = computed(() => {
  if (!selectedCity.value) return ''
  return cityData.value.find(city => city.slug === selectedCity.value)?.name || ''
})

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
  selectedCity.value = null
  selectedRating.value = null
}

const applyFilters = () => {
  isFilterOpen.value = false
}

const filteredPhotographers = computed(() => {
  if (!Array.isArray(photographers.value)) return []
  
  let filtered = [...photographers.value] // Create a copy to avoid mutating original

  // Note: City filtering is already done by the API in fetchPhotographers()
  // No need to filter by city again here - API already returns only matching photographers

  // Filter by rating
  if (selectedRating.value) {
    filtered = filtered.filter(p => (p.rating || 0) >= selectedRating.value)
  }

  // Sort
  switch (sortBy.value) {
    case 'rating':
      filtered.sort((a, b) => (b.rating || 0) - (a.rating || 0))
      break
    case 'popular':
      filtered.sort((a, b) => (b.reviews_count || 0) - (a.reviews_count || 0))
      break
    case 'reviews':
      filtered.sort((a, b) => (b.reviews_count || 0) - (a.reviews_count || 0))
      break
    default:
      filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
  }

  return filtered
})

const displayedPhotographers = computed(() => {
  return filteredPhotographers.value.slice(0, displayLimit.value)
})

const fetchPhotographers = async () => {
  loading.value = true
  try {
    const params = { per_page: 100 }
    
    // If a city is selected, filter by city slug
    if (selectedCity.value) {
      params.city = selectedCity.value
    }
    
    const response = await api.get('/photographers', { params })
    log('Raw API Response:', response.data)
    
    // Handle different response structures
    let data = []
    
    // First check if it's a success response with .data property
    if (response.data?.data && Array.isArray(response.data.data)) {
      data = response.data.data
      log('Using response.data.data (success response structure)')
    } 
    // Check if the entire response is an array
    else if (Array.isArray(response.data)) {
      data = response.data
      log('Using response.data directly as array')
    } 
    // Check if it's an object with photographers
    else if (response.data && typeof response.data === 'object') {
      data = Object.values(response.data).filter(item => 
        item && typeof item === 'object' && (item.id || item.user_id)
      )
      log('Extracted data from object values:', data.length)
    }
    
    log('Extracted data before transform:', data.length, data)
    log('API Response for city:', selectedCity.value || 'all', 'Count:', data.length)
    
    // Transform photographer data to ensure consistent structure
    photographers.value = data.map(p => ({
      ...p,
      // Ensure city is accessible in multiple ways
      city_name: p.city?.name || p.city || 'Unknown',
      city_slug: p.city?.slug || slugify(p.city?.name || p.city || ''),
      // Ensure categories array exists and is properly formatted
      categories: Array.isArray(p.categories) 
        ? p.categories.map(cat => cat.name || cat)
        : [],
      // Add convenient accessors
      rating: p.average_rating || 0,
      reviews_count: p.rating_count || 0,
      name: p.user?.name || 'Unknown',
      profile_photo: p.profile_picture_url || p.profile_picture || null,
      has_photo: !!(p.profile_picture_url || p.profile_picture)
    }))
    
    log('Photographers after transform:', photographers.value.length)
  } catch (error) {
    handleApiError(error, 'Failed to fetch photographers')
    photographers.value = []
  } finally {
    loading.value = false
  }
}

const loadLocations = async () => {
  try {
    const response = await api.get('/locations')
    const allLocations = response.data.data || response.data || []
    const visibleLocations = allLocations.filter(city => city.type !== 'division')
    locations.value = visibleLocations.map(city => ({
      slug: city.slug,
      name: city.name,
      display: city.name,
      count: Number(city.photographers_count || city.count || 0)
    }))
  } catch (error) {
    handleApiError(error, 'Failed to load locations')
    locations.value = []
  }
}

// Watch for city changes to refetch photographers
watch(selectedCity, (newCity) => {
  if (newCity) {
    router.replace({ query: { ...route.query, city: newCity } })
  } else {
    const { city, ...rest } = route.query
    router.replace({ query: rest })
  }
  fetchPhotographers()
})

onMounted(async () => {
  const initialCity = route.query.city
  log('OnMounted - Initial city from URL:', initialCity)
  
  if (typeof initialCity === 'string') {
    selectedCity.value = initialCity
    log('OnMounted - Set selectedCity to:', selectedCity.value)
  }

  await Promise.all([loadLocations(), fetchPhotographers()])
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
