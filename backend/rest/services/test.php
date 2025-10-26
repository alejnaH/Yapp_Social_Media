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
require_once __DIR__ . '/../dao/FollowDao.php'; // ✅ NEW

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
$followDao   = new FollowDao(); // ✅ NEW

echo "<pre>";

try {
    /* ===========================
     * Setup (ACTIVE): create temp users
     * =========================== */
    $tmpUserA = [
        'Username' => 'follow_tester_A_' . mt_rand(1000, 9999),
        'Email'    => 'follow_tester_A_' . mt_rand(1000, 9999) . '@example.com',
        'Password' => password_hash('secret123', PASSWORD_BCRYPT),
        'FullName' => 'Follow Tester A',
        'Role'     => 'user'
    ];
    $userAId = $userDao->create_user($tmpUserA);
    echo "✅ Temp user A created with ID: $userAId\n";

    $tmpUserB = [
        'Username' => 'follow_tester_B_' . mt_rand(1000, 9999),
        'Email'    => 'follow_tester_B_' . mt_rand(1000, 9999) . '@example.com',
        'Password' => password_hash('secret123', PASSWORD_BCRYPT),
        'FullName' => 'Follow Tester B',
        'Role'     => 'user'
    ];
    $userBId = $userDao->create_user($tmpUserB);
    echo "✅ Temp user B created with ID: $userBId\n";

    /* ===========================================================
     * FollowDao tests (ACTIVE)
     *  - userA follows userB
     * =========================================================== */

    // 1) Follow (idempotent)
    $inserted = $followDao->follow_user($userAId, $userBId);
    echo "✅ follow_user(A → B): inserted=" . $inserted . " (1 new, 0 already following)\n";

    // 2) is_following
    $isAB = $followDao->is_following($userAId, $userBId);
    echo "✅ is_following(A → B): " . ($isAB ? 'true' : 'false') . "\n";

    // 3) counts
    $followersOfB  = $followDao->count_followers($userBId);   // how many follow B
    $followingOfA  = $followDao->count_following($userAId);   // how many A follows
    echo "✅ count_followers(B): $followersOfB\n";
    echo "✅ count_following(A): $followingOfA\n";

    // 4) lists (IDs)
    $followersListB = $followDao->get_followers($userBId); // list of follower IDs of B
    echo "✅ get_followers(B): " . count($followersListB) . " user(s)\n";
    if (!empty($followersListB)) { print_r($followersListB); }

    $followingListA = $followDao->get_following($userAId); // list of followed IDs by A
    echo "✅ get_following(A): " . count($followingListA) . " user(s)\n";
    if (!empty($followingListA)) { print_r($followingListA); }

    // 5) with user info (if you kept these helpers in DAO)
    if (method_exists($followDao, 'get_followers_with_user_info')) {
        $followersInfoB = $followDao->get_followers_with_user_info($userBId);
        echo "✅ get_followers_with_user_info(B): " . count($followersInfoB) . " row(s)\n";
        if (!empty($followersInfoB)) { print_r($followersInfoB[0]); }
    }

    if (method_exists($followDao, 'get_following_with_user_info')) {
        $followingInfoA = $followDao->get_following_with_user_info($userAId);
        echo "✅ get_following_with_user_info(A): " . count($followingInfoA) . " row(s)\n";
        if (!empty($followingInfoA)) { print_r($followingInfoA[0]); }
    }

    // 6) (Optional) mutual follows — keep B→A commented unless you want mutuals
    /*
    $followDao->follow_user($userBId, $userAId);
    if (method_exists($followDao, 'are_mutuals')) {
        $mutual = $followDao->are_mutuals($userAId, $userBId);
        echo "✅ are_mutuals(A,B): " . ($mutual ? 'true' : 'false') . "\n";
    }
    */

    // 7) (Optional) Unfollow & verify — keep commented to preserve data
    /*
    $removed = $followDao->unfollow_user($userAId, $userBId);
    echo "✅ unfollow_user(A → B): " . ($removed ? "removed" : "no-op") . "\n";
    $isAfter = $followDao->is_following($userAId, $userBId);
    $followersOfBAfter = $followDao->count_followers($userBId);
    $followingOfAAfter = $followDao->count_following($userAId);
    echo "✅ After unfollow — is_following(A→B): " . ($isAfter ? 'true' : 'false')
         . ", followers(B): $followersOfBAfter, following(A): $followingOfAAfter\n";
    */

    /* ===========================================================
     * Your previous tests (COMMENTED, preserved exactly)
     * =========================================================== */

    /* ---------- SubscriptionDao tests ----------
    // Setup: create a Community
    $communityId = $commDao->create_community([
        'Name'        => 'Subs Community ' . mt_rand(1000, 9999),
        'Description' => 'For subscription tests',
        'OwnerID'     => $userAId,
    ]);
    echo "✅ Community created with ID: $communityId\n";

    // Subscription tests
    $sInserted = $subDao->subscribe($userAId, $communityId);
    echo "✅ subscribe(): inserted=" . $sInserted . " (1 new, 0 already subscribed)\n";
    $isSub = $subDao->isSubscribed($userAId, $communityId);
    echo "✅ isSubscribed(): " . ($isSub ? 'true' : 'false') . "\n";
    $subCount = $subDao->countSubscribers($communityId);
    echo "✅ countSubscribers($communityId): $subCount\n";
    $communitiesForUser = $subDao->getSubscriptionsByUser($userAId);
    echo "✅ getSubscriptionsByUser($userAId): " . count($communitiesForUser) . "\n";
    $usersForCommunity = $subDao->getSubscribersByCommunity($communityId);
    echo "✅ getSubscribersByCommunity($communityId): " . count($usersForCommunity) . "\n";
    if (method_exists($subDao, 'getSubscribersWithUserInfo')) {
        $withInfo = $subDao->getSubscribersWithUserInfo($communityId);
        echo "✅ getSubscribersWithUserInfo($communityId): " . count($withInfo) . "\n";
    }
    // Optional unsubscribe...
    */

    /* ---------- CommentDao tests ----------
    // Setup: create a Post (for Comment & Like tests)
    $postId = $postDao->create_post([
        'UserID'  => $userAId,
        'Title'   => 'Post for comment/like testing',
        'Content' => 'Body for comment/like testing'
    ]);
    echo "✅ Post created with ID: $postId\n";

    // CommentDao tests...
    $commentId = $commentDao->create_comment([
        'PostID'  => $postId,
        'UserID'  => $userAId,
        'Content' => 'First test comment'
    ]);
    echo "✅ Comment created with ID: $commentId\n";
    $c1 = $commentDao->get_comment_by_id($commentId);
    echo "✅ Comment get_comment_by_id():\n"; print_r($c1);
    $byPost = $commentDao->getByPostId($postId);
    echo "✅ Comment getByPostId($postId): " . count($byPost) . "\n";
    $byUser = $commentDao->getByUserId($userAId);
    echo "✅ Comment getByUserId($userAId): " . count($byUser) . "\n";
    $withUser = $commentDao->getCommentsWithUserInfo($postId);
    echo "✅ Comment getCommentsWithUserInfo($postId): " . count($withUser) . "\n";
    $rows = $commentDao->update_comment($commentId, ['Content' => 'First test comment (edited)']);
    echo "✅ Comment update_comment(): $rows\n";
    $c1e = $commentDao->get_comment_by_id($commentId);
    echo "✅ Comment after edit:\n"; print_r($c1e);
    */

    /* ---------- LikeDao tests ----------
    $added = $likeDao->add_like($userAId, $postId);
    echo "✅ Like add_like(): inserted=" . $added . "\n";
    $has = $likeDao->has_user_liked_post($userAId, $postId);
    echo "✅ Like has_user_liked_post(): " . ($has ? 'true' : 'false') . "\n";
    $count = $likeDao->get_like_count($postId);
    echo "✅ Like get_like_count($postId): $count\n";
    $likesByPost = $likeDao->get_by_post_id($postId);
    echo "✅ Like get_by_post_id($postId): " . count($likesByPost) . "\n";
    $likesByUser = $likeDao->get_by_user_id($userAId);
    echo "✅ Like get_by_user_id($userAId): " . count($likesByUser) . "\n";
    $likesWithUser = $likeDao->get_likes_with_user_info($postId);
    echo "✅ Like get_likes_with_user_info($postId): " . count($likesWithUser) . "\n";
    // $removed = $likeDao->remove_like($userAId, $postId);
    // echo "✅ Like remove_like(): $removed\n";
    */

    /* ---------- Community setup + CommunityCommentDao tests ----------
    $communityId2 = $commDao->create_community([
        'Name'        => 'Comment Community ' . mt_rand(1000, 9999),
        'Description' => 'For community comment/like testing',
        'OwnerID'     => $userAId,
    ]);
    echo "✅ Community created with ID: $communityId2\n";

    $communityPostId = $cpDao->create_post([
        'CommunityID' => $communityId2,
        'UserID'      => $userAId,
        'Title'       => 'Community post for comment/like testing',
        'Content'     => 'Body for community comment/like testing'
    ]);
    echo "✅ CommunityPost created with ID: $communityPostId\n";

    $ccId = $ccDao->create_comment([
        'CommunityPostID' => $communityPostId,
        'UserID'          => $userAId,
        'Content'         => 'First community comment'
    ]);
    echo "✅ CommunityComment created with ID: $ccId\n";
    $cc1 = $ccDao->get_comment_by_id($ccId);
    echo "✅ CommunityComment get_comment_by_id():\n"; print_r($cc1);
    $ccByPost = $ccDao->getByCommunityPostId($communityPostId);
    echo "✅ CommunityComment getByCommunityPostId($communityPostId): " . count($ccByPost) . "\n";
    $ccByUser = $ccDao->getByUserId($userAId);
    echo "✅ CommunityComment getByUserId($userAId): " . count($ccByUser) . "\n";
    $ccWithUser = $ccDao->getCommentsWithUserInfo($communityPostId);
    echo "✅ CommunityComment getCommentsWithUserInfo($communityPostId): " . count($ccWithUser) . "\n";
    $rows = $ccDao->update_comment($ccId, ['Content' => 'First community comment (edited)']);
    echo "✅ CommunityComment update_comment(): $rows\n";
    $cc1e = $ccDao->get_comment_by_id($ccId);
    echo "✅ CommunityComment after edit:\n"; print_r($cc1e);
    */

    /* ---------- CommunityLikeDao tests ----------
    $cadded = $clikeDao->add_like($userAId, $communityPostId);
    echo "✅ CommunityLike add_like(): inserted=" . $cadded . "\n";
    $chas = $clikeDao->has_user_liked_post($userAId, $communityPostId);
    echo "✅ CommunityLike has_user_liked_post(): " . ($chas ? 'true' : 'false') . "\n";
    $ccount = $clikeDao->get_like_count($communityPostId);
    echo "✅ CommunityLike get_like_count($communityPostId): $ccount\n";
    $clikesByPost = $clikeDao->get_by_community_post_id($communityPostId);
    echo "✅ CommunityLike get_by_community_post_id($communityPostId): " . count($clikesByPost) . "\n";
    $clikesByUser = $clikeDao->get_by_user_id($userAId);
    echo "✅ CommunityLike get_by_user_id($userAId): " . count($clikesByUser) . "\n";
    $clikesWithUser = $clikeDao->get_likes_with_user_info($communityPostId);
    echo "✅ CommunityLike get_likes_with_user_info($communityPostId): " . count($clikesWithUser) . "\n";
    // $cremoved = $clikeDao->remove_like($userAId, $communityPostId);
    // echo "✅ CommunityLike remove_like(): $cremoved\n";
    */

    /* ===========================
     * Optional cleanup (commented)
     * =========================== */
    /*
    // $followDao->unfollow_user($userAId, $userBId);
    // $userDao->delete_user($userBId);
    // $userDao->delete_user($userAId);
    */

} catch (Exception $e) {
    echo "❌ Tests failed: " . $e->getMessage() . "\n";
}

echo "</pre>";
