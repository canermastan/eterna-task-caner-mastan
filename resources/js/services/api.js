import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
    timeout: 30000,
});

// Token management
const TOKEN_KEY = 'sanctum_token';

// Request interceptor to add Bearer token
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem(TOKEN_KEY);
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => Promise.reject(error)
);

// Response interceptor for token-based auth
api.interceptors.response.use(
    (response) => response,
    async (error) => {
        // Handle 401 unauthorized - token expired or invalid
        if (error.response?.status === 401) {
            // Clear invalid token
            localStorage.removeItem(TOKEN_KEY);
            
            // Redirect to login if not already on auth page
            if (!window.location.pathname.includes('/auth/')) {
                window.location.href = '/auth/login';
            }
        }

        return Promise.reject(error);
    }
);

// Token management functions
export const setToken = (token) => {
    localStorage.setItem(TOKEN_KEY, token);
};

export const getToken = () => {
    return localStorage.getItem(TOKEN_KEY);
};

export const removeToken = () => {
    localStorage.removeItem(TOKEN_KEY);
};

export const hasToken = () => {
    return !!localStorage.getItem(TOKEN_KEY);
};

export default api; 