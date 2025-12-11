var CommentService = {
    
    // Load comments for a post
    loadComments: function(postId, callback) {
        RestClient.get(`comments/post/${postId}/with-user`, function(comments) {
            if (callback) callback(comments);
        }, function(error) {
            toastr.error("Failed to load comments");
            console.error(error);
        });
    },
    
    // Create a new comment
    createComment: function(postId, content, callback) {
        const currentUser = Utils.parseJwt(localStorage.getItem("user_token"));
        const userId = currentUser.user.UserID;
        
        const commentData = {
            PostID: postId,
            UserID: userId,
            Content: content
        };
        
        RestClient.post("comments", JSON.stringify(commentData), function(response) {
            toastr.success("Comment posted!");
            if (callback) callback(response);
        }, function(error) {
            toastr.error("Failed to post comment");
            console.error(error);
        });
    },
    
    // Update a comment
    updateComment: function(commentId, content, callback) {
        const commentData = {
            Content: content
        };
        
        RestClient.put(`comments/${commentId}`, JSON.stringify(commentData), function(response) {
            toastr.success("Comment updated!");
            if (callback) callback(response);
        }, function(error) {
            toastr.error("Failed to update comment");
            console.error(error);
        });
    },
    
    // Delete a comment
    deleteComment: function(commentId, callback) {
        if (!confirm("Are you sure you want to delete this comment?")) return;
        
        RestClient.delete(`comments/${commentId}`, null, function(response) {
            toastr.success("Comment deleted!");
            if (callback) callback(response);
        }, function(error) {
            toastr.error("Failed to delete comment");
            console.error(error);
        });
    },
    
    // Render comments to the page
    renderComments: function(comments, currentUserId) {
        const container = $(".postpage-comments-list");
        container.empty();
        
        if (!comments || comments.length === 0) {
            container.html('<p style="text-align:center;color:#999;padding:20px;">No comments yet. Be the first to comment!</p>');
            return;
        }
        
        comments.forEach(function(comment) {
            const commentHtml = CommentService.createCommentHtml(comment, currentUserId);
            container.append(commentHtml);
        });
        
        // Attach event handlers
        CommentService.attachCommentHandlers(currentUserId);
    },
    
    // Create HTML for a single comment
    createCommentHtml: function(comment, currentUserId) {
        const isOwner = currentUserId == comment.UserID;
        const commentTime = PostService.formatTime(comment.TimeOfComment || comment.CreatedAt);
        const username = comment.Username || 'Unknown';
        
        return `
        <div class="postpage-comment" data-comment-id="${comment.CommentID}">
            <img src="assets/images/profile-icon.png" alt="Avatar" class="postpage-comment-avatar">
            <div class="postpage-comment-content">
                <div class="postpage-comment-header">
                    <span class="postpage-comment-username">@${username}</span>
                    <span class="postpage-comment-time">commented at ${commentTime}</span>
                    ${isOwner ? `
                    <div style="margin-left:auto;">
                        <button class="btn btn-sm btn-link edit-comment-btn" data-comment-id="${comment.CommentID}" style="font-size:0.75rem;padding:0 5px;">Edit</button>
                        <button class="btn btn-sm btn-link text-danger delete-comment-btn" data-comment-id="${comment.CommentID}" style="font-size:0.75rem;padding:0 5px;">Delete</button>
                    </div>
                    ` : ''}
                </div>
                <div class="postpage-comment-body" data-comment-id="${comment.CommentID}">${PostService.escapeHtml(comment.Content)}</div>
            </div>
        </div>
        `;
    },
    
    // Attach event handlers to comments
    attachCommentHandlers: function(currentUserId) {
        // Delete comment
        $(".delete-comment-btn").off('click').on('click', function(e) {
            e.preventDefault();
            const commentId = $(this).data('comment-id');
            CommentService.deleteComment(commentId, function() {
                $(`.postpage-comment[data-comment-id="${commentId}"]`).fadeOut(300, function() {
                    $(this).remove();
                });
            });
        });
        
        // Edit comment (inline editing)
        $(".edit-comment-btn").off('click').on('click', function(e) {
            e.preventDefault();
            const commentId = $(this).data('comment-id');
            const commentBody = $(`.postpage-comment-body[data-comment-id="${commentId}"]`);
            const currentText = commentBody.text();
            
            // Replace text with textarea
            commentBody.html(`
                <textarea class="form-control form-control-sm" style="margin-bottom:5px;">${currentText}</textarea>
                <button class="btn btn-sm btn-primary save-comment-btn" data-comment-id="${commentId}">Save</button>
                <button class="btn btn-sm btn-secondary cancel-comment-btn">Cancel</button>
            `);
            
            // Save button
            $(".save-comment-btn").on('click', function() {
                const newContent = $(this).siblings('textarea').val().trim();
                if (newContent) {
                    CommentService.updateComment(commentId, newContent, function() {
                        commentBody.text(newContent);
                    });
                }
            });
            
            // Cancel button
            $(".cancel-comment-btn").on('click', function() {
                commentBody.text(currentText);
            });
        });
    }
};