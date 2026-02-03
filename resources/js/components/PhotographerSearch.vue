<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <!-- Hero Section with Mobile-First Responsive Design -->
    <section class="relative overflow-hidden bg-gradient-to-br from-burgundy via-[#8E0E3F] to-[#6F112D] text-white">
      <!-- Decorative Background Elements (Hidden on Mobile, Visible on Desktop) -->
      <div class="absolute inset-0 overflow-hidden hidden sm:block">
        <div class="absolute top-0 left-0 w-48 h-48 sm:w-64 sm:h-64 md:w-96 md:h-96 bg-white/5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-48 h-48 sm:w-64 sm:h-64 md:w-96 md:h-96 bg-white/5 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
        <div class="absolute top-1/2 left-1/2 w-32 h-32 sm:w-48 sm:h-48 md:w-64 md:h-64 bg-white/3 rounded-full blur-2xl -translate-x-1/2 -translate-y-1/2"></div>
      </div>

      <!-- Mobile Background Gradient for Visibility -->
      <div class="absolute inset-0 sm:hidden opacity-40">
        <div class="absolute top-10 left-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute bottom-10 right-5 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
      </div>

      <div class="relative z-10 w-full px-4 sm:px-6 md:px-8 lg:px-4 py-6 sm:py-8 md:py-12 lg:py-16">
        <div class="max-w-5xl mx-auto">
          <!-- Logo/Brand Section - Responsive Text -->
          <div class="text-center mb-4 sm:mb-5 md:mb-6 lg:mb-8">
            <div class="inline-flex mb-2 sm:mb-3 px-3 sm:px-4 py-1 sm:py-1.5 bg-white/10 backdrop-blur-sm rounded-full border border-white/20 hover:bg-white/15 transition-colors">
              <p class="text-xs sm:text-sm font-medium flex items-center gap-1 sm:gap-2 justify-center">
                <svg class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z"/>
                </svg>
                <span>A Project by</span>
                <a href="https://somogrobangladesh.com/" target="_blank" rel="noopener" class="underline hover:text-white/80 transition-colors font-semibold">
                  Somogro Bangladesh
                </a>
              </p>
            </div>
          </div>

          <!-- Main Heading - Fluid Typography with clamp() -->
          <div class="text-center mb-5 sm:mb-6 md:mb-8 lg:mb-10">
            <h1 class="font-bold mb-2 sm:mb-3 md:mb-4 tracking-tight leading-tight" style="font-size: clamp(1.75rem, 5vw, 4rem);">
              <span class="inline-block animate-fade-in">Find Your Perfect</span>
              <br class="sm:hidden" />
              <span class="block bg-gradient-to-r from-white via-gray-100 to-white bg-clip-text text-transparent animate-fade-in-delay">Photographer</span>
            </h1>
            <p class="font-medium text-gray-100 max-w-2xl mx-auto leading-relaxed animate-fade-in-delay-2" style="font-size: clamp(0.95rem, 2vw, 1.25rem);">
              Connect with <span class="font-bold text-white">{{ totalPhotographers }}+ verified professionals</span> across Bangladesh for weddings, portraits & events.
            </p>
          </div>

          <!-- Search Bar - Fully Responsive -->
          <div class="max-w-3xl mx-auto animate-fade-in-delay-3">
            <!-- Search Container -->
            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg sm:rounded-xl md:rounded-2xl p-2.5 sm:p-3 md:p-4 shadow-2xl">
              <div class="flex flex-col gap-2.5 sm:gap-3 md:gap-3">
                <!-- Search Input Row -->
                <div class="flex flex-col sm:flex-row gap-2.5 sm:gap-3">
                  <div class="flex-1 relative">
                    <svg class="absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 w-4 h-4 sm:w-5 sm:h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                      v-model="searchQuery"
                      @keyup.enter="search"
                      type="text"
                      placeholder="Search by name or specialty..."
                      class="w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-3 sm:py-3.5 md:py-4 rounded-lg sm:rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all bg-white/95 text-sm sm:text-base"
                    />
                  </div>
                  <button
                    @click="search"
                    class="group px-4 sm:px-6 md:px-8 py-3 sm:py-3.5 md:py-4 bg-gradient-to-r from-burgundy to-[#6F112D] hover:from-[#6F112D] hover:to-burgundy text-white font-semibold rounded-lg sm:rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 whitespace-nowrap flex items-center justify-center gap-2 text-sm sm:text-base md:text-base w-full sm:w-auto"
                  >
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span class="hidden sm:inline">Search</span>
                  </button>
                </div>

                <!-- Popular Categories (Quick Filters) - Mobile Optimized -->
                <div class="flex flex-wrap justify-center gap-1.5 sm:gap-2 md:gap-2">
                  <button
                    v-for="category in popularCategories"
                    :key="category.slug"
                    @click="quickFilter(category.slug)"
                    class="px-2.5 sm:px-3 md:px-4 py-1 sm:py-1.5 md:py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-full text-xs sm:text-sm border border-white/20 transition-all hover:scale-105 whitespace-nowrap font-medium"
                  >
                    <span class="mr-1">{{ category.icon }}</span>{{ category.name }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Quick Stats - Responsive Grid -->
            <div class="grid grid-cols-3 gap-2 sm:gap-3 md:gap-6 lg:gap-8 mt-5 sm:mt-6 md:mt-8 lg:mt-10 pb-16 sm:pb-20 md:pb-24 lg:pb-28">
              <div class="text-center px-1 sm:px-2">
                <div class="font-bold mb-3 sm:mb-3 md:mb-4 leading-tight text-white" style="font-size: clamp(1.75rem, 5vw, 3.5rem);">
                  <span v-if="totalPhotographers === '...'" class="inline-block animate-pulse">...</span>
                  <span v-else>{{ totalPhotographers }}<span class="text-lg sm:text-2xl md:text-3xl">+</span></span>
                </div>
                <div class="text-xs sm:text-sm font-bold text-white uppercase tracking-wide" style="font-size: clamp(0.65rem, 1.5vw, 0.9rem);">Photographers</div>
              </div>
              <div class="text-center px-1 sm:px-2 border-x border-white/40">
                <div class="font-bold mb-3 sm:mb-3 md:mb-4 leading-tight text-white" style="font-size: clamp(1.75rem, 5vw, 3.5rem);">64</div>
                <div class="text-xs sm:text-sm font-bold text-white uppercase tracking-wide" style="font-size: clamp(0.65rem, 1.5vw, 0.9rem);">Districts</div>
              </div>
              <div class="text-center px-1 sm:px-2">
                <div class="font-bold mb-3 sm:mb-3 md:mb-4 leading-tight text-white" style="font-size: clamp(1.75rem, 5vw, 3.5rem);">10K+</div>
                <div class="text-xs sm:text-sm font-bold text-white uppercase tracking-wide" style="font-size: clamp(0.65rem, 1.5vw, 0.9rem);">Happy Clients</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Smooth Rounded Curve Separator -->
      <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
          <path d="M0 20 Q360 5 720 0 Q1080 5 1440 20 L1440 100 L0 100 Z" fill="#f9fafb"/>
        </svg>
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
                <svg class="absolute left-2 sm:left-3 top-1/2 -translate-y-1/2 w-4 h-4 sm:w-5 sm:h-5 text-gray-400 pointer-events-none flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <select v-model="filters.category" class="w-full pl-8 sm:pl-10 pr-2 sm:pr-3 py-2 sm:py-2.5 md:py-3 border-2 border-gray-200 rounded-lg sm:rounded-xl focus:border-burgundy focus:ring-2 focus:ring-burgundy/20 transition-all appearance-none bg-white cursor-pointer hover:border-gray-300 text-xs sm:text-sm font-medium">
                  <option value="">All</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.slug">
                    {{ cat.name }}
                  </option>
                </select>
                <svg class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 w-3 h-3 sm:w-4 sm:h-4 text-gray-400 pointer-events-none flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </div>
            </div>
            
            <!-- City Filter -->
            <div class="relative col-span-1">
              <label class="block text-xs sm:text-sm font-semibold mb-1 sm:mb-2 text-gray-700">City</label>
              <div class="relative">
                <svg class="absolute left-2 sm:left-3 top-1/2 -translate-y-1/2 w-4 h-4 sm:w-5 sm:h-5 text-gray-400 pointer-events-none flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <select v-model="filters.city" class="w-full pl-8 sm:pl-10 pr-2 sm:pr-3 py-2 sm:py-2.5 md:py-3 border-2 border-gray-200 rounded-lg sm:rounded-xl focus:border-burgundy focus:ring-2 focus:ring-burgundy/20 transition-all appearance-none bg-white cursor-pointer hover:border-gray-300 text-xs sm:text-sm font-medium">
                  <option value="">All</option>
                  <option v-for="city in cities" :key="city.id" :value="city.slug">
                    {{ city.name }}
                  </option>
                </select>
                <svg class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 w-3 h-3 sm:w-4 sm:h-4 text-gray-400 pointer-events-none flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </div>
            </div>
            
            <!-- Rating Filter -->
            <div class="relative col-span-1 hidden sm:block">
              <label class="block text-xs sm:text-sm font-semibold mb-1 sm:mb-2 text-gray-700">Rating</label>
              <div class="relative">
                <svg class="absolute left-2 sm:left-3 top-1/2 -translate-y-1/2 w-4 h-4 sm:w-5 sm:h-5 text-yellow-400 pointer-events-none flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <select v-model="filters.rating" class="w-full pl-8 sm:pl-10 pr-2 sm:pr-3 py-2 sm:py-2.5 md:py-3 border-2 border-gray-200 rounded-lg sm:rounded-xl focus:border-burgundy focus:ring-2 focus:ring-burgundy/20 transition-all appearance-none bg-white cursor-pointer hover:border-gray-300 text-xs sm:text-sm font-medium">
                  <option value="">All</option>
                  <option value="5">★★★★★</option>
                  <option value="4">★★★★+</option>
                  <option value="3">★★★+</option>
                </select>
                <svg class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 w-3 h-3 sm:w-4 sm:h-4 text-gray-400 pointer-events-none flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </div>
            </div>
            
            <!-- Sort By Filter -->
            <div class="relative col-span-1 hidden sm:block">
              <label class="block text-xs sm:text-sm font-semibold mb-1 sm:mb-2 text-gray-700">Sort</label>
              <div class="relative">
                <svg class="absolute left-2 sm:left-3 top-1/2 -translate-y-1/2 w-4 h-4 sm:w-5 sm:h-5 text-gray-400 pointer-events-none flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/>
                </svg>
                <select v-model="filters.sort" class="w-full pl-8 sm:pl-10 pr-2 sm:pr-3 py-2 sm:py-2.5 md:py-3 border-2 border-gray-200 rounded-lg sm:rounded-xl focus:border-burgundy focus:ring-2 focus:ring-burgundy/20 transition-all appearance-none bg-white cursor-pointer hover:border-gray-300 text-xs sm:text-sm font-medium">
                  <option value="relevance">Relevant</option>
                  <option value="rating">Rated</option>
                  <option value="newest">Newest</option>
                </select>
                <svg class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 w-3 h-3 sm:w-4 sm:h-4 text-gray-400 pointer-events-none flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
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
        <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 md:gap-6 lg:gap-8">
          <LoadingSkeleton v-for="n in 8" :key="n" type="card" />
        </div>

        <!-- Empty State -->
        <EmptyState
          v-else-if="photographers.length === 0"
          icon="camera"
          :title="searchQuery || filters.category || filters.city || filters.rating ? 'No photographers found' : 'Building Our Community'"
          :description="searchQuery || filters.category || filters.city || filters.rating ? 'Try adjusting your filters or search terms to find more photographers.' : 'We\'re building our photographer community. Check back soon or be among the first to join!'"
          :actionText="searchQuery || filters.category || filters.city || filters.rating ? 'Reset Filters' : ''"
          @action="resetFilters"
        />

        <!-- Photographers Grid -->
        <div v-else>
          <!-- Results Header - Responsive Typography -->
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 mb-6 sm:mb-8 md:mb-10">
            <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 leading-tight" style="font-size: clamp(1.25rem, 3vw, 1.875rem);">
              <span v-if="searchQuery || filters.category || filters.city || filters.rating">
                {{ photographers.length }} Photographer{{ photographers.length !== 1 ? 's' : '' }} Found
              </span>
              <span v-else>
                Featured Photographers
              </span>
            </h2>
            <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-600 font-medium whitespace-nowrap">
              <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
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
              ctaText="View Profile"
              @click="viewPhotographer(photographer)"
              @book="bookPhotographer(photographer)"
            />
          </div>

          <!-- Pagination - Mobile Responsive -->
          <div v-if="totalPages > 1" class="flex flex-wrap justify-center items-center gap-2 mt-8 sm:mt-12 md:mt-16">
            <button
              v-if="currentPage > 1"
              @click="previousPage"
              class="px-3 sm:px-4 md:px-6 py-2 sm:py-2.5 md:py-3 border-2 border-gray-200 rounded-lg sm:rounded-xl hover:border-burgundy hover:text-burgundy transition-all font-medium text-sm sm:text-base flex items-center gap-1 sm:gap-2"
            >
              <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              <span class="hidden sm:inline">Previous</span>
            </button>
            
            <div class="flex items-center gap-1 sm:gap-2">
              <span v-for="page in visiblePages" :key="page" 
                @click="goToPage(page)"
                :class="page === currentPage ? 'bg-burgundy text-white border-burgundy' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-200'"
                class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 flex items-center justify-center rounded-lg sm:rounded-xl cursor-pointer transition-all font-semibold border-2 text-xs sm:text-sm md:text-base">
                {{ page }}
              </span>
            </div>
            
            <button
              v-if="currentPage < totalPages"
              @click="nextPage"
              class="px-3 sm:px-4 md:px-6 py-2 sm:py-2.5 md:py-3 border-2 border-gray-200 rounded-lg sm:rounded-xl hover:border-burgundy hover:text-burgundy transition-all font-medium text-sm sm:text-base flex items-center gap-1 sm:gap-2"
            >
              <span class="hidden sm:inline">Next</span>
              <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
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

const router = useRouter();
const photographers = ref([]);
const categories = ref([]);
const cities = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const currentPage = ref(1);
const totalPages = ref(1);
const totalPhotographers = ref('...');

const filters = ref({
  category: '',
  city: '',
  rating: '',
  sort: 'relevance',
});

const popularCategories = ref([
  { name: 'Wedding', slug: 'wedding', icon: '💒' },
  { name: 'Portrait', slug: 'portrait', icon: '📸' },
  { name: 'Event', slug: 'event', icon: '🎉' },
  { name: 'Newborn', slug: 'newborn', icon: '👶' },
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

const fetchCategories = async () => {
  try {
    const { data } = await api.get('/categories');
    if (data.status === 'success') {
      categories.value = data.data;
    }
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

const fetchCities = async () => {
  try {
    const { data } = await api.get('/cities');
    if (data.status === 'success') {
      cities.value = data.data;
    }
  } catch (error) {
    console.error('Error fetching cities:', error);
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
    
    console.log('API Response:', data); // Debug log

    if (data.status === 'success') {
      photographers.value = data.data;
      totalPages.value = data.meta?.last_page || 1;
      currentPage.value = data.meta?.current_page || 1;
      totalPhotographers.value = data.meta?.total || data.data?.length || 0;
      console.log('Total Photographers:', totalPhotographers.value); // Debug log
    }
  } catch (error) {
    console.error('Error fetching photographers:', error);
    photographers.value = [];
  } finally {
    loading.value = false;
  }
};

const search = () => {
  currentPage.value = 1;
  fetchPhotographers();
};

const viewPhotographer = (photographer) => {
  router.push(`/photographer/${photographer.slug}`);
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

onMounted(() => {
  fetchCategories();
  fetchCities();
  fetchPhotographers();
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

/* Prevent horizontal scroll */
body, html {
  overflow-x: hidden;
}
</style>
