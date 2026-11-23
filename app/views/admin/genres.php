<?php
/**
 * @var array  $genres
 * @var string $error
 */
?>
<h1 class="h3 mb-3">Жанры</h1>
<?php require __DIR__ . '/_menu.php'; ?>

<form action="<?= url('admin_genres') ?>" method="post" class="row g-2 mb-4 needs-validation" novalidate>
    <?= csrf_field() ?>
    <div class="col-md-5">
        <input type="text" name="name" class="form-control<?= $error ? ' is-invalid' : '' ?>"
               placeholder="Название нового жанра" required maxlength="50">
        <div class="invalid-feedback"><?= e($error ?: 'Введите название жанра.') ?></div>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-success">Добавить</button>
    </div>
</form>

<table class="table align-middle">
    <thead>
    <tr><th>ID</th><th>Название</th><th>Книг</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($genres as $genre): ?>
        <tr>
            <td><?= $genre['id'] ?></td>
            <td>
                <form action="<?= url('admin_genre_update') ?>" method="post" class="d-flex gap-2">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $genre['id'] ?>">
                    <input type="text" name="name" value="<?= e($genre['name']) ?>"
                           class="form-control form-control-sm" required maxlength="50">
                    <button type="submit" class="btn btn-sm btn-outline-primary">Сохранить</button>
                </form>
            </td>
            <td><?= $genre['books_count'] ?></td>
            <td>
                <form action="<?= url('admin_genre_delete') ?>" method="post"
                      onsubmit="return confirm('Удалить жанр?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $genre['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger"
                        <?= $genre['books_count'] > 0 ? 'disabled title="В жанре есть книги"' : '' ?>>Удалить</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
