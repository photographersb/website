<template>
  <div class="min-h-screen bg-gray-50">
    <Toast
      v-if="toastVisible"
      :message="toastMessage"
      :type="toastType"
      @close="closeToast"
    />
    <!-- Loading State -->
    <div
      v-if="loading"
      class="container mx-auto px-4 py-16 text-center"
    >
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-red-600" />
      <p class="text-gray-600 mt-4">
        Loading submission...
      </p>
    </div>

    <!-- Submission Content -->
    <div
      v-else-if="submission"
      class="pb-16"
    >
      <!-- Header -->
      <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-6">
          <router-link
            :to="`/competitions/${competition?.slug}/gallery`"
            class="text-red-600 hover:text-red-700 inline-flex items-center gap-2 mb-4"
          >
            <svg
              class="w-5 h-5"
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
            {{ backLabel }}
          </router-link>
          <div
            v-if="isVoteMode"
            class="hidden sm:flex items-center gap-2 text-xs text-gray-600"
          >
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-100 text-gray-700">
              Shortcuts: Left/Right, V, S, Enter
            </span>
          </div>
        </div>
      </div>

      <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Image Section -->
          <div class="lg:col-span-2">
            <div
              v-if="isVoteMode && showVoteTip"
              class="sm:hidden mb-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-amber-900"
            >
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="text-sm font-semibold">Quick vote tip</p>
                  <p class="text-xs text-amber-800">
                    Swipe to skip, tap Vote to support your favorite.
                  </p>
                </div>
                <button
                  class="text-xs font-semibold text-amber-800"
                  @click="dismissVoteTip"
                >
                  Got it
                </button>
              </div>
            </div>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
              <!-- Winner Badge -->
              <div
                v-if="submission.is_winner"
                class="bg-gradient-to-r from-yellow-400 to-yellow-600 text-white px-6 py-3 flex items-center justify-center gap-2"
              >
                <svg
                  class="w-6 h-6"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span class="font-bold text-lg">{{ submission.winner_position }} Place Winner</span>
              </div>

              <!-- Image -->
              <div
                class="relative bg-black"
                @touchstart="onTouchStart"
                @touchend="onTouchEnd"
              >
                <div
                  v-if="isVoteMode"
                  class="absolute left-3 top-3 sm:hidden flex flex-col gap-1"
                >
                  <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white bg-opacity-90 text-gray-900 text-xs font-semibold">
                    Vote Mode
                  </span>
                  <span class="inline-flex items-center px-3 py-1 rounded-full bg-black bg-opacity-70 text-white text-[11px]">
                    Swipe to skip
                  </span>
                </div>
                <transition
                  name="vote-fade"
                  mode="out-in"
                >
                  <img 
                    :key="submission.id"
                    :src="submission.image_url" 
                    :alt="submission.title"
                    class="w-full h-auto max-h-[80vh] object-contain mx-auto"
                  >
                </transition>
                <div
                  v-if="canSlide"
                  class="absolute inset-0 flex items-center justify-between px-4 pointer-events-none"
                >
                  <button
                    class="pointer-events-auto w-10 h-10 rounded-full bg-white bg-opacity-90 text-gray-900 shadow-md hover:bg-white transition"
                    title="Previous submission"
                    @click="goPrev"
                  >
                    <svg class="w-5 h-5 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                  </button>
                  <button
                    class="pointer-events-auto w-10 h-10 rounded-full bg-white bg-opacity-90 text-gray-900 shadow-md hover:bg-white transition"
                    title="Next submission"
                    @click="goNext"
                  >
                    <svg class="w-5 h-5 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </button>
                </div>
                <div
                  v-if="canSlide"
                  :class="[
                    'absolute bottom-3 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full bg-black bg-opacity-60 text-white text-xs tracking-wide',
                    isVoteMode ? 'hidden sm:flex' : ''
                  ]"
                >
                  {{ slidePositionLabel }}
                </div>
                <div
                  v-if="isVoteMode && canSlide"
                  class="absolute bottom-3 left-3 right-3 sm:hidden"
                >
                  <div class="flex items-center justify-between text-[11px] text-white text-opacity-90 mb-1">
                    <span>Progress</span>
                    <span>{{ voteProgressLabel }}</span>
                  </div>
                  <div class="h-1.5 rounded-full bg-white bg-opacity-30 overflow-hidden">
                    <div
                      class="h-full bg-gradient-to-r from-rose-400 to-red-500"
                      :style="{ width: `${voteProgressPercent}%` }"
                    />
                  </div>
                </div>
              </div>

              <div
                v-if="competition?.sponsors && competition.sponsors.length > 0"
                class="px-6 py-3 border-t bg-white"
              >
                <div class="flex flex-wrap items-center gap-2">
                  <span class="text-xs font-semibold text-gray-500">Sponsored by</span>
                  <div class="flex flex-wrap items-center gap-3">
                    <a
                      v-for="sponsor in competition.sponsors.filter(s => s.is_active)"
                      :key="sponsor.id"
                      :href="sponsor.website_url"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-100"
                      :title="sponsor.name"
                    >
                      <img
                        v-if="sponsor.logo_url"
                        :src="sponsor.logo_url"
                        :alt="sponsor.name"
                        class="h-5 w-auto object-contain"
                      >
                      <span v-else>{{ sponsor.name }}</span>
                    </a>
                  </div>
                </div>
              </div>

              <!-- Stats Bar -->
              <div class="px-6 py-4 border-t flex flex-wrap items-center justify-between gap-4 bg-gray-50">
                <div class="flex items-center gap-6">
                  <!-- Vote Button -->
                  <button 
                    :disabled="votingInProgress"
                    :class="[
                      'flex items-center gap-2 px-4 py-2 rounded-full font-semibold transition-all text-base',
                      hasVoted 
                        ? 'bg-red-600 text-white hover:bg-red-700' 
                        : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
                    ]"
                    @click="toggleVote"
                  >
                    <svg
                      class="w-5 h-5"
                      :fill="hasVoted ? 'currentColor' : 'none'"
                      :stroke="hasVoted ? 'none' : 'currentColor'"
                      viewBox="0 0 20 20"
                    >
                      <path
                        v-if="hasVoted"
                        fill-rule="evenodd"
                        d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                        clip-rule="evenodd"
                      />
                      <path
                        v-else
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                      />
                    </svg>
                    <span class="font-semibold">{{ submission.vote_count }}</span>
                  </button>
                  
                  <div class="flex items-center gap-2 text-gray-700">
                    <svg
                      class="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                      />
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                      />
                    </svg>
                    <span class="font-semibold">{{ submission.view_count }}</span>
                  </div>
                </div>

                <!-- Share Buttons -->
                <div
                  :class="[
                    'flex-wrap items-center gap-2',
                    isVoteMode ? 'hidden sm:flex' : 'flex'
                  ]"
                >
                  <button
                    class="inline-flex items-center gap-2 px-3 py-2 sm:px-3 sm:py-2 rounded-full bg-blue-50 text-blue-700 text-sm font-semibold hover:bg-blue-100"
                    title="Share on Facebook"
                    @click="shareOnFacebook"
                  >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                    </svg>
                    <span class="hidden sm:inline">Facebook</span>
                  </button>
                  <button
                    class="inline-flex items-center gap-2 px-3 py-2 sm:px-3 sm:py-2 rounded-full bg-sky-50 text-sky-700 text-sm font-semibold hover:bg-sky-100"
                    title="Share on Twitter"
                    @click="shareOnTwitter"
                  >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                    </svg>
                    <span class="hidden sm:inline">Twitter</span>
                  </button>
                  <button
                    class="inline-flex items-center gap-2 px-3 py-2 sm:px-3 sm:py-2 rounded-full bg-emerald-50 text-emerald-700 text-sm font-semibold hover:bg-emerald-100"
                    title="Share on WhatsApp"
                    @click="shareOnWhatsApp"
                  >
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M20.52 3.48A11.91 11.91 0 0012.03 0C5.38 0 .02 5.36 0 12a12.07 12.07 0 001.6 6.07L0 24l6.14-1.6a12.1 12.1 0 005.89 1.5h.01c6.64 0 12.02-5.36 12.02-12a11.9 11.9 0 00-3.54-8.42z" />
                    </svg>
                    <span class="hidden sm:inline">WhatsApp</span>
                  </button>
                  <button
                    class="inline-flex items-center gap-2 px-3 py-2 sm:px-3 sm:py-2 rounded-full bg-indigo-50 text-indigo-700 text-sm font-semibold hover:bg-indigo-100"
                    title="Share on Telegram"
                    @click="shareOnTelegram"
                  >
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M22 2L2 11l5.5 2.1L9 21l3.4-4.3L18 20l4-18z" />
                    </svg>
                    <span class="hidden sm:inline">Telegram</span>
                  </button>
                  <button
                    class="inline-flex items-center gap-2 px-3 py-2 sm:px-3 sm:py-2 rounded-full bg-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-300"
                    title="Copy Link"
                    @click="copyLink"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                    </svg>
                    <span class="hidden sm:inline">Copy Link</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Info Sidebar -->
          <div class="space-y-6">
            <!-- Title & Description -->
            <div class="bg-white rounded-lg shadow-lg p-6">
              <h1 class="text-2xl font-bold text-gray-900 mb-4">
                {{ submission.title }}
              </h1>
              
              <p
                v-if="submission.description"
                class="text-gray-700 mb-6 whitespace-pre-line"
              >
                {{ submission.description }}
              </p>

              <!-- Photographer Info -->
              <div class="border-t pt-4">
                <div class="flex items-center gap-4 mb-4">
                  <img 
                    :src="submission.photographer?.profile_image
                      || submission.photographer?.profile_picture_url
                      || submission.photographer?.profile_picture
                      || submission.photographer?.avatar_url
                      || '/images/default-avatar.png'" 
                    :alt="submission.photographer?.name"
                    class="w-12 h-12 rounded-full object-cover"
                  >
                  <div>
                    <p class="font-semibold text-gray-900">
                      {{ submission.photographer?.name }}
                    </p>
                    <p class="text-sm text-gray-600">
                      Photographer
                    </p>
                  </div>
                </div>
                
                <router-link 
                  v-if="submission.photographer?.photographer"
                  :to="submission.photographer?.username ? `/@${submission.photographer.username}` : `/photographer/${submission.photographer.photographer.slug}`"
                  class="block w-full text-center bg-gray-100 text-gray-700 py-2 rounded-lg font-medium hover:bg-gray-200 transition-colors"
                >
                  View Profile
                </router-link>
              </div>
            </div>

            <!-- Photo Details -->
            <div class="bg-white rounded-lg shadow-lg p-6">
              <h2 class="font-bold text-gray-900 mb-4">
                Photo Details
              </h2>
              
              <div class="space-y-3 text-sm">
                <div
                  v-if="submission.location"
                  class="flex items-start gap-3"
                >
                  <svg
                    class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                    />
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                    />
                  </svg>
                  <div>
                    <p class="text-gray-600">
                      Location
                    </p>
                    <p class="text-gray-900 font-medium">
                      {{ submission.location }}
                    </p>
                  </div>
                </div>

                <div
                  v-if="submission.date_taken"
                  class="flex items-start gap-3"
                >
                  <svg
                    class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                    />
                  </svg>
                  <div>
                    <p class="text-gray-600">
                      Date Taken
                    </p>
                    <p class="text-gray-900 font-medium">
                      {{ formatDate(submission.date_taken) }}
                    </p>
                  </div>
                </div>

                <div
                  v-if="submission.camera_make || submission.camera_model"
                  class="flex items-start gap-3"
                >
                  <svg
                    class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"
                    />
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"
                    />
                  </svg>
                  <div>
                    <p class="text-gray-600">
                      Camera
                    </p>
                    <p class="text-gray-900 font-medium">
                      {{ submission.camera_make }} {{ submission.camera_model }}
                    </p>
                  </div>
                </div>

                <div
                  v-if="cameraSettingParts.length"
                  class="flex items-start gap-3"
                >
                  <svg
                    class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                    />
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                    />
                  </svg>
                  <div>
                    <p class="text-gray-600">
                      Settings
                    </p>
                    <div class="flex flex-wrap gap-2 mt-2">
                      <span
                        v-for="(setting, index) in cameraSettingParts"
                        :key="`${setting.type}-${index}`"
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-sm font-medium"
                      >
                        <svg
                          v-if="setting.type === 'iso'"
                          class="w-6 h-4"
                          viewBox="0 0 48 32"
                          fill="none"
                          stroke="currentColor"
                        >
                          <rect x="2" y="2" width="44" height="28" rx="10" stroke-width="2" />
                          <text
                            x="24"
                            y="21"
                            text-anchor="middle"
                            font-size="12"
                            font-weight="700"
                            font-family="ui-sans-serif, system-ui, sans-serif"
                            fill="currentColor"
                          >
                            ISO
                          </text>
                        </svg>
                        <svg
                          v-else-if="setting.type === 'aperture'"
                          class="w-4 h-4"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                        >
                          <circle cx="12" cy="12" r="8" stroke-width="2" />
                          <polygon points="12,5 14.6,9.6 9.4,9.6" fill="currentColor" opacity="0.35" />
                          <polygon points="19,12 14.4,14.6 14.4,9.4" fill="currentColor" opacity="0.35" />
                          <polygon points="12,19 9.4,14.4 14.6,14.4" fill="currentColor" opacity="0.35" />
                          <polygon points="5,12 9.6,9.4 9.6,14.6" fill="currentColor" opacity="0.35" />
                          <polygon points="16.9,7.1 13.8,9.2 15.9,12.3" fill="currentColor" opacity="0.35" />
                          <polygon points="7.1,16.9 10.2,14.8 8.1,11.7" fill="currentColor" opacity="0.35" />
                          <circle cx="12" cy="12" r="2.4" stroke-width="2" />
                        </svg>
                        <svg
                          v-else-if="setting.type === 'shutter'"
                          class="w-4 h-4"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                        >
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
                        </svg>
                        <svg
                          v-else
                          class="w-4 h-4"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                        >
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                        </svg>
                        {{ setting.value }}
                      </span>
                    </div>
                  </div>
                </div>

                <div
                  v-if="submission.hashtags"
                  class="flex items-start gap-3"
                >
                  <svg
                    class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"
                    />
                  </svg>
                  <div>
                    <p class="text-gray-600">
                      Tags
                    </p>
                    <p class="text-gray-900 font-medium">
                      {{ submission.hashtags }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Judge Scores -->
            <div
              v-if="showJudgeScores"
              class="bg-white rounded-lg shadow-lg p-6"
            >
              <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-900">
                  Judge Scores
                </h2>
                <span class="text-xs text-gray-500">
                  {{ judgeSummary.count }} judge{{ judgeSummary.count === 1 ? '' : 's' }}
                </span>
              </div>
              <div class="text-sm text-gray-600 mb-4">
                Average total: <span class="font-semibold text-gray-900">{{ formatScore(judgeSummary.average) }}/50</span>
              </div>
              <div class="space-y-3">
                <div
                  v-for="score in judgeScores"
                  :key="score.id"
                  class="border border-gray-200 rounded-lg p-4"
                >
                  <div class="flex items-center gap-3">
                    <img
                      v-if="score.judge?.profile_photo_url"
                      :src="score.judge.profile_photo_url"
                      :alt="score.judge?.name || 'Judge'"
                      class="w-10 h-10 rounded-full object-cover"
                      loading="lazy"
                      decoding="async"
                    >
                    <div
                      v-else
                      class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-700 font-semibold"
                    >
                      {{ (score.judge?.name || 'J').charAt(0) }}
                    </div>
                    <div class="min-w-0">
                      <p class="font-semibold text-gray-900 truncate">
                        {{ score.judge?.name || 'Judge' }}
                      </p>
                      <p class="text-xs text-gray-500">
                        Total: <span class="font-semibold text-gray-900">{{ formatScore(score.total_score) }}/50</span>
                      </p>
                    </div>
                  </div>
                  <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 mt-3">
                    <p>Composition: <span class="font-semibold text-gray-900">{{ formatScore(score.composition_score) }}/10</span></p>
                    <p>Technical: <span class="font-semibold text-gray-900">{{ formatScore(score.technical_score) }}/10</span></p>
                    <p>Creativity: <span class="font-semibold text-gray-900">{{ formatScore(score.creativity_score) }}/10</span></p>
                    <p>Story: <span class="font-semibold text-gray-900">{{ formatScore(score.story_score) }}/10</span></p>
                    <p>Impact: <span class="font-semibold text-gray-900">{{ formatScore(score.impact_score) }}/10</span></p>
                  </div>
                  <p
                    v-if="score.feedback"
                    class="text-xs text-gray-600 mt-3"
                  >
                    Feedback: <span class="text-gray-900">{{ score.feedback }}</span>
                  </p>
                </div>
              </div>
            </div>

            <!-- Competition Info -->
            <div class="bg-white rounded-lg shadow-lg p-6">
              <h2 class="font-bold text-gray-900 mb-4">
                Competition
              </h2>
              <router-link
                :to="`/competitions/${competition?.slug}`"
                class="block hover:bg-gray-50 transition-colors rounded-lg"
              >
                <h3 class="font-semibold text-red-600 mb-1">
                  {{ competition?.title }}
                </h3>
                <p
                  v-if="competition?.theme"
                  class="text-sm text-gray-600"
                >
                  {{ competition.theme }}
                </p>
              </router-link>
            </div>

          </div>
        </div>
      </div>

      <div
        v-if="submission"
        class="sm:hidden fixed bottom-0 inset-x-0 z-30"
      >
        <div class="bg-white bg-opacity-95 backdrop-blur border-t border-gray-200 px-4 pt-3 pb-4">
          <div
            v-if="isVoteMode"
            class="flex items-center gap-3"
          >
            <button
              class="flex-1 py-3 rounded-xl font-semibold border border-gray-200 text-gray-900 bg-white"
              @click="handleVoteModeSkip"
            >
              Skip
            </button>
            <button
              :disabled="votingInProgress || !canVote"
              class="flex-1 py-3 rounded-xl font-semibold text-white"
              :class="hasVoted ? 'bg-gray-800' : 'bg-red-600'"
              @click="handleVoteModePrimary"
            >
              {{ hasVoted ? 'Next' : 'Vote' }}
            </button>
          </div>
          <div
            v-else
            class="flex items-center gap-3"
          >
            <button
              class="flex-1 py-3 rounded-xl font-semibold border border-gray-200 text-gray-900 bg-white"
              @click="viewGallery"
            >
              Gallery
            </button>
            <button
              :disabled="votingInProgress || !canVote"
              class="flex-1 py-3 rounded-xl font-semibold text-white"
              :class="hasVoted ? 'bg-red-700' : 'bg-red-600'"
              @click="toggleVote"
            >
              {{ hasVoted ? 'Voted' : 'Vote' }}
            </button>
          </div>
        </div>
      </div>
      <div class="h-24 sm:hidden" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api';
import Toast from '../components/ui/Toast.vue';
import { useApiError } from '../composables/useApiError';
import { formatDate as formatDateValue } from '../utils/formatters';

const route = useRoute();
const router = useRouter();
const { toastMessage, toastType, toastVisible, showToast, handleApiError, closeToast } = useApiError();

const submission = ref(null);
const competition = ref(null);
const loading = ref(true);
const hasVoted = ref(false);
const votingInProgress = ref(false);
const competitionSubmissions = ref([]);
const submissionCache = ref({});
const slideshowCompetitionId = ref(null);
const currentIndex = ref(-1);
const isFetchingSlides = ref(false);
const touchStartX = ref(0);
const touchEndX = ref(0);
const prefetchingIds = new Set();
const showVoteTip = ref(false);

const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const isAuthenticated = !!user.value;

const cameraSettingParts = computed(() => {
  const raw = submission.value?.camera_settings;
  if (!raw) return [];

  const parseString = (value) => {
    return value
      .split(',')
      .map(part => part.trim())
      .filter(Boolean)
      .map((part) => {
        const lower = part.toLowerCase();
        let type = 'setting';
        if (lower.includes('iso')) {
          type = 'iso';
        } else if (lower.startsWith('f/') || lower.includes('aperture')) {
          type = 'aperture';
        } else if (lower.includes('s') || lower.includes('sec') || lower.includes('/')) {
          type = 'shutter';
        }
        return { type, value: part };
      });
  };

  let settings = raw;
  if (typeof raw === 'string') {
    try {
      settings = JSON.parse(raw);
    } catch (error) {
      return parseString(raw);
    }
  }

  if (settings && typeof settings === 'object') {
    if (typeof settings.raw === 'string' && settings.raw.trim().length > 0) {
      return parseString(settings.raw);
    }
    const iso = settings.iso ?? settings.ISO;
    const shutter = settings.shutter_speed ?? settings.shutter;
    const aperture = settings.aperture ?? settings.f_number;
    const parts = [];
    if (aperture) parts.push({ type: 'aperture', value: `f/${aperture}`.replace('f/f/', 'f/') });
    if (shutter) parts.push({ type: 'shutter', value: String(shutter) });
    if (iso) parts.push({ type: 'iso', value: `ISO ${iso}` });
    return parts.length ? parts : [{ type: 'setting', value: JSON.stringify(settings) }];
  }

  return parseString(String(settings));
});

const judgeScores = computed(() => submission.value?.scores || []);
const showJudgeScores = computed(() => {
  return competition.value?.show_judge_reactions && judgeScores.value.length > 0;
});
const judgeSummary = computed(() => {
  const scores = judgeScores.value;
  if (!scores.length) {
    return { count: 0, average: 0 };
  }
  const total = scores.reduce((sum, score) => sum + Number(score.total_score || 0), 0);
  return {
    count: scores.length,
    average: total / scores.length,
  };
});

const isVoteMode = computed(() => route.query.mode === 'vote');
const backLabel = computed(() => (isVoteMode.value ? 'Exit Vote Mode' : 'Back to Gallery'));
const voteProgressPercent = computed(() => {
  if (!canSlide.value || currentIndex.value < 0) return 0;
  return Math.round(((currentIndex.value + 1) / competitionSubmissions.value.length) * 100);
});
const voteProgressLabel = computed(() => {
  if (!canSlide.value || currentIndex.value < 0) return '';
  return `${currentIndex.value + 1} / ${competitionSubmissions.value.length}`;
});
const canVote = computed(() => {
  if (!competition.value) return false;
  if (!competition.value.allow_public_voting) return false;
  if (competition.value.status !== 'active') return false;
  if (!competition.value.voting_start_at || !competition.value.voting_end_at) return true;
  const now = new Date();
  return now >= new Date(competition.value.voting_start_at)
    && now <= new Date(competition.value.voting_end_at);
});
const voteDisabledReason = computed(() => {
  if (!competition.value) return 'Voting unavailable';
  if (!competition.value.allow_public_voting) return 'Public voting is disabled';
  if (competition.value.status !== 'active') return 'Voting is closed';
  return 'Voting is closed';
});

const canSlide = computed(() => {
  return competitionSubmissions.value.length > 1 && currentIndex.value !== -1;
});

const slidePositionLabel = computed(() => {
  if (currentIndex.value < 0) return '';
  return `${currentIndex.value + 1} of ${competitionSubmissions.value.length}`;
});

onMounted(async () => {
  const tipSeen = localStorage.getItem('psbVoteModeTipSeen') === '1';
  showVoteTip.value = isVoteMode.value && !tipSeen;
  window.addEventListener('keydown', handleKeydown);
  await fetchSubmission();
  if (isAuthenticated) {
    await checkVoteStatus();
  }
});

onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleKeydown);
});

