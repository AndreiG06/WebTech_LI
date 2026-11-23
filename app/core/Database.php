<?php

/**
 * Подключение к базе данных MySQL через PDO.
 *
 * Соединение создаётся один раз и потом переиспользуется.
 */
class Database
{
    /** @var PDO|null Единственное соединение с БД */
    private static ?PDO $connection = null;

    /**
     * Возвращает соединение с базой данных.
     *
     * @return PDO Объект PDO, настроенный на выбрасывание исключений.
     */
    public static function connect(): PDO
    {
        if (self::$connection === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

            self::$connection = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }

        return self::$connection;
    }
}
