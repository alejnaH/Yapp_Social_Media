var app = $.spapp({
    defaultView: "login",
    templateDir: "views/"
});

app.run();

// ==================== LOGIN - SIGNUP JS FOR REMOVING THE NAVBAR, REDIRECTING AND VERIFICATION ====================

// API Base URL
const API_BASE_URL = 'http://localhost/Yapp/backend';

// Setup AJAX to automatically include JWT token in all requests
$.ajaxSetup({
    beforeSend: function(xhr) {
        const token = getJwtToken();
        if (token) {
            xhr.setRequestHeader('Authentication', token);
        }
    }
});

// ==================== AUTHENTICATION GUARD ====================
// Check authentication on every view change
$(window).on('hashchange', function() {
    const currentHash = window.location.hash.replace('#', '') || 'login';
    
    // Public pages that don't require authentication
    const publicPages = ['login', 'signup'];
    
    // If user is NOT logged in and trying to access protected page
    if (!isLoggedIn() && !publicPages.includes(currentHash)) {
        window.location.hash = '#login';
        return false;
    }
    
    // Update navbar visibility
    updateNavbarVisibility();
});

// Check auth on initial page load
$(document).ready(function() {
    // Hide navbar initially
    updateNavbarVisibility();
    
    // If not logged in, force to login page
    if (!isLoggedIn()) {
        window.location.hash = '#login';
    }
});

// ==================== NAVBAR VISIBILITY ====================
function updateNavbarVisibility() {
    const $navbar = $('#navbar');
    
    if (isLoggedIn()) {
        $navbar.show();
    } else {
        $navbar.hide();
    }
}

// ==================== UTILITY FUNCTIONS ====================
// Get JWT token from localStorage
function getJwtToken() {
    return localStorage.getItem('jwtToken');
}

// Get current user from localStorage
function getCurrentUser() {
    const userStr = localStorage.getItem('user');
    return userStr ? JSON.parse(userStr) : null;
}

// Check if user is logged in
function isLoggedIn() {
    return !!getJwtToken();
}

// Logout function
function logout() {
    localStorage.removeItem('jwtToken');
    localStorage.removeItem('user');
    updateNavbarVisibility(); // Hide navbar immediately
    window.location.hash = '#login';
}

// ==================== SIGNUP ====================
// ==================== SIGNUP ====================
// ==================== SIGNUP ====================
// ==================== SIGNUP ====================
// ==================== SIGNUP ====================
$(document).on('submit', '#signupForm', function(e) {
    e.preventDefault();
    
    const fullName = $('#form3Example1c').val().trim();
    const username = $('#form3Example2c').val().trim();
    const email = $('#form3Example3c').val().trim();
    const password = $('#form3Example4c').val();
    const confirmPassword = $('#form3Example4cd').val();
    
    // Clear previous alerts
    $('#signup-alerts').html('');
    
    // Validation - All fields required
    if (!fullName || !username || !email || !password || !confirmPassword) {
        showSignupAlert('All fields are required', 'danger');
        return;
    }
    
    // Name validation - max 10 characters, no @ or .com
    if (fullName.length > 10) {
        showSignupAlert('Name must be maximum 10 characters', 'danger');
        return;
    }
    
    if (fullName.includes('@') || fullName.toLowerCase().includes('.com')) {
        showSignupAlert('Name cannot contain @ or .com', 'danger');
        return;
    }
    
   // Username validation - max 6 characters, no @ or .com
if (username.length > 6) {
    showSignupAlert('Username must be maximum 6 characters', 'danger');
    return;
}

if (username.length < 1) {
    showSignupAlert('Username is required', 'danger');
    return;
}

if (username.includes('@') || username.toLowerCase().includes('.com')) {
    showSignupAlert('Username cannot contain @ or .com', 'danger');
    return;
}

    
    // Email validation - must end with @email.com or @gmail.com
    const emailRegex = /^[^\s@]+@(email\.com|gmail\.com)$/i;
    if (!emailRegex.test(email)) {
        showSignupAlert('Email must end with @email.com or @gmail.com', 'danger');
        return;
    }
    
    if (password !== confirmPassword) {
        showSignupAlert('Passwords do not match', 'danger');
        return;
    }
    
    if (password.length < 3) {
        showSignupAlert('Password must be at least 3 characters', 'danger');
        return;
    }



    
    // Show loading state
    const $button = $('#signupButton');
    $button.prop('disabled', true);
    $button.find('.signup-text').addClass('d-none');
    $button.find('.spinner-border').removeClass('d-none');
    
    // Send registration request
    $.ajax({
        url: API_BASE_URL + '/auth/register',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            name: fullName,
            email: email,
            password: password,
            role: 'user'
        }),
        success: function(response) {
            showSignupAlert('Registration successful! Redirecting to login...', 'success');
            
            // Clear form
            $('#signupForm')[0].reset();
            
            // Redirect to login after 1.5 seconds
            setTimeout(function() {
                window.location.hash = '#login';
            }, 1500);
        },
        error: function(xhr) {
            let errorMsg = 'Registration failed';
            try {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    errorMsg = xhr.responseText;
                }
            } catch(e) {
                // fallback
            }
            showSignupAlert(errorMsg, 'danger');
        },
        complete: function() {
            // Hide loading state
            $button.prop('disabled', false);
            $button.find('.signup-text').removeClass('d-none');
            $button.find('.spinner-border').addClass('d-none');
        }
    });
});

