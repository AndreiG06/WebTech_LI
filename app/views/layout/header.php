<?php
/** @var string|null $title Заголовок страницы */
$currentUser = Auth::user();
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Книжная полка') ?> — Книжная полка</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= url('home') ?>">📚 Книжная полка</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainMenu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= url('home') ?>">Главная</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('search') ?>">Поиск</a></li>
                <?php if ($currentUser): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('book_create') ?>">Добавить книгу</a></li>
                <?php endif; ?>
                <?php if ($currentUser && $currentUser['role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link text-warning" href="<?= url('admin') ?>">Админ-панель</a></li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav">
                <?php if ($currentUser): ?>
                    <li class="nav-item">
                        <span class="navbar-text me-3">👤 <?= e($currentUser['username']) ?></span>
                    </li>
                    <li class="nav-item">
                        <form action="<?= url('logout') ?>" method="post" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-light btn-sm mt-1">Выйти</button>
                        </form>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('login') ?>">Вход</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('register') ?>">Регистрация</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container my-4 flex-grow-1">
    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
