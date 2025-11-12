<?php
require_once __DIR__ . '/vendor/autoload.php';   // ← was ../vendor/autoload.php

require_once __DIR__ . '/rest/services/UserService.php';
Flight::register('userService', 'UserService');

require_once __DIR__ . '/rest/routes/UserRoutes.php';

Flight::route('GET /', function () { echo 'Backend alive'; });

Flight::start();
