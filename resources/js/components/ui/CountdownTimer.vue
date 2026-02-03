<template>
  <div class="inline-flex items-center gap-1.5 text-sm font-semibold" :class="urgencyClass">
    <svg v-if="showIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>{{ timeText }}</span>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  deadline: {
    type: [String, Date],
    required: true,
  },
  showIcon: {
    type: Boolean,
    default: true,
  },
  format: {
    type: String,
    default: 'short', // short: "2d 5h", long: "2 days 5 hours"
  },
});

const now = ref(new Date());
let interval = null;

const timeRemaining = computed(() => {
  const deadlineDate = new Date(props.deadline);
  const diff = deadlineDate.getTime() - now.value.getTime();
  
  if (diff <= 0) {
    return { expired: true, days: 0, hours: 0, minutes: 0, seconds: 0 };
  }
  
  const days = Math.floor(diff / (1000 * 60 * 60 * 24));
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((diff % (1000 * 60)) / 1000);
  
  return { expired: false, days, hours, minutes, seconds };
});

const timeText = computed(() => {
  const time = timeRemaining.value;
  
  if (time.expired) {
    return 'Expired';
  }
  
  if (props.format === 'long') {
    const parts = [];
    if (time.days > 0) parts.push(`${time.days} day${time.days !== 1 ? 's' : ''}`);
    if (time.hours > 0) parts.push(`${time.hours} hour${time.hours !== 1 ? 's' : ''}`);
    if (time.days === 0 && time.minutes > 0) parts.push(`${time.minutes} min${time.minutes !== 1 ? 's' : ''}`);
    return parts.join(' ') || '< 1 minute';
  }
  
  // Short format
  if (time.days > 0) {
    return `${time.days}d ${time.hours}h`;
  } else if (time.hours > 0) {
    return `${time.hours}h ${time.minutes}m`;
  } else if (time.minutes > 0) {
    return `${time.minutes}m ${time.seconds}s`;
  } else {
    return `${time.seconds}s`;
  }
});

const urgencyClass = computed(() => {
  const time = timeRemaining.value;
  
  if (time.expired) {
    return 'text-gray-500';
  }
  
  const totalHours = time.days * 24 + time.hours;
  
  if (totalHours < 24) {
    return 'text-red-600 animate-pulse'; // Less than 1 day - critical
  } else if (totalHours < 72) {
    return 'text-orange-600'; // Less than 3 days - urgent
  } else if (time.days < 7) {
    return 'text-yellow-600'; // Less than 1 week - soon
  } else {
    return 'text-gray-700'; // More than 1 week - normal
  }
});

onMounted(() => {
  // Update every second
  interval = setInterval(() => {
    now.value = new Date();
  }, 1000);
});

onUnmounted(() => {
  if (interval) {
    clearInterval(interval);
  }
});
</script>

<style scoped>
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

.animate-pulse {
  animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
