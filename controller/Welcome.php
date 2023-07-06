<?php

class Welcome {

  function index(){
    global $data;
    $data['name'] = 'John Doe';
  }

  function test() {
    echo '789';
  }

}