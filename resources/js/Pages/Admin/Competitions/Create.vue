<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader
      title="Create Competition"
      subtitle="Set up a new photography competition"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <!-- Authorization Error Alert -->
      <div
        v-if="errors.auth"
        class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8"
      >
        <div class="bg-red-50 border border-red-300 rounded-lg p-4">
          <div class="flex items-start gap-3">
            <div class="text-red-600 text-lg font-bold">
              ⚠️
            </div>
            <div>
              <h3 class="text-red-900 font-semibold">
                Authorization Error
              </h3>
              <p class="text-red-700 text-sm mt-1">
                {{ errors.auth }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Form -->
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form
          class="space-y-6"
          @submit.prevent="submitForm"
        >
          <!-- Basic Information -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
              Basic Information
            </h2>

            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                <input
                  v-model="form.title"
                  type="text"
                  placeholder="e.g., Spring Photography Contest 2026"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                <span
                  v-if="errors.title"
                  class="text-red-600 text-sm"
                >{{ errors.title }}</span>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                <input
                  v-model="form.slug"
                  type="text"
                  placeholder="Auto-generated if left blank"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                <span
                  v-if="errors.slug"
                  class="text-red-600 text-sm"
                >{{ errors.slug }}</span>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Theme *</label>
                <input
                  v-model="form.theme"
                  type="text"
                  placeholder="e.g., Nature & Wildlife"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                <span
                  v-if="errors.theme"
                  class="text-red-600 text-sm"
                >{{ errors.theme }}</span>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Competition Mode *</label>
                <select
                  v-model="form.mode"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                  <option value="open">
                    Open Contest
                  </option>
                  <option value="pro">
                    Pro Awards
                  </option>
                  <option value="student">
                    Student Challenge
                  </option>
                  <option value="district_battle">
                    District Pride Battle
                  </option>
                </select>
                <span
                  v-if="errors.mode"
                  class="text-red-600 text-sm"
                >{{ errors.mode }}</span>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select
                  v-model="form.category_id"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                  <option value="">
                    -- Select Category --
                  </option>
                  <option
                    v-for="cat in categories"
                    :key="cat.id"
                    :value="cat.id"
                  >
                    {{ cat.name }}
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea
                  v-model="form.description"
                  placeholder="Tell photographers about this competition..."
                  rows="4"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hero Image URL</label>
                <input
                  v-model="form.hero_image"
                  type="url"
                  placeholder="https://..."
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                <input
                  type="file"
                  accept="image/*"
                  class="upload-input mt-2 block text-sm"
                  @change="handleImageUpload('hero_image', $event)"
                >
                <div class="mt-2 flex flex-wrap gap-2">
                  <button
                    type="button"
                    class="rounded-full border border-burgundy px-4 py-1 text-xs font-semibold text-burgundy hover:bg-burgundy hover:text-white"
                    @click="openPexelsPicker('hero_image', 1600, 900)"
                  >
                    Choose from Pexels
                  </button>
                </div>
                <p class="mt-1 upload-hint">Max 5 MB. JPG/PNG. 1600x900 px.</p>
                <p
                  v-if="uploadingImages.hero_image"
                  class="mt-1 text-xs text-gray-500"
                >
                  Uploading...
                </p>
                <p
                  v-if="form.hero_image_credit_name"
                  class="mt-1 text-xs text-gray-500"
                >
                  Pexels credit:
                  <a
                    :href="form.hero_image_credit_url || 'https://www.pexels.com'"
                    target="_blank"
                    rel="noopener"
                    class="font-semibold text-burgundy underline"
                  >
                    {{ form.hero_image_credit_name }}
                  </a>
                </p>
                <span
                  v-if="errors.hero_image"
                  class="text-red-600 text-sm"
                >{{ errors.hero_image }}</span>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image URL</label>
                <input
                  v-model="form.cover_image"
                  type="url"
                  placeholder="https://..."
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                <input
                  type="file"
                  accept="image/*"
                  class="upload-input mt-2 block text-sm"
                  @change="handleImageUpload('cover_image', $event)"
                >
                <div class="mt-2 flex flex-wrap gap-2">
                  <button
                    type="button"
                    class="rounded-full border border-burgundy px-4 py-1 text-xs font-semibold text-burgundy hover:bg-burgundy hover:text-white"
                    @click="openPexelsPicker('cover_image', 1200, 1200)"
                  >
                    Choose from Pexels
                  </button>
                </div>
                <p class="mt-1 upload-hint">Max 5 MB. JPG/PNG. 1200x1200 px.</p>
                <p
                  v-if="uploadingImages.cover_image"
                  class="mt-1 text-xs text-gray-500"
                >
                  Uploading...
                </p>
                <p
                  v-if="form.cover_image_credit_name"
                  class="mt-1 text-xs text-gray-500"
                >
                  Pexels credit:
                  <a
                    :href="form.cover_image_credit_url || 'https://www.pexels.com'"
                    target="_blank"
                    rel="noopener"
                    class="font-semibold text-burgundy underline"
                  >
                    {{ form.cover_image_credit_name }}
                  </a>
                </p>
                <span
                  v-if="errors.cover_image"
                  class="text-red-600 text-sm"
                >{{ errors.cover_image }}</span>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Banner Image URL</label>
                <input
                  v-model="form.banner_image"
                  type="url"
                  placeholder="https://..."
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                <input
                  type="file"
                  accept="image/*"
                  class="upload-input mt-2 block text-sm"
                  @change="handleImageUpload('banner_image', $event)"
                >
                <div class="mt-2 flex flex-wrap gap-2">
                  <button
                    type="button"
                    class="rounded-full border border-burgundy px-4 py-1 text-xs font-semibold text-burgundy hover:bg-burgundy hover:text-white"
                    @click="openPexelsPicker('banner_image', 1920, 600)"
                  >
                    Choose from Pexels
                  </button>
                </div>
                <p class="mt-1 upload-hint">Max 5 MB. JPG/PNG. 1920x600 px.</p>
                <p
                  v-if="uploadingImages.banner_image"
                  class="mt-1 text-xs text-gray-500"
                >
                  Uploading...
                </p>
                <p
                  v-if="form.banner_image_credit_name"
                  class="mt-1 text-xs text-gray-500"
                >
                  Pexels credit:
                  <a
                    :href="form.banner_image_credit_url || 'https://www.pexels.com'"
                    target="_blank"
                    rel="noopener"
                    class="font-semibold text-burgundy underline"
                  >
                    {{ form.banner_image_credit_name }}
                  </a>
                </p>
                <span
                  v-if="errors.banner_image"
                  class="text-red-600 text-sm"
                >{{ errors.banner_image }}</span>
              </div>
            </div>
          </div>

          <!-- Timeline -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
              Competition Timeline
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Competition Start</label>
                <input
                  v-model="form.start_date"
                  type="text"
                  inputmode="numeric"
                  placeholder="dd-mm-yyyy"
                  pattern="\d{2}-\d{2}-\d{4}"
                  class="js-date w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                <p
                  v-if="errors.start_date"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ Array.isArray(errors.start_date) ? errors.start_date[0] : errors.start_date }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Submission Closes *</label>
                <input
                  v-model="form.submission_deadline"
                  type="text"
                  inputmode="numeric"
                  placeholder="dd-mm-yyyy hh:mm"
                  pattern="\d{2}-\d{2}-\d{4} \d{2}:\d{2}"
                  required
                  class="js-datetime w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                <p
                  v-if="errors.submission_deadline"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ Array.isArray(errors.submission_deadline) ? errors.submission_deadline[0] : errors.submission_deadline }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Competition End</label>
                <input
                  v-model="form.end_date"
                  type="text"
                  inputmode="numeric"
                  placeholder="dd-mm-yyyy"
                  pattern="\d{2}-\d{2}-\d{4}"
                  class="js-date w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                <p
                  v-if="errors.end_date"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ Array.isArray(errors.end_date) ? errors.end_date[0] : errors.end_date }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Voting Opens *</label>
                <input
                  v-model="form.voting_start_at"
                  type="text"
                  inputmode="numeric"
                  placeholder="dd-mm-yyyy hh:mm"
                  pattern="\d{2}-\d{2}-\d{4} \d{2}:\d{2}"
                  required
                  class="js-datetime w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                <p
                  v-if="errors.voting_start_at"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ Array.isArray(errors.voting_start_at) ? errors.voting_start_at[0] : errors.voting_start_at }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Voting Closes *</label>
                <input
                  v-model="form.voting_end_at"
                  type="text"
                  inputmode="numeric"
                  placeholder="dd-mm-yyyy hh:mm"
                  pattern="\d{2}-\d{2}-\d{4} \d{2}:\d{2}"
                  required
                  class="js-datetime w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                <p
                  v-if="errors.voting_end_at"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ Array.isArray(errors.voting_end_at) ? errors.voting_end_at[0] : errors.voting_end_at }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Public Voting Opens</label>
                <input
                  v-model="form.voting_start_date"
                  type="text"
                  inputmode="numeric"
                  placeholder="dd-mm-yyyy hh:mm"
                  pattern="\d{2}-\d{2}-\d{4} \d{2}:\d{2}"
                  class="js-datetime w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                <p
                  v-if="errors.voting_start_date"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ Array.isArray(errors.voting_start_date) ? errors.voting_start_date[0] : errors.voting_start_date }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Public Voting Closes</label>
                <input
                  v-model="form.voting_end_date"
                  type="text"
                  inputmode="numeric"
                  placeholder="dd-mm-yyyy hh:mm"
                  pattern="\d{2}-\d{2}-\d{4} \d{2}:\d{2}"
                  class="js-datetime w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                <p
                  v-if="errors.voting_end_date"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ Array.isArray(errors.voting_end_date) ? errors.voting_end_date[0] : errors.voting_end_date }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Judging Opens</label>
                <input
                  v-model="form.judging_start_at"
                  type="text"
                  inputmode="numeric"
                  placeholder="dd-mm-yyyy hh:mm"
                  pattern="\d{2}-\d{2}-\d{4} \d{2}:\d{2}"
                  class="js-datetime w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                <p
                  v-if="errors.judging_start_at"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ Array.isArray(errors.judging_start_at) ? errors.judging_start_at[0] : errors.judging_start_at }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Judging Closes</label>
                <input
                  v-model="form.judging_end_at"
                  type="text"
                  inputmode="numeric"
                  placeholder="dd-mm-yyyy hh:mm"
                  pattern="\d{2}-\d{2}-\d{4} \d{2}:\d{2}"
                  class="js-datetime w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                <p
                  v-if="errors.judging_end_at"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ Array.isArray(errors.judging_end_at) ? errors.judging_end_at[0] : errors.judging_end_at }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Judging Deadline</label>
                <input
                  v-model="form.judging_deadline"
                  type="text"
                  inputmode="numeric"
                  placeholder="dd-mm-yyyy"
                  pattern="\d{2}-\d{2}-\d{4}"
                  class="js-date w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                <p
                  v-if="errors.judging_deadline"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ Array.isArray(errors.judging_deadline) ? errors.judging_deadline[0] : errors.judging_deadline }}
                </p>
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Results Announced *</label>
                <input
                  v-model="form.results_announcement_date"
                  type="text"
                  inputmode="numeric"
                  placeholder="dd-mm-yyyy hh:mm"
                  pattern="\d{2}-\d{2}-\d{4} \d{2}:\d{2}"
                  required
                  class="js-datetime w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                <p
                  v-if="errors.results_announcement_date"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ Array.isArray(errors.results_announcement_date) ? errors.results_announcement_date[0] : errors.results_announcement_date }}
                </p>
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Public Announcement Date</label>
                <input
                  v-model="form.announcement_date"
                  type="text"
                  inputmode="numeric"
                  placeholder="dd-mm-yyyy"
                  pattern="\d{2}-\d{2}-\d{4}"
                  class="js-date w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                <p
                  v-if="errors.announcement_date"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ Array.isArray(errors.announcement_date) ? errors.announcement_date[0] : errors.announcement_date }}
                </p>
              </div>
            </div>
          </div>

          <!-- Details -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
              Details
              <span class="text-xs text-gray-400 ml-2">(v2.0 - Updated Feb 2)</span>
            </h2>

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
                    >
                    <button
                      v-if="cashPrizeTotal > 0"
                      type="button"
                      title="Sum all cash prizes below and set total prize pool"
                      class="px-4 py-2 bg-burgundy text-white text-sm rounded-lg hover:bg-burgundy-dark transition-all font-medium whitespace-nowrap flex items-center gap-2"
                      @click="form.total_prize_pool = cashPrizeTotal"
                    >
                      <span>⚡</span>
                      <span>Calculate</span>
                    </button>
                  </div>
                </div>

                <!-- Cash Total Info Card -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex flex-col justify-center">
                  <div class="text-xs text-blue-600 font-medium mb-1">
                    Cash Prizes Total
                  </div>
                  <div class="text-2xl font-bold text-blue-900">
                    ৳ {{ formatCurrency(cashPrizeTotal) }}
                  </div>
                </div>
              </div>

              <!-- Match Status Badge -->
              <div
                v-if="form.total_prize_pool > 0 || errors.total_prize_pool"
                class="mb-3"
              >
                <div
                  v-if="form.total_prize_pool === cashPrizeTotal"
                  class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-50 border border-emerald-300 rounded-lg"
                >
                  <span class="text-emerald-700 text-sm font-medium">✅ Total matches cash prizes</span>
                </div>
                <div
                  v-else-if="errors.total_prize_pool"
                  class="inline-flex items-center gap-2 px-3 py-2 bg-red-50 border border-red-300 rounded-lg"
                >
                  <span class="text-red-700 text-sm font-medium">⚠️ Mismatch - Check prizes</span>
                </div>
              </div>

              <!-- Error Message -->
              <p
                v-if="errors.total_prize_pool"
                class="mt-2 text-sm text-red-600 font-medium"
              >
                {{ errors.total_prize_pool }}
              </p>
              <p
                v-else
                class="mt-2 text-xs text-gray-500"
              >
                Enter the total prize pool amount. Use Calculate to auto-fill from cash prizes below.
              </p>
            </div>

            <div class="mb-6">
              <div class="flex items-center justify-between mb-3">
                <label class="block text-sm font-medium text-gray-700">Prizes</label>
                <button
                  type="button"
                  class="px-3 py-1 bg-burgundy text-white text-sm rounded-lg hover:bg-burgundy-dark transition-all font-medium"
                  @click="addPrize"
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
                      <span
                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-700"
                      >
                        🏆
                      </span>
                      <span class="font-semibold text-gray-900">{{ formatPrizeLabel(prize) }}</span>
                    </div>
                    <button
                      type="button"
                      class="text-sm text-red-600 hover:text-red-700"
                      @click="removePrize(index)"
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
                        <option
                          v-for="rank in prizeRanks"
                          :key="rank"
                          :value="rank"
                        >
                          {{ rank }}
                        </option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-gray-600 mb-1">Reward Type</label>
                      <select
                        v-model="prize.type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent text-sm"
                      >
                        <option
                          v-for="type in prizeTypes"
                          :key="type"
                          :value="type"
                        >
                          {{ type }}
                        </option>
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
                      >
                    </div>
                    <div
                      v-if="prize.type !== 'Cash'"
                      class="md:col-span-2"
                    >
                      <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                      <input
                        v-model="prize.description"
                        type="text"
                        placeholder="e.g., Certificate, Gift Box, Trophy"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent text-sm"
                      >
                    </div>
                  </div>
                </div>

                <div
                  v-if="form.prizes.length === 0"
                  class="text-sm text-gray-500 text-center py-4 md:col-span-2"
                >
                  No prizes added yet. Click "Add Prize" to create prize entries.
                </div>
              </div>

              <p
                v-if="errors.prizes"
                class="mt-2 text-sm text-red-600"
              >
                {{ errors.prizes }}
              </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Winners *</label>
                <input
                  v-model.number="form.number_of_winners"
                  type="number"
                  min="1"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Max Submissions Per User</label>
                <input
                  v-model.number="form.max_submissions_per_user"
                  type="number"
                  min="1"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Entry Type</label>
                <select
                  v-model="form.entry_type"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                  <option value="single">
                    Single
                  </option>
                  <option value="series">
                    Series
                  </option>
                  <option value="both">
                    Both
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Series Min Images</label>
                <input
                  v-model.number="form.series_min_images"
                  type="number"
                  min="1"
                  :disabled="form.entry_type === 'single'"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Series Max Images</label>
                <input
                  v-model.number="form.series_max_images"
                  type="number"
                  min="1"
                  :disabled="form.entry_type === 'single'"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
              </div>
            </div>

            <div class="flex items-center mt-4">
              <input
                v-model="form.district_battle_enabled"
                type="checkbox"
                class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
              >
              <label class="ml-2 block text-sm text-gray-900">
                Enable District Pride Battle
              </label>
            </div>

            <div class="mt-4">
              <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-700">Rules</label>
                <button
                  type="button"
                  title="Fill with universal competition rules"
                  class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded hover:bg-purple-200 transition"
                  @click="form.rules = defaultRules"
                >
                  Use Default
                </button>
              </div>
              <textarea
                v-model="form.rules"
                placeholder="Enter competition rules..."
                rows="5"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p class="mt-1 text-xs text-gray-500">
                Tip: Click "Use Default" to populate universal rules, then customize as needed.
              </p>
            </div>

            <div class="mt-4">
              <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-700">Terms & Conditions</label>
                <button
                  type="button"
                  title="Fill with universal terms and conditions"
                  class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded hover:bg-purple-200 transition"
                  @click="form.terms_and_conditions = defaultTerms"
                >
                  Use Default
                </button>
              </div>
              <textarea
                v-model="form.terms_and_conditions"
                placeholder="Enter terms and conditions..."
                rows="5"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p class="mt-1 text-xs text-gray-500">
                Tip: Click "Use Default" to populate standard terms, then customize as needed.
              </p>
            </div>
          </div>

          <!-- Sponsors -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
              Sponsors
            </h2>

            <div class="mb-4">
              <input
                v-model="sponsorSearch"
                type="text"
                placeholder="Search sponsors..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              >
            </div>

            <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-4 space-y-3">
              <div
                v-if="filteredSponsors.length === 0"
                class="text-gray-500 text-center py-8"
              >
                No sponsors available
              </div>
              <label
                v-for="sponsor in filteredSponsors"
                :key="sponsor.id"
                class="flex items-center p-3 rounded-lg hover:bg-gray-50 cursor-pointer"
              >
                <input
                  v-model="form.sponsor_ids"
                  type="checkbox"
                  :value="sponsor.id"
                  class="h-4 w-4 rounded border-gray-300"
                >
                <span class="ml-3 font-medium">{{ sponsor.name }}</span>
              </label>
            </div>

            <p class="mt-2 text-sm text-gray-600">
              Selected: {{ form.sponsor_ids.length }}
            </p>
          </div>

          <!-- Judges -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
              Judges
            </h2>

            <div class="mb-4">
              <input
                v-model="judgeSearch"
                type="text"
                placeholder="Search judges..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              >
            </div>

            <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-4 space-y-3">
              <div
                v-if="filteredJudges.length === 0"
                class="text-gray-500 text-center py-8"
              >
                No judges available
              </div>
              <label
                v-for="judge in filteredJudges"
                :key="judge.id"
                class="flex items-center p-3 rounded-lg hover:bg-gray-50 cursor-pointer"
              >
                <input
                  v-model="form.judge_ids"
                  type="checkbox"
                  :value="judge.id"
                  class="h-4 w-4 rounded border-gray-300"
                >
                <span class="ml-3 font-medium">{{ judge.name }}</span>
              </label>
            </div>

            <p class="mt-2 text-sm text-gray-600">
              Selected: {{ form.judge_ids.length }}
            </p>
          </div>

          <!-- Entry Fees & Payment -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
              Entry Fees & Payment
            </h2>
            <p class="text-sm text-gray-600 mb-4">
              Set tiered entry fees (BDT) for different user types. Payments are manually verified (bKash/Nagad/Rocket/manual).
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div
                v-for="(fee, index) in form.entry_fees"
                :key="index"
                class="border border-gray-200 rounded-lg p-4"
              >
                <div class="text-sm font-medium text-gray-700 mb-2">
                  {{ formatUserType(fee.user_type) }}
                </div>
                <div class="flex items-center gap-2">
                  <span class="text-gray-500">৳</span>
                  <input
                    v-model.number="fee.fee_amount"
                    type="number"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                  >
                </div>
              </div>
            </div>
          </div>

          <!-- Status -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
              Status
            </h2>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <select
                v-model="form.status"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              >
                <option value="draft">
                  Draft
                </option>
                <option value="active">
                  Active
                </option>
                <option value="judging">
                  Judging
                </option>
                <option value="completed">
                  Completed
                </option>
                <option value="cancelled">
                  Cancelled
                </option>
                <option value="archived">
                  Archived
                </option>
              </select>
            </div>

            <div class="mt-4">
              <label class="flex items-center">
                <input
                  v-model="form.is_featured"
                  type="checkbox"
                  class="h-4 w-4 rounded border-gray-300"
                >
                <span class="ml-2 text-sm font-medium text-gray-700">Featured Competition</span>
              </label>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="flex items-center">
                  <input
                    v-model="form.is_paid_competition"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300"
                  >
                  <span class="ml-2 text-sm font-medium text-gray-700">Paid Competition</span>
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
                >
              </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
              <label class="flex items-center">
                <input
                  v-model="form.allow_public_voting"
                  type="checkbox"
                  class="h-4 w-4 rounded border-gray-300"
                >
                <span class="ml-2 text-sm font-medium text-gray-700">Allow Public Voting</span>
              </label>
              <label class="flex items-center">
                <input
                  v-model="form.voting_enabled"
                  type="checkbox"
                  class="h-4 w-4 rounded border-gray-300"
                >
                <span class="ml-2 text-sm font-medium text-gray-700">Enable People’s Choice Voting</span>
              </label>
              <label class="flex items-center">
                <input
                  v-model="form.allow_judge_scoring"
                  type="checkbox"
                  class="h-4 w-4 rounded border-gray-300"
                >
                <span class="ml-2 text-sm font-medium text-gray-700">Allow Judge Scoring</span>
              </label>
              <label class="flex items-center">
                <input
                  v-model="form.allow_watermark"
                  type="checkbox"
                  class="h-4 w-4 rounded border-gray-300"
                >
                <span class="ml-2 text-sm font-medium text-gray-700">Allow Watermark</span>
              </label>
              <label class="flex items-center">
                <input
                  v-model="form.require_watermark"
                  type="checkbox"
                  class="h-4 w-4 rounded border-gray-300"
                >
                <span class="ml-2 text-sm font-medium text-gray-700">Require Watermark</span>
              </label>
              <label class="flex items-center">
                <input
                  v-model="form.is_public"
                  type="checkbox"
                  class="h-4 w-4 rounded border-gray-300"
                >
                <span class="ml-2 text-sm font-medium text-gray-700">Public Competition</span>
              </label>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Public Vote Weight (%)</label>
                <input
                  v-model.number="form.vote_weight_percent"
                  type="number"
                  min="0"
                  max="100"
                  step="1"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Judge Score Weight (%)</label>
                <input
                  v-model.number="form.judge_weight_percent"
                  type="number"
                  min="0"
                  max="100"
                  step="1"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
              </div>
            </div>
            <div class="mt-2 text-sm text-gray-600">
              Total: {{ Number(form.vote_weight_percent || 0) + Number(form.judge_weight_percent || 0) }}%
            </div>
            <p
              v-if="errors.vote_weight"
              class="mt-1 text-sm text-red-600"
            >
              {{ errors.vote_weight }}
            </p>
          </div>

          <!-- Buttons -->
          <div class="flex justify-end gap-4">
            <router-link
              to="/admin/competitions"
              class="px-6 py-2 border-2 border-burgundy text-burgundy rounded-lg font-medium hover:bg-gray-50 transition"
            >
              Cancel
            </router-link>
            <button
              type="submit"
              :disabled="loading"
              class="px-6 py-2 bg-burgundy text-white rounded-lg font-medium hover:bg-burgundy-dark disabled:opacity-50 transition"
            >
              {{ loading ? 'Creating...' : 'Create Competition' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <PexelsPickerModal
      :visible="pexelsPickerOpen"
      :target-width="pexelsTarget.width"
      :target-height="pexelsTarget.height"
      @close="closePexelsPicker"
      @select="handlePexelsSelect"
    />
  </div>
</template>

<script>
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
import PexelsPickerModal from '../../../components/PexelsPickerModal.vue'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.min.css'
import { formatNumber } from '../../../utils/formatters'
import { validateUploadFile } from '../../../utils/imageValidation'
import api from '../../../api'

export default {
  components: {
    AdminHeader,
    AdminQuickNav,
    PexelsPickerModal
  },

  data() {
    return {
      loading: false,
      errors: {},
      uploadingImages: {
        hero_image: false,
        cover_image: false,
        banner_image: false,
      },
      pexelsPickerOpen: false,
      pexelsTarget: {
        field: 'hero_image',
        width: 1600,
        height: 900,
      },
      categories: [],
      judges: [],
      sponsors: [],
      sponsorSearch: '',
      judgeSearch: '',
      form: {
        title: '',
        slug: '',
        theme: '',
        description: '',
        mode: 'open',
        category_id: '',
        start_date: '',
        submission_deadline: '',
        end_date: '',
        voting_start_at: '',
        voting_end_at: '',
        voting_start_date: '',
        voting_end_date: '',
        judging_start_at: '',
        judging_end_at: '',
        judging_deadline: '',
        results_announcement_date: '',
        announcement_date: '',
        hero_image: '',
        hero_image_credit_name: '',
        hero_image_credit_url: '',
        banner_image: '',
        banner_image_credit_name: '',
        banner_image_credit_url: '',
        cover_image: '',
        cover_image_credit_name: '',
        cover_image_credit_url: '',
        total_prize_pool: 2,
        number_of_winners: 1,
        max_submissions_per_user: 3,
        entry_type: 'single',
        series_min_images: 5,
        series_max_images: 10,
        prizes: [{ rank: '1st', type: 'Cash', amount: 2, description: '' }],
        rules: '',
        terms_and_conditions: '',
        status: 'draft',
        is_public: true,
        is_featured: false,
        is_paid_competition: false,
        participation_fee: 0,
        allow_public_voting: true,
        voting_enabled: false,
        allow_judge_scoring: true,
        vote_weight_percent: 40,
        judge_weight_percent: 60,
        allow_watermark: false,
        require_watermark: false,
        district_battle_enabled: false,
        sponsor_ids: [],
        sponsors: [],
        entry_fees: [
          { user_type: 'guest', fee_amount: 0 },
          { user_type: 'registered', fee_amount: 0 },
          { user_type: 'verified', fee_amount: 0 },
          { user_type: 'student', fee_amount: 0 }
        ],
        judge_ids: []
      },
        pickerInstances: [],
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
For questions or concerns regarding the competition, please contact our support team at support@photographersb.com`
    }
  },
  computed: {
    filteredSponsors() {
      if (!this.sponsorSearch) return this.sponsors;
      return this.sponsors.filter(s =>
        (s.name || '').toLowerCase().includes(this.sponsorSearch.toLowerCase())
      );
    },
    filteredJudges() {
      if (!this.judgeSearch) return this.judges;
      return this.judges.filter(j =>
        (j.name || '').toLowerCase().includes(this.judgeSearch.toLowerCase())
      );
    },
    cashPrizeTotal() {
      return this.form.prizes
        .filter(prize => prize.type === 'Cash')
        .reduce((sum, prize) => sum + (Number(prize.amount) || 0), 0);
    }
  },

  watch: {
    'form.sponsor_ids': {
      handler() {
        this.syncSponsorRows();
      },
      deep: true
    }
  },
  mounted() {
    this.initializePickers();
    this.loadCategories();
    this.loadSponsors();
    this.loadJudges();
  },
  beforeUnmount() {
    this.destroyPickers();
  },

  methods: {
    initializePickers() {
      this.destroyPickers();
      const root = this.$el;
      const dateInputs = root.querySelectorAll('.js-date');
      const dateTimeInputs = root.querySelectorAll('.js-datetime');

      dateInputs.forEach((input) => {
        const instance = flatpickr(input, {
          dateFormat: 'd-m-Y',
          allowInput: true,
          onChange: () => input.dispatchEvent(new Event('input'))
        });
        this.pickerInstances.push(instance);
      });

      dateTimeInputs.forEach((input) => {
        const instance = flatpickr(input, {
          dateFormat: 'd-m-Y H:i',
          enableTime: true,
          time_24hr: true,
          allowInput: true,
          onChange: () => input.dispatchEvent(new Event('input'))
        });
        this.pickerInstances.push(instance);
      });
    },
    destroyPickers() {
      this.pickerInstances.forEach((instance) => instance.destroy());
      this.pickerInstances = [];
    },
    syncSponsorRows() {
      const rows = Array.isArray(this.form.sponsors) ? [...this.form.sponsors] : [];
      const byId = new Map(rows.map(row => [row.sponsor_id, row]));
      const next = this.form.sponsor_ids.map((id, index) => {
        const existing = byId.get(id);
        return {
          sponsor_id: id,
          tier: existing?.tier || 'bronze',
          sort_order: existing?.sort_order ?? index,
          sponsored_amount: existing?.sponsored_amount ?? null
        };
      });
      this.form.sponsors = next;
    },
    async loadCategories() {
      try {
        const { data } = await api.get('/categories');
        if (data.data) {
          this.categories = data.data;
        }
      } catch (error) {
        console.error('Error loading categories:', error);
      }
    },

    async loadSponsors() {
      try {
        const { data } = await api.get('/admin/platform-sponsors');
        if (data.data) {
          this.sponsors = data.data.filter(s => s.status === 'active');
        }
      } catch (error) {
        console.error('Error loading sponsors:', error);
      }
    },

    async loadJudges() {
      try {
        const { data } = await api.get('/admin/judges', {
          params: {
            status: 'active',
            per_page: 200
          }
        });
        const payload = data.data?.data || data.data || [];
        this.judges = payload;
      } catch (error) {
        console.error('Error loading judges:', error);
      }
    },

    async handleImageUpload(field, event) {
      const file = event.target.files?.[0];
      if (!file) return;

      if (field === 'hero_image') {
        this.form.hero_image_credit_name = '';
        this.form.hero_image_credit_url = '';
      }
      if (field === 'banner_image') {
        this.form.banner_image_credit_name = '';
        this.form.banner_image_credit_url = '';
      }
      if (field === 'cover_image') {
        this.form.cover_image_credit_name = '';
        this.form.cover_image_credit_url = '';
      }

      const rules = {
        hero_image: { width: 1600, height: 900 },
        cover_image: { width: 1200, height: 1200 },
        banner_image: { width: 1920, height: 600 }
      };
      const rule = rules[field] || {};
      const validation = await validateUploadFile(file, {
        label: 'Image',
        maxBytes: 5 * 1024 * 1024,
        allowedTypes: ['image/jpeg', 'image/png'],
        imageWidth: rule.width,
        imageHeight: rule.height
      });

      if (!validation.ok) {
        this.errors[field] = validation.message;
        event.target.value = '';
        return;
      }

      this.uploadingImages[field] = true;
      this.errors[field] = '';

      try {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('folder', 'competitions');

        const response = await api.post('/admin/media/upload', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });

        if (response.data?.status === 'success' && response.data.data?.url) {
          this.form[field] = response.data.data.url;
        } else {
          this.errors[field] = response.data?.message || 'Image upload failed.';
        }
      } catch (error) {
        this.errors[field] = error.response?.data?.message || 'Image upload failed.';
      } finally {
        this.uploadingImages[field] = false;
        event.target.value = '';
      }
    },

    openPexelsPicker(field, width, height) {
      this.pexelsTarget = { field, width, height };
      this.pexelsPickerOpen = true;
    },

    closePexelsPicker() {
      this.pexelsPickerOpen = false;
    },

    applyPexelsCredit(field, credit) {
      if (field === 'hero_image') {
        this.form.hero_image_credit_name = credit?.name || '';
        this.form.hero_image_credit_url = credit?.url || '';
      }
      if (field === 'banner_image') {
        this.form.banner_image_credit_name = credit?.name || '';
        this.form.banner_image_credit_url = credit?.url || '';
      }
      if (field === 'cover_image') {
        this.form.cover_image_credit_name = credit?.name || '';
        this.form.cover_image_credit_url = credit?.url || '';
      }
    },

    async handlePexelsSelect({ file, credit }) {
      const field = this.pexelsTarget.field;
      this.uploadingImages[field] = true;
      this.errors[field] = '';
      try {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('folder', 'competitions');

        const response = await api.post('/admin/media/upload', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });

        if (response.data?.status === 'success' && response.data.data?.url) {
          this.form[field] = response.data.data.url;
          this.applyPexelsCredit(field, credit);
        } else {
          this.errors[field] = response.data?.message || 'Image upload failed.';
        }
      } catch (error) {
        this.errors[field] = error.response?.data?.message || 'Image upload failed.';
      } finally {
        this.uploadingImages[field] = false;
        this.closePexelsPicker();
      }
    },

    async submitForm() {
      this.loading = true;
      this.errors = {};

      try {
        const invalidCashPrize = this.form.prizes.some(
          prize => prize.type === 'Cash' && Number(prize.amount) <= 1
        );
        const invalidNonCashPrize = this.form.prizes.some(
          prize => prize.type !== 'Cash' && !(prize.description || '').trim()
        );

        if (invalidCashPrize) {
          this.errors.prizes = 'Cash prize amount must be greater than 1.';
          throw new Error(this.errors.prizes);
        }

        if (invalidNonCashPrize) {
          this.errors.prizes = 'Non-cash prizes require a description.';
          throw new Error(this.errors.prizes);
        }

        if (Number(this.form.total_prize_pool) !== this.cashPrizeTotal) {
          this.errors.total_prize_pool = 'Total Prize Pool must equal the sum of cash prizes.';
          throw new Error(this.errors.total_prize_pool);
        }

        this.syncSponsorRows();

        const startDate = this.parseDateInput(this.form.start_date);
        const endDate = this.parseDateInput(this.form.end_date);
        const judgingDeadline = this.parseDateInput(this.form.judging_deadline);
        const announcementDate = this.parseDateInput(this.form.announcement_date);
        const submissionDeadline = this.parseDateTimeInput(this.form.submission_deadline);
        const votingStartAt = this.parseDateTimeInput(this.form.voting_start_at);
        const votingEndAt = this.parseDateTimeInput(this.form.voting_end_at);
        const votingStartDate = this.parseDateTimeInput(this.form.voting_start_date);
        const votingEndDate = this.parseDateTimeInput(this.form.voting_end_date);
        const judgingStartAt = this.parseDateTimeInput(this.form.judging_start_at);
        const judgingEndAt = this.parseDateTimeInput(this.form.judging_end_at);
        const resultsAnnouncementDate = this.parseDateTimeInput(this.form.results_announcement_date);

        if (!submissionDeadline) {
          this.errors.submission_deadline = 'Use DD-MM-YYYY HH:mm format.';
        }
        if (!votingStartAt) {
          this.errors.voting_start_at = 'Use DD-MM-YYYY HH:mm format.';
        }
        if (!votingEndAt) {
          this.errors.voting_end_at = 'Use DD-MM-YYYY HH:mm format.';
        }
        if (!resultsAnnouncementDate) {
          this.errors.results_announcement_date = 'Use DD-MM-YYYY HH:mm format.';
        }
        if (this.form.start_date && !startDate) {
          this.errors.start_date = 'Use DD-MM-YYYY format.';
        }
        if (this.form.end_date && !endDate) {
          this.errors.end_date = 'Use DD-MM-YYYY format.';
        }
        if (this.form.judging_deadline && !judgingDeadline) {
          this.errors.judging_deadline = 'Use DD-MM-YYYY format.';
        }
        if (this.form.announcement_date && !announcementDate) {
          this.errors.announcement_date = 'Use DD-MM-YYYY format.';
        }
        if (this.form.voting_start_date && !votingStartDate) {
          this.errors.voting_start_date = 'Use DD-MM-YYYY HH:mm format.';
        }
        if (this.form.voting_end_date && !votingEndDate) {
          this.errors.voting_end_date = 'Use DD-MM-YYYY HH:mm format.';
        }
        if (this.form.judging_start_at && !judgingStartAt) {
          this.errors.judging_start_at = 'Use DD-MM-YYYY HH:mm format.';
        }
        if (this.form.judging_end_at && !judgingEndAt) {
          this.errors.judging_end_at = 'Use DD-MM-YYYY HH:mm format.';
        }

        if (Object.keys(this.errors).length > 0) {
          throw new Error('Please fix the timeline dates.');
        }

        const voteWeightTotal = Number(this.form.vote_weight_percent || 0) + Number(this.form.judge_weight_percent || 0);
        if (this.form.status !== 'draft' && Math.abs(voteWeightTotal - 100) > 0.5) {
          this.errors.vote_weight = 'Public vote and judge score must total 100%.';
          throw new Error(this.errors.vote_weight);
        }

        const payload = {
          title: this.form.title,
          slug: this.form.slug || null,
          theme: this.form.theme,
          description: this.form.description,
          mode: this.form.mode,
          category_id: this.form.category_id || null,
          start_date: startDate || null,
          submission_deadline: submissionDeadline + ':00',
          end_date: endDate || null,
          voting_start_at: votingStartAt + ':00',
          voting_end_at: votingEndAt + ':00',
          voting_start_date: votingStartDate ? votingStartDate + ':00' : null,
          voting_end_date: votingEndDate ? votingEndDate + ':00' : null,
          judging_start_at: judgingStartAt ? judgingStartAt + ':00' : null,
          judging_end_at: judgingEndAt ? judgingEndAt + ':00' : null,
          judging_deadline: judgingDeadline || null,
          results_announcement_date: resultsAnnouncementDate + ':00',
          announcement_date: announcementDate || null,
          hero_image: this.form.hero_image || null,
          banner_image: this.form.banner_image || null,
          cover_image: this.form.cover_image || null,
          total_prize_pool: this.form.total_prize_pool,
          number_of_winners: this.form.number_of_winners,
          participation_fee: this.form.is_paid_competition ? this.form.participation_fee : 0,
          is_paid_competition: this.form.is_paid_competition,
          max_submissions_per_user: this.form.max_submissions_per_user,
          entry_type: this.form.entry_type,
          series_min_images: this.form.series_min_images,
          series_max_images: this.form.series_max_images,
          prizes: this.form.prizes.map(prize => {
            const prizeData = {
              position: prize.rank,
              amount: prize.amount || 0,
              prize_type: prize.type === 'Cash' ? 'cash' : 'gift',
              award_type: prize.award_type || 'global',
              sponsor_id: prize.sponsor_id || null,
              sort_order: prize.sort_order || 0
            };
            if (prize.description && prize.description.trim()) {
              prizeData.description = prize.description;
            }
            return prizeData;
          }),
          rules: this.form.rules,
          terms_and_conditions: this.form.terms_and_conditions,
          status: this.form.status,
          is_featured: this.form.is_featured ? 1 : 0,
          is_public: this.form.is_public,
          allow_public_voting: this.form.allow_public_voting,
          voting_enabled: this.form.voting_enabled,
          allow_judge_scoring: this.form.allow_judge_scoring,
          vote_weight: Number(this.form.vote_weight_percent || 0) / 100,
          judge_weight: Number(this.form.judge_weight_percent || 0) / 100,
          allow_watermark: this.form.allow_watermark,
          require_watermark: this.form.require_watermark,
          district_battle_enabled: this.form.district_battle_enabled,
          sponsor_ids: this.form.sponsor_ids,
          sponsors: this.form.sponsors,
          entry_fees: this.form.entry_fees,
          judge_ids: this.form.judge_ids
        };

        await api.post('/admin/competitions', payload);

        // Success
        this.$router.push('/admin/competitions');
      } catch (error) {
        console.error('Error creating competition:', error);
        const status = error.response?.status;
        const data = error.response?.data || {};
        if (status === 401) {
          this.errors.auth = 'Your session has expired. Please log in again.';
          setTimeout(() => this.$router.push('/login'), 2000);
        } else if (status === 403) {
          this.errors.auth = `Access denied. Your role (${data.user_role}) does not have permission to create competitions. Admin/Super Admin access required.`;
        } else if (data.errors) {
          this.errors = data.errors || {};
        }
        alert(data.message || error.message || 'Error creating competition');
      } finally {
        this.loading = false;
      }
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

    formatUserType(type) {
      const map = {
        guest: 'Guest',
        registered: 'Registered',
        verified: 'Verified',
        student: 'Student'
      };
      return map[type] || type;
    },

    formatPrizeLabel(prize) {
      const rank = prize.rank || 'Prize';
      const type = prize.type || 'Reward';
      return `${rank} Place - ${type}`;
    },

    formatCurrency(value) {
      return formatNumber(value)
    },
    parseDateInput(value) {
      if (!value) return null;
      const match = value.trim().match(/^(\d{2})-(\d{2})-(\d{4})$/);
      if (!match) return null;
      const [, day, month, year] = match;
      return `${year}-${month}-${day}`;
    },
    parseDateTimeInput(value) {
      if (!value) return null;
      const match = value.trim().match(/^(\d{2})-(\d{2})-(\d{4})\s(\d{2}):(\d{2})$/);
      if (!match) return null;
      const [, day, month, year, hour, minute] = match;
      return `${year}-${month}-${day}T${hour}:${minute}`;
    }
  }
}
</script>

<style scoped>
</style>
