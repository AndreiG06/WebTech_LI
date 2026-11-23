<h1 class="h3 mb-3">Новый администратор</h1>
<?php require __DIR__ . '/_menu.php'; ?>

<div class="row">
    <div class="col-md-6 col-lg-5">
        <form action="<?= url('admin_create_admin') ?>" method="post" class="card card-body needs-validation" novalidate>
            <?= csrf_field() ?>
            <?php require __DIR__ . '/../auth/_user_fields.php'; ?>
            <button type="submit" class="btn btn-primary">Создать администратора</button>
        </form>
    </div>
</div>
