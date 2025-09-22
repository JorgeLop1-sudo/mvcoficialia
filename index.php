<?php
require_once __DIR__ . '/controllers/LoginController.php';
require_once __DIR__ . '/controllers/HomeAdminController.php';
require_once __DIR__ . '/controllers/AreasAdminController.php';
require_once __DIR__ . '/controllers/UsersAdminController.php';
require_once __DIR__ . '/controllers/ExpedientesAdminController.php';
require_once __DIR__ . '/controllers/ConfigAdminController.php'; 
require_once __DIR__ . '/controllers/ResponderOficioController.php'; 
require_once __DIR__ . '/controllers/HomeUserController.php';
require_once __DIR__ . '/controllers/ExpedientesUserController.php';
require_once __DIR__ . '/controllers/ConfigUserController.php'; 


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
    case 'expedientesadmin': // Añadir este caso
        (new ExpedientesAdminController())->index();
        break;    
    case 'configadmin': // Añadir este caso
        (new ConfigAdminController())->index();
        break;
    case 'expedientesuser': // Añadir este caso
        (new ExpedientesUserController())->index();
        break;    
    case 'configuser': // Añadir este caso
        (new ConfigUserController())->index();
        break;       
    case 'responderoficio':
        (new ResponderOficioController())->index();
        break;
    case 'homeuser':
        (new HomeUserController())->user();
        break;
    default:
        (new LoginController())->login();
        break;
}

