<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b">
      <div class="container mx-auto px-4 py-6">
        <router-link
          :to="`/competitions/${competition?.slug}`"
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
          Back to Competition
        </router-link>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
          {{ competition?.title }} - {{ pageLabel }}
        </h1>
        <p class="text-gray-600">
          {{ totalSubmissions }} {{ totalSubmissions === 1 ? 'Entry' : 'Entries' }}
          <span v-if="isLeaderboard">ranked by votes</span>
        </p>
        <div class="mt-3 flex flex-wrap gap-2">
          <button
            class="px-3 py-2 rounded-full bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 min-h-[40px]"
            aria-label="Share this gallery on Facebook"
            @click="shareOnFacebook"
          >
            Facebook
          </button>
          <button
            class="px-3 py-2 rounded-full bg-emerald-500 text-white text-xs font-semibold hover:bg-emerald-600 min-h-[40px]"
            aria-label="Share this gallery on WhatsApp"
            @click="shareOnWhatsApp"
          >
            WhatsApp
          </button>
          <button
            class="px-3 py-2 rounded-full bg-blue-700 text-white text-xs font-semibold hover:bg-blue-800 min-h-[40px]"
            aria-label="Share this gallery on LinkedIn"
            @click="shareOnLinkedIn"
          >
            LinkedIn
          </button>
          <button
            class="px-3 py-2 rounded-full border border-gray-300 text-gray-700 text-xs font-semibold hover:bg-gray-100 min-h-[40px]"
            aria-label="Copy gallery link"
            @click="copyLink"
          >
            Copy link
          </button>
        </div>
        <div
          v-if="showScoreSplit"
          class="mt-2 flex flex-wrap items-center gap-2 text-xs"
        >
          <span class="text-gray-500 font-semibold uppercase tracking-wide">Score Split</span>
          <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-800 px-2.5 py-1 font-semibold">
            Public {{ voteWeightPercent }}%
          </span>
          <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 text-amber-800 px-2.5 py-1 font-semibold">
            Judges {{ judgeWeightPercent }}%
          </span>
        </div>
        <div
          v-if="votingBannerText"
          :class="[
            'mt-4 rounded-xl border px-4 py-3 text-sm font-medium',
            votingBannerTone === 'live'
              ? 'border-amber-200 bg-amber-50 text-amber-900'
              : votingBannerTone === 'upcoming'
                ? 'border-slate-200 bg-slate-50 text-slate-700'
                : 'border-gray-200 bg-gray-50 text-gray-600'
          ]"
        >
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <span>{{ votingBannerText }}</span>
            <button
              v-if="votingBannerCtaLabel"
              class="inline-flex items-center justify-center rounded-full px-4 py-2 text-xs font-semibold"
              :class="votingBannerTone === 'live'
                ? 'bg-amber-600 text-white hover:bg-amber-700'
                : 'bg-gray-800 text-white hover:bg-gray-900'"
              @click="handleVotingBannerCta"
            >
              {{ votingBannerCtaLabel }}
            </button>
          </div>
        </div>
        <div
          v-if="showVoteTips && canVote && !isLeaderboard"
          class="mt-3 rounded-xl border border-[#eadfd7] bg-white/90 px-4 py-3 text-sm text-gray-700"
        >
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p>
              Tip: Use Vote Mode to swipe fast. Resume keeps your place.
            </p>
            <button
              class="text-xs font-semibold text-gray-600"
              @click="dismissVoteTips"
            >
              Hide tips
            </button>
          </div>
        </div>
        <div
          v-if="canVote && submissions.length"
          class="mt-4 rounded-2xl border border-red-100 bg-gradient-to-br from-white to-red-50 p-4"
        >
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <p class="text-sm font-semibold text-gray-900">Vote Mode</p>
              <p class="text-xs text-gray-600">Swipe to skip, tap Vote to support your favorite.</p>
            </div>
            <div class="flex flex-wrap gap-2">
              <button
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-600 text-white font-semibold shadow hover:bg-red-700 transition min-h-[44px]"
                @click="startVoteMode"
              >
                Start Vote Mode
                <span class="text-xs font-medium bg-white/20 px-2 py-0.5 rounded-full">Swipe</span>
              </button>
              <button
                v-if="resumeSubmissionId"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-red-200 text-red-700 font-semibold hover:bg-red-100 transition min-h-[44px]"
                @click="resumeVoteMode"
              >
                Resume Voting
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters & Sort -->
    <div class="bg-white border-b sticky top-0 z-10 shadow-sm">
      <div class="container mx-auto px-4 py-4">
        <div class="flex items-center gap-3 sm:hidden">
          <div class="relative flex-1">
            <input 
              v-model="searchQuery"
              type="text"
              :placeholder="searchPlaceholder" 
              class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
              @input="debouncedSearch"
            >
            <svg
              class="absolute left-3 top-3 w-5 h-5 text-gray-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
              />
            </svg>
          </div>
          <button
            class="px-4 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium min-h-[44px]"
            @click="showFilters = !showFilters"
          >
            Filters
          </button>
        </div>

        <div :class="[showFilters ? 'block' : 'hidden sm:block', 'mt-3 sm:mt-0']">
          <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1 hidden sm:block">
              <div class="relative">
                <input 
                  v-model="searchQuery"
                  type="text"
                  :placeholder="searchPlaceholder" 
                  class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                  @input="debouncedSearch"
                >
                <svg
                  class="absolute left-3 top-2.5 w-5 h-5 text-gray-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                  />
                </svg>
              </div>
            </div>

            <!-- Sort -->
            <div class="flex gap-2">
              <select 
                v-model="sortBy"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white"
                @change="fetchSubmissions"
              >
                <option value="created_at">
                  Recently Added
                </option>
                <option value="most_voted">
                  Most Voted
                </option>
                <option value="trending">
                  Trending
                </option>
                <option value="random">
                  Random
                </option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div
      v-if="loading"
      class="container mx-auto px-4 py-16 text-center"
      role="status"
      aria-live="polite"
    >
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-red-600" />
      <p class="text-gray-600 mt-4">
        Loading submissions...
      </p>
    </div>

    <!-- Content after loading -->
    <div v-else>
      <div
        v-if="!canVote"
        class="bg-amber-50 border-b border-amber-200"
      >
        <div class="container mx-auto px-4 py-3 text-sm text-amber-900">
          {{ voteDisabledReason }}
        </div>
      </div>
      <!-- Sponsors Section -->
      <div
        v-if="sponsorList.length > 0"
        class="bg-white border-b shadow-sm"
      >
        <div class="container mx-auto px-4 py-6">
          <div class="text-center mb-4">
            <h3 class="text-sm font-semibold text-gray-600 inline-flex items-center gap-2">
              <svg
                class="w-4 h-4"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              PROUDLY SPONSORED BY
            </h3>
          </div>
          <div class="flex flex-wrap items-center justify-center gap-6">
            <a 
              v-for="sponsor in sponsorList"
              :key="sponsor.id"
              :href="sponsor.website || undefined"
              target="_blank"
              rel="noopener noreferrer"
              :class="[
                'flex flex-col items-center justify-center gap-1 p-4 rounded-xl transition-all hover:scale-105 shadow-md hover:shadow-lg',
                sponsor.tier === 'platinum' ? 'bg-gradient-to-br from-slate-100 to-slate-200 border-2 border-slate-400' :
                sponsor.tier === 'gold' ? 'bg-gradient-to-br from-yellow-50 to-yellow-100 border-2 border-yellow-400' :
                sponsor.tier === 'silver' ? 'bg-gradient-to-br from-gray-100 to-gray-200 border-2 border-gray-400' :
                'bg-gradient-to-br from-orange-50 to-orange-100 border-2 border-orange-300'
              ]"
              :title="sponsor.name"
            >
              <div
                :class="[
                  sponsor.tier === 'platinum' ? 'w-40 sm:w-44' :
                  sponsor.tier === 'gold' ? 'w-36 sm:w-40' :
                  sponsor.tier === 'silver' ? 'w-32 sm:w-36' :
                  'w-28 sm:w-32'
                ]"
                :style="{ aspectRatio: sponsorLogoRatio }"
              >
                <img 
                  v-if="sponsor.logo_url"
                  :src="sponsor.logo_url" 
                  :alt="sponsor.name"
                  class="w-full h-full object-contain"
                >
                <span
                  v-else
                  class="w-full h-full flex items-center justify-center text-gray-700 font-semibold text-sm"
                >{{ sponsor.name }}</span>
              </div>
              <span
                v-if="sponsor.logo_credit_name"
                class="text-[11px] text-gray-500"
              >
                Photo:
                <span class="font-semibold text-gray-700">{{ sponsor.logo_credit_name }}</span>
              </span>
            </a>
          </div>
        </div>
      </div>

      <!-- Gallery Grid -->
      <div class="container mx-auto px-4 py-8">
        <!-- Empty State -->
        <div
          v-if="submissions.length === 0"
          class="text-center py-16"
        >
          <svg
            class="mx-auto h-24 w-24 text-gray-400 mb-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
            />
          </svg>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">
            No Submissions Yet
          </h3>
          <p class="text-gray-600 mb-6">
            Be the first to submit a photo to this competition!
          </p>
          <router-link 
            v-if="isAuthenticated"
            :to="`/competitions/${competition?.slug}/submit`" 
            class="inline-block bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors"
          >
            Submit Your Photo
          </router-link>
        </div>

        <!-- Submissions Grid -->
        <div
          v-else
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
        >
          <div 
            v-for="(submission, index) in submissions" 
            :key="submission.id"
            class="group cursor-pointer bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2"
            role="button"
            tabindex="0"
            :aria-label="`View submission ${submission.title || 'photo'}`"
            @click="viewSubmission(submission)"
            @keydown.enter="viewSubmission(submission)"
            @keydown.space.prevent="viewSubmission(submission)"
            @mouseenter="prefetchFullImage(submission)"
            @focus="prefetchFullImage(submission)"
          >
            <!-- Image -->
            <div class="relative aspect-square overflow-hidden bg-gray-200">
              <img 
                :src="submission.thumbnail_url || submission.image_url" 
                :alt="submission.title"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                loading="lazy"
                decoding="async"
                :fetchpriority="index < 4 ? 'high' : 'auto'"
              >
              <div
                v-if="isLeaderboard"
                class="absolute left-3 top-3"
              >
                <span class="inline-flex items-center gap-1 rounded-full bg-black/70 text-white px-2.5 py-1 text-xs font-semibold">
                  Rank #{{ submission.rank ?? (index + 1) }}
                </span>
              </div>
            
              <!-- Overlay on hover -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                  <div class="flex items-center gap-4 text-sm">
                    <div class="flex items-center gap-1">
                      <svg
                        class="w-5 h-5"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      {{ submission.vote_count }}
                    </div>
                    <div
                      v-if="showJudgeScores && submission.judge_score !== null"
                      class="flex items-center gap-1"
                    >
                      <span class="text-xs font-semibold">Judge</span>
                      {{ formatScore(submission.judge_score) }}
                    </div>
                    <div
                      v-if="!isLeaderboard"
                      class="flex items-center gap-1"
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
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                        />
                      </svg>
                      {{ submission.view_count }}
                    </div>
                    <div
                      v-else
                      class="flex items-center gap-1"
                    >
                      <span class="font-semibold">Rank</span>
                      #{{ submission.rank ?? (index + 1) }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Winner Badge -->
              <div
                v-if="submission.is_winner"
                class="absolute top-3 right-3"
              >
                <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg flex items-center gap-1">
                  <svg
                    class="w-4 h-4"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  {{ submission.winner_position }}
                </span>
              </div>
            </div>

            <!-- Info -->
            <div class="p-4">
              <h3 class="font-bold text-gray-900 mb-1 line-clamp-1 group-hover:text-red-600 transition-colors">
                {{ submission.title }}
              </h3>
              <span
                v-if="submission.has_voted"
                class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-700 px-2 py-1 text-xs font-semibold mb-2"
              >
                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                  <path
                    fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-7.071 7.071a1 1 0 01-1.414 0L3.293 8.85a1 1 0 111.414-1.414l3.515 3.515 6.364-6.364a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
                You voted
              </span>
              <p class="text-sm text-gray-600 mb-2 line-clamp-1">
                by {{ submission.photographer?.name || 'Anonymous' }}
              </p>
              <div
                v-if="isLeaderboard"
                class="flex flex-wrap items-center gap-2 mb-2 text-xs"
              >
                <span class="inline-flex items-center gap-1 rounded-full bg-red-50 text-red-700 px-2.5 py-1 font-semibold">
                  <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path
                      fill-rule="evenodd"
                      d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  {{ submission.vote_count }} votes
                </span>
                <span
                  v-if="showJudgeScores && submission.judge_score !== null"
                  class="inline-flex items-center gap-1 rounded-full bg-amber-50 text-amber-800 px-2.5 py-1 font-semibold"
                >
                  Judge {{ formatScore(submission.judge_score) }}/50
                </span>
              </div>
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-sm">
                <div class="flex items-center gap-3 text-gray-500">
                  <span
                    v-if="!isLeaderboard"
                    class="flex items-center gap-1"
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
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                      />
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                      />
                    </svg>
                    {{ submission.view_count }}
                  </span>
                  <span
                    v-else
                    class="flex items-center gap-1 font-semibold"
                  >
                    Rank #{{ submission.rank ?? (index + 1) }}
                  </span>
                  <span
                    v-if="showJudgeScores && submission.judge_score !== null"
                    class="flex items-center gap-1 text-[#7a1f2b] font-semibold"
                  >
                    Judge {{ formatScore(submission.judge_score) }}/50
                  </span>
                </div>
              
                <!-- Vote Button -->
                <button 
                  :disabled="!canVote || votingInProgress[submission.id]"
                  :title="!canVote ? voteDisabledReason : 'Vote'"
                  :aria-label="submission.has_voted ? 'Remove vote' : 'Vote for submission'"
                  :class="[
                    'flex items-center justify-center gap-1 px-4 py-2 rounded-full font-medium transition-all w-full sm:w-auto',
                    !canVote
                      ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                      : submission.has_voted 
                        ? 'bg-red-600 text-white hover:bg-red-700' 
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  ]"
                  @click.stop="toggleVote(submission)"
                >
                  <svg
                    class="w-4 h-4"
                    :fill="submission.has_voted ? 'currentColor' : 'none'"
                    :stroke="submission.has_voted ? 'none' : 'currentColor'"
                    viewBox="0 0 20 20"
                  >
                    <path
                      v-if="submission.has_voted"
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
                  {{ submission.vote_count }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div
          v-if="pagination && pagination.last_page > 1"
          class="mt-8 flex justify-center"
        >
          <div class="flex gap-2">
            <button 
              :disabled="pagination.current_page === 1"
              class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              @click="changePage(pagination.current_page - 1)"
            >
              Previous
            </button>
          
            <div class="flex gap-1">
              <button 
                v-for="page in visiblePages" 
                :key="page"
                :class="[
                  'px-4 py-2 border rounded-lg',
                  page === pagination.current_page 
                    ? 'bg-red-600 text-white border-red-600' 
                    : 'border-gray-300 hover:bg-gray-50'
                ]"
                @click="changePage(page)"
              >
                {{ page }}
              </button>
            </div>

            <button 
              :disabled="pagination.current_page === pagination.last_page"
              class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              @click="changePage(pagination.current_page + 1)"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api';

