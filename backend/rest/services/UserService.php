<?php
require_once __DIR__ . '/../dao/UserDao.php';

class UserService {
    private $userDao;

    public function __construct() {
        $this->userDao = new UserDao();
    }

    /* CREATE User */
    public function create_user(array $user) {
        if (empty($user)) {
            return "Invalid input";
        }
        return $this->userDao->create_user($user);
    }

    /* GET User by ID */
    public function get_user_by_id(int $user_id) {
        if (empty($user_id)) {
            return "Invalid user ID";
        }
        return $this->userDao->get_user_by_id($user_id);
    }

    /* UPDATE User */
    public function update_user(int $user_id, array $user) {
        if (empty($user_id)) {
            return "Invalid user ID";
        }
        if (empty($user)) {
            return "Invalid input";
        }
        return $this->userDao->update_user($user_id, $user);
    }

    /* DELETE User */
    public function delete_user(int $user_id) {
        if (empty($user_id)) {
            return "Invalid user ID";
        }
        return $this->userDao->delete_user($user_id);
    }

    /* GET by Email */
    public function get_by_email(string $email) {
        if (empty($email)) {
            return "Invalid email";
        }
        return $this->userDao->getByEmail($email);
    }

    /* GET by Username */
    public function get_by_username(string $username) {
        if (empty($username)) {
            return "Invalid username";
        }
        return $this->userDao->getByUsername($username);
    }
}
?>
