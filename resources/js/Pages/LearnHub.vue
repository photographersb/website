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
          <p v-if="feedbackMessage" class="text-xs text-gray-600 mb-3">{{ feedbackMessage }}</p>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="course in courses" :key="course.id" class="border border-gray-100 rounded-xl p-3 sm:p-4 space-y-2">
              <p class="text-xs text-gray-500">{{ course.category }} • {{ course.difficulty_level }}</p>
              <h3 class="text-base font-semibold text-gray-900">{{ course.title }}</h3>
              <p class="text-sm text-gray-600 line-clamp-3">{{ course.description }}</p>
              <p class="text-xs text-gray-500">Instructor: {{ course.instructor?.name || 'Unknown' }}</p>
              <div class="flex items-center justify-between gap-3 pt-1">
                <span class="text-sm font-semibold text-gray-900">{{ Number(course.price || 0) <= 0 ? 'Free' : `৳${course.price}` }}</span>
                <div class="flex items-center gap-2">
                  <button class="text-xs px-3 py-1.5 rounded border border-gray-300 text-gray-700 hover:bg-gray-100" @click="openCourse(course.id)">Open</button>
                  <button class="text-xs px-3 py-1.5 rounded bg-rose-600 text-white hover:bg-rose-700" @click="enroll(course)">Enroll</button>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-5 border border-gray-100 rounded-xl p-3 sm:p-4 bg-gray-50/70">
            <div class="flex items-center justify-between gap-3 mb-3">
              <h3 class="text-sm sm:text-base font-semibold text-gray-900">Course Detail & Progress</h3>
              <button
                v-if="activeCourse?.id"
                class="text-xs text-rose-700 hover:underline"
                @click="openCourse(activeCourse.id)"
              >
                Refresh
              </button>
            </div>

            <p v-if="courseLoading" class="text-sm text-gray-500">Loading course...</p>

            <div v-else-if="activeCourse" class="space-y-4">
              <div class="border border-gray-200 rounded-lg p-3 bg-white">
                <p class="text-sm font-semibold text-gray-900">{{ activeCourse.title }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ activeCourse.category }} • {{ activeCourse.difficulty_level }}</p>
                <p class="text-sm text-gray-700 mt-2">{{ activeCourse.description }}</p>
                <p class="text-xs text-gray-600 mt-2">Completion: {{ myEnrollment?.completion_percentage || 0 }}%</p>
              </div>

              <div class="space-y-2 max-h-56 overflow-auto pr-1">
                <div v-for="lesson in courseLessons" :key="lesson.id" class="border border-gray-200 rounded-lg p-2.5 bg-white flex items-center justify-between gap-2">
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ lesson.title }}</p>
                    <p class="text-xs text-gray-500">Lesson {{ lesson.sort_order || '-' }}</p>
                  </div>
                  <button class="text-xs px-2.5 py-1 rounded bg-gray-900 text-white hover:bg-black" @click="markLessonComplete(lesson)">
                    Mark Complete
                  </button>
                </div>
                <p v-if="courseLessons.length === 0" class="text-xs text-gray-500">No lessons published yet for this course.</p>
              </div>

              <div class="border border-gray-200 rounded-lg p-3 bg-white space-y-2">
                <p class="text-sm font-semibold text-gray-900">Submit Course Review</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                  <select v-model.number="reviewForm.rating" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option :value="0">Select rating</option>
                    <option :value="5">5 - Excellent</option>
                    <option :value="4">4 - Great</option>
                    <option :value="3">3 - Good</option>
                    <option :value="2">2 - Fair</option>
                    <option :value="1">1 - Poor</option>
                  </select>
                  <button
                    class="rounded-lg bg-rose-600 text-white text-xs px-3 py-2 font-medium hover:bg-rose-700 disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="(myEnrollment?.completion_percentage || 0) < 100"
                    @click="submitCourseReview"
                  >
                    Submit Review
                  </button>
                </div>
                <textarea v-model="reviewForm.feedback" rows="2" placeholder="Share your learning experience"
                  class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                <p v-if="(myEnrollment?.completion_percentage || 0) < 100" class="text-xs text-gray-500">
                  Complete this course to submit a review.
                </p>
              </div>

              <div class="space-y-2 max-h-40 overflow-auto pr-1">
                <div v-for="review in courseReviews" :key="review.id" class="border border-gray-200 rounded-lg p-2.5 bg-white">
                  <p class="text-xs text-gray-500">{{ review.user?.name || 'Learner' }} • {{ review.rating }}/5</p>
                  <p class="text-sm text-gray-700 mt-1">{{ review.feedback || 'No written feedback.' }}</p>
                </div>
                <p v-if="courseReviews.length === 0" class="text-xs text-gray-500">No reviews yet.</p>
              </div>
            </div>

            <p v-else class="text-sm text-gray-500">Open a course to track lesson progress and submit a review.</p>
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
const activeCourse = ref(null)
const courseLessons = ref([])
const courseReviews = ref([])
const myEnrollment = ref(null)
const courseLoading = ref(false)
const feedbackMessage = ref('')

