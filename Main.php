<?php
namespace App;

defined('DOCROOT') or die(header('HTTP/1.0 403 Forbidden'));

use App\Helpers\TextToSpeech;

class Main
{
    protected $tts;
    public $showPage;
    public $data;

    public function __construct($requestType, $request) {
        $this->tts = new TextToSpeech();
        $this->showPage = true;

        if(strtoupper($requestType) !== 'GET') {
            $this->handleRequest($requestType, $request);
        }

        return null;
    }

    public function handleRequest($type, $request) {
        $this->data = [];
        $this->showPage = false;

        // debug
        var_dump( json_encode($type) );
        var_dump( json_encode($request) );


    }


    public function sendPolyRequest($request) {
        $response = [
            'success'       => false,
            'audio_path'    => null,
            'messages'      => [],
        ];

        $text   = $request['text_content']; // todo
        $voice  = $request['voice'];        // todo

        $polyResponse = $this->tts->sendRequest($text, $voice);

        if($polyResponse) {
            $response['success'] = true;
            // todo
            $response['audio_path'] = $polyResponse;
        }

        return json_encode($response);
        //exit( json_encode($response) ); // ?
    }

    public function getVoices() {
        return TextToSpeech::getVoices();
    }

    public function getSSMLReplacements() {
        return TextToSpeech::getSSML();
    }

}
