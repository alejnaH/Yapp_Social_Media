<?php

/**
 * @OA\Get(
 *     path="/comments/{id}",
 *     tags={"comments"},
 *     summary="Get a comment by ID",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Comment ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns the comment with the given ID"
 *     )
 * )
 */
Flight::route('GET /comments/@id', function($id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(Flight::commentService()->get_comment_by_id((int)$id));
});

/**
 * @OA\Get(
 *     path="/comments/post/{post_id}",
 *     tags={"comments"},
 *     summary="Get comments for a specific post",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="post_id",
 *         in="path",
 *         required=true,
 *         description="Post ID whose comments will be returned",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of comments for the given post"
 *     )
 * )
 */
Flight::route('GET /comments/post/@post_id', function($post_id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(Flight::commentService()->get_by_post_id((int)$post_id));
});

/**
 * @OA\Get(
 *     path="/comments/user/{user_id}",
 *     tags={"comments"},
 *     summary="Get comments made by a specific user",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID whose comments will be returned",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of comments created by the given user"
 *     )
 * )
 */
Flight::route('GET /comments/user/@user_id', function($user_id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(Flight::commentService()->get_by_user_id((int)$user_id));
});

/**
 * @OA\Get(
 *     path="/comments/post/{post_id}/with-user",
 *     tags={"comments"},
 *     summary="Get comments for a post including user info",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="post_id",
 *         in="path",
 *         required=true,
 *         description="Post ID whose comments (with user info) will be returned",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of comments with joined user information (e.g. username, full name)"
 *     )
 * )
 */
Flight::route('GET /comments/post/@post_id/with-user', function($post_id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(Flight::commentService()->get_comments_with_user_info((int)$post_id));
});

/**
 * @OA\Post(
 *     path="/comments",
 *     tags={"comments"},
 *     summary="Create a new comment",
 *     security={{"ApiKey":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"PostID", "Content"},
 *             @OA\Property(property="PostID", type="integer", example=10),
 *             @OA\Property(property="Content", type="string", example="Nice post!")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Comment created")
 * )
 */
Flight::route('POST /comments', function() {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);

    $currentUser = Flight::get('user');
    $data = Flight::request()->data->getData();

    // 🔥 SECURITY FIX
    unset($data['UserID']);
    $data['UserID'] = (int)$currentUser->UserID;

    Flight::json(Flight::commentService()->create_comment($data));
});


/**
 * @OA\Put(
 *     path="/comments/{id}",
 *     tags={"comments"},
 *     summary="Update an existing comment",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Comment ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="Content",
 *                 type="string",
 *                 example="Edited comment content",
 *                 description="Updated text content of the comment"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Comment updated"
 *     )
 * )
 */
Flight::route('PUT /comments/@id', function($id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);

    $currentUser = Flight::get('user');
    $comment = Flight::commentService()->get_comment_by_id((int)$id);

    if (!$comment) Flight::halt(404, "Comment not found");

    if ($currentUser->UserID != $comment['UserID'] && $currentUser->Role !== 'admin') {
        Flight::halt(403, "You can only edit your own comments");
    }

    $data = Flight::request()->data->getData();
    unset($data['UserID']);

    Flight::json(Flight::commentService()->update_comment((int)$id, $data));
});


/**
 * @OA\Delete(
 *     path="/comments/{id}",
 *     tags={"comments"},
 *     summary="Delete a comment by ID",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Comment ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Comment deleted"
 *     )
 * )
 */
Flight::route('DELETE /comments/@id', function($id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);

    $currentUser = Flight::get('user');
    $comment = Flight::commentService()->get_comment_by_id((int)$id);

    if (!$comment) Flight::halt(404, "Comment not found");

    if ($currentUser->UserID != $comment['UserID'] && $currentUser->Role !== 'admin') {
        Flight::halt(403, "You can only delete your own comments");
    }

    Flight::json(Flight::commentService()->delete_comment((int)$id));
});

