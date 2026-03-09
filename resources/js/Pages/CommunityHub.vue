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
            <p v-if="feedbackMessage" class="text-xs text-gray-600">{{ feedbackMessage }}</p>
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
                <button
                  v-if="canRemoveDiscussion(item)"
                  @click="removeDiscussion(item.id)"
                  class="hover:text-red-700"
                >
                  Remove
                </button>
              </div>
            </div>
            <p v-if="!hubLoading && discussions.length === 0" class="text-sm text-gray-500">
              No discussions yet. Be the first to start one.
            </p>
          </div>

          <div class="mt-5 border border-gray-100 rounded-xl p-3 sm:p-4 bg-gray-50/70">
            <div class="flex items-center justify-between gap-3 mb-3">
              <h3 class="text-sm sm:text-base font-semibold text-gray-900">Discussion Thread</h3>
              <button
                v-if="activeDiscussion?.id"
                class="text-xs text-rose-700 hover:underline"
                @click="loadDiscussionThread(activeDiscussion.id)"
              >
                Refresh
              </button>
            </div>

            <p v-if="discussionLoading" class="text-sm text-gray-500">Loading thread...</p>

            <div v-else-if="activeDiscussion" class="space-y-3">
              <div class="border border-gray-200 rounded-lg p-3 bg-white">
                <p class="text-sm font-semibold text-gray-900">{{ activeDiscussion.title }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ activeDiscussion.user?.name || 'Community Member' }} • {{ activeDiscussion.category }}</p>
                <p class="text-sm text-gray-700 mt-2">{{ activeDiscussion.content }}</p>
              </div>

              <div class="space-y-2 max-h-56 overflow-auto pr-1">
                <div v-for="comment in discussionComments" :key="comment.id" class="border border-gray-200 rounded-lg p-2.5 bg-white">
                  <p class="text-xs text-gray-500">{{ comment.user?.name || 'Member' }}</p>
                  <p class="text-sm text-gray-700 mt-1">{{ comment.content }}</p>
                </div>
                <p v-if="discussionComments.length === 0" class="text-xs text-gray-500">No comments yet.</p>
              </div>

              <div class="space-y-2">
                <textarea
                  v-model="discussionCommentDraft"
                  rows="2"
                  placeholder="Write a comment"
                  class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                />
                <button class="rounded-lg bg-gray-900 text-white text-xs px-3 py-2 font-medium hover:bg-black" @click="submitDiscussionComment">
                  Post Comment
                </button>
              </div>
            </div>

            <p v-else class="text-sm text-gray-500">Select a discussion using the comment button to view the thread.</p>
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
              <p v-if="!hubLoading && leaderboard.length === 0" class="text-xs text-gray-500">
                Leaderboard will appear when community activity starts.
              </p>
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
              <p v-if="!hubLoading && mentors.length === 0" class="text-xs text-gray-500">
                No mentors are active right now. Please check again later.
              </p>
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
              <div class="flex items-center gap-2">
                <button class="text-xs px-3 py-1 rounded border border-gray-300 text-gray-700 hover:bg-gray-100" @click="openGroup(group.id)">Open</button>
                <button class="text-xs px-3 py-1 rounded bg-rose-600 text-white hover:bg-rose-700" @click="joinGroup(group.id)">Join</button>
              </div>
            </div>
            <p v-if="!hubLoading && groups.length === 0" class="text-sm text-gray-500">
              No groups available yet.
            </p>
          </div>

          <div class="mt-5 border border-gray-100 rounded-xl p-3 sm:p-4 bg-gray-50/70">
            <div class="flex items-center justify-between gap-3 mb-3">
              <h3 class="text-sm sm:text-base font-semibold text-gray-900">Group Feed</h3>
              <button
                v-if="activeGroup?.id"
                class="text-xs text-rose-700 hover:underline"
                @click="openGroup(activeGroup.id)"
              >
                Refresh
              </button>
            </div>

            <p v-if="groupLoading" class="text-sm text-gray-500">Loading group...</p>

            <div v-else-if="activeGroup" class="space-y-3">
              <div class="border border-gray-200 rounded-lg p-3 bg-white">
                <p class="text-sm font-semibold text-gray-900">{{ activeGroup.name }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ activeGroup.members_count || 0 }} members</p>
                <p class="text-sm text-gray-700 mt-2">{{ activeGroup.description }}</p>
                <button
                  class="mt-2 text-xs text-red-700 hover:underline"
                  @click="leaveGroup(activeGroup.id)"
                >
                  Leave group
                </button>

                <div v-if="canManageGroup(activeGroup)" class="mt-3 pt-3 border-t border-gray-200 space-y-2">
                  <p class="text-xs font-semibold text-gray-800">Group Management</p>
                  <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-2">
                    <select v-model.number="ownershipTargetUserId" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs">
                      <option :value="null">Select member for ownership transfer</option>
                      <option
                        v-for="member in transferableMembers"
                        :key="`owner-${member.user_id}`"
                        :value="member.user_id"
                      >
                        {{ member.user?.name || `User #${member.user_id}` }}
                      </option>
                    </select>
                    <button
                      class="text-xs px-3 py-2 rounded bg-gray-900 text-white hover:bg-black"
                      @click="transferOwnership"
                    >
                      Transfer
                    </button>
                  </div>
                  <button class="text-xs text-red-700 hover:underline" @click="archiveActiveGroup">
                    Archive Group
                  </button>
                </div>
              </div>

              <div class="space-y-2">
                <textarea
                  v-model="groupPostDraft"
                  rows="2"
                  placeholder="Write a post for this group"
                  class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                />
                <button class="rounded-lg bg-gray-900 text-white text-xs px-3 py-2 font-medium hover:bg-black" @click="publishGroupPost">
                  Publish Post
                </button>
              </div>

              <div class="space-y-2 max-h-64 overflow-auto pr-1">
                <div v-for="post in groupPosts" :key="post.id" class="border border-gray-200 rounded-lg p-2.5 bg-white">
                  <p class="text-xs text-gray-500">{{ post.user?.name || 'Member' }}</p>
                  <p class="text-sm text-gray-700 mt-1">{{ post.content }}</p>
                  <button
                    v-if="canRemoveGroupPost(post)"
                    class="mt-1 text-[11px] text-red-700 hover:underline"
                    @click="removeGroupPost(post.id)"
                  >
                    Remove post
                  </button>
                  <div class="mt-2 space-y-1.5">
                    <input
                      v-model="groupCommentDrafts[post.id]"
                      type="text"
                      placeholder="Write a comment"
                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs"
                    />
                    <button class="text-xs px-2.5 py-1 rounded bg-rose-600 text-white hover:bg-rose-700" @click="commentOnGroupPost(post.id)">
                      Comment
                    </button>
                  </div>
                </div>
                <p v-if="groupPosts.length === 0" class="text-xs text-gray-500">No posts yet in this group.</p>
              </div>
            </div>

            <p v-else class="text-sm text-gray-500">Open a group to view and contribute to its feed.</p>
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

          <div class="mt-6 pt-5 border-t border-gray-100">
            <div class="flex items-center justify-between mb-2">
              <h3 class="text-sm font-semibold text-gray-900">Community Notifications</h3>
              <button class="text-xs text-rose-700 hover:underline" @click="loadNotifications">Refresh</button>
            </div>
            <div class="space-y-2 max-h-52 overflow-auto pr-1">
              <div v-for="note in notifications" :key="note.id" class="border border-gray-100 rounded-lg p-2.5">
                <p class="text-xs font-medium text-gray-800">{{ note.title || 'Community update' }}</p>
                <p class="text-xs text-gray-600 mt-1">{{ note.body || note.message || 'You have a new community notification.' }}</p>
                <button
                  v-if="!note.read_at"
                  class="mt-2 text-[11px] px-2 py-1 rounded bg-gray-900 text-white hover:bg-black"
                  @click="markNotificationRead(note.id)"
                >
                  Mark as read
                </button>
              </div>
              <p v-if="!hubLoading && notifications.length === 0" class="text-xs text-gray-500">No notifications yet.</p>
            </div>
          </div>

          <div class="mt-6 pt-5 border-t border-gray-100 space-y-2">
            <h3 class="text-sm font-semibold text-gray-900">Report Content</h3>
            <select v-model="reportForm.reportable_type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
              <option value="discussion">Discussion</option>
              <option value="discussion_comment">Discussion comment</option>
              <option value="group_post">Group post</option>
              <option value="group_post_comment">Group post comment</option>
            </select>
            <input
              v-model.number="reportForm.reportable_id"
              type="number"
              min="1"
              placeholder="Content ID"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
            />
            <input
              v-model="reportForm.reason"
              type="text"
              placeholder="Reason (e.g., spam, abuse)"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
            />
            <textarea
              v-model="reportForm.details"
              rows="2"
              placeholder="Optional details"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
            />
            <button class="rounded-lg bg-rose-600 text-white text-xs px-3 py-2 font-medium hover:bg-rose-700" @click="submitReport">
              Submit Report
            </button>
          </div>
        </article>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../api'

