<?php
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/PostDao.php';
require_once __DIR__ . '/../dao/CommunityDao.php';
require_once __DIR__ . '/../dao/CommunityPostDao.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$userDao = new UserDao();
$postDao = new PostDao();
$commDao = new CommunityDao();
$cpDao   = new CommunityPostDao();

echo "<pre>";

/** =========================
 *  (OLD) UserDao quick test
 *  =========================
 *  NOTE: Kept exactly as you had it, but commented out per your request.
 */
/*
try {
    $newUser = [
        'Username' => 'testuser' . mt_rand(1000, 9999),
        'Email'    => 'test' . mt_rand(1000, 9999) . '@example.com',
        'Password' => password_hash('password123', PASSWORD_BCRYPT),
        'FullName' => 'Test User',
        'Role'     => 'user'
    ];

    $newId = $userDao->create_user($newUser);
    echo "✅ User created with ID: $newId\n";

    $user = $userDao->getByEmail($newUser['Email']);
    echo "✅ getByEmail():\n"; print_r($user);

    $user = $userDao->getByUsername($newUser['Username']);
    echo "✅ getByUsername():\n"; print_r($user);

    $user = $userDao->get_user_by_id($newId);
    echo "✅ get_user_by_id():\n"; print_r($user);

    $rows = $userDao->update_user($newId, ['FullName' => 'Updated Test User', 'Role' => 'admin']);
    echo "✅ update_user(): $rows row(s) affected\n";
} catch (Exception $e) {
    echo "❌ User tests failed: " . $e->getMessage() . "\n";
}
*/

/** =========================
 *  (OLD) PostDao tests
 *  =========================
 *  NOTE: Kept exactly as you had it, but commented out per your request.
 */
/*
try {
    // Use the same user as post owner
    $postId = $postDao->create_post([
        'UserID'  => $newId,
        'Title'   => 'Hello Post',
        'Content' => 'This is the first content body.'
    ]);
    echo "✅ Post created with ID: $postId\n";

    $one = $postDao->get_one_post($postId);
    echo "✅ get_one_post():\n"; print_r($one);

    $all = $postDao->get_all_posts();
    echo "✅ get_all_posts(): total=" . count($all) . " (showing up to 2)\n";
    print_r(array_slice($all, 0, 2));

    $rows = $postDao->edit_post($postId, ['Title' => 'Hello Post (edited)', 'Content' => 'Updated content.']);
    echo "✅ edit_post(): $rows row(s) affected\n";

    $edited = $postDao->get_one_post($postId);
    echo "✅ get_one_post() after edit:\n"; print_r($edited);

    $byUser = $postDao->getByUserId($newId);
    echo "✅ getByUserId($newId): found " . count($byUser) . " post(s)\n";

    $withInfo = $postDao->getPostsWithUserInfo();
    echo "✅ getPostsWithUserInfo(): got " . count($withInfo) . " rows\n";
    if (!empty($withInfo)) { print_r($withInfo[0]); }

    $withInfoYou = $postDao->getPostsWithUserInfo($newId);
    echo "✅ getPostsWithUserInfo($newId): got " . count($withInfoYou) . " rows\n";
    if (!empty($withInfoYou)) { print_r($withInfoYou[0]); }

    $details = $postDao->getPostWithDetails($postId);
    echo "✅ getPostWithDetails($postId):\n"; print_r($details);

    // keep data
    // $del = $postDao->delete_post($postId);
    // echo "✅ delete_post(): $del row(s) deleted\n";
    // $delUser = $userDao->delete_user($newId);
    // echo "✅ delete_user(): $delUser row(s) deleted\n";
} catch (Exception $e) {
    echo "❌ Post tests failed: " . $e->getMessage() . "\n";
}
*/

