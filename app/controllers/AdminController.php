<?php

/**
 * Админ-панель. Все методы доступны только пользователю с ролью «admin».
 *
 * Функции администратора:
 *  1. Управление книгами (просмотр, добавление, редактирование, удаление).
 *  2. Управление пользователями (просмотр, удаление).
 *  3. Создание новых учётных записей администраторов.
 *  4. Управление жанрами (просмотр, добавление, переименование, удаление).
 */
class AdminController extends Controller
{
    /**
     * Проверяет права администратора перед любым действием.
     */
    public function __construct()
    {
        Auth::requireAdmin();
    }

    /**
     * Главная страница админ-панели со статистикой.
     *
     * @return void
     */
    public function index(): void
    {
        $this->render('admin/index', [
            'title'       => 'Админ-панель',
            'booksCount'  => (new Book())->count(),
            'usersCount'  => (new User())->count(),
            'genresCount' => count((new Genre())->all()),
        ]);
    }

    /**
     * Список всех книг.
     *
     * @return void
     */
    public function books(): void
    {
        $this->render('admin/books', [
            'title' => 'Управление книгами',
            'books' => (new Book())->all(),
        ]);
    }

    /**
     * Редактирование книги.
     *
     * @return void
     */
    public function editBook(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $bookModel = new Book();
        $book = $bookModel->find($id);

        if ($book === null) {
            $this->notFound();
            return;
        }

        $genreModel = new Genre();
        $errors = [];

        if (is_post()) {
            verify_csrf();

            [$book, $errors] = Book::validate($_POST, $genreModel->allIds());

            if (empty($errors)) {
                $bookModel->update($id, $book);
                set_flash('success', 'Книга обновлена.');
                redirect('admin_books');
            }
        }

        $this->render('books/form', [
            'title'       => 'Редактирование книги',
            'heading'     => 'Редактирование книги',
            'formAction'  => url('admin_book_edit', ['id' => $id]),
            'submitLabel' => 'Сохранить',
            'book'        => $book,
            'errors'      => $errors,
            'genres'      => $genreModel->all(),
        ]);
    }

    /**
     * Удаление книги (POST).
     *
     * @return void
     */
    public function deleteBook(): void
    {
        if (is_post()) {
            verify_csrf();
            (new Book())->delete((int)($_POST['id'] ?? 0));
            set_flash('success', 'Книга удалена.');
        }
        redirect('admin_books');
    }

    /**
     * Список пользователей.
     *
     * @return void
     */
    public function users(): void
    {
        $this->render('admin/users', [
            'title'         => 'Пользователи',
            'users'         => (new User())->all(),
            'currentUserId' => Auth::user()['id'],
        ]);
    }

    /**
     * Удаление пользователя (POST). Удалить самого себя нельзя.
     *
     * @return void
     */
    public function deleteUser(): void
    {
        if (is_post()) {
            verify_csrf();
            $id = (int)($_POST['id'] ?? 0);

            if ($id === (int)Auth::user()['id']) {
                set_flash('danger', 'Нельзя удалить собственную учётную запись.');
            } else {
                (new User())->delete($id);
                set_flash('success', 'Пользователь удалён.');
            }
        }
        redirect('admin_users');
    }

    /**
     * Создание новой учётной записи с ролью администратора.
     *
     * @return void
     */
    public function createAdmin(): void
    {
        $old = ['username' => '', 'email' => ''];
        $errors = [];

        if (is_post()) {
            verify_csrf();

            $old['username'] = trim($_POST['username'] ?? '');
            $old['email'] = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $errors = AuthController::validateNewUser(
                $old['username'], $old['email'], $password, $_POST['password_confirm'] ?? ''
            );

            if (empty($errors)) {
                (new User())->create($old['username'], $old['email'], $password, 'admin');
                set_flash('success', 'Администратор ' . $old['username'] . ' создан.');
                redirect('admin_users');
            }
        }

        $this->render('admin/create_admin', [
            'title'  => 'Новый администратор',
            'old'    => $old,
            'errors' => $errors,
        ]);
    }

    /**
     * Список жанров и добавление нового жанра.
     *
     * @return void
     */
    public function genres(): void
    {
        $genreModel = new Genre();
        $error = '';

        if (is_post()) {
            verify_csrf();
            $name = trim($_POST['name'] ?? '');
            $error = $this->validateGenreName($name);

            if ($error === '') {
                $genreModel->create($name);
                set_flash('success', 'Жанр «' . $name . '» добавлен.');
                redirect('admin_genres');
            }
        }

        $this->render('admin/genres', [
            'title'  => 'Жанры',
            'genres' => $genreModel->allWithBookCount(),
            'error'  => $error,
        ]);
    }

    /**
     * Переименование жанра (POST).
     *
     * @return void
     */
    public function updateGenre(): void
    {
        if (is_post()) {
            verify_csrf();
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $error = $this->validateGenreName($name, $id);

            if ($error === '') {
                (new Genre())->update($id, $name);
                set_flash('success', 'Жанр переименован.');
            } else {
                set_flash('danger', $error);
            }
        }
        redirect('admin_genres');
    }

    /**
     * Удаление жанра (POST). Жанр, в котором есть книги, удалить нельзя.
     *
     * @return void
     */
    public function deleteGenre(): void
    {
        if (is_post()) {
            verify_csrf();
            $id = (int)($_POST['id'] ?? 0);
            $genreModel = new Genre();

            if ($genreModel->countBooks($id) > 0) {
                set_flash('danger', 'Нельзя удалить жанр, в котором есть книги.');
            } else {
                $genreModel->delete($id);
                set_flash('success', 'Жанр удалён.');
            }
        }
        redirect('admin_genres');
    }

    /**
     * Проверяет название жанра.
     *
     * @param string $name      Название жанра.
     * @param int    $currentId id редактируемого жанра (0 — при добавлении нового).
     * @return string Текст ошибки или пустая строка, если всё в порядке.
     */
    private function validateGenreName(string $name, int $currentId = 0): string
    {
        $v = new Validator();
        $v->required('name', $name, 'Название жанра')->maxLength('name', $name, 50, 'Название жанра');
        if ($v->fails()) {
            return $v->errors()['name'];
        }

        $existing = (new Genre())->findByName($name);
        if ($existing !== null && (int)$existing['id'] !== $currentId) {
            return 'Такой жанр уже существует.';
        }

        return '';
    }
}
