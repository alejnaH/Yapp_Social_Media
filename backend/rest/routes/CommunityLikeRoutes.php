<?php

/**
 * @OA\Post(
 *     path="/community-likes",
 *     tags={"community-likes"},
 *     summary="Add a like to a community post (idempotent)",
 *     description="Adds a like from a user to a community post. If the like already exists, nothing changes.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id", "community_post_id"},
 *             @OA\Property(
 *                 property="user_id",
 *                 type="integer",
 *                 example=3,
 *                 description="ID of the user who is liking the community post"
 *             ),
 *             @OA\Property(
 *                 property="community_post_id",
 *                 type="integer",
 *                 example=5,
 *                 description="ID of the community post that is being liked"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns 1 if a new like was created, 0 if it already existed"
 *     )
 * )
 */
Flight::route('POST /community-likes', function() {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::communityLikeService()->add_like(
            (int)$data['user_id'],
            (int)$data['community_post_id']
        )
    );
});

/**
 * @OA\Delete(
 *     path="/community-likes",
 *     tags={"community-likes"},
 *     summary="Remove a like from a community post",
 *     description="Removes a like from a user on a community post if it exists.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id", "community_post_id"},
 *             @OA\Property(
 *                 property="user_id",
 *                 type="integer",
 *                 example=3,
 *                 description="ID of the user whose like will be removed"
 *             ),
 *             @OA\Property(
 *                 property="community_post_id",
 *                 type="integer",
 *                 example=5,
 *                 description="ID of the community post from which the like is removed"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns 1 if a like was removed, 0 if there was no like"
 *     )
 * )
 */
Flight::route('DELETE /community-likes', function() {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::communityLikeService()->remove_like(
            (int)$data['user_id'],
            (int)$data['community_post_id']
        )
    );
});

/**
 * @OA\Get(
 *     path="/community-likes/check/{user_id}/{community_post_id}",
 *     tags={"community-likes"},
 *     summary="Check if a user liked a community post",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID to check",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Parameter(
 *         name="community_post_id",
 *         in="path",
 *         required=true,
 *         description="Community post ID to check",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Boolean result: true if user liked the post, false otherwise"
 *     )
 * )
 */
Flight::route('GET /community-likes/check/@user_id/@community_post_id', function($user_id, $community_post_id) {
    Flight::json(
        Flight::communityLikeService()->has_user_liked_post(
            (int)$user_id,
            (int)$community_post_id
        )
    );
});

/**
 * @OA\Get(
 *     path="/community-likes/post/{community_post_id}",
 *     tags={"community-likes"},
 *     summary="List likes for a given community post",
 *     @OA\Parameter(
 *         name="community_post_id",
 *         in="path",
 *         required=true,
 *         description="Community post ID whose likes will be returned",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of likes for the given community post"
 *     )
 * )
 */
Flight::route('GET /community-likes/post/@community_post_id', function($community_post_id) {
    Flight::json(
        Flight::communityLikeService()->get_by_community_post_id((int)$community_post_id)
    );
});

/**
 * @OA\Get(
 *     path="/community-likes/user/{user_id}",
 *     tags={"community-likes"},
 *     summary="List likes made by a specific user",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID whose likes will be returned",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of likes made by the given user across community posts"
 *     )
 * )
 */
Flight::route('GET /community-likes/user/@user_id', function($user_id) {
    Flight::json(
        Flight::communityLikeService()->get_by_user_id((int)$user_id)
    );
});

/**
 * @OA\Get(
 *     path="/community-likes/count/{community_post_id}",
 *     tags={"community-likes"},
 *     summary="Get like count for a community post",
 *     @OA\Parameter(
 *         name="community_post_id",
 *         in="path",
 *         required=true,
 *         description="Community post ID whose like count will be returned",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Number of likes for the given community post"
 *     )
 * )
 */
Flight::route('GET /community-likes/count/@community_post_id', function($community_post_id) {
    Flight::json(
        Flight::communityLikeService()->get_like_count((int)$community_post_id)
    );
});

/**
 * @OA\Get(
 *     path="/community-likes/post/{community_post_id}/with-user",
 *     tags={"community-likes"},
 *     summary="List likes for a community post including user info",
 *     @OA\Parameter(
 *         name="community_post_id",
 *         in="path",
 *         required=true,
 *         description="Community post ID whose likes (with user info) will be returned",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of likes joined with user information (e.g. username, full name)"
 *     )
 * )
 */
Flight::route('GET /community-likes/post/@community_post_id/with-user', function($community_post_id) {
    Flight::json(
        Flight::communityLikeService()->get_likes_with_user_info((int)$community_post_id)
    );
});
