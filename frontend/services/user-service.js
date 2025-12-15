var UserService = {
    
    // Initialize (called on app start)
    init: function() {
        // Any initialization logic if needed
        console.log("UserService initialized");
    },
    
    // Login
    login: function(email, password) {
        console.log("UserService.login called with:", email);
        
        const credentials = {
            email: email,
            password: password
        };
        
        console.log("Sending POST to auth/login");
        
        RestClient.post("auth/login", JSON.stringify(credentials), function(data) {
            console.log("Login response:", data);
            
            // Backend returns: { success: true, data: { token: "..." } }
            const token = data.data ? data.data.token : data.token;
            
            if (token) {
                localStorage.setItem("user_token", token);
                toastr.success("Login successful!");
                window.location.hash = "#dashboard";
            } else {
                console.error("No token in response:", data);
                toastr.error("Login failed - no token received");
            }
        }, function(error) {
            console.error("Login error:", error);
            toastr.error("Invalid email or password");
        });
    },

    
    // Logout
    logout: function() {
        localStorage.removeItem("user_token");
        toastr.success("Logged out successfully");
        window.location.hash = "#login";
    },
    
    // Get current user profile
    getCurrentUserProfile: function(callback) {
        const token = localStorage.getItem("user_token");
        if (!token) {
            window.location.hash = "#login";
            return;
        }
        
        const payload = Utils.parseJwt(token);
        const userId = payload.user.UserID;
        
        RestClient.get(`users/${userId}`, function(user) {
            if (callback) callback(user);
        }, function(error) {
            toastr.error("Failed to load profile");
            console.error(error);
        });
    },
    
    // Update user profile
    updateProfile: function(userId, fullName, bio, callback) {
        const userData = {
            FullName: fullName,
            Bio: bio
        };
        
        RestClient.put(`users/${userId}`, JSON.stringify(userData), function(response) {
            toastr.success("Profile updated successfully!");
            if (callback) callback(response);
        }, function(error) {
            toastr.error("Failed to update profile");
            console.error(error);
        });
    },
    
    // Get user statistics
    getUserStats: function(userId, callback) {
        // Get post count
        RestClient.get(`posts/user/${userId}`, function(posts) {
            const postCount = posts ? posts.length : 0;
            
            // Get comment count
            RestClient.get(`comments/user/${userId}`, function(comments) {
                const commentCount = comments ? comments.length : 0;
                
                // Get like count (likes given by user)
                RestClient.get(`likes/user/${userId}`, function(likes) {
                    const likeCount = likes ? likes.length : 0;
                    
                    if (callback) {
                        callback({
                            posts: postCount,
                            comments: commentCount,
                            likes: likeCount
                        });
                    }
                }, function(error) {
                    console.error("Failed to load likes:", error);
                    if (callback) callback({ posts: postCount, comments: commentCount, likes: 0 });
                });
            }, function(error) {
                console.error("Failed to load comments:", error);
                if (callback) callback({ posts: postCount, comments: 0, likes: 0 });
            });
        }, function(error) {
            console.error("Failed to load posts:", error);
            if (callback) callback({ posts: 0, comments: 0, likes: 0 });
        });
    }
};