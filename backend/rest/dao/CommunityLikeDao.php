<?php
require_once __DIR__ . '/BaseDao.php';

class CommunityLikeDao extends BaseDao {
    public function __construct() {
        parent::__construct('CommunityLike');
    }

    /** Idempotent like (composite PK). Returns 1 if inserted, 0 if already exists. */
    public function add_like(int $userId, int $communityPostId): int {
        $sql = "INSERT IGNORE INTO `CommunityLike` (`CommunityPostID`, `UserID`)
                VALUES (:postId, :userId)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':postId', $communityPostId, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /** Remove a like (composite key). */
    public function remove_like(int $userId, int $communityPostId): int {
        $sql = "DELETE FROM `CommunityLike`
                WHERE `UserID` = :userId AND `CommunityPostID` = :postId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':postId', $communityPostId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /** Existence check. */
    public function has_user_liked_post(int $userId, int $communityPostId): bool {
        $sql = "SELECT 1 FROM `CommunityLike`
                WHERE `UserID` = :userId AND `CommunityPostID` = :postId
                LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':postId', $communityPostId, PDO::PARAM_INT);
        $stmt->execute();
        return (bool)$stmt->fetchColumn();
    }

    /** Likes for a community post (oldest first). */
    public function get_by_community_post_id(int $communityPostId): array {
        $sql = "SELECT * FROM `CommunityLike`
                WHERE `CommunityPostID` = :postId
                ORDER BY `LikedAt` ASC, `UserID` ASC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':postId', $communityPostId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Likes made by a user (newest first). */
    public function get_by_user_id(int $userId): array {
        $sql = "SELECT * FROM `CommunityLike`
                WHERE `UserID` = :userId
                ORDER BY `LikedAt` DESC, `CommunityPostID` DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Count likes on a post. */
    public function get_like_count(int $communityPostId): int {
        $sql = "SELECT COUNT(*)
                FROM `CommunityLike`
                WHERE `CommunityPostID` = :postId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':postId', $communityPostId, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /** Likes with user info. */
    public function get_likes_with_user_info(int $communityPostId): array {
        $sql = "SELECT cl.*, u.Username, u.FullName
                FROM `CommunityLike` cl
                JOIN `User` u ON u.`UserID` = cl.`UserID`
                WHERE cl.`CommunityPostID` = :postId
                ORDER BY cl.`LikedAt` ASC, cl.`UserID` ASC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':postId', $communityPostId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
