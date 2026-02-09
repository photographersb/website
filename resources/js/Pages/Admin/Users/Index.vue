<template>
  <div class="min-h-screen">
    <!-- Admin Header with Back Button & Notifications -->
    <AdminHeader 
      title="👥 User Management" 
      subtitle="Manage all platform users and their accounts"
    />

    <!-- Main Content -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <!-- Quick Navigation -->
      <AdminQuickNav />

      <section class="page-hero">
        <div class="hero-copy">
          <p class="hero-kicker">USER REGISTRY</p>
          <h1 class="hero-title">Every account, always visible.</h1>
          <p class="hero-subtitle">
            Manage roles, access, and growth signals across the platform.
          </p>
          <div class="hero-actions">
            <button
              class="btn-admin-primary"
              @click="showAddModal = true"
            >
              Add User
            </button>
            <button
              class="btn-admin-secondary"
              @click="fetchUsers"
            >
              Refresh List
            </button>
          </div>
        </div>
        <div class="hero-status">
          <div class="status-card">
            <span class="status-label">Total Users</span>
            <span class="status-value">{{ stats.total || 0 }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Active</span>
            <span class="status-value">{{ stats.active || 0 }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Suspended</span>
            <span class="status-value">{{ stats.suspended || 0 }}</span>
          </div>
        </div>
      </section>

      <div class="page-topbar">
        <div class="status-chip">
          Photographers: {{ stats.photographers || 0 }}
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card stat-blue">
          <div class="stat-icon">
            <svg
              class="w-8 h-8"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
              />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Total Users</span>
            <span class="stat-value">{{ stats.total }}</span>
          </div>
        </div>

        <div class="stat-card stat-green">
          <div class="stat-icon">
            <svg
              class="w-8 h-8"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Active Users</span>
            <span class="stat-value">{{ stats.active }}</span>
          </div>
        </div>

        <div class="stat-card stat-purple">
          <div class="stat-icon">
            <svg
              class="w-8 h-8"
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
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Photographers</span>
            <span class="stat-value">{{ stats.photographers }}</span>
          </div>
        </div>

        <div class="stat-card stat-red">
          <div class="stat-icon">
            <svg
              class="w-8 h-8"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"
              />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Suspended</span>
            <span class="stat-value">{{ stats.suspended }}</span>
          </div>
        </div>
      </div>

      <!-- Filters & Search -->
      <div class="content-card">
        <div class="filters-bar">
          <div class="search-box">
            <svg
              class="search-icon"
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
            <input 
              v-model="filters.search" 
              type="text"
              placeholder="Search by name or email..." 
              class="search-input" 
              @input="debounceSearch"
            >
          </div>

          <select
            v-model="filters.role"
            class="filter-select"
            @change="fetchUsers"
          >
            <option value="">
              All Roles
            </option>
            <option value="client">
              Client
            </option>
            <option value="photographer">
              Photographer
            </option>
            <option value="admin">
              Admin
            </option>
            <option value="super_admin">
              Super Admin
            </option>
          </select>

          <button
            class="btn-export"
            @click="exportUsers"
          >
            <svg
              class="w-5 h-5 mr-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
            Export
          </button>
        </div>

        <!-- Loading State -->
        <div
          v-if="loading"
          class="loading-state"
        >
          <div class="spinner" />
          <p>Loading users...</p>
        </div>

        <!-- Users Table -->
        <div
          v-else
          class="table-container"
        >
          <table class="data-table">
            <thead>
              <tr>
                <th>User</th>
                <th>Contact</th>
                <th>Role</th>
                <th>Status</th>
                <th>Verified</th>
                <th>Joined</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="users.length === 0">
                <td
                  colspan="7"
                  class="empty-state"
                >
                  <div class="empty-icon">
                    👥
                  </div>
                  <p>No users found</p>
                </td>
              </tr>
              <tr
                v-for="user in users"
                :key="user.id"
                class="user-row"
              >
                <td>
                  <div class="user-cell">
                    <div class="user-avatar">
                      {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                      <div class="user-name">
                        {{ user.name }}
                      </div>
                      <div class="user-id">
                        ID: {{ user.id }}
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="contact-cell">
                    <div class="email">
                      {{ user.email }}
                    </div>
                    <div class="phone">
                      {{ user.phone || 'N/A' }}
                    </div>
                  </div>
                </td>
                <td>
                  <span
                    class="badge"
                    :class="'badge-' + user.role"
                  >
                    {{ formatRole(user.role) }}
                  </span>
                </td>
                <td>
                  <span
                    v-if="user.is_suspended"
                    class="badge badge-danger"
                  >Suspended</span>
                  <span
                    v-else
                    class="badge badge-success"
                  >Active</span>
                </td>
                <td>
                  <span
                    v-if="user.email_verified_at"
                    class="verified-badge"
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
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                    Verified
                  </span>
                  <span
                    v-else
                    class="unverified-badge"
                  >Not Verified</span>
                </td>
                <td>{{ formatDate(user.created_at) }}</td>
                <td>
                  <div class="action-buttons">
                    <button
                      class="btn-action"
                      title="View Details"
                      @click="viewUser(user)"
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
                    </button>
                    <button
                      class="btn-action"
                      title="Edit User"
                      @click="editUser(user)"
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
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                        />
                      </svg>
                    </button>
                    <button
                      v-if="!user.mentor"
                      class="btn-action btn-mentor"
                      title="Promote to Mentor"
                      @click="showPromoteToMentor(user)"
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
                          d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                        />
                      </svg>
                    </button>
                    <button
                      v-if="!user.judge"
                      class="btn-action btn-judge"
                      title="Promote to Judge"
                      @click="showPromoteToJudge(user)"
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
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                        />
                      </svg>
                    </button>
                    <button
                      v-if="!user.is_suspended"
                      class="btn-action btn-warning"
                      title="Suspend User"
                      @click="suspendUser(user)"
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
                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"
                        />
                      </svg>
                    </button>
                    <button
                      v-else
                      class="btn-action btn-success"
                      title="Unsuspend User"
                      @click="unsuspendUser(user)"
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
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                      </svg>
                    </button>
                    <button
                      class="btn-action btn-danger"
                      title="Delete User"
                      @click="deleteUser(user)"
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
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                        />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div
          v-if="stats.total > 0"
          class="pagination"
        >
          <div class="pagination-info">
            Showing {{ users.length }} of {{ stats.total }} users
          </div>
          <div class="pagination-controls">
            <button 
              :disabled="pagination.currentPage === 1"
              class="pagination-btn"
              @click="changePage(pagination.currentPage - 1)"
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
                  d="M15 19l-7-7 7-7"
                />
              </svg>
              Previous
            </button>
          
            <div class="pagination-pages">
              <span class="pagination-current">Page {{ pagination.currentPage }} of {{ pagination.totalPages }}</span>
            </div>
          
            <button 
              :disabled="pagination.currentPage === pagination.totalPages"
              class="pagination-btn"
              @click="changePage(pagination.currentPage + 1)"
            >
              Next
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
        </div>
      </div>
    </div>

    <!-- View User Modal -->
    <div
      v-if="showViewModal"
      class="modal-overlay"
      @click.self="showViewModal = false"
    >
      <div class="modal">
        <div class="modal-header">
          <h3>User Details</h3>
          <button
            class="modal-close"
            @click="showViewModal = false"
          >
            ×
          </button>
        </div>
        <div class="modal-body">
          <div
            v-if="loadingUserDetails"
            class="loading-state-inline"
          >
            <div class="spinner-small" />
            <p>Loading user details...</p>
          </div>
          <div
            v-else-if="selectedUser"
            class="detail-grid"
          >
            <div class="detail-item">
              <span class="detail-label">Name:</span>
              <span class="detail-value">{{ selectedUser.name }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Email:</span>
              <span class="detail-value">{{ selectedUser.email }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Phone:</span>
              <span class="detail-value">{{ selectedUser.phone || 'N/A' }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Role:</span>
              <span
                class="badge"
                :class="'badge-' + selectedUser.role"
              >{{ formatRole(selectedUser.role) }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Status:</span>
              <span
                v-if="selectedUser.is_suspended"
                class="badge badge-danger"
              >Suspended</span>
              <span
                v-else
                class="badge badge-success"
              >Active</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Email Verified:</span>
              <span>{{ selectedUser.email_verified_at ? 'Yes' : 'No' }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Joined:</span>
              <span>{{ formatDate(selectedUser.created_at) }}</span>
            </div>
            <div class="detail-item full-width">
              <span class="detail-label">Additional Roles:</span>
              <div class="role-badges">
                <span
                  v-if="selectedUser.photographer"
                  class="badge badge-photographer"
                >
                  📷 Photographer
                </span>
                <span
                  v-if="selectedUser.mentor"
                  class="badge badge-mentor"
                >
                  🎓 Mentor
                </span>
                <span
                  v-if="selectedUser.judge"
                  class="badge badge-judge"
                >
                  ⚖️ Judge
                </span>
                <span
                  v-if="!selectedUser.photographer && !selectedUser.mentor && !selectedUser.judge"
                  class="text-muted"
                >
                  None yet
                </span>
              </div>
            </div>

            <!-- Photographer Profile Data -->
            <div
              v-if="selectedUser.photographer"
              class="detail-item full-width"
              style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;"
            >
              <span
                class="detail-label"
                style="font-size: 1.1em; font-weight: 600;"
              >📷 Photographer Profile</span>
              
              <!-- Basic Info Section -->
              <div style="margin-top: 12px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                <div class="detail-item">
                  <span class="detail-label">Slug/Username:</span>
                  <span class="detail-value">{{ selectedUser.photographer.slug || 'N/A' }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Verified:</span>
                  <span>{{ selectedUser.photographer.is_verified ? '✅ Yes' : '❌ No' }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Experience:</span>
                  <span class="detail-value">{{ selectedUser.photographer.experience_years || 0 }} years</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Service Radius:</span>
                  <span class="detail-value">{{ selectedUser.photographer.service_area_radius || 'N/A' }} km</span>
                </div>
                <div class="detail-item full-width">
                  <span class="detail-label">Bio:</span>
                  <span
                    class="detail-value"
                    style="white-space: pre-wrap;"
                  >{{ selectedUser.photographer.bio || 'N/A' }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Location:</span>
                  <span class="detail-value">{{ selectedUser.photographer.location || 'N/A' }}</span>
                </div>
              </div>

              <!-- Specializations & Categories -->
              <div
                v-if="selectedUser.photographer.specializations || selectedUser.photographer.categories"
                style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #f3f4f6;"
              >
                <div
                  v-if="selectedUser.photographer.specializations"
                  class="detail-item full-width"
                >
                  <span class="detail-label">Specializations:</span>
                  <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px;">
                    <span
                      v-for="spec in selectedUser.photographer.specializations"
                      v-if="selectedUser.photographer.specializations && selectedUser.photographer.specializations.length > 0"
                      :key="spec"
                      class="badge"
                      style="background-color: #dcfce7; color: #166534;"
                    >
                      {{ spec }}
                    </span>
                    <span
                      v-else
                      class="text-muted"
                    >None</span>
                  </div>
                </div>
                <div
                  v-if="selectedUser.photographer.categories"
                  class="detail-item full-width"
                >
                  <span class="detail-label">Categories:</span>
                  <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px;">
                    <span
                      v-for="cat in selectedUser.photographer.categories"
                      v-if="selectedUser.photographer.categories && selectedUser.photographer.categories.length > 0"
                      :key="cat.id"
                      class="badge"
                      style="background-color: #f3e8ff; color: #6b21a8;"
                    >
                      {{ cat.name }}
                    </span>
                    <span
                      v-else
                      class="text-muted"
                    >None</span>
                  </div>
                </div>
              </div>

              <!-- Social Links & Web -->
              <div
                v-if="selectedUser.photographer.website_url || selectedUser.photographer.instagram_url || selectedUser.photographer.facebook_url"
                style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #f3f4f6;"
              >
                <div class="detail-label">
                  Links & Social Media:
                </div>
                <div style="margin-top: 8px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px;">
                  <div
                    v-if="selectedUser.photographer.website_url"
                    style="padding: 8px; background: #f0f9ff; border-radius: 4px;"
                  >
                    <span style="font-size: 0.85em; color: #0369a1; font-weight: 600;">🌐 Website:</span><br>
                    <a
                      :href="selectedUser.photographer.website_url"
                      target="_blank"
                      style="color: #0284c7; text-decoration: underline; word-break: break-all; font-size: 0.9em;"
                    >
                      {{ selectedUser.photographer.website_url }}
                    </a>
                  </div>
                  <div
                    v-if="selectedUser.photographer.instagram_url"
                    style="padding: 8px; background: #fce7f3; border-radius: 4px;"
                  >
                    <span style="font-size: 0.85em; color: #be185d; font-weight: 600;">📷 Instagram:</span><br>
                    <a
                      :href="selectedUser.photographer.instagram_url"
                      target="_blank"
                      style="color: #db2777; text-decoration: underline; word-break: break-all; font-size: 0.9em;"
                    >
                      {{ selectedUser.photographer.instagram_url }}
                    </a>
                  </div>
                  <div
                    v-if="selectedUser.photographer.facebook_url"
                    style="padding: 8px; background: #eff6ff; border-radius: 4px;"
                  >
                    <span style="font-size: 0.85em; color: #1e40af; font-weight: 600;">f Facebook:</span><br>
                    <a
                      :href="selectedUser.photographer.facebook_url"
                      target="_blank"
                      style="color: #1d4ed8; text-decoration: underline; word-break: break-all; font-size: 0.9em;"
                    >
                      {{ selectedUser.photographer.facebook_url }}
                    </a>
                  </div>
                  <div
                    v-if="selectedUser.photographer.twitter_url"
                    style="padding: 8px; background: #e0f2fe; border-radius: 4px;"
                  >
                    <span style="font-size: 0.85em; color: #0555b3; font-weight: 600;">𝕏 Twitter:</span><br>
                    <a
                      :href="selectedUser.photographer.twitter_url"
                      target="_blank"
                      style="color: #0369a1; text-decoration: underline; word-break: break-all; font-size: 0.9em;"
                    >
                      {{ selectedUser.photographer.twitter_url }}
                    </a>
                  </div>
                </div>
              </div>

              <!-- Stats Section -->
              <div
                v-if="selectedUser.photographer.stats || selectedUser.photographer.achievements"
                style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #f3f4f6;"
              >
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                  <div style="padding: 8px; background: #fef3c7; border-radius: 4px; text-align: center;">
                    <div style="font-weight: 600; color: #92400e; font-size: 1.2em;">
                      {{ selectedUser.photographer.average_rating || 0 }}
                    </div>
                    <div style="font-size: 0.85em; color: #b45309;">
                      Rating
                    </div>
                  </div>
                  <div style="padding: 8px; background: #dcfce7; border-radius: 4px; text-align: center;">
                    <div style="font-weight: 600; color: #166534; font-size: 1.2em;">
                      {{ selectedUser.photographer.rating_count || 0 }}
                    </div>
                    <div style="font-size: 0.85em; color: #1f8417;">
                      Reviews
                    </div>
                  </div>
                  <div style="padding: 8px; background: #e0e7ff; border-radius: 4px; text-align: center;">
                    <div style="font-weight: 600; color: #4f46e5; font-size: 1.2em;">
                      {{ selectedUser.photographer.completed_bookings || 0 }}
                    </div>
                    <div style="font-size: 0.85em; color: #4338ca;">
                      Bookings
                    </div>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div style="margin-top: 12px; display: flex; gap: 8px;">
                <button
                  class="btn-primary"
                  style="flex: 1;"
                  @click="editPhotographerProfile(selectedUser)"
                >
                  ✏️ Edit Profile
                </button>
                <button
                  class="btn-secondary"
                  style="flex: 1;"
                  @click="viewPhotographerDetails(selectedUser)"
                >
                  📊 View Details
                </button>
              </div>
            </div>
          </div>
          <div
            v-else
            class="empty-state-inline"
          >
            <p>No user data available</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Photographer Profile Modal -->
    <div
      v-if="showEditProfileModal"
      class="modal-overlay"
      @click.self="showEditProfileModal = false"
    >
      <div
        class="modal modal-large"
        style="max-height: 90vh; overflow-y: auto;"
      >
        <div class="modal-header">
          <h3>Edit Photographer Profile - {{ selectedUser?.name }}</h3>
          <button
            class="modal-close"
            @click="showEditProfileModal = false"
          >
            ×
          </button>
        </div>
        <div class="modal-body">
          <form
            class="user-form"
            @submit.prevent="savePhotographerProfile"
          >
            <!-- Bio Section with Auto-Generate -->
            <div class="form-group">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <label>Professional Bio</label>
                <div>
                  <button
                    type="button"
                    class="btn-secondary"
                    style="padding: 6px 12px; font-size: 0.9em; margin-right: 8px;"
                    @click="generateBio"
                  >
                    🔄 Generate Bio
                  </button>
                  <button
                    type="button"
                    class="btn-secondary"
                    style="padding: 6px 12px; font-size: 0.9em;"
                    @click="clearBio"
                  >
                    🗑️ Clear
                  </button>
                </div>
              </div>
              <textarea
                v-model="photographerForm.bio"
                class="form-input"
                rows="4"
                placeholder="Write a professional biography..."
                style="margin-bottom: 8px;"
              />
              
              <!-- Profile Data Preview -->
              <div
                v-if="photographerForm.experience_years || photographerForm.service_area_radius || specializations"
                style="background: #eff6ff; border: 1px solid #93c5fd; border-radius: 6px; padding: 12px; margin-bottom: 12px;"
              >
                <div style="font-size: 0.85em; color: #1e40af; font-weight: 600; margin-bottom: 8px;">
                  📊 Profile Data Being Used:
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 0.85em;">
                  <div
                    v-if="photographerForm.experience_years"
                    style="color: #1e40af;"
                  >
                    <strong>🎯 Experience:</strong> {{ photographerForm.experience_years }} years
                  </div>
                  <div
                    v-if="specializations"
                    style="color: #1e40af;"
                  >
                    <strong>📸 Specializations:</strong> {{ specializations }}
                  </div>
                  <div
                    v-if="photographerForm.location"
                    style="color: #1e40af;"
                  >
                    <strong>📍 Location:</strong> {{ photographerForm.location }}
                  </div>
                  <div
                    v-if="photographerForm.service_area_radius"
                    style="color: #1e40af;"
                  >
                    <strong>📍 Radius:</strong> {{ photographerForm.service_area_radius }} km
                  </div>
                </div>
              </div>
              
              <!-- Generated Bios - Multiple Options -->
              <div
                v-if="generatedBios.length > 0"
                style="background: #f0fdf4; border: 1px solid #86efac; border-radius: 6px; padding: 12px;"
              >
                <div style="font-size: 0.85em; color: #22863a; font-weight: 600; margin-bottom: 12px;">
                  ✨ {{ generatedBios.length }} Generated Bio Options - Pick Your Favorite:
                </div>
                <div style="display: flex; flex-direction: column; gap: 10px; max-height: 400px; overflow-y: auto;">
                  <div
                    v-for="(bio, index) in generatedBios"
                    :key="index"
                    style="background: white; border: 1px solid #86efac; border-radius: 4px; padding: 10px; cursor: pointer; transition: all 0.2s;"
                    @mouseover="$event.target.parentElement.style.background='#dcfce7'"
                    @mouseleave="$event.target.parentElement.style.background='white'"
                    @click="() => useBio(bio)"
                  >
                    <div style="font-size: 0.75em; color: #65a30d; font-weight: 600; margin-bottom: 4px;">
                      Option {{ index + 1 }} - {{ bio.style }}
                    </div>
                    <div style="font-size: 0.9em; color: #166534; line-height: 1.5;">
                      {{ bio.text }}
                    </div>
                    <button
                      type="button"
                      class="btn-success"
                      style="margin-top: 6px; padding: 4px 10px; font-size: 0.8em;"
                      @click.stop="() => useBio(bio)"
                    >
                      ✅ Use This
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Experience & Location -->
            <div class="form-row">
              <div class="form-group">
                <label>Experience (Years)</label>
                <input
                  v-model.number="photographerForm.experience_years"
                  type="number"
                  min="0"
                  class="form-input"
                  placeholder="0"
                >
              </div>
              <div class="form-group">
                <label>Location</label>
                <input
                  v-model="photographerForm.location"
                  type="text"
                  class="form-input"
                  placeholder="e.g., Dhaka, Bangladesh"
                >
              </div>
            </div>

            <!-- Service Area -->
            <div class="form-row">
              <div class="form-group">
                <label>Service Area Radius (km)</label>
                <input
                  v-model.number="photographerForm.service_area_radius"
                  type="number"
                  min="0"
                  step="0.1"
                  class="form-input"
                  placeholder="50"
                >
              </div>
              <div class="form-group">
                <label>Verified</label>
                <select
                  v-model="photographerForm.is_verified"
                  class="form-input"
                >
                  <option :value="false">
                    No
                  </option>
                  <option :value="true">
                    Yes
                  </option>
                </select>
              </div>
            </div>

            <!-- Web Links -->
            <div style="border-top: 1px solid #e5e7eb; margin: 16px 0; padding-top: 16px;">
              <h4 style="font-weight: 600; margin-bottom: 12px;">
                🌐 Web & Social Links
              </h4>
              
              <div class="form-group">
                <label>Website URL</label>
                <input
                  v-model="photographerForm.website_url"
                  type="url"
                  class="form-input"
                  placeholder="https://example.com"
                >
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label>Instagram URL</label>
                  <input
                    v-model="photographerForm.instagram_url"
                    type="url"
                    class="form-input"
                    placeholder="https://instagram.com/..."
                  >
                </div>
                <div class="form-group">
                  <label>Facebook URL</label>
                  <input
                    v-model="photographerForm.facebook_url"
                    type="url"
                    class="form-input"
                    placeholder="https://facebook.com/..."
                  >
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label>Twitter/X URL</label>
                  <input
                    v-model="photographerForm.twitter_url"
                    type="url"
                    class="form-input"
                    placeholder="https://twitter.com/..."
                  >
                </div>
                <div class="form-group">
                  <label>LinkedIn URL</label>
                  <input
                    v-model="photographerForm.linkedin_url"
                    type="url"
                    class="form-input"
                    placeholder="https://linkedin.com/..."
                  >
                </div>
              </div>

              <div class="form-group">
                <label>YouTube URL</label>
                <input
                  v-model="photographerForm.youtube_url"
                  type="url"
                  class="form-input"
                  placeholder="https://youtube.com/..."
                >
              </div>

              <div class="form-group">
                <label>Profile Image URL</label>
                <input
                  v-model="photographerForm.profile_picture"
                  type="url"
                  class="form-input"
                  placeholder="https://example.com/image.jpg"
                >
              </div>
            </div>

            <!-- Specializations -->
            <div style="border-top: 1px solid #e5e7eb; margin: 16px 0; padding-top: 16px;">
              <h4 style="font-weight: 600; margin-bottom: 12px;">
                Specializations
              </h4>
              <div class="form-group">
                <label>Enter specializations (comma-separated)</label>
                <textarea
                  v-model="specializations"
                  class="form-input"
                  rows="3"
                  placeholder="e.g., Portrait Photography, Wedding Photography, Event Photography"
                />
              </div>
            </div>

            <!-- Info Section -->
            <div style="background: #f0f9ff; border-left: 4px solid #0284c7; padding: 12px; border-radius: 4px; margin: 16px 0;">
              <p style="font-size: 0.9em; color: #0555b3; margin: 0;">
                <strong>💡 Note:</strong> Categories and Portfolio are managed separately. Use the "View Details" button to manage those.
              </p>
            </div>

            <!-- Action Buttons -->
            <div class="form-actions">
              <button
                type="button"
                class="btn-secondary"
                @click="showEditProfileModal = false"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="btn-primary"
              >
                💾 Save Changes
              </button>
              <button
                type="button"
                class="btn-danger"
                @click="deletePhotographerProfile"
              >
                🗑️ Delete Profile
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Add/Edit User Modal -->
    <div
      v-if="showAddModal || showEditModal"
      class="modal-overlay"
      @click.self="closeEditModal"
    >
      <div class="modal">
        <div class="modal-header">
          <h3>{{ showEditModal ? 'Edit User' : 'Add New User' }}</h3>
          <button
            class="modal-close"
            @click="closeEditModal"
          >
            ×
          </button>
        </div>
        <div class="modal-body">
          <form
            class="user-form"
            @submit.prevent="saveUser"
          >
            <div class="form-group">
              <label>Name *</label>
              <input
                v-model="userForm.name"
                type="text"
                required
                class="form-input"
                placeholder="Full Name"
              >
            </div>
            <div class="form-group">
              <label>Email *</label>
              <input
                v-model="userForm.email"
                type="email"
                required
                class="form-input"
                placeholder="email@example.com"
              >
            </div>
            <div class="form-group">
              <label>Phone</label>
              <input
                v-model="userForm.phone"
                type="text"
                class="form-input"
                placeholder="+880 1234567890"
              >
            </div>
            <div class="form-group">
              <label>Password {{ showEditModal ? '' : '*' }}</label>
              <input
                v-model="userForm.password"
                type="password"
                :required="!showEditModal"
                class="form-input"
                placeholder="Min 8 characters"
              >
              <small
                v-if="showEditModal"
                class="form-hint"
              >Leave blank to keep current password</small>
            </div>
            <div class="form-group">
              <label>Role *</label>
              <select
                v-model="userForm.role"
                required
                class="form-input"
              >
                <option value="client">
                  Client
                </option>
                <option value="photographer">
                  Photographer
                </option>
                <option value="studio_owner">
                  Studio Owner
                </option>
                <option value="studio_photographer">
                  Studio Photographer
                </option>
                <option value="admin">
                  Admin
                </option>
                <option value="super_admin">
                  Super Admin
                </option>
                <option value="moderator">
                  Moderator
                </option>
              </select>
            </div>
            <div class="form-actions">
              <button
                type="button"
                class="btn-secondary"
                @click="closeEditModal"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="btn-primary"
              >
                {{ showEditModal ? 'Update User' : 'Create User' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Promote to Mentor Modal -->
    <div
      v-if="showPromoteMentorModal"
      class="modal-overlay"
      @click.self="closePromotionModals"
    >
      <div class="modal modal-large">
        <div class="modal-header">
          <h3>🎓 Promote {{ userToPromote?.name }} to Mentor</h3>
          <button
            class="modal-close"
            @click="closePromotionModals"
          >
            ×
          </button>
        </div>
        <div class="modal-body">
          <form
            class="user-form"
            @submit.prevent="promoteToMentor"
          >
            <div class="form-row">
              <div class="form-group">
                <label>Name *</label>
                <input
                  v-model="mentorForm.name"
                  type="text"
                  required
                  class="form-input"
                  placeholder="Full Name"
                >
              </div>
              <div class="form-group">
                <label>Title *</label>
                <input
                  v-model="mentorForm.title"
                  type="text"
                  required
                  class="form-input"
                  placeholder="e.g., Senior Photographer"
                >
              </div>
            </div>
            <div class="form-group">
              <label>Organization</label>
              <input
                v-model="mentorForm.organization"
                type="text"
                class="form-input"
                placeholder="Company/Studio Name"
              >
            </div>
            <div class="form-group">
              <label>Bio *</label>
              <textarea
                v-model="mentorForm.bio"
                required
                class="form-input"
                rows="4"
                placeholder="Professional biography and expertise..."
              />
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Email *</label>
                <input
                  v-model="mentorForm.email"
                  type="email"
                  required
                  class="form-input"
                  placeholder="email@example.com"
                >
              </div>
              <div class="form-group">
                <label>Phone</label>
                <input
                  v-model="mentorForm.phone"
                  type="text"
                  class="form-input"
                  placeholder="+880 1234567890"
                >
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Country *</label>
                <input
                  v-model="mentorForm.country"
                  type="text"
                  required
                  class="form-input"
                  placeholder="Bangladesh"
                >
              </div>
              <div class="form-group">
                <label>Location *</label>
                <input
                  v-model="mentorForm.city"
                  type="text"
                  required
                  class="form-input"
                  placeholder="Dhaka"
                >
              </div>
            </div>
            <div class="form-group">
              <label class="checkbox-label">
                <input
                  v-model="mentorForm.is_active"
                  type="checkbox"
                >
                <span>Active (visible to users)</span>
              </label>
            </div>
            <div class="form-actions">
              <button
                type="button"
                class="btn-secondary"
                @click="closePromotionModals"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="btn-primary"
              >
                Promote to Mentor
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Promote to Judge Modal -->
    <div
      v-if="showPromoteJudgeModal"
      class="modal-overlay"
      @click.self="closePromotionModals"
    >
      <div class="modal modal-large">
        <div class="modal-header">
          <h3>⚖️ Promote {{ userToPromote?.name }} to Judge</h3>
          <button
            class="modal-close"
            @click="closePromotionModals"
          >
            ×
          </button>
        </div>
        <div class="modal-body">
          <form
            class="user-form"
            @submit.prevent="promoteToJudge"
          >
            <div class="form-row">
              <div class="form-group">
                <label>Name *</label>
                <input
                  v-model="judgeForm.name"
                  type="text"
                  required
                  class="form-input"
                  placeholder="Full Name"
                >
              </div>
              <div class="form-group">
                <label>Title *</label>
                <input
                  v-model="judgeForm.title"
                  type="text"
                  required
                  class="form-input"
                  placeholder="e.g., Photography Judge"
                >
              </div>
            </div>
            <div class="form-group">
              <label>Organization</label>
              <input
                v-model="judgeForm.organization"
                type="text"
                class="form-input"
                placeholder="Professional Association/Studio"
              >
            </div>
            <div class="form-group">
              <label>Bio *</label>
              <textarea
                v-model="judgeForm.bio"
                required
                class="form-input"
                rows="4"
                placeholder="Professional biography and judging experience..."
              />
            </div>
            <div class="form-group">
              <label>Email *</label>
              <input
                v-model="judgeForm.email"
                type="email"
                required
                class="form-input"
                placeholder="email@example.com"
              >
            </div>
            <div class="form-group">
              <label class="checkbox-label">
                <input
                  v-model="judgeForm.is_active"
                  type="checkbox"
                >
                <span>Active (can judge competitions)</span>
              </label>
            </div>
            <div class="form-actions">
              <button
                type="button"
                class="btn-secondary"
                @click="closePromotionModals"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="btn-primary"
              >
                Promote to Judge
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <div
      v-if="showToast"
      class="toast"
      :class="toastType"
    >
      <div class="toast-content">
        <svg
          v-if="toastType === 'error'"
          class="toast-icon"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>
        <svg
          v-else
          class="toast-icon"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>
        <div class="toast-message">
          <span>{{ toastMessage }}</span>
        </div>
        <button
          class="toast-copy"
          title="Copy to clipboard"
          @click="copyToClipboard(toastMessage)"
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
              d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
            />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
import { ref, computed, onMounted } from 'vue'
import api from '../../../api'

const users = ref([])
const loading = ref(false)
const showViewModal = ref(false)
const showAddModal = ref(false)
const showEditModal = ref(false)
const selectedUser = ref(null)
const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success')
const showPromoteMentorModal = ref(false)
const showPromoteJudgeModal = ref(false)
const userToPromote = ref(null)
const loadingUserDetails = ref(false)

const userForm = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  role: 'client'
})

const mentorForm = ref({
  name: '',
  title: '',
  organization: '',
  bio: '',
  email: '',
  phone: '',
  country: 'Bangladesh',
  city: '',
  is_active: true
})

const judgeForm = ref({
  name: '',
  title: '',
  organization: '',
  bio: '',
  email: '',
  is_active: true
})

const filters = ref({
  search: '',
  role: ''
})

const stats = ref({
  total: 0,
  active: 0,
  photographers: 0,
  suspended: 0
})

const pagination = ref({
  currentPage: 1,
  perPage: 30,
  totalPages: 1
})

let searchTimeout = null

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    pagination.value.currentPage = 1 // Reset to first page on new search
    fetchUsers()
  }, 500)
}

const changePage = (page) => {
  if (page < 1 || page > pagination.value.totalPages) return
  pagination.value.currentPage = page
  fetchUsers()
}

const fetchUsers = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.value.currentPage,
      per_page: pagination.value.perPage
    }

    if (filters.value.search) params.search = filters.value.search
    if (filters.value.role) params.role = filters.value.role

    const { data } = await api.get('/admin/users', { params })

    if (data.status === 'success') {
      users.value = data.data.users
      
      // Update pagination from meta
      pagination.value.currentPage = data.meta.current_page
      pagination.value.perPage = data.meta.per_page
      pagination.value.totalPages = data.meta.last_page
      
      // Use backend-calculated stats (accounts for filters)
      stats.value = data.data.stats || {
        total: data.meta.total,
        active: 0,
        photographers: 0,
        suspended: 0
      }
    }
  } catch (error) {
    console.error('Error fetching users:', error)
    showToastMessage('Error loading users')
  } finally {
    loading.value = false
  }
}

const viewUser = async (user) => {
  loadingUserDetails.value = true
  try {
    const { data } = await api.get(`/admin/users/${user.id}`)
    console.log('User details response:', data)
    
    if (data.status === 'success' && data.user) {
      selectedUser.value = data.user
    } else {
      selectedUser.value = user
    }
    showViewModal.value = true
  } catch (error) {
    console.error('Error fetching user details:', error)
    selectedUser.value = user
    showViewModal.value = true
  } finally {
    loadingUserDetails.value = false
  }
}

const editUser = (user) => {
  selectedUser.value = user
  userForm.value = {
    name: user.name,
    email: user.email,
    phone: user.phone || '',
    password: '',
    role: user.role
  }
  showEditModal.value = true
}

const closeEditModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  selectedUser.value = null
  userForm.value = {
    name: '',
    email: '',
    phone: '',
    password: '',
    role: 'client'
  }
}

const saveUser = async () => {
  try {
    const url = showEditModal.value 
      ? `/admin/users/${selectedUser.value.id}` 
      : '/admin/users'

    const request = showEditModal.value
      ? api.put(url, userForm.value)
      : api.post(url, userForm.value)

    const { data } = await request
    
    if (data.status === 'success') {
      showToastMessage(data.message)
      closeEditModal()
      fetchUsers()
    } else {
      showToastMessage(data.message || 'Error saving user')
    }
  } catch (error) {
    console.error('Error saving user:', error)
    showToastMessage('Error saving user')
  }
}

const deleteUser = async (user) => {
  if (!confirm(`Are you sure you want to delete ${user.name}? This action cannot be undone.`)) return
  
  try {
    const { data } = await api.delete(`/admin/users/${user.id}`)
    
    if (data.status === 'success') {
      showToastMessage(data.message, 'success')
      fetchUsers()
    } else {
      showToastMessage(data.message || 'Error deleting user', 'error')
    }
  } catch (error) {
    console.error('Error deleting user:', error)
    const errorMsg = error.response?.data?.message || error.message || 'Error deleting user'
    showToastMessage(errorMsg, 'error')
  }
}

const suspendUser = async (user) => {
  if (!confirm(`Are you sure you want to suspend ${user.name}?`)) return
  
  try {
    const { data } = await api.post(`/admin/users/${user.id}/suspend`, {
      reason: 'Suspended by admin'
    })
    
    if (data.status === 'success') {
      showToastMessage(data.message)
      fetchUsers()
    } else {
      showToastMessage(data.message || 'Error suspending user')
    }
  } catch (error) {
    console.error('Error suspending user:', error)
    showToastMessage('Error suspending user')
  }
}

const unsuspendUser = async (user) => {
  if (!confirm(`Are you sure you want to unsuspend ${user.name}?`)) return
  
  try {
    const { data } = await api.post(`/admin/users/${user.id}/unsuspend`)
    
    if (data.status === 'success') {
      showToastMessage(data.message)
      fetchUsers()
    } else {
      showToastMessage(data.message || 'Error unsuspending user')
    }
  } catch (error) {
    console.error('Error unsuspending user:', error)
    showToastMessage('Error unsuspending user')
  }
}

const showPromoteToMentor = (user) => {
  userToPromote.value = user
  mentorForm.value = {
    name: user.name,
    title: '',
    organization: '',
    bio: '',
    email: user.email,
    phone: user.phone || '',
    country: 'Bangladesh',
    city: '',
    is_active: true
  }
  showPromoteMentorModal.value = true
}

const showPromoteToJudge = (user) => {
  userToPromote.value = user
  judgeForm.value = {
    name: user.name,
    title: '',
    organization: '',
    bio: '',
    email: user.email,
    is_active: true
  }
  showPromoteJudgeModal.value = true
}

const promoteToMentor = async () => {
  if (!userToPromote.value) return
  
  try {
    const { data } = await api.post(`/admin/users/${userToPromote.value.id}/promote-to-mentor`, mentorForm.value)
    
    if (data.status === 'success') {
      showToastMessage(`${userToPromote.value.name} has been promoted to Mentor!`, 'success')
      showPromoteMentorModal.value = false
      fetchUsers()
    } else {
      showToastMessage(data.message || 'Error promoting user to mentor', 'error')
    }
  } catch (error) {
    console.error('Error promoting to mentor:', error)
    showToastMessage('Error promoting user to mentor', 'error')
  }
}

const promoteToJudge = async () => {
  if (!userToPromote.value) return
  
  try {
    const { data } = await api.post(`/admin/users/${userToPromote.value.id}/promote-to-judge`, judgeForm.value)
    
    if (data.status === 'success') {
      showToastMessage(`${userToPromote.value.name} has been promoted to Judge!`, 'success')
      showPromoteJudgeModal.value = false
      fetchUsers()
    } else {
      showToastMessage(data.message || 'Error promoting user to judge', 'error')
    }
  } catch (error) {
    console.error('Error promoting to judge:', error)
    showToastMessage('Error promoting user to judge', 'error')
  }
}

const closePromotionModals = () => {
  showPromoteMentorModal.value = false
  showPromoteJudgeModal.value = false
  userToPromote.value = null
}

const exportUsers = () => {
  showToastMessage('Export feature coming soon')
}

const formatRole = (role) => {
  const roles = {
    'client': 'Client',
    'photographer': 'Photographer',
    'studio_owner': 'Studio Owner',
    'studio_photographer': 'Studio Photographer',
    'admin': 'Admin',
    'super_admin': 'Super Admin',
    'moderator': 'Moderator'
  }
  return roles[role] || role
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  const d = new Date(date)
  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = d.getFullYear()
  return `${day}-${month}-${year}` // DD-MM-YYYY for Bangladesh
}

const showToastMessage = (message, type = 'success') => {
  toastMessage.value = message
  toastType.value = type
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, type === 'error' ? 5000 : 3000) // Errors stay longer
}

const copyToClipboard = (text) => {
  navigator.clipboard.writeText(text).then(() => {
    // Show copied feedback
    const originalMessage = toastMessage.value
    toastMessage.value = 'Copied to clipboard!'
    setTimeout(() => {
      toastMessage.value = originalMessage
    }, 1000)
  })
}

// Photographer Profile Data Management
const showEditProfileModal = ref(false)
const specializations = ref('')
const generatedBios = ref([])
const photographerForm = ref({
  bio: '',
  location: '',
  experience_years: 0,
  specializations: [],
  service_area_radius: 50,
  website_url: '',
  instagram_url: '',
  facebook_url: '',
  twitter_url: '',
  linkedin_url: '',
  youtube_url: '',
  profile_picture: '',
  is_verified: false
})

const editPhotographerProfile = (user) => {
  if (user.photographer) {
    photographerForm.value = {
      bio: user.photographer.bio || '',
      location: user.photographer.location || '',
      experience_years: user.photographer.experience_years || 0,
      specializations: user.photographer.specializations || [],
      service_area_radius: user.photographer.service_area_radius || 50,
      website_url: user.photographer.website_url || '',
      instagram_url: user.photographer.instagram_url || '',
      facebook_url: user.photographer.facebook_url || '',
      twitter_url: user.photographer.twitter_url || '',
      linkedin_url: user.photographer.linkedin_url || '',
      youtube_url: user.photographer.youtube_url || '',
      profile_picture: user.photographer.profile_picture || '',
      is_verified: user.photographer.is_verified || false
    }
    specializations.value = (user.photographer.specializations || []).join(', ')
  } else {
    photographerForm.value = {
      bio: '',
      location: '',
      experience_years: 0,
      specializations: [],
      service_area_radius: 50,
      website_url: '',
      instagram_url: '',
      facebook_url: '',
      twitter_url: '',
      linkedin_url: '',
      youtube_url: '',
      profile_picture: '',
      is_verified: false
    }
    specializations.value = ''
  }
  generatedBios.value = []
  showEditProfileModal.value = true
}

const savePhotographerProfile = async () => {
  if (!selectedUser.value || !selectedUser.value.photographer) {
    showToastMessage('Error: Photographer profile not found', 'error')
    return
  }

  try {
    // Convert specializations string to array
    const data = {
      ...photographerForm.value,
      specializations: specializations.value
        .split(',')
        .map(s => s.trim())
        .filter(s => s.length > 0)
    }

    const response = await api.put(`/admin/photographers/${selectedUser.value.photographer.id}`, data)
    const responseData = response.data
    
    if (responseData.status === 'success') {
      showToastMessage('Photographer profile updated successfully!', 'success')
      showEditProfileModal.value = false
      // Refresh the user view
      viewUser(selectedUser.value)
      fetchUsers()
    } else {
      showToastMessage(responseData.message || 'Error updating photographer profile', 'error')
    }
  } catch (error) {
    console.error('Error saving photographer profile:', error)
    showToastMessage('Error updating photographer profile', 'error')
  }
}

const deletePhotographerProfile = async () => {
  if (!selectedUser.value || !selectedUser.value.photographer) {
    showToastMessage('Error: Photographer profile not found', 'error')
    return
  }

  if (!confirm('Are you sure you want to delete this photographer profile? This action cannot be undone.')) {
    return
  }

  try {
    const { data } = await api.delete(`/admin/photographers/${selectedUser.value.photographer.id}`)
    
    if (data.status === 'success') {
      showToastMessage('Photographer profile deleted successfully!', 'success')
      showEditProfileModal.value = false
      showViewModal.value = false
      // Refresh the user list
      fetchUsers()
    } else {
      showToastMessage(data.message || 'Error deleting photographer profile', 'error')
    }
  } catch (error) {
    console.error('Error deleting photographer profile:', error)
    showToastMessage('Error deleting photographer profile', 'error')
  }
}

const viewPhotographerDetails = (user) => {
  if (user.photographer && user.photographer.slug) {
    // Open in new tab
    window.open(`/photographer/${user.photographer.slug}`, '_blank')
  } else {
    showToastMessage('Photographer profile not available', 'error')
  }
}

// Auto-generate bio from photographer profile data
const generateBio = () => {
  const exp = photographerForm.value.experience_years
  const specs = specializations.value
    .split(',')
    .map(s => s.trim())
    .filter(s => s.length > 0)
  const location = photographerForm.value.location
  const radius = photographerForm.value.service_area_radius

  const bios = []

  // STYLE 1: Professional & Formal
  if (specs.length && exp && location) {
    bios.push({
      style: 'Professional',
      text: `A seasoned ${specs[0]} photographer with ${exp} years of professional experience. Serving ${location} and surrounding areas within ${radius}km radius. Dedicated to delivering exceptional results with meticulous attention to detail.`
    })
  }

  // STYLE 2: Creative & Passionate
  if (specs.length && exp) {
    bios.push({
      style: 'Passionate',
      text: `Creative ${specs.join(' & ')} photographer with ${exp} years of passion-driven experience. Specializing in capturing genuine moments and transforming them into timeless visual stories.`
    })
  }

  // STYLE 3: Artistic & Unique
  if (specs.length) {
    bios.push({
      style: 'Artistic',
      text: `Artistic lens specialist in ${specs.join(', ')}. Bringing creativity, innovation, and technical expertise to every frame. Every photo tells a story worth capturing.`
    })
  }

  // STYLE 4: Friendly & Approachable
  if (specs.length && location) {
    bios.push({
      style: 'Friendly',
      text: `Your ${specs[0]} photographer in ${location}! With years of experience, I love capturing the moments that matter most. Let's create something beautiful together.`
    })
  }

  // STYLE 5: Client-Focused
  if (specs.length && exp && location) {
    bios.push({
      style: 'Client-Focused',
      text: `${specs[0]} photographer committed to bringing your vision to life. ${exp} years of experience ensuring your special moments are captured beautifully in ${location}.`
    })
  }

  // STYLE 6: Innovative & Modern
  if (specs.length) {
    bios.push({
      style: 'Innovative',
      text: `Modern ${specs.join(' + ')} photographer blending traditional techniques with contemporary aesthetics. Creating stunning visual content that stands out.`
    })
  }

  // STYLE 7: Premium & Luxury
  if (specs.length && exp && location) {
    bios.push({
      style: 'Premium',
      text: `Premium ${specs[0]} services with ${exp} years of excellence. Based in ${location}, serving discerning clients across ${radius}km radius seeking sophisticated visual storytelling.`
    })
  }

  // STYLE 8: Storyteller
  if (specs.length && exp) {
    bios.push({
      style: 'Storyteller',
      text: `${specs.join(' and ')} storyteller. ${exp} years of capturing authentic narratives through the lens, preserving your most precious memories in stunning detail.`
    })
  }

  // STYLE 9: Technical Expert
  if (specs.length && exp) {
    bios.push({
      style: 'Technical Expert',
      text: `Expert ${specs[0]} photographer with ${exp}+ years mastering composition, lighting, and post-production. Technical precision meets artistic vision in every shot.`
    })
  }

  // STYLE 10: Enthusiast Professional
  if (specs.length && location) {
    bios.push({
      style: 'Enthusiast',
      text: `Enthusiastic ${specs[0]} professional based in ${location}. Combining technical skill with genuine passion to deliver photos you'll treasure forever.`
    })
  }

  // STYLE 11: Minimalist & Direct
  if (specs.length && exp) {
    bios.push({
      style: 'Direct',
      text: `${specs.join(', ')} photographer. ${exp} years. Exceptional results. That's what you get.`
    })
  }

  // STYLE 12: Vision-Driven
  if (specs.length && exp && location) {
    bios.push({
      style: 'Vision-Driven',
      text: `Visionary ${specs[0]} photographer in ${location} with ${exp} years dedicated to capturing moments exactly as you imagine them. Local roots, global vision.`
    })
  }

  // FALLBACK if minimal data
  if (bios.length === 0) {
    bios.push({
      style: 'Classic',
      text: 'Professional photographer dedicated to capturing beautiful moments and creating lasting visual memories.'
    })
  }

  generatedBios.value = bios
}

const useBio = (bio) => {
  if (bio && bio.text) {
    photographerForm.value.bio = bio.text
    generatedBios.value = []
    showToastMessage(`✅ Bio added! "${bio.style}" style applied.`)
  }
}

const clearBio = () => {
  photographerForm.value.bio = ''
  generatedBios.value = []
  showToastMessage('Bio cleared')
}

onMounted(() => {
  fetchUsers()
})
</script>

<style scoped>
.page-hero { display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr); gap: 1.5rem; padding: 1.75rem 2rem; border-radius: 1.5rem; border: 1px solid rgba(142, 14, 63, 0.2); background: linear-gradient(135deg, rgba(255, 255, 255, 0.92), rgba(247, 239, 233, 0.82)), linear-gradient(90deg, rgba(142, 14, 63, 0.06), transparent 45%, rgba(109, 72, 56, 0.08)); box-shadow: 0 25px 55px rgba(24, 12, 8, 0.1), inset 0 0 0 1px rgba(255, 255, 255, 0.6); backdrop-filter: blur(6px); }
.hero-copy { display: flex; flex-direction: column; gap: 0.85rem; }
.hero-kicker { font-size: 0.7rem; letter-spacing: 0.28em; text-transform: uppercase; color: var(--admin-text-secondary); font-weight: 700; }
.hero-title { font-size: 2rem; line-height: 1.1; color: var(--admin-text-primary); text-shadow: 0 2px 14px rgba(142, 14, 63, 0.18); }
.hero-subtitle { color: var(--admin-text-secondary); max-width: 480px; }
.hero-actions { display: flex; flex-wrap: wrap; gap: 0.75rem; }
.hero-status { display: grid; gap: 0.8rem; }
.status-card { background: rgba(255, 255, 255, 0.85); border: 1px solid rgba(142, 14, 63, 0.2); border-radius: 1rem; padding: 1rem 1.25rem; box-shadow: 0 16px 35px rgba(22, 12, 8, 0.08); display: flex; flex-direction: column; gap: 0.35rem; }
.status-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.2em; color: var(--admin-text-secondary); }
.status-value { font-size: 1.1rem; font-weight: 700; color: var(--admin-text-primary); }
.page-topbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.9rem 1.25rem; background: rgba(255, 255, 255, 0.88); border: 1px solid rgba(140, 108, 95, 0.2); border-radius: 1.1rem; box-shadow: 0 18px 35px rgba(18, 9, 6, 0.08); backdrop-filter: blur(8px); }
.status-chip { background: rgba(142, 14, 63, 0.12); color: var(--admin-text-primary); padding: 0.4rem 0.8rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
@media (max-width: 1024px) { .page-hero { grid-template-columns: 1fr; } }

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
  border-left: 4px solid;
}

