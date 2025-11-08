<?php
require_once __DIR__ . '/../dao/CommunityLikeDao.php';

class CommunityLikeService {
    private $communityLikeDao;

    public function __construct() {
        $this->communityLikeDao = new CommunityLikeDao();
    }

    /** Idempotent like (returns 1 if inserted, 0 if already existed) */
    public function add_like(int $user_id, int $community_post_id) {
        if (empty($user_id)) return "Invalid user ID";
        if (empty($community_post_id)) return "Invalid community post ID";
        return $this->communityLikeDao->add_like($user_id, $community_post_id);
    }

    /** Remove like (returns affected rows: 1 if removed, 0 if none) */
    public function remove_like(int $user_id, int $community_post_id) {
        if (empty($user_id)) return "Invalid user ID";
        if (empty($community_post_id)) return "Invalid community post ID";
        return $this->communityLikeDao->remove_like($user_id, $community_post_id);
    }

    /** Check existence */
    public function has_user_liked_post(int $user_id, int $community_post_id) {
        if (empty($user_id)) return "Invalid user ID";
        if (empty($community_post_id)) return "Invalid community post ID";
        return $this->communityLikeDao->has_user_liked_post($user_id, $community_post_id);
    }

    /** List likes for a post (oldest first) */
    public function get_by_community_post_id(int $community_post_id) {
        if (empty($community_post_id)) return "Invalid community post ID";
        return $this->communityLikeDao->get_by_community_post_id($community_post_id);
    }

    /** List likes by a user (newest first) */
    public function get_by_user_id(int $user_id) {
        if (empty($user_id)) return "Invalid user ID";
        return $this->communityLikeDao->get_by_user_id($user_id);
    }

    /** Count likes on a post */
    public function get_like_count(int $community_post_id) {
        if (empty($community_post_id)) return "Invalid community post ID";
        return $this->communityLikeDao->get_like_count($community_post_id);
    }

    /** Likes with user info (joined) */
    public function get_likes_with_user_info(int $community_post_id) {
        if (empty($community_post_id)) return "Invalid community post ID";
        return $this->communityLikeDao->get_likes_with_user_info($community_post_id);
    }
}
