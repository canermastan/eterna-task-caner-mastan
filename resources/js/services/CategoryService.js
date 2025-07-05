import api from './api';

class CategoryService {
    async getCategories() {
        const response = await api.get('/categories');
        return response.data.data;
    }

    async createCategory(categoryData) {
        const response = await api.post('/categories', categoryData);
        return response.data;
    }

    async updateCategory(id, categoryData) {
        const response = await api.put(`/categories/${id}`, categoryData);
        return response.data;
    }

    async deleteCategory(id) {
        const response = await api.delete(`/categories/${id}`);
        return response.data;
    }
}

export default new CategoryService(); 