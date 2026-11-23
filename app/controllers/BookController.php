<?php

/**
 * Книги: поиск, просмотр и добавление.
 */
class BookController extends Controller
{
    /**
     * Форма поиска книг и результаты. Доступна всем.
     *
     * @return void
     */
    public function search(): void
    {
        $filters = [
            'query'     => trim($_GET['query'] ?? ''),
            'genre_id'  => $_GET['genre_id'] ?? '',
            'language'  => $_GET['language'] ?? '',
            'year_from' => $_GET['year_from'] ?? '',
            'year_to'   => $_GET['year_to'] ?? '',
        ];

        // Некорректные значения фильтров просто игнорируем
        if (!ctype_digit((string)$filters['genre_id'])) {
            $filters['genre_id'] = '';
        }
        if (!array_key_exists($filters['language'], Book::LANGUAGES)) {
            $filters['language'] = '';
        }
        foreach (['year_from', 'year_to'] as $key) {
            if (!ctype_digit((string)$filters[$key])) {
                $filters[$key] = '';
            }
        }

        $this->render('books/search', [
            'title'   => 'Поиск книг',
            'filters' => $filters,
            'genres'  => (new Genre())->all(),
            'books'   => (new Book())->search($filters),
        ]);
    }

    /**
     * Страница одной книги. Доступна всем.
     *
     * @return void
     */
    public function show(): void
    {
        $book = (new Book())->find((int)($_GET['id'] ?? 0));

        if ($book === null) {
            $this->notFound();
            return;
        }

        $this->render('books/show', [
            'title' => $book['title'],
            'book'  => $book,
        ]);
    }

    /**
     * Форма добавления книги. Только для авторизованных пользователей.
     *
     * @return void
     */
    public function create(): void
    {
        Auth::requireLogin();

        $genreModel = new Genre();
        $book = ['title' => '', 'author' => '', 'genre_id' => '', 'year' => '',
                 'language' => 'ru', 'is_recommended' => 0, 'description' => ''];
        $errors = [];

        if (is_post()) {
            verify_csrf();

            [$book, $errors] = Book::validate($_POST, $genreModel->allIds());

            if (empty($errors)) {
                $id = (new Book())->create($book, Auth::user()['id']);
                set_flash('success', 'Книга успешно добавлена!');
                redirect('book', ['id' => $id]);
            }
        }

        $this->render('books/form', [
            'title'       => 'Добавить книгу',
            'heading'     => 'Добавление книги',
            'formAction'  => url('book_create'),
            'submitLabel' => 'Добавить',
            'book'        => $book,
            'errors'      => $errors,
            'genres'      => $genreModel->all(),
        ]);
    }
}
