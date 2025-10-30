<?php
require_once __DIR__ . '/BaseDao.php';

class CommunityDao extends BaseDao {
    public function __construct() {
        parent::__construct('Community'); // PK -> CommunityID
    }

    /* CREATE */
    public function create_community(array $c): int {
        if (!isset($c['Name'], $c['OwnerID'])) {
            throw new InvalidArgumentException("Name and OwnerID are required.");
        }
        $data = [
            'Name'        => $c['Name'],
            'Description' => $c['Description'] ?? null,
            'OwnerID'     => (int)$c['OwnerID'],
            // CreatedAt/UpdatedAt handled by DB
        ];
        return $this->insert($data); // BaseDao
    }

    /* UPDATE (allow Name/Description/OwnerID) */
    public function update_community(int $community_id, array $data): int {
        $allowed = [];
        if (isset($data['Name']))        { $allowed['Name'] = $data['Name']; }
        if (isset($data['Description'])) { $allowed['Description'] = $data['Description']; }
        if (isset($data['OwnerID']))     { $allowed['OwnerID'] = (int)$data['OwnerID']; }

        if (empty($allowed)) return 0;
        return $this->updateById($community_id, $allowed); // BaseDao
    }

    /* DELETE */
    public function delete_community(int $community_id): int {
        return $this->deleteById($community_id); // BaseDao
    }

    /* GET one (delegate to BaseDao) */
    public function get_community_by_id(int $community_id): ?array {
        return $this->getById($community_id); // BaseDao
    }

    /* GET all (custom ordering) */
    public function get_all_communities(): array {
        $stmt = $this->connection->prepare(
            "SELECT * FROM `Community` ORDER BY `CreatedAt` DESC, `CommunityID` DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(); // FETCH_ASSOC set in BaseDao
    }

    /* Communities by owner (custom ordering + filter) */
    public function get_communities_by_owner(int $ownerId): array {
        $stmt = $this->connection->prepare(
            "SELECT * FROM `Community`
             WHERE `OwnerID` = :oid
             ORDER BY `CreatedAt` DESC, `CommunityID` DESC"
        );
        $stmt->bindValue(':oid', $ownerId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /* Find by unique name (custom exact-match lookup) */
    public function get_community_by_name(string $name): ?array {
        $stmt = $this->connection->prepare(
            "SELECT * FROM `Community` WHERE `Name` = :name LIMIT 1"
        );
        $stmt->bindValue(':name', $name);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }
}
