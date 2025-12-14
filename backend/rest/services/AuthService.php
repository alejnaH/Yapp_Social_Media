<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../dao/AuthDao.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class AuthService extends BaseService {
   private $auth_dao;
   public function __construct() {
       $this->auth_dao = new AuthDao();
       parent::__construct(new AuthDao);
   }

   public function get_user_by_email($email){
       return $this->auth_dao->get_user_by_email($email);
   }

   public function register($entity) {
        if (empty($entity['username']) || empty($entity['fullname']) || empty($entity['email']) || empty($entity['password']) || empty($entity['password2'])) {
            return ['success' => false, 'error' => 'Name, email, and password are required.'];
        }

        if($entity['password'] !== $entity['password2']){
            return ['success' => false, 'error' => 'Passwords dont match.'];
        }
        
        $userDao = new UserDao();
        $username_exists = $userDao->getByUsername($entity['username']);
        if ($username_exists) {
            return ['success' => false, 'error' => 'Username already taken.'];
}

        $email_exists = $this->auth_dao->get_user_by_email($entity['email']);
        if ($email_exists) {
            return ['success' => false, 'error' => 'Email already registered.'];
        }

        $data = [
                'Username' => $entity['username'],
                'FullName' => $entity['fullname'],
                'Email'    => $entity['email'],
                'Password' => password_hash($entity['password'], PASSWORD_BCRYPT),
                'Role'     => 'user'
];

$id = $this->auth_dao->insert($data);
            $entity = $this->auth_dao->getById($id);
        unset($entity['Password']);
        return ['success' => true, 'data' => $entity];
    }

   public function login($entity) {  
       if (empty($entity['email']) || empty($entity['password'])) {
           return ['success' => false, 'error' => 'Email and password are required.'];
       }

       $user = $this->auth_dao->get_user_by_email($entity['email']);
       if(!$user){
           return ['success' => false, 'error' => 'Invalid username or password.'];
       }

       if(!$user || !password_verify($entity['password'], $user['Password']))
           return ['success' => false, 'error' => 'Invalid username or password.'];

       unset($user['Password']);
       $jwt_payload = [
           'user' => $user,
           'iat' => time(),
           'exp' => time() + (60 * 60 * 24) 
       ];

       $token = JWT::encode(
           $jwt_payload,
           Config::JWT_SECRET(),
           'HS256'
       );

       return ['success' => true, 'data' => array_merge($user, ['token' => $token])];             
   }
}