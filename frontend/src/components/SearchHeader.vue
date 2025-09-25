<template>
  <div class="text-center mb-8 relative">
    <h1 class="text-3xl font-bold text-gray-800 mb-8 tracking-wide dark:text-gray-200">TODO LIST</h1>
    
    <div class="flex flex-col sm:flex-row items-center justify-around mb-4">
      <!-- Search Bar -->
      <div class="relative flex-1 max-w-xl">
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Search note..." 
          class="w-full pl-4 pr-12 py-3 border-2 border-gray-300 rounded-full text-base  focus:outline-none focus:border-indigo-500 transition-all duration-300 shadow-sm hover:shadow-md dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200"
        />
        <button class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-transparent border-none text-gray-500 cursor-pointer p-2 rounded-full hover:text-indigo-500 transition-colors">
          <Icon icon="mdi:magnify" width="20" height="20"/>
        </button>
      </div>
      
      <!-- Controls Group -->
      <div class="flex gap-3 items-center">
        <!-- Animated Dropdown -->
        <div class="relative group">
          <select 
            v-model="selectedFilter" 
            class="px-6 py-3 border-2 border-indigo-500 rounded-full bg-indigo-500 text-white font-semibold cursor-pointer text-sm transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 hover:bg-indigo-600 hover:border-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-200 dark:focus:ring-indigo-800 appearance-none pr-10"
          >
            <option value="ALL">ALL</option>
            <option value="COMPLETED">COMPLETED</option>
            <option value="PENDING">PENDING</option>
          </select>
          <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
            <Icon icon="mdi:chevron-down" class="text-white transition-transform duration-300 group-hover:rotate-180" width="16" height="16"/>
          </div>
        </div>
        
        <!-- Notification Button -->
        <div class="relative">
          <button 
            @click="toggleNotifications"
            class="p-3 border-2 border-gray-300 rounded-full bg-white text-gray-700 cursor-pointer transition-all duration-300 flex items-center justify-center hover:border-purple-500 hover:bg-purple-500 hover:text-white dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 hover:scale-105 shadow-sm hover:shadow-md"
            title="Notifications"
          >
            <Icon icon="mdi:bell" width="20" height="20"/>
          </button>
          <span 
            v-if="unreadCount > 0"
            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse"
          >
            {{ unreadCount }}
          </span>

          <!-- Notification Popup -->
          <div 
            v-if="showNotifications"
            class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50"
          >
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
              <div class="flex justify-between items-center">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Notifications</h3>
                <span class="text-sm text-indigo-600 dark:text-indigo-400">{{ unreadCount }} new</span>
              </div>
            </div>
            
            <div class="max-h-64 overflow-y-auto">
              <div 
                v-for="notification in notifications"
                :key="notification.id"
                :class="['p-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors cursor-pointer', { 'bg-blue-50 dark:bg-blue-900': !notification.read }]"
                @click="markNotificationAsRead(notification)"
              >
                <div class="flex items-start space-x-3">
                  <div class="flex-shrink-0">
                    <div :class="['w-8 h-8 rounded-full flex items-center justify-center', getNotificationBg(notification.type)]">
                      <Icon :icon="getNotificationIcon(notification.type)" :class="getNotificationIconColor(notification.type)" width="16" height="16"/>
                    </div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between">
                      <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                        {{ getNotificationTitle(notification.type) }}
                      </p>
                      <span v-if="!notification.read" class="ml-2 inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                      {{ notification.message }}
                    </p>
                    <div v-if="notification.data" class="mt-1">
                      <span class="text-xs text-indigo-600 dark:text-indigo-400">
                        By: {{ notification.data.user_name }}
                      </span>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500 mt-1 block">
                      {{ formatTime(notification.created_at) }}
                    </span>
                  </div>
                  <button 
                    @click.stop="removeNotification(notification.id)"
                    class="flex-shrink-0 text-gray-400 hover:text-red-500 transition-colors p-1 rounded"
                    title="Dismiss"
                  >
                    <Icon icon="mdi:close" width="16" height="16"/>
                  </button>
                </div>
              </div>
              
              <div v-if="notifications.length === 0" class="p-8 text-center">
                <Icon icon="mdi:bell-off" class="text-gray-400 mx-auto mb-2" width="32" height="32"/>
                <p class="text-gray-500 dark:text-gray-400 text-sm">No notifications yet</p>
              </div>
            </div>
            
            <div class="p-3 border-t border-gray-200 dark:border-gray-700">
              <button 
                @click="markAllAsRead"
                class="w-full py-2 text-sm text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="unreadCount === 0"
              >
                Mark all as read
              </button>
            </div>
          </div>
        </div>
        
        <!-- Logout Button -->
        <button 
          @click="logout" 
          class="p-3 border-2 border-gray-300 rounded-full bg-white text-gray-700 cursor-pointer transition-all duration-300 flex items-center justify-center hover:border-red-600 hover:bg-red-600 hover:text-white dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 hover:scale-105 shadow-sm hover:shadow-md"
          title="Logout"
        >
          <Icon icon="mdi:logout" width="20" height="20"/>
        </button>
      </div>
    </div>

    <!-- Toast Notification -->
    <div 
      v-if="showToast" 
      :class="['fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300', toastClasses]"
      :style="toastStyle"
    >
      <div class="flex items-center space-x-3">
        <Icon :icon="getToastIcon()" class="text-white" width="20" height="20"/>
        <div>
          <p class="text-white font-medium">{{ toastMessage }}</p>
          <p class="text-white text-opacity-80 text-sm" v-if="toastTask">Task: {{ toastTask }}</p>
        </div>
        <button @click="hideToast" class="text-white hover:text-opacity-70 transition-colors">
          <Icon icon="mdi:close" width="16" height="16"/>
        </button>
      </div>
    </div>

    <!-- Backdrop for notifications -->
    <div 
      v-if="showNotifications"
      class="fixed inset-0 z-40"
      @click="showNotifications = false"
    ></div>
  </div>
