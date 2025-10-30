<?php
require_once __DIR__ . '/BaseDao.php';

class FollowDao extends BaseDao {
    public function __construct() {
        parent::__construct("Follow");
    }

    /* Follow a user */
    public function follow_user(int $followerId, int $followedId): int {
        // DB has CHECK(FollowerID != FollowedID), FK to User(UserID), PK(FollowerID,FollowedID)
        $sql = "INSERT IGNORE INTO `Follow` (`FollowerID`, `FollowedID`)
                VALUES (:followerId, :followedId)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':followerId', $followerId, PDO::PARAM_INT);
        $stmt->bindValue(':followedId', $followedId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount(); // 1 new, 0 existed
    }

    /* Unfollow a user */
    public function unfollow_user(int $followerId, int $followedId): bool {
        $sql = "DELETE FROM `Follow`
                WHERE `FollowerID` = :followerId AND `FollowedID` = :followedId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':followerId', $followerId, PDO::PARAM_INT);
        $stmt->bindValue(':followedId', $followedId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /* Is followerId following followedId? */
    public function is_following(int $followerId, int $followedId): bool {
        $sql = "SELECT 1 FROM `Follow`
                WHERE `FollowerID` = :followerId AND `FollowedID` = :followedId
                LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':followerId', $followerId, PDO::PARAM_INT);
        $stmt->bindValue(':followedId', $followedId, PDO::PARAM_INT);
        $stmt->execute();
        return (bool)$stmt->fetchColumn();
    }

    /* Count followers of a user (how many follow this user). */
    public function count_followers(int $userId): int {
        $sql = "SELECT COUNT(*) FROM `Follow` WHERE `FollowedID` = :userId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /* Count following (how many users this user follows). */
    public function count_following(int $userId): int {
        $sql = "SELECT COUNT(*) FROM `Follow` WHERE `FollowerID` = :userId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /* IDs of users who follow this user (followers). */
    public function get_followers(int $userId): array {
        $sql = "SELECT `FollowerID`
                FROM `Follow`
                WHERE `FollowedID` = :userId
                ORDER BY `FollowedAt` DESC, `FollowerID` DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /* IDs of users that this user is following. */
    public function get_following(int $userId): array {
        $sql = "SELECT `FollowedID`
                FROM `Follow`
                WHERE `FollowerID` = :userId
                ORDER BY `FollowedAt` DESC, `FollowedID` DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /* Followers with user info (Username, FullName). */
    public function get_followers_with_user_info(int $userId): array {
        $sql = "SELECT f.`FollowerID` AS UserID, f.`FollowedAt`, u.`Username`, u.`FullName`
                FROM `Follow` f
                JOIN `User` u ON u.`UserID` = f.`FollowerID`
                WHERE f.`FollowedID` = :userId
                ORDER BY f.`FollowedAt` DESC, f.`FollowerID` DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Following with user info (people this user follows). */
    public function get_following_with_user_info(int $userId): array {
        $sql = "SELECT f.`FollowedID` AS UserID, f.`FollowedAt`, u.`Username`, u.`FullName`
                FROM `Follow` f
                JOIN `User` u ON u.`UserID` = f.`FollowedID`
                WHERE f.`FollowerID` = :userId
                ORDER BY f.`FollowedAt` DESC, f.`FollowedID` DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* OPTIONAL: mutual follows between userA and userB (both directions). */
    public function are_mutuals(int $userA, int $userB): bool {
        $sql = "SELECT 
                    SUM(CASE WHEN `FollowerID` = :a AND `FollowedID` = :b THEN 1 ELSE 0 END) +
                    SUM(CASE WHEN `FollowerID` = :b AND `FollowedID` = :a THEN 1 ELSE 0 END) AS edges
                FROM `Follow`
                WHERE (`FollowerID` = :a AND `FollowedID` = :b)
                   OR (`FollowerID` = :b AND `FollowedID` = :a)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':a', $userA, PDO::PARAM_INT);
        $stmt->bindValue(':b', $userB, PDO::PARAM_INT);
        $stmt->execute();
        return ((int)$stmt->fetchColumn()) === 2;
    }
}
