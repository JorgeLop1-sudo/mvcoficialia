<?php
require_once __DIR__ . '/controllers/LoginController.php';
require_once __DIR__ . '/controllers/HomeAdminController.php';
require_once __DIR__ . '/controllers/AreasAdminController.php';
require_once __DIR__ . '/controllers/UsersAdminController.php';

$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':
        (new LoginController())->login();
        break;
    case 'logout':
        (new LoginController())->logout();
        break;
    case 'homeadmin':
        (new HomeAdminController())->admin();
        break;
    case 'areasadmin':
        (new AreasAdminController())->index();
        break;
    case 'usersadmin': // Añadir este caso
        (new UsersAdminController())->index();
        break;    
    default:
        (new LoginController())->login();
        break;
}

