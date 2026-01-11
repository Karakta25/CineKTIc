<?php

namespace App\Config;

use mysqli;
use Exception;

class Database
{
    private static ?Database $instance = null;
    private mysqli $connection;

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
        $this->connect();
    }

    /**
     * Get database instance 
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Establish database connection
     */
    private function connect(): void
    {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $user = $_ENV['DB_USER'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';
        $database = $_ENV['DB_NAME'] ?? '';

        $this->connection = new mysqli($host, $user, $password, $database);

        if ($this->connection->connect_error) {
            logMessage("Database connection failed: " . $this->connection->connect_error, 'ERROR');
            throw new Exception("Database connection failed");
        }

        $this->connection->set_charset('utf8mb4');
    }

    /**
     * Get the mysqli connection
     */
    public function getConnection(): mysqli
    {
        return $this->connection;
    }

    /**
     * Execute a SELECT query with prepared statement
     *
     * @param string $sql SQL query with placeholders (?)
     * @param array $params Parameters to bind
     * @param string $types Parameter types (e.g., 'ssi' for string, string, int)
     * @return array|null Result array or null
     */
    public function query(string $sql, array $params = [], string $types = ''): ?array
    {
        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            logMessage("Query prepare failed: " . $this->connection->error . " | SQL: $sql", 'ERROR');
            throw new Exception("Query preparation failed");
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            logMessage("Query execution failed: " . $stmt->error . " | SQL: $sql", 'ERROR');
            $stmt->close();
            throw new Exception("Query execution failed");
        }

        $result = $stmt->get_result();

        if ($result === false) {
            $stmt->close();
            return null;
        }

        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $data;
    }

    /**
     * Execute a single SELECT query and return one row
     *
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind
     * @param string $types Parameter types
     * @return array|null Single row or null
     */
    public function queryOne(string $sql, array $params = [], string $types = ''): ?array
    {
        $result = $this->query($sql, $params, $types);

        if (empty($result)) {
            return null;
        }

        return $result[0];
    }

    /**
     * Execute an INSERT/UPDATE/DELETE query with prepared statement
     *
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind
     * @param string $types Parameter types
     * @return bool Success status
     */
    public function execute(string $sql, array $params = [], string $types = ''): bool
    {
        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            logMessage("Execute prepare failed: " . $this->connection->error . " | SQL: $sql", 'ERROR');
            throw new Exception("Statement preparation failed");
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $success = $stmt->execute();

        if (!$success) {
            logMessage("Execute failed: " . $stmt->error . " | SQL: $sql", 'ERROR');
        }

        $stmt->close();

        return $success;
    }

    /**
     * Get the ID of the last inserted row
     */
    public function lastInsertId(): int
    {
        return $this->connection->insert_id;
    }

    /**
     * Get the number of affected rows from the last operation
     */
    public function affectedRows(): int
    {
        return $this->connection->affected_rows;
    }

    /**
     * Begin a transaction
     */
    public function beginTransaction(): bool
    {
        return $this->connection->begin_transaction();
    }

    /**
     * Commit a transaction
     */
    public function commit(): bool
    {
        return $this->connection->commit();
    }

    /**
     * Rollback a transaction
     */
    public function rollback(): bool
    {
        return $this->connection->rollback();
    }

    /**
     * Escape a string for use in queries
     */
    public function escape(string $value): string
    {
        return $this->connection->real_escape_string($value);
    }

    /**
     * Close the database connection
     */
    public function close(): void
    {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    /**
     * Prevent cloning of the instance
     */
    private function __clone() {}

    /**
     * Prevent unserialization of the instance
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    /**
     * Close connection on destruct
     */
    public function __destruct()
    {
        $this->close();
    }
}
