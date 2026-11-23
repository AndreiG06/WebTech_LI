<?php
/**
 * @var array $users
 * @var int   $currentUserId
 */
?>
<h1 class="h3 mb-3">Пользователи</h1>
<?php require __DIR__ . '/_menu.php'; ?>

<a href="<?= url('admin_create_admin') ?>" class="btn btn-success mb-3">+ Создать администратора</a>

<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
        <tr>
            <th>ID</th>
            <th>Логин</th>
            <th>Email</th>
            <th>Роль</th>
            <th>Книг</th>
            <th>Зарегистрирован</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= e($user['username']) ?></td>
                <td><?= e($user['email']) ?></td>
                <td>
                    <?php if ($user['role'] === 'admin'): ?>
                        <span class="badge bg-warning text-dark">администратор</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">пользователь</span>
                    <?php endif; ?>
                </td>
                <td><?= $user['books_count'] ?></td>
                <td><?= e(date('d.m.Y', strtotime($user['created_at']))) ?></td>
                <td>
                    <?php if ((int)$user['id'] !== (int)$currentUserId): ?>
                        <form action="<?= url('admin_user_delete') ?>" method="post"
                              data-confirm="Удалить пользователя <?= e($user['username']) ?>?"
                              onsubmit="return confirm(this.dataset.confirm);">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                        </form>
                    <?php else: ?>
                        <span class="text-muted small">это вы</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
