<template>
  <div class="min-h-screen bg-gray-50 py-6 sm:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      <header class="bg-white border border-gray-100 rounded-2xl p-4 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <h1 class="text-2xl sm:text-3xl font-semibold font-serif text-gray-900">Photography Community</h1>
            <p class="text-sm sm:text-base text-gray-600 mt-1">Discuss, collaborate, join local clubs, and grow through mentorship.</p>
          </div>
          <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search posts, groups, members, topics"
              class="w-full sm:w-80 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
              @keyup.enter="runSearch"
            />
            <button
              class="rounded-lg bg-rose-600 text-white text-sm px-4 py-2 font-medium hover:bg-rose-700"
              @click="runSearch"
            >
              Search
            </button>
          </div>
        </div>
      </header>

      <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <article class="xl:col-span-2 bg-white border border-gray-100 rounded-2xl p-4 sm:p-5">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Photography Discussions</h2>
            <span class="text-xs text-gray-500">Gear • Editing • Techniques • Locations</span>
          </div>

          <div class="space-y-3 mb-4">
            <input v-model="discussionForm.title" type="text" placeholder="Discussion title"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
              <input v-model="discussionForm.category" type="text" placeholder="Category (e.g., editing tips)"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
              <input v-model="discussionForm.tags" type="text" placeholder="Tags (comma separated)"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
            </div>
            <textarea v-model="discussionForm.content" rows="3" placeholder="Share your question or insight"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
            <button class="rounded-lg bg-gray-900 text-white text-sm px-4 py-2 font-medium hover:bg-black" @click="createDiscussion">
              Start Discussion
            </button>
          </div>

          <div class="space-y-3">
            <div v-for="item in discussions" :key="item.id" class="border border-gray-100 rounded-xl p-3 sm:p-4">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <h3 class="text-sm sm:text-base font-semibold text-gray-900">{{ item.title }}</h3>
                  <p class="text-xs text-gray-500 mt-1">{{ item.user?.name || 'Community Member' }} • {{ item.category }}</p>
                </div>
                <span v-if="item.is_featured" class="text-[11px] px-2 py-1 rounded-full bg-amber-100 text-amber-800">Featured</span>
              </div>
              <p class="text-sm text-gray-700 mt-2 line-clamp-3">{{ item.content }}</p>
              <div class="flex flex-wrap gap-2 mt-2">
                <span v-for="tag in item.tags || []" :key="`${item.id}-${tag}`" class="text-[11px] px-2 py-1 rounded bg-rose-50 text-rose-700">#{{ tag }}</span>
              </div>
              <div class="flex items-center gap-4 mt-3 text-xs text-gray-600">
                <button @click="likeDiscussion(item.id)" class="hover:text-rose-700">👍 {{ item.likes_count || 0 }}</button>
                <button @click="openDiscussion(item.id)" class="hover:text-rose-700">💬 {{ item.comments_count || 0 }}</button>
                <button @click="shareDiscussion(item.id)" class="hover:text-rose-700">🔗 Share</button>
              </div>
            </div>
          </div>
        </article>

        <aside class="space-y-6">
          <div class="bg-white border border-gray-100 rounded-2xl p-4 sm:p-5">
            <div class="flex items-center justify-between mb-3">
              <h2 class="text-base font-semibold text-gray-900">Top Contributors</h2>
              <router-link to="/community/top-referrers" class="text-xs text-rose-700 hover:underline">More</router-link>
            </div>
            <div class="space-y-2">
              <div v-for="member in leaderboard.slice(0, 5)" :key="member.id" class="flex items-center justify-between text-sm">
                <span class="text-gray-700">#{{ member.rank }} {{ member.name }}</span>
                <span class="text-gray-500">{{ member.score }} pts</span>
              </div>
            </div>
          </div>

          <div class="bg-white border border-gray-100 rounded-2xl p-4 sm:p-5">
            <h2 class="text-base font-semibold text-gray-900 mb-3">Mentorship Programs</h2>
            <div class="space-y-3">
              <div v-for="mentor in mentors.slice(0, 4)" :key="mentor.id" class="border border-gray-100 rounded-lg p-3">
                <p class="text-sm font-medium text-gray-800">{{ mentor.user?.name }}</p>
                <p class="text-xs text-gray-500">{{ mentor.years_experience }} years • {{ mentor.availability_status }}</p>
                <button class="mt-2 text-xs text-rose-700 hover:underline" @click="requestMentorship(mentor.user_id)">Request Mentorship</button>
              </div>
            </div>
          </div>
        </aside>
      </section>

      <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <article class="bg-white border border-gray-100 rounded-2xl p-4 sm:p-5">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold text-gray-900">Local Photography Groups</h2>
            <span class="text-xs text-gray-500">City-based clubs</span>
          </div>
          <div class="space-y-3 mb-4">
            <input v-model="groupForm.name" type="text" placeholder="Group name"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
            <textarea v-model="groupForm.description" rows="2" placeholder="Group description"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
              <select v-model="groupForm.type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="interest">Interest group</option>
                <option value="local_club">Local club</option>
              </select>
              <input v-model="groupForm.cover_image_url" type="url" placeholder="Cover image URL"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
            </div>
            <button class="rounded-lg bg-gray-900 text-white text-sm px-4 py-2 font-medium hover:bg-black" @click="createGroup">Create Group</button>
          </div>
          <div class="space-y-2">
            <div v-for="group in groups" :key="group.id" class="border border-gray-100 rounded-lg p-3 flex items-center justify-between gap-3">
              <div>
                <p class="text-sm font-medium text-gray-800">{{ group.name }}</p>
                <p class="text-xs text-gray-500">{{ group.members_count }} members • {{ group.type === 'local_club' ? 'Local club' : 'Interest group' }}</p>
              </div>
              <button class="text-xs px-3 py-1 rounded bg-rose-600 text-white hover:bg-rose-700" @click="joinGroup(group.id)">Join</button>
            </div>
          </div>
        </article>

        <article class="bg-white border border-gray-100 rounded-2xl p-4 sm:p-5">
          <h2 class="text-lg font-semibold text-gray-900 mb-3">Search Results</h2>
          <div v-if="searchResults" class="space-y-4 text-sm">
            <div>
              <p class="font-medium text-gray-800 mb-1">Posts</p>
              <ul class="space-y-1 text-gray-600">
                <li v-for="post in searchResults.posts || []" :key="`p-${post.id}`">• {{ post.title }}</li>
              </ul>
            </div>
            <div>
              <p class="font-medium text-gray-800 mb-1">Groups</p>
              <ul class="space-y-1 text-gray-600">
                <li v-for="group in searchResults.groups || []" :key="`g-${group.id}`">• {{ group.name }}</li>
              </ul>
            </div>
            <div>
              <p class="font-medium text-gray-800 mb-1">Members</p>
              <ul class="space-y-1 text-gray-600">
                <li v-for="member in searchResults.members || []" :key="`m-${member.id}`">• {{ member.name }} <span class="text-gray-400">@{{ member.username }}</span></li>
              </ul>
            </div>
          </div>
          <p v-else class="text-sm text-gray-500">Use search to find posts, groups, members, and topics.</p>
        </article>
      </section>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'

