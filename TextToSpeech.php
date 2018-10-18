<?php
namespace App\Helpers;

defined('DOCROOT') or die(header('HTTP/1.0 403 Forbidden'));

require DOCROOT.'./vendor/autoload.php';

use Aws\Polly\PollyClient;

class TextToSpeech
{
    private $credentials = [];
    private $settings = [];
    private $polly;

    public function __construct() {
        $this->credentials = include "./credentials.php";
        $this->settings = static::getSettings();

        $this->initPolly();
    }

    private function initPolly() {
        try {
            $this->polly = new PollyClient(  $this->getPollyConfig() );
        } catch(\Exception $e) {
            // todo: figure out how to do a better job with this
            var_dump($e);
            exit;
        }
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

    private static function getSettings($key = null) {
        $settings = [
            'audio_format'  => [
                'mp3'   => 'audio/mpeg',
            ],
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
                "EPPP"          => '<say-as interpret-as="character">EPPP</say-as>',
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
                case 'voices':  return $settings['voices']; break;
                case 'ssml':    return $settings['ssml'];   break;
                default: return [];
            }
        }

        return $settings;
    }

    public static function getVoices() { return static::getSettings('voices'); }
    public static function getSSML() { return static::getSettings('ssml'); }
}
