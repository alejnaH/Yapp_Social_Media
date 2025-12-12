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
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
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
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
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
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
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
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(Flight::userService()->get_by_username($username));
});
/**
 * @OA\Post(
 *     path="/users",
 *     tags={"users"},
 *     summary="Create a new user",
 *     description="Creates a new user. Password will be hashed in the frontend or before saving.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"Username", "Email", "Password", "FullName"},
 *             @OA\Property(property="Username", type="string", example="john_doe"),
 *             @OA\Property(property="Email", type="string", example="john@gmail.com"),
 *             @OA\Property(property="Password", type="string", example="hashed_password_here"),
 *             @OA\Property(property="FullName", type="string", example="John Doe"),
 *             @OA\Property(property="Role", type="string", example="user")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns the ID of the newly created user"
 *     )
 * )
 */
Flight::route('POST /users', function() {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::userService()->create_user($data));
});

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
 *             @OA\Property(property="Role", type="string", example="admin")
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
    
    // If the current user is not the same as the ID, block
    if ($currentUser->UserID != $id) {
        Flight::halt(403, "You can only edit your own profile.");
    }
    
    $data = Flight::request()->data->getData();
    Flight::json(Flight::userService()->update_user($id, $data));
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