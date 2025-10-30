<?php
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/PostDao.php';
require_once __DIR__ . '/../dao/LikeDao.php';
require_once __DIR__ . '/../dao/CommentDao.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>";

try {
    $userDao = new UserDao();
    $postDao = new PostDao();
    $likeDao = new LikeDao();
    $commentDao = new CommentDao();

    /* =============================================
     * Test setup (deterministic, no randoms)
     * ============================================= */
    $testEmail = 'post_test_user@example.com';
    $testUsername = 'post_test_user';
    $testPostTitle = 'Test Post Title';
    $testPostUpdated = 'Updated Post Title';

    // 🧹 Cleanup previous test data (re-run safe)
    if ($existingUser = $userDao->getByEmail($testEmail)) {
        $userDao->delete_user($existingUser['UserID']);
        echo "🧹 Cleaned previous user and cascaded posts.\n";
    }

    /* =============================================
     * 1) Create a User
     * ============================================= */
    $userId = $userDao->create_user([
        'Username' => $testUsername,
        'Email'    => $testEmail,
        'Password' => password_hash('secret123', PASSWORD_BCRYPT),
        'FullName' => 'Post Test User',
        'Role'     => 'user'
    ]);
    echo "✅ User created: ID={$userId}\n";

    /* =============================================
     * 2) Create a Post
     * ============================================= */
    $postId = $postDao->create_post([
        'UserID'  => $userId,
        'Title'   => $testPostTitle,
        'Content' => 'This is the body of the test post.'
    ]);
    echo "✅ Post created: ID={$postId}\n";

    /* =============================================
     * 3) get_one_post (BaseDao:getById)
     * ============================================= */
    $p1 = $postDao->get_one_post($postId);
    echo "✅ get_one_post:\n"; print_r($p1);

    /* =============================================
     * 4) get_all_posts
     * ============================================= */
    $all = $postDao->get_all_posts();
    echo "✅ get_all_posts(): " . count($all) . " total\n";

    /* =============================================
     * 5) getByUserId
     * ============================================= */
    $byUser = $postDao->getByUserId($userId);
    echo "✅ getByUserId($userId): " . count($byUser) . " post(s)\n";
    if (!empty($byUser)) print_r($byUser[0]);

    /* =============================================
     * 6) edit_post (BaseDao:updateById)
     * ============================================= */
    $rowsUpd = $postDao->edit_post($postId, [
        'Title'   => $testPostUpdated,
        'Content' => 'Updated content.'
    ]);
    echo "✅ edit_post: rows affected={$rowsUpd}\n";
    $pAfter = $postDao->get_one_post($postId);
    echo "✅ After edit:\n"; print_r($pAfter);

    /* =============================================
     * 7) getPostsWithUserInfo (with current user)
     * ============================================= */
    $postsWithInfo = $postDao->getPostsWithUserInfo($userId);
    echo "✅ getPostsWithUserInfo(currentUserId={$userId}): count=" . count($postsWithInfo) . "\n";
    if (!empty($postsWithInfo)) print_r($postsWithInfo[0]);

    /* =============================================
     * 8) getPostWithDetails
     * ============================================= */
    // Add a like and comment for richer data
    $likeDao->add_like($userId, $postId);
    $commentId = $commentDao->create_comment([
        'PostID'  => $postId,
        'UserID'  => $userId,
        'Content' => 'Test comment for this post'
    ]);

    $details = $postDao->getPostWithDetails($postId);
    echo "✅ getPostWithDetails(PostID={$postId}):\n"; print_r($details);

    /* =============================================
     * 9) delete_post (optional)
     * ============================================= */
    /*
    $rowsDel = $postDao->delete_post($postId);
    echo "✅ delete_post: affected_rows={$rowsDel}\n";
    $afterDel = $postDao->get_one_post($postId);
    echo "✅ fetch after delete (should be null): "; var_dump($afterDel);
    */

    /* =============================================
     * 🧹 Optional cleanup
     * ============================================= */
    /*
    $userDao->delete_user($userId); // cascade deletes posts, likes, comments
    echo "🧹 Cleanup: deleted user and cascade data.\n";
    */

} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
}

echo "</pre>";
