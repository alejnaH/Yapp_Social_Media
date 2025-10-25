<?php
require_once __DIR__ . '/BaseDao.php';

class PostDao extends BaseDao {
    public function __construct() {
        parent::__construct("Post");
    }

    /* CREATE Post */
    public function create_post(array $post): int {
        if (!isset($post['UserID'], $post['Title'], $post['Content'])) {
            throw new InvalidArgumentException("UserID, Title and Content are required to create a post.");
        }
        $data = [
            'UserID'  => $post['UserID'],
            'Title'   => $post['Title'],
            'Content' => $post['Content'],
            // TimeOfPost, UpdatedAt handled by DB
        ];
        return $this->insert($data);
    }

    /* UPDATE Post (Title/Content only) */
    public function edit_post(int $post_id, array $data): int {
        $allowed = [];
        if (isset($data['Title']))   { $allowed['Title'] = $data['Title']; }
        if (isset($data['Content'])) { $allowed['Content'] = $data['Content']; }

        if (empty($allowed)) return 0;
        return $this->updateById($post_id, $allowed);
    }

    /* DELETE Post */
    public function delete_post(int $post_id): int {
        return $this->deleteById($post_id);
    }

    /* GET one Post */
    public function get_one_post(int $post_id): ?array {
        $stmt = $this->connection->prepare("SELECT * FROM `Post` WHERE `PostID` = :id");
        $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    /* GET all Posts (newest first) */
    public function get_all_posts(): array {
        $stmt = $this->connection->prepare(
            "SELECT * FROM `Post` ORDER BY `TimeOfPost` DESC, `PostID` DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* EXTRA HELPERS */

    /* Posts by a given UserID */
    public function getByUserId(int $userId): array {
        $stmt = $this->connection->prepare(
            "SELECT * FROM `Post` WHERE `UserID` = :userId ORDER BY `TimeOfPost` DESC, `PostID` DESC"
        );
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Posts with author info and like counts; marks if current user liked */
    public function getPostsWithUserInfo(?int $currentUserId = null): array {
        if ($currentUserId !== null) {
            $sql = "SELECT
                        p.*,
                        u.Username,
                        u.FullName AS UserFullName,
                        COUNT(DISTINCT l.UserID) AS like_count,
                        MAX(CASE WHEN l.UserID = :currentUserId THEN 1 ELSE 0 END) AS user_liked
                    FROM `Post` p
                    JOIN `User` u ON p.UserID = u.UserID
                    LEFT JOIN `Like` l ON l.PostID = p.PostID
                    GROUP BY p.PostID
                    ORDER BY p.TimeOfPost DESC, p.PostID DESC";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':currentUserId', $currentUserId, PDO::PARAM_INT);
        } else {
            $sql = "SELECT
                        p.*,
                        u.Username,
                        u.FullName AS UserFullName,
                        COUNT(DISTINCT l.UserID) AS like_count,
                        0 AS user_liked
                    FROM `Post` p
                    JOIN `User` u ON p.UserID = u.UserID
                    LEFT JOIN `Like` l ON l.PostID = p.PostID
                    GROUP BY p.PostID
                    ORDER BY p.TimeOfPost DESC, p.PostID DESC";
            $stmt = $this->connection->prepare($sql);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* One post + user + counts for comments/likes */
    public function getPostWithDetails(int $postId): ?array {
        $sql = "SELECT
                    p.*,
                    u.Username,
                    u.FullName AS UserFullName,
                    COUNT(DISTINCT c.CommentID) AS comment_count,
                    COUNT(DISTINCT l.UserID)   AS like_count
                FROM `Post` p
                JOIN `User` u ON p.UserID = u.UserID
                LEFT JOIN `Comment` c ON c.PostID = p.PostID
                LEFT JOIN `Like` l ON l.PostID = p.PostID
                WHERE p.PostID = :postId
                GROUP BY p.PostID";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    /* Community posts (uses CommunityPost table per your schema) */
    public function getPostsByCommunity(int $communityId): array {
        $sql = "SELECT
                    cp.*,
                    u.Username,
                    u.FullName AS UserFullName
                FROM `CommunityPost` cp
                JOIN `User` u ON cp.UserID = u.UserID
                WHERE cp.CommunityID = :communityId
                ORDER BY cp.TimeOfPost DESC, cp.CommunityPostID DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':communityId', $communityId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}