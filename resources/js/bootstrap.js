import axios from 'axios'

const token = localStorage.getItem('auth_token')

if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL || '/api/v1'

window.axios = axios

export default axios
