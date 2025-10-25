<?php
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/PostDao.php';
require_once __DIR__ . '/../dao/CommunityDao.php';
require_once __DIR__ . '/../dao/CommunityPostDao.php';
require_once __DIR__ . '/../dao/CommentDao.php';
require_once __DIR__ . '/../dao/CommunityCommentDao.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$userDao   = new UserDao();
$postDao   = new PostDao();
$commDao   = new CommunityDao();
$cpDao     = new CommunityPostDao();
$commentDao= new CommentDao();
$ccDao     = new CommunityCommentDao();

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
     * Setup: create a Post (for Comment tests)
     * =========================== */
    $postId = $postDao->create_post([
        'UserID'  => $userId,
        'Title'   => 'Post for comment testing',
        'Content' => 'Body for comment testing'
    ]);
    echo "✅ Post created with ID: $postId (for Comment tests)\n";

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
     * Setup: create Community + CommunityPost (for CommunityComment tests)
     * =========================== */
    $communityId = $commDao->create_community([
        'Name'        => 'Comment Community ' . mt_rand(1000, 9999),
        'Description' => 'For community comment tests',
        'OwnerID'     => $userId,
    ]);
    echo "✅ Community created with ID: $communityId\n";

    $communityPostId = $cpDao->create_post([
        'CommunityID' => $communityId,
        'UserID'      => $userId,
        'Title'       => 'Community post for comment testing',
        'Content'     => 'Body for community comment testing'
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
     * Optional cleanup (commented to keep data)
     * =========================== */
    /*
    $delCc = $ccDao->delete_comment($ccId);
    echo "✅ CommunityComment delete_comment(): $delCc row(s) deleted\n";

    $delC  = $commDao->delete_community($communityId);
    echo "✅ Community delete_community(): $delC row(s) deleted\n";

    $delCmt = $commentDao->delete_comment($commentId);
    echo "✅ Comment delete_comment(): $delCmt row(s) deleted\n";

    $delPost = $postDao->delete_post($postId);
    echo "✅ Post delete_post(): $delPost row(s) deleted\n";

    $delUser = $userDao->delete_user($userId);
    echo "✅ User delete_user(): $delUser row(s) deleted\n";
    */

} catch (Exception $e) {
    echo "❌ Tests failed: " . $e->getMessage() . "\n";
}

echo "</pre>";
