<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">
            Scoring System
          </h1>
          <p class="text-gray-600 mt-2">
            Configure competition scoring criteria and weights
          </p>
        </div>
        
        <AdminQuickNav />

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Active Criteria
            </p>
            <p class="text-2xl font-bold text-gray-900">
              {{ criteria.length }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Total Weight
            </p>
            <p
              class="text-2xl font-bold"
              :class="totalWeight === 100 ? 'text-green-600' : 'text-red-600'"
            >
              {{ totalWeight }}%
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Status
            </p>
            <p
              class="text-2xl font-bold"
              :class="totalWeight === 100 ? 'text-green-600' : 'text-red-600'"
            >
              {{ totalWeight === 100 ? 'Valid' : 'Invalid' }}
            </p>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">
            Scoring Criteria
          </h2>

          <div class="space-y-4">
            <div
              v-for="item in criteria"
              :key="item.id"
              class="p-4 border border-gray-200 rounded-lg"
            >
              <div class="flex justify-between items-start mb-3">
                <div>
                  <h3 class="font-semibold text-gray-900">
                    {{ item.name }}
                  </h3>
                  <p class="text-sm text-gray-600">
                    {{ item.description }}
                  </p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">{{ item.weight }}%</span>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Weight (%)</label>
                  <input
                    v-model.number="item.weight"
                    type="number"
                    min="0"
                    max="100"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Scoring Range</label>
                  <select
                    v-model="item.range"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                  >
                    <option value="1-5">
                      1-5
                    </option>
                    <option value="1-10">
                      1-10
                    </option>
                    <option value="1-100">
                      1-100
                    </option>
                  </select>
                </div>
              </div>

              <div class="mt-4 flex gap-2">
                <button
                  class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700"
                  @click="saveCriterion(item.id)"
                >
                  Save
                </button>
                <button
                  class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700"
                  @click="removeCriterion(item.id)"
                >
                  Remove
                </button>
              </div>
            </div>
          </div>

          <div class="mt-6">
            <h3 class="font-semibold text-gray-900 mb-3">
              Add New Criterion
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
              <input
                v-model="newCriterion.name"
                type="text"
                placeholder="Criterion Name"
                class="px-3 py-2 border border-gray-300 rounded-lg"
              >
              <input
                v-model="newCriterion.description"
                type="text"
                placeholder="Short Description"
                class="px-3 py-2 border border-gray-300 rounded-lg"
              >
              <input
                v-model.number="newCriterion.weight"
                type="number"
                min="0"
                max="100"
                placeholder="Weight %"
                class="px-3 py-2 border border-gray-300 rounded-lg"
              >
            </div>
            <div class="mt-3">
              <button
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                @click="addCriterion"
              >
                Add Criterion
              </button>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">
            Scoring Guidelines
          </h2>
          <textarea
            v-model="guidelines"
            rows="6"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            placeholder="Provide scoring guidelines for judges..."
          />
          <div class="mt-4">
            <button
              class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
              @click="saveGuidelines"
            >
              Save Guidelines
            </button>
          </div>
        </div>
    </div>
    <Toast
      v-if="toast.show"
      :message="toast.message"
      :type="toast.type"
      @close="toast.show = false"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import AdminHeader from '../../components/AdminHeader.vue'
import AdminQuickNav from '../../components/AdminQuickNav.vue'
import Toast from '../../components/ui/Toast.vue'
const toast = ref({ show: false, message: '', type: 'success' })

const criteria = ref([
  { id: 1, name: 'Composition', description: 'Framing, balance, and visual structure', weight: 30, range: '1-10' },
  { id: 2, name: 'Lighting', description: 'Use of natural or artificial light', weight: 25, range: '1-10' },
  { id: 3, name: 'Creativity', description: 'Originality and artistic vision', weight: 25, range: '1-10' },
  { id: 4, name: 'Technical Quality', description: 'Focus, exposure, and clarity', weight: 20, range: '1-10' }
])

const newCriterion = ref({ name: '', description: '', weight: 0 })
const guidelines = ref('Provide consistent, objective scores. Use the full scoring range and include notes for borderline cases.')

const totalWeight = computed(() => criteria.value.reduce((sum, item) => sum + Number(item.weight || 0), 0))

const saveCriterion = (id) => {
  toast.value = { show: true, message: `Criterion ${id} saved.`, type: 'success' }
}

const removeCriterion = (id) => {
  const index = criteria.value.findIndex(item => item.id === id)
  if (index > -1) {
    criteria.value.splice(index, 1)
    toast.value = { show: true, message: 'Criterion removed.', type: 'success' }
  }
}

const addCriterion = () => {
  if (!newCriterion.value.name || !newCriterion.value.description) {
    toast.value = { show: true, message: 'Please enter name and description.', type: 'error' }
    return
  }
  criteria.value.push({
    id: Math.max(...criteria.value.map(c => c.id)) + 1,
    name: newCriterion.value.name,
    description: newCriterion.value.description,
    weight: newCriterion.value.weight || 0,
    range: '1-10'
  })
  newCriterion.value = { name: '', description: '', weight: 0 }
  toast.value = { show: true, message: 'Criterion added.', type: 'success' }
}

const saveGuidelines = () => {
  toast.value = { show: true, message: 'Guidelines saved.', type: 'success' }
}
</script>
