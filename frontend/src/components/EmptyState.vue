<script setup>
defineProps({
  message: String
})
</script>

<template>
  <div class="text-center py-12 text-gray-500 text-lg">
    <p>{{ message }}</p>
  </div>
</template>
