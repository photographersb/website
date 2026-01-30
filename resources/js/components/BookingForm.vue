<template>
  <div class="min-h-screen bg-gray-50 py-8 md:py-12">
    <div class="container mx-auto px-4 max-w-2xl">
      <h1 class="text-2xl md:text-3xl font-bold mb-6 md:mb-8">Create Booking Inquiry</h1>

      <div v-if="photographer" class="bg-white rounded-lg shadow p-4 md:p-6 mb-6">
        <div class="flex items-center gap-3 md:gap-4">
          <img
            :src="photographer.profile_picture || 'https://via.placeholder.com/80'"
            :alt="photographer.user?.name || 'Photographer'"
            class="w-12 h-12 md:w-16 md:h-16 rounded-full object-cover"
          />
          <div>
            <h2 class="text-lg md:text-xl font-bold">{{ photographer.user?.name || photographer.business_name || 'Unknown' }}</h2>
            <p class="text-gray-600 text-xs md:text-sm">{{ photographer.bio }}</p>
          </div>
        </div>
      </div>

      <form @submit.prevent="submitInquiry" class="bg-white rounded-lg shadow p-6 md:p-8 space-y-5 md:space-y-6">
        <!-- Event Date -->
        <div>
          <label class="block text-sm font-medium mb-2">Event Date *</label>
          <input
            v-model="form.event_date"
            type="date"
            required
            class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-burgundy"
          />
          <p v-if="errors.event_date" class="mt-1 text-sm text-red-600">{{ errors.event_date[0] }}</p>
        </div>

        <!-- Event Location -->
        <div>
          <label class="block text-sm font-medium mb-2">Event Location *</label>
          <input
            v-model="form.event_location"
            type="text"
            placeholder="Enter location"
            required
            class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-burgundy"
          />
          <p v-if="errors.event_location" class="mt-1 text-sm text-red-600">{{ errors.event_location[0] }}</p>
        </div>

        <!-- Guest Count -->
        <div>
          <label class="block text-sm font-medium mb-2">Guest Count *</label>
          <input
            v-model.number="form.guest_count"
            type="number"
            min="1"
            required
            class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-burgundy"
          />
          <p v-if="errors.guest_count" class="mt-1 text-sm text-red-600">{{ errors.guest_count[0] }}</p>
        </div>

        <!-- Budget Range -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-2">Budget Min (৳)</label>
            <input
              v-model.number="form.budget_min"
              type="number"
              class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-burgundy"
            />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Budget Max (৳)</label>
            <input
              v-model.number="form.budget_max"
              type="number"
              class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-burgundy"
            />
          </div>
        </div>

        <!-- Requirements -->
        <div>
          <label class="block text-sm font-medium mb-2">Special Requirements</label>
          <textarea
            v-model="form.requirements"
            placeholder="Any special requests or requirements..."
            rows="4"
            class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-burgundy"
          ></textarea>
        </div>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="submitting"
          class="w-full bg-burgundy text-white py-3 rounded-lg hover:bg-[#6F112D] disabled:opacity-50"
        >
          {{ submitting ? 'Sending...' : 'Send Inquiry' }}
        </button>

        <!-- Message -->
        <div v-if="message" :class="`p-4 rounded ${error ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'}`">
          {{ message }}
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api';

const route = useRoute();
const router = useRouter();

const photographer = ref(null);
const form = ref({
  photographer_id: route.params.id,
  event_date: '',
  event_location: '',
  guest_count: 1,
  budget_min: null,
  budget_max: null,
  requirements: '',
});

const submitting = ref(false);
const message = ref('');
const error = ref(false);
const errors = ref({});

const fetchPhotographer = async () => {
  try {
    const { data } = await api.get(`/photographers/${route.params.id}`);
    if (data.status === 'success') {
      photographer.value = data.data;
      // Set the photographer_id to the actual numeric ID
      form.value.photographer_id = data.data.id;
    }
  } catch (error) {
    console.error('Error fetching photographer:', error);
  }
};

const submitInquiry = async () => {
  submitting.value = true;
  error.value = false;
  message.value = '';
  errors.value = {};

  try {
    const { data } = await api.post('/bookings/inquiry', form.value);

    if (data.status === 'success') {
      message.value = 'Inquiry sent successfully! The photographer will contact you soon.';
      error.value = false;
      setTimeout(() => {
        router.push('/');
      }, 2000);
    }
  } catch (err) {
    error.value = true;
    
    // Handle validation errors
    if (err.response?.status === 422 && err.response?.data?.errors) {
      errors.value = err.response.data.errors;
      message.value = 'Please correct the errors below.';
    } else {
      // Handle other errors
      const errorMessage = err.response?.data?.message;
      message.value = errorMessage || 'Failed to send inquiry. Please try again.';
    }
    
    console.error('Error sending inquiry:', err);
  } finally {
    submitting.value = false;
  }
};

onMounted(() => {
  fetchPhotographer();
});
</script>
