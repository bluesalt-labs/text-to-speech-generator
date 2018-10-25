<?php

namespace App;

class Controller
{
    protected $app;
    protected $response;

    public function __construct() {
        $this->response = new Response();
    }

    public function get_index(Request $request) {
        $this->response->loadView('home');
        return $this->response;
    }

    public function post_SubmitAudioRequest(Request $request) {
        $tts = new Helpers\TextToSpeech();

        $response = [
            'success'       => false,
            'audio_path'    => null,
            'audio_name'    => null,
            'messages'      => [],
        ];

        $polyResponse = null;

        try {
            $text       = $request->attributes('text');
            $voice      = $request->attributes('voice');
            $sessionKey = app()->getSessionKey();

            $polyResponse = $tts->sendRequest($text, $voice, $sessionKey);
        } catch (\Error $e) {
            $response['messages'][] = $e->getMessage();
        }

        if($polyResponse) {
            $response['success'] = $polyResponse['success'];
            $response['audio_path'] = $polyResponse['path'];
            $response['audio_name'] = $polyResponse['name'];
        }

        $this->response->data($response);

        return $this->response->toJson();
    }

    public function get_404($message = null) {
        return $this->response->return404($message);
    }

    public function get_500($message = null) {
        return $this->response->return500($message);
    }
}