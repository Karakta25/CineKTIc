<?php require __DIR__ . '/../layouts/header.php'; ?>

<!-- #HERO -->
<section class="hero">
  <div class="container">
    <div class="hero-content">
      <p class="hero-subtitle">Welcome back, <?= e($userData['first_name']) ?>!</p>
      <h1 class="h1 hero-title">
        Unlimited <strong>Movie</strong>, TVs Shows, & More.
      </h1>
      <div class="meta-wrapper">
        <div class="badge badge-outline">PG 18</div>
        <div class="badge badge-outline">HD</div>
        <div class="ganre-wrapper">
          <a href="<?= url('/movies/trending') ?>">Action,</a>
          <a href="<?= url('/movies/trending') ?>">Drama,</a>
          <a href="<?= url('/movies/top-rated') ?>">Thriller</a>
        </div>
      </div>
      <a href="<?= url('/watchlist') ?>">
        <button class="btn btn-primary">
          <ion-icon name="bookmarks" aria-hidden="true"></ion-icon>
          <span>My Watchlist</span>
        </button>
      </a>
    </div>
  </div>
</section>

<!-- #TRENDING MOVIES -->
<section class="upcoming-trending">
  <div class="container">
    <div class="flex-wrapper">
      <div class="title-wrapper">
        <h2 class="h2 section-title">Trending Movies</h2>
      </div>
      <ul class="filter-list">
        <li>
          <a href="<?= url('/movies/trending') ?>" class="btn btn-primary">View All</a>
        </li>
      </ul>
    </div>

    <ul class="movies-list has-scrollbar">
      <?php foreach ($trendingMovies as $movie): ?>
        <li>
          <div class="movie-card">
            <a href="<?= url('/movies/' . urlencode($movie['Title'])) ?>">
              <figure class="card-banner">
                <img src="<?= e('../imagesTrending/' . $movie['Title'] . '.jpg') ?>"
                     alt="<?= e($movie['Title']) ?> Movie Poster" />
              </figure>
            </a>
            <div class="title-wrapper">
              <a href="<?= url('/movies/' . urlencode($movie['Title'])) ?>">
                <h3 class="card-title"><?= e($movie['Title']) ?></h3>
              </a>
              <time datetime="<?= e($movie['ReleaseYear']) ?>"><?= e($movie['ReleaseYear']) ?></time>
            </div>
            <div class="card-meta">
              <div class="badge badge-outline">HD</div>
              <div class="rating">
                <ion-icon name="star"></ion-icon>
                <data><?= e($movie['Rating']) ?></data>
              </div>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<!-- #UPCOMING MOVIES -->
<section class="upcoming-trending">
  <div class="container">
    <div class="flex-wrapper">
      <div class="title-wrapper">
        <h2 class="h2 section-title">Upcoming Movies</h2>
      </div>
      <ul class="filter-list">
        <li>
          <a href="<?= url('/movies/upcoming') ?>" class="btn btn-primary">View All</a>
        </li>
      </ul>
    </div>

    <ul class="movies-list has-scrollbar">
      <?php foreach ($upcomingMovies as $movie): ?>
        <li>
          <div class="movie-card">
            <a href="<?= url('/movies/' . urlencode($movie['Title'])) ?>">
              <figure class="card-banner">
                <img src="<?= e('../imagesUpcoming/' . $movie['Title'] . '.jpg') ?>"
                     alt="<?= e($movie['Title']) ?> Movie Poster" />
              </figure>
            </a>
            <div class="title-wrapper">
              <a href="<?= url('/movies/' . urlencode($movie['Title'])) ?>">
                <h3 class="card-title"><?= e($movie['Title']) ?></h3>
              </a>
              <time datetime="<?= e($movie['ReleaseYear']) ?>"><?= e($movie['ReleaseYear']) ?></time>
            </div>
            <div class="card-meta">
              <div class="badge badge-outline">HD</div>
              <div class="rating">
                <ion-icon name="star"></ion-icon>
                <data><?= e($movie['Rating']) ?></data>
              </div>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<!-- #TOP RATED MOVIES -->
<section class="upcoming-trending">
  <div class="container">
    <div class="flex-wrapper">
      <div class="title-wrapper">
        <h2 class="h2 section-title">Top Rated Movies</h2>
      </div>
      <ul class="filter-list">
        <li>
          <a href="<?= url('/movies/top-rated') ?>" class="btn btn-primary">View All</a>
        </li>
      </ul>
    </div>

    <ul class="movies-list has-scrollbar">
      <?php foreach ($topRatedMovies as $movie): ?>
        <li>
          <div class="movie-card">
            <a href="<?= url('/movies/' . urlencode($movie['Title'])) ?>">
              <figure class="card-banner">
                <img src="<?= e('../imagesTopRated/' . $movie['Title'] . '.jpg') ?>"
                     alt="<?= e($movie['Title']) ?> Movie Poster" />
              </figure>
            </a>
            <div class="title-wrapper">
              <a href="<?= url('/movies/' . urlencode($movie['Title'])) ?>">
                <h3 class="card-title"><?= e($movie['Title']) ?></h3>
              </a>
              <time datetime="<?= e($movie['ReleaseYear']) ?>"><?= e($movie['ReleaseYear']) ?></time>
            </div>
            <div class="card-meta">
              <div class="badge badge-outline">HD</div>
              <div class="rating">
                <ion-icon name="star"></ion-icon>
                <data><?= e($movie['Rating']) ?></data>
              </div>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
