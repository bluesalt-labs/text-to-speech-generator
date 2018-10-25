<?php
namespace App\Helpers;

use Aws\Polly\PollyClient;

class TextToSpeech
{
    private $credentials = [];
    private $settings = [];
    private $polly;

    public function __construct() {
        $this->credentials = [
            'key'       => env('AWS_KEY', 'key'),
            'secret'    => env('AWS_SECRET', 'secret'),
            'region'    => env('AWS_REGION', 'us-east-1'),
        ];


        $this->settings = static::getSettings();
        $this->initPolly();
    }

    public function sendRequest($text, $voiceKey, $sessionKey) {
        $response       = null;
        $requestData    = null;
        $processedText  = $this->processRawText($text);
        $voice          = $this->getVoiceNameByKey($voiceKey);
        $format         = $this->settings['audio_format'];

        if($processedText && $voice && $format) {
            $requestData = [
                'Text'          => '<speak>'.$processedText.'</speak>',
                'OutputFormat'  => $format,
                'TextType'      => 'ssml',
                'VoiceId'       => $voice,
            ];
        }



        if($requestData) {

            $response = $this->polly->synthesizeSpeech($requestData);

            $fileInfo = $this->getAudioOutputFileInfo($voice);
            $success = file_put_contents(PUBLIC_ROOT.$fileInfo['path'], $response['AudioStream']);

            return [
                'success'   => $success,
                'path'      => $fileInfo['path'],
                'name'      => $fileInfo['name'],
            ];
        }

        // todo: DRY
        return [
            'success'   => false,
            'path'      => null,
            'name'      => null,
        ];
    }

    public static function getScriptProgress($sessionKey) {

    }

    //public static function updateScriptProgress

    private function initPolly() {
        try {
            $this->polly = new PollyClient(  $this->getPollyConfig() );
        } catch(\Exception $e) {
            // todo: figure out a better way to handle this. probably credentials are bad.

            exit(
                json_encode([
                    "messages" => [$e->getMessage()]
                ])
            );
        }
    }

    private function getAudioOutputFileInfo($voice) {
        $date       = new \DateTime();
        $basePath   = $this->settings['output_path'];
        $timestamp  = $date->format('YmdHis');
        $extension  = $this->settings['audio_format'];
        $fullPath   = null;


        $output = [
            'path'  => null,
            'name'  => null
        ];

        if($basePath && $timestamp && $extension) {
            $filename = $timestamp."_$voice.".$extension;
            $output['path'] = $basePath.$filename;
            $output['name'] = $filename;
        }

        return $output;
    }

    private function getPollyConfig() {
        return [
            'version'     => 'latest',
            'region'      => $this->credentials['region'],
            'credentials' => [
                'key'         => $this->credentials['key'],
                'secret'      => $this->credentials['secret'],
            ],
        ];
    }

    private function getVoiceNameByKey($voiceKey) {
        if( array_key_exists(intval($voiceKey), $this->settings['voices']) ) {
            return $this->settings['voices'][$voiceKey]['name'];
        }

        return null;
    }

    private function processRawText($text) {
        $cleanString = $text;

        foreach($this->settings['ssml'] as $acronym => $replacement) {
            $cleanString = str_replace($acronym, $replacement, $cleanString);

        }

        return $cleanString;
    }


    /*
    todo:
        - read $settings['max_request_characters'] characters from input text
        - save to text file
        - more stuff
        -
        - create endpoint that can be pinged with specific session ID to get status of current script.
    */

    private static function getSettings($key = null) {
        $settings = [
            'max_request_characters' => 3000,
            'audio_format'  => 'mp3',
            'output_path'   => 'audio_output/',
            'cache_paths'   => [
                'text'  => 'cache/text/',
                'audio' => 'cache/audio/',
            ],
            'ssml'          => [
                "("             => '<s>(',
                ")"             => ')</s>',
                ")</s>."        => ')</s>',
                ")</s>;"        => ')</s>',
                ")</s>:"        => ')</s>',
                "EPPP"          => 'E Triple P',
                "CPLEE"         => 'See Plea',
                "DSM-IV"        => '<say-as interpret-as="character">DSM</say-as> 4',
                "DSM-5"         => '<say-as interpret-as="character">DSM</say-as> 5',
                "APA"           => '<say-as interpret-as="character">APA</say-as>',
                "PTSD"          => '<say-as interpret-as="character">PTSD</say-as>',
            ],
            'voices'        => [
                1   => [
                    "preferred" => false,
                    "gender"	=> "m",
                    "name"	    => "Russell",
                    "language"  => "en-AU",
                ],
                2   => [
                    "preferred" => false,
                    "gender"	=> "f",
                    "name"	    => "Nicole",
                    "language"  => "en-AU",
                ],
                3   => [
                    "preferred" => true,
                    "gender"	=> "m",
                    "name"	    => "Brian",
                    "language"	=> "en-GB",
                ],
                4   => [
                    "preferred" => true,
                    "gender"	=> "f",
                    "name"      => "Amy",
                    "language"	=> "en-GB",
                ],
                5   => [
                    "preferred" => false,
                    "gender"	=> "f",
                    "name"	    => "Emma",
                    "language"  => "en-GB",
                ],
                6   => [
                    "preferred" => false,
                    "gender"	=> "f",
                    "name"	    => "Aditi",
                    "language"  => "en-IN",
                ],
                7   => [
                    "preferred" => false,
                    "gender"	=> "f",
                    "name"	    => "Raveena",
                    "language"  => "en-IN",
                ],
                8   => [
                    "preferred" => false,
                    "gender"	=> "m",
                    "name"	    => "Joey",
                    "language"  => "en-US",
                ],
                9   => [
                    "preferred" => false,
                    "gender"	=> "m",
                    "name"	    => "Justin",
                    "language"  => "en-US",
                ],
                10  => [
                    "preferred" => true,
                    "gender"	=> "m",
                    "name"	    => "Matthew",
                    "language"  => "en-US",
                ],
                11  => [
                    "preferred" => false,
                    "gender"	=> "f",
                    "name"	    => "Ivy",
                    "language"  => "en-US",
                ],
                12  => [
                    "preferred" => true,
                    "gender"	=> "f",
                    "name"	    => "Joanna",
                    "language"  => "en-US",
                ],
                13  => [
                    "preferred" => false,
                    "gender"	=> "f",
                    "name"	    => "Kendra",
                    "language"  => "en-US",
                ],
                14  => [
                    "preferred" => false,
                    "gender"	=> "f",
                    "name"	    => "Kimberly",
                    "language"  => "en-US",
                ],
                15  => [
                    "preferred" => false,
                    "gender"	=> "f",
                    "name"	    => "Salli",
                    "language"  => "en-US",
                ],
                16  => [
                    "preferred" => false,
                    "gender"	=> "m",
                    "name"	    => "Geraint",
                    "language"  => "en-GB-WLS",
                ],
            ],
        ];

        if($key) {
            switch($key) {
                case 'voices': return $settings['voices']; break;
                case 'ssml': return $settings['ssml'];   break;
                case 'max_request_characters': return $settings['max_request_characters']; break;
                default: return [];
            }
        }

        return $settings;
    }

    public static function getVoices() { return static::getSettings('voices'); }
    public static function getSSML() { return static::getSettings('ssml'); }
    public static function getMaxCharacters() { return static::getSettings('max_request_characters'); }
}
