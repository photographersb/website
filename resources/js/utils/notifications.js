import { createApp, h } from 'vue'
import Notification from '../components/Notification.vue'

let notificationContainer = null
let notificationCount = 0

export const notify = ({ type = 'info', title, message, duration = 5000 }) => {
  // Create container if doesn't exist
  if (!notificationContainer) {
    notificationContainer = document.createElement('div')
    notificationContainer.id = 'notification-container'
    document.body.appendChild(notificationContainer)
  }

  // Create notification wrapper
  const wrapper = document.createElement('div')
  wrapper.id = `notification-${++notificationCount}`
  notificationContainer.appendChild(wrapper)

  // Create notification component
  const app = createApp({
    render() {
      return h(Notification, {
        type,
        title,
        message,
        duration,
        onClose: () => {
          app.unmount()
          wrapper.remove()
          if (notificationContainer && notificationContainer.children.length === 0) {
            notificationContainer.remove()
            notificationContainer = null
          }
        }
      })
    }
  })

  app.mount(wrapper)
}

// Convenience methods
export const notifySuccess = (message, title = 'Success') => {
  notify({ type: 'success', title, message })
}

export const notifyError = (message, title = 'Error') => {
  notify({ type: 'error', title, message })
}

export const notifyWarning = (message, title = 'Warning') => {
  notify({ type: 'warning', title, message })
}

export const notifyInfo = (message, title = 'Info') => {
  notify({ type: 'info', title, message })
}
