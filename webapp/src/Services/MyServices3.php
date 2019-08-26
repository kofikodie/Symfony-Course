<?php
 namespace App\Services;


 class MyServices3{
     public $secSecond;

     public function __construct($service){
         dump($service);
         $this->secSecond = $service;
     }
 }