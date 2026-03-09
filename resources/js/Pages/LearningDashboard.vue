<template>
  <div class="p-4 sm:p-6 lg:p-8 space-y-6">
    <header>
      <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900">Learning Dashboard</h1>
      <p class="text-sm text-gray-600 mt-1">Track progress, completed courses, and certificates.</p>
    </header>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
      <article v-for="card in cards" :key="card.label" class="bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-xs uppercase tracking-wide text-gray-500">{{ card.label }}</p>
        <p class="text-2xl font-semibold text-gray-900 mt-1">{{ card.value }}</p>
      </article>
    </section>

    <section class="bg-white rounded-xl border border-gray-200 p-4">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-semibold text-gray-900">My Enrollments</h2>
        <router-link to="/learn" class="text-xs text-rose-700 hover:underline">Browse courses</router-link>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full min-w-[680px] text-left text-sm">
          <thead>
            <tr class="border-b border-gray-200 text-gray-600">
              <th class="py-2 pr-2">Course</th>
              <th class="py-2 pr-2">Level</th>
              <th class="py-2 pr-2">Status</th>
              <th class="py-2 pr-2">Progress</th>
              <th class="py-2 pr-2">Certificate</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in enrollments" :key="item.id" class="border-b border-gray-100">
              <td class="py-2 pr-2">{{ item.course?.title || 'Course' }}</td>
              <td class="py-2 pr-2 capitalize">{{ item.course?.difficulty_level || '—' }}</td>
              <td class="py-2 pr-2 capitalize">{{ item.status }}</td>
              <td class="py-2 pr-2">{{ Number(item.completion_percentage || 0).toFixed(0) }}%</td>
              <td class="py-2 pr-2">{{ item.certificate_id ? 'Issued' : '—' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import api from '../api'

const summary = ref({
  active_courses: 0,
  completed_courses: 0,
  certificates_earned: 0,
  avg_completion: 0,
})

const enrollments = ref([])

const cards = computed(() => [
  { label: 'Active Courses', value: summary.value.active_courses || 0 },
  { label: 'Completed Courses', value: summary.value.completed_courses || 0 },
  { label: 'Certificates', value: summary.value.certificates_earned || 0 },
  { label: 'Avg Completion', value: `${Number(summary.value.avg_completion || 0).toFixed(0)}%` },
])

onMounted(async () => {
  try {
    const { data } = await api.get('/learn/dashboard')
    const payload = data?.data || {}
    summary.value = payload.summary || summary.value
    enrollments.value = payload.enrollments || []
  } catch (error) {
    console.error('Failed to load learning dashboard', error)
  }
})
</script>
