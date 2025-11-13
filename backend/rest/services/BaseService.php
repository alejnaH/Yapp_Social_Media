<?php
/* Lab material code (fixed) */
require_once __DIR__ . '/../dao/BaseDao.php';

class BaseService {
    protected $dao;

    public function __construct($dao) {
        $this->dao = $dao;
    }

    public function getAll() {
        return $this->dao->getAll();
    }

    public function getById(int $id) {
        return $this->dao->getById($id);
    }

    public function create(array $data) {
        return $this->dao->insert($data);
    }

    public function update(int $id, array $data) {
        return $this->dao->updateById($id, $data);
    }

    public function delete(int $id) {
        return $this->dao->deleteById($id);
    }
}
?>
