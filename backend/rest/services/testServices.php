<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/UserService.php';
require_once __DIR__ . '/PostService.php';
require_once __DIR__ . '/CommentService.php';
require_once __DIR__ . '/LikeService.php';
require_once __DIR__ . '/CommunityService.php';
require_once __DIR__ . '/CommunityPostService.php';
require_once __DIR__ . '/CommunityCommentService.php';
require_once __DIR__ . '/CommunityLikeService.php';
require_once __DIR__ . '/SubscriptionService.php';
require_once __DIR__ . '/FollowService.php';

echo "<pre>";

try {
    echo "================ SERVICE TESTS START ================\n";

    $userService = new UserService();
    $postService = new PostService();
    $commentService = new CommentService();
    $likeService = new LikeService();
    $communityService = new CommunityService();
    $cPostService = new CommunityPostService();
    $cCommentService = new CommunityCommentService();
    $cLikeService = new CommunityLikeService();
    $subService = new SubscriptionService();
    $followService = new FollowService();

    // USERS
    $userA = [
        'Username' => 'svc_userA_' . mt_rand(1000,9999),
        'Email'    => 'svc_userA_' . mt_rand(1000,9999) . '@example.com',
        'Password' => password_hash('secret', PASSWORD_BCRYPT),
        'FullName' => 'Service User A',
        'Role'     => 'user'
    ];
    $userB = [
        'Username' => 'svc_userB_' . mt_rand(1000,9999),
        'Email'    => 'svc_userB_' . mt_rand(1000,9999) . '@example.com',
        'Password' => password_hash('secret', PASSWORD_BCRYPT),
        'FullName' => 'Service User B',
        'Role'     => 'user'
    ];
    $userAId = $userService->create_user($userA);
    $userBId = $userService->create_user($userB);
    echo "✅ Service users created: A=$userAId, B=$userBId\n";

    // COMMUNITY
    $communityId = $communityService->create_community([
        'Name' => 'Service Community ' . mt_rand(1000,9999),
        'Description' => 'Service-level test',
        'OwnerID' => $userAId
    ]);
    echo "✅ Service community created: $communityId\n";

    // POSTS
    $postId = $postService->create_post([
        'UserID' => $userAId,
        'Title' => 'Service Post Title',
        'Content' => 'Service Post content'
    ]);
    echo "✅ Service post created: $postId\n";

    $posts = $postService->get_all_posts();
    echo "✅ get_all_posts(): " . count($posts) . " found\n";

    $postsByUser = $postService->get_by_user_id($userAId);
    echo "✅ get_by_user_id($userAId): " . count($postsByUser) . " found\n";

    // COMMENTS
    $commentId = $commentService->create_comment([
        'PostID' => $postId,
        'UserID' => $userBId,
        'Content' => 'Service comment here'
    ]);
    echo "✅ Service comment created: $commentId\n";

    // LIKES
    $likeService->add_like($userBId, $postId);
    echo "✅ Service like added by UserB on Post\n";

    // FOLLOW
    $followService->follow_user($userAId, $userBId);
    echo "✅ Service UserA follows UserB\n";

    // SUBSCRIBE
    $subService->subscribe($userAId, $communityId);
    echo "✅ Service UserA subscribed to community\n";

    // COMMUNITY POST
    $cpId = $cPostService->create_post([
        'CommunityID' => $communityId,
        'UserID' => $userAId,
        'Title' => 'Service Community Post',
        'Content' => 'Service community post content'
    ]);
    echo "✅ Service community post created: $cpId\n";

    // COMMUNITY COMMENT
    $ccId = $cCommentService->create_comment([
        'CommunityPostID' => $cpId,
        'UserID' => $userBId,
        'Content' => 'Service community comment content'
    ]);
    echo "✅ Service community comment created: $ccId\n";

    // COMMUNITY LIKE
    $cLikeService->add_like($userBId, $cpId);
    echo "✅ Service community like added\n";

    echo "================ SERVICE TESTS COMPLETE ================\n";

} catch (Throwable $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "</pre>";
