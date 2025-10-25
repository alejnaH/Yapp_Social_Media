<?php
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/PostDao.php';
require_once __DIR__ . '/../dao/CommunityDao.php';
require_once __DIR__ . '/../dao/CommunityPostDao.php';
require_once __DIR__ . '/../dao/CommentDao.php';
require_once __DIR__ . '/../dao/CommunityCommentDao.php';
require_once __DIR__ . '/../dao/LikeDao.php';
require_once __DIR__ . '/../dao/CommunityLikeDao.php'; // NEW

error_reporting(E_ALL);
ini_set('display_errors', 1);

$userDao     = new UserDao();
$postDao     = new PostDao();
$commDao     = new CommunityDao();
$cpDao       = new CommunityPostDao();
$commentDao  = new CommentDao();
$ccDao       = new CommunityCommentDao();
$likeDao     = new LikeDao();
$clikeDao    = new CommunityLikeDao(); // NEW

echo "<pre>";

try {
    /* ===========================
     * Setup: create temp user
     * =========================== */
    $tmpUser = [
        'Username' => 'cmt_tester_' . mt_rand(1000, 9999),
        'Email'    => 'cmt_tester_' . mt_rand(1000, 9999) . '@example.com',
        'Password' => password_hash('secret123', PASSWORD_BCRYPT),
        'FullName' => 'Comment Tester',
        'Role'     => 'user'
    ];
    $userId = $userDao->create_user($tmpUser);
    echo "✅ Temp user created with ID: $userId\n";

    /* ===========================
     * Setup: create a Post (for Comment & Like tests)
     * =========================== */
    $postId = $postDao->create_post([
        'UserID'  => $userId,
        'Title'   => 'Post for comment/like testing',
        'Content' => 'Body for comment/like testing'
    ]);
    echo "✅ Post created with ID: $postId\n";

    /* ===========================
     * CommentDao tests
     * =========================== */

    // 1) Create comment
    $commentId = $commentDao->create_comment([
        'PostID'  => $postId,
        'UserID'  => $userId,
        'Content' => 'First test comment'
    ]);
    echo "✅ Comment created with ID: $commentId\n";

    // 2) Get by ID
    $c1 = $commentDao->get_comment_by_id($commentId);
    echo "✅ Comment get_comment_by_id():\n";
    print_r($c1);

    // 3) Get by PostID
    $byPost = $commentDao->getByPostId($postId);
    echo "✅ Comment getByPostId($postId): found " . count($byPost) . " comment(s)\n";
    if (!empty($byPost)) { print_r($byPost[0]); }

    // 4) Get by UserID
    $byUser = $commentDao->getByUserId($userId);
    echo "✅ Comment getByUserId($userId): found " . count($byUser) . " comment(s)\n";
    if (!empty($byUser)) { print_r($byUser[0]); }

    // 5) Get with user info (join User)
    $withUser = $commentDao->getCommentsWithUserInfo($postId);
    echo "✅ Comment getCommentsWithUserInfo($postId): " . count($withUser) . " row(s)\n";
    if (!empty($withUser)) { print_r($withUser[0]); }

    // 6) Update comment content
    $rows = $commentDao->update_comment($commentId, ['Content' => 'First test comment (edited)']);
    echo "✅ Comment update_comment(): $rows row(s) affected\n";

    // 7) Verify update
    $c1e = $commentDao->get_comment_by_id($commentId);
    echo "✅ Comment after edit:\n";
    print_r($c1e);

    /* ===========================
     * LikeDao tests (for the same Post)
     * =========================== */

    // A) Add like (idempotent insert)
    $added = $likeDao->add_like($userId, $postId);
    echo "✅ Like add_like(): inserted=" . $added . " (1 means new like, 0 means already existed)\n";

    // B) Has user liked?
    $has = $likeDao->has_user_liked_post($userId, $postId);
    echo "✅ Like has_user_liked_post(): " . ($has ? 'true' : 'false') . "\n";

    // C) Like count
    $count = $likeDao->get_like_count($postId);
    echo "✅ Like get_like_count($postId): $count\n";

    // D) Get likes by post
    $likesByPost = $likeDao->get_by_post_id($postId);
    echo "✅ Like get_by_post_id($postId): found " . count($likesByPost) . " like(s)\n";
    if (!empty($likesByPost)) { print_r($likesByPost[0]); }

    // E) Get likes by user
    $likesByUser = $likeDao->get_by_user_id($userId);
    echo "✅ Like get_by_user_id($userId): found " . count($likesByUser) . " like(s)\n";
    if (!empty($likesByUser)) { print_r($likesByUser[0]); }

    // F) Likes with user info
    $likesWithUser = $likeDao->get_likes_with_user_info($postId);
    echo "✅ Like get_likes_with_user_info($postId): " . count($likesWithUser) . " row(s)\n";
    if (!empty($likesWithUser)) { print_r($likesWithUser[0]); }

    // G) (Optional) Remove like & verify
    /*
    $removed = $likeDao->remove_like($userId, $postId);
    echo "✅ Like remove_like(): removed=" . $removed . " (1 means deleted, 0 means none)\n";
    $hasAfter = $likeDao->has_user_liked_post($userId, $postId);
    $countAfter = $likeDao->get_like_count($postId);
    echo "✅ Like after removal — has_user_liked_post: " . ($hasAfter ? 'true' : 'false') . ", count: $countAfter\n";
    */

    /* ===========================
     * Setup: create Community + CommunityPost
     * =========================== */
    $communityId = $commDao->create_community([
        'Name'        => 'Comment Community ' . mt_rand(1000, 9999),
        'Description' => 'For community comment/like tests',
        'OwnerID'     => $userId,
    ]);
    echo "✅ Community created with ID: $communityId\n";

    $communityPostId = $cpDao->create_post([
        'CommunityID' => $communityId,
        'UserID'      => $userId,
        'Title'       => 'Community post for comment/like testing',
        'Content'     => 'Body for community comment/like testing'
    ]);
    echo "✅ CommunityPost created with ID: $communityPostId\n";

    /* ===========================
     * CommunityCommentDao tests
     * =========================== */

    // 1) Create community comment
    $ccId = $ccDao->create_comment([
        'CommunityPostID' => $communityPostId,
        'UserID'          => $userId,
        'Content'         => 'First community comment'
    ]);
    echo "✅ CommunityComment created with ID: $ccId\n";

    // 2) Get by ID
    $cc1 = $ccDao->get_comment_by_id($ccId);
    echo "✅ CommunityComment get_comment_by_id():\n";
    print_r($cc1);

    // 3) Get by CommunityPostID
    $ccByPost = $ccDao->getByCommunityPostId($communityPostId);
    echo "✅ CommunityComment getByCommunityPostId($communityPostId): found " . count($ccByPost) . " comment(s)\n";
    if (!empty($ccByPost)) { print_r($ccByPost[0]); }

    // 4) Get by UserID
    $ccByUser = $ccDao->getByUserId($userId);
    echo "✅ CommunityComment getByUserId($userId): found " . count($ccByUser) . " comment(s)\n";
    if (!empty($ccByUser)) { print_r($ccByUser[0]); }

    // 5) Get with user info
    $ccWithUser = $ccDao->getCommentsWithUserInfo($communityPostId);
    echo "✅ CommunityComment getCommentsWithUserInfo($communityPostId): " . count($ccWithUser) . " row(s)\n";
    if (!empty($ccWithUser)) { print_r($ccWithUser[0]); }

    // 6) Update community comment
    $rows = $ccDao->update_comment($ccId, ['Content' => 'First community comment (edited)']);
    echo "✅ CommunityComment update_comment(): $rows row(s) affected\n";

    // 7) Verify update
    $cc1e = $ccDao->get_comment_by_id($ccId);
    echo "✅ CommunityComment after edit:\n";
    print_r($cc1e);

    /* ===========================
     * CommunityLikeDao tests (for the CommunityPost)
     * =========================== */

    // A) Add like
    $cadded = $clikeDao->add_like($userId, $communityPostId);
    echo "✅ CommunityLike add_like(): inserted=" . $cadded . " (1 new, 0 existed)\n";

    // B) Has user liked?
    $chas = $clikeDao->has_user_liked_post($userId, $communityPostId);
    echo "✅ CommunityLike has_user_liked_post(): " . ($chas ? 'true' : 'false') . "\n";

    // C) Like count
    $ccount = $clikeDao->get_like_count($communityPostId);
    echo "✅ CommunityLike get_like_count($communityPostId): $ccount\n";

    // D) Likes by community post
    $clikesByPost = $clikeDao->get_by_community_post_id($communityPostId);
    echo "✅ CommunityLike get_by_community_post_id($communityPostId): found " . count($clikesByPost) . " like(s)\n";
    if (!empty($clikesByPost)) { print_r($clikesByPost[0]); }

    // E) Likes by user
    $clikesByUser = $clikeDao->get_by_user_id($userId);
    echo "✅ CommunityLike get_by_user_id($userId): found " . count($clikesByUser) . " like(s)\n";
    if (!empty($clikesByUser)) { print_r($clikesByUser[0]); }

    // F) Likes with user info
    $clikesWithUser = $clikeDao->get_likes_with_user_info($communityPostId);
    echo "✅ CommunityLike get_likes_with_user_info($communityPostId): " . count($clikesWithUser) . " row(s)\n";
    if (!empty($clikesWithUser)) { print_r($clikesWithUser[0]); }

    // G) (Optional) Remove like & verify
    /*
    $cremoved = $clikeDao->remove_like($userId, $communityPostId);
    echo "✅ CommunityLike remove_like(): removed=" . $cremoved . " (1 deleted, 0 none)\n";
    $chasAfter   = $clikeDao->has_user_liked_post($userId, $communityPostId);
    $ccountAfter = $clikeDao->get_like_count($communityPostId);
    echo "✅ CommunityLike after removal — has_user_liked_post: " . ($chasAfter ? 'true' : 'false') . ", count: $ccountAfter\n";
    */

    /* ===========================
     * Optional cleanup (commented)
     * =========================== */
    /*
    // CommunityLike cleanup
    $clikeDao->add_like($userId, $communityPostId);
    $clikeDao->remove_like($userId, $communityPostId);

    // Like cleanup
    $likeDao->add_like($userId, $postId);
    $likeDao->remove_like($userId, $postId);

    // Comments cleanup
    $commentDao->delete_comment($commentId);
    $ccDao->delete_comment($ccId);

    // Posts cleanup
    $postDao->delete_post($postId);
    // Community cleanup
    $commDao->delete_community($communityId);
    // User cleanup
    $userDao->delete_user($userId);
    */

} catch (Exception $e) {
    echo "❌ Tests failed: " . $e->getMessage() . "\n";
}

echo "</pre>";
