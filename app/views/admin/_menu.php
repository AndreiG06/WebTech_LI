<?php
/** Меню админ-панели. Подсвечивает текущий раздел. */
$adminPage = $_GET['page'] ?? 'admin';
$adminLinks = [
    'admin'              => 'Обзор',
    'admin_books'        => 'Книги',
    'admin_users'        => 'Пользователи',
    'admin_create_admin' => 'Новый админ',
    'admin_genres'       => 'Жанры',
];
?>
<ul class="nav nav-tabs mb-4">
    <?php foreach ($adminLinks as $page => $label): ?>
        <li class="nav-item">
            <a class="nav-link<?= $adminPage === $page ? ' active' : '' ?>" href="<?= url($page) ?>"><?= $label ?></a>
        </li>
    <?php endforeach; ?>
</ul>
