import api, { setToken, getToken, removeToken, hasToken } from './api';

class AuthService {
    constructor() {
        this.currentUser = null;
        this.initializeFromToken();
    }

    // Initialize user from stored token on app start
    initializeFromToken() {
        if (hasToken()) {
            // Don't fetch user data immediately, let components call checkAuth when needed
            // This prevents unnecessary API calls on every page load
        }
    }

    async register(userData) {
        try {
            const response = await api.post('/auth/register', userData);
            return this._handleAuthResponse(response);
        } catch (error) {
            throw error;
        }
    }

    async login(credentials) {
        try {
            const response = await api.post('/auth/login', credentials);
            return this._handleAuthResponse(response);
        } catch (error) {
            throw error;
        }
    }

    _handleAuthResponse(response) {
        if (response.data.success && response.data.data) {
            const { user, token } = response.data.data;
            
            if (user && token) {
                // Store token and user data
                setToken(token);
                this.currentUser = user;
                
                return {
                    ...response.data,
                    shouldRedirect: true,
                    redirectTo: '/'
                };
            }
        }
        
        return response.data;
    }

    async logout() {
        try {
            // Call logout endpoint if token exists
            if (hasToken()) {
                await api.post('/auth/logout');
            }
        } catch (error) {
            // Even if logout fails, clear local data
            console.warn('Logout request failed:', error);
        } finally {
            this.clearAuthData();
            if (!window.location.pathname.includes('/auth/')) {
                window.location.href = '/auth/login';
            }
        }
    }

    async me() {
        try {
            if (!hasToken()) {
                throw new Error('No token available');
            }

            const response = await api.get('/auth/me');
            if (response.data.success && response.data.data?.user) {
                this.currentUser = response.data.data.user;
                return response.data;
            }
            throw new Error('Invalid response');
        } catch (error) {
            this.currentUser = null;
            throw error;
        }
    }

    async checkAuth() {
        try {
            if (!hasToken()) {
                return false;
            }
            
            await this.me();
            return true;
        } catch (error) {
            // If token is invalid, clear it
            this.clearAuthData();
            return false;
        }
    }

    isAuthenticated() {
        return hasToken() && !!this.currentUser;
    }

    getCurrentUser() {
        return this.currentUser;
    }

    clearAuthData() {
        removeToken();
        this.currentUser = null;
    }

    // Get token for external use (e.g., file uploads)
    getAuthToken() {
        return getToken();
    }
}

export default new AuthService(); 