watch(
  () => route.params.submissionId,
  async () => {
    loading.value = true;
    await fetchSubmission();
    if (isAuthenticated) {
      await checkVoteStatus();
    }
    setCurrentIndex();
    prefetchNeighbors();
  }
);

watch(
  () => isVoteMode.value,
  (value) => {
    if (!value) {
      showVoteTip.value = false;
      return;
    }
    const tipSeen = localStorage.getItem('psbVoteModeTipSeen') === '1';
    showVoteTip.value = !tipSeen;
  }
);

const fetchSubmission = async () => {
  try {
    const submissionId = Number(route.params.submissionId);
    // First get competition
    const compData = await api.get(`/competitions/${route.params.slug}`);
    competition.value = compData.data.data;

    // Then get submission
    const cached = submissionCache.value[submissionId];
    if (cached) {
      submission.value = cached;
    }
    const { data } = await api.get(
      `/competitions/${competition.value.id}/submissions/${route.params.submissionId}`
    );
    submission.value = data.data;
    cacheSubmission(data.data);

    storeVoteResume();

    if (slideshowCompetitionId.value !== competition.value.id) {
      await fetchCompetitionSubmissions();
    }
    setCurrentIndex();
    prefetchNeighbors();
  } catch (error) {
    handleApiError(error, 'Submission not found');
    router.push(`/competitions/${route.params.slug}/gallery`);
  } finally {
    loading.value = false;
  }
};

