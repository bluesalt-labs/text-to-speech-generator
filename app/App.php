<?php

namespace App;

class App
{
    protected $sessionKey;
    protected $request;
    protected $controller;
    public $jobWorker;
    public $response;

    /**
     * App constructor.
     */
    public function __construct() {
        $this->request = new Request();
        $this->controller = new Controller();
        $this->response = new Response();

        $this->getOrCreateSessionKey();

        $this->jobWorker = new Helpers\JobWorker();
    }

    /**
     * Handles an incoming HTTP request
     *
     * @return Controller
     */
    public function handleRequest() {
        $action = $this->request->getControllerAction();

        if(method_exists($this->controller, $action)) {
            return $this->controller->$action( $this->request );
        } else {
            return $this->controller->get_404("Page doesn't exist");
        }

    }

    /**
     * Get the status of the JobWorker
     * @param $jobID
     * @return array
     */
    public function getJobStatus($jobID) {
        return $this->jobWorker->getStatus($jobID);
    }

    /**
     * Submit a new JobWorker request
     *
     * @param $text
     * @param $voice
     * @param bool $useSSML
     * @return array
     */
    public function submitJobRequest($text, $voice, $useSSML = true) {
        return $this->jobWorker->start($this->sessionKey, $text, $voice, $useSSML);
    }

    /**
     * Generates a new session key on App init.
     *
     * @param int $length
     * @return string
     */
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

    /**
     * Get the current session key.
     *
     * @return string
     */
    public function getSessionKey() {
        return $this->sessionKey;
    }

    /**
     * Get available voices from the TextToSpeech helper class.
     *
     * @return array
     */
    public function getVoices() {
        return Helpers\TextToSpeech::getAvailableVoices();
    }

    /**
     * Get SSML replacements array from the TextToSpeech helper class.
     *
     * @return array
     */
    public function getSSMLReplacements() {
        return Helpers\TextToSpeech::getSSMLReplacements();
    }

    /**
     * * Get max characters per request from the TextToSpeech helper class.
     *
     * @return int
     */
    public function getMaxCharacters() {
        return Helpers\TextToSpeech::getMaxCharacters();
    }

}
