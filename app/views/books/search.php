<?php
/**
 * @var array $filters
 * @var array $genres
 * @var array $books
 */
?>
<h1 class="h3 mb-3">Поиск книг</h1>

<form action="index.php" method="get" class="card card-body mb-4">
    <input type="hidden" name="page" value="search">
    <div class="row g-3">
        <div class="col-md-4">
            <label for="query" class="form-label">Название или автор</label>
            <input type="text" id="query" name="query" class="form-control" maxlength="100"
                   value="<?= e($filters['query']) ?>" placeholder="Например: Булгаков">
        </div>
        <div class="col-md-3">
            <label for="genre_id" class="form-label">Жанр</label>
            <select id="genre_id" name="genre_id" class="form-select">
                <option value="">Любой</option>
                <?php foreach ($genres as $genre): ?>
                    <option value="<?= $genre['id'] ?>" <?= (string)$genre['id'] === (string)$filters['genre_id'] ? 'selected' : '' ?>>
                        <?= e($genre['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="language" class="form-label">Язык</label>
            <select id="language" name="language" class="form-select">
                <option value="">Любой</option>
                <?php foreach (Book::LANGUAGES as $code => $name): ?>
                    <option value="<?= $code ?>" <?= $code === $filters['language'] ? 'selected' : '' ?>><?= $name ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Год издания</label>
            <div class="input-group">
                <input type="number" name="year_from" class="form-control" placeholder="от" min="0"
                       value="<?= e($filters['year_from']) ?>">
                <input type="number" name="year_to" class="form-control" placeholder="до" min="0"
                       value="<?= e($filters['year_to']) ?>">
            </div>
        </div>
    </div>
    <div class="mt-3">
        <button type="submit" class="btn btn-primary">Найти</button>
        <a href="<?= url('search') ?>" class="btn btn-outline-secondary">Сбросить</a>
    </div>
</form>

<p class="text-muted">Найдено книг: <?= count($books) ?></p>

<?php if (empty($books)): ?>
    <div class="alert alert-info">По вашему запросу ничего не найдено. Попробуйте изменить условия поиска.</div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
        <?php foreach ($books as $book): ?>
            <div class="col"><?php require __DIR__ . '/_card.php'; ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
