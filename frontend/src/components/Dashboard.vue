<template>
<div class="w-full ">
  <div class=" container mx-auto px-4 sm:px-6 lg:px-8 py-8  min-h-screen font-sans ">
    <div class="text-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-8 tracking-wide dark:text-gray-200">TODO LIST</h1>
      
      <div class="flex flex-col sm:flex-row gap-4 items-center justify-center mb-4">
        <div class="relative flex-1 max-w-md">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Search note..." 
            class="w-full pl-4 pr-12 py-3 border-2 border-gray-300 rounded-full text-base bg-white focus:outline-none focus:border-indigo-500 transition-colors dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200"
          />
          <button class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-transparent border-none text-gray-500 cursor-pointer p-2 rounded-full hover:text-indigo-500 transition-colors">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8"/>
              <path d="M21 21l-4.35-4.35"/>
            </svg>
          </button>
        </div>
        
        <div class="flex gap-2 items-center">
          <select v-model="selectedFilter" class="px-4 py-3 border-2 border-indigo-500 rounded-full bg-indigo-500 text-white font-semibold cursor-pointer text-sm">
            <option value="ALL">ALL</option>
            <option value="COMPLETED">COMPLETED</option>
            <option value="PENDING">PENDING</option>
          </select>
          
          <button @click="toggleDarkMode" class="p-3 border-2 border-gray-300 rounded-full bg-white text-gray-700 cursor-pointer transition-all duration-300 flex items-center justify-center hover:border-indigo-500 hover:bg-indigo-500 hover:text-white dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
            <svg v-if="isDarkMode" width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <svg v-else width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
              <path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div>
      <p class="text-gray-600 dark:text-gray-400 mt-2">Loading tasks...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-8">
      <p class="text-red-500 dark:text-red-400">{{ error }}</p>
      <button @click="fetchTasks" class="mt-2 px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600">
        Retry
      </button>
    </div>

    <!-- Tasks List -->
    <div v-else class="space-y-4">
      <div 
        v-for="task in filteredTasks" 
        :key="task.id" 
        class="bg-white rounded-xl p-5 shadow-sm border border-gray-300 transition-all duration-300 mb-4 flex flex-col sm:flex-row items-center justify-between hover:shadow-md hover:-translate-y-1 dark:bg-gray-800 dark:border-gray-600"
        :class="{ 'bg-gray-50 border-gray-400 dark:bg-gray-700': task.completed, 'border-2 border-indigo-500': task.id === editingId }"
      >
        <div class="flex items-center flex-1 w-full mb-4 sm:mb-0 ">
          <div class="relative mr-4">
            <input 
              :id="`task-${task.id}`"
              v-model="task.completed" 
              type="checkbox" 
              class="w-5 h-5 opacity-0 absolute cursor-pointer"
              @change="updateTask(task)"
            />
            <label :for="`task-${task.id}`" class="block w-5 h-5 border-2 border-gray-400 rounded cursor-pointer transition-all duration-300"></label>
          </div>
          
          <div class="flex-1" @dblclick="startEdit(task)">
            <input 
              v-if="task.id === editingId"
              v-model="editingText"
              @blur="finishEdit"
              @keyup.enter="finishEdit"
              @keyup.escape="cancelEdit"
              class="w-full p-2 border-2 border-indigo-500 rounded text-lg font-medium bg-white dark:bg-gray-800 dark:text-gray-200"
              ref="editInput"
            />
            <div v-else>
              <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200" :class="{ 'line-through text-gray-500': task.completed }">
                {{ task.title }}
              </h3>
              <p v-if="task.description" class="text-sm text-gray-600 dark:text-gray-400 mt-1" :class="{ 'line-through': task.completed }">
                {{ task.description }}
              </p>
            </div>
          </div>
        </div>

        <div class="flex gap-2 items-center self-end sm:self-auto">
          <!-- if user is owner of task make the button not disable else disable it -->
          <button 
            v-if="!task.completed"
            @click="startEdit(task)" 
            class="p-2 border-none rounded cursor-pointer transition-all duration-300 flex items-center justify-center bg-gray-50 text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300"
            title="Edit task"
          >
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
              <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
          </button>
          
          <button 
            @click="deleteTask(task.id)" 
            class="p-2 border-none rounded cursor-pointer transition-all duration-300 flex items-center justify-center bg-red-100 text-red-600 hover:bg-red-200 dark:bg-red-900 dark:text-red-300"
            title="Delete task"
          >
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <polyline points="3,6 5,6 21,6"/>
              <path d="M19,6v14a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6m3,0V4a2,2,0,0,1,2-2h4a2,2,0,0,1,2,2V6"/>
            </svg>
          </button>

          <button 
            v-if="task.completed"
            class="p-2 border-none rounded cursor-pointer flex items-center justify-center bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300"
            title="Completed"
          >
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </button>
        </div>
      </div>

      <div v-if="filteredTasks.length === 0 && !loading" class="text-center py-12 text-gray-500 text-lg">
        <p>{{ getEmptyMessage() }}</p>
      </div>
    </div>

    <!-- Add Task Button -->
    <button
      @click="showAddModal = true"
      class="fixed bottom-8 right-8 w-15 h-15 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white border-none cursor-pointer shadow-lg shadow-indigo-500/40 transition-all duration-300 flex items-center justify-center hover:scale-110 hover:shadow-xl hover:shadow-indigo-500/60"
      title="Add new task"
    >
      <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 4v16m8-8H4" />
      </svg>
    </button>

    <!-- Add Task Modal -->
    <div
      v-if="showAddModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click="closeAddModal"
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

        <!-- Form -->
        <form @submit.prevent="addTask">
          <!-- Title -->
          <input
            v-model="newNoteTitle"
            type="text"
            placeholder="Task title..."
            class="w-full p-4 border-2 border-gray-300 rounded-lg font-inherit text-base mb-4 focus:outline-none focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
            required
          />

          <!-- Description -->
          <textarea
            v-model="newNoteDescription"
            placeholder="Enter your task description here..."
            class="w-full p-4 border-2 border-gray-300 rounded-lg resize-y font-inherit text-base mb-4 focus:outline-none focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
            rows="4"
          ></textarea>

          <div class="flex gap-4 justify-end">
            <button
              type="button"
              @click="closeAddModal"
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
  </div>
