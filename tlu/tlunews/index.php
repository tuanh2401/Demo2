<?php
session_start();
require_once 'config/database.php';

// Include các Model
require_once 'models/News.php';
require_once 'models/Category.php';
require_once 'models/User.php';

$url = isset($_GET['url']) ? $_GET['url'] : 'home';
$url = rtrim($url, '/');
$url = explode('/', $url);

// Xác định controller và action
$controller = isset($url[0]) ? ucfirst($url[0]) . 'Controller' : 'HomeController';
$action = isset($url[1]) ? $url[1] : 'index';
$param = isset($url[2]) ? $url[2] : null;

// Kiểm tra file controller tồn tại
$controllerFile = "controllers/$controller.php";
if (!file_exists($controllerFile)) {
    header("HTTP/1.0 404 Not Found");
    require 'views/404.php';
    exit;
}

require_once $controllerFile;
$controllerInstance = new $controller();

// Kiểm tra method tồn tại
if (!method_exists($controllerInstance, $action)) {
    $action = 'index';
}

// Gọi method
$controllerInstance->$action($param);
