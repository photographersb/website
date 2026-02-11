<template>
  <div class="review-reply-form mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
    <h4 class="font-semibold text-gray-800 mb-3">
      {{ existingReply ? 'Edit Your Reply' : 'Reply to this Review' }}
    </h4>

    <form @submit.prevent="submitReply">
      <textarea
        v-model="replyText"
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent resize-none"
        rows="4"
        placeholder="Write a professional response to this review..."
        minlength="10"
        maxlength="1000"
        required
      />
      
      <div class="flex justify-between items-center mt-2">
        <span class="text-sm text-gray-500">{{ replyText.length }}/1000 characters</span>
        
        <div class="flex gap-2">
          <button
            v-if="existingReply"
            type="button"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
            @click="cancelEdit"
          >
            Cancel
          </button>
          
          <button
            v-if="existingReply"
            type="button"
            :disabled="loading"
            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors disabled:opacity-50"
            @click="deleteReply"
          >
            Delete
          </button>
          
          <button
            type="submit"
            :disabled="loading || replyText.length < 10"
            class="px-4 py-2 bg-burgundy-600 text-white rounded-lg hover:bg-burgundy-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading">{{ existingReply ? 'Updating...' : 'Posting...' }}</span>
            <span v-else>{{ existingReply ? 'Update Reply' : 'Post Reply' }}</span>
          </button>
        </div>
      </div>

      <div
        v-if="error"
        class="mt-3 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm"
      >
        {{ error }}
      </div>

      <div
        v-if="success"
        class="mt-3 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm"
      >
        {{ success }}
      </div>
    </form>

    <!-- Professional Reply Tips -->
    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
      <p class="text-sm text-blue-800 font-medium mb-2">
        💡 Tips for a Great Reply:
      </p>
      <ul class="text-xs text-blue-700 space-y-1">
        <li>• Thank the client for their feedback</li>
        <li>• Address any concerns professionally</li>
        <li>• Highlight positive aspects they mentioned</li>
        <li>• Keep it concise and friendly</li>
        <li>• Avoid being defensive or argumentative</li>
      </ul>
    </div>
  </div>
</template>

<script>
import { ref, watch } from 'vue';

export default {
  name: 'ReviewReplyForm',
  props: {
    reviewId: {
      type: Number,
      required: true,
    },
    existingReply: {
      type: Object,
      default: null,
    },
  },
  emits: ['replied', 'updated', 'deleted'],
  setup(props, { emit }) {
    const replyText = ref('');
    const loading = ref(false);
    const error = ref('');
    const success = ref('');

    // Initialize with existing reply if editing
    watch(() => props.existingReply, (newReply) => {
      if (newReply) {
        replyText.value = newReply.reply;
      }
    }, { immediate: true });

    const submitReply = async () => {
      loading.value = true;
      error.value = '';
      success.value = '';

      try {
        const url = props.existingReply
          ? `/api/v1/photographer/reviews/replies/${props.existingReply.id}`
          : `/api/v1/photographer/reviews/${props.reviewId}/reply`;

        const method = props.existingReply ? 'PATCH' : 'POST';

        const response = await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            reply: replyText.value,
          }),
        });

        const data = await response.json();

        if (data.status === 'success') {
          success.value = data.message;
          
          if (props.existingReply) {
            emit('updated', data.data);
          } else {
            emit('replied', data.data);
            replyText.value = '';
          }

          // Clear success message after 3 seconds
          setTimeout(() => {
            success.value = '';
          }, 3000);
        } else {
          error.value = data.message || 'Failed to submit reply';
        }
      } catch (err) {
        error.value = 'Network error. Please try again.';
        console.error('Reply submit error:', err);
      } finally {
        loading.value = false;
      }
    };

    const deleteReply = async () => {
      if (!confirm('Are you sure you want to delete this reply?')) {
        return;
      }

      loading.value = true;
      error.value = '';

      try {
        const response = await fetch(`/api/v1/photographer/reviews/replies/${props.existingReply.id}`, {
          method: 'DELETE',
          headers: {},
        });

        const data = await response.json();

        if (data.status === 'success') {
          emit('deleted');
          replyText.value = '';
        } else {
          error.value = data.message || 'Failed to delete reply';
        }
      } catch (err) {
        error.value = 'Network error. Please try again.';
        console.error('Reply delete error:', err);
      } finally {
        loading.value = false;
      }
    };

    const cancelEdit = () => {
      replyText.value = props.existingReply?.reply || '';
      error.value = '';
      success.value = '';
    };

    return {
      replyText,
      loading,
      error,
      success,
      submitReply,
      deleteReply,
      cancelEdit,
    };
  },
};
</script>
