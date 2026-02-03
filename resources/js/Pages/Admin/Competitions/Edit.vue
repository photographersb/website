<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader
      title="Edit Competition"
      subtitle="Update competition details"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <!-- Authorization Error Alert -->
      <div v-if="errors.auth" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-red-50 border border-red-300 rounded-lg p-4">
          <div class="flex items-start gap-3">
            <div class="text-red-600 text-lg font-bold">⚠️</div>
            <div>
              <h3 class="text-red-900 font-semibold">Authorization Error</h3>
              <p class="text-red-700 text-sm mt-1">{{ errors.auth }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Form -->
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form @submit.prevent="submitForm" class="space-y-6">
          <!-- Basic Information -->
          <div class="bg-white rounded-lg shadow-card p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Basic Information</h2>

            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                <input
                  v-model="form.title"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
                <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                <input
                  v-model="form.slug"
                  type="text"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
                <p class="mt-1 text-sm text-gray-500">Leave blank to auto-generate from title</p>
                <p v-if="errors.slug" class="mt-1 text-sm text-red-600">{{ errors.slug }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Theme *</label>
                <input
                  v-model="form.theme"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
                <p v-if="errors.theme" class="mt-1 text-sm text-red-600">{{ errors.theme }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select
                  v-model="form.category_id"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                  <option :value="null">Select a category (optional)</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
                <p v-if="errors.category_id" class="mt-1 text-sm text-red-600">{{ errors.category_id[0] }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea
                  v-model="form.description"
                  rows="4"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                ></textarea>
                <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hero Image URL</label>
                <input
                  v-model="form.hero_image"
                  type="url"
                  placeholder="https://..."
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
                <p v-if="errors.hero_image" class="mt-1 text-sm text-red-600">{{ errors.hero_image }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Banner Image URL</label>
                <input
                  v-model="form.banner_image"
                  type="url"
                  placeholder="https://..."
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
                <p v-if="errors.banner_image" class="mt-1 text-sm text-red-600">{{ errors.banner_image }}</p>
              </div>
            </div>
          </div>

          <!-- Timeline -->
          <div class="bg-white rounded-lg shadow-card p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Competition Timeline</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Submission Deadline *</label>
                <input
                  v-model="form.submission_deadline"
                  type="datetime-local"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
                <p v-if="errors.submission_deadline" class="mt-1 text-sm text-red-600">{{ errors.submission_deadline[0] }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Voting Start Date *</label>
                <input
                  v-model="form.voting_start_at"
                  type="datetime-local"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
                <p v-if="errors.voting_start_at" class="mt-1 text-sm text-red-600">{{ errors.voting_start_at[0] }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Voting End Date *</label>
                <input
                  v-model="form.voting_end_at"
                  type="datetime-local"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
                <p v-if="errors.voting_end_at" class="mt-1 text-sm text-red-600">{{ errors.voting_end_at[0] }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Judging Start</label>
                <input
                  v-model="form.judging_start_at"
                  type="datetime-local"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Judging End</label>
                <input
                  v-model="form.judging_end_at"
                  type="datetime-local"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Winner Announcement Date *</label>
                <input
                  v-model="form.results_announcement_date"
                  type="datetime-local"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
                <p v-if="errors.results_announcement_date" class="mt-1 text-sm text-red-600">{{ errors.results_announcement_date[0] }}</p>
              </div>
            </div>
          </div>

          <!-- Competition Details -->
          <div class="bg-white rounded-lg shadow-card p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Competition Details</h2>

            <!-- Prize Pool Section -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-3">Total Prize Pool *</label>
              
              <!-- Input & Button Row -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                <!-- Prize Pool Input -->
                <div class="md:col-span-2">
                  <div class="flex gap-2">
                    <input
                      v-model.number="form.total_prize_pool"
                      type="number"
                      min="0"
                      required
                      :class="[
                        'flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent transition',
                        errors.total_prize_pool
                          ? 'border-red-300 bg-red-50'
                          : 'border-gray-300 bg-white'
                      ]"
                    />
                    <button
                      v-if="cashPrizeTotal > 0"
                      type="button"
                      @click="form.total_prize_pool = cashPrizeTotal"
                      title="Sum all cash prizes below and set total prize pool"
                      class="px-4 py-2 bg-burgundy text-white text-sm rounded-lg hover:bg-burgundy-dark transition-all font-medium whitespace-nowrap flex items-center gap-2"
                    >
                      <span>⚡</span>
                      <span>Calculate</span>
                    </button>
                  </div>
                </div>

                <!-- Cash Total Info Card -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex flex-col justify-center">
                  <div class="text-xs text-blue-600 font-medium mb-1">Cash Prizes Total</div>
                  <div class="text-2xl font-bold text-blue-900">
                    ৳ {{ formatCurrency(cashPrizeTotal) }}
                  </div>
                </div>
              </div>

              <!-- Match Status Badge -->
              <div v-if="form.total_prize_pool > 0 || errors.total_prize_pool" class="mb-3">
                <div v-if="form.total_prize_pool === cashPrizeTotal" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-50 border border-emerald-300 rounded-lg">
                  <span class="text-emerald-700 text-sm font-medium">✅ Total matches cash prizes</span>
                </div>
                <div v-else-if="errors.total_prize_pool" class="inline-flex items-center gap-2 px-3 py-2 bg-red-50 border border-red-300 rounded-lg">
                  <span class="text-red-700 text-sm font-medium">⚠️ Mismatch - Check prizes</span>
                </div>
              </div>

              <!-- Error Message -->
              <p v-if="errors.total_prize_pool" class="mt-2 text-sm text-red-600 font-medium">
                {{ errors.total_prize_pool }}
              </p>
              <p v-else class="mt-2 text-xs text-gray-500">
                Enter the total prize pool amount. Use Calculate to auto-fill from cash prizes below.
              </p>
            </div>

            <!-- Prizes Section -->
            <div class="md:col-span-2">
              <div class="flex items-center justify-between mb-4">
                <label class="block text-sm font-medium text-gray-700">Prizes</label>
                <button
                  type="button"
                  @click="addPrize"
                  class="px-3 py-1 bg-burgundy text-white text-sm rounded-lg hover:bg-burgundy-dark transition-all font-medium"
                >
                  + Add Prize
                </button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                  v-for="(prize, index) in form.prizes"
                  :key="index"
                  class="border border-gray-200 rounded-xl p-4 bg-white shadow-sm"
                >
                  <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                      <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-700">🏆</span>
                      <span class="font-semibold text-gray-900">{{ formatPrizeLabel(prize) }}</span>
                    </div>
                    <button
                      type="button"
                      @click="removePrize(index)"
                      class="text-sm text-red-600 hover:text-red-700"
                    >
                      Remove
                    </button>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-medium text-gray-600 mb-1">Rank</label>
                      <select
                        v-model="prize.rank"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent text-sm"
                      >
                        <option v-for="rank in prizeRanks" :key="rank" :value="rank">{{ rank }}</option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-gray-600 mb-1">Reward Type</label>
                      <select
                        v-model="prize.type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent text-sm"
                      >
                        <option v-for="type in prizeTypes" :key="type" :value="type">{{ type }}</option>
                      </select>
                    </div>
                    <div class="md:col-span-2">
                      <label class="block text-xs font-medium text-gray-600 mb-1">Amount (must be > 1)</label>
                      <input
                        v-model.number="prize.amount"
                        type="number"
                        min="2"
                        step="1"
                        :disabled="prize.type !== 'Cash'"
                        placeholder="Cash amount"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent text-sm"
                      />
                    </div>
                    <div class="md:col-span-2" v-if="prize.type !== 'Cash'">
                      <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                      <input
                        v-model="prize.description"
                        type="text"
                        placeholder="e.g., Certificate, Gift Box, Trophy"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent text-sm"
                      />
                    </div>
                  </div>
                </div>

                <div v-if="form.prizes.length === 0" class="text-sm text-gray-500 text-center py-4 md:col-span-2">
                  No prizes added yet. Click "Add Prize" to create prize entries.
                </div>
              </div>

              <p v-if="errors.prizes" class="mt-2 text-sm text-red-600">{{ errors.prizes }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Max Submissions Per User</label>
                <input
                  v-model.number="form.max_submissions_per_user"
                  type="number"
                  min="1"
                  max="10"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
                <p v-if="errors.max_submissions_per_user" class="mt-1 text-sm text-red-600">{{ errors.max_submissions_per_user }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Winners *</label>
                <input
                  v-model.number="form.number_of_winners"
                  type="number"
                  min="1"
                  max="10"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
                <p v-if="errors.number_of_winners" class="mt-1 text-sm text-red-600">{{ errors.number_of_winners }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mt-4">
              <div class="md:col-span-2">
                <div class="flex items-center justify-between mb-2">
                  <label class="block text-sm font-medium text-gray-700">Rules</label>
                  <button
                    type="button"
                    @click="form.rules = defaultRules"
                    title="Fill with universal competition rules"
                    class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded hover:bg-purple-200 transition"
                  >
                    Use Default
                  </button>
                </div>
                <textarea
                  v-model="form.rules"
                  rows="5"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                ></textarea>
                <p class="mt-1 text-xs text-gray-500">Tip: Click "Use Default" to populate universal rules, then customize as needed.</p>
                <p v-if="errors.rules" class="mt-1 text-sm text-red-600">{{ errors.rules }}</p>
              </div>

              <div class="md:col-span-2">
                <div class="flex items-center justify-between mb-2">
                  <label class="block text-sm font-medium text-gray-700">Terms & Conditions</label>
                  <button
                    type="button"
                    @click="form.terms_and_conditions = defaultTerms"
                    title="Fill with universal terms and conditions"
                    class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded hover:bg-purple-200 transition"
                  >
                    Use Default
                  </button>
                </div>
                <textarea
                  v-model="form.terms_and_conditions"
                  rows="5"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                ></textarea>
                <p class="mt-1 text-xs text-gray-500">Tip: Click "Use Default" to populate standard terms, then customize as needed.</p>
                <p v-if="errors.terms_and_conditions" class="mt-1 text-sm text-red-600">{{ errors.terms_and_conditions[0] }}</p>
              </div>
            </div>
          </div>

          <!-- Sponsors Section -->
          <div class="bg-white rounded-lg shadow-card p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Sponsors</h2>

            <div class="space-y-3">
              <input
                v-model="sponsorSearch"
                type="text"
                placeholder="Search sponsors..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />

              <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-3 space-y-2">
                <div v-if="filteredSponsors.length === 0" class="text-sm text-gray-500">
                  No active sponsors found.
                </div>
                <label
                  v-for="sponsor in filteredSponsors"
                  :key="sponsor.id"
                  class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50"
                >
                  <input
                    type="checkbox"
                    :value="sponsor.id"
                    v-model="form.sponsor_ids"
                    class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
                  />
                  <img
                    v-if="sponsor.logo"
                    :src="sponsor.logo"
                    :alt="sponsor.name"
                    class="w-8 h-8 rounded object-cover border border-gray-200"
                  />
                  <div>
                    <div class="font-medium text-gray-900">{{ sponsor.name }}</div>
                    <div class="text-xs text-gray-500">{{ sponsor.website || 'No website' }}</div>
                  </div>
                </label>
              </div>
              <p class="text-sm text-gray-500">Selected: {{ form.sponsor_ids.length }}</p>
              <p v-if="errors.sponsor_ids" class="mt-1 text-sm text-red-600">{{ errors.sponsor_ids[0] }}</p>
            </div>
          </div>

          <!-- Judges Section -->
          <div class="bg-white rounded-lg shadow-card p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Judges</h2>

            <div class="space-y-3">
              <input
                v-model="judgeSearch"
                type="text"
                placeholder="Search judges..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />

              <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-3 space-y-2">
                <div v-if="filteredJudges.length === 0" class="text-sm text-gray-500">
                  No active judges found.
                </div>
                <label
                  v-for="judge in filteredJudges"
                  :key="judge.id"
                  class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50"
                >
                  <input
                    type="checkbox"
                    :value="judge.user_id || judge.id"
                    v-model="form.judge_ids"
                    class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
                  />
                  <div>
                    <div class="font-medium text-gray-900">{{ judge.name }}</div>
                    <div class="text-xs text-gray-500">{{ judge.email || 'No email' }}</div>
                  </div>
                </label>
              </div>
              <p class="text-sm text-gray-500">Selected: {{ form.judge_ids.length }}</p>
              <p v-if="errors.judge_ids" class="mt-1 text-sm text-red-600">{{ errors.judge_ids[0] }}</p>
            </div>
          </div>

          <!-- Status & Settings -->
          <div class="bg-white rounded-lg shadow-card p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Status & Settings</h2>

            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                <select
                  v-model="form.status"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                  <option value="draft">Draft</option>
                  <option value="active">Active</option>
                  <option value="judging">Judging</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                  <option value="archived">Archived</option>
                </select>
                <p v-if="errors.status" class="mt-1 text-sm text-red-600">{{ errors.status }}</p>
              </div>

              <div class="flex items-center">
                <input
                  v-model="form.is_featured"
                  type="checkbox"
                  class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
                />
                <label class="ml-2 block text-sm text-gray-900">
                  Featured Competition (Show prominently on homepage)
                </label>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="flex items-center">
                    <input
                      v-model="form.is_paid_competition"
                      type="checkbox"
                      class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
                    />
                    <span class="ml-2 text-sm text-gray-900">Paid Competition</span>
                  </label>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Participation Fee</label>
                  <input
                    v-model.number="form.participation_fee"
                    type="number"
                    min="0"
                    :disabled="!form.is_paid_competition"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                  />
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="flex items-center">
                  <input
                    v-model="form.allow_public_voting"
                    type="checkbox"
                    class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-900">Allow Public Voting</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="form.allow_judge_scoring"
                    type="checkbox"
                    class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-900">Allow Judge Scoring</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="form.allow_watermark"
                    type="checkbox"
                    class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-900">Allow Watermark</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="form.require_watermark"
                    type="checkbox"
                    class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-900">Require Watermark</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="form.is_public"
                    type="checkbox"
                    class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-900">Public Competition</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Submit Buttons -->
          <div class="flex items-center justify-end gap-4">
            <a
              href="/admin/competitions"
              class="px-6 py-3 bg-white text-burgundy border-2 border-burgundy rounded-lg font-medium hover:bg-primary-50 transition-all"
            >
              Cancel
            </a>
            <button
              type="submit"
              :disabled="processing"
              class="px-6 py-3 bg-burgundy text-white rounded-lg font-medium hover:bg-burgundy-dark transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ processing ? 'Updating...' : 'Update Competition' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Toast Notification -->
      <div v-if="showToast" :class="['toast', toastType]">{{ toastMessage }}</div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'

export default {
  components: {
    AdminHeader,
    AdminQuickNav
  },

  data() {
    return {
      processing: false,
      errors: {},
      categories: [],
      availableSponsors: [],
      availableJudges: [],
      sponsorSearch: '',
      judgeSearch: '',
      showToast: false,
      toastMessage: '',
      toastType: 'success',
      form: {
        title: '',
        slug: '',
        theme: '',
        description: '',
        category_id: null,
        submission_deadline: '',
        voting_start_at: '',
        voting_end_at: '',
        judging_start_at: '',
        judging_end_at: '',
        results_announcement_date: '',
        hero_image: '',
        banner_image: '',
        total_prize_pool: 2,
        prizes: [],
        max_submissions_per_user: 3,
        rules: '',
        terms_and_conditions: '',
        status: 'draft',
        is_public: true,
        is_featured: false,
        number_of_winners: 1,
        is_paid_competition: false,
        participation_fee: 0,
        allow_public_voting: true,
        allow_judge_scoring: true,
        allow_watermark: false,
        require_watermark: false,
        sponsor_ids: [],
        judge_ids: [],
      },
      prizeRanks: ['1st', '2nd', '3rd', '4th', '5th', '6th', 'Grand Prize', 'Honorable Mention'],
      prizeTypes: ['Cash', 'Certificate', 'Gift Box', 'Trophy', 'Other'],
      defaultRules: `1. Eligibility
- Participants must be professional photographers or photography enthusiasts
- Participants must be 18 years or older
- Employees and family members of the organizing committee are not eligible

2. Submission Requirements
- All submissions must be original works by the participant
- Each submission must include a title and brief description
- Photos must be in JPEG or PNG format, minimum 2000x2000px resolution
- Maximum file size: 50 MB per image

3. Content Guidelines
- Submissions must not contain watermarks or signatures
- No explicitly offensive, discriminatory, or inappropriate content
- All content must comply with local and international copyright laws
- No digitally manipulated images (minor editing allowed)

4. Ownership & Usage Rights
- Participants retain full copyright of their work
- By submitting, you grant us permission to display entries during judging
- Winning entries may be used for promotional purposes with proper attribution
- Organizers are not responsible for any unsolicited or unauthorized use

5. Disqualification
- Plagiarized or previously published work
- Entries that violate copyright or intellectual property rights
- Inappropriate or offensive content
- Submissions exceeding the deadline
- Multiple entries from same participant exceeding the limit

6. Judging & Results
- Winners will be selected by a panel of professional judges
- Judging criteria: Composition, Creativity, Technical Quality, and Theme Relevance
- Results will be announced on the specified date
- Winners will be notified via email

7. Prize Claim
- Winners must claim their prizes within 30 days of announcement
- Prize transfer or cash equivalents are not available
- Prizes are non-transferable and non-refundable`,
      defaultTerms: `1. Agreement to Terms
By participating in this competition, you agree to comply with all rules, regulations, and terms outlined herein.

2. Participant Responsibilities
- You are responsible for obtaining all necessary permissions and rights to submit your work
- You confirm that your submission is original and does not infringe on any third-party rights
- You agree to use the Photographer SB platform in good faith and not engage in fraudulent activities

3. Intellectual Property Rights
- Participants retain all copyright and intellectual property rights to their submissions
- By submitting to the competition, you grant Photographer SB a non-exclusive license to use, display, and reproduce your work for competition purposes
- You consent to the use of your name and work in connection with competition announcements and promotional materials

4. Limitation of Liability
- Photographer SB is not responsible for lost, damaged, or corrupted submissions
- We are not liable for any technical issues, server errors, or connectivity problems
- Participants submit at their own risk

5. Privacy & Data Protection
- Your personal information will be used only for competition purposes
- We comply with all applicable data protection regulations
- Your data will not be shared with third parties without consent

6. Disqualification & Removal
- Photographer SB reserves the right to disqualify any participant or submission that violates these terms
- We may remove offensive or inappropriate content at our discretion
- Disqualified participants forfeit any prizes and may be banned from future competitions

7. Modification of Terms
- Photographer SB reserves the right to modify these terms and conditions at any time
- Continued participation implies acceptance of modified terms

8. Dispute Resolution
- Any disputes arising from this competition will be governed by local laws
- Participants agree to attempt resolution through our support team first
- In case of unresolved disputes, mediation or arbitration may be required

9. Entire Agreement
- These terms and conditions constitute the entire agreement between you and Photographer SB
- If any provision is found invalid, remaining provisions remain in effect

10. Contact Information
For questions or concerns regarding the competition, please contact our support team at support@photographersb.com`,
    };
  },

  computed: {
    filteredSponsors() {
      const term = (this.sponsorSearch || '').toLowerCase();
      if (!term) return this.availableSponsors;
      return this.availableSponsors.filter(s =>
        (s.name || '').toLowerCase().includes(term) ||
        (s.website || '').toLowerCase().includes(term)
      );
    },
    filteredJudges() {
      const term = (this.judgeSearch || '').toLowerCase();
      if (!term) return this.availableJudges;
      return this.availableJudges.filter(j =>
        (j.name || '').toLowerCase().includes(term) ||
        (j.email || '').toLowerCase().includes(term)
      );
    },
    cashPrizeTotal() {
      return this.form.prizes
        .filter(prize => prize.type === 'Cash')
        .reduce((sum, prize) => sum + (Number(prize.amount) || 0), 0);
    }
  },

  mounted() {
    this.fetchCategories();
    this.fetchAvailableSponsors();
    this.fetchAvailableJudges();
    this.fetchCompetition();
  },

  methods: {
    async fetchCompetition() {
      try {
        const token = localStorage.getItem('auth_token');
        const competitionId = this.$route.params.id;

        const headers = {
          'Accept': 'application/json'
        };
        if (token) {
          headers.Authorization = `Bearer ${token}`;
        }

        const response = await axios.get(`/admin/competitions/${competitionId}`, {
          headers,
          validateStatus: (status) => status < 500
        });

        console.log('Competition response:', response.data);

        if (response.status === 401 || response.status === 403) {
          this.showToastMessage('Unauthorized. Please log in again.', 'error');
          return;
        }

        if (response.status === 404) {
          this.showToastMessage('Competition not found.', 'error');
          return;
        }

        if (response.data.status === 'success' && response.data.data) {
          const comp = response.data.data;
          const prizes = Array.isArray(comp.prizes)
            ? comp.prizes.map(prize => ({
                rank: prize.rank || prize.position || '',
                type: prize.type || 'Cash',
                amount: prize.cash_amount || prize.amount || 0,
                description: prize.description || ''
              }))
            : (comp.prizes ? JSON.parse(comp.prizes) : []);

          this.form = {
            title: comp.title || '',
            slug: comp.slug || '',
            theme: comp.theme || '',
            description: comp.description || '',
            category_id: comp.category_id || null,
            submission_deadline: this.formatDateForInput(comp.submission_deadline),
            voting_start_at: this.formatDateForInput(comp.voting_start_at),
            voting_end_at: this.formatDateForInput(comp.voting_end_at),
            judging_start_at: this.formatDateForInput(comp.judging_start_at),
            judging_end_at: this.formatDateForInput(comp.judging_end_at),
            results_announcement_date: this.formatDateForInput(comp.results_announcement_date),
            hero_image: comp.hero_image || '',
            banner_image: comp.banner_image || '',
            total_prize_pool: Number(comp.total_prize_pool || 0),
            prizes,
            max_submissions_per_user: comp.max_submissions_per_user || 3,
            number_of_winners: comp.number_of_winners || 1,
            is_paid_competition: !!comp.is_paid_competition,
            participation_fee: Number(comp.participation_fee || 0),
            allow_public_voting: comp.allow_public_voting ?? true,
            allow_judge_scoring: comp.allow_judge_scoring ?? true,
            allow_watermark: !!comp.allow_watermark,
            require_watermark: !!comp.require_watermark,
            is_public: comp.is_public ?? true,
            rules: comp.rules || '',
            terms_and_conditions: comp.terms_and_conditions || '',
            status: comp.status || 'draft',
            is_featured: !!comp.is_featured,
            sponsor_ids: (comp.sponsorRecords || []).map(s => s.id),
            judge_ids: (comp.judges || []).map(j => j.user_id || j.id),
          };
        } else {
          console.error('Invalid response structure:', response.data);
          this.showToastMessage('Failed to load competition details', 'error');
        }
      } catch (error) {
        console.error('Error loading competition:', error);
        if (error.response) {
          console.error('Response status:', error.response.status);
          console.error('Response data:', error.response.data);
        }
        this.showToastMessage('Failed to load competition details', 'error');
      }
    },

    async fetchCategories() {
      try {
        const response = await axios.get('/categories');
        if (response.data.status === 'success') {
          this.categories = response.data.data;
        }
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    },

    async fetchAvailableSponsors() {
      try {
        const token = localStorage.getItem('auth_token');
        const headers = {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        };
        if (token) {
          headers.Authorization = `Bearer ${token}`;
        }
        const response = await axios.get('/admin/platform-sponsors', {
          headers,
          validateStatus: (status) => status < 500
        });
        console.log('Sponsors response:', response.data);
        if (response.status === 401 || response.status === 403) {
          console.error('Authentication failed for sponsors');
          this.showToastMessage('Authentication failed. Please log in again.', 'error');
          return;
        }
        if (response.data && response.data.data) {
          this.availableSponsors = response.data.data.filter(s => s.status === 'active');
          console.log('Available sponsors:', this.availableSponsors);
        }
      } catch (error) {
        console.error('Error fetching sponsors:', error);
        if (error.response) {
          console.error('Response status:', error.response.status);
          console.error('Response data:', error.response.data);
        }
        this.showToastMessage('Failed to load sponsors', 'error');
      }
    },

    async fetchAvailableJudges() {
      try {
        const token = localStorage.getItem('auth_token');
        const headers = {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        };
        if (token) {
          headers.Authorization = `Bearer ${token}`;
        }

        const [judgeProfilesResponse, judgeUsersResponse] = await Promise.all([
          axios.get('/admin/judges?status=active&per_page=200', {
            headers,
            validateStatus: (status) => status < 500
          }),
          axios.get('/admin/users?role=judge', {
            headers,
            validateStatus: (status) => status < 500
          })
        ]);

        console.log('Judge profiles response:', judgeProfilesResponse.data);
        console.log('Judge users response:', judgeUsersResponse.data);

        if (judgeProfilesResponse.status === 401 || judgeUsersResponse.status === 401) {
          console.error('Authentication failed for judges');
          this.showToastMessage('Authentication failed. Please log in again.', 'error');
          return;
        }

        const judgeProfiles = judgeProfilesResponse?.data?.data?.data || [];
        const judgeUsers = judgeUsersResponse?.data?.data || [];

        const options = [];
        const seenUserIds = new Set();

        judgeProfiles.forEach(profile => {
          if (!profile.user_id) return;
          if (seenUserIds.has(profile.user_id)) return;
          options.push({
            id: profile.user_id,
            name: profile.name || profile.user?.name,
            email: profile.email || profile.user?.email,
            source: 'profile',
            profile_id: profile.id,
          });
          seenUserIds.add(profile.user_id);
        });

        judgeUsers.forEach(user => {
          if (seenUserIds.has(user.id)) return;
          options.push({
            id: user.id,
            name: user.name,
            email: user.email,
            source: 'user',
          });
          seenUserIds.add(user.id);
        });

        this.availableJudges = options;
        console.log('Available judges:', this.availableJudges);
      } catch (error) {
        console.error('Error fetching judges:', error);
        if (error.response) {
          console.error('Response status:', error.response.status);
          console.error('Response data:', error.response.data);
        }
        this.showToastMessage('Failed to load judges', 'error');
      }
    },

    async submitForm() {
      this.processing = true;
      this.errors = {};

      try {
        const token = localStorage.getItem('auth_token');
        const competitionId = this.$route.params.id;

        const invalidCashPrize = this.form.prizes.some(
          prize => prize.type === 'Cash' && Number(prize.amount) <= 1
        );
        const invalidNonCashPrize = this.form.prizes.some(
          prize => prize.type !== 'Cash' && !(prize.description || '').trim()
        );

        if (invalidCashPrize) {
          this.errors.prizes = 'Cash prize amount must be greater than 1.';
        }
        if (invalidNonCashPrize) {
          this.errors.prizes = 'Non-cash prizes require a description.';
        }
        if (Number(this.form.total_prize_pool) !== this.cashPrizeTotal) {
          this.errors.total_prize_pool = 'Total Prize Pool must equal the sum of cash prizes.';
        }

        if (Object.keys(this.errors).length > 0) {
          this.processing = false;
          this.showToastMessage('Please fix the errors in the form', 'error');
          return;
        }

        const formData = {
          title: this.form.title,
          slug: this.form.slug || null,
          theme: this.form.theme,
          description: this.form.description,
          category_id: this.form.category_id,
          submission_deadline: this.form.submission_deadline,
          voting_start_at: this.form.voting_start_at,
          voting_end_at: this.form.voting_end_at,
          judging_start_at: this.form.judging_start_at || null,
          judging_end_at: this.form.judging_end_at || null,
          results_announcement_date: this.form.results_announcement_date,
          hero_image: this.form.hero_image || null,
          banner_image: this.form.banner_image || null,
          prizes: this.form.prizes.map(prize => {
            const prizeData = {
              position: prize.rank,
              amount: prize.amount || 0
            };
            if (prize.description && prize.description.trim()) {
              prizeData.description = prize.description;
            }
            return prizeData;
          }),
          total_prize_pool: this.form.total_prize_pool,
          number_of_winners: this.form.number_of_winners,
          participation_fee: this.form.is_paid_competition ? this.form.participation_fee : 0,
          is_paid_competition: this.form.is_paid_competition,
          max_submissions_per_user: this.form.max_submissions_per_user || 3,
          allow_public_voting: this.form.allow_public_voting,
          allow_judge_scoring: this.form.allow_judge_scoring,
          allow_watermark: this.form.allow_watermark,
          require_watermark: this.form.require_watermark,
          is_public: this.form.is_public,
          rules: this.form.rules,
          terms_and_conditions: this.form.terms_and_conditions,
          status: this.form.status,
          is_featured: this.form.is_featured ? 1 : 0,
          sponsor_ids: this.form.sponsor_ids,
          judge_ids: this.form.judge_ids,
        };

        const headers = {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        };
        if (token) {
          headers.Authorization = `Bearer ${token}`;
        }

        const response = await axios.put(`/admin/competitions/${competitionId}`, formData, {
          headers
        });

        if (response.data.status === 'success') {
          this.showToastMessage('Competition updated successfully!', 'success');
          setTimeout(() => {
            window.location.href = '/admin/competitions';
          }, 1500);
        }
      } catch (error) {
        console.error('Error updating competition:', error);
        if (error.response && error.response.data) {
          // Handle authorization errors specifically
          if (error.response.status === 401) {
            this.errors.auth = 'Your session has expired. Please log in again.';
            setTimeout(() => window.location.href = '/login', 2000);
          } else if (error.response.status === 403) {
            this.errors.auth = `Access denied. Your role (${error.response.data.user_role}) does not have permission to edit competitions. Admin/Super Admin access required.`;
          } else if (error.response.data.errors) {
            this.errors = error.response.data.errors;
          }
          this.showToastMessage(error.response.data.message || 'Error updating competition', 'error');
        } else {
          this.showToastMessage('Error updating competition. Please try again.', 'error');
        }
      } finally {
        this.processing = false;
      }
    },

    showToastMessage(message, type = 'success') {
      this.toastMessage = message;
      this.toastType = type;
      this.showToast = true;
      setTimeout(() => {
        this.showToast = false;
      }, 3000);
    },

    addPrize() {
      this.form.prizes.push({
        rank: '1st',
        type: 'Cash',
        amount: 2,
        description: ''
      });
    },

    removePrize(index) {
      this.form.prizes.splice(index, 1);
    },

    formatPrizeLabel(prize) {
      const rank = prize.rank || 'Prize';
      const type = prize.type || 'Reward';
      return `${rank} Place - ${type}`;
    },

    formatDateForInput(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');
      return `${year}-${month}-${day}T${hours}:${minutes}`;
    },

    formatCurrency(value) {
      const num = parseFloat(value) || 0;
      return num.toLocaleString('bn-BD', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      });
    }
  },
};
</script>

<style scoped>
.toast {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  padding: 1rem 1.5rem;
  border-radius: 0.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  animation: slideIn 0.3s ease-out;
  z-index: 1000;
  font-weight: 600;
}

.toast.success {
  background: #16a34a;
  color: white;
}

.toast.error {
  background: #dc2626;
  color: white;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}
</style>
