<?php
/**
 * @var int $booksCount
 * @var int $usersCount
 * @var int $genresCount
 */
?>
<h1 class="h3 mb-3">Админ-панель</h1>
<?php require __DIR__ . '/_menu.php'; ?>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center"><div class="card-body">
            <div class="display-6"><?= $booksCount ?></div>
            <div class="text-muted">книг</div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card text-center"><div class="card-body">
            <div class="display-6"><?= $usersCount ?></div>
            <div class="text-muted">пользователей</div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card text-center"><div class="card-body">
            <div class="display-6"><?= $genresCount ?></div>
            <div class="text-muted">жанров</div>
        </div></div>
    </div>
</div>

<h2 class="h5">Возможности администратора</h2>
<ol>
    <li><a href="<?= url('admin_books') ?>">Управление книгами</a>: просмотр, добавление, редактирование, удаление.</li>
    <li><a href="<?= url('admin_users') ?>">Управление пользователями</a>: просмотр и удаление учётных записей.</li>
    <li><a href="<?= url('admin_create_admin') ?>">Создание учётной записи администратора</a>.</li>
    <li><a href="<?= url('admin_genres') ?>">Управление жанрами</a>: добавление, переименование, удаление.</li>
</ol>
