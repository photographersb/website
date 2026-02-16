<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader 
      title="Edit Event" 
      subtitle="Update event details"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

    <!-- Loading State -->
    <div v-if="loading" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center">
      <div class="text-gray-600">Loading event data...</div>
    </div>

    <!-- Form -->
    <div v-else class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <form @submit.prevent="submitForm" class="space-y-6" novalidate>
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Basic Information</h2>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
              <input
                v-model="form.title"
                type="text"
                :required="form.status !== 'draft'"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Event Type *</label>
              <select
                v-model="form.event_type"
                :required="form.status !== 'draft'"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              >
                <option value="">Select event type</option>
                <option value="workshop">Workshop</option>
                <option value="photowalk">Photowalk</option>
                <option value="expo">Expo</option>
                <option value="seminar">Seminar</option>
                <option value="meetup">Meetup</option>
                <option value="webinar">Webinar</option>
                <option value="exhibition">Exhibition</option>
                <option value="competition">Competition</option>
                <option value="other">Other</option>
              </select>
              <p v-if="errors.event_type" class="mt-1 text-sm text-red-600">{{ errors.event_type }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
              <textarea
                v-model="form.description"
                rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="Detailed description of the event..."
              ></textarea>
              <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Hero Image URL</label>
              <input
                v-model="form.hero_image_url"
                type="url"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="https://example.com/hero-image.jpg"
              />
              <input
                type="file"
                accept="image/*"
                class="upload-input mt-2 block text-sm"
                @change="handleImageUpload('hero_image_url', $event)"
              />
              <div class="mt-2 flex flex-wrap gap-2">
                <button
                  type="button"
                  class="rounded-full border border-burgundy px-4 py-1 text-xs font-semibold text-burgundy hover:bg-burgundy hover:text-white"
                  @click="openPexelsPicker('hero_image_url', 1600, 900)"
                >
                  Choose from Pexels
                </button>
              </div>
              <p class="mt-1 upload-hint">Max 5 MB. JPG/PNG. 1600x900 px.</p>
              <p
                v-if="uploadingImages.hero_image_url"
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
              <p class="mt-1 text-sm text-gray-500">Optional hero banner image URL</p>
              <p v-if="errors.hero_image_url" class="mt-1 text-sm text-red-600">{{ errors.hero_image_url }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Banner Image URL</label>
              <input
                v-model="form.banner_image"
                type="url"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="https://example.com/banner.jpg"
              />
              <p class="mt-1 text-sm text-gray-500">Optional banner image URL</p>
              <input
                type="file"
                accept="image/*"
                class="upload-input mt-2 block text-sm"
                @change="handleImageUpload('banner_image', $event)"
              />
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
              <p v-if="errors.banner_image" class="mt-1 text-sm text-red-600">{{ errors.banner_image }}</p>
            </div>
          </div>
        </div>

        <!-- Date, Time & Location -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Date, Time & Location</h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Event Start Date *</label>
              <input
                v-model="form.event_date"
                type="text"
                inputmode="numeric"
                placeholder="dd-mm-yyyy"
                pattern="\d{2}-\d{2}-\d{4}"
                :required="form.status !== 'draft'"
                class="js-date w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.event_date" class="mt-1 text-sm text-red-600">{{ errors.event_date }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Event End Date</label>
              <input
                v-model="form.event_end_date"
                type="text"
                inputmode="numeric"
                placeholder="dd-mm-yyyy"
                pattern="\d{2}-\d{2}-\d{4}"
                class="js-date w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p class="mt-1 text-sm text-gray-500">Leave blank for single-day event</p>
              <p v-if="errors.event_end_date" class="mt-1 text-sm text-red-600">{{ errors.event_end_date }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
              <input
                v-model="form.start_time"
                type="time"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.start_time" class="mt-1 text-sm text-red-600">{{ errors.start_time }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
              <input
                v-model="form.end_time"
                type="time"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.end_time" class="mt-1 text-sm text-red-600">{{ errors.end_time }}</p>
            </div>

            <div class="flex items-center md:col-span-2">
              <input
                v-model="form.all_day_event"
                type="checkbox"
                class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
              />
              <label class="ml-2 block text-sm text-gray-900">
                All Day Event
              </label>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Duration (hours)</label>
              <input
                v-model.number="form.duration_hours"
                type="number"
                min="0.5"
                step="0.5"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="2.5"
              />
              <p class="mt-1 text-sm text-gray-500">Duration in hours (e.g., 2.5 for 2.5 hours)</p>
              <p v-if="errors.duration_hours" class="mt-1 text-sm text-red-600">{{ errors.duration_hours }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
              <select
                v-model="form.city_id"
                :required="form.status !== 'draft'"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              >
                <option value="">Select location</option>
                <option v-for="city in cities" :key="city.id" :value="city.id">
                  {{ city.name }}
                </option>
              </select>
              <p v-if="cities.length === 0" class="mt-1 text-sm text-warning-700">⚠️ No locations available. Please add locations from Admin → Locations first.</p>
              <p v-if="errors.city_id" class="mt-1 text-sm text-red-600">{{ errors.city_id }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Venue Name *</label>
              <input
                v-model="form.venue_name"
                type="text"
                :required="form.status !== 'draft'"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="e.g., ICCB, Hotel InterContinental, Dhaka Club"
              />
              <p v-if="errors.venue_name" class="mt-1 text-sm text-red-600">{{ errors.venue_name }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Venue Full Address *</label>
              <textarea
                v-model="form.venue_address"
                rows="2"
                :required="form.status !== 'draft'"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="e.g., 123 Main Street, Building Name, Floor 2, Near Landmark"
              ></textarea>
              <p v-if="errors.venue_address" class="mt-1 text-sm text-red-600">{{ errors.venue_address }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Location Display Name *</label>
              <input
                v-model="form.location"
                type="text"
                :required="form.status !== 'draft'"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="Optional: Short location label for listings"
              />
              <p class="mt-1 text-sm text-gray-500">Short name for event listings (e.g., "Gulshan 2"). Required when publishing.</p>
              <p v-if="errors.location" class="mt-1 text-sm text-red-600">{{ errors.location }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Additional Address Notes</label>
              <input
                v-model="form.address"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="Optional: Additional directions or notes"
              />
              <p class="mt-1 text-sm text-gray-500">Optional additional address information</p>
              <p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">📍 Google Maps Link</label>
              <input
                v-model="form.google_map_link"
                type="url"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="https://maps.google.com/maps?q=..."
              />
              <p class="mt-1 text-sm text-gray-500">Paste a Google Maps link for the venue.</p>
              <p v-if="errors.google_map_link" class="mt-1 text-sm text-red-600">{{ errors.google_map_link }}</p>
            </div>
          </div>
        </div>

        <!-- Attendance & Pricing -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Attendance & Pricing</h2>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Event Mode</label>
              <select
                v-model="form.event_mode"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              >
                <option value="free">Free</option>
                <option value="paid">Paid</option>
              </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Max Attendees</label>
                <input
                  v-model.number="form.max_attendees"
                  type="number"
                  min="1"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                  placeholder="50"
                />
                <p class="mt-1 text-sm text-gray-500">Leave blank for unlimited</p>
                <p v-if="errors.max_attendees" class="mt-1 text-sm text-red-600">{{ errors.max_attendees }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ticket Price (৳)</label>
                <input
                  v-model.number="form.ticket_price"
                  type="number"
                  min="0"
                  step="100"
                  :disabled="form.event_mode !== 'paid'"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent disabled:bg-gray-100"
                  placeholder="500"
                />
                <p class="mt-1 text-sm text-gray-500">Set to 0 for free ticketed events</p>
                <p v-if="errors.ticket_price" class="mt-1 text-sm text-red-600">{{ errors.ticket_price }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Max Tickets Per User</label>
                <input
                  v-model.number="form.max_tickets_per_user"
                  type="number"
                  min="1"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                  placeholder="1"
                />
                <p v-if="errors.max_tickets_per_user" class="mt-1 text-sm text-red-600">{{ errors.max_tickets_per_user }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Registration Deadline</label>
                <input
                  v-model="form.registration_deadline"
                  type="text"
                  inputmode="numeric"
                  placeholder="dd-mm-yyyy hh:mm"
                  pattern="\d{2}-\d{2}-\d{4} \d{2}:\d{2}"
                  class="js-datetime w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
                <p v-if="errors.registration_deadline" class="mt-1 text-sm text-red-600">{{ errors.registration_deadline }}</p>
              </div>
            </div>

            <div class="flex flex-col gap-2">
              <div class="flex items-center">
                <input
                  v-model="form.require_registration"
                  type="checkbox"
                  class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
                />
                <label class="ml-2 block text-sm text-gray-900">
                  Require Registration (Users must register to attend)
                </label>
              </div>

              <div class="flex items-center">
                <input
                  v-model="form.is_ticketed"
                  type="checkbox"
                  class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
                />
                <label class="ml-2 block text-sm text-gray-900">
                  Ticketed Event (Requires paid tickets)
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Requirements -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Requirements & Details</h2>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
              <textarea
                v-model="form.requirements"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="What attendees need to bring or know (e.g., 'Bring your own camera')"
              ></textarea>
              <p v-if="errors.requirements" class="mt-1 text-sm text-red-600">{{ errors.requirements }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Organizer/Photographer *</label>
              <select
                v-model="form.organizer_id"
                :required="form.status !== 'draft'"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              >
                <option value="">Select photographer</option>
                <option v-for="photographer in photographers" :key="photographer.id" :value="photographer.id">
                  {{ photographer.user?.name || `Photographer #${photographer.id}` }}
                </option>
              </select>
              <p class="mt-1 text-sm text-gray-500">Select the photographer organizing this event</p>
              <p v-if="errors.organizer_id" class="mt-1 text-sm text-red-600">{{ errors.organizer_id }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Mentors</label>
              <div class="space-y-2">
                <div
                  v-for="mentor in mentors"
                  :key="mentor.id"
                  class="flex items-center"
                >
                  <input
                    :id="`mentor-${mentor.id}`"
                    v-model="form.mentor_ids"
                    type="checkbox"
                    :value="mentor.id"
                    class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
                  />
                  <label
                    :for="`mentor-${mentor.id}`"
                    class="ml-2 block text-sm text-gray-900"
                  >
                    {{ mentor.name }} <span class="text-gray-500">({{ mentor.email }})</span>
                  </label>
                </div>
              </div>
              <p v-if="mentors.length === 0" class="mt-1 text-sm text-warning-700">
                ℹ️ No active mentors available. Mentors can be assigned to events for guidance and support.
              </p>
              <p class="mt-1 text-sm text-gray-500">Select one or more mentors for this event (optional)</p>
              <p v-if="errors.mentor_ids" class="mt-1 text-sm text-red-600">{{ errors.mentor_ids }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <label class="flex items-center">
                <input
                  v-model="form.certificates_enabled"
                  type="checkbox"
                  class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
                />
                <span class="ml-2 text-sm text-gray-900">Enable Certificates</span>
              </label>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Certificate Template</label>
                <select
                  v-model="form.certificate_template_id"
                  :disabled="!form.certificates_enabled"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                  <option value="">Select template</option>
                  <option v-for="template in certificateTemplates" :key="template.id" :value="template.id">
                    {{ template.name || `Template #${template.id}` }}
                  </option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Sponsors -->
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
                  v-model="form.sponsor_ids"
                  type="checkbox"
                  :value="sponsor.id"
                  class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
                />
                <div>
                  <div class="font-medium text-gray-900">{{ sponsor.name }}</div>
                  <div class="text-xs text-gray-500">{{ sponsor.website_url || sponsor.website || 'No website' }}</div>
                </div>
              </label>
            </div>

            <div v-if="form.sponsors.length" class="mt-4 border border-gray-200 rounded-lg p-4">
              <div class="text-sm font-medium text-gray-700 mb-3">Sponsor Tiers & Ordering</div>
              <div class="space-y-3">
                <div
                  v-for="(sponsorRow, index) in form.sponsors"
                  :key="sponsorRow.sponsor_id"
                  class="grid grid-cols-1 md:grid-cols-4 gap-3"
                >
                  <div class="md:col-span-2">
                    <div class="text-xs text-gray-500">Sponsor</div>
                    <div class="text-sm font-semibold text-gray-900">
                      {{ availableSponsors.find(s => s.id === sponsorRow.sponsor_id)?.name || 'Sponsor' }}
                    </div>
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tier</label>
                    <select
                      v-model="sponsorRow.tier"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    >
                      <option value="title">Title</option>
                      <option value="gold">Gold</option>
                      <option value="silver">Silver</option>
                      <option value="bronze">Bronze</option>
                      <option value="support">Support</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sponsored Amount (৳)</label>
                    <input
                      v-model.number="sponsorRow.sponsored_amount"
                      type="number"
                      min="0"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- SEO -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">SEO</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
              <input
                v-model="form.meta_title"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
              <textarea
                v-model="form.meta_description"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">OG Image URL</label>
              <input
                v-model="form.og_image"
                type="url"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
              />
              <input
                type="file"
                accept="image/*"
                class="upload-input mt-2 block text-sm"
                @change="handleImageUpload('og_image', $event)"
              />
              <p class="mt-1 upload-hint">Max 5 MB. JPG/PNG. 1200x630 px.</p>
              <p v-if="uploadingImages.og_image" class="mt-1 text-xs text-gray-500">Uploading...</p>
            </div>
          </div>
        </div>

        <!-- Status & Settings -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <h2 class="text-xl font-bold text-gray-900">Status & Settings</h2>
            <span
              v-if="eventState"
              class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
              :class="eventState.tone"
            >
              {{ eventState.label }}
            </span>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
              <select
                v-model="form.status"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              >
                <option value="draft">Draft (Not visible to public)</option>
                <option value="published">Published (Visible to public)</option>
                <option value="cancelled">Cancelled</option>
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
                Featured Event (Show prominently on events page)
              </label>
            </div>

            <div v-if="form.is_featured">
              <label class="block text-sm font-medium text-gray-700 mb-2">Featured Until</label>
              <input
                v-model="form.featured_until"
                type="text"
                inputmode="numeric"
                placeholder="dd-mm-yyyy hh:mm"
                pattern="\d{2}-\d{2}-\d{4} \d{2}:\d{2}"
                class="js-datetime w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p class="mt-1 text-sm text-gray-500">Leave blank to feature indefinitely</p>
              <p v-if="errors.featured_until" class="mt-1 text-sm text-red-600">{{ errors.featured_until }}</p>
            </div>

            <div class="flex items-center">
              <input
                v-model="form.is_verified"
                type="checkbox"
                class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
              />
              <label class="ml-2 block text-sm text-gray-900">
                Verified Event (Official/trusted event)
              </label>
            </div>
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-4">
          <router-link
            to="/admin/events"
            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-all"
          >
            Cancel
          </router-link>
          <button
            type="button"
            @click="saveDraft"
            :disabled="processing"
            class="px-6 py-3 bg-gray-100 text-gray-800 rounded-lg font-medium hover:bg-gray-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Save Draft
          </button>
          <button
            type="submit"
            :disabled="processing"
            class="px-6 py-3 bg-burgundy text-white rounded-lg font-medium hover:bg-burgundy-dark transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ processing ? 'Updating...' : 'Update Event' }}
          </button>
        </div>
      </form>
    </div>

    <PexelsPickerModal
      :visible="pexelsPickerOpen"
      :target-width="pexelsTarget.width"
      :target-height="pexelsTarget.height"
      @close="closePexelsPicker"
      @select="handlePexelsSelect"
    />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch, computed, nextTick } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../../api';
import { validateUploadFile } from '../../../utils/imageValidation'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
import PexelsPickerModal from '../../../components/PexelsPickerModal.vue'
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
const router = useRouter();
const processing = ref(false);
const loading = ref(true);
const cities = ref([]);
const photographers = ref([]);
const mentors = ref([]);
const availableSponsors = ref([]);
const sponsorSearch = ref('');
const certificateTemplates = ref([]);
const errors = ref({});
const allowPrefill = ref(false);
const pickerInstances = [];
const eventTiming = ref({
  start_datetime: null,
  end_datetime: null,
  event_date: null,
  duration_hours: null,
});
const uploadingImages = ref({
  hero_image_url: false,
  banner_image: false,
  og_image: false,
});

const pexelsPickerOpen = ref(false);
const pexelsTarget = ref({
  field: 'hero_image_url',
  width: 1600,
  height: 900,
});

const form = ref({
  title: '',
  event_type: '',
  type: '',
  description: '',
  hero_image_url: '',
  hero_image_credit_name: '',
  hero_image_credit_url: '',
  banner_image: '',
  banner_image_credit_name: '',
  banner_image_credit_url: '',
  gallery_images: [],
  event_date: '',
  event_end_date: '',
  start_time: '',
  end_time: '',
  all_day_event: false,
  duration_hours: null,
  city_id: '',
  venue_name: '',
  venue_address: '',
  location: '',
  address: '',
  google_map_link: '',
  latitude: null,
  longitude: null,
  max_attendees: null,
  capacity: null,
  max_tickets_per_user: 1,
  require_registration: true,
  is_ticketed: false,
  ticket_price: 0,
  event_mode: 'free',
  price: 0,
  currency: 'BDT',
  registration_deadline: '',
  certificates_enabled: false,
  certificate_template_id: '',
  requirements: '',
  organizer_id: null,
  mentor_ids: [],
  mentors: [],
  sponsor_ids: [],
  sponsors: [],
  status: 'published',
  is_featured: false,
  featured_until: '',
  meta_title: '',
  meta_description: '',
  og_image: '',
  is_verified: false,
});

const eventState = computed(() => {
  if (!form.value.status) {
    return null;
  }

  if (form.value.status === 'cancelled') {
    return { label: 'Cancelled', tone: 'bg-red-100 text-red-800' };
  }

  const timing = eventTiming.value || {};
  const now = new Date();
  let start = timing.start_datetime ? new Date(timing.start_datetime) : null;
  let end = timing.end_datetime ? new Date(timing.end_datetime) : null;

  if (!start && timing.event_date) {
    start = new Date(timing.event_date);
    if (timing.duration_hours) {
      end = new Date(start.getTime() + Number(timing.duration_hours) * 60 * 60 * 1000);
    } else {
      end = new Date(start);
      end.setHours(23, 59, 59, 999);
    }
  }

  if (!start) {
    return { label: 'Date not set', tone: 'bg-gray-100 text-gray-700' };
  }

  if (end && now > end) {
    return { label: 'Ended', tone: 'bg-gray-100 text-gray-700' };
  }

  if (now < start) {
    return { label: 'Upcoming', tone: 'bg-blue-100 text-blue-800' };
  }

  return { label: 'Ongoing', tone: 'bg-green-100 text-green-800' };
});

const filteredSponsors = computed(() => {
  const term = (sponsorSearch.value || '').toLowerCase();
  if (!term) return availableSponsors.value;
  return availableSponsors.value.filter(sponsor =>
    (sponsor.name || '').toLowerCase().includes(term) ||
    (sponsor.website_url || sponsor.website || '').toLowerCase().includes(term)
  );
});

const EVENT_TYPE_PRESETS = {
  workshop: {
    description: 'Hands-on training focused on techniques, lighting, and workflow. Includes guided practice and live critique sessions.',
    requirements: 'Bring a camera with a charged battery, at least one lens, and a memory card. A tripod is recommended.',
  },
  photowalk: {
    description: 'A guided outdoor session covering composition, street storytelling, and light hunting across key locations.',
    requirements: 'Comfortable walking shoes, a camera or phone with a full charge, and weather-appropriate clothing.',
  },
  expo: {
    description: 'A showcase of photography brands, gear demos, and creative showcases with networking opportunities.',
    requirements: 'Carry a valid ID for entry and prepare any business cards or portfolios for networking.',
  },
  exhibition: {
    description: 'Curated photography exhibition featuring thematic galleries, artist talks, and community engagement.',
    requirements: 'No special equipment required. Photography inside the venue may be restricted by organizers.',
  },
  seminar: {
    description: 'Expert-led talks on industry trends, business strategy, and creative growth for photographers.',
    requirements: 'Bring a notebook or device for notes. Arrive 15 minutes early for seating.',
  },
  meetup: {
    description: 'Community gathering for photographers to connect, collaborate, and share experiences.',
    requirements: 'No equipment required. Optional: bring a portfolio or recent work to share.',
  },
  webinar: {
    description: 'Online session covering photography techniques, post-processing, or business insights.',
    requirements: 'Stable internet connection, headphones, and a quiet space for participation.',
  },
  competition: {
    description: 'Photography competition with submission guidelines, judging criteria, and award announcements.',
    requirements: 'Prepare your submissions in the required format and adhere to the deadline and theme.',
  },
  other: {
    description: 'Special event tailored for photographers with unique experiences and learning opportunities.',
    requirements: 'Follow the organizer instructions shared in the event announcement.',
  },
};

const applyEventTypePreset = (eventType) => {
  const preset = EVENT_TYPE_PRESETS[eventType];
  if (!preset) return;
  form.value.description = preset.description;
  form.value.requirements = preset.requirements;
};

const syncSponsorRows = () => {
  const rows = Array.isArray(form.value.sponsors) ? [...form.value.sponsors] : [];
  const byId = new Map(rows.map(row => [row.sponsor_id, row]));
  form.value.sponsors = form.value.sponsor_ids.map((id, index) => {
    const existing = byId.get(id);
    return {
      sponsor_id: id,
      tier: existing?.tier || 'bronze',
      sort_order: existing?.sort_order ?? index,
      sponsored_amount: existing?.sponsored_amount ?? null,
    };
  });
};

const formatDateForInput = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  if (Number.isNaN(date.getTime())) return '';
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${day}-${month}-${year}`;
};

const formatDateTimeForInput = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  if (Number.isNaN(date.getTime())) return '';
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${day}-${month}-${year} ${hours}:${minutes}`;
};

const getEventIdFromPath = () => {
  const segments = window.location.pathname.split('/').filter(Boolean);
  const editIndex = segments.indexOf('edit');
  if (editIndex !== -1 && segments[editIndex + 1]) {
    return segments[editIndex + 1];
  }
  return segments[segments.length - 1];
};

const fetchEvent = async () => {
  try {
    const eventId = getEventIdFromPath();
    const response = await api.get(`/admin/events/${eventId}`);
    
    const event = response.data.data || response.data;
    
    // Populate form with event data
    const inferredPaid = Boolean(event.event_mode === 'paid' || event.is_ticketed || Number(event.ticket_price || event.price || 0) > 0);
    const sponsorRows = Array.isArray(event.sponsors)
      ? event.sponsors.map((sponsor, index) => ({
          sponsor_id: sponsor.id,
          tier: sponsor.pivot?.tier || 'bronze',
          sort_order: sponsor.pivot?.sort_order ?? index,
          sponsored_amount: sponsor.pivot?.sponsored_amount ?? null,
        }))
      : [];

    form.value = {
      title: event.title || '',
      event_type: event.event_type || event.type || '',
      type: event.type || event.event_type || '',
      description: event.description || '',
      hero_image_url: event.hero_image_url || '',
      hero_image_credit_name: event.hero_image_credit_name || '',
      hero_image_credit_url: event.hero_image_credit_url || '',
      banner_image: event.banner_image || '',
      banner_image_credit_name: event.banner_image_credit_name || '',
      banner_image_credit_url: event.banner_image_credit_url || '',
      gallery_images: event.gallery_images || [],
      event_date: formatDateForInput(event.event_date),
      event_end_date: formatDateForInput(event.event_end_date),
      start_time: event.start_time || '',
      end_time: event.end_time || '',
      all_day_event: Boolean(event.all_day_event),
      duration_hours: event.duration_hours || null,
      city_id: event.city_id || '',
      venue_name: event.venue_name || '',
      venue_address: event.venue_address || '',
      location: event.location || '',
      address: event.address || '',
      google_map_link: event.google_map_link || '',
      latitude: event.latitude || null,
      longitude: event.longitude || null,
      max_attendees: event.max_attendees || null,
      capacity: event.capacity || null,
      max_tickets_per_user: event.max_tickets_per_user || 1,
      require_registration: event.require_registration ?? true,
      is_ticketed: Boolean(event.is_ticketed),
      ticket_price: Number(event.ticket_price || 0),
      event_mode: event.event_mode || (inferredPaid ? 'paid' : 'free'),
      price: Number(event.price || 0),
      currency: event.currency || 'BDT',
      registration_deadline: formatDateTimeForInput(event.registration_deadline),
      certificates_enabled: Boolean(event.certificates_enabled || event.certificate_template_id),
      certificate_template_id: event.certificate_template_id || '',
      requirements: event.requirements || '',
      organizer_id: event.organizer_id || null,
      mentor_ids: Array.isArray(event.mentors) ? event.mentors.map(mentor => mentor.id) : [],
      mentors: event.mentors || [],
      sponsor_ids: Array.isArray(event.sponsors) ? event.sponsors.map(sponsor => sponsor.id) : [],
      sponsors: sponsorRows,
      status: event.status || 'published',
      is_featured: Boolean(event.is_featured),
      featured_until: formatDateTimeForInput(event.featured_until),
      meta_title: event.meta_title || '',
      meta_description: event.meta_description || '',
      og_image: event.og_image || '',
      is_verified: Boolean(event.is_verified),
    };
    eventTiming.value = {
      start_datetime: event.start_datetime || null,
      end_datetime: event.end_datetime || null,
      event_date: event.event_date || null,
      duration_hours: event.duration_hours || null,
    };
    allowPrefill.value = true;
  } catch (error) {
    console.error('Error fetching event:', error);
    
    if (error.response?.status === 404) {
      alert('Event not found. It may have been deleted.');
    } else {
      alert(error.response?.data?.message || 'Failed to load event data');
    }
    
    router.push('/admin/events');
  } finally {
    loading.value = false;
  }
};

watch(
  () => form.value.sponsor_ids,
  () => syncSponsorRows(),
  { deep: true }
);

watch(
  () => form.value.event_mode,
  (mode) => {
    const isPaid = mode === 'paid';
    if (form.value.is_ticketed !== isPaid) {
      form.value.is_ticketed = isPaid;
    }
    if (!isPaid) {
      form.value.ticket_price = 0;
    }
  }
);

watch(
  () => form.value.is_ticketed,
  (value) => {
    const nextMode = value ? 'paid' : 'free';
    if (form.value.event_mode !== nextMode) {
      form.value.event_mode = nextMode;
    }
  }
);

watch(
  () => form.value.is_featured,
  async (value) => {
    if (!value) return;
    await nextTick();
    initializePickers();
  }
);

watch(
  () => form.value.event_type,
  (value, oldValue) => {
    if (!allowPrefill.value || !value || value === oldValue) return;
    applyEventTypePreset(value);
  }
);

const fetchCities = async () => {
  try {
    const response = await api.get('/locations', {
      params: {
        type: 'district'
      }
    });
    
    console.log('Cities API Response:', response.data);
    
    if (response.data.status === 'success' && Array.isArray(response.data.data)) {
      cities.value = response.data.data;
      console.log(`Loaded ${cities.value.length} cities`);
    } else {
      console.warn('Unexpected response structure:', response.data);
      cities.value = [];
    }
  } catch (error) {
    console.error('Error fetching cities:', error);
    cities.value = [];
  }
};

const fetchPhotographers = async () => {
  try {
    const response = await api.get('/admin/photographers', {
      params: {
        status: 'active',
        minimal: 1
      }
    });
    
    console.log('Photographers API Response:', response.data);
    
    if (response.data.status === 'success' && Array.isArray(response.data.data)) {
      photographers.value = response.data.data;
      console.log(`Loaded ${photographers.value.length} photographers`);
    } else {
      console.warn('Unexpected response structure:', response.data);
      photographers.value = [];
    }
  } catch (error) {
    console.error('Error fetching photographers:', error);
    photographers.value = [];
  }
};

const fetchMentors = async () => {
  try {
    const response = await api.get('/admin/mentors', {
      params: {
        status: 'active',
        minimal: 1
      }
    });

    if (response.data.status === 'success' && Array.isArray(response.data.data)) {
      mentors.value = response.data.data;
    } else {
      mentors.value = [];
    }
  } catch (error) {
    mentors.value = [];
  }
};

const fetchSponsors = async () => {
  try {
    const response = await api.get('/admin/platform-sponsors');
    const data = response.data?.data || [];
    availableSponsors.value = data.filter(sponsor => sponsor.status === 'active' || sponsor.is_active);
  } catch (error) {
    availableSponsors.value = [];
  }
};

const fetchCertificateTemplates = async () => {
  try {
    const response = await api.get('/admin/certificate-templates');
    certificateTemplates.value = response.data?.data || [];
  } catch (error) {
    certificateTemplates.value = [];
  }
};

const submitForm = async () => {
  processing.value = true;
  errors.value = {};

  try {
    const eventId = getEventIdFromPath();
    const eventDate = parseDateInput(form.value.event_date);
    const eventEndDate = parseDateInput(form.value.event_end_date);
    const registrationDeadline = parseDateTimeInput(form.value.registration_deadline);
    const featuredUntil = parseDateTimeInput(form.value.featured_until);

    if (form.value.status !== 'draft' && !eventDate) {
      errors.value.event_date = 'Use DD-MM-YYYY format.';
    }
    if (form.value.event_end_date && !eventEndDate) {
      errors.value.event_end_date = 'Use DD-MM-YYYY format.';
    }
    if (form.value.registration_deadline && !registrationDeadline) {
      errors.value.registration_deadline = 'Use DD-MM-YYYY HH:mm format.';
    }
    if (form.value.featured_until && !featuredUntil) {
      errors.value.featured_until = 'Use DD-MM-YYYY HH:mm format.';
    }

    if (Object.keys(errors.value).length > 0) {
      alert('Please fix the date fields.');
      return;
    }

    const formData = {
      ...form.value,
      event_date: eventDate || null,
      event_end_date: eventEndDate || null,
      registration_deadline: registrationDeadline ? `${registrationDeadline}:00` : null,
      featured_until: featuredUntil ? `${featuredUntil}:00` : null,
      type: form.value.event_type,
      organizer_id: form.value.organizer_id ? parseInt(form.value.organizer_id, 10) : null,
      city_id: form.value.city_id ? parseInt(form.value.city_id, 10) : null,
      ticket_price: form.value.event_mode === 'paid' ? form.value.ticket_price : 0,
      is_featured: form.value.is_featured ? 1 : 0,
      require_registration: form.value.require_registration ? 1 : 0,
      is_ticketed: form.value.event_mode === 'paid' ? 1 : 0,
      all_day_event: form.value.all_day_event ? 1 : 0
    };

    const response = await api.put(`/admin/events/${eventId}`, formData);
    
    if (response.data.status === 'success') {
      alert('Event updated successfully!');
      router.push('/admin/events');
    }
  } catch (error) {
    console.error('Error updating event:', error);
    
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors;
    } else {
      alert(error.response?.data?.message || 'Failed to update event');
    }
  } finally {
    processing.value = false;
  }
};

const initializePickers = () => {
  destroyPickers();
  const dateInputs = document.querySelectorAll('.js-date');
  const dateTimeInputs = document.querySelectorAll('.js-datetime');

  dateInputs.forEach((input) => {
    const instance = flatpickr(input, {
      dateFormat: 'd-m-Y',
      allowInput: true,
      onChange: () => input.dispatchEvent(new Event('input'))
    });
    pickerInstances.push(instance);
  });

  dateTimeInputs.forEach((input) => {
    const instance = flatpickr(input, {
      dateFormat: 'd-m-Y H:i',
      enableTime: true,
      time_24hr: true,
      allowInput: true,
      onChange: () => input.dispatchEvent(new Event('input'))
    });
    pickerInstances.push(instance);
  });
};

const destroyPickers = () => {
  pickerInstances.forEach((instance) => instance.destroy());
  pickerInstances.length = 0;
};

const parseDateInput = (value) => {
  if (!value) return null;
  const match = value.trim().match(/^(\d{2})-(\d{2})-(\d{4})$/);
  if (!match) return null;
  const [, day, month, year] = match;
  return `${year}-${month}-${day}`;
};

const parseDateTimeInput = (value) => {
  if (!value) return null;
  const match = value.trim().match(/^(\d{2})-(\d{2})-(\d{4})\s(\d{2}):(\d{2})$/);
  if (!match) return null;
  const [, day, month, year, hour, minute] = match;
  return `${year}-${month}-${day}T${hour}:${minute}`;
};

const handleImageUpload = async (field, event) => {
  const file = event.target.files?.[0];
  if (!file) return;

  if (field === 'hero_image_url') {
    form.value.hero_image_credit_name = '';
    form.value.hero_image_credit_url = '';
  }
  if (field === 'banner_image') {
    form.value.banner_image_credit_name = '';
    form.value.banner_image_credit_url = '';
  }

  const rules = {
    hero_image_url: { width: 1600, height: 900 },
    banner_image: { width: 1920, height: 600 },
    og_image: { width: 1200, height: 630 }
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
    errors.value[field] = validation.message;
    event.target.value = '';
    return;
  }

  uploadingImages.value[field] = true;
  errors.value[field] = '';

  try {
    const formData = new FormData();
    formData.append('image', file);
    formData.append('folder', 'events');

    const response = await api.post('/admin/media/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    if (response.data?.status === 'success' && response.data.data?.url) {
      form.value[field] = response.data.data.url;
    } else {
      errors.value[field] = response.data?.message || 'Image upload failed.';
    }
  } catch (error) {
    errors.value[field] = error.response?.data?.message || 'Image upload failed.';
  } finally {
    uploadingImages.value[field] = false;
    event.target.value = '';
  }
};

const openPexelsPicker = (field, width, height) => {
  pexelsTarget.value = { field, width, height };
  pexelsPickerOpen.value = true;
};

const closePexelsPicker = () => {
  pexelsPickerOpen.value = false;
};

const applyPexelsCredit = (field, credit) => {
  if (field === 'hero_image_url') {
    form.value.hero_image_credit_name = credit?.name || '';
    form.value.hero_image_credit_url = credit?.url || '';
  }
  if (field === 'banner_image') {
    form.value.banner_image_credit_name = credit?.name || '';
    form.value.banner_image_credit_url = credit?.url || '';
  }
};

const handlePexelsSelect = async ({ file, credit }) => {
  const field = pexelsTarget.value.field;
  uploadingImages.value[field] = true;
  errors.value[field] = '';
  try {
    const formData = new FormData();
    formData.append('image', file);
    formData.append('folder', 'events');

    const response = await api.post('/admin/media/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    if (response.data?.status === 'success' && response.data.data?.url) {
      form.value[field] = response.data.data.url;
      applyPexelsCredit(field, credit);
    } else {
      errors.value[field] = response.data?.message || 'Image upload failed.';
    }
  } catch (error) {
    errors.value[field] = error.response?.data?.message || 'Image upload failed.';
  } finally {
    uploadingImages.value[field] = false;
    closePexelsPicker();
  }
};

const saveDraft = async () => {
  form.value.status = 'draft';
  await submitForm();
};

onMounted(() => {
  initializePickers();
  fetchCities();
  fetchPhotographers();
  fetchMentors();
  fetchSponsors();
  fetchCertificateTemplates();
  fetchEvent();
});

onBeforeUnmount(() => {
  destroyPickers();
});
</script>
