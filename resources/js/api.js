import axios from 'axios'

const defaultBaseUrl = `${window.location.origin}/api/v1`
const csrfCookieUrl = `${window.location.origin}/sanctum/csrf-cookie`

const api = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || defaultBaseUrl,
    withCredentials: true,
    withXSRFToken: true,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
})

export const ensureCsrfCookie = async () => {
    await axios.get(csrfCookieUrl, { withCredentials: true })
}

const hasXsrfCookie = () => {
    return typeof document !== 'undefined' && document.cookie.includes('XSRF-TOKEN=')
}

api.interceptors.request.use(
    async (config) => {
        if (typeof config.baseURL === 'string' && typeof config.url === 'string') {
            const base = config.baseURL.replace(/\/+$/, '')
            if (base.endsWith('/api/v1') && config.url.startsWith('/api/v1/')) {
                config.url = config.url.replace(/^\/api\/v1/, '')
            }
        }

        // Add token to Authorization header if it exists
        const token = localStorage.getItem('token')
        if (token) {
            config.headers.Authorization = `Bearer ${token}`
        }

        const method = String(config.method || 'get').toLowerCase()
        const isUnsafe = !['get', 'head', 'options'].includes(method)
        if (isUnsafe && !hasXsrfCookie()) {
            await ensureCsrfCookie()
        }

        return config
    },
    (error) => Promise.reject(error)
)

api.interceptors.response.use(
    (response) => response,
    (error) => {
        const status = error.response?.status
        if (status === 401 || status === 419) {
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            localStorage.removeItem('user_role')
            localStorage.removeItem('user_name')
            localStorage.removeItem('user_email')
            localStorage.removeItem('user_id')
            const path = window.location.pathname
            const isAdminPath = path.startsWith('/admin')
            const isAdminLogin = path === '/admin/login'
            if (isAdminPath && !isAdminLogin) {
                window.location.href = '/admin/login'
            }
        }
        if (!status || status >= 500) {
            const message = error.response?.data?.message || error.response?.data?.error || error.message || 'Request failed'
            if (typeof window !== 'undefined') {
                window.dispatchEvent(new CustomEvent('sb-error-report', { detail: { message, status } }))
            }
        }
        return Promise.reject(error)
    }
)

export default api
