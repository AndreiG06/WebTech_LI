<?php
/**
 * @var string $email
 * @var string $error
 */
?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <h1 class="h3 mb-3">Вход</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <form action="<?= url('login') ?>" method="post" class="card card-body needs-validation" novalidate>
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= e($email) ?>" required>
                <div class="invalid-feedback">Введите корректный email.</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <div class="invalid-feedback">Введите пароль.</div>
            </div>
            <button type="submit" class="btn btn-primary">Войти</button>
            <div class="mt-3 small">
                <a href="<?= url('forgot') ?>">Забыли пароль?</a> ·
                <a href="<?= url('register') ?>">Регистрация</a>
            </div>
        </form>
    </div>
</div>
