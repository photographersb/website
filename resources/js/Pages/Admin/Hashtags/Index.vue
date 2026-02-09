<template>
  <div class="min-h-screen">
    <AdminHeader
      title="#️⃣ Featured Hashtags"
      subtitle="Manage trending and featured hashtags"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <section class="page-hero">
        <div class="hero-copy">
          <p class="hero-kicker">TREND SIGNALS</p>
          <h1 class="hero-title">Hashtags, curated and elevated.</h1>
          <p class="hero-subtitle">
            Highlight the tags that drive discovery and momentum.
          </p>
          <div class="hero-actions">
            <button class="btn-admin-primary">Add Hashtag</button>
            <button class="btn-admin-secondary">Refresh List</button>
          </div>
        </div>
        <div class="hero-status">
          <div class="status-card">
            <span class="status-label">Featured</span>
            <span class="status-value">0</span>
          </div>
          <div class="status-card">
            <span class="status-label">Trending</span>
            <span class="status-value">0</span>
          </div>
          <div class="status-card">
            <span class="status-label">Total</span>
            <span class="status-value">0</span>
          </div>
        </div>
      </section>

      <div class="page-topbar">
        <div class="status-chip">
          Manage visibility rules and featured cadence
        </div>
      </div>

      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
          <div class="text-6xl mb-4">
            #️⃣
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-2">
            Featured Hashtags
          </h3>
          <p class="text-gray-600 mb-6">
            Manage trending and featured hashtags across the platform
          </p>
          <button
            class="btn-admin-secondary"
            @click="router.visit('/admin/dashboard')"
          >
            Back to Dashboard
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
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
</style>
