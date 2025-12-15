var CommunityCommentService = {
    
    // Load comments for a community post
    loadComments: function(communityPostId, callback) {
        RestClient.get(`community-comments/post/${communityPostId}/with-user`, function(comments) {
            if (callback) callback(comments);
        }, function(error) {
            toastr.error("Failed to load comments");
            console.error(error);
        });
    },
    
    // Create comment
    createComment: function(communityPostId, content, callback) {
        const currentUser = Utils.parseJwt(localStorage.getItem("user_token"));
        const userId = currentUser.user.UserID;
        
        const commentData = {
            CommunityPostID: communityPostId,
            UserID: userId,
            Content: content
        };
        
        RestClient.post("community-comments", JSON.stringify(commentData), function(response) {
            toastr.success("Comment posted!");
            if (callback) callback(response);
        }, function(error) {
            toastr.error("Failed to post comment");
            console.error(error);
        });
    },
    
    // Update comment
    updateComment: function(commentId, content, callback) {
        const commentData = {
            Content: content
        };
        
        RestClient.put(`community-comments/${commentId}`, JSON.stringify(commentData), function(response) {
            toastr.success("Comment updated!");
            if (callback) callback(response);
        }, function(error) {
            toastr.error("Failed to update comment");
            console.error(error);
        });
    },
    
    // Delete comment
    deleteComment: function(commentId, callback) {
        if (!confirm("Are you sure you want to delete this comment?")) return;
        
        RestClient.delete(`community-comments/${commentId}`, null, function(response) {
            toastr.success("Comment deleted!");
            if (callback) callback(response);
        }, function(error) {
            toastr.error("Failed to delete comment");
            console.error(error);
        });
    },
    
    // Render comments (same as regular comments but with different CSS classes)
    renderComments: function(comments, currentUserId) {
        const container = $(".communitypost-comments-list");
        container.empty();
        
        if (!comments || comments.length === 0) {
            container.html('<p style="text-align:center;color:#999;padding:20px;">No comments yet. Be the first to comment!</p>');
            return;
        }
        
        comments.forEach(function(comment) {
            const commentHtml = CommunityCommentService.createCommentHtml(comment, currentUserId);
            container.append(commentHtml);
        });
        
        CommunityCommentService.attachCommentHandlers(currentUserId);
    },
    
    // Create comment HTML
    createCommentHtml: function(comment, currentUserId) {
        const isOwner = currentUserId == comment.UserID;
        const commentTime = PostService.formatTime(comment.TimeOfComment || comment.CreatedAt);
        const username = comment.Username || 'Unknown';
        
        return `
        <div class="communitypost-comment" data-comment-id="${comment.CommunityCommentID}">
            <img src="assets/images/profile-icon.png" alt="Avatar" class="communitypost-comment-avatar">
            <div class="communitypost-comment-content">
                <div class="communitypost-comment-header">
                    <span class="communitypost-comment-username">@${username}</span>
                    ${isOwner ? `
                    <div style="margin-left:auto;">
                        <button class="btn btn-sm btn-link edit-community-comment-btn" data-comment-id="${comment.CommunityCommentID}" style="font-size:0.75rem;padding:0 5px;">Edit</button>
                        <button class="btn btn-sm btn-link text-danger delete-community-comment-btn" data-comment-id="${comment.CommunityCommentID}" style="font-size:0.75rem;padding:0 5px;">Delete</button>
                    </div>
                    ` : ''}
                </div>
                <div class="communitypost-comment-body" data-comment-id="${comment.CommunityCommentID}">${PostService.escapeHtml(comment.Content)}</div>
            </div>
        </div>
        `;
    },
    
    // Attach event handlers
    attachCommentHandlers: function(currentUserId) {
        // Delete comment
        $(".delete-community-comment-btn").off('click').on('click', function(e) {
            e.preventDefault();
            const commentId = $(this).data('comment-id');
            CommunityCommentService.deleteComment(commentId, function() {
                $(`.communitypost-comment[data-comment-id="${commentId}"]`).fadeOut(300, function() {
                    $(this).remove();
                });
            });
        });
        
        // Edit comment
        $(".edit-community-comment-btn").off('click').on('click', function(e) {
            e.preventDefault();
            const commentId = $(this).data('comment-id');
            const commentBody = $(`.communitypost-comment-body[data-comment-id="${commentId}"]`);
            const currentText = commentBody.text();
            
            commentBody.html(`
                <textarea class="form-control form-control-sm" style="margin-bottom:5px;">${currentText}</textarea>
                <button class="btn btn-sm btn-primary save-community-comment-btn" data-comment-id="${commentId}">Save</button>
                <button class="btn btn-sm btn-secondary cancel-community-comment-btn">Cancel</button>
            `);
            
            $(".save-community-comment-btn").on('click', function() {
                const newContent = $(this).siblings('textarea').val().trim();
                if (newContent) {
                    CommunityCommentService.updateComment(commentId, newContent, function() {
                        commentBody.text(newContent);
                    });
                }
            });
            
            $(".cancel-community-comment-btn").on('click', function() {
                commentBody.text(currentText);
            });
        });
    }
};