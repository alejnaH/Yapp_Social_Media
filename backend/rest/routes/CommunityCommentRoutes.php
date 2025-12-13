<?php

/**
 * @OA\Get(
 *     path="/community-comments/{id}",
 *     tags={"community-comments"},
 *     summary="Get a community comment by ID",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Community comment ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns the community comment with the given ID"
 *     )
 * )
 */
Flight::route('GET /community-comments/@id', function($id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(Flight::communityCommentService()->get_comment_by_id((int)$id));
});

/**
 * @OA\Get(
 *     path="/community-comments/post/{community_post_id}",
 *     tags={"community-comments"},
 *     summary="Get community comments for a specific community post",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="community_post_id",
 *         in="path",
 *         required=true,
 *         description="Community post ID whose comments will be returned",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of community comments for the given community post"
 *     )
 * )
 */
Flight::route('GET /community-comments/post/@community_post_id', function($community_post_id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(Flight::communityCommentService()->get_by_community_post_id((int)$community_post_id));
});

/**
 * @OA\Get(
 *     path="/community-comments/user/{user_id}",
 *     tags={"community-comments"},
 *     summary="Get community comments made by a specific user",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID whose community comments will be returned",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of community comments created by the given user"
 *     )
 * )
 */
Flight::route('GET /community-comments/user/@user_id', function($user_id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);

    $currentUser = Flight::get('user');

    if ($currentUser->Role !== 'admin' && (int)$currentUser->UserID !== (int)$user_id) {
        Flight::halt(403, "You can only view your own comments");
    }

    Flight::json(Flight::communityCommentService()->get_by_user_id((int)$user_id));
});


/**
 * @OA\Get(
 *     path="/community-comments/post/{community_post_id}/with-user",
 *     tags={"community-comments"},
 *     summary="Get community comments for a post including user info",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="community_post_id",
 *         in="path",
 *         required=true,
 *         description="Community post ID whose comments (with user info) will be returned",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of community comments with joined user information (e.g. username, full name)"
 *     )
 * )
 */
Flight::route('GET /community-comments/post/@community_post_id/with-user', function($community_post_id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(Flight::communityCommentService()->get_comments_with_user_info((int)$community_post_id));
});

/**
 * @OA\Post(
 *     path="/community-comments",
 *     tags={"community-comments"},
 *     summary="Create a new community comment",
 *     security={{"ApiKey":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"CommunityPostID", "Content"},
 *             @OA\Property(
 *                 property="CommunityPostID",
 *                 type="integer",
 *                 example=5,
 *                 description="ID of the community post this comment belongs to"
 *             ),
 *             @OA\Property(
 *                 property="Content",
 *                 type="string",
 *                 example="I really like this community post!",
 *                 description="Text content of the community comment"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="New community comment created"
 *     )
 * )
 */
Flight::route('POST /community-comments', function() {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);

    $currentUser = Flight::get('user');
    $data = Flight::request()->data->getData();

    unset($data['UserID']);
    $data['UserID'] = (int)$currentUser->UserID;

    Flight::json(Flight::communityCommentService()->create_comment($data));
});


/**
 * @OA\Put(
 *     path="/community-comments/{id}",
 *     tags={"community-comments"},
 *     summary="Update an existing community comment",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Community comment ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="Content",
 *                 type="string",
 *                 example="Edited community comment content",
 *                 description="Updated text content of the community comment"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Community comment updated"
 *     )
 * )
 */
Flight::route('PUT /community-comments/@id', function($id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);

    $currentUser = Flight::get('user');
    $comment = Flight::communityCommentService()->get_comment_by_id((int)$id);
    if (!$comment) Flight::halt(404, "Comment not found");

    if ($currentUser->UserID != $comment['UserID'] && $currentUser->Role !== 'admin') {
        Flight::halt(403, "You can only edit your own comments");
    }

    $data = Flight::request()->data->getData();
    unset($data['UserID']); // extra safety

    Flight::json(Flight::communityCommentService()->update_comment((int)$id, $data));
});


/**
 * @OA\Delete(
 *     path="/community-comments/{id}",
 *     tags={"community-comments"},
 *     summary="Delete a community comment by ID",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Community comment ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Community comment deleted"
 *     )
 * )
 */
Flight::route('DELETE /community-comments/@id', function($id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);

    $currentUser = Flight::get('user');
    $comment = Flight::communityCommentService()->get_comment_by_id((int)$id);
    if (!$comment) Flight::halt(404, "Comment not found");

    if ($currentUser->UserID != $comment['UserID'] && $currentUser->Role !== 'admin') {
        Flight::halt(403, "You can only delete your own comments");
    }

    Flight::json(Flight::communityCommentService()->delete_comment((int)$id));
});
