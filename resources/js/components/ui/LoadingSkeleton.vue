<template>
  <div class="animate-pulse">
    <!-- Card Skeleton -->
    <div
      v-if="type === 'card'"
      class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100"
    >
      <!-- Image Skeleton -->
      <div class="h-48 md:h-56 bg-gray-200" />
      
      <!-- Content Skeleton -->
      <div class="p-4 md:p-5">
        <!-- Title -->
        <div class="h-6 bg-gray-200 rounded w-3/4 mb-3" />
        
        <!-- Rating -->
        <div class="flex items-center gap-2 mb-3">
          <div class="h-4 bg-gray-200 rounded w-20" />
          <div class="h-4 bg-gray-200 rounded w-16" />
        </div>
        
        <!-- Info -->
        <div class="h-4 bg-gray-200 rounded w-full mb-2" />
        <div class="h-4 bg-gray-200 rounded w-5/6 mb-4" />
        
        <!-- Button -->
        <div class="h-10 bg-gray-200 rounded-lg" />
      </div>
    </div>

    <!-- List Item Skeleton -->
    <div
      v-else-if="type === 'list'"
      class="bg-white rounded-lg shadow-sm p-4 border border-gray-100"
    >
      <div class="flex items-center gap-4">
        <!-- Avatar -->
        <div class="w-12 h-12 bg-gray-200 rounded-full flex-shrink-0" />
        
        <!-- Content -->
        <div class="flex-1">
          <div class="h-5 bg-gray-200 rounded w-32 mb-2" />
          <div class="h-4 bg-gray-200 rounded w-48" />
        </div>
        
        <!-- Action -->
        <div class="h-8 w-20 bg-gray-200 rounded" />
      </div>
    </div>

    <!-- Profile Skeleton -->
    <div
      v-else-if="type === 'profile'"
      class="space-y-6"
    >
      <!-- Hero -->
      <div class="h-64 bg-gray-200 rounded-2xl" />
      
      <!-- Profile Info -->
      <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-start gap-4 mb-4">
          <div class="w-24 h-24 bg-gray-200 rounded-full" />
          <div class="flex-1">
            <div class="h-8 bg-gray-200 rounded w-48 mb-2" />
            <div class="h-5 bg-gray-200 rounded w-32" />
          </div>
        </div>
        <div class="grid grid-cols-3 gap-3 mb-4">
          <div class="h-16 bg-gray-200 rounded" />
          <div class="h-16 bg-gray-200 rounded" />
          <div class="h-16 bg-gray-200 rounded" />
        </div>
        <div class="space-y-2">
          <div class="h-12 bg-gray-200 rounded-lg" />
          <div class="h-12 bg-gray-200 rounded-lg" />
        </div>
      </div>
    </div>

    <!-- Table Row Skeleton -->
    <div
      v-else-if="type === 'table-row'"
      class="border-b border-gray-200"
    >
      <div class="grid grid-cols-6 gap-4 p-4">
        <div class="h-4 bg-gray-200 rounded" />
        <div class="h-4 bg-gray-200 rounded" />
        <div class="h-4 bg-gray-200 rounded" />
        <div class="h-4 bg-gray-200 rounded" />
        <div class="h-4 bg-gray-200 rounded" />
        <div class="h-4 bg-gray-200 rounded" />
      </div>
    </div>

    <!-- Generic Content Skeleton -->
    <div
      v-else
      class="space-y-3"
    >
      <div class="h-4 bg-gray-200 rounded w-full" />
      <div class="h-4 bg-gray-200 rounded w-5/6" />
      <div class="h-4 bg-gray-200 rounded w-4/6" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'card', // card, list, profile, table-row, content
  },
});
</script>

<style scoped>
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
