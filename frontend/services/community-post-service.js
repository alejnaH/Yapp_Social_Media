var CommunityPostService = {
    
    // Load posts for a specific community
    loadCommunityPosts: function(communityId, callback) {
        RestClient.get(`community-posts/community/${communityId}`, function(posts) {
            if (callback) callback(posts);
        }, function(error) {
            toastr.error("Failed to load community posts");
            console.error(error);
        });
    },
    
    // Load single community post
    loadCommunityPost: function(postId, callback) {
        RestClient.get(`community-posts/${postId}`, function(post) {
            if (callback) callback(post);
        }, function(error) {
            toastr.error("Failed to load post");
            console.error(error);
        });
    },
    
    // Create community post
    createCommunityPost: function(communityId, title, content, callback) {
        const currentUser = Utils.parseJwt(localStorage.getItem("user_token"));
        const userId = currentUser.user.UserID;
        
        const postData = {
            CommunityID: communityId,
            UserID: userId,
            Title: title,
            Content: content
        };
        
        RestClient.post("community-posts", JSON.stringify(postData), function(response) {
            toastr.success("Post created successfully!");
            if (callback) callback(response);
        }, function(error) {
            toastr.error("Failed to create post");
            console.error(error);
        });
    },
    
    // Update community post
    updateCommunityPost: function(postId, title, content, callback) {
        const postData = {
            Title: title,
            Content: content
        };
        
        RestClient.put(`community-posts/${postId}`, JSON.stringify(postData), function(response) {
            toastr.success("Post updated successfully!");
            if (callback) callback(response);
        }, function(error) {
            toastr.error("Failed to update post");
            console.error(error);
        });
    },
    
    // Delete community post
    deleteCommunityPost: function(postId, callback) {
        if (!confirm("Are you sure you want to delete this post?")) return;
        
        RestClient.delete(`community-posts/${postId}`, null, function(response) {
            toastr.success("Post deleted successfully!");
            if (callback) callback(response);
        }, function(error) {
            toastr.error("Failed to delete post");
            console.error(error);
        });
    },
    
    // Render community posts
    renderCommunityPosts: function(posts, currentUserId, communityId) {
        const container = $(".community-scope");
        container.empty();
        
        if (!posts || posts.length === 0) {
            container.html('<p style="text-align:center;color:#999;padding:40px;">No posts yet. Be the first to post!</p>');
            return;
        }
        
        posts.forEach(function(post) {
            const postHtml = CommunityPostService.createCommunityPostCard(post, currentUserId);
            container.append(postHtml);
        });

        applyLikedStateToCommunityButtons();

        // Attach event handlers
        CommunityPostService.attachCommunityPostHandlers(currentUserId, communityId);
    },
    
    // Create HTML for community post card
    createCommunityPostCard: function(post, currentUserId) {
        const currentUser = Utils.parseJwt(localStorage.getItem("user_token"));
        const isAdmin = currentUser && currentUser.user.Role === 'admin';
        
        const postTime = PostService.formatTime(post.TimeOfPost || post.CreatedAt);
        const isOwner = currentUserId == post.UserID;
        const canModify = isOwner || isAdmin; // Owner OR admin
        const username = post.Username || 'Unknown';
        const communityName = post.CommunityName || '';
    
        return `
        <div class="my-custom-card" data-community-post-id="${post.CommunityPostID}">
            <div class="community-post-card-link-wrapper" data-community-post-id="${post.CommunityPostID}" style="cursor:pointer;">
                <div class="card-body" style="padding:0;">
                    <div class="avatar-header">
                        <img src="assets/images/profile-icon.png" alt="Avatar" class="avatar-img">
                        <div>
                            <span class="card-author">@${username}</span>
                            ${communityName ? `<span style="margin-left:8px;background:#dc3545;color:white;padding:2px 8px;border-radius:12px;font-size:0.75rem;font-weight:600;">c/${PostService.escapeHtml(communityName)}</span>` : ''}
                            <small class="post-time" style="color:#9b9b9b;font-size:0.9rem;margin-left:8px;">Posted at ${postTime}</small>
                        </div>
                    </div>
                    <h5 class="card-title">${PostService.escapeHtml(post.Title)}</h5>
                    <p class="card-text">${PostService.escapeHtml(post.Content)}</p>
                </div>
            </div>
            <div class="card-footer">
                <div class="card-separator"></div>
                <button class="action community-like-btn" data-community-post-id="${post.CommunityPostID}" aria-label="Like">
                    <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                        <path d="M12 21s-8.5-5.6-8.5-11.3A4.7 4.7 0 0 1 8.2 5a5.3 5.3 0 0 1 3.8 1.7A5.3 5.3 0 0 1 15.8 5a4.7 4.7 0 0 1 4.7 4.7C20.5 15.4 12 21 12 21z" fill="currentColor"/>
                    </svg>
                    <span class="community-like-count">0</span>
                </button>
                <button class="action community-comment-btn" data-community-post-id="${post.CommunityPostID}" aria-label="Comment">
                    <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                        <path d="M20 2H4a2 2 0 0 0-2 2v18l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z" fill="currentColor"/>
                    </svg>
                    <span class="community-comment-count">0</span>
                </button>
                ${canModify ? `
                <div style="margin-left:auto;display:flex;gap:8px;">
                    ${isOwner ? `<button class="btn btn-sm btn-outline-secondary edit-community-post-btn" data-community-post-id="${post.CommunityPostID}" style="font-size:0.8rem;">Edit</button>` : ''}
                    <button class="btn btn-sm btn-outline-danger delete-community-post-btn" data-community-post-id="${post.CommunityPostID}" style="font-size:0.8rem;">Delete</button>
                </div>
                ` : ''}
            </div>
        </div>
        `;
    },

    // Attach event handlers
    attachCommunityPostHandlers: function(currentUserId, communityId) {
        // Click post to view details - UPDATED SELECTOR
        $(".community-post-card-link-wrapper").off('click').on('click', function(e) {
            const postId = $(this).data('community-post-id');
            sessionStorage.setItem('currentCommunityPostId', postId);
            window.location.hash = "#community-post";
        });

        // Delete post
        $(".delete-community-post-btn").off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const postId = $(this).data('community-post-id');

            CommunityPostService.deleteCommunityPost(postId, function() {
                $(`.my-custom-card[data-community-post-id="${postId}"]`).fadeOut(300, function() {
                    $(this).remove();
                });
            });
        });

        // Edit post
        $(".edit-community-post-btn").off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const postId = $(this).data('community-post-id');

            // Get post data
            CommunityPostService.loadCommunityPost(postId, function(post) {
                $("#editCommunityPostModal input[name='title']").val(post.Title);
                $("#editCommunityPostModal textarea").val(post.Content);
                $("#editCommunityPostModal").data('post-id', postId);
                $("#editCommunityPostModal").modal('show');
            });
        });

        // Like button
        $(".community-like-btn").off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const postId = $(this).data('community-post-id');
            const btn = $(this);

            CommunityLikeService.toggleLike(postId, function(isLiked) {
                if (isLiked) {
                    btn.addClass('liked');
                    const currentCount = parseInt(btn.find('.community-like-count').text());
                    btn.find('.community-like-count').text(currentCount + 1);
                } else {
                    btn.removeClass('liked');
                    const currentCount = parseInt(btn.find('.community-like-count').text());
                    btn.find('.community-like-count').text(Math.max(0, currentCount - 1));
                }
            });
        });

        // Comment button
        $(".community-comment-btn").off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const postId = $(this).data('community-post-id');
            sessionStorage.setItem('currentCommunityPostId', postId);
            window.location.hash = "#community-post";
        });

        // Load like/comment counts for each post
        $(".my-custom-card[data-community-post-id]").each(function() {
            const postId = $(this).data('community-post-id');
            const likeCountElement = $(this).find('.community-like-count');
            const commentCountElement = $(this).find('.community-comment-count');

            // Load like count
            RestClient.get(`community-likes/count/${postId}`, function(count) {
                likeCountElement.text(count || 0);
            });

            // Load comment count
            RestClient.get(`community-comments/post/${postId}`, function(comments) {
                commentCountElement.text(comments ? comments.length : 0);
            });
        });
    }
};

function applyLikedStateToCommunityButtons() {
  const currentUser = Utils.parseJwt(localStorage.getItem("user_token"));
  if (!currentUser) return;
  const userId = currentUser.user.UserID;

  $(".action.community-like-btn[data-community-post-id]").each(function () {
    const btn = $(this);
    const postId = btn.data("community-post-id");

    CommunityLikeService.checkIfLiked(userId, postId, function (hasLiked) {
      btn.toggleClass("liked", !!hasLiked);
    });
  });
}
