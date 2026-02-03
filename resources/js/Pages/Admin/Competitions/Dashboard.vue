<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader 
      title="🏆 Competition Dashboard" 
      subtitle="Comprehensive overview and management of photography competitions"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <div class="flex justify-end">
        <router-link to="/admin/competitions/create" class="px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Create Competition
        </router-link>
      </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="spinner"></div>
      <p>Loading dashboard...</p>
    </div>

    <div v-else>
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
        <!-- All Competitions -->
        <div class="dashboard-card full-width">
          <div class="card-header">
            <h2 class="card-title">📋 All Competitions ({{ allCompetitions.length }})</h2>
          </div>
          <div v-if="allCompetitions.length > 0" class="competitions-grid">
            <div v-for="comp in allCompetitions" :key="comp.id" class="competition-card">
              <div class="competition-header">
                <div class="competition-title">
                  <h3>{{ comp.title }}</h3>
                  <span :class="['status-badge', statusClass(comp.status)]">{{ comp.status }}</span>
                </div>
                <div class="competition-actions">
                  <router-link :to="`/admin/competitions/${comp.id}`" class="btn-icon" title="View Details">
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
                  <router-link :to="`/admin/competitions/${comp.id}/submissions`" class="btn-icon" title="View Submissions">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </router-link>
                  <button @click="archiveCompetition(comp.id)" class="btn-icon btn-archive" title="Archive">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9-3h4m-4 0a1 1 0 11-2 0m2 0a1 1 0 10-2 0m-6 5h2m4 0h2" />
                    </svg>
                  </button>
                  <button @click="deleteCompetition(comp.id)" class="btn-icon btn-delete" title="Delete">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
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
            <p>No competitions created yet. Create one to get started!</p>
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
            <router-link
              v-for="comp in upcomingDeadlines"
              :key="comp.id"
              :to="`/admin/competitions/${comp.id}/edit`"
              class="deadline-item cursor-pointer hover:bg-gray-50 transition-colors"
            >
              <div class="deadline-info">
                <div class="deadline-title">{{ comp.title }}</div>
                <div class="deadline-date">{{ formatDate(comp.submission_deadline) }}</div>
              </div>
              <div class="deadline-countdown">{{ getDaysUntil(comp.submission_deadline) }} days</div>
            </router-link>
          </div>
          <div v-else class="empty-state-small">
            <p>No upcoming deadlines</p>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="quick-actions-card">
        <h2 class="card-title">⚡ Quick Actions</h2>
        <div class="actions-grid">
          <router-link to="/admin/competitions/create" class="action-button">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Create Competition</span>
          </router-link>
          <router-link to="/admin/competitions?status=active" class="action-button">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <span>View Active</span>
          </router-link>
          <router-link to="/admin/competitions/submissions" class="action-button">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            <span>Review Submissions</span>
          </router-link>
          <button @click="exportReport" class="action-button">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Export Report</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Toast -->
    <div v-if="showToast" class="toast">{{ toastMessage }}</div>
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
      stats: {
        total: 0,
        active: 0,
        upcoming: 0,
        completed: 0,
        totalSubmissions: 0,
        totalParticipants: 0,
        totalPrizePool: 0,
        pendingSubmissions: 0
      },
      activeCompetitions: [],
      upcomingCompetitions: [],
      completedCompetitions: [],
      allCompetitions: [],
      recentSubmissions: [],
      upcomingDeadlines: [],
      loading: true,
      showToast: false,
      toastMessage: ''
    };
  },

  computed: {
  },

  mounted() {
    this.fetchDashboardData();
  },

  methods: {
    async fetchDashboardData() {
      try {
        this.loading = true;
        
        const token = localStorage.getItem('auth_token');
        
        // Fetch from API (axios already has /api/v1 as base URL)
        const response = await axios.get('/admin/competitions?stats_scope=dashboard', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
        
        if (response.data.status === 'success') {
          // Update stats from API (don't recalculate)
          if (response.data.stats) {
            this.stats = { ...this.stats, ...response.data.stats };
          }
          
          const lists = response.data.lists || {};
          this.activeCompetitions = lists.active || [];
          this.upcomingCompetitions = lists.upcoming || [];
          this.completedCompetitions = lists.completed || [];

          const allComps = response.data.data || [];
          this.allCompetitions = allComps;

          // Extract upcoming deadlines (next 7 days)
          const now = new Date();
          const sevenDaysFromNow = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000);
          this.upcomingDeadlines = allComps
            .filter(c => {
              const deadline = new Date(c.submission_deadline);
              return deadline > now && deadline <= sevenDaysFromNow;
            })
            .sort((a, b) => new Date(a.submission_deadline) - new Date(b.submission_deadline))
            .slice(0, 5);

          // Clear mock data - submissions will be shown only when fetched from real API
          this.recentSubmissions = [];
        }
      } catch (error) {
        console.error('Error fetching dashboard data:', error);
        this.showToastMessage('Error loading dashboard data');
      } finally {
        this.loading = false;
      }
    },

    exportReport() {
      this.showToastMessage('Export functionality coming soon');
    },

    statusClass(status) {
      const classes = {
        draft: 'status-draft',
        upcoming: 'status-upcoming',
        active: 'status-active',
        judging: 'status-judging',
        completed: 'status-completed',
        cancelled: 'status-cancelled',
        pending: 'status-pending',
        approved: 'status-active'
      };
      return classes[status] || 'status-draft';
    },

    formatDate(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
      });
    },

    formatNumber(num) {
      if (num === null || num === undefined || isNaN(num)) {
        return '0';
      }
      return new Intl.NumberFormat('en-US').format(num);
    },

    formatTimeAgo(date) {
      if (!date) return 'N/A';
      
      const now = new Date();
      const past = new Date(date);
      const diffMs = now - past;
      const diffMins = Math.floor(diffMs / 60000);
      const diffHours = Math.floor(diffMs / 3600000);
      const diffDays = Math.floor(diffMs / 86400000);

      if (diffMins < 1) return 'Just now';
      if (diffMins < 60) return `${diffMins} min ago`;
      if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
      if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
      
      return this.formatDate(date);
    },

    getDaysUntil(date) {
      if (!date) return 0;
      
      const now = new Date();
      const future = new Date(date);
      const diffMs = future - now;
      const diffDays = Math.ceil(diffMs / 86400000);
      
      return diffDays > 0 ? diffDays : 0;
    },

    async deleteCompetition(id) {
      if (!confirm('Are you sure you want to delete this competition? This action cannot be undone.')) {
        return;
      }

      try {
        const token = localStorage.getItem('auth_token');
        const response = await axios.delete(`/api/v1/admin/competitions/${id}`, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });

        if (response.data.status === 'success') {
          this.showToastMessage('Competition deleted successfully', 'success');
          // Refresh the list
          this.fetchDashboardData();
        }
      } catch (error) {
        console.error('Error deleting competition:', error);
        this.showToastMessage('Failed to delete competition', 'error');
      }
    },

    async archiveCompetition(id) {
      if (!confirm('Are you sure you want to archive this competition?')) {
        return;
      }

      try {
        const token = localStorage.getItem('auth_token');
        const response = await axios.put(`/api/v1/admin/competitions/${id}`, 
          { is_archived: true },
          {
            headers: {
              'Authorization': `Bearer ${token}`,
              'Accept': 'application/json'
            }
          }
        );

        if (response.data.status === 'success' || response.status === 200) {
          this.showToastMessage('Competition archived successfully', 'success');
          // Refresh the list
          this.fetchDashboardData();
        }
      } catch (error) {
        console.error('Error archiving competition:', error);
        this.showToastMessage('Failed to archive competition', 'error');
      }
    },

    showToastMessage(message) {
      this.toastMessage = message;
      this.showToast = true;
      setTimeout(() => {
        this.showToast = false;
      }, 3000);
    }
  }
};
</script>

