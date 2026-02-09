<template>
  <div class="admin-quicknav-wrapper">
    <div class="admin-quicknav-bar">
      <div class="admin-quicknav-scroll">
        <div class="admin-quicknav-label">
          <span>Admin Tools</span>
        </div>
        <div class="admin-quicknav-row">
        
        <!-- Primary Actions -->
        <router-link to="/admin/dashboard" class="admin-nav-link">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
          Dashboard
        </router-link>

        <router-link to="/admin/analytics" class="admin-nav-link">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
          Analytics
        </router-link>

        <div class="admin-quicknav-divider"></div>

        <!-- User Management Dropdown -->
        <div class="admin-nav-dropdown" @mouseenter="handleEnter('users')" @mouseleave="handleLeave('users')">
          <button type="button" class="admin-nav-link dropdown-trigger" :ref="el => setTriggerRef('users', el)" @click="toggleDropdown('users')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Users
            <svg class="w-3 h-3 ml-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </button>
          <div v-show="activeDropdown === 'users'" class="admin-dropdown-menu" :style="dropdownStyle('users')" @mouseenter="handleEnter('users')" @mouseleave="handleLeave('users')">
            <router-link to="/admin/users" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
              All Users
            </router-link>
            <router-link to="/admin/photographers" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
              </svg>
              Photographers
            </router-link>
            <router-link to="/admin/featured-photographers" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
              </svg>
              Featured Photographers
            </router-link>
            <router-link to="/admin/verifications" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Verifications
            </router-link>
            <div class="border-t border-gray-200 my-1"></div>
            <router-link to="/admin/mentors" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
              Mentors
            </router-link>
            <router-link to="/admin/judges" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 20h10a2 2 0 002-2V6a2 2 0 00-2-2H9l-2 2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              Judges
            </router-link>
          </div>
        </div>

        <!-- Events Dropdown -->
        <div class="admin-nav-dropdown" @mouseenter="handleEnter('events')" @mouseleave="handleLeave('events')">
          <button type="button" class="admin-nav-link dropdown-trigger" :ref="el => setTriggerRef('events', el)" @click="toggleDropdown('events')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Events
            <svg class="w-3 h-3 ml-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </button>
          <div v-show="activeDropdown === 'events'" class="admin-dropdown-menu" :style="dropdownStyle('events')" @mouseenter="handleEnter('events')" @mouseleave="handleLeave('events')">
            <router-link to="/admin/events" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              Events
            </router-link>
            <router-link to="/admin/competitions" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
              </svg>
              Competitions
            </router-link>
            <router-link to="/admin/bookings" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
              Bookings
            </router-link>
          </div>
        </div>

        <!-- Content Dropdown -->
        <div class="admin-nav-dropdown" @mouseenter="handleEnter('content')" @mouseleave="handleLeave('content')">
          <button type="button" class="admin-nav-link dropdown-trigger" :ref="el => setTriggerRef('content', el)" @click="toggleDropdown('content')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Content
            <svg class="w-3 h-3 ml-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </button>
          <div v-show="activeDropdown === 'content'" class="admin-dropdown-menu" :style="dropdownStyle('content')" @mouseenter="handleEnter('content')" @mouseleave="handleLeave('content')">
            <router-link to="/admin/reviews" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
              </svg>
              Reviews
            </router-link>
            <router-link to="/admin/categories" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
              </svg>
              Categories
            </router-link>
            <router-link to="/admin/locations" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              Locations
            </router-link>
            <router-link to="/admin/hashtags" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
              </svg>
              Hashtags
            </router-link>
            <router-link to="/admin/share-frames" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              Share Frames
            </router-link>
            <router-link to="/admin/seo" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              SEO Settings
            </router-link>
          </div>
        </div>

        <!-- Finance Dropdown -->
        <div class="admin-nav-dropdown" @mouseenter="handleEnter('finance')" @mouseleave="handleLeave('finance')">
          <button type="button" class="admin-nav-link dropdown-trigger" :ref="el => setTriggerRef('finance', el)" @click="toggleDropdown('finance')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Finance
            <svg class="w-3 h-3 ml-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </button>
          <div v-show="activeDropdown === 'finance'" class="admin-dropdown-menu" :style="dropdownStyle('finance')" @mouseenter="handleEnter('finance')" @mouseleave="handleLeave('finance')">
            <router-link to="/admin/transactions" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
              </svg>
              Transactions
            </router-link>
            <router-link to="/admin/sponsors" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Sponsors
            </router-link>
          </div>
        </div>

        <div class="admin-quicknav-divider"></div>

        <!-- System Dropdown (Gray) -->
        <div class="admin-nav-dropdown" @mouseenter="handleEnter('system')" @mouseleave="handleLeave('system')">
          <button type="button" class="admin-nav-link admin-nav-link-secondary dropdown-trigger" :ref="el => setTriggerRef('system', el)" @click="toggleDropdown('system')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            </svg>
            System
            <svg class="w-3 h-3 ml-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </button>
          <div v-show="activeDropdown === 'system'" class="admin-dropdown-menu" :style="dropdownStyle('system')" @mouseenter="handleEnter('system')" @mouseleave="handleLeave('system')">
            <router-link to="/admin/settings" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
              </svg>
              Settings
            </router-link>
            <router-link to="/admin/activity-logs" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              Activity Logs
            </router-link>
            <router-link to="/admin/audit-logs" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
              </svg>
              Audit Logs
            </router-link>
            <router-link to="/admin/error-center" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
              Error Center
            </router-link>
          </div>
        </div>

        <!-- Messages Dropdown (Gray) -->
        <div class="admin-nav-dropdown" @mouseenter="handleEnter('comms')" @mouseleave="handleLeave('comms')">
          <button type="button" class="admin-nav-link admin-nav-link-secondary dropdown-trigger" :ref="el => setTriggerRef('comms', el)" @click="toggleDropdown('comms')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Messages
            <svg class="w-3 h-3 ml-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </button>
          <div v-show="activeDropdown === 'comms'" class="admin-dropdown-menu" :style="dropdownStyle('comms')" @mouseenter="handleEnter('comms')" @mouseleave="handleLeave('comms')">
            <router-link to="/admin/contact-messages" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              Contact Messages
            </router-link>
            <router-link to="/admin/notices" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
              </svg>
              Notices
            </router-link>
            <router-link to="/admin/notifications" class="admin-dropdown-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
              </svg>
              Notifications
            </router-link>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick, onMounted, onBeforeUnmount } from 'vue'

