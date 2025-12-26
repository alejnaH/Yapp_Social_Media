<?php

// --- CORS HEADERS (MUST BE AT THE VERY TOP) ---
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// You can restrict this to your Vercel domain for security
header('Access-Control-Allow-Origin: *');
// Or, more securely:
// if (in_array($origin, ['https://yapp-social-media-app-86mu.vercel.app', 'http://localhost:8000'])) {
//     header("Access-Control-Allow-Origin: $origin");
// }

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authentication');
header('Access-Control-Max-Age: 86400'); // Cache preflight for 1 day
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
// --- END CORS HEADERS ---

require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// MIDDLEWARE & ROLES
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/data/roles.php';

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// SERVICES
require_once __DIR__ . '/rest/services/UserService.php';
Flight::register('userService', 'UserService');

require_once __DIR__ . '/rest/services/CommentService.php';
Flight::register('commentService', 'CommentService');

require_once __DIR__ . '/rest/services/CommunityCommentService.php';
Flight::register('communityCommentService', 'CommunityCommentService');

require_once __DIR__ . '/rest/services/PostService.php';
Flight::register('postService', 'PostService');

require_once __DIR__ . '/rest/services/CommunityLikeService.php';
Flight::register('communityLikeService', 'CommunityLikeService');

require_once __DIR__ . '/rest/services/CommunityPostService.php';
Flight::register('communityPostService', 'CommunityPostService');

require_once __DIR__ . '/rest/services/CommunityService.php';
Flight::register('communityService', 'CommunityService');

require_once __DIR__ . '/rest/services/FollowService.php';
Flight::register('followService', 'FollowService');

require_once __DIR__ . '/rest/services/LikeService.php';
Flight::register('likeService', 'LikeService');

require_once __DIR__ . '/rest/services/SubscriptionService.php';
Flight::register('subscriptionService', 'SubscriptionService');

require_once __DIR__ . '/rest/services/AuthService.php';
Flight::register('auth_service', "AuthService");

Flight::register('auth_middleware', 'AuthMiddleware');

Flight::before('start', function(&$params, &$output) {
    $url = Flight::request()->url;
    
    // Skip auth for public routes
    if (
        strpos($url, '/auth/login') === 0 ||
        strpos($url, '/auth/register') === 0
    ) {
        return TRUE;
    }
    
    // For all other routes, verify token
    // I had to do this since the jwt token verification was not working properly and I was getting errors on protected routes
    try {
        $token = Flight::request()->getHeader("Authentication");
        
        if (!$token) {
            Flight::halt(401, "Missing Authentication header");
        }
        Flight::auth_middleware()->verifyToken($token);
        
    } catch (Exception $e) {
        Flight::halt(401, "Invalid token: " . $e->getMessage());
    }
});


// ROUTES
require_once __DIR__ . '/rest/routes/UserRoutes.php';
require_once __DIR__ . '/rest/routes/CommentRoutes.php';
require_once __DIR__ . '/rest/routes/CommunityCommentRoutes.php';
require_once __DIR__ . '/rest/routes/PostRoutes.php';
require_once __DIR__ . '/rest/routes/CommunityLikeRoutes.php';
require_once __DIR__ . '/rest/routes/CommunityPostRoutes.php';
require_once __DIR__ . '/rest/routes/CommunityRoutes.php';
require_once __DIR__ . '/rest/routes/FollowRoutes.php';
require_once __DIR__ . '/rest/routes/LikeRoutes.php';
require_once __DIR__ . '/rest/routes/SubscriptionRoutes.php';
require_once __DIR__ . '/rest/routes/AuthRoutes.php';


// Health-check
Flight::route('GET /', function () {
    echo 'Backend alive';
});

Flight::start();