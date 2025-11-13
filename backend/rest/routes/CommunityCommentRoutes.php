<?php

// GET community comment by ID
Flight::route('GET /community-comments/@id', function($id) {
    Flight::json(Flight::communityCommentService()->get_comment_by_id((int)$id));
});

// GET community comments by community post ID
Flight::route('GET /community-comments/post/@community_post_id', function($community_post_id) {
    Flight::json(Flight::communityCommentService()->get_by_community_post_id((int)$community_post_id));
});

// GET community comments by user ID
Flight::route('GET /community-comments/user/@user_id', function($user_id) {
    Flight::json(Flight::communityCommentService()->get_by_user_id((int)$user_id));
});

// GET community comments with user info (JOIN) for a given community post
Flight::route('GET /community-comments/post/@community_post_id/with-user', function($community_post_id) {
    Flight::json(Flight::communityCommentService()->get_comments_with_user_info((int)$community_post_id));
});

// CREATE new community comment
Flight::route('POST /community-comments', function() {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::communityCommentService()->create_comment($data));
});

// UPDATE community comment
Flight::route('PUT /community-comments/@id', function($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::communityCommentService()->update_comment((int)$id, $data));
});

// DELETE community comment
Flight::route('DELETE /community-comments/@id', function($id) {
    Flight::json(Flight::communityCommentService()->delete_comment((int)$id));
});
