<template>
  <AdminLayout 
    page-title="Error Center"
    page-description="Monitor and manage system errors"
    :show-breadcrumbs="true"
  >
    <div class="space-y-6">
      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-4 border border-red-200">
          <p class="text-sm text-red-600 font-medium">
            Critical (P0)
          </p>
          <p class="text-3xl font-bold text-red-800 mt-2">
            {{ errorStats.critical }}
          </p>
        </div>
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-4 border border-yellow-200">
          <p class="text-sm text-yellow-600 font-medium">
            High (P1)
          </p>
          <p class="text-3xl font-bold text-yellow-800 mt-2">
            {{ errorStats.high }}
          </p>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
          <p class="text-sm text-blue-600 font-medium">
            Medium (P2)
          </p>
          <p class="text-3xl font-bold text-blue-800 mt-2">
            {{ errorStats.medium }}
          </p>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
          <p class="text-sm text-green-600 font-medium">
            Resolved
          </p>
          <p class="text-3xl font-bold text-green-800 mt-2">
            {{ errorStats.resolved }}
          </p>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col sm:flex-row gap-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search errors..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
        <select
          v-model="severityFilter"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
          <option value="">
            All Severity
          </option>
          <option value="P0">
            Critical (P0)
          </option>
          <option value="P1">
            High (P1)
          </option>
          <option value="P2">
            Medium (P2)
          </option>
        </select>
        <select
          v-model="resolvedFilter"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
          <option value="">
            All Status
          </option>
          <option value="false">
            Open
          </option>
          <option value="true">
            Resolved
          </option>
        </select>
      </div>

      <!-- Errors Table -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
        <div
          v-if="isLoading"
          class="p-8 text-center"
        >
          Loading errors...
        </div>
        <div
          v-else-if="filteredErrors.length === 0"
          class="p-12 text-center text-gray-500"
        >
          No errors found
        </div>
        <table
          v-else
          class="w-full text-sm"
        >
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Type
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Message
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                File:Line
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Count
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Last Seen
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Status
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr
              v-for="error in filteredErrors"
              :key="error.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-3">
                <span
                  :class="getSeverityColor(error.severity)"
                  class="px-2 py-1 rounded-full text-xs font-medium"
                >{{ error.severity }}</span>
              </td>
              <td class="px-6 py-3 text-gray-900">
                {{ error.message }}
              </td>
              <td class="px-6 py-3 text-gray-600 font-mono text-xs">
                {{ error.file }}:{{ error.line }}
              </td>
              <td class="px-6 py-3 text-gray-600">
                {{ error.occurrence_count }}
              </td>
              <td class="px-6 py-3 text-gray-600">
                {{ formatDate(error.last_occurrence_at) }}
              </td>
              <td class="px-6 py-3">
                <select
                  :value="error.is_resolved"
                  class="px-3 py-1 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                  @change="(e) => updateErrorStatus(error, e)"
                >
                  <option :value="false">
                    Open
                  </option>
                  <option :value="true">
                    Resolved
                  </option>
                </select>
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
import { formatDateTime as formatDateTimeValue } from '../../../utils/formatters';

const errors = ref([]);
const isLoading = ref(false);
const searchQuery = ref('');
const severityFilter = ref('');
const resolvedFilter = ref('');
const addAlert = inject('addAlert', null);

const errorStats = computed(() => {
  return {
    critical: errors.value.filter(e => e.severity === 'P0').length,
    high: errors.value.filter(e => e.severity === 'P1').length,
    medium: errors.value.filter(e => e.severity === 'P2').length,
    resolved: errors.value.filter(e => e.is_resolved).length,
  };
});

const filteredErrors = computed(() => {
  let filtered = errors.value;
  
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    filtered = filtered.filter(e => 
      e.message.toLowerCase().includes(q) || 
      e.exception_class?.toLowerCase().includes(q) ||
      e.file.toLowerCase().includes(q)
    );
  }
  
  if (severityFilter.value) filtered = filtered.filter(e => e.severity === severityFilter.value);
  if (resolvedFilter.value !== '') filtered = filtered.filter(e => e.is_resolved === (resolvedFilter.value === 'true'));
  
  return filtered.sort((a, b) => new Date(b.last_occurrence_at) - new Date(a.last_occurrence_at));
});

const getSeverityColor = (severity) => {
  const colors = {
    'P0': 'bg-red-100 text-red-800',
    'P1': 'bg-yellow-100 text-yellow-800',
    'P2': 'bg-blue-100 text-blue-800',
  };
  return colors[severity] || 'bg-gray-100 text-gray-800';
};

const formatDate = (date) => {
  if (!date) return '';
  return formatDateTimeValue(date);
};

const fetchErrors = async () => {
  isLoading.value = true;
  try {
    const response = await fetch('/api/v1/admin/errors', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    if (response.ok) {
      const data = await response.json();
      errors.value = data.data || [];
    }
  } catch (error) {
    console.error('Error:', error);
    if (addAlert) addAlert('Failed to load errors', 'error');
  } finally {
    isLoading.value = false;
  }
};

const updateErrorStatus = async (error, event) => {
  const newStatus = event.target.value === 'true';
  try {
    const response = await fetch(`/api/v1/admin/errors/${error.id}`, {
      method: 'PATCH',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ is_resolved: newStatus })
    });
    if (response.ok) {
      error.is_resolved = newStatus;
      if (addAlert) addAlert('Error status updated', 'success');
    }
  } catch (err) {
    if (addAlert) addAlert('Failed to update error status', 'error');
  }
};

onMounted(fetchErrors);
</script>