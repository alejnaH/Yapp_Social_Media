var LikeService = {
    
    // Toggle like on a post
    toggleLike: function(postId, callback) {
        const currentUser = Utils.parseJwt(localStorage.getItem("user_token"));
        const userId = currentUser.user.UserID;
        
        // First check if already liked
        RestClient.get(`likes/check/${userId}/${postId}`, function(hasLiked) {
            if (hasLiked) {
                // Unlike
                LikeService.removeLike(userId, postId, callback);
            } else {
                // Like
                LikeService.addLike(userId, postId, callback);
            }
        });
    },
    
    // Add a like
    addLike: function(userId, postId, callback) {
        const likeData = {
            user_id: userId,
            post_id: postId
        };
        
        RestClient.post("likes", JSON.stringify(likeData), function(response) {
            if (callback) callback(true);
        }, function(error) {
            toastr.error("Failed to like post");
            console.error(error);
        });
    },
    
    // Remove a like
    removeLike: function(userId, postId, callback) {
        const likeData = {
            user_id: userId,
            post_id: postId
        };
        
        RestClient.delete("likes", JSON.stringify(likeData), function(response) {
            if (callback) callback(false);
        }, function(error) {
            toastr.error("Failed to unlike post");
            console.error(error);
        });
    },
    
    // Get like count for a post
    getLikeCount: function(postId, callback) {
        RestClient.get(`likes/count/${postId}`, function(count) {
            if (callback) callback(count);
        }, function(error) {
            console.error("Failed to get like count:", error);
        });
    },
    
    // Check if user liked a post
    checkIfLiked: function(userId, postId, callback) {
        RestClient.get(`likes/check/${userId}/${postId}`, function(hasLiked) {
            if (callback) callback(hasLiked);
        }, function(error) {
            console.error("Failed to check like status:", error);
        });
    }
};