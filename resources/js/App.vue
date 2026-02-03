<template>
  <div id="app" class="bg-gray-50 min-h-screen">
    <MetaTags />
    <!-- Marketplace Navigation (Hidden in Admin Area) -->
    <nav v-if="!isAdminRoute" class="sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm overflow-x-hidden">
      <div class="container mx-auto px-4 md:px-6 py-3">
        <div class="flex justify-between items-center">
          <!-- Logo -->
          <router-link to="/" class="flex items-center group">
            <img src="/images/logo.svg" alt="Photographers - Across Somagro Bangladesh" class="h-8 md:h-12 w-auto" />
          </router-link>

          <!-- Mobile Menu Button -->
          <button 
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <!-- Desktop Navigation Links -->
          <div class="hidden md:flex gap-1 items-center">
            <router-link
              v-for="link in navLinks"
              :key="link.path"
              :to="link.path"
              class="flex flex-col items-center gap-1 px-4 py-2 rounded-lg text-gray-600 hover:text-burgundy hover:bg-gray-50 transition-colors"
              active-class="text-burgundy bg-burgundy-50"
            >
              <component :is="link.icon" class="w-5 h-5" />
              <span class="text-xs font-medium">{{ link.name }}</span>
            </router-link>

            <!-- User Menu -->
            <div v-if="user" class="flex gap-2 items-center ml-4 pl-4 border-l border-gray-200">
              <!-- Notifications -->
              <router-link
                to="/notifications"
                class="relative flex flex-col items-center gap-1 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors"
              >
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="text-xs text-gray-600">Alerts</span>
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
              </router-link>

              <!-- Transactions -->
              <router-link
                to="/transactions"
                class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors"
              >
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span class="text-xs text-gray-600">Wallet</span>
              </router-link>

              <!-- Verification Center -->
              <router-link
                v-if="isPhotographer"
                to="/verification"
                class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors"
              >
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs text-gray-600">Verify</span>
              </router-link>

              <!-- User Avatar -->
              <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-burgundy-50 border border-burgundy-100">
                <div class="w-8 h-8 rounded-full bg-burgundy flex items-center justify-center text-white text-sm font-bold">
                  {{ user.name.charAt(0).toUpperCase() }}
                </div>
                <span class="text-sm font-medium text-gray-900">{{ user.name }}</span>
              </div>

              <!-- Dashboard Links -->
              <router-link
                v-if="isJudge"
                to="/judge/dashboard"
                class="flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                Judge Panel
              </router-link>
              <router-link
                v-if="isPhotographer"
                to="/dashboard"
                class="flex items-center gap-2 px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors font-medium"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                Dashboard
              </router-link>
              <router-link
                v-if="isAdmin"
                to="/admin/dashboard"
                class="flex items-center gap-2 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors font-medium"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Admin
              </router-link>

              <!-- Logout Button -->
              <button
                @click="logout"
                class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg hover:bg-red-50 text-gray-600 hover:text-red-600 transition-colors"
                title="Logout"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="text-xs">Logout</span>
              </button>
            </div>

            <!-- Login Button (Only show when NOT logged in) -->
            <router-link
              v-if="!user"
              to="/auth"
              class="flex items-center gap-2 px-5 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors font-medium ml-4"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
              </svg>
              Login
            </router-link>
          </div>
        </div>

        <!-- Mobile Menu -->
        <div v-if="mobileMenuOpen" class="md:hidden mt-4 pb-4 space-y-2 border-t pt-4">
          <router-link
            v-for="link in navLinks"
            :key="link.path"
            :to="link.path"
            @click="mobileMenuOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors"
            active-class="bg-burgundy-50 text-burgundy"
          >
            <component :is="link.icon" class="w-5 h-5" />
            <span class="font-medium">{{ link.name }}</span>
          </router-link>

          <div v-if="user" class="space-y-2 border-t pt-4 mt-4">
            <!-- User Info Toggle -->
            <button
              class="w-full flex items-center justify-between gap-3 px-4 py-3 bg-burgundy-50 rounded-lg"
              @click="mobileUserMenuOpen = !mobileUserMenuOpen"
            >
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-burgundy flex items-center justify-center text-white font-bold">
                  {{ user.name.charAt(0).toUpperCase() }}
                </div>
                <div class="text-left">
                  <p class="font-medium text-gray-900">{{ user.name }}</p>
                  <p class="text-xs text-gray-600">{{ user.email }}</p>
                </div>
              </div>
              <svg
                class="w-5 h-5 text-gray-600 transition-transform"
                :class="mobileUserMenuOpen ? 'rotate-180' : ''"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Mobile User Links -->
            <div v-if="mobileUserMenuOpen" class="space-y-2">
              <router-link
                to="/notifications"
                @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="font-medium">Notifications</span>
              </router-link>

              <router-link
                to="/transactions"
                @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span class="font-medium">Transactions</span>
              </router-link>

              <router-link
                v-if="isPhotographer"
                to="/verification"
                @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">Verification</span>
              </router-link>

              <router-link
                v-if="isPhotographer"
                to="/dashboard"
                @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 rounded-lg bg-burgundy text-white font-medium"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span>Dashboard</span>
              </router-link>

              <router-link
                v-if="isAdmin"
                to="/admin/dashboard"
                @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 rounded-lg bg-orange-500 text-white font-medium"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Admin Panel</span>
              </router-link>

              <button
                @click="logout"
                class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-red-50 text-red-600 transition-colors font-medium w-full text-left"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Logout</span>
              </button>
            </div>
          </div>

          <!-- Mobile Login Button (Only show when NOT logged in) -->
          <router-link
            v-if="!user"
            to="/auth"
            @click="mobileMenuOpen = false"
            class="flex items-center justify-center gap-2 px-4 py-3 bg-burgundy text-white rounded-lg font-medium"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
            </svg>
            Login / Register
          </router-link>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="animate-fade-in pb-20 md:pb-0">
      <router-view />
    </main>

    <!-- Cookie Consent Banner -->
    <CookieConsent />

    <!-- Mobile Bottom Navigation -->
    <MobileBottomNav
      v-if="!isAdminRoute"
      :isLoggedIn="!!user"
      :userRole="user?.role"
      :unreadNotifications="0"
      :activeCompetitionsCount="0"
    />

    <!-- Marketplace Footer -->
    <footer class="bg-gray-900 text-white mt-16 sm:mt-20 mb-20 md:mb-0 overflow-x-hidden">
      <div class="container mx-auto px-4 md:px-6 py-8 md:py-12">
        <!-- Mobile: Compact Layout -->
        <div class="md:hidden space-y-5">
          <!-- Logo & Social -->
          <div class="text-center">
            <img src="/images/logo-white.svg" alt="Photographers - Across Somagro Bangladesh" class="h-10 w-auto mx-auto mb-3" />
            <p class="text-gray-400 text-sm mb-4">
              Bangladesh's Photography Marketplace
            </p>
            <div class="flex gap-2 justify-center">
              <a href="https://www.facebook.com/thephotographersbd" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-lg bg-gray-800 hover:bg-blue-600 flex items-center justify-center transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
              </a>
              <a href="https://www.instagram.com/thephotographersbd" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-lg bg-gray-800 hover:bg-pink-600 flex items-center justify-center transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
              </a>
              <a href="https://wa.me/8801767300900" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-lg bg-gray-800 hover:bg-green-500 flex items-center justify-center transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
              </a>
            </div>
          </div>

          <!-- Quick Links Grid - Mobile Optimized -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-center">
            <router-link to="/" class="text-gray-400 hover:text-white text-sm py-2 rounded transition-colors">Find Photographers</router-link>
            <router-link to="/events" class="text-gray-400 hover:text-white text-sm py-2 rounded transition-colors">Events</router-link>
            <router-link to="/competitions" class="text-gray-400 hover:text-white text-sm py-2 rounded transition-colors">Competitions</router-link>
            <router-link to="/auth" class="text-gray-400 hover:text-white text-sm py-2 rounded transition-colors">Join Us</router-link>
            <router-link to="/verification" class="text-gray-400 hover:text-white text-sm py-2 rounded transition-colors">Verification</router-link>
            <router-link to="/help" class="text-gray-400 hover:text-white text-sm py-2 rounded transition-colors">Help</router-link>
            <router-link to="/contact" class="text-gray-400 hover:text-white text-sm py-2 rounded transition-colors">Contact</router-link>
          </div>

          <!-- Legal Links -->
          <div class="flex flex-wrap justify-center gap-4 text-xs text-gray-400 border-t border-gray-800 pt-6">
            <router-link to="/privacy" class="hover:text-white transition-colors">Privacy</router-link>
            <span>•</span>
            <router-link to="/terms" class="hover:text-white transition-colors">Terms</router-link>
            <span>•</span>
            <router-link to="/about" class="hover:text-white transition-colors">About</router-link>
          </div>

          <p class="text-center text-gray-500 text-xs">
            &copy; 2026 Photographer SB
          </p>
        </div>

        <!-- Desktop: Original 4-Column Layout -->
        <div class="hidden md:block">
          <div class="grid grid-cols-4 gap-8 mb-8">
            <!-- About Column -->
            <div>
              <div class="mb-4">
                <img src="/images/logo-white.svg" alt="Photographers - Across Somagro Bangladesh" class="h-10 w-auto" />
              </div>
              <p class="text-gray-400 text-sm leading-relaxed mb-4">
                Connecting Bangladesh's finest photographers with clients nationwide.
              </p>
              <div class="flex gap-2">
                <a href="https://www.facebook.com/thephotographersbd" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-blue-600 flex items-center justify-center transition-colors" title="Follow us on Facebook">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a href="https://www.instagram.com/thephotographersbd" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-pink-600 flex items-center justify-center transition-colors" title="Follow us on Instagram">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </a>
                <a href="https://wa.me/8801767300900" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-green-500 flex items-center justify-center transition-colors" title="Chat on WhatsApp">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                </a>
              </div>
            </div>

            <!-- Quick Links -->
            <div>
              <h4 class="text-base font-bold mb-4">Quick Links</h4>
              <ul class="space-y-2">
                <li><router-link to="/about" class="text-gray-400 hover:text-white text-sm transition-colors">About Us</router-link></li>
                <li><router-link to="/how-it-works" class="text-gray-400 hover:text-white text-sm transition-colors">How It Works</router-link></li>
                <li><router-link to="/about" class="text-gray-400 hover:text-white text-sm transition-colors">Pricing</router-link></li>
                <li><router-link to="/events" class="text-gray-400 hover:text-white text-sm transition-colors">Blog</router-link></li>
              </ul>
            </div>

            <!-- Services -->
            <div>
              <h4 class="text-base font-bold mb-4">Services</h4>
              <ul class="space-y-2">
                <li><router-link to="/" class="text-gray-400 hover:text-white text-sm transition-colors">Find Photographers</router-link></li>
                <li><router-link to="/events" class="text-gray-400 hover:text-white text-sm transition-colors">Events</router-link></li>
                <li><router-link to="/competitions" class="text-gray-400 hover:text-white text-sm transition-colors">Competitions</router-link></li>
                <li><router-link to="/auth" class="text-gray-400 hover:text-white text-sm transition-colors">Join as Photographer</router-link></li>
                <li><router-link to="/become-sponsor" class="text-gray-400 hover:text-white text-sm transition-colors">Become a Sponsor</router-link></li>
              </ul>
            </div>

            <!-- Support -->
            <div>
              <h4 class="text-base font-bold mb-4">Support</h4>
              <ul class="space-y-2">
                <li><router-link to="/help" class="text-gray-400 hover:text-white text-sm transition-colors">Help Center</router-link></li>
                <li><router-link to="/contact" class="text-gray-400 hover:text-white text-sm transition-colors">Contact Us</router-link></li>
                <li><router-link to="/privacy" class="text-gray-400 hover:text-white text-sm transition-colors">Privacy Policy</router-link></li>
                <li><router-link to="/terms" class="text-gray-400 hover:text-white text-sm transition-colors">Terms of Service</router-link></li>
              </ul>
            </div>
          </div>

          <!-- Bottom Bar -->
          <div class="pt-6 border-t border-gray-800">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
              <p class="text-gray-400 text-sm">
                &copy; 2026 Photographer SB. All rights reserved.
              </p>
              <div class="flex gap-6 text-sm text-gray-400">
                <router-link to="/privacy" class="hover:text-white transition-colors">Privacy</router-link>
                <router-link to="/terms" class="hover:text-white transition-colors">Terms</router-link>
                <router-link to="/privacy" class="hover:text-white transition-colors">Cookies</router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, h } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from './api'
