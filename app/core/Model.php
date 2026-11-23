<?php

/**
 * Базовая модель. Даёт всем моделям доступ к базе данных.
 */
abstract class Model
{
    /** @var PDO Соединение с базой данных */
    protected PDO $db;

    /**
     * Получает соединение с БД при создании модели.
     */
    public function __construct()
    {
        $this->db = Database::connect();
    }
}
