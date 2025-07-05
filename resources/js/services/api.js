import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
    withCredentials: true,
    timeout: 30000,
});

class SanctumCSRF {
    constructor() {
        this.initialized = false;
        this.lastInitTime = 0;
        this.initPromise = null;
        this.CACHE_DURATION = 30000; // 30 seconds cache
    }

    async initialize() {
        const now = Date.now();
        
        // Return if recently initialized
        if (this.initialized && (now - this.lastInitTime < this.CACHE_DURATION)) {
            return;
        }

        // Prevent concurrent initialization
        if (this.initPromise) {
            return this.initPromise;
        }

        this.initPromise = this._performInit();
        try {
            await this.initPromise;
        } finally {
            this.initPromise = null;
        }
    }

    async _performInit() {
        try {
            await axios.get('/sanctum/csrf-cookie', { 
                withCredentials: true,
                timeout: 5000,
            });
            this.initialized = true;
            this.lastInitTime = Date.now();
        } catch (error) {
            this.initialized = false;
            throw new Error('CSRF initialization failed');
        }
    }

    reset() {
        this.initialized = false;
        this.lastInitTime = 0;
        this.initPromise = null;
    }

    isValid() {
        const now = Date.now();
        return this.initialized && (now - this.lastInitTime < this.CACHE_DURATION);
    }
}

const sanctumCSRF = new SanctumCSRF();

// Request interceptor for Sanctum SPA with intelligent CSRF handling
api.interceptors.request.use(
    async (config) => {
        // Only initialize CSRF for non-GET requests that require it
        if (shouldInitializeCSRF(config)) {
            try {
                await sanctumCSRF.initialize();
            } catch (error) {
                if (import.meta.env.DEV) {
                    console.warn('Sanctum CSRF initialization failed:', error);
                }
                // Don't block request on CSRF failure - let server handle it
            }
        }
        return config;
    },
    (error) => Promise.reject(error)
);

function shouldInitializeCSRF(config) {
    if (config.method === 'get') return false;
    
    if (config.url.includes('sanctum/csrf-cookie')) return false;
    
    if (sanctumCSRF.isValid()) return false;
    
    // Skip for auth endpoints if we're on auth pages (avoid loops)
    if (config.url.includes('/auth/') && window.location.pathname.includes('/auth/')) {
        return false;
    }
    
    return true;
}

// Response interceptor for Sanctum SPA
api.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;

        // Handle CSRF token mismatch with retry
        if (error.response?.status === 419 && !originalRequest._retry) {
            originalRequest._retry = true;
            
            try {
                sanctumCSRF.reset();
                await sanctumCSRF.initialize();
                return api(originalRequest);
            } catch (refreshError) {
                if (import.meta.env.DEV) {
                    console.error('Sanctum CSRF retry failed:', refreshError);
                }
            }
        }

        if (error.response?.status === 401) {
            sanctumCSRF.reset();
        }

        return Promise.reject(error);
    }
);

export default api; 