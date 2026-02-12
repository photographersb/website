import { ref, computed } from 'vue'
import api from '../api'

/**
 * Composable for monitoring and managing API health
 * Provides retry logic, fallback handling, and user feedback
 */
export function useApiHealth() {
  const isHealthy = ref(true)
  const isChecking = ref(false)
  const lastCheckTime = ref(null)
  const failureCount = ref(0)
  const maxRetries = 3
  const retryDelay = 1000 // ms

  /**
   * Check if API is responsive
   */
  async function checkHealth() {
    isChecking.value = true
    try {
      await api.get('/admin/health', {
        timeout: 5000, // 5 second timeout
      })
      isHealthy.value = true
      failureCount.value = 0
      lastCheckTime.value = new Date()
      return true
    } catch (error) {
      isHealthy.value = false
      failureCount.value++
      lastCheckTime.value = new Date()
      console.warn('API health check failed:', error.message)
      return false
    } finally {
      isChecking.value = false
    }
  }

  /**
   * Retry a failed API call with exponential backoff
   */
  async function retryRequest(fn, attempt = 1) {
    try {
      return await fn()
    } catch (error) {
      // Don't retry network errors or 4xx errors
      if (!error.response || (error.response?.status >= 400 && error.response?.status < 500)) {
        throw error
      }

      if (attempt < maxRetries) {
        const delay = retryDelay * Math.pow(2, attempt - 1) // Exponential backoff
        await new Promise(resolve => setTimeout(resolve, delay))
        return retryRequest(fn, attempt + 1)
      }

      throw error
    }
  }

  /**
   * Make API call with automatic health check and retry
   */
  async function fetchWithFallback(fn, fallbackValue = null) {
    if (!isHealthy.value) {
      // Try health check before failing
      const healthy = await checkHealth()
      if (!healthy) {
        return fallbackValue
      }
    }

    try {
      return await retryRequest(fn)
    } catch (error) {
      // Mark API as unhealthy on critical errors
      if (error.code === 'ECONNABORTED' || error.code === 'ECONNREFUSED' || error.message.includes('timeout')) {
        isHealthy.value = false
      }
      throw error
    }
  }

  /**
   * Check if we should show the offline message
   */
  const shouldShowOfflineMessage = computed(() => {
    return !isHealthy.value && failureCount.value >= 2
  })

  /**
   * Get user-friendly status message
   */
  const statusMessage = computed(() => {
    if (isHealthy.value) {
      return 'API is online'
    }
    if (failureCount.value === 1) {
      return 'Connection issues detected'
    }
    return 'API is temporarily unavailable'
  })

  return {
    isHealthy,
    isChecking,
    failureCount,
    lastCheckTime,
    checkHealth,
    retryRequest,
    fetchWithFallback,
    shouldShowOfflineMessage,
    statusMessage,
  }
}
