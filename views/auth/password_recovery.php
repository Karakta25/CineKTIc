<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Password Recovery - CineKTic</title>
    <link rel="shortcut icon" href="<?= asset('images/favicon.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= asset('css/registrationAndLogInStyle.css') ?>">
</head>
<body>
<div class="container container-log">
    <div class="login form">
        <div class="header-container">
            <ion-button class="custom-button">
                <a href="<?= url('/login') ?>">
                    <img src="<?= asset('images/Arrowleft.png') ?>" style="height: 35px; width: 35px; margin-top: 8px" alt="Back to Login">
                </a>
            </ion-button>
            <header class="forgotPass" style="margin-left: 0; margin-right: 30px;"><strong>Forgotten password</strong></header>
        </div>

        <?php if ($message = flash('success')): ?>
            <div class="form-box success">
                <p><?= e($message) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($message = flash('error')): ?>
            <div class="form-box error">
                <p><?= e($message) ?></p>
            </div>
        <?php endif; ?>

        <form action="<?= url('/password-recovery') ?>" method="post">
            <?= csrfField() ?>

            <input type="email" name="email" placeholder="Enter your email" value="<?= e(old('email')) ?>" required>
            <input type="submit" name="login" class="button" value="Send">
        </form>

        <div class="signup">
            <span class="signup">Remember your password?
                <a href="<?= url('/login') ?>">Login</a>
            </span>
        </div>

        <div class="signup">
            <span class="signup">Don't have an account?
                <a href="<?= url('/register') ?>">Sign up</a>
            </span>
        </div>
    </div>
</div>

<!-- ionicon link -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
