<?php
namespace App\Helpers;

defined('DOCROOT') or die(header('HTTP/1.0 403 Forbidden'));

class TextToSpeech
{
    private $credentials = [];
    private $settings = [];

    public function __construct() {
        $this->credentials = include "./credentials.php";
        $this->settings = static::getSettings();

    }


    private static function getSettings($key = null) {
        $settings = [
            'voices'    => [
                1   => array(
                    "gender"	=> "f",
                    "name"	    => "Joanna",
                    "language"  => "en-US",
                ),
                2   => array(
                    "gender"	=> "m",
                    "name"	    => "Matthew",
                    "language"  => "en-US",
                ),
                3	=> array(
                    "gender"	=> "f",
                    "name"      => "Amy",
                    "language"	=> "en-GB",
                ),
                4   => array(
                    "gender"	=> "m",
                    "name"	    => "Brian",
                    "language"	=> "en-GB",
                ),
            ],
            'ssml'  => [
                "("         => '<s>(',
                ")"         => ')</s>',
                ")</s>."    => ')</s>',
                ")</s>;"    => ')</s>',
                ")</s>:"    => ')</s>',
                "EPPP"      => '<say-as interpret-as="character">EPPP</say-as>',
                "DSM-IV"    => '<say-as interpret-as="character">DSM</say-as> 4',
                "DSM-5"     => '<say-as interpret-as="character">DSM</say-as> 5',
                "APA"       => '<say-as interpret-as="character">APA</say-as>',
                "PTSD"      => '<say-as interpret-as="character">PTSD</say-as>',
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