const fetchCompetitionSubmissions = async () => {
  if (!competition.value || isFetchingSlides.value) return;
  isFetchingSlides.value = true;
  try {
    const perPage = 200;
    let page = 1;
    let lastPage = 1;
    const all = [];
    do {
      const { data } = await api.get(`/competitions/${competition.value.id}/submissions`, {
        params: {
          page,
          per_page: perPage,
          sort_by: 'created_at',
          sort_order: 'asc'
        }
      });
      const payload = data.data;
      const pageItems = Array.isArray(payload.data) ? payload.data : payload;
      all.push(...pageItems);
      if (payload.current_page) {
        lastPage = payload.last_page || 1;
        page += 1;
      } else {
        lastPage = 0;
      }
    } while (page <= lastPage);

    competitionSubmissions.value = all;
    slideshowCompetitionId.value = competition.value.id;
  } catch (error) {
    console.error('Error fetching slideshow submissions:', error);
  } finally {
    isFetchingSlides.value = false;
  }
};

const setCurrentIndex = () => {
  const currentId = Number(route.params.submissionId);
  currentIndex.value = competitionSubmissions.value.findIndex(
    (item) => Number(item.id) === currentId
  );
  prefetchNeighbors();
};

const goNext = () => {
  if (!canSlide.value) return;
  const nextIndex = (currentIndex.value + 1) % competitionSubmissions.value.length;
  const nextSubmission = competitionSubmissions.value[nextIndex];
  router.push(`/competitions/${competition.value.slug}/submissions/${nextSubmission.id}`);
};

