<?php

/**
 * Модель жанра (таблица genres).
 */
class Genre extends Model
{
    /**
     * Возвращает все жанры по алфавиту.
     *
     * @return array Список жанров.
     */
    public function all(): array
    {
        return $this->db->query('SELECT * FROM genres ORDER BY name')->fetchAll();
    }

    /**
     * Возвращает все жанры с количеством книг в каждом.
     *
     * @return array Список жанров с полем books_count.
     */
    public function allWithBookCount(): array
    {
        return $this->db->query(
            'SELECT g.id, g.name, COUNT(b.id) AS books_count
             FROM genres g
             LEFT JOIN books b ON b.genre_id = g.id
             GROUP BY g.id
             ORDER BY g.name'
        )->fetchAll();
    }

    /**
     * Ищет жанр по id.
     *
     * @param int $id Идентификатор жанра.
     * @return array|null Данные жанра или null.
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM genres WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Ищет жанр по названию (нужно для проверки на дубликаты).
     *
     * @param string $name Название жанра.
     * @return array|null Данные жанра или null.
     */
    public function findByName(string $name): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM genres WHERE name = ?');
        $stmt->execute([$name]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Возвращает список id всех жанров (для проверки выпадающего списка).
     *
     * @return int[] Массив идентификаторов.
     */
    public function allIds(): array
    {
        return array_map('intval', $this->db->query('SELECT id FROM genres')->fetchAll(PDO::FETCH_COLUMN));
    }

    /**
     * Добавляет новый жанр.
     *
     * @param string $name Название жанра.
     * @return void
     */
    public function create(string $name): void
    {
        $stmt = $this->db->prepare('INSERT INTO genres (name) VALUES (?)');
        $stmt->execute([$name]);
    }

    /**
     * Переименовывает жанр.
     *
     * @param int    $id   Идентификатор жанра.
     * @param string $name Новое название.
     * @return void
     */
    public function update(int $id, string $name): void
    {
        $stmt = $this->db->prepare('UPDATE genres SET name = ? WHERE id = ?');
        $stmt->execute([$name, $id]);
    }

    /**
     * Удаляет жанр.
     *
     * @param int $id Идентификатор жанра.
     * @return void
     */
    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM genres WHERE id = ?');
        $stmt->execute([$id]);
    }

    /**
     * Считает, сколько книг относится к жанру.
     *
     * @param int $id Идентификатор жанра.
     * @return int Количество книг.
     */
    public function countBooks(int $id): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM books WHERE genre_id = ?');
        $stmt->execute([$id]);
        return (int)$stmt->fetchColumn();
    }
}
