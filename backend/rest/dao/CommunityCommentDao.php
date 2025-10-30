<?php
require_once __DIR__ . '/BaseDao.php';

class CommunityCommentDao extends BaseDao {
    public function __construct() {
        parent::__construct('CommunityComment'); // PK -> CommunityCommentID
    }

    /* CREATE */
    public function create_comment(array $comment): int {
        if (!isset($comment['CommunityPostID'], $comment['UserID'], $comment['Content'])) {
            throw new InvalidArgumentException("CommunityPostID, UserID and Content are required.");
        }

        $data = [
            'CommunityPostID' => (int)$comment['CommunityPostID'],
            'UserID'          => (int)$comment['UserID'],
            'Content'         => $comment['Content'],
            // Time/UpdatedAt handled by DB defaults
        ];
        return $this->insert($data); // BaseDao
    }

    /* UPDATE */
    public function update_comment(int $commentId, array $data): int {
        $allowed = [];
        if (isset($data['Content'])) {
            $allowed['Content'] = $data['Content'];
        }

        if (empty($allowed)) return 0;

        return $this->updateById($commentId, $allowed); // BaseDao
    }

    /* DELETE */
    public function delete_comment(int $commentId): int {
        return $this->deleteById($commentId); // BaseDao
    }

    /* GET by ID (delegate to BaseDao) */
    public function get_comment_by_id(int $commentId): ?array {
        return $this->getById($commentId); // BaseDao
    }

    /* List comments for a specific community post (custom ordering) */
    public function getByCommunityPostId(int $communityPostId): array {
        $stmt = $this->connection->prepare(
            "SELECT * FROM `CommunityComment`
             WHERE `CommunityPostID` = :pid
             ORDER BY `Time` ASC, `CommunityCommentID` ASC"
        );
        $stmt->bindValue(':pid', $communityPostId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(); // FETCH_ASSOC set in BaseDao
    }

    /* List comments by a user across all community posts (custom ordering) */
    public function getByUserId(int $userId): array {
        $stmt = $this->connection->prepare(
            "SELECT * FROM `CommunityComment`
             WHERE `UserID` = :uid
             ORDER BY `Time` DESC, `CommunityCommentID` DESC"
        );
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /* Join with user info for a community post (table-specific) */
    public function getCommentsWithUserInfo(int $communityPostId): array {
        $sql = "SELECT
                    cc.*,
                    u.Username,
                    u.FullName AS UserFullName
                FROM `CommunityComment` cc
                JOIN `User` u ON cc.`UserID` = u.`UserID`
                WHERE cc.`CommunityPostID` = :pid
                ORDER BY cc.`Time` ASC, cc.`CommunityCommentID` ASC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':pid', $communityPostId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}
