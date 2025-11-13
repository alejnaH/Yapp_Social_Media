<?php

// FOLLOW user
Flight::route('POST /follow', function() {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::followService()->follow_user(
            (int)$data['follower_id'],
            (int)$data['followed_id']
        )
    );
});

// UNFOLLOW user
Flight::route('DELETE /follow', function() {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::followService()->unfollow_user(
            (int)$data['follower_id'],
            (int)$data['followed_id']
        )
    );
});

// CHECK if follower_id is following followed_id
Flight::route('GET /follow/check/@follower_id/@followed_id', function($follower_id, $followed_id) {
    Flight::json(
        Flight::followService()->is_following(
            (int)$follower_id,
            (int)$followed_id
        )
    );
});

// CHECK mutuals
Flight::route('GET /follow/mutuals/@user_a/@user_b', function($user_a, $user_b) {
    Flight::json(
        Flight::followService()->are_mutuals((int)$user_a, (int)$user_b)
    );
});

// COUNT followers of a user
Flight::route('GET /follow/followers/count/@user_id', function($user_id) {
    Flight::json(
        Flight::followService()->count_followers((int)$user_id)
    );
});

// COUNT following of a user
Flight::route('GET /follow/following/count/@user_id', function($user_id) {
    Flight::json(
        Flight::followService()->count_following((int)$user_id)
    );
});

// GET followers (IDs only)
Flight::route('GET /follow/followers/@user_id', function($user_id) {
    Flight::json(
        Flight::followService()->get_followers((int)$user_id)
    );
});

// GET following (IDs only)
Flight::route('GET /follow/following/@user_id', function($user_id) {
    Flight::json(
        Flight::followService()->get_following((int)$user_id)
    );
});

// GET followers with user info (joined)
Flight::route('GET /follow/followers/@user_id/with-user', function($user_id) {
    Flight::json(
        Flight::followService()->get_followers_with_user_info((int)$user_id)
    );
});

// GET following with user info (joined)
Flight::route('GET /follow/following/@user_id/with-user', function($user_id) {
    Flight::json(
        Flight::followService()->get_following_with_user_info((int)$user_id)
    );
});
