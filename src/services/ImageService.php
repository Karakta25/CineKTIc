<?php

namespace App\Services;

class ImageService
{
    private string $basePath;
    private string $publicPath;

    public function __construct()
    {
        $this->basePath = BASE_PATH . '/storage/images';
        $this->publicPath = BASE_PATH . '/public/assets/images';
    }

    /**
     * Save trending movies images
     *
     * @param array $movies Array of movies with image data
     * @return int Number of images saved
     */
    public function saveTrendingImages(array $movies): int
    {
        $targetDir = $this->publicPath . '/trending';
        return $this->saveImages($movies, $targetDir);
    }

    /**
     * Save upcoming movies images
     *
     * @param array $movies Array of movies with image data
     * @return int Number of images saved
     */
    public function saveUpcomingImages(array $movies): int
    {
        $targetDir = $this->publicPath . '/upcoming';
        return $this->saveImages($movies, $targetDir);
    }

    /**
     * Save top-rated movies images
     *
     * @param array $movies Array of movies with image data
     * @return int Number of images saved
     */
    public function saveTopRatedImages(array $movies): int
    {
        $targetDir = $this->publicPath . '/topRated';
        return $this->saveImages($movies, $targetDir);
    }

    /**
     * Save images for watchlist movies
     *
     * @param string $userEmail User email (for user-specific directory)
     * @param array $movies Array of movies with image data
     * @return int Number of images saved
     */
    public function saveWatchlistImages(string $userEmail, array $movies): int
    {
        $safeEmail = $this->sanitizeFilename($userEmail);
        $targetDir = $this->basePath . '/watchlist/' . $safeEmail;
        return $this->saveImages($movies, $targetDir);
    }

    /**
     * Clear watchlist images for a user
     *
     * @param string $userEmail User email
     * @return bool True if cleared successfully
     */
    public function clearWatchlistImages(string $userEmail): bool
    {
        $safeEmail = $this->sanitizeFilename($userEmail);
        $targetDir = $this->basePath . '/watchlist/' . $safeEmail;

        if (!is_dir($targetDir)) {
            return true; // Nothing to clear
        }

        try {
            $files = glob($targetDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            return true;
        } catch (\Exception $e) {
            logMessage("Failed to clear watchlist images: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * Save a single movie image
     *
     * @param string $title Movie title
     * @param string $imageData Binary image data
     * @param string $directory Target directory (default: public images)
     * @return string|null Path to saved image or null on failure
     */
    public function saveMovieImage(string $title, string $imageData, string $directory = null): ?string
    {
        $targetDir = $directory ?? $this->publicPath;

        if (!$this->ensureDirectoryExists($targetDir)) {
            return null;
        }

        $safeTitle = $this->sanitizeFilename($title);
        $filePath = $targetDir . '/' . $safeTitle . '.jpg';

        try {
            if (file_put_contents($filePath, $imageData) !== false) {
                return $filePath;
            }
            return null;
        } catch (\Exception $e) {
            logMessage("Failed to save image for '{$title}': " . $e->getMessage(), 'ERROR');
            return null;
        }
    }

    /**
     * Get image path for a movie
     *
     * @param string $title Movie title
     * @param string $type Image type (trending, upcoming, topRated, watchlist)
     * @param string|null $userEmail User email (required for watchlist)
     * @return string|null Image path or null if not found
     */
    public function getImagePath(string $title, string $type = 'default', ?string $userEmail = null): ?string
    {
        $safeTitle = $this->sanitizeFilename($title);

        switch ($type) {
            case 'trending':
                $path = $this->publicPath . '/trending/' . $safeTitle . '.jpg';
                break;
            case 'upcoming':
                $path = $this->publicPath . '/upcoming/' . $safeTitle . '.jpg';
                break;
            case 'topRated':
                $path = $this->publicPath . '/topRated/' . $safeTitle . '.jpg';
                break;
            case 'watchlist':
                if (!$userEmail) {
                    return null;
                }
                $safeEmail = $this->sanitizeFilename($userEmail);
                $path = $this->basePath . '/watchlist/' . $safeEmail . '/' . $safeTitle . '.jpg';
                break;
            default:
                $path = $this->publicPath . '/' . $safeTitle . '.jpg';
        }

        return file_exists($path) ? $path : null;
    }

    /**
     * Delete a movie image
     *
     * @param string $title Movie title
     * @param string $directory Directory containing the image
     * @return bool True if deleted successfully
     */
    public function deleteImage(string $title, string $directory): bool
    {
        $safeTitle = $this->sanitizeFilename($title);
        $filePath = $directory . '/' . $safeTitle . '.jpg';

        if (file_exists($filePath)) {
            try {
                return unlink($filePath);
            } catch (\Exception $e) {
                logMessage("Failed to delete image '{$title}': " . $e->getMessage(), 'ERROR');
                return false;
            }
        }

        return true; // File doesn't exist, consider it deleted
    }

    /**
     * Save multiple images
     *
     * @param array $movies Array of movies with Title and image fields
     * @param string $targetDir Target directory
     * @return int Number of images saved
     */
    private function saveImages(array $movies, string $targetDir): int
    {
        if (!$this->ensureDirectoryExists($targetDir)) {
            return 0;
        }

        $count = 0;

        foreach ($movies as $movie) {
            if (!isset($movie['Title']) || !isset($movie['image'])) {
                continue;
            }

            $title = $movie['Title'];
            $imageData = $movie['image'];

            if ($this->saveMovieImage($title, $imageData, $targetDir)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Ensure directory exists, create if it doesn't
     *
     * @param string $directory Directory path
     * @return bool True if directory exists or was created
     */
    private function ensureDirectoryExists(string $directory): bool
    {
        if (is_dir($directory)) {
            return true;
        }

        try {
            return mkdir($directory, 0755, true);
        } catch (\Exception $e) {
            logMessage("Failed to create directory '{$directory}': " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * Sanitize filename to prevent directory traversal attacks
     *
     * @param string $filename Original filename
     * @return string Sanitized filename
     */
    private function sanitizeFilename(string $filename): string
    {
        // Remove directory separators and null bytes
        $filename = str_replace(['/', '\\', "\0"], '', $filename);

        // Remove leading dots
        $filename = ltrim($filename, '.');

        // Limit length
        $filename = substr($filename, 0, 200);

        return $filename;
    }

    /**
     * Get URL for a movie image (for use in templates)
     *
     * @param string $title Movie title
     * @param string $type Image type
     * @return string Image URL
     */
    public function getImageUrl(string $title, string $type = 'default'): string
    {
        $safeTitle = $this->sanitizeFilename($title);

        switch ($type) {
            case 'trending':
                return url("assets/images/trending/{$safeTitle}.jpg");
            case 'upcoming':
                return url("assets/images/upcoming/{$safeTitle}.jpg");
            case 'topRated':
                return url("assets/images/topRated/{$safeTitle}.jpg");
            default:
                return url("assets/images/{$safeTitle}.jpg");
        }
    }
}
