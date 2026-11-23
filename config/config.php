<?php
/**
 * Настройки приложения.
 *
 * Перед запуском укажите свои параметры подключения к MySQL.
 * Для XAMPP по умолчанию: пользователь root без пароля.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'bookshelf');
define('DB_USER', 'root');
define('DB_PASS', '');

// Адрес сайта — нужен для ссылки в письме восстановления пароля
define('APP_URL', 'http://localhost:8080');

// Файл, куда «отправляются» письма (вместо настоящего почтового сервера)
define('MAIL_LOG', ROOT . '/storage/mail.log');
