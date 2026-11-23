<?php

/**
 * Регистрация, вход, выход и восстановление пароля.
 */
class AuthController extends Controller
{
    /**
     * Регистрация нового пользователя.
     *
     * @return void
     */
    public function register(): void
    {
        if (Auth::check()) {
            redirect('home');
        }

        $old = ['username' => '', 'email' => ''];
        $errors = [];

        if (is_post()) {
            verify_csrf();

            $old['username'] = trim($_POST['username'] ?? '');
            $old['email'] = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            $errors = $this->validateNewUser($old['username'], $old['email'], $password, $passwordConfirm);

            if (empty($errors)) {
                $userModel = new User();
                $id = $userModel->create($old['username'], $old['email'], $password);
                Auth::login($userModel->findById($id));
                set_flash('success', 'Регистрация прошла успешно. Добро пожаловать!');
                redirect('home');
            }
        }

        $this->render('auth/register', [
            'title'  => 'Регистрация',
            'old'    => $old,
            'errors' => $errors,
        ]);
    }

    /**
     * Вход в систему по email и паролю.
     *
     * @return void
     */
    public function login(): void
    {
        if (Auth::check()) {
            redirect('home');
        }

        $email = '';
        $error = '';

        if (is_post()) {
            verify_csrf();

            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $user = (new User())->findByEmail($email);

            // Не уточняем, что именно неверно — так сложнее подобрать существующий email
            if ($user !== null && password_verify($password, $user['password_hash'])) {
                Auth::login($user);
                set_flash('success', 'Вы вошли как ' . $user['username'] . '.');
                redirect($user['role'] === 'admin' ? 'admin' : 'home');
            }

            $error = 'Неверный email или пароль.';
        }

        $this->render('auth/login', [
            'title' => 'Вход',
            'email' => $email,
            'error' => $error,
        ]);
    }

    /**
     * Выход из системы (только POST-запросом с CSRF-токеном).
     *
     * @return void
     */
    public function logout(): void
    {
        if (is_post()) {
            verify_csrf();
            Auth::logout();
            set_flash('info', 'Вы вышли из системы.');
        }
        redirect('home');
    }

    /**
     * «Забыли пароль?» — отправка ссылки для сброса на email.
     *
     * @return void
     */
    public function forgot(): void
    {
        $email = '';
        $error = '';
        $sent = false;

        if (is_post()) {
            verify_csrf();

            $email = trim($_POST['email'] ?? '');
            $v = (new Validator())->email('email', $email);

            if ($v->fails()) {
                $error = $v->errors()['email'];
            } else {
                $user = (new User())->findByEmail($email);
                if ($user !== null) {
                    $token = (new PasswordReset())->create($email);
                    $link = APP_URL . '/' . url('reset', ['token' => $token]);
                    send_mail(
                        $email,
                        'Восстановление пароля — Книжная полка',
                        "Здравствуйте, {$user['username']}!\n\n"
                        . "Чтобы задать новый пароль, перейдите по ссылке (действует 1 час):\n$link\n\n"
                        . 'Если вы не запрашивали сброс пароля, просто проигнорируйте это письмо.'
                    );
                }
                // Одинаковый ответ, даже если email не найден, — чтобы нельзя было проверять чужие адреса
                $sent = true;
            }
        }

        $this->render('auth/forgot', [
            'title' => 'Восстановление пароля',
            'email' => $email,
            'error' => $error,
            'sent'  => $sent,
        ]);
    }

    /**
     * Установка нового пароля по ссылке из письма.
     *
     * @return void
     */
    public function reset(): void
    {
        $token = (string)($_GET['token'] ?? $_POST['token'] ?? '');
        $resetModel = new PasswordReset();
        $email = $token !== '' ? $resetModel->findEmailByToken($token) : null;
        $user = $email !== null ? (new User())->findByEmail($email) : null;

        if ($user === null) {
            set_flash('danger', 'Ссылка для сброса пароля недействительна или устарела.');
            redirect('forgot');
        }

        $errors = [];

        if (is_post()) {
            verify_csrf();

            $password = $_POST['password'] ?? '';
            $v = new Validator();
            $v->minLength('password', $password, 6, 'Пароль')
              ->same('password_confirm', $_POST['password_confirm'] ?? '', $password, 'Пароли не совпадают.');

            if (!$v->fails()) {
                (new User())->updatePassword((int)$user['id'], $password);
                $resetModel->deleteByEmail($email);

                set_flash('success', 'Пароль изменён. Теперь можно войти с новым паролем.');
                redirect('login');
            }
            $errors = $v->errors();
        }

        $this->render('auth/reset', [
            'title'  => 'Новый пароль',
            'token'  => $token,
            'errors' => $errors,
        ]);
    }

    /**
     * Проверяет данные нового пользователя (используется и в регистрации, и в админке).
     *
     * @param string $username        Логин.
     * @param string $email           Email.
     * @param string $password        Пароль.
     * @param string $passwordConfirm Подтверждение пароля.
     * @return array Ошибки в виде [поле => текст], пустой массив если всё хорошо.
     */
    public static function validateNewUser(string $username, string $email, string $password, string $passwordConfirm): array
    {
        $v = new Validator();
        $v->required('username', $username, 'Логин')
          ->matches('username', $username, '/^[a-zA-Z0-9_]{3,30}$/',
              'Логин: от 3 до 30 символов, только латинские буквы, цифры и _.');
        $v->required('email', $email, 'Email')->email('email', $email);
        $v->minLength('password', $password, 6, 'Пароль');
        $v->same('password_confirm', $passwordConfirm, $password, 'Пароли не совпадают.');

        $userModel = new User();
        if ($username !== '' && $userModel->findByUsername($username) !== null) {
            $v->addError('username', 'Такой логин уже занят.');
        }
        if ($email !== '' && $userModel->findByEmail($email) !== null) {
            $v->addError('email', 'Пользователь с таким email уже зарегистрирован.');
        }

        return $v->errors();
    }
}
