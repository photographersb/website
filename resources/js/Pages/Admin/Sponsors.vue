<template>
  <div class="min-h-screen">
    <AdminHeader
      title="🤝 Sponsors"
      subtitle="Manage platform sponsors and competition sponsors"
    />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <section class="page-hero">
        <div class="hero-copy">
          <p class="hero-kicker">PARTNER ACCESS</p>
          <h1 class="hero-title">Sponsors, consolidated.</h1>
          <p class="hero-subtitle">
            One place for platform sponsors and competition sponsors.
          </p>
          <div class="hero-actions">
            <button
              class="btn-admin-primary"
              @click="openCreateModal"
            >
              + Add Sponsor
            </button>
            <button
              class="btn-admin-secondary"
              @click="refreshCurrentTab"
            >
              Refresh
            </button>
          </div>
        </div>
        <div class="hero-status">
          <div class="status-card">
            <span class="status-label">Platform</span>
            <span class="status-value">{{ platformSponsors.length }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Competition</span>
            <span class="status-value">{{ competitionSponsors.length }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Active</span>
            <span class="status-value">{{ activeCount }}</span>
          </div>
        </div>
      </section>

      <div class="page-topbar">
        <div class="status-chip">
          Platform sponsors · Competition sponsors
        </div>
      </div>

      <AdminQuickNav />

      <div class="tab-bar">
        <button
          class="tab-button"
          :class="{ active: activeTab === 'platform' }"
          @click="activeTab = 'platform'"
        >
          Platform Sponsors
        </button>
        <button
          class="tab-button"
          :class="{ active: activeTab === 'competition' }"
          @click="activeTab = 'competition'"
        >
          Competition Sponsors
        </button>
      </div>

      <div class="filter-grid">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <select
            v-model="selectedStatus"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg"
          >
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search sponsors..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg"
          >
        </div>
        <div v-if="activeTab === 'competition'">
          <label class="block text-sm font-medium text-gray-700 mb-2">Competition</label>
          <select
            v-model="selectedCompetitionId"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg"
          >
            <option value="">Select competition</option>
            <option
              v-for="competition in competitions"
              :key="competition.id"
              :value="competition.id"
            >
              {{ competition.title }}
            </option>
          </select>
        </div>
      </div>

      <div v-if="activeTab === 'platform'" class="panel">
        <div v-if="loading" class="loading">Loading platform sponsors...</div>
        <div v-else-if="filteredPlatform.length === 0" class="empty">No platform sponsors found.</div>
        <div v-else class="table-wrap">
          <table class="table">
            <thead>
              <tr>
                <th>Logo</th>
                <th>Name</th>
                <th>Website</th>
                <th>Status</th>
                <th>Featured</th>
                <th class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="sponsor in filteredPlatform" :key="sponsor.id">
                <td>
                  <img
                    v-if="sponsor.logo"
                    :src="sponsor.logo"
                    :alt="sponsor.name"
                    class="logo"
                  >
                </td>
                <td>{{ sponsor.name }}</td>
                <td class="text-sm text-gray-600">{{ sponsor.website || sponsor.website_url || '-' }}</td>
                <td>
                  <span :class="['badge', sponsor.status === 'active' ? 'badge-active' : 'badge-inactive']">{{ sponsor.status }}</span>
                </td>
                <td>{{ sponsor.is_featured ? 'Yes' : 'No' }}</td>
                <td class="text-right">
                  <button class="link" @click="openEditPlatform(sponsor)">Edit</button>
                  <button class="link danger" @click="deletePlatformSponsor(sponsor)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div v-else class="panel">
        <div v-if="loading" class="loading">Loading competition sponsors...</div>
        <div v-else-if="!selectedCompetitionId" class="empty">Select a competition to view sponsors.</div>
        <div v-else-if="filteredCompetition.length === 0" class="empty">No competition sponsors found.</div>
        <div v-else class="table-wrap">
          <table class="table">
            <thead>
              <tr>
                <th>Logo</th>
                <th>Name</th>
                <th>Tier</th>
                <th>Website</th>
                <th>Active</th>
                <th class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="sponsor in filteredCompetition" :key="sponsor.id">
                <td>
                  <img
                    v-if="sponsor.logo_url"
                    :src="sponsor.logo_url"
                    :alt="sponsor.name"
                    class="logo"
                  >
                </td>
                <td>{{ sponsor.name }}</td>
                <td class="text-sm text-gray-600">{{ sponsor.tier }}</td>
                <td class="text-sm text-gray-600">{{ sponsor.website_url || '-' }}</td>
                <td>
                  <span :class="['badge', sponsor.is_active ? 'badge-active' : 'badge-inactive']">{{ sponsor.is_active ? 'active' : 'inactive' }}</span>
                </td>
                <td class="text-right">
                  <button class="link" @click="openEditCompetition(sponsor)">Edit</button>
                  <button class="link" @click="toggleCompetitionStatus(sponsor)">Toggle</button>
                  <button class="link danger" @click="deleteCompetitionSponsor(sponsor)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-lg w-full p-6 space-y-4">
        <h2 class="text-xl font-bold text-gray-900">
          {{ editingPlatform || editingCompetition ? 'Edit Sponsor' : 'Add Sponsor' }}
        </h2>

        <div v-if="activeTab === 'platform'" class="space-y-3">
          <input v-model="platformForm.name" type="text" placeholder="Sponsor Name" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
          <input v-model="platformForm.website" type="text" placeholder="Website" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
          <textarea v-model="platformForm.description" rows="3" placeholder="Description" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
          <div>
            <input v-model="platformForm.logo" type="url" placeholder="Logo URL" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            <input type="file" accept="image/*" class="upload-input mt-2 block text-sm" @change="handlePlatformLogoUpload">
            <p class="mt-1 text-xs text-gray-500">Max 5 MB. JPG/PNG. Min 600x300 px. Recommended 1472x392 px.</p>
            <p v-if="uploadingImages.platform" class="mt-1 text-xs text-gray-500">Uploading...</p>
          </div>
          <select v-model="platformForm.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
          <label class="flex items-center gap-2 text-sm">
            <input v-model="platformForm.is_featured" type="checkbox">
            Featured
          </label>
        </div>

        <div v-else class="space-y-3">
          <input v-model="competitionForm.name" type="text" placeholder="Sponsor Name" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
          <input v-model="competitionForm.website_url" type="text" placeholder="Website URL" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
          <textarea v-model="competitionForm.description" rows="3" placeholder="Description" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
          <div class="grid grid-cols-2 gap-3">
            <select v-model="competitionForm.tier" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
              <option value="platinum">Platinum</option>
              <option value="gold">Gold</option>
              <option value="silver">Silver</option>
              <option value="bronze">Bronze</option>
            </select>
            <input v-model="competitionForm.contribution_amount" type="number" step="0.01" placeholder="Contribution" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
          </div>
          <div class="grid grid-cols-2 gap-3">
            <input v-model="competitionForm.display_order" type="number" placeholder="Display Order" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            <label class="flex items-center gap-2 text-sm">
              <input v-model="competitionForm.is_active" type="checkbox">
              Active
            </label>
          </div>
          <div>
            <input type="file" accept="image/*" class="upload-input block text-sm" @change="handleCompetitionLogoSelect">
            <p class="mt-1 text-xs text-gray-500">Max 5 MB. JPG/PNG. Min 600x300 px. Recommended 1472x392 px.</p>
            <p v-if="competitionForm.logo_url" class="mt-2 text-xs text-gray-500">Current logo set.</p>
          </div>
        </div>

        <div class="flex gap-3 pt-4">
          <button class="btn-admin-secondary flex-1" @click="closeModal">Cancel</button>
          <button class="btn-admin-primary flex-1" @click="saveSponsor">Save</button>
        </div>
      </div>
    </div>

    <Toast
      v-if="toast.show"
      :message="toast.message"
      :type="toast.type"
      @close="toast.show = false"
    />

    <PexelsPickerModal
      :visible="pexelsPickerOpen"
      :target-width="pexelsTarget.width"
      :target-height="pexelsTarget.height"
      @close="closePexelsPicker"
      @select="handlePexelsSelect"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import AdminHeader from '../../components/AdminHeader.vue'
import AdminQuickNav from '../../components/AdminQuickNav.vue'
import Toast from '../../components/ui/Toast.vue'
import PexelsPickerModal from '../../components/PexelsPickerModal.vue'
import api from '../../api'
import { validateUploadFile } from '../../utils/imageValidation'

const loading = ref(false)
const toast = ref({ show: false, message: '', type: 'success' })
const selectedStatus = ref('')
const searchQuery = ref('')
const activeTab = ref('platform')
const showModal = ref(false)
const pexelsPickerOpen = ref(false)
const pexelsTarget = ref({
  context: 'platform',
  width: 600,
  height: 300
})

const platformSponsors = ref([])
const competitions = ref([])
const selectedCompetitionId = ref('')
const competitionSponsors = ref([])

const uploadingImages = ref({ platform: false })
const competitionLogoFile = ref(null)
const LOGO_MAX_BYTES = 5 * 1024 * 1024
const LOGO_MIN_WIDTH = 600
const LOGO_MIN_HEIGHT = 300
const LOGO_RATIO = 1472 / 392
const LOGO_RATIO_TOLERANCE = 0.2

const platformForm = ref({
  name: '',
  website: '',
  description: '',
  logo: '',
  logo_credit_name: '',
  logo_credit_url: '',
  status: 'active',
  is_featured: false,
  display_order: 0,
  start_date: '',
  end_date: ''
})

const competitionForm = ref({
  name: '',
  website_url: '',
  description: '',
  tier: 'bronze',
  contribution_amount: '',
  display_order: 0,
  is_active: true,
  logo_url: '',
  logo_credit_name: '',
  logo_credit_url: ''
})

const editingPlatform = ref(null)
const editingCompetition = ref(null)

const filteredPlatform = computed(() => {
  return platformSponsors.value.filter(s => {
    const statusMatch = !selectedStatus.value || s.status === selectedStatus.value
    const searchMatch = !searchQuery.value || s.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    return statusMatch && searchMatch
  })
})

const filteredCompetition = computed(() => {
  return competitionSponsors.value.filter(s => {
    const statusValue = s.is_active ? 'active' : 'inactive'
    const statusMatch = !selectedStatus.value || statusValue === selectedStatus.value
    const searchMatch = !searchQuery.value || s.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    return statusMatch && searchMatch
  })
})

const activeCount = computed(() => {
  const platformActive = platformSponsors.value.filter(s => s.status === 'active').length
  const competitionActive = competitionSponsors.value.filter(s => s.is_active).length
  return platformActive + competitionActive
})

const showToast = (message, type = 'success') => {
  toast.value = { show: true, message, type }
}

const readImageDimensions = (file) => new Promise((resolve, reject) => {
  const img = new Image()
  const url = URL.createObjectURL(file)
  img.onload = () => {
    resolve({ width: img.width, height: img.height })
    URL.revokeObjectURL(url)
  }
  img.onerror = () => {
    URL.revokeObjectURL(url)
    reject(new Error('Invalid image'))
  }
  img.src = url
})

const warnOnLogoRatio = async (file) => {
  try {
    const { width, height } = await readImageDimensions(file)
    if (!width || !height) return
    const ratio = width / height
    const minRatio = LOGO_RATIO * (1 - LOGO_RATIO_TOLERANCE)
    const maxRatio = LOGO_RATIO * (1 + LOGO_RATIO_TOLERANCE)
    if (ratio < minRatio || ratio > maxRatio) {
      showToast(`Logo ratio looks off (${width}x${height}). Best results at 1472x392.`, 'error')
    }
  } catch (error) {
    showToast('Logo is not a valid image.', 'error')
  }
}

const validateLogoFile = async (file) => {
  if (!file) {
    return false
  }

  const validation = await validateUploadFile(file, {
    label: 'Logo',
    maxBytes: LOGO_MAX_BYTES,
    allowedTypes: ['image/jpeg', 'image/png'],
    minImageWidth: LOGO_MIN_WIDTH,
    minImageHeight: LOGO_MIN_HEIGHT
  })

  if (!validation.ok) {
    showToast(validation.message, 'error')
    return false
  }

  await warnOnLogoRatio(file)

  return true
}

const resetForms = () => {
  platformForm.value = {
    name: '',
    website: '',
    description: '',
    logo: '',
    logo_credit_name: '',
    logo_credit_url: '',
    status: 'active',
    is_featured: false,
    display_order: 0,
    start_date: '',
    end_date: ''
  }
  competitionForm.value = {
    name: '',
    website_url: '',
    description: '',
    tier: 'bronze',
    contribution_amount: '',
    display_order: 0,
    is_active: true,
    logo_url: '',
    logo_credit_name: '',
    logo_credit_url: ''
  }
  competitionLogoFile.value = null
}

const fetchPlatformSponsors = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/platform-sponsors')
    platformSponsors.value = data?.data || []
  } catch (error) {
    showToast('Failed to load platform sponsors', 'error')
  } finally {
    loading.value = false
  }
}

