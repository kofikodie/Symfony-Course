<?php

namespace App\Services;


class MyServices{
    use OptionalServiceTrait;
    public function __construct(){
    }

    public function someAction(){
        dump($this->service->doSomething());
    }
}