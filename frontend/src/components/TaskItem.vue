<template>
  <div 
    class="bg-white rounded-xl p-5 shadow-sm border border-gray-300 transition-all duration-300 mb-4 flex flex-col sm:flex-row items-center justify-between hover:shadow-md hover:-translate-y-1 dark:bg-gray-800 dark:border-gray-600"
    :class="{ 'bg-gray-50 border-gray-400 dark:bg-gray-700': task.completed }"
  >
    <div class="flex items-center flex-1 w-full mb-4 sm:mb-0">
      <div class="relative mr-4">
        <input 
          :id="`task-${task.id}`"
          v-model="task.completed" 
          type="checkbox" 
          class="w-5 h-5 opacity-0 absolute cursor-pointer"
          @change="$emit('update-task', task)"
        />
        <label :for="`task-${task.id}`" class="block w-5 h-5 border-2 border-gray-400 rounded cursor-pointer transition-all duration-300"></label>
      </div>
      
      <div class="flex-1">
        <div @click="openEditModal">
          <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 cursor-pointer" :class="{ 'line-through text-gray-500': task.completed }">
            {{ task.title }}
          </h3>
          <p v-if="task.description" class="text-sm text-gray-600 dark:text-gray-400 mt-1" :class="{ 'line-through': task.completed }">
            {{ task.description }}
          </p>
        </div>
      </div>
    </div>

    <div class="flex gap-2 items-center self-end sm:self-auto">
      <button 
        @click="openEditModal" 
        class="p-2 border-none rounded cursor-pointer transition-all duration-300 flex items-center justify-center bg-gray-50 text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300"
        title="Edit task"
      >
        <Icon icon="mdi:pencil" width="18" height="18"/>    
      </button>
      
      <button 
        @click="$emit('delete-task', task.id)" 
        class="p-2 border-none rounded cursor-pointer transition-all duration-300 flex items-center justify-center bg-red-100 text-red-600 hover:bg-red-200 dark:bg-red-900 dark:text-red-300"
        title="Delete task"
      >
        <Icon icon="mdi:trash-can" width="18" height="18"/>
      </button>

      <button 
        v-if="task.completed"
        class="p-2 border-none rounded cursor-pointer flex items-center justify-center bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300"
        title="Completed"
      >
        <Icon icon="mdi:check-circle" width="18" height="18"/>
      </button>
    </div>
  </div>

  <!-- Edit Task Modal -->
  <div
    v-if="showEditModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    @click="closeEditModal"
  >
    <div
      class="bg-white rounded-xl p-8 max-w-md w-11/12 shadow-lg dark:bg-gray-800"
      @click.stop
    >
      <h3 class="m-0 mb-4 text-gray-800 text-xl font-semibold dark:text-gray-200">
        Edit Task
      </h3>

      <!-- Edit Form -->
      <form @submit.prevent="saveEdit">
        <!-- Title -->
        <input
          v-model="editTitle"
          type="text"
          placeholder="Task title..."
          class="w-full p-4 border-2 border-gray-300 rounded-lg font-inherit text-base mb-4 focus:outline-none focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
          required
          ref="titleInput"
        />

        <!-- Description -->
        <textarea
          v-model="editDescription"
          placeholder="Enter your task description here..."
          class="w-full p-4 border-2 border-gray-300 rounded-lg resize-y font-inherit text-base mb-4 focus:outline-none focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
          rows="4"
        ></textarea>

        <!-- Completion Status -->
        <div class="flex items-center mb-4">
          <input
            :id="`edit-completed-${task.id}`"
            v-model="editCompleted"
            type="checkbox"
            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
          />
          <label :for="`edit-completed-${task.id}`" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
            Mark as completed
          </label>
        </div>

        <div class="flex gap-4 justify-end">
          <button
            type="button"
            @click="closeEditModal"
            class="px-6 py-3 border-2 border-gray-300 rounded-lg bg-white text-gray-700 cursor-pointer font-medium transition-all duration-300 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-6 py-3 border-2 border-indigo-500 rounded-lg bg-indigo-500 text-white cursor-pointer font-medium transition-all duration-300 hover:bg-indigo-600 hover:border-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="!editTitle.trim()"
          >
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, nextTick } from 'vue'
import { Icon } from '@iconify/vue'

const props = defineProps({
  task: Object
})

const emit = defineEmits(['update-task', 'delete-task'])

// Edit modal state
const showEditModal = ref(false)
const editTitle = ref('')
const editDescription = ref('')
const editCompleted = ref(false)
const titleInput = ref(null)

// Open edit modal
function openEditModal() {
  editTitle.value = props.task.title
  editDescription.value = props.task.description || ''
  editCompleted.value = props.task.completed
  showEditModal.value = true
  
  // Focus input when modal opens
  nextTick(() => {
    if (titleInput.value) {
      titleInput.value.focus()
      titleInput.value.select()
    }
  })
}

// Close edit modal
function closeEditModal() {
  showEditModal.value = false
  // Reset form values
  editTitle.value = ''
  editDescription.value = ''
  editCompleted.value = false
}

// Save edited task
function saveEdit() {
  if (!editTitle.value.trim()) return

  const updatedTask = {
    ...props.task,
    title: editTitle.value.trim(),
    description: editDescription.value.trim() || null,
    completed: editCompleted.value
  }

  emit('update-task', updatedTask)
  closeEditModal()
}
</script>

<style scoped>
/* Custom styles for the checkbox */
input[type="checkbox"]:checked + label {
  background: #667eea;
  border-color: #667eea;
}

input[type="checkbox"]:checked + label::after {
  content: '✓';
  position: absolute;
  top: -2px;
  left: 3px;
  color: white;
  font-size: 14px;
  font-weight: bold;
}
</style>