const goPrev = () => {
  if (!canSlide.value) return;
  const prevIndex =
    (currentIndex.value - 1 + competitionSubmissions.value.length) %
    competitionSubmissions.value.length;
  const prevSubmission = competitionSubmissions.value[prevIndex];
  router.push(`/competitions/${competition.value.slug}/submissions/${prevSubmission.id}`);
};

const onTouchStart = (event) => {
  touchStartX.value = event.changedTouches[0].clientX;
};

const onTouchEnd = (event) => {
  touchEndX.value = event.changedTouches[0].clientX;
  const delta = touchStartX.value - touchEndX.value;
  if (Math.abs(delta) < 50) return;
  if (delta > 0) {
    goNext();
  } else {
    goPrev();
  }
};

const handleKeydown = (event) => {
  const target = event.target;
  if (target && target.isContentEditable) return;
  const tagName = target?.tagName;
  if (tagName === 'INPUT' || tagName === 'TEXTAREA' || tagName === 'SELECT') return;

  if (event.key === 'ArrowRight') {
    event.preventDefault();
    goNext();
    return;
  }
  if (event.key === 'ArrowLeft') {
    event.preventDefault();
    goPrev();
    return;
  }

  if (!isVoteMode.value) return;

  if (event.key === 'v' || event.key === 'V' || event.key === 'Enter' || event.key === ' ') {
    event.preventDefault();
    handleVoteModePrimary();
  } else if (event.key === 's' || event.key === 'S') {
    event.preventDefault();
    handleVoteModeSkip();
  }
};

