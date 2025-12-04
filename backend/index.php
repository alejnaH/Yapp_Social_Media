<?php
require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


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

// This wildcard route intercepts all requests and applies authentication checks before proceeding.
Flight::route('/*', function() {
   if(  
       strpos(Flight::request()->url, '/auth/login') === 0 ||
       strpos(Flight::request()->url, '/auth/register') === 0
   ) {
       return TRUE;
   } else {
       try {
           $token = Flight::request()->getHeader("Authentication");
           if(!$token)
               Flight::halt(401, "Missing authentication header");


           $decoded_token = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));


           Flight::set('user', $decoded_token->user);
           Flight::set('jwt_token', $token);
           return TRUE;
       } catch (\Exception $e) {
           Flight::halt(401, $e->getMessage());
       }
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
