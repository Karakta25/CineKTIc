<?php

namespace App\Services;

use App\Repositories\UserRepository;

class AuthService
{
    private UserRepository $userRepo;
    private SessionService $session;
    private EmailService $email;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
        $this->session = new SessionService();
        $this->email = new EmailService();
    }

    /**
     * Authenticate user with email and password
     *
     * @param string $email User email
     * @param string $password User password
     * @return array ['success' => bool, 'message' => string]
     */
    public function login(string $email, string $password): array
    {
        // Validate inputs
        if (empty($email) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Email and password are required'
            ];
        }

        // Find user
        $user = $this->userRepo->findByEmail($email);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            logMessage("Failed login attempt for: {$email}", 'WARNING');
            return [
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }

        // Set session and regenerate ID for security
        $this->session->start();
        $this->session->regenerate();
        $this->session->setUser($email);

        logMessage("Successful login: {$email}", 'INFO');

        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => $user
        ];
    }

    /**
     * Register a new user
     *
     * @param string $firstName First name
     * @param string $lastName Last name
     * @param string $email Email address
     * @param string $password Password
     * @param string $confirmPassword Password confirmation
     * @return array ['success' => bool, 'message' => string, 'errors' => array]
     */
    public function register(string $firstName, string $lastName, string $email, string $password, string $confirmPassword): array
    {
        $errors = [];

        // Validation
        if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword)) {
            $errors[] = 'All fields are required';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email is not valid';
        }

        if (strlen($password) < 5) {
            $errors[] = 'Password must be at least 5 characters long';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }

        // Check if email already exists
        if ($this->userRepo->exists($email)) {
            $errors[] = 'Email already exists';
        }

        // If there are validation errors, return them
        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Registration failed',
                'errors' => $errors
            ];
        }

        // Hash password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Create user
        $created = $this->userRepo->create($firstName, $lastName, $email, $passwordHash);

        if (!$created) {
            logMessage("User registration failed for: {$email}", 'ERROR');
            return [
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'errors' => ['An error occurred during registration']
            ];
        }

        // Send welcome email 
        try {
            $this->email->sendWelcomeEmail($email, $firstName);
        } catch (\Exception $e) {
            logMessage("Failed to send welcome email: " . $e->getMessage(), 'WARNING');
        }

        logMessage("New user registered: {$email}", 'INFO');

        return [
            'success' => true,
            'message' => 'Registration successful! You can now log in.',
            'errors' => []
        ];
    }

    /**
     * Log out the current user
     */
    public function logout(): void
    {
        $user = $this->session->getUser();

        if ($user) {
            logMessage("User logged out: {$user}", 'INFO');
        }

        $this->session->destroy();
    }

    /**
     * Check if user is authenticated
     *
     * @return bool True if user is logged in
     */
    public function isAuthenticated(): bool
    {
        $this->session->start();
        return $this->session->isAuthenticated();
    }

    /**
     * Get current authenticated user data
     *
     * @return array|null User data or null if not authenticated
     */
    public function getCurrentUser(): ?array
    {
        $this->session->start();
        $email = $this->session->getUser();

        if (!$email) {
            return null;
        }

        return $this->userRepo->findByEmail($email);
    }

    /**
     * Send password reset email with temporary password
     *
     * @param string $email User email
     * @return array ['success' => bool, 'message' => string]
     */
    public function resetPassword(string $email): array
    {
        // Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Please provide a valid email address'
            ];
        }

        // Check if user exists
        if (!$this->userRepo->exists($email)) {
            return [
                'success' => true,
                'message' => 'If the email exists, a password reset link has been sent'
            ];
        }

        // Generate temporary password
        $tempPassword = $this->generateTemporaryPassword();

        // Hash the temporary password
        $passwordHash = password_hash($tempPassword, PASSWORD_DEFAULT);

        // Update user password
        $updated = $this->userRepo->updatePassword($email, $passwordHash);

        if (!$updated) {
            logMessage("Password reset failed for: {$email}", 'ERROR');
            return [
                'success' => false,
                'message' => 'Password reset failed. Please try again.'
            ];
        }

        // Send password reset email
        try {
            $emailSent = $this->email->sendPasswordResetEmail($email, $tempPassword);

            if (!$emailSent) {
                logMessage("Password reset email failed for: {$email}", 'ERROR');
                return [
                    'success' => false,
                    'message' => 'Failed to send reset email. Please try again.'
                ];
            }

            logMessage("Password reset email sent to: {$email}", 'INFO');

            return [
                'success' => true,
                'message' => 'A temporary password has been sent to your email'
            ];
        } catch (\Exception $e) {
            logMessage("Password reset email exception: " . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Failed to send reset email. Please try again.'
            ];
        }
    }

    /**
     * Change user password
     *
     * @param string $email User email
     * @param string $currentPassword Current password
     * @param string $newPassword New password
     * @param string $confirmPassword Confirm new password
     * @return array ['success' => bool, 'message' => string]
     */
    public function changePassword(string $email, string $currentPassword, string $newPassword, string $confirmPassword): array
    {
        // Validate inputs
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            return [
                'success' => false,
                'message' => 'All fields are required'
            ];
        }

        if ($newPassword !== $confirmPassword) {
            return [
                'success' => false,
                'message' => 'New passwords do not match'
            ];
        }

        if (strlen($newPassword) < 5) {
            return [
                'success' => false,
                'message' => 'New password must be at least 5 characters long'
            ];
        }

        // Verify current password
        $user = $this->userRepo->findByEmail($email);

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Current password is incorrect'
            ];
        }

        // Hash new password
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password
        $updated = $this->userRepo->updatePassword($email, $passwordHash);

        if (!$updated) {
            return [
                'success' => false,
                'message' => 'Failed to update password. Please try again.'
            ];
        }

        logMessage("Password changed for: {$email}", 'INFO');

        return [
            'success' => true,
            'message' => 'Password changed successfully'
        ];
    }

    /**
     * Generate a random temporary password
     *
     * @return string Temporary password
     */
    private function generateTemporaryPassword(): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $length = 12;
        $tempPassword = '';

        for ($i = 0; $i < $length; $i++) {
            $tempPassword .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $tempPassword;
    }
}
