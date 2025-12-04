<?php
require_once __DIR__ . '/vendor/autoload.php';

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

require 'rest/services/AuthService.php';
Flight::register('auth_service', "AuthService");



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
