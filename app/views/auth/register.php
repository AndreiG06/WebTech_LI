<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <h1 class="h3 mb-3">Регистрация</h1>

        <form action="<?= url('register') ?>" method="post" class="card card-body needs-validation" novalidate>
            <?= csrf_field() ?>
            <?php require __DIR__ . '/_user_fields.php'; ?>
            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
            <div class="mt-3 small">Уже есть аккаунт? <a href="<?= url('login') ?>">Войти</a></div>
        </form>
    </div>
</div>
