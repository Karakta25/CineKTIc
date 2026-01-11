<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sign Up - CineKTic</title>
    <link rel="shortcut icon" href="<?= asset('images/favicon.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= asset('css/registrationAndLogInStyle.css') ?>">
</head>
<body>
<div class="container">
    <div class="registration form">
        <div class="header-container">
            <ion-button class="custom-button">
                <a href="<?= url('/') ?>">
                    <img src="<?= asset('images/Arrowleft.png') ?>" style="height: 35px; width: 35px; margin-top: 8px" alt="Back">
                </a>
            </ion-button>
            <header><strong>Sign Up</strong></header>
        </div>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="form-box error">
                <?php foreach ($errors as $error): ?>
                    <p><?= e($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($message = flash('error')): ?>
            <div class="form-box error">
                <p><?= e($message) ?></p>
            </div>
        <?php endif; ?>

        <form action="<?= url('/register') ?>" method="post">
            <?= csrfField() ?>

            <input type="text" name="first_name" placeholder="Enter your first name" value="<?= e(old('first_name')) ?>" required>
            <input type="text" name="last_name" placeholder="Enter your last name" value="<?= e(old('last_name')) ?>" required>
            <input type="email" name="email" placeholder="Enter your email" value="<?= e(old('email')) ?>" required>
            <input type="password" name="password" placeholder="Create a password" required>
            <input type="password" name="confirm_password" placeholder="Confirm your password" required>
            <input type="submit" name="submit" class="button" value="Sign Up">
        </form>

        <div class="signup">
            <span class="signup">Already have an account?
                <a href="<?= url('/login') ?>">Login</a>
            </span>
        </div>
    </div>
</div>

<!-- ionicon link -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
