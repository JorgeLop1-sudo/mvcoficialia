<?php
require_once __DIR__ . '/controllers/LoginController.php';

$action = $_GET['action'] ?? 'login';
$controller = new LoginController();

switch ($action) {
    case 'login':
        $controller->login();
        break;
    case 'homeadmin':
        $controller->dashboard();
        break;
    case 'logout':
        $controller->logout();
        break;
    default:
        $controller->login();
        break;
}
