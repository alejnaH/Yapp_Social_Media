<?php

// GET all community posts (global)
Flight::route('GET /community-posts', function() {
    Flight::json(Flight::communityPostService()->get_all_posts());
});

// GET community post by ID
Flight::route('GET /community-posts/@id', function($id) {
    Flight::json(Flight::communityPostService()->get_post_by_id((int)$id));
});

// GET posts by community ID (with user info)
Flight::route('GET /community-posts/community/@community_id', function($community_id) {
    Flight::json(
        Flight::communityPostService()->get_posts_by_community((int)$community_id)
    );
});

// CREATE new community post
Flight::route('POST /community-posts', function() {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::communityPostService()->create_post($data)
    );
});

// UPDATE community post
Flight::route('PUT /community-posts/@id', function($id) {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::communityPostService()->update_post((int)$id, $data)
    );
});

// DELETE community post
Flight::route('DELETE /community-posts/@id', function($id) {
    Flight::json(
        Flight::communityPostService()->delete_post((int)$id)
    );
});
