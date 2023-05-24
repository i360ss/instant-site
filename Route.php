<?php
if(!defined('APP_INIT')){
  exit('Bad request');
}

// Page Router
$route = [
  '/' => [
    'view' => 'welcome',
    'title' => 'Welcome',
    'age' => 26,
    'action' => [Welcome::class],
  ],
  '/doc/v1/' => [
    'view' => 'get-started',
    'title' => 'Get Started',
    'name' => 'John'
  ],
  '/doc/v1/authentication' => [
    'view' => 'auth',
    'title' => 'Authentication'
  ],
  '/doc/v1/route' => [
    'view' => 'route',
    'title' => 'Route'
  ]
];