<template>
  <div
    id="app"
    :class="['min-h-screen flex flex-col', isAdminRoute ? 'admin-theme' : 'bg-gray-50']"
  >
    <MetaTags />

    <div
      v-if="errorReport"
      class="sb-error-banner"
      role="alert"
    >
      <div class="sb-error-banner__content">
        <div>
          <p class="sb-error-banner__title">Sorry, something went wrong.</p>
          <p class="sb-error-banner__message">We are on it. You can report this to our admin for a faster fix.</p>
          <p class="sb-error-banner__detail">{{ errorReport }}</p>
        </div>
        <div class="sb-error-banner__actions">
          <a
            :href="mailtoUrl"
            class="sb-error-banner__btn"
            target="_blank"
            rel="noopener"
          >Email Admin</a>
          <a
            :href="whatsAppUrl"
            class="sb-error-banner__btn sb-error-banner__btn--whatsapp"
            target="_blank"
            rel="noopener"
          >WhatsApp Admin</a>
          <button
            class="sb-error-banner__dismiss"
            type="button"
            @click="clearErrorReport"
          >Dismiss</button>
        </div>
      </div>
    </div>


    <!-- Marketplace Navigation (Hidden in Admin Area) -->
    <nav
      v-if="!isAdminRoute"
      class="sticky top-0 z-50 sb-nav"
    >
      <div class="sb-nav__bg" aria-hidden="true"></div>
      <div class="container mx-auto px-4 md:px-6 py-3 md:py-4 sb-nav__inner">
        <div class="flex items-center justify-between gap-3 md:gap-4 sb-nav__row">
          <router-link
            to="/"
            class="flex items-center"
          >
            <img
              src="/images/logo.svg"
              alt="Photographers - Across Somagro Bangladesh"
              class="h-8 md:h-12 w-auto"
            >
          </router-link>

          <button
            class="md:hidden p-2 rounded-lg sb-nav__icon-btn"
            @click="mobileMenuOpen = !mobileMenuOpen"
          >
            <svg
              v-if="!mobileMenuOpen"
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
            <svg
              v-else
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

          <div class="hidden md:flex items-center gap-1.5 xl:gap-2 sb-nav__links">
            <router-link
              v-for="link in navLinks"
              :key="link.path"
              :to="link.path"
              class="sb-nav__link"
              active-class="sb-nav__link--active"
            >
              <component
                :is="link.icon"
                class="w-5 h-5"
              />
              <span class="sb-nav__link-label">{{ link.name }}</span>
            </router-link>
          </div>

          <div class="hidden md:flex items-center gap-2 sb-nav__actions">
            <span class="sb-nav__divider" aria-hidden="true" />
            <div
              v-if="user"
              class="flex items-center gap-2"
            >
              <div class="sb-user-chip">
                <div class="sb-user-chip__avatar">
                  {{ user.name.charAt(0).toUpperCase() }}
                </div>
                <span class="sb-user-chip__name">{{ user.name }}</span>
              </div>

              <router-link
                v-if="isJudge"
                to="/judge/dashboard"
                class="sb-btn sb-btn--judge sb-btn--compact"
              >
                Judge Panel
              </router-link>
              <router-link
                v-if="isClient"
                to="/client/dashboard"
                class="sb-btn sb-btn--primary sb-btn--compact"
              >
                Client Dashboard
              </router-link>
              <router-link
                v-if="isPhotographer"
                to="/dashboard"
                class="sb-btn sb-btn--primary sb-btn--compact"
              >
                Dashboard
              </router-link>
              <router-link
                v-if="isAdmin"
                to="/admin/dashboard"
                class="sb-btn sb-btn--admin sb-btn--compact"
              >
                Admin Dashboard
              </router-link>

              <button
                class="sb-btn sb-btn--logout sb-btn--compact"
                title="Logout"
                @click="logout"
              >
                Logout
              </button>
            </div>

            <div
              v-else
              class="flex items-center gap-2"
            >
              <router-link
                to="/auth?tab=register"
                class="sb-btn sb-btn--ghost sb-btn--ghost-sm justify-center"
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
                    d="M16 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2m13-11a4 4 0 11-8 0 4 4 0 018 0zm5 3v4m2-2h-4"
                  />
                </svg>
                Register
              </router-link>
              <router-link
                to="/auth"
                class="sb-btn sb-btn--primary justify-center"
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
                    d="M12 11V7a4 4 0 00-8 0v4m-1 0h10a2 2 0 012 2v6a2 2 0 01-2 2H3a2 2 0 01-2-2v-6a2 2 0 012-2z"
                  />
                </svg>
                Login
              </router-link>
            </div>
          </div>
        </div>

        <div
          v-if="mobileMenuOpen"
          class="md:hidden mt-4 pb-4 space-y-2 border-t pt-4 sb-nav__panel"
        >
          <router-link
            v-for="link in navLinks"
            :key="link.path"
            :to="link.path"
            class="sb-nav__link sb-nav__link--mobile"
            active-class="sb-nav__link--active"
            @click="mobileMenuOpen = false"
          >
            <component
              :is="link.icon"
              class="w-5 h-5"
            />
            <span class="font-medium">{{ link.name }}</span>
          </router-link>

          <div
            v-if="user"
            class="space-y-2 border-t pt-4 mt-4"
          >
            <button
              class="w-full flex items-center justify-between gap-3 px-4 py-3 sb-user-chip"
              @click="mobileUserMenuOpen = !mobileUserMenuOpen"
            >
              <div class="flex items-center gap-3">
                <div class="sb-user-chip__avatar sb-user-chip__avatar--lg">
                  {{ user.name.charAt(0).toUpperCase() }}
                </div>
                <div class="text-left">
                  <p class="font-semibold text-gray-900">
                    {{ user.name }}
                  </p>
                  <p class="text-xs text-gray-600">
                    {{ user.email }}
                  </p>
                </div>
              </div>
              <svg
                class="w-5 h-5 text-gray-600 transition-transform"
                :class="mobileUserMenuOpen ? 'rotate-180' : ''"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7"
                />
              </svg>
            </button>

            <div
              v-if="mobileUserMenuOpen"
              class="space-y-2"
            >
              <router-link
                v-if="isJudge"
                to="/judge/dashboard"
                class="sb-btn sb-btn--judge w-full"
                @click="mobileMenuOpen = false"
              >
                Judge Panel
              </router-link>
              <router-link
                v-if="isClient"
                to="/client/dashboard"
                class="sb-btn sb-btn--primary w-full"
                @click="mobileMenuOpen = false"
              >
                Client Dashboard
              </router-link>
              <router-link
                v-if="isPhotographer"
                to="/dashboard"
                class="sb-btn sb-btn--primary w-full"
                @click="mobileMenuOpen = false"
              >
                Dashboard
              </router-link>
              <router-link
                v-if="isAdmin"
                to="/admin/dashboard"
                class="sb-btn sb-btn--admin w-full"
                @click="mobileMenuOpen = false"
              >
                Admin Dashboard
              </router-link>
              <button
                class="sb-btn sb-btn--logout w-full"
                @click="logout"
              >
                Logout
              </button>
            </div>
          </div>

          <div
            v-else
            class="space-y-2"
          >
            <router-link
              to="/auth?tab=register"
              class="sb-btn sb-btn--ghost sb-btn--ghost-sm w-full justify-center"
              @click="mobileMenuOpen = false"
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
                  d="M16 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2m13-11a4 4 0 11-8 0 4 4 0 018 0zm5 3v4m2-2h-4"
                />
              </svg>
              Register
            </router-link>
            <router-link
              to="/auth"
              class="sb-btn sb-btn--primary w-full justify-center"
              @click="mobileMenuOpen = false"
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
                  d="M12 11V7a4 4 0 00-8 0v4m-1 0h10a2 2 0 012 2v6a2 2 0 01-2 2H3a2 2 0 01-2-2v-6a2 2 0 012-2z"
                />
              </svg>
              Login
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main :class="['flex-1 animate-fade-in', !isAdminRoute ? 'public-main pb-20 md:pb-0' : '']">
      <router-view />
    </main>

    <!-- Cookie Consent Banner -->
    <CookieConsent />

    <!-- Mobile Bottom Navigation -->
    <MobileBottomNav
      v-if="!isAdminRoute"
      :is-logged-in="!!user"
      :user-role="user?.role"
      :unread-notifications="0"
      :active-competitions-count="0"
    />

    <!-- Marketplace Footer -->
    <footer class="bg-gray-900 text-white mt-auto pt-16 sm:pt-20 pb-20 md:pb-0 overflow-x-hidden">
      <div class="container mx-auto px-4 md:px-6 py-8 md:py-12">
        <!-- Mobile: Compact Layout -->
        <div class="md:hidden space-y-6">
          <!-- Logo & Social -->
          <div class="text-center">
            <img
              src="/images/logo-white.svg"
              alt="Photographers - Across Somagro Bangladesh"
              class="h-10 w-auto mx-auto mb-3"
            >
            <p class="text-gray-400 text-sm mb-4">
              Bangladesh's Photography Marketplace
            </p>
            <div class="flex gap-2 justify-center">
              <a
                href="https://www.facebook.com/thephotographersbd"
                target="_blank"
                rel="noopener noreferrer"
                class="w-10 h-10 rounded-lg bg-gray-800 hover:bg-blue-600 flex items-center justify-center transition-colors"
              >
                <svg
                  class="w-5 h-5"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                ><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" /></svg>
              </a>
              <a
                href="https://www.instagram.com/thephotographersbd"
                target="_blank"
                rel="noopener noreferrer"
                class="w-10 h-10 rounded-lg bg-gray-800 hover:bg-pink-600 flex items-center justify-center transition-colors"
              >
                <svg
                  class="w-5 h-5"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                ><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" /></svg>
              </a>
              <a
                href="https://wa.me/8801767300900"
                target="_blank"
                rel="noopener noreferrer"
                class="w-10 h-10 rounded-lg bg-gray-800 hover:bg-green-500 flex items-center justify-center transition-colors"
              >
                <svg
                  class="w-5 h-5"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                ><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" /></svg>
              </a>
            </div>
          </div>

          <!-- Quick Links Grid - Mobile Optimized -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-center">
            <router-link
              to="/"
              class="text-gray-400 hover:text-white text-sm py-2 rounded transition-colors"
            >
              Find Photographers
            </router-link>
            <router-link
              to="/events"
              class="text-gray-400 hover:text-white text-sm py-2 rounded transition-colors"
            >
              Events
            </router-link>
            <router-link
              to="/competitions"
              class="text-gray-400 hover:text-white text-sm py-2 rounded transition-colors"
            >
              Competitions
            </router-link>
            <router-link
              to="/auth"
              class="text-gray-400 hover:text-white text-sm py-2 rounded transition-colors"
            >
              Join Us
            </router-link>
            <router-link
              to="/verification"
              class="text-gray-400 hover:text-white text-sm py-2 rounded transition-colors"
            >
              Verification
            </router-link>
            <router-link
              to="/help"
              class="text-gray-400 hover:text-white text-sm py-2 rounded transition-colors"
            >
              Help
            </router-link>
            <router-link
              to="/contact"
              class="text-gray-400 hover:text-white text-sm py-2 rounded transition-colors"
            >
              Contact
            </router-link>
          </div>

          <!-- Legal Links -->
          <div class="flex flex-wrap justify-center gap-4 text-xs text-gray-400 border-t border-gray-800 pt-6">
            <router-link
              to="/privacy"
              class="hover:text-white transition-colors"
            >
              Privacy
            </router-link>
            <span>•</span>
            <router-link
              to="/terms"
              class="hover:text-white transition-colors"
            >
              Terms
            </router-link>
            <span>•</span>
            <router-link
              to="/about"
              class="hover:text-white transition-colors"
            >
              About
            </router-link>
          </div>

          <p class="text-center text-gray-500 text-xs">
            &copy; 2026 Photographer SB
          </p>
        </div>

        <!-- Desktop: Original 4-Column Layout -->
        <div class="hidden md:block">
          <!-- Brand & Social -->
          <div class="flex justify-between items-start mb-12 pb-10 border-b border-gray-800">
            <div class="max-w-xs">
              <div class="mb-4">
                <img
                  src="/images/logo-white.svg"
                  alt="Photographers - Across Somagro Bangladesh"
                  class="h-10 w-auto"
                >
              </div>
              <p class="text-gray-400 text-sm leading-relaxed">
                Connecting Bangladesh's finest photographers with clients nationwide.
              </p>
            </div>
            <div class="flex gap-2">
              <a
                href="https://www.facebook.com/thephotographersbd"
                target="_blank"
                rel="noopener noreferrer"
                class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-blue-600 flex items-center justify-center transition-colors"
                title="Follow us on Facebook"
              >
                <svg
                  class="w-5 h-5"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                ><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" /></svg>
              </a>
              <a
                href="https://www.instagram.com/thephotographersbd"
                target="_blank"
                rel="noopener noreferrer"
                class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-pink-600 flex items-center justify-center transition-colors"
                title="Follow us on Instagram"
              >
                <svg
                  class="w-5 h-5"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                ><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" /></svg>
              </a>
              <a
                href="https://wa.me/8801767300900"
                target="_blank"
                rel="noopener noreferrer"
                class="w-9 h-9 rounded-lg bg-gray-800 hover:bg-green-500 flex items-center justify-center transition-colors"
                title="Chat on WhatsApp"
              >
                <svg
                  class="w-5 h-5"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                ><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" /></svg>
              </a>
            </div>
          </div>

          <!-- 4 Column Links Grid -->
          <div class="grid grid-cols-4 gap-12 mb-8">
            <div>
              <h4 class="text-base font-bold mb-4">
                Quick Links
              </h4>
              <ul class="space-y-2">
                <li>
                  <router-link
                    to="/about"
                    class="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    About Us
                  </router-link>
                </li>
                <li>
                  <router-link
                    to="/how-it-works"
                    class="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    How It Works
                  </router-link>
                </li>
                <li>
                  <router-link
                    to="/pricing"
                    class="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    Pricing
                  </router-link>
                </li>
                <li>
                  <a
                    href="https://blog.photographersb.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    Blog
                  </a>
                </li>
                <li>
                  <router-link
                    to="/help"
                    class="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    Help Center
                  </router-link>
                </li>
              </ul>
            </div>

            <!-- Discover -->
            <div>
              <h4 class="text-base font-bold mb-4">
                Discover
              </h4>
              <ul class="space-y-2">
                <li>
                  <router-link
                    to="/"
                    class="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    Find Photographers
                  </router-link>
                </li>
                <li>
                  <router-link
                    to="/?section=cities"
                    class="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    Browse Cities
                  </router-link>
                </li>
                <li>
                  <router-link
                    to="/?section=categories"
                    class="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    Browse Categories
                  </router-link>
                </li>
                <li>
                  <router-link
                    to="/events"
                    class="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    Events
                  </router-link>
                </li>
                <li>
                  <router-link
                    to="/competitions"
                    class="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    Competitions
                  </router-link>
                </li>
              </ul>
            </div>

            <!-- For Photographers -->
            <div>
              <h4 class="text-base font-bold mb-4">
                For Photographers
              </h4>
              <ul class="space-y-2">
                <li>
                  <router-link
                    to="/auth"
                    class="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    Join as Photographer
                  </router-link>
                </li>
                <li>
                  <router-link
                    to="/be-featured"
                    class="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    Be Featured
                  </router-link>
                </li>
                <li>
                  <router-link
                    to="/become-sponsor"
                    class="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    Become a Sponsor
                  </router-link>
                </li>
                <li>
                  <router-link
                    to="/?section=hashtags"
                    class="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    Trending Topics
                  </router-link>
                </li>
              </ul>
            </div>

            <!-- Legal & Support -->
            <div>
              <h4 class="text-base font-bold mb-4">
                Legal & Support
              </h4>
              <ul class="space-y-2">
                <li>
                  <router-link
                    to="/contact"
                    class="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    Contact Us
                  </router-link>
                </li>
                <li>
                  <router-link
                    to="/privacy"
                    class="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    Privacy Policy
                  </router-link>
                </li>
                <li>
                  <router-link
                    to="/terms"
                    class="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    Terms of Service
                  </router-link>
                </li>
                <li>
                  <router-link
                    to="/cookies"
                    class="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    Cookie Policy
                  </router-link>
                </li>
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
                <router-link
                  to="/privacy"
                  class="hover:text-white transition-colors"
                >
                  Privacy
                </router-link>
                <router-link
                  to="/terms"
                  class="hover:text-white transition-colors"
                >
                  Terms
                </router-link>
                <router-link
                  to="/cookies"
                  class="hover:text-white transition-colors"
                >
                  Cookies
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch, h } from 'vue'
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
const errorReport = ref('')

