<script setup lang="ts">
import { useNotificationStore } from '../stores/notification'

const notificationStore = useNotificationStore()

const getNotificationClasses = (type: string) => {
  const baseClasses = 'flex items-center justify-between p-4 mb-4 text-sm rounded-lg'

  switch (type) {
    case 'success':
      return `${baseClasses} bg-green-100 text-green-800 border border-green-200`
    case 'error':
      return `${baseClasses} bg-red-100 text-red-800 border border-red-200`
    case 'warning':
      return `${baseClasses} bg-yellow-100 text-yellow-800 border border-yellow-200`
    case 'info':
    default:
      return `${baseClasses} bg-blue-100 text-blue-800 border border-blue-200`
  }
}

const getIconClasses = (type: string) => {
  const baseClasses = 'w-5 h-5 mr-3 flex-shrink-0'

  switch (type) {
    case 'success':
      return `${baseClasses} text-green-600`
    case 'error':
      return `${baseClasses} text-red-600`
    case 'warning':
      return `${baseClasses} text-yellow-600`
    case 'info':
    default:
      return `${baseClasses} text-blue-600`
  }
}

const getIcon = (type: string) => {
  switch (type) {
    case 'success':
      return '✓'
    case 'error':
      return '✕'
    case 'warning':
      return '⚠'
    case 'info':
    default:
      return 'ℹ'
  }
}
</script>

<template>
  <div class="fixed top-4 right-4 z-50 max-w-sm">
    <transition-group name="notification" tag="div">
      <div
        v-for="notification in notificationStore.notifications"
        :key="notification.id"
        :class="getNotificationClasses(notification.type)"
        role="alert"
      >
        <div class="flex items-center">
          <span :class="getIconClasses(notification.type)">
            {{ getIcon(notification.type) }}
          </span>
          <span>{{ notification.message }}</span>
        </div>
        <button
          @click="notificationStore.removeNotification(notification.id)"
          class="ml-4 text-gray-400 hover:text-gray-600 focus:outline-none"
          aria-label="Close notification"
        >
          ✕
        </button>
      </div>
    </transition-group>
  </div>
</template>

<style scoped>
.notification-enter-active,
.notification-leave-active {
  transition: all 0.3s ease;
}

.notification-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.notification-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
</style>
