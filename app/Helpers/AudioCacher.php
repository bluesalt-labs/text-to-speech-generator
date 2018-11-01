<?php
namespace App\Helpers;

class AudioCacher
{
    protected $jobID;
    protected $fileCounter;

    //public function __construct($jobID) {
    //    $this->jobID = $jobID;
    //    $this->fileCounter = 0;
    //    app()->jobStatus->audioCacherInitiated();
    //    return $this;
    //}

    /**
     * Load text file contents, send Polly request, and cache the resulting file.
     *
     * @param $jobID
     * @param $textFiles
     * @return array
     */
    public static function cacheAudioFiles($jobID, $textFiles, $ttsSettings) {
        $output = [
            'success'   => false,
            'files'     => [],
            'messages'  => [],
        ];

        $initiateData = static::initiateJobCache($jobID);

        if(!$initiateData['success']) {
            $output['success'] = false;
            $output['messages'] = $initiateData['messages'];

            return $output;
        }

        $tts = new TextToSpeech();
        $outputExt = TextToSpeech::getAudioOutputExtension($ttsSettings['output_format']);


        foreach($textFiles as $fileNum => $fileData) {
            $ttsSettings['text'] = TextCacher::getCacheFileContents($jobID, $fileData['file_num']);

            // get audio data
            // todo: break loop on failure and get message
            $audioData = static::requestAudio($jobID, $tts, $ttsSettings);

            // write cacheFile
            // todo: break loop on failure and get message
            $cacheWriteData = static::writeCacheFile($jobID, $initiateData['cache_dir'], $fileNum, $audioData);

            $output['files'][$fileNum] = $cacheWriteData['file_path'];
        }

        $output['success'] = true;

        JobStatusWriter::audioCacheCompleted($jobID, $output);
        return $output;
    }

    public static function getCacheFile($jobID, $fileNum) {

    }

    public static function clearJobCache($jobID) {
        // todo
    }


    private static function initiateJobCache($jobID) {
        $output = [
            'success'   => false,
            'cache_dir' => null,
            'messages'  => [],
        ];

        $output['cache_dir'] = $cacheDirPath = static::getCacheDirPath($jobID);
        $output['success']   = mkdir($cacheDirPath);

        JobStatusWriter::audioCacheInitiated($jobID, $output);
        return $output;
    }


    public static function writeCacheFile($jobID, $basePath, $fileNum, $audioData) {
        $output = [
            'success'   => false,
            'file_path' => null,
            'file_num'  => $fileNum,
            'messages'  => [],
        ];

        $output['file_path'] = $filePath = $basePath.$fileNum.'';// todo get audio file extension.
        $output['success']  = null; // todo: write audio file.



        //app()->jobStatus->textCacheCompleted();// todo: not correct message
        return $output;
        // todo: write file
        //$this->fileCounter++;
    }


    private static function getCacheBasepath($fullpath = true) {
        return (!!$fullpath ? DOC_ROOT : '').
            env('CACHE_PATH_AUDIO', 'cache/audio/');
    }


    private static function requestAudio($jobID, TextToSpeech $tts, $ttsSettings) {
        $output = [
            'success'       => false,
            'response_data' => null,
            'messages'      => [],
        ];

        // send TTS request
        $ttsResponseData = $tts->sendRequest($ttsSettings, true);

        // todo: is this right?

        $output['success'] = $ttsResponseData;
        $output['response_data'] = $ttsResponseData['response_data'];
        // todo: make sure this works
        $output['messages'] = array_merge($ttsResponseData['messages'], $output['messages']);

        JobStatusWriter::pollyRequestSent($jobID, $output);
        return $output;
    }
}
