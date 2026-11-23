<?php
/**
 * @var string $token
 * @var array  $errors
 */
?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <h1 class="h3 mb-3">Новый пароль</h1>

        <form action="<?= url('reset') ?>" method="post" class="card card-body needs-validation" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= e($token) ?>">

            <div class="mb-3">
                <label for="password" class="form-label">Новый пароль</label>
                <input type="password" id="password" name="password" required minlength="6"
                       class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>">
                <div class="invalid-feedback"><?= e($errors['password'] ?? 'Пароль должен быть не короче 6 символов.') ?></div>
            </div>
            <div class="mb-3">
                <label for="password_confirm" class="form-label">Повторите пароль</label>
                <input type="password" id="password_confirm" name="password_confirm" required data-match="password"
                       class="form-control<?= isset($errors['password_confirm']) ? ' is-invalid' : '' ?>">
                <div class="invalid-feedback"><?= e($errors['password_confirm'] ?? 'Пароли не совпадают.') ?></div>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить пароль</button>
        </form>
    </div>
</div>
