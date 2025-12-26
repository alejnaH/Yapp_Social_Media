<?php
require_once __DIR__ . '/BaseDao.php';

class LikeDao extends BaseDao {
    public function __construct() {
        // exact table casing from your schema
        parent::__construct("Like");
    }

    /* Create a (idempotent). Returns 1 if inserted, 0 if it already existed. */
    public function add_like(int $userId, int $postId): int {
        // INSERT IGNORE prevents a duplicate-key error on (PostID, UserID)
        $sql = "INSERT IGNORE INTO `like` (`PostID`, `UserID`) VALUES (:postId, :userId)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount(); // 1 if inserted, 0 if already existed
    }

    /* Remove a like. Returns affected rows (0 or 1). */
    public function remove_like(int $userId, int $postId): int {
        $sql = "DELETE FROM `like` WHERE `UserID` = :userId AND `PostID` = :postId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /* Has the user liked this post? */
    public function has_user_liked_post(int $userId, int $postId): bool {
        $sql = "SELECT 1 FROM `like` WHERE `UserID` = :userId AND `PostID` = :postId LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
        return (bool)$stmt->fetchColumn();
    }

    /* All likes for a post (oldest first). */
    public function get_by_post_id(int $postId): array {
        $sql = "SELECT * FROM `like` WHERE `PostID` = :postId ORDER BY `LikedAt` ASC, `UserID` ASC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* All likes by a user (newest first). */
    public function get_by_user_id(int $userId): array {
        $sql = "SELECT * FROM `like` WHERE `UserID` = :userId ORDER BY `LikedAt` DESC, `PostID` DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Number of likes on a post. */
    public function get_like_count(int $postId): int {
        $sql = "SELECT COUNT(*) FROM `like` WHERE `PostID` = :postId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /* OPTIONAL: Users who liked a post (with usernames). */
    public function get_likes_with_user_info(int $postId): array {
        $sql = "SELECT l.*, u.Username, u.FullName
                FROM `like` l
                JOIN `User` u ON u.`UserID` = l.`UserID`
                WHERE l.`PostID` = :postId
                ORDER BY l.`LikedAt` ASC, l.`UserID` ASC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
