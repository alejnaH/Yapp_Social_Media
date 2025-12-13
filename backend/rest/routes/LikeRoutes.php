<?php

/**
 * @OA\Post(
 *     path="/likes",
 *     tags={"likes"},
 *     summary="Add a like to a post (idempotent)",
 *     security={{"ApiKey":{}}},
 *     description="Adds a like from a user to a post. If the like already exists, nothing changes.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id", "post_id"},
 *             @OA\Property(
 *                 property="user_id",
 *                 type="integer",
 *                 example=3,
 *                 description="For USER role, must match authenticated user id. ADMIN may specify any user."
 *             ),
 *             @OA\Property(
 *                 property="post_id",
 *                 type="integer",
 *                 example=15,
 *                 description="ID of the post that is being liked"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns 1 if a new like was created, 0 if it already existed"
 *     )
 * )
 */
Flight::route('POST /likes', function() {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);

    $data = Flight::request()->data->getData();
    $currentUser = Flight::get('user');

    if ($currentUser->Role !== 'admin' && (int)$currentUser->UserID !== (int)$data['user_id']) {
        Flight::json(["message" => "You can only like as yourself"], 403);
        return;
    }

    Flight::json(
        Flight::likeService()->add_like((int)$data['user_id'], (int)$data['post_id'])
    );
});

/**
 * @OA\Delete(
 *     path="/likes",
 *     tags={"likes"},
 *     summary="Remove a like from a post",
 *     security={{"ApiKey":{}}},
 *     description="Removes a like from a user on a post if it exists.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id", "post_id"},
 *             @OA\Property(
 *                 property="user_id",
 *                 type="integer",
 *                 example=3,
 *                 description="For USER role, must match authenticated user id. ADMIN may specify any user."
 *             ),
 *             @OA\Property(
 *                 property="post_id",
 *                 type="integer",
 *                 example=15,
 *                 description="ID of the post from which the like is removed"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns number of affected rows (1 if removed, 0 if no like existed)"
 *     )
 * )
 */
Flight::route('DELETE /likes', function() {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);

    $data = Flight::request()->data->getData();
    $currentUser = Flight::get('user');

    if ($currentUser->Role !== 'admin' && (int)$currentUser->UserID !== (int)$data['user_id']) {
        Flight::json(["message" => "You can only unlike as yourself"], 403);
        return;
    }

    Flight::json(
        Flight::likeService()->remove_like((int)$data['user_id'], (int)$data['post_id'])
    );
});


/**
 * @OA\Get(
 *     path="/likes/check/{user_id}/{post_id}",
 *     tags={"likes"},
 *     summary="Check if a user liked a post",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID to check",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Parameter(
 *         name="post_id",
 *         in="path",
 *         required=true,
 *         description="Post ID to check",
 *         @OA\Schema(type="integer", example=15)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Boolean result: true if user liked the post, false otherwise"
 *     )
 * )
 */
Flight::route('GET /likes/check/@user_id/@post_id', function($user_id, $post_id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);

    $currentUser = Flight::get('user');
    if ($currentUser->Role !== 'admin' && (int)$currentUser->UserID !== (int)$user_id) {
        Flight::json(["message" => "You can only check your own likes"], 403);
        return;
    }

    Flight::json(
        Flight::likeService()->has_user_liked_post((int)$user_id, (int)$post_id)
    );
});

/**
 * @OA\Get(
 *     path="/likes/post/{post_id}",
 *     tags={"likes"},
 *     summary="List likes for a post",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="post_id",
 *         in="path",
 *         required=true,
 *         description="Post ID whose likes will be returned",
 *         @OA\Schema(type="integer", example=15)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of likes for the given post"
 *     )
 * )
 */
Flight::route('GET /likes/post/@post_id', function($post_id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(
        Flight::likeService()->get_by_post_id((int)$post_id)
    );
});

/**
 * @OA\Get(
 *     path="/likes/user/{user_id}",
 *     tags={"likes"},
 *     summary="List likes made by a specific user",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID whose likes will be returned",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of likes made by the given user"
 *     )
 * )
 */
Flight::route('GET /likes/user/@user_id', function($user_id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);

    $currentUser = Flight::get('user');
    if ($currentUser->Role !== 'admin' && (int)$currentUser->UserID !== (int)$user_id) {
        Flight::json(["message" => "You can only view your own likes"], 403);
        return;
    }

    Flight::json(
        Flight::likeService()->get_by_user_id((int)$user_id)
    );
});


/**
 * @OA\Get(
 *     path="/likes/count/{post_id}",
 *     tags={"likes"},
 *     summary="Get like count for a post",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="post_id",
 *         in="path",
 *         required=true,
 *         description="Post ID whose like count will be returned",
 *         @OA\Schema(type="integer", example=15)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Number of likes for the given post"
 *     )
 * )
 */
Flight::route('GET /likes/count/@post_id', function($post_id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(
        Flight::likeService()->get_like_count((int)$post_id)
    );
});

/**
 * @OA\Get(
 *     path="/likes/post/{post_id}/with-user",
 *     tags={"likes"},
 *     summary="List likes for a post including user info",
 *     security={{"ApiKey":{}}},
 *     description="Returns users who liked the post along with their basic info.",
 *     @OA\Parameter(
 *         name="post_id",
 *         in="path",
 *         required=true,
 *         description="Post ID whose likes (with user info) will be returned",
 *         @OA\Schema(type="integer", example=15)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of likes joined with user information (e.g. username, full name)"
 *     )
 * )
 */
Flight::route('GET /likes/post/@post_id/with-user', function($post_id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(
        Flight::likeService()->get_likes_with_user_info((int)$post_id)
    );
});
