<?php
/**
 * Таблица маршрутов приложения.
 *
 * Ключ — значение параметра ?page=..., значение — [контроллер, метод].
 */
return [
    // Общедоступные страницы
    'home'   => ['HomeController', 'index'],
    'search' => ['BookController', 'search'],
    'book'   => ['BookController', 'show'],

    // Аутентификация
    'register' => ['AuthController', 'register'],
    'login'    => ['AuthController', 'login'],
    'logout'   => ['AuthController', 'logout'],
    'forgot'   => ['AuthController', 'forgot'],
    'reset'    => ['AuthController', 'reset'],

    // Только для авторизованных пользователей
    'book_create' => ['BookController', 'create'],

    // Только для администратора
    'admin'              => ['AdminController', 'index'],
    'admin_books'        => ['AdminController', 'books'],
    'admin_book_edit'    => ['AdminController', 'editBook'],
    'admin_book_delete'  => ['AdminController', 'deleteBook'],
    'admin_users'        => ['AdminController', 'users'],
    'admin_user_delete'  => ['AdminController', 'deleteUser'],
    'admin_create_admin' => ['AdminController', 'createAdmin'],
    'admin_genres'       => ['AdminController', 'genres'],
    'admin_genre_update' => ['AdminController', 'updateGenre'],
    'admin_genre_delete' => ['AdminController', 'deleteGenre'],
];
