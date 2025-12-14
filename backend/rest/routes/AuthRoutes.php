<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
Flight::group('/auth', function() {
   /**
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Register new user",
     *     description="Add a new user (name, email, password)",
     *     tags={"auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="username", type="string", example="Johny", description="Username"),
     *             @OA\Property(property="fullname", type="string", example="John", description="Fullname"),
     *             @OA\Property(property="email", type="string", example="johny@gmail.com", description="User email"),
     *             @OA\Property(property="password", type="string", example="John123", description="User password"),
     *             @OA\Property(property="password2", type="string", example="John123", description="Second user password")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User registered successfully"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    Flight::route('POST /register', function () {
        $data = Flight::request()->data->getData();
         if (!is_array($data)) {
            Flight::halt(400, 'Invalid request payload');
        }

        $response = Flight::auth_service()->register($data);
        if ($response['success']) {
            Flight::json([
                "success" => true,
                'message' => 'User registered successfully',
                'data' => $response['data']
            ]);
        } else {
            Flight::json([
                "success" => false,
                'message' => $response['error'] ?? 'Registration failed',
                'errors' => $response['errors'] ?? []
            ], 400);
        }
    });

   /**
    * @OA\Post(
    *      path="/auth/login",
    *      tags={"auth"},
    *      summary="Login to system using email and password",
    *      @OA\Response(
    *           response=200,
    *           description="Student data and JWT"
    *      ),
    *      @OA\RequestBody(
    *          description="Credentials",
    *          @OA\JsonContent(
    *              required={"email","password"},
    *              @OA\Property(property="email", type="string", example="example@gmail.com", description="User email address"),
    *              @OA\Property(property="password", type="string", example="example123", description="User password")
    *          )
    *      )
    * )
    */

   Flight::route('POST /login', function() {
    $data = json_decode(Flight::request()->getBody(), true);

    if (!is_array($data)) {
        Flight::halt(400, 'Invalid request payload');
    }

    $response = Flight::auth_service()->login($data);

    if ($response['success']) {
        Flight::json([
            "success" => true,
            'message' => 'User logged in successfully',
            'data' => $response['data']
        ]);
    } else {
        Flight::halt(401, $response['error']);
    }
});
});