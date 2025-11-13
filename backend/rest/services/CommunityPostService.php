<?php
require_once __DIR__ . '/../dao/CommunityPostDao.php';

class CommunityPostService {
    private $communityPostDao;

    public function __construct() {
        $this->communityPostDao = new CommunityPostDao();
    }

    /* CREATE */
    public function create_post(array $post) {
        if (empty($post)) return "Invalid input";
        return $this->communityPostDao->create_post($post);
    }

    /* GET by ID */
    public function get_post_by_id(int $community_post_id) {
        if (empty($community_post_id)) return "Invalid community post ID";
        return $this->communityPostDao->get_post_by_id($community_post_id);
    }

    /* UPDATE */
    public function update_post(int $community_post_id, array $data) {
        if (empty($community_post_id)) return "Invalid community post ID";
        if (empty($data)) return "Invalid input";
        return $this->communityPostDao->update_post($community_post_id, $data);
    }

    /* DELETE */
    public function delete_post(int $community_post_id) {
        if (empty($community_post_id)) return "Invalid community post ID";
        return $this->communityPostDao->delete_post($community_post_id);
    }

    /* GET all posts (global) */
    public function get_all_posts() {
        return $this->communityPostDao->get_all_posts();
    }

    /* GET posts by community (with user info) */
    public function get_posts_by_community(int $community_id) {
        if (empty($community_id)) return "Invalid community ID";
        return $this->communityPostDao->get_posts_by_community($community_id);
    }
}
