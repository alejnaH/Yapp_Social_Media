<?php
require_once __DIR__ . '/BaseDao.php';

class CommentDao extends BaseDao {
    public function __construct() {
        parent::__construct('Comment'); // PK -> CommentID
    }

    /* CREATE */
    public function create_comment(array $comment): int {
        if (!isset($comment['PostID'], $comment['UserID'], $comment['Content'])) {
            throw new InvalidArgumentException("PostID, UserID and Content are required.");
        }

        $data = [
            'PostID'  => (int)$comment['PostID'],
            'UserID'  => (int)$comment['UserID'],
            'Content' => $comment['Content'],
            // Time / UpdatedAt handled by DB defaults
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

    /* GET by ID */
    public function get_comment_by_id(int $commentId): ?array {
        return $this->getById($commentId); // BaseDao
    }

    /* List comments for a post (custom ordering) */
    public function getByPostId(int $postId): array {
        $stmt = $this->connection->prepare(
            "SELECT * FROM `Comment`
             WHERE `PostID` = :postId
             ORDER BY `Time` ASC, `CommentID` ASC"
        );
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(); // default FETCH_ASSOC from BaseDao ctor
    }

    /* List comments by a user (custom ordering) */
    public function getByUserId(int $userId): array {
        $stmt = $this->connection->prepare(
            "SELECT * FROM `Comment`
             WHERE `UserID` = :userId
             ORDER BY `Time` DESC, `CommentID` DESC"
        );
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /* Join with user info (table-specific) */
    public function getCommentsWithUserInfo(int $postId): array {
        $sql = "SELECT
                    c.*,
                    u.Username,
                    u.FullName AS UserFullName
                FROM `Comment` c
                JOIN `User` u ON c.`UserID` = u.`UserID`
                WHERE c.`PostID` = :postId
                ORDER BY c.`Time` ASC, c.`CommentID` ASC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}
