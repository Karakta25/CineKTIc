<?php

namespace App\Middleware;

use App\Services\SessionService;

class AuthMiddleware
{
    private SessionService $session;

    public function __construct()
    {
        $this->session = new SessionService();
    }

    /**
     * Handle the middleware
     * Checks if user is authenticated, redirects to login if not
     *
     * @return bool True if user is authenticated, false otherwise
     */
    public function handle(): bool
    {
        $this->session->start();

        if (!$this->session->isAuthenticated()) {
            // Store intended URL for redirect after login
            $intendedUrl = $_SERVER['REQUEST_URI'] ?? '/';
            $this->session->set('intended_url', $intendedUrl);

            // Redirect to login page
            header('Location: /login');
            exit;
        }

        return true;
    }
}
