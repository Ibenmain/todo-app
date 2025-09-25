<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    @click="$emit('close')"
  >
    <div
      class="bg-white rounded-xl p-8 max-w-md w-11/12 shadow-lg dark:bg-gray-800"
      @click.stop
    >
      <h3
        class="m-0 mb-4 text-gray-800 text-xl font-semibold dark:text-gray-200"
      >
        Add New Task
      </h3>

      <form @submit.prevent="$emit('add-task', { title: newNoteTitle, description: newNoteDescription })">
        <input
          v-model="newNoteTitle"
          type="text"
          placeholder="Task title..."
          class="w-full p-4 border-2 border-gray-300 rounded-lg font-inherit text-base mb-4 focus:outline-none focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
          required
        />

        <textarea
          v-model="newNoteDescription"
          placeholder="Enter your task description here..."
          class="w-full p-4 border-2 border-gray-300 rounded-lg resize-y font-inherit text-base mb-4 focus:outline-none focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
          rows="4"
        ></textarea>

        <div class="flex gap-4 justify-end">
          <button
            type="button"
            @click="$emit('close')"
            class="px-6 py-3 border-2 border-gray-300 rounded-lg bg-white text-gray-700 cursor-pointer font-medium transition-all duration-300 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-6 py-3 border-2 border-indigo-500 rounded-lg bg-indigo-500 text-white cursor-pointer font-medium transition-all duration-300 hover:bg-indigo-600 hover:border-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="!newNoteTitle.trim()"
          >
            Add Task
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, defineProps, defineEmits } from 'vue'

const props = defineProps({
  show: Boolean
})

const emit = defineEmits(['close', 'add-task'])

const newNoteTitle = ref('')
const newNoteDescription = ref('')

watch(() => props.show, (newVal) => {
  if (!newVal) {
    newNoteTitle.value = ''
    newNoteDescription.value = ''
  }
})
</script>