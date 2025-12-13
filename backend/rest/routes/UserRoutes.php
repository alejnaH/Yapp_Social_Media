<?php
/** I added this function because it:
 * Removes sensitive fields from user objects before returning them in API responses.
 * This prevents exposure of confidential data such as password hashes.
 * Used for all user-related GET endpoints.
 */function sanitize_user($u) {
    if (!$u) return $u;

    // remove sensitive fields
    unset($u['Password']);

    return $u;
}

function sanitize_users($users) {
    if (!is_array($users)) return $users;

    // if it's a list of users
    $is_list = array_keys($users) === range(0, count($users) - 1);
    if ($is_list) {
        return array_map('sanitize_user', $users);
    }

    // single user associative array
    return sanitize_user($users);
}

/**
 * @OA\Get(
 *     path="/users",
 *     tags={"users"},
 *     summary="Get all users",
 *     description="Returns a list of all users in the system.",
 *     @OA\Response(
 *         response=200,
 *         description="Array of user objects"
 *     )
 * )
 */
Flight::route('GET /users', function() {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $users = Flight::userService()->get_all();
    Flight::json(sanitize_users($users));

});

/**
 * @OA\Get(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Get user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User object or null if not found"
 *     )
 * )
 */
Flight::route('GET /users/@id', function($id) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $user = Flight::userService()->get_user_by_id((int)$id);
    Flight::json(sanitize_user($user));

});

/**
 * @OA\Get(
 *     path="/users/email/{email}",
 *     tags={"users"},
 *     summary="Get user by email",
 *     @OA\Parameter(
 *         name="email",
 *         in="path",
 *         required=true,
 *         description="Email address of the user",
 *         @OA\Schema(type="string", example="example@gmail.com")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User object or null"
 *     )
 * )
 */
Flight::route('GET /users/email/@email', function($email) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $user = Flight::userService()->get_by_email($email);
    Flight::json(sanitize_user($user));

});
/**
 * @OA\Get(
 *     path="/users/username/{username}",
 *     tags={"users"},
 *     summary="Get user by username",
 *     @OA\Parameter(
 *         name="username",
 *         in="path",
 *         required=true,
 *         description="Username",
 *         @OA\Schema(type="string", example="ilma_sljivo")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User object or null"
 *     )
 * )
 */
Flight::route('GET /users/username/@username', function($username) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $user = Flight::userService()->get_by_username($username);
    Flight::json(sanitize_user($user));

});

//Flight::route('POST /users', function() {
   // $data = Flight::request()->data->getData();
    //Flight::json(Flight::userService()->create_user($data));
//});

/**
 * @OA\Put(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Update a user",
 *     description="Updates user fields such as username, email, or full name.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the user to update",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="Username", type="string", example="updated_username"),
 *             @OA\Property(property="Email", type="string", example="updated@gmail.com"),
 *             @OA\Property(property="FullName", type="string", example="Updated Full Name"),
 
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Number of affected rows (0 or 1)"
 *     )
 * )
 */
Flight::route('PUT /users/@id', function($id) {
    $currentUser = Flight::get('user'); // from JWT

    // Owner-only edit
    if ((int)$currentUser->UserID !== (int)$id) {
        Flight::halt(403, "You can only edit your own profile.");
    }

    $data = Flight::request()->data->getData();

    // SECURITY: Never allow a regular user to update Role (or Password) from this endpoint
    unset($data['Role']);
    unset($data['Password']);

    Flight::json(Flight::userService()->update_user((int)$id, $data));
});


/**
 * @OA\Delete(
 *     path="/users/{id}",
 *     tags={"users"},
 *     summary="Delete a user",
 *     description="Deletes a user by ID.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the user to delete",
 *         @OA\Schema(type="integer", example=7)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Number of deleted rows (0 or 1)"
 *     )
 * )
 */
Flight::route('DELETE /users/@id', function($id) {
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::json(Flight::userService()->delete_user($id));
});