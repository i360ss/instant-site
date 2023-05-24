<?php
if(!defined('APP_INIT')){
  exit('Bad request');
}


// Autoload
spl_autoload_register(function ($class_name) {
  include __DIR__.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.$class_name . '.php';
});


// Load Routes
require 'Route.php';


// Get and sanitize Request URL
$raw_request_uri = $_SERVER['REQUEST_URI'];
$clean_request_uri = strtok($raw_request_uri, '?');
$path = filter_var($clean_request_uri, FILTER_SANITIZE_URL);
$segment = explode('/', $path);
$version = isset($segment[2]) ? $segment[2] : '';


// BASE URL
$base_url = "http";
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
  $base_url = "https";
}
$base_url .= "://";
$base_url .= filter_var($_SERVER['HTTP_HOST'], FILTER_SANITIZE_URL);
define('BASE_URL', $base_url.'/');


// Define Routes, Data and views
$_GLOBALS['data'] = [];
if(isset($route[$path])){
  $data = $route[$path];
  $view_file = $version.'/'.$data['view'];
  $data['version'] = $version;

  // Load Controller and method
  if(isset($route[$path]['action'])){
    $callback = [new $route[$path]['action'][0], isset($route[$path]['action'][1]) ? $route[$path]['action'][1] : 'index'];
    call_user_func($callback);
  }
} else {
  $view_file = '404';
}


// Current Page
$data['view'] = '../pages/'.$view_file.'.phtml';
if(!file_exists($data['view'])){
  $data['view'] = '../pages/404.phtml';
}


// Load Component
function comp($comp) {
  global $data;
  extract($data);
  require '../components/'.$comp.'.phtml';
}


// General functions
require 'functions.php';
extract($data);