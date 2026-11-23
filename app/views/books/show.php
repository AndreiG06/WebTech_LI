<?php /** @var array $book */ ?>
<a href="<?= url('search') ?>" class="btn btn-link px-0 mb-2">← к поиску</a>

<div class="card">
    <div class="card-body">
        <h1 class="h3"><?= e($book['title']) ?></h1>
        <p class="text-muted mb-2"><?= e($book['author']) ?></p>

        <p>
            <span class="badge bg-secondary"><?= e($book['genre_name']) ?></span>
            <span class="badge bg-info text-dark"><?= e(Book::LANGUAGES[$book['language']]) ?></span>
            <?php if ($book['is_recommended']): ?>
                <span class="badge bg-success">Рекомендую</span>
            <?php endif; ?>
        </p>

        <table class="table table-sm w-auto">
            <tr><th>Год издания</th><td><?= e($book['year']) ?></td></tr>
            <tr><th>Добавил</th><td><?= e($book['added_by'] ?? 'удалённый пользователь') ?></td></tr>
            <tr><th>Дата добавления</th><td><?= e(date('d.m.Y', strtotime($book['created_at']))) ?></td></tr>
        </table>

        <h2 class="h5">Описание</h2>
        <p class="description"><?= nl2br(e($book['description'])) ?></p>

        <?php if (Auth::isAdmin()): ?>
            <a href="<?= url('admin_book_edit', ['id' => $book['id']]) ?>" class="btn btn-outline-primary btn-sm">Редактировать</a>
        <?php endif; ?>
    </div>
</div>
