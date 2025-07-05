export function formatCommentContent(content) {
  try {
    if (typeof content === 'string' && content.trim().startsWith('{')) {
      try {
        const parsed = JSON.parse(content);
        if (parsed.content) {
          return formatCommentContent(parsed.content);
        }
        return JSON.stringify(parsed);
      } catch {
        return content;
      }
    }

    if (typeof content === 'object' && content !== null) {
      if (content.content) {
        return formatCommentContent(content.content);
      }
      return JSON.stringify(content);
    }

    return content || '';
  } catch {
    return content || '';
  }
}

export function isVisibleComment(comment, currentUserId, isAdmin) {
  // Rejected comments are hidden from everyone
  if (comment.status === 'rejected') return false;

  // Admin sees approved + pending
  if (isAdmin) return true;

  // Normal users
  if (comment.status === 'approved') return true;
  if (comment.status === 'pending' && comment.user?.id === currentUserId) return true;

  return false;
} 