<?php

/**
 * Главная страница — общедоступный раздел сайта.
 */
class HomeController extends Controller
{
    /**
     * Показывает главную: новые книги, рекомендации и жанры (всё берётся из БД).
     *
     * @return void
     */
    public function index(): void
    {
        $bookModel = new Book();

        $this->render('home', [
            'title'       => 'Главная',
            'latestBooks' => $bookModel->latest(6),
            'recommended' => $bookModel->recommended(4),
            'genres'      => (new Genre())->allWithBookCount(),
            'booksCount'  => $bookModel->count(),
        ]);
    }
}
