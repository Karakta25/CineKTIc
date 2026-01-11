<?php require __DIR__ . '/../layouts/header.php'; ?>

<!-- #MOVIES LIST -->
<section class="top-rated">
  <div class="container">
    <p class="section-subtitle">Online Streaming</p>
    <h2 class="h2 section-title">Upcoming Movies</h2>

    <ul class="movies-list">
      <?php if (empty($movies)): ?>
        <li>
          <p>No upcoming movies found.</p>
        </li>
      <?php else: ?>
        <?php foreach ($movies as $movie): ?>
          <li>
            <div class="movie-card">
              <a href="<?= url('/movies/' . urlencode($movie['Title'])) ?>">
                <figure class="card-banner">
                  <img src="<?= e('../imagesUpcoming/' . $movie['Title'] . '.jpg') ?>"
                       alt="<?= e($movie['Title']) ?> Movie Poster"
                       onerror="this.src='<?= asset('images/placeholder.jpg') ?>'" />
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
      <?php endif; ?>
    </ul>
  </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
