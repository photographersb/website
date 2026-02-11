<template>
  <div class="min-h-screen bg-[#f7f2ee] text-[#1d1014]">
    <!-- Loading State -->
    <div
      v-if="loading"
      class="container mx-auto px-4 py-16 text-center"
    >
      <div class="inline-block animate-spin rounded-full h-16 w-16 border-b-4 border-[#7a1f2b]" />
      <p class="text-gray-600 mt-6 text-lg">
        Loading competition details...
      </p>
    </div>

    <!-- Competition Content -->
    <div
      v-else-if="competition"
      class="pb-16"
    >
      <!-- Hero Banner -->
      <div class="relative h-80 sm:h-96 md:h-[34rem] bg-[#1b0b12] overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(245,158,11,0.28)_0,_transparent_55%)]" />
        <div class="absolute -top-24 -right-24 h-72 w-72 rounded-full bg-[#f3b35a]/30 blur-3xl" />
        <div class="absolute -bottom-28 -left-24 h-80 w-80 rounded-full bg-[#c46b7a]/20 blur-3xl" />
        <img
          v-if="competition.hero_image || competition.banner_image"
          :src="competition.hero_image || competition.banner_image"
          :alt="competition.title"
          class="w-full h-full object-cover opacity-35"
          decoding="async"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent" />
        
        <div class="absolute inset-0 container mx-auto px-4 flex flex-col justify-end py-8 sm:py-12 md:py-16">
          <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
            <span :class="`px-4 sm:px-5 py-2 sm:py-2.5 rounded-full text-sm sm:text-base font-semibold ${getStatusBadgeClass(competition.status)} shadow-lg`">
              {{ formatStatus(competition.status) }}
            </span>
            <span
              v-if="votingCountdownLabel"
              :class="`px-4 sm:px-5 py-2 sm:py-2.5 rounded-full text-sm sm:text-base font-semibold ${votingCountdownClass} shadow-lg`"
            >
              {{ votingCountdownLabel }}
            </span>
            <span
              v-if="competition.is_featured"
              class="px-4 sm:px-5 py-2 sm:py-2.5 bg-amber-400 text-[#1b0b12] rounded-full text-sm sm:text-base font-semibold flex items-center gap-2 shadow-lg"
            >
              <svg
                class="w-5 h-5"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              Featured
            </span>
            <span
              v-if="competition.status === 'active'"
              class="px-4 sm:px-5 py-2 sm:py-2.5 bg-white/15 backdrop-blur-sm text-white rounded-full text-sm sm:text-base font-semibold"
            >
              ⏰ {{ getTimeRemaining(competition.submission_deadline) }}
            </span>
            <button
              v-if="showWinnersQuickLink"
              class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-full bg-white text-[#1b0b12] text-sm sm:text-base font-semibold shadow-lg hover:bg-white/90 transition"
              @click="viewWinners"
            >
              View Winners
            </button>
          </div>
          <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-semibold font-serif text-white mb-3 sm:mb-4 leading-tight drop-shadow-2xl">
            {{ competition.title }}
          </h1>
          <p
            v-if="competition.theme"
            class="text-lg sm:text-xl md:text-2xl text-[#f3d7c4] font-medium mb-8 drop-shadow-lg"
          >
            📷 {{ competition.theme }}
          </p>
          <p
            v-if="heroCredit"
            class="text-xs text-white/80"
          >
            Photo by
            <a
              :href="heroCredit.url"
              target="_blank"
              rel="noopener"
              class="font-semibold text-white underline"
            >
              {{ heroCredit.name }}
            </a>
            on Pexels.
          </p>
        </div>
      </div>

      <div class="container mx-auto px-4 -mt-16 sm:-mt-20 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
              <div class="bg-white/85 backdrop-blur rounded-2xl shadow-lg border border-[#eadfd7] p-6 md:p-8 text-center transition-transform hover:-translate-y-1">
                <div class="text-2xl md:text-3xl lg:text-4xl font-semibold text-[#7a1f2b] mb-2">
                  {{ Number.isFinite(Number(competition.total_prize_pool)) ? formatNumber(Math.floor(competition.total_prize_pool)) : 'TBD' }}
                </div>
                <div class="text-xs md:text-sm text-[#5f4a4f] font-medium">
                  Prize Pool
                </div>
              </div>
              <div class="bg-white/85 backdrop-blur rounded-2xl shadow-lg border border-[#eadfd7] p-6 md:p-8 text-center transition-transform hover:-translate-y-1">
                <div class="text-2xl md:text-3xl lg:text-3xl font-semibold text-[#1b0b12] mb-2">
                  {{ submissionCountDisplay }}
                </div>
                <div class="text-xs md:text-sm text-[#5f4a4f] font-medium">
                  Submissions
                </div>
              </div>
              <div class="bg-white/85 backdrop-blur rounded-2xl shadow-lg border border-[#eadfd7] p-6 md:p-8 text-center transition-transform hover:-translate-y-1">
                <div class="text-2xl md:text-3xl lg:text-3xl font-semibold text-[#7a1f2b] mb-2">
                  {{ voteCountDisplay }}
                </div>
                <div class="text-xs md:text-sm text-[#5f4a4f] font-medium">
                  Votes
                </div>
              </div>
              <div class="bg-white/85 backdrop-blur rounded-2xl shadow-lg border border-[#eadfd7] p-6 md:p-8 text-center transition-transform hover:-translate-y-1">
                <div class="text-2xl md:text-3xl lg:text-3xl font-semibold text-[#1b0b12] mb-2">
                  {{ competition.number_of_winners }}
                </div>
                <div class="text-xs md:text-sm text-[#5f4a4f] font-medium">
                  Winners
                </div>
              </div>
            </div>

            <div
              v-if="showVoteCta"
              class="bg-white/90 backdrop-blur rounded-2xl shadow-lg border border-[#eadfd7] p-5 md:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
            >
              <div>
                <p class="text-sm font-semibold text-[#1b0b12]">Public voting is live</p>
                <p class="text-xs text-gray-600">Support your favorite photos before voting closes.</p>
              </div>
              <div class="flex flex-wrap items-center gap-2">
                <button
                  class="px-5 py-2.5 rounded-full bg-[#7a1f2b] text-white text-sm font-semibold hover:bg-[#6a1a24] transition"
                  @click="handleVoteCta"
                >
                  {{ voteCtaLabel }}
                </button>
                <button
                  v-if="showVoteModeCta"
                  class="px-5 py-2.5 rounded-full border border-[#7a1f2b] text-[#7a1f2b] text-sm font-semibold hover:bg-[#7a1f2b]/10 transition"
                  @click="handleVoteModeCta"
                >
                  {{ voteModeCtaLabel }}
                </button>
              </div>
            </div>


            <div
              v-if="showResultsCta"
              class="bg-white/90 backdrop-blur rounded-2xl shadow-lg border border-[#eadfd7] p-5 md:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
            >
              <div>
                <p class="text-sm font-semibold text-[#1b0b12]">See the results</p>
                <p class="text-xs text-gray-600">
                  {{ resultsCtaDescription }}
                </p>
              </div>
              <button
                class="px-5 py-2.5 rounded-full bg-[#1b0b12] text-white text-sm font-semibold hover:bg-black transition"
                @click="handleResultsCta"
              >
                {{ resultsCtaLabel }}
              </button>
            </div>

            <div
              v-if="showScoreSplit"
              class="flex flex-wrap items-center gap-3"
            >
              <span class="text-xs uppercase tracking-wide text-[#5f4a4f] font-semibold">
                Winning Score Split
              </span>
              <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 text-xs font-semibold">
                Public Vote {{ voteWeightPercent }}%
              </span>
              <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#efe5dc] text-[#7a1f2b] text-xs font-semibold">
                Judge Score {{ judgeWeightPercent }}%
              </span>
            </div>

            <!-- Judge Reactions -->
            <div
              v-if="competition.judge_reactions && competition.judge_reactions.score_count > 0"
              class="bg-white/90 backdrop-blur rounded-2xl shadow-lg border border-[#eadfd7] p-6 md:p-8"
            >
              <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <h2 class="text-2xl md:text-3xl font-semibold font-serif text-[#1b0b12] flex items-center gap-3">
                  <span class="w-1.5 h-8 bg-[#7a1f2b] rounded-full" />
                  Judge Reactions
                </h2>
                <span class="text-sm text-gray-500">
                  {{ competition.judge_reactions.score_count }} scores
                </span>
              </div>
              <div class="space-y-4">
                <div
                  v-for="row in reactionRows"
                  :key="row.label"
                  class="bg-[#f7f2ee] rounded-xl p-4 border border-[#eadfd7]"
                >
                  <div class="flex items-center justify-between mb-2">
                    <p class="font-semibold text-gray-900">
                      {{ row.label }}
                    </p>
                    <p class="text-sm font-semibold text-gray-700">
                      {{ row.value.toFixed(1) }} / {{ row.max }}
                    </p>
                  </div>
                  <div class="h-2 bg-[#efe5dc] rounded-full overflow-hidden">
                    <div
                      class="h-full bg-gradient-to-r from-[#7a1f2b] to-[#c75d5d]"
                      :style="{ width: row.percent + '%' }"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Description -->
            <div class="bg-white/90 backdrop-blur rounded-2xl shadow-lg border border-[#eadfd7] p-6 md:p-8">
              <h2 class="text-2xl md:text-3xl font-semibold font-serif mb-4 text-[#1b0b12] flex items-center gap-3">
                <span class="w-1.5 h-8 bg-[#7a1f2b] rounded-full" />
                About This Competition
              </h2>
              <div class="prose prose-lg max-w-none">
                <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                  {{ competition.description }}
                </p>
              </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white/90 backdrop-blur rounded-2xl shadow-lg border border-[#eadfd7] p-6 md:p-8">
              <h2 class="text-2xl md:text-3xl font-semibold font-serif mb-6 text-[#1b0b12] flex items-center gap-3">
                <span class="w-1.5 h-8 bg-[#7a1f2b] rounded-full" />
                Competition Timeline
              </h2>
              <div class="space-y-4 sm:space-y-5">
                <div class="flex items-start gap-4">
                  <div :class="`w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg ${isDatePassed(competition.submission_deadline) ? 'bg-gray-400' : 'bg-gradient-to-br from-[#7a1f2b] to-[#c75d5d]'}`">
                    <svg
                      class="w-6 h-6 text-white"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                      />
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-base sm:text-lg">
                      Submission Deadline
                    </h3>
                    <p class="text-sm sm:text-base text-gray-600 break-words">
                      {{ formatDateTime(competition.submission_deadline) }}
                    </p>
                    <p
                      v-if="!isDatePassed(competition.submission_deadline)"
                      class="text-sm text-[#7a1f2b] font-medium mt-1"
                    >
                      {{ getTimeRemaining(competition.submission_deadline) }}
                    </p>
                  </div>
                </div>

                <div
                  v-if="competition.voting_start_at"
                  class="flex items-start gap-4"
                >
                  <div :class="`w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg ${isDatePassed(competition.voting_end_at) ? 'bg-gray-400' : isVotingActive ? 'bg-gradient-to-br from-amber-500 to-amber-700' : 'bg-gray-400'}`">
                    <svg
                      class="w-6 h-6 text-white"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"
                      />
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-base sm:text-lg">
                      Voting Period
                    </h3>
                    <p class="text-sm sm:text-base text-gray-600 break-words">
                      {{ formatDateTime(competition.voting_start_at) }} - {{ formatDateTime(competition.voting_end_at) }}
                    </p>
                    <p
                      v-if="isVotingActive"
                      class="text-sm text-amber-600 font-medium mt-1"
                    >
                      Voting ends in {{ votingCountdownShort }}
                    </p>
                    <p
                      v-else-if="isVotingUpcoming"
                      class="text-sm text-amber-700 font-medium mt-1"
                    >
                      Voting opens in {{ votingCountdownShort }}
                    </p>
                    <p
                      v-else-if="isVotingEnded"
                      class="text-sm text-gray-500 font-medium mt-1"
                    >
                      Voting has closed
                    </p>
                  </div>
                </div>

                <div
                  v-if="competition.results_announcement_date"
                  class="flex items-start gap-4"
                >
                  <div :class="`w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg ${competition.results_published ? 'bg-gradient-to-br from-green-500 to-green-700' : 'bg-gray-400'}`">
                    <svg
                      class="w-6 h-6 text-white"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"
                      />
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-base sm:text-lg">
                      Winner Announcement
                    </h3>
                    <p class="text-sm sm:text-base text-gray-600 break-words">
                      {{ formatDate(competition.results_announcement_date) }}
                    </p>
                    <p
                      v-if="competition.results_published"
                      class="text-sm text-green-600 font-medium mt-1"
                    >
                      Results are now available!
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Judges Section -->
            <div
              v-if="judgeProfilesList.length > 0"
              class="bg-white/90 backdrop-blur rounded-2xl shadow-lg border border-[#eadfd7] p-6 md:p-8"
            >
              <h2 class="text-2xl md:text-3xl font-semibold font-serif mb-6 text-[#1b0b12] flex items-center gap-3">
                <span class="w-1.5 h-8 bg-[#7a1f2b] rounded-full" />
                Our Judges
              </h2>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                  v-for="judge in judgeProfilesList"
                  :key="judge.id" 
                  class="bg-[#f7f2ee] rounded-xl p-6 hover:shadow-lg transition-shadow border border-[#eadfd7]"
                >
                  <div class="flex flex-col items-center text-center">
                    <!-- Judge Avatar -->
                    <img
                      v-if="judge.profile_photo_url" 
                      :src="judge.profile_photo_url" 
                      :alt="judge.name" 
                      class="w-20 h-20 rounded-full object-cover mb-4 border-4 border-[#7a1f2b]"
                      loading="lazy"
                      decoding="async"
                    >
                    <div
                      v-else 
                      class="w-20 h-20 rounded-full bg-gradient-to-br from-[#7a1f2b] to-[#c75d5d] flex items-center justify-center text-white text-2xl font-bold mb-4 border-4 border-[#7a1f2b]"
                    >
                      {{ judge.name.charAt(0) }}
                    </div>
                    
                    <!-- Judge Info -->
                    <h3 class="text-lg font-bold text-gray-900 mb-1">
                      {{ judge.name }}
                    </h3>
                    <p
                      v-if="judge.expertise"
                      class="text-sm text-[#7a1f2b] font-medium mb-3"
                    >
                      {{ judge.expertise }}
                    </p>
                    <p class="text-xs text-gray-600 leading-relaxed">
                      {{ judge.bio || 'Professional photographer and expert judge' }}
                    </p>
                    
                    <!-- Judge Badge -->
                    <div class="mt-4 inline-block bg-[#7a1f2b] text-white px-3 py-1 rounded-full text-xs font-semibold">
                      ⚖️ Chief Judge
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Rules & Eligibility Section -->
            <div class="bg-white/90 backdrop-blur rounded-2xl shadow-lg border border-[#eadfd7] p-6 md:p-8">
              <button
                class="w-full flex items-center justify-between mb-6 hover:opacity-80 transition-opacity"
                @click="showRules = !showRules"
              >
                <h2 class="text-2xl md:text-3xl font-semibold font-serif text-[#1b0b12] flex items-center gap-3">
                  <span class="w-1.5 h-8 bg-[#7a1f2b] rounded-full" />
                  Rules & Eligibility
                </h2>
                <svg
                  :class="['w-6 h-6 text-[#7a1f2b] transition-transform', showRules ? 'rotate-180' : '']"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 14l-7 7m0 0l-7-7m7 7V3"
                  />
                </svg>
              </button>
              <div
                v-if="showRules"
                class="prose prose-sm max-w-none text-gray-700 leading-relaxed animate-in"
              >
                <p
                  v-if="competition.rules"
                  class="whitespace-pre-line"
                >
                  {{ competition.rules }}
                </p>
                <p
                  v-else
                  class="text-gray-500 italic"
                >
                  No specific rules provided for this competition. Please check with the organizer for details.
                </p>
                <div
                  v-if="competition.terms_and_conditions"
                  class="mt-6 pt-6 border-t"
                >
                  <h3 class="text-lg font-bold text-gray-900 mb-3">
                    Terms & Conditions
                  </h3>
                  <p class="whitespace-pre-line">
                    {{ competition.terms_and_conditions }}
                  </p>
                </div>
              </div>
              <div
                v-else
                class="text-center py-4"
              >
                <p class="text-sm text-gray-600">
                  Click to view competition rules and eligibility criteria
                </p>
              </div>
            </div>

            <!-- Sponsors Section -->
            <div
              v-if="sponsorList.length > 0"
              class="bg-white/90 backdrop-blur rounded-2xl shadow-lg border border-[#eadfd7] p-6 md:p-8"
            >
              <h2 class="text-2xl md:text-3xl font-semibold font-serif mb-6 text-[#1b0b12] flex items-center gap-3">
                <span class="w-1.5 h-8 bg-[#7a1f2b] rounded-full" />
                Our Sponsors
              </h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <component
                  :is="sponsor.website ? 'a' : 'div'"
                  v-for="sponsor in sponsorList"
                  :key="sponsor.id" 
                  :href="sponsor.website || undefined"
                  target="_blank"
                  rel="noopener"
                  :aria-label="sponsor.website ? `Visit ${sponsor.name}` : sponsor.name"
                  class="bg-[#f7f2ee] rounded-xl p-6 hover:shadow-lg transition-shadow border border-[#eadfd7] flex items-center justify-center"
                >
                  <div
                    class="w-44 sm:w-52 md:w-56 flex-shrink-0"
                    :style="{ aspectRatio: sponsorLogoRatio }"
                  >
                    <img
                      v-if="sponsor.logo_url" 
                      :src="sponsor.logo_url" 
                      :alt="sponsor.name" 
                      class="w-full h-full object-contain"
                    >
                    <div
                      v-else 
                      class="w-full h-full rounded-lg bg-gray-300 flex items-center justify-center text-gray-600 font-bold text-sm"
                    >
                      {{ sponsor.name.substring(0, 2).toUpperCase() }}
                    </div>
                  </div>
                </component>
              </div>
            </div>

            <!-- Mentors Section -->
            <div
              v-if="competition.mentors && competition.mentors.length > 0"
              class="bg-white/90 backdrop-blur rounded-2xl shadow-lg border border-[#eadfd7] p-6 md:p-8"
            >
              <h2 class="text-2xl md:text-3xl font-semibold font-serif mb-6 text-[#1b0b12] flex items-center gap-3">
                <span class="w-1.5 h-8 bg-[#7a1f2b] rounded-full" />
                Our Mentors
              </h2>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                  v-for="mentor in competition.mentors"
                  :key="mentor.id" 
                  class="bg-[#f7f2ee] rounded-xl p-6 hover:shadow-lg transition-shadow border border-[#eadfd7]"
                >
                  <div class="flex flex-col items-center text-center">
                    <!-- Mentor Avatar -->
                    <img
                      v-if="mentor.profile_photo_url" 
                      :src="mentor.profile_photo_url" 
                      :alt="mentor.name" 
                      class="w-20 h-20 rounded-full object-cover mb-4 border-4 border-blue-500"
                    >
                    <div
                      v-else 
                      class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-2xl font-bold mb-4 border-4 border-blue-500"
                    >
                      {{ mentor.name.charAt(0) }}
                    </div>
                    
                    <!-- Mentor Info -->
                    <h3 class="text-lg font-bold text-gray-900 mb-1">
                      {{ mentor.name }}
                    </h3>
                    <p
                      v-if="mentor.expertise"
                      class="text-sm text-blue-600 font-medium mb-3"
                    >
                      {{ mentor.expertise }}
                    </p>
                    <p class="text-xs text-gray-600 leading-relaxed">
                      {{ mentor.bio || 'Experienced mentor and guide' }}
                    </p>
                    
                    <!-- Mentor Badge -->
                    <div class="mt-4 inline-block bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                      👨‍🏫 Mentor
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Submissions / Leaderboard -->
            <div class="bg-white/90 backdrop-blur rounded-2xl shadow-lg border border-[#eadfd7] p-6 md:p-8">
              <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl md:text-3xl font-semibold font-serif text-[#1b0b12] flex items-center gap-3">
                  <span class="w-1.5 h-8 bg-[#7a1f2b] rounded-full" />
                  {{ competition.status === 'completed' ? 'Winners' : isVotingActive ? 'Leaderboard' : 'Top Submissions' }}
                </h2>
                <button
                  v-if="competition.total_submissions > 0 && showLeaderboardCta"
                  class="bg-[#7a1f2b] hover:bg-[#5f1421] text-white font-semibold px-4 py-2 rounded-full flex items-center gap-2 transition-colors shadow-lg" 
                  @click="viewLeaderboard"
                >
                  View All
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
                      d="M9 5l7 7-7 7"
                    />
                  </svg>
                </button>
              </div>

              <div
                v-if="topSubmissions.length > 0"
                class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5"
              >
                <div
                  v-for="(submission, index) in topSubmissions"
                  :key="submission.id" 
                  class="relative group cursor-pointer rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-shadow focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#7a1f2b] focus-visible:ring-offset-2"
                  role="button"
                  tabindex="0"
                  :aria-label="`View submission ${submission.title || 'photo'}`"
                  @click="viewSubmission(submission)"
                  @keydown.enter="viewSubmission(submission)"
                  @keydown.space.prevent="viewSubmission(submission)"
                >
                  <img
                    :src="submission.image_url"
                    :alt="submission.title"
                    class="w-full h-72 object-cover group-hover:scale-105 transition-transform duration-300"
                  >
                  <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent opacity-100 transition-opacity">
                    <div class="absolute bottom-0 left-0 right-0 p-5 text-white">
                      <div class="flex items-center justify-between mb-3">
                        <span class="text-2xl md:text-3xl font-black">#{{ index + 1 }}</span>
                        <span class="bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full text-sm font-semibold">
                          {{ submission.vote_count }} votes
                        </span>
                        <span
                          v-if="showJudgeScores && submission.judge_score !== null"
                          class="bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full text-sm font-semibold"
                        >
                          Judge {{ formatScore(submission.judge_score) }}/50
                        </span>
                      </div>
                      <h4 class="font-bold text-lg mb-1">
                        {{ submission.title }}
                      </h4>
                      <p class="text-sm text-gray-200">
                        by {{ submission.photographer?.name || 'Anonymous' }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              <div
                v-else
                class="text-center py-12 text-gray-500"
              >
                <svg
                  class="w-16 h-16 mx-auto mb-4 text-gray-300"
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
                <p class="text-lg font-semibold">
                  No submissions yet
                </p>
                <p class="text-sm">
                  Be the first to participate!
                </p>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="lg:col-span-1 space-y-8">
            <!-- Action Card -->
            <div class="bg-white/90 backdrop-blur rounded-2xl shadow-lg border border-[#eadfd7] p-6 lg:sticky lg:top-6">
              <div class="space-y-5">
                <!-- Status Info -->
                <div class="p-5 bg-[#f7f2ee] rounded-xl border-l-4 border-[#7a1f2b]">
                  <h3 class="font-bold mb-4 text-lg text-gray-900">
                    Competition Details
                  </h3>
                  <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center">
                      <span class="text-gray-600 font-medium">Entry Fee</span>
                      <span class="font-semibold">{{ competition.is_paid_competition ? `৳${competition.participation_fee}` : 'Free' }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Max Entries</span>
                      <span class="font-semibold">{{ competition.max_submissions_per_user }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Public Voting</span>
                      <span class="font-semibold">{{ competition.allow_public_voting ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Watermark</span>
                      <span class="font-semibold">{{ competition.require_watermark ? 'Required' : 'Optional' }}</span>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div
                  v-if="competition.status === 'active'"
                  class="space-y-3"
                >
                  <button
                    v-if="isAuthenticated"
                    class="w-full bg-[#7a1f2b] text-white py-3 rounded-xl font-semibold hover:bg-[#5f1421] transition-colors"
                    @click="submitPhoto"
                  >
                    Submit Your Photo
                  </button>
                  <button
                    v-else
                    class="w-full bg-[#7a1f2b] text-white py-3 rounded-xl font-semibold hover:bg-[#5f1421] transition-colors"
                    @click="$router.push('/login')"
                  >
                    Login to Participate
                  </button>
                  <button
                    class="w-full bg-[#efe5dc] text-[#1b0b12] py-3 rounded-xl font-semibold hover:bg-[#eadfd7] transition-colors"
                    @click="showLeaderboardCta ? viewLeaderboard() : viewGallery()"
                  >
                    {{ showLeaderboardCta ? 'View Leaderboard' : 'View Gallery' }}
                  </button>
                </div>

                <div
                  v-else-if="competition.status === 'judging'"
                  class="space-y-3"
                >
                  <button
                    v-if="showLeaderboardCta"
                    class="w-full bg-amber-500 text-white py-3 rounded-xl font-semibold hover:bg-amber-600 transition-colors"
                    @click="viewLeaderboard"
                  >
                    View Leaderboard
                  </button>
                  <div class="text-center text-sm text-gray-600">
                    <p class="font-semibold">
                      Judging in Progress
                    </p>
                    <p>Winners will be announced soon!</p>
                  </div>
                </div>

                <div
                  v-else-if="competition.status === 'completed'"
                  class="space-y-3"
                >
                  <button
                    v-if="showLeaderboardCta"
                    class="w-full bg-[#1b0b12] text-white py-3 rounded-xl font-semibold hover:bg-[#14070d] transition-colors"
                    @click="viewLeaderboard"
                  >
                    View Winners
                  </button>
                  <div class="text-center text-sm text-gray-600">
                    <p class="font-semibold">
                      Competition Ended
                    </p>
                    <p>Check out the winners!</p>
                  </div>
                </div>

                <div
                  v-else
                  class="text-center p-4 bg-gray-50 rounded-lg"
                >
                  <p class="text-gray-600 font-semibold">
                    Coming Soon
                  </p>
                  <p class="text-sm text-gray-500 mt-1">
                    Submissions not yet open
                  </p>
                </div>
              </div>

              <!-- Organizer Info -->
              <div
                v-if="competition.organizer"
                class="mt-6 pt-6 border-t"
              >
                <h3 class="font-semibold mb-3">
                  Organized By
                </h3>
                <div class="flex items-center gap-3">
                  <img
                    v-if="competition.organizer.profile_photo_url"
                    :src="competition.organizer.profile_photo_url"
                    :alt="competition.organizer.business_name"
                    class="w-12 h-12 rounded-full object-cover"
                  >
                  <div
                    v-else
                    class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600 font-bold"
                  >
                    {{ competition.organizer.business_name?.charAt(0) }}
                  </div>
                  <div>

                <div
                  v-if="competition"
                  class="sm:hidden fixed bottom-0 inset-x-0 z-30"
                >
                  <div class="bg-white/95 backdrop-blur border-t border-[#eadfd7] px-4 pt-3 pb-4">
                    <div class="flex items-center gap-3">
                      <button
                        v-if="mobileSecondary"
                        class="flex-1 py-3 rounded-xl font-semibold border border-[#eadfd7] text-[#1b0b12] bg-white"
                        @click="handleMobileAction(mobileSecondary.action)"
                      >
                        {{ mobileSecondary.label }}
                      </button>
                      <button
                        class="flex-1 py-3 rounded-xl font-semibold text-white"
                        :class="mobilePrimary.class"
                        @click="handleMobileAction(mobilePrimary.action)"
                      >
                        {{ mobilePrimary.label }}
                      </button>
                    </div>
                  </div>
                </div>
                <div class="h-24 sm:hidden" />
                    <p class="font-semibold">
                      {{ competition.organizer.business_name }}
                    </p>
                    <p class="text-sm text-gray-600">
                      {{ competition.organizer.city }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Share Card -->
            <div class="bg-white/90 backdrop-blur rounded-2xl shadow-lg border border-[#eadfd7] p-6 md:p-8">
              <h3 class="font-semibold mb-3 text-sm sm:text-base">
                Share Competition
              </h3>
              <div class="flex flex-wrap gap-2">
                <button
                  class="flex-1 min-w-[100px] bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm sm:text-base"
                  @click="shareOnFacebook"
                >
                  Facebook
                </button>
                <button
                  class="flex-1 min-w-[100px] bg-sky-500 text-white py-2 rounded-lg hover:bg-sky-600 transition-colors text-sm sm:text-base"
                  @click="shareOnTwitter"
                >
                  Twitter
                </button>
                <button
                  class="px-3 sm:px-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                  aria-label="Copy competition link"
                  @click="copyLink"
                >
                  <svg
                    class="w-5 h-5 text-gray-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div
      v-else
      class="container mx-auto px-4 py-16 text-center"
    >
      <svg
        class="mx-auto h-16 w-16 text-gray-400 mb-4"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
        />
      </svg>
      <h3 class="text-xl font-semibold text-gray-900 mb-2">
        Competition Not Found
      </h3>
      <p class="text-gray-600 mb-6">
        The competition you're looking for doesn't exist or has been removed.
      </p>
      <button
        class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-colors"
        @click="$router.push('/competitions')"
      >
        Browse Competitions
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '@/api';
import {
  formatDate as formatDateValue,
  formatDateTime as formatDateTimeValue,
  formatNumber as formatNumberValue
} from '../utils/formatters';

const route = useRoute();
const router = useRouter();

const competition = ref(null);
const topSubmissions = ref([]);
const loading = ref(true);
const isAuthenticated = ref(false);
const showRules = ref(false);
const resumeVoteSubmissionId = ref(null);
const leaderboardTotals = ref({ submissions: null, votes: null });

const isVotingActive = computed(() => {
  if (!competition.value) return false;
  if (!competition.value.voting_start_at || !competition.value.voting_end_at) return false;
  const now = new Date();
  const start = new Date(competition.value.voting_start_at);
  const end = new Date(competition.value.voting_end_at);
  return now >= start && now <= end;
});

const isVotingUpcoming = computed(() => {
  if (!competition.value) return false;
  if (!competition.value.voting_start_at || !competition.value.voting_end_at) return false;
  const now = new Date();
  const start = new Date(competition.value.voting_start_at);
  return now < start;
});

const isVotingEnded = computed(() => {
  if (!competition.value) return false;
  if (!competition.value.voting_end_at) return false;
  const now = new Date();
  const end = new Date(competition.value.voting_end_at);
  return now > end;
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

const votingCountdownLabel = computed(() => {
  if (!competition.value) return '';
  if (!competition.value.allow_public_voting) return '';
  if (!competition.value.voting_start_at || !competition.value.voting_end_at) return '';
  if (isVotingActive.value) {
    return `Voting ends in ${votingCountdownShort.value}`;
  }
  if (isVotingUpcoming.value) {
    return `Voting opens in ${votingCountdownShort.value}`;
  }
  if (isVotingEnded.value) {
    return 'Voting closed';
  }
  return '';
});

const votingCountdownClass = computed(() => {
  if (isVotingActive.value) return 'bg-amber-100 text-amber-800';
  if (isVotingUpcoming.value) return 'bg-slate-100 text-slate-700';
  if (isVotingEnded.value) return 'bg-gray-200 text-gray-600';
  return 'bg-gray-200 text-gray-600';
});

const showLeaderboardCta = computed(() => {
  if (!competition.value) return false;
  if (competition.value.status === 'completed') return true;
  return competition.value.allow_public_voting === true;
});

const showVoteCta = computed(() => {
  if (!competition.value) return false;
  if (!competition.value.allow_public_voting) return false;
  if (!isVotingActive.value) return false;
  return true;
});

const showResultsCta = computed(() => {
  if (!competition.value) return false;
  if (competition.value.status === 'completed') return true;
  if (isVotingEnded.value) return true;
  if (competition.value.status === 'judging') return true;
  return false;
});

const showWinnersQuickLink = computed(() => {
  if (!competition.value) return false;
  if (competition.value.results_published) return true;
  return competition.value.status === 'completed';
});


const resultsCtaLabel = computed(() => {
  if (competition.value?.status === 'completed') return 'View Winners';
  return 'View Leaderboard';
});

const resultsCtaDescription = computed(() => {
  if (competition.value?.status === 'completed') {
    return 'The winners are live. Explore the final rankings.';
  }
  return 'Voting is closed. Explore the leaderboard rankings.';
});

const showVoteModeCta = computed(() => showVoteCta.value === true);

const voteModeCtaLabel = computed(() => {
  return resumeVoteSubmissionId.value ? 'Resume Vote Mode' : 'Start Vote Mode';
});

const voteCtaLabel = computed(() => {
  return isAuthenticated.value ? 'Vote Now' : 'Login to Vote';
});

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

const judgeProfilesList = computed(() => {
  if (!competition.value) return [];
  const list = competition.value.judgeProfiles || competition.value.judge_profiles || [];
  return Array.isArray(list) ? list : [];
});

const sponsorList = computed(() => {
  if (!competition.value) return [];
  const raw = competition.value.sponsors
    || competition.value.sponsorRecords
    || competition.value.sponsor_records
    || [];

  const list = Array.isArray(raw) ? raw : [];
  return list.map((item, index) => {
    const pivot = item.pivot || {};
    const name = item.name || pivot.name || 'Sponsor';
    const logo = item.logo_url || pivot.logo_url || item.logo || null;
    const website = item.website || item.website_url || pivot.website_url || null;
    const description = item.description || pivot.description || null;
    return {
      id: item.id || pivot.sponsor_id || `${name}-${index}`,
      name,
      logo_url: logo,
      website,
      description,
    };
  });
});

const sponsorLogoRatio = '1472 / 392';

const showScoreSplit = computed(() => {
  if (!competition.value) return false;
  if (competition.value.vote_weight === null && competition.value.judge_weight === null) return false;
  if (voteWeightPercent.value === 0 && judgeWeightPercent.value === 0) return false;
  return true;
});

const mobilePrimary = computed(() => {
  if (!competition.value) return null;
  const status = competition.value.status;

  if (status === 'active') {
    return {
      label: isAuthenticated.value ? 'Submit Photo' : 'Login to Participate',
      action: isAuthenticated.value ? 'submit' : 'login',
      class: 'bg-[#7a1f2b]'
    };
  }

  if (status === 'judging' && showLeaderboardCta.value) {
    return {
      label: 'View Leaderboard',
      action: 'leaderboard',
      class: 'bg-amber-600'
    };
  }

  if (status === 'completed' && showLeaderboardCta.value) {
    return {
      label: 'View Winners',
      action: 'leaderboard',
      class: 'bg-[#1b0b12]'
    };
  }

  return {
    label: 'View Gallery',
    action: 'gallery',
    class: 'bg-[#7a1f2b]'
  };
});

const mobileSecondary = computed(() => {
  if (!competition.value) return null;
  const status = competition.value.status;
  if (status === 'active') {
    return { label: 'View Gallery', action: 'gallery' };
  }
  if ((status === 'judging' || status === 'completed') && showLeaderboardCta.value) {
    return { label: 'View Gallery', action: 'gallery' };
  }
  return null;
});

const heroCredit = computed(() => {
  if (!competition.value) return null;
  if (competition.value.hero_image && competition.value.hero_image_credit_name) {
    return {
      name: competition.value.hero_image_credit_name,
      url: competition.value.hero_image_credit_url || 'https://www.pexels.com',
    };
  }
  if (!competition.value.hero_image && competition.value.banner_image && competition.value.banner_image_credit_name) {
    return {
      name: competition.value.banner_image_credit_name,
      url: competition.value.banner_image_credit_url || 'https://www.pexels.com',
    };
  }
  return null;
});

const reactionRows = computed(() => {
  const reactions = competition.value?.judge_reactions;
  if (!reactions) return [];

  const rows = [
    { label: 'Composition', value: Number(reactions.composition_avg || 0), max: 10 },
    { label: 'Technical Quality', value: Number(reactions.technical_avg || 0), max: 10 },
    { label: 'Creativity', value: Number(reactions.creativity_avg || 0), max: 10 },
    { label: 'Story/Impact', value: Number(reactions.story_avg || 0), max: 10 },
    { label: 'Overall Impression', value: Number(reactions.impact_avg || 0), max: 10 },
    { label: 'Total Score', value: Number(reactions.total_avg || 0), max: 50 },
  ];

  return rows.map((row) => ({
    ...row,
    percent: row.max > 0 ? Math.min(100, Math.round((row.value / row.max) * 100)) : 0,
  }));
});

const showJudgeScores = computed(() => competition.value?.show_judge_reactions === true);
const submissionCountDisplay = computed(() => {
  const total = Number(competition.value?.total_submissions);
  if (Number.isFinite(total)) return total;
  const fallback = Number(leaderboardTotals.value.submissions);
  if (Number.isFinite(fallback)) return fallback;
  return 0;
});
const voteCountDisplay = computed(() => {
  const total = Number(competition.value?.total_votes);
  if (Number.isFinite(total)) return total;
  const fallback = Number(leaderboardTotals.value.votes);
  if (Number.isFinite(fallback)) return fallback;
  return 0;
});

const fetchCompetition = async () => {
  try {
    const slug = route.params.slug;
    const { data } = await api.get(`/competitions/${slug}`);
    
    if (data.status === 'success') {
      competition.value = data.data;
      refreshVoteResumeId();
      fetchTopSubmissions(slug);
    }
  } catch (error) {
    console.error('Error fetching competition:', error);
    competition.value = null;
  } finally {
    loading.value = false;
  }
};

const fetchTopSubmissions = async (slug) => {
  try {
    const competitionSlug = slug || competition.value?.slug || route.params.slug;
    const { data } = await api.get(`/competitions/${competitionSlug}/leaderboard`);
    if (data.status === 'success') {
      const all = Array.isArray(data.data) ? data.data : [];
      leaderboardTotals.value = {
        submissions: all.length,
        votes: all.reduce((sum, item) => sum + Number(item.vote_count || 0), 0),
      };
      topSubmissions.value = all.slice(0, 6);
    }
  } catch (error) {
    console.error('Error fetching submissions:', error);
  }
};

const checkAuth = () => {
  isAuthenticated.value = !!localStorage.getItem('user');
};

const refreshVoteResumeId = () => {
  const slug = competition.value?.slug || route.params.slug;
  if (!slug) {
    resumeVoteSubmissionId.value = null;
    return;
  }
  const stored = localStorage.getItem(`psbVoteResume:${slug}`);
  resumeVoteSubmissionId.value = stored ? Number(stored) : null;
};

const submitPhoto = () => {
  router.push(`/competitions/${competition.value.slug}/submit`);
};

const viewGallery = () => {
  router.push(`/competitions/${competition.value.slug}/gallery`);
};

const viewLeaderboard = () => {
  router.push(`/competitions/${competition.value.slug}/leaderboard`);
};

const viewWinners = () => {
  router.push(`/competitions/${competition.value.slug}/winners`);
};

const handleMobileAction = (action) => {
  if (action === 'submit') {
    submitPhoto();
    return;
  }
  if (action === 'login') {
    router.push('/login');
    return;
  }
  if (action === 'leaderboard') {
    viewLeaderboard();
    return;
  }
  viewGallery();
};

const viewSubmission = (submission) => {
  if (!submission?.id || !competition.value?.slug) return;
  router.push(`/competitions/${competition.value.slug}/submissions/${submission.id}`);
};

const getStatusBadgeClass = (status) => {
  const classes = {
    draft: 'bg-[#efe5dc] text-[#7a1f2b]',
    active: 'bg-emerald-100 text-emerald-800',
    judging: 'bg-amber-100 text-amber-800',
    completed: 'bg-blue-100 text-blue-800',
    cancelled: 'bg-rose-100 text-rose-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const formatStatus = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1);
};

const formatDate = (date) => {
  return formatDateValue(date);
};

const formatDateTime = (date) => {
  return formatDateTimeValue(date);
};

const formatNumber = (num) => {
  return formatNumberValue(num);
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

const isDatePassed = (date) => {
  return new Date(date) < new Date();
};

const getTimeRemaining = (deadline) => {
  const now = new Date();
  const end = new Date(deadline);
  const diff = end - now;
  
  if (diff < 0) return 'Deadline passed';
  
  const days = Math.floor(diff / (1000 * 60 * 60 * 24));
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  
  if (days > 0) return `${days} day${days > 1 ? 's' : ''}`;
  if (hours > 0) return `${hours} hour${hours > 1 ? 's' : ''}`;
  return 'Less than an hour';
};

const shareOnFacebook = () => {
  const url = encodeURIComponent(window.location.href);
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
};

const shareOnTwitter = () => {
  const url = encodeURIComponent(window.location.href);
  const text = encodeURIComponent(`Check out this photography competition: ${competition.value.title}`);
  window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
};

const handleVoteCta = () => {
  if (!competition.value?.slug) return;
  if (!isAuthenticated.value) {
    router.push('/login');
    return;
  }
  router.push(`/competitions/${competition.value.slug}/gallery`);
};

const handleVoteModeCta = () => {
  if (!competition.value?.slug) return;
  if (!isAuthenticated.value) {
    router.push('/login');
    return;
  }
  router.push(`/competitions/${competition.value.slug}/gallery?mode=vote`);
};

const handleResultsCta = () => {
  if (!competition.value?.slug) return;
  if (competition.value.status === 'completed') {
    router.push(`/competitions/${competition.value.slug}/winners`);
    return;
  }
  router.push(`/competitions/${competition.value.slug}/leaderboard`);
};


const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href);
    alert('Link copied to clipboard!');
  } catch (error) {
    console.error('Error copying link:', error);
  }
};

onMounted(() => {
  checkAuth();
  fetchCompetition();
});
</script>
