<?php
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/PostDao.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$userDao = new UserDao();
$postDao = new PostDao();

echo "<pre>";

/** --- UserDao quick test --- */
try {
    $newUser = [
        'Username' => 'testuser' . mt_rand(1000, 9999),
        'Email'    => 'test' . mt_rand(1000, 9999) . '@example.com',
        'Password' => password_hash('password123', PASSWORD_BCRYPT),
        'FullName' => 'Test User',
        'Role'     => 'user'
    ];

    $newId = $userDao->create_user($newUser);   // <-- was $dao
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

/** --- PostDao tests --- */
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

   
} catch (Exception $e) {
    echo "❌ Post tests failed: " . $e->getMessage() . "\n";
}

echo "</pre>";
