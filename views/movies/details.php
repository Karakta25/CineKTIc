<?php require __DIR__ . '/../layouts/header.php'; ?>

<!-- #MOVIE DETAILS -->
<section class="movie-detail">
  <div class="grid-container">

    <div class="namebox">
      <h1 class="h1 detail-title">
        <?= e($movie['Title']) ?>
      </h1>
      <div class="date-time">
        <div>
          <ion-icon name="calendar-outline"></ion-icon>
          <time datetime="<?= e($movie['ReleaseYear']) ?>"><?= e($movie['ReleaseYear']) ?></time>
        </div>
        <?php if (!empty($movie['MovieLength'])): ?>
          <div>
            <ion-icon name="time-outline"></ion-icon>
            <time datetime="PT<?= e($movie['MovieLength']) ?>"><?= e($movie['MovieLength']) ?> min</time>
          </div>
        <?php endif; ?>
        <div class="rating">
          <ion-icon name="star"></ion-icon>
          <data><?= e($movie['Rating']) ?></data>
        </div>
        <?php if (!empty($movie['DirectorFirstName']) && !empty($movie['DirectorLastName'])): ?>
          <div>
            <p class="director">
              <span class="director-label">Director</span>
              <span class="director-name"><?= e($movie['DirectorFirstName'] . ' ' . $movie['DirectorLastName']) ?></span>
            </p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <figure class="movie-detail-banner">
      <img src="<?= e('./assets/images/' . $movie['Title'] . '.jpg') ?>"
           alt="<?= e($movie['Title']) ?> movie poster"
           onerror="this.src='<?= asset('images/placeholder.jpg') ?>'">
    </figure>

    <?php if (!empty($movie['trailerURL'])): ?>
      <div class="video">
        <iframe
          src="<?= e($movie['trailerURL']) ?>"
          width="740"
          height="420"
          allow="autoplay; encrypted-media"
          allowfullscreen></iframe>
      </div>
    <?php endif; ?>

    <?php if (!empty($genres)): ?>
      <div class="genre-wrapper">
        <?php foreach ($genres as $genre): ?>
          <button class="genre-btn"><?= e($genre) ?></button>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($isAuthenticated): ?>
      <div class="watchlist-form" style="height: 80px; grid-row: 3; grid-column: 7 / 9">
        <?php if ($inWatchlist): ?>
          <button class="watchlist-btn added" data-movie-id="<?= e($movie['MovieID']) ?>" onclick="removeFromWatchlist(this)">
            <ion-icon name="checkmark-circle"></ion-icon>
            In Watchlist
          </button>
        <?php else: ?>
          <button class="watchlist-btn" data-movie-id="<?= e($movie['MovieID']) ?>" onclick="addToWatchlist(this)">
            <ion-icon name="add-circle"></ion-icon>
            Add to Watchlist
          </button>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($movie['Plot'])): ?>
      <div class="storyline">
        <p class="storyline-title">Storyline</p>
        <p><?= e($movie['Plot']) ?></p>
      </div>
    <?php endif; ?>

    <?php if (!empty($actors)): ?>
      <div class="actors-wrapper">
        <div class="title-wrapper">
          <p class="title">Cast</p>
        </div>
        <ul class="actors-list has-scrollbar">
          <?php foreach ($actors as $actor): ?>
            <li>
              <div class="actor-card">
                <?php if (!empty($actor['image'])): ?>
                  <figure class="actor-banner">
                    <img src="data:image/jpeg;base64,<?= base64_encode($actor['image']) ?>"
                         alt="<?= e($actor['FirstName'] . ' ' . $actor['LastName']) ?>"
                         onerror="this.src='<?= asset('images/actor-placeholder.jpg') ?>'">
                  </figure>
                <?php endif; ?>
                <div class="actor-content">
                  <p class="actor-name"><?= e($actor['FirstName'] . ' ' . $actor['LastName']) ?></p>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

  </div>
</section>

<script>
function addToWatchlist(button) {
  const movieId = button.getAttribute('data-movie-id');

  fetch('<?= url('/watchlist/add') ?>', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'movie_id=' + movieId
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      button.innerHTML = '<ion-icon name="checkmark-circle"></ion-icon> In Watchlist';
      button.classList.add('added');
      button.setAttribute('onclick', 'removeFromWatchlist(this)');
      showNotification(data.message, 'success');
    } else {
      showNotification(data.message, 'error');
    }
  })
  .catch(error => {
    showNotification('An error occurred. Please try again.', 'error');
  });
}

function removeFromWatchlist(button) {
  const movieId = button.getAttribute('data-movie-id');

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
      button.innerHTML = '<ion-icon name="add-circle"></ion-icon> Add to Watchlist';
      button.classList.remove('added');
      button.setAttribute('onclick', 'addToWatchlist(this)');
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
  // Simple notification - you can enhance this
  alert(message);
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
