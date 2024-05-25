<?php
if(!defined('APP_INIT')){
  exit('Bad request');
}

// Enable errors
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);


// Autoload
spl_autoload_register(function ($class_name) {
  $directories = [
    __DIR__ . DIRECTORY_SEPARATOR . 'controller',
    __DIR__ . DIRECTORY_SEPARATOR . 'middleware'
  ];

  foreach ($directories as $directory) {
    $file_path = $directory . DIRECTORY_SEPARATOR . $class_name . '.php';
    if (file_exists($file_path)) {
      include $file_path;
      return;
    }
  }
});


// Start session
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Load Routes
require __DIR__.'/Route.php';

// Get and sanitize Request URL
$raw_request_uri = $_SERVER['REQUEST_URI'];
$clean_request_uri = strtok($raw_request_uri, '?');
$path = filter_var($clean_request_uri, FILTER_SANITIZE_URL);
$segment = explode('/', $path);

// BASE URL
$base_url = "http";
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
  $base_url = "https";
}
$base_url .= "://";
$base_url .= filter_var($_SERVER['HTTP_HOST'], FILTER_SANITIZE_URL);
define('BASE_URL', htmlspecialchars($base_url).'/');

// Request Method
$rq_method = $_SERVER['REQUEST_METHOD'];
$rt = $route[$rq_method];

// Define Routes, Data and views
$_GLOBALS['data'] = [];

if(isset($rt) && isset($rt[$path])){
  // Set up Initial Data
  $data = $rt[$path];
  $data['segment'] = $segment;
  $data['query'] = $_GET;
  $data['base_url'] = BASE_URL;
  $data['base_path'] = parse_url(BASE_URL.$path);
  $data['current_url'] = rtrim(BASE_URL, '/').$path;
  $data['path'] = $path;

  $view_file = $data['view'] ?? false;

  // Load Middleware
  if(isset($rt[$path]['middleware'])){
    if(is_array($rt[$path]['middleware']) && class_exists($rt[$path]['middleware'][0])){
      $callback = [new $rt[$path]['middleware'][0], isset($rt[$path]['middleware'][1]) ? $rt[$path]['middleware'][1] : 'handle'];
      call_user_func($callback);
    } elseif(is_callable($rt[$path]['middleware'])){
      call_user_func($rt[$path]['middleware']);
    }
  }

  // Load Controller and method
  if(isset($rt[$path]['action'])){
    if(is_array($rt[$path]['action']) && class_exists($rt[$path]['action'][0])){
      $callback = [new $rt[$path]['action'][0], isset($rt[$path]['action'][1]) ? $rt[$path]['action'][1] : 'index'];
      call_user_func($callback);
    } elseif(is_callable($rt[$path]['action'])){
      call_user_func($rt[$path]['action']);
    }
  }
} else {
  $data = [];
  $data['segment'] = $segment;
  $data['query'] = $_GET;
  $data['base_url'] = BASE_URL;
  $data['base_path'] = parse_url(BASE_URL.$path);
  $data['current_url'] = rtrim(BASE_URL, '/').$path;
  $data['path'] = $path;
  $title = '404 Page not found';
  $view_file = '404';
}

// Current Page
$data['view'] = $view_file ? '../view/pages/'.$view_file.'.phtml' : false;
if(!file_exists($data['view'])){
  $data['view'] = '../view/pages/404.phtml';
}

/**
 * Load Component
 * @param string $load_this_component Component name to load
 * @param array $comp_data Component data to bind with the component
 */
function comp($load_this_component, $comp_data=[]) {
  global $data;
  $component_data = array_merge($comp_data, $data);
  extract($component_data);
  ob_start();
  include '../view/components/'.$load_this_component.'.phtml';

  return ob_get_clean();
}

// Echo Component
function _comp($load_this_component, $comp_data=[]) {
  echo comp($load_this_component, $comp_data);
}

/**
 * Include a partial
 * @param string $load_this_partial Partial name
 * @param array $partial_data Partial specific data
 */
function partial($load_this_partial, $partial_data=[]){
  global $data;
  extract($data);
  extract($partial_data);
  include '../view/partials/'.$load_this_partial.'.phtml';
}

/**
 * Redirect
 * @param string $path Target path to redirect
 * @param $code HTTP status code
 */
function redirect($path, $code = 302) {
  http_response_code($code);
  header("Location: $path");
  exit();
}

/**
 * Store and get temporary Session data
 */
function set_flash($key, $data) {
  $_SESSION['__flash'][$key] = $data;
}
function get_flash($key=false, $get_array=false) {
  if (isset($_SESSION['__flash'])) {
    if ($key && isset($_SESSION['__flash'][$key])) {
      return $_SESSION['__flash'][$key];
    } else {
      return $get_array ? $_SESSION['__flash'] : '';
    }
  }
  return false;
}

/**
 * Escape string
 */
function esc($str) {
  return htmlspecialchars($str);
}

/**
 * Auth functions
 */
function auth_info($key=false) {
  if (isset($_SESSION['user_info'])) {
    return $key !== false ? $_SESSION['user_info'][$key] : $_SESSION['user_info'];
  } else {
    return false;
  }
}
function auth_id() {
  return auth_info('id');
}
function auth_logged() {
  return (isset($_SESSION['user_logged']) && $_SESSION['user_logged'] === true) ? true : false;
}
function auth_type() {
  if (isset($_SESSION['user_info'])) {
    return $_SESSION['user_info']['user_type'] ?? false;
  } else {
    return false;
  }
}

// General functions
require 'functions.php';
extract($data);
