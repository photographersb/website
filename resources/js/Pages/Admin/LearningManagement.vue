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

    <section class="bg-white rounded-xl border border-gray-200 p-4">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-semibold text-gray-900">Course Reviews Moderation</h2>
        <button class="text-xs px-2.5 py-1.5 rounded bg-gray-900 text-white" @click="load">Refresh</button>
      </div>

      <p v-if="feedbackMessage" class="text-xs text-gray-600 mb-3">{{ feedbackMessage }}</p>

      <div class="space-y-2 max-h-80 overflow-auto pr-1">
        <div v-for="review in reviews" :key="review.id" class="border border-gray-100 rounded-lg p-3 text-sm">
          <div class="flex items-start justify-between gap-3">
            <div>
              <p class="font-medium text-gray-900">{{ review.user?.name || 'Learner' }} • {{ review.rating }}/5</p>
              <p class="text-xs text-gray-500">{{ review.course?.title || 'Course' }} • {{ review.status }}</p>
            </div>
            <div class="flex items-center gap-2">
              <button class="text-xs px-2 py-1 rounded bg-green-600 text-white" @click="setReviewStatus(review, 'published')">Publish</button>
              <button class="text-xs px-2 py-1 rounded bg-gray-700 text-white" @click="setReviewStatus(review, 'hidden')">Hide</button>
            </div>
          </div>
          <p class="text-sm text-gray-700 mt-2">{{ review.feedback || 'No written feedback.' }}</p>
        </div>
        <p v-if="reviews.length === 0" class="text-sm text-gray-500">No reviews available for moderation.</p>
      </div>
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
const reviews = ref([])
const feedbackMessage = ref('')

const extractCollection = (payload) => payload?.data?.data || payload?.data || []

const cards = computed(() => [
  { label: 'Total Courses', value: metrics.value.total_courses || 0 },
  { label: 'Published Courses', value: metrics.value.published_courses || 0 },
  { label: 'Enrollments', value: metrics.value.total_enrollments || 0 },
  { label: 'Completed', value: metrics.value.completed_enrollments || 0 },
  { label: 'Reviews', value: metrics.value.reviews_count || 0 },
  { label: 'Avg Rating', value: Number(metrics.value.average_rating || 0).toFixed(1) },
])

const load = async () => {
  const [overviewRes, instructorsRes, reviewsRes] = await Promise.all([
    api.get('/admin/learning/overview'),
    api.get('/admin/learning/instructors', { params: { per_page: 20 } }),
    api.get('/admin/learning/reviews', { params: { per_page: 20 } }),
  ])

  const overview = overviewRes.data?.data || {}
  metrics.value = overview.metrics || metrics.value
  topCourses.value = overview.top_courses || []
  instructors.value = extractCollection(instructorsRes.data)
  reviews.value = extractCollection(reviewsRes.data)
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

const setReviewStatus = async (review, status) => {
  try {
    await api.post(`/admin/learning/reviews/${review.id}/status`, { status })
    review.status = status
    feedbackMessage.value = `Review set to ${status}.`
  } catch (error) {
    feedbackMessage.value = error?.response?.data?.message || 'Unable to update review status.'
  }
}

onMounted(async () => {
  try {
    await load()
  } catch (error) {
    console.error('Failed to load learning management', error)
  }
})
</script>
