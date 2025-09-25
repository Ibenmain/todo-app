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
        <div @click="openViewModal" class="break-words px-3">
          <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200" :class="{ 'line-through text-gray-500': task.completed }">
            {{ task.title.length > 40 ? task.title.substring(0, 40) + '...' : task.title }}
          </h3>
          <p v-if="task.description" class="text-sm text-gray-600 dark:text-gray-400 mt-1" :class="{ 'line-through': task.completed }">
            {{ task.description.length > 100 ? task.description.substring(0, 100) + '...' : task.description }}
          </p>
        </div>
      </div>
    </div>

    <div class="flex gap-2 items-center self-end sm:self-auto">
      <button 
        @click="openViewModal" 
        class="p-2 border-none rounded cursor-pointer transition-all duration-300 flex items-center justify-center bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-800 dark:bg-blue-900 dark:text-blue-300"
        title="View task details"
      >
        <Icon icon="mdi:eye-outline" width="18" height="18"/>    
      </button>
      
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

  <div
    v-if="showViewModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click="closeViewModal"
  >
    <div
      class="bg-white rounded-xl p-8 max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-lg dark:bg-gray-800"
      @click.stop
    >
      <div class="flex justify-between items-start mb-6">
        <h3 class="m-0 text-gray-800 text-2xl font-bold dark:text-gray-200">
          Task Details
        </h3>
        <button
          @click="closeViewModal"
          class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          title="Close"
        >
          <Icon icon="mdi:close" width="24" height="24" class="text-gray-500 dark:text-gray-400"/>
        </button>
      </div>

      <div class="space-y-6">
        <div class="flex items-center gap-3">
          <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Status:</span>
          <span 
            :class="[
              'px-3 py-1 rounded-full text-sm font-medium',
              task.completed 
                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
            ]"
          >
            {{ task.completed ? 'Completed' : 'Pending' }}
          </span>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
            Title
          </label>
          <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700">
            <p class="text-lg text-gray-800 dark:text-gray-200 font-medium">{{ task.title }}</p>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
            Description
          </label>
          <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700 min-h-[100px]">
            <p 
              v-if="task.description" 
              class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap"
            >
              {{ task.description }}
            </p>
            <p v-else class="text-gray-500 dark:text-gray-400 italic">
              No description provided
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
          <div>
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
              Created Date
            </label>
            <p class="text-gray-800 dark:text-gray-200">
              {{ formatDate(task.created_at) }}
            </p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
              Last Updated
            </label>
            <p class="text-gray-800 dark:text-gray-200">
              {{ formatDate(task.updated_at) }}
            </p>
          </div>
        </div>

        <div class="flex gap-3 justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
          <button
            @click="closeViewModal"
            class="px-6 py-3 border-2 border-gray-300 rounded-lg bg-white text-gray-700 cursor-pointer font-medium transition-all duration-300 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
          >
            Close
          </button>
          <button
            @click="openEditFromView"
            class="px-6 py-3 border-2 border-indigo-500 rounded-lg bg-indigo-500 text-white cursor-pointer font-medium transition-all duration-300 hover:bg-indigo-600 hover:border-indigo-600 flex items-center gap-2"
          >
            <Icon icon="mdi:pencil" width="16" height="16"/>
            Edit Task
          </button>
        </div>
      </div>
    </div>
  </div>

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

      <form @submit.prevent="saveEdit">
        <input
          v-model="editTitle"
          type="text"
          placeholder="Task title..."
          class="w-full p-4 border-2 border-gray-300 rounded-lg font-inherit text-base mb-4 focus:outline-none focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
          required
          ref="titleInput"
        />

        <textarea
          v-model="editDescription"
          placeholder="Enter your task description here..."
          class="w-full p-4 border-2 border-gray-300 rounded-lg resize-y font-inherit text-base mb-4 focus:outline-none focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
          rows="4"
        ></textarea>

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

const showViewModal = ref(false)
const showEditModal = ref(false)

const editTitle = ref('')
const editDescription = ref('')
const editCompleted = ref(false)
const titleInput = ref(null)

function openViewModal() {
  showViewModal.value = true
}

function closeViewModal() {
  showViewModal.value = false
}

function openEditFromView() {
  closeViewModal()
  openEditModal()
}

function openEditModal() {
  editTitle.value = props.task.title
  editDescription.value = props.task.description || ''
  editCompleted.value = props.task.completed
  showEditModal.value = true
  
  nextTick(() => {
    if (titleInput.value) {
      titleInput.value.focus()
      titleInput.value.select()
    }
  })
}

function closeEditModal() {
  showEditModal.value = false
  editTitle.value = ''
  editDescription.value = ''
  editCompleted.value = false
}

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

function formatDate(dateString) {
  if (!dateString) return 'Unknown'
  
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

<style scoped>
input[type="checkbox"]:checked + label {
  background: #667eea;
  border-color: #667eea;
}

.break-words {
  word-break: break-word;
  cursor: pointer;
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

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-content::-webkit-scrollbar {
  width: 6px;
}

.modal-content::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.modal-content::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.modal-content::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}

.dark .modal-content::-webkit-scrollbar-track {
  background: #374151;
}

.dark .modal-content::-webkit-scrollbar-thumb {
  background: #6b7280;
}

.dark .modal-content::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}
</style>