<?php

// ADD like (idempotent)
Flight::route('POST /community-likes', function() {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::communityLikeService()->add_like(
            (int)$data['user_id'],
            (int)$data['community_post_id']
        )
    );
});

// REMOVE like
Flight::route('DELETE /community-likes', function() {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::communityLikeService()->remove_like(
            (int)$data['user_id'],
            (int)$data['community_post_id']
        )
    );
});

// CHECK if user liked a post
Flight::route('GET /community-likes/check/@user_id/@community_post_id', function($user_id, $community_post_id) {
    Flight::json(
        Flight::communityLikeService()->has_user_liked_post(
            (int)$user_id,
            (int)$community_post_id
        )
    );
});

// LIST likes for a given community post
Flight::route('GET /community-likes/post/@community_post_id', function($community_post_id) {
    Flight::json(
        Flight::communityLikeService()->get_by_community_post_id((int)$community_post_id)
    );
});

// LIST likes by a user
Flight::route('GET /community-likes/user/@user_id', function($user_id) {
    Flight::json(
        Flight::communityLikeService()->get_by_user_id((int)$user_id)
    );
});

// LIKE count for a community post
Flight::route('GET /community-likes/count/@community_post_id', function($community_post_id) {
    Flight::json(
        Flight::communityLikeService()->get_like_count((int)$community_post_id)
    );
});

// Likes with user info (JOIN)
Flight::route('GET /community-likes/post/@community_post_id/with-user', function($community_post_id) {
    Flight::json(
        Flight::communityLikeService()->get_likes_with_user_info((int)$community_post_id)
    );
});
