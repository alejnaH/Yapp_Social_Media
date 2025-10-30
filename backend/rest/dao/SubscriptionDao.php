<?php
require_once __DIR__ . '/BaseDao.php';

class SubscriptionDao extends BaseDao {
    public function __construct() {
        parent::__construct("Subscription");
    }

    /* Subscribe a user to a community.*/
    public function subscribe(int $userId, int $communityId): int {
        // Composite PK (CommunityID, UserID) prevents duplicates; IGNORE avoids exception
        $sql = "INSERT IGNORE INTO `Subscription` (`CommunityID`, `UserID`)
                VALUES (:communityId, :userId)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':communityId', $communityId, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /* Unsubscribe */
    public function unsubscribe(int $userId, int $communityId): bool {
        $sql = "DELETE FROM `Subscription`
                WHERE `CommunityID` = :communityId AND `UserID` = :userId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':communityId', $communityId, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /* Is the user subscribed to this community? */
    public function isSubscribed(int $userId, int $communityId): bool {
        $sql = "SELECT 1 FROM `Subscription`
                WHERE `CommunityID` = :communityId AND `UserID` = :userId
                LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':communityId', $communityId, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return (bool)$stmt->fetchColumn();
    }

    /* Community IDs this user is subscribed to (ordered newest first). */
    public function getSubscriptionsByUser(int $userId): array {
        $sql = "SELECT `CommunityID`
                FROM `Subscription`
                WHERE `UserID` = :userId
                ORDER BY `SubscribedAt` DESC, `CommunityID` DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /* User IDs subscribed to a community (ordered newest first). */
    public function getSubscribersByCommunity(int $communityId): array {
        $sql = "SELECT `UserID`
                FROM `Subscription`
                WHERE `CommunityID` = :communityId
                ORDER BY `SubscribedAt` DESC, `UserID` DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':communityId', $communityId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /* Total members (subscribers) in a community. */
    public function countSubscribers(int $communityId): int {
        $sql = "SELECT COUNT(*)
                FROM `Subscription`
                WHERE `CommunityID` = :communityId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':communityId', $communityId, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /** OPTIONAL: subscribers with user info (handy for member lists). */
    public function getSubscribersWithUserInfo(int $communityId): array {
        $sql = "SELECT s.`UserID`, s.`SubscribedAt`, u.`Username`, u.`FullName`
                FROM `Subscription` s
                JOIN `User` u ON u.`UserID` = s.`UserID`
                WHERE s.`CommunityID` = :communityId
                ORDER BY s.`SubscribedAt` DESC, s.`UserID` DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':communityId', $communityId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
