<?php

// GET all users
Flight::route('GET /users', function() {
    Flight::json(Flight::userService()->get_all());
});

// GET user by ID
Flight::route('GET /users/@id', function($id) {
    Flight::json(Flight::userService()->get_user_by_id($id));
});

// GET user by email
Flight::route('GET /users/email/@email', function($email) {
    Flight::json(Flight::userService()->get_by_email($email));
});

// GET user by username
Flight::route('GET /users/username/@username', function($username) {
    Flight::json(Flight::userService()->get_by_username($username));
});

// CREATE new user
Flight::route('POST /users', function() {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::userService()->create_user($data));
});

// UPDATE user
Flight::route('PUT /users/@id', function($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::userService()->update_user($id, $data));
});

// DELETE user
Flight::route('DELETE /users/@id', function($id) {
    Flight::json(Flight::userService()->delete_user($id));
});
