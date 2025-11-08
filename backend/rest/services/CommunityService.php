<?php
require_once __DIR__ . '/../dao/CommunityDao.php';

class CommunityService {
    private $communityDao;

    public function __construct() {
        $this->communityDao = new CommunityDao();
    }

    /* CREATE */
    public function create_community(array $community) {
        if (empty($community)) return "Invalid input";
        return $this->communityDao->create_community($community);
    }

    /* GET by ID */
    public function get_community_by_id(int $community_id) {
        if (empty($community_id)) return "Invalid community ID";
        return $this->communityDao->get_community_by_id($community_id);
    }

    /* UPDATE */
    public function update_community(int $community_id, array $data) {
        if (empty($community_id)) return "Invalid community ID";
        if (empty($data)) return "Invalid input";
        return $this->communityDao->update_community($community_id, $data);
    }

    /* DELETE */
    public function delete_community(int $community_id) {
        if (empty($community_id)) return "Invalid community ID";
        return $this->communityDao->delete_community($community_id);
    }

    /* GET all */
    public function get_all_communities() {
        return $this->communityDao->get_all_communities();
    }

    /* GET by owner */
    public function get_communities_by_owner(int $owner_id) {
        if (empty($owner_id)) return "Invalid owner ID";
        return $this->communityDao->get_communities_by_owner($owner_id);
    }

    /* GET by name */
    public function get_community_by_name(string $name) {
        if (empty($name)) return "Invalid name";
        return $this->communityDao->get_community_by_name($name);
    }
}
