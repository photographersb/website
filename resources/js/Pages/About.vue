<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-burgundy via-[#8E0E3F] to-[#6F112D] text-white">
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white bg-opacity-5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2" />
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white bg-opacity-5 rounded-full blur-3xl translate-x-1/2 translate-y-1/2" />
      </div>

      <div class="container mx-auto px-4 py-16 md:py-24 relative z-10 text-center">
        <div class="inline-block mb-4 px-6 py-2 bg-white bg-opacity-10 backdrop-blur-sm rounded-full border border-white border-opacity-20">
          <p class="text-sm md:text-base font-medium flex items-center gap-2 justify-center">
            <svg
              class="w-5 h-5"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z" />
            </svg>
            A Project by <a
              href="https://somogrobangladesh.com/"
              target="_blank"
              rel="noopener"
              class="underline hover:text-white hover:text-opacity-80 transition-colors"
            >Somogro Bangladesh</a>
          </p>
        </div>
        <h1 class="text-4xl md:text-6xl font-bold mb-4 tracking-tight">
          About Photographer SB
        </h1>
        <p class="text-lg md:text-xl text-gray-100 max-w-3xl mx-auto">
          Bangladesh's premier photography marketplace
        </p>
      </div>
    </section>

    <div class="container mx-auto px-4 py-12 max-w-4xl">
      <!-- Story Section -->
      <div class="bg-white rounded-xl shadow-lg p-8 mb-8 border border-gray-100">
        <h2 class="text-2xl font-bold mb-4 text-burgundy">
          Our Story
        </h2>
        <p class="text-gray-700 mb-4">
          Photographer SB is Bangladesh's premier photography marketplace, connecting talented photographers with clients across the nation. Founded in 2025, we're committed to showcasing the incredible photography talent that Bangladesh has to offer.
        </p>
        <p class="text-gray-700 mb-4">
          Our platform serves as a bridge between professional photographers and those seeking their services, making it easier than ever to find the perfect photographer for any occasion - from weddings and events to portraits and commercial shoots.
        </p>
      </div>

      <!-- Mission & Vision -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-burgundy rounded-lg flex items-center justify-center">
              <svg
                class="w-6 h-6 text-white"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <h3 class="text-xl font-bold">
              Our Mission
            </h3>
          </div>
          <p class="text-gray-700">
            To empower photographers across Bangladesh by providing them with a platform to showcase their work, connect with clients, and grow their business while delivering exceptional service to our users.
          </p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-burgundy rounded-lg flex items-center justify-center">
              <svg
                class="w-6 h-6 text-white"
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
            <h3 class="text-xl font-bold">
              Our Vision
            </h3>
          </div>
          <p class="text-gray-700">
            To become the go-to platform for photography services in Bangladesh, recognized for quality, reliability, and innovation in connecting creative professionals with their ideal clients.
          </p>
        </div>
      </div>

      <!-- Why Choose Us -->
      <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
        <h2 class="text-2xl font-bold mb-6 text-burgundy">
          Why Choose Photographer SB?
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="flex gap-4">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-burgundy-50 rounded-lg flex items-center justify-center">
                <span class="text-2xl">✓</span>
              </div>
            </div>
            <div>
              <h4 class="font-bold mb-2">
                Verified Photographers
              </h4>
              <p class="text-sm text-gray-600">
                All photographers are carefully verified to ensure quality and professionalism.
              </p>
            </div>
          </div>

          <div class="flex gap-4">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-burgundy-50 rounded-lg flex items-center justify-center">
                <span class="text-2xl">⭐</span>
              </div>
            </div>
            <div>
              <h4 class="font-bold mb-2">
                Transparent Reviews
              </h4>
              <p class="text-sm text-gray-600">
                Genuine client reviews help you make informed decisions.
              </p>
            </div>
          </div>

          <div class="flex gap-4">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-burgundy-50 rounded-lg flex items-center justify-center">
                <span class="text-2xl">🔒</span>
              </div>
            </div>
            <div>
              <h4 class="font-bold mb-2">
                Secure Payments
              </h4>
              <p class="text-sm text-gray-600">
                Safe and secure payment processing with multiple options.
              </p>
            </div>
          </div>

          <div class="flex gap-4">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-burgundy-50 rounded-lg flex items-center justify-center">
                <span class="text-2xl">💬</span>
              </div>
            </div>
            <div>
              <h4 class="font-bold mb-2">
                Easy Communication
              </h4>
              <p class="text-sm text-gray-600">
                Direct messaging and inquiry system for seamless collaboration.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-burgundy text-white rounded-lg shadow-lg p-6 text-center">
          <div class="text-3xl font-bold mb-2">
            {{ stats.photographers }}+
          </div>
          <div class="text-sm">
            Photographers
          </div>
        </div>
        <div class="bg-burgundy text-white rounded-lg shadow-lg p-6 text-center">
          <div class="text-3xl font-bold mb-2">
            {{ formatNumber(stats.visitors) }}+
          </div>
          <div class="text-sm">
            Happy Clients
          </div>
        </div>
        <div class="bg-burgundy text-white rounded-lg shadow-lg p-6 text-center">
          <div class="text-3xl font-bold mb-2">
            {{ stats.cities }}+
          </div>
          <div class="text-sm">
            Cities Covered
          </div>
        </div>
        <div class="bg-burgundy text-white rounded-lg shadow-lg p-6 text-center">
          <div class="text-3xl font-bold mb-2">
            {{ stats.rating }}★
          </div>
          <div class="text-sm">
            Average Rating
          </div>
        </div>
      </div>

      <!-- Team Values -->
      <div class="bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold mb-6 text-burgundy">
          Our Values
        </h2>
        <div class="space-y-4">
          <div>
            <h4 class="font-bold mb-2">
              🎯 Quality First
            </h4>
            <p class="text-gray-700">
              We maintain high standards for both photographers and client service.
            </p>
          </div>
          <div>
            <h4 class="font-bold mb-2">
              🤝 Trust & Transparency
            </h4>
            <p class="text-gray-700">
              Building trust through transparent processes and honest communication.
            </p>
          </div>
          <div>
            <h4 class="font-bold mb-2">
              🚀 Innovation
            </h4>
            <p class="text-gray-700">
              Constantly improving our platform with new features and technologies.
            </p>
          </div>
          <div>
            <h4 class="font-bold mb-2">
              💚 Community Focus
            </h4>
            <p class="text-gray-700">
              Supporting the photography community and fostering creative growth.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/api';

// Stats data
const stats = ref({
  photographers: 0,
  cities: 0,
  visitors: 0,
  rating: 0
});

// Fetch platform stats
const fetchStats = async () => {
  try {
    const response = await api.get('/platform/stats');
    if (response.data.status === 'success') {
      stats.value = response.data.data;
    }
  } catch (error) {
    console.error('Failed to fetch platform stats:', error);
  }
};

// Format number with K/M suffix
const formatNumber = (num) => {
  if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
  if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
  return num;
};

// Load stats on mount
onMounted(() => {
  fetchStats();
});
</script>
