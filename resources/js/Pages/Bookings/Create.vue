<template>
  <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto sb-ui-card">
      <div class="px-6 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          Request Booking
        </h1>
        <p class="text-gray-600 mb-8">
          Photographer: <span class="font-semibold">{{ photographer.name }}</span>
        </p>

        <div
          v-if="isSelfBooking"
          class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700"
        >
          You cannot create a booking request for your own profile.
        </div>

        <form
          class="space-y-6"
          @submit.prevent="submitBooking"
        >
          <!-- Event Date -->
          <div>
            <label
              for="event_date"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Event Date <span class="text-red-500">*</span>
            </label>
            <input
              id="event_date"
              v-model="form.event_date"
              type="date"
              required
              class="sb-ui-input"
            >
            <p
              v-if="errors.event_date"
              class="text-red-500 text-sm mt-1"
            >
              {{ errors.event_date }}
            </p>
          </div>

          <!-- Event Time -->
          <div>
            <label
              for="event_time"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Event Time (Optional)
            </label>
            <input
              id="event_time"
              v-model="form.event_time"
              type="time"
              class="sb-ui-input"
            >
          </div>

          <!-- Duration -->
          <div>
            <label
              for="duration_hours"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Duration (hours)
            </label>
            <input
              id="duration_hours"
              v-model.number="form.duration_hours"
              type="number"
              min="1"
              max="24"
              class="sb-ui-input"
            >
          </div>

          <!-- Category -->
          <div>
            <label
              for="category_id"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Category (Optional)
            </label>
            <select
              id="category_id"
              v-model="form.category_id"
              class="sb-ui-select"
            >
              <option :value="null">
                Select category
              </option>
              <option
                v-for="category in props.categories || []"
                :key="category.id"
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
          </div>

          <!-- City -->
          <div>
            <label
              for="city_id"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              City (Optional)
            </label>
            <select
              id="city_id"
              v-model="form.city_id"
              class="sb-ui-select"
            >
              <option :value="null">
                Select city
              </option>
              <option
                v-for="city in props.locations || []"
                :key="city.id"
                :value="city.id"
              >
                {{ city.name }}
              </option>
            </select>
          </div>

          <!-- Venue Address -->
          <div>
            <label
              for="venue_address"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Venue Address (Optional)
            </label>
            <textarea
              id="venue_address"
              v-model="form.venue_address"
              rows="2"
              class="sb-ui-textarea"
            />
          </div>

          <!-- Budget Range -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label
                for="budget_min"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Min Budget (৳)
              </label>
              <input
                id="budget_min"
                v-model.number="form.budget_min"
                type="number"
                class="sb-ui-input"
              >
            </div>
            <div>
              <label
                for="budget_max"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Max Budget (৳)
              </label>
              <input
                id="budget_max"
                v-model.number="form.budget_max"
                type="number"
                class="sb-ui-input"
              >
              <p
                v-if="errors.budget_max"
                class="text-red-500 text-sm mt-1"
              >
                {{ errors.budget_max }}
              </p>
            </div>
          </div>

          <!-- Notes -->
          <div>
            <label
              for="notes"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Additional Notes (Optional)
            </label>
            <textarea
              id="notes"
              v-model="form.notes"
              rows="4"
              placeholder="Tell the photographer more about your event..."
              class="sb-ui-textarea"
            />
          </div>

          <!-- Submit Button -->
          <div class="flex gap-4">
            <button
              type="submit"
              :disabled="isSubmitting || isSelfBooking"
              class="sb-ui-btn sb-ui-btn--primary flex-1 disabled:bg-gray-400"
            >
              {{ isSelfBooking ? 'Self Booking Blocked' : (isSubmitting ? 'Sending...' : 'Send Booking Request') }}
            </button>
            <Link
              :href="route('home')"
              class="sb-ui-btn sb-ui-btn--secondary flex-1"
            >
              Cancel
            </Link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  photographer: Object,
  categories: Array,
  locations: Array,
  currentUserId: Number,
})

const isSubmitting = ref(false)
const errors = reactive({})

const form = useForm({
  photographer_user_id: props.photographer.id,
  category_id: null,
  city_id: null,
  venue_address: '',
  event_date: '',
  event_time: '',
  duration_hours: 4,
  budget_min: '',
  budget_max: '',
  notes: '',
})

const isSelfBooking = computed(() => {
  return !!props.currentUserId && props.photographer?.id === props.currentUserId
})

const submitBooking = () => {
  if (isSelfBooking.value) {
    return
  }
  isSubmitting.value = true
  form.post(route('booking.store'), {
    onSuccess: () => {
      isSubmitting.value = false
    },
    onError: (err) => {
      Object.assign(errors, err || {})
      isSubmitting.value = false
    },
  })
}
</script>
