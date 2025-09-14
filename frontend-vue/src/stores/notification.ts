import { ref } from 'vue'
import { defineStore } from 'pinia'

interface Notification {
  id: number
  type: 'success' | 'error' | 'warning' | 'info'
  message: string
  duration?: number
}

export const useNotificationStore = defineStore('notification', () => {
  const notifications = ref<Notification[]>([])
  let nextId = 1

  const addNotification = (notification: Omit<Notification, 'id'>) => {
    const id = nextId++
    const newNotification: Notification = {
      id,
      duration: 5000,
      ...notification,
    }

    notifications.value.push(newNotification)

    // Auto remove after duration
    if (newNotification.duration && newNotification.duration > 0) {
      setTimeout(() => {
        removeNotification(id)
      }, newNotification.duration)
    }

    return id
  }

  const removeNotification = (id: number) => {
    const index = notifications.value.findIndex(n => n.id === id)
    if (index > -1) {
      notifications.value.splice(index, 1)
    }
  }

  const clearAll = () => {
    notifications.value = []
  }

  return {
    notifications,
    addNotification,
    removeNotification,
    clearAll,
  }
})
