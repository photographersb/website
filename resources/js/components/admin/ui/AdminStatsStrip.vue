<template>
  <div class="admin-stats-strip">
    <div
      v-for="(stat, index) in stats"
      :key="index"
      :class="['admin-stat-card', `admin-stat-card--${stat.tone || 'neutral'}`]"
    >
      <div class="admin-stat-card__icon">
        <component
          :is="stat.icon"
          v-if="stat.icon"
          class="admin-stat-card__icon-svg"
        />
      </div>
      <div class="admin-stat-card__body">
        <p class="admin-stat-card__label">{{ stat.label }}</p>
        <p class="admin-stat-card__value">{{ stat.value }}</p>
        <p
          v-if="stat.meta"
          class="admin-stat-card__meta"
        >
          {{ stat.meta }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  stats: {
    type: Array,
    default: () => [],
  },
});
</script>

<style scoped>
.admin-stats-strip {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1rem;
}

.admin-stat-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.25rem;
  border-radius: 1rem;
  background: var(--admin-bg-card);
  border: 1px solid var(--admin-border);
  box-shadow: var(--admin-shadow-sm);
  position: relative;
  overflow: hidden;
}

.admin-stat-card::after {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: 1rem;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.35), transparent 60%);
  pointer-events: none;
}

.admin-stat-card__icon {
  width: 3rem;
  height: 3rem;
  border-radius: 0.9rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: rgba(139, 21, 56, 0.12);
  color: var(--admin-brand-primary);
  flex-shrink: 0;
}

.admin-stat-card__icon-svg {
  width: 1.5rem;
  height: 1.5rem;
}

.admin-stat-card__label {
  font-size: 0.85rem;
  color: var(--admin-text-secondary);
  margin: 0 0 0.2rem 0;
}

.admin-stat-card__value {
  font-size: 1.7rem;
  font-weight: 700;
  color: var(--admin-text-primary);
  margin: 0;
}

.admin-stat-card__meta {
  font-size: 0.78rem;
  color: var(--admin-text-muted);
  margin: 0.25rem 0 0 0;
}

.admin-stat-card--success .admin-stat-card__icon {
  background: rgba(5, 150, 105, 0.12);
  color: var(--admin-success);
}

.admin-stat-card--warning .admin-stat-card__icon {
  background: rgba(217, 119, 6, 0.14);
  color: var(--admin-warning);
}

.admin-stat-card--info .admin-stat-card__icon {
  background: rgba(37, 99, 235, 0.12);
  color: #2563eb;
}

.admin-stat-card--neutral .admin-stat-card__icon {
  background: rgba(15, 23, 42, 0.1);
  color: #0f172a;
}
</style>
