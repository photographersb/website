<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <button
              class="text-gray-600 hover:text-gray-900"
              @click="$router.back()"
            >
              <svg
                class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 19l-7-7 7-7"
                />
              </svg>
            </button>
            <h1 class="text-2xl font-bold text-gray-900">
              Featured Photographer Analytics
            </h1>
          </div>
          <button
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2"
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
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
              />
            </svg>
            Export
          </button>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Time Period Selector -->
      <div class="mb-8 flex gap-4">
        <button
          v-for="period in [7, 30, 90, 365]"
          :key="period"
          :class="[
            'px-4 py-2 rounded-lg font-semibold transition-all',
            selectedDays === period
              ? 'bg-burgundy-600 text-white'
              : 'bg-white text-gray-700 border border-gray-300 hover:border-burgundy-600'
          ]"
          @click="selectedDays = period"
        >
          Last {{ period }} days
        </button>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <!-- Total Views -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-gray-600 font-semibold">
              Total Views
            </h3>
            <svg
              class="w-5 h-5 text-blue-600"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
              <path
                fill-rule="evenodd"
                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <p class="text-3xl font-bold text-gray-900">
            {{ formatNumber(analytics?.summary?.total_views || 0) }}
          </p>
          <p class="text-sm text-gray-500 mt-2">
            {{ analytics?.summary?.avg_daily_views ? formatFixed(analytics.summary.avg_daily_views, 1, '0.0') : '0.0' }} avg/day
          </p>
        </div>

        <!-- Profile Clicks -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-gray-600 font-semibold">
              Profile Clicks
            </h3>
            <svg
              class="w-5 h-5 text-purple-600"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <p class="text-3xl font-bold text-gray-900">
            {{ formatNumber(analytics?.summary?.total_profile_clicks || 0) }}
          </p>
          <p class="text-sm text-gray-500 mt-2">
            {{ ctr }}% click rate
          </p>
        </div>

        <!-- Portfolio Clicks -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-gray-600 font-semibold">
              Portfolio Views
            </h3>
            <svg
              class="w-5 h-5 text-green-600"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" />
            </svg>
          </div>
          <p class="text-3xl font-bold text-gray-900">
            {{ formatNumber(analytics?.summary?.total_portfolio_clicks || 0) }}
          </p>
          <p class="text-sm text-gray-500 mt-2">
            {{ portfolioCTR }}% engagement
          </p>
        </div>

        <!-- Inquiries -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-gray-600 font-semibold">
              Inquiries
            </h3>
            <svg
              class="w-5 h-5 text-amber-600"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
              <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
            </svg>
          </div>
          <p class="text-3xl font-bold text-gray-900">
            {{ formatNumber(analytics?.summary?.total_inquiries || 0) }}
          </p>
          <p class="text-sm text-gray-500 mt-2">
            {{ inquiryRate }}% conversion
          </p>
        </div>

        <!-- Bookings -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-gray-600 font-semibold">
              Bookings
            </h3>
            <svg
              class="w-5 h-5 text-rose-600"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M6 2a1 1 0 00-1 1v2H4a2 2 0 00-2 2v2h16V7a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v2H7V3a1 1 0 00-1-1zm0 5a2 2 0 002 2h8a2 2 0 002-2H6z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <p class="text-3xl font-bold text-gray-900">
            {{ formatNumber(analytics?.summary?.total_bookings || 0) }}
          </p>
          <p class="text-sm text-gray-500 mt-2">
            {{ bookingRate }}% booking rate
          </p>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Views Trend Chart -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-lg font-bold text-gray-900 mb-4">
            Views Trend
          </h2>
          <div class="h-80">
            <canvas ref="viewsChart" />
          </div>
        </div>

        <!-- Conversion Funnel -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-lg font-bold text-gray-900 mb-4">
            Conversion Funnel
          </h2>
          <div class="space-y-4">
            <div>
              <div class="flex justify-between mb-2">
                <span class="text-sm font-semibold text-gray-700">Views</span>
                <span class="text-sm font-semibold text-gray-900">{{ analytics?.summary?.total_views || 0 }}</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-3">
                <div
                  class="bg-blue-600 h-3 rounded-full"
                  style="width: 100%"
                />
              </div>
            </div>

            <div>
              <div class="flex justify-between mb-2">
                <span class="text-sm font-semibold text-gray-700">Profile Clicks</span>
                <span class="text-sm font-semibold text-gray-900">{{ analytics?.summary?.total_profile_clicks || 0 }} ({{ ctr }}%)</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-3">
                <div
                  class="bg-purple-600 h-3 rounded-full"
                  :style="{ width: (ctr || 0) + '%' }"
                />
              </div>
            </div>

            <div>
              <div class="flex justify-between mb-2">
                <span class="text-sm font-semibold text-gray-700">Inquiries</span>
                <span class="text-sm font-semibold text-gray-900">{{ analytics?.summary?.total_inquiries || 0 }} ({{ inquiryRate }}%)</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-3">
                <div
                  class="bg-amber-600 h-3 rounded-full"
                  :style="{ width: (inquiryRate || 0) + '%' }"
                />
              </div>
            </div>

            <div>
              <div class="flex justify-between mb-2">
                <span class="text-sm font-semibold text-gray-700">Bookings</span>
                <span class="text-sm font-semibold text-gray-900">{{ analytics?.summary?.total_bookings || 0 }} ({{ bookingRate }}%)</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-3">
                <div
                  class="bg-rose-600 h-3 rounded-full"
                  :style="{ width: (bookingRate || 0) + '%' }"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Days Table -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">
          Top Performing Days
        </h2>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b-2 border-gray-200">
                <th class="text-left px-4 py-2 text-gray-700 font-semibold">
                  Date
                </th>
                <th class="text-left px-4 py-2 text-gray-700 font-semibold">
                  Views
                </th>
                <th class="text-left px-4 py-2 text-gray-700 font-semibold">
                  Profile Clicks
                </th>
                <th class="text-left px-4 py-2 text-gray-700 font-semibold">
                  Inquiries
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="day in topDays"
                :key="day.date"
                class="border-b border-gray-200 hover:bg-gray-50"
              >
                <td class="px-4 py-3 text-gray-900">
                  {{ formatDate(day.date) }}
                </td>
                <td class="px-4 py-3 text-gray-900 font-semibold">
                  {{ day.views }}
                </td>
                <td class="px-4 py-3 text-gray-900">
                  {{ day.profile_clicks }}
                </td>
                <td class="px-4 py-3 text-gray-900">
                  {{ day.inquiry_messages }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/api'
import Chart from 'chart.js/auto'
import { formatDate as formatDateValue, formatFixed } from '@/utils/formatters'

const route = useRoute()
const selectedDays = ref(30)
const analytics = ref(null)
const viewsChart = ref(null)
let chart = null

const featured = computed(() => analytics.value?.featured_photographer)
const trendData = computed(() => analytics.value?.trend_data || [])
const topDays = computed(() => analytics.value?.top_days || [])

const ctr = computed(() => {
  if (!analytics.value?.summary?.total_views) return 0
  return ((analytics.value.summary.total_profile_clicks / analytics.value.summary.total_views) * 100).toFixed(2)
})

const portfolioCTR = computed(() => {
  if (!analytics.value?.summary?.total_views) return 0
  return ((analytics.value.summary.total_portfolio_clicks / analytics.value.summary.total_views) * 100).toFixed(2)
})

const inquiryRate = computed(() => {
  if (!analytics.value?.summary?.total_views) return 0
  return ((analytics.value.summary.total_inquiries / analytics.value.summary.total_views) * 100).toFixed(2)
})

const bookingRate = computed(() => {
  if (!analytics.value?.summary?.total_views) return 0
  return ((analytics.value.summary.total_bookings / analytics.value.summary.total_views) * 100).toFixed(2)
})

const fetchAnalytics = async () => {
  try {
    const response = await api.get(
      `/featured-photographers/analytics/${route.params.id}?days=${selectedDays.value}`
    )
    analytics.value = response.data.data
    renderChart()
  } catch (error) {
    console.error('Error fetching analytics:', error)
  }
}

const renderChart = () => {
  if (!viewsChart.value || !trendData.value.length) return

  if (chart) {
    chart.destroy()
  }

  const ctx = viewsChart.value.getContext('2d')
  chart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: trendData.value.map(d => formatDate(d.date)),
      datasets: [
        {
          label: 'Views',
          data: trendData.value.map(d => d.views),
          borderColor: '#3b82f6',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          tension: 0.4,
          fill: true,
        },
        {
          label: 'Profile Clicks',
          data: trendData.value.map(d => d.profile_clicks),
          borderColor: '#8b5cf6',
          backgroundColor: 'rgba(139, 92, 246, 0.1)',
          tension: 0.4,
        },
        {
          label: 'Inquiries',
          data: trendData.value.map(d => d.inquiry_messages),
          borderColor: '#f59e0b',
          backgroundColor: 'rgba(245, 158, 11, 0.1)',
          tension: 0.4,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
        },
      },
      scales: {
        y: {
          beginAtZero: true,
        },
      },
    },
  })
}

const formatDate = (date) => {
  return formatDateValue(date)
}

const formatNumber = (value) => Number(value || 0).toLocaleString()

const exportData = async () => {
  try {
    const response = await api.get(
      `/featured-photographers/analytics/${route.params.id}/export?days=${selectedDays.value}`,
      { responseType: 'blob' }
    )
    const url = window.URL.createObjectURL(response.data)
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `analytics-${route.params.id}.csv`)
    document.body.appendChild(link)
    link.click()
  } catch (error) {
    console.error('Error exporting data:', error)
  }
}

watch(selectedDays, () => {
  fetchAnalytics()
})

onMounted(() => {
  fetchAnalytics()
})
</script>

<style scoped>
/* Filters for grid */
</style>
