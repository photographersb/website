<template>
  <div class="min-h-screen">
    <AdminHeader 
      title="💰 Payment Gateway Management" 
      subtitle="Configure and monitor payment gateways"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <section class="page-hero">
        <div class="hero-copy">
          <p class="hero-kicker">GATEWAY STACK</p>
          <h1 class="hero-title">Payments, gateways, and controls.</h1>
          <p class="hero-subtitle">
            Monitor gateway health, activation status, and payout readiness.
          </p>
          <div class="hero-actions">
            <button class="btn-admin-primary">Review Settings</button>
            <button class="btn-admin-secondary">Refresh Status</button>
          </div>
        </div>
        <div class="hero-status">
          <div class="status-card">
            <span class="status-label">Active Gateways</span>
            <span class="status-value">4</span>
          </div>
          <div class="status-card">
            <span class="status-label">Total Revenue</span>
            <span class="status-value">৳0</span>
          </div>
          <div class="status-card">
            <span class="status-label">Uptime</span>
            <span class="status-value">99.9%</span>
          </div>
        </div>
      </section>

      <div class="page-topbar">
        <div class="status-chip">
          All gateways healthy
        </div>
      </div>

      <div class="content-card">
        <div class="gateways-grid">
          <div class="gateway-card">
            <div class="gateway-icon">
              💳
            </div>
            <h3>Card Payment</h3>
            <span class="gateway-status active">Active</span>
            <div class="gateway-stats">
              <p>Transactions: 0</p>
              <p>Revenue: ৳0</p>
            </div>
          </div>

          <div class="gateway-card">
            <div class="gateway-icon">
              📱
            </div>
            <h3>bKash</h3>
            <span class="gateway-status active">Active</span>
            <div class="gateway-stats">
              <p>Transactions: 0</p>
              <p>Revenue: ৳0</p>
            </div>
          </div>

          <div class="gateway-card">
            <div class="gateway-icon">
              📲
            </div>
            <h3>Nagad</h3>
            <span class="gateway-status active">Active</span>
            <div class="gateway-stats">
              <p>Transactions: 0</p>
              <p>Revenue: ৳0</p>
            </div>
          </div>

          <div class="gateway-card">
            <div class="gateway-icon">
              🏦
            </div>
            <h3>Bank Transfer</h3>
            <span class="gateway-status active">Active</span>
            <div class="gateway-stats">
              <p>Transactions: 0</p>
              <p>Revenue: ৳0</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const gateways = ref([])
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
.admin-payments { padding: 2rem; max-width: 1400px; margin: 0 auto; }
.page-header { margin-bottom: 2rem; }
.page-title { font-size: 2rem; color: #1f2937; margin-bottom: 0.5rem; }
.page-subtitle { color: #6b7280; }
.content-card { background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 2rem; }
.gateways-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
.gateway-card { background: #f9fafb; padding: 2rem; border-radius: 0.75rem; text-align: center; }
.gateway-icon { font-size: 3rem; margin-bottom: 1rem; }
.gateway-card h3 { margin-bottom: 1rem; color: #1f2937; }
.gateway-status { padding: 0.25rem 1rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; }
.gateway-status.active { background: #d1fae5; color: #065f46; }
.gateway-stats { margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb; }
.gateway-stats p { color: #6b7280; margin: 0.5rem 0; }
</style>
