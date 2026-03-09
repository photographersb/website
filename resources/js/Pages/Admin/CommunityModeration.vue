<template>
  <div class="min-h-screen bg-gray-50 p-4 sm:p-6">
    <div class="max-w-7xl mx-auto space-y-6">
      <div class="bg-white border border-gray-100 rounded-2xl p-4 sm:p-6">
        <h1 class="text-2xl font-semibold font-serif text-gray-900">Community Moderation</h1>
        <p class="text-sm text-gray-600 mt-1">Manage featured discussions and review community reports.</p>
      </div>

      <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <article class="bg-white border border-gray-100 rounded-2xl p-4 sm:p-5">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold text-gray-900">Feature Discussions</h2>
            <button class="text-xs text-rose-700 hover:underline" @click="loadDiscussions">Refresh</button>
          </div>

          <div class="space-y-3 max-h-[520px] overflow-auto pr-1">
            <div v-for="item in discussions" :key="item.id" class="border border-gray-100 rounded-xl p-3">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="text-sm font-semibold text-gray-900">{{ item.title }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ item.user?.name || 'Unknown' }} • {{ item.category }} • {{ item.status }}</p>
                </div>
                <span :class="item.is_featured ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700'" class="text-[11px] px-2 py-1 rounded-full">
                  {{ item.is_featured ? 'Featured' : 'Normal' }}
                </span>
              </div>
              <p class="text-xs text-gray-600 mt-2 line-clamp-2">{{ item.content }}</p>
              <button
                class="mt-3 text-xs px-3 py-1 rounded bg-gray-900 text-white hover:bg-black"
                @click="toggleFeatured(item)"
              >
                {{ item.is_featured ? 'Unfeature' : 'Feature' }}
              </button>
            </div>
          </div>
        </article>

        <article class="bg-white border border-gray-100 rounded-2xl p-4 sm:p-5">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold text-gray-900">Reported Content</h2>
            <button class="text-xs text-rose-700 hover:underline" @click="loadReports">Refresh</button>
          </div>

          <div class="space-y-3 max-h-[520px] overflow-auto pr-1">
            <div v-for="report in reports" :key="report.id" class="border border-gray-100 rounded-xl p-3">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="text-sm font-semibold text-gray-900">{{ report.reason }}</p>
                  <p class="text-xs text-gray-500">Reporter: {{ report.reporter?.name || 'Unknown' }} • {{ report.status }}</p>
                </div>
                <span :class="statusClass(report.status)" class="text-[11px] px-2 py-1 rounded-full">{{ report.status }}</span>
              </div>
              <p class="text-xs text-gray-600 mt-2 line-clamp-2">{{ report.details || 'No details provided.' }}</p>

              <div v-if="report.status === 'pending'" class="flex gap-2 mt-3">
                <button class="text-xs px-3 py-1 rounded bg-emerald-600 text-white hover:bg-emerald-700" @click="resolveReport(report.id, 'resolved')">Resolve</button>
                <button class="text-xs px-3 py-1 rounded bg-gray-700 text-white hover:bg-gray-800" @click="resolveReport(report.id, 'dismissed')">Dismiss</button>
              </div>
            </div>
          </div>
        </article>
      </section>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import api from '../../api'

const discussions = ref([])
const reports = ref([])

const loadDiscussions = async () => {
  const { data } = await api.get('/admin/community/moderation/discussions', { params: { per_page: 40 } })
  discussions.value = data?.data || []
}

const loadReports = async () => {
  const { data } = await api.get('/admin/community/moderation/reports', { params: { per_page: 40 } })
  reports.value = data?.data || []
}

const toggleFeatured = async (discussion) => {
  await api.post(`/admin/community/moderation/feature/discussions/${discussion.id}`, {
    is_featured: !discussion.is_featured,
  })
  await loadDiscussions()
}

const resolveReport = async (reportId, status) => {
  await api.post(`/admin/community/moderation/reports/${reportId}/resolve`, { status })
  await loadReports()
}

const statusClass = (status) => {
  if (status === 'resolved') return 'bg-emerald-100 text-emerald-800'
  if (status === 'dismissed') return 'bg-gray-200 text-gray-700'
  return 'bg-amber-100 text-amber-800'
}

onMounted(async () => {
  await Promise.all([loadDiscussions(), loadReports()])
})
</script>
