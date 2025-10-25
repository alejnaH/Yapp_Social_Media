<?php
require_once __DIR__ . '/BaseDao.php';


class UserDao extends BaseDao {
    public function __construct() {
        parent::__construct("User");
    }

    /* CREATE User */
    public function create_user(array $user) {
        $sql = "INSERT INTO `User` (Username, Email, Password, FullName, Role)
                VALUES (:Username, :Email, :Password, :FullName, COALESCE(:Role, 'user'))";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':Username', $user['Username']);
        $stmt->bindValue(':Email',    $user['Email']);
        $stmt->bindValue(':Password', $user['Password']);
        $stmt->bindValue(':FullName', $user['FullName']);
        $stmt->bindValue(':Role',     $user['Role'] ?? null);
        $stmt->execute();
        return (int)$this->connection->lastInsertId();
    }

    /* UPDATE User */
    public function update_user(int $user_id, array $user): int {
        // optionally prevent updating PK or immutable fields:
        unset($user['UserID'], $user['CreatedAt'], $user['UpdatedAt']);
        return parent::update("User", $user_id, $user);
    }

    /* DELETE User */
    public function delete_user(int $user_id): int {
        return parent::delete("User", $user_id);
    }

    /* GET User */
    public function getByEmail(string $email) {
        $stmt = $this->connection->prepare("SELECT * FROM `User` WHERE Email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUsername(string $username) {
        $stmt = $this->connection->prepare("SELECT * FROM `User` WHERE Username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_user_by_id(int $user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM `User` WHERE UserID = :id");
        $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
