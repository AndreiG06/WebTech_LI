<?php
/**
 * @var string $email
 * @var string $error
 * @var bool   $sent
 */
?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <h1 class="h3 mb-3">Восстановление пароля</h1>

        <?php if ($sent): ?>
            <div class="alert alert-success">
                Если такой email зарегистрирован, мы отправили на него ссылку для смены пароля.
                <hr>
                <small>Учебный режим: письмо записано в файл <code>storage/mail.log</code>.</small>
            </div>
            <a href="<?= url('login') ?>">← Вернуться ко входу</a>
        <?php else: ?>
            <form action="<?= url('forgot') ?>" method="post" class="card card-body needs-validation" novalidate>
                <?= csrf_field() ?>
                <p class="text-muted">Укажите email, который вы использовали при регистрации.</p>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control<?= $error ? ' is-invalid' : '' ?>"
                           value="<?= e($email) ?>" required>
                    <div class="invalid-feedback"><?= e($error ?: 'Введите корректный email.') ?></div>
                </div>
                <button type="submit" class="btn btn-primary">Отправить ссылку</button>
            </form>
        <?php endif; ?>
    </div>
</div>