const adminEmail = 'dev@photographersb.com'
const whatsappNumber = '8801767300900'

const buildReportMessage = (message) => {
  const time = new Date().toISOString()
  const url = window.location.href
  return `Error: ${message}\nTime: ${time}\nURL: ${url}`
}

const mailtoUrl = computed(() => {
  if (!errorReport.value) return `mailto:${adminEmail}`
  const subject = encodeURIComponent('Error Report')
  const body = encodeURIComponent(buildReportMessage(errorReport.value))
  return `mailto:${adminEmail}?subject=${subject}&body=${body}`
})

const whatsAppUrl = computed(() => {
  const text = errorReport.value ? buildReportMessage(errorReport.value) : 'Hello, I encountered an error.'
  return `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(text)}`
})

const setErrorReport = (message) => {
  const cleaned = String(message || '').slice(0, 300)
  errorReport.value = cleaned || 'An unexpected error occurred.'
}

const clearErrorReport = () => {
  errorReport.value = ''
}

const handleError = (event) => {
  const message = event?.message || event?.error?.message || 'Unexpected error'
  setErrorReport(message)
}

const handleRejection = (event) => {
  const message = event?.reason?.message || event?.reason || 'Unhandled promise rejection'
  setErrorReport(message)
}

const handleApiError = (event) => {
  const message = event?.detail?.message || 'Request failed'
  setErrorReport(message)
}

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

const DiscoverIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 2l7 4v12l-7 4-7-4V6l7-4z' }),
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8l2.5 2.5L12 14l-2.5-2.5L12 8z' })
])

const CommunityIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17 20h5V10a2 2 0 00-2-2h-3m-4 12h4M7 20H2V10a2 2 0 012-2h3m0 12v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h6' })
])

const LearningIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' })
])

const navLinks = [
  { name: 'Home', path: '/', icon: HomeIcon },
  { name: 'Discover', path: '/discover', icon: DiscoverIcon },
  { name: 'Community', path: '/community', icon: CommunityIcon },
  { name: 'Learn', path: '/learn', icon: LearningIcon },
  { name: 'Events', path: '/events', icon: CalendarIcon },
  { name: 'Competitions', path: '/competitions', icon: TrophyIcon },
]

const normalizeRole = (role) => String(role || '').toLowerCase().replace(/\s+/g, '_')

const isAdmin = computed(() => {
  return user.value && ['admin', 'super_admin'].includes(normalizeRole(user.value.role))
})

const isPhotographer = computed(() => {
  return user.value && ['photographer'].includes(normalizeRole(user.value.role))
})

const isJudge = computed(() => {
  return user.value && (normalizeRole(user.value.role) === 'judge' || user.value.is_judge === true)
})