const reviewForm = ref({
  rating: 0,
  feedback: '',
})

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
  courses.value = data?.data?.data || data?.data || []
}

const enroll = async (course) => {
  try {
    await api.post(`/learn/courses/${course.id}/enroll`)
    feedbackMessage.value = 'Enrolled successfully.'
    await Promise.all([loadHub(), openCourse(course.id)])
  } catch (error) {
    console.error('Enrollment failed', error)
    feedbackMessage.value = error?.response?.data?.message || 'Enrollment failed. Please try again.'
  }
}

const openCourse = async (courseId) => {
  courseLoading.value = true
  try {
    const { data } = await api.get(`/learn/courses/${courseId}`)
    const payload = data?.data || {}
    activeCourse.value = payload.course || null
    courseLessons.value = payload.course?.lessons || []
    courseReviews.value = payload.course?.reviews || []
    myEnrollment.value = payload.my_enrollment || null
    feedbackMessage.value = ''
  } catch (error) {
    activeCourse.value = null
    courseLessons.value = []
    courseReviews.value = []
    myEnrollment.value = null
    feedbackMessage.value = error?.response?.data?.message || 'Unable to load course details.'
  } finally {
    courseLoading.value = false
  }
}

const markLessonComplete = async (lesson) => {
  if (!activeCourse.value?.id || !lesson?.id) return
  try {
    const { data } = await api.post(`/learn/courses/${activeCourse.value.id}/lessons/${lesson.id}/progress`, {
      progress_percentage: 100,
    })
    myEnrollment.value = data?.data?.enrollment || myEnrollment.value
    feedbackMessage.value = 'Lesson marked complete.'
    await Promise.all([openCourse(activeCourse.value.id), loadHub()])
  } catch (error) {
    feedbackMessage.value = error?.response?.data?.message || 'Unable to track lesson progress.'
  }
}

const submitCourseReview = async () => {
  if (!activeCourse.value?.id) return
  if (!reviewForm.value.rating || reviewForm.value.rating < 1 || reviewForm.value.rating > 5) {
    feedbackMessage.value = 'Please choose a rating between 1 and 5.'
    return
  }

  try {
    await api.post(`/learn/courses/${activeCourse.value.id}/reviews`, {
      rating: reviewForm.value.rating,
      feedback: reviewForm.value.feedback?.trim() || null,
    })

    reviewForm.value = { rating: 0, feedback: '' }
    feedbackMessage.value = 'Review submitted successfully.'
    await openCourse(activeCourse.value.id)
  } catch (error) {
    feedbackMessage.value = error?.response?.data?.message || 'Unable to submit review right now.'
  }
}

onMounted(async () => {
  try {
    await Promise.all([loadHub(), loadCourses()])
    if (courses.value.length > 0) {
      await openCourse(courses.value[0].id)
    }
  } catch (error) {
    console.error('Failed to load learning hub', error)
  }
})
</script>
