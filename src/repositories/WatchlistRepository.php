<?php

namespace App\Repositories;

use App\Config\Database;

class WatchlistRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Add a movie to user's watchlist
     *
     * @param string $userEmail User email
     * @param int $movieId Movie ID
     * @return bool True if added successfully
     */
    public function addMovie(string $userEmail, int $movieId): bool
    {
        // Check if already in watchlist to prevent duplicates
        if ($this->isMovieInWatchlist($userEmail, $movieId)) {
            return false; // Already in watchlist
        }

        $sql = "INSERT INTO movie_user (user_email, movie_id) VALUES (?, ?)";
        return $this->db->execute($sql, [$userEmail, $movieId], 'si');
    }

    /**
     * Remove a movie from user's watchlist
     *
     * @param string $userEmail User email
     * @param int $movieId Movie ID
     * @return bool True if removed successfully
     */
    public function removeMovie(string $userEmail, int $movieId): bool
    {
        $sql = "DELETE FROM movie_user WHERE user_email = ? AND movie_id = ?";
        return $this->db->execute($sql, [$userEmail, $movieId], 'si');
    }

    /**
     * Get all movies in user's watchlist
     *
     * @param string $userEmail User email
     * @return array List of movies with details
     */
    public function getUserMovies(string $userEmail): array
    {
        $sql = "SELECT m.MovieID, m.Title, m.Rating, m.ReleaseYear, m.image, m.Plot
                FROM movie_user mu
                JOIN movie m ON mu.movie_id = m.MovieID
                WHERE mu.user_email = ?
                ORDER BY m.Title ASC";

        return $this->db->query($sql, [$userEmail], 's') ?? [];
    }

    /**
     * Get movie IDs in user's watchlist
     *
     * @param string $userEmail User email
     * @return array List of movie IDs
     */
    public function getUserMovieIds(string $userEmail): array
    {
        $sql = "SELECT movie_id FROM movie_user WHERE user_email = ?";
        $results = $this->db->query($sql, [$userEmail], 's') ?? [];

        return array_column($results, 'movie_id');
    }

    /**
     * Check if a movie is in user's watchlist
     *
     * @param string $userEmail User email
     * @param int $movieId Movie ID
     * @return bool True if movie is in watchlist
     */
    public function isMovieInWatchlist(string $userEmail, int $movieId): bool
    {
        $sql = "SELECT COUNT(*) as count FROM movie_user WHERE user_email = ? AND movie_id = ?";
        $result = $this->db->queryOne($sql, [$userEmail, $movieId], 'si');

        return $result && $result['count'] > 0;
    }

    /**
     * Get watchlist count for a user
     *
     * @param string $userEmail User email
     * @return int Number of movies in watchlist
     */
    public function getWatchlistCount(string $userEmail): int
    {
        $sql = "SELECT COUNT(*) as count FROM movie_user WHERE user_email = ?";
        $result = $this->db->queryOne($sql, [$userEmail], 's');

        return $result ? (int)$result['count'] : 0;
    }

    /**
     * Clear user's entire watchlist
     *
     * @param string $userEmail User email
     * @return bool True if cleared successfully
     */
    public function clearWatchlist(string $userEmail): bool
    {
        $sql = "DELETE FROM movie_user WHERE user_email = ?";
        return $this->db->execute($sql, [$userEmail], 's');
    }

    /**
     * Get all users who have a specific movie in their watchlist
     *
     * @param int $movieId Movie ID
     * @return array List of user emails
     */
    public function getUsersWithMovie(int $movieId): array
    {
        $sql = "SELECT user_email FROM movie_user WHERE movie_id = ?";
        $results = $this->db->query($sql, [$movieId], 'i') ?? [];

        return array_column($results, 'user_email');
    }
}
