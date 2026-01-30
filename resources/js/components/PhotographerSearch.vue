<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <!-- Hero Section with Parallax Effect -->
    <section class="relative overflow-hidden bg-gradient-to-br from-burgundy via-[#8E0E3F] to-[#6F112D] text-white">
      <!-- Decorative Background Elements -->
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white/5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
        <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-white/3 rounded-full blur-2xl"></div>
      </div>

      <div class="container mx-auto px-4 py-16 md:py-24 lg:py-32 relative z-10">
        <!-- Logo/Brand Section -->
        <div class="text-center mb-8 md:mb-12">
          <div class="inline-block mb-4 px-6 py-2 bg-white/10 backdrop-blur-sm rounded-full border border-white/20">
            <p class="text-sm md:text-base font-medium flex items-center gap-2 justify-center">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z"/>
              </svg>
              A Project by <a href="https://somogrobangladesh.com/" target="_blank" rel="noopener" class="underline hover:text-white/80 transition-colors">Somogro Bangladesh</a>
            </p>
          </div>
        </div>

        <!-- Main Heading -->
        <div class="text-center mb-10 md:mb-14">
          <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold mb-4 md:mb-6 tracking-tight">
            <span class="inline-block animate-fade-in">Find Your</span>
            <span class="block bg-gradient-to-r from-white via-gray-100 to-white bg-clip-text text-transparent animate-fade-in-delay">Perfect Photographer</span>
          </h1>
          <p class="text-lg md:text-2xl text-gray-100 max-w-3xl mx-auto leading-relaxed animate-fade-in-delay-2">
            Connect with <span class="font-semibold text-white">{{ totalPhotographers }}+ verified professional photographers</span> across Bangladesh.<br class="hidden md:block" />
            From weddings to portraits, events to commercial shoots.
          </p>
        </div>

        <!-- Search Bar -->
        <div class="max-w-3xl mx-auto animate-fade-in-delay-3">
          <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-3 shadow-2xl">
            <div class="flex flex-col sm:flex-row gap-3">
              <div class="flex-1 relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                  v-model="searchQuery"
                  @keyup.enter="search"
                  type="text"
                  placeholder="Search by name, specialty, or location..."
                  class="w-full pl-12 pr-4 py-4 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all bg-white/95"
                />
              </div>
              <button
                @click="search"
                class="group px-8 py-4 bg-gradient-to-r from-burgundy to-[#6F112D] hover:from-[#6F112D] hover:to-burgundy text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 whitespace-nowrap"
              >
                <span class="flex items-center gap-2">
                  Search
                  <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                  </svg>
                </span>
              </button>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="grid grid-cols-3 gap-4 md:gap-8 mt-8 md:mt-12">
            <div class="text-center">
              <div class="text-3xl md:text-5xl font-bold mb-1 md:mb-2">
                <span v-if="totalPhotographers === '...'">
                  <span class="inline-block animate-pulse">...</span>
                </span>
                <span v-else>{{ totalPhotographers }}+</span>
              </div>
              <div class="text-sm md:text-base text-gray-200">Photographers</div>
            </div>
            <div class="text-center border-x border-white/20">
              <div class="text-3xl md:text-5xl font-bold mb-1 md:mb-2">64</div>
              <div class="text-sm md:text-base text-gray-200">Districts</div>
            </div>
            <div class="text-center">
              <div class="text-3xl md:text-5xl font-bold mb-1 md:mb-2">10K+</div>
              <div class="text-sm md:text-base text-gray-200">Happy Clients</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Wave Separator -->
      <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
          <path d="M0 0L60 10C120 20 240 40 360 46.7C480 53 600 47 720 43.3C840 40 960 40 1080 46.7C1200 53 1320 67 1380 73.3L1440 80V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V0Z" fill="#f9fafb"/>
        </svg>
      </div>
    </section>

    <!-- Smart Filters Section -->
    <section class="bg-white border-b sticky top-0 z-40 shadow-sm">
      <div class="container mx-auto px-4 py-4 md:py-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
          <div class="relative">
            <label class="block text-sm font-semibold mb-2 text-gray-700">Category</label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
              </svg>
              <select v-model="filters.category" class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-burgundy focus:ring-2 focus:ring-burgundy/20 transition-all appearance-none bg-white cursor-pointer hover:border-gray-300">
                <option value="">All Categories</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.slug">
                  {{ cat.name }}
                </option>
              </select>
              <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </div>
          
          <div class="relative">
            <label class="block text-sm font-semibold mb-2 text-gray-700">City</label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              <select v-model="filters.city" class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-burgundy focus:ring-2 focus:ring-burgundy/20 transition-all appearance-none bg-white cursor-pointer hover:border-gray-300">
                <option value="">All Cities</option>
                <option v-for="city in cities" :key="city.id" :value="city.slug">
                  {{ city.name }}
                </option>
              </select>
              <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </div>
          
          <div class="relative">
            <label class="block text-sm font-semibold mb-2 text-gray-700">Rating</label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-yellow-400 pointer-events-none" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
              <select v-model="filters.rating" class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-burgundy focus:ring-2 focus:ring-burgundy/20 transition-all appearance-none bg-white cursor-pointer hover:border-gray-300">
                <option value="">All Ratings</option>
                <option value="5">⭐⭐⭐⭐⭐ 5 Stars</option>
                <option value="4">⭐⭐⭐⭐ 4+ Stars</option>
                <option value="3">⭐⭐⭐ 3+ Stars</option>
              </select>
              <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </div>
          
          <div class="relative">
            <label class="block text-sm font-semibold mb-2 text-gray-700">Sort By</label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/>
              </svg>
              <select v-model="filters.sort" class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-burgundy focus:ring-2 focus:ring-burgundy/20 transition-all appearance-none bg-white cursor-pointer hover:border-gray-300">
                <option value="relevance">Most Relevant</option>
                <option value="rating">Highest Rated</option>
                <option value="newest">Newest First</option>
              </select>
              <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Photographers Grid -->
    <section class="py-12 md:py-16 lg:py-20">
      <div class="container mx-auto px-4">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-20">
          <div class="inline-block animate-spin rounded-full h-16 w-16 border-b-4 border-burgundy mb-4"></div>
          <p class="text-gray-600 text-lg">Finding the best photographers for you...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="photographers.length === 0" class="text-center py-20">
          <div class="inline-block p-6 bg-gray-100 rounded-full mb-6">
            <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-2">No photographers found</h3>
          <p class="text-gray-600 mb-6">
            <span v-if="searchQuery || filters.category || filters.city || filters.rating">
              Try adjusting your filters or search terms
            </span>
            <span v-else>
              We're building our photographer community. Check back soon!
            </span>
          </p>
          <button v-if="searchQuery || filters.category || filters.city || filters.rating" @click="resetFilters" class="px-6 py-3 bg-burgundy text-white rounded-xl hover:bg-[#6F112D] transition-colors">
            Reset Filters
          </button>
        </div>

        <!-- Photographers Grid -->
        <div v-else>
          <!-- Results Header -->
          <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
              <span v-if="searchQuery || filters.category || filters.city || filters.rating">
                {{ photographers.length }} Photographer{{ photographers.length !== 1 ? 's' : '' }} Found
              </span>
              <span v-else>
                Featured Photographers
              </span>
            </h2>
            <div class="flex items-center gap-2 text-sm text-gray-600">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              Verified Professionals
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 lg:gap-8">
            <div
              v-for="photographer in photographers"
              :key="photographer.id"
              class="group bg-white rounded-2xl shadow-sm hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 cursor-pointer transform hover:-translate-y-2"
              @click="viewPhotographer(photographer)"
            >
              <!-- Image Container -->
              <div class="relative h-56 overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200">
                <img
                  :src="photographer.avatar"
                  :alt="photographer.name"
                  class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                  loading="lazy"
                />
                <!-- Overlay on Hover -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                  <div class="absolute bottom-4 left-4 right-4">
                    <p class="text-white text-sm font-medium truncate">View Portfolio →</p>
                  </div>
                </div>
                <!-- Verified Badge -->
                <div class="absolute top-4 right-4 bg-white/95 backdrop-blur-sm px-3 py-1 rounded-full flex items-center gap-1 shadow-lg">
                  <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  <span class="text-xs font-semibold text-gray-700">Verified</span>
                </div>
              </div>

              <!-- Content -->
              <div class="p-5">
                <h3 class="font-bold text-xl text-gray-900 mb-2 truncate group-hover:text-burgundy transition-colors">
                  {{ photographer.user?.name || photographer.business_name || 'Unknown' }}
                </h3>
                
                <!-- Rating -->
                <div class="flex items-center gap-2 mb-3">
                  <div class="flex items-center">
                    <svg v-for="i in 5" :key="i" :class="i <= Math.round(Number(photographer.average_rating) || 0) ? 'text-yellow-400' : 'text-gray-300'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                  </div>
                  <span class="text-sm font-semibold text-gray-700">{{ (Number(photographer.average_rating) || 0).toFixed(1) }}</span>
                  <span class="text-sm text-gray-500">({{ photographer.rating_count || 0 }} reviews)</span>
                </div>
                
                <!-- Bio -->
                <p class="text-sm text-gray-600 mb-4 line-clamp-2 leading-relaxed">
                  {{ photographer.bio || 'Professional photographer with years of experience' }}
                </p>
                
                <!-- Action Button -->
                <button
                  @click.stop="bookPhotographer(photographer)"
                  class="w-full bg-gradient-to-r from-burgundy to-[#6F112D] hover:from-[#6F112D] hover:to-burgundy text-white py-3 rounded-xl font-semibold transition-all duration-300 transform group-hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  Book Now
                </button>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="flex justify-center items-center gap-2 mt-16">
            <button
              v-if="currentPage > 1"
              @click="previousPage"
              class="px-6 py-3 border-2 border-gray-200 rounded-xl hover:border-burgundy hover:text-burgundy transition-all font-medium flex items-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              Previous
            </button>
            
            <div class="flex items-center gap-2">
              <span v-for="page in visiblePages" :key="page" 
                @click="goToPage(page)"
                :class="page === currentPage ? 'bg-burgundy text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                class="w-12 h-12 flex items-center justify-center rounded-xl cursor-pointer transition-all font-semibold border-2 border-gray-200">
                {{ page }}
              </span>
            </div>
            
            <button
              v-if="currentPage < totalPages"
              @click="nextPage"
              class="px-6 py-3 border-2 border-gray-200 rounded-xl hover:border-burgundy hover:text-burgundy transition-all font-medium flex items-center gap-2"
            >
              Next
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

// Watch filters for changes
watch(filters, () => {
  currentPage.value = 1;
  fetchPhotographers();
}, { deep: true });

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
</style>
