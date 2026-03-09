<template>
  <div class="min-h-screen bg-gray-50 py-6 sm:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      <header class="bg-white border border-gray-100 rounded-2xl p-4 sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
          <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900">Learn Photography</h1>
            <p class="text-sm sm:text-base text-gray-600 mt-1">Courses, workshops, and instructors to level up your craft.</p>
          </div>
          <div class="w-full lg:w-[520px] grid grid-cols-1 sm:grid-cols-4 gap-2">
            <input
              v-model="filters.q"
              type="text"
              placeholder="Search courses"
              class="sm:col-span-2 rounded-lg border border-gray-300 px-3 py-2 text-sm"
              @keyup.enter="loadCourses"
            />
            <select v-model="filters.category" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
              <option value="">All categories</option>
              <option v-for="item in categories" :key="item.category" :value="item.category">{{ item.category }}</option>
            </select>
            <select v-model="filters.difficulty_level" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
              <option value="">All levels</option>
              <option value="beginner">Beginner</option>
              <option value="intermediate">Intermediate</option>
              <option value="advanced">Advanced</option>
            </select>
          </div>
        </div>
      </header>

      <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <article class="xl:col-span-2 bg-white border border-gray-100 rounded-2xl p-4 sm:p-5">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Featured Courses</h2>
            <button class="text-sm px-3 py-1.5 rounded-lg bg-gray-900 text-white hover:bg-black" @click="loadCourses">Apply filters</button>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="course in courses" :key="course.id" class="border border-gray-100 rounded-xl p-3 sm:p-4 space-y-2">
              <p class="text-xs text-gray-500">{{ course.category }} • {{ course.difficulty_level }}</p>
              <h3 class="text-base font-semibold text-gray-900">{{ course.title }}</h3>
              <p class="text-sm text-gray-600 line-clamp-3">{{ course.description }}</p>
              <p class="text-xs text-gray-500">Instructor: {{ course.instructor?.name || 'Unknown' }}</p>
              <div class="flex items-center justify-between gap-3 pt-1">
                <span class="text-sm font-semibold text-gray-900">{{ Number(course.price || 0) <= 0 ? 'Free' : `৳${course.price}` }}</span>
                <button class="text-xs px-3 py-1.5 rounded bg-rose-600 text-white hover:bg-rose-700" @click="enroll(course)">Enroll</button>
              </div>
            </div>
          </div>
        </article>

        <aside class="space-y-6">
          <div class="bg-white border border-gray-100 rounded-2xl p-4 sm:p-5">
            <h2 class="text-base font-semibold text-gray-900 mb-3">My Learning</h2>
            <div class="space-y-2 text-sm text-gray-700">
              <div class="flex items-center justify-between"><span>Active courses</span><strong>{{ myLearning.active_courses || 0 }}</strong></div>
              <div class="flex items-center justify-between"><span>Completed</span><strong>{{ myLearning.completed_courses || 0 }}</strong></div>
              <div class="flex items-center justify-between"><span>Certificates</span><strong>{{ myLearning.certificates_earned || 0 }}</strong></div>
            </div>
            <router-link to="/dashboard/learning" class="inline-block mt-3 text-xs text-rose-700 hover:underline">Open dashboard</router-link>
          </div>

          <div class="bg-white border border-gray-100 rounded-2xl p-4 sm:p-5">
            <h2 class="text-base font-semibold text-gray-900 mb-3">Instructor Spotlight</h2>
            <div class="space-y-2">
              <div v-for="instructor in instructors.slice(0, 5)" :key="instructor.id" class="text-sm">
                <p class="font-medium text-gray-800">{{ instructor.user?.name }}</p>
                <p class="text-xs text-gray-500">Rating {{ instructor.student_rating }} • Students {{ instructor.students_count }}</p>
              </div>
            </div>
          </div>
        </aside>
      </section>

      <section class="bg-white border border-gray-100 rounded-2xl p-4 sm:p-5">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-lg font-semibold text-gray-900">Recommended Workshops</h2>
          <router-link to="/events" class="text-xs text-rose-700 hover:underline">See events</router-link>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
          <div v-for="workshop in workshops" :key="workshop.id" class="border border-gray-100 rounded-lg p-3">
            <p class="text-sm font-medium text-gray-900">{{ workshop.title }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ workshop.event_date ? new Date(workshop.event_date).toLocaleDateString() : 'Date TBA' }} • {{ workshop.venue_name || 'Venue TBD' }}</p>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import api from '../api'

const filters = ref({
  q: '',
  category: '',
  difficulty_level: '',
})

const courses = ref([])
const categories = ref([])
const workshops = ref([])
const instructors = ref([])
const myLearning = ref({})

const loadHub = async () => {
  const { data } = await api.get('/learn/hub')
  const payload = data?.data || {}
  categories.value = payload.course_categories || []
  workshops.value = payload.recommended_workshops || []
  instructors.value = payload.instructors_spotlight || []
  myLearning.value = payload.my_learning || {}
}

const loadCourses = async () => {
  const { data } = await api.get('/learn/courses', { params: filters.value })
  courses.value = data?.data || []
}

const enroll = async (course) => {
  try {
    await api.post(`/learn/courses/${course.id}/enroll`)
    await loadHub()
  } catch (error) {
    console.error('Enrollment failed', error)
  }
}

onMounted(async () => {
  try {
    await Promise.all([loadHub(), loadCourses()])
  } catch (error) {
    console.error('Failed to load learning hub', error)
  }
})
</script>
