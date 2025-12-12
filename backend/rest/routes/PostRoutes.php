<?php

/**
 * @OA\Get(
 *     path="/posts",
 *     tags={"posts"},
 *     summary="Get all posts (newest first)",
 *     @OA\Response(
 *         response=200,
 *         description="Array of all posts ordered by TimeOfPost (newest first)"
 *     )
 * )
 */
Flight::route('GET /posts', function() {
    Flight::json(Flight::postService()->get_all_posts());
});

/**
 * @OA\Get(
 *     path="/posts/{id}",
 *     tags={"posts"},
 *     summary="Get a single post by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Post ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns the post with the given ID"
 *     )
 * )
 */
Flight::route('GET /posts/@id', function($id) {
    Flight::json(Flight::postService()->get_one_post((int)$id));
});

/**
 * @OA\Get(
 *     path="/posts/user/{user_id}",
 *     tags={"posts"},
 *     summary="Get posts created by a specific user",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID whose posts will be returned",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of posts created by the given user (newest first)"
 *     )
 * )
 */
Flight::route('GET /posts/user/@user_id', function($user_id) {
    Flight::json(Flight::postService()->get_by_user_id((int)$user_id));
});

/**
 * @OA\Get(
 *     path="/posts/community/{community_id}",
 *     tags={"posts"},
 *     summary="Get community posts for a given community ID",
 *     @OA\Parameter(
 *         name="community_id",
 *         in="path",
 *         required=true,
 *         description="Community ID whose posts will be returned",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of posts from the CommunityPost table for the given community"
 *     )
 * )
 */
Flight::route('GET /posts/community/@community_id', function($community_id) {
    Flight::json(Flight::postService()->get_posts_by_community((int)$community_id));
});

/**
 * @OA\Get(
 *     path="/posts-with-user-info",
 *     tags={"posts"},
 *     summary="Get posts with user info and like counts",
 *     description="Returns posts joined with user data and like counts. Optionally mark whether the current user liked each post.",
 *     @OA\Parameter(
 *         name="current_user_id",
 *         in="query",
 *         required=false,
 *         description="If provided, marks posts where this user has liked them",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Array of posts with username, full name, like_count, and user_liked (0/1)"
 *     )
 * )
 */
Flight::route('GET /posts-with-user-info', function() {
    $current_user_id = Flight::request()->query['current_user_id'] ?? null;
    Flight::json(Flight::postService()->get_posts_with_user_info($current_user_id));
});

/**
 * @OA\Get(
 *     path="/posts/{id}/details",
 *     tags={"posts"},
 *     summary="Get a single post with full details",
 *     description="Returns a post with joined user info plus aggregated comment and like counts.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Post ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Post details including username, full name, comment_count, and like_count"
 *     )
 * )
 */
Flight::route('GET /posts/@id/details', function($id) {
    Flight::json(Flight::postService()->get_post_with_details((int)$id));
});

/**
 * @OA\Post(
 *     path="/posts",
 *     tags={"posts"},
 *     summary="Create a new post",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"UserID", "Title", "Content"},
 *             @OA\Property(
 *                 property="UserID",
 *                 type="integer",
 *                 example=3,
 *                 description="ID of the user who creates the post"
 *             ),
 *             @OA\Property(
 *                 property="Title",
 *                 type="string",
 *                 example="My first post",
 *                 description="Title of the post"
 *             ),
 *             @OA\Property(
 *                 property="Content",
 *                 type="string",
 *                 example="This is the content of my first post.",
 *                 description="Main text content of the post"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="New post created (returns created post ID or object depending on implementation)"
 *     )
 * )
 */
Flight::route('POST /posts', function() {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::postService()->create_post($data));
});

/**
 * @OA\Put(
 *     path="/posts/{id}",
 *     tags={"posts"},
 *     summary="Update an existing post",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Post ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="Title",
 *                 type="string",
 *                 example="Updated post title",
 *                 description="Updated title of the post"
 *             ),
 *             @OA\Property(
 *                 property="Content",
 *                 type="string",
 *                 example="Updated post content",
 *                 description="Updated content of the post"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Post updated"
 *     )
 * )
 */
Flight::route('PUT /posts/@id', function($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::postService()->edit_post((int)$id, $data));
});

/**
 * @OA\Delete(
 *     path="/posts/{id}",
 *     tags={"posts"},
 *     summary="Delete a post by ID",
 *     description="Users can delete their own posts. Admins can delete any post.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Post ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Post deleted"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - can only delete your own posts unless you're admin"
 *     )
 * )
 */
Flight::route('DELETE /posts/@id', function($id) {
    $currentUser = Flight::get('user');
    
    // Get the post to check ownership
    $post = Flight::postService()->get_one_post((int)$id);
    
    if (!$post) {
        Flight::halt(404, "Post not found");
    }
    
    // Allow deletion if user owns the post OR is admin
    if ($currentUser->UserID != $post['UserID'] && $currentUser->Role !== 'admin') {
        Flight::halt(403, "You can only delete your own posts");
    }
    
    Flight::json(Flight::postService()->delete_post((int)$id));
});