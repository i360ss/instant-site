<?php

class Welcome {

  function index(){
    global $data;
    $data['name'] = 'John Doe';
  }

  function test() {
    global $data;
    $data['name'] = 'Max Smith';
  }

}