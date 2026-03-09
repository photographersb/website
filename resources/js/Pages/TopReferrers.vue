<template>
  <div class="min-h-screen bg-[#f7f2ee] py-6 sm:py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 xl:px-12 max-w-5xl">
      <section class="rounded-2xl bg-[#1b0b12] text-white p-5 sm:p-7 mb-6">
        <h1 class="text-2xl sm:text-3xl font-semibold font-serif">Community Top Referrers</h1>
        <p class="text-white/80 text-sm sm:text-base mt-2">
          Users who invited the most photographers to Photographer SB.
        </p>
      </section>

      <section class="bg-white rounded-2xl border border-[#eadfd7] p-4 sm:p-6">
        <div v-if="loading" class="py-10 text-center text-gray-600" role="status" aria-live="polite">Loading leaderboard...</div>

        <div v-else>
          <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left">
              <thead>
                <tr class="border-b border-gray-200 text-xs uppercase tracking-wide text-gray-600">
                  <th class="py-3 px-2">Rank</th>
                  <th class="py-3 px-2">User</th>
                  <th class="py-3 px-2">Referral Code</th>
                  <th class="py-3 px-2">Photographers Invited</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in rows" :key="item.id" class="border-b border-gray-100">
                  <td class="py-3 px-2 font-semibold text-[#7a1f2b]">#{{ index + 1 }}</td>
                  <td class="py-3 px-2 text-gray-900">
                    {{ item.name }}
                    <span v-if="item.username" class="text-gray-500">(@{{ item.username }})</span>
                  </td>
                  <td class="py-3 px-2 text-sm font-mono text-gray-700">{{ item.referral_code || '—' }}</td>
                  <td class="py-3 px-2 font-semibold text-gray-900">{{ item.successful_photographer_referrals || 0 }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <p v-if="!rows.length" class="text-sm text-gray-600 mt-4">No referrer data yet.</p>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import api from '../api';

const loading = ref(false);
const rows = ref([]);

const load = async () => {
  try {
    loading.value = true;
    const { data } = await api.get('/growth/top-referrers', { params: { limit: 50 } });
    rows.value = data?.data || [];
  } catch (error) {
    console.error('Failed to load top referrers:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(load);
</script>