const route = useRoute()
const router = useRouter()

const discussions = ref([])
const groups = ref([])
const mentors = ref([])
const leaderboard = ref([])
const searchQuery = ref('')
const searchResults = ref(null)
const hubLoading = ref(false)
const feedbackMessage = ref('')
const activeDiscussion = ref(null)
const discussionComments = ref([])
const discussionCommentDraft = ref('')
const discussionLoading = ref(false)
const activeGroup = ref(null)
const groupPosts = ref([])
const groupPostDraft = ref('')
const groupCommentDrafts = ref({})
const groupLoading = ref(false)
const notifications = ref([])
const currentUser = ref(JSON.parse(localStorage.getItem('user') || 'null'))
const ownershipTargetUserId = ref(null)

const normalizedRole = computed(() => String(currentUser.value?.role || '').toLowerCase().replace(/\s+/g, '_'))
const isAdminUser = computed(() => ['admin', 'super_admin', 'moderator'].includes(normalizedRole.value))

const reportForm = ref({
  reportable_type: 'discussion',
  reportable_id: null,
  reason: '',
  details: '',
})

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
  hubLoading.value = true
  try {
    const { data } = await api.get('/community/hub')
    const payload = data?.data || {}
    discussions.value = payload.featured_posts || []
    groups.value = payload.local_groups || []
    mentors.value = payload.mentorship_programs || []
    leaderboard.value = payload.top_contributors || []

    // Fallback fetches keep the page useful even when hub sections are sparse.
    const fallbackRequests = []

    if (discussions.value.length === 0) {
      fallbackRequests.push(
        api.get('/community/discussions', { params: { per_page: 6 } })
          .then(({ data: fallbackData }) => {
            discussions.value = fallbackData?.data || []
          })
      )
    }

    if (mentors.value.length === 0) {
      fallbackRequests.push(
        api.get('/community/mentors', { params: { per_page: 6 } })
          .then(({ data: fallbackData }) => {
            mentors.value = fallbackData?.data || []
          })
      )
    }

    if (leaderboard.value.length === 0) {
      fallbackRequests.push(
        api.get('/community/leaderboard')
          .then(({ data: fallbackData }) => {
            leaderboard.value = fallbackData?.data || []
          })
      )
    }

    if (fallbackRequests.length > 0) {
      await Promise.allSettled(fallbackRequests)
    }
  } finally {
    hubLoading.value = false
  }
}

