<template>
  <div class="flex flex-col h-full">
    <!-- Sidebar Container -->
    <nav
      :class="[
        'admin-sidebar fixed md:static inset-y-0 left-0 z-50 w-64 overflow-y-auto transition-transform duration-300 transform',
        isOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0',
      ]"
    >
      <!-- Logo Section -->
      <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
        <router-link
          to="/admin/dashboard"
          class="flex items-center space-x-2"
        >
          <div class="admin-sidebar__brand w-9 h-9 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-lg">📷</span>
          </div>
          <div class="flex flex-col">
            <span class="text-sm font-bold text-gray-900">ADMIN HQ</span>
            <span class="text-xs text-gray-500">Photographer SB</span>
          </div>
        </router-link>

        <!-- Close button for mobile -->
        <button
          class="md:hidden p-1 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100"
          @click="$emit('toggle')"
        >
          <svg
            class="h-6 w-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>

      <!-- User Info Section (collapsed when sidebar minimized) -->
      <div
        v-if="user"
        class="px-4 py-4 border-b border-gray-200"
      >
        <div class="flex items-center space-x-3">
          <div class="admin-avatar w-10 h-10 rounded-full flex items-center justify-center font-bold">
            {{ user.name?.charAt(0).toUpperCase() }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate">
              {{ user.name }}
            </p>
            <p class="text-xs text-gray-500 truncate">
              {{ user.role }}
            </p>
          </div>
        </div>
      </div>

      <!-- Search Section -->
      <div class="px-4 py-4 border-b border-gray-200">
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search menu..."
            class="admin-input w-full text-sm"
          >
          <svg
            class="absolute right-3 top-2.5 h-5 w-5 text-gray-400"
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
        </div>
      </div>

      <!-- Menu Sections -->
      <div class="flex-1 px-4 py-6 space-y-8">
        <div
          v-for="(section, sectionKey) in filteredMenu"
          :key="sectionKey"
          class="space-y-2"
        >
          <!-- Section Header -->
          <div
            class="flex items-center justify-between px-3 py-2 cursor-pointer"
            @click="toggleSection(sectionKey)"
          >
            <div class="flex items-center space-x-2 min-w-0">
              <div
                :class="[
                  'w-5 h-5 rounded-md flex items-center justify-center text-white text-xs font-bold',
                  `bg-gradient-to-br ${section.color}`
                ]"
              >
                <component
                  :is="section.icon"
                  v-if="isIconComponent(section.icon)"
                  class="w-4 h-4"
                />
                <span v-else>{{ section.icon }}</span>
              </div>
              <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider truncate">
                {{ section.section }}
              </span>
            </div>
            <svg
              :class="[
                'w-4 h-4 text-gray-400 transition-transform',
                expandedSections.includes(sectionKey) ? 'rotate-180' : ''
              ]"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 14l-7-7m0 0L5 14m7-7v12"
              />
            </svg>
          </div>

          <!-- Section Items -->
          <transition name="collapse">
            <div
              v-if="expandedSections.includes(sectionKey)"
              class="space-y-1"
            >
              <router-link
                v-for="item in section.items"
                :key="item.id"
                :to="item.route"
                :class="[
                  'admin-nav-link flex items-center justify-between px-3 py-2 text-sm transition-colors group',
                  isActiveRoute(item.route)
                    ? 'admin-nav-link--active font-medium'
                    : ''
                ]"
                :title="item.description"
                @click="handleItemClick"
              >
                <div class="flex items-center space-x-2 flex-1 min-w-0">
                  <span class="w-4 h-4 flex items-center justify-center flex-shrink-0">
                    <component 
                      :is="item.icon" 
                      v-if="isIconComponent(item.icon)" 
                      :class="[
                        'w-4 h-4',
                        isActiveRoute(item.route) ? 'text-amber-700' : 'text-gray-400 group-hover:text-gray-600'
                      ]"
                    />
                  </span>
                  <span class="truncate">{{ item.label }}</span>
                </div>

                <!-- Badge -->
                <div
                  v-if="item.badge && badgeCounts[item.badge]"
                  :class="[
                    'ml-2 px-2 py-0.5 text-xs font-semibold rounded-full',
                    badgeCounts[item.badge] > 0 ? 'admin-nav-badge' : 'bg-gray-300 text-gray-700'
                  ]"
                >
                  {{ badgeCounts[item.badge] }}
                </div>
              </router-link>
            </div>
          </transition>
        </div>
      </div>

      <!-- Footer Section -->
      <div class="border-t border-gray-200 px-4 py-4 space-y-2">
        <button
          class="admin-footer-link w-full flex items-center space-x-2 px-3 py-2 text-sm text-gray-700"
          @click="toggleTheme"
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
              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
            />
          </svg>
          <span>{{ isDarkMode ? 'Light Mode' : 'Dark Mode' }}</span>
        </button>

        <router-link
          to="/admin/settings/general"
          class="admin-footer-link w-full flex items-center space-x-2 px-3 py-2 text-sm text-gray-700"
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
              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
            />
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
            />
          </svg>
          <span>Settings</span>
        </router-link>

        <button
          class="admin-footer-link admin-footer-link--danger w-full flex items-center space-x-2 px-3 py-2 text-sm"
          @click="handleLogout"
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
              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
            />
          </svg>
          <span>Logout</span>
        </button>
      </div>
    </nav>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { filterMenuByPermissions } from '../config/adminMenu.js';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: true
  },
  user: {
    type: Object,
    default: null
  },
  menuConfig: {
    type: Object,
    required: true
  }
});

