<?php
namespace App\Services;

trait OptionalServiceTrait {

    private $service;

    /**
     * @required
     * @param MyServices2 $second_service
     */
    public function setSecondService(MyServices2 $second_service){
        $this->service = $second_service;
    }

}