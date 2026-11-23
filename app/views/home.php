<?php
/**
 * @var array $latestBooks
 * @var array $recommended
 * @var array $genres
 * @var int   $booksCount
 */
?>
<div class="p-4 mb-4 bg-light rounded-3 border">
    <h1 class="h2">Добро пожаловать на «Книжную полку»!</h1>
    <p class="mb-3">Каталог книг, которые советуют читатели. Сейчас в каталоге книг: <strong><?= $booksCount ?></strong>.</p>
    <a href="<?= url('search') ?>" class="btn btn-primary">Найти книгу</a>
    <?php if (!Auth::check()): ?>
        <a href="<?= url('register') ?>" class="btn btn-outline-primary">Зарегистрироваться и добавить свою</a>
    <?php endif; ?>
</div>

<div class="row">
    <div class="col-lg-9">
        <h2 class="h4 mb-3">Новые поступления</h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3 mb-4">
            <?php foreach ($latestBooks as $book): ?>
                <div class="col"><?php require __DIR__ . '/books/_card.php'; ?></div>
            <?php endforeach; ?>
        </div>

        <h2 class="h4 mb-3">⭐ Читатели рекомендуют</h2>
        <div class="row row-cols-1 row-cols-md-2 g-3 mb-4">
            <?php foreach ($recommended as $book): ?>
                <div class="col"><?php require __DIR__ . '/books/_card.php'; ?></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="col-lg-3">
        <h2 class="h4 mb-3">Жанры</h2>
        <ul class="list-group">
            <?php foreach ($genres as $genre): ?>
                <a href="<?= url('search', ['genre_id' => $genre['id']]) ?>"
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <?= e($genre['name']) ?>
                    <span class="badge bg-primary rounded-pill"><?= $genre['books_count'] ?></span>
                </a>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
