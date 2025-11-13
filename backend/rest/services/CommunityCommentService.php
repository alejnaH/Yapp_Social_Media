<?php
require_once __DIR__ . '/../dao/CommunityCommentDao.php';

class CommunityCommentService {
    private $communityCommentDao;

    public function __construct() {
        $this->communityCommentDao = new CommunityCommentDao();
    }

    /* CREATE */
    public function create_comment(array $comment) {
        if (empty($comment)) return "Invalid input";
        return $this->communityCommentDao->create_comment($comment);
    }

    /* GET by ID */
    public function get_comment_by_id(int $comment_id) {
        if (empty($comment_id)) return "Invalid comment ID";
        return $this->communityCommentDao->get_comment_by_id($comment_id);
    }

    /* UPDATE */
    public function update_comment(int $comment_id, array $data) {
        if (empty($comment_id)) return "Invalid comment ID";
        if (empty($data)) return "Invalid input";
        return $this->communityCommentDao->update_comment($comment_id, $data);
    }

    /* DELETE */
    public function delete_comment(int $comment_id) {
        if (empty($comment_id)) return "Invalid comment ID";
        return $this->communityCommentDao->delete_comment($comment_id);
    }

    /* LIST by CommunityPostID */
    public function get_by_community_post_id(int $community_post_id) {
        if (empty($community_post_id)) return "Invalid community post ID";
        return $this->communityCommentDao->getByCommunityPostId($community_post_id);
    }

    /* LIST by UserID */
    public function get_by_user_id(int $user_id) {
        if (empty($user_id)) return "Invalid user ID";
        return $this->communityCommentDao->getByUserId($user_id);
    }

    /* LIST with user info (joined) */
    public function get_comments_with_user_info(int $community_post_id) {
        if (empty($community_post_id)) return "Invalid community post ID";
        return $this->communityCommentDao->getCommentsWithUserInfo($community_post_id);
    }
}
