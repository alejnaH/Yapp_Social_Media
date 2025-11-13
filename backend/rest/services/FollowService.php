<?php
require_once __DIR__ . '/../dao/FollowDao.php';

class FollowService {
    private $followDao;

    public function __construct() {
        $this->followDao = new FollowDao();
    }

    /* Follow a user (idempotent: 1 if inserted, 0 if already exists) */
    public function follow_user(int $follower_id, int $followed_id) {
        if (empty($follower_id)) return "Invalid follower ID";
        if (empty($followed_id))  return "Invalid followed ID";
        return $this->followDao->follow_user($follower_id, $followed_id);
    }

    /* Unfollow a user (true if removed, false if not following) */
    public function unfollow_user(int $follower_id, int $followed_id) {
        if (empty($follower_id)) return "Invalid follower ID";
        if (empty($followed_id))  return "Invalid followed ID";
        return $this->followDao->unfollow_user($follower_id, $followed_id);
    }

    /* Check if follower_id is following followed_id */
    public function is_following(int $follower_id, int $followed_id) {
        if (empty($follower_id)) return "Invalid follower ID";
        if (empty($followed_id))  return "Invalid followed ID";
        return $this->followDao->is_following($follower_id, $followed_id);
    }

    /* Count followers of a user */
    public function count_followers(int $user_id) {
        if (empty($user_id)) return "Invalid user ID";
        return $this->followDao->count_followers($user_id);
    }

    /* Count following of a user */
    public function count_following(int $user_id) {
        if (empty($user_id)) return "Invalid user ID";
        return $this->followDao->count_following($user_id);
    }

    /* Get follower IDs of a user */
    public function get_followers(int $user_id) {
        if (empty($user_id)) return "Invalid user ID";
        return $this->followDao->get_followers($user_id);
    }

    /* Get following IDs of a user */
    public function get_following(int $user_id) {
        if (empty($user_id)) return "Invalid user ID";
        return $this->followDao->get_following($user_id);
    }

    /* Followers with user info */
    public function get_followers_with_user_info(int $user_id) {
        if (empty($user_id)) return "Invalid user ID";
        return $this->followDao->get_followers_with_user_info($user_id);
    }

    /* Following with user info */
    public function get_following_with_user_info(int $user_id) {
        if (empty($user_id)) return "Invalid user ID";
        return $this->followDao->get_following_with_user_info($user_id);
    }

    /* Check if two users follow each other (mutuals) */
    public function are_mutuals(int $user_a, int $user_b) {
        if (empty($user_a) || empty($user_b)) return "Invalid user IDs";
        return $this->followDao->are_mutuals($user_a, $user_b);
    }
}
