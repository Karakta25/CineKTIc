<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - CineKTic</title>
    <link rel="shortcut icon" href="<?= asset('images/favicon.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= asset('css/registrationAndLogInStyle.css') ?>">
</head>
<body>
<div class="container container-log">
    <div class="login form">
        <header><strong>Login</strong></header>

        <?php if ($message = flash('error')): ?>
            <div class="form-box error">
                <p><?= e($message) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($message = flash('success')): ?>
            <div class="form-box success">
                <p><?= e($message) ?></p>
            </div>
        <?php endif; ?>

        <form action="<?= url('/login') ?>" method="post">
            <?= csrfField() ?>

            <input type="email" name="email" placeholder="Enter your email" value="<?= e(old('email')) ?>" required>
            <input type="password" name="password" placeholder="Enter your password" required>
            <input type="submit" name="login" class="button" value="Login">
        </form>

        <div class="signup">
            <span class="signup">Don't have an account?
                <a href="<?= url('/register') ?>">Sign up</a>
            </span>
        </div>

        <div class="signup">
            <span class="signup">
                <a href="<?= url('/password-recovery') ?>">Forgotten password?</a>
            </span>
        </div>

        <div class="signup">
            <span class="signup">Return to
                <a href="<?= url('/') ?>">Home page</a>
            </span>
        </div>
    </div>
</div>
</body>
</html>
