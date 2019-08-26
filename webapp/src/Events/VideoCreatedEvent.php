<?php

namespace App\Events;

use Symfony\Contracts\EventDispatcher\Event;

class VideoCreatedEvent extends Event{
    protected $video;

    public function __construct($video){
        $this->video = $video;
    }
}