const activeDropdown = ref(null)
const triggerRefs = ref({})
const dropdownPositions = ref({})
const closeTimers = ref({})
const clickOpened = ref(null)

const setTriggerRef = (name, el) => {
  if (el) {
    triggerRefs.value[name] = el
  }
}

const updateDropdownPosition = (name) => {
  const trigger = triggerRefs.value[name]
  if (!trigger) return
  const rect = trigger.getBoundingClientRect()
  dropdownPositions.value = {
    ...dropdownPositions.value,
    [name]: {
      top: `${rect.bottom + 8}px`,
      left: `${rect.left}px`,
      minWidth: `${Math.max(rect.width, 200)}px`
    }
  }
}

const dropdownStyle = (name) => dropdownPositions.value[name] || {}

const openDropdown = (name) => {
  activeDropdown.value = name
  nextTick(() => updateDropdownPosition(name))
}

const closeDropdown = (name) => {
  if (activeDropdown.value === name) {
    activeDropdown.value = null
  }
  if (clickOpened.value === name) {
    clickOpened.value = null
  }
}

const toggleDropdown = (name) => {
  if (activeDropdown.value === name && clickOpened.value === name) {
    closeDropdown(name)
    return
  }
  activeDropdown.value = name
  clickOpened.value = name
  nextTick(() => updateDropdownPosition(name))
}

const handleEnter = (name) => {
  if (closeTimers.value[name]) {
    clearTimeout(closeTimers.value[name])
  }
  if (clickOpened.value && clickOpened.value !== name) {
    closeDropdown(clickOpened.value)
  }
  openDropdown(name)
}

const handleLeave = (name) => {
  if (closeTimers.value[name]) {
    clearTimeout(closeTimers.value[name])
  }
  if (clickOpened.value === name) {
    return
  }
  closeTimers.value[name] = setTimeout(() => closeDropdown(name), 300)
}

