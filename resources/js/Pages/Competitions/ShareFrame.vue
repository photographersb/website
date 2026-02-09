<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">
              Share Your Entry
            </h1>
            <p class="text-gray-600 mt-1">
              Generate beautiful social media frames to share and get votes
            </p>
          </div>
          <Link
            :href="`/competitions/${competition.id}/submissions/${submission.id}`"
            class="text-burgundy-600 hover:text-burgundy-700 font-medium"
          >
            ← Back to Submission
          </Link>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Success/Error Messages -->
      <div
        v-if="$page.props.flash?.success"
        class="mb-6"
      >
        <div class="bg-green-50 border border-green-300 rounded-lg p-4">
          <div class="flex items-start gap-3">
            <div class="text-green-600 text-lg font-bold">
              ✓
            </div>
            <div>
              <h3 class="text-green-900 font-semibold">
                Success
              </h3>
              <p class="text-green-700 text-sm mt-1">
                {{ $page.props.flash.success }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <div
        v-if="$page.props.flash?.error"
        class="mb-6"
      >
        <div class="bg-red-50 border border-red-300 rounded-lg p-4">
          <div class="flex items-start gap-3">
            <div class="text-red-600 text-lg font-bold">
              ⚠️
            </div>
            <div>
              <h3 class="text-red-900 font-semibold">
                Error
              </h3>
              <p class="text-red-700 text-sm mt-1">
                {{ $page.props.flash.error }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div
        v-if="!shareFrame"
        class="max-w-2xl mx-auto"
      >
        <!-- Generate Instructions -->
        <div class="bg-white rounded-lg shadow-card p-8 text-center">
          <div class="w-16 h-16 bg-burgundy-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg
              class="w-8 h-8 text-burgundy-600"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"
              />
            </svg>
          </div>
          
          <h2 class="text-2xl font-bold text-gray-900 mb-3">
            Generate Share Frames
          </h2>
          <p class="text-gray-600 mb-6">
            Create professionally designed frames in multiple formats optimized for Instagram, Facebook, and WhatsApp. Each frame includes a QR code for easy voting.
          </p>

          <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="text-left">
              <div class="text-burgundy-600 font-semibold mb-1">
                📱 Instagram Story
              </div>
              <p class="text-sm text-gray-600">
                1080×1920 (9:16)
              </p>
            </div>
            <div class="text-left">
              <div class="text-burgundy-600 font-semibold mb-1">
                📷 Instagram Post
              </div>
              <p class="text-sm text-gray-600">
                1080×1080 (1:1)
              </p>
            </div>
            <div class="text-left">
              <div class="text-burgundy-600 font-semibold mb-1">
                🖼️ Portrait
              </div>
              <p class="text-sm text-gray-600">
                1080×1350 (4:5)
              </p>
            </div>
            <div class="text-left">
              <div class="text-burgundy-600 font-semibold mb-1">
                🌄 Landscape
              </div>
              <p class="text-sm text-gray-600">
                1200×675 (16:9)
              </p>
            </div>
          </div>

          <button
            :disabled="generating"
            class="bg-burgundy-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-burgundy-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
            @click="generateFrames"
          >
            {{ generating ? 'Generating Frames...' : 'Generate Share Frames' }}
          </button>
        </div>
      </div>

      <!-- Share Frames Display -->
      <div
        v-else
        class="space-y-6"
      >
        <!-- Vote Link -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <div class="flex items-center gap-2 mb-4">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-burgundy-50 text-burgundy-700">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553 2.276a2 2 0 010 3.448L15 18M4 6v12a2 2 0 002 2h8a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2z" />
              </svg>
            </span>
            <div>
              <h2 class="text-xl font-bold text-gray-900">
                Share Your Vote Link
              </h2>
              <p class="text-sm text-gray-500">Send the message with a full preview-ready link.</p>
            </div>
          </div>

          <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="flex items-center gap-2 flex-1 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
              <svg class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.172 13.828a4 4 0 010-5.656l3-3a4 4 0 015.656 5.656l-1.5 1.5" />
              </svg>
              <input
                :value="voteShareText"
                readonly
                class="w-full bg-transparent text-sm text-gray-700 focus:outline-none"
              >
            </div>
            <button
              class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-lg bg-burgundy-600 text-white font-semibold hover:bg-burgundy-700 transition"
              @click="copyVoteLink"
            >
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h8m-8-4h8m-5-6h5a2 2 0 012 2v12a2 2 0 01-2 2H9a2 2 0 01-2-2v-5" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7H5a2 2 0 00-2 2v10a2 2 0 002 2h2" />
              </svg>
              {{ copied ? 'Copied' : 'Copy Message' }}
            </button>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-5">
            <button
              class="flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50/60 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100"
              @click="shareVote('whatsapp')"
            >
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M20.52 3.48A11.91 11.91 0 0012.03 0C5.38 0 .02 5.36 0 12a12.07 12.07 0 001.6 6.07L0 24l6.14-1.6a12.1 12.1 0 005.89 1.5h.01c6.64 0 12.02-5.36 12.02-12a11.9 11.9 0 00-3.54-8.42z" />
                </svg>
              </span>
              WhatsApp
            </button>
            <button
              class="flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50/60 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100"
              @click="shareVote('facebook')"
            >
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M24 12.07C24 5.4 18.63 0 12 0S0 5.4 0 12.07c0 5.99 4.39 10.95 10.12 11.85v-8.38H7.08v-3.47h3.04V9.43c0-3 1.79-4.67 4.53-4.67 1.32 0 2.69.23 2.69.23v2.96h-1.52c-1.49 0-1.96.93-1.96 1.87v2.25h3.33l-.53 3.47h-2.8v8.38C19.61 23.02 24 18.06 24 12.07z" />
                </svg>
              </span>
              Facebook
            </button>
            <button
              class="flex items-center gap-2 rounded-lg border border-indigo-200 bg-indigo-50/60 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100"
              @click="shareVote('messenger')"
            >
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 0C5.38 0 0 4.95 0 11.05c0 3.48 1.75 6.57 4.5 8.6V24l4.1-2.26c1.13.31 2.33.48 3.4.48 6.62 0 12-4.95 12-11.05C24 4.95 18.62 0 12 0zm1.44 13.73l-3.06-3.27-5.99 3.27 6.58-6.99 3.1 3.27 5.95-3.27-6.58 6.99z" />
                </svg>
              </span>
              Messenger
            </button>
            <button
              class="flex items-center gap-2 rounded-lg border border-sky-200 bg-sky-50/60 px-3 py-2 text-sm font-semibold text-sky-700 hover:bg-sky-100"
              @click="shareVote('telegram')"
            >
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-sky-100">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M22 2L2 11l5.5 2.1L9 21l3.4-4.3L18 20l4-18z" />
                </svg>
              </span>
              Telegram
            </button>
          </div>

          <p class="text-sm text-gray-600 mt-3">
            Share this message on social media or in groups to drive votes fast.
          </p>
        </div>

        <!-- Frame Downloads -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">
              🖼️ Download Share Frames
            </h2>
            <button
              :disabled="regenerating"
              class="text-sm text-burgundy-600 hover:text-burgundy-700 font-medium"
              @click="regenerateFrames"
            >
              {{ regenerating ? 'Regenerating...' : '🔄 Regenerate' }}
            </button>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Instagram Story -->
            <div class="border border-gray-200 rounded-lg p-4">
              <div class="aspect-[9/16] bg-gray-100 rounded-lg mb-3 overflow-hidden">
                <img
                  :src="shareFrame.story_frame_url"
                  alt="Instagram Story Frame"
                  class="w-full h-full object-contain"
                >
              </div>
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="font-semibold text-gray-900">
                    Instagram Story
                  </h3>
                  <p class="text-sm text-gray-600">
                    1080×1920 (9:16)
                  </p>
                </div>
                <a
                  :href="`/competitions/${competition.id}/submissions/${submission.id}/share-frame/download/story`"
                  class="px-4 py-2 bg-burgundy-600 text-white rounded-lg font-semibold hover:bg-burgundy-700 transition text-sm"
                >
                  Download
                </a>
              </div>
            </div>

            <!-- Instagram Post -->
            <div class="border border-gray-200 rounded-lg p-4">
              <div class="aspect-square bg-gray-100 rounded-lg mb-3 overflow-hidden">
                <img
                  :src="shareFrame.post_frame_url"
                  alt="Instagram Post Frame"
                  class="w-full h-full object-contain"
                >
              </div>
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="font-semibold text-gray-900">
                    Instagram Post
                  </h3>
                  <p class="text-sm text-gray-600">
                    1080×1080 (1:1)
                  </p>
                </div>
                <a
                  :href="`/competitions/${competition.id}/submissions/${submission.id}/share-frame/download/post`"
                  class="px-4 py-2 bg-burgundy-600 text-white rounded-lg font-semibold hover:bg-burgundy-700 transition text-sm"
                >
                  Download
                </a>
              </div>
            </div>

            <!-- Portrait -->
            <div class="border border-gray-200 rounded-lg p-4">
              <div class="aspect-[4/5] bg-gray-100 rounded-lg mb-3 overflow-hidden">
                <img
                  :src="shareFrame.portrait_frame_url"
                  alt="Portrait Frame"
                  class="w-full h-full object-contain"
                >
              </div>
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="font-semibold text-gray-900">
                    Portrait
                  </h3>
                  <p class="text-sm text-gray-600">
                    1080×1350 (4:5)
                  </p>
                </div>
                <a
                  :href="`/competitions/${competition.id}/submissions/${submission.id}/share-frame/download/portrait`"
                  class="px-4 py-2 bg-burgundy-600 text-white rounded-lg font-semibold hover:bg-burgundy-700 transition text-sm"
                >
                  Download
                </a>
              </div>
            </div>

            <!-- Landscape -->
            <div class="border border-gray-200 rounded-lg p-4">
              <div class="aspect-[16/9] bg-gray-100 rounded-lg mb-3 overflow-hidden">
                <img
                  :src="shareFrame.landscape_frame_url"
                  alt="Landscape Frame"
                  class="w-full h-full object-contain"
                >
              </div>
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="font-semibold text-gray-900">
                    Landscape
                  </h3>
                  <p class="text-sm text-gray-600">
                    1200×675 (16:9)
                  </p>
                </div>
                <a
                  :href="`/competitions/${competition.id}/submissions/${submission.id}/share-frame/download/landscape`"
                  class="px-4 py-2 bg-burgundy-600 text-white rounded-lg font-semibold hover:bg-burgundy-700 transition text-sm"
                >
                  Download
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Sharing Tips -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
          <h3 class="font-semibold text-blue-900 mb-3">
            💡 Tips for Maximum Votes
          </h3>
          <ul class="space-y-2 text-sm text-blue-800">
            <li class="flex items-start gap-2">
              <span>✓</span>
              <span>Post on Instagram Stories for 24-hour visibility and tag friends</span>
            </li>
            <li class="flex items-start gap-2">
              <span>✓</span>
              <span>Share in WhatsApp groups and ask family to vote</span>
            </li>
            <li class="flex items-start gap-2">
              <span>✓</span>
              <span>Post as Instagram feed post for permanent visibility</span>
            </li>
            <li class="flex items-start gap-2">
              <span>✓</span>
              <span>Share on Facebook with the landscape format</span>
            </li>
            <li class="flex items-start gap-2">
              <span>✓</span>
              <span>The QR code makes it super easy for people to vote instantly</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';

const props = defineProps({
  competition: Object,
  submission: Object,
  shareFrame: Object,
  voteUrl: String,
});

const generating = ref(false);
const regenerating = ref(false);
const copied = ref(false);

const voteShareText = computed(() => {
  const title = props.submission?.title ? `"${props.submission.title}"` : 'my entry';
  const comp = props.competition?.title || 'the competition';
  return `Vote for ${title} in ${comp} on Photographer SB: ${props.voteUrl}`;
});

const shareLinks = computed(() => {
  const encodedUrl = encodeURIComponent(props.voteUrl);
  const encodedText = encodeURIComponent(voteShareText.value);
  return {
    whatsapp: `https://wa.me/?text=${encodedText}`,
    facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}&quote=${encodedText}`,
    messenger: `fb-messenger://share?link=${encodedUrl}&app_id=0&redirect_uri=${encodedUrl}`,
    telegram: `https://t.me/share/url?url=${encodedUrl}&text=${encodedText}`,
  };
});

const generateFrames = () => {
  generating.value = true;
  router.post(
    `/competitions/${props.competition.id}/submissions/${props.submission.id}/share-frame/generate`,
    {},
    {
      onFinish: () => {
        generating.value = false;
      },
    }
  );
};

const regenerateFrames = () => {
  regenerating.value = true;
  router.post(
    `/competitions/${props.competition.id}/submissions/${props.submission.id}/share-frame/regenerate`,
    {},
    {
      onFinish: () => {
        regenerating.value = false;
      },
    }
  );
};

const copyVoteLink = async () => {
  try {
    await navigator.clipboard.writeText(voteShareText.value);
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 2000);
  } catch (err) {
    console.error('Failed to copy:', err);
  }
};

const shareVote = (channel) => {
  const link = shareLinks.value[channel];
  if (!link) return;
  window.open(link, '_blank', 'noopener');
};
</script>
