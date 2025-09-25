<template>
<div class="w-full ">
  <div class=" container mx-auto px-4 sm:px-6 lg:px-8 py-8  min-h-screen font-sans ">
    <SearchHeader 
    ref="searchHeader"
    :is-dark-mode="isDarkMode"
    @toggle-dark-mode="toggleDarkMode"
    />
    
    <LoadingState v-if="loading" />
    
    <ErrorState v-else-if="error" :error="error" @retry="fetchTasks" />
    
    <div v-else class="space-y-4 ">
      <TaskItem 
      v-for="task in filteredTasks" 
      :key="task.id" 
      :task="task"
      @update-task="updateTask"
      @delete-task="deleteTask"
      />

      <EmptyState v-if="filteredTasks.length === 0 && !loading" :message="getEmptyMessage()" />
    </div>
    
    <button
    @click="showAddModal = true"
    class="fixed bottom-8 right-12 w-15 h-15 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white border-none cursor-pointer shadow-lg shadow-indigo-500/40 transition-all duration-300 flex items-center justify-center hover:scale-110 hover:shadow-xl hover:shadow-indigo-500/60"
    title="Add new task"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
      </svg>
    </button>

    <AddTaskModal 
      :show="showAddModal"
      @close="closeAddModal"
      @add-task="addTask"
    />
  </div>
</div>
</template>

<script>
import { ref, computed, nextTick, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import SearchHeader from '@/components/SearchHeader.vue'
import TaskItem from '@/components/TaskItem.vue'
import AddTaskModal from '@/components/AddTaskModal.vue'
import LoadingState from '@/components/LoadingState.vue'
import ErrorState from '@/components/ErrorState.vue'
import EmptyState from '@/components/EmptyState.vue'

export default {
  name: 'TodoPage',
  components: {
    SearchHeader,
    TaskItem,
    AddTaskModal,
    LoadingState,
    ErrorState,
    EmptyState
  },
  setup() {
    const router = useRouter()
        const isDarkMode = ref(false)
    const showAddModal = ref(false)
    const editingId = ref(null)
    const editingText = ref('')
    const error = ref(null)
    const loading = ref(false)
    const tasks = ref([])
    const searchHeader = ref(null)
    const taskItems = ref([])

    function getAuthToken() {
      const token = localStorage.getItem('token')
      if (!token) {
        console.error('No auth token found')
        router.push('/auth')
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
    async function fetchTasks() {
      loading.value = true
      error.value = null
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/tasks', {
          headers: getAuthHeaders()
        })
        tasks.value = response.data
      } catch (err) {
        error.value = 'Failed to load tasks. Please try again.'
        console.error('Error fetching tasks:', err)
      } finally {
        loading.value = false
      }
    }
  

    async function addTask(newTask) {
  try {
    const res = await axios.post("http://127.0.0.1:8000/api/tasks", newTask, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    })
    tasks.value.unshift(res.data.task)
    closeAddModal()

  } catch (err) {
    console.error("Error adding task:", err)
  }
}

    async function updateTask(updatedTask) {
        try {
          const response = await axios.put(`http://127.0.0.1:8000/api/tasks/${updatedTask.id}`, {
            completed: updatedTask.completed,
            title: updatedTask.title,
            description: updatedTask.description
          }, {
            headers: getAuthHeaders()
          })
          const index = tasks.value.findIndex(t => t.id === updatedTask.id)
          if (index !== -1) {
            tasks.value[index] = { ...tasks.value[index], ...updatedTask }
          }
        } catch (err) {
          error.value = 'Failed to update task. Please try again.'
          console.error('Error updating task:', err)
        }
    }
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
    function startEdit(task) {
      editingId.value = task.id
      editingText.value = task.title
      nextTick(() => {
        const taskItem = taskItems.value.find(item => item.task.id === task.id)
        if (taskItem) {
          taskItem.editingText = task.title
          taskItem.focusInput()
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
    }
    const filteredTasks = computed(() => {
      let filtered = tasks.value
      const searchQuery = searchHeader.value?.searchQuery || ''
      const selectedFilter = searchHeader.value?.selectedFilter || 'ALL'
      if (searchQuery.trim()) {
        filtered = filtered.filter(task => 
          task.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
          (task.description && task.description.toLowerCase().includes(searchQuery.toLowerCase()))
        )
      }
      if (selectedFilter === 'COMPLETED') {
        filtered = filtered.filter(task => task.completed)
      } else if (selectedFilter === 'PENDING') {
        filtered = filtered.filter(task => !task.completed)
      }

      return filtered
    })

    function toggleDarkMode() {
      isDarkMode.value = !isDarkMode.value
      document.body.classList.toggle('dark-mode', isDarkMode.value)
    }

    function getEmptyMessage() {
      const searchQuery = searchHeader.value?.searchQuery || ''
      const selectedFilter = searchHeader.value?.selectedFilter || 'ALL'
      
      if (searchQuery.trim()) {
        return `No tasks found matching "${searchQuery}"`
      }
      if (selectedFilter === 'COMPLETED') {
        return 'No completed tasks found'
      }
      if (selectedFilter === 'PENDING') {
        return 'No pending tasks found'
      }
      return 'No tasks yet. Click the + button to add your first task!'
    }
    onMounted(() => {
      const savedDarkMode = localStorage.getItem('darkMode') === 'true'
      if (savedDarkMode) {
        isDarkMode.value = true
        document.body.classList.add('dark-mode')
      }
      
      fetchTasks()
    })
    watch(isDarkMode, (newValue) => {
      localStorage.setItem('darkMode', newValue.toString())
    })

    return {
      isDarkMode,
      showAddModal,
      editingId,
      error,
      loading,
      tasks,
      searchHeader,
      taskItems,
      fetchTasks,
      addTask,
      updateTask,
      deleteTask,
      startEdit,
      finishEdit,
      cancelEdit,
      closeAddModal,
      filteredTasks,
      toggleDarkMode,
      getEmptyMessage
    }
  }
}
</script>

<style scoped>
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

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>