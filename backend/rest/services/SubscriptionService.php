<?php
require_once __DIR__ . '/../dao/SubscriptionDao.php';

class SubscriptionService {
    private $subscriptionDao;

    public function __construct() {
        $this->subscriptionDao = new SubscriptionDao();
    }

    /* Subscribe a user to a community (returns 1 if inserted, 0 if already exists) */
    public function subscribe(int $user_id, int $community_id) {
        if (empty($user_id)) return "Invalid user ID";
        if (empty($community_id)) return "Invalid community ID";
        return $this->subscriptionDao->subscribe($user_id, $community_id);
    }

    /* Unsubscribe (returns true if unsubscribed, false otherwise) */
    public function unsubscribe(int $user_id, int $community_id) {
        if (empty($user_id)) return "Invalid user ID";
        if (empty($community_id)) return "Invalid community ID";
        return $this->subscriptionDao->unsubscribe($user_id, $community_id);
    }

    /* Check if user is subscribed to a specific community */
    public function is_subscribed(int $user_id, int $community_id) {
        if (empty($user_id)) return "Invalid user ID";
        if (empty($community_id)) return "Invalid community ID";
        return $this->subscriptionDao->isSubscribed($user_id, $community_id);
    }

    /* Get all community IDs the user is subscribed to */
    public function get_subscriptions_by_user(int $user_id) {
        if (empty($user_id)) return "Invalid user ID";
        return $this->subscriptionDao->getSubscriptionsByUser($user_id);
    }

    /* Get all user IDs subscribed to a given community */
    public function get_subscribers_by_community(int $community_id) {
        if (empty($community_id)) return "Invalid community ID";
        return $this->subscriptionDao->getSubscribersByCommunity($community_id);
    }

    /* Count total subscribers of a community */
    public function count_subscribers(int $community_id) {
        if (empty($community_id)) return "Invalid community ID";
        return $this->subscriptionDao->countSubscribers($community_id);
    }

    /* Get subscribers with their user info (Username, FullName, etc.) */
    public function get_subscribers_with_user_info(int $community_id) {
        if (empty($community_id)) return "Invalid community ID";
        return $this->subscriptionDao->getSubscribersWithUserInfo($community_id);
    }
}
