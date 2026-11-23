<?php

/**
 * Модель пользователя (таблица users).
 */
class User extends Model
{
    /**
     * Ищет пользователя по id.
     *
     * @param int $id Идентификатор пользователя.
     * @return array|null Данные пользователя или null, если не найден.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Ищет пользователя по email.
     *
     * @param string $email Адрес электронной почты.
     * @return array|null Данные пользователя или null.
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Ищет пользователя по логину.
     *
     * @param string $username Имя пользователя.
     * @return array|null Данные пользователя или null.
     */
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Создаёт нового пользователя. Пароль сохраняется только в виде хэша.
     *
     * @param string $username Имя пользователя.
     * @param string $email    Email.
     * @param string $password Пароль в открытом виде (будет захэширован).
     * @param string $role     Роль: "user" или "admin".
     * @return int id созданного пользователя.
     */
    public function create(string $username, string $email, string $password, string $role = 'user'): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Меняет пароль пользователя.
     *
     * @param int    $id       Идентификатор пользователя.
     * @param string $password Новый пароль в открытом виде.
     * @return void
     */
    public function updatePassword(int $id, string $password): void
    {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
    }

    /**
     * Возвращает всех пользователей вместе с количеством добавленных книг.
     *
     * @return array Список пользователей.
     */
    public function all(): array
    {
        return $this->db->query(
            'SELECT u.id, u.username, u.email, u.role, u.created_at, COUNT(b.id) AS books_count
             FROM users u
             LEFT JOIN books b ON b.user_id = u.id
             GROUP BY u.id
             ORDER BY u.id'
        )->fetchAll();
    }

    /**
     * Удаляет пользователя. Его книги остаются, но без автора-пользователя.
     *
     * @param int $id Идентификатор пользователя.
     * @return void
     */
    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
    }

    /**
     * Считает количество пользователей.
     *
     * @return int Количество записей в таблице users.
     */
    public function count(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }
}
