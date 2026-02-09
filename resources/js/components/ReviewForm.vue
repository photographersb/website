<template>
  <div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4 max-w-2xl">
      <h1 class="text-3xl font-bold mb-8">
        Write a Review
      </h1>

      <div
        v-if="photographer"
        class="bg-white rounded-lg shadow p-6 mb-6"
      >
        <div class="flex items-center gap-4">
          <img
            :src="photographer.avatar || '/images/placeholder.svg'"
            :alt="photographer.user?.name || 'Photographer'"
            class="w-16 h-16 rounded-full object-cover"
          >
          <div>
            <h2 class="text-xl font-bold">
              {{ photographer.user?.name || 'Unknown' }}
            </h2>
            <p class="text-gray-600 text-sm">
              {{ photographer.bio }}
            </p>
          </div>
        </div>
      </div>

      <form
        class="bg-white rounded-lg shadow p-8 space-y-6"
        @submit.prevent="submitReview"
      >
        <!-- Rating -->
        <div>
          <label class="block text-sm font-medium mb-2">Overall Rating *</label>
          <div class="flex gap-2">
            <button
              v-for="star in 5"
              :key="star"
              type="button"
              :class="`text-3xl ${star <= form.rating ? 'text-yellow-400' : 'text-gray-300'}`"
              @click="form.rating = star"
            >
              ★
            </button>
          </div>
          <p
            v-if="form.rating"
            class="text-sm text-gray-600 mt-1"
          >
            {{ getRatingText(form.rating) }}
          </p>
          <p
            v-if="errors.rating"
            class="mt-1 text-sm text-red-600"
          >
            {{ errors.rating[0] }}
          </p>
        </div>

        <!-- Review Title -->
        <div>
          <label class="block text-sm font-medium mb-2">Review Title *</label>
          <input
            v-model="form.title"
            type="text"
            required
            placeholder="Sum up your experience in one sentence"
            class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-burgundy"
          >
          <p
            v-if="errors.title"
            class="mt-1 text-sm text-red-600"
          >
            {{ errors.title[0] }}
          </p>
        </div>

        <!-- Review Comment -->
        <div>
          <label class="block text-sm font-medium mb-2">Your Review *</label>
          <textarea
            v-model="form.comment"
            required
            rows="6"
            placeholder="Share your experience with this photographer..."
            class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-burgundy"
          />
          <p class="text-xs text-gray-500 mt-1">
            Minimum 50 characters
          </p>
          <p
            v-if="errors.comment"
            class="mt-1 text-sm text-red-600"
          >
            {{ errors.comment[0] }}
          </p>
        </div>

        <!-- Detailed Ratings -->
        <div class="border-t pt-6">
          <h3 class="font-bold mb-4">
            Detailed Ratings
          </h3>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-2">Professionalism</label>
              <div class="flex gap-2">
                <button
                  v-for="star in 5"
                  :key="star"
                  type="button"
                  :class="`text-2xl ${star <= form.professionalism_rating ? 'text-yellow-400' : 'text-gray-300'}`"
                  @click="form.professionalism_rating = star"
                >
                  ★
                </button>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium mb-2">Quality of Work</label>
              <div class="flex gap-2">
                <button
                  v-for="star in 5"
                  :key="star"
                  type="button"
                  :class="`text-2xl ${star <= form.quality_rating ? 'text-yellow-400' : 'text-gray-300'}`"
                  @click="form.quality_rating = star"
                >
                  ★
                </button>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium mb-2">Communication</label>
              <div class="flex gap-2">
                <button
                  v-for="star in 5"
                  :key="star"
                  type="button"
                  :class="`text-2xl ${star <= form.communication_rating ? 'text-yellow-400' : 'text-gray-300'}`"
                  @click="form.communication_rating = star"
                >
                  ★
                </button>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium mb-2">Value for Money</label>
              <div class="flex gap-2">
                <button
                  v-for="star in 5"
                  :key="star"
                  type="button"
                  :class="`text-2xl ${star <= form.value_rating ? 'text-yellow-400' : 'text-gray-300'}`"
                  @click="form.value_rating = star"
                >
                  ★
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Would Recommend -->
        <div>
          <label class="flex items-center gap-2">
            <input
              v-model="form.would_recommend"
              type="checkbox"
              class="w-5 h-5 text-burgundy"
            >
            <span class="font-medium">I would recommend this photographer</span>
          </label>
        </div>

        <!-- Anonymous Review Option -->
        <div class="bg-gray-50 p-4 rounded-lg">
          <label class="flex items-start gap-3">
            <input
              v-model="form.is_anonymous"
              type="checkbox"
              class="w-5 h-5 text-burgundy mt-1"
            >
            <div>
              <span class="font-medium block">Post as Anonymous</span>
              <span class="text-sm text-gray-600">Your name will be hidden from public view, but admin can see it for service improvement.</span>
            </div>
          </label>
        </div>

        <!-- Submit -->
        <div class="flex gap-4">
          <button
            type="submit"
            :disabled="submitting || !isFormValid"
            class="flex-1 bg-burgundy text-white py-3 rounded-lg hover:bg-[#6F112D] disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ submitting ? 'Submitting...' : 'Submit Review' }}
          </button>
          <button
            type="button"
            class="px-6 py-3 border rounded-lg hover:bg-gray-50"
            @click="$router.back()"
          >
            Cancel
          </button>
        </div>

        <!-- Message -->
        <div
          v-if="message"
          :class="`p-4 rounded ${error ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'}`"
        >
          {{ message }}
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api';

const route = useRoute();
const router = useRouter();

const photographer = ref(null);
const submitting = ref(false);
const message = ref('');
const error = ref(false);
const errors = ref({});

const form = ref({
  photographer_id: route.params.photographerId,
  booking_id: route.query.booking_id || null,
  rating: 0,
  title: '',
  comment: '',
  professionalism_rating: 0,
  quality_rating: 0,
  communication_rating: 0,
  value_rating: 0,
  would_recommend: true,
  is_anonymous: false,
});

const isFormValid = computed(() => {
  return form.value.rating > 0 && 
         form.value.title.length > 0 && 
         form.value.comment.length >= 50;
});

const fetchPhotographer = async () => {
  try {
    const { data } = await api.get(`/photographers/${route.params.photographerId}`);
    if (data.status === 'success') {
      photographer.value = data.data;
      // Set the photographer_id to the actual numeric ID
      form.value.photographer_id = data.data.id;
    }
  } catch (error) {
    console.error('Error fetching photographer:', error);
  }
};

const getRatingText = (rating) => {
  const texts = {
    1: 'Poor',
    2: 'Fair',
    3: 'Good',
    4: 'Very Good',
    5: 'Excellent'
  };
  return texts[rating] || '';
};

const submitReview = async () => {
  if (!isFormValid.value) return;

  submitting.value = true;
  error.value = false;
  message.value = '';
  errors.value = {};

  try {
    const { data } = await api.post('/reviews', form.value);

    if (data.status === 'success') {
      message.value = 'Review submitted successfully! Thank you for your feedback.';
      error.value = false;
      setTimeout(() => {
        router.push(`/photographer/${photographer.value.slug}`);
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
      message.value = errorMessage || 'Failed to submit review. Please try again.';
    }
    
    console.error('Error submitting review:', err);
  } finally {
    submitting.value = false;
  }
};

onMounted(() => {
  fetchPhotographer();
});
</script>
