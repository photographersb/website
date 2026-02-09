<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 pt-8 sm:pt-10 lg:pt-12 pb-24 md:pb-12 px-3 sm:px-4">
    <div class="max-w-6xl mx-auto">
      <!-- Hero Section -->
      <div class="mb-12 sm:mb-16">
        <div class="flex items-start sm:items-center gap-3 sm:gap-4 mb-4">
          <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-gradient-to-br from-burgundy-500 to-burgundy-700 flex items-center justify-center shadow-xl">
            <svg
              class="w-8 h-8 text-white"
              fill="currentColor"
              viewBox="0 0 24 24"
            >
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
            </svg>
          </div>
          <div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 flex items-center gap-3">
              ✨ Be Featured
            </h1>
            <p class="text-sm sm:text-base lg:text-lg text-burgundy-600 font-semibold mt-1">
              Boost your visibility and get more bookings
            </p>
          </div>
        </div>
        <div class="h-1 w-24 bg-gradient-to-r from-burgundy-500 to-burgundy-400 rounded-full" />
      </div>

      <!-- Intro Box -->
      <div class="mb-12 bg-gradient-to-r from-burgundy-50 to-rose-50 border-2 border-burgundy-300 rounded-2xl p-6 sm:p-8">
        <p class="text-base text-gray-800 leading-relaxed mb-4">
          Stand out from the competition! Our featured listings give your profile premium visibility across the platform. Whether you're looking to showcase your work on the homepage, reach photographers in your category, or dominate your local market, we have the perfect plan for you.
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="flex items-start gap-3 p-4 bg-white rounded-lg border border-burgundy-200">
            <svg
              class="w-6 h-6 text-burgundy-600 flex-shrink-0 mt-0.5"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"
              />
            </svg>
            <div>
              <p class="font-bold text-gray-900">
                Increased Visibility
              </p>
              <p class="text-sm text-gray-600">
                Get featured on homepage & category pages
              </p>
            </div>
          </div>
          <div class="flex items-start gap-3 p-4 bg-white rounded-lg border border-burgundy-200">
            <svg
              class="w-6 h-6 text-burgundy-600 flex-shrink-0 mt-0.5"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"
              />
            </svg>
            <div>
              <p class="font-bold text-gray-900">
                More Bookings
              </p>
              <p class="text-sm text-gray-600">
                Attract qualified clients actively searching
              </p>
            </div>
          </div>
          <div class="flex items-start gap-3 p-4 bg-white rounded-lg border border-burgundy-200">
            <svg
              class="w-6 h-6 text-burgundy-600 flex-shrink-0 mt-0.5"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"
              />
            </svg>
            <div>
              <p class="font-bold text-gray-900">
                Priority Support
              </p>
              <p class="text-sm text-gray-600">
                Dedicated support for featured members
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Monthly Package Section -->
      <div
        id="packages"
        class="mb-12"
      >
        <div class="flex items-center gap-3 mb-6 sm:mb-8">
          <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br from-burgundy-100 to-burgundy-200 flex items-center justify-center">
            <svg
              class="w-6 h-6 text-burgundy-700"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path d="M8.16 2.75a.75.75 0 00-.75.75v.256a4.75 4.75 0 00-1.446 9.167.75.75 0 101.416.464 3.25 3.25 0 110.989-2.958H8a.75.75 0 000-1.5h-2.5a.75.75 0 00-.75.75v2.5a.75.75 0 001.5 0v-1.256a4.75 4.75 0 004.75-4.75V3.5a.75.75 0 00-.75-.75z" />
            </svg>
          </div>
          <h2 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-gray-900 to-burgundy-700 bg-clip-text text-transparent">
            Monthly Packages
          </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
          <!-- Starter Package -->
          <div class="bg-white rounded-2xl shadow-lg overflow-hidden border-2 border-gray-200 hover:border-burgundy-500 transition-all hover:shadow-xl hover:scale-105">
            <div class="p-6 sm:p-8">
              <div class="inline-block px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-bold mb-4">
                Starter
              </div>
              <p class="text-gray-600 mb-6">
                Perfect for building your portfolio
              </p>
              
              <div class="mb-8">
                <span class="text-4xl font-bold text-gray-900">৳999</span>
                <span class="text-gray-600">/month</span>
              </div>

              <ul class="space-y-4 mb-8">
                <li class="flex items-start gap-3">
                  <svg
                    class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-gray-700">Featured in category page</span>
                </li>
                <li class="flex items-start gap-3">
                  <svg
                    class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-gray-700">Profile badge & priority in search</span>
                </li>
                <li class="flex items-start gap-3">
                  <svg
                    class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-gray-700">Email notifications of inquiries</span>
                </li>
                <li class="flex items-start gap-3">
                  <svg
                    class="w-5 h-5 text-gray-300 flex-shrink-0 mt-0.5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-gray-400">Homepage featured listing</span>
                </li>
              </ul>

              <button
                class="w-full px-6 py-3 bg-gray-200 text-gray-700 font-bold rounded-lg hover:bg-gray-300 transition-all"
                @click="contactWhatsApp('Starter', 999)"
              >
                Choose Plan
              </button>
            </div>
          </div>

          <!-- Professional Package (Recommended) -->
          <div class="bg-gradient-to-br from-burgundy-50 to-rose-50 rounded-2xl shadow-2xl overflow-hidden border-3 border-burgundy-500 relative">
            <div class="absolute top-0 right-0 bg-gradient-to-r from-burgundy-600 to-burgundy-700 text-white px-4 py-2 text-xs font-bold rounded-bl-lg">
              RECOMMENDED
            </div>
            <div class="p-6 sm:p-8">
              <div class="inline-block px-4 py-2 bg-burgundy-100 text-burgundy-700 rounded-full text-sm font-bold mb-4">
                Professional
              </div>
              <p class="text-gray-600 mb-6">
                Most popular choice for active photographers
              </p>
              
              <div class="mb-8">
                <span class="text-4xl font-bold text-burgundy-700">৳2,499</span>
                <span class="text-gray-600">/month</span>
              </div>

              <ul class="space-y-4 mb-8">
                <li class="flex items-start gap-3">
                  <svg
                    class="w-5 h-5 text-burgundy-600 flex-shrink-0 mt-0.5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-gray-700 font-semibold">Featured in category & location pages</span>
                </li>
                <li class="flex items-start gap-3">
                  <svg
                    class="w-5 h-5 text-burgundy-600 flex-shrink-0 mt-0.5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-gray-700 font-semibold">Premium profile badge</span>
                </li>
                <li class="flex items-start gap-3">
                  <svg
                    class="w-5 h-5 text-burgundy-600 flex-shrink-0 mt-0.5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-gray-700 font-semibold">Priority in all search results</span>
                </li>
                <li class="flex items-start gap-3">
                  <svg
                    class="w-5 h-5 text-burgundy-600 flex-shrink-0 mt-0.5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-gray-700 font-semibold">Homepage featured listing</span>
                </li>
                <li class="flex items-start gap-3">
                  <svg
                    class="w-5 h-5 text-burgundy-600 flex-shrink-0 mt-0.5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-gray-700 font-semibold">Dedicated support</span>
                </li>
              </ul>

              <button
                class="w-full px-6 py-3 bg-gradient-to-r from-burgundy-600 to-burgundy-700 text-white font-bold rounded-lg hover:from-burgundy-700 hover:to-burgundy-800 transition-all shadow-lg"
                @click="contactWhatsApp('Professional', 2499)"
              >
                Choose Plan
              </button>
            </div>
          </div>

          <!-- Enterprise Package -->
          <div class="bg-white rounded-2xl shadow-lg overflow-hidden border-2 border-gray-200 hover:border-burgundy-500 transition-all hover:shadow-xl hover:scale-105">
            <div class="p-6 sm:p-8">
              <div class="inline-block px-4 py-2 bg-yellow-100 text-yellow-700 rounded-full text-sm font-bold mb-4">
                Enterprise
              </div>
              <p class="text-gray-600 mb-6">
                Maximum visibility and control
              </p>
              
              <div class="mb-8">
                <span class="text-4xl font-bold text-gray-900">৳5,999</span>
                <span class="text-gray-600">/month</span>
              </div>

              <ul class="space-y-4 mb-8">
                <li class="flex items-start gap-3">
                  <svg
                    class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-gray-700">All Professional features</span>
                </li>
                <li class="flex items-start gap-3">
                  <svg
                    class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-gray-700">Featured in homepage top slots</span>
                </li>
                <li class="flex items-start gap-3">
                  <svg
                    class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-gray-700">Exclusive platinum badge</span>
                </li>
                <li class="flex items-start gap-3">
                  <svg
                    class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-gray-700">Analytics & performance reports</span>
                </li>
                <li class="flex items-start gap-3">
                  <svg
                    class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-gray-700">24/7 priority support</span>
                </li>
              </ul>

              <button
                class="w-full px-6 py-3 bg-amber-500 text-white font-bold rounded-lg hover:bg-amber-600 transition-all"
                @click="contactWhatsApp('Enterprise', 5999)"
              >
                Choose Plan
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Category Wise Section -->
      <div class="mb-12">
        <div class="flex items-center gap-3 mb-6 sm:mb-8">
          <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br from-burgundy-100 to-burgundy-200 flex items-center justify-center">
            <svg
              class="w-6 h-6 text-burgundy-700"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path d="M5 3a2 2 0 00-2 2v6h6V5a2 2 0 00-2-2H5zM15 3a2 2 0 00-2 2v6h6V5a2 2 0 00-2-2h-2zM5 13a2 2 0 00-2 2v2h6v-2a2 2 0 00-2-2H5zM15 13a2 2 0 00-2 2v2h6v-2a2 2 0 00-2-2h-2z" />
            </svg>
          </div>
          <h2 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-gray-900 to-burgundy-700 bg-clip-text text-transparent">
            Featured by Category
          </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div
            v-for="category in categories"
            :key="category"
            class="p-6 bg-white rounded-xl border-2 border-gray-200 hover:border-burgundy-500 hover:shadow-lg transition-all cursor-pointer flex flex-col"
          >
            <div class="flex items-center justify-between mb-3">
              <span class="text-2xl">📷</span>
              <span class="inline-block px-3 py-1 bg-rose-100 text-rose-700 text-xs font-bold rounded-full">Featured</span>
            </div>
            <p class="font-bold text-gray-900 mb-2">
              {{ category }}
            </p>
            <p class="text-sm text-gray-600 mb-4 flex-grow">
              Get featured among top {{ category }} photographers
            </p>
            <button
              class="w-full px-4 py-2 bg-gradient-to-r from-burgundy-600 to-burgundy-700 text-white text-sm font-bold rounded-lg hover:from-burgundy-700 hover:to-burgundy-800 transition-all"
              @click="scrollToSection('packages')"
            >
              Learn More
            </button>
          </div>
        </div>
      </div>

      <!-- Location Wise Section -->
      <div class="mb-12">
        <div class="flex items-center gap-3 mb-6 sm:mb-8">
          <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br from-burgundy-100 to-burgundy-200 flex items-center justify-center">
            <svg
              class="w-6 h-6 text-burgundy-700"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <h2 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-gray-900 to-burgundy-700 bg-clip-text text-transparent">
            Featured by Location
          </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div
            v-for="location in locations"
            :key="location"
            class="p-6 bg-white rounded-xl border-2 border-gray-200 hover:border-burgundy-500 hover:shadow-lg transition-all cursor-pointer flex flex-col"
          >
            <div class="flex items-center justify-between mb-3">
              <span class="text-2xl">📍</span>
              <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Featured</span>
            </div>
            <p class="font-bold text-gray-900 mb-2">
              {{ location }}
            </p>
            <p class="text-sm text-gray-600 mb-4 flex-grow">
              Be the top choice for photographers in {{ location }}
            </p>
            <button
              class="w-full px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white text-sm font-bold rounded-lg hover:from-green-700 hover:to-green-800 transition-all"
              @click="scrollToSection('packages')"
            >
              Learn More
            </button>
          </div>
        </div>
      </div>

      <!-- Homepage Section -->
      <div class="mb-12">
        <div class="flex items-center gap-3 mb-6 sm:mb-8">
          <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br from-burgundy-100 to-burgundy-200 flex items-center justify-center">
            <svg
              class="w-6 h-6 text-burgundy-700"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
            </svg>
          </div>
          <h2 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-gray-900 to-burgundy-700 bg-clip-text text-transparent">
            Homepage Featured
          </h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
          <div class="bg-gradient-to-br from-burgundy-50 to-rose-50 border-2 border-burgundy-300 rounded-2xl p-6 sm:p-8">
            <div class="flex items-start gap-4 mb-4">
              <div class="w-12 h-12 rounded-full bg-burgundy-600 flex items-center justify-center text-white text-xl flex-shrink-0">
                ⭐
              </div>
              <div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                  Premium Homepage Spot
                </h3>
                <p class="text-gray-700 leading-relaxed">
                  Get featured in our homepage carousel and reach thousands of potential clients. Your portfolio will be displayed in the "Trending Photographers" section with dedicated visibility.
                </p>
              </div>
            </div>
            <ul class="space-y-3 ml-16">
              <li class="flex items-center gap-2">
                <svg
                  class="w-5 h-5 text-burgundy-600"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span class="text-gray-700">Rotated every 24 hours for fair visibility</span>
              </li>
              <li class="flex items-center gap-2">
                <svg
                  class="w-5 h-5 text-burgundy-600"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span class="text-gray-700">High-quality image showcase</span>
              </li>
              <li class="flex items-center gap-2">
                <svg
                  class="w-5 h-5 text-burgundy-600"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span class="text-gray-700">Direct link to your profile</span>
              </li>
            </ul>
          </div>

          <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border-2 border-yellow-300 rounded-2xl p-6 sm:p-8">
            <div class="flex items-start gap-4 mb-4">
              <div class="w-12 h-12 rounded-full bg-yellow-600 flex items-center justify-center text-white text-xl flex-shrink-0">
                🔥
              </div>
              <div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                  Trending Photographers Section
                </h3>
                <p class="text-gray-700 leading-relaxed">
                  Stand out in our "Trending" section where the hottest photographers are showcased. This section gets premium placement and attracts serious clients.
                </p>
              </div>
            </div>
            <ul class="space-y-3 ml-16">
              <li class="flex items-center gap-2">
                <svg
                  class="w-5 h-5 text-yellow-600"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span class="text-gray-700">Curated based on bookings & ratings</span>
              </li>
              <li class="flex items-center gap-2">
                <svg
                  class="w-5 h-5 text-yellow-600"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span class="text-gray-700">Increased click-through rates</span>
              </li>
              <li class="flex items-center gap-2">
                <svg
                  class="w-5 h-5 text-yellow-600"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span class="text-gray-700">Premium badge on profile</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- FAQ Section -->
      <div class="mb-12">
        <div class="flex items-center gap-3 mb-6 sm:mb-8">
          <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br from-burgundy-100 to-burgundy-200 flex items-center justify-center">
            <svg
              class="w-6 h-6 text-burgundy-700"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 7a1 1 0 11-2 0 1 1 0 012 0zm5-1a1 1 0 11-2 0 1 1 0 012 0z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <h2 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-gray-900 to-burgundy-700 bg-clip-text text-transparent">
            Frequently Asked Questions
          </h2>
        </div>

        <div class="space-y-4">
          <div
            v-for="(faq, index) in faqs"
            :key="index"
            class="bg-white rounded-lg border-2 border-gray-200 overflow-hidden hover:border-burgundy-500 transition-all"
          >
            <button
              class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors"
              @click="activeTab = activeTab === index ? null : index"
            >
              <span class="font-bold text-gray-900 text-left">{{ faq.q }}</span>
              <svg
                :class="['w-5 h-5 text-burgundy-600 transition-transform', activeTab === index ? 'rotate-180' : '']"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
            <transition name="slide">
              <div
                v-if="activeTab === index"
                class="px-6 py-4 bg-gray-50 border-t-2 border-gray-200"
              >
                <p class="text-gray-700">
                  {{ faq.a }}
                </p>
              </div>
            </transition>
          </div>
        </div>
      </div>

      <!-- CTA Section -->
      <div class="bg-gradient-to-r from-burgundy-600 to-burgundy-700 rounded-2xl p-8 sm:p-12 text-center text-white">
        <h2 class="text-3xl sm:text-4xl font-bold mb-4">
          Ready to Get Featured?
        </h2>
        <p class="text-burgundy-100 mb-8 text-lg max-w-2xl mx-auto">
          Choose your plan today and start getting more bookings. Featured photographers earn 3x more bookings on average.
        </p>
        <button class="px-8 py-4 bg-white text-burgundy-700 font-bold rounded-lg hover:bg-gray-100 transition-all shadow-lg hover:shadow-xl">
          View All Plans
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

