<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\MovieRepository;
use App\Repositories\UserRepository;
use App\Services\ImageService;
use App\Services\SessionService;

class HomeController extends Controller
{
    private MovieRepository $movieRepo;
    private UserRepository $userRepo;
    private ImageService $imageService;
    private SessionService $session;

    public function __construct()
    {
        parent::__construct();
        $this->movieRepo = new MovieRepository();
        $this->userRepo = new UserRepository();
        $this->imageService = new ImageService();
        $this->session = new SessionService();
        $this->session->start();
    }

    /**
     * Guest homepage (not authenticated)
     */
    public function index(): void
    {
        // Get movie data for the homepage
        $trendingMovies = $this->movieRepo->getTrending(6);
        $upcomingMovies = $this->movieRepo->getUpcoming(6);
        $topRatedMovies = $this->movieRepo->getTopRated(6);

        // Save images for display
        $this->imageService->saveTrendingImages($trendingMovies);
        $this->imageService->saveUpcomingImages($upcomingMovies);
        $this->imageService->saveTopRatedImages($topRatedMovies);

        $this->render('home/index', [
            'pageTitle' => 'CineKTic - Movie Magic Unleashed',
            'trendingMovies' => $trendingMovies,
            'upcomingMovies' => $upcomingMovies,
            'topRatedMovies' => $topRatedMovies,
            'isAuthenticated' => false
        ]);
    }

    /**
     * Authenticated user homepage
     */
    public function loggedIn(): void
    {
        // Get current user
        $userEmail = $this->session->getUser();
        $userData = $this->userRepo->findByEmail($userEmail);

        if (!$userData) {
            // User not found, logout and redirect
            $this->session->destroy();
            $this->redirect('/');
            return;
        }

        // Get movie data for the homepage
        $trendingMovies = $this->movieRepo->getTrending(6);
        $upcomingMovies = $this->movieRepo->getUpcoming(6);
        $topRatedMovies = $this->movieRepo->getTopRated(6);

        // Save images for display
        $this->imageService->saveTrendingImages($trendingMovies);
        $this->imageService->saveUpcomingImages($upcomingMovies);
        $this->imageService->saveTopRatedImages($topRatedMovies);

        $this->render('home/logged_in', [
            'pageTitle' => 'CineKTic - Welcome ' . $userData['first_name'],
            'userData' => $userData,
            'trendingMovies' => $trendingMovies,
            'upcomingMovies' => $upcomingMovies,
            'topRatedMovies' => $topRatedMovies,
            'isAuthenticated' => true
        ]);
    }
}
