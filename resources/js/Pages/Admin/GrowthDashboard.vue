<template>
  <div class="p-4 sm:p-6 lg:p-8 space-y-6">
    <header>
      <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900">Growth Dashboard</h1>
      <p class="text-sm text-gray-600 mt-1">Referrals, sharing, and organic growth metrics.</p>
    </header>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
      <article class="bg-white rounded-xl border border-gray-200 p-4" v-for="card in cards" :key="card.label">
        <p class="text-xs uppercase tracking-wide text-gray-500">{{ card.label }}</p>
        <p class="text-2xl font-semibold text-gray-900 mt-1">{{ card.value }}</p>
      </article>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-2 gap-4">
      <article class="bg-white rounded-xl border border-gray-200 p-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Top Referrers</h2>
        <div class="overflow-x-auto">
          <table class="w-full min-w-[520px] text-left text-sm">
            <thead>
              <tr class="border-b border-gray-200 text-gray-600">
                <th class="py-2 pr-2">User</th>
                <th class="py-2 pr-2">Code</th>
                <th class="py-2 pr-2">Photographer Referrals</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in topReferrers" :key="item.id" class="border-b border-gray-100">
                <td class="py-2 pr-2">{{ item.name }}</td>
                <td class="py-2 pr-2 font-mono">{{ item.referral_code || '—' }}</td>
                <td class="py-2 pr-2 font-semibold">{{ item.successful_photographer_referrals || 0 }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </article>

      <article class="bg-white rounded-xl border border-gray-200 p-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Shares by Platform</h2>
        <ul class="space-y-2">
          <li v-for="item in sharesByPlatform" :key="item.platform" class="flex items-center justify-between text-sm">
            <span class="capitalize">{{ item.platform }}</span>
            <strong>{{ item.total }}</strong>
          </li>
        </ul>

        <h2 class="text-lg font-semibold text-gray-900 mb-3 mt-6">Shares by Entity</h2>
        <ul class="space-y-2">
          <li v-for="item in sharesByType" :key="item.entity_type" class="flex items-center justify-between text-sm">
            <span class="capitalize">{{ item.entity_type }}</span>
            <strong>{{ item.total }}</strong>
          </li>
        </ul>
      </article>
    </section>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import api from '../../api';

const metrics = ref({
  total_referrals: 0,
  successful_referrals: 0,
  profile_shares: 0,
  competition_shares: 0,
  event_shares: 0,
});

const topReferrers = ref([]);
const sharesByPlatform = ref([]);
const sharesByType = ref([]);

const cards = computed(() => [
  { label: 'Total Referrals', value: metrics.value.total_referrals || 0 },
  { label: 'Successful Referrals', value: metrics.value.successful_referrals || 0 },
  { label: 'Profile Shares', value: metrics.value.profile_shares || 0 },
  { label: 'Competition Shares', value: metrics.value.competition_shares || 0 },
  { label: 'Event Shares', value: metrics.value.event_shares || 0 },
]);

const load = async () => {
  try {
    const { data } = await api.get('/admin/growth/dashboard');
    const payload = data?.data || {};
    metrics.value = payload.metrics || metrics.value;
    topReferrers.value = payload.top_referrers || [];
    sharesByPlatform.value = payload.shares_by_platform || [];
    sharesByType.value = payload.shares_by_type || [];
  } catch (error) {
    console.error('Failed to load growth dashboard:', error);
  }
};

onMounted(load);
</script>
