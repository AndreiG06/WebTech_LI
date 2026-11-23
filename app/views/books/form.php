<?php
/**
 * Форма книги — общая для добавления и редактирования.
 *
 * @var string $heading     Заголовок формы
 * @var string $formAction  Куда отправлять форму
 * @var string $submitLabel Текст кнопки
 * @var array  $book        Значения полей
 * @var array  $errors      Ошибки сервера [поле => текст]
 * @var array  $genres      Список жанров
 */

/**
 * Возвращает класс is-invalid, если у поля есть ошибка сервера.
 */
$invalid = fn(string $field): string => isset($errors[$field]) ? ' is-invalid' : '';
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h1 class="h3 mb-3"><?= e($heading) ?></h1>

        <?php if ($errors): ?>
            <div class="alert alert-danger">Форма заполнена с ошибками. Исправьте отмеченные поля.</div>
        <?php endif; ?>

        <form action="<?= e($formAction) ?>" method="post" class="card card-body needs-validation" novalidate>
            <?= csrf_field() ?>

            <!-- 1. Текстовое поле -->
            <div class="mb-3">
                <label for="title" class="form-label">Название *</label>
                <input type="text" id="title" name="title" class="form-control<?= $invalid('title') ?>"
                       value="<?= e($book['title']) ?>" required maxlength="200">
                <div class="invalid-feedback"><?= e($errors['title'] ?? 'Введите название книги.') ?></div>
            </div>

            <!-- 2. Текстовое поле -->
            <div class="mb-3">
                <label for="author" class="form-label">Автор *</label>
                <input type="text" id="author" name="author" class="form-control<?= $invalid('author') ?>"
                       value="<?= e($book['author']) ?>" required minlength="2" maxlength="100">
                <div class="invalid-feedback"><?= e($errors['author'] ?? 'Введите автора (от 2 символов).') ?></div>
            </div>

            <div class="row">
                <!-- 3. Выпадающий список -->
                <div class="col-md-6 mb-3">
                    <label for="genre_id" class="form-label">Жанр *</label>
                    <select id="genre_id" name="genre_id" class="form-select<?= $invalid('genre_id') ?>" required>
                        <option value="">— выберите жанр —</option>
                        <?php foreach ($genres as $genre): ?>
                            <option value="<?= $genre['id'] ?>" <?= (string)$genre['id'] === (string)$book['genre_id'] ? 'selected' : '' ?>>
                                <?= e($genre['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback"><?= e($errors['genre_id'] ?? 'Выберите жанр.') ?></div>
                </div>

                <!-- 4. Числовое поле -->
                <div class="col-md-6 mb-3">
                    <label for="year" class="form-label">Год издания *</label>
                    <input type="number" id="year" name="year" class="form-control<?= $invalid('year') ?>"
                           value="<?= e($book['year']) ?>" required min="1000" max="<?= date('Y') ?>">
                    <div class="invalid-feedback"><?= e($errors['year'] ?? 'Год должен быть от 1000 до ' . date('Y') . '.') ?></div>
                </div>
            </div>

            <!-- 5. Переключатели (radio) -->
            <div class="mb-3">
                <label class="form-label d-block">Язык книги *</label>
                <?php foreach (Book::LANGUAGES as $code => $name): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input<?= $invalid('language') ?>" type="radio" name="language"
                               id="lang_<?= $code ?>" value="<?= $code ?>" required
                               <?= $book['language'] === $code ? 'checked' : '' ?>>
                        <label class="form-check-label" for="lang_<?= $code ?>"><?= $name ?></label>
                    </div>
                <?php endforeach; ?>
                <?php if (isset($errors['language'])): ?>
                    <div class="text-danger small"><?= e($errors['language']) ?></div>
                <?php endif; ?>
            </div>

            <!-- 6. Флажок (checkbox) -->
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="is_recommended" name="is_recommended" value="1"
                       <?= $book['is_recommended'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_recommended">Рекомендую эту книгу</label>
            </div>

            <!-- 7. Многострочное поле -->
            <div class="mb-3">
                <label for="description" class="form-label">Описание *</label>
                <textarea id="description" name="description" rows="5"
                          class="form-control<?= $invalid('description') ?>"
                          required minlength="10" maxlength="2000"><?= e($book['description']) ?></textarea>
                <div class="invalid-feedback"><?= e($errors['description'] ?? 'Описание: от 10 до 2000 символов.') ?></div>
            </div>

            <div>
                <button type="submit" class="btn btn-primary"><?= e($submitLabel) ?></button>
            </div>
        </form>
    </div>
</div>
