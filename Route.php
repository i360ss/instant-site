<?php
if(!defined('APP_INIT')){
  exit('Bad request');
}

// Page Router
$route = [
  'GET' => [
    '/' => [
      'view' => 'welcome',
      'title' => 'Welcome'
    ],
    '/contact' => [
      'view' => 'contact',
      'title' => 'Contact Us',
      'intro_text' => 'Get in touch with us!'
    ]
  ]
];
