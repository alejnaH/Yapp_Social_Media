<?php
/**
* @OA\Info(
*     title="API",
*     description="User API",
*     version="1.0",
*     @OA\Contact(
*         email="alejna.hasanagic@stu.ibu.edu.ba",
*         name="Web Programming"
*     )
* )
*/
/**
* @OA\Server(
*     url= "http://localhost/Yapp/backend",
*     description="API server"
* )
*/
/**
* @OA\SecurityScheme(
*     securityScheme="ApiKey",
*     type="apiKey",
*     in="header",
*     name="Authentication"
* )
*/