import { formatNumber } from '@/utils/formatters'

const activeTab = ref(null)

const scrollToSection = (sectionId) => {
  const element = document.querySelector(`#${sectionId}`)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
}

const categories = [
  'Wedding Photography',
  'Portrait Photography',
  'Event Photography',
  'Commercial Photography',
  'Nature Photography',
  'Fashion Photography',
  'Product Photography',
  'Real Estate Photography'
]

const locations = [
  'Dhaka',
  'Chittagong',
  'Sylhet',
  'Khulna',
  'Rajshahi',
  'Barisal',
  'Rangpur',
  'Mymensingh'
]

const contactWhatsApp = (planName, price) => {
  const phoneNumber = '8801767300900'
  const formattedPrice = formatNumber(price)
  const message = `Hi, I'm interested in the ${planName} package (৳${formattedPrice}/month). Please provide more details.`
  const encodedMessage = encodeURIComponent(message)
  const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodedMessage}`
  window.open(whatsappUrl, '_blank')
}

const faqs = [
  {
    q: 'How long does the featured listing last?',
    a: 'Featured listings are active for one full month from the date of purchase. After the month ends, you can renew to maintain your featured status.'
  },
  {
    q: 'Can I cancel my subscription anytime?',
    a: 'Yes, you can cancel your subscription at any time. If you cancel before the month ends, your featured status will remain active until the subscription period ends.'
  },
  {
    q: 'What if I upgrade to a higher plan?',
    a: 'You can upgrade at any time. The difference in price will be prorated based on the remaining days of your current subscription.'
  },
  {
    q: 'How are featured photographers selected?',
    a: 'Featured photographers are selected based on their subscription plan. All subscriptions are treated equally within their tier, and rotation is based on fair scheduling algorithms.'
  },
  {
    q: 'Do I get analytics for my featured listing?',
    a: 'Analytics are available for the Professional and Enterprise plans. You can track views, clicks, and inquiries through your dashboard.'
  },
  {
    q: 'Can I feature multiple categories or locations?',
    a: 'Yes! You can purchase featured listings for multiple categories or locations simultaneously to maximize your reach.'
  }
]
</script>

<style scoped>
.slide-enter-active, .slide-leave-active {
  transition: all 0.3s ease;
}

.slide-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