.stat-blue { border-color: var(--admin-brand-primary); }
.stat-green { border-color: var(--admin-brand-primary); }
.stat-purple { border-color: var(--admin-brand-primary); }
.stat-red { border-color: var(--admin-brand-primary); }

.stat-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(59, 130, 246, 0.1);
}

.stat-blue .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }
.stat-green .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }
.stat-purple .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }
.stat-red .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }

.stat-content {
  display: flex;
  flex-direction: column;
}

.stat-label {
  color: #6b7280;
  font-size: 0.875rem;
  margin-bottom: 0.25rem;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
}

.content-card {
  background: white;
  border-radius: 1rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  padding: 1.5rem;
}

.filters-bar {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.search-box {
  position: relative;
  flex: 1;
  min-width: 300px;
}

.search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  width: 1.25rem;
  height: 1.25rem;
  color: #9ca3af;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 3rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 0.875rem;
}

.search-input:focus {
  outline: none;
  border-color: var(--admin-brand-primary);
  box-shadow: 0 0 0 3px rgba(139, 21, 56, 0.12);
}

.filter-select {
  padding: 0.75rem 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  cursor: pointer;
}

.filter-select:focus {
  outline: none;
  border-color: var(--admin-brand-primary);
}

.btn-primary {
  display: flex;
  align-items: center;
  background: var(--admin-brand-primary);
  color: white;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.2s;
}

