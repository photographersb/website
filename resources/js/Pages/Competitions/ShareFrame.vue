<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Share Your Entry</h1>
            <p class="text-gray-600 mt-1">Generate beautiful social media frames to share and get votes</p>
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
      <div v-if="$page.props.flash?.success" class="mb-6">
        <div class="bg-green-50 border border-green-300 rounded-lg p-4">
          <div class="flex items-start gap-3">
            <div class="text-green-600 text-lg font-bold">✓</div>
            <div>
              <h3 class="text-green-900 font-semibold">Success</h3>
              <p class="text-green-700 text-sm mt-1">{{ $page.props.flash.success }}</p>
            </div>
          </div>
        </div>
      </div>

      <div v-if="$page.props.flash?.error" class="mb-6">
        <div class="bg-red-50 border border-red-300 rounded-lg p-4">
          <div class="flex items-start gap-3">
            <div class="text-red-600 text-lg font-bold">⚠️</div>
            <div>
              <h3 class="text-red-900 font-semibold">Error</h3>
              <p class="text-red-700 text-sm mt-1">{{ $page.props.flash.error }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div v-if="!shareFrame" class="max-w-2xl mx-auto">
        <!-- Generate Instructions -->
        <div class="bg-white rounded-lg shadow-card p-8 text-center">
          <div class="w-16 h-16 bg-burgundy-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-burgundy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
            </svg>
          </div>
          
          <h2 class="text-2xl font-bold text-gray-900 mb-3">Generate Share Frames</h2>
          <p class="text-gray-600 mb-6">
            Create professionally designed frames in multiple formats optimized for Instagram, Facebook, and WhatsApp. Each frame includes a QR code for easy voting.
          </p>

          <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="text-left">
              <div class="text-burgundy-600 font-semibold mb-1">📱 Instagram Story</div>
              <p class="text-sm text-gray-600">1080×1920 (9:16)</p>
            </div>
            <div class="text-left">
              <div class="text-burgundy-600 font-semibold mb-1">📷 Instagram Post</div>
              <p class="text-sm text-gray-600">1080×1080 (1:1)</p>
            </div>
            <div class="text-left">
              <div class="text-burgundy-600 font-semibold mb-1">🖼️ Portrait</div>
              <p class="text-sm text-gray-600">1080×1350 (4:5)</p>
            </div>
            <div class="text-left">
              <div class="text-burgundy-600 font-semibold mb-1">🌄 Landscape</div>
              <p class="text-sm text-gray-600">1200×675 (16:9)</p>
            </div>
          </div>

          <button
            @click="generateFrames"
            :disabled="generating"
            class="bg-burgundy-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-burgundy-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ generating ? 'Generating Frames...' : 'Generate Share Frames' }}
          </button>
        </div>
      </div>

      <!-- Share Frames Display -->
      <div v-else class="space-y-6">
        <!-- Vote Link -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">📱 Share Your Vote Link</h2>
          
          <div class="flex gap-3">
            <input
              :value="voteUrl"
              readonly
              class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
            />
            <button
              @click="copyVoteLink"
              class="px-6 py-2 bg-burgundy-600 text-white rounded-lg font-semibold hover:bg-burgundy-700 transition"
            >
              {{ copied ? '✓ Copied!' : 'Copy Link' }}
            </button>
          </div>
          
          <p class="text-sm text-gray-600 mt-2">
            Share this short link on social media, WhatsApp, or anywhere to get votes!
          </p>
        </div>

        <!-- Frame Downloads -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">🖼️ Download Share Frames</h2>
            <button
              @click="regenerateFrames"
              :disabled="regenerating"
              class="text-sm text-burgundy-600 hover:text-burgundy-700 font-medium"
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
                />
              </div>
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="font-semibold text-gray-900">Instagram Story</h3>
                  <p class="text-sm text-gray-600">1080×1920 (9:16)</p>
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
                />
              </div>
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="font-semibold text-gray-900">Instagram Post</h3>
                  <p class="text-sm text-gray-600">1080×1080 (1:1)</p>
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
                />
              </div>
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="font-semibold text-gray-900">Portrait</h3>
                  <p class="text-sm text-gray-600">1080×1350 (4:5)</p>
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
                />
              </div>
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="font-semibold text-gray-900">Landscape</h3>
                  <p class="text-sm text-gray-600">1200×675 (16:9)</p>
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
          <h3 class="font-semibold text-blue-900 mb-3">💡 Tips for Maximum Votes</h3>
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
import { ref } from 'vue';
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
    await navigator.clipboard.writeText(props.voteUrl);
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 2000);
  } catch (err) {
    console.error('Failed to copy:', err);
  }
};
</script>
