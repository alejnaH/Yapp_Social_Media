var AdminService = {
    
    // Load all users
    loadUsers: function(callback) {
        RestClient.get('users', function(users) {
            console.log("Loaded users:", users);
            if (callback) callback(users);
        }, function(error) {
            toastr.error("Failed to load users");
            console.error(error);
        });
    },
    
    // Delete user (admin only)
    deleteUser: function(userId, callback) {
        if (!confirm("Are you sure you want to delete this user? This will permanently delete all their posts, comments, likes, and communities. This action cannot be undone!")) {
            return;
        }
        
        RestClient.delete(`users/${userId}`, null, function(response) {
            toastr.success("User deleted successfully!");
            if (callback) callback(response);
        }, function(error) {
            toastr.error("Failed to delete user");
            console.error(error);
        });
    },
    
    // Render users table
    renderUsersTable: function(users) {
        const tbody = $("#yappadmin-adminTableBody");
        tbody.empty();
        
        if (!users || users.length === 0) {
            tbody.html('<tr><td colspan="4" class="text-center">No users found</td></tr>');
            return;
        }
        
        const currentUser = Utils.parseJwt(localStorage.getItem("user_token"));
        const currentUserId = currentUser.user.UserID;
        
        users.forEach(function(user) {
            const isCurrentUser = user.UserID === currentUserId;
            const isAdmin = user.Role === 'admin';
            
            const row = `
            <tr data-user-id="${user.UserID}">
                <td class="yappadmin-name">${AdminService.escapeHtml(user.FullName || 'N/A')}</td>
                <td class="yappadmin-username">@${AdminService.escapeHtml(user.Username)}</td>
                <td class="yappadmin-email">${AdminService.escapeHtml(user.Email)}</td>
                <td class="text-center">
                    ${isCurrentUser ? 
                        '<span class="badge bg-primary">You</span>' : 
                        (isAdmin ? 
                            '<span class="badge bg-warning text-dark">Admin</span>' : 
                            `<button class="btn yappadmin-btn-ban btn-sm delete-user-btn" data-user-id="${user.UserID}">Delete</button>`
                        )
                    }
                </td>
            </tr>
            `;
            tbody.append(row);
        });
        
        // Attach delete handlers
        AdminService.attachDeleteHandlers();
    },
    
    // Attach delete button handlers
    attachDeleteHandlers: function() {
        $(".delete-user-btn").off('click').on('click', function() {
            const userId = $(this).data('user-id');
            AdminService.deleteUser(userId, function() {
                // Remove row from table
                $(`tr[data-user-id="${userId}"]`).fadeOut(300, function() {
                    $(this).remove();
                });
            });
        });
    },
    
    // Search filter
    filterUsers: function(searchTerm, allUsers) {
        const filtered = allUsers.filter(function(user) {
            const name = (user.FullName || '').toLowerCase();
            const username = (user.Username || '').toLowerCase();
            const email = (user.Email || '').toLowerCase();
            const term = searchTerm.toLowerCase();
            
            return name.includes(term) || username.includes(term) || email.includes(term);
        });
        
        AdminService.renderUsersTable(filtered);
    },
    
    // Escape HTML
    escapeHtml: function(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};