<?php
require_once __DIR__ . '/../config.php';


class BaseDao {
    protected string $table;
    protected PDO $connection;

    public function __construct(string $table) {
        $this->table = $table;
        $this->connection = Database::connect();
        // Recommended defaults:
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    /** Resolve primary key based on convention: <TableName>ID (e.g., UserID) */
    protected function primaryKeyFor(string $table): string {
        return $table . 'ID';
    }

    /** ======= READS ======= */
    public function getAll(): array {
        $sql = "SELECT * FROM `{$this->table}`";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $pk = $this->primaryKeyFor($this->table);
        $sql = "SELECT * FROM `{$this->table}` WHERE `$pk` = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /** ======= INSERT ======= */
    /**
     * Insert with associative array of column => value.
     * Returns the last insert id as int.
     */
    public function insert(array $data): int {
        if (empty($data)) {
            throw new InvalidArgumentException('insert() called with empty data.');
        }

        // Quote identifiers with backticks
        $columns = array_keys($data);
        $colList = '`' . implode('`, `', $columns) . '`';
        $phList  = ':' . implode(', :', $columns);

        $sql = "INSERT INTO `{$this->table}` ($colList) VALUES ($phList)";
        $stmt = $this->connection->prepare($sql);

        foreach ($data as $k => $v) {
            $stmt->bindValue(':' . $k, $v);
        }

        $stmt->execute();
        return (int)$this->connection->lastInsertId();
    }

    /** ======= UPDATE (public: uses $this->table) ======= */
    /**
     * Update current table by id. Returns affected row count.
     */
    public function updateById(int $id, array $data): int {
        if (empty($data)) {
            return 0; // nothing to update
        }
        $pk = $this->primaryKeyFor($this->table);

        $setParts = [];
        foreach ($data as $col => $_) {
            $setParts[] = "`$col` = :$col";
        }
        $setSql = implode(', ', $setParts);

        $sql = "UPDATE `{$this->table}` SET $setSql WHERE `$pk` = :id";
        $stmt = $this->connection->prepare($sql);

        foreach ($data as $col => $val) {
            $stmt->bindValue(':' . $col, $val);
        }
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->rowCount();
    }

    /** ======= DELETE (public: uses $this->table) ======= */
    /**
     * Delete from current table by id. Returns affected row count.
     */
    public function deleteById(int $id): int {
        $pk = $this->primaryKeyFor($this->table);
        $sql = "DELETE FROM `{$this->table}` WHERE `$pk` = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /** ======= Protected helpers (for DAOs that pass the table explicitly) ======= */
    /**
     * Generic UPDATE for an explicit $table. Returns affected row count.
     */
    protected function update(string $table, int $id, array $data): int {
        if (empty($data)) {
            return 0;
        }
        $pk = $this->primaryKeyFor($table);

        $setParts = [];
        foreach ($data as $col => $_) {
            $setParts[] = "`$col` = :$col";
        }
        $setSql = implode(', ', $setParts);

        $sql = "UPDATE `{$table}` SET $setSql WHERE `$pk` = :id";
        $stmt = $this->connection->prepare($sql);

        foreach ($data as $col => $val) {
            $stmt->bindValue(':' . $col, $val);
        }
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Generic DELETE for an explicit $table. Returns affected row count.
     */
    protected function delete(string $table, int $id): int {
        $pk = $this->primaryKeyFor($table);
        $sql = "DELETE FROM `{$table}` WHERE `$pk` = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}

?>
