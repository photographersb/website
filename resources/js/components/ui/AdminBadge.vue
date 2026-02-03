<template>
  <span :class="badgeClasses">
    <slot />
  </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'success', 'warning', 'danger', 'info'].includes(value)
  },
  status: {
    type: String,
    default: null,
    validator: (value) => !value || ['published', 'draft', 'active', 'inactive', 'pending', 'approved', 'rejected', 'cancelled', 'featured', 'pending_review'].includes(value)
  }
});

const badgeClasses = computed(() => {
  const baseClasses = 'badge';
  
  // If status prop is provided, use status-specific classes
  if (props.status) {
    const statusMap = {
      published: 'status-published',
      active: 'status-active',
      approved: 'status-approved',
      draft: 'status-draft',
      pending: 'status-pending',
      pending_review: 'status-pending_review',
      rejected: 'status-rejected',
      cancelled: 'status-cancelled',
      inactive: 'status-inactive',
      featured: 'status-featured'
    };
    return `${baseClasses} ${statusMap[props.status] || 'badge-primary'}`;
  }
  
  // Otherwise use variant
  return `${baseClasses} badge-${props.variant}`;
});
</script>
