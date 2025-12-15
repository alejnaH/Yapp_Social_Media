var CommunityService = {
    
    // Load all communities
    loadAllCommunities: function(callback) {
        RestClient.get('communities', function(communities) {
            if (callback) callback(communities);
        }, function(error) {
            toastr.error("Failed to load communities");
            console.error(error);
        });
    },
    
    // Load single community
    loadCommunity: function(communityId, callback) {
        RestClient.get(`communities/${communityId}`, function(community) {
            if (callback) callback(community);
        }, function(error) {
            toastr.error("Failed to load community");
            console.error(error);
        });
    },
    
    // Load communities owned by user
    loadUserCommunities: function(userId, callback) {
        RestClient.get(`communities/owner/${userId}`, function(communities) {
            if (callback) callback(communities);
        }, function(error) {
            toastr.error("Failed to load your communities");
            console.error(error);
        });
    },
    
    // Create community
    createCommunity: function(name, description, callback) {
        const currentUser = Utils.parseJwt(localStorage.getItem("user_token"));
        const userId = currentUser.user.UserID;
        
        const communityData = {
            Name: name,
            Description: description,
            OwnerID: userId
        };
        
        RestClient.post("communities", JSON.stringify(communityData), function(response) {
            toastr.success("Community created successfully!");
            if (callback) callback(response);
        }, function(error) {
            toastr.error("Failed to create community");
            console.error(error);
        });
    },
    
    // Update community
    updateCommunity: function(communityId, name, description, callback) {
        const communityData = {
            Name: name,
            Description: description
        };
        
        RestClient.put(`communities/${communityId}`, JSON.stringify(communityData), function(response) {
            toastr.success("Community updated!");
            if (callback) callback(response);
        }, function(error) {
            toastr.error("Failed to update community");
            console.error(error);
        });
    },
    
    // Delete community
    deleteCommunity: function(communityId, callback) {
        if (!confirm("Are you sure you want to delete this community?")) return;
        
        RestClient.delete(`communities/${communityId}`, null, function(response) {
            toastr.success("Community deleted!");
            if (callback) callback(response);
        }, function(error) {
            toastr.error("Failed to delete community");
            console.error(error);
        });
    },
    
    // Subscribe to community
    subscribe: function(communityId, callback) {
        const currentUser = Utils.parseJwt(localStorage.getItem("user_token"));
        const userId = currentUser.user.UserID;
        
        const subData = {
            user_id: userId,
            community_id: communityId
        };
        
        RestClient.post("subscriptions", JSON.stringify(subData), function(response) {
            toastr.success("Subscribed to community!");
            if (callback) callback(true);
        }, function(error) {
            toastr.error("Failed to subscribe");
            console.error(error);
        });
    },
    
    // Unsubscribe from community
    unsubscribe: function(communityId, callback) {
        const currentUser = Utils.parseJwt(localStorage.getItem("user_token"));
        const userId = currentUser.user.UserID;
        
        const subData = {
            user_id: userId,
            community_id: communityId
        };
        
        RestClient.delete("subscriptions", JSON.stringify(subData), function(response) {
            toastr.success("Unsubscribed from community!");
            if (callback) callback(false);
        }, function(error) {
            toastr.error("Failed to unsubscribe");
            console.error(error);
        });
    },
    
    // Check if subscribed
    checkSubscription: function(communityId, callback) {
        const currentUser = Utils.parseJwt(localStorage.getItem("user_token"));
        const userId = currentUser.user.UserID;
        
        RestClient.get(`subscriptions/check/${userId}/${communityId}`, function(isSubscribed) {
            if (callback) callback(isSubscribed);
        }, function(error) {
            console.error("Failed to check subscription:", error);
        });
    },
    
    // Get subscriber count
    getSubscriberCount: function(communityId, callback) {
        RestClient.get(`subscriptions/community/${communityId}/count`, function(count) {
            if (callback) callback(count);
        }, function(error) {
            console.error("Failed to get subscriber count:", error);
        });
    },
    
    // Render communities to explore page
    renderCommunities: function(communities, currentUserId) {
        const container = $(".explore-main");
        
        // Keep the topbar
        const topbar = container.find('.explore-topbar').clone();
        container.empty();
        container.append(topbar);
        
        if (!communities || communities.length === 0) {
            container.append('<p style="text-align:center;color:#999;padding:40px;">No communities yet. Be the first to create one!</p>');
            return;
        }
        
        communities.forEach(function(community) {
            const cardHtml = CommunityService.createCommunityCard(community);
            container.append(cardHtml);
        });
        
        // Attach event handlers
        CommunityService.attachCommunityHandlers();
    },
    
    // Create HTML for a single community card
    createCommunityCard: function(community) {
        return `
        <section class="explore-community-card tall-card" data-community-id="${community.CommunityID}">
            <div class="card-link-wrapper" data-community-id="${community.CommunityID}" style="cursor:pointer;">
                <div class="explore-community-body">
                    <div class="explore-community-header">
                        <h2 class="explore-community-title">${PostService.escapeHtml(community.Name)}</h2>
                        <p class="explore-community-desc">${PostService.escapeHtml(community.Description || 'No description')}</p>
                    </div>
                    <div class="explore-community-footer">
                        <div class="explore-members-box">Members: <span class="explore-member-count" data-community-id="${community.CommunityID}">Loading...</span></div>
                        <button class="explore-subscribe-btn" data-community-id="${community.CommunityID}" data-subscribed="false" type="button" onclick="event.preventDefault(); event.stopPropagation();">Subscribe</button>
                    </div>
                </div>
            </div>
        </section>
        `;
    },
    
    // Attach event handlers to community cards
    attachCommunityHandlers: function() {
        const currentUser = Utils.parseJwt(localStorage.getItem("user_token"));
        const userId = currentUser.user.UserID;
        
        // Click community to view details
        $(".card-link-wrapper").off('click').on('click', function(e) {
            const communityId = $(this).data('community-id');
            sessionStorage.setItem('currentCommunityId', communityId);
            window.location.hash = "#community";
        });
        
        // Load member counts for each community
        $(".explore-member-count").each(function() {
            const communityId = $(this).data('community-id');
            const element = $(this);
            CommunityService.getSubscriberCount(communityId, function(count) {
                element.text(count || 0);
            });
        });
        
        // Check subscription status for each community
        $(".explore-subscribe-btn").each(function() {
            const communityId = $(this).data('community-id');
            const btn = $(this);
            CommunityService.checkSubscription(communityId, function(isSubscribed) {
                if (isSubscribed) {
                    btn.text('Unsubscribe').data('subscribed', 'true');
                } else {
                    btn.text('Subscribe').data('subscribed', 'false');
                }
            });
        });
        
        // Subscribe/Unsubscribe button
        $(".explore-subscribe-btn").off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const communityId = $(this).data('community-id');
            const btn = $(this);
            const isSubscribed = btn.data('subscribed') === 'true';
            
            if (isSubscribed) {
                CommunityService.unsubscribe(communityId, function() {
                    btn.text('Subscribe').data('subscribed', 'false');
                    // Update member count
                    const countElement = $(`.explore-member-count[data-community-id="${communityId}"]`);
                    const currentCount = parseInt(countElement.text()) || 0;
                    countElement.text(Math.max(0, currentCount - 1));
                });
            } else {
                CommunityService.subscribe(communityId, function() {
                    btn.text('Unsubscribe').data('subscribed', 'true');
                    // Update member count
                    const countElement = $(`.explore-member-count[data-community-id="${communityId}"]`);
                    const currentCount = parseInt(countElement.text()) || 0;
                    countElement.text(currentCount + 1);
                });
            }
        });
        
        // Search functionality
        $("#explore-search").off('input').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            $(".explore-community-card").each(function() {
                const title = $(this).find('.explore-community-title').text().toLowerCase();
                const desc = $(this).find('.explore-community-desc').text().toLowerCase();
                if (title.includes(searchTerm) || desc.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    }
};