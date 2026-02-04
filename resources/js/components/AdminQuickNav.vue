<template>
  <div class="admin-quicknav-wrapper">
    <div class="admin-quicknav-bar bg-white rounded-lg shadow p-4 mb-6">
      <div class="admin-quicknav-header">
        <h3 class="text-lg font-semibold text-gray-900">⚡ Quick Navigation</h3>
      </div>
      <div class="admin-quicknav-row">
      <!-- Users -->
      <router-link to="/admin/users" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Users</span>
      </router-link>

      <!-- Photographers -->
      <router-link to="/admin/photographers" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Photographers</span>
      </router-link>

      <!-- Verifications -->
      <router-link to="/admin/verifications" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Verifications</span>
      </router-link>

      <!-- Bookings -->
      <router-link to="/admin/bookings" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Bookings</span>
      </router-link>

      <!-- Competitions -->
      <router-link to="/admin/competitions" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Competitions</span>
      </router-link>

      <!-- Mentors -->
      <router-link to="/admin/mentors" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Mentors</span>
      </router-link>

      <!-- Judges -->
      <router-link to="/admin/judges" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 20h10a2 2 0 002-2V6a2 2 0 00-2-2H9l-2 2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Judges</span>
      </router-link>

      <!-- Events -->
      <router-link to="/admin/events" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Events</span>
      </router-link>

      <!-- Reviews -->
      <router-link to="/admin/reviews" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Reviews</span>
      </router-link>

      <!-- Transactions -->
      <router-link to="/admin/transactions" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Transactions</span>
      </router-link>

      <!-- Activity Logs -->
      <router-link to="/admin/activity-logs" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 00-4-4H3m18 6v-2a4 4 0 00-4-4h-2m-4 6V5m0 12H7m4 0h4" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Activity</span>
      </router-link>

      <!-- Sponsors -->
      <router-link to="/admin/sponsors" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Sponsors</span>
      </router-link>

      <!-- Categories -->
      <router-link to="/admin/categories" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 11h10M7 15h10M7 19h10" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Categories</span>
      </router-link>

      <!-- Cities -->
      <router-link to="/admin/cities" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Cities</span>
      </router-link>

      <!-- SEO Center -->
      <router-link to="/admin/seo" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <span class="text-xs font-medium text-primary-700">SEO</span>
      </router-link>

      <!-- Messages -->
      <router-link to="/admin/contact-messages" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Messages</span>
      </router-link>

      <!-- Notices -->
      <router-link to="/admin/notices" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Notices</span>
      </router-link>

      <!-- Settings -->
      <router-link to="/admin/settings" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Settings</span>
      </router-link>

      <!-- Notifications -->
      <router-link to="/admin/notifications" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Notifications</span>
      </router-link>

      <!-- Error Center -->
      <router-link to="/admin/error-center" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 5v1M12 3a9 9 0 110 18 9 9 0 010-18z" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Errors</span>
      </router-link>

      <!-- Audit Logs -->
      <router-link to="/admin/audit-logs" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Audit</span>
      </router-link>

      <!-- Share Frames -->
      <router-link to="/admin/share-frames" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0l7.172 7.172m-7.172-7.172a2 2 0 012.828 0L20 16" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Frames</span>
      </router-link>

      <!-- Hashtags -->
      <router-link to="/admin/hashtags" class="quick-nav-btn bg-primary-50 hover:bg-primary-100 border-primary-200">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
        </svg>
        <span class="text-xs font-medium text-primary-700">Hashtags</span>
      </router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminQuickNav',
}
</script>

<style scoped>
.admin-quicknav-wrapper {
  width: 100%;
}

.admin-quicknav-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.75rem;
}

.admin-quicknav-row {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

@media (max-width: 768px) {
  .admin-quicknav-row {
    flex-wrap: nowrap;
    overflow-x: auto;
    padding-bottom: 0.5rem;
  }
}

.quick-nav-btn {
  @apply flex flex-col items-center justify-center p-4 border rounded-lg transition-all duration-200 cursor-pointer;
  @apply hover:shadow-md hover:scale-105 active:scale-95;
  min-width: 110px;
}

.quick-nav-btn svg {
  @apply mb-1.5;
}

.quick-nav-btn.router-link-active {
  @apply ring-2 ring-offset-2 shadow-md scale-105;
}
</style>