const triggerHaptic = (duration = 15) => {
  if (navigator && typeof navigator.vibrate === 'function') {
    navigator.vibrate(duration);
  }
};

const cacheSubmission = (payload) => {
  if (!payload || !payload.id) return;
  submissionCache.value[payload.id] = payload;
};

const prefetchImage = (url) => {
  if (!url) return;
  const img = new Image();
  img.src = url;
};

const prefetchSubmission = async (submissionId) => {
  if (!competition.value) return;
  if (!submissionId) return;
  if (submissionCache.value[submissionId]) return;
  if (prefetchingIds.has(submissionId)) return;
  prefetchingIds.add(submissionId);
  try {
    const { data } = await api.get(
      `/competitions/${competition.value.id}/submissions/${submissionId}`
    );
    cacheSubmission(data.data);
  } catch (error) {
    console.warn('Prefetch failed for submission', submissionId, error);
  } finally {
    prefetchingIds.delete(submissionId);
  }
};

const prefetchNeighbors = () => {
  if (!competitionSubmissions.value.length || currentIndex.value < 0) return;
  const total = competitionSubmissions.value.length;
  const offsets = [1, 2, -1, -2];
  offsets.forEach((offset) => {
    const index = (currentIndex.value + offset + total) % total;
    const item = competitionSubmissions.value[index];
    if (!item) return;
    prefetchImage(item.image_url);
    prefetchSubmission(Number(item.id));
  });
};

