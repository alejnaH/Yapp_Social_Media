<?php

/**
 * @OA\Post(
 *     path="/subscriptions",
 *     tags={"subscriptions"},
 *     summary="Subscribe a user to a community",
 *     description="Creates a subscription between a user and a community. Idempotent – returns 1 if a new subscription was created, 0 if it already existed.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id", "community_id"},
 *             @OA\Property(
 *                 property="user_id",
 *                 type="integer",
 *                 example=3,
 *                 description="ID of the user who is subscribing"
 *             ),
 *             @OA\Property(
 *                 property="community_id",
 *                 type="integer",
 *                 example=5,
 *                 description="ID of the community to subscribe to"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns 1 if subscription was created, 0 if it already existed"
 *     )
 * )
 */
Flight::route('POST /subscriptions', function() {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::subscriptionService()->subscribe(
            (int)$data['user_id'],
            (int)$data['community_id']
        )
    );
});

/**
 * @OA\Delete(
 *     path="/subscriptions",
 *     tags={"subscriptions"},
 *     summary="Unsubscribe a user from a community",
 *     description="Removes a subscription between a user and a community if it exists.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id", "community_id"},
 *             @OA\Property(
 *                 property="user_id",
 *                 type="integer",
 *                 example=3,
 *                 description="ID of the user who is unsubscribing"
 *             ),
 *             @OA\Property(
 *                 property="community_id",
 *                 type="integer",
 *                 example=5,
 *                 description="ID of the community to unsubscribe from"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Boolean: true if subscription was removed, false if it did not exist"
 *     )
 * )
 */
Flight::route('DELETE /subscriptions', function() {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::subscriptionService()->unsubscribe(
            (int)$data['user_id'],
            (int)$data['community_id']
        )
    );
});

/**
 * @OA\Get(
 *     path="/subscriptions/check/{user_id}/{community_id}",
 *     tags={"subscriptions"},
 *     summary="Check if a user is subscribed to a community",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Parameter(
 *         name="community_id",
 *         in="path",
 *         required=true,
 *         description="Community ID",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Boolean: true if user is subscribed, false otherwise"
 *     )
 * )
 */
Flight::route('GET /subscriptions/check/@user_id/@community_id', function($user_id, $community_id) {
    Flight::json(
        Flight::subscriptionService()->is_subscribed(
            (int)$user_id,
            (int)$community_id
        )
    );
});

/**
 * @OA\Get(
 *     path="/subscriptions/user/{user_id}",
 *     tags={"subscriptions"},
 *     summary="Get all communities a user is subscribed to",
 *     description="Returns an array of CommunityID values that the user is subscribed to.",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID whose subscriptions will be returned",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of community IDs"
 *     )
 * )
 */
Flight::route('GET /subscriptions/user/@user_id', function($user_id) {
    Flight::json(
        Flight::subscriptionService()->get_subscriptions_by_user((int)$user_id)
    );
});

/**
 * @OA\Get(
 *     path="/subscriptions/community/{community_id}",
 *     tags={"subscriptions"},
 *     summary="Get all subscribers of a community (IDs only)",
 *     description="Returns an array of UserID values for users subscribed to the given community.",
 *     @OA\Parameter(
 *         name="community_id",
 *         in="path",
 *         required=true,
 *         description="Community ID whose subscribers will be returned",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of user IDs subscribed to this community"
 *     )
 * )
 */
Flight::route('GET /subscriptions/community/@community_id', function($community_id) {
    Flight::json(
        Flight::subscriptionService()->get_subscribers_by_community((int)$community_id)
    );
});

/**
 * @OA\Get(
 *     path="/subscriptions/community/{community_id}/count",
 *     tags={"subscriptions"},
 *     summary="Get subscriber count for a community",
 *     @OA\Parameter(
 *         name="community_id",
 *         in="path",
 *         required=true,
 *         description="Community ID whose subscriber count will be returned",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Integer: number of subscribers for this community"
 *     )
 * )
 */
Flight::route('GET /subscriptions/community/@community_id/count', function($community_id) {
    Flight::json(
        Flight::subscriptionService()->count_subscribers((int)$community_id)
    );
});

/**
 * @OA\Get(
 *     path="/subscriptions/community/{community_id}/with-user",
 *     tags={"subscriptions"},
 *     summary="Get subscribers of a community with user info",
 *     description="Returns subscribed users with their basic user information (e.g. username, full name).",
 *     @OA\Parameter(
 *         name="community_id",
 *         in="path",
 *         required=true,
 *         description="Community ID whose subscribers (with user info) will be returned",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of subscribers with user details"
 *     )
 * )
 */
Flight::route('GET /subscriptions/community/@community_id/with-user', function($community_id) {
    Flight::json(
        Flight::subscriptionService()->get_subscribers_with_user_info((int)$community_id)
    );
});
