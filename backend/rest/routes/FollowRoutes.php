<?php

/**
 * @OA\Post(
 *     path="/follow",
 *     tags={"follow"},
 *     summary="Follow a user",
 *     description="Creates a follow relationship. Operation is idempotent: returns 1 if a new follow was created, 0 if it already existed.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"follower_id", "followed_id"},
 *             @OA\Property(
 *                 property="follower_id",
 *                 type="integer",
 *                 example=3,
 *                 description="ID of the user who is following"
 *             ),
 *             @OA\Property(
 *                 property="followed_id",
 *                 type="integer",
 *                 example=10,
 *                 description="ID of the user being followed"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns 1 if follow was inserted, 0 if it already existed"
 *     )
 * )
 */
Flight::route('POST /follow', function() {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::followService()->follow_user(
            (int)$data['follower_id'],
            (int)$data['followed_id']
        )
    );
});

/**
 * @OA\Delete(
 *     path="/follow",
 *     tags={"follow"},
 *     summary="Unfollow a user",
 *     description="Removes an existing follow relationship, if present.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"follower_id", "followed_id"},
 *             @OA\Property(
 *                 property="follower_id",
 *                 type="integer",
 *                 example=3,
 *                 description="ID of the user who is unfollowing"
 *             ),
 *             @OA\Property(
 *                 property="followed_id",
 *                 type="integer",
 *                 example=10,
 *                 description="ID of the user being unfollowed"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Boolean: true if a follow was removed, false if there was nothing to remove"
 *     )
 * )
 */
Flight::route('DELETE /follow', function() {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::followService()->unfollow_user(
            (int)$data['follower_id'],
            (int)$data['followed_id']
        )
    );
});

/**
 * @OA\Get(
 *     path="/follow/check/{follower_id}/{followed_id}",
 *     tags={"follow"},
 *     summary="Check if one user is following another",
 *     @OA\Parameter(
 *         name="follower_id",
 *         in="path",
 *         required=true,
 *         description="ID of the potential follower",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Parameter(
 *         name="followed_id",
 *         in="path",
 *         required=true,
 *         description="ID of the potential followed user",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Boolean: true if follower_id is following followed_id, false otherwise"
 *     )
 * )
 */
Flight::route('GET /follow/check/@follower_id/@followed_id', function($follower_id, $followed_id) {
    Flight::json(
        Flight::followService()->is_following(
            (int)$follower_id,
            (int)$followed_id
        )
    );
});

/**
 * @OA\Get(
 *     path="/follow/mutuals/{user_a}/{user_b}",
 *     tags={"follow"},
 *     summary="Check if two users are mutuals (follow each other)",
 *     @OA\Parameter(
 *         name="user_a",
 *         in="path",
 *         required=true,
 *         description="First user ID",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Parameter(
 *         name="user_b",
 *         in="path",
 *         required=true,
 *         description="Second user ID",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Boolean: true if both users follow each other, false otherwise"
 *     )
 * )
 */
Flight::route('GET /follow/mutuals/@user_a/@user_b', function($user_a, $user_b) {
    Flight::json(
        Flight::followService()->are_mutuals((int)$user_a, (int)$user_b)
    );
});

/**
 * @OA\Get(
 *     path="/follow/followers/count/{user_id}",
 *     tags={"follow"},
 *     summary="Get follower count for a user",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID whose followers are counted",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Integer: number of followers"
 *     )
 * )
 */
Flight::route('GET /follow/followers/count/@user_id', function($user_id) {
    Flight::json(
        Flight::followService()->count_followers((int)$user_id)
    );
});

/**
 * @OA\Get(
 *     path="/follow/following/count/{user_id}",
 *     tags={"follow"},
 *     summary="Get following count for a user",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID whose following list is counted",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Integer: number of users this user is following"
 *     )
 * )
 */
Flight::route('GET /follow/following/count/@user_id', function($user_id) {
    Flight::json(
        Flight::followService()->count_following((int)$user_id)
    );
});

/**
 * @OA\Get(
 *     path="/follow/followers/{user_id}",
 *     tags={"follow"},
 *     summary="Get followers of a user (IDs only)",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID whose followers will be listed",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of user IDs that follow this user"
 *     )
 * )
 */
Flight::route('GET /follow/followers/@user_id', function($user_id) {
    Flight::json(
        Flight::followService()->get_followers((int)$user_id)
    );
});

/**
 * @OA\Get(
 *     path="/follow/following/{user_id}",
 *     tags={"follow"},
 *     summary="Get users that a user is following (IDs only)",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID whose following list will be returned",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of user IDs that this user is following"
 *     )
 * )
 */
Flight::route('GET /follow/following/@user_id', function($user_id) {
    Flight::json(
        Flight::followService()->get_following((int)$user_id)
    );
});

/**
 * @OA\Get(
 *     path="/follow/followers/{user_id}/with-user",
 *     tags={"follow"},
 *     summary="Get followers of a user with user info",
 *     description="Returns followers with their basic user details (e.g. username, full name).",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID whose followers will be returned",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of followers with user information"
 *     )
 * )
 */
Flight::route('GET /follow/followers/@user_id/with-user', function($user_id) {
    Flight::json(
        Flight::followService()->get_followers_with_user_info((int)$user_id)
    );
});

/**
 * @OA\Get(
 *     path="/follow/following/{user_id}/with-user",
 *     tags={"follow"},
 *     summary="Get users that a user is following with user info",
 *     description="Returns list of users that this user follows, with their basic user details.",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID whose following list (with user info) will be returned",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of followed users with user information"
 *     )
 * )
 */
Flight::route('GET /follow/following/@user_id/with-user', function($user_id) {
    Flight::json(
        Flight::followService()->get_following_with_user_info((int)$user_id)
    );
});
