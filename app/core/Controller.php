<?php

/**
 * Базовый контроллер. От него наследуются все остальные контроллеры.
 */
abstract class Controller
{
    /**
     * Выводит страницу: шапку, сам шаблон и подвал.
     *
     * @param string $view Путь к шаблону относительно app/views без ".php" (например "books/show").
     * @param array  $data Переменные, которые будут доступны в шаблоне.
     * @return void
     */
    protected function render(string $view, array $data = []): void
    {
        extract($data);

        require ROOT . '/app/views/layout/header.php';
        require ROOT . "/app/views/$view.php";
        require ROOT . '/app/views/layout/footer.php';
    }

    /**
     * Показывает страницу «404 — не найдено».
     *
     * @return void
     */
    public function notFound(): void
    {
        http_response_code(404);
        $this->render('errors/404', ['title' => 'Страница не найдена']);
    }
}