<style scoped>
.competitions-dashboard {
  padding: 2rem;
  background: #f8f9fa;
  min-height: 100vh;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.page-subtitle {
  color: #6b7280;
  margin-top: 0.25rem;
}

.btn-primary {
  display: inline-flex;
  align-items: center;
  padding: 0.75rem 1.5rem;
  background: var(--admin-brand-primary);
  color: white;
  border-radius: 0.5rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
}

.btn-primary:hover {
  background: var(--admin-brand-primary-hover);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(139, 21, 56, 0.3);
}

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem;
  gap: 1rem;
}

.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid #e5e7eb;
  border-top-color: var(--admin-brand-primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

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
  display: flex;
  align-items: center;
  gap: 1rem;
  border-left: 4px solid;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-blue { border-color: var(--admin-info); }
.stat-green { border-color: var(--admin-success); }
.stat-yellow { border-color: var(--admin-warning); }
.stat-purple { border-color: var(--admin-info); }

.stat-icon {
  padding: 1rem;
  border-radius: 0.75rem;
  flex-shrink: 0;
}

.stat-blue .stat-icon { background: var(--admin-info-light); color: var(--admin-info); }
.stat-green .stat-icon { background: var(--admin-success-light); color: var(--admin-success); }
.stat-yellow .stat-icon { background: var(--admin-warning-light); color: var(--admin-warning); }
.stat-purple .stat-icon { background: var(--admin-info-light); color: var(--admin-info); }

.stat-content {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.stat-label {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
}

.stat-trend {
  font-size: 0.75rem;
  color: #9ca3af;
}

.secondary-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-box {
  background: white;
  border-radius: 0.75rem;
  padding: 1.25rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.stat-box-icon {
  padding: 0.75rem;
  background: var(--admin-brand-primary-soft);
  border-radius: 0.5rem;
  color: var(--admin-brand-primary);
}

.stat-box-content {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.stat-box-label {
  font-size: 0.75rem;
  color: #6b7280;
  font-weight: 500;
}

.stat-box-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.dashboard-card {
  background: white;
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.dashboard-card.full-width {
  grid-column: 1 / -1;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.card-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.card-link {
  color: var(--admin-brand-primary);
  font-size: 0.875rem;
  font-weight: 600;
  text-decoration: none;
  transition: color 0.2s;
}

.card-link:hover {
  color: var(--admin-brand-primary-hover);
}

.competitions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.competition-card {
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1.25rem;
  transition: all 0.2s;
}

.competition-card:hover {
  border-color: var(--admin-brand-primary);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.competition-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.competition-title h3 {
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
}

.competition-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-icon {
  padding: 0.5rem;
  border-radius: 0.375rem;
  color: #6b7280;
  transition: all 0.2s;
  text-decoration: none;
}

.btn-icon:hover {
  background: var(--admin-bg-hover);
  color: var(--admin-brand-primary);
}

.btn-archive:hover {
  color: #f59e0b;
}

.btn-delete:hover {
  color: #ef4444;
}

.competition-meta {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: #6b7280;
}

.competition-dates {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  font-size: 0.875rem;
}

.date-item {
  display: flex;
  gap: 0.5rem;
}

.date-label {
  color: #6b7280;
  font-weight: 500;
}

.date-value {
  color: #1f2937;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.status-draft { background: var(--admin-bg-hover); color: var(--admin-text-secondary); }
.status-upcoming { background: var(--admin-warning-light); color: var(--admin-warning-text); }
.status-active { background: var(--admin-success-light); color: var(--admin-success-text); }
.status-judging { background: var(--admin-info-light); color: var(--admin-info-text); }
.status-completed { background: var(--admin-info-light); color: var(--admin-info-text); }
.status-cancelled { background: var(--admin-danger-light); color: var(--admin-danger-text); }
.status-pending { background: var(--admin-warning-light); color: var(--admin-warning-text); }

.submissions-list, .deadlines-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.submission-item, .deadline-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 0.5rem;
}

.submission-info, .deadline-info {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.submission-user, .deadline-title {
  font-weight: 600;
  color: #1f2937;
  font-size: 0.875rem;
}

.submission-competition, .deadline-date {
  font-size: 0.75rem;
  color: #6b7280;
}

.submission-time {
  font-size: 0.75rem;
  color: #9ca3af;
}

.status-badge-sm {
  padding: 0.25rem 0.5rem;
  border-radius: 0.375rem;
  font-size: 0.75rem;
  font-weight: 600;
}

.deadline-countdown {
  padding: 0.5rem 1rem;
  background: var(--admin-brand-primary);
  color: white;
  border-radius: 0.5rem;
  font-weight: 700;
  font-size: 0.875rem;
}

.empty-state-small {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1rem;
  color: #9ca3af;
  text-align: center;
}

.quick-actions-card {
  background: white;
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-top: 1rem;
}

.action-button {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  padding: 1.5rem 1rem;
  background: #f9fafb;
  border: 2px solid #e5e7eb;
  border-radius: 0.75rem;
  color: #1f2937;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
  cursor: pointer;
}

.action-button:hover {
  background: var(--admin-brand-primary);
  border-color: var(--admin-brand-primary);
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(139, 21, 56, 0.3);
}

.toast {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  background: #1f2937;
  color: white;
  padding: 1rem 1.5rem;
  border-radius: 0.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  animation: slideIn 0.3s ease-out;
  z-index: 1000;
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

@media (max-width: 1024px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
  
  .competitions-grid {
    grid-template-columns: 1fr;
  }
}
</style>
