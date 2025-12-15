<?php

/**
 * @OA\Info(
 *     title="Yapp API",
 *     description="Yapp backend REST API",
 *     version="1.0",
 *     @OA\Contact(
 *         email="alejna.hasanagic@stu.ibu.edu.ba",
 *         name="Web Programming"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost/Yapp/backend",
 *     description="Local API server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="ApiKey",
 *     type="apiKey",
 *     in="header",
 *     name="Authentication",
 *     description="Paste JWT token here (RAW JWT token)."
 * )
 */
