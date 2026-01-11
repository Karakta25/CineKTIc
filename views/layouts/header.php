<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'CineKTic - Movie Magic Unleashed') ?></title>

  <!-- favicon -->
  <link rel="shortcut icon" href="<?= asset('images/favicon.svg') ?>" type="image/svg+xml">

  <!--  custom css link -->
  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">

  <!-- google font link -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body id="top">

  <!--  #HEADER -->
  <header class="header" data-header>
    <div class="container">

      <div class="overlay" data-overlay></div>

      <a href="<?= url('/') ?>" class="logo">
        <img src="<?= asset('images/logo.svg') ?>" alt="CineKTic logo">
      </a>

      <div class="header-actions">

        <form action="<?= url('/movies/search') ?>" class="search-bar" method="get">
          <input type="text" name="q" class="search-textbox" placeholder="Search..." value="<?= e($_GET['q'] ?? '') ?>">
          <button type="submit" class="search-btn">
            <ion-icon name="search-outline"></ion-icon>
          </button>
        </form>

        <?php if (isAuthenticated()): ?>
          <?php
            // Get user data
            $userEmail = currentUser();
            $user = $userData ?? ['first_name' => 'User']; // $userData should be passed from controller
          ?>
          <div class="icon-container">
            <ion-icon name='person-circle-outline' id="person-circle-outline"></ion-icon>
            <span><?= e($user['first_name']) ?></span>

            <div class="submenu">
              <ul>
                <li><a href="<?= url('/watchlist') ?>" style="text-decoration: none; padding: 8px 0; font-size: 16px; color: black;">My watchlist</a></li>
                <li><a href="<?= url('/logout') ?>" style="text-decoration: none; padding: 8px 0; font-size: 16px; color: black;">Log out</a></li>
              </ul>
            </div>
          </div>
        <?php else: ?>
          <a href="<?= url('/register') ?>">
            <button class="btn btn-primary">Sign up</button>
          </a>
        <?php endif; ?>

      </div>

      <button class="menu-open-btn" data-menu-open-btn>
        <ion-icon name="reorder-two"></ion-icon>
      </button>

      <nav class="navbar" data-navbar>

        <div class="navbar-top">
          <a href="<?= url('/') ?>" class="logo">
            <img src="<?= asset('images/logo.svg') ?>" alt="CineKTic logo">
          </a>

          <button class="menu-close-btn" data-menu-close-btn>
            <ion-icon name="close-outline"></ion-icon>
          </button>
        </div>

        <ul class="navbar-list">
          <li>
            <a href="<?= url('/') ?>" class="navbar-link">Home</a>
          </li>
          <li>
            <a href="<?= url('/movies/trending') ?>" class="navbar-link">Trending</a>
          </li>
          <li>
            <a href="<?= url('/movies/upcoming') ?>" class="navbar-link">Upcoming</a>
          </li>
          <li>
            <a href="<?= url('/movies/top-rated') ?>" class="navbar-link">Top Rated</a>
          </li>
        </ul>

        <ul class="navbar-social-list">
          <li>
            <a href="#" class="navbar-social-link">
              <ion-icon name="logo-instagram"></ion-icon>
            </a>
          </li>
          <li>
            <a href="#" class="navbar-social-link">
              <ion-icon name="logo-facebook"></ion-icon>
            </a>
          </li>
          <li>
            <a href="#" class="navbar-social-link">
              <ion-icon name="logo-pinterest"></ion-icon>
            </a>
          </li>
          <li>
            <a href="#" class="navbar-social-link">
              <ion-icon name="logo-youtube"></ion-icon>
            </a>
          </li>
        </ul>

      </nav>

    </div>
  </header>

  <main>
    <article>
