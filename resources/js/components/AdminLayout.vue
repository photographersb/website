<template>
  <div class="admin-shell">
    <!-- Admin Layout Container -->
    <div class="flex h-screen admin-shell__frame">
      <!-- SIDEBAR -->
      <AdminSidebar 
        :is-open="sidebarOpen"
        :user="user"
        :menu-config="menuConfig"
        @toggle="sidebarOpen = !sidebarOpen"
      />

      <!-- MAIN CONTENT AREA -->
      <div class="flex-1 flex flex-col overflow-hidden">
        <!-- HEADER -->
        <AdminHeader 
          :user="user"
          :system-health="systemHealth"
          :sidebar-open="sidebarOpen"
          @toggle-sidebar="sidebarOpen = !sidebarOpen"
        />

        <!-- PAGE CONTENT -->
        <main class="flex-1 overflow-auto">
          <div class="admin-content">
            <!-- Breadcrumbs (optional) -->
            <div
              v-if="showBreadcrumbs"
              class="mb-6"
            >
              <nav
                class="flex"
                aria-label="Breadcrumb"
              >
                <ol class="flex items-center space-x-4">
                  <li>
                    <div class="flex items-center">
                      <router-link 
                        to="/admin/dashboard"
                        class="text-sm font-medium text-gray-500 hover:text-gray-900"
                      >
                        Admin
                      </router-link>
                    </div>
                  </li>
                  <li
                    v-for="(crumb, index) in breadcrumbs"
                    :key="index"
                  >
                    <div class="flex items-center">
                      <svg 
                        class="flex-shrink-0 h-5 w-5 text-gray-400"
                        fill="currentColor" 
                        viewBox="0 0 20 20"
                      >
                        <path 
                          fill-rule="evenodd"
                          d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <router-link 
                        v-if="!crumb.active"
                        :to="crumb.href || '#'"
                        class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-900"
                      >
                        {{ crumb.name }}
                      </router-link>
                      <span
                        v-else
                        class="ml-4 text-sm font-medium text-gray-800"
                      >
                        {{ crumb.name }}
                      </span>
                    </div>
                  </li>
                </ol>
              </nav>
            </div>

            <!-- Page Title (optional) -->
            <div
              v-if="pageTitle"
              class="mb-8"
            >
              <h1 class="text-3xl font-bold admin-page-title">
                {{ pageTitle }}
              </h1>
              <p
                v-if="pageDescription"
                class="mt-2 text-sm admin-page-subtitle"
              >
                {{ pageDescription }}
              </p>
            </div>

            <!-- Alerts/Notifications -->
            <div
              v-if="alerts.length"
              class="mb-6 space-y-4"
            >
              <div
                v-for="(alert, index) in alerts"
                :key="index"
                :class="[
                  'p-4 rounded-lg border',
                  alert.type === 'error' && 'bg-red-50 border-red-200 text-red-800',
                  alert.type === 'warning' && 'bg-yellow-50 border-yellow-200 text-yellow-800',
                  alert.type === 'success' && 'bg-green-50 border-green-200 text-green-800',
                  alert.type === 'info' && 'bg-blue-50 border-blue-200 text-blue-800',
                ]"
              >
                <div class="flex justify-between items-start">
                  <div>{{ alert.message }}</div>
                  <button
                    class="text-gray-400 hover:text-gray-600"
                    @click="removeAlert(index)"
                  >
                    <svg
                      class="h-5 w-5"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Slot for page content -->
            <slot />
          </div>
        </main>

        <!-- FOOTER (optional) -->
        <footer
          v-if="showFooter"
          class="bg-white border-t border-gray-200"
        >
          <div class="px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between text-sm text-gray-500">
              <div>
                <p>&copy; 2026 Photographer SB Admin. All rights reserved.</p>
              </div>
              <div class="flex space-x-6">
                <a
                  href="#"
                  class="hover:text-gray-700"
                >Documentation</a>
                <a
                  href="#"
                  class="hover:text-gray-700"
                >Support</a>
                <a
                  href="#"
                  class="hover:text-gray-700"
                >Privacy</a>
              </div>
            </div>
          </div>
        </footer>
      </div>
    </div>

    <!-- Sidebar Mobile Overlay -->
    <Teleport to="body">
      <transition name="fade">
        <div
          v-if="sidebarOpen && isMobile"
          class="fixed inset-0 bg-gray-600 bg-opacity-50 z-10 md:hidden"
          @click="sidebarOpen = false"
        />
      </transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, provide, onBeforeUnmount } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AdminSidebar from './AdminSidebar.vue';
import AdminHeader from './AdminHeader.vue';
import { adminMenuConfig } from '../config/adminMenu.js';
import api from '../api';

const props = defineProps({
  showBreadcrumbs: {
    type: Boolean,
    default: true
  },
  pageTitle: {
    type: String,
    default: null
  },
  pageDescription: {
    type: String,
    default: null
  },
  showFooter: {
    type: Boolean,
    default: true
  }
});

const route = useRoute();
const router = useRouter();

// State
const sidebarOpen = ref(true);
const isMobile = ref(window.innerWidth < 768);
const user = ref(null);
const systemHealth = ref(null);
const alerts = ref([]);
const breadcrumbs = ref([]);
const menuConfig = ref(adminMenuConfig);

// Computed
const currentRoute = computed(() => route.name);

// Methods
const removeAlert = (index) => {
  alerts.value.splice(index, 1);
};

const addAlert = (message, type = 'info', duration = 5000) => {
  const alert = { message, type };
  alerts.value.push(alert);
  
  if (duration > 0) {
    setTimeout(() => {
      removeAlert(alerts.value.indexOf(alert));
    }, duration);
  }
  
  return alert;
};

const updateBreadcrumbs = () => {
  const routeMeta = route.meta;
  if (routeMeta && routeMeta.breadcrumbs) {
    breadcrumbs.value = routeMeta.breadcrumbs;
  } else {
    breadcrumbs.value = [];
  }
};

const fetchUserData = async () => {
  try {
    const response = await api.get('/admin/profile');
    const data = response?.data;
    if (!data) return;
    user.value = data.data || data;
  } catch (error) {
    console.error('Error fetching user data:', error);
  }
};

const fetchSystemHealth = async () => {
  try {
    const response = await api.get('/admin/system-health');
    const data = response?.data;
    if (!data) return;
    systemHealth.value = data.data || data;
  } catch (error) {
    console.error('Error fetching system health:', error);
  }
};

const handleResize = () => {
  isMobile.value = window.innerWidth < 768;
  if (isMobile.value) {
    sidebarOpen.value = false;
  } else {
    sidebarOpen.value = true;
  }
};

// Provide methods to child components
provide('addAlert', addAlert);
provide('removeAlert', removeAlert);

// Lifecycle
onMounted(() => {
  // Fetch initial data
  fetchUserData();
  fetchSystemHealth();
  updateBreadcrumbs();

  // Handle window resize
  window.addEventListener('resize', handleResize);

  // Set initial mobile state
  if (isMobile.value) {
    sidebarOpen.value = false;
  }

  // Watch for route changes
  router.afterEach(() => {
    updateBreadcrumbs();
  });
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize);
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