const runSearch = async () => {
  if (!searchQuery.value || searchQuery.value.trim().length < 2) {
    searchResults.value = null
    return
  }

  try {
    const { data } = await api.get('/community/search', { params: { q: searchQuery.value.trim() } })
    searchResults.value = data?.data || null
  } catch (error) {
    feedbackMessage.value = error?.response?.data?.message || 'Search failed. Please try again.'
  }
}

const loadDiscussionThread = async (discussionId) => {
  discussionLoading.value = true
  try {
    const { data } = await api.get(`/community/discussions/${discussionId}`)
    const payload = data?.data || null
    activeDiscussion.value = payload
    discussionComments.value = payload?.comments || []
    feedbackMessage.value = ''
  } catch (error) {
    activeDiscussion.value = null
    discussionComments.value = []
    handleActionError(error, 'Unable to load discussion thread right now.')
  } finally {
    discussionLoading.value = false
  }
}

const handleActionError = (error, fallbackMessage) => {
  const status = error?.response?.status
  if (status === 401 || status === 403) {
    feedbackMessage.value = 'Please sign in to use community actions.'
    router.push('/auth')
    return
  }

  feedbackMessage.value = error?.response?.data?.message || fallbackMessage
}

const createDiscussion = async () => {
  if (!discussionForm.value.title?.trim() || !discussionForm.value.content?.trim()) {
    feedbackMessage.value = 'Title and content are required to start a discussion.'
    return
  }

  const tags = discussionForm.value.tags
    .split(',')
    .map(tag => tag.trim())
    .filter(Boolean)

  try {
    await api.post('/community/discussions', {
      title: discussionForm.value.title,
      content: discussionForm.value.content,
      category: discussionForm.value.category,
      tags,
    })

    discussionForm.value = { title: '', content: '', category: 'photography-techniques', tags: '' }
    feedbackMessage.value = 'Discussion posted successfully.'
    await loadHub()
  } catch (error) {
    handleActionError(error, 'Unable to post discussion right now.')
  }
}

const likeDiscussion = async (discussionId) => {
  try {
    await api.post(`/community/discussions/${discussionId}/like`)
    await loadHub()
  } catch (error) {
    handleActionError(error, 'Unable to like discussion right now.')
  }
}

