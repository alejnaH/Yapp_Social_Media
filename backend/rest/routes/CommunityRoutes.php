<?php

// GET all communities
Flight::route('GET /communities', function() {
    Flight::json(
        Flight::communityService()->get_all_communities()
    );
});

// GET community by ID
Flight::route('GET /communities/@id', function($id) {
    Flight::json(
        Flight::communityService()->get_community_by_id((int)$id)
    );
});

// GET communities by owner ID
Flight::route('GET /communities/owner/@owner_id', function($owner_id) {
    Flight::json(
        Flight::communityService()->get_communities_by_owner((int)$owner_id)
    );
});

// GET community by name
Flight::route('GET /communities/name/@name', function($name) {
    Flight::json(
        Flight::communityService()->get_community_by_name($name)
    );
});

// CREATE new community
Flight::route('POST /communities', function() {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::communityService()->create_community($data)
    );
});

// UPDATE community
Flight::route('PUT /communities/@id', function($id) {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::communityService()->update_community((int)$id, $data)
    );
});

// DELETE community
Flight::route('DELETE /communities/@id', function($id) {
    Flight::json(
        Flight::communityService()->delete_community((int)$id)
    );
});
