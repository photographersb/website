<template>
  <AdminLayout 
    page-title="Reviews Management"
    page-description="Manage platform reviews and ratings"
    :show-breadcrumbs="true"
  >
    <BaseModal 
      :is-open="showModal"
      title="Review Details"
      :is-loading="isSubmitting"
      @close="showModal = false"
      @submit="handleApproveReview"
    >
      <div class="space-y-3">
        <p><strong>Reviewer:</strong> {{ selectedReview?.reviewer_name }}</p>
        <p><strong>Rating:</strong> {{ selectedReview?.rating }}/5 ⭐</p>
        <p><strong>Subject:</strong> {{ selectedReview?.subject }}</p>
        <p><strong>Comment:</strong> {{ selectedReview?.comment }}</p>
        <p><strong>Date:</strong> {{ formatDate(selectedReview?.created_at) }}</p>
      </div>
    </BaseModal>

    <div class="space-y-6">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col sm:flex-row gap-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search reviews..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
        <select
          v-model="ratingFilter"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
          <option value="">
            All Ratings
          </option>
          <option value="5">
            5 Stars
          </option>
          <option value="4">
            4+ Stars
          </option>
          <option value="3">
            3+ Stars
          </option>
          <option value="1">
            1-2 Stars
          </option>
        </select>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div
          v-if="isLoading"
          class="p-8 text-center"
        >
          Loading reviews...
        </div>
        <div
          v-else-if="filteredReviews.length === 0"
          class="p-12 text-center text-gray-500"
        >
          No reviews found
        </div>
        <div
          v-else
          class="divide-y"
        >
          <div
            v-for="review in filteredReviews"
            :key="review.id"
            class="p-6 hover:bg-gray-50"
          >
            <div class="flex justify-between items-start mb-2">
              <div>
                <p class="font-medium">
                  {{ review.reviewer_name }}
                </p>
                <p class="text-sm text-gray-500">
                  {{ review.subject }}
                </p>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-lg">{{ '⭐'.repeat(review.rating) }}</span>
                <span class="text-sm text-gray-600">{{ review.rating }}/5</span>
              </div>
            </div>
            <p class="text-sm text-gray-700 mb-3">
              {{ review.comment }}
            </p>
            <div class="flex justify-between items-center">
              <span class="text-xs text-gray-500">{{ formatDate(review.created_at) }}</span>
              <div class="flex gap-2">
                <button
                  class="text-green-600 hover:text-green-800 text-sm font-medium"
                  @click="approveReview(review)"
                >
                  Approve
                </button>
                <button
                  class="text-red-600 hover:text-red-800 text-sm font-medium"
                  @click="deleteReview(review)"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue';
import AdminLayout from '../../AdminLayout.vue';
import BaseModal from '../modals/BaseModal.vue';
import { formatDate as formatDateValue } from '../../../utils/formatters';

const reviews = ref([]);
const isLoading = ref(false);
const isSubmitting = ref(false);
const searchQuery = ref('');
const ratingFilter = ref('');
const showModal = ref(false);
const selectedReview = ref(null);
const addAlert = inject('addAlert', null);

const filteredReviews = computed(() => {
  let filtered = reviews.value;
  if (searchQuery.value) filtered = filtered.filter(r => r.comment.toLowerCase().includes(searchQuery.value.toLowerCase()));
  if (ratingFilter.value) filtered = filtered.filter(r => r.rating >= parseInt(ratingFilter.value));
  return filtered;
});

const formatDate = (date) => {
  if (!date) return '';
  return formatDateValue(date);
};

const fetchReviews = async () => {
  isLoading.value = true;
  try {
    const response = await fetch('/api/v1/admin/reviews', {
      headers: {}
    });
    if (response.ok) {
      const data = await response.json();
      reviews.value = data.data || [];
    }
  } catch (error) {
    console.error('Error:', error);
    if (addAlert) addAlert('Failed to load reviews', 'error');
  } finally {
    isLoading.value = false;
  }
};

const approveReview = (review) => {
  selectedReview.value = review;
  showModal.value = true;
};

const handleApproveReview = async () => {
  isSubmitting.value = true;
  try {
    const response = await fetch(`/api/v1/admin/reviews/${selectedReview.value.id}/approve`, {
      method: 'POST',
      headers: {}
    });
    if (response.ok) {
      const index = reviews.value.findIndex(r => r.id === selectedReview.value.id);
      reviews.value[index] = selectedReview.value;
      if (addAlert) addAlert('Review approved', 'success');
      showModal.value = false;
    }
  } catch (error) {
    if (addAlert) addAlert('Failed to approve review', 'error');
  } finally {
    isSubmitting.value = false;
  }
};

const deleteReview = async (review) => {
  if (!confirm('Delete this review?')) return;
  try {
    const response = await fetch(`/api/v1/admin/reviews/${review.id}`, {
      method: 'DELETE',
      headers: {}
    });
    if (response.ok) {
      reviews.value = reviews.value.filter(r => r.id !== review.id);
      if (addAlert) addAlert('Review deleted', 'success');
    }
  } catch (error) {
    if (addAlert) addAlert('Failed to delete review', 'error');
  }
};

onMounted(fetchReviews);
</script>
