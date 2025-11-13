<?php

// ADD like (idempotent)
Flight::route('POST /likes', function() {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::likeService()->add_like(
            (int)$data['user_id'],
            (int)$data['post_id']
        )
    );
});

// REMOVE like
Flight::route('DELETE /likes', function() {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::likeService()->remove_like(
            (int)$data['user_id'],
            (int)$data['post_id']
        )
    );
});

// CHECK if user liked a post
Flight::route('GET /likes/check/@user_id/@post_id', function($user_id, $post_id) {
    Flight::json(
        Flight::likeService()->has_user_liked_post(
            (int)$user_id,
            (int)$post_id
        )
    );
});

// LIST likes for a post
Flight::route('GET /likes/post/@post_id', function($post_id) {
    Flight::json(
        Flight::likeService()->get_by_post_id((int)$post_id)
    );
});

// LIST likes by a user
Flight::route('GET /likes/user/@user_id', function($user_id) {
    Flight::json(
        Flight::likeService()->get_by_user_id((int)$user_id)
    );
});

// COUNT likes on a post
Flight::route('GET /likes/count/@post_id', function($post_id) {
    Flight::json(
        Flight::likeService()->get_like_count((int)$post_id)
    );
});

// Likes with user info (JOIN)
Flight::route('GET /likes/post/@post_id/with-user', function($post_id) {
    Flight::json(
        Flight::likeService()->get_likes_with_user_info((int)$post_id)
    );
});
