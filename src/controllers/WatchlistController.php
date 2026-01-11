<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\WatchlistRepository;
use App\Repositories\MovieRepository;
use App\Services\SessionService;

class WatchlistController extends Controller
{
    private WatchlistRepository $watchlistRepo;
    private MovieRepository $movieRepo;
    private SessionService $session;

    public function __construct()
    {
        parent::__construct();
        $this->watchlistRepo = new WatchlistRepository();
        $this->movieRepo = new MovieRepository();
        $this->session = new SessionService();
        $this->session->start();
    }

    /**
     * Display user's watchlist
     */
    public function index(): void
    {
        $userEmail = $this->session->getUser();

        if (!$userEmail) {
            $this->redirect('/login');
            return;
        }

        $movies = $this->watchlistRepo->getUserMovies($userEmail);
        $count = $this->watchlistRepo->getWatchlistCount($userEmail);

        $this->render('watchlist/index', [
            'pageTitle' => 'My Watchlist - CineKTic',
            'movies' => $movies,
            'count' => $count,
            'isAuthenticated' => true
        ]);
    }

    /**
     * Add a movie to watchlist (AJAX endpoint)
     */
    public function add(): void
    {
        if (!$this->isPost()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $userEmail = $this->session->getUser();

        if (!$userEmail) {
            $this->jsonResponse(['success' => false, 'message' => 'Please login first']);
            return;
        }

        $movieId = $this->post('movie_id', 0);

        if ($movieId <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid movie ID']);
            return;
        }

        // Check if movie exists
        $movie = $this->movieRepo->findById($movieId);
        if (!$movie) {
            $this->jsonResponse(['success' => false, 'message' => 'Movie not found']);
            return;
        }

        // Check if already in watchlist
        if ($this->watchlistRepo->isMovieInWatchlist($userEmail, $movieId)) {
            $this->jsonResponse(['success' => false, 'message' => 'Movie already in watchlist']);
            return;
        }

        // Add to watchlist
        $result = $this->watchlistRepo->addMovie($userEmail, $movieId);

        if ($result) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Movie added to watchlist successfully'
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Failed to add movie to watchlist'
            ]);
        }
    }

    /**
     * Remove a movie from watchlist (AJAX endpoint)
     */
    public function remove(): void
    {
        if (!$this->isPost()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $userEmail = $this->session->getUser();

        if (!$userEmail) {
            $this->jsonResponse(['success' => false, 'message' => 'Please login first']);
            return;
        }

        $movieId = $this->post('movie_id', 0);

        if ($movieId <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid movie ID']);
            return;
        }

        // Remove from watchlist
        $result = $this->watchlistRepo->removeMovie($userEmail, $movieId);

        if ($result) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Movie removed from watchlist successfully'
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Failed to remove movie from watchlist'
            ]);
        }
    }


    private function jsonResponse(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
