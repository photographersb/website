<template>
  <div class="min-h-screen bg-gray-50 py-10 px-4">
    <div class="max-w-3xl mx-auto">
      <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-start gap-4">
          <div class="w-16 h-16 rounded-full bg-burgundy-100 flex items-center justify-center text-burgundy-700 text-xl font-bold">
            {{ initials }}
          </div>
          <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ photographerName }}</h1>
            <p class="text-gray-600">Photographer Verification Status</p>

            <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-full" :class="badgeClass">
              <span class="w-2 h-2 rounded-full" :class="dotClass"></span>
              <span class="text-sm font-medium">{{ verificationLabel }}</span>
            </div>
          </div>
        </div>

        <div class="mt-6 space-y-2 text-sm text-gray-700">
          <div v-if="photographer?.slug"><span class="text-gray-500">Profile:</span> /photographer/{{ photographer.slug }}</div>
          <div v-if="photographer?.city"><span class="text-gray-500">City:</span> {{ photographer.city?.name || photographer.city }}</div>
          <div v-if="photographer?.average_rating"><span class="text-gray-500">Rating:</span> {{ photographer.average_rating }}</div>
        </div>
      </div>

      <div class="mt-6 text-center text-sm text-gray-500">
        This verification is based on platform records and may update.
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../api'

const route = useRoute()
const photographer = ref(null)

const fetchPhotographer = async () => {
  const response = await api.get(`/photographers/${route.params.slug}`)
  photographer.value = response.data.data || response.data
}

const photographerName = computed(() => {
  return photographer.value?.business_name || photographer.value?.user?.name || 'Photographer'
})

const initials = computed(() => {
  const name = photographerName.value || ''
  return name.split(' ').map(p => p[0]).join('').slice(0, 2).toUpperCase()
})

const isVerified = computed(() => !!photographer.value?.is_verified)

const verificationLabel = computed(() => (isVerified.value ? 'Verified Photographer' : 'Not Verified'))

const badgeClass = computed(() =>
  isVerified.value ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'
)

const dotClass = computed(() =>
  isVerified.value ? 'bg-green-600' : 'bg-gray-500'
)

onMounted(fetchPhotographer)
</script>
