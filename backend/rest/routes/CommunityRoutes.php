<?php

/**
 * @OA\Get(
 *     path="/communities",
 *     tags={"communities"},
 *     summary="Get all communities",
 *     @OA\Response(
 *         response=200,
 *         description="Array of all communities ordered by creation time (newest first)"
 *     )
 * )
 */
Flight::route('GET /communities', function() {
    Flight::json(
        Flight::communityService()->get_all_communities()
    );
});

/**
 * @OA\Get(
 *     path="/communities/{id}",
 *     tags={"communities"},
 *     summary="Get a community by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Community ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns the community with the given ID"
 *     )
 * )
 */
Flight::route('GET /communities/@id', function($id) {
    Flight::json(
        Flight::communityService()->get_community_by_id((int)$id)
    );
});

/**
 * @OA\Get(
 *     path="/communities/owner/{owner_id}",
 *     tags={"communities"},
 *     summary="Get communities by owner ID",
 *     @OA\Parameter(
 *         name="owner_id",
 *         in="path",
 *         required=true,
 *         description="Owner (user) ID whose communities will be returned",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of communities owned by the given user (newest first)"
 *     )
 * )
 */
Flight::route('GET /communities/owner/@owner_id', function($owner_id) {
    Flight::json(
        Flight::communityService()->get_communities_by_owner((int)$owner_id)
    );
});

/**
 * @OA\Get(
 *     path="/communities/name/{name}",
 *     tags={"communities"},
 *     summary="Get a community by its name",
 *     @OA\Parameter(
 *         name="name",
 *         in="path",
 *         required=true,
 *         description="Exact name of the community",
 *         @OA\Schema(type="string", example="Data Science Enthusiasts")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns the community with the given name, if it exists"
 *     )
 * )
 */
Flight::route('GET /communities/name/@name', function($name) {
    Flight::json(
        Flight::communityService()->get_community_by_name($name)
    );
});

/**
 * @OA\Post(
 *     path="/communities",
 *     tags={"communities"},
 *     summary="Create a new community",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"Name", "OwnerID"},
 *             @OA\Property(
 *                 property="Name",
 *                 type="string",
 *                 example="Data Science Enthusiasts",
 *                 description="Name of the community (must be unique)"
 *             ),
 *             @OA\Property(
 *                 property="Description",
 *                 type="string",
 *                 example="A place to discuss machine learning, statistics, and data engineering.",
 *                 description="Short description of the community"
 *             ),
 *             @OA\Property(
 *                 property="OwnerID",
 *                 type="integer",
 *                 example=3,
 *                 description="User ID of the community owner/creator"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="New community created (returns created community ID or object depending on implementation)"
 *     )
 * )
 */
Flight::route('POST /communities', function() {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::communityService()->create_community($data)
    );
});

/**
 * @OA\Put(
 *     path="/communities/{id}",
 *     tags={"communities"},
 *     summary="Update an existing community",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Community ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="Name",
 *                 type="string",
 *                 example="Updated Community Name",
 *                 description="Updated community name"
 *             ),
 *             @OA\Property(
 *                 property="Description",
 *                 type="string",
 *                 example="Updated community description",
 *                 description="Updated description"
 *             ),
 *             @OA\Property(
 *                 property="OwnerID",
 *                 type="integer",
 *                 example=5,
 *                 description="Updated owner user ID"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Community updated"
 *     )
 * )
 */
Flight::route('PUT /communities/@id', function($id) {
    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::communityService()->update_community((int)$id, $data)
    );
});

/**
 * @OA\Delete(
 *     path="/communities/{id}",
 *     tags={"communities"},
 *     summary="Delete a community by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Community ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Community deleted"
 *     )
 * )
 */
Flight::route('DELETE /communities/@id', function($id) {
    Flight::json(
        Flight::communityService()->delete_community((int)$id)
    );
});