</div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, watch } from 'vue'
import axios from 'axios'

// Reactive data
const searchQuery = ref('')
const selectedFilter = ref('ALL')
const isDarkMode = ref(false)
const showAddModal = ref(false)
const editingId = ref(null)
const editingText = ref('')
const editInput = ref(null)
const newNoteTitle = ref('')
const newNoteDescription = ref('')
const error = ref(null)
const loading = ref(false)
const tasks = ref([])

function getAuthToken() {
    const token = localStorage.getItem('token')
    if (!token) {
        console.error('No auth token found')
        window.location.href = '/auth'
        return null
    }
    return token
}

function getAuthHeaders() {
    return {
        'Authorization': `Bearer ${getAuthToken()}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
}

// Fetch tasks from API
async function fetchTasks() {
    loading.value = true
    error.value = null
    try {
        const response = await axios.get('http://127.0.0.1:8000/api/tasks', {
            headers: getAuthHeaders()
        })
      tasks.value = response.data
        console.log('Tasks fetched:', tasks.value)
    } catch (err) {
        error.value = 'Failed to load tasks. Please try again.'
        console.error('Error fetching tasks:', err)
    } finally {
        loading.value = false
    }
}

// Add new task
async function addTask() {
    if (!newNoteTitle.value.trim()) return

    try {
        const response = await axios.post('http://127.0.0.1:8000/api/tasks', {
            title: newNoteTitle.value.trim(),
            description: newNoteDescription.value.trim() || null,
        }, {
            headers: getAuthHeaders()
        })

        // Add the new task to the beginning of the list
        tasks.value.unshift(response.data)
        
        // Reset form and close modal
        newNoteTitle.value = ''
        newNoteDescription.value = ''
        closeAddModal()
    } catch (err) {
        error.value = 'Failed to create task. Please try again.'
        console.error('Error creating task:', err)
    }
}

// Update only my task completion status
async function updateTask(task) {
    try {
        const response = await axios.put(`http://127.0.0.1:8000/api/tasks/${task.id}`, {
            completed: task.completed,
            title: task.title,
            description: task.description
        }, {
            headers: getAuthHeaders()
        })
      // Object.assign(task, response.data)
        console.log('Task updated:', response.data)
    } catch (err) {
        // Revert the change if API call fails
        task.completed = !task.completed
        error.value = 'Failed to update task. Please try again.'
        console.error('Error updating task:', err)
    }
}

