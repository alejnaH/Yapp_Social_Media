<?php

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
    Flight::json(Flight::userService()->get_all());
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
    Flight::json(Flight::userService()->get_user_by_id($id));
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
    Flight::json(Flight::userService()->get_by_email($email));
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
    Flight::json(Flight::userService()->get_by_username($username));
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