</template>

<script>
import { Icon } from '@iconify/vue'
import axios from 'axios'

export default {
  name: 'HeaderComponent',
  components: {
    Icon
  },
  props: {
    isDarkMode: Boolean
  },
  emits: ['toggle-dark-mode'],
  data() {
    return {
      searchQuery: '',
      selectedFilter: 'ALL',
      showNotifications: false,
      showToast: false,
      toastMessage: '',
      toastType: 'info',
      toastTask: '',
      toastTimeout: null,
      notifications: [],
      pusher: null,
      channel: null,
      loading: false
    }
  },
  computed: {
    unreadCount() {
      return this.notifications.filter(n => !n.read).length
    },
    toastClasses() {
      const classes = {
        info: 'bg-blue-500',
        success: 'bg-green-500',
        warning: 'bg-yellow-500',
        error: 'bg-red-500'
      }
      return classes[this.toastType] || 'bg-blue-500'
    },
    toastStyle() {
      return this.showToast 
        ? 'translate-x-0 opacity-100' 
        : 'translate-x-full opacity-0'
    }
  },
  async mounted() {
    await this.fetchNotifications()
    this.initializePusher()
    document.addEventListener('click', this.handleClickOutside)
    document.addEventListener('keydown', this.handleEscapeKey)
  },
  beforeUnmount() {
    if (this.channel) {
      this.channel.unbind('notification.created')
    }
    document.removeEventListener('click', this.handleClickOutside)
    document.removeEventListener('keydown', this.handleEscapeKey)
  },
  methods: {
    initializePusher() {
      try {
        this.pusher = new Pusher('d0d482a2fbc24bdf5600', {
          cluster: 'mt1',
          forceTLS: true
        })

        this.channel = this.pusher.subscribe('notifications')

        this.channel.bind('notification.created', (data) => {
          this.handleRealTimeNotification(data)
        })

      } catch (error) {
        console.error('Error initializing Pusher:', error)
      }
    },

    handleRealTimeNotification(data) {
      const notification = {
        id: data.id || Date.now(),
        message: data.message,
        type: data.type || 'info',
        data: data.data || {},
        timestamp: data.timestamp,
        read: false,
        created_at: data.timestamp
      }

      this.notifications.unshift(notification)

      this.showToastNotification(
        data.message, 
        data.type || 'info', 
        data.data?.task_title
      )
    },
    async fetchNotifications() {
      try {
        const token = localStorage.getItem('token')
        if (!token) {
          console.log('No token found, skipping notification fetch')
          return
        }
        const response = await axios.get('http://127.0.0.1:8000/api/notifications', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        })
        this.notifications = response.data.map(notification => ({
          ...notification,
          read: Boolean(notification.read)
        }))

        console.log('Notifications loaded:', this.notifications.length)
      } catch (error) {
        console.error('Error fetching notifications:', error)
      }
    },

    async markNotificationAsRead(notification) {
      if (notification.read) {
        this.showNotifications = false
        return
      }

      try {
        const token = localStorage.getItem('token')
        if (!token) return

        // Update locally first for immediate feedback
        notification.read = true

        // Send API request to mark as read
        console.log('Marking notification as read:', notification.id)
        await axios.post(`http://127.0.0.1:8000/api/notifications/${notification.id}`, {}, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        })

        console.log('Notification marked as read:', notification.id)
        
        // Close dropdown after a short delay for better UX
        setTimeout(() => {
          this.showNotifications = false
        }, 300)

      } catch (error) {
        console.error('Error marking notification as read:', error)
        // Revert local change if API call fails
        notification.read = false
      }
    },

    async markAllAsRead() {
      if (this.unreadCount === 0) return

      try {
        const token = localStorage.getItem('token')
        if (!token) return

        this.loading = true

        // Update all notifications locally first
        this.notifications.forEach(notification => {
          notification.read = true
        })

        // Send API request to mark all as read
        await axios.put('http://127.0.0.1:8000/api/notifications/mark-all-read', {}, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        })

        console.log('All notifications marked as read')
        this.showToastNotification('All notifications marked as read', 'success')

      } catch (error) {
        console.error('Error marking all notifications as read:', error)
        // Revert local changes if API call fails
        this.notifications.forEach(notification => {
          if (!notification.originalReadState) {
            notification.read = false
          }
        })
      } finally {
        this.loading = false
      }
    },

    async removeNotification(notificationId) {
      try {
        const token = localStorage.getItem('token')
        if (!token) return

        // Remove from local list first
        this.notifications = this.notifications.filter(n => n.id !== notificationId)

        // Send API request to delete notification
        await axios.delete(`http://127.0.0.1:8000/api/notifications/${notificationId}`, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        })

        console.log('Notification removed:', notificationId)

      } catch (error) {
        console.error('Error removing notification:', error)
        // Note: We don't revert here as the user expects immediate removal
      }
    },

    showToastNotification(message, type = 'info', taskTitle = '') {
      this.toastMessage = message
      this.toastType = type
      this.toastTask = taskTitle
      this.showToast = true

      clearTimeout(this.toastTimeout)
      this.toastTimeout = setTimeout(() => {
        this.hideToast()
      }, 5000)
    },

    hideToast() {
      this.showToast = false
      clearTimeout(this.toastTimeout)
    },

    toggleNotifications() {
      this.showNotifications = !this.showNotifications
    },

    getNotificationIcon(type) {
      const icons = {
        info: 'mdi:information',
        success: 'mdi:check-circle',
        warning: 'mdi:alert-circle',
        error: 'mdi:close-circle'
      }
      return icons[type] || 'mdi:bell'
    },

    getNotificationBg(type) {
      const backgrounds = {
        info: 'bg-blue-100',
        success: 'bg-green-100',
        warning: 'bg-yellow-100',
        error: 'bg-red-100'
      }
      return backgrounds[type] || 'bg-gray-100'
    },

    getNotificationIconColor(type) {
      const colors = {
        info: 'text-blue-600',
        success: 'text-green-600',
        warning: 'text-yellow-600',
        error: 'text-red-600'
      }
      return colors[type] || 'text-gray-600'
    },

    getNotificationTitle(type) {
      const titles = {
        info: 'Task Created',
        success: 'Task Completed',
        warning: 'Task Updated',
        error: 'Task Deleted'
      }
      return titles[type] || 'Notification'
    },

    getToastIcon() {
      return this.getNotificationIcon(this.toastType)
    },

    formatTime(timestamp) {
      if (!timestamp) return 'Just now'
      
      const now = new Date()
      const time = new Date(timestamp)
      const diffInSeconds = Math.floor((now - time) / 1000)
      
      if (diffInSeconds < 60) return 'Just now'
      if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`
      if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`
      return `${Math.floor(diffInSeconds / 86400)}d ago`
    },

    handleClickOutside(event) {
      if (!this.$el.contains(event.target)) {
        this.showNotifications = false
      }
    },

    handleEscapeKey(event) {
      if (event.key === 'Escape' && this.showNotifications) {
        this.showNotifications = false
      }
    },

    logout() {
      localStorage.removeItem('token')
      window.location.href = '/auth/login'
    }
  }
}
</script>

<style scoped>
/* Smooth transitions for notifications */
.notification-item {
  transition: all 0.2s ease-in-out;
}

/* Loading state for mark all as read button */
button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Hover effects for notification items */
.notification-item:hover {
  transform: translateX(2px);
}

/* Visual indicator for unread notifications */
.unread-dot {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

/* Custom scrollbar for notification dropdown */
.max-h-64::-webkit-scrollbar {
  width: 6px;
}

.max-h-64::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.max-h-64::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.max-h-64::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}

.dark .max-h-64::-webkit-scrollbar-track {
  background: #374151;
}

.dark .max-h-64::-webkit-scrollbar-thumb {
  background: #6b7280;
}

.dark .max-h-64::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}
</style>