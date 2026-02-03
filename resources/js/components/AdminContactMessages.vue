<template>
  <div class="admin-contact-messages min-h-screen bg-gray-50">
    <!-- Admin Header -->
    <AdminHeader 
      title="📧 Contact Messages & Inquiries" 
      subtitle="Manage customer inquiries and support requests"
    />

    <!-- Main Content -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      
      <!-- Quick Navigation -->
      <AdminQuickNav />

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
          <p class="text-gray-600 text-sm">Total Messages</p>
          <p class="text-2xl font-bold text-gray-900">{{ totalMessages }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
          <p class="text-gray-600 text-sm">Pending</p>
          <p class="text-2xl font-bold text-warning-700">{{ pendingCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
          <p class="text-gray-600 text-sm">Contact Forms</p>
          <p class="text-2xl font-bold text-primary-700">{{ contactCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
          <p class="text-gray-600 text-sm">Sponsorship Inquiries</p>
          <p class="text-2xl font-bold text-primary-700">{{ sponsorshipCount }}</p>
        </div>
      </div>

      <!-- Filters & Search -->
      <div class="bg-white rounded-lg shadow p-4 mb-6 flex flex-wrap gap-4">
        <div class="flex-1 min-w-64">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by name, email..."
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-admin-primary"
          />
        </div>
        <select
          v-model="filterType"
          class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-admin-primary"
        >
          <option value="">All Types</option>
          <option value="contact">Contact Form</option>
          <option value="sponsorship">Sponsorship Inquiry</option>
        </select>
        <select
          v-model="filterStatus"
          class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-admin-primary"
        >
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="read">Read</option>
          <option value="resolved">Resolved</option>
        </select>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block">
          <svg class="w-8 h-8 animate-spin" style="color: var(--admin-brand-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-danger-50 border border-danger-200 rounded-lg p-4 mb-6">
        <p class="text-danger-700">{{ error }}</p>
        <button @click="fetchMessages" class="btn-text" style="color: var(--admin-brand-primary);">
          Try Again →
        </button>
      </div>

      <!-- Messages Table -->
      <div v-else class="bg-white rounded-lg shadow overflow-hidden">
        <table v-if="filteredMessages.length > 0" class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">From</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Type</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Subject</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="message in filteredMessages" :key="message.id" class="hover:bg-gray-50">
              <td class="px-6 py-4">
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ message.name }}</p>
                  <p class="text-xs text-gray-500">{{ message.email }}</p>
                  <p v-if="message.phone" class="text-xs text-gray-500">{{ message.phone }}</p>
                </div>
              </td>
              <td class="px-6 py-4 text-sm">
                <span :class="getTypeClass(message.type)">
                  {{ capitalizeFirst(message.type) }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm">
                <p class="font-medium text-gray-900 max-w-xs truncate">{{ message.subject }}</p>
              </td>
              <td class="px-6 py-4 text-sm">
                <select
                  :value="message.status"
                  @change="(e) => updateStatus(message.id, e.target.value)"
                  :class="getStatusClass(message.status)"
                  class="px-2 py-1 rounded text-xs font-medium border-0 focus:outline-none focus:ring-2 focus:ring-admin-primary"
                >
                  <option value="pending">Pending</option>
                  <option value="read">Read</option>
                  <option value="resolved">Resolved</option>
                </select>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">
                {{ formatDate(message.created_at) }}
              </td>
              <td class="px-6 py-4 text-sm flex gap-2">
                <button
                  @click="viewMessage(message)"
                  class="btn-text"
                  style="color: var(--admin-brand-primary);"
                >
                  View
                </button>
                <button
                  @click="deleteMessage(message.id)"
                  class="text-red-600 hover:text-red-700 font-medium"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Empty State -->
        <div v-else class="text-center py-12 text-gray-500">
          <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          <p>No messages found</p>
        </div>
      </div>
    </div>

    <!-- Message Detail Modal -->
    <div v-if="selectedMessage" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200 sticky top-0 bg-white">
          <h2 class="text-2xl font-bold text-gray-900">Message Details</h2>
          <button
            @click="selectedMessage = null"
            class="text-gray-500 hover:text-gray-700"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">
          <!-- Contact Info -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-gray-600">Name</p>
              <p class="text-lg font-medium text-gray-900">{{ selectedMessage.name }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Email</p>
              <a :href="`mailto:${selectedMessage.email}`" class="text-lg font-medium" style="color: var(--admin-brand-primary);">
                {{ selectedMessage.email }}
              </a>
            </div>
            <div>
              <p class="text-sm text-gray-600">Type</p>
              <span :class="getTypeClass(selectedMessage.type)" class="inline-block">
                {{ capitalizeFirst(selectedMessage.type) }}
              </span>
            </div>
            <div>
              <p class="text-sm text-gray-600">Phone</p>
              <p class="text-lg font-medium text-gray-900">{{ selectedMessage.phone || '-' }}</p>
            </div>
            <div v-if="selectedMessage.type === 'sponsorship'" colspan="2" class="col-span-2">
              <p class="text-sm text-gray-600">Company Name (from message)</p>
              <p class="text-lg font-medium text-gray-900">{{ extractCompanyName(selectedMessage.message) }}</p>
            </div>
          </div>

          <!-- Subject -->
          <div>
            <p class="text-sm text-gray-600 mb-2">Subject</p>
            <p class="text-gray-900">{{ selectedMessage.subject }}</p>
          </div>

          <!-- Message -->
          <div>
            <p class="text-sm text-gray-600 mb-2">Message</p>
            <div class="bg-gray-50 rounded-lg p-4 text-gray-900 whitespace-pre-wrap">
              {{ selectedMessage.message }}
            </div>
          </div>

          <!-- Date -->
          <div class="text-xs text-gray-500 border-t border-gray-200 pt-4">
            Received: {{ formatFullDate(selectedMessage.created_at) }}
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-between gap-3 p-6 border-t border-gray-200 bg-gray-50">
          <div class="flex gap-2">
            <button
              @click="replyToMessage(selectedMessage)"
              class="btn-primary"
            >
              Reply
            </button>
            <a
              v-if="selectedMessage.type === 'sponsorship'"
              :href="`/admin/sponsors/new?inquiry_id=${selectedMessage.id}`"
              class="btn-primary text-center"
            >
              Create Sponsor
            </a>
          </div>
          <button
            @click="selectedMessage = null"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
          >
            Close
          </button>
        </div>
      </div>
    </div>

    <!-- Reply Modal -->
    <div v-if="showReplyModal && selectedMessage" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
          <h2 class="text-2xl font-bold text-gray-900">Reply to Message</h2>
          <button
            @click="showReplyModal = false"
            class="text-gray-500 hover:text-gray-700"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="p-6 space-y-4">
          <div>
            <p class="text-sm text-gray-600 mb-2">To: {{ selectedMessage.email }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
            <input
              v-model="replyData.subject"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-admin-primary"
              placeholder="Re: Original subject"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
            <textarea
              v-model="replyData.message"
              rows="6"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-admin-primary"
              placeholder="Your reply..."
            />
          </div>
        </div>

        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50">
          <button
            @click="showReplyModal = false"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            @click="sendReply"
            :disabled="sendingReply"
            class="btn-primary disabled:opacity-50"
          >
            {{ sendingReply ? 'Sending...' : 'Send Reply' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import AdminHeader from './AdminHeader.vue';
import AdminQuickNav from './AdminQuickNav.vue';

export default {
  name: 'AdminContactMessages',
  components: {
    AdminHeader,
    AdminQuickNav,
  },
  setup() {
    const messages = ref([]);
    const loading = ref(true);
    const error = ref('');
    const searchQuery = ref('');
    const filterType = ref('');
    const filterStatus = ref('');
    const selectedMessage = ref(null);
    const showReplyModal = ref(false);
    const sendingReply = ref(false);

    const replyData = ref({
      subject: '',
      message: ''
    });

    const filteredMessages = computed(() => {
      return messages.value.filter(msg => {
        const matchesSearch = 
          msg.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          msg.email.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesType = !filterType.value || msg.type === filterType.value;
        const matchesStatus = !filterStatus.value || msg.status === filterStatus.value;
        return matchesSearch && matchesType && matchesStatus;
      });
    });

    const totalMessages = computed(() => messages.value.length);
    const pendingCount = computed(() => messages.value.filter(m => m.status === 'pending').length);
    const contactCount = computed(() => messages.value.filter(m => m.type === 'contact').length);
    const sponsorshipCount = computed(() => messages.value.filter(m => m.type === 'sponsorship').length);

    const fetchMessages = async () => {
      try {
        loading.value = true;
        error.value = '';

        const token = localStorage.getItem('auth_token');
        const response = await fetch('/api/v1/admin/contact-messages', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
          },
        });

        if (!response.ok) throw new Error('Failed to fetch messages');
        const data = await response.json();
        messages.value = data.data || [];
      } catch (err) {
        error.value = err.message || 'Failed to load messages';
        console.error('Fetch error:', err);
      } finally {
        loading.value = false;
      }
    };

    const updateStatus = async (messageId, newStatus) => {
      try {
        const token = localStorage.getItem('auth_token');
        const response = await fetch(`/api/v1/admin/contact-messages/${messageId}`, {
          method: 'PATCH',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify({ status: newStatus }),
        });

        if (!response.ok) throw new Error('Failed to update status');
        const index = messages.value.findIndex(m => m.id === messageId);
        if (index >= 0) {
          messages.value[index].status = newStatus;
        }
      } catch (err) {
        error.value = err.message || 'Failed to update status';
      }
    };

    const deleteMessage = async (messageId) => {
      if (!confirm('Are you sure you want to delete this message?')) return;

      try {
        const token = localStorage.getItem('auth_token');
        const response = await fetch(`/api/v1/admin/contact-messages/${messageId}`, {
          method: 'DELETE',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
          },
        });

        if (!response.ok) throw new Error('Failed to delete message');
        messages.value = messages.value.filter(m => m.id !== messageId);
      } catch (err) {
        error.value = err.message || 'Failed to delete message';
      }
    };

    const viewMessage = (message) => {
      selectedMessage.value = message;
      if (message.status === 'pending') {
        updateStatus(message.id, 'read');
      }
    };

    const replyToMessage = (message) => {
      replyData.value = {
        subject: `Re: ${message.subject}`,
        message: ''
      };
      showReplyModal.value = true;
    };

    const sendReply = async () => {
      if (!replyData.value.message.trim()) {
        error.value = 'Please enter a reply message';
        return;
      }

      try {
        sendingReply.value = true;
        const token = localStorage.getItem('auth_token');

        // Send email reply via your mail service
        const response = await fetch('/api/v1/admin/send-reply', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            message_id: selectedMessage.value.id,
            to_email: selectedMessage.value.email,
            subject: replyData.value.subject,
            body: replyData.value.message,
          }),
        });

        if (!response.ok) throw new Error('Failed to send reply');
        
        showReplyModal.value = false;
        replyData.value = { subject: '', message: '' };
        await updateStatus(selectedMessage.value.id, 'resolved');
      } catch (err) {
        error.value = err.message || 'Failed to send reply';
      } finally {
        sendingReply.value = false;
      }
    };

    const getTypeClass = (type) => {
      const classes = {
        contact: 'badge badge-primary',
        sponsorship: 'badge badge-info',
      };
      return classes[type] || 'badge bg-gray-100 text-gray-700';
    };

    const getStatusClass = (status) => {
      const classes = {
        pending: 'badge badge-warning',
        read: 'badge badge-primary',
        resolved: 'badge badge-success',
      };
      return classes[status] || 'badge bg-gray-100 text-gray-700';
    };

    const formatDate = (date) => {
      const d = new Date(date);
      const day = String(d.getDate()).padStart(2, '0');
      const month = String(d.getMonth() + 1).padStart(2, '0');
      const year = d.getFullYear();
      return `${day}-${month}-${year}`;
    };

    const formatFullDate = (date) => {
      const d = new Date(date);
      const day = String(d.getDate()).padStart(2, '0');
      const month = String(d.getMonth() + 1).padStart(2, '0');
      const year = d.getFullYear();
      let hours = d.getHours();
      const minutes = String(d.getMinutes()).padStart(2, '0');
      const ampm = hours >= 12 ? 'PM' : 'AM';
      hours = hours % 12 || 12;
      return `${day}-${month}-${year} ${hours}:${minutes} ${ampm}`;
    };

    const capitalizeFirst = (str) => {
      return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
    };

    const extractCompanyName = (message) => {
      const match = message.match(/Company:\s*(.+)/);
      return match ? match[1] : 'N/A';
    };

    onMounted(() => {
      fetchMessages();
    });

    return {
      messages,
      loading,
      error,
      searchQuery,
      filterType,
      filterStatus,
      selectedMessage,
      showReplyModal,
      sendingReply,
      replyData,
      filteredMessages,
      totalMessages,
      pendingCount,
      contactCount,
      sponsorshipCount,
      fetchMessages,
      updateStatus,
      deleteMessage,
      viewMessage,
      replyToMessage,
      sendReply,
      getTypeClass,
      getStatusClass,
      formatDate,
      formatFullDate,
      capitalizeFirst,
      extractCompanyName,
    };
  },
};
</script>

<style scoped>
.admin-contact-messages {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}
</style>
