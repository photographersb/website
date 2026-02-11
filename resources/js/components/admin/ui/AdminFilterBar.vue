<template>
  <div class="admin-filter-bar">
    <div class="admin-filter-bar__fields">
      <slot />
    </div>
    <div
      v-if="$slots.actions"
      class="admin-filter-bar__actions"
    >
      <slot name="actions" />
    </div>
  </div>
</template>

<script setup>
defineProps({});
</script>

<style scoped>
.admin-filter-bar {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.25rem;
  border-radius: 1rem;
  border: 1px solid var(--admin-border);
  background: rgba(255, 255, 255, 0.8);
  box-shadow: var(--admin-shadow-sm);
}

.admin-filter-bar__fields {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  align-items: center;
  flex: 1;
  min-width: 220px;
}

.admin-filter-bar__actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

@media (max-width: 768px) {
  .admin-filter-bar {
    flex-direction: column;
    align-items: stretch;
  }

  .admin-filter-bar__actions {
    justify-content: flex-start;
  }
}
</style>
