<?php
/**
 * Поля «логин, email, пароль, подтверждение» — общие для регистрации
 * и для создания администратора в админ-панели.
 *
 * @var array $old    Ранее введённые значения
 * @var array $errors Ошибки сервера
 */
$invalid = fn(string $field): string => isset($errors[$field]) ? ' is-invalid' : '';
?>
<div class="mb-3">
    <label for="username" class="form-label">Логин</label>
    <input type="text" id="username" name="username" class="form-control<?= $invalid('username') ?>"
           value="<?= e($old['username']) ?>" required pattern="[a-zA-Z0-9_]{3,30}">
    <div class="invalid-feedback"><?= e($errors['username'] ?? 'От 3 до 30 символов: латинские буквы, цифры и _.') ?></div>
</div>
<div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input type="email" id="email" name="email" class="form-control<?= $invalid('email') ?>"
           value="<?= e($old['email']) ?>" required>
    <div class="invalid-feedback"><?= e($errors['email'] ?? 'Введите корректный email.') ?></div>
</div>
<div class="mb-3">
    <label for="password" class="form-label">Пароль</label>
    <input type="password" id="password" name="password" class="form-control<?= $invalid('password') ?>"
           required minlength="6">
    <div class="invalid-feedback"><?= e($errors['password'] ?? 'Пароль должен быть не короче 6 символов.') ?></div>
</div>
<div class="mb-3">
    <label for="password_confirm" class="form-label">Повторите пароль</label>
    <input type="password" id="password_confirm" name="password_confirm"
           class="form-control<?= $invalid('password_confirm') ?>" required data-match="password">
    <div class="invalid-feedback"><?= e($errors['password_confirm'] ?? 'Пароли не совпадают.') ?></div>
</div>
