<template>
  <nav class="mobile-bottom-nav md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 safe-area-inset">
    <div class="flex justify-around items-center h-16">
      <router-link
        to="/"
        class="nav-item"
        :class="{ active: isActive('/') }"
      >
        <svg
          class="w-6 h-6"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
          />
        </svg>
        <span class="text-xs mt-1">Home</span>
      </router-link>

      <router-link
        to="/photographers"
        class="nav-item"
        :class="{ active: isActive('/photographers') }"
      >
        <svg
          class="w-6 h-6"
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
        <span class="text-xs mt-1">Search</span>
      </router-link>

      <router-link
        to="/competitions"
        class="nav-item"
        :class="{ active: isActive('/competitions') }"
      >
        <svg
          class="w-6 h-6"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
          />
        </svg>
        <span class="text-xs mt-1">Compete</span>
      </router-link>

      <router-link
        to="/notifications"
        class="nav-item relative"
        :class="{ active: isActive('/notifications') }"
      >
        <svg
          class="w-6 h-6"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
          />
        </svg>
        <span
          v-if="unreadCount > 0"
          class="absolute top-0 right-1/4 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
        >
          {{ unreadCount > 9 ? '9+' : unreadCount }}
        </span>
        <span class="text-xs mt-1">Alerts</span>
      </router-link>

      <button
        class="nav-item"
        :class="{ active: showUserMenu }"
        @click="toggleUserMenu"
      >
        <svg
          class="w-6 h-6"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
          />
        </svg>
        <span class="text-xs mt-1">Account</span>
      </button>
    </div>

    <!-- User Menu Dropdown (slides up from bottom) -->
    <transition name="slide-up">
      <div
        v-if="showUserMenu"
        class="user-menu-overlay"
        @click="showUserMenu = false"
      >
        <div
          class="user-menu-panel"
          @click.stop
        >
          <div class="menu-header">
            <h3 class="text-lg font-semibold">
              Account
            </h3>
            <button
              class="text-gray-500"
              @click="showUserMenu = false"
            >
              <svg
                class="w-6 h-6"
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

          <div
            v-if="isAuthenticated"
            class="menu-items"
          >
            <router-link
              v-if="user?.role === 'photographer'"
              to="/dashboard"
              class="menu-item"
              @click="showUserMenu = false"
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
                  d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                />
              </svg>
              <span>Dashboard</span>
            </router-link>

            <router-link
              v-if="user?.role === 'admin' || user?.role === 'super_admin'"
              to="/admin/dashboard"
              class="menu-item"
              @click="showUserMenu = false"
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
                  d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"
                />
              </svg>
              <span>Admin Panel</span>
            </router-link>

            <router-link
              to="/transactions"
              class="menu-item"
              @click="showUserMenu = false"
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
                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                />
              </svg>
              <span>Transactions</span>
            </router-link>

            <router-link
              to="/events"
              class="menu-item"
              @click="showUserMenu = false"
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
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                />
              </svg>
              <span>Events</span>
            </router-link>

            <button
              class="menu-item text-red-600"
              @click="logout"
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
                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                />
              </svg>
              <span>Logout</span>
            </button>
          </div>

          <div
            v-else
            class="menu-items"
          >
            <router-link
              to="/auth"
              class="menu-item"
              @click="showUserMenu = false"
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
                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
                />
              </svg>
              <span>Login / Register</span>
            </router-link>
          </div>
        </div>
      </div>
    </transition>
  </nav>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';

export default {
  name: 'MobileBottomNav',
  setup() {
    const router = useRouter();
    const showUserMenu = ref(false);
    const unreadCount = ref(0);

    const isAuthenticated = computed(() => {
      return !!localStorage.getItem('user');
    });

    const user = computed(() => {
      const userData = localStorage.getItem('user');
      return userData ? JSON.parse(userData) : null;
    });

    const isActive = (path) => {
      return router.currentRoute.value.path === path;
    };

    const toggleUserMenu = () => {
      showUserMenu.value = !showUserMenu.value;
    };

    const logout = async () => {
      try {
        await fetch('/api/v1/auth/logout', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
        });
      } catch (error) {
        console.error('Logout error:', error);
      } finally {
        localStorage.removeItem('user');
        showUserMenu.value = false;
        router.push('/');
      }
    };

    const fetchUnreadCount = async () => {
      if (!isAuthenticated.value) return;

      try {
        const response = await fetch('/api/v1/notifications/unread-count', {
          headers: {},
        });
        const data = await response.json();
        if (data.status === 'success') {
          unreadCount.value = data.data.unread_count;
        }
      } catch (error) {
        console.error('Error fetching unread count:', error);
      }
    };

    onMounted(() => {
      fetchUnreadCount();
      // Poll every 30 seconds
      setInterval(fetchUnreadCount, 30000);
    });

    return {
      showUserMenu,
      isAuthenticated,
      user,
      unreadCount,
      isActive,
      toggleUserMenu,
      logout,
    };
  },
};
</script>

<style scoped>
.mobile-bottom-nav {
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
}

.nav-item {
  @apply flex flex-col items-center justify-center flex-1 py-2 text-gray-600 transition-colors;
}

.nav-item.active {
  @apply text-burgundy-600;
}

.nav-item svg {
  transition: transform 0.2s;
}

.nav-item:active svg {
  transform: scale(0.9);
}

/* User Menu Overlay */
.user-menu-overlay {
  @apply fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end;
}

.user-menu-panel {
  @apply bg-white w-full rounded-t-2xl max-h-96 overflow-y-auto;
}

.menu-header {
  @apply flex justify-between items-center p-4 border-b border-gray-200 sticky top-0 bg-white;
}

.menu-items {
  @apply p-2;
}

.menu-item {
  @apply flex items-center gap-3 w-full p-4 hover:bg-gray-50 rounded-lg transition-colors;
}

/* Slide up animation */
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s ease;
}

.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(100%);
  opacity: 0;
}

/* Safe area for notched devices */
.safe-area-inset {
  padding-bottom: env(safe-area-inset-bottom);
}
</style>
