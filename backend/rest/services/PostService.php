<?php
require_once __DIR__ . '/../dao/PostDao.php';

class PostService {
    private $postDao;

    public function __construct() {
        $this->postDao = new PostDao();
    }

    /* CREATE Post */
    public function create_post(array $post) {
        if (empty($post)) return "Invalid input";
        return $this->postDao->create_post($post);
    }

    /* GET single Post */
    public function get_one_post(int $post_id) {
        if (empty($post_id)) return "Invalid post ID";
        return $this->postDao->get_one_post($post_id);
    }

    /* UPDATE Post */
    public function edit_post(int $post_id, array $data) {
        if (empty($post_id)) return "Invalid post ID";
        if (empty($data))    return "Invalid input";
        return $this->postDao->edit_post($post_id, $data);
    }

    /* DELETE Post */
    public function delete_post(int $post_id) {
        if (empty($post_id)) return "Invalid post ID";
        return $this->postDao->delete_post($post_id);
    }

    /* GET all Posts (newest first) */
    public function get_all_posts() {
        return $this->postDao->get_all_posts();
    }

    /* Posts by a specific UserID */
    public function get_by_user_id(int $user_id) {
        if (empty($user_id)) return "Invalid user ID";
        return $this->postDao->getByUserId($user_id);
    }

    /* Posts with user info and like counts (optionally mark current user's likes) */
    public function get_posts_with_user_info(?int $current_user_id = null) {
        // Allow null to get list without "user_liked" resolution
        return $this->postDao->getPostsWithUserInfo($current_user_id);
    }

    /* Single post with comment and like counts */
    public function get_post_with_details(int $post_id) {
        if (empty($post_id)) return "Invalid post ID";
        return $this->postDao->getPostWithDetails($post_id);
    }

    /* Community posts (proxied to DAO method that queries CommunityPost table) */
    public function get_posts_by_community(int $community_id) {
        if (empty($community_id)) return "Invalid community ID";
        return $this->postDao->getPostsByCommunity($community_id);
    }
}
