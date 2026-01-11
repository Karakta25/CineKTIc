<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
use App\Services\SessionService;

class AuthController extends Controller
{
    private AuthService $authService;
    private SessionService $session;

    public function __construct()
    {
        parent::__construct();
        $this->authService = new AuthService();
        $this->session = new SessionService();
        $this->session->start();
    }

    /**
     * Show login form
     */
    public function showLogin(): void
    {
        // Redirect if already authenticated
        if ($this->authService->isAuthenticated()) {
            $this->redirect('/home');
        }

        $this->render('auth/login');
    }

    /**
     * Process login
     */
    public function login(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/login');
            return;
        }

        // Validate CSRF token
        if (!$this->validateCsrfToken()) {
            $this->session->flash('error', 'Invalid request. Please try again.');
            $this->redirect('/login');
            return;
        }

        $email = $this->post('email', '');
        $password = $this->post('password', '');

        // Store old input for form repopulation
        $this->session->setOldInput(['email' => $email]);

        // Attempt login
        $result = $this->authService->login($email, $password);

        if ($result['success']) {
            // Check if there's an intended URL to redirect to
            $intendedUrl = $this->session->get('intended_url', '/home');
            $this->session->remove('intended_url');

            $this->redirect($intendedUrl);
        } else {
            $this->session->flash('error', $result['message']);
            $this->redirect('/login');
        }
    }

    /**
     * Show registration form
     */
    public function showRegister(): void
    {
        // Redirect if already authenticated
        if ($this->authService->isAuthenticated()) {
            $this->redirect('/home');
        }

        $this->render('auth/register');
    }

    /**
     * Process registration
     */
    public function register(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/register');
            return;
        }

        // Validate CSRF token
        if (!$this->validateCsrfToken()) {
            $this->session->flash('error', 'Invalid request. Please try again.');
            $this->redirect('/register');
            return;
        }

        $firstName = $this->post('first_name', '');
        $lastName = $this->post('last_name', '');
        $email = $this->post('email', '');
        $password = $this->post('password', '');
        $confirmPassword = $this->post('confirm_password', '');

        // Store old input for form repopulation (except passwords)
        $this->session->setOldInput([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email
        ]);

        // Attempt registration
        $result = $this->authService->register($firstName, $lastName, $email, $password, $confirmPassword);

        if ($result['success']) {
            $this->session->flash('success', $result['message']);
            $this->session->clearOldInput();
            $this->redirect('/login');
        } else {
            $this->render('auth/register', [
                'errors' => $result['errors']
            ]);
        }
    }

    /**
     * Show password recovery form
     */
    public function showPasswordRecovery(): void
    {
        // Redirect if already authenticated
        if ($this->authService->isAuthenticated()) {
            $this->redirect('/home');
        }

        $this->render('auth/password_recovery');
    }

    /**
     * Process password recovery
     */
    public function recoverPassword(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/password-recovery');
            return;
        }

        // Validate CSRF token
        if (!$this->validateCsrfToken()) {
            $this->session->flash('error', 'Invalid request. Please try again.');
            $this->redirect('/password-recovery');
            return;
        }

        $email = $this->post('email', '');

        // Store old input
        $this->session->setOldInput(['email' => $email]);

        // Attempt password reset
        $result = $this->authService->resetPassword($email);

        if ($result['success']) {
            $this->session->flash('success', $result['message']);
            $this->session->clearOldInput();
        } else {
            $this->session->flash('error', $result['message']);
        }

        $this->redirect('/password-recovery');
    }

    /**
     * Logout user
     */
    public function logout(): void
    {
        $this->authService->logout();
        $this->session->flash('success', 'You have been logged out successfully.');
        $this->redirect('/');
    }
}