const checkVoteStatus = async () => {
  try {
    const { data } = await api.get(
      `/competitions/${competition.value.id}/submissions/${route.params.submissionId}/vote-status`
    );
    hasVoted.value = data.data.has_voted;
  } catch (error) {
    handleApiError(error, 'Error checking vote status');
  }
};

const toggleVote = async (options = {}) => {
  if (!canVote.value) {
    showToast(voteDisabledReason.value, 'warning');
    return false;
  }
  if (!isAuthenticated) {
    showToast('Please login to vote', 'warning');
    router.push('/login');
    return false;
  }
  
  votingInProgress.value = true;
  
  try {
    if (hasVoted.value) {
      // Unvote
      await api.delete(
        `/competitions/${competition.value.id}/submissions/${submission.value.id}/vote`
      );
      hasVoted.value = false;
      submission.value.vote_count--;
    } else {
      // Vote
      const { data } = await api.post(
        `/competitions/${competition.value.id}/submissions/${submission.value.id}/vote`
      );
      hasVoted.value = true;
      submission.value.vote_count = data.data.vote_count;
      showToast('Thanks for voting! Your support means a lot.', 'success');
      triggerHaptic(20);
      if (options.advanceOnVote) {
        setTimeout(() => {
          goNext();
        }, 350);
      }
      return true;
    }
  } catch (error) {
    handleApiError(error, 'Failed to process vote');
  } finally {
    votingInProgress.value = false;
  }
  return false;
};