const shareDiscussion = async (discussionId) => {
  try {
    await api.post(`/community/discussions/${discussionId}/share`)
    feedbackMessage.value = 'Discussion share tracked.'
  } catch (error) {
    handleActionError(error, 'Unable to share discussion right now.')
  }
}

const openDiscussion = (discussionId) => {
  router.push({ path: '/community', query: { discussion: discussionId } })
  loadDiscussionThread(discussionId)
}

const canRemoveDiscussion = (discussion) => {
  const userId = currentUser.value?.id
  if (!userId) return false
  return isAdminUser.value || Number(discussion?.user_id) === Number(userId)
}

const canManageGroup = (group) => {
  const userId = Number(currentUser.value?.id || 0)
  if (!userId) return false
  return isAdminUser.value || Number(group?.created_by) === userId
}

const transferableMembers = computed(() => {
  const members = activeGroup.value?.members || []
  const currentOwnerId = Number(activeGroup.value?.created_by || 0)
  return members.filter(member => Number(member.user_id) !== currentOwnerId)
})

const removeDiscussion = async (discussionId) => {
  if (!confirm('Remove this discussion?')) return

  try {
    await api.delete(`/community/discussions/${discussionId}`)
    if (activeDiscussion.value?.id === discussionId) {
      activeDiscussion.value = null
      discussionComments.value = []
    }
    feedbackMessage.value = 'Discussion removed successfully.'
    await loadHub()
  } catch (error) {
    handleActionError(error, 'Unable to remove discussion right now.')
  }
}

const submitDiscussionComment = async () => {
  if (!activeDiscussion.value?.id) return
  if (!discussionCommentDraft.value.trim()) {
    feedbackMessage.value = 'Comment content is required.'
    return
  }

  try {
    await api.post(`/community/discussions/${activeDiscussion.value.id}/comments`, {
      content: discussionCommentDraft.value.trim(),
    })
    discussionCommentDraft.value = ''
    feedbackMessage.value = 'Comment posted successfully.'
    await Promise.all([loadDiscussionThread(activeDiscussion.value.id), loadHub()])
  } catch (error) {
    handleActionError(error, 'Unable to post comment right now.')
  }
}

const openGroup = async (groupId) => {
  groupLoading.value = true
  try {
    const { data } = await api.get(`/community/groups/${groupId}`)
    const payload = data?.data || null
    activeGroup.value = payload
    groupPosts.value = payload?.posts || []
    ownershipTargetUserId.value = null
    feedbackMessage.value = ''

    router.push({ path: '/community', query: { ...route.query, group: groupId } })
  } catch (error) {
    activeGroup.value = null
    groupPosts.value = []
    handleActionError(error, 'Unable to load group right now.')
  } finally {
    groupLoading.value = false
  }
}

const publishGroupPost = async () => {
  if (!activeGroup.value?.id) return
  if (!groupPostDraft.value.trim()) {
    feedbackMessage.value = 'Post content is required.'
    return
  }

  try {
    await api.post(`/community/groups/${activeGroup.value.id}/posts`, {
      content: groupPostDraft.value.trim(),
      image_url: null,
    })
    groupPostDraft.value = ''
    feedbackMessage.value = 'Group post published successfully.'
    await Promise.all([openGroup(activeGroup.value.id), loadHub()])
  } catch (error) {
    handleActionError(error, 'Unable to publish group post right now.')
  }
}

const commentOnGroupPost = async (postId) => {
  if (!activeGroup.value?.id) return
  const draft = groupCommentDrafts.value[postId]?.trim()
  if (!draft) {
    feedbackMessage.value = 'Comment content is required.'
    return
  }

  try {
    await api.post(`/community/group-posts/${postId}/comments`, {
      content: draft,
    })
    groupCommentDrafts.value = { ...groupCommentDrafts.value, [postId]: '' }
    feedbackMessage.value = 'Comment posted successfully.'
    await openGroup(activeGroup.value.id)
  } catch (error) {
    handleActionError(error, 'Unable to post comment right now.')
  }
}

const canRemoveGroupPost = (post) => {
  const userId = currentUser.value?.id
  if (!userId) return false
  return isAdminUser.value || Number(post?.user_id) === Number(userId)
}

const removeGroupPost = async (postId) => {
  if (!activeGroup.value?.id) return
  if (!confirm('Remove this group post?')) return

  try {
    await api.delete(`/community/group-posts/${postId}`)
    feedbackMessage.value = 'Group post removed successfully.'
    await openGroup(activeGroup.value.id)
    await loadHub()
  } catch (error) {
    handleActionError(error, 'Unable to remove group post right now.')
  }
}

