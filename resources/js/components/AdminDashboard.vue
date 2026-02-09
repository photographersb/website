<template>
  <AdminLayout 
    page-title="Admin Dashboard"
    page-description="Real-time platform analytics and management"
    :show-breadcrumbs="false"
    :show-footer="true"
  >
    <div class="space-y-8">
      <!-- SECTION 1: TIME RANGE SELECTOR & CONTROLS -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
          <label class="text-sm font-medium text-gray-700">Period:</label>
          <select 
            v-model="timeRange" 
            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium bg-white hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 transition-colors" 
            @change="refreshData"
          >
            <option value="today">
              Today
            </option>
            <option value="week">
              This Week
            </option>
            <option value="month">
              This Month
            </option>
            <option value="year">
              This Year
            </option>
            <option value="custom">
              Custom Range
            </option>
          </select>
        </div>

        <div class="flex items-center gap-3">
          <button
            :disabled="isLoading"
            class="flex items-center gap-2 px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition-colors"
            title="Refresh dashboard data"
            @click="refreshData"
          >
            <svg 
              :class="['w-4 h-4', isLoading && 'animate-spin']"
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
              />
            </svg>
            {{ isLoading ? 'Refreshing...' : 'Refresh' }}
          </button>

          <button
            class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors"
            title="Export dashboard data"
            @click="exportData"
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
                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
              />
            </svg>
            Export
          </button>
        </div>
      </div>

      <!-- SECTION 2: KPI CARDS (8 Key Metrics) -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Users KPI -->
        <KPICard
          title="Total Users"
          :value="dashboardData?.stats?.total_users || 0"
          :change="dashboardData?.stats?.users_change || 0"
          icon="👥"
          color-class="from-blue-500 to-blue-600"
          :secondary-value="`${dashboardData?.stats?.active_users || 0} active`"
        />

        <!-- Photographers KPI -->
        <KPICard
          title="Photographers"
          :value="dashboardData?.stats?.total_photographers || 0"
          :change="dashboardData?.stats?.photographers_change || 0"
          icon="📷"
          color-class="from-pink-500 to-pink-600"
          :secondary-value="`${dashboardData?.stats?.verified_photographers || 0} verified`"
        />

        <!-- Bookings KPI -->
        <KPICard
          title="Total Bookings"
          :value="dashboardData?.stats?.total_bookings || 0"
          :change="dashboardData?.stats?.bookings_change || 0"
          icon="📅"
          color-class="from-green-500 to-green-600"
          :secondary-value="`${dashboardData?.stats?.pending_bookings || 0} pending`"
        />

        <!-- Revenue KPI -->
        <KPICard
          title="Revenue"
          :value="formatCurrency(dashboardData?.stats?.total_revenue || 0)"
          :change="dashboardData?.stats?.revenue_change || 0"
          icon="💰"
          color-class="from-orange-500 to-orange-600"
          :secondary-value="`${dashboardData?.stats?.revenue_transactions || 0} transactions`"
        />

        <!-- Competitions KPI -->
        <KPICard
          title="Competitions"
          :value="dashboardData?.stats?.active_competitions || 0"
          :change="dashboardData?.stats?.competitions_change || 0"
          icon="🏆"
          color-class="from-yellow-500 to-yellow-600"
          :secondary-value="`${dashboardData?.stats?.competition_submissions || 0} submissions`"
        />

        <!-- Events KPI -->
        <KPICard
          title="Events"
          :value="dashboardData?.stats?.total_events || 0"
          :change="dashboardData?.stats?.events_change || 0"
          icon="📅"
          color-class="from-purple-500 to-purple-600"
          :secondary-value="`${dashboardData?.stats?.upcoming_events || 0} upcoming`"
        />

        <!-- Reviews KPI -->
        <KPICard
          title="Reviews"
          :value="dashboardData?.stats?.total_reviews || 0"
          :change="dashboardData?.stats?.reviews_change || 0"
          icon="⭐"
          color-class="from-red-500 to-red-600"
          :secondary-value="`${formatFixed(dashboardData?.stats?.avg_rating, 1, '0.0')} avg rating`"
        />

        <!-- System Health KPI -->
        <KPICard
          title="System Health"
          :value="`${dashboardData?.stats?.system_health || 0}%`"
          :change="dashboardData?.stats?.health_change || 0"
          icon="⚙️"
          color-class="from-teal-500 to-teal-600"
          :secondary-value="dashboardData?.stats?.health_status || 'Healthy'"
        />
      </div>

      <!-- SECTION 3: QUICK ACTIONS -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">
            🚀 Quick Actions
          </h3>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 p-6">
          <router-link
            to="/admin/competitions"
            class="flex flex-col items-center p-3 rounded-lg border border-gray-200 hover:border-amber-300 hover:bg-amber-50 transition-colors text-center"
          >
            <span class="text-2xl mb-2">🏆</span>
            <span class="text-xs font-medium text-gray-700">New Competition</span>
          </router-link>

          <router-link
            to="/admin/events"
            class="flex flex-col items-center p-3 rounded-lg border border-gray-200 hover:border-amber-300 hover:bg-amber-50 transition-colors text-center"
          >
            <span class="text-2xl mb-2">📅</span>
            <span class="text-xs font-medium text-gray-700">New Event</span>
          </router-link>

          <router-link
            to="/admin/users"
            class="flex flex-col items-center p-3 rounded-lg border border-gray-200 hover:border-amber-300 hover:bg-amber-50 transition-colors text-center"
          >
            <span class="text-2xl mb-2">👤</span>
            <span class="text-xs font-medium text-gray-700">Add User</span>
          </router-link>

          <router-link
            to="/admin/photographers"
            class="flex flex-col items-center p-3 rounded-lg border border-gray-200 hover:border-amber-300 hover:bg-amber-50 transition-colors text-center"
          >
            <span class="text-2xl mb-2">📷</span>
            <span class="text-xs font-medium text-gray-700">Add Photographer</span>
          </router-link>

          <router-link
            to="/admin/verifications"
            class="flex flex-col items-center p-3 rounded-lg border border-gray-200 hover:border-amber-300 hover:bg-amber-50 transition-colors text-center"
          >
            <span class="text-2xl mb-2">✓</span>
            <span class="text-xs font-medium text-gray-700">Verify Users</span>
          </router-link>

          <router-link
            to="/admin/approvals"
            class="flex flex-col items-center p-3 rounded-lg border border-gray-200 hover:border-amber-300 hover:bg-amber-50 transition-colors text-center"
          >
            <span class="text-2xl mb-2">📋</span>
            <span class="text-xs font-medium text-gray-700">Approvals</span>
          </router-link>
        </div>
      </div>

      <!-- SECTION 4: OPERATIONS PANEL (Critical Items) -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pending Approvals -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
              ⏳ Pending Approvals
            </h3>
            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-bold">
              {{ dashboardData?.pending?.approvals || 0 }}
            </span>
          </div>
          <div class="divide-y divide-gray-200 max-h-72 overflow-y-auto">
            <div
              v-if="!dashboardData?.pending?.approvals_list?.length"
              class="px-6 py-8 text-center text-gray-500"
            >
              No pending approvals
            </div>
            <div
              v-for="item in dashboardData?.pending?.approvals_list?.slice(0, 5)"
              :key="item.id"
              class="px-6 py-3 hover:bg-gray-50 transition-colors flex items-center justify-between"
            >
              <div>
                <p class="text-sm font-medium text-gray-900">
                  {{ item.name }}
                </p>
                <p class="text-xs text-gray-500">
                  {{ item.type }}
                </p>
              </div>
              <button
                class="px-3 py-1 bg-green-500 text-white rounded text-xs font-medium hover:bg-green-600 transition-colors"
                @click="approveItem(item)"
              >
                Approve
              </button>
            </div>
          </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
              📊 Recent Activities
            </h3>
            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-bold">
              {{ dashboardData?.activities?.length || 0 }}
            </span>
          </div>
          <div class="divide-y divide-gray-200 max-h-72 overflow-y-auto">
            <div
              v-if="!dashboardData?.activities?.length"
              class="px-6 py-8 text-center text-gray-500"
            >
              No recent activities
            </div>
            <div
              v-for="activity in dashboardData?.activities?.slice(0, 5)"
              :key="activity.id"
              class="px-6 py-3 hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-start gap-3">
                <div :class="['w-2 h-2 rounded-full mt-2', getActivityColor(activity.type)]" />
                <div class="flex-1">
                  <p class="text-sm text-gray-900">
                    {{ activity.message }}
                  </p>
                  <p class="text-xs text-gray-500 mt-1">
                    {{ formatTime(activity.timestamp) }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- SECTION 5: MODULE GRID (12 Modules) -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">
            📊 Admin Modules
          </h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
          <ModuleCard
            title="Users & Roles"
            description="Manage users and permissions"
            icon="👥"
            :stats="[
              { label: 'Total Users', value: dashboardData?.stats?.total_users || 0 },
              { label: 'Active', value: dashboardData?.stats?.active_users || 0 }
            ]"
            href="/admin/users"
          />

          <ModuleCard
            title="Photographers"
            description="Manage photographer profiles"
            icon="📷"
            :stats="[
              { label: 'Total', value: dashboardData?.stats?.total_photographers || 0 },
              { label: 'Verified', value: dashboardData?.stats?.verified_photographers || 0 }
            ]"
            href="/admin/photographers"
          />

          <ModuleCard
            title="Verifications"
            description="Review pending verifications"
            icon="✓"
            :stats="[
              { label: 'Pending', value: dashboardData?.pending?.verifications || 0 },
              { label: 'Approved', value: dashboardData?.stats?.verified_photographers || 0 }
            ]"
            href="/admin/verifications"
          />

          <ModuleCard
            title="Events"
            description="Manage platform events"
            icon="📅"
            :stats="[
              { label: 'Total', value: dashboardData?.stats?.total_events || 0 },
              { label: 'Upcoming', value: dashboardData?.stats?.upcoming_events || 0 }
            ]"
            href="/admin/events"
          />

          <ModuleCard
            title="Competitions"
            description="Manage competitions"
            icon="🏆"
            :stats="[
              { label: 'Active', value: dashboardData?.stats?.active_competitions || 0 },
              { label: 'Submissions', value: dashboardData?.stats?.competition_submissions || 0 }
            ]"
            href="/admin/competitions"
          />

          <ModuleCard
            title="Judges & Mentors"
            description="Manage judges and mentors"
            icon="🎓"
            :stats="[
              { label: 'Judges', value: dashboardData?.stats?.total_judges || 0 },
              { label: 'Mentors', value: dashboardData?.stats?.total_mentors || 0 }
            ]"
            href="/admin/judges"
          />

          <ModuleCard
            title="Sponsors"
            description="Manage sponsors"
            icon="🎁"
            :stats="[
              { label: 'Active', value: dashboardData?.stats?.active_sponsors || 0 },
              { label: 'Total', value: dashboardData?.stats?.total_sponsors || 0 }
            ]"
            href="/admin/sponsors"
          />

          <ModuleCard
            title="Reviews & Feedback"
            description="Manage reviews and feedback"
            icon="⭐"
            :stats="[
              { label: 'Reviews', value: dashboardData?.stats?.total_reviews || 0 },
              { label: 'Avg Rating', value: `${formatFixed(dashboardData?.stats?.avg_rating, 1, '0.0')}` }
            ]"
            href="/admin/reviews"
          />

          <ModuleCard
            title="Bookings"
            description="Photography bookings"
            icon="💼"
            :stats="[
              { label: 'Total', value: dashboardData?.stats?.total_bookings || 0 },
              { label: 'Pending', value: dashboardData?.stats?.pending_bookings || 0 }
            ]"
            href="/admin/bookings"
          />

          <ModuleCard
            title="Transactions"
            description="Payment transactions"
            icon="💳"
            :stats="[
              { label: 'Revenue', value: formatCurrency(dashboardData?.stats?.total_revenue || 0) },
              { label: 'Count', value: dashboardData?.stats?.revenue_transactions || 0 }
            ]"
            href="/admin/transactions"
          />

          <ModuleCard
            title="SEO & Tracking"
            description="SEO settings and analytics"
            icon="🔍"
            :stats="[
              { label: 'Indexed', value: dashboardData?.stats?.indexed_pages || 0 },
              { label: 'Traffic', value: dashboardData?.stats?.monthly_traffic || 0 }
            ]"
            href="/admin/settings/seo"
          />

          <ModuleCard
            title="System Health"
            description="Monitor system performance"
            icon="⚙️"
            :stats="[
              { label: 'Health', value: `${dashboardData?.stats?.system_health || 0}%` },
              { label: 'Status', value: dashboardData?.stats?.health_status || 'OK' }
            ]"
            href="/admin/system-health"
          />
        </div>
      </div>

      <!-- SECTION 6: ANALYTICS CHARTS -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Booking Trends -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
              📈 Booking Trends
            </h3>
          </div>
          <div class="p-6">
            <div class="h-64 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg flex items-center justify-center">
              <p class="text-gray-500">
                Chart Component Placeholder
              </p>
            </div>
          </div>
        </div>

        <!-- Revenue Analytics -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
              💹 Revenue Analytics
            </h3>
          </div>
          <div class="p-6">
            <div class="h-64 bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg flex items-center justify-center">
              <p class="text-gray-500">
                Chart Component Placeholder
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
  <!-- Time Range & Refresh Controls -->
  <div class="flex justify-end items-center gap-4 mb-6">
    <select
      v-model="timeRange"
      class="px-3 py-2 border border-gray-300 rounded-lg text-sm"
      @change="refreshData"
    >
      <option value="today">
        Today
      </option>
      <option value="week">
        This Week
      </option>
      <option value="month">
        This Month
      </option>
      <option value="year">
        This Year
      </option>
    </select>
    <button
      :disabled="loading"
      class="px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark disabled:opacity-50 flex items-center gap-2"
      @click="refreshData"
    >
      <svg
        v-if="loading"
        class="w-4 h-4 animate-spin"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
        />
      </svg>
      <svg
        v-else
        class="w-4 h-4"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
        />
      </svg>
      Refresh
    </button>
  </div>

  <!-- Loading State -->
  <div
    v-if="loading"
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center"
  >
    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-burgundy" />
    <p class="mt-4 text-gray-600">
      Loading dashboard data...
    </p>
  </div>

  <!-- Error State -->
  <div
    v-else-if="error"
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12"
  >
    <div class="bg-danger-50 border border-danger-200 rounded-lg p-6">
      <h3 class="text-lg font-semibold text-danger-700">
        Failed to Load Dashboard
      </h3>
      <p class="text-danger-700 mt-2">
        {{ error }}
      </p>
      <button
        class="mt-4 btn-admin-danger"
        @click="refreshData"
      >
        Try Again
      </button>
    </div>
  </div>

  <!-- Dashboard Content -->
  <div
    v-else-if="dashboardData"
    class="space-y-6"
  >
    <!-- ⚡ QUICK NAVIGATION - Component Based -->
    <AdminQuickNav />

    <!-- KEY METRICS - Top Priority -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Total Users Card -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-gray-600 text-sm font-medium">
              Total Users
            </p>
            <p class="text-3xl font-bold text-gray-900 mt-2">
              {{ formatNumber(stats.total_users) }}
            </p>
            <p class="text-xs text-gray-500 mt-1">
              {{ stats.active_users || 0 }} active
            </p>
          </div>
          <div class="text-primary-700 bg-primary-50 p-3 rounded-lg">
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
              />
            </svg>
          </div>
        </div>
      </div>

      <!-- Total Photographers Card -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-gray-600 text-sm font-medium">
              Photographers
            </p>
            <p class="text-3xl font-bold text-gray-900 mt-2">
              {{ formatNumber(stats.total_photographers) }}
            </p>
            <p class="text-xs text-gray-500 mt-1">
              {{ stats.verified_photographers || 0 }} verified
            </p>
          </div>
          <div class="text-primary-700 bg-primary-50 p-3 rounded-lg">
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0118.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"
              />
            </svg>
          </div>
        </div>
      </div>

      <!-- Total Bookings Card -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-gray-600 text-sm font-medium">
              Total Bookings
            </p>
            <p class="text-3xl font-bold text-gray-900 mt-2">
              {{ formatNumber(stats.total_bookings) }}
            </p>
            <p class="text-xs text-gray-500 mt-1">
              {{ stats.pending_bookings || 0 }} pending
            </p>
          </div>
          <div class="text-primary-700 bg-primary-50 p-3 rounded-lg">
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
              />
            </svg>
          </div>
        </div>
      </div>

      <!-- Total Revenue Card -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-gray-600 text-sm font-medium">
              Total Revenue
            </p>
            <p class="text-3xl font-bold text-gray-900 mt-2">
              ৳{{ formatNumber(stats.total_revenue) }}
            </p>
            <p class="text-xs text-gray-500 mt-1">
              ৳{{ formatNumber(stats.monthly_revenue || 0) }} this month
            </p>
          </div>
          <div class="text-primary-700 bg-primary-50 p-3 rounded-lg">
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Secondary Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-lg shadow p-4">
        <p class="text-gray-600 text-xs font-medium uppercase">
          Events
        </p>
        <p class="text-2xl font-bold text-gray-900 mt-1">
          {{ formatNumber(stats.total_events) }}
        </p>
        <p class="text-xs text-gray-500 mt-1">
          {{ stats.published_events || 0 }} published
        </p>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <p class="text-gray-600 text-xs font-medium uppercase">
          Competitions
        </p>
        <p class="text-2xl font-bold text-gray-900 mt-1">
          {{ formatNumber(stats.total_competitions) }}
        </p>
        <p class="text-xs text-gray-500 mt-1">
          {{ stats.active_competitions || 0 }} active
        </p>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <p class="text-gray-600 text-xs font-medium uppercase">
          Reviews
        </p>
        <p class="text-2xl font-bold text-primary-700 mt-1">
          {{ parseFloat(stats.avg_rating).toFixed(1) }}★
        </p>
        <p class="text-xs text-gray-500 mt-1">
          {{ stats.total_reviews || 0 }} total
        </p>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <p class="text-gray-600 text-xs font-medium uppercase">
          Active Visitors
        </p>
        <p class="text-2xl font-bold text-gray-900 mt-1">
          {{ formatNumber(stats.active_visitors) }}
        </p>
        <p class="text-xs text-gray-500 mt-1">
          {{ stats.visitors_today || 0 }} today
        </p>
      </div>
    </div>

    <!-- TOP PHOTOGRAPHERS & ACTIVITY -->
    <div class="space-y-6">
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h2 class="text-lg font-semibold text-gray-900">
            Top Rated Photographers
          </h2>
          <router-link
            to="/admin/photographers"
            class="text-burgundy hover:text-burgundy-dark text-sm font-medium"
          >
            View All →
          </router-link>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                  Name
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                  Email
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                  Rating
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                  Reviews
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                  Status
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr
                v-for="photographer in topPhotographers"
                :key="photographer.id"
                class="hover:bg-gray-50"
              >
                <td class="px-6 py-4 text-sm">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-xs font-bold text-primary-700">
                      {{ photographer.user?.name?.charAt(0) || 'U' }}
                    </div>
                    <span class="font-medium">{{ photographer.user?.name || 'Unknown' }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ photographer.user?.email || 'N/A' }}
                </td>
                <td class="px-6 py-4 text-sm font-semibold text-primary-700">
                  {{ parseFloat(photographer.average_rating).toFixed(1) }}★
                </td>
                <td class="px-6 py-4 text-sm">
                  {{ photographer.rating_count }}
                </td>
                <td class="px-6 py-4 text-sm">
                  <span
                    v-if="photographer.is_verified"
                    class="status-active"
                  >
                    Verified
                  </span>
                  <span
                    v-else
                    class="status-pending"
                  >
                    Pending
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Activity Cards -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Bookings -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
              Recent Bookings
            </h3>
          </div>
          <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
            <div
              v-for="booking in recentBookings"
              :key="booking.id"
              class="px-6 py-4 hover:bg-gray-50"
            >
              <p class="font-medium text-sm">
                {{ booking.client?.name || 'N/A' }}
              </p>
              <p class="text-xs text-gray-600 mt-1">
                {{ booking.event_type }} • ৳{{ formatNumber(booking.total_amount) }}
              </p>
              <div class="flex justify-between items-center mt-2">
                <span class="text-xs text-gray-500">{{ formatTimeAgo(booking.created_at) }}</span>
                <span :class="getStatusBadgeClass(booking.status)">
                  {{ capitalizeFirst(booking.status) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Reviews -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
              Recent Reviews
            </h3>
          </div>
          <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
            <div
              v-for="review in recentReviews"
              :key="review.id"
              class="px-6 py-4 hover:bg-gray-50"
            >
              <p class="font-medium text-sm">
                {{ review.booking?.photographer?.user?.name || 'N/A' }}
              </p>
              <p class="text-xs text-primary-700 mt-1 font-semibold">
                {{ review.rating }}★ Rating
              </p>
              <p class="text-xs text-gray-600 mt-1">
                By: {{ review.booking?.client?.name || 'N/A' }}
              </p>
              <p class="text-xs text-gray-500 mt-2">
                {{ formatTimeAgo(review.created_at) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Recent Competitions -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
              Recent Competitions
            </h3>
          </div>
          <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
            <div
              v-for="competition in recentCompetitions"
              :key="competition.id"
              class="px-6 py-4 hover:bg-gray-50"
            >
              <p class="font-medium text-sm truncate">
                {{ competition.title }}
              </p>
              <p class="text-xs text-gray-600 mt-1">
                {{ competition.submissions_count || 0 }} submissions
              </p>
              <div class="flex justify-between items-center mt-2">
                <span class="text-xs text-gray-500">Prize: ৳{{ formatNumber(competition.prize_pool || 0) }}</span>
                <span :class="getCompetitionStatusBadge(competition.status)">
                  {{ capitalizeFirst(competition.status) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ALERTS & PENDING ITEMS -->
    <div
      v-if="hasPendingItems"
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"
    >
      <div
        v-if="stats.pending_bookings > 0"
        class="bg-warning-50 border border-warning-200 rounded-lg p-4"
      >
        <div class="flex items-center justify-between">
          <div>
            <p class="text-warning-700 font-semibold">
              {{ stats.pending_bookings }} Pending Bookings
            </p>
            <p class="text-warning-700 text-sm">
              Awaiting confirmation
            </p>
          </div>
          <router-link
            to="/admin/bookings?status=pending"
            class="text-burgundy hover:text-burgundy-dark"
          >
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5l7 7-7 7"
              />
            </svg>
          </router-link>
        </div>
      </div>
      <div
        v-if="stats.pending_verifications > 0"
        class="bg-warning-50 border border-warning-200 rounded-lg p-4"
      >
        <div class="flex items-center justify-between">
          <div>
            <p class="text-warning-700 font-semibold">
              {{ stats.pending_verifications }} Pending Verifications
            </p>
            <p class="text-warning-700 text-sm">
              Photographer applications
            </p>
          </div>
          <router-link
            to="/admin/verifications?status=pending"
            class="text-burgundy hover:text-burgundy-dark"
          >
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5l7 7-7 7"
              />
            </svg>
          </router-link>
        </div>
      </div>
      <div
        v-if="stats.pending_submissions > 0"
        class="bg-warning-50 border border-warning-200 rounded-lg p-4"
      >
        <div class="flex items-center justify-between">
          <div>
            <p class="text-warning-700 font-semibold">
              {{ stats.pending_submissions }} Pending Submissions
            </p>
            <p class="text-warning-700 text-sm">
              Awaiting review
            </p>
          </div>
          <router-link
            to="/admin/competitions/submissions?status=pending"
            class="text-burgundy hover:text-burgundy-dark"
          >
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5l7 7-7 7"
              />
            </svg>
          </router-link>
        </div>
      </div>
      <div
        v-if="stats.pending_reviews > 0"
        class="bg-warning-50 border border-warning-200 rounded-lg p-4"
      >
        <div class="flex items-center justify-between">
          <div>
            <p class="text-warning-700 font-semibold">
              {{ stats.pending_reviews }} Pending Reviews
            </p>
            <p class="text-warning-700 text-sm">
              Need moderation
            </p>
          </div>
          <router-link
            to="/admin/reviews?status=pending"
            class="text-burgundy hover:text-burgundy-dark"
          >
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5l7 7-7 7"
              />
            </svg>
          </router-link>
        </div>
      </div>
    </div>

    <!-- REVENUE ANALYTICS -->
    <div class="bg-white rounded-lg shadow p-6">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-900">
          💰 Revenue Analytics
        </h3>
        <p class="text-sm text-gray-500">
          Last 12 months
        </p>
      </div>
      <div
        v-if="revenueTrend.length > 0"
        class="space-y-4"
      >
        <div
          v-for="month in revenueTrend"
          :key="month.month"
          class="flex items-center"
        >
          <div class="w-24 text-sm font-medium text-gray-600">
            {{ formatMonth(month.month) }}
          </div>
          <div
            class="flex-1 bg-gray-100 rounded-full h-8 flex items-center ml-2"
            :style="{ width: '100%' }"
          >
            <div
              class="bg-burgundy h-8 rounded-full flex items-center justify-end pr-3"
              :style="{ width: getRevenuePercentage(month.revenue) + '%', minWidth: '5%' }"
            >
              <span class="text-xs font-semibold text-white">৳{{ formatNumber(month.revenue) }}</span>
            </div>
          </div>
        </div>
      </div>
      <div
        v-else
        class="text-center py-8 text-gray-500"
      >
        <p>No revenue data available</p>
      </div>
    </div>

    <!-- PAYMENT METHODS & TRAFFIC SOURCES -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Payment Methods -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          💳 Payment Methods
        </h3>
        <div
          v-if="paymentGateways.length > 0"
          class="space-y-3"
        >
          <div
            v-for="gateway in paymentGateways"
            :key="gateway.payment_method"
            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
          >
            <div>
              <p class="font-medium text-gray-800">
                {{ capitalizeFirst(gateway.payment_method) }}
              </p>
              <p class="text-xs text-gray-600">
                {{ gateway.count }} transactions
              </p>
            </div>
            <div class="text-right">
              <p class="font-semibold text-gray-900">
                ৳{{ formatNumber(gateway.total) }}
              </p>
              <p class="text-xs text-gray-500">
                {{ getPaymentPercentage(gateway.total) }}%
              </p>
            </div>
          </div>
        </div>
        <div
          v-else
          class="text-center py-8 text-gray-500"
        >
          <p>No payment data available</p>
        </div>
      </div>

      <!-- Top Traffic Sources -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          🔗 Traffic Sources
        </h3>
        <div
          v-if="trafficSources.length > 0"
          class="space-y-3"
        >
          <div
            v-for="source in trafficSources"
            :key="source.referrer"
            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
          >
            <div>
              <p class="font-medium text-gray-800 truncate">
                {{ source.referrer || 'Direct' }}
              </p>
              <p class="text-xs text-gray-600">
                {{ source.count }} visits
              </p>
            </div>
            <div class="text-right">
              <p class="text-sm font-semibold text-burgundy">
                {{ getTrafficPercentage(source.count) }}%
              </p>
            </div>
          </div>
        </div>
        <div
          v-else
          class="text-center py-8 text-gray-500"
        >
          <p>No traffic data available</p>
        </div>
      </div>
    </div>

    <!-- DEVICE & PAGE ANALYTICS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Device Breakdown -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          📱 Device Breakdown
        </h3>
        <div
          v-if="deviceBreakdown.length > 0"
          class="space-y-3"
        >
          <div
            v-for="device in deviceBreakdown"
            :key="device.device_type"
            class="flex items-center justify-between"
          >
            <div class="flex items-center gap-3">
              <svg
                v-if="device.device_type === 'mobile'"
                class="w-5 h-5 text-burgundy"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"
                />
              </svg>
              <svg
                v-else-if="device.device_type === 'desktop'"
                class="w-5 h-5 text-gray-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9.75 17L9 20m0 0l-.75 3M9 20a.75.75 0 001.5 0m0 0a.75.75 0 00-1.5 0m0 0L3 13m5.25-4L15 4m-6-1h.008v.008H9V3m6 0h.008v.008H15V3"
                />
              </svg>
              <svg
                v-else
                class="w-5 h-5 text-burgundy"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"
                />
              </svg>
              <div>
                <p class="font-medium text-gray-800">
                  {{ capitalizeFirst(device.device_type) }}
                </p>
                <p class="text-xs text-gray-600">
                  {{ device.count }} visits
                </p>
              </div>
            </div>
            <p class="text-sm font-semibold text-gray-900">
              {{ getDevicePercentage(device.count) }}%
            </p>
          </div>
        </div>
        <div
          v-else
          class="text-center py-8 text-gray-500"
        >
          <p>No device data available</p>
        </div>
      </div>

      <!-- Top Pages -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          📄 Top Pages
        </h3>
        <div
          v-if="topPages.length > 0"
          class="space-y-2"
        >
          <div
            v-for="(page, index) in topPages"
            :key="page.page_title"
            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
          >
            <div>
              <p class="font-medium text-gray-800 truncate text-sm">
                {{ index + 1 }}. {{ page.page_title }}
              </p>
            </div>
            <div class="text-right">
              <p class="text-sm font-semibold text-gray-900">
                {{ page.views }}
              </p>
              <p class="text-xs text-gray-500">
                views
              </p>
            </div>
          </div>
        </div>
        <div
          v-else
          class="text-center py-8 text-gray-500"
        >
          <p>No page data available</p>
        </div>
      </div>
    </div>

    <!-- TRANSACTIONS TABLE -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-900">
          Recent Transactions
        </h2>
        <router-link
          to="/admin/transactions"
          class="text-burgundy hover:text-burgundy-dark text-sm font-medium"
        >
          View All →
        </router-link>
      </div>
      <div
        v-if="recentTransactions.length > 0"
        class="overflow-x-auto"
      >
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                User
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                Amount
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                Method
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                Date
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr
              v-for="transaction in recentTransactions"
              :key="transaction.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4 text-sm">
                <div>
                  <p class="font-medium text-gray-900">
                    {{ transaction.user_name }}
                  </p>
                  <p class="text-xs text-gray-500">
                    {{ transaction.user_email }}
                  </p>
                </div>
              </td>
              <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                ৳{{ formatNumber(transaction.amount) }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">
                {{ capitalizeFirst(transaction.payment_method || 'N/A') }}
              </td>
              <td class="px-6 py-4 text-sm">
                <span :class="getTransactionStatusClass(transaction.status)">
                  {{ capitalizeFirst(transaction.status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">
                {{ formatTimeAgo(transaction.created_at) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div
        v-else
        class="p-6 text-center text-gray-500"
      >
        <p>No transactions available</p>
      </div>
    </div>

    <!-- ACTIVITY LOGS -->
    <div class="bg-white rounded-lg shadow p-6">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">
          📋 Recent Activity Logs
        </h3>
        <router-link
          to="/admin/activity-logs"
          class="text-burgundy hover:text-burgundy-dark text-sm font-medium"
        >
          View All →
        </router-link>
      </div>
      <div
        v-if="recentActivityLogs.length > 0"
        class="space-y-3 max-h-96 overflow-y-auto"
      >
        <div
          v-for="log in recentActivityLogs"
          :key="log.id"
          class="flex items-start gap-3 p-3 hover:bg-gray-50 rounded-lg border-l-4"
          :class="getActivityLogBorderColor(log.action)"
        >
          <div class="pt-1">
            <svg
              class="w-4 h-4 text-gray-400"
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
          <div class="flex-1">
            <p class="font-medium text-gray-900 text-sm">
              {{ log.user_name }}
            </p>
            <p class="text-xs text-gray-600 mt-1">
              {{ log.description || log.action }}
            </p>
            <p class="text-xs text-gray-400 mt-1">
              {{ formatTimeAgo(log.created_at) }}
            </p>
          </div>
        </div>
      </div>
      <div
        v-else
        class="text-center py-8 text-gray-500"
      >
        <p>No activity logs available</p>
      </div>
    </div>

    <!-- TOP PHOTOGRAPHERS BY BOOKINGS -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-900">
          ⭐ Most Booked Photographers
        </h2>
        <router-link
          to="/admin/photographers?sort=bookings"
          class="text-burgundy hover:text-burgundy-dark text-sm font-medium"
        >
          View All →
        </router-link>
      </div>
      <div
        v-if="topPhotographersByBookings.length > 0"
        class="overflow-x-auto"
      >
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                Rank
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                Photographer
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                Bookings
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                Rating
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                Status
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr
              v-for="(photographer, index) in topPhotographersByBookings"
              :key="photographer.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4">
                <div class="w-8 h-8 rounded-full bg-gradient-to-r from-primary-600 to-primary-800 flex items-center justify-center text-white font-bold text-sm">
                  {{ index + 1 }}
                </div>
              </td>
              <td class="px-6 py-4 text-sm">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-xs font-bold text-primary-700">
                    {{ photographer.user?.name?.charAt(0) || 'P' }}
                  </div>
                  <span class="font-medium">{{ photographer.user?.name || 'Unknown' }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm font-semibold text-primary-700">
                {{ photographer.total_bookings || 0 }}
              </td>
              <td class="px-6 py-4 text-sm font-semibold text-primary-700">
                {{ parseFloat(photographer.average_rating || 0).toFixed(1) }}★
              </td>
              <td class="px-6 py-4 text-sm">
                <span
                  v-if="photographer.is_verified"
                  class="status-active"
                >
                  Verified
                </span>
                <span
                  v-else
                  class="status-pending"
                >
                  Pending
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div
        v-else
        class="p-6 text-center text-gray-500"
      >
        <p>No photographer data available</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue';
import AdminLayout from './AdminLayout.vue';
import KPICard from './dashboard/KPICard.vue';
import ModuleCard from './dashboard/ModuleCard.vue';
import { formatDate, formatFixed, formatMonthYear } from '../utils/formatters';

// State
const isLoading = ref(false);
const timeRange = ref('month');
const dashboardData = ref(null);

// Injected methods
const addAlert = inject('addAlert', null);

// Fetch dashboard data
const fetchDashboardData = async () => {
  isLoading.value = true;
  try {
    const response = await fetch(
      `/api/v1/admin/dashboard?range=${timeRange.value}`,
      {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      }
    );

    if (response.ok) {
      dashboardData.value = await response.json();
    } else {
      throw new Error('Failed to fetch dashboard data');
    }
  } catch (error) {
    console.error('Error fetching dashboard:', error);
    if (addAlert) {
      addAlert('Failed to load dashboard data', 'error', 5000);
    }
  } finally {
    isLoading.value = false;
  }
};

// Refresh data
const refreshData = async () => {
  await fetchDashboardData();
  if (addAlert) {
    addAlert('Dashboard refreshed successfully', 'success', 3000);
  }
};

// Export data
const exportData = () => {
  const data = JSON.stringify(dashboardData.value, null, 2);
  const element = document.createElement('a');
  element.setAttribute(
    'href',
    'data:text/plain;charset=utf-8,' + encodeURIComponent(data)
  );
  element.setAttribute('download', `dashboard-${timeRange.value}-${new Date().toISOString().split('T')[0]}.json`);
  element.style.display = 'none';
  document.body.appendChild(element);
  element.click();
  document.body.removeChild(element);
};

// Approve item
const approveItem = async (item) => {
  try {
    const response = await fetch(
      `/api/v1/admin/${item.type}/${item.id}/approve`,
      {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      }
    );

    if (response.ok) {
      if (addAlert) {
        addAlert(`${item.type} approved successfully`, 'success', 3000);
      }
      await fetchDashboardData();
    }
  } catch (error) {
    console.error('Error approving item:', error);
    if (addAlert) {
      addAlert(`Failed to approve ${item.type}`, 'error', 5000);
    }
  }
};

// Format currency
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-BD', {
    style: 'currency',
    currency: 'BDT',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount);
};

// Format time
const formatTime = (timestamp) => {
  if (!timestamp) return '';
  const date = new Date(timestamp);
  const now = new Date();
  const diff = now - date;
  const minutes = Math.floor(diff / 60000);
  const hours = Math.floor(diff / 3600000);
  const days = Math.floor(diff / 86400000);

  if (minutes < 1) return 'Just now';
  if (minutes < 60) return `${minutes}m ago`;
  if (hours < 24) return `${hours}h ago`;
  if (days < 7) return `${days}d ago`;
  return formatDate(date);
};

// Get activity color
const getActivityColor = (type) => {
  const colors = {
    success: 'bg-green-500',
    warning: 'bg-yellow-500',
    error: 'bg-red-500',
    info: 'bg-blue-500'
  };
  return colors[type] || 'bg-gray-500';
};

// Lifecycle
onMounted(() => {
  fetchDashboardData();
});
</script>

<style scoped>
.dashboard-grid {
  display: grid;
  gap: 1.5rem;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
}

@media (max-width: 768px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
}
</style>

    const stats = computed(() => {
      return dashboardData.value?.stats || {
        total_users: 0,
        total_photographers: 0,
        total_revenue: 0,
        total_bookings: 0,
        pending_bookings: 0,
        total_competitions: 0,
        active_competitions: 0,
        total_submissions: 0,
        pending_submissions: 0,
        active_visitors: 0,
        page_views_today: 0,
        verified_photographers: 0,
        active_users: 0,
        visitors_today: 0,
        monthly_revenue: 0,
      };
    });

    const topPhotographers = computed(() => {
      return dashboardData.value?.top_photographers?.slice(0, 5) || [];
    });

    const recentBookings = computed(() => {
      return dashboardData.value?.recent_bookings?.slice(0, 5) || [];
    });

    const recentReviews = computed(() => {
      return dashboardData.value?.recent_reviews?.slice(0, 5) || [];
    });

    const recentCompetitions = computed(() => {
      return dashboardData.value?.recent_competitions?.slice(0, 5) || [];
    });

    const platformHealth = computed(() => {
      return dashboardData.value?.platform_health || {};
    });

    // NEW COMPUTED PROPERTIES FOR ENHANCED DASHBOARD
    const revenueTrend = computed(() => {
      return dashboardData.value?.revenue_trend || [];
    });

    const userGrowth = computed(() => {
      return dashboardData.value?.user_growth || [];
    });

    const bookingStats = computed(() => {
      return dashboardData.value?.booking_stats || [];
    });

    const paymentGateways = computed(() => {
      return dashboardData.value?.payment_gateways || [];
    });

    const topPhotographersByBookings = computed(() => {
      return dashboardData.value?.top_photographers_by_bookings || [];
    });

    const recentTransactions = computed(() => {
      return dashboardData.value?.recent_transactions?.slice(0, 15) || [];
    });

    const recentActivityLogs = computed(() => {
      return dashboardData.value?.recent_activity_logs?.slice(0, 10) || [];
    });

    const visitorAnalytics = computed(() => {
      return dashboardData.value?.visitor_analytics || {};
    });

    const deviceBreakdown = computed(() => {
      return visitorAnalytics.value?.device_breakdown || [];
    });

    const topPages = computed(() => {
      return visitorAnalytics.value?.top_pages?.slice(0, 8) || [];
    });

    const trafficSources = computed(() => {
      return visitorAnalytics.value?.traffic_sources?.slice(0, 8) || [];
    });

    const hasPendingItems = computed(() => {
      return (stats.value.pending_bookings > 0 || 
              stats.value.pending_verifications > 0 || 
              stats.value.pending_submissions > 0 || 
              stats.value.pending_reviews > 0);
    });

    const fetchDashboardData = async () => {
      try {
        loading.value = true;
        error.value = '';

        const token = localStorage.getItem('auth_token');
        if (!token) {
          error.value = 'Authentication token not found. Please login first.';
          loading.value = false;
          return;
        }

        const response = await fetch('/api/v1/admin/dashboard', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
          },
        });

        const data = await response.json();

        if (response.ok && data.status === 'success') {
          dashboardData.value = data.data;
        } else {
          error.value = data.message || 'Failed to load dashboard';
        }
      } catch (err) {
        console.error('Dashboard error:', err);
        error.value = 'Network error. Please check your connection.';
      } finally {
        loading.value = false;
      }
    };

    const refreshData = () => {
      fetchDashboardData();
    };

    const formatNumber = (num) => {
      if (num === null || num === undefined || isNaN(num)) return '0';
      return new Intl.NumberFormat('en-BD').format(num);
    };

    const formatTimeAgo = (date) => {
      if (!date) return '';
      const now = new Date();
      const then = new Date(date);
      const diff = now - then;
      const seconds = Math.floor(diff / 1000);
      const minutes = Math.floor(seconds / 60);
      const hours = Math.floor(minutes / 60);
      const days = Math.floor(hours / 24);

      if (seconds < 60) return 'Just now';
      if (minutes < 60) return `${minutes}m ago`;
      if (hours < 24) return `${hours}h ago`;
      if (days < 7) return `${days}d ago`;
      return formatDate(then);
    };

    const capitalizeFirst = (str) => {
      if (!str) return '';
      return str.charAt(0).toUpperCase() + str.slice(1);
    };

    const getStatusBadgeClass = (status) => {
      const classes = {
        confirmed: 'status-approved',
        pending: 'status-pending',
        cancelled: 'status-cancelled',
        completed: 'status-approved',
      };
      return classes[status] || 'badge bg-gray-100 text-gray-700';
    };

    const getCompetitionStatusBadge = (status) => {
      const classes = {
        active: 'status-active',
        draft: 'status-draft',
        upcoming: 'badge badge-primary',
        judging: 'badge badge-warning',
        completed: 'badge badge-success',
        cancelled: 'status-cancelled',
      };
      return classes[status] || 'badge bg-gray-100 text-gray-700';
    };

    // NEW HELPER FUNCTIONS
    const formatMonth = (monthStr) => {
      if (!monthStr) return '';
      const date = new Date(monthStr + '-01');
      return formatMonthYear(date);
    };

    const getMaxRevenue = computed(() => {
      if (revenueTrend.value.length === 0) return 1;
      return Math.max(...revenueTrend.value.map(m => m.revenue || 0));
    });

    const getRevenuePercentage = (revenue) => {
      const max = getMaxRevenue.value;
      if (max === 0) return 0;
      return Math.round((revenue / max) * 100);
    };

    const getTotalPayments = computed(() => {
      return paymentGateways.value.reduce((sum, g) => sum + (g.total || 0), 0);
    });

    const getPaymentPercentage = (amount) => {
      const total = getTotalPayments.value;
      if (total === 0) return 0;
      return Math.round((amount / total) * 100);
    };

    const getTotalTraffic = computed(() => {
      return trafficSources.value.reduce((sum, s) => sum + (s.count || 0), 0);
    });

    const getTrafficPercentage = (count) => {
      const total = getTotalTraffic.value;
      if (total === 0) return 0;
      return Math.round((count / total) * 100);
    };

    const getTotalDevices = computed(() => {
      return deviceBreakdown.value.reduce((sum, d) => sum + (d.count || 0), 0);
    });

    const getDevicePercentage = (count) => {
      const total = getTotalDevices.value;
      if (total === 0) return 0;
      return Math.round((count / total) * 100);
    };

    const getTransactionStatusClass = (status) => {
      const classes = {
        completed: 'badge badge-success',
        pending: 'badge badge-warning',
        failed: 'badge badge-danger',
        refunded: 'badge badge-info',
      };
      return classes[status] || 'badge bg-gray-100 text-gray-700';
    };

    const getActivityLogBorderColor = (action) => {
      const colors = {
        'create': 'border-success-200',
        'update': 'border-primary-200',
        'delete': 'border-danger-200',
        'verify': 'border-warning-200',
        'approved': 'border-success-200',
        'rejected': 'border-danger-200',
      };
      
      // Check if action contains any of the keywords
      for (const [key, color] of Object.entries(colors)) {
        if (action?.toLowerCase().includes(key)) return color;
      }
      return 'border-gray-300';
    };

    onMounted(() => {
      fetchDashboardData();
    });

    return {
      loading,
      error,
      dashboardData,
      stats,
      timeRange,
      topPhotographers,
      recentBookings,
      recentReviews,
      recentCompetitions,
      platformHealth,
      fetchDashboardData,
      refreshData,
      formatNumber,
      formatTimeAgo,
      capitalizeFirst,
      getStatusBadgeClass,
      getCompetitionStatusBadge,
      // NEW PROPERTIES AND METHODS
      revenueTrend,
      userGrowth,
      bookingStats,
      paymentGateways,
      topPhotographersByBookings,
      recentTransactions,
      recentActivityLogs,
      visitorAnalytics,
      deviceBreakdown,
      topPages,
      trafficSources,
      hasPendingItems,
      formatMonth,
      getRevenuePercentage,
      getPaymentPercentage,
      getTrafficPercentage,
      getDevicePercentage,
      getTransactionStatusClass,
      getActivityLogBorderColor,
    };
  },
};
</script>

<style scoped>
.admin-dashboard {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  background-color: #f8fafc;
}

/* International standard section styling */
.dashboard-section {
  scroll-margin-top: 80px;
}

/* Better card styling */
.stat-card {
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

/* Table improvements */
table tbody tr {
  transition: background-color 0.2s ease;
}

table tbody tr:hover {
  background-color: rgba(59, 130, 246, 0.05);
}

/* Badge styling */
.status-badge {
  font-weight: 500;
  letter-spacing: 0.5px;
}

/* Section title styling */
.section-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

/* Bar chart styling */
.revenue-bar {
  transition: width 0.3s ease;
}

/* Responsive grid */
@media (max-width: 768px) {
  .dashboard-section {
    margin-bottom: 1.5rem;
  }
}
</style>
