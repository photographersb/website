<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Photographer Verifications
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <AdminQuickNav />

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
          <h3 class="font-semibold text-gray-800 mb-4">
            Filters
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm text-gray-700 mb-1">Status</label>
              <select class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                <option value="">
                  All
                </option>
                <option value="pending">
                  Pending
                </option>
                <option value="approved">
                  Approved
                </option>
                <option value="rejected">
                  Rejected
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm text-gray-700 mb-1">Type</label>
              <select class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                <option value="">
                  All
                </option>
                <option value="phone">
                  Phone
                </option>
                <option value="nid">
                  National ID
                </option>
                <option value="business">
                  Business
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm text-gray-700 mb-1">Search</label>
              <input
                type="text"
                placeholder="Name, email..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg"
              >
            </div>
          </div>
        </div>

        <!-- Verifications Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <table class="w-full">
            <thead class="bg-gray-50 border-b">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                  Photographer
                </th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                  Type
                </th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                  Status
                </th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                  Action
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="v in verifications"
                :key="v.id"
                class="border-b hover:bg-gray-50"
              >
                <td class="px-6 py-4">
                  {{ v.user.name }}
                </td>
                <td class="px-6 py-4">
                  {{ v.type }}
                </td>
                <td class="px-6 py-4">
                  <span
                    class="px-2 py-1 rounded text-xs font-semibold"
                    :class="statusClassMap[v.status] || statusClassMap.default"
                  >{{ v.status }}</span>
                </td>
                <td class="px-6 py-4">
                  <Link
                    :href="route('admin.verifications.show', v.id)"
                    class="text-blue-600 hover:text-blue-800"
                  >
                    Review
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link } from '@inertiajs/vue3'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'

defineProps({
  verifications: Array
})

const statusClassMap = {
  approved: 'bg-green-100',
  pending: 'bg-yellow-100',
  rejected: 'bg-red-100',
  default: 'bg-gray-100'
}
</script>
