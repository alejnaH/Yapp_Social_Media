<?php
require_once __DIR__ . '/../dao/UserDao.php';

$userDao = new UserDao();

$userDao->insert([
   'Username' => 'johndoe',
   'Email' => 'john@example.com',
   'Password' => password_hash('password123', PASSWORD_DEFAULT),
   'FullName' => 'John Doe',
]);

$users = $userDao->getAll();
print_r($users);
