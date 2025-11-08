<?php
require_once __DIR__ . '/../dao/LikeDao.php';

class LikeService {
    private $likeDao;

    public function __construct() {
        $this->likeDao = new LikeDao();
    }

    /* Add a like (idempotent: returns 1 if inserted, 0 if already existed) */
    public function add_like(int $user_id, int $post_id) {
        if (empty($user_id)) return "Invalid user ID";
        if (empty($post_id)) return "Invalid post ID";
        return $this->likeDao->add_like($user_id, $post_id);
    }

    /* Remove a like (returns number of affected rows) */
    public function remove_like(int $user_id, int $post_id) {
        if (empty($user_id)) return "Invalid user ID";
        if (empty($post_id)) return "Invalid post ID";
        return $this->likeDao->remove_like($user_id, $post_id);
    }

    /* Check if user has liked the post */
    public function has_user_liked_post(int $user_id, int $post_id) {
        if (empty($user_id)) return "Invalid user ID";
        if (empty($post_id)) return "Invalid post ID";
        return $this->likeDao->has_user_liked_post($user_id, $post_id);
    }

    /* Get all likes for a specific post */
    public function get_by_post_id(int $post_id) {
        if (empty($post_id)) return "Invalid post ID";
        return $this->likeDao->get_by_post_id($post_id);
    }

    /* Get all likes made by a specific user */
    public function get_by_user_id(int $user_id) {
        if (empty($user_id)) return "Invalid user ID";
        return $this->likeDao->get_by_user_id($user_id);
    }

    /* Get total like count for a post */
    public function get_like_count(int $post_id) {
        if (empty($post_id)) return "Invalid post ID";
        return $this->likeDao->get_like_count($post_id);
    }

    /* Get likes with user info (joined with User table) */
    public function get_likes_with_user_info(int $post_id) {
        if (empty($post_id)) return "Invalid post ID";
        return $this->likeDao->get_likes_with_user_info($post_id);
    }
}
