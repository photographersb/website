import axios from 'axios'

localStorage.removeItem('token')

axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL || '/api/v1'
axios.defaults.withCredentials = true
axios.defaults.withXSRFToken = true

const csrfCookieUrl = `${window.location.origin}/sanctum/csrf-cookie`
const hasXsrfCookie = () => document.cookie.includes('XSRF-TOKEN=')

const getCookie = (name) => {
    const value = `; ${document.cookie}`
    const parts = value.split(`; ${name}=`)
    if (parts.length === 2) {
        return parts.pop().split(';').shift()
    }
    return ''
}

const shouldAttachAuth = (url) => {
    if (!url || typeof url !== 'string') return false
    if (url.startsWith('/api/') || url.startsWith('/sanctum/')) return true
    if (url.startsWith(`${window.location.origin}/api/`)) return true
    if (url.startsWith(`${window.location.origin}/sanctum/`)) return true
    return false
}

const scrubAuthorizationHeader = (headers) => {
    if (!headers) return
    const auth = headers.get('Authorization') || headers.get('authorization')
    if (!auth) return
    const normalized = String(auth).toLowerCase()
    if (normalized.includes('bearer null') || normalized.includes('bearer undefined')) {
        headers.delete('Authorization')
        headers.delete('authorization')
    }
}

const originalFetch = window.fetch ? window.fetch.bind(window) : null

if (originalFetch) {
    window.fetch = async (input, init = {}) => {
        const url = typeof input === 'string' ? input : input?.url
        if (!shouldAttachAuth(url)) {
            return originalFetch(input, init)
        }

        const method = String(init.method || (input instanceof Request ? input.method : 'GET')).toUpperCase()
        const isUnsafe = !['GET', 'HEAD', 'OPTIONS'].includes(method)
        if (isUnsafe && url && !url.includes('/sanctum/csrf-cookie') && !hasXsrfCookie()) {
            await originalFetch(csrfCookieUrl, { credentials: 'include' })
        }

        const headers = new Headers(init.headers || (input instanceof Request ? input.headers : undefined))
        scrubAuthorizationHeader(headers)

        const xsrfToken = getCookie('XSRF-TOKEN')
        if (xsrfToken && !headers.has('X-XSRF-TOKEN')) {
            headers.set('X-XSRF-TOKEN', decodeURIComponent(xsrfToken))
        }

        if (!headers.has('Accept')) {
            headers.set('Accept', 'application/json')
        }

        const hasBody = init.body !== undefined
        const isFormData = typeof FormData !== 'undefined' && init.body instanceof FormData
        if (hasBody && !isFormData && !headers.has('Content-Type')) {
            headers.set('Content-Type', 'application/json')
        }

        const mergedInit = {
            ...init,
            credentials: 'include',
            headers,
        }

        return originalFetch(input, mergedInit).then((response) => {
            if (response.status === 401 || response.status === 419) {
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
            return response
        })
    }
}

window.axios = axios

export default axios
