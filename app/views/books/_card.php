<?php
/**
 * Карточка книги (используется на главной и в поиске).
 * @var array $book
 */
?>
<div class="card h-100 book-card">
    <div class="card-body">
        <h5 class="card-title">
            <a href="<?= url('book', ['id' => $book['id']]) ?>" class="text-decoration-none">
                <?= e($book['title']) ?>
            </a>
        </h5>
        <h6 class="card-subtitle mb-2 text-muted"><?= e($book['author']) ?>, <?= e($book['year']) ?></h6>
        <span class="badge bg-secondary"><?= e($book['genre_name']) ?></span>
        <span class="badge bg-info text-dark"><?= e(Book::LANGUAGES[$book['language']]) ?></span>
        <?php if ($book['is_recommended']): ?>
            <span class="badge bg-success">Рекомендую</span>
        <?php endif; ?>
    </div>
</div>
