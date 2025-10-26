<?php
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/PostDao.php';
require_once __DIR__ . '/../dao/CommunityDao.php';
require_once __DIR__ . '/../dao/CommunityPostDao.php';
require_once __DIR__ . '/../dao/CommentDao.php';
require_once __DIR__ . '/../dao/CommunityCommentDao.php';
require_once __DIR__ . '/../dao/LikeDao.php';
require_once __DIR__ . '/../dao/CommunityLikeDao.php';
require_once __DIR__ . '/../dao/SubscriptionDao.php'; 

error_reporting(E_ALL);
ini_set('display_errors', 1);

$userDao     = new UserDao();
$postDao     = new PostDao();
$commDao     = new CommunityDao();
$cpDao       = new CommunityPostDao();
$commentDao  = new CommentDao();
$ccDao       = new CommunityCommentDao();
$likeDao     = new LikeDao();
$clikeDao    = new CommunityLikeDao();
$subDao      = new SubscriptionDao();

echo "<pre>";

try {
    /* ===========================
     * Setup (ACTIVE): create temp user
     * =========================== */
    $tmpUser = [
        'Username' => 'sub_tester_' . mt_rand(1000, 9999),
        'Email'    => 'sub_tester_' . mt_rand(1000, 9999) . '@example.com',
        'Password' => password_hash('secret123', PASSWORD_BCRYPT),
        'FullName' => 'Subscription Tester',
        'Role'     => 'user'
    ];
    $userId = $userDao->create_user($tmpUser);
    echo "✅ Temp user created with ID: $userId\n";

    /* ===========================
     * Setup (ACTIVE): create a Community
     * =========================== */
    $communityId = $commDao->create_community([
        'Name'        => 'Subs Community ' . mt_rand(1000, 9999),
        'Description' => 'For subscription tests',
        'OwnerID'     => $userId,
    ]);
    echo "✅ Community created with ID: $communityId\n";

    /* ===========================================================
     * SubscriptionDao tests (ACTIVE)
     * =========================================================== */

    // 1) Subscribe (idempotent)
    $inserted = $subDao->subscribe($userId, $communityId);
    echo "✅ subscribe(): inserted=" . $inserted . " (1 new, 0 already subscribed)\n";

    // 2) isSubscribed
    $isSub = $subDao->isSubscribed($userId, $communityId);
    echo "✅ isSubscribed(): " . ($isSub ? 'true' : 'false') . "\n";

    // 3) countSubscribers
    $subCount = $subDao->countSubscribers($communityId);
    echo "✅ countSubscribers($communityId): $subCount\n";

    // 4) getSubscriptionsByUser
    $communitiesForUser = $subDao->getSubscriptionsByUser($userId);
    echo "✅ getSubscriptionsByUser($userId): " . count($communitiesForUser) . " community(ies)\n";
    if (!empty($communitiesForUser)) { print_r($communitiesForUser); }

    // 5) getSubscribersByCommunity
    $usersForCommunity = $subDao->getSubscribersByCommunity($communityId);
    echo "✅ getSubscribersByCommunity($communityId): " . count($usersForCommunity) . " user(s)\n";
    if (!empty($usersForCommunity)) { print_r($usersForCommunity); }

    // 6) (Optional) with user info — only if you kept the helper in DAO
    if (method_exists($subDao, 'getSubscribersWithUserInfo')) {
        $withInfo = $subDao->getSubscribersWithUserInfo($communityId);
        echo "✅ getSubscribersWithUserInfo($communityId): " . count($withInfo) . " row(s)\n";
        if (!empty($withInfo)) { print_r($withInfo[0]); }
    }

    // 7) (Optional) Unsubscribe & verify — keep commented to preserve data
    /*
    $removed = $subDao->unsubscribe($userId, $communityId);
    echo "✅ unsubscribe(): " . ($removed ? "removed" : "no-op") . "\n";
    $isAfter = $subDao->isSubscribed($userId, $communityId);
    $countAfter = $subDao->countSubscribers($communityId);
    echo "✅ After unsubscribe — isSubscribed: " . ($isAfter ? 'true' : 'false') . ", count: $countAfter\n";
    */

    /* ===========================================================
     * Your previous tests (COMMENTED, preserved exactly)
     * =========================================================== */

    /* ---------- CommentDao tests ----------
    // Setup: create a Post (for Comment & Like tests)
    $postId = $postDao->create_post([
        'UserID'  => $userId,
        'Title'   => 'Post for comment/like testing',
        'Content' => 'Body for comment/like testing'
    ]);
    echo "✅ Post created with ID: $postId\n";

    // CommentDao tests...
    $commentId = $commentDao->create_comment([
        'PostID'  => $postId,
        'UserID'  => $userId,
        'Content' => 'First test comment'
    ]);
    echo "✅ Comment created with ID: $commentId\n";
    $c1 = $commentDao->get_comment_by_id($commentId);
    echo "✅ Comment get_comment_by_id():\n"; print_r($c1);
    $byPost = $commentDao->getByPostId($postId);
    echo "✅ Comment getByPostId($postId): " . count($byPost) . "\n";
    $byUser = $commentDao->getByUserId($userId);
    echo "✅ Comment getByUserId($userId): " . count($byUser) . "\n";
    $withUser = $commentDao->getCommentsWithUserInfo($postId);
    echo "✅ Comment getCommentsWithUserInfo($postId): " . count($withUser) . "\n";
    $rows = $commentDao->update_comment($commentId, ['Content' => 'First test comment (edited)']);
    echo "✅ Comment update_comment(): $rows\n";
    $c1e = $commentDao->get_comment_by_id($commentId);
    echo "✅ Comment after edit:\n"; print_r($c1e);
    */

    /* ---------- LikeDao tests ----------
    $added = $likeDao->add_like($userId, $postId);
    echo "✅ Like add_like(): inserted=" . $added . "\n";
    $has = $likeDao->has_user_liked_post($userId, $postId);
    echo "✅ Like has_user_liked_post(): " . ($has ? 'true' : 'false') . "\n";
    $count = $likeDao->get_like_count($postId);
    echo "✅ Like get_like_count($postId): $count\n";
    $likesByPost = $likeDao->get_by_post_id($postId);
    echo "✅ Like get_by_post_id($postId): " . count($likesByPost) . "\n";
    $likesByUser = $likeDao->get_by_user_id($userId);
    echo "✅ Like get_by_user_id($userId): " . count($likesByUser) . "\n";
    $likesWithUser = $likeDao->get_likes_with_user_info($postId);
    echo "✅ Like get_likes_with_user_info($postId): " . count($likesWithUser) . "\n";
    // $removed = $likeDao->remove_like($userId, $postId);
    // echo "✅ Like remove_like(): $removed\n";
    */

    /* ---------- Community setup + CommunityCommentDao tests ----------
    $communityPostId = $cpDao->create_post([
        'CommunityID' => $communityId,
        'UserID'      => $userId,
        'Title'       => 'Community post for comment/like testing',
        'Content'     => 'Body for community comment/like testing'
    ]);
    echo "✅ CommunityPost created with ID: $communityPostId\n";

    $ccId = $ccDao->create_comment([
        'CommunityPostID' => $communityPostId,
        'UserID'          => $userId,
        'Content'         => 'First community comment'
    ]);
    echo "✅ CommunityComment created with ID: $ccId\n";
    $cc1 = $ccDao->get_comment_by_id($ccId);
    echo "✅ CommunityComment get_comment_by_id():\n"; print_r($cc1);
    $ccByPost = $ccDao->getByCommunityPostId($communityPostId);
    echo "✅ CommunityComment getByCommunityPostId($communityPostId): " . count($ccByPost) . "\n";
    $ccByUser = $ccDao->getByUserId($userId);
    echo "✅ CommunityComment getByUserId($userId): " . count($ccByUser) . "\n";
    $ccWithUser = $ccDao->getCommentsWithUserInfo($communityPostId);
    echo "✅ CommunityComment getCommentsWithUserInfo($communityPostId): " . count($ccWithUser) . "\n";
    $rows = $ccDao->update_comment($ccId, ['Content' => 'First community comment (edited)']);
    echo "✅ CommunityComment update_comment(): $rows\n";
    $cc1e = $ccDao->get_comment_by_id($ccId);
    echo "✅ CommunityComment after edit:\n"; print_r($cc1e);
    */

    /* ---------- CommunityLikeDao tests ----------
    $cadded = $clikeDao->add_like($userId, $communityPostId);
    echo "✅ CommunityLike add_like(): inserted=" . $cadded . "\n";
    $chas = $clikeDao->has_user_liked_post($userId, $communityPostId);
    echo "✅ CommunityLike has_user_liked_post(): " . ($chas ? 'true' : 'false') . "\n";
    $ccount = $clikeDao->get_like_count($communityPostId);
    echo "✅ CommunityLike get_like_count($communityPostId): $ccount\n";
    $clikesByPost = $clikeDao->get_by_community_post_id($communityPostId);
    echo "✅ CommunityLike get_by_community_post_id($communityPostId): " . count($clikesByPost) . "\n";
    $clikesByUser = $clikeDao->get_by_user_id($userId);
    echo "✅ CommunityLike get_by_user_id($userId): " . count($clikesByUser) . "\n";
    $clikesWithUser = $clikeDao->get_likes_with_user_info($communityPostId);
    echo "✅ CommunityLike get_likes_with_user_info($communityPostId): " . count($clikesWithUser) . "\n";
    // $cremoved = $clikeDao->remove_like($userId, $communityPostId);
    // echo "✅ CommunityLike remove_like(): $cremoved\n";
    */

    /* ===========================
     * Optional cleanup (commented)
     * =========================== */
    /*
    // $subDao->unsubscribe($userId, $communityId);
    // $commDao->delete_community($communityId);
    // $userDao->delete_user($userId);
    */

} catch (Exception $e) {
    echo "❌ Tests failed: " . $e->getMessage() . "\n";
}

echo "</pre>";
