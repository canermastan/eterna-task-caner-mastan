import './bootstrap';
import { createApp, h } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import { VueQueryPlugin } from '@tanstack/vue-query';
import { RouterView } from 'vue-router';
import VueTheMask from 'vue-the-mask';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import AuthService from '@/services/AuthService';

import Home from '@/pages/Home.vue';
import Login from '@/pages/auth/Login.vue';
import Register from '@/pages/auth/Register.vue';
import PostCreate from '@/pages/posts/Create.vue';
import PostShow from '@/pages/posts/Show.vue';
import MyPosts from '@/pages/posts/MyPosts.vue';
import Categories from '@/pages/categories/Index.vue';
import AdminPosts from '@/pages/admin/Posts.vue';
import AdminComments from '@/pages/admin/Comments.vue';

import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';

const routes = [
    {
        path: '/',
        component: AppLayout,
        children: [
            { path: '', name: 'home', component: Home },
            { path: '/posts/create', name: 'posts.create', component: PostCreate },
            { path: '/posts/my-posts', name: 'posts.my', component: MyPosts },
            { path: '/posts/:id', name: 'posts.show', component: PostShow },
            { path: '/categories', name: 'categories.index', component: Categories },
            { path: '/admin/posts', name: 'admin.posts', component: AdminPosts },
            { path: '/admin/comments', name: 'admin.comments', component: AdminComments },
        ]
    },
    {
        path: '/auth',
        component: AuthLayout,
        children: [
            { path: '', redirect: '/auth/login' },
            { path: 'login', name: 'auth.login', component: Login },
            { path: 'register', name: 'auth.register', component: Register },
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation guard
router.beforeEach(async (to, from, next) => {
    if (to.path.startsWith('/admin')) {
        try {
            await AuthService.checkAuth();
            const currentUser = AuthService.getCurrentUser();
            
            if (!currentUser || currentUser.role !== 'admin') {
                next('/auth/login');
                return;
            }
        } catch (error) {
            next('/auth/login');
            return;
        }
    }
    
    next();
});

const App = {
    render() {
        return h(RouterView);
    }
};

const app = createApp(App);

app.use(router);
app.use(VueQueryPlugin);
app.use(VueTheMask); // For phone number input mask

app.component('v-select', VueSelect); // For category select input

const initializeApp = async () => {
    try {
        await AuthService.checkAuth();
    } catch (error) {
        console.log('Error details:', error.message);
    } finally {
        app.mount('#app');
    }
};

initializeApp();