const isClient = computed(() => {
  return user.value && normalizeRole(user.value.role) === 'client'
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
    localStorage.removeItem('user')
    localStorage.removeItem('user_role')
    user.value = null
    router.push('/')
  }
}

const shouldHydrateFromApi = (hasStoredUser) => {
  if (hasStoredUser) return true
  if (route?.meta?.requiresAuth || route?.meta?.requiresAdmin) return true
  const path = route?.path || ''
  return path.startsWith('/admin') || path.startsWith('/dashboard') || path.startsWith('/judge') || path.startsWith('/client')
}

const hydrateUser = async () => {
  const storedUser = localStorage.getItem('user')
  const hasStoredUser = Boolean(storedUser)
  if (hasStoredUser) {
    user.value = JSON.parse(storedUser)
  }

  if (!shouldHydrateFromApi(hasStoredUser)) {
    return
  }

  try {
    const { data } = await api.get('/auth/me')
    const resolvedUser = data?.data || data?.user || data
    if (resolvedUser?.role) {
      user.value = resolvedUser
      localStorage.setItem('user', JSON.stringify(resolvedUser))
      localStorage.setItem('user_role', normalizeRole(resolvedUser.role))
    }
  } catch (error) {
    // Ignore hydration failures; user will remain logged out in UI.
  }
}

