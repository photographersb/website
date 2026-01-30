<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b">
      <div class="container mx-auto px-4 py-6">
        <h1 class="text-3xl font-bold">Photographer Dashboard</h1>
        <p class="text-gray-600">Welcome back, {{ user?.name }}</p>
      </div>
    </div>

    <div class="container mx-auto px-4 py-8">
      <!-- Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 mb-2">Total Bookings</p>
          <p class="text-3xl font-bold text-burgundy">{{ stats.total_bookings || 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 mb-2">Pending Requests</p>
          <p class="text-3xl font-bold text-yellow-600">{{ stats.pending_bookings || 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 mb-2">Average Rating</p>
          <p class="text-3xl font-bold text-green-600">{{ stats.average_rating || 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 mb-2">Total Revenue</p>
          <p class="text-3xl font-bold text-burgundy">৳{{ stats.total_revenue || 0 }}</p>
        </div>
      </div>

      <!-- Share Profile -->
      <div class="bg-gradient-to-r from-burgundy to-[#8B1538] rounded-lg shadow-lg p-6 mb-8 text-white">
        <div class="flex items-center justify-between gap-4">
          <div class="flex-1">
            <h3 class="text-lg font-bold mb-2">Share Your Profile</h3>
            <p class="text-sm text-white/90 mb-3">Share your professional profile with clients</p>
            <div class="flex gap-2">
              <input
                type="text"
                :value="profileUrl"
                readonly
                class="flex-1 px-4 py-2 rounded-lg bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/30"
              />
              <button
                @click="copyProfileLink"
                class="px-6 py-2 bg-white text-burgundy rounded-lg font-medium hover:bg-gray-100 transition-colors flex items-center gap-2"
              >
                <svg v-if="!copied" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <svg v-else class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                {{ copied ? 'Copied!' : 'Copy' }}
              </button>
              <a
                :href="profileUrl"
                target="_blank"
                class="px-6 py-2 bg-white/10 backdrop-blur-sm border border-white/20 text-white rounded-lg font-medium hover:bg-white/20 transition-colors flex items-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                View
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="bg-white rounded-lg shadow">
        <div class="border-b px-6 py-4">
          <div class="flex gap-6">
            <button
              @click="activeTab = 'bookings'"
              :class="`pb-2 font-medium ${activeTab === 'bookings' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600'}`"
            >
              Bookings
            </button>
            <button
              @click="activeTab = 'profile'"
              :class="`pb-2 font-medium ${activeTab === 'profile' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600'}`"
            >
              Profile
            </button>
            <button
              @click="activeTab = 'portfolio'"
              :class="`pb-2 font-medium ${activeTab === 'portfolio' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600'}`"
            >
              Portfolio
            </button>
            <button
              @click="activeTab = 'packages'"
              :class="`pb-2 font-medium ${activeTab === 'packages' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600'}`"
            >
              Packages
            </button>
            <button
              @click="activeTab = 'reviews'"
              :class="`pb-2 font-medium ${activeTab === 'reviews' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600'}`"
            >
              Reviews
            </button>
            <button
              @click="activeTab = 'competitions'"
              :class="`pb-2 font-medium ${activeTab === 'competitions' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600'}`"
            >
              My Competitions
            </button>
            <button
              @click="activeTab = 'events'"
              :class="`pb-2 font-medium ${activeTab === 'events' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600'}`"
            >
              My Events
            </button>
          </div>
        </div>

        <div class="p-6">
          <!-- Bookings Tab -->
          <div v-if="activeTab === 'bookings'">
            <h2 class="text-xl font-bold mb-4">Recent Bookings</h2>
            <div v-if="bookings.length === 0" class="text-center py-12 text-gray-600">
              No bookings yet
            </div>
            <div v-else class="space-y-4">
              <div
                v-for="booking in bookings"
                :key="booking.id"
                class="border rounded-lg p-4 hover:bg-gray-50"
              >
                <div class="flex justify-between items-start mb-2">
                  <div>
                    <h3 class="font-bold">{{ booking.client?.name || 'Unknown Client' }}</h3>
                    <p class="text-sm text-gray-600">{{ booking.event_location }}</p>
                  </div>
                  <span
                    :class="`px-3 py-1 rounded-full text-xs ${getBookingStatusClass(booking.status)}`"
                  >
                    {{ booking.status }}
                  </span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <span class="text-gray-600">Event Date:</span>
                    <span class="ml-2 font-semibold">{{ formatDate(booking.event_date) }}</span>
                  </div>
                  <div>
                    <span class="text-gray-600">Budget:</span>
                    <span class="ml-2 font-semibold">৳{{ booking.budget_min }} - ৳{{ booking.budget_max }}</span>
                  </div>
                </div>
                <div v-if="booking.status === 'pending'" class="flex gap-2 mt-3">
                  <button 
                    @click="updateBookingStatus(booking.id, 'confirmed')"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm"
                  >
                    Accept
                  </button>
                  <button 
                    @click="updateBookingStatus(booking.id, 'rejected')"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm"
                  >
                    Decline
                  </button>
                </div>
                <div v-else-if="booking.status === 'confirmed'" class="flex gap-2 mt-3">
                  <button 
                    @click="updateBookingStatus(booking.id, 'completed')"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm"
                  >
                    Mark Completed
                  </button>
                  <button 
                    @click="updateBookingStatus(booking.id, 'cancelled')"
                    class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm"
                  >
                    Cancel
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Profile Tab -->
          <div v-if="activeTab === 'profile'">
            <h2 class="text-xl font-bold mb-4">Profile Settings</h2>
            <div class="space-y-4">
              <div>
                <ImageUpload
                  label="Profile Picture"
                  placeholder="Upload your profile picture"
                  @upload="handleProfileImageUpload"
                />
              </div>
              <div>
                <label class="block text-sm font-medium mb-2">Bio</label>
                <textarea
                  v-model="profileForm.bio"
                  rows="4"
                  class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                  placeholder="Tell clients about yourself..."
                ></textarea>
              </div>
              <div>
                <label class="block text-sm font-medium mb-2">Experience (Years)</label>
                <input
                  v-model.number="profileForm.experience_years"
                  type="number"
                  class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                />
              </div>
              <button 
                @click="saveProfile"
                class="px-6 py-2 bg-burgundy text-white rounded hover:bg-[#6F112D]"
              >
                Save Changes
              </button>
            </div>
          </div>

          <!-- Portfolio Tab -->
          <div v-if="activeTab === 'portfolio'">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-xl font-bold">Portfolio Albums</h2>
              <button @click="showAlbumModal = true" class="px-4 py-2 bg-burgundy text-white rounded hover:bg-[#6F112D]">
                + Add Album
              </button>
            </div>
            
            <div v-if="albums.length === 0" class="text-center py-12 text-gray-600">
              <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <p class="text-lg font-medium mb-2">No albums yet</p>
              <p class="text-sm">Create your first album to showcase your work!</p>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div
                v-for="album in albums"
                :key="album.id"
                class="bg-white border rounded-lg overflow-hidden hover:shadow-lg transition-shadow"
              >
                <div class="h-48 bg-gray-200 relative">
                  <img
                    v-if="album.cover_photo"
                    :src="album.cover_photo"
                    :alt="album.title"
                    class="w-full h-full object-cover"
                  />
                  <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                    <button
                      @click="viewAlbum(album)"
                      class="px-4 py-2 bg-white text-burgundy rounded-lg font-medium"
                    >
                      View Album
                    </button>
                  </div>
                </div>
                <div class="p-4">
                  <h3 class="font-bold text-lg mb-1">{{ album.title }}</h3>
                  <p class="text-sm text-gray-600 mb-2">{{ album.description }}</p>
                  <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">{{ album.photo_count || 0 }} photos</span>
                    <span
                      :class="`px-2 py-1 rounded text-xs ${album.is_public ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`"
                    >
                      {{ album.is_public ? 'Public' : 'Private' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Album Modal -->
          <div
            v-if="showAlbumModal"
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
            @click.self="showAlbumModal = false"
          >
            <div class="bg-white rounded-lg max-w-md w-full p-6">
              <h3 class="text-xl font-bold mb-4">Create New Album</h3>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium mb-2">Album Name *</label>
                  <input
                    v-model="albumForm.name"
                    type="text"
                    class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                    placeholder="Wedding Photography"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium mb-2">Description</label>
                  <textarea
                    v-model="albumForm.description"
                    rows="3"
                    class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                    placeholder="Beautiful wedding moments captured..."
                  ></textarea>
                </div>
                <div class="flex items-center gap-2">
                  <input
                    v-model="albumForm.is_public"
                    type="checkbox"
                    id="is_public"
                    class="w-4 h-4 text-burgundy focus:ring-burgundy"
                  />
                  <label for="is_public" class="text-sm font-medium">Make this album public</label>
                </div>
              </div>
              <div class="flex gap-3 mt-6">
                <button
                  @click="createAlbum"
                  :disabled="!albumForm.name || creatingAlbum"
                  class="flex-1 px-4 py-2 bg-burgundy text-white rounded hover:bg-[#6F112D] disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {{ creatingAlbum ? 'Creating...' : 'Create Album' }}
                </button>
                <button
                  @click="showAlbumModal = false"
                  class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50"
                >
                  Cancel
                </button>
              </div>
            </div>
          </div>

          <!-- Album Photo Manager -->
          <AlbumPhotoManager
            v-if="selectedAlbum"
            :album="selectedAlbum"
            @close="closePhotoManager"
            @updated="closePhotoManager"
          />

          <!-- Portfolio Tab -->
          <div v-if="activeTab === 'packages'">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-xl font-bold">Service Packages</h2>
              <button @click="showPackageModal = true" class="px-4 py-2 bg-burgundy text-white rounded hover:bg-[#6F112D]">
                + Add Package
              </button>
            </div>
            
            <div v-if="packages.length === 0" class="text-center py-12 text-gray-600">
              <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
              <p class="text-lg font-medium mb-2">No packages yet</p>
              <p class="text-sm">Create service packages to attract clients!</p>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div
                v-for="pkg in packages"
                :key="pkg.id"
                class="bg-white border rounded-lg overflow-hidden hover:shadow-lg transition-shadow"
              >
                <!-- Package Cover Image -->
                <div v-if="pkg.cover_image" class="h-48 bg-gray-100">
                  <img
                    :src="pkg.cover_image"
                    :alt="pkg.name"
                    class="w-full h-full object-cover"
                  />
                </div>
                <div v-else class="h-48 bg-gradient-to-br from-burgundy/10 to-purple-100 flex items-center justify-center">
                  <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
                
                <div class="p-6">
                  <div class="flex items-start justify-between mb-4">
                    <div>
                      <h3 class="font-bold text-xl mb-1">{{ pkg.name }}</h3>
                      <p class="text-2xl font-bold text-burgundy">৳{{ pkg.price }}</p>
                    </div>
                    <span
                      :class="`px-3 py-1 rounded-full text-xs font-medium ${pkg.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`"
                    >
                      {{ pkg.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </div>
                  
                  <p class="text-sm text-gray-600 mb-4">{{ pkg.description }}</p>
                  
                  <div class="space-y-2 mb-4">
                    <div class="flex items-center gap-2 text-sm">
                      <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>{{ pkg.duration_hours }} hours coverage</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                      <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>{{ pkg.edited_photos }} edited photos</span>
                    </div>
                    <div v-if="pkg.raw_photos" class="flex items-center gap-2 text-sm">
                      <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>{{ pkg.raw_photos }} raw photos</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                      <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>{{ pkg.delivery_days }} days delivery</span>
                    </div>
                  </div>

                  <div class="flex gap-2">
                    <button
                      @click="editPackage(pkg)"
                      class="flex-1 px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                    >
                      Edit
                    </button>
                    <button
                      @click="deletePackage(pkg)"
                      class="px-3 py-2 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200"
                    >
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Package Modal -->
          <div
            v-if="showPackageModal"
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
            @click.self="closePackageModal"
          >
            <div class="bg-white rounded-lg max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
              <h3 class="text-xl font-bold mb-4">{{ editingPackage ? 'Edit Package' : 'Create New Package' }}</h3>
              <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium mb-2">Package Name *</label>
                    <input
                      v-model="packageForm.name"
                      type="text"
                      class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                      placeholder="Basic Package"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-2">Price (৳) *</label>
                    <input
                      v-model.number="packageForm.price"
                      type="number"
                      class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                      placeholder="15000"
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium mb-2">Description *</label>
                  <textarea
                    v-model="packageForm.description"
                    rows="3"
                    class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                    placeholder="Perfect for small events and gatherings..."
                  ></textarea>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded p-4">
                  <p class="text-sm font-medium mb-2">📸 Package Images (Optional)</p>
                  <div class="space-y-3">
                    <div>
                      <label class="block text-xs text-gray-600 mb-1">Cover Image URL</label>
                      <input
                        v-model="packageForm.cover_image"
                        type="url"
                        class="w-full border rounded px-3 py-2 text-sm focus:ring-2 focus:ring-purple-600"
                        placeholder="https://images.pexels.com/photos/..."
                      />
                      <p class="text-xs text-gray-500 mt-1">Add a Pexels or external image URL</p>
                    </div>
                    <div v-if="packageForm.cover_image" class="mt-2">
                      <img :src="packageForm.cover_image" alt="Cover preview" class="w-32 h-32 object-cover rounded" />
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <div>
                    <label class="block text-sm font-medium mb-2">Coverage Hours *</label>
                    <input
                      v-model.number="packageForm.duration_hours"
                      type="number"
                      class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                      placeholder="4"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-2">Edited Photos *</label>
                    <input
                      v-model.number="packageForm.edited_photos"
                      type="number"
                      class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                      placeholder="50"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-2">Raw Photos</label>
                    <input
                      v-model.number="packageForm.raw_photos"
                      type="number"
                      class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                      placeholder="0"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-2">Delivery Days *</label>
                    <input
                      v-model.number="packageForm.delivery_days"
                      type="number"
                      class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-burgundy"
                      placeholder="7"
                    />
                  </div>
                </div>

                <div class="flex items-center gap-2">
                  <input
                    v-model="packageForm.is_active"
                    type="checkbox"
                    id="pkg_is_active"
                    class="w-4 h-4 text-burgundy focus:ring-burgundy"
                  />
                  <label for="pkg_is_active" class="text-sm font-medium">Make this package active</label>
                </div>
              </div>
              
              <div class="flex gap-3 mt-6">
                <button
                  @click="savePackage"
                  :disabled="!packageForm.name || !packageForm.price || creatingPackage"
                  class="flex-1 px-4 py-2 bg-burgundy text-white rounded hover:bg-[#6F112D] disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {{ creatingPackage ? 'Saving...' : (editingPackage ? 'Update Package' : 'Create Package') }}
                </button>
                <button
                  @click="closePackageModal"
                  class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50"
                >
                  Cancel
                </button>
              </div>
            </div>
          </div>

          <!-- Reviews Tab -->
          <div v-if="activeTab === 'reviews'">
            <h2 class="text-xl font-bold mb-4">Client Reviews</h2>
            <div class="text-center py-12 text-gray-600">
              No reviews yet
            </div>
          </div>

          <!-- Competitions Tab -->
          <div v-if="activeTab === 'competitions'">
            <div class="mb-6">
              <h2 class="text-xl font-bold">My Competition Submissions</h2>
              <p class="text-sm text-gray-600 mt-1">View and manage your submissions to photography competitions</p>
            </div>
            <div class="space-y-4">
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                  <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <div>
                    <h3 class="font-semibold text-blue-900">Participate in Competitions</h3>
                    <p class="text-sm text-blue-700 mt-1">Submit your best work to photography competitions and compete for prizes.</p>
                    <ul class="mt-2 text-sm text-blue-700 space-y-1">
                      <li>• Browse active competitions in the Competitions page</li>
                      <li>• Submit your best photographs</li>
                      <li>• Get public votes and judge ratings</li>
                      <li>• Win prizes and build your reputation</li>
                    </ul>
                    <a href="/competitions" class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                      Browse Competitions
                    </a>
                  </div>
                </div>
              </div>
              <div class="text-center py-12 text-gray-600">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                <p class="text-lg font-medium mb-2">No competitions yet</p>
                <p class="text-sm mb-4">Create your first competition to get started</p>
              </div>
            </div>
          </div>

          <!-- Events Tab -->
          <div v-if="activeTab === 'events'">
            <div class="mb-6">
              <h2 class="text-xl font-bold">My Events</h2>
              <p class="text-sm text-gray-600 mt-1">Events you've registered for or attended</p>
            </div>

            <!-- Events coming soon notice -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                  <h3 class="font-semibold text-blue-900">Participate in Events</h3>
                  <p class="text-sm text-blue-700 mt-1">Browse photography events, workshops, and meetups organized by admins.</p>
                  <a href="/events" class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    Browse Events
                  </a>
                </div>
              </div>
            </div>

            <!-- Create Event Form -->
            <div v-if="showEventForm" class="bg-white border rounded-lg p-6 mb-6">
              <h3 class="text-lg font-semibold mb-4">Create New Event</h3>
              <form @submit.prevent="createEvent" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Event Title *</label>
                  <input
                    v-model="eventForm.title"
                    type="text"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                    placeholder="Photography Workshop 2026"
                  />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Type *</label>
                    <select
                      v-model="eventForm.event_type"
                      required
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                    >
                      <option value="">Select type</option>
                      <option value="workshop">Workshop</option>
                      <option value="exhibition">Exhibition</option>
                      <option value="meetup">Meetup</option>
                      <option value="competition">Competition</option>
                      <option value="seminar">Seminar</option>
                      <option value="other">Other</option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Date *</label>
                    <input
                      v-model="eventForm.event_date"
                      type="datetime-local"
                      required
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                  <textarea
                    v-model="eventForm.description"
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                    placeholder="Describe your event..."
                  ></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                    <input
                      v-model="eventForm.location"
                      type="text"
                      required
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                      placeholder="123 Main St"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                    <select
                      v-model="eventForm.city_id"
                      required
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                    >
                      <option value="">Select city</option>
                      <option v-for="city in cities" :key="city.id" :value="city.id">
                        {{ city.name }}
                      </option>
                    </select>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Attendees</label>
                    <input
                      v-model.number="eventForm.max_attendees"
                      type="number"
                      min="1"
                      max="500"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                      placeholder="50"
                    />
                    <p class="text-xs text-gray-500 mt-1">Max 500 for photographers</p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ticket Price (৳)</label>
                    <input
                      v-model.number="eventForm.ticket_price"
                      type="number"
                      min="0"
                      max="50000"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                      placeholder="500"
                    />
                    <p class="text-xs text-gray-500 mt-1">Max ৳50,000</p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duration (hours)</label>
                    <input
                      v-model.number="eventForm.duration_hours"
                      type="number"
                      min="0.5"
                      max="24"
                      step="0.5"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                      placeholder="2.5"
                    />
                    <p class="text-xs text-gray-500 mt-1">Max 24 hours</p>
                  </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                  <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm text-yellow-800">
                      <p class="font-medium">Event Approval Required</p>
                      <p>Your event will be saved as a draft and requires admin approval before going live. You'll be notified once approved.</p>
                    </div>
                  </div>
                </div>

                <div class="flex justify-end gap-3">
                  <button
                    type="button"
                    @click="showEventForm = false"
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    :disabled="creatingEvent"
                    class="px-6 py-2 bg-burgundy text-white rounded-lg hover:bg-[#6F112D] disabled:opacity-50"
                  >
                    {{ creatingEvent ? 'Creating...' : 'Create Event' }}
                  </button>
                </div>
              </form>
            </div>

            <!-- Events List -->
            <div v-if="events.length === 0 && !showEventForm" class="text-center py-12 text-gray-600">
              <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <p class="text-lg font-medium mb-2">No events yet</p>
              <p class="text-sm mb-4">Create your first event to get started</p>
            </div>

            <div v-else class="space-y-4">
              <div
                v-for="event in events"
                :key="event.id"
                class="bg-white border rounded-lg p-6 hover:shadow-md transition-shadow"
              >
                <div class="flex justify-between items-start mb-3">
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                      <h3 class="text-lg font-bold">{{ event.title }}</h3>
                      <span
                        :class="{
                          'px-2 py-1 rounded text-xs font-medium': true,
                          'bg-yellow-100 text-yellow-800': event.status === 'draft',
                          'bg-green-100 text-green-800': event.status === 'published',
                          'bg-red-100 text-red-800': event.status === 'cancelled'
                        }"
                      >
                        {{ event.status }}
                      </span>
                    </div>
                    <p class="text-sm text-gray-600">{{ event.event_type }} • {{ event.location }}</p>
                  </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-4">
                  <div>
                    <span class="text-gray-600">Date:</span>
                    <p class="font-medium">{{ formatDate(event.event_date) }}</p>
                  </div>
                  <div>
                    <span class="text-gray-600">RSVPs:</span>
                    <p class="font-medium">{{ event.rsvp_count || 0 }}</p>
                  </div>
                  <div>
                    <span class="text-gray-600">Views:</span>
                    <p class="font-medium">{{ event.view_count || 0 }}</p>
                  </div>
                  <div>
                    <span class="text-gray-600">Price:</span>
                    <p class="font-medium">{{ event.ticket_price ? `৳${event.ticket_price}` : 'Free' }}</p>
                  </div>
                </div>

                <div class="flex gap-2">
                  <button
                    @click="viewEvent(event)"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm"
                  >
                    View
                  </button>
                  <button
                    v-if="event.status === 'draft'"
                    @click="editEvent(event)"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm"
                  >
                    Edit
                  </button>
                  <button
                    v-if="event.status === 'published'"
                    @click="cancelEvent(event)"
                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm"
                  >
                    Cancel Event
                  </button>
                  <button
                    v-if="event.status === 'draft' && (event.rsvp_count === 0 || !event.rsvp_count)"
                    @click="deleteEvent(event)"
                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
          <button @click="$router.push('/competitions')" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition-all group">
            <svg class="w-8 h-8 text-gray-600 group-hover:text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
            <span class="text-sm font-medium text-center">Browse Competitions</span>
          </button>

          <button @click="$router.push('/events')" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition-all group">
            <svg class="w-8 h-8 text-gray-600 group-hover:text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="text-sm font-medium text-center">View Events</span>
          </button>

          <button @click="$router.push('/transactions')" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition-all group">
            <svg class="w-8 h-8 text-gray-600 group-hover:text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="text-sm font-medium text-center">Transactions</span>
          </button>

          <button @click="$router.push('/notifications')" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition-all group">
            <svg class="w-8 h-8 text-gray-600 group-hover:text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="text-sm font-medium text-center">Notifications</span>
          </button>

          <button @click="$router.push('/')" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition-all group">
            <svg class="w-8 h-8 text-gray-600 group-hover:text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span class="text-sm font-medium text-center">Search Photographers</span>
          </button>

          <button @click="$router.push('/help')" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition-all group">
            <svg class="w-8 h-8 text-gray-600 group-hover:text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-medium text-center">Help & Support</span>
          </button>

          <button @click="$router.push('/settings')" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition-all group">
            <svg class="w-8 h-8 text-gray-600 group-hover:text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="text-sm font-medium text-center">Account Settings</span>
          </button>

          <button @click="logout" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition-all group">
            <svg class="w-8 h-8 text-gray-600 group-hover:text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="text-sm font-medium text-center">Logout</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../api';
import ImageUpload from './ImageUpload.vue';
import AlbumPhotoManager from './AlbumPhotoManager.vue';

const user = ref(null);
const activeTab = ref('bookings');
const stats = ref({});
const bookings = ref([]);
const showEventForm = ref(false);
const creatingEvent = ref(false);
const events = ref([]);
const cities = ref([]);
const showAlbumModal = ref(false);
const showPackageModal = ref(false);
const copied = ref(false);
const albums = ref([]);
const creatingAlbum = ref(false);
const selectedAlbum = ref(null);
const packages = ref([]);
const creatingPackage = ref(false);
const editingPackage = ref(null);

const albumForm = ref({
  name: '',
  description: '',
  is_public: true,
});

const packageForm = ref({
  name: '',
  description: '',
  price: 0,
  duration_hours: 0,
  edited_photos: 0,
  raw_photos: 0,
  delivery_days: 7,
  is_active: true,
  cover_image: '',
  sample_images: [],
});

const eventForm = ref({
  title: '',
  event_type: '',
  description: '',
  event_date: '',
  location: '',
  city_id: '',
  max_attendees: null,
  ticket_price: 0,
  duration_hours: null,
});

const profileForm = ref({
  bio: '',
  experience_years: 0,
});

const profileUrl = computed(() => {
  const slug = user.value?.photographer?.slug;
  if (!slug) return window.location.origin + '/photographer/your-username';
  return window.location.origin + '/photographer/' + slug;
});

const copyProfileLink = async () => {
  try {
    await navigator.clipboard.writeText(profileUrl.value);
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 2000);
  } catch (error) {
    console.error('Failed to copy:', error);
    // Fallback for older browsers
    const input = document.createElement('input');
    input.value = profileUrl.value;
    document.body.appendChild(input);
    input.select();
    document.execCommand('copy');
    document.body.removeChild(input);
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 2000);
  }
};

const fetchDashboardData = async () => {
  try {
    const { data: userData } = await api.get('/auth/me');
    if (userData.status === 'success') {
      user.value = userData.data;
      
      // Load photographer data into profile form
      if (user.value.photographer) {
        profileForm.value.bio = user.value.photographer.bio || '';
        profileForm.value.experience_years = user.value.photographer.experience_years || 0;
      }
    }

    // Fetch dashboard stats from API
    try {
      const { data: dashboardData } = await api.get('/photographer/dashboard');
      if (dashboardData.status === 'success') {
        stats.value = dashboardData.data.stats;
        bookings.value = dashboardData.data.bookings || [];
      }
    } catch (error) {
      console.error('Error fetching dashboard stats:', error);
      // Fallback to basic bookings
      const { data: bookingsData } = await api.get('/bookings');
      if (bookingsData.status === 'success') {
        bookings.value = bookingsData.data;
      }

      // Fallback stats
      stats.value = {
        total_bookings: bookings.value.length,
        pending_bookings: bookings.value.filter(b => b.status === 'pending').length,
        average_rating: 0,
        total_revenue: 0,
      };
    }
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
  }
};

const getBookingStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-green-100 text-green-800',
    completed: 'bg-blue-100 text-blue-800',
    cancelled: 'bg-red-100 text-red-800',
    rejected: 'bg-gray-100 text-gray-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const updateBookingStatus = async (bookingId, newStatus) => {
  try {
    const { data } = await api.patch(`/bookings/${bookingId}/status`, { status: newStatus });
    
    if (data.status === 'success') {
      // Update local booking list
      const booking = bookings.value.find(b => b.id === bookingId);
      if (booking) {
        booking.status = newStatus;
      }
      
      // Refresh stats
      stats.value.pending_bookings = bookings.value.filter(b => b.status === 'pending').length;
    }
  } catch (error) {
    console.error('Error updating booking status:', error);
    alert('Failed to update booking status');
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });
};

const handleProfileImageUpload = async (file) => {
  const formData = new FormData();
  formData.append('avatar', file);

  try {
    const { data } = await api.post('/photographer/profile/avatar', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    if (data.status === 'success') {
      alert('Profile picture updated successfully!');
      fetchDashboardData();
    }
  } catch (error) {
    console.error('Error uploading image:', error);
    alert('Failed to upload image');
  }
};

const saveProfile = async () => {
  try {
    const { data } = await api.patch('/photographer/profile', profileForm.value);
    
    if (data.status === 'success') {
      alert('Profile updated successfully!');
      fetchDashboardData();
    }
  } catch (error) {
    console.error('Error saving profile:', error);
    alert('Failed to save profile');
  }
};

// Event Management Functions
const fetchCities = async () => {
  try {
    const { data } = await api.get('/cities');
    cities.value = data.data || data;
  } catch (error) {
    console.error('Error fetching cities:', error);
  }
};

const fetchEvents = async () => {
  try {
    const { data } = await api.get('/photographer/events');
    events.value = data.data || data;
  } catch (error) {
    console.error('Error fetching events:', error);
  }
};

const createEvent = async () => {
  creatingEvent.value = true;
  try {
    // Get photographer ID from user
    const photographerId = user.value.photographer?.id;
    if (!photographerId) {
      alert('Photographer profile not found. Please complete your profile first.');
      return;
    }

    const payload = {
      ...eventForm.value,
      organizer_id: photographerId,
    };

    const { data } = await api.post('/photographer/events', payload);
    
    if (data.status === 'success') {
      alert('Event created successfully! It will be reviewed by admin before going live.');
      showEventForm.value = false;
      
      // Reset form
      eventForm.value = {
        title: '',
        event_type: '',
        description: '',
        event_date: '',
        location: '',
        city_id: '',
        max_attendees: null,
        ticket_price: 0,
        duration_hours: null,
      };
      
      fetchEvents();
    }
  } catch (error) {
    console.error('Error creating event:', error);
    alert(error.response?.data?.message || 'Failed to create event');
  } finally {
    creatingEvent.value = false;
  }
};

const viewEvent = (event) => {
  window.open(`/events/${event.slug}`, '_blank');
};

const editEvent = (event) => {
  // TODO: Implement edit form
  alert('Edit functionality coming soon. For now, please contact admin to make changes.');
};

const cancelEvent = async (event) => {
  if (!confirm('Are you sure you want to cancel this event? Attendees will be notified.')) {
    return;
  }

  try {
    const { data } = await api.post(`/photographer/events/${event.id}/cancel`);
    
    if (data.status === 'success') {
      alert('Event cancelled successfully');
      fetchEvents();
    }
  } catch (error) {
    console.error('Error cancelling event:', error);
    alert(error.response?.data?.message || 'Failed to cancel event');
  }
};

const deleteEvent = async (event) => {
  if (!confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
    return;
  }

  try {
    const { data } = await api.delete(`/photographer/events/${event.id}`);
    
    if (data.status === 'success') {
      alert('Event deleted successfully');
      fetchEvents();
    }
  } catch (error) {
    console.error('Error deleting event:', error);
    alert(error.response?.data?.message || 'Failed to delete event');
  }
};

// Album Management Functions
const fetchAlbums = async () => {
  try {
    const { data } = await api.get('/photographer/albums');
    albums.value = data.data || [];
  } catch (error) {
    console.error('Error fetching albums:', error);
  }
};

const createAlbum = async () => {
  if (!albumForm.value.title) {
    alert('Please enter an album title');
    return;
  }

  creatingAlbum.value = true;
  try {
    const { data } = await api.post('/photographer/albums', albumForm.value);
    
    if (data.status === 'success') {
      alert('Album created successfully!');
      showAlbumModal.value = false;
      
      // Reset form
      albumForm.value = {
        title: '',
        description: '',
        is_public: true,
      };
      
      fetchAlbums();
    }
  } catch (error) {
    console.error('Error creating album:', error);
    alert(error.response?.data?.message || 'Failed to create album');
  } finally {
    creatingAlbum.value = false;
  }
};

const viewAlbum = (album) => {
  selectedAlbum.value = album;
};

const closePhotoManager = () => {
  selectedAlbum.value = null;
  fetchAlbums(); // Refresh album list to update photo counts
};

// Package Management Functions
const fetchPackages = async () => {
  try {
    const { data } = await api.get('/photographer/packages');
    packages.value = data.data || [];
  } catch (error) {
    console.error('Error fetching packages:', error);
  }
};

const savePackage = async () => {
  if (!packageForm.value.name || !packageForm.value.price) {
    alert('Please fill in required fields (name and price)');
    return;
  }

  creatingPackage.value = true;
  try {
    const endpoint = editingPackage.value 
      ? `/photographer/packages/${editingPackage.value.id}`
      : '/photographer/packages';
    
    const method = editingPackage.value ? 'put' : 'post';
    
    const { data } = await api[method](endpoint, packageForm.value);
    
    if (data.status === 'success') {
      alert(editingPackage.value ? 'Package updated successfully!' : 'Package created successfully!');
      closePackageModal();
      fetchPackages();
    }
  } catch (error) {
    console.error('Error saving package:', error);
    alert(error.response?.data?.message || 'Failed to save package');
  } finally {
    creatingPackage.value = false;
  }
};

const editPackage = (pkg) => {
  editingPackage.value = pkg;
  packageForm.value = {
    name: pkg.name,
    description: pkg.description,
    price: pkg.price,
    duration_hours: pkg.duration_hours,
    edited_photos: pkg.edited_photos,
    raw_photos: pkg.raw_photos || 0,
    delivery_days: pkg.delivery_days,
    is_active: pkg.is_active,
    cover_image: pkg.cover_image || '',
    sample_images: pkg.sample_images || [],
  };
  showPackageModal.value = true;
};

const deletePackage = async (pkg) => {
  if (!confirm(`Are you sure you want to delete "${pkg.name}"?`)) {
    return;
  }

  try {
    const { data } = await api.delete(`/photographer/packages/${pkg.id}`);
    
    if (data.status === 'success') {
      alert('Package deleted successfully');
      fetchPackages();
    }
  } catch (error) {
    console.error('Error deleting package:', error);
    alert(error.response?.data?.message || 'Failed to delete package');
  }
};

const closePackageModal = () => {
  showPackageModal.value = false;
  editingPackage.value = null;
  packageForm.value = {
    name: '',
    description: '',
    price: 0,
    duration_hours: 0,
    edited_photos: 0,
    raw_photos: 0,
    delivery_days: 7,
    is_active: true,
    cover_image: '',
    sample_images: [],
  };
};

onMounted(() => {
  fetchDashboardData();
  fetchCities();
  fetchEvents();
  fetchAlbums();
  fetchPackages();
});
</script>
