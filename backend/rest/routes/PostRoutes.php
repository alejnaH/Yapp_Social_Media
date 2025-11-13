<?php

// GET all posts (newest first)
Flight::route('GET /posts', function() {
    Flight::json(Flight::postService()->get_all_posts());
});

// GET one post by ID
Flight::route('GET /posts/@id', function($id) {
    Flight::json(Flight::postService()->get_one_post((int)$id));
});

// GET posts by user ID
Flight::route('GET /posts/user/@user_id', function($user_id) {
    Flight::json(Flight::postService()->get_by_user_id((int)$user_id));
});

// GET posts by community ID
Flight::route('GET /posts/community/@community_id', function($community_id) {
    Flight::json(Flight::postService()->get_posts_by_community((int)$community_id));
});

// GET posts with user info + like counts (optionally pass ?current_user_id=5)
Flight::route('GET /posts-with-user-info', function() {
    $current_user_id = Flight::request()->query['current_user_id'] ?? null;
    Flight::json(Flight::postService()->get_posts_with_user_info($current_user_id));
});

// GET single post with full details (comments + likes)
Flight::route('GET /posts/@id/details', function($id) {
    Flight::json(Flight::postService()->get_post_with_details((int)$id));
});

// CREATE post
Flight::route('POST /posts', function() {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::postService()->create_post($data));
});

// UPDATE post
Flight::route('PUT /posts/@id', function($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::postService()->edit_post((int)$id, $data));
});

// DELETE post
Flight::route('DELETE /posts/@id', function($id) {
    Flight::json(Flight::postService()->delete_post((int)$id));
});
