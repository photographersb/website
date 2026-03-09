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

      <section class="bg-white rounded-2xl border border-[#eadfd7] p-4 sm:p-6 mt-6 space-y-4">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">My Referral Hub</h2>

        <p v-if="feedbackMessage" class="text-xs text-gray-600">{{ feedbackMessage }}</p>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
          <div class="rounded-xl border border-gray-200 p-3 bg-gray-50">
            <p class="text-xs text-gray-500">Referral code</p>
            <p class="text-sm font-semibold text-gray-900 mt-1">{{ mySummary?.referral_code || 'Sign in required' }}</p>
          </div>
          <div class="rounded-xl border border-gray-200 p-3 bg-gray-50">
            <p class="text-xs text-gray-500">Total referrals</p>
            <p class="text-sm font-semibold text-gray-900 mt-1">{{ mySummary?.total_referrals || 0 }}</p>
          </div>
          <div class="rounded-xl border border-gray-200 p-3 bg-gray-50">
            <p class="text-xs text-gray-500">Successful</p>
            <p class="text-sm font-semibold text-gray-900 mt-1">{{ mySummary?.successful_referrals || 0 }}</p>
          </div>
          <div class="rounded-xl border border-gray-200 p-3 bg-gray-50">
            <p class="text-xs text-gray-500">Photographer referrals</p>
            <p class="text-sm font-semibold text-gray-900 mt-1">{{ mySummary?.successful_photographer_referrals || 0 }}</p>
          </div>
        </div>

        <div class="border border-gray-200 rounded-xl p-3 bg-gray-50/70 space-y-2">
          <p class="text-sm font-semibold text-gray-900">Invite by Email</p>
          <textarea
            v-model="inviteEmailsRaw"
            rows="2"
            placeholder="Enter up to 10 emails, separated by commas"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
          />
          <div class="flex items-center gap-2">
            <button class="text-xs px-3 py-1.5 rounded bg-[#7a1f2b] text-white hover:bg-[#5f1421]" @click="sendInvites">Send Invites</button>
            <button class="text-xs px-3 py-1.5 rounded border border-gray-300 text-gray-700 hover:bg-gray-100" @click="copyReferralUrl">Copy Referral URL</button>
          </div>
        </div>

        <div class="border border-gray-200 rounded-xl p-3 bg-gray-50/70 space-y-2">
          <p class="text-sm font-semibold text-gray-900">Share Frame</p>
          <div class="flex items-center gap-2">
            <button class="text-xs px-3 py-1.5 rounded bg-gray-900 text-white hover:bg-black" @click="generateShareFrame">Generate Preview</button>
            <span class="text-xs text-gray-500">Uses growth share-frame API</span>
          </div>
          <div v-if="shareFrame" class="rounded-lg border border-gray-200 bg-white p-2.5 text-xs text-gray-700">
            <p><strong>Title:</strong> {{ shareFrame.title }}</p>
            <p><strong>Subtitle:</strong> {{ shareFrame.subtitle }}</p>
            <p><strong>Theme:</strong> {{ shareFrame.theme }}</p>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api';

const router = useRouter();
const loading = ref(false);
const rows = ref([]);
const mySummary = ref(null);
const inviteEmailsRaw = ref('');
const feedbackMessage = ref('');
const shareFrame = ref(null);

const handleAuthError = (error, fallbackMessage) => {
  const status = error?.response?.status;
  if (status === 401 || status === 403) {
    feedbackMessage.value = 'Please sign in to use referral actions.';
    router.push('/auth');
    return;
  }

  feedbackMessage.value = error?.response?.data?.message || fallbackMessage;
};

const load = async () => {
  try {
    loading.value = true;
    const { data } = await api.get('/growth/leaderboard', { params: { limit: 50 } });
    rows.value = data?.data || [];
  } catch (error) {
    console.error('Failed to load top referrers:', error);
    feedbackMessage.value = error?.response?.data?.message || 'Unable to load leaderboard.';
  } finally {
    loading.value = false;
  }
};

const loadMyReferrals = async () => {
  try {
    const { data } = await api.get('/growth/my-referrals');
    mySummary.value = data?.data || null;
  } catch (error) {
    mySummary.value = null;
    if (error?.response?.status !== 401 && error?.response?.status !== 403) {
      feedbackMessage.value = error?.response?.data?.message || 'Unable to load your referral summary.';
    }
  }
};

const sendInvites = async () => {
  const emails = inviteEmailsRaw.value
    .split(',')
    .map((item) => item.trim())
    .filter(Boolean);

  if (emails.length === 0) {
    feedbackMessage.value = 'Please enter at least one email address.';
    return;
  }

  try {
    const { data } = await api.post('/growth/invite-email', { emails });
    inviteEmailsRaw.value = '';
    feedbackMessage.value = `Invite emails sent: ${data?.data?.count || emails.length}`;
  } catch (error) {
    handleAuthError(error, 'Unable to send invite emails right now.');
  }
};

const copyReferralUrl = async () => {
  const url = mySummary.value?.referral_url;
  if (!url) {
    feedbackMessage.value = 'Referral URL not available. Please sign in first.';
    return;
  }

  await navigator.clipboard.writeText(url);
  feedbackMessage.value = 'Referral URL copied.';
};

const generateShareFrame = async () => {
  try {
    const { data } = await api.get('/growth/share-frame', {
      params: {
        type: 'event_participation',
        title: 'Photographer SB Referral Champion',
        subtitle: 'Invite and grow with the community',
      },
    });
    shareFrame.value = data?.data || null;
    feedbackMessage.value = 'Share frame preview generated.';
  } catch (error) {
    handleAuthError(error, 'Unable to generate share frame right now.');
  }
};

onMounted(async () => {
  await Promise.all([load(), loadMyReferrals()]);
});
</script>