function showSignupAlert(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const html = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>`;
    $('#signup-alerts').html(html);
}

// ==================== LOGIN ====================
$(document).on('submit', '#loginForm', function(e) {
    e.preventDefault();
    
    const usernameOrEmail = $('#username').val().trim();
    const password = $('#password').val();
    
    // Clear previous alerts
    $('#login-alerts').html('');
    
    // Validation
    if (!usernameOrEmail || !password) {
        showLoginAlert('Email and password are required', 'danger');
        return;
    }
    
    // Show loading state
    const $button = $('#loginButton');
    $button.prop('disabled', true);
    $button.find('.login-text').addClass('d-none');
    $button.find('.spinner-border').removeClass('d-none');
    
    // Send login request - backend requires email
    $.ajax({
        url: API_BASE_URL + '/auth/login',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            email: usernameOrEmail,
            password: password
        }),
        success: function(response) {
            // Store JWT token and user data
            if (response.data && response.data.token) {
                localStorage.setItem('jwtToken', response.data.token);
                
                // Store user data (remove token from user object)
                const userData = JSON.parse(JSON.stringify(response.data));
                delete userData.token;
                localStorage.setItem('user', JSON.stringify(userData));
                
                showLoginAlert('Login successful! Redirecting to dashboard...', 'success');
                
                // Clear form
                $('#loginForm')[0].reset();
                
                // Update navbar visibility before redirect
                updateNavbarVisibility();
                
                // Redirect to dashboard after 1 second
                setTimeout(function() {
                    window.location.hash = '#dashboard';
                }, 1000);
            } else {
                showLoginAlert('Login response incomplete', 'danger');
            }
        },
        error: function(xhr) {
            let errorMsg = 'Login failed';
            if (xhr.status === 401) {
                errorMsg = 'Invalid email or password';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.statusText) {
                errorMsg = xhr.statusText;
            }
            showLoginAlert(errorMsg, 'danger');
        },
        complete: function() {
            // Hide loading state
            $button.prop('disabled', false);
            $button.find('.login-text').removeClass('d-none');
            $button.find('.spinner-border').addClass('d-none');
        }
    });
});

function showLoginAlert(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const html = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>`;
    $('#login-alerts').html(html);
}

// ==================== LOGOUT ====================
// Attach logout to Sign Out button
$(document).on('click', '.button-login', function(e) {
    e.preventDefault();
    logout();
});
