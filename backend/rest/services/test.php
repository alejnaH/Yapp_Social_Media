<?php
require_once __DIR__ . '/../dao/UserDao.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$dao = new UserDao();

echo "<pre>";

try {
    $newUser = [
        'Username' => 'testuser136',
        'Email'    => 'testuser1364@example.com',
        'Password' => password_hash('password123', PASSWORD_BCRYPT),
        'FullName' => 'Test User',
        'Role'     => 'user'
    ];

    $newId = $dao->create_user($newUser);
    echo "✅ User created with ID: $newId\n";
} catch (Exception $e) {
    echo "❌ Create failed: " . $e->getMessage() . "\n";
}

// 2. GET BY EMAIL
try {
    $user = $dao->getByEmail($newUser['Email']);
    echo "✅ getByEmail():\n";
    print_r($user);
} catch (Exception $e) {
    echo "❌ getByEmail failed: " . $e->getMessage() . "\n";
}

// 3. GET BY USERNAME
try {
    $user = $dao->getByUsername($newUser['Username']);
    echo "✅ getByUsername():\n";
    print_r($user);
} catch (Exception $e) {
    echo "❌ getByUsername failed: " . $e->getMessage() . "\n";
}

// 4. GET BY ID
try {
    $user = $dao->get_user_by_id($newId);
    echo "✅ get_user_by_id():\n";
    print_r($user);
} catch (Exception $e) {
    echo "❌ get_user_by_id failed: " . $e->getMessage() . "\n";
}

// 5. UPDATE USER
try {
    $updated = [
        'FullName' => 'Updated Test User',
        'Role'     => 'admin'
    ];
    $rows = $dao->update_user($newId, $updated);
    echo "✅ update_user(): $rows row(s) affected\n";
} catch (Exception $e) {
    echo "❌ update_user failed: " . $e->getMessage() . "\n";
}

// 6. DELETE USER
try {
    $rows = $dao->delete_user($newId);
    echo "✅ delete_user(): $rows row(s) deleted\n";
} catch (Exception $e) {
    echo "❌ delete_user failed: " . $e->getMessage() . "\n";
}

echo "</pre>";
