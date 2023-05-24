<?php

class Welcome {

  function index(){
    global $data;
    $data['name'] = 'Shakir';
  }

  function test() {
    echo '789';
  }

}