<?php
namespace App\Services;


class MyServices4{
    public function __construct(){
        dump('I am services four');
    }

    public function someMethod(){
        return 'I am method from services four';
    }
}