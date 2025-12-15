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
            'CommunityID' => (int)$post['CommunityID'],
            'UserID'      => (int)$post['UserID'],
            'Title'       => $post['Title'],
            'Content'     => $post['Content'],
            // TimeOfPost, UpdatedAt handled by DB
        ];

        return $this->insert($data);
    }

    /* UPDATE */
    public function update_post(int $communityPostId, array $data): int {
        $allowed = [];
        if (isset($data['Title']))   $allowed['Title'] = $data['Title'];
        if (isset($data['Content'])) $allowed['Content'] = $data['Content'];

        return empty($allowed) ? 0 : $this->updateById($communityPostId, $allowed);
    }

    /* DELETE */
    public function delete_post(int $communityPostId): int {
        return $this->deleteById($communityPostId);
    }

    /* GET (all posts global) */
    public function get_all_posts(): array {
        $sql = "SELECT * FROM `CommunityPost`
                ORDER BY `TimeOfPost` DESC, `CommunityPostID` DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /* GET all posts in one community with user info AND community name */
    public function get_posts_by_community(int $communityId): array {
        $sql = "SELECT cp.*, u.Username, u.FullName AS UserFullName, c.Name AS CommunityName
                FROM `CommunityPost` cp
                JOIN `User` u ON cp.UserID = u.UserID
                JOIN `Community` c ON cp.CommunityID = c.CommunityID
                WHERE cp.CommunityID = :cid
                ORDER BY cp.TimeOfPost DESC, cp.CommunityPostID DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':cid', $communityId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /* NEW: GET posts from user's subscribed communities with community name */
    public function get_posts_from_subscribed_communities(int $userId): array {
        $sql = "SELECT cp.*, u.Username, u.FullName AS UserFullName, c.Name AS CommunityName
                FROM `CommunityPost` cp
                JOIN `User` u ON cp.UserID = u.UserID
                JOIN `Community` c ON cp.CommunityID = c.CommunityID
                JOIN `Subscription` s ON cp.CommunityID = s.CommunityID
                WHERE s.UserID = :uid
                ORDER BY cp.TimeOfPost DESC, cp.CommunityPostID DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /* GET one post by ID with community name */
    public function get_post_by_id(int $communityPostId): ?array {
        $sql = "SELECT cp.*, u.Username, u.FullName AS UserFullName, c.Name AS CommunityName
                FROM `CommunityPost` cp
                JOIN `User` u ON cp.UserID = u.UserID
                JOIN `Community` c ON cp.CommunityID = c.CommunityID
                WHERE cp.CommunityPostID = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $communityPostId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}