const route = useRoute();
const router = useRouter();

const competition = ref(null);
const submissions = ref([]);
const loading = ref(true);
const searchQuery = ref('');
const sortBy = ref('created_at');
const pagination = ref(null);
const totalSubmissions = ref(0);
const votingInProgress = ref({});
const showFilters = ref(false);
const resumeSubmissionId = ref(null);
const prefetchedImages = new Set();
const autoVoteModeTriggered = ref(false);
const showVoteTips = ref(false);

const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const isAuthenticated = computed(() => !!user.value);
const isLeaderboard = computed(() => route.path.includes('/leaderboard'));
const pageLabel = computed(() => (isLeaderboard.value ? 'Leaderboard' : 'Gallery'));
const searchPlaceholder = computed(() => (isLeaderboard.value
  ? 'Search entries by title or photographer...'
  : 'Search by title or photographer...'));
const showJudgeScores = computed(() => competition.value?.show_judge_reactions === true);
const voteWeightPercent = computed(() => {
  const value = Number(competition.value?.vote_weight ?? 0);
  if (Number.isNaN(value)) return 0;
  return Math.round(value * 100);
});
const judgeWeightPercent = computed(() => {
  const value = Number(competition.value?.judge_weight ?? 0);
  if (Number.isNaN(value)) return Math.max(0, 100 - voteWeightPercent.value);
  return Math.round(value * 100);
});
const showScoreSplit = computed(() => {
  if (!competition.value) return false;
  if (!isLeaderboard.value && !showJudgeScores.value) return false;
  if (competition.value.vote_weight === null && competition.value.judge_weight === null) return false;
  return true;
});
const sponsorLogoRatio = '1472 / 392';
const sponsorList = computed(() => {
  if (!competition.value) return [];
  const raw = competition.value.sponsors
    || competition.value.sponsorRecords
    || competition.value.sponsor_records
    || [];
  const list = Array.isArray(raw) ? raw : [];

  return list
    .filter((item) => {
      const pivot = item.pivot || {};
      const active = item.is_active ?? pivot.is_active ?? true;
      return active !== false;
    })
    .map((item, index) => {
      const pivot = item.pivot || {};
      const name = item.name || pivot.name || 'Sponsor';
      const logo = item.logo_url || pivot.logo_url || item.logo || null;
      const website = item.website_url || item.website || pivot.website_url || null;
      const description = item.description || pivot.description || null;
      const tier = item.tier || pivot.tier || 'bronze';
      return {
        id: item.id || pivot.sponsor_id || `${name}-${index}`,
        name,
        logo_url: logo,
        website,
        description,
        logo_credit_name: item.logo_credit_name || pivot.logo_credit_name || null,
        tier,
      };
    });
});
const isVotingActive = computed(() => {
  if (!competition.value) return false;
  if (!competition.value.voting_start_at || !competition.value.voting_end_at) return false;
  const now = new Date();
  return now >= new Date(competition.value.voting_start_at)
    && now <= new Date(competition.value.voting_end_at);
});
const isVotingUpcoming = computed(() => {
  if (!competition.value) return false;
  if (!competition.value.voting_start_at) return false;
  const now = new Date();
  return now < new Date(competition.value.voting_start_at);
});
const isVotingEnded = computed(() => {
  if (!competition.value) return false;
  if (!competition.value.voting_end_at) return false;
  const now = new Date();
  return now > new Date(competition.value.voting_end_at);
});
const votingCountdownShort = computed(() => {
  if (!competition.value) return '';
  if (isVotingActive.value) {
    return getTimeRemaining(competition.value.voting_end_at);
  }
  if (isVotingUpcoming.value) {
    return getTimeRemaining(competition.value.voting_start_at);
  }
  return '';
});
const votingBannerText = computed(() => {
  if (!competition.value) return '';
  if (!competition.value.allow_public_voting) return 'Public voting is disabled for this competition.';
  if (!competition.value.voting_start_at || !competition.value.voting_end_at) return '';
  if (isVotingActive.value) {
    return `Voting is live. Ends in ${votingCountdownShort.value}.`;
  }
  if (isVotingUpcoming.value) {
    return `Voting opens in ${votingCountdownShort.value}.`;
  }
  if (isVotingEnded.value) {
    return 'Voting has closed. Check the leaderboard for results.';
  }
  return '';
});
const votingBannerCtaLabel = computed(() => {
  if (!competition.value) return '';
  if (isVotingActive.value && !isAuthenticated.value) return 'Login to vote';
  if (isVotingEnded.value) return 'View Leaderboard';
  return '';
});
const votingBannerCtaAction = computed(() => {
  if (!competition.value) return '';
  if (isVotingActive.value && !isAuthenticated.value) return 'login';
  if (isVotingEnded.value) return 'leaderboard';
  return '';
});
const votingBannerTone = computed(() => {
  if (isVotingActive.value) return 'live';
  if (isVotingUpcoming.value) return 'upcoming';
  return 'closed';
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

const getTimeRemaining = (deadline) => {
  const now = new Date();
  const end = new Date(deadline);
  const diff = end - now;
  if (diff <= 0) return '0h';
  const days = Math.floor(diff / (1000 * 60 * 60 * 24));
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  if (days > 0) return `${days}d ${hours}h`;
  return `${hours}h`;
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

let searchTimeout = null;

onMounted(async () => {
  if (isLeaderboard.value) {
    sortBy.value = 'most_voted';
  }
  const tipsHidden = localStorage.getItem('psbGalleryVoteTipsHidden') === '1';
  showVoteTips.value = !tipsHidden;
  await fetchCompetition();
  await fetchSubmissions();
});

const fetchCompetition = async () => {
  try {
    const { data } = await api.get(`/competitions/${route.params.slug}`);
    competition.value = data.data;
    updatePageMeta();
    refreshResumeId();
  } catch (error) {
    console.error('Error fetching competition:', error);
    router.push('/competitions');
  }
};

const fetchSubmissions = async (page = 1) => {
  loading.value = true;
  try {
    const sortValue = sortBy.value === 'judge_score' ? 'final_score' : sortBy.value;
    const params = {
      page,
      sort_by: sortValue,
      search: searchQuery.value || undefined
    };

    const { data } = await api.get(`/competitions/${competition.value.id}/submissions`, { params });
    
    submissions.value = data.data.data || data.data;
    
    // Check vote status only when voting is available
    if (isAuthenticated.value && canVote.value) {
      await checkVoteStatus();
    }
    
    pagination.value = {
      current_page: data.data.current_page,
      last_page: data.data.last_page,
      per_page: data.data.per_page,
      total: data.data.total
    };
    totalSubmissions.value = pagination.value.total;
    maybeAutoStartVoteMode();
  } catch (error) {
    console.error('Error fetching submissions:', error);
  } finally {
    loading.value = false;
  }
};

const checkVoteStatus = async () => {
  try {
    await Promise.all(
      submissions.value.map(async (submission) => {
        const { data } = await api.get(
          `/competitions/${competition.value.id}/submissions/${submission.id}/vote-status`
        );
        submission.has_voted = data.data.has_voted;
      })
    );
  } catch (error) {
    console.error('Error checking vote status:', error);
  }
};

const toggleVote = async (submission) => {
  if (!canVote.value) {
    alert(voteDisabledReason.value);
    return;
  }
  if (!isAuthenticated.value) {
    alert('Please login to vote');
    router.push('/login');
    return;
  }
  
  votingInProgress.value[submission.id] = true;
  
  try {
    if (submission.has_voted) {
      // Unvote
      await api.delete(`/competitions/${competition.value.id}/submissions/${submission.id}/vote`);
      submission.has_voted = false;
      submission.vote_count--;
    } else {
      // Vote
      const { data } = await api.post(
        `/competitions/${competition.value.id}/submissions/${submission.id}/vote`
      );
      submission.has_voted = true;
      submission.vote_count = data.data.vote_count;
    }
  } catch (error) {
    console.error('Error voting:', error);
    alert(error.response?.data?.message || 'Failed to process vote');
  } finally {
    votingInProgress.value[submission.id] = false;
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    fetchSubmissions();
  }, 500);
};

const changePage = (page) => {
  if (page < 1 || page > pagination.value.last_page) return;
  fetchSubmissions(page);
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const visiblePages = computed(() => {
  if (!pagination.value) return [];
  
  const current = pagination.value.current_page;
  const last = pagination.value.last_page;
  const delta = 2;
  const range = [];
  
  for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
    range.push(i);
  }
  
  if (current - delta > 2) {
    range.unshift('...');
  }
  if (current + delta < last - 1) {
    range.push('...');
  }
  
  range.unshift(1);
  if (last > 1) {
    range.push(last);
  }
  
  return range.filter((v, i, a) => a.indexOf(v) === i && v !== '...' || v === '...');
});

const viewSubmission = (submission) => {
  router.push(`/competitions/${competition.value.slug}/submissions/${submission.id}`);
};

const prefetchFullImage = (submission) => {
  if (!submission) return;
  const url = submission.image_url || submission.thumbnail_url;
  if (!url) return;
  if (prefetchedImages.has(url)) return;
  prefetchedImages.add(url);
  const img = new Image();
  img.src = url;
};

const startVoteMode = () => {
  if (!submissions.value.length) {
    alert('No submissions available yet.');
    return;
  }
  const firstSubmission = submissions.value[0];
  router.push(`/competitions/${competition.value.slug}/submissions/${firstSubmission.id}?mode=vote`);
};

const resumeVoteMode = () => {
  if (!resumeSubmissionId.value || !competition.value?.slug) return;
  router.push(`/competitions/${competition.value.slug}/submissions/${resumeSubmissionId.value}?mode=vote`);
};

const handleVotingBannerCta = () => {
  if (!competition.value) return;
  if (votingBannerCtaAction.value === 'login') {
    router.push('/login');
    return;
  }
  if (votingBannerCtaAction.value === 'leaderboard') {
    router.push(`/competitions/${competition.value.slug}/leaderboard`);
  }
};

const refreshResumeId = () => {
  const slug = route.params.slug;
  if (!slug) {
    resumeSubmissionId.value = null;
    return;
  }
  const stored = localStorage.getItem(`psbVoteResume:${slug}`);
  resumeSubmissionId.value = stored ? Number(stored) : null;
};

const dismissVoteTips = () => {
  showVoteTips.value = false;
  localStorage.setItem('psbGalleryVoteTipsHidden', '1');
};

const maybeAutoStartVoteMode = () => {
  if (autoVoteModeTriggered.value) return;
  if (route.query.mode !== 'vote') return;
  if (!submissions.value.length) return;
  autoVoteModeTriggered.value = true;
  if (resumeSubmissionId.value) {
    resumeVoteMode();
    return;
  }
  startVoteMode();
};

const shareOnFacebook = () => {
  const url = encodeURIComponent(window.location.href);
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
  trackShareLog('facebook');
};

const shareOnWhatsApp = () => {
  const url = encodeURIComponent(window.location.href);
  const text = encodeURIComponent(`Check this ${pageLabel.value.toLowerCase()} from ${competition.value?.title || 'competition'}`);
  window.open(`https://wa.me/?text=${text}%20${url}`, '_blank');
  trackShareLog('whatsapp');
};

const shareOnLinkedIn = () => {
  const url = encodeURIComponent(window.location.href);
  window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank');
  trackShareLog('linkedin');
};

const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href);
    alert('Link copied to clipboard!');
    trackShareLog('copy');
  } catch (error) {
    console.error('Copy failed:', error);
  }
};

const trackShareLog = async (platform) => {
  try {
    await api.post('/growth/share-log', {
      entity_type: 'competition',
      entity_id: competition.value?.id || null,
      platform,
    });
  } catch (error) {
    console.warn('Share log failed:', error);
  }
};

const setMetaTag = (attribute, key, content) => {
  if (!content) return;
  let tag = document.head.querySelector(`meta[${attribute}="${key}"]`);
  if (!tag) {
    tag = document.createElement('meta');
    tag.setAttribute(attribute, key);
    document.head.appendChild(tag);
  }
  tag.setAttribute('content', content);
};

const updatePageMeta = () => {
  if (!competition.value || typeof window === 'undefined') return;
  const title = `${competition.value.title} ${isLeaderboard.value ? 'Leaderboard' : 'Gallery'} | Photographer SB`;
  const description = `Browse public submissions and votes for ${competition.value.title}.`;
  const canonical = `${window.location.origin}${route.fullPath}`;

  document.title = title;
  setMetaTag('name', 'description', description);
  setMetaTag('property', 'og:title', title);
  setMetaTag('property', 'og:description', description);
  setMetaTag('property', 'og:url', canonical);
  setMetaTag('property', 'og:type', 'website');
};
</script>
