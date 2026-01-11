<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private PHPMailer $mailer;
    private string $fromAddress;
    private string $fromName;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    /**
     * Configure PHPMailer with SMTP settings from environment
     */
    private function configure(): void
    {
        try {
            // SMTP configuration
            $this->mailer->isSMTP();
            $this->mailer->Host = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $_ENV['MAIL_USERNAME'] ?? '';
            $this->mailer->Password = $_ENV['MAIL_PASSWORD'] ?? '';
            $this->mailer->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? 'tls';
            $this->mailer->Port = (int)($_ENV['MAIL_PORT'] ?? 587);

            // Set from address
            $this->fromAddress = $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@example.com';
            $this->fromName = $_ENV['MAIL_FROM_NAME'] ?? 'CineKTic Team';

            $this->mailer->setFrom($this->fromAddress, $this->fromName);
            $this->mailer->isHTML(true);
        } catch (Exception $e) {
            logMessage("Email configuration failed: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }

    /**
     * Send welcome email to new user
     *
     * @param string $toEmail Recipient email
     * @param string $firstName Recipient first name
     * @return bool True if sent successfully
     */
    public function sendWelcomeEmail(string $toEmail, string $firstName): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail);

            $this->mailer->Subject = 'Welcome to CineKTic!';
            $this->mailer->Body = $this->getWelcomeEmailTemplate($firstName);
            $this->mailer->AltBody = "Welcome to CineKTic, {$firstName}! Thank you for registering with us.";

            $result = $this->mailer->send();

            if ($result) {
                logMessage("Welcome email sent to: {$toEmail}", 'INFO');
            }

            return $result;
        } catch (Exception $e) {
            logMessage("Failed to send welcome email: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * Send password recovery email
     *
     * @param string $toEmail Recipient email
     * @param string $tempPassword Temporary password
     * @return bool True if sent successfully
     */
    public function sendPasswordResetEmail(string $toEmail, string $tempPassword): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail);

            $this->mailer->Subject = 'CineKTic - Password Recovery';
            $this->mailer->Body = $this->getPasswordResetEmailTemplate($tempPassword);
            $this->mailer->AltBody = "Your temporary password is: {$tempPassword}. Please change it after logging in.";

            $result = $this->mailer->send();

            if ($result) {
                logMessage("Password reset email sent to: {$toEmail}", 'INFO');
            }

            return $result;
        } catch (Exception $e) {
            logMessage("Failed to send password reset email: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * Send membership confirmation email
     *
     * @param string $toEmail Recipient email
     * @return bool True if sent successfully
     */
    public function sendMembershipConfirmation(string $toEmail): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail);

            $this->mailer->Subject = 'CineKTic - Membership Confirmation';
            $this->mailer->Body = $this->getMembershipConfirmationTemplate();
            $this->mailer->AltBody = "Thank you for your membership!";

            $result = $this->mailer->send();

            if ($result) {
                logMessage("Membership confirmation sent to: {$toEmail}", 'INFO');
            }

            return $result;
        } catch (Exception $e) {
            logMessage("Failed to send membership confirmation: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * Send generic email
     *
     * @param string $toEmail Recipient email
     * @param string $subject Email subject
     * @param string $body Email body (HTML)
     * @param string $altBody Plain text alternative
     * @return bool True if sent successfully
     */
    public function send(string $toEmail, string $subject, string $body, string $altBody = ''): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail);

            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = $altBody ?: strip_tags($body);

            $result = $this->mailer->send();

            if ($result) {
                logMessage("Email sent to: {$toEmail} | Subject: {$subject}", 'INFO');
            }

            return $result;
        } catch (Exception $e) {
            logMessage("Failed to send email: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * Get welcome email HTML template
     */
    private function getWelcomeEmailTemplate(string $firstName): string
    {
        return "
            <html>
            <body style='font-family: Arial, sans-serif;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #2c3e50;'>Welcome to CineKTic, {$firstName}!</h2>
                    <p>Thank you for registering with CineKTic, your go-to platform for movie information and tracking.</p>
                    <p>You can now:</p>
                    <ul>
                        <li>Browse trending, upcoming, and top-rated movies</li>
                        <li>Create and manage your personal watchlist</li>
                        <li>Discover new movies and read detailed information</li>
                    </ul>
                    <p>Start exploring movies now!</p>
                    <p style='color: #7f8c8d; font-size: 12px; margin-top: 30px;'>
                        This is an automated message from CineKTic. Please do not reply to this email.
                    </p>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Get password reset email HTML template
     */
    private function getPasswordResetEmailTemplate(string $tempPassword): string
    {
        return "
            <html>
            <body style='font-family: Arial, sans-serif;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #2c3e50;'>CineKTic - Password Recovery</h2>
                    <p>You requested a password reset for your CineKTic account.</p>
                    <p>Your temporary password is:</p>
                    <div style='background-color: #f4f4f4; padding: 15px; margin: 20px 0; font-size: 18px; font-weight: bold; letter-spacing: 2px;'>
                        {$tempPassword}
                    </div>
                    <p><strong>Important:</strong> Please change this password immediately after logging in for security reasons.</p>
                    <p style='color: #e74c3c; margin-top: 20px;'>
                        If you did not request this password reset, please contact us immediately.
                    </p>
                    <p style='color: #7f8c8d; font-size: 12px; margin-top: 30px;'>
                        This is an automated message from CineKTic. Please do not reply to this email.
                    </p>
                </div>
            </body>
            </html>
        ";
    }

    /**
     * Get membership confirmation email HTML template
     */
    private function getMembershipConfirmationTemplate(): string
    {
        return "
            <html>
            <body style='font-family: Arial, sans-serif;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #2c3e50;'>Thank You for Your Membership!</h2>
                    <p>Your CineKTic membership has been confirmed.</p>
                    <p>Enjoy unlimited access to all our features!</p>
                    <p style='color: #7f8c8d; font-size: 12px; margin-top: 30px;'>
                        This is an automated message from CineKTic. Please do not reply to this email.
                    </p>
                </div>
            </body>
            </html>
        ";
    }
}
