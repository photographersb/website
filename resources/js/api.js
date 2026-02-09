import axios from 'axios'

const defaultBaseUrl = `${window.location.origin}/api/v1`

const api = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || defaultBaseUrl,
    withCredentials: true,
    withXSRFToken: true,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
})

// Request interceptor - add auth token to all requests
api.interceptors.request.use(
    (config) => {
        if (typeof config.baseURL === 'string' && typeof config.url === 'string') {
            const base = config.baseURL.replace(/\/+$/, '')
            if (base.endsWith('/api/v1') && config.url.startsWith('/api/v1/')) {
                config.url = config.url.replace(/^\/api\/v1/, '')
            }
        }

        const token = localStorage.getItem('token') || localStorage.getItem('auth_token')
        if (token) {
            localStorage.setItem('token', token)
            localStorage.setItem('auth_token', token)
            config.headers.Authorization = `Bearer ${token}`
        }
        return config
    },
    (error) => Promise.reject(error)
)

// Response interceptor - handle 401 (expired token)
api.interceptors.response.use(
    (response) => response,
    (error) => {
        const status = error.response?.status
        if (status === 401 || status === 419) {
            localStorage.removeItem('token')
            localStorage.removeItem('auth_token')
            localStorage.removeItem('user')
            localStorage.removeItem('user_role')
            const isAdminPath = window.location.pathname.startsWith('/admin')
            window.location.href = isAdminPath ? '/admin/login' : '/auth'
        }
        return Promise.reject(error)
    }
)

export default api