const fetchCompetitions = async () => {
  try {
    const { data } = await api.get('/admin/competitions')
    competitions.value = data?.data || []
    if (!selectedCompetitionId.value && competitions.value.length > 0) {
      selectedCompetitionId.value = competitions.value[0].id
    }
  } catch (error) {
    showToast('Failed to load competitions', 'error')
  }
}

const fetchCompetitionSponsors = async () => {
  if (!selectedCompetitionId.value) {
    competitionSponsors.value = []
    return
  }
  loading.value = true
  try {
    const { data } = await api.get(`/competitions/${selectedCompetitionId.value}/sponsors`, {
      params: { active_only: false }
    })
    competitionSponsors.value = data?.data?.all || []
  } catch (error) {
    showToast('Failed to load competition sponsors', 'error')
  } finally {
    loading.value = false
  }
}

const refreshCurrentTab = async () => {
  if (activeTab.value === 'platform') {
    await fetchPlatformSponsors()
  } else {
    await fetchCompetitionSponsors()
  }
}

const openCreateModal = () => {
  editingPlatform.value = null
  editingCompetition.value = null
  resetForms()
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const openEditPlatform = (sponsor) => {
  editingPlatform.value = sponsor
  editingCompetition.value = null
  platformForm.value = {
    name: sponsor.name || '',
    website: sponsor.website || sponsor.website_url || '',
    description: sponsor.description || '',
    logo: sponsor.logo || '',
    logo_credit_name: sponsor.logo_credit_name || '',
    logo_credit_url: sponsor.logo_credit_url || '',
    status: sponsor.status || 'active',
    is_featured: !!sponsor.is_featured,
    display_order: sponsor.display_order || 0,
    start_date: sponsor.start_date || '',
    end_date: sponsor.end_date || ''
  }
  showModal.value = true
}

const openEditCompetition = (sponsor) => {
  editingCompetition.value = sponsor
  editingPlatform.value = null
  competitionForm.value = {
    name: sponsor.name || '',
    website_url: sponsor.website_url || '',
    description: sponsor.description || '',
    tier: sponsor.tier || 'bronze',
    contribution_amount: sponsor.contribution_amount || '',
    display_order: sponsor.display_order || 0,
    is_active: sponsor.is_active ?? true,
    logo_url: sponsor.logo_url || '',
    logo_credit_name: sponsor.logo_credit_name || '',
    logo_credit_url: sponsor.logo_credit_url || ''
  }
  competitionLogoFile.value = null
  showModal.value = true
}

const saveSponsor = async () => {
  try {
    if (activeTab.value === 'platform') {
      if (editingPlatform.value) {
        await api.put(`/admin/platform-sponsors/${editingPlatform.value.id}`, platformForm.value)
      } else {
        await api.post('/admin/platform-sponsors', platformForm.value)
      }
      showToast('Platform sponsor saved')
      await fetchPlatformSponsors()
    } else {
      if (!selectedCompetitionId.value) {
        showToast('Select a competition first', 'error')
        return
      }
      const payload = new FormData()
      payload.append('name', competitionForm.value.name)
      if (competitionForm.value.website_url) payload.append('website_url', competitionForm.value.website_url)
      if (competitionForm.value.description) payload.append('description', competitionForm.value.description)
      if (competitionForm.value.tier) payload.append('tier', competitionForm.value.tier)
      if (competitionForm.value.contribution_amount !== '') payload.append('contribution_amount', competitionForm.value.contribution_amount)
      if (competitionForm.value.display_order !== '') payload.append('display_order', competitionForm.value.display_order)
      if (competitionForm.value.logo_credit_name) payload.append('logo_credit_name', competitionForm.value.logo_credit_name)
      if (competitionForm.value.logo_credit_url) payload.append('logo_credit_url', competitionForm.value.logo_credit_url)
      payload.append('is_active', competitionForm.value.is_active ? 1 : 0)
      if (competitionLogoFile.value) payload.append('logo', competitionLogoFile.value)

      if (editingCompetition.value) {
        payload.append('_method', 'PUT')
        await api.post(`/competition-sponsors/${editingCompetition.value.id}`, payload, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
      } else {
        await api.post(`/competitions/${selectedCompetitionId.value}/sponsors`, payload, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
      }
      showToast('Competition sponsor saved')
      await fetchCompetitionSponsors()
    }
    showModal.value = false
  } catch (error) {
    showToast('Failed to save sponsor', 'error')
  }
}

const deletePlatformSponsor = async (sponsor) => {
  if (!window.confirm(`Delete ${sponsor.name}?`)) return
  try {
    await api.delete(`/admin/platform-sponsors/${sponsor.id}`)
    await fetchPlatformSponsors()
    showToast('Platform sponsor deleted')
  } catch (error) {
    showToast('Failed to delete sponsor', 'error')
  }
}

const deleteCompetitionSponsor = async (sponsor) => {
  if (!window.confirm(`Delete ${sponsor.name}?`)) return
  try {
    await api.delete(`/competition-sponsors/${sponsor.id}`)
    await fetchCompetitionSponsors()
    showToast('Competition sponsor deleted')
  } catch (error) {
    showToast('Failed to delete sponsor', 'error')
  }
}

const toggleCompetitionStatus = async (sponsor) => {
  try {
    await api.post(`/competition-sponsors/${sponsor.id}/toggle-active`)
    await fetchCompetitionSponsors()
  } catch (error) {
    showToast('Failed to toggle status', 'error')
  }
}

const handlePlatformLogoUpload = async (event) => {
  const file = event.target.files?.[0]
  if (!file) return
  platformForm.value.logo_credit_name = ''
  platformForm.value.logo_credit_url = ''
  const isValid = await validateLogoFile(file)
  if (!isValid) {
    event.target.value = ''
    return
  }
  uploadingImages.value.platform = true
  try {
    const formData = new FormData()
    formData.append('image', file)
    formData.append('folder', 'sponsors')
    const response = await api.post('/admin/media/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    if (response.data?.status === 'success' && response.data.data?.url) {
      platformForm.value.logo = response.data.data.url
    } else {
      showToast('Logo upload failed', 'error')
    }
  } catch (error) {
    showToast('Logo upload failed', 'error')
  } finally {
    uploadingImages.value.platform = false
    event.target.value = ''
  }
}

const handleCompetitionLogoSelect = (event) => {
  const file = event.target.files?.[0]
  if (!file) {
    competitionLogoFile.value = null
    return
  }
  competitionForm.value.logo_credit_name = ''
  competitionForm.value.logo_credit_url = ''
  validateLogoFile(file).then((isValid) => {
    if (isValid) {
      competitionLogoFile.value = file
    } else {
      competitionLogoFile.value = null
      event.target.value = ''
    }
  })
}

const openPexelsPicker = (context, width, height) => {
  pexelsTarget.value = { context, width, height }
  pexelsPickerOpen.value = true
}

const closePexelsPicker = () => {
  pexelsPickerOpen.value = false
}

const handlePexelsSelect = async ({ file, credit }) => {
  const context = pexelsTarget.value.context
  if (context === 'platform') {
    uploadingImages.value.platform = true
    try {
      const formData = new FormData()
      formData.append('image', file)
      formData.append('folder', 'sponsors')
      const response = await api.post('/admin/media/upload', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      if (response.data?.status === 'success' && response.data.data?.url) {
        platformForm.value.logo = response.data.data.url
        platformForm.value.logo_credit_name = credit?.name || ''
        platformForm.value.logo_credit_url = credit?.url || ''
      } else {
        showToast('Logo upload failed', 'error')
      }
    } catch (error) {
      showToast('Logo upload failed', 'error')
    } finally {
      uploadingImages.value.platform = false
      closePexelsPicker()
    }
    return
  }

  competitionLogoFile.value = file
  competitionForm.value.logo_url = competitionForm.value.logo_url || 'selected'
  competitionForm.value.logo_credit_name = credit?.name || ''
  competitionForm.value.logo_credit_url = credit?.url || ''
  closePexelsPicker()
}

watch(selectedCompetitionId, async () => {
  if (activeTab.value === 'competition') {
    await fetchCompetitionSponsors()
  }
})

watch(activeTab, async (tab) => {
  if (tab === 'competition') {
    await fetchCompetitionSponsors()
  }
})

onMounted(async () => {
  await fetchPlatformSponsors()
  await fetchCompetitions()
  if (selectedCompetitionId.value) {
    await fetchCompetitionSponsors()
  }
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
.filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; }
.tab-bar { display: inline-flex; gap: 0.6rem; background: rgba(255,255,255,0.9); border: 1px solid rgba(140,108,95,0.2); border-radius: 999px; padding: 0.35rem; }
.tab-button { padding: 0.5rem 1rem; border-radius: 999px; font-weight: 600; font-size: 0.85rem; color: var(--admin-text-secondary); }
.tab-button.active { background: #8e0e3f; color: #fff; }
.panel { background: #fff; border: 1px solid rgba(140,108,95,0.2); border-radius: 1.2rem; padding: 1.5rem; box-shadow: 0 18px 35px rgba(18, 9, 6, 0.08); }
.table-wrap { overflow-x: auto; }
.table { width: 100%; border-collapse: collapse; }
.table th { text-align: left; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.12em; color: #6b7280; padding: 0.75rem 0.5rem; border-bottom: 1px solid #e5e7eb; }
.table td { padding: 0.75rem 0.5rem; border-bottom: 1px solid #f1f1f1; }
.logo { width: 44px; height: 44px; object-fit: contain; background: #f9fafb; border-radius: 0.5rem; padding: 0.25rem; }
.badge { display: inline-flex; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
.badge-active { background: #dcfce7; color: #166534; }
.badge-inactive { background: #e5e7eb; color: #374151; }
.link { color: #8e0e3f; margin-left: 0.75rem; font-weight: 600; }
.link.danger { color: #dc2626; }
.loading { padding: 2rem; text-align: center; color: #6b7280; }
.empty { padding: 2rem; text-align: center; color: #6b7280; }
@media (max-width: 1024px) { .page-hero { grid-template-columns: 1fr; } }
</style>