try {
    /* ===========================
     *  0) Create a temp user
     * =========================== */
    $tmpUser = [
        'Username' => 'comm_tester_' . mt_rand(1000, 9999),
        'Email'    => 'comm_tester_' . mt_rand(1000, 9999) . '@example.com',
        'Password' => password_hash('secret123', PASSWORD_BCRYPT),
        'FullName' => 'Community Tester',
        'Role'     => 'user'
    ];
    $ownerId = $userDao->create_user($tmpUser);
    echo "✅ Temp user created with ID: $ownerId\n";

    /* ===========================
     *  1) CommunityDao — CREATE
     * =========================== */
    $communityName = 'Test Community ' . mt_rand(1000, 9999);
    $communityId = $commDao->create_community([
        'Name'        => $communityName,
        'Description' => 'Temporary test community for DAO tests',
        'OwnerID'     => $ownerId,
    ]);
    echo "✅ Community created with ID: $communityId\n";

    /* ===========================
     *  2) CommunityDao — GET BY ID
     * =========================== */
    $c = $commDao->get_community_by_id($communityId);
    echo "✅ get_community_by_id():\n";
    print_r($c);

    /* ===========================
     *  3) CommunityDao — GET BY NAME
     * =========================== */
    $cByName = $commDao->get_community_by_name($communityName);
    echo "✅ get_community_by_name('{$communityName}'):\n";
    print_r($cByName);

    /* ===========================
     *  4) CommunityDao — GET ALL
     * =========================== */
    $allComms = $commDao->get_all_communities();
    echo "✅ get_all_communities(): total=" . count($allComms) . " (showing up to 2)\n";
    print_r(array_slice($allComms, 0, 2));

    /* ===========================
     *  5) CommunityDao — UPDATE
     * =========================== */
    $rows = $commDao->update_community($communityId, [
        'Description' => 'Updated description for test community',
        'Name'        => $communityName . ' (edited)'
    ]);
    echo "✅ update_community(): $rows row(s) affected\n";

    /* ===========================
     *  6) CommunityDao — VERIFY UPDATE
     * =========================== */
    $cEdited = $commDao->get_community_by_id($communityId);
    echo "✅ get_community_by_id() after edit:\n";
    print_r($cEdited);

    // Also verify getByName with the edited name
    $cByNameEdited = $commDao->get_community_by_name($cEdited['Name']);
    echo "✅ get_community_by_name(edited name):\n";
    print_r($cByNameEdited);

    /* ===========================
     *  7) CommunityDao — BY OWNER
     * =========================== */
    $owned = $commDao->get_communities_by_owner($ownerId);
    echo "✅ get_communities_by_owner($ownerId): found " . count($owned) . " community(ies)\n";

    /* =====================================
     *  8) CommunityPostDao — CREATE POST
     * ===================================== */
    $cpId = $cpDao->create_post([
        'CommunityID' => $communityId,
        'UserID'      => $ownerId, // author = same temp user
        'Title'       => 'Hello Community',
        'Content'     => 'This is a community post body.'
    ]);
    echo "✅ CommunityPost created with ID: $cpId\n";

    /* =====================================
     *  9) CommunityPostDao — GET ONE
     * ===================================== */
    $cpOne = $cpDao->get_post_by_id($cpId);
    echo "✅ CommunityPost get_post_by_id():\n";
    print_r($cpOne);

    /* =====================================
     * 10) CommunityPostDao — GET ALL
     * ===================================== */
    $cpAll = $cpDao->get_all_posts();
    echo "✅ CommunityPost get_all_posts(): total=" . count($cpAll) . " (showing up to 2)\n";
    print_r(array_slice($cpAll, 0, 2));

    /* =====================================
     * 11) CommunityPostDao — BY COMMUNITY
     * ===================================== */
    $byCommunity = $cpDao->get_posts_by_community($communityId);
    echo "✅ CommunityPost get_posts_by_community($communityId): found " . count($byCommunity) . " post(s)\n";
    if (!empty($byCommunity)) { print_r($byCommunity[0]); }

    /* =====================================
     * 12) CommunityPostDao — UPDATE
     * ===================================== */
    $rows = $cpDao->update_post($cpId, [
        'Title'   => 'Hello Community (edited)',
        'Content' => 'Updated community post content.'
    ]);
    echo "✅ CommunityPost update_post(): $rows row(s) affected\n";

    /* =====================================
     * 13) CommunityPostDao — VERIFY UPDATE
     * ===================================== */
    $cpEdited = $cpDao->get_post_by_id($cpId);
    echo "✅ CommunityPost get_post_by_id() after edit:\n";
    print_r($cpEdited);

    /* =====================================
     * Optional cleanup (leave commented if you want to keep data)
     * ===================================== */
    /*
    $delCp   = $cpDao->delete_post($cpId);
    echo "✅ CommunityPost delete_post(): $delCp row(s) deleted\n";

    $delComm = $commDao->delete_community($communityId);
    echo "✅ Community delete_community(): $delComm row(s) deleted\n";

    $delUser = $userDao->delete_user($ownerId);
    echo "✅ User delete_user(): $delUser row(s) deleted\n";
    */

} catch (Exception $e) {
    echo "❌ Tests failed: " . $e->getMessage() . "\n";
}

echo "</pre>";