// Delete task
async function deleteTask(id) {
    if (!confirm('Are you sure you want to delete this task?')) return

    try {
        await axios.delete(`http://127.0.0.1:8000/api/tasks/${id}`, {
            headers: getAuthHeaders()
        })
        tasks.value = tasks.value.filter(task => task.id !== id)
    } catch (err) {
        error.value = 'Failed to delete task. Please try again.'
        console.error('Error deleting task:', err)
    }
}

// Edit task functions
function startEdit(task) {
    editingId.value = task.id
    editingText.value = task.title
    nextTick(() => {
        if (editInput.value) {
            editInput.value.focus()
            editInput.value.select()
        }
    })
}

async function finishEdit() {
    if (editingId.value && editingText.value.trim()) {
        const task = tasks.value.find(t => t.id === editingId.value)
        if (task) {
            const originalTitle = task.title
            task.title = editingText.value.trim()
            
            try {
                await axios.put(`http://127.0.0.1:8000/api/tasks/${task.id}`, {
                    title: task.title,
                    description: task.description,
                    completed: task.completed
                }, {
                    headers: getAuthHeaders()
                })
            } catch (err) {
                // Revert if API call fails
                task.title = originalTitle
                error.value = 'Failed to update task. Please try again.'
                console.error('Error updating task:', err)
            }
        }
    }
    cancelEdit()
}

function cancelEdit() {
    editingId.value = null
    editingText.value = ''
}

function closeAddModal() {
    showAddModal.value = false
    newNoteTitle.value = ''
    newNoteDescription.value = ''
}

// Computed properties
const filteredTasks = computed(() => {
    let filtered = tasks.value

    // Filter by search query
    if (searchQuery.value.trim()) {
        filtered = filtered.filter(task => 
            task.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (task.description && task.description.toLowerCase().includes(searchQuery.value.toLowerCase()))
        )
    }

    // Filter by completion status
    if (selectedFilter.value === 'COMPLETED') {
        filtered = filtered.filter(task => task.completed)
    } else if (selectedFilter.value === 'PENDING') {
        filtered = filtered.filter(task => !task.completed)
    }

    return filtered
})

function toggleDarkMode() {
    isDarkMode.value = !isDarkMode.value
    document.body.classList.toggle('dark-mode', isDarkMode.value)
}

function getEmptyMessage() {
    if (searchQuery.value.trim()) {
        return `No tasks found matching "${searchQuery.value}"`
    }
    if (selectedFilter.value === 'COMPLETED') {
        return 'No completed tasks found'
    }
    if (selectedFilter.value === 'PENDING') {
        return 'No pending tasks found'
    }
    return 'No tasks yet. Click the + button to add your first task!'
}

// Load dark mode preference and fetch tasks on mount
onMounted(() => {
    const savedDarkMode = localStorage.getItem('darkMode') === 'true'
    if (savedDarkMode) {
        isDarkMode.value = true
        document.body.classList.add('dark-mode')
    }
    
    // Fetch tasks when component mounts
    fetchTasks()
})

// Save dark mode preference
watch(isDarkMode, (newValue) => {
    localStorage.setItem('darkMode', newValue.toString())
})
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

:global(body.dark-mode) {
  background-color: #1a202c;
  color: #e2e8f0;
}

/* Loading spinner */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>