<?php

namespace App;

use App\Helpers\TextToSpeech;

class Main
{
    protected $tts;

    public function __construct() {
        $this->tts = new TextToSpeech();
    }


    public function testString() {
        return "This is a test string";
    }

}
