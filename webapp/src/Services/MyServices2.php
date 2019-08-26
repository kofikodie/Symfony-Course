<?php

namespace App\Services;

class MyServices2 implements ServiceInterface {
    public function __construct(){
        dump('I am a second service');
    }

    public function doSomething(){
        return 'I did something';
    }

    public function clear(){
        dump('Clear....');
    }
}