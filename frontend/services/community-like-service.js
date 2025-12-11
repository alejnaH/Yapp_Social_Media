var CommunityLikeService = {
    
    // Toggle like on a community post
    toggleLike: function(communityPostId, callback) {
        const currentUser = Utils.parseJwt(localStorage.getItem("user_token"));
        const userId = currentUser.user.UserID;
        
        // Check if already liked
        RestClient.get(`community-likes/check/${userId}/${communityPostId}`, function(hasLiked) {
            if (hasLiked) {
                CommunityLikeService.removeLike(userId, communityPostId, callback);
            } else {
                CommunityLikeService.addLike(userId, communityPostId, callback);
            }
        });
    },
    
    // Add a like
    addLike: function(userId, communityPostId, callback) {
        const likeData = {
            user_id: userId,
            community_post_id: communityPostId
        };
        
        RestClient.post("community-likes", JSON.stringify(likeData), function(response) {
            if (callback) callback(true);
        }, function(error) {
            toastr.error("Failed to like post");
            console.error(error);
        });
    },
    
    // Remove a like
    removeLike: function(userId, communityPostId, callback) {
        const likeData = {
            user_id: userId,
            community_post_id: communityPostId
        };
        
        RestClient.delete("community-likes", JSON.stringify(likeData), function(response) {
            if (callback) callback(false);
        }, function(error) {
            toastr.error("Failed to unlike post");
            console.error(error);
        });
    },
    
    // Check if user liked post
    checkIfLiked: function(userId, communityPostId, callback) {
        RestClient.get(`community-likes/check/${userId}/${communityPostId}`, function(hasLiked) {
            if (callback) callback(hasLiked);
        });
    }
};