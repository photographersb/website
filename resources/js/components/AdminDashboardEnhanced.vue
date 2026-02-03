<template>
  <div class="admin-dashboard min-h-screen bg-gray-50">
    <!-- Admin Header -->
    <AdminHeader 
      title="📊 Admin Dashboard" 
      subtitle="Complete platform management console"
      :show-back="false"
    />

    <!-- Main Content -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-16">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-burgundy"></div>
        <p class="mt-4 text-gray-600">Loading dashboard...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-red-700">Error Loading Dashboard</h3>
        <p class="text-red-700 mt-2">{{ error }}</p>
        <button @click="loadDashboardData" class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
          Try Again
        </button>
      </div>

      <!-- Dashboard Content -->
      <div v-else class="space-y-8">
        
        <!-- 1️⃣ CORE KPIs - Row 1 -->
        <section class="space-y-4">
          <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <span>📊</span> Core Metrics
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Users -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500 hover:shadow-lg transition-shadow">
              <div class="flex justify-between items-start">
                <div>
                  <p class="text-gray-600 text-sm font-medium">Total Users</p>
                  <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatNumber(stats.total_users) }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ stats.active_users || 0 }} active</p>
                </div>
                <div class="text-blue-500 text-3xl">👥</div>
              </div>
            </div>

            <!-- Total Photographers -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500 hover:shadow-lg transition-shadow">
              <div class="flex justify-between items-start">
                <div>
                  <p class="text-gray-600 text-sm font-medium">Photographers</p>
                  <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatNumber(stats.total_photographers) }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ stats.verified_photographers || 0 }} verified</p>
                </div>
                <div class="text-green-500 text-3xl">📸</div>
              </div>
            </div>

            <!-- Total Events -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500 hover:shadow-lg transition-shadow">
              <div class="flex justify-between items-start">
                <div>
                  <p class="text-gray-600 text-sm font-medium">Events</p>
                  <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatNumber(stats.total_events) }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ stats.active_events || 0 }} active</p>
                </div>
                <div class="text-purple-500 text-3xl">🎉</div>
              </div>
            </div>

            <!-- Total Competitions -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500 hover:shadow-lg transition-shadow">
              <div class="flex justify-between items-start">
                <div>
                  <p class="text-gray-600 text-sm font-medium">Competitions</p>
                  <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatNumber(stats.total_competitions) }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ stats.active_competitions || 0 }} active</p>
                </div>
                <div class="text-yellow-500 text-3xl">🏆</div>
              </div>
            </div>
          </div>
        </section>

        <!-- 2️⃣ QUICK ACTIONS - Row 2 -->
        <section class="space-y-4">
          <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <span>⚡</span> Quick Actions
          </h2>
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <router-link to="/admin/events/create" class="group bg-white rounded-lg shadow-md p-4 text-center hover:shadow-lg hover:border-burgundy border-2 border-transparent transition-all">
              <div class="text-3xl mb-2">➕</div>
              <p class="font-semibold text-sm text-gray-900">Create Event</p>
            </router-link>

            <router-link to="/admin/competitions/create" class="group bg-white rounded-lg shadow-md p-4 text-center hover:shadow-lg hover:border-burgundy border-2 border-transparent transition-all">
              <div class="text-3xl mb-2">🏆</div>
              <p class="font-semibold text-sm text-gray-900">New Competition</p>
            </router-link>

            <router-link to="/admin/platform-sponsors" class="group bg-white rounded-lg shadow-md p-4 text-center hover:shadow-lg hover:border-burgundy border-2 border-transparent transition-all">
              <div class="text-3xl mb-2">🤝</div>
              <p class="font-semibold text-sm text-gray-900">Add Sponsor</p>
            </router-link>

            <router-link to="/admin/mentors" class="group bg-white rounded-lg shadow-md p-4 text-center hover:shadow-lg hover:border-burgundy border-2 border-transparent transition-all">
              <div class="text-3xl mb-2">👨‍🏫</div>
              <p class="font-semibold text-sm text-gray-900">Add Mentor</p>
            </router-link>

            <router-link to="/admin/judges" class="group bg-white rounded-lg shadow-md p-4 text-center hover:shadow-lg hover:border-burgundy border-2 border-transparent transition-all">
              <div class="text-3xl mb-2">⚖️</div>
              <p class="font-semibold text-sm text-gray-900">Add Judge</p>
            </router-link>

            <router-link to="/admin/notices" class="group bg-white rounded-lg shadow-md p-4 text-center hover:shadow-lg hover:border-burgundy border-2 border-transparent transition-all">
              <div class="text-3xl mb-2">📢</div>
              <p class="font-semibold text-sm text-gray-900">New Notice</p>
            </router-link>
          </div>
        </section>

        <!-- 3️⃣ PENDING ITEMS - Row 3 -->
        <section v-if="hasPendingItems" class="space-y-4">
          <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <span>⚠️</span> Pending Actions
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <router-link v-if="stats.pending_bookings > 0" to="/admin/bookings?status=pending" class="bg-amber-50 border-2 border-amber-200 rounded-lg p-4 hover:shadow-lg transition-shadow hover:border-amber-400">
              <p class="text-amber-800 font-bold text-2xl">{{ stats.pending_bookings }}</p>
              <p class="text-amber-700 font-semibold text-sm">Pending Bookings</p>
              <p class="text-amber-600 text-xs mt-1">Awaiting confirmation</p>
            </router-link>

            <router-link v-if="stats.pending_verifications > 0" to="/admin/photographers/onboarding/pending" class="bg-amber-50 border-2 border-amber-200 rounded-lg p-4 hover:shadow-lg transition-shadow hover:border-amber-400">
              <p class="text-amber-800 font-bold text-2xl">{{ stats.pending_verifications }}</p>
              <p class="text-amber-700 font-semibold text-sm">Pending Verifications</p>
              <p class="text-amber-600 text-xs mt-1">Photographer applications</p>
            </router-link>

            <router-link v-if="stats.pending_submissions > 0" to="/admin/competitions/submissions?status=pending" class="bg-amber-50 border-2 border-amber-200 rounded-lg p-4 hover:shadow-lg transition-shadow hover:border-amber-400">
              <p class="text-amber-800 font-bold text-2xl">{{ stats.pending_submissions }}</p>
              <p class="text-amber-700 font-semibold text-sm">Pending Submissions</p>
              <p class="text-amber-600 text-xs mt-1">Awaiting review</p>
            </router-link>

            <router-link v-if="stats.pending_reviews > 0" to="/admin/reviews" class="bg-amber-50 border-2 border-amber-200 rounded-lg p-4 hover:shadow-lg transition-shadow hover:border-amber-400">
              <p class="text-amber-800 font-bold text-2xl">{{ stats.pending_reviews }}</p>
              <p class="text-amber-700 font-semibold text-sm">Pending Reviews</p>
              <p class="text-amber-600 text-xs mt-1">Need moderation</p>
            </router-link>
          </div>
        </section>

        <!-- 4️⃣ MANAGEMENT MODULES - Row 4 -->
        <section class="space-y-4">
          <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <span>📁</span> Management Modules
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Users Module Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-blue-500">
              <div class="bg-blue-50 px-6 py-4">
                <h3 class="text-lg font-bold text-blue-900 flex items-center gap-2">
                  <span>👥</span> Users Management
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/users" class="flex items-center gap-3 p-2 rounded hover:bg-blue-50 text-gray-700 hover:text-blue-700 transition-colors">
                  <span>📋</span> <span class="text-sm">All Users</span>
                </router-link>
                <router-link to="/admin/pending-users" class="flex items-center gap-3 p-2 rounded hover:bg-blue-50 text-gray-700 hover:text-blue-700 transition-colors">
                  <span>⏳</span> <span class="text-sm">Pending Approvals ({{ stats.pending_users || 0 }})</span>
                </router-link>
                <router-link to="/admin/users?role=photographer" class="flex items-center gap-3 p-2 rounded hover:bg-blue-50 text-gray-700 hover:text-blue-700 transition-colors">
                  <span>📸</span> <span class="text-sm">Photographers ({{ stats.total_photographers || 0 }})</span>
                </router-link>
              </div>
            </div>

            <!-- Photographers Module Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-green-500">
              <div class="bg-green-50 px-6 py-4">
                <h3 class="text-lg font-bold text-green-900 flex items-center gap-2">
                  <span>📸</span> Photographers
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/photographers" class="flex items-center gap-3 p-2 rounded hover:bg-green-50 text-gray-700 hover:text-green-700 transition-colors">
                  <span>📋</span> <span class="text-sm">Directory</span>
                </router-link>
                <router-link to="/admin/verifications" class="flex items-center gap-3 p-2 rounded hover:bg-green-50 text-gray-700 hover:text-green-700 transition-colors">
                  <span>✅</span> <span class="text-sm">Verifications</span>
                </router-link>
              </div>
            </div>

            <!-- Events Module Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-purple-500">
              <div class="bg-purple-50 px-6 py-4">
                <h3 class="text-lg font-bold text-purple-900 flex items-center gap-2">
                  <span>🎉</span> Events
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/events" class="flex items-center gap-3 p-2 rounded hover:bg-purple-50 text-gray-700 hover:text-purple-700 transition-colors">
                  <span>📋</span> <span class="text-sm">All Events</span>
                </router-link>
                <router-link to="/admin/events/create" class="flex items-center gap-3 p-2 rounded hover:bg-purple-50 text-gray-700 hover:text-purple-700 transition-colors">
                  <span>➕</span> <span class="text-sm">Create Event</span>
                </router-link>
              </div>
            </div>

            <!-- Bookings Module Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-orange-500">
              <div class="bg-orange-50 px-6 py-4">
                <h3 class="text-lg font-bold text-orange-900 flex items-center gap-2">
                  <span>📅</span> Bookings
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/bookings" class="flex items-center gap-3 p-2 rounded hover:bg-orange-50 text-gray-700 hover:text-orange-700 transition-colors">
                  <span>📋</span> <span class="text-sm">All Bookings</span>
                </router-link>
                <router-link to="/admin/bookings?status=pending" class="flex items-center gap-3 p-2 rounded hover:bg-orange-50 text-gray-700 hover:text-orange-700 transition-colors">
                  <span>⏳</span> <span class="text-sm">Pending ({{ stats.pending_bookings || 0 }})</span>
                </router-link>
              </div>
            </div>

            <!-- Competitions Module Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-yellow-500">
              <div class="bg-yellow-50 px-6 py-4">
                <h3 class="text-lg font-bold text-yellow-900 flex items-center gap-2">
                  <span>🏆</span> Competitions
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/competitions" class="flex items-center gap-3 p-2 rounded hover:bg-yellow-50 text-gray-700 hover:text-yellow-700 transition-colors">
                  <span>📋</span> <span class="text-sm">All Competitions</span>
                </router-link>
                <router-link to="/admin/competitions/submissions" class="flex items-center gap-3 p-2 rounded hover:bg-yellow-50 text-gray-700 hover:text-yellow-700 transition-colors">
                  <span>📤</span> <span class="text-sm">Submissions</span>
                </router-link>
                <router-link to="/admin/competitions/create" class="flex items-center gap-3 p-2 rounded hover:bg-yellow-50 text-gray-700 hover:text-yellow-700 transition-colors">
                  <span>➕</span> <span class="text-sm">Create</span>
                </router-link>
              </div>
            </div>

            <!-- Reviews Module Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-red-500">
              <div class="bg-red-50 px-6 py-4">
                <h3 class="text-lg font-bold text-red-900 flex items-center gap-2">
                  <span>⭐</span> Reviews
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/reviews" class="flex items-center gap-3 p-2 rounded hover:bg-red-50 text-gray-700 hover:text-red-700 transition-colors">
                  <span>📋</span> <span class="text-sm">All Reviews</span>
                </router-link>
                <router-link to="/admin/reviews/stats" class="flex items-center gap-3 p-2 rounded hover:bg-red-50 text-gray-700 hover:text-red-700 transition-colors">
                  <span>📊</span> <span class="text-sm">Statistics</span>
                </router-link>
              </div>
            </div>

            <!-- Transactions Module Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-green-600">
              <div class="bg-green-50 px-6 py-4">
                <h3 class="text-lg font-bold text-green-900 flex items-center gap-2">
                  <span>💳</span> Transactions
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/transactions" class="flex items-center gap-3 p-2 rounded hover:bg-green-50 text-gray-700 hover:text-green-700 transition-colors">
                  <span>📋</span> <span class="text-sm">All Transactions</span>
                </router-link>
                <router-link to="/admin/transactions/stats" class="flex items-center gap-3 p-2 rounded hover:bg-green-50 text-gray-700 hover:text-green-700 transition-colors">
                  <span>📊</span> <span class="text-sm">Statistics</span>
                </router-link>
              </div>
            </div>

            <!-- Contacts & Support Module Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-indigo-500">
              <div class="bg-indigo-50 px-6 py-4">
                <h3 class="text-lg font-bold text-indigo-900 flex items-center gap-2">
                  <span>💬</span> Support & Messages
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/contact-messages" class="flex items-center gap-3 p-2 rounded hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 transition-colors">
                  <span>📨</span> <span class="text-sm">Contact Messages</span>
                </router-link>
              </div>
            </div>

            <!-- Notices & Communication Module Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-pink-500">
              <div class="bg-pink-50 px-6 py-4">
                <h3 class="text-lg font-bold text-pink-900 flex items-center gap-2">
                  <span>📢</span> Notices
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/notices" class="flex items-center gap-3 p-2 rounded hover:bg-pink-50 text-gray-700 hover:text-pink-700 transition-colors">
                  <span>📋</span> <span class="text-sm">All Notices</span>
                </router-link>
                <router-link to="/admin/notices/roles/available" class="flex items-center gap-3 p-2 rounded hover:bg-pink-50 text-gray-700 hover:text-pink-700 transition-colors">
                  <span>👥</span> <span class="text-sm">Roles</span>
                </router-link>
              </div>
            </div>
          </div>
        </section>

        <!-- 5️⃣ SPECIALIST MODULES - Row 5 -->
        <section class="space-y-4">
          <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <span>🎯</span> Specialist Modules
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Sponsors Module -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-cyan-500">
              <div class="bg-cyan-50 px-6 py-4">
                <h3 class="text-lg font-bold text-cyan-900 flex items-center gap-2">
                  <span>🤝</span> Sponsors
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/platform-sponsors" class="flex items-center gap-3 p-2 rounded hover:bg-cyan-50 text-gray-700 hover:text-cyan-700 transition-colors">
                  <span>📋</span> <span class="text-sm">All Sponsors</span>
                </router-link>
              </div>
            </div>

            <!-- Mentors Module -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-amber-600">
              <div class="bg-amber-50 px-6 py-4">
                <h3 class="text-lg font-bold text-amber-900 flex items-center gap-2">
                  <span>👨‍🏫</span> Mentors
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/mentors" class="flex items-center gap-3 p-2 rounded hover:bg-amber-50 text-gray-700 hover:text-amber-700 transition-colors">
                  <span>📋</span> <span class="text-sm">All Mentors</span>
                </router-link>
              </div>
            </div>

            <!-- Judges Module -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-slate-600">
              <div class="bg-slate-50 px-6 py-4">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                  <span>⚖️</span> Judges
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/judges" class="flex items-center gap-3 p-2 rounded hover:bg-slate-50 text-gray-700 hover:text-slate-700 transition-colors">
                  <span>📋</span> <span class="text-sm">All Judges</span>
                </router-link>
                <router-link to="/judge/dashboard" class="flex items-center gap-3 p-2 rounded hover:bg-slate-50 text-gray-700 hover:text-slate-700 transition-colors">
                  <span>📊</span> <span class="text-sm">Judge Dashboard</span>
                </router-link>
              </div>
            </div>

            <!-- Hashtags Module -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-rose-500">
              <div class="bg-rose-50 px-6 py-4">
                <h3 class="text-lg font-bold text-rose-900 flex items-center gap-2">
                  <span>#️⃣</span> Hashtags
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/hashtags" class="flex items-center gap-3 p-2 rounded hover:bg-rose-50 text-gray-700 hover:text-rose-700 transition-colors">
                  <span>📋</span> <span class="text-sm">All Hashtags</span>
                </router-link>
                <router-link to="/admin/hashtags/featured" class="flex items-center gap-3 p-2 rounded hover:bg-rose-50 text-gray-700 hover:text-rose-700 transition-colors">
                  <span>⭐</span> <span class="text-sm">Featured</span>
                </router-link>
              </div>
            </div>

            <!-- Certificates Module -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-teal-500">
              <div class="bg-teal-50 px-6 py-4">
                <h3 class="text-lg font-bold text-teal-900 flex items-center gap-2">
                  <span>🎓</span> Certificates
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/certificates" class="flex items-center gap-3 p-2 rounded hover:bg-teal-50 text-gray-700 hover:text-teal-700 transition-colors">
                  <span>📋</span> <span class="text-sm">All Certificates</span>
                </router-link>
                <router-link to="/admin/certificates/manual-issuance" class="flex items-center gap-3 p-2 rounded hover:bg-teal-50 text-gray-700 hover:text-teal-700 transition-colors">
                  <span>✍️</span> <span class="text-sm">Manual Issuance</span>
                </router-link>
                <router-link to="/admin/certificates/templates" class="flex items-center gap-3 p-2 rounded hover:bg-teal-50 text-gray-700 hover:text-teal-700 transition-colors">
                  <span>🎨</span> <span class="text-sm">Design Templates</span>
                </router-link>
              </div>
            </div>
          </div>
        </section>

        <!-- 6️⃣ TOOLS & UTILITIES - Row 6 -->
        <section class="space-y-4">
          <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <span>🛠️</span> Tools & Utilities
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Share Frames Module -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-fuchsia-500">
              <div class="bg-fuchsia-50 px-6 py-4">
                <h3 class="text-lg font-bold text-fuchsia-900 flex items-center gap-2">
                  <span>📸</span> Share Frames
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/share-frames" class="flex items-center gap-3 p-2 rounded hover:bg-fuchsia-50 text-gray-700 hover:text-fuchsia-700 transition-colors">
                  <span>🎨</span> <span class="text-sm">Frame Generator</span>
                </router-link>
                <p class="text-xs text-gray-500 px-2 py-1">Create branded social media frames</p>
              </div>
            </div>

            <!-- Settings Module -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-gray-600">
              <div class="bg-gray-50 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                  <span>⚙️</span> Settings
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/settings" class="flex items-center gap-3 p-2 rounded hover:bg-gray-50 text-gray-700 hover:text-gray-900 transition-colors">
                  <span>📋</span> <span class="text-sm">Platform Settings</span>
                </router-link>
                <router-link to="/admin/settings/changes" class="flex items-center gap-3 p-2 rounded hover:bg-gray-50 text-gray-700 hover:text-gray-900 transition-colors">
                  <span>📝</span> <span class="text-sm">Audit Trail</span>
                </router-link>
              </div>
            </div>

            <!-- Activity & Logs Module -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-violet-500">
              <div class="bg-violet-50 px-6 py-4">
                <h3 class="text-lg font-bold text-violet-900 flex items-center gap-2">
                  <span>📊</span> Logs & Activity
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/activity-logs" class="flex items-center gap-3 p-2 rounded hover:bg-violet-50 text-gray-700 hover:text-violet-700 transition-colors">
                  <span>📝</span> <span class="text-sm">Activity Logs</span>
                </router-link>
                <router-link to="/admin/audit-logs" class="flex items-center gap-3 p-2 rounded hover:bg-violet-50 text-gray-700 hover:text-violet-700 transition-colors">
                  <span>🔍</span> <span class="text-sm">Audit Logs</span>
                </router-link>
                <router-link to="/admin/error-center" class="flex items-center gap-3 p-2 rounded hover:bg-violet-50 text-gray-700 hover:text-violet-700 transition-colors">
                  <span>🚨</span> <span class="text-sm">Error Center</span>
                </router-link>
              </div>
            </div>

            <!-- System Module -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-lime-500">
              <div class="bg-lime-50 px-6 py-4">
                <h3 class="text-lg font-bold text-lime-900 flex items-center gap-2">
                  <span>🔧</span> System
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/seo" class="flex items-center gap-3 p-2 rounded hover:bg-lime-50 text-gray-700 hover:text-lime-700 transition-colors">
                  <span>🔍</span> <span class="text-sm">SEO Settings</span>
                </router-link>
                <router-link to="/admin/categories" class="flex items-center gap-3 p-2 rounded hover:bg-lime-50 text-gray-700 hover:text-lime-700 transition-colors">
                  <span>📂</span> <span class="text-sm">Categories</span>
                </router-link>
              </div>
            </div>
          </div>
        </section>

        <!-- 7️⃣ CONTENT MANAGEMENT -->
        <section class="space-y-4">
          <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <span>📝</span> Content Management
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Categories -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-violet-500">
              <div class="bg-violet-50 px-6 py-4">
                <h3 class="text-lg font-bold text-violet-900 flex items-center gap-2">
                  <span>📂</span> Categories
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/categories" class="flex items-center gap-3 p-2 rounded hover:bg-violet-50 text-gray-700 hover:text-violet-700 transition-colors">
                  <span>📋</span> <span class="text-sm">Photography Categories</span>
                </router-link>
              </div>
            </div>

            <!-- Geographic Settings -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border-t-4 border-fuchsia-500">
              <div class="bg-fuchsia-50 px-6 py-4">
                <h3 class="text-lg font-bold text-fuchsia-900 flex items-center gap-2">
                  <span>🌍</span> Geographic
                </h3>
              </div>
              <div class="px-6 py-4 space-y-2 border-t">
                <router-link to="/admin/cities" class="flex items-center gap-3 p-2 rounded hover:bg-fuchsia-50 text-gray-700 hover:text-fuchsia-700 transition-colors">
                  <span>🏙️</span> <span class="text-sm">Cities</span>
                </router-link>
              </div>
            </div>
          </div>
        </section>

      </div>
    </div>
  </div>
