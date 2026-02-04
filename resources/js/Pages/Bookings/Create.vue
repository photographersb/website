<template>
  <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow">
      <div class="px-6 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Request Booking</h1>
        <p class="text-gray-600 mb-8">
          Photographer: <span class="font-semibold">{{ photographer.name }}</span>
        </p>

        <form @submit.prevent="submitBooking" class="space-y-6">
          <!-- Event Date -->
          <div>
            <label for="event_date" class="block text-sm font-medium text-gray-700 mb-1">
              Event Date <span class="text-red-500">*</span>
            </label>
            <input
              id="event_date"
              v-model="form.event_date"
              type="date"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <p v-if="errors.event_date" class="text-red-500 text-sm mt-1">{{ errors.event_date }}</p>
          </div>

          <!-- Event Time -->
          <div>
            <label for="event_time" class="block text-sm font-medium text-gray-700 mb-1">
              Event Time (Optional)
            </label>
            <input
              id="event_time"
              v-model="form.event_time"
              type="time"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <!-- Duration -->
          <div>
            <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-1">
              Duration (hours)
            </label>
            <input
              id="duration_hours"
              v-model.number="form.duration_hours"
              type="number"
              min="1"
              max="24"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <!-- Venue Address -->
          <div>
            <label for="venue_address" class="block text-sm font-medium text-gray-700 mb-1">
              Venue Address (Optional)
            </label>
            <textarea
              id="venue_address"
              v-model="form.venue_address"
              rows="2"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            ></textarea>
          </div>

          <!-- Budget Range -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="budget_min" class="block text-sm font-medium text-gray-700 mb-1">
                Min Budget (৳)
              </label>
              <input
                id="budget_min"
                v-model.number="form.budget_min"
                type="number"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div>
              <label for="budget_max" class="block text-sm font-medium text-gray-700 mb-1">
                Max Budget (৳)
              </label>
              <input
                id="budget_max"
                v-model.number="form.budget_max"
                type="number"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <p v-if="errors.budget_max" class="text-red-500 text-sm mt-1">{{ errors.budget_max }}</p>
            </div>
          </div>

          <!-- Notes -->
          <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
              Additional Notes (Optional)
            </label>
            <textarea
              id="notes"
              v-model="form.notes"
              rows="4"
              placeholder="Tell the photographer more about your event..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            ></textarea>
          </div>

          <!-- Submit Button -->
          <div class="flex gap-4">
            <button
              type="submit"
              :disabled="isSubmitting"
              class="flex-1 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-medium py-2 px-4 rounded-lg transition"
            >
              {{ isSubmitting ? 'Sending...' : 'Send Booking Request' }}
            </button>
            <Link
              :href="route('home')"
              class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg text-center transition"
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
import { ref, reactive } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  photographer: Object,
  categories: Array,
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

const submitBooking = () => {
  isSubmitting.value = true
  form.post(route('booking.store'), {
    onSuccess: () => {
      isSubmitting.value = false
    },
    onError: (err) => {
      errors.value = err
      isSubmitting.value = false
    },
  })
}
</script>
