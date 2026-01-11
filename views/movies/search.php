<?php require __DIR__ . '/../layouts/header.php'; ?>

<!-- #SEARCH RESULTS -->
<section class="top-rated">
  <div class="container">
    <p class="section-subtitle">Search Results</p>
    <h2 class="h2 section-title">Results for "<?= e($searchQuery) ?>"</h2>

    <?php if (empty($movies)): ?>
      <p style="text-align: center; padding: 40px 0; font-size: 18px;">
        No movies found matching your search. Try different keywords.
      </p>
    <?php else: ?>
      <p style="margin-bottom: 20px; color: var(--light-azure);">
        Found <?= count($movies) ?> <?= count($movies) === 1 ? 'movie' : 'movies' ?>
      </p>

      <ul class="movies-list">
        <?php foreach ($movies as $movie): ?>
          <li>
            <div class="movie-card">
              <a href="<?= url('/movies/' . urlencode($movie['Title'])) ?>">
                <figure class="card-banner">
                  <img src="<?= e($movie['image'] ?? '../imagesTrending/' . $movie['Title'] . '.jpg') ?>"
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
      </ul>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
