var PostService = {
    
    // Load all posts or user's feed
    loadPosts: function(feedType = 'explore') {
        const currentUser = Utils.parseJwt(localStorage.getItem("user_token"));
        const userId = currentUser ? currentUser.user.UserID : null;
        
        let url = '';
        
        if (feedType === 'communities') {
            // Load posts from subscribed communities (NEW SIMPLE VERSION)
            PostService.loadCommunityFeed(userId);
            return;
        } else {
            url = `posts-with-user-info?current_user_id=${userId}`;
        }
        
        RestClient.get(url, function(posts) {
            PostService.renderPosts(posts, userId);
        }, function(error) {
            toastr.error("Failed to load posts");
            console.error(error);
        });
    },
    
    
    // NEW: Load community feed (SIMPLIFIED - one backend call!)
    loadCommunityFeed: function(userId) {
        RestClient.get(`community-posts/subscribed/${userId}`, function(posts) {
            if (!posts || posts.length === 0) {
                const container = $("#posts-container");
                container.html('<p style="text-align:center;color:#999;padding:40px;">You haven\'t subscribed to any communities yet. <a href="#explore-communities">Explore communities</a> to get started!</p>');
                return;
            }
            
            // Render community posts
            PostService.renderCommunityPosts(posts, userId);
        }, function(error) {
            toastr.error("Failed to load community feed");
            console.error(error);
        });
    },
    
    // Render community posts
    renderCommunityPosts: function(posts, currentUserId) {
        const container = $("#posts-container");
        container.empty();
        
        if (!posts || posts.length === 0) {
            container.html('<p style="text-align:center;color:#999;padding:40px;">No posts in your subscribed communities yet.</p>');
            return;
        }
        
        posts.forEach(function(post) {
            const postHtml = CommunityPostService.createCommunityPostCard(post, currentUserId);
            container.append(postHtml);
        });

        applyLikedStateToCommunityButtons();  
        
        // Attach community post handlers
        CommunityPostService.attachCommunityPostHandlers(currentUserId, null);
    },

    
    // Render posts to the dashboard
    renderPosts: function(posts, currentUserId) {
        const container = $("#posts-container");
        container.empty();
        
        if (!posts || posts.length === 0) {
            container.html('<p style="text-align:center;color:#999;padding:40px;">No posts yet. Be the first to post!</p>');
            return;
        }
        
        posts.forEach(function(post) {
            const postHtml = PostService.createPostCard(post, currentUserId);
            container.append(postHtml);
        });

        applyLikedStateToDashboardButtons(); 
        
        // Attach event handlers after rendering
        PostService.attachPostEventHandlers(currentUserId);
    },
    
    // Create HTML for a single post card
    createPostCard: function(post, currentUserId) {
        const currentUser = Utils.parseJwt(localStorage.getItem("user_token"));
        const isAdmin = currentUser && currentUser.user.Role === 'admin';

        const postTime = PostService.formatTime(post.TimeOfPost || post.CreatedAt);
        const isOwner = currentUserId == post.UserID;
        const canModify = isOwner || isAdmin; // Owner OR admin can modify
        const likeCount = post.like_count || 0;
        const commentCount = post.comment_count || 0;
        const username = post.Username || 'Unknown';
        const fullName = post.FullName || '';

        return `
        <div class="my-custom-card" data-post-id="${post.PostID}">
            <div class="post-card-link-wrapper" data-post-id="${post.PostID}" style="cursor:pointer;">
                <div class="card-body" style="padding:0;">
                    <div class="avatar-header">
                        <img src="assets/images/profile-icon.png" alt="Avatar" class="avatar-img">
                        <div>
                            <span class="card-author">@${username}</span>
                            <small class="post-time" style="color:#9b9b9b;font-size:0.9rem;margin-left:8px;">Posted at ${postTime}</small>
                        </div>
                    </div>
                    <h5 class="card-title">${PostService.escapeHtml(post.Title)}</h5>
                    <p class="card-text">${PostService.escapeHtml(post.Content)}</p>
                </div>
            </div>
            <div class="card-footer">
                <div class="card-separator"></div>
                <button class="action like-btn" data-post-id="${post.PostID}" aria-label="Like">
                    <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                        <path d="M12 21s-8.5-5.6-8.5-11.3A4.7 4.7 0 0 1 8.2 5a5.3 5.3 0 0 1 3.8 1.7A5.3 5.3 0 0 1 15.8 5a4.7 4.7 0 0 1 4.7 4.7C20.5 15.4 12 21 12 21z" fill="currentColor"/>
                    </svg>
                    <span class="like-count">${likeCount}</span>
                </button>
                <button class="action comment-btn" data-post-id="${post.PostID}" aria-label="Comment">
                    <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                        <path d="M20 2H4a2 2 0 0 0-2 2v18l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z" fill="currentColor"/>
                    </svg>
                    <span>${commentCount}</span>
                </button>
                ${canModify ? `
                <div style="margin-left:auto;display:flex;gap:8px;">
                    ${isOwner ? `<button class="btn btn-sm btn-outline-secondary edit-post-btn" data-post-id="${post.PostID}" style="font-size:0.8rem;">Edit</button>` : ''}
                    <button class="btn btn-sm btn-outline-danger delete-post-btn" data-post-id="${post.PostID}" style="font-size:0.8rem;">Delete</button>
                </div>
                ` : ''}
            </div>
        </div>
        `;
    },

    // Attach event handlers to posts
    attachPostEventHandlers: function(currentUserId) {
        // Click post to view details - UPDATED SELECTOR
        $(".post-card-link-wrapper").off('click').on('click', function(e) {
            const postId = $(this).data('post-id');
            // Store post ID in sessionStorage
            sessionStorage.setItem('currentPostId', postId);
            window.location.hash = "#post";
        });

        // Delete post
        $(".delete-post-btn").off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const postId = $(this).data('post-id');

            if (confirm("Are you sure you want to delete this post?")) {
                PostService.deletePost(postId);
            }
        });

        // Edit post
        $(".edit-post-btn").off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const postId = $(this).data('post-id');
            PostService.editPost(postId);
        });

        $(".like-btn").off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const postId = $(this).data('post-id');
            const btn = $(this);

            LikeService.toggleLike(postId, function(isLiked) {
                // Update UI
                if (isLiked) {
                    btn.addClass('liked');
                    const currentCount = parseInt(btn.find('.like-count').text());
                    btn.find('.like-count').text(currentCount + 1);
                } else {
                    btn.removeClass('liked');
                    const currentCount = parseInt(btn.find('.like-count').text());
                    btn.find('.like-count').text(Math.max(0, currentCount - 1));
                }
            });
        });
        
        // Comment button - navigate to post detail
        $(".comment-btn").off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const postId = $(this).data('post-id');
            sessionStorage.setItem('currentPostId', postId);
            window.location.hash = "#post";
        });
    },
    
    // Create new post
    createPost: function(title, content) {
        const currentUser = Utils.parseJwt(localStorage.getItem("user_token"));
        const userId = currentUser.user.UserID;
        
        const postData = {
            UserID: userId,
            Title: title,
            Content: content
        };
        
        RestClient.post("posts", JSON.stringify(postData), function(response) {
            toastr.success("Post created successfully!");
            $("#writePostModal").modal('hide');
            $("#writePostModal textarea").val('');
            $("#writePostModal input[name='title']").val('');
            
            // Reload posts
            const currentFeed = $(".feed-toggle-btn.active").attr('id') === 'myFeedBtn' ? 'myFeed' : 'explore';
            PostService.loadPosts(currentFeed);
        }, function(error) {
            toastr.error("Failed to create post");
            console.error(error);
        });
    },
    
    // Delete post
    deletePost: function(postId) {
        RestClient.delete(`posts/${postId}`, null, function(response) {
            toastr.success("Post deleted successfully!");
            $(`.my-custom-card[data-post-id="${postId}"]`).fadeOut(300, function() {
                $(this).remove();
            });
        }, function(error) {
            toastr.error("Failed to delete post");
            console.error(error);
        });
    },
    
    // Edit post (opens modal with current data)
    editPost: function(postId) {
        // Get post data first
        RestClient.get(`posts/${postId}`, function(post) {
            // Populate edit modal
            $("#editPostModal input[name='title']").val(post.Title);
            $("#editPostModal textarea").val(post.Content);
            $("#editPostModal").data('post-id', postId);
            $("#editPostModal").modal('show');
        }, function(error) {
            toastr.error("Failed to load post data");
        });
    },
    
    // Update post
    updatePost: function(postId, title, content) {
        const postData = {
            Title: title,
            Content: content
        };
        
        RestClient.put(`posts/${postId}`, JSON.stringify(postData), function(response) {
            toastr.success("Post updated successfully!");
            $("#editPostModal").modal('hide');
            
            // Reload posts
            const currentFeed = $(".feed-toggle-btn.active").attr('id') === 'myFeedBtn' ? 'myFeed' : 'explore';
            PostService.loadPosts(currentFeed);
        }, function(error) {
            toastr.error("Failed to update post");
            console.error(error);
        });
    },
    
    // Utility: Format timestamp
    formatTime: function(timestamp) {
        if (!timestamp) return '';
        const date = new Date(timestamp);
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        return `${hours}:${minutes}`;
    },
    
    // Utility: Escape HTML to prevent XSS
    escapeHtml: function(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

function applyLikedStateToDashboardButtons() {
  const currentUser = Utils.parseJwt(localStorage.getItem("user_token"));
  if (!currentUser) return;
  const userId = currentUser.user.UserID;

  $(".action.like-btn[data-post-id]").each(function () {
    const btn = $(this);
    const postId = btn.data("post-id");

    LikeService.checkIfLiked(userId, postId, function (hasLiked) {
      btn.toggleClass("liked", !!hasLiked);
    });
  });
}
