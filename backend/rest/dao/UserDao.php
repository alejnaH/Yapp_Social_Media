<?php
require_once __DIR__ . '/BaseDao.php';

class UserDao extends BaseDao {
    public function __construct() {
        parent::__construct('User');
    }

    /* CREATE User */
    public function create_user(array $user): int {
        return $this->createUser($user);
    }

    /* UPDATE User */
    public function update_user(int $user_id, array $user): int {
        // prevent updating PK / timestamps
        unset($user['UserID'], $user['CreatedAt'], $user['UpdatedAt']);
        if (empty($user)) return 0;
        return $this->updateById($user_id, $user);
    }

    /* DELETE User — delegate to BaseDao::deleteById */
    public function delete_user(int $user_id): int {
        return $this->deleteById($user_id);
    }

    /* GET User by ID — delegate to BaseDao::getById */
    public function get_user_by_id(int $user_id): ?array {
        return $this->getById($user_id);
    }

    /* Table-specific finders */
    public function getByEmail(string $email): ?array {
        $stmt = $this->connection->prepare("SELECT * FROM `User` WHERE `Email` = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    public function getByUsername(string $username): ?array {
        $stmt = $this->connection->prepare("SELECT * FROM `User` WHERE `Username` = :username");
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    /* GET all users (PDO direct) */
    public function get_all_users(): array {
        $stmt = $this->connection->prepare("SELECT * FROM `User`");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
