<?php require __DIR__ . '/../layouts/header.php'; ?>

<!-- #WATCHLIST -->
<section class="top-rated">
  <div class="container">
    <p class="section-subtitle">My Collection</p>
    <h2 class="h2 section-title">My Watchlist</h2>

    <?php if (empty($movies)): ?>
      <div style="text-align: center; padding: 60px 20px;">
        <ion-icon name="bookmarks-outline" style="font-size: 80px; color: var(--light-azure);"></ion-icon>
        <p style="font-size: 20px; margin-top: 20px; color: var(--light-azure);">
          Your watchlist is empty
        </p>
        <p style="margin-top: 10px; margin-bottom: 30px; color: var(--cadet-grey);">
          Add movies to your watchlist to keep track of what you want to watch
        </p>
        <a href="<?= url('/movies/trending') ?>">
          <button class="btn btn-primary">Browse Movies</button>
        </a>
      </div>
    <?php else: ?>
      <p style="margin-bottom: 20px; color: var(--light-azure);">
        You have <?= $count ?> <?= $count === 1 ? 'movie' : 'movies' ?> in your watchlist
      </p>

      <ul class="movies-list">
        <?php foreach ($movies as $movie): ?>
          <li>
            <div class="movie-card">
              <a href="<?= url('/movies/' . urlencode($movie['Title'])) ?>">
                <figure class="card-banner">
                  <img src="<?= e('./assets/images/' . $movie['Title'] . '.jpg') ?>"
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
                <button class="remove-btn"
                        data-movie-id="<?= e($movie['MovieID']) ?>"
                        onclick="removeFromWatchlist(this, '<?= e($movie['Title']) ?>')"
                        title="Remove from watchlist">
                  <ion-icon name="trash-outline"></ion-icon>
                </button>
              </div>

              <?php if (!empty($movie['Plot'])): ?>
                <p class="card-text" style="margin-top: 10px; color: var(--cadet-grey); font-size: 14px; line-height: 1.6;">
                  <?= e(substr($movie['Plot'], 0, 150)) ?><?= strlen($movie['Plot']) > 150 ? '...' : '' ?>
                </p>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</section>

<script>
function removeFromWatchlist(button, movieTitle) {
  if (!confirm('Are you sure you want to remove "' + movieTitle + '" from your watchlist?')) {
    return;
  }

  const movieId = button.getAttribute('data-movie-id');
  const movieCard = button.closest('li');

  fetch('<?= url('/watchlist/remove') ?>', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'movie_id=' + movieId
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Remove the movie card with animation
      movieCard.style.opacity = '0';
      movieCard.style.transform = 'scale(0.8)';
      movieCard.style.transition = 'all 0.3s ease';

      setTimeout(() => {
        movieCard.remove();

        // Check if watchlist is now empty
        const remainingMovies = document.querySelectorAll('.movies-list li').length;
        if (remainingMovies === 0) {
          location.reload(); // Reload to show empty state
        }
      }, 300);

      showNotification(data.message, 'success');
    } else {
      showNotification(data.message, 'error');
    }
  })
  .catch(error => {
    showNotification('An error occurred. Please try again.', 'error');
  });
}

function showNotification(message, type) {
  // Simple notification - you can enhance this with a better UI
  alert(message);
}
</script>

<style>
.remove-btn {
  background: transparent;
  border: none;
  color: var(--radical-red);
  cursor: pointer;
  font-size: 20px;
  padding: 5px;
  transition: all 0.3s ease;
}

.remove-btn:hover {
  color: var(--red-orange);
  transform: scale(1.1);
}

.card-meta {
  display: flex;
  align-items: center;
  gap: 10px;
}
</style>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
