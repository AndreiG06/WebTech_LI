<?php
/**
 * Вспомогательные функции, которые используются во всём приложении.
 */

/**
 * Экранирует строку для безопасного вывода в HTML (защита от XSS).
 *
 * @param mixed $value Любое значение, которое нужно вывести на страницу.
 * @return string Безопасная строка.
 */
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Формирует ссылку на страницу приложения.
 *
 * @param string $page   Название страницы (ключ из config/routes.php).
 * @param array  $params Дополнительные GET-параметры.
 * @return string Готовый адрес, например "index.php?page=book&id=5".
 */
function url(string $page, array $params = []): string
{
    return 'index.php?' . http_build_query(array_merge(['page' => $page], $params));
}

/**
 * Перенаправляет пользователя на другую страницу и завершает скрипт.
 *
 * @param string $page   Название страницы.
 * @param array  $params Дополнительные GET-параметры.
 * @return void
 */
function redirect(string $page, array $params = []): void
{
    header('Location: ' . url($page, $params));
    exit;
}

/**
 * Возвращает CSRF-токен текущей сессии (создаёт его при первом обращении).
 *
 * @return string Случайный токен.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Возвращает скрытое поле формы с CSRF-токеном.
 *
 * @return string HTML-код поля input.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Проверяет CSRF-токен из POST-запроса. При несовпадении останавливает работу.
 *
 * @return void
 */
function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals(csrf_token(), $token)) {
        http_response_code(419);
        exit('Ошибка безопасности: форма устарела. Обновите страницу и попробуйте снова.');
    }
}

/**
 * Проверяет, что текущий запрос отправлен методом POST.
 *
 * @return bool true, если это POST-запрос.
 */
function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Сохраняет одноразовое сообщение, которое покажется на следующей странице.
 *
 * @param string $type    Тип сообщения Bootstrap: success, danger, warning, info.
 * @param string $message Текст сообщения.
 * @return void
 */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Достаёт одноразовое сообщение и удаляет его из сессии.
 *
 * @return array|null Массив ['type' => ..., 'message' => ...] или null.
 */
function get_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * «Отправляет» письмо: в учебном проекте письмо записывается в файл storage/mail.log,
 * чтобы не настраивать почтовый сервер.
 *
 * @param string $to      Адрес получателя.
 * @param string $subject Тема письма.
 * @param string $body    Текст письма.
 * @return bool true, если запись прошла успешно.
 */
function send_mail(string $to, string $subject, string $body): bool
{
    $letter = "==========\n"
        . 'Дата: ' . date('Y-m-d H:i:s') . "\n"
        . "Кому: $to\n"
        . "Тема: $subject\n\n"
        . $body . "\n\n";

    return file_put_contents(MAIL_LOG, $letter, FILE_APPEND) !== false;
}
