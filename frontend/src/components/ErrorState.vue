<script setup>
defineProps({
  error: String
})

defineEmits(['retry'])
</script>

<template>
  <div class="text-center py-8">
    <p class="text-red-500 dark:text-red-400">{{ error }}</p>
    <button @click="$emit('retry')" class="mt-2 px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600">
      Retry
    </button>
  </div>
</template>