const handleDocumentClick = (event) => {
  if (!clickOpened.value) return
  const target = event.target
  if (target && target.closest('.admin-quicknav-wrapper')) {
    return
  }
  closeDropdown(clickOpened.value)
}

const handleViewportChange = () => {
  if (activeDropdown.value) {
    updateDropdownPosition(activeDropdown.value)
  }
}

onMounted(() => {
  window.addEventListener('resize', handleViewportChange)
  window.addEventListener('scroll', handleViewportChange, true)
  document.addEventListener('click', handleDocumentClick, true)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleViewportChange)
  window.removeEventListener('scroll', handleViewportChange, true)
  document.removeEventListener('click', handleDocumentClick, true)
})
</script>

<style scoped>
.admin-quicknav-wrapper {
  width: 100%;
  overflow: visible;
}

.admin-quicknav-bar {
  position: sticky;
  top: 0;
  z-index: 40;
  overflow: visible;
  margin-bottom: 1rem;
  padding: 0.65rem 0.75rem;
  border-radius: 0.9rem;
  border: 1px solid var(--admin-border);
  background:
    linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(249, 250, 251, 0.98));
  box-shadow: var(--admin-shadow-sm);
}

.admin-quicknav-scroll {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  max-width: 100%;
  overflow-x: auto;
  overflow-y: visible;
}

.admin-quicknav-label {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  margin-right: 0.75rem;
  padding-right: 0.75rem;
  border-right: 1px solid var(--admin-border);
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--admin-text-secondary);
}

.admin-quicknav-divider {
  height: 1.75rem;
  width: 1px;
  background: var(--admin-border);
  margin: 0 0.35rem;
}

.admin-quicknav-row {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  flex-wrap: nowrap;
  overflow-x: auto;
  overflow-y: visible;
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 transparent;
}

.admin-quicknav-row::-webkit-scrollbar {
  height: 6px;
}

.admin-quicknav-row::-webkit-scrollbar-track {
  background: transparent;
}

.admin-quicknav-row::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.admin-quicknav-row::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Admin Navigation Links */
.admin-nav-link {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.45rem 0.85rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--admin-text-secondary);
  background: white;
  border: 1px solid var(--admin-border);
  border-radius: 0.75rem;
  text-decoration: none;
  white-space: nowrap;
  transition: var(--sb-transition-fast);
  cursor: pointer;
}

.admin-nav-link:hover {
  background: var(--admin-bg-hover);
  border-color: var(--admin-brand-primary);
  color: var(--admin-brand-primary);
}

.admin-nav-link.router-link-active {
  background: var(--admin-brand-primary);
  border-color: var(--admin-brand-primary);
  color: white;
  font-weight: 600;
}

.admin-nav-link-secondary {
  color: var(--admin-text-muted);
  border-color: var(--admin-border-dark);
}

.admin-nav-link-secondary:hover {
  color: var(--admin-text-primary);
  border-color: var(--admin-border-dark);
  background: var(--admin-bg-hover);
}

/* Dropdown Styles */
.admin-nav-dropdown {
  position: relative;
  display: inline-block;
}

.admin-dropdown-menu {
  position: fixed;
  top: 0;
  left: 0;
  background: var(--admin-bg-card);
  border: 1px solid var(--admin-border);
  border-radius: 0.9rem;
  box-shadow: var(--admin-shadow-lg);
  padding: 0.4rem;
  min-width: 200px;
  z-index: 9999;
}

.admin-dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.625rem;
  padding: 0.6rem 0.85rem;
  font-size: 0.875rem;
  color: var(--admin-text-secondary);
  border-radius: 0.65rem;
  transition: var(--sb-transition-fast);
  text-decoration: none;
  white-space: nowrap;
}

.admin-dropdown-item:hover {
  background: var(--admin-bg-hover);
  color: var(--admin-text-primary);
}

.admin-dropdown-item.router-link-active {
  background: var(--admin-brand-primary-soft);
  color: var(--admin-brand-primary);
  font-weight: 600;
}

.admin-dropdown-item.router-link-active:hover {
  background: var(--admin-brand-primary-soft);
}
</style>
