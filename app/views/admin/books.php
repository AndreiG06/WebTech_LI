<?php /** @var array $books */ ?>
<h1 class="h3 mb-3">Управление книгами</h1>
<?php require __DIR__ . '/_menu.php'; ?>

<a href="<?= url('book_create') ?>" class="btn btn-success mb-3">+ Добавить книгу</a>

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Автор</th>
            <th>Жанр</th>
            <th>Год</th>
            <th>Добавил</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($books as $book): ?>
            <tr>
                <td><?= $book['id'] ?></td>
                <td><a href="<?= url('book', ['id' => $book['id']]) ?>"><?= e($book['title']) ?></a></td>
                <td><?= e($book['author']) ?></td>
                <td><?= e($book['genre_name']) ?></td>
                <td><?= e($book['year']) ?></td>
                <td><?= e($book['added_by'] ?? '—') ?></td>
                <td class="text-nowrap">
                    <a href="<?= url('admin_book_edit', ['id' => $book['id']]) ?>" class="btn btn-sm btn-outline-primary">Изменить</a>
                    <form action="<?= url('admin_book_delete') ?>" method="post" class="d-inline"
                          data-confirm="Удалить книгу «<?= e($book['title']) ?>»?"
                          onsubmit="return confirm(this.dataset.confirm);">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $book['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
