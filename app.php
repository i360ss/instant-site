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
    if(is_array($route[$path]['action']) && is_object($route[$path]['action'][0])){
      $callback = [new $route[$path]['action'][0], isset($route[$path]['action'][1]) ? $route[$path]['action'][1] : 'index'];
      call_user_func($callback);
    } elseif(is_callable($route[$path]['action'])){
      call_user_func($route[$path]['action']);
    }
  }
} else {
  $view_file = '404';
}


// Current Page
$data['view'] = '../view/pages/'.$view_file.'.phtml';
if(!file_exists($data['view'])){
  $data['view'] = '../view/pages/404.phtml';
}


/**
 * Load Component
 * @param string $load_this_component Component name to load
 * @param array $comp_data Component specific data to bind with the component
 */
function comp($load_this_component, $comp_data=[]) {
  global $data;
  $component_data = array_merge($comp_data, $data);
  extract($component_data);
  ob_start();
  include '../view/components/'.$load_this_component.'.phtml';
  $content = ob_get_clean();

  return $content;
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
  extract($partial_data);
  include '../view/partials/'.$load_this_partial.'.phtml';
}


// General functions
require 'functions.php';
extract($data);