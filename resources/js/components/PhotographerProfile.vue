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
                :src="photographer.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(photographer.user?.name || 'User') + '&size=200&background=6c0b1a&color=fff'"
                :alt="photographer.user?.name || 'Photographer'"
                class="w-32 h-32 rounded-full mx-auto mb-4 object-cover border-4 border-burgundy shadow-lg"
              />
              <h1 class="text-2xl font-bold text-gray-900 mb-2">
                {{ photographer.user?.name || photographer.business_name || 'Unknown' }}
              </h1>
              
              <div class="flex items-center justify-center gap-2 mb-3">
                <div class="flex items-center gap-1">
                  <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                  </svg>
                  <span class="font-bold text-gray-900">{{ photographer.average_rating || '0.0' }}</span>
                  <span class="text-gray-600 text-sm">({{ photographer.rating_count || 0 }})</span>
                </div>
                <span v-if="photographer.is_verified" class="inline-flex items-center gap-1 bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  Verified
                </span>
              </div>

              <p v-if="photographer.bio" class="text-gray-600 text-sm mb-4 line-clamp-3">{{ photographer.bio }}</p>

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
                <button
                  @click="startBooking"
                  class="w-full bg-burgundy text-white px-6 py-3 rounded-lg font-semibold hover:bg-[#8B1538] transition shadow-md hover:shadow-lg"
                >
                  Book Now
                </button>
                <button
                  @click="writeReview"
                  class="w-full border-2 border-burgundy text-burgundy px-6 py-3 rounded-lg font-semibold hover:bg-burgundy hover:text-white transition"
                >
                  Write Review
                </button>
              </div>
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

          <!-- Portfolio -->
          <div v-if="albums && albums.length > 0" class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Portfolio</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
              <div
                v-for="album in albums"
                :key="album.id"
                class="group relative aspect-square rounded-lg overflow-hidden cursor-pointer hover:shadow-xl transition-all"
              >
                <img
                  :src="album.cover_photo_url || 'https://via.placeholder.com/400'"
                  :alt="album.name"
                  class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                />
              </div>
            </div>
          </div>

          <!-- Packages -->
          <div v-if="packages && packages.length > 0" class="bg-white rounded-lg shadow-lg p-6">
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

          <!-- Reviews -->
          <div v-if="reviews && reviews.length > 0" class="bg-white rounded-lg shadow-lg p-6">
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
                      {{ (review.reviewer?.name || 'A')[0].toUpperCase() }}
                    </div>
                    <div>
                      <p class="font-semibold text-gray-900">{{ review.reviewer?.name || 'Anonymous' }}</p>
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

          <!-- Empty State -->
          <div v-if="!albums?.length && !packages?.length && !reviews?.length" class="bg-white rounded-lg shadow-lg p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <p class="text-gray-500">No portfolio or packages available yet</p>
          </div>
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
const loading = ref(true);

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

const selectPackage = (pkg) => {
  router.push(`/booking/${photographer.value.id}?package=${pkg.id}`);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString();
};

onMounted(() => {
  fetchPhotographer();
});
</script>
