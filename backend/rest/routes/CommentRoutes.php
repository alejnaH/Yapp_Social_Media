<?php

// GET comment by ID
Flight::route('GET /comments/@id', function($id) {
    Flight::json(Flight::commentService()->get_comment_by_id((int)$id));
});

// GET comments by post ID
Flight::route('GET /comments/post/@post_id', function($post_id) {
    Flight::json(Flight::commentService()->get_by_post_id((int)$post_id));
});

// GET comments by user ID
Flight::route('GET /comments/user/@user_id', function($user_id) {
    Flight::json(Flight::commentService()->get_by_user_id((int)$user_id));
});

// GET comments for a post with user info (JOIN)
Flight::route('GET /comments/post/@post_id/with-user', function($post_id) {
    Flight::json(Flight::commentService()->get_comments_with_user_info((int)$post_id));
});

// CREATE new comment
Flight::route('POST /comments', function() {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::commentService()->create_comment($data));
});

// UPDATE comment
Flight::route('PUT /comments/@id', function($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::commentService()->update_comment((int)$id, $data));
});

// DELETE comment
Flight::route('DELETE /comments/@id', function($id) {
    Flight::json(Flight::commentService()->delete_comment((int)$id));
});
