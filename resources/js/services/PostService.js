import api from './api';

class PostService {
    async getPosts(params = {}) {
        const response = await api.get('/posts', { params });
        return response.data;
    }

    async getPost(id) {
        const response = await api.get(`/posts/${id}`);
        return response.data.data;
    }

    async getPostBySlug(slug) {
        const response = await api.get(`/posts/slug/${slug}`);
        return response.data.data; 
    }

    async getPostsByUser(userId, params = {}) {
        const response = await api.get(`/posts/user/${userId}`, { params });
        return response.data;
    }

    async createPost(postData) {
        const response = await api.post('/posts', postData, this.formDataConfig(postData));
        return response.data;
    }

    async updatePost(id, postData) {
        const response = await api.post(`/posts/${id}`, postData, this.formDataConfig(postData));
        return response.data;
    }

    async deletePost(id) {
        const response = await api.delete(`/posts/${id}`);
        return response.data;
    }

    async toggleDraftPublished(id) {
        const response = await api.patch(`/posts/${id}/toggle-draft-published`);
        return response.data;
    }

    async uploadCoverImage(file) {
        const formData = new FormData();
        formData.append('cover_image', file);
        
        const response = await api.post('/posts/upload-cover', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        
        return response.data;
    }

    async getAllPosts() {
        const response = await api.get('/posts/all');
        return response.data;
    }

    async getMyPosts(params = {}) {
        const response = await api.get('/posts/my-posts', { params });
        return response.data;
    }

    async formDataConfig(postData) {
        const isFormData = postData instanceof FormData;
        return isFormData ? {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        } : {};
    }
}

export default new PostService(); 