</template>

<script>
import AdminHeader from './AdminHeader.vue';

export default {
  name: 'AdminDashboardEnhanced',
  components: {
    AdminHeader,
  },
  data() {
    return {
      loading: true,
      error: null,
      stats: {
        total_users: 0,
        active_users: 0,
        total_photographers: 0,
        verified_photographers: 0,
        total_events: 0,
        active_events: 0,
        total_competitions: 0,
        active_competitions: 0,
        pending_bookings: 0,
        pending_verifications: 0,
        pending_submissions: 0,
        pending_reviews: 0,
        pending_users: 0,
      }
    }
  },
  computed: {
    hasPendingItems() {
      return this.stats.pending_bookings > 0 || 
             this.stats.pending_verifications > 0 || 
             this.stats.pending_submissions > 0 || 
             this.stats.pending_reviews > 0;
    }
  },
  methods: {
    loadDashboardData() {
      this.loading = true;
      this.error = null;

      fetch('/api/v1/admin/dashboard', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
          'Accept': 'application/json'
        }
      })
      .then(res => res.json())
      .then(data => {
        if (data.data && data.data.stats) {
          this.stats = {
            total_users: data.data.stats.total_users || 0,
            active_users: data.data.stats.active_users || 0,
            total_photographers: data.data.stats.total_photographers || 0,
            verified_photographers: data.data.stats.verified_photographers || 0,
            total_events: data.data.stats.total_events || 0,
            active_events: data.data.stats.published_events || data.data.stats.active_events || 0,
            total_competitions: data.data.stats.total_competitions || 0,
            active_competitions: data.data.stats.active_competitions || 0,
            pending_bookings: data.data.stats.pending_bookings || 0,
            pending_verifications: data.data.stats.pending_verifications || 0,
            pending_submissions: data.data.stats.pending_submissions || 0,
            pending_reviews: data.data.stats.pending_reviews || 0,
            pending_users: data.data.stats.pending_users || 0,
          };
        }
        this.loading = false;
      })
      .catch(err => {
        this.error = err.message || 'Failed to load dashboard';
        this.loading = false;
      });
    },
    formatNumber(num) {
      if (!num) return 0;
      if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
      if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
      return num.toString();
    }
  },
  mounted() {
    this.loadDashboardData();
  }
}
</script>

<style scoped>
.admin-dashboard {
  --burgundy: #8E0E3F;
  --burgundy-dark: #6c0b1a;
}
</style>
