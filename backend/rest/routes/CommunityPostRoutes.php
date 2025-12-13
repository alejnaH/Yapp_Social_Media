<?php

/**
 * @OA\Get(
 *     path="/community-posts",
 *     tags={"community-posts"},
 *     summary="Get all community posts (global feed)",
 *     security={{"ApiKey":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Array of all community posts from all communities, newest first"
 *     )
 * )
 */
Flight::route('GET /community-posts', function() {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(Flight::communityPostService()->get_all_posts());
});

/**
 * @OA\Get(
 *     path="/community-posts/{id}",
 *     tags={"community-posts"},
 *     summary="Get a community post by ID",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Community post ID",
 *         @OA\Schema(type="integer", example=12)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns the community post with the given ID"
 *     )
 * )
 */
Flight::route('GET /community-posts/@id', function($id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(Flight::communityPostService()->get_post_by_id((int)$id));
});

/**
 * @OA\Get(
 *     path="/community-posts/community/{community_id}",
 *     tags={"community-posts"},
 *     summary="Get all community posts for a specific community (with user info)",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="community_id",
 *         in="path",
 *         required=true,
 *         description="Community ID whose posts will be returned",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of posts with username & full name included"
 *     )
 * )
 */
Flight::route('GET /community-posts/community/@community_id', function($community_id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    Flight::json(
        Flight::communityPostService()->get_posts_by_community((int)$community_id)
    );
});

/**
 * @OA\Get(
 *     path="/community-posts/subscribed/{user_id}",
 *     tags={"community-posts"},
 *     summary="Get posts from communities the user is subscribed to",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of posts from subscribed communities with community names"
 *     )
 * )
 */
Flight::route('GET /community-posts/subscribed/@user_id', function($user_id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);

    $currentUser = Flight::get('user');
    if ($currentUser->Role !== 'admin' && (int)$currentUser->UserID !== (int)$user_id) {
        Flight::halt(403, "You can only view your own feed");
    }

    Flight::json(
        Flight::communityPostService()->get_posts_from_subscribed_communities((int)$user_id)
    );
});


/**
 * @OA\Post(
 *     path="/community-posts",
 *     tags={"community-posts"},
 *     summary="Create a new community post",
 *     security={{"ApiKey":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"CommunityID", "UserID", "Title", "Content"},
 *             @OA\Property(
 *                 property="CommunityID",
 *                 type="integer",
 *                 example=5,
 *                 description="ID of the community where the post is published"
 *             ),
 *             @OA\Property(
 *                 property="UserID",
 *                 type="integer",
 *                 example=3,
 *                 description="User ID of the author. For USER role, must match the authenticated user's ID. ADMIN may specify any user."
 *             ),
 *             @OA\Property(
 *                 property="Title",
 *                 type="string",
 *                 example="Big Announcement!",
 *                 description="Title of the community post"
 *             ),
 *             @OA\Property(
 *                 property="Content",
 *                 type="string",
 *                 example="We are launching a new feature soon!",
 *                 description="Main text of the post"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="New community post created"
 *     )
 * )
 */
Flight::route('POST /community-posts', function() {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);

    $data = Flight::request()->data->getData();
    $currentUser = Flight::get('user');

    if ($currentUser->Role !== 'admin' && (int)$currentUser->UserID !== (int)$data['UserID']) {
        Flight::halt(403, "You can only create posts as yourself");
    }

    Flight::json(
        Flight::communityPostService()->create_post($data)
    );
});


/**
 * @OA\Put(
 *     path="/community-posts/{id}",
 *     tags={"community-posts"},
 *     summary="Update an existing community post",
 *     security={{"ApiKey":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Community post ID",
 *         @OA\Schema(type="integer", example=12)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="Title",
 *                 type="string",
 *                 example="Updated Title!",
 *                 description="Updated community post title"
 *             ),
 *             @OA\Property(
 *                 property="Content",
 *                 type="string",
 *                 example="Updated post content...",
 *                 description="Updated community post text"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Community post updated"
 *     )
 * )
 */
Flight::route('PUT /community-posts/@id', function($id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);

    $currentUser = Flight::get('user');
    $post = Flight::communityPostService()->get_post_by_id((int)$id);

    if (!$post) {
        Flight::halt(404, "Post not found");
    }

    if ($currentUser->Role !== 'admin' && $currentUser->UserID !== $post['UserID']) {
        Flight::halt(403, "You can only update your own posts");
    }

    $data = Flight::request()->data->getData();
    Flight::json(
        Flight::communityPostService()->update_post((int)$id, $data)
    );
});


/**
 * @OA\Delete(
 *     path="/community-posts/{id}",
 *     tags={"community-posts"},
 *     summary="Delete a community post",
 *     security={{"ApiKey":{}}},
 *     description="Users can delete their own posts. Admins can delete any post.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Community post ID",
 *         @OA\Schema(type="integer", example=12)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Community post deleted"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - can only delete your own posts unless you're admin"
 *     )
 * )
 */
Flight::route('DELETE /community-posts/@id', function($id) {
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    $currentUser = Flight::get('user');
    
    // Get the post to check ownership
    $post = Flight::communityPostService()->get_post_by_id((int)$id);
    
    if (!$post) {
        Flight::halt(404, "Post not found");
    }
    
    // Allow deletion if user owns the post OR is admin
    if ($currentUser->UserID != $post['UserID'] && $currentUser->Role !== 'admin') {
        Flight::halt(403, "You can only delete your own posts");
    }
    
    Flight::json(
        Flight::communityPostService()->delete_post((int)$id)
    );
});