onMounted(() => {
  hydrateUser()

  window.addEventListener('error', handleError)
  window.addEventListener('unhandledrejection', handleRejection)
  window.addEventListener('sb-error-report', handleApiError)
})

onBeforeUnmount(() => {
  window.removeEventListener('error', handleError)
  window.removeEventListener('unhandledrejection', handleRejection)
  window.removeEventListener('sb-error-report', handleApiError)
})

watch(mobileMenuOpen, (isOpen) => {
  if (!isOpen) {
    mobileUserMenuOpen.value = false
  }
})

// Watch for route changes and re-hydrate user when navigating to protected routes
watch(() => route.path, (newPath) => {
  // Re-hydrate user when navigating to dashboard, admin, or judge areas
  if (newPath.startsWith('/dashboard') || newPath.startsWith('/admin') || newPath.startsWith('/judge') || newPath.startsWith('/client')) {
    hydrateUser()
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

.sb-error-banner {
  position: sticky;
  top: 0;
  z-index: 60;
  background: #fff7ed;
  border-bottom: 1px solid #fed7aa;
  padding: 0.75rem 1rem;
}

.sb-error-banner__content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  max-width: 1200px;
  margin: 0 auto;
}

.sb-error-banner__title {
  font-weight: 700;
  color: #9a3412;
  margin-bottom: 0.25rem;
}

.sb-error-banner__message {
  color: #7c2d12;
  font-size: 0.9rem;
}

.sb-error-banner__detail {
  color: #9a3412;
  font-size: 0.8rem;
  margin-top: 0.25rem;
}


.sb-error-banner__actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.sb-error-banner__btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.4rem 0.75rem;
  border-radius: 999px;
  background: #111827;
  color: #fff;
  font-size: 0.8rem;
  font-weight: 600;
  transition: transform 0.15s ease, opacity 0.15s ease;
}

.sb-error-banner__btn:hover {
  transform: translateY(-1px);
  opacity: 0.95;
}

.sb-error-banner__btn--whatsapp {
  background: #16a34a;
}

.sb-error-banner__dismiss {
  background: transparent;
  color: #9a3412;
  font-weight: 600;
  font-size: 0.8rem;
}

@media (max-width: 768px) {
  .sb-error-banner__content {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
