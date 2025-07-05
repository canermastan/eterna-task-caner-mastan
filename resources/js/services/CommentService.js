import api from './api';

class CommentService {
    async getPostComments(postId, params = {}) {
        const response = await api.get(`/comments/post/${postId}`, { params });
        return response.data;
    }

    async createComment(commentData) {
        const response = await api.post('/comments', commentData);
        return response.data;
    }

    async updateComment(commentId, commentData) {
        const response = await api.put(`/comments/${commentId}`, commentData);
        return response.data;
    }

    async deleteComment(commentId) {
        const response = await api.delete(`/comments/${commentId}`);
        return response.data;
    }

    async approveComment(commentId) {
        const response = await api.patch(`/comments/${commentId}/approve`);
        return response.data;
    }

    async rejectComment(commentId) {
        const response = await api.patch(`/comments/${commentId}/reject`);
        return response.data;
    }

    async getAllComments(params = {}) {
        const response = await api.get('/comments', { params });
        return response.data;
    }
}

export default new CommentService(); 