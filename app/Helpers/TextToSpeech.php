<?php
namespace App\Helpers;

use Aws\Polly\PollyClient;

class TextToSpeech
{
    protected $polly;

    /**
     * TextToSpeech constructor.
     */
    public function __construct() {
        $this->polly = static::getPollyClient();
    }

    /**
     * Send a TextToSpeech request through the AWS Polly API and return the response.
     * Returns an array with function success/messages and  Polly response data.
     *
     * @param $requestData - ['text_type', 'output_format', 'text', 'voice_key']
     * @return array
     */
    public function sendRequest($requestData, $textIsClean = false) {
        $output = [
            'success'       => false,
            'response_data' => null,
            'messages'      => [],
        ];

        $requestDataValidation = static::validateRequestData($requestData);

        if(!$requestDataValidation['success']) {
            // todo: make sure this works
            $output['success'] = false;
            $output['messages'] = array_merge($requestDataValidation['messages'], $output['messages']);

            return $output;
        }

        try {
            $processedText = ($textIsClean ? $requestData['text'] : static::cleanString($requestData['text']) );
            $type   = $requestData['text_type'];
            $format = $requestData['output_format'];

            if($type === 'ssml') {
                $processedText = '<speak>'.static::processSSMLReplacements($requestData['text']).'</speak>';
            }

            $voice = static::getVoiceNameByKey($requestData['voice_key']);
        } catch(\Exception $e) {
            // todo: make sure this works
            $output['success'] = false;
            $output['messages'] = array_merge($requestDataValidation['messages'], $output['messages']);

            return $output;
        }

        // Send the request
        $responseData = $this->polly->synthesizeSpeech([
            'Text'          => $processedText,
            'OutputFormat'  => $format,
            'TextType'      => $type,
            'VoiceId'       => $voice,
        ]);

        // todo: check for error
        $output['success'] = true;
        $output['response_data'] = $responseData;

        return $output;
    }

    /**
     * Runs sendRequest statically.
     *
     * @param $requestData
     * @return array
     */
    public static function staticSendRequest($requestData) {
        $tts = new self;

        return $tts->sendRequest($requestData);
    }

    /**
     * Determines if a text string is greater than the maximum number of allowed characters.
     *
     * @param string $text
     * @return bool
     */
    public static function isTextTooLong($text) {
        return !!(sizeof($text) > static::getMaxCharacters());
    }

    /**
     * Get the maximum number of characters per Polly request.
     *
     * @return int
     */
    public static function getMaxCharacters() {
        return intval( env('MAX_REQUEST_CHARS', 3000) );
    }


    //**************** Polly Client Functions ********************************//

    /**
     * Get the AWS Polly client. Returns an array with function success/messages and the client
     *
     * @return array
     */
    protected static function getPollyClient() {
        $output = [
            'success'   => false,
            'client'    => null,
            'messages'  => [],
        ];

        try {
            $output['client'] = new PollyClient( static::getPollyConfig() );
            $output['success']  = true;
        } catch(\Exception $e) {
            $output['success']  = false;
            $output['messages'] = $e->getMessage();
        }

        return $output;
    }

    /**
     * Get the AWS Polly configuration from the environment variables
     * @return array
     */
    protected static function getPollyConfig() {
        $credentials = static::getCredentials();

        return [
            'version'   => $credentials['version'],
            'region'    => $credentials['region'],
            'credentials' => [
                'key'         => $credentials['key'],
                'secret'      => $credentials['secret'],
            ],
        ];
    }

    protected static function getCredentials() {
        return [
            'version'   => env('POLLY_VERSION', 'latest'),
            'region'    => env('AWS_REGION', 'us-east-1'),
            'key'       => env('AWS_KEY', 'key'),
            'secret'    => env('AWS_SECRET', 'secret'),
        ];
    }

    protected static function validateRequestData($requestData) {
        $output = [
            'success'   => true,
            'messages'  => [],
        ];

        //['text_type', 'output_format', 'text', 'voice_key']
        if(gettype($requestData) === 'array') {
            $output['success'] = false;
            $output['messages'][] = "Request Data is not an array.";
        }

        if(!array_key_exists('text_type', $requestData)) {
            $output['success'] = false;
            $output['messages'][] = "Must specify text type.";
        }

        if(!array_key_exists('output_format', $requestData)) {
            $output['success'] = false;
            $output['messages'][] = "Request Data is not an array.";
        }

        return $output;
    }


    //**************** TextToSpeech Internal Helper Functions ****************//

    public static function cleanString($text) {
        return strip_tags(preg_replace(array("/:|<\/(li|p)>/","/&#?[a-z0-9]+;/i"), array(",", ''), trim($text)));
    }

    private static function textToSSML($text) {
        $cleanString = $text;

        foreach(static::getSSMLReplacements() as $acronym => $replacement) {
            $cleanString = str_replace($acronym, $replacement, $cleanString);
        }

        return "'<speak><amazon:auto-breaths duration=\"short\">".$cleanString."</amazon:auto-breaths></speak>'";

        return $cleanString;
    }

    private function getVoiceNameByKey($voiceKey) {
        $voices = self::getAvailableVoices();

        if( array_key_exists(intval($voiceKey), $voices) ) {
            return $voices[$voiceKey]['name'];
        } else {
            throw new \InvalidArgumentException("Voice Key $voiceKey does not exist");
        }
    }

    public static function getAudioOutputExtension($type) {
        $types = static::getAudioOutputTypes();

        if( array_key_exists($type, $types)) {
            return $types[$type]['ext'];
        }

        return null;
    }


    //**************** TextToSpeech Setting Array Functions ******************//
    public static function getAudioOutputTypes() {
        return [
            'mp3'   => [
                'ext'           => 'mp3',
                'mime_type'     => 'audio/mpeg',
                'polly_type'    => 'mp3',
            ],
            'ogg'   => [
                'ext'           => 'ogg',
                'mime_type'     => 'audio/ogg',
                'polly_type'    => 'ogg_vorbis',
            ],
            'pcm'   => [
                'ext'           => 'pcm',
                'mime_type'     => 'audio/pcm',
                'polly_type'    => 'pcm',
            ],
            'json'  => [
                'ext'           => 'json',
                'mime_type'     => 'application/json',
                'polly_type'    => 'json',
            ],
        ];
    }


    public static function getSSMLReplacements() {
        return [
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
        ];
    }


    public static function getAvailableVoices() {
        return [
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
        ];
    }

}
