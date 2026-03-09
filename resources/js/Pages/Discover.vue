<template>
  <div class="min-h-screen bg-[#f7f2ee] py-6 sm:py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 xl:px-12 max-w-7xl space-y-6 sm:space-y-8">
      <section class="rounded-2xl bg-[#1b0b12] text-white p-5 sm:p-8">
        <div class="flex flex-col gap-4 sm:gap-6">
          <div>
            <h1 class="text-2xl sm:text-4xl font-semibold font-serif">Discover Photography Inspiration</h1>
            <p class="text-white/85 text-sm sm:text-base mt-2">
              Explore trending photographers, photos, competitions, events, categories, and locations in one place.
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-12 gap-3 sm:gap-4">
            <div class="md:col-span-5">
              <label for="discover-query" class="sr-only">Search discovery</label>
              <input
                id="discover-query"
                v-model="search.query"
                type="text"
                placeholder="Search photographers, events, competitions, categories, cities..."
                class="w-full min-h-[44px] px-4 py-2 rounded-lg border border-white/30 bg-white/10 text-white placeholder:text-white/65 focus:outline-none focus:ring-2 focus:ring-amber-300"
                @keyup.enter="runSearch"
              >
            </div>

            <div class="md:col-span-3">
              <label for="discover-type" class="sr-only">Search type</label>
              <select
                id="discover-type"
                v-model="search.type"
                class="w-full min-h-[44px] px-4 py-2 rounded-lg border border-white/30 bg-white/10 text-white focus:outline-none focus:ring-2 focus:ring-amber-300"
              >
                <option class="text-gray-900" value="all">All Types</option>
                <option class="text-gray-900" value="photographers">Photographers</option>
                <option class="text-gray-900" value="photos">Photos</option>
                <option class="text-gray-900" value="competitions">Competitions</option>
                <option class="text-gray-900" value="events">Events</option>
                <option class="text-gray-900" value="categories">Categories</option>
                <option class="text-gray-900" value="cities">Cities</option>
              </select>
            </div>

            <div class="md:col-span-2">
              <label for="discover-category" class="sr-only">Category filter</label>
              <select
                id="discover-category"
                v-model="search.category"
                class="w-full min-h-[44px] px-4 py-2 rounded-lg border border-white/30 bg-white/10 text-white focus:outline-none focus:ring-2 focus:ring-amber-300"
              >
                <option class="text-gray-900" value="">All Categories</option>
                <option
                  v-for="item in hub.popular_categories"
                  :key="item.slug"
                  class="text-gray-900"
                  :value="item.slug"
                >
                  {{ item.name }}
                </option>
              </select>
            </div>

            <div class="md:col-span-2 flex gap-2">
              <label for="discover-city" class="sr-only">City filter</label>
              <select
                id="discover-city"
                v-model="search.city"
                class="w-full min-h-[44px] px-4 py-2 rounded-lg border border-white/30 bg-white/10 text-white focus:outline-none focus:ring-2 focus:ring-amber-300"
              >
                <option class="text-gray-900" value="">All Cities</option>
                <option
                  v-for="item in hub.popular_locations"
                  :key="item.slug"
                  class="text-gray-900"
                  :value="item.slug"
                >
                  {{ item.name }}
                </option>
              </select>
              <button
                type="button"
                class="min-h-[44px] px-4 rounded-lg bg-amber-400 text-[#1b0b12] font-semibold hover:bg-amber-300"
                @click="runSearch"
              >
                Go
              </button>
            </div>
          </div>
        </div>
      </section>

      <section v-if="showSearch" class="bg-white rounded-2xl border border-[#eadfd7] p-4 sm:p-6">
        <div class="flex items-center justify-between gap-2 mb-4">
          <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Search Results</h2>
          <button class="text-sm font-medium text-[#7a1f2b] hover:underline" @click="clearSearch">Clear</button>
        </div>

        <div v-if="searchLoading" class="py-8 text-center text-gray-600" role="status" aria-live="polite">Searching...</div>

        <div v-else class="space-y-4">
          <div v-if="searchResults.photographers?.length">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Photographers</h3>
            <div class="overflow-x-auto pb-2">
              <div class="flex gap-3 min-w-max">
                <button
                  v-for="item in searchResults.photographers"
                  :key="`sr-ph-${item.id}`"
                  class="w-64 text-left bg-gray-50 rounded-xl border border-gray-200 p-3 hover:border-[#7a1f2b]"
                  @click="viewPhotographer(item)"
                >
                  <p class="font-semibold text-gray-900 line-clamp-1">{{ item.user?.name || 'Photographer' }}</p>
                  <p class="text-xs text-gray-600 mt-1">{{ item.city?.name || 'Bangladesh' }}</p>
                </button>
              </div>
            </div>
          </div>

          <div v-if="searchResults.photos?.length">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Photos</h3>
            <div class="overflow-x-auto pb-2">
              <div class="min-w-[700px] columns-2 md:columns-3 gap-4">
                <button
                  v-for="item in searchResults.photos"
                  :key="`sr-photo-${item.id}`"
                  class="mb-4 break-inside-avoid w-full text-left"
                  @click="openPhotoViewer(item)"
                >
                  <img
                    :src="item.thumbnail_url || item.image_url"
                    :alt="item.title || 'Discovery photo'"
                    class="w-full rounded-lg object-cover"
                    loading="lazy"
                    decoding="async"
                  >
                  <p class="text-xs mt-2 text-gray-700 line-clamp-1">{{ item.title || 'Untitled photo' }}</p>
                </button>
              </div>
            </div>
          </div>

          <div v-if="searchResults.competitions?.length">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Competitions</h3>
            <div class="overflow-x-auto pb-2">
              <div class="flex gap-3 min-w-max">
                <button
                  v-for="item in searchResults.competitions"
                  :key="`sr-co-${item.id}`"
                  class="w-64 text-left bg-gray-50 rounded-xl border border-gray-200 p-3 hover:border-[#7a1f2b]"
                  @click="viewCompetition(item)"
                >
                  <p class="font-semibold text-gray-900 line-clamp-1">{{ item.title }}</p>
                  <p class="text-xs text-gray-600 mt-1">{{ item.theme || 'Photography' }}</p>
                </button>
              </div>
            </div>
          </div>

          <div v-if="searchResults.events?.length">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Events</h3>
            <div class="overflow-x-auto pb-2">
              <div class="flex gap-3 min-w-max">
                <button
                  v-for="item in searchResults.events"
                  :key="`sr-ev-${item.id}`"
                  class="w-64 text-left bg-gray-50 rounded-xl border border-gray-200 p-3 hover:border-[#7a1f2b]"
                  @click="viewEvent(item)"
                >
                  <p class="font-semibold text-gray-900 line-clamp-1">{{ item.title }}</p>
                  <p class="text-xs text-gray-600 mt-1">{{ formatDate(item.event_date) }}</p>
                </button>
              </div>
            </div>
          </div>

          <div v-if="searchResults.categories?.length || searchResults.cities?.length" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div v-if="searchResults.categories?.length" class="bg-gray-50 rounded-xl border border-gray-200 p-3">
              <h3 class="text-sm font-semibold text-gray-700 mb-2">Categories</h3>
              <div class="space-y-2">
                <button
                  v-for="item in searchResults.categories"
                  :key="`sr-cat-${item.id}`"
                  class="w-full text-left text-sm text-[#7a1f2b] hover:underline"
                  @click="goToCategory(item.slug)"
                >
                  {{ item.name }}
                </button>
              </div>
            </div>
            <div v-if="searchResults.cities?.length" class="bg-gray-50 rounded-xl border border-gray-200 p-3">
              <h3 class="text-sm font-semibold text-gray-700 mb-2">Cities</h3>
              <div class="space-y-2">
                <button
                  v-for="item in searchResults.cities"
                  :key="`sr-city-${item.id}`"
                  class="w-full text-left text-sm text-[#7a1f2b] hover:underline"
                  @click="goToLocation(item.slug)"
                >
                  {{ item.name }}
                </button>
              </div>
            </div>
          </div>

          <p
            v-if="!hasAnySearchResult"
            class="text-sm text-gray-600"
          >
            No discovery result found for your current search and filters.
          </p>
        </div>
      </section>

      <section
        v-if="hub.personalized"
        class="bg-white rounded-2xl border border-[#eadfd7] p-4 sm:p-6"
      >
        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Personalized for You</h2>
        <p class="text-sm text-gray-600 mt-1 mb-4">Suggestions based on your city and category profile.</p>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
          <div class="rounded-xl border border-gray-200 p-3">
            <p class="text-sm font-semibold text-gray-700 mb-2">Photographers in your city</p>
            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
              <button
                v-for="item in hub.personalized.photographers_in_city"
                :key="`p-ph-${item.id}`"
                class="w-full text-left text-sm hover:text-[#7a1f2b]"
                @click="viewPhotographer(item)"
              >
                {{ item.user?.name || 'Photographer' }}
              </button>
            </div>
          </div>

          <div class="rounded-xl border border-gray-200 p-3">
            <p class="text-sm font-semibold text-gray-700 mb-2">Events near you</p>
            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
              <button
                v-for="item in hub.personalized.events_near_you"
                :key="`p-ev-${item.id}`"
                class="w-full text-left text-sm hover:text-[#7a1f2b]"
                @click="viewEvent(item)"
              >
                {{ item.title }}
              </button>
            </div>
          </div>

          <div class="rounded-xl border border-gray-200 p-3">
            <p class="text-sm font-semibold text-gray-700 mb-2">Competitions for your style</p>
            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
              <button
                v-for="item in hub.personalized.competitions_for_you"
                :key="`p-co-${item.id}`"
                class="w-full text-left text-sm hover:text-[#7a1f2b]"
                @click="viewCompetition(item)"
              >
                {{ item.title }}
              </button>
            </div>
          </div>
        </div>
      </section>

      <section class="bg-white rounded-2xl border border-[#eadfd7] p-4 sm:p-6">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Trending Photographers</h2>
          <router-link class="text-sm text-[#7a1f2b] font-medium hover:underline" to="/photographers">View all</router-link>
        </div>
        <div class="overflow-x-auto pb-2">
          <div class="flex gap-4 min-w-max">
            <article
              v-for="item in hub.trending_photographers"
              :key="item.id"
              class="w-[260px] bg-gray-50 rounded-xl border border-gray-200 p-3"
            >
              <button class="w-full text-left" @click="viewPhotographer(item)">
                <div class="flex items-center gap-3">
                  <img
                    :src="item.profile_picture || fallbackProfile"
                    :alt="item.user?.name || 'Photographer'"
                    class="w-12 h-12 rounded-full object-cover"
                    loading="lazy"
                    decoding="async"
                  >
                  <div class="min-w-0">
                    <p class="font-semibold text-gray-900 truncate">{{ item.user?.name || 'Photographer' }}</p>
                    <p class="text-xs text-gray-600 truncate">{{ item.city?.name || 'Bangladesh' }}</p>
                  </div>
                </div>

                <div class="mt-2 flex flex-wrap gap-1">
                  <span
                    v-for="cat in (item.categories || []).slice(0, 3)"
                    :key="cat.id"
                    class="text-[11px] px-2 py-1 rounded-full bg-white border border-gray-200 text-gray-700"
                  >
                    {{ cat.name }}
                  </span>
                </div>

                <div class="mt-3 flex items-center justify-between text-xs">
                  <span class="text-gray-600">⭐ {{ Number(item.average_rating || 0).toFixed(1) }}</span>
                  <span class="font-semibold text-emerald-700">
                    {{ item.trust_score?.trust_badge || item.trustScore?.trust_badge || (item.is_verified ? 'Verified' : 'Standard') }}
                  </span>
                </div>
              </button>
            </article>
          </div>
        </div>
      </section>

      <section class="bg-white rounded-2xl border border-[#eadfd7] p-4 sm:p-6">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Trending Photos</h2>
          <button
            class="text-sm text-[#7a1f2b] font-medium hover:underline"
            @click="loadMorePhotos"
          >
            Load more
          </button>
        </div>
        <div class="overflow-x-auto pb-2">
          <div class="min-w-[760px] columns-2 md:columns-3 lg:columns-4 gap-4">
            <button
              v-for="item in photos"
              :key="item.id"
              class="mb-4 break-inside-avoid w-full text-left"
              @click="openPhotoViewer(item)"
            >
              <img
                :src="item.thumbnail_url || item.image_url"
                :alt="item.title || 'Trending photo'"
                class="w-full rounded-lg object-cover"
                loading="lazy"
                decoding="async"
              >
              <div class="mt-2 px-1">
                <p class="text-sm font-semibold text-gray-900 line-clamp-1">{{ item.title || 'Untitled photo' }}</p>
                <p class="text-xs text-gray-600 line-clamp-1">{{ item.photographer_name }} · {{ item.category }}</p>
                <p class="text-xs text-[#7a1f2b] mt-1">{{ item.likes_or_votes }} likes/votes</p>
              </div>
            </button>
          </div>
        </div>
      </section>

      <section class="bg-white rounded-2xl border border-[#eadfd7] p-4 sm:p-6">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Trending Competitions</h2>
          <router-link class="text-sm text-[#7a1f2b] font-medium hover:underline" to="/competitions">View all</router-link>
        </div>
        <div class="overflow-x-auto pb-2">
          <div class="flex gap-4 min-w-max">
            <article
              v-for="item in hub.trending_competitions"
              :key="item.id"
              class="w-[280px] bg-gray-50 rounded-xl border border-gray-200 overflow-hidden"
            >
              <button class="w-full text-left" @click="viewCompetition(item)">
                <img
                  :src="item.cover_image || item.banner_image || fallbackCover"
                  :alt="item.title"
                  class="w-full h-40 object-cover"
                  loading="lazy"
                  decoding="async"
                >
                <div class="p-3">
                  <p class="font-semibold text-gray-900 line-clamp-1">{{ item.title }}</p>
                  <p class="text-xs text-gray-600 mt-1 line-clamp-1">{{ item.theme || 'Photography Theme' }}</p>
                  <div class="mt-2 text-xs text-gray-700 space-y-1">
                    <p>Deadline: {{ formatDate(item.submission_deadline) }}</p>
                    <p>Prize: {{ formatMoney(item.total_prize_pool) }}</p>
                  </div>
                </div>
              </button>
            </article>
          </div>
        </div>
      </section>

      <section class="bg-white rounded-2xl border border-[#eadfd7] p-4 sm:p-6">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Upcoming Events</h2>
          <router-link class="text-sm text-[#7a1f2b] font-medium hover:underline" to="/events">View all</router-link>
        </div>
        <div class="overflow-x-auto pb-2">
          <div class="flex gap-4 min-w-max">
            <article
              v-for="item in hub.upcoming_events"
              :key="item.id"
              class="w-[280px] bg-gray-50 rounded-xl border border-gray-200 overflow-hidden"
            >
              <button class="w-full text-left" @click="viewEvent(item)">
                <img
                  :src="item.hero_image_url || item.banner_image || fallbackCover"
                  :alt="item.title"
                  class="w-full h-40 object-cover"
                  loading="lazy"
                  decoding="async"
                >
                <div class="p-3">
                  <p class="font-semibold text-gray-900 line-clamp-1">{{ item.title }}</p>
                  <div class="mt-2 text-xs text-gray-700 space-y-1">
                    <p>{{ formatDate(item.event_date) }}</p>
                    <p>{{ item.city?.name || 'Bangladesh' }}</p>
                    <p>{{ item.category?.name || 'Photography Event' }}</p>
                  </div>
                </div>
              </button>
            </article>
          </div>
        </div>
      </section>

      <section class="bg-white rounded-2xl border border-[#eadfd7] p-4 sm:p-6">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3">Popular Categories</h2>
        <div class="overflow-x-auto pb-2">
          <div class="flex gap-3 min-w-max">
            <button
              v-for="item in hub.popular_categories"
              :key="item.id"
              class="w-[230px] text-left rounded-xl border border-gray-200 bg-gray-50 p-3 hover:border-[#7a1f2b]"
              @click="goToCategory(item.slug)"
            >
              <p class="font-semibold text-gray-900">{{ item.name }}</p>
              <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ item.description || 'Discover photographers in this style.' }}</p>
              <p class="text-xs text-[#7a1f2b] mt-2">{{ item.photographers_count }} photographers</p>
            </button>
          </div>
        </div>
      </section>

      <section class="bg-white rounded-2xl border border-[#eadfd7] p-4 sm:p-6">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3">Popular Locations</h2>
        <div class="overflow-x-auto pb-2">
          <div class="flex gap-3 min-w-max">
            <article
              v-for="item in hub.popular_locations"
              :key="item.id"
              class="w-[240px] rounded-xl border border-gray-200 bg-gray-50 p-3"
            >
              <p class="font-semibold text-gray-900">{{ item.name }}</p>
              <p class="text-xs text-gray-600 mt-1">{{ item.photographers_count }} photographers · {{ item.events_count }} events</p>
              <div class="mt-3 flex gap-2">
                <button
                  class="min-h-[40px] px-3 py-1.5 text-xs rounded-full bg-[#7a1f2b] text-white hover:bg-[#5f1421]"
                  @click="goToLocation(item.slug)"
                >
                  Photographers
                </button>
                <button
                  class="min-h-[40px] px-3 py-1.5 text-xs rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100"
                  @click="goToEventsByLocation(item.slug)"
                >
                  Events
                </button>
              </div>
            </article>
          </div>
        </div>
      </section>
    </div>

    <div
      v-if="viewer.open"
      class="fixed inset-0 z-50 bg-black/85 p-4 sm:p-8 flex items-center justify-center"
      role="dialog"
      aria-modal="true"
      aria-label="Photo viewer"
      @click.self="closeViewer"
    >
      <div class="w-full max-w-5xl max-h-[92vh] overflow-auto">
        <div class="flex justify-end mb-2">
          <button
            class="min-h-[40px] px-3 py-1.5 rounded-lg bg-white text-gray-900 text-sm font-semibold"
            @click="closeViewer"
          >
            Close
          </button>
        </div>
        <img
          :src="viewer.photo?.image_url || viewer.photo?.thumbnail_url"
          :alt="viewer.photo?.title || 'Full image preview'"
          class="w-full max-h-[80vh] object-contain rounded-lg"
        >
        <div class="text-white mt-3">
          <p class="font-semibold">{{ viewer.photo?.title || 'Untitled photo' }}</p>
          <p class="text-sm text-white/80">{{ viewer.photo?.photographer_name }} · {{ viewer.photo?.category }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api';

const router = useRouter();

const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const hubLoading = ref(false);
const hub = reactive({
  trending_photographers: [],
  trending_photos: [],
  trending_competitions: [],
  upcoming_events: [],
  popular_categories: [],
  popular_locations: [],
  personalized: null,
});

const photos = ref([]);
const photoPage = ref(1);

const search = reactive({
  query: '',
  type: 'all',
  category: '',
  city: '',
});

const showSearch = ref(false);
const searchLoading = ref(false);
const searchResults = reactive({
  photographers: [],
  photos: [],
  competitions: [],
  events: [],
  categories: [],
  cities: [],
});

const viewer = reactive({
  open: false,
  photo: null,
});

const fallbackProfile = '/images/avatar-placeholder.jpg';
const fallbackCover = '/images/placeholder.svg';

const hasAnySearchResult = computed(() => Object.values(searchResults).some((value) => Array.isArray(value) && value.length));

const loadHub = async () => {
  try {
    hubLoading.value = true;
    const { data } = await api.get('/discover/hub', { params: { limit: 12 } });
    const payload = data?.data || {};

    hub.trending_photographers = payload.trending_photographers || [];
    hub.trending_photos = payload.trending_photos || [];
    hub.trending_competitions = payload.trending_competitions || [];
    hub.upcoming_events = payload.upcoming_events || [];
    hub.popular_categories = payload.popular_categories || [];
    hub.popular_locations = payload.popular_locations || [];
    hub.personalized = payload.personalized || null;

    photos.value = [...(hub.trending_photos || [])];
  } catch (error) {
    console.error('Failed to load discovery hub:', error);
  } finally {
    hubLoading.value = false;
  }
};

const loadMorePhotos = async () => {
  try {
    photoPage.value += 1;
    const { data } = await api.get('/discover/photos', {
      params: {
        per_page: 24,
        page: photoPage.value,
      },
    });

    const list = data?.data || [];
    photos.value = [...photos.value, ...list.filter((item) => !photos.value.some((existing) => existing.id === item.id))];
  } catch (error) {
    console.error('Failed to load more photos:', error);
  }
};

const runSearch = async () => {
  const term = search.query.trim();
  if (term.length < 2) {
    showSearch.value = false;
    return;
  }

  try {
    showSearch.value = true;
    searchLoading.value = true;

    const { data } = await api.get('/discover/search', {
      params: {
        q: term,
        type: search.type,
        category: search.category || undefined,
        city: search.city || undefined,
      },
    });

    const payload = data?.data || {};
    searchResults.photographers = payload.photographers || [];
    searchResults.photos = payload.photos || [];
    searchResults.competitions = payload.competitions || [];
    searchResults.events = payload.events || [];
    searchResults.categories = payload.categories || [];
    searchResults.cities = payload.cities || [];
  } catch (error) {
    console.error('Failed to search discovery:', error);
  } finally {
    searchLoading.value = false;
  }
};

const clearSearch = () => {
  search.query = '';
  search.type = 'all';
  search.category = '';
  search.city = '';
  showSearch.value = false;
  searchResults.photographers = [];
  searchResults.photos = [];
  searchResults.competitions = [];
  searchResults.events = [];
  searchResults.categories = [];
  searchResults.cities = [];
};

const openPhotoViewer = (photo) => {
  viewer.photo = photo;
  viewer.open = true;
};

const closeViewer = () => {
  viewer.open = false;
  viewer.photo = null;
};

const viewPhotographer = (photographer) => {
  const username = photographer?.user?.username || photographer?.slug;
  if (!username) return;
  router.push(`/@${username}`);
};

const viewCompetition = (competition) => {
  if (!competition?.slug) return;
  router.push(`/competitions/${competition.slug}`);
};

const viewEvent = (event) => {
  if (!event?.slug) return;
  router.push(`/events/${event.slug}`);
};

const goToCategory = (slug) => {
  if (!slug) return;
  router.push({ path: '/photographers/by-category', query: { category: slug } });
};

const goToLocation = (slug) => {
  if (!slug) return;
  router.push({ path: '/photographers/by-location', query: { location: slug } });
};

const goToEventsByLocation = (slug) => {
  if (!slug) return;
  router.push({ path: '/events', query: { location: slug } });
};

const formatDate = (value) => {
  if (!value) return 'TBA';
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return 'TBA';
  return new Intl.DateTimeFormat('en-BD', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  }).format(date);
};

const formatMoney = (value) => {
  const amount = Number(value || 0);
  if (!amount) return 'Prize details coming soon';
  return new Intl.NumberFormat('en-BD', {
    style: 'currency',
    currency: 'BDT',
    maximumFractionDigits: 0,
  }).format(amount);
};

onMounted(async () => {
  await loadHub();
});
</script>
