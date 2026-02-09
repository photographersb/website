<template>
  <AdminLayout 
    page-title="Bookings Management"
    page-description="Manage all photography bookings"
    :show-breadcrumbs="true"
  >
    <BaseModal 
      :is-open="showModal"
      :title="editingBooking ? 'Edit Booking' : 'Booking Details'"
      :is-loading="isSubmitting"
      @close="showModal = false"
      @submit="handleSaveBooking"
    >
      <div class="space-y-3">
        <div v-if="editingBooking">
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select
            v-model="formData.status"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
          >
            <option value="pending">
              Pending
            </option>
            <option value="confirmed">
              Confirmed
            </option>
            <option value="completed">
              Completed
            </option>
            <option value="cancelled">
              Cancelled
            </option>
          </select>
        </div>
        <div v-else>
          <p><strong>Client:</strong> {{ editingBooking?.client_name }}</p>
          <p><strong>Photographer:</strong> {{ editingBooking?.photographer_name }}</p>
          <p><strong>Date:</strong> {{ formatDate(editingBooking?.booking_date) }}</p>
          <p><strong>Amount:</strong> {{ formatCurrency(editingBooking?.amount) }}</p>
          <p><strong>Status:</strong> {{ editingBooking?.status }}</p>
        </div>
      </div>
    </BaseModal>

    <div class="space-y-6">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col sm:flex-row gap-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by client or photographer..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
        <select
          v-model="statusFilter"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
          <option value="">
            All Status
          </option>
          <option value="pending">
            Pending
          </option>
          <option value="confirmed">
            Confirmed
          </option>
          <option value="completed">
            Completed
          </option>
          <option value="cancelled">
            Cancelled
          </option>
        </select>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
        <div
          v-if="isLoading"
          class="p-8 text-center"
        >
          Loading bookings...
        </div>
        <div
          v-else-if="filteredBookings.length === 0"
          class="p-12 text-center text-gray-500"
        >
          No bookings found
        </div>
        <table
          v-else
          class="w-full text-sm"
        >
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Client
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Photographer
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Date
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Amount
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Status
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr
              v-for="booking in filteredBookings"
              :key="booking.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4">
                {{ booking.client_name }}
              </td>
              <td class="px-6 py-4">
                {{ booking.photographer_name }}
              </td>
              <td class="px-6 py-4">
                {{ formatDate(booking.booking_date) }}
              </td>
              <td class="px-6 py-4 font-medium">
                {{ formatCurrency(booking.amount) }}
              </td>
              <td class="px-6 py-4">
                <span :class="`px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(booking.status)}`">
                  {{ booking.status }}
                </span>
              </td>
              <td class="px-6 py-4">
                <button
                  class="text-blue-600 hover:text-blue-800 text-sm font-medium mr-3"
                  @click="editBooking(booking)"
                >
                  Edit
                </button>
                <button
                  class="text-red-600 hover:text-red-800 text-sm font-medium"
                  @click="deleteBooking(booking)"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue';
import AdminLayout from '../../AdminLayout.vue';
import BaseModal from '../modals/BaseModal.vue';
import { formatDate as formatDateValue } from '../../../utils/formatters';

const bookings = ref([]);
const isLoading = ref(false);
const isSubmitting = ref(false);
const searchQuery = ref('');
const statusFilter = ref('');
const showModal = ref(false);
const editingBooking = ref(null);
const formData = ref({ status: 'pending' });
const addAlert = inject('addAlert', null);

const filteredBookings = computed(() => {
  let filtered = bookings.value;
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    filtered = filtered.filter(b => b.client_name.toLowerCase().includes(q) || b.photographer_name.toLowerCase().includes(q));
  }
  if (statusFilter.value) filtered = filtered.filter(b => b.status === statusFilter.value);
  return filtered;
});

const formatDate = (date) => {
  if (!date) return '';
  return formatDateValue(date);
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount || 0);
};

const getStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
};

const fetchBookings = async () => {
  isLoading.value = true;
  try {
    const response = await fetch('/api/v1/admin/bookings', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    if (response.ok) {
      const data = await response.json();
      bookings.value = data.data || [];
    }
  } catch (error) {
    console.error('Error:', error);
    if (addAlert) addAlert('Failed to load bookings', 'error');
  } finally {
    isLoading.value = false;
  }
};

const editBooking = (booking) => {
  editingBooking.value = booking;
  formData.value = { status: booking.status };
  showModal.value = true;
};

const handleSaveBooking = async () => {
  isSubmitting.value = true;
  try {
    const response = await fetch(`/api/v1/admin/bookings/${editingBooking.value.id}`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(formData.value)
    });
    if (response.ok) {
      const data = await response.json();
      const index = bookings.value.findIndex(b => b.id === editingBooking.value.id);
      bookings.value[index] = data.data;
      if (addAlert) addAlert('Booking updated', 'success');
      showModal.value = false;
    }
  } catch (error) {
    if (addAlert) addAlert('Failed to update booking', 'error');
  } finally {
    isSubmitting.value = false;
  }
};

const deleteBooking = async (booking) => {
  if (!confirm('Delete this booking?')) return;
  try {
    const response = await fetch(`/api/v1/admin/bookings/${booking.id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    if (response.ok) {
      bookings.value = bookings.value.filter(b => b.id !== booking.id);
      if (addAlert) addAlert('Booking deleted', 'success');
    }
  } catch (error) {
    if (addAlert) addAlert('Failed to delete booking', 'error');
  }
};

onMounted(fetchBookings);
</script>
