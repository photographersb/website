<template>
  <div class="admin-section-header">
    <div class="admin-section-header__titles">
      <p v-if="eyebrow" class="admin-section-header__eyebrow">
        {{ eyebrow }}
      </p>
      <h1 class="admin-section-header__title">
        {{ title }}
      </h1>
      <p v-if="subtitle" class="admin-section-header__subtitle">
        {{ subtitle }}
      </p>
    </div>
    <div v-if="$slots.actions" class="admin-section-header__actions">
      <slot name="actions" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
  subtitle: {
    type: String,
    default: '',
  },
  eyebrow: {
    type: String,
    default: '',
  },
});
</script>

<style scoped>
.admin-section-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1.5rem;
  padding: 1.5rem;
  border-radius: 1.25rem;
  background:
    linear-gradient(135deg, rgba(255, 255, 255, 0.92), rgba(253, 242, 245, 0.9)),
    radial-gradient(circle at 10% 20%, rgba(139, 21, 56, 0.12), transparent 50%);
  border: 1px solid var(--admin-border);
  box-shadow: var(--admin-shadow);
}

.admin-section-header__titles {
  max-width: 640px;
}

.admin-section-header__eyebrow {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.18em;
  color: var(--admin-text-muted);
  margin-bottom: 0.5rem;
}

.admin-section-header__title {
  font-size: 2rem;
  line-height: 1.2;
  color: var(--admin-text-primary);
  margin: 0 0 0.4rem 0;
}

.admin-section-header__subtitle {
  color: var(--admin-text-secondary);
  font-size: 0.95rem;
  margin: 0;
}

.admin-section-header__actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

@media (max-width: 768px) {
  .admin-section-header {
    flex-direction: column;
  }

  .admin-section-header__actions {
    width: 100%;
    justify-content: flex-start;
  }
}
</style>
