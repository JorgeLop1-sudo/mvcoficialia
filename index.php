<?php
require_once __DIR__ . '/controllers/LoginController.php';
require_once __DIR__ . '/controllers/HomeDashController.php';
require_once __DIR__ . '/controllers/AreasAdminController.php';
require_once __DIR__ . '/controllers/UsersAdminController.php';
require_once __DIR__ . '/controllers/ExpedientesController.php';
require_once __DIR__ . '/controllers/ConfigController.php'; 
require_once __DIR__ . '/controllers/ResponderOficioController.php'; 


$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':
        (new LoginController())->login();
        break;
    case 'logout':
        (new LoginController())->logout();
        break;
    case 'homedash':
        (new HomeDashController())->dash();
        break;
    case 'areasadmin':
        (new AreasAdminController())->index();
        break;
    case 'usersadmin': // Añadir este caso
        (new UsersAdminController())->index();
        break;
    case 'expedientes': // Añadir este caso
        (new ExpedientesController())->index();
        break;    
    case 'config': // Añadir este caso
        (new ConfigController())->index();
        break;      
    case 'responderoficio':
        (new ResponderOficioController())->index();
        break;
    default:
        (new LoginController())->login();
        break;
}

