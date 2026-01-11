<?php

namespace App\Repositories;

use App\Config\Database;

class MovieRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find a movie by title
     *
     * @param string $title Movie title
     * @return array|null Movie data with director info or null if not found
     */
    public function findByTitle(string $title): ?array
    {
        $sql = "SELECT m.MovieID, m.Title, m.Rating, m.ReleaseYear, m.Plot, m.MovieLength,
                       m.image, m.trailerURL, d.FirstName as DirectorFirstName,
                       d.LastName as DirectorLastName
                FROM movie m
                JOIN director d ON m.DirectorID = d.DirectorID
                WHERE m.Title = ?
                LIMIT 1";

        return $this->db->queryOne($sql, [$title], 's');
    }

    /**
     * Find a movie by ID
     *
     * @param int $movieId Movie ID
     * @return array|null Movie data or null if not found
     */
    public function findById(int $movieId): ?array
    {
        $sql = "SELECT m.MovieID, m.Title, m.Rating, m.ReleaseYear, m.Plot, m.MovieLength,
                       m.image, m.trailerURL, d.FirstName as DirectorFirstName,
                       d.LastName as DirectorLastName
                FROM movie m
                JOIN director d ON m.DirectorID = d.DirectorID
                WHERE m.MovieID = ?
                LIMIT 1";

        return $this->db->queryOne($sql, [$movieId], 'i');
    }

    /**
     * Get trending movies (highest rated movies)
     *
     * @param int $limit Number of movies to retrieve
     * @return array List of trending movies
     */
    public function getTrending(int $limit = 10): array
    {
        $sql = "SELECT m.MovieID, m.Title, m.Rating, m.ReleaseYear, m.image
                FROM movie m
                ORDER BY m.Rating DESC
                LIMIT ?";

        return $this->db->query($sql, [$limit], 'i') ?? [];
    }

    /**
     * Get upcoming movies (future release dates or most recent)
     *
     * @param int $limit Number of movies to retrieve
     * @return array List of upcoming movies
     */
    public function getUpcoming(int $limit = 10): array
    {
        $sql = "SELECT m.MovieID, m.Title, m.Rating, m.ReleaseYear, m.image
                FROM movie m
                ORDER BY m.ReleaseYear DESC
                LIMIT ?";

        return $this->db->query($sql, [$limit], 'i') ?? [];
    }

    /**
     * Get top-rated movies
     *
     * @param int $limit Number of movies to retrieve
     * @return array List of top-rated movies
     */
    public function getTopRated(int $limit = 10): array
    {
        $sql = "SELECT m.MovieID, m.Title, m.Rating, m.ReleaseYear, m.image
                FROM movie m
                WHERE m.Rating >= 8.0
                ORDER BY m.Rating DESC
                LIMIT ?";

        return $this->db->query($sql, [$limit], 'i') ?? [];
    }

    /**
     * Search movies by title
     *
     * @param string $query Search query
     * @return array List of matching movies
     */
    public function searchByTitle(string $query): array
    {
        $searchTerm = "%{$query}%";
        $sql = "SELECT m.MovieID, m.Title, m.Rating, m.ReleaseYear, m.image
                FROM movie m
                WHERE m.Title LIKE ?
                ORDER BY m.Rating DESC
                LIMIT 50";

        return $this->db->query($sql, [$searchTerm], 's') ?? [];
    }

    /**
     * Get genres for a specific movie
     *
     * @param int $movieId Movie ID
     * @return array List of genre names
     */
    public function getMovieGenres(int $movieId): array
    {
        $sql = "SELECT g.GenreName
                FROM movie_genre mg
                JOIN genre g ON mg.genre_Id = g.GenreID
                WHERE mg.movie_id = ?";

        $results = $this->db->query($sql, [$movieId], 'i') ?? [];

        return array_column($results, 'GenreName');
    }

    /**
     * Get actors for a specific movie
     *
     * @param int $movieId Movie ID
     * @return array List of actors with their details
     */
    public function getMovieActors(int $movieId): array
    {
        $sql = "SELECT a.ActorID, a.FirstName, a.LastName, a.image
                FROM actor_movie am
                JOIN actor a ON am.ActorID = a.ActorID
                WHERE am.MovieID = ?";

        return $this->db->query($sql, [$movieId], 'i') ?? [];
    }

    /**
     * Get all movies 
     *
     * @return array List of all movies
     */
    public function getAll(): array
    {
        $sql = "SELECT m.MovieID, m.Title, m.Rating, m.ReleaseYear
                FROM movie m
                ORDER BY m.Title ASC";

        return $this->db->query($sql) ?? [];
    }

    /**
     * Get movie count
     *
     * @return int Total number of movies
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) as count FROM movie";
        $result = $this->db->queryOne($sql);

        return $result ? (int)$result['count'] : 0;
    }
}
