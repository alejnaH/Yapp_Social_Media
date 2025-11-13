<?php

/**
 * @OA\Get(
 *     path="/community-comments/{id}",
 *     tags={"community-comments"},
 *     summary="Get a community comment by ID",
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
    Flight::json(Flight::communityCommentService()->get_comment_by_id((int)$id));
});

/**
 * @OA\Get(
 *     path="/community-comments/post/{community_post_id}",
 *     tags={"community-comments"},
 *     summary="Get community comments for a specific community post",
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
    Flight::json(Flight::communityCommentService()->get_by_community_post_id((int)$community_post_id));
});

/**
 * @OA\Get(
 *     path="/community-comments/user/{user_id}",
 *     tags={"community-comments"},
 *     summary="Get community comments made by a specific user",
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
    Flight::json(Flight::communityCommentService()->get_by_user_id((int)$user_id));
});

/**
 * @OA\Get(
 *     path="/community-comments/post/{community_post_id}/with-user",
 *     tags={"community-comments"},
 *     summary="Get community comments for a post including user info",
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
    Flight::json(Flight::communityCommentService()->get_comments_with_user_info((int)$community_post_id));
});

/**
 * @OA\Post(
 *     path="/community-comments",
 *     tags={"community-comments"},
 *     summary="Create a new community comment",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"CommunityPostID", "UserID", "Content"},
 *             @OA\Property(
 *                 property="CommunityPostID",
 *                 type="integer",
 *                 example=5,
 *                 description="ID of the community post this comment belongs to"
 *             ),
 *             @OA\Property(
 *                 property="UserID",
 *                 type="integer",
 *                 example=3,
 *                 description="ID of the user who created the community comment"
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
    $data = Flight::request()->data->getData();
    Flight::json(Flight::communityCommentService()->create_comment($data));
});

/**
 * @OA\Put(
 *     path="/community-comments/{id}",
 *     tags={"community-comments"},
 *     summary="Update an existing community comment",
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
    $data = Flight::request()->data->getData();
    Flight::json(Flight::communityCommentService()->update_comment((int)$id, $data));
});

/**
 * @OA\Delete(
 *     path="/community-comments/{id}",
 *     tags={"community-comments"},
 *     summary="Delete a community comment by ID",
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
    Flight::json(Flight::communityCommentService()->delete_comment((int)$id));
});
