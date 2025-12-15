function requireAuth() {
    const token = localStorage.getItem("user_token");
    if (!token) {
        return false;
    }

    const payload = Utils.parseJwt(token);
    if (!payload || payload.exp * 1000 < Date.now()) {
        localStorage.removeItem("user_token");
        return false;
    }
    return true;
}

var app = $.spapp({
    defaultView: "login",
    templateDir: "views/",
    pageNotFound: "login"
});

// Public routes
app.route({
    view: "login",
    load: "login.html",
    onReady: function() {
        if (requireAuth()) {
            window.location.hash = "#dashboard";
        }
    }
});

app.route({
    view: "signup",
    load: "signup.html",
    onReady: function() {
        if (requireAuth()) {
            window.location.hash = "#dashboard";
        }
    }
});

// Protected routes - Dashboard
app.route({
    view: "dashboard",
    load: "dashboard.html",
    onCreate: function() {
        if (!requireAuth()) {
            window.location.hash = "#login";
            return false;
        }
    },
    onReady: function() {
        if (!requireAuth()) {
            window.location.hash = "#login";
            return false;
        }
    }
});

// Protected routes - Profile
app.route({
    view: "profile",
    load: "profile.html",
    onCreate: function() {
        if (!requireAuth()) {
            window.location.hash = "#login";
            return false;
        }
    },
    onReady: function() {
        if (!requireAuth()) {
            window.location.hash = "#login";
            return false;
        }
        if (typeof loadProfile === 'function') {
            loadProfile();
        }
    }
});

// Protected routes - Edit Profile
app.route({
    view: "edit-profile",
    load: "edit-profile.html",
    onCreate: function() {
        if (!requireAuth()) {
            window.location.hash = "#login";
            return false;
        }
    },
    onReady: function() {
        if (!requireAuth()) {
            window.location.hash = "#login";
            return false;
        }
        if (typeof loadEditProfile === 'function') {
            loadEditProfile();
        }
    }
});

// Protected routes - Post
app.route({
    view: "post",
    load: "post.html",
    onCreate: function() {
        if (!requireAuth()) {
            window.location.hash = "#login";
            return false;
        }
    },
    onReady: function() {
        if (!requireAuth()) {
            window.location.hash = "#login";
            return false;
        }
        // Reload post detail every time the page is accessed
        if (typeof loadPostDetail === 'function') {
            loadPostDetail();
        }
    }
});


// Protected routes - Community
app.route({
    view: "community",
    load: "community.html",
    onCreate: function() {
        if (!requireAuth()) {
            window.location.hash = "#login";
            return false;
        }
    },
    onReady: function() {
        if (!requireAuth()) {
            window.location.hash = "#login";
            return false;
        }
        if (typeof loadCommunityDetail === 'function') {
            loadCommunityDetail();
        }
    }
});

// Protected routes - Explore Communities
app.route({
    view: "explore-communities",
    load: "explore-communities.html",
    onCreate: function() {
        if (!requireAuth()) {
            window.location.hash = "#login";
            return false;
        }
    },
    onReady: function() {
        if (!requireAuth()) {
            window.location.hash = "#login";
            return false;
        }
        // Reload communities every time the page is accessed
        if (typeof loadExploreCommunities === 'function') {
            loadExploreCommunities();
        }
    }
});


// Protected routes - My Communities
app.route({
    view: "my-communities",
    load: "my-communities.html",
    onCreate: function() {
        if (!requireAuth()) {
            window.location.hash = "#login";
            return false;
        }
    },
    onReady: function() {
        if (!requireAuth()) {
            window.location.hash = "#login";
            return false;
        }
        if (typeof loadMyCommunities === 'function') {
            loadMyCommunities();
        }
    }
});

// Protected routes - Community Post
app.route({
    view: "community-post",
    load: "community-post.html",
    onCreate: function() {
        if (!requireAuth()) {
            window.location.hash = "#login";
            return false;
        }
    },
    onReady: function() {
        if (!requireAuth()) {
            window.location.hash = "#login";
            return false;
        }
        if (typeof loadCommunityPostDetail === 'function') {
            loadCommunityPostDetail();
        }
    }
});

// Admin page (admin only)
app.route({
    view: "admin",
    load: "admin.html",
    onCreate: function() {
        if (!requireAuth()) {
            window.location.hash = "#login";
            return false;
        }
        
        // Check if user is admin
        const currentUser = Utils.parseJwt(localStorage.getItem("user_token"));
        if (currentUser.user.Role !== 'admin') {
            toastr.error("Access denied. Admin only.");
            window.location.hash = "#dashboard";
            return false;
        }
    },
    onReady: function() {
        if (!requireAuth()) {
            window.location.hash = "#login";
            return false;
        }
        
        const currentUser = Utils.parseJwt(localStorage.getItem("user_token"));
        if (currentUser.user.Role !== 'admin') {
            window.location.hash = "#dashboard";
            return false;
        }
        
        if (typeof loadAdminPage === 'function') {
            loadAdminPage();
        }
    }
});


app.run();