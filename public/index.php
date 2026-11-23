<?php
/**
 * Единая точка входа приложения (front controller).
 *
 * Все запросы приходят сюда, а дальше по параметру ?page=...
 * вызывается нужный метод нужного контроллера.
 */

// Cookie сессии недоступна из JavaScript и не отправляется с чужих сайтов
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

define('ROOT', dirname(__DIR__));

require ROOT . '/config/config.php';
require ROOT . '/app/core/helpers.php';

// Автозагрузка классов из папок core, controllers и models
spl_autoload_register(function (string $className) {
    foreach (['core', 'controllers', 'models'] as $folder) {
        $file = ROOT . "/app/$folder/$className.php";
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

$routes = require ROOT . '/config/routes.php';
$page = $_GET['page'] ?? 'home';

if (!isset($routes[$page])) {
    (new HomeController())->notFound();
    exit;
}

[$controllerName, $methodName] = $routes[$page];
$controller = new $controllerName();
$controller->$methodName();
