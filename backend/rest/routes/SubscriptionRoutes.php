<?php

// SUBSCRIBE to a community
Flight::route('POST /subscriptions', function() {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::subscriptionService()->subscribe(
            (int)$data['user_id'],
            (int)$data['community_id']
        )
    );
});

// UNSUBSCRIBE from a community
Flight::route('DELETE /subscriptions', function() {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::subscriptionService()->unsubscribe(
            (int)$data['user_id'],
            (int)$data['community_id']
        )
    );
});

// CHECK if user is subscribed
Flight::route('GET /subscriptions/check/@user_id/@community_id', function($user_id, $community_id) {
    Flight::json(
        Flight::subscriptionService()->is_subscribed(
            (int)$user_id,
            (int)$community_id
        )
    );
});

// GET all communities a user is subscribed to
Flight::route('GET /subscriptions/user/@user_id', function($user_id) {
    Flight::json(
        Flight::subscriptionService()->get_subscriptions_by_user((int)$user_id)
    );
});

// GET all subscribers for a community
Flight::route('GET /subscriptions/community/@community_id', function($community_id) {
    Flight::json(
        Flight::subscriptionService()->get_subscribers_by_community((int)$community_id)
    );
});

// COUNT subscribers of a community
Flight::route('GET /subscriptions/community/@community_id/count', function($community_id) {
    Flight::json(
        Flight::subscriptionService()->count_subscribers((int)$community_id)
    );
});

// Subscribers with user info (JOIN)
Flight::route('GET /subscriptions/community/@community_id/with-user', function($community_id) {
    Flight::json(
        Flight::subscriptionService()->get_subscribers_with_user_info((int)$community_id)
    );
});