const createGroup = async () => {
  if (!groupForm.value.name?.trim() || !groupForm.value.description?.trim()) {
    feedbackMessage.value = 'Group name and description are required.'
    return
  }

  try {
    await api.post('/community/groups', groupForm.value)
    groupForm.value = { name: '', description: '', type: 'interest', cover_image_url: '' }
    feedbackMessage.value = 'Group created successfully.'
    await loadHub()
  } catch (error) {
    handleActionError(error, 'Unable to create group right now.')
  }
}

const joinGroup = async (groupId) => {
  try {
    await api.post(`/community/groups/${groupId}/join`)
    feedbackMessage.value = 'Joined group successfully.'
    await loadHub()
  } catch (error) {
    handleActionError(error, 'Unable to join group right now.')
  }
}

const leaveGroup = async (groupId) => {
  if (!confirm('Leave this group?')) return

  try {
    await api.delete(`/community/groups/${groupId}/leave`)
    feedbackMessage.value = 'Left group successfully.'
    if (activeGroup.value?.id === groupId) {
      await openGroup(groupId)
    }
    await loadHub()
  } catch (error) {
    handleActionError(error, 'Unable to leave group right now.')
  }
}

const transferOwnership = async () => {
  if (!activeGroup.value?.id) return
  if (!ownershipTargetUserId.value) {
    feedbackMessage.value = 'Please select a member to transfer ownership.'
    return
  }

  if (!confirm('Transfer group ownership to selected member?')) return

  try {
    await api.post(`/community/groups/${activeGroup.value.id}/transfer-ownership`, {
      target_user_id: Number(ownershipTargetUserId.value),
    })
    feedbackMessage.value = 'Ownership transferred successfully.'
    await Promise.all([openGroup(activeGroup.value.id), loadHub()])
  } catch (error) {
    handleActionError(error, 'Unable to transfer ownership right now.')
  }
}

const archiveActiveGroup = async () => {
  if (!activeGroup.value?.id) return
  if (!confirm('Archive this group? It will no longer accept new posts.')) return

  try {
    await api.delete(`/community/groups/${activeGroup.value.id}`)
    feedbackMessage.value = 'Group archived successfully.'
    activeGroup.value = null
    groupPosts.value = []
    await loadHub()
  } catch (error) {
    handleActionError(error, 'Unable to archive group right now.')
  }
}

const requestMentorship = async (mentorUserId) => {
  try {
    await api.post(`/community/mentors/${mentorUserId}/request`, {
      message: 'Hello, I would like mentorship guidance to improve my photography career.',
      preferred_session_type: 'portfolio_review',
    })
    feedbackMessage.value = 'Mentorship request sent successfully.'
  } catch (error) {
    handleActionError(error, 'Unable to send mentorship request right now.')
  }
}

const loadNotifications = async () => {
  try {
    const { data } = await api.get('/community/notifications', { params: { per_page: 10 } })
    notifications.value = data?.data?.data || []
  } catch (error) {
    notifications.value = []
    handleActionError(error, 'Unable to load notifications right now.')
  }
}

const markNotificationRead = async (notificationId) => {
  try {
    await api.post(`/community/notifications/${notificationId}/read`)
    notifications.value = notifications.value.map(note =>
      note.id === notificationId ? { ...note, read_at: note.read_at || new Date().toISOString() } : note
    )
  } catch (error) {
    handleActionError(error, 'Unable to mark notification as read right now.')
  }
}

const submitReport = async () => {
  if (!reportForm.value.reportable_id || !reportForm.value.reason?.trim()) {
    feedbackMessage.value = 'Report content ID and reason are required.'
    return
  }

  try {
    await api.post('/community/reports', {
      reportable_type: reportForm.value.reportable_type,
      reportable_id: Number(reportForm.value.reportable_id),
      reason: reportForm.value.reason.trim(),
      details: reportForm.value.details?.trim() || null,
    })

    reportForm.value = {
      reportable_type: 'discussion',
      reportable_id: null,
      reason: '',
      details: '',
    }
    feedbackMessage.value = 'Content report submitted successfully.'
  } catch (error) {
    handleActionError(error, 'Unable to submit report right now.')
  }
}

onMounted(async () => {
  try {
    await loadHub()
    const discussionId = Number(route.query.discussion)
    const groupId = Number(route.query.group)
    if (!Number.isNaN(discussionId) && discussionId > 0) {
      await loadDiscussionThread(discussionId)
    }
    if (!Number.isNaN(groupId) && groupId > 0) {
      await openGroup(groupId)
    }
    await loadNotifications()
  } catch (error) {
    console.error('Failed to load community hub', error)
  }
})
</script>
