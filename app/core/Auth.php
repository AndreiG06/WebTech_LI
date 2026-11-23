<?php

/**
 * Работа с авторизацией: вход, выход и проверка прав доступа.
 *
 * В сессии хранится только id пользователя, а остальные данные
 * (в том числе роль) каждый раз берутся из базы. Поэтому если админ удалит
 * пользователя, тот сразу потеряет доступ.
 */
class Auth
{
    /** @var array|null Данные текущего пользователя (кэш на время запроса) */
    private static ?array $currentUser = null;

    /**
     * Авторизует пользователя: записывает его id в сессию.
     *
     * @param array $user Строка из таблицы users.
     * @return void
     */
    public static function login(array $user): void
    {
        // Новый id сессии защищает от атаки «фиксация сессии»
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        self::$currentUser = null;
    }

    /**
     * Выходит из аккаунта: полностью очищает сессию.
     *
     * @return void
     */
    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        session_start();
        session_regenerate_id(true);
        self::$currentUser = null;
    }

    /**
     * Возвращает данные текущего пользователя.
     *
     * @return array|null Массив с полями id, username, email, role или null, если гость.
     */
    public static function user(): ?array
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }

        if (self::$currentUser === null) {
            $user = (new User())->findById((int)$_SESSION['user_id']);
            if ($user === null) {
                // Пользователь был удалён — выкидываем его из системы
                unset($_SESSION['user_id']);
                return null;
            }
            self::$currentUser = $user;
        }

        return self::$currentUser;
    }

    /**
     * Проверяет, вошёл ли пользователь в систему.
     *
     * @return bool true, если пользователь авторизован.
     */
    public static function check(): bool
    {
        return self::user() !== null;
    }

    /**
     * Проверяет, является ли текущий пользователь администратором.
     *
     * @return bool true, если роль пользователя — admin.
     */
    public static function isAdmin(): bool
    {
        $user = self::user();
        return $user !== null && $user['role'] === 'admin';
    }

    /**
     * Пускает дальше только авторизованных. Гостя отправляет на страницу входа.
     *
     * @return void
     */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            set_flash('warning', 'Чтобы открыть эту страницу, войдите в систему.');
            redirect('login');
        }
    }

    /**
     * Пускает дальше только администратора. Остальным показывает ошибку 403.
     *
     * @return void
     */
    public static function requireAdmin(): void
    {
        self::requireLogin();

        if (!self::isAdmin()) {
            http_response_code(403);
            exit('Доступ запрещён: эта страница только для администратора.');
        }
    }
}
