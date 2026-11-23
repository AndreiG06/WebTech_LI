-- База данных приложения «Книжная полка»
-- Запуск: импортируйте этот файл в phpMyAdmin или выполните
--   mysql -u root < database/schema.sql

DROP DATABASE IF EXISTS bookshelf;
CREATE DATABASE bookshelf CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bookshelf;

-- Пользователи. Пароль хранится только в виде хэша (password_hash в PHP)
CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(30)  NOT NULL UNIQUE,
    email         VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Жанры книг
CREATE TABLE genres (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Книги (основной ресурс приложения)
CREATE TABLE books (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    title          VARCHAR(200) NOT NULL,
    author         VARCHAR(100) NOT NULL,
    genre_id       INT NOT NULL,
    year           SMALLINT NOT NULL,
    language       ENUM('ru', 'en', 'other') NOT NULL,
    is_recommended TINYINT(1) NOT NULL DEFAULT 0,
    description    TEXT NOT NULL,
    user_id        INT NULL,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id)  REFERENCES users(id)  ON DELETE SET NULL
);

-- Токены для восстановления пароля (хранится хэш токена, а не сам токен)
CREATE TABLE password_resets (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(100) NOT NULL,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL
);

-- ---------- Тестовые данные ----------

-- admin@bookshelf.local / admin123  (администратор)
-- reader@bookshelf.local / user123  (обычный пользователь)
INSERT INTO users (username, email, password_hash, role) VALUES
('admin',  'admin@bookshelf.local',  '$2y$10$x4ToNOzRMe3Kq/CMEENTf..hcDwFmqBJ9nXAyKQ0KN2ZM1jmXKP6u', 'admin'),
('reader', 'reader@bookshelf.local', '$2y$10$L6HcI1paClHwUQedeUF2Jei.WxblJmbkZUBZFK1PRj4Q1xsFMzQYK', 'user');

INSERT INTO genres (name) VALUES
('Роман'), ('Фантастика'), ('Детектив'), ('Фэнтези'), ('Научпоп'), ('Программирование');

INSERT INTO books (title, author, genre_id, year, language, is_recommended, description, user_id) VALUES
('Мастер и Маргарита', 'Михаил Булгаков', 1, 1967, 'ru', 1, 'Роман о визите дьявола в Москву 1930-х годов и истории Мастера и его возлюбленной.', 1),
('Преступление и наказание', 'Фёдор Достоевский', 1, 1866, 'ru', 1, 'История бедного студента Раскольникова, решившегося на преступление.', 1),
('Пикник на обочине', 'Аркадий и Борис Стругацкие', 2, 1972, 'ru', 1, 'Повесть о Зоне, оставшейся после визита инопланетян, и сталкерах.', 2),
('Dune', 'Frank Herbert', 2, 1965, 'en', 0, 'Epic science fiction novel set on the desert planet Arrakis.', 2),
('Убийство в «Восточном экспрессе»', 'Агата Кристи', 3, 1934, 'ru', 0, 'Эркюль Пуаро расследует убийство в застрявшем поезде.', 1),
('Хоббит', 'Дж. Р. Р. Толкин', 4, 1937, 'ru', 1, 'Путешествие Бильбо Бэггинса туда и обратно.', 2),
('Краткая история времени', 'Стивен Хокинг', 5, 1988, 'ru', 0, 'Популярно о Большом взрыве, чёрных дырах и природе времени.', 1),
('Clean Code', 'Robert C. Martin', 6, 2008, 'en', 1, 'Practical advice on writing readable and maintainable code.', 1);
