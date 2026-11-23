<?php

/**
 * Модель книги (таблица books) — основной ресурс приложения.
 */
class Book extends Model
{
    /** Языки книги: значение в БД => название для пользователя */
    public const LANGUAGES = [
        'ru'    => 'Русский',
        'en'    => 'Английский',
        'other' => 'Другой',
    ];

    /** Общая часть запроса: книга + название жанра + кто добавил */
    private const SELECT_WITH_JOINS =
        'SELECT b.*, g.name AS genre_name, u.username AS added_by
         FROM books b
         JOIN genres g ON g.id = b.genre_id
         LEFT JOIN users u ON u.id = b.user_id';

    /**
     * Последние добавленные книги.
     *
     * @param int $limit Сколько книг вернуть.
     * @return array Список книг.
     */
    public function latest(int $limit): array
    {
        $stmt = $this->db->prepare(self::SELECT_WITH_JOINS . ' ORDER BY b.created_at DESC, b.id DESC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Книги с отметкой «рекомендую».
     *
     * @param int $limit Сколько книг вернуть.
     * @return array Список книг.
     */
    public function recommended(int $limit): array
    {
        $stmt = $this->db->prepare(
            self::SELECT_WITH_JOINS . ' WHERE b.is_recommended = 1 ORDER BY b.id DESC LIMIT ?'
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Все книги (для админ-панели).
     *
     * @return array Список книг.
     */
    public function all(): array
    {
        return $this->db->query(self::SELECT_WITH_JOINS . ' ORDER BY b.id DESC')->fetchAll();
    }

    /**
     * Ищет книгу по id.
     *
     * @param int $id Идентификатор книги.
     * @return array|null Данные книги или null.
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(self::SELECT_WITH_JOINS . ' WHERE b.id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Поиск книг по критериям. Пустые критерии не учитываются.
     *
     * @param array $filters Ключи: query (название или автор), genre_id, language, year_from, year_to.
     * @return array Найденные книги.
     */
    public function search(array $filters): array
    {
        $where = [];
        $params = [];

        if ($filters['query'] !== '') {
            $where[] = '(b.title LIKE ? OR b.author LIKE ?)';
            $params[] = '%' . $filters['query'] . '%';
            $params[] = '%' . $filters['query'] . '%';
        }
        if ($filters['genre_id'] !== '') {
            $where[] = 'b.genre_id = ?';
            $params[] = (int)$filters['genre_id'];
        }
        if ($filters['language'] !== '') {
            $where[] = 'b.language = ?';
            $params[] = $filters['language'];
        }
        if ($filters['year_from'] !== '') {
            $where[] = 'b.year >= ?';
            $params[] = (int)$filters['year_from'];
        }
        if ($filters['year_to'] !== '') {
            $where[] = 'b.year <= ?';
            $params[] = (int)$filters['year_to'];
        }

        $sql = self::SELECT_WITH_JOINS;
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY b.title';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Добавляет книгу.
     *
     * @param array $data   Проверенные данные из формы (см. validate()).
     * @param int   $userId Кто добавил книгу.
     * @return int id новой книги.
     */
    public function create(array $data, int $userId): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO books (title, author, genre_id, year, language, is_recommended, description, user_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['title'], $data['author'], $data['genre_id'], $data['year'],
            $data['language'], $data['is_recommended'], $data['description'], $userId,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Обновляет книгу.
     *
     * @param int   $id   Идентификатор книги.
     * @param array $data Проверенные данные из формы.
     * @return void
     */
    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE books
             SET title = ?, author = ?, genre_id = ?, year = ?, language = ?, is_recommended = ?, description = ?
             WHERE id = ?'
        );
        $stmt->execute([
            $data['title'], $data['author'], $data['genre_id'], $data['year'],
            $data['language'], $data['is_recommended'], $data['description'], $id,
        ]);
    }

    /**
     * Удаляет книгу.
     *
     * @param int $id Идентификатор книги.
     * @return void
     */
    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM books WHERE id = ?');
        $stmt->execute([$id]);
    }

    /**
     * Считает количество книг.
     *
     * @return int Количество записей в таблице books.
     */
    public function count(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM books')->fetchColumn();
    }

    /**
     * Проверяет данные формы книги на стороне сервера.
     * Используется и при добавлении, и при редактировании.
     *
     * @param array $input    Сырые данные из $_POST.
     * @param int[] $genreIds Допустимые id жанров.
     * @return array Массив из двух элементов: [очищенные данные, ошибки].
     */
    public static function validate(array $input, array $genreIds): array
    {
        $data = [
            'title'          => trim($input['title'] ?? ''),
            'author'         => trim($input['author'] ?? ''),
            'genre_id'       => $input['genre_id'] ?? '',
            'year'           => trim($input['year'] ?? ''),
            'language'       => $input['language'] ?? '',
            'is_recommended' => isset($input['is_recommended']) ? 1 : 0,
            'description'    => trim($input['description'] ?? ''),
        ];

        $v = new Validator();
        $v->required('title', $data['title'], 'Название')
          ->maxLength('title', $data['title'], 200, 'Название');
        $v->required('author', $data['author'], 'Автор')
          ->minLength('author', $data['author'], 2, 'Автор')
          ->maxLength('author', $data['author'], 100, 'Автор');
        $v->inList('genre_id', $data['genre_id'], $genreIds, 'Жанр');
        $v->required('year', $data['year'], 'Год издания')
          ->integerBetween('year', $data['year'], 1000, (int)date('Y'), 'Год издания');
        $v->inList('language', $data['language'], array_keys(self::LANGUAGES), 'Язык');
        $v->required('description', $data['description'], 'Описание')
          ->minLength('description', $data['description'], 10, 'Описание')
          ->maxLength('description', $data['description'], 2000, 'Описание');

        return [$data, $v->errors()];
    }
}