const router = useRouter()

const discussions = ref([])
const groups = ref([])
const mentors = ref([])
const leaderboard = ref([])
const searchQuery = ref('')
const searchResults = ref(null)

const discussionForm = ref({
  title: '',
  content: '',
  category: 'photography-techniques',
  tags: '',
})

const groupForm = ref({
  name: '',
  description: '',
  type: 'interest',
  cover_image_url: '',
})

const loadHub = async () => {
  const { data } = await api.get('/community/hub')
  const payload = data?.data || {}
  discussions.value = payload.featured_posts || []
  groups.value = payload.local_groups || []
  mentors.value = payload.mentorship_programs || []
  leaderboard.value = payload.top_contributors || []
}

const runSearch = async () => {
  if (!searchQuery.value || searchQuery.value.trim().length < 2) {
    searchResults.value = null
    return
  }

  const { data } = await api.get('/community/search', { params: { q: searchQuery.value.trim() } })
  searchResults.value = data?.data || null
}

const createDiscussion = async () => {
  const tags = discussionForm.value.tags
    .split(',')
    .map(tag => tag.trim())
    .filter(Boolean)

  await api.post('/community/discussions', {
    title: discussionForm.value.title,
    content: discussionForm.value.content,
    category: discussionForm.value.category,
    tags,
  })

  discussionForm.value = { title: '', content: '', category: 'photography-techniques', tags: '' }
  await loadHub()
}

const likeDiscussion = async (discussionId) => {
  await api.post(`/community/discussions/${discussionId}/like`)
  await loadHub()
}

const shareDiscussion = async (discussionId) => {
  await api.post(`/community/discussions/${discussionId}/share`)
}

const openDiscussion = (discussionId) => {
  router.push({ path: '/community', query: { discussion: discussionId } })
}

const createGroup = async () => {
  await api.post('/community/groups', groupForm.value)
  groupForm.value = { name: '', description: '', type: 'interest', cover_image_url: '' }
  await loadHub()
}

const joinGroup = async (groupId) => {
  await api.post(`/community/groups/${groupId}/join`)
  await loadHub()
}

const requestMentorship = async (mentorUserId) => {
  await api.post(`/community/mentors/${mentorUserId}/request`, {
    message: 'Hello, I would like mentorship guidance to improve my photography career.',
    preferred_session_type: 'portfolio_review',
  })
}

onMounted(async () => {
  try {
    await loadHub()
  } catch (error) {
    console.error('Failed to load community hub', error)
  }
})
</script>