const handleVoteModePrimary = async () => {
  if (hasVoted.value) {
    triggerHaptic(10);
    goNext();
    return;
  }
  await toggleVote({ advanceOnVote: true });
};

const handleVoteModeSkip = () => {
  triggerHaptic(10);
  goNext();
};

const storeVoteResume = () => {
  if (!isVoteMode.value) return;
  const slug = competition.value?.slug;
  const id = submission.value?.id;
  if (!slug || !id) return;
  localStorage.setItem(`psbVoteResume:${slug}`, String(id));
};

const dismissVoteTip = () => {
  showVoteTip.value = false;
  localStorage.setItem('psbVoteModeTipSeen', '1');
};

const formatDate = (date) => {
  return formatDateValue(date);
};

const formatScore = (value) => {
  if (value === null || value === undefined || value === '') {
    return '0.0';
  }
  const parsed = Number(value);
  if (Number.isNaN(parsed)) {
    return '0.0';
  }
  return parsed.toFixed(1);
};

const getVoteShareUrl = () => {
  const shortUrl = submission.value?.short_url;
  if (shortUrl) {
    return `${window.location.origin}/vote/${shortUrl}`;
  }
  return window.location.href;
};

const shareOnFacebook = () => {
  const url = getVoteShareUrl();
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
  trackShareLog('facebook');
};

