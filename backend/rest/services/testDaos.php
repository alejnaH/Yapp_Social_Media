<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/PostDao.php';
require_once __DIR__ . '/../dao/CommentDao.php';
require_once __DIR__ . '/../dao/LikeDao.php';
require_once __DIR__ . '/../dao/CommunityDao.php';
require_once __DIR__ . '/../dao/CommunityPostDao.php';
require_once __DIR__ . '/../dao/CommunityCommentDao.php';
require_once __DIR__ . '/../dao/CommunityLikeDao.php';
require_once __DIR__ . '/../dao/SubscriptionDao.php';
require_once __DIR__ . '/../dao/FollowDao.php';

echo "<pre>";

try {
    echo "================ DAO TESTS START ================\n";

    // 1️⃣ USERS
    $userDao = new UserDao();
    $userA = [
        'Username' => 'dao_userA_' . mt_rand(1000,9999),
        'Email'    => 'dao_userA_' . mt_rand(1000,9999) . '@example.com',
        'Password' => password_hash('secret', PASSWORD_BCRYPT),
        'FullName' => 'User A',
        'Role'     => 'user'
    ];
    $userB = [
        'Username' => 'dao_userB_' . mt_rand(1000,9999),
        'Email'    => 'dao_userB_' . mt_rand(1000,9999) . '@example.com',
        'Password' => password_hash('secret', PASSWORD_BCRYPT),
        'FullName' => 'User B',
        'Role'     => 'user'
    ];
    $userAId = $userDao->create_user($userA);
    $userBId = $userDao->create_user($userB);
    echo "✅ Created users: A=$userAId, B=$userBId\n";

    // 2️⃣ COMMUNITY
    $commDao = new CommunityDao();
    $communityId = $commDao->create_community([
        'Name' => 'DAO Community ' . mt_rand(1000,9999),
        'Description' => 'For DAO testing',
        'OwnerID' => $userAId
    ]);
    echo "✅ Community created: $communityId\n";

    // 3️⃣ POSTS
    $postDao = new PostDao();
    $postId = $postDao->create_post([
        'UserID' => $userAId,
        'Title' => 'DAO Post Title',
        'Content' => 'DAO Post content here'
    ]);
    echo "✅ Post created: $postId\n";

    $allPosts = $postDao->get_all_posts();
    echo "✅ get_all_posts(): " . count($allPosts) . " found\n";

    $userPosts = $postDao->getByUserId($userAId);
    echo "✅ getByUserId($userAId): " . count($userPosts) . " found\n";

    $postDetails = $postDao->getPostWithDetails($postId);
    echo "✅ getPostWithDetails($postId):\n"; print_r($postDetails);

    // 4️⃣ COMMENTS
    $commentDao = new CommentDao();
    $commentId = $commentDao->create_comment([
        'PostID' => $postId,
        'UserID' => $userBId,
        'Content' => 'DAO Comment content'
    ]);
    echo "✅ Comment created: $commentId\n";

    $commentsByPost = $commentDao->getByPostId($postId);
    echo "✅ getByPostId($postId): " . count($commentsByPost) . " found\n";

    $commentsWithInfo = $commentDao->getCommentsWithUserInfo($postId);
    echo "✅ getCommentsWithUserInfo($postId): " . count($commentsWithInfo) . " found\n";

    // 5️⃣ LIKES
    $likeDao = new LikeDao();
    $likeDao->add_like($userBId, $postId);
    echo "✅ Like added by User B\n";

    $hasLiked = $likeDao->has_user_liked_post($userBId, $postId);
    echo "✅ has_user_liked_post(): " . ($hasLiked ? 'true' : 'false') . "\n";

    $likeCount = $likeDao->get_like_count($postId);
    echo "✅ get_like_count(): $likeCount\n";

    // 6️⃣ FOLLOW
    $followDao = new FollowDao();
    $followDao->follow_user($userAId, $userBId);
    echo "✅ UserA follows UserB\n";

    $isFollowing = $followDao->is_following($userAId, $userBId);
    echo "✅ is_following(): " . ($isFollowing ? 'true' : 'false') . "\n";

    // 7️⃣ SUBSCRIPTIONS
    $subDao = new SubscriptionDao();
    $subDao->subscribe($userAId, $communityId);
    echo "✅ UserA subscribed to community $communityId\n";

    $isSub = $subDao->isSubscribed($userAId, $communityId);
    echo "✅ isSubscribed(): " . ($isSub ? 'true' : 'false') . "\n";

    // 8️⃣ COMMUNITY POSTS + COMMENTS + LIKES
    $cpDao = new CommunityPostDao();
    $cpId = $cpDao->create_post([
        'CommunityID' => $communityId,
        'UserID' => $userAId,
        'Title' => 'Community Post',
        'Content' => 'Community Post content'
    ]);
    echo "✅ CommunityPost created: $cpId\n";

    $ccDao = new CommunityCommentDao();
    $ccId = $ccDao->create_comment([
        'CommunityPostID' => $cpId,
        'UserID' => $userBId,
        'Content' => 'Community comment content'
    ]);
    echo "✅ CommunityComment created: $ccId\n";

    $clDao = new CommunityLikeDao();
    $clDao->add_like($userBId, $cpId);
    echo "✅ CommunityLike added\n";

    echo "================ DAO TESTS COMPLETE ================\n";

} catch (Throwable $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "</pre>";
