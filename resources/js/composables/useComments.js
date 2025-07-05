import { ref, computed, watch, onUnmounted } from 'vue';
import { useQuery, useQueryClient } from '@tanstack/vue-query';
import CommentService from '@/services/CommentService';
import { isVisibleComment } from '@/utils/commentUtils';

/**
 * useComments composable
 * @param {import('vue').Ref<number|null>} postIdRef  Reactive post id
 * @param {import('vue').Ref<boolean>} isAdminRef     Reactive admin flag
 * @param {import('vue').Ref<object|null>} currentUserRef Reactive current user (may be null)
 */
export default function useComments(postIdRef, isAdminRef, currentUserRef) {
  const queryClient = useQueryClient();

  // pagination state (local)
  const currentPage = ref(1);
  const perPage = ref(15);

  // vue-query for comments list
  const queryKey = computed(() => ['comments', 'post', postIdRef.value]);

  const {
    data: commentsRaw,
    isLoading: commentsLoading,
    error: commentsError,
    refetch,
  } = useQuery({
    queryKey,
    queryFn: () => {
      if (!postIdRef.value) return Promise.resolve({ data: [], pagination: {} });

      const params = { page: currentPage.value, per_page: perPage.value };
      if (!isAdminRef.value) params.status = 'approved';
      return CommentService.getPostComments(postIdRef.value, params);
    },
    enabled: computed(() => !!postIdRef.value),
    keepPreviousData: true,
  });

  // visible comments according to role & status
  const comments = computed(() => {
    const rawData = commentsRaw.value?.data || [];

    const filterChildren = (parent) => {
      if (parent.children && parent.children.length) {
        parent.children = parent.children.filter(child =>
          isVisibleComment(child, currentUserRef.value?.id, isAdminRef.value)
        );
      }
    };

    return rawData
      .filter(c => isVisibleComment(c, currentUserRef.value?.id, isAdminRef.value))
      .map(c => {
        filterChildren(c);
        return c;
      });
  });

  const pagination = computed(() => commentsRaw.value?.pagination || {
    current_page: 1,
    last_page: 1,
    total: 0,
    per_page: perPage.value,
    has_more_pages: false,
  });

  const nextPage = () => {
    if (currentPage.value < pagination.value.last_page) currentPage.value += 1;
  };

  const prevPage = () => {
    if (currentPage.value > 1) currentPage.value -= 1;
  };

  const loadMore = () => {
    if (pagination.value.has_more_pages) nextPage();
  };

  let echoChannel = null;

  const setupRealtime = () => {
    if (!postIdRef.value || !window.Echo) return;

    cleanupRealtime();
    const channelName = `post.${postIdRef.value}.comments`;
    echoChannel = window.Echo.channel(channelName);

    echoChannel.listen('.comment.created', (event) => {
      const newComment = event.comment;
      if (!newComment) return;

      queryClient.setQueryData(queryKey.value, (old) => {
        if (!old) return old;
        
        if (newComment.parent_id) {
          // It's a reply - add to parent's children
          const parentIndex = old.data.findIndex(c => c.id === newComment.parent_id);
          if (parentIndex !== -1) {
            // Check for duplicates in children (including optimistic ones)
            const existingChild = old.data[parentIndex].children?.find(child => 
              child.id === newComment.id || (child.isOptimistic && child.content === newComment.content)
            );
            if (existingChild) {
              // If optimistic comment exists, replace it with real data
              if (existingChild.isOptimistic) {
                const updatedData = [...old.data];
                updatedData[parentIndex] = {
                  ...updatedData[parentIndex],
                  children: updatedData[parentIndex].children.map(child =>
                    child.id === existingChild.id ? { ...newComment, isOptimistic: false } : child
                  )
                };
                return { ...old, data: updatedData };
              }
              return old; // Real comment already exists
            }
            
            const updatedData = [...old.data];
            updatedData[parentIndex] = {
              ...updatedData[parentIndex],
              children: [
                ...(updatedData[parentIndex].children || []),
                newComment
              ]
            };
            
            return {
              ...old,
              data: updatedData,
              pagination: {
                ...old.pagination,
                total: old.pagination.total + 1,
              },
            };
          }
        } else {
          // It's a root comment - add to main list
          // Check for duplicates (including optimistic ones)
          const existingComment = old.data.find(c => 
            c.id === newComment.id || (c.isOptimistic && c.content === newComment.content)
          );
          if (existingComment) {
            // If optimistic comment exists, replace it with real data
            if (existingComment.isOptimistic) {
              const updatedData = old.data.map(comment =>
                comment.id === existingComment.id ? { ...newComment, isOptimistic: false } : comment
              );
              return { ...old, data: updatedData };
            }
            return old; // Real comment already exists
          }
          
          return {
            ...old,
            data: [newComment, ...old.data],
            pagination: {
              ...old.pagination,
              total: old.pagination.total + 1,
            },
          };
        }
        
        return old;
      });
    });

    // comment.updated (useful for approve/reject later)
    echoChannel.listen('.comment.updated', (event) => {
      const updated = event.comment;
      if (!updated) return;

      if (updated.status === 'rejected') {
        // remove rejected
        removeFromCache(updated.id);
        return;
      }

      queryClient.setQueryData(queryKey.value, (old) => {
        if (!old) return old;
        const mutate = (arr) => arr.map((c) => {
          if (c.id === updated.id) return { ...c, ...updated };
          if (c.children) c.children = mutate(c.children);
          return c;
        });
        return { ...old, data: mutate([...old.data]) };
      });
    });

    // comment.deleted
    echoChannel.listen('.comment.deleted', (event) => {
      if (!event.commentId) return;
      removeFromCache(event.commentId);
    });
  };

  const removeFromCache = (id) => {
    queryClient.setQueryData(queryKey.value, (old) => {
      if (!old) return old;
      const data = [];
      let total = old.pagination.total;
      old.data.forEach((c) => {
        if (c.id === id) {
          total -= 1;
          return; // skip root comment
        }
        if (c.children) {
          const before = c.children.length;
          c.children = c.children.filter(child => child.id !== id);
          total -= before - c.children.length;
        }
        data.push(c);
      });
      return { ...old, data, pagination: { ...old.pagination, total } };
    });
  };

  const cleanupRealtime = () => {
    if (echoChannel) {
      echoChannel.stopListening('.comment.created');
      echoChannel.stopListening('.comment.updated');
      echoChannel.stopListening('.comment.deleted');
      window.Echo.leave(`post.${postIdRef.value}.comments`);
      echoChannel = null;
    }
  };

  watch(postIdRef, (val) => {
    if (val) {
      currentPage.value = 1; // reset page
      setupRealtime();
    }
  }, { immediate: true });

  // refetch when page changes
  watch(currentPage, () => {
    if (postIdRef.value) refetch();
  });

  onUnmounted(() => cleanupRealtime());

  /* composable returns */
  return {
    comments,
    commentsLoading,
    commentsError,
    pagination,
    loadMore,
    nextPage,
    prevPage,
    refetch,
  };
} 