<?php

namespace App\Repositories;

use App\Config\Database;

class UserRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find a user by email
     *
     * @param string $email User email
     * @return array|null User data or null if not found
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        return $this->db->queryOne($sql, [$email], 's');
    }

    /**
     * Check if a user exists with the given email
     *
     * @param string $email User email
     * @return bool True if user exists, false otherwise
     */
    public function exists(string $email): bool
    {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = ?";
        $result = $this->db->queryOne($sql, [$email], 's');

        return $result && $result['count'] > 0;
    }

    /**
     * Create a new user
     *
     * @param string $firstName First name
     * @param string $lastName Last name
     * @param string $email Email address
     * @param string $passwordHash Hashed password
     * @return bool True if created successfully
     */
    public function create(string $firstName, string $lastName, string $email, string $passwordHash): bool
    {
        $sql = "INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
        return $this->db->execute($sql, [$firstName, $lastName, $email, $passwordHash], 'ssss');
    }

    /**
     * Update user password
     *
     * @param string $email User email
     * @param string $passwordHash New hashed password
     * @return bool True if updated successfully
     */
    public function updatePassword(string $email, string $passwordHash): bool
    {
        $sql = "UPDATE users SET password = ? WHERE email = ?";
        return $this->db->execute($sql, [$passwordHash, $email], 'ss');
    }

    /**
     * Get all users
     *
     * @return array List of all users
     */
    public function getAll(): array
    {
        $sql = "SELECT first_name, last_name, email FROM users";
        return $this->db->query($sql) ?? [];
    }

    /**
     * Delete a user by email
     *
     * @param string $email User email
     * @return bool True if deleted successfully
     */
    public function delete(string $email): bool
    {
        $sql = "DELETE FROM users WHERE email = ?";
        return $this->db->execute($sql, [$email], 's');
    }

    /**
     * Update user profile information
     *
     * @param string $email User email
     * @param string $firstName New first name
     * @param string $lastName New last name
     * @return bool True if updated successfully
     */
    public function updateProfile(string $email, string $firstName, string $lastName): bool
    {
        $sql = "UPDATE users SET first_name = ?, last_name = ? WHERE email = ?";
        return $this->db->execute($sql, [$firstName, $lastName, $email], 'sss');
    }
}
