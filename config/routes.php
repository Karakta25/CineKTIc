<?php

/**
 * Application Routes
 * Format: $router->METHOD(path, controller, action, [middleware])
 */

// ============================================
// Public Routes (No Authentication Required)
// ============================================

// Home
$router->get('/', 'HomeController', 'index');

// Authentication Routes
$router->get('/login', 'AuthController', 'showLogin');
$router->post('/login', 'AuthController', 'login');
$router->get('/register', 'AuthController', 'showRegister');
$router->post('/register', 'AuthController', 'register');
$router->get('/password-recovery', 'AuthController', 'showPasswordRecovery');
$router->post('/password-recovery', 'AuthController', 'recoverPassword');

// Movie Routes (Public)
$router->get('/movies/trending', 'MovieController', 'trending');
$router->get('/movies/upcoming', 'MovieController', 'upcoming');
$router->get('/movies/top-rated', 'MovieController', 'topRated');
$router->get('/movies/search', 'MovieController', 'search');
$router->get('/movies/{title}', 'MovieController', 'details');

// ============================================
// Protected Routes (Authentication Required)
// ============================================

// Logout
$router->get('/logout', 'AuthController', 'logout', ['auth']);

// Authenticated Home
$router->get('/home', 'HomeController', 'loggedIn', ['auth']);

// Watchlist Routes
$router->get('/watchlist', 'WatchlistController', 'index', ['auth']);
$router->post('/watchlist/add', 'WatchlistController', 'add', ['auth']);
$router->post('/watchlist/remove', 'WatchlistController', 'remove', ['auth']);
