<?php

/**
 * @OA\Post(
 *     path="/likes",
 *     tags={"likes"},
 *     summary="Add a like to a post (idempotent)",
 *     description="Adds a like from a user to a post. If the like already exists, nothing changes.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id", "post_id"},
 *             @OA\Property(
 *                 property="user_id",
 *                 type="integer",
 *                 example=3,
 *                 description="ID of the user who is liking the post"
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
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::likeService()->add_like(
            (int)$data['user_id'],
            (int)$data['post_id']
        )
    );
});

/**
 * @OA\Delete(
 *     path="/likes",
 *     tags={"likes"},
 *     summary="Remove a like from a post",
 *     description="Removes a like from a user on a post if it exists.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id", "post_id"},
 *             @OA\Property(
 *                 property="user_id",
 *                 type="integer",
 *                 example=3,
 *                 description="ID of the user whose like will be removed"
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
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::likeService()->remove_like(
            (int)$data['user_id'],
            (int)$data['post_id']
        )
    );
});

/**
 * @OA\Get(
 *     path="/likes/check/{user_id}/{post_id}",
 *     tags={"likes"},
 *     summary="Check if a user liked a post",
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
    Flight::json(
        Flight::likeService()->has_user_liked_post(
            (int)$user_id,
            (int)$post_id
        )
    );
});

/**
 * @OA\Get(
 *     path="/likes/post/{post_id}",
 *     tags={"likes"},
 *     summary="List likes for a post",
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
    Flight::json(
        Flight::likeService()->get_by_post_id((int)$post_id)
    );
});

/**
 * @OA\Get(
 *     path="/likes/user/{user_id}",
 *     tags={"likes"},
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
 *         description="Array of likes made by the given user"
 *     )
 * )
 */
Flight::route('GET /likes/user/@user_id', function($user_id) {
    Flight::json(
        Flight::likeService()->get_by_user_id((int)$user_id)
    );
});

/**
 * @OA\Get(
 *     path="/likes/count/{post_id}",
 *     tags={"likes"},
 *     summary="Get like count for a post",
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
    Flight::json(
        Flight::likeService()->get_like_count((int)$post_id)
    );
});

/**
 * @OA\Get(
 *     path="/likes/post/{post_id}/with-user",
 *     tags={"likes"},
 *     summary="List likes for a post including user info",
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
    Flight::json(
        Flight::likeService()->get_likes_with_user_info((int)$post_id)
    );
});
