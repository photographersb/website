<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Admin Header with Back Button & Notifications -->
    <AdminHeader 
      title="🏆 Competition Management Dashboard" 
      subtitle="Comprehensive overview and management of photography competitions"
    />

    <!-- Main Content -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      
      <!-- Quick Navigation -->
      <AdminQuickNav />

      <!-- Create Competition Button -->
      <div class="flex justify-end">
        <router-link to="/admin/competitions/create" class="inline-flex items-center px-6 py-3 bg-burgundy text-white rounded-lg font-semibold hover:bg-burgundy-dark transition-all shadow-lg hover:shadow-xl">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          New Competition
        </router-link>
      </div>

      <!-- Primary Stats Grid -->
      <div class="stats-grid">
      <div class="stat-card stat-blue">
        <div class="stat-icon">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Total Competitions</span>
          <span class="stat-value">{{ stats.total }}</span>
          <span class="stat-trend">All time</span>
        </div>
      </div>

      <div class="stat-card stat-green">
        <div class="stat-icon">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Active Now</span>
          <span class="stat-value">{{ stats.active }}</span>
          <span class="stat-trend">Accepting submissions</span>
        </div>
      </div>

      <div class="stat-card stat-yellow">
        <div class="stat-icon">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Upcoming</span>
          <span class="stat-value">{{ stats.upcoming }}</span>
          <span class="stat-trend">Starting soon</span>
        </div>
      </div>

      <div class="stat-card stat-purple">
        <div class="stat-icon">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Completed</span>
          <span class="stat-value">{{ stats.completed }}</span>
          <span class="stat-trend">Winners announced</span>
        </div>
      </div>
    </div>

    <!-- Secondary Stats -->
    <div class="secondary-stats">
      <div class="stat-box">
        <div class="stat-box-icon">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
        <div class="stat-box-content">
          <span class="stat-box-label">Total Submissions</span>
          <span class="stat-box-value">{{ formatNumber(stats.totalSubmissions || 0) }}</span>
        </div>
      </div>

      <div class="stat-box">
        <div class="stat-box-icon">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </div>
        <div class="stat-box-content">
          <span class="stat-box-label">Total Participants</span>
          <span class="stat-box-value">{{ formatNumber(stats.totalParticipants || 0) }}</span>
        </div>
      </div>

      <div class="stat-box">
        <div class="stat-box-icon">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="stat-box-content">
          <span class="stat-box-label">Total Prize Pool</span>
          <span class="stat-box-value">৳{{ formatNumber(stats.totalPrizePool || 0) }}</span>
        </div>
      </div>

      <div class="stat-box">
        <div class="stat-box-icon">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
          </svg>
        </div>
        <div class="stat-box-content">
          <span class="stat-box-label">Pending Review</span>
          <span class="stat-box-value">{{ formatNumber(stats.pendingSubmissions || 0) }}</span>
        </div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-grid">
      <!-- Active Competitions -->
      <div class="dashboard-card full-width">
        <div class="card-header">
          <h2 class="card-title">🎯 Active Competitions</h2>
          <router-link to="/admin/competitions/active" class="card-link">View All →</router-link>
        </div>
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Loading competitions...</p>
        </div>
        <div v-else-if="activeCompetitions.length > 0" class="competitions-grid">
          <div v-for="comp in activeCompetitions" :key="comp.id" class="competition-card">
            <div class="competition-header">
              <div class="competition-title">
                <h3>{{ comp.title }}</h3>
                <span :class="['status-badge', statusClass(comp.status)]">{{ comp.status }}</span>
              </div>
              <div class="competition-actions">
                <router-link :to="`/admin/competitions/${comp.id}`" class="btn-icon" title="View">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </router-link>
                <router-link :to="`/admin/competitions/${comp.id}/edit`" class="btn-icon" title="Edit">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </router-link>
              </div>
            </div>
            <div class="competition-body">
              <div class="competition-meta">
                <div class="meta-item">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span>{{ comp.submissions_count || 0 }} submissions</span>
                </div>
                <div class="meta-item">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>৳{{ formatNumber(comp.total_prize_pool || 0) }}</span>
                </div>
              </div>
              <div class="competition-dates">
                <div class="date-item">
                  <span class="date-label">Deadline:</span>
                  <span class="date-value">{{ formatDate(comp.submission_deadline) }}</span>
                </div>
                <div class="date-item">
                  <span class="date-label">Voting Ends:</span>
                  <span class="date-value">{{ formatDate(comp.voting_end) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="empty-state-small">
          <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <p>No active competitions</p>
        </div>
      </div>

      <!-- Draft Competitions -->
      <div class="dashboard-card full-width">
        <div class="card-header">
          <h2 class="card-title">📝 Draft Competitions</h2>
          <router-link to="/admin/competitions/draft" class="card-link">View All →</router-link>
        </div>
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Loading competitions...</p>
        </div>
        <div v-else-if="draftCompetitions.length > 0" class="competitions-grid">
          <div v-for="comp in draftCompetitions" :key="comp.id" class="competition-card">
            <div class="competition-header">
              <div class="competition-title">
                <h3>{{ comp.title }}</h3>
                <span :class="['status-badge', statusClass(comp.status)]">{{ comp.status }}</span>
              </div>
              <div class="competition-actions">
                <router-link :to="`/admin/competitions/${comp.id}`" class="btn-icon" title="View">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </router-link>
                <router-link :to="`/admin/competitions/${comp.id}/edit`" class="btn-icon" title="Edit">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </router-link>
              </div>
            </div>
            <div class="competition-body">
              <div class="competition-meta">
                <div class="meta-item">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span>{{ comp.submissions_count || 0 }} submissions</span>
                </div>
                <div class="meta-item">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>৳{{ formatNumber(comp.total_prize_pool || 0) }}</span>
                </div>
              </div>
              <div class="competition-dates">
                <div class="date-item">
                  <span class="date-label">Deadline:</span>
                  <span class="date-value">{{ formatDate(comp.submission_deadline) }}</span>
                </div>
                <div class="date-item">
                  <span class="date-label">Voting Ends:</span>
                  <span class="date-value">{{ formatDate(comp.voting_end) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="empty-state-small">
          <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          <p>No draft competitions</p>
        </div>
      </div>

      <!-- Recent Submissions -->
      <div class="dashboard-card">
        <div class="card-header">
          <h2 class="card-title">📸 Recent Submissions</h2>
          <router-link to="/admin/competitions/submissions" class="card-link">View All →</router-link>
        </div>
        <div v-if="recentSubmissions.length > 0" class="submissions-list">
          <div v-for="sub in recentSubmissions" :key="sub.id" class="submission-item">
            <div class="submission-info">
              <div class="submission-user">{{ sub.user?.name || 'Anonymous' }}</div>
              <div class="submission-competition">{{ sub.competition?.title }}</div>
              <div class="submission-time">{{ formatTimeAgo(sub.created_at) }}</div>
            </div>
            <span :class="['status-badge-sm', statusClass(sub.status)]">{{ sub.status }}</span>
          </div>
        </div>
        <div v-else class="empty-state-small">
          <p>No recent submissions</p>
        </div>
      </div>

      <!-- Upcoming Deadlines -->
      <div class="dashboard-card">
        <div class="card-header">
          <h2 class="card-title">⏰ Upcoming Deadlines</h2>
        </div>
        <div v-if="upcomingDeadlines.length > 0" class="deadlines-list">
          <div v-for="comp in upcomingDeadlines" :key="comp.id" class="deadline-item">
            <div class="deadline-info">
              <div class="deadline-title">{{ comp.title }}</div>
              <div class="deadline-date">{{ formatDate(comp.submission_deadline) }}</div>
            </div>
            <div class="deadline-countdown">{{ getDaysUntil(comp.submission_deadline) }} days</div>
          </div>
        </div>
        <div v-else class="empty-state-small">
          <p>No upcoming deadlines</p>
        </div>
      </div>
    </div>

    <!-- Filters & Competition List -->
    <div class="content-card">
      <div class="card-header">
        <h2 class="card-title">All Competitions</h2>
        <button @click="exportCompetitions" class="btn-export">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Export
        </button>
      </div>

      <div class="filters-bar">
        <div class="search-box">
          <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input 
            v-model="form.search" 
            @input="debounceSearch"
            type="text" 
            placeholder="Search competitions..." 
            class="search-input"
          />
        </div>

        <select v-model="form.status" @change="applyFilters" class="filter-select">
          <option value="">All Status</option>
          <option value="draft">Draft</option>
          <option value="active">Active</option>
          <option value="judging">Judging</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
          <option value="archived">Archived</option>
        </select>

        <select v-model="form.category_id" @change="applyFilters" class="filter-select">
          <option value="">All Categories</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>

        <select v-model="form.featured" @change="applyFilters" class="filter-select">
          <option value="">All Featured</option>
          <option value="1">Featured</option>
          <option value="0">Not Featured</option>
        </select>

        <button @click="clearFilters" class="btn-clear">Clear Filters</button>
      </div>

      <!-- Competitions Table -->
      <div v-if="!loading && competitions.data && competitions.data.length > 0" class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>Competition</th>
              <th>Status</th>
              <th>Submissions</th>
              <th>Prize Pool</th>
              <th>Deadline</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="comp in competitions.data" :key="comp.id" class="table-row">
              <td>
                <div class="table-title">
                  <div class="title-text">{{ comp.title }}</div>
                  <div class="title-category">{{ comp.category?.name }}</div>
                </div>
              </td>
              <td>
                <span :class="['status-badge', statusClass(comp.status)]">{{ comp.status }}</span>
              </td>
              <td>{{ comp.submissions_count || 0 }}</td>
              <td>৳{{ formatNumber(comp.total_prize_pool || 0) }}</td>
              <td>{{ formatDate(comp.submission_deadline) }}</td>
              <td>
                <div class="action-buttons">
                  <router-link :to="`/admin/competitions/${comp.id}`" class="btn-action" title="View">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </router-link>
                  <router-link :to="`/admin/competitions/${comp.id}/edit`" class="btn-action" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </router-link>
                  <button @click="deleteCompetition(comp.id)" class="btn-action btn-danger" title="Delete">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div v-else-if="!loading && (!competitions.data || competitions.data.length === 0)" class="empty-state">
        <div class="empty-icon">🏆</div>
        <p class="empty-title">No competitions found</p>
        <p class="empty-subtitle">Create your first competition to get started</p>
        <router-link to="/admin/competitions/create" class="btn-primary mt-4">
          Create Competition
        </router-link>
      </div>

      <!-- Pagination -->
      <div v-if="competitions.total > 0" class="pagination">
        <div class="pagination-info">
          Showing {{ competitions.from || 0 }} to {{ competitions.to || 0 }} of {{ competitions.total || 0 }} competitions
        </div>
      </div>
    </div>

    <!-- Toast -->
    <div v-if="showToast" class="toast">{{ toastMessage }}</div>
    </div>
  </div>
</template>
            <select
              v-model="form.status"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
            >
              <option value="">All Status</option>
              <option value="draft">Draft</option>
              <option value="published">Published</option>
              <option value="closed">Closed</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Date Filter</label>
            <select
              v-model="form.date_filter"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
            >
              <option value="">All Dates</option>
              <option value="upcoming">Upcoming</option>
              <option value="active">Active</option>
              <option value="completed">Completed</option>
            </select>
          </div>

          <div class="flex items-end gap-2">
            <button
              type="submit"
              class="flex-1 px-4 py-2 bg-burgundy text-white rounded-lg font-medium hover:bg-burgundy-dark transition-all"
            >
              Apply Filters
            </button>
            <button
              type="button"
              @click="clearFilters"
              class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-all"
            >
              Clear
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Competitions Table -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
      <div class="bg-white rounded-lg shadow-card overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Competition</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prize Pool</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submissions</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="competition in competitions.data" :key="competition.id" class="hover:bg-gray-50">
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <div>
                    <div class="flex items-center gap-2">
                      <div class="text-sm font-medium text-gray-900">{{ competition.title }}</div>
                      <span v-if="competition.is_featured" class="badge badge-primary">
                        Featured
                      </span>
                    </div>
                    <div class="text-sm text-gray-500">{{ competition.theme }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">
                <div>Submit by: {{ formatDate(competition.submission_deadline) }}</div>
                <div v-if="competition.voting_start_at">Vote: {{ formatDate(competition.voting_start_at) }} - {{ formatDate(competition.voting_end_at) }}</div>
              </td>
              <td class="px-6 py-4 text-sm font-medium text-gray-900">
                {{ competition.total_prize_pool ? `৳${formatNumber(competition.total_prize_pool)}` : 'N/A' }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">
                {{ competition.total_submissions || 0 }} submissions<br />
                {{ competition.total_votes || 0 }} votes
              </td>
              <td class="px-6 py-4">
                <span :class="statusClass(competition.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                  {{ competition.status }}
                </span>
              </td>
              <td class="px-6 py-4 text-right text-sm font-medium">
                <div class="flex items-center justify-end gap-2">
                  <a
                    :href="`/admin/competitions/${competition.id}/submissions`"
                    class="text-burgundy hover:text-burgundy-dark"
                    title="View Submissions"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </a>
                  <a
                    :href="`/admin/competitions/${competition.id}/submissions`"
                    class="text-burgundy hover:text-burgundy-dark"
                    title="Moderate Submissions"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </a>
                  <a
                    :href="`/admin/competitions/${competition.id}/edit`"
                    class="text-burgundy hover:text-burgundy-dark"
                    title="Edit"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </a>
                  <button
                    @click="deleteCompetition(competition.id)"
                    class="text-red-600 hover:text-red-900"
                    title="Delete"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div v-if="competitions.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Showing <span class="font-medium">{{ competitions.from }}</span> to <span class="font-medium">{{ competitions.to }}</span> of
              <span class="font-medium">{{ competitions.total }}</span> results
            </div>
            <div class="flex gap-2">
              <button
                v-for="(link, index) in competitions.links"
                :key="index"
                @click="link.url ? navigateToPage(link.url) : null"
                v-html="link.label"
                :disabled="!link.url"
                :class="[
                  'px-3 py-2 text-sm font-medium rounded-lg',
                  link.active ? 'bg-burgundy text-white' : 'bg-white text-gray-700 hover:bg-gray-50',
                  !link.url ? 'opacity-50 cursor-not-allowed' : ''
                ]"
              />
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="competitions.data.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No competitions found</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by creating a new competition.</p>
          <div class="mt-6">
            <a
              href="/admin/competitions/create"
              class="inline-flex items-center px-4 py-2 bg-burgundy text-white rounded-lg font-medium hover:bg-burgundy-dark"
            >
              Create Competition
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import AdminHeader from '../../../components/AdminHeader.vue';
import AdminQuickNav from '../../../components/AdminQuickNav.vue';

export default {
  components: {
    AdminHeader,
    AdminQuickNav
  },
  data() {
    return {
      competitions: { data: [], links: [], from: 0, to: 0, total: 0 },
      stats: { total: 0, active: 0, upcoming: 0, completed: 0 },
      categories: [],
      form: {
        search: '',
        status: '',
        category_id: '',
        featured: '',
        date_filter: '',
      },
      loading: true,
      showToast: false,
      toastMessage: '',
      searchTimeout: null,
    };
  },

  computed: {
    activeCompetitions() {
      return (this.competitions.data || []).filter(c => c.status === 'active').slice(0, 3);
    },
    draftCompetitions() {
      return (this.competitions.data || []).filter(c => c.status === 'draft').slice(0, 3);
    },
    upcomingDeadlines() {
      const now = new Date();
      return (this.competitions.data || [])
        .filter(c => c.status === 'active' && new Date(c.submission_deadline) > now)
        .sort((a, b) => new Date(a.submission_deadline) - new Date(b.submission_deadline))
        .slice(0, 5);
    },
    recentSubmissions() {
      // This would need separate API call, return empty for now
      return [];
    }
  },

  mounted() {
    this.fetchCategories();
    this.fetchCompetitions();
  },

  methods: {
    async fetchCategories() {
      try {
        const response = await axios.get('/api/v1/categories');
        if (response.data.status === 'success') {
          this.categories = response.data.data;
        }
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    },

    async fetchCompetitions() {
      try {
        this.loading = true;
        const params = new URLSearchParams();
        if (this.form.search) params.append('search', this.form.search);
        if (this.form.status) params.append('status', this.form.status);
        if (this.form.category_id) params.append('category_id', this.form.category_id);
        if (this.form.featured !== '') params.append('featured', this.form.featured);
        if (this.form.date_filter) params.append('date_filter', this.form.date_filter);

        const token = localStorage.getItem('auth_token');
        const response = await axios.get(`/api/v1/admin/competitions?${params.toString()}`, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
        
        // Extract competitions from response
        if (response.data.status === 'success') {
          // Build pagination structure from meta
          const meta = response.data.meta || {};
          this.competitions = {
            data: response.data.data || [],
            links: this.buildPaginationLinks(meta),
            from: ((meta.current_page - 1) * meta.per_page) + 1,
            to: Math.min(meta.current_page * meta.per_page, meta.total),
            total: meta.total || 0,
            current_page: meta.current_page || 1,
            last_page: meta.last_page || 1
          };
          
          this.stats = response.data.stats || {
            total: 0,
            active: 0,
            upcoming: 0,
            completed: 0,
            totalSubmissions: 0,
            totalParticipants: 0,
            totalPrizePool: 0
          };
        }
      } catch (error) {
        console.error('Error fetching competitions:', error);
      } finally {
        this.loading = false;
      }
    },

    buildPaginationLinks(meta) {
      if (!meta.last_page) return [];
      
      const links = [];
      const currentPage = meta.current_page || 1;
      const lastPage = meta.last_page || 1;
      
      // Previous button
      links.push({
        url: currentPage > 1 ? `?page=${currentPage - 1}` : null,
        label: '&laquo; Previous',
        active: false
      });
      
      // Page numbers
      for (let i = 1; i <= lastPage; i++) {
        links.push({
          url: `?page=${i}`,
          label: String(i),
          active: i === currentPage
        });
      }
      
      // Next button
      links.push({
        url: currentPage < lastPage ? `?page=${currentPage + 1}` : null,
        label: 'Next &raquo;',
        active: false
      });
      
      return links;
    },

    applyFilters() {
      this.fetchCompetitions();
    },

    debounceSearch() {
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout);
      }
      this.searchTimeout = setTimeout(() => {
        this.fetchCompetitions();
      }, 300);
    },

    clearFilters() {
      this.form = {
        search: '',
        status: '',
        date_filter: '',
        category_id: '',
        featured: '',
      };
      this.fetchCompetitions();
    },

    deleteCompetition(id) {
      if (confirm('Are you sure you want to delete this competition? This action cannot be undone.')) {
        // Handle delete via API
        console.log('Delete competition:', id);
      }
    },

    statusClass(status) {
      const classes = {
        draft: 'bg-gray-100 text-gray-800',
        active: 'status-active',
        judging: 'bg-info-100 text-info-800',
        completed: 'bg-primary-100 text-primary-800',
        cancelled: 'bg-red-100 text-red-800',
      };
      return classes[status] || 'bg-gray-100 text-gray-800';
    },

    formatDate(date) {
      if (!date) return 'N/A'
      const d = new Date(date)
      const day = String(d.getDate()).padStart(2, '0')
      const month = String(d.getMonth() + 1).padStart(2, '0')
      const year = d.getFullYear()
      return `${day}-${month}-${year}`
    },

    formatNumber(num) {
      return new Intl.NumberFormat('en-BD').format(num);
    },

    formatTimeAgo(date) {
      const now = new Date();
      const past = new Date(date);
      const diffMs = now - past;
      const diffMins = Math.floor(diffMs / 60000);
      const diffHours = Math.floor(diffMs / 3600000);
      const diffDays = Math.floor(diffMs / 86400000);
      
      if (diffMins < 60) return `${diffMins}m ago`;
      if (diffHours < 24) return `${diffHours}h ago`;
      return `${diffDays}d ago`;
    },

    getDaysUntil(date) {
      const now = new Date();
      const target = new Date(date);
      const diffMs = target - now;
      const diffDays = Math.ceil(diffMs / 86400000);
      return diffDays > 0 ? diffDays : 0;
    },

    exportCompetitions() {
      alert('Export functionality coming soon!');
    },

    route(name, params) {
      return `/admin/competitions${params ? '/' + params : ''}`;
    },

    navigateToPage(url) {
      if (url) {
        // Extract query params from URL
        const urlObj = new URL(url, window.location.origin);
        const page = urlObj.searchParams.get('page');
        if (page) {
          this.form.page = page;
          this.fetchCompetitions();
        }
      }
    },
  },
};
</script>
