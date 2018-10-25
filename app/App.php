<?php

namespace App;

use Dotenv\Dotenv;

class App
{
    protected $sessionKey;
    protected $dotenv;

    protected $request;
    protected $controller;
    public $response;

    public function __construct() {
        $this->request = new Request();
        $this->controller = new Controller();
        $this->response = new Response();

        $this->getOrCreateSessionKey();
    }

    public function getEnvironmentVariable($key, $default = null) {
        $value = getenv($key);

        return ($value ? $value : $default);
    }

    public function handleRequest() {
        $action = $this->request->getControllerAction();

        if(method_exists($this->controller, $action)) {
            return $this->controller->$action( $this->request );
        } else {
            return $this->controller->get_404("Page doesn't exist");
        }

    }

   ////public function handleRequestWithData($type, $requestData) {

   //    switch($type) {
   //        case 'POST':
   //            $method = strtolower( $requestData->method );

   //            switch($method) {
   //                case 'tts':
   //                    $this->handleTTSRequest($requestData);
   //                    break;
   //                default:
   //                    $this->data['messages'][] = "'$method' '$type' requests not implemented.";
   //            }
   //            break;
   //        default:
   //            $this->data['messages'][] = "'".$type."' requests with data not implemented (yet...).";
   //    }

   //    exit( json_encode($this->data) );
   //}


    private function getOrCreateSessionKey($length = 12) {
        if($this->request->attributes('session_key')) {
            return $this->request->attributes('session_key');
        }

        // If a key doesn't exist already, create a new one.
        $key = '';
        $pool = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));

        for($i = 0; $i < $length; $i++) {
            $key .= $pool[mt_rand(0, count($pool) - 1)];
        }

        return $this->sessionKey = $key;
    }

    public function getSessionKey() {
        return $this->sessionKey;
    }

    private function loadDotenv() {
        $this->dotenv = new Dotenv(DOC_ROOT);
        $this->dotenv->load();
    }

    public function getVoices() {
        return Helpers\TextToSpeech::getVoices();
    }

    public function getSSMLReplacements() {
        return Helpers\TextToSpeech::getSSML();
    }

    public function getMaxCharacters() {
        return Helpers\TextToSpeech::getMaxCharacters();
    }

}