.btn-primary:hover {
  background: var(--admin-brand-primary-dark);
}

.btn-export {
  display: flex;
  align-items: center;
  background: white;
  color: #6b7280;
  padding: 0.75rem 1.5rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
}

.btn-export:hover {
  background: #f9fafb;
  border-color: var(--admin-brand-primary);
  color: var(--admin-brand-primary);
}

.loading-state {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
}

.loading-state-inline {
  text-align: center;
  padding: 2rem;
  color: #6b7280;
}

.empty-state-inline {
  text-align: center;
  padding: 2rem;
  color: #9ca3af;
}

.spinner {
  width: 3rem;
  height: 3rem;
  border: 3px solid #e5e7eb;
  border-top-color: var(--admin-brand-primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

.spinner-small {
  width: 1.5rem;
  height: 1.5rem;
  border: 2px solid #e5e7eb;
  border-top-color: var(--admin-brand-primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 0.5rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.table-container {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  text-align: left;
  padding: 1rem;
  background: #f9fafb;
  color: #6b7280;
  font-weight: 600;
  font-size: 0.875rem;
  border-bottom: 2px solid #e5e7eb;
}

.data-table td {
  padding: 1rem;
  border-bottom: 1px solid #f3f4f6;
}

.user-row:hover {
  background: #f9fafb;
}

.user-cell {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.user-avatar {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 50%;
  background: var(--admin-brand-primary);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 1rem;
}

.user-name {
  font-weight: 600;
  color: #1f2937;
}

.user-id {
  font-size: 0.75rem;
  color: #9ca3af;
}

.contact-cell {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.email {
  color: #1f2937;
}

.phone {
  font-size: 0.875rem;
  color: #6b7280;
}

.badge {
  display: inline-flex;
  align-items: center;
  padding: 0.375rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  white-space: nowrap;
}

.badge-client { background: var(--admin-info-light); color: var(--admin-info-text); }
.badge-photographer { background: var(--admin-info-light); color: var(--admin-info-text); }
.badge-admin { background: var(--admin-info-light); color: var(--admin-info-text); }
.badge-super_admin { background: var(--admin-info-light); color: var(--admin-info-text); }
.badge-success { background: var(--admin-success-light); color: var(--admin-success-text); }
.badge-danger { background: var(--admin-danger-light); color: var(--admin-danger-text); }

.verified-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  color: var(--admin-success);
  font-size: 0.875rem;
  font-weight: 500;
}

.unverified-badge {
  color: #9ca3af;
  font-size: 0.875rem;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.btn-action {
  width: 2rem;
  height: 2rem;
  border: 1px solid #e5e7eb;
  background: white;
  border-radius: 0.375rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
  transition: all 0.2s;
}

.btn-action:hover {
  background: #f9fafb;
  border-color: var(--admin-brand-primary);
  color: var(--admin-brand-primary);
}

.btn-danger:hover {
  background: var(--admin-danger-light);
  border-color: var(--admin-danger);
  color: var(--admin-danger-text);
}

.btn-success:hover {
  background: var(--admin-success-light);
  border-color: var(--admin-success);
  color: var(--admin-success-text);
}

.btn-mentor {
  color: #7c3aed;
  border-color: #7c3aed;
}

.btn-mentor:hover {
  background: #ede9fe;
  border-color: #7c3aed;
  color: #7c3aed;
}

.btn-judge {
  color: #0891b2;
  border-color: #0891b2;
}

.btn-judge:hover {
  background: #cffafe;
  border-color: #0891b2;
  color: #0891b2;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #9ca3af;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.pagination-info {
  color: #6b7280;
  font-size: 0.875rem;
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.pagination-pages {
  color: #6b7280;
  font-size: 0.875rem;
  padding: 0 1rem;
}

.pagination-current {
  font-weight: 600;
  color: #1f2937;
}

.pagination-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  background: white;
  color: #374151;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.pagination-btn:hover:not(:disabled) {
  background: #f9fafb;
  border-color: #9ca3af;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal {
  background: white;
  border-radius: 1rem;
  max-width: 600px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-large {
  max-width: 800px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
}

.modal-close {
  background: none;
  border: none;
  font-size: 2rem;
  color: #9ca3af;
  cursor: pointer;
  padding: 0;
  width: 2rem;
  height: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 0.375rem;
}

.modal-close:hover {
  background: #f3f4f6;
  color: #6b7280;
}

.modal-body {
  padding: 1.5rem;
}

.detail-grid {
  display: grid;
  gap: 1rem;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: #f9fafb;
  border-radius: 0.5rem;
}

.detail-item.full-width {
  flex-direction: column;
  align-items: flex-start;
  gap: 0.75rem;
}

.detail-label {
  font-weight: 600;
  color: #6b7280;
}

.detail-value {
  color: #1f2937;
}

.role-badges {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  width: 100%;
}

.badge-photographer {
  background: #fef3c7;
  color: #92400e;
}

.badge-mentor {
  background: #ede9fe;
  color: #581c87;
}

.badge-judge {
  background: #cffafe;
  color: #164e63;
}

.text-muted {
  color: #9ca3af;
  font-size: 0.875rem;
}

.user-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group label {
  font-weight: 600;
  color: #374151;
  font-size: 0.875rem;
}

.form-input {
  padding: 0.75rem 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  transition: all 0.2s;
  font-family: inherit;
}

.form-input:focus {
  outline: none;
  border-color: var(--admin-brand-primary);
  box-shadow: 0 0 0 3px rgba(139, 21, 56, 0.1);
}

.form-input[type="checkbox"] {
  width: auto;
  cursor: pointer;
  margin-right: 0.5rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  cursor: pointer;
  font-weight: 500;
  color: #374151;
}

.form-hint {
  font-size: 0.75rem;
  color: #6b7280;
}

.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
  justify-content: flex-end;
}

.btn-secondary {
  padding: 0.75rem 1.5rem;
  border: 1px solid #e5e7eb;
  background: white;
  color: #6b7280;
  border-radius: 0.5rem;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-secondary:hover {
  background: #f9fafb;
  border-color: var(--admin-brand-primary);
  color: var(--admin-brand-primary);
}

.btn-warning:hover {
  background: #fef3c7;
  border-color: #f59e0b;
  color: #f59e0b;
}

.toast {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  background: #065f46;
  color: white;
  padding: 0;
  border-radius: 0.5rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  animation: slideIn 0.3s ease-out;
  z-index: 1001;
  min-width: 320px;
  max-width: 500px;
}

.toast.error {
  background: #dc2626;
}

.toast.success {
  background: #065f46;
}

.toast-content {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
}

.toast-icon {
  width: 1.5rem;
  height: 1.5rem;
  flex-shrink: 0;
}

.toast-message {
  flex: 1;
  font-size: 0.875rem;
  line-height: 1.5;
  word-break: break-word;
  user-select: text;
  cursor: text;
}

.toast-copy {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  border-radius: 0.25rem;
  padding: 0.5rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
  flex-shrink: 0;
}

.toast-copy:hover {
  background: rgba(255, 255, 255, 0.3);
}

.toast-copy svg {
  color: white;
}

@keyframes slideIn {
  from {
    transform: translateX(400px);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.w-4 { width: 1rem; }
.h-4 { height: 1rem; }
.w-5 { width: 1.25rem; }
.h-5 { height: 1.25rem; }
.w-8 { width: 2rem; }
.h-8 { height: 2rem; }
.mr-2 { margin-right: 0.5rem; }
</style>
