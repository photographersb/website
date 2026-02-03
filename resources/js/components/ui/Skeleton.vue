<template>
  <div class="skeleton" :class="[`skeleton-${type}`, { 'skeleton-animated': animated }]">
    <div class="skeleton-loader"></div>
  </div>
</template>

<script setup>
const props = defineProps({
  type: {
    type: String,
    default: 'text',
    validator: (value) => ['text', 'title', 'avatar', 'thumbnail', 'button', 'card'].includes(value)
  },
  animated: {
    type: Boolean,
    default: true
  }
});
</script>

<style scoped>
.skeleton {
  position: relative;
  overflow: hidden;
  background: #e5e7eb;
  border-radius: 4px;
}

.skeleton-loader {
  width: 100%;
  height: 100%;
}

.skeleton-animated .skeleton-loader::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.6),
    transparent
  );
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}

/* Types */
.skeleton-text {
  height: 16px;
  margin-bottom: 8px;
}

.skeleton-title {
  height: 24px;
  width: 60%;
  margin-bottom: 12px;
}

.skeleton-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
}

.skeleton-thumbnail {
  width: 100%;
  height: 200px;
  border-radius: 8px;
}

.skeleton-button {
  height: 40px;
  width: 120px;
  border-radius: 8px;
}

.skeleton-card {
  height: 300px;
  border-radius: 12px;
}
</style>
