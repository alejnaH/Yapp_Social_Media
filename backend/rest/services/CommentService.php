<?php
require_once __DIR__ . '/../dao/CommentDao.php';

class CommentService {
    private $commentDao;

    public function __construct() {
        $this->commentDao = new CommentDao();
    }

    /* CREATE */
    public function create_comment(array $comment) {
        if (empty($comment)) return "Invalid input";
        return $this->commentDao->create_comment($comment);
    }

    /* GET by ID */
    public function get_comment_by_id(int $comment_id) {
        if (empty($comment_id)) return "Invalid comment ID";
        return $this->commentDao->get_comment_by_id($comment_id);
    }

    /* UPDATE */
    public function update_comment(int $comment_id, array $data) {
        if (empty($comment_id)) return "Invalid comment ID";
        if (empty($data)) return "Invalid input";
        return $this->commentDao->update_comment($comment_id, $data);
    }

    /* DELETE */
    public function delete_comment(int $comment_id) {
        if (empty($comment_id)) return "Invalid comment ID";
        return $this->commentDao->delete_comment($comment_id);
    }

    /* LIST by Post */
    public function get_by_post_id(int $post_id) {
        if (empty($post_id)) return "Invalid post ID";
        return $this->commentDao->getByPostId($post_id);
    }

    /* LIST by User */
    public function get_by_user_id(int $user_id) {
        if (empty($user_id)) return "Invalid user ID";
        return $this->commentDao->getByUserId($user_id);
    }

    /* LIST with user info (join) */
    public function get_comments_with_user_info(int $post_id) {
        if (empty($post_id)) return "Invalid post ID";
        return $this->commentDao->getCommentsWithUserInfo($post_id);
    }
}
