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
     *             @OA\Property(property="name", type="string", example="John Doe", description="User name"),
     *             @OA\Property(property="email", type="string", example="ima@gmail.com", description="User email"),
     *             @OA\Property(property="password", type="string", example="ima", description="User password"),
     *             @OA\Property(property="role", type="string", example="admin")
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
            Flight::halt(500, $response);
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
    *              @OA\Property(property="email", type="string", example="ima@gmail.com", description="Student email address"),
    *              @OA\Property(property="password", type="string", example="ima", description="Student password")
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