<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\MovieRepository;
use App\Services\ImageService;
use App\Services\SessionService;

class MovieController extends Controller
{
    private MovieRepository $movieRepo;
    private ImageService $imageService;
    private SessionService $session;

    public function __construct()
    {
        parent::__construct();
        $this->movieRepo = new MovieRepository();
        $this->imageService = new ImageService();
        $this->session = new SessionService();
        $this->session->start();
    }

    /**
     * Display trending movies page
     */
    public function trending(): void
    {
        $movies = $this->movieRepo->getTrending(20);
        $this->imageService->saveTrendingImages($movies);

        $this->render('movies/trending', [
            'pageTitle' => 'Trending Movies - CineKTic',
            'movies' => $movies,
            'isAuthenticated' => $this->isAuthenticated()
        ]);
    }

    /**
     * Display upcoming movies page
     */
    public function upcoming(): void
    {
        $movies = $this->movieRepo->getUpcoming(20);
        $this->imageService->saveUpcomingImages($movies);

        $this->render('movies/upcoming', [
            'pageTitle' => 'Upcoming Movies - CineKTic',
            'movies' => $movies,
            'isAuthenticated' => $this->isAuthenticated()
        ]);
    }

    /**
     * Display top-rated movies page
     */
    public function topRated(): void
    {
        $movies = $this->movieRepo->getTopRated(20);
        $this->imageService->saveTopRatedImages($movies);

        $this->render('movies/top_rated', [
            'pageTitle' => 'Top Rated Movies - CineKTic',
            'movies' => $movies,
            'isAuthenticated' => $this->isAuthenticated()
        ]);
    }

    /**
     * Search movies by title
     */
    public function search(): void
    {
        $query = $this->get('q', '');

        if (empty($query)) {
            $this->redirect('/');
            return;
        }

        $movies = $this->movieRepo->searchByTitle($query);

        $this->render('movies/search', [
            'pageTitle' => "Search Results for '{$query}' - CineKTic",
            'movies' => $movies,
            'searchQuery' => $query,
            'isAuthenticated' => $this->isAuthenticated()
        ]);
    }

    /**
     * Display movie details page
     *
     * @param string $title Movie title from URL
     */
    public function details(string $title): void
    {
        // Decode URL-encoded title
        $title = urldecode($title);

        // Replace hyphens with spaces
        $title = str_replace('-', ' ', $title);

        $movie = $this->movieRepo->findByTitle($title);

        if (!$movie) {
            // Movie not found
            http_response_code(404);
            echo "Movie not found";
            return;
        }

        // Get additional movie data
        $genres = $this->movieRepo->getMovieGenres($movie['MovieID']);
        $actors = $this->movieRepo->getMovieActors($movie['MovieID']);

        // Check if movie is in user's watchlist (if authenticated)
        $inWatchlist = false;
        if ($this->isAuthenticated()) {
            $watchlistRepo = new \App\Repositories\WatchlistRepository();
            $inWatchlist = $watchlistRepo->isMovieInWatchlist($this->session->getUser(), $movie['MovieID']);
        }

        $this->render('movies/details', [
            'pageTitle' => $movie['Title'] . ' - CineKTic',
            'movie' => $movie,
            'genres' => $genres,
            'actors' => $actors,
            'inWatchlist' => $inWatchlist,
            'isAuthenticated' => $this->isAuthenticated()
        ]);
    }

    /**
     * Check if user is authenticated
     */
    private function isAuthenticated(): bool
    {
        return $this->session->has('user');
    }
}
