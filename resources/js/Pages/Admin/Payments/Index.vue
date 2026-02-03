<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader 
      title="💰 Payment Gateway Management" 
      subtitle="Configure and monitor payment gateways"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

    <div class="content-card">
      <div class="gateways-grid">
        <div class="gateway-card">
          <div class="gateway-icon">💳</div>
          <h3>Card Payment</h3>
          <span class="gateway-status active">Active</span>
          <div class="gateway-stats">
            <p>Transactions: 0</p>
            <p>Revenue: ৳0</p>
          </div>
        </div>

        <div class="gateway-card">
          <div class="gateway-icon">📱</div>
          <h3>bKash</h3>
          <span class="gateway-status active">Active</span>
          <div class="gateway-stats">
            <p>Transactions: 0</p>
            <p>Revenue: ৳0</p>
          </div>
        </div>

        <div class="gateway-card">
          <div class="gateway-icon">📲</div>
          <h3>Nagad</h3>
          <span class="gateway-status active">Active</span>
          <div class="gateway-stats">
            <p>Transactions: 0</p>
            <p>Revenue: ৳0</p>
          </div>
        </div>

        <div class="gateway-card">
          <div class="gateway-icon">🏦</div>
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
