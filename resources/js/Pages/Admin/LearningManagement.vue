<template>
  <div class="p-4 sm:p-6 lg:p-8 space-y-6">
    <header>
      <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900">Learning Management</h1>
      <p class="text-sm text-gray-600 mt-1">Manage courses, instructors, and course reviews.</p>
    </header>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
      <article v-for="card in cards" :key="card.label" class="bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-xs uppercase tracking-wide text-gray-500">{{ card.label }}</p>
        <p class="text-2xl font-semibold text-gray-900 mt-1">{{ card.value }}</p>
      </article>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-2 gap-4">
      <article class="bg-white rounded-xl border border-gray-200 p-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Top Courses</h2>
        <div class="space-y-2">
          <div v-for="course in topCourses" :key="course.id" class="border border-gray-100 rounded-lg p-3 text-sm">
            <p class="font-medium text-gray-900">{{ course.title }}</p>
            <p class="text-xs text-gray-500">{{ course.instructor?.name || 'Unknown' }} • {{ course.enrollments_count || 0 }} enrollments • {{ Number(course.average_rating || 0).toFixed(1) }}★</p>
            <div class="flex items-center gap-2 mt-2">
              <select v-model="course.status" class="rounded border border-gray-300 px-2 py-1 text-xs">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
              </select>
              <label class="text-xs text-gray-600 inline-flex items-center gap-1">
                <input type="checkbox" v-model="course.is_featured" />
                Featured
              </label>
              <button class="text-xs px-2 py-1 rounded bg-gray-900 text-white" @click="saveCourse(course)">Save</button>
            </div>
          </div>
        </div>
      </article>

      <article class="bg-white rounded-xl border border-gray-200 p-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Instructor Approvals</h2>
        <div class="space-y-2">
          <div v-for="item in instructors" :key="item.id" class="border border-gray-100 rounded-lg p-3 text-sm">
            <p class="font-medium text-gray-900">{{ item.user?.name }}</p>
            <p class="text-xs text-gray-500">Approved: {{ item.is_approved ? 'Yes' : 'No' }} • Active: {{ item.is_active ? 'Yes' : 'No' }}</p>
            <button class="mt-2 text-xs px-2 py-1 rounded bg-rose-600 text-white" @click="toggleApproval(item)">
              {{ item.is_approved ? 'Mark Unapproved' : 'Approve Instructor' }}
            </button>
          </div>
        </div>
      </article>
    </section>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import api from '../../api'

const metrics = ref({
  total_courses: 0,
  published_courses: 0,
  total_enrollments: 0,
  completed_enrollments: 0,
  average_rating: 0,
  pending_instructors: 0,
})

const topCourses = ref([])
const instructors = ref([])

const cards = computed(() => [
  { label: 'Total Courses', value: metrics.value.total_courses || 0 },
  { label: 'Published Courses', value: metrics.value.published_courses || 0 },
  { label: 'Enrollments', value: metrics.value.total_enrollments || 0 },
  { label: 'Completed', value: metrics.value.completed_enrollments || 0 },
])

const load = async () => {
  const [overviewRes, instructorsRes] = await Promise.all([
    api.get('/admin/learning/overview'),
    api.get('/admin/learning/instructors', { params: { per_page: 20 } }),
  ])

  const overview = overviewRes.data?.data || {}
  metrics.value = overview.metrics || metrics.value
  topCourses.value = overview.top_courses || []
  instructors.value = instructorsRes.data?.data || []
}

const saveCourse = async (course) => {
  await api.post(`/admin/learning/courses/${course.id}/status`, {
    status: course.status,
    is_featured: !!course.is_featured,
  })
}

const toggleApproval = async (profile) => {
  await api.post(`/admin/learning/instructors/${profile.id}/approval`, {
    is_approved: !profile.is_approved,
    is_active: true,
  })
  profile.is_approved = !profile.is_approved
}

onMounted(async () => {
  try {
    await load()
  } catch (error) {
    console.error('Failed to load learning management', error)
  }
})
</script>
