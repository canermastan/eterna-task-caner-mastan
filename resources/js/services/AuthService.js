import api from './api';

class AuthService {
    constructor() {
        this.currentUser = null;
    }

    async register(userData) {
        const response = await api.post('/auth/register', userData);
        return this._handleAuthResponse(response);
    }

    async login(credentials) {
        const response = await api.post('/auth/login', credentials);
        return this._handleAuthResponse(response);
    }

    _handleAuthResponse(response) {
        if (response.data.success && response.data.data?.user) {
            this.currentUser = response.data.data.user;
            
            if (response.data.data.authenticated) {
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
            await api.post('/auth/logout');
        } catch (error) {
            throw error;
        } finally {
            this.clearAuthData();
            if (!window.location.pathname.includes('/auth/')) {
                window.location.href = '/auth/login';
            }
        }
    }

    async me() {
        try {
            const response = await api.get('/auth/me');
            if (response.data.success && response.data.data.user) {
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
            await this.me();
            return true;
        } catch (error) {
            return false;
        }
    }

    isAuthenticated() {
        return !!this.currentUser;
    }

    getCurrentUser() {
        return this.currentUser;
    }

    clearAuthData() {
        this.currentUser = null;
    }
}

export default new AuthService(); 