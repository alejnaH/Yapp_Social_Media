<?php
require_once __DIR__ . '/BaseDao.php';

class CommunityPostDao extends BaseDao {
    public function __construct() {
        parent::__construct("CommunityPost");
    }

    /* CREATE */
    public function create_post(array $post): int {
        if (!isset($post['CommunityID'], $post['UserID'], $post['Title'], $post['Content'])) {
            throw new InvalidArgumentException("CommunityID, UserID, Title and Content are required.");
        }

        $data = [
            'CommunityID' => $post['CommunityID'],
            'UserID'      => $post['UserID'],
            'Title'       => $post['Title'],
            'Content'     => $post['Content'],
            // TimeOfPost, UpdatedAt handled by DB
        ];
        return $this->insert($data);
    }

    /* UPDATE */
    public function update_post(int $community_post_id, array $data): int {
        $allowed = [];
        if (isset($data['Title']))   { $allowed['Title'] = $data['Title']; }
        if (isset($data['Content'])) { $allowed['Content'] = $data['Content']; }

        if (empty($allowed)) return 0;
        return $this->updateById($community_post_id, $allowed);
    }

    /* DELETE */
    public function delete_post(int $community_post_id): int {
        return $this->deleteById($community_post_id);
    }

    /* GET */
    public function get_all_posts(): array {
        $stmt = $this->connection->prepare(
            "SELECT * FROM `CommunityPost` ORDER BY `TimeOfPost` DESC, `CommunityPostID` DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** GET all posts for a given CommunityID (with basic user info) */
    public function get_posts_by_community(int $communityId): array {
        $sql = "SELECT cp.*, u.Username, u.FullName AS UserFullName
                FROM `CommunityPost` cp
                JOIN `User` u ON cp.UserID = u.UserID
                WHERE cp.CommunityID = :cid
                ORDER BY cp.TimeOfPost DESC, cp.CommunityPostID DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':cid', $communityId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** GET one CommunityPost by ID */
    public function get_post_by_id(int $community_post_id): ?array {
        $stmt = $this->connection->prepare(
            "SELECT * FROM `CommunityPost` WHERE `CommunityPostID` = :id"
        );
        $stmt->bindValue(':id', $community_post_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }
}
?>