const shareOnTwitter = () => {
  const url = getVoteShareUrl();
  const text = `Support "${submission.value.title}" in ${competition.value?.title} on Photographer SB.`;
  window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`, '_blank');
};

const shareOnWhatsApp = () => {
  const url = getVoteShareUrl();
  const text = `Quick favor? 💛 Vote for "${submission.value.title}" in ${competition.value?.title}. Your vote means a lot! ${url}`;
  window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
  trackShareLog('whatsapp');
};

const shareOnTelegram = () => {
  const url = getVoteShareUrl();
  const text = `If you like this photo, please vote for "${submission.value.title}" in ${competition.value?.title}.`;
  window.open(`https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`, '_blank');
  trackShareLog('telegram');
};

const copyLink = () => {
  navigator.clipboard.writeText(getVoteShareUrl());
  showToast('Link copied to clipboard!', 'success');
  trackShareLog('copy');
};

const trackShareLog = async (platform) => {
  try {
    await api.post('/growth/share-log', {
      entity_type: 'competition_submission',
      entity_id: submission.value?.id || null,
      platform,
    });
  } catch (error) {
    console.warn('Share log failed:', error);
  }
};

const viewGallery = () => {
  if (!competition.value?.slug) return;
  router.push(`/competitions/${competition.value.slug}/gallery`);
};
</script>

<style scoped>
.vote-fade-enter-active,
.vote-fade-leave-active {
  transition: opacity 240ms ease, transform 240ms ease;
}

.vote-fade-enter-from,
.vote-fade-leave-to {
  opacity: 0;
  transform: scale(0.985);
}
</style>