import MobileBottomNav from './components/ui/MobileBottomNav.vue'
import CookieConsent from './components/CookieConsent.vue'
import MetaTags from './components/MetaTags.vue'

const router = useRouter()
const route = useRoute()
const user = ref(null)
const mobileMenuOpen = ref(false)
const mobileUserMenuOpen = ref(false)

// Icon components as inline SVGs
const HomeIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' })
])

const CalendarIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' })
])

const TrophyIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' })
])

const navLinks = [
  { name: 'Home', path: '/', icon: HomeIcon },
  { name: 'Events', path: '/events', icon: CalendarIcon },
  { name: 'Competitions', path: '/competitions', icon: TrophyIcon },
]

const isAdmin = computed(() => {
  return user.value && ['admin', 'super_admin'].includes(user.value.role)
})

const isPhotographer = computed(() => {
  return user.value && ['photographer', 'studio_owner'].includes(user.value.role)
})

const isJudge = computed(() => {
  return user.value && (user.value.role === 'judge' || user.value.is_judge === true)
})

const isAdminRoute = computed(() => {
  return route.path.startsWith('/admin')
})

const logout = async () => {
  try {
    await api.post('/auth/logout')
  } catch (error) {
    console.error('Logout error:', error)
  } finally {
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user')
    user.value = null
    router.push('/')
  }
}

onMounted(() => {
  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    user.value = JSON.parse(storedUser)
  }
})

watch(mobileMenuOpen, (isOpen) => {
  if (!isOpen) {
    mobileUserMenuOpen.value = false
  }
})
</script>

<style scoped>
#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

main {
  flex: 1;
}
</style>