defineEmits(['toggle']);

const route = useRoute();
const router = useRouter();

// State
const searchQuery = ref('');
const expandedSections = ref([]);
const isDarkMode = ref(false);
const badgeCounts = ref({
  pending: 0,
  new: 0,
  errors: 0,
  urgent: 0
});

// Auto-expand all sections initially
onMounted(() => {
  expandedSections.value = Object.keys(props.menuConfig);
  fetchBadgeCounts();
});

// Computed
const filteredMenu = computed(() => {
  const baseMenu = props.menuConfig;
  
  if (!searchQuery.value) {
    return baseMenu;
  }

  const query = searchQuery.value.toLowerCase();
  const filtered = {};

  Object.entries(baseMenu).forEach(([key, section]) => {
    const filteredItems = section.items.filter(item =>
      item.label.toLowerCase().includes(query) ||
      item.description.toLowerCase().includes(query)
    );

    if (filteredItems.length > 0) {
      filtered[key] = {
        ...section,
        items: filteredItems
      };
    }
  });

  return filtered;
});

// Methods
const isActiveRoute = (routePath) => {
  return route.path === routePath;
};

const toggleSection = (sectionKey) => {
  const index = expandedSections.value.indexOf(sectionKey);
  if (index > -1) {
    expandedSections.value.splice(index, 1);
  } else {
    expandedSections.value.push(sectionKey);
  }
};

const handleItemClick = () => {
  // Close sidebar on mobile
  if (window.innerWidth < 768) {
    // Emit close event to parent
  }
};

const toggleTheme = () => {
  isDarkMode.value = !isDarkMode.value;
  // Apply theme logic here
  document.documentElement.classList.toggle('dark', isDarkMode.value);
  localStorage.setItem('adminTheme', isDarkMode.value ? 'dark' : 'light');
};

const handleLogout = async () => {
  try {
    await fetch('/api/v1/auth/logout', {
      method: 'POST',
      headers: {}
    });
  } catch (error) {
    console.error('Logout error:', error);
  }

  localStorage.removeItem('token');
  localStorage.removeItem('user');
  localStorage.removeItem('user_role');
  router.push('/login');
};

const fetchBadgeCounts = async () => {
  try {
    const response = await fetch('/api/v1/admin/dashboard', {
      headers: {}
    });

    if (response.ok) {
      const data = await response.json();
      const counts = data.data?.badge_counts || data.badge_counts || data;
      badgeCounts.value = {
        pending: counts.pending_count || 0,
        new: counts.new_count || 0,
        errors: counts.error_count || 0,
        urgent: counts.urgent_count || 0
      };
    }
  } catch (error) {
    console.error('Error fetching badge counts:', error);
  }
};

const isIconComponent = (icon) => {
  // Check if icon is a string emoji or component name
  return typeof icon === 'string' && icon.length > 1;
};

// Watch for route changes to auto-expand relevant section
watch(() => route.path, (newPath) => {
  // Find which section contains the current route
  Object.entries(props.menuConfig).forEach(([key, section]) => {
    const hasActiveItem = section.items.some(item => item.route === newPath);
    if (hasActiveItem && !expandedSections.value.includes(key)) {
      expandedSections.value.push(key);
    }
  });
});

// Load theme preference
onMounted(() => {
  const savedTheme = localStorage.getItem('adminTheme');
  if (savedTheme === 'dark') {
    isDarkMode.value = true;
    document.documentElement.classList.add('dark');
  }
});
</script>

<style scoped>
.collapse-enter-active,
.collapse-leave-active {
  transition: all 0.3s ease;
}

.collapse-enter-from {
  opacity: 0;
  transform: scaleY(0.95);
}

.collapse-leave-to {
  opacity: 0;
  transform: scaleY(0.95);
}
</style>
