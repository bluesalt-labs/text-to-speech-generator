<?php
namespace App;

defined('DOCROOT') or die(header('HTTP/1.0 403 Forbidden'));

use App\Helpers\TextToSpeech;

class Main
{
    protected $tts;
    public $data;

    public function __construct() {
        $this->tts      = new TextToSpeech();
        $this->data     = [];
        $requestType    = strtoupper( $_SERVER['REQUEST_METHOD'] );

        $this->handleRequest($requestType);
    }

    public function handleRequest($type) {
        $requestData = $this->getRequestData();

        if(!$requestData && $type === 'GET' ) {
            $this->exitAndContinue();
        } else {
            header('Content-Type: application/json');
            $this->handleRequestWithData($type, $requestData);
        }
    }

    public function getRequestData() {
        $data = [];

        try {
            $data = json_decode( file_get_contents('php://input') );
        } catch (\Exception $e) {  }

        return $data;
    }

    public function exitAndContinue() {
        return null;
    }

    public function handleRequestWithData($type, $requestData) {

        switch($type) {
            case 'POST':
                $method = strtolower( $requestData->method );

                switch($method) {
                    case 'tts':
                        $this->handleTTSRequest($requestData);
                        break;
                    default:
                        $this->data['messages'][] = "'$method' '$type' requests not implemented.";
                }
                break;
            default:
                $this->data['messages'][] = "'".$type."' requests with data not implemented (yet...).";
        }

        exit( json_encode($this->data) );
    }

    public function handleTTSRequest($request) {
        $response = [
            'success'       => false,
            'audio_path'    => null,
            'audio_name'    => null,
            'messages'      => [],
        ];

        $polyResponse = null;

        try {
            $text   = $request->text;
            $voice  = $request->voice;

            $polyResponse = $this->tts->sendRequest($text, $voice);
        } catch (\Exception $e) {
            $response['messages'] = $e->getMessage();
        }

        if($polyResponse) {
            $response['success'] = $polyResponse['success'];
            $response['audio_path'] = $polyResponse['path'];
            $response['audio_name'] = $polyResponse['name'];
        }

        $this->data = array_merge($this->data, $response);
    }

    public function getVoices() {
        return TextToSpeech::getVoices();
    }

    public function getSSMLReplacements() {
        return TextToSpeech::getSSML();
    }

}
