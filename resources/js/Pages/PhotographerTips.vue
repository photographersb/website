<template>
  <div class="min-h-screen bg-[#f7f2ee] text-[#1d1014]">
    <div class="relative overflow-hidden bg-[#1b0b12]">
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(245,158,11,0.28)_0,_transparent_55%)]" />
      <div class="container mx-auto px-4 relative pt-10 pb-16">
        <button
          class="inline-flex items-center gap-2 rounded-full border border-white border-opacity-20 bg-white bg-opacity-10 px-4 py-2 text-sm font-medium text-white text-opacity-90 transition hover:bg-white hover:bg-opacity-20"
          @click="goBack"
        >
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M10 19l-7-7m0 0l7-7m-7 7h18"
            />
          </svg>
          Back to profile
        </button>

        <div class="mt-10 max-w-3xl text-white">
          <p class="text-xs uppercase tracking-[0.35em] text-white text-opacity-70">
            Tip the photographer
          </p>
          <h1 class="mt-4 text-3xl md:text-4xl font-semibold font-serif tracking-tight">
            {{ photographerName || 'Support this photographer' }}
          </h1>
          <p class="mt-3 text-base text-white text-opacity-80">
            Send a tip securely and support the creator behind the work.
          </p>
        </div>
      </div>
    </div>

    <div class="container mx-auto px-4 max-w-5xl -mt-10 relative z-10 pb-12">
      <div class="bg-white bg-opacity-90 rounded-2xl border border-[#eadfd7] shadow-lg p-6">
        <div
          v-if="loading"
          class="text-sm text-[#7a1f2b]"
        >
          Loading tip details...
        </div>
        <div
          v-else-if="error"
          class="text-sm text-[#7a1f2b]"
        >
          Tip details are unavailable right now.
        </div>
        <BuyMeCoffeeButton
          v-else
          :photographerId="photographerId"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../api'
import BuyMeCoffeeButton from '../components/BuyMeCoffeeButton.vue'

const route = useRoute()
const router = useRouter()

const photographerId = ref(null)
const photographerName = ref('')
const loading = ref(true)
const error = ref(false)

const goBack = () => {
  router.push(`/photographer/${route.params.slug}`)
}

const fetchPhotographer = async () => {
  loading.value = true
  error.value = false

  try {
    const { data } = await api.get(`/photographers/${route.params.slug}`)
    if (data.status === 'success') {
      photographerId.value = data.data?.id || null
      photographerName.value = data.data?.user?.name
        || data.data?.business_name
        || 'Photographer'
    } else {
      error.value = true
    }
  } catch (err) {
    error.value = true
  } finally {
    loading.value = false
  }
}

onMounted(fetchPhotographer)
</script>
