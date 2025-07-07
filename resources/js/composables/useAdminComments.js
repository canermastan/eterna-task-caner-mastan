import { computed } from 'vue';
import { useQuery, useQueryClient } from '@tanstack/vue-query';
import CommentService from '@/services/CommentService';

export default function useAdminComments() {
  const queryClient = useQueryClient();

  // Fetch all comments
  const {
    data: commentsRaw,
    isLoading,
    error,
  } = useQuery({
    queryKey: ['admin-comments'],
    queryFn: () => CommentService.getAllComments(),
  });

  const comments = computed(() => commentsRaw.value?.data || []);

  const stats = computed(() => {
    const pending = comments.value.filter(c => c.status === 'pending').length;
    const approved = comments.value.filter(c => c.status === 'approved').length;
    const rejected = comments.value.filter(c => c.status === 'rejected').length;
    return {
      pending_count: pending,
      approved_count: approved,
      rejected_count: rejected,
      total_count: comments.value.length,
    };
  });

  const invalidateCaches = (comment) => {
    // Admin comments cache
    queryClient.invalidateQueries({ queryKey: ['admin-comments'] });
    
    // General comments cache
    queryClient.invalidateQueries({ queryKey: ['comments'] });
    
    // Post-specific comments cache
    if (comment?.post?.id) {
      queryClient.invalidateQueries({ queryKey: ['comments', 'post', comment.post.id] });
    }
    
    // Invalidate all comment-related queries
    queryClient.invalidateQueries({ queryKey: ['comments'] });
    queryClient.invalidateQueries({ queryKey: ['post-comments'] });
    
    // Force refetch
    queryClient.refetchQueries({ queryKey: ['admin-comments'] });
  };

  const approveComment = async (comment) => {
    await CommentService.approveComment(comment.id);
    invalidateCaches(comment);
  };

  const rejectComment = async (comment) => {
    await CommentService.rejectComment(comment.id);
    invalidateCaches(comment);
  };

  const deleteComment = async (comment) => {
    await CommentService.deleteComment(comment.id);
    invalidateCaches(comment);
  };

  return {
    comments,
    stats,
    isLoading,
    error,
    approveComment,
    rejectComment,
    deleteComment,
  };
} 