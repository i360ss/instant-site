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
    'action' => [Welcome::class, 'test'],
  ]
];