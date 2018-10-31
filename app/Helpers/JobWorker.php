<?php
namespace App\Helpers;

class JobWorker
{
    protected $jobID;
    protected $running;

    public function __construct() {
        $this->working  = false;
    }

    /**
     * Initiate the JobWorker request.
     *
     * @param string $sessionKey
     * @param string $text
     * @param int|array $voiceKeys
     * @param string $textType
     * @param string $outputFormat
     * @return array
     */
    public function start($sessionKey, $text, $voiceKeys, $textType = 'ssml', $outputFormat = 'mp3') {
        // todo: multiple requested voices
        $this->working = true;
        $output = [
            'success'   => false,
            'messages'  => [],
        ];

        // Don't start another job if one is running in the same session.
        if($this->working) {
            $output['job_id']   = $this->jobID;
            $output['messages'][] = "A job is already running in this session. Please try again later";

            JobStatusWriter::jobStarted($this->jobID, $output);

            return $output;
        }

        // Otherwise, start a new job.
        $this->jobID = $this->generateJobID($sessionKey);

        JobStatusWriter::jobStarted($this->jobID, $output);

        // todo: for now, just use the first voice
        if(gettype($voiceKeys) === 'array') {
            $voice = $voiceKeys[0];
        } else {
            $voice = $voiceKeys;
        }

        $ttsRequestSettings = [
            'text_type'     => $textType,
            'output_format' => $outputFormat,
            'text'          => null,
            'voice_key'     => $voice, // todo: see above
        ];

        // Cache the text files
        $textCacheData = TextCacher::cacheTextFiles($this->jobID, $text);

        if(!$textCacheData['success']) {
            $output['messages'] = array_merge($output['messages'], $textCacheData['messages']);
            return $this->finalizeJob($output);
        }

        // Generate audio files and cache each part.
        $audioCacheData = AudioCacher::cacheAudioFiles($this->jobID, $textCacheData['files'], $ttsRequestSettings);

        if(!$audioCacheData['success']) {
            $output['messages'] = array_merge($output['messages'], $audioCacheData['messages']);
            return $this->finalizeJob($output);
        }

        // Stitch the cached audio files
        // todo: set up a way to stitch data later so the user can check over the audio files individually
            // and if any single file is messed up they can change the text and resubmit just that part.
            // Then finalizing that result would run the audio stitcher.
        $audioStitcherData = AudioStitcher::initiate($this->jobID);

        if(!$audioStitcherData ['success']) {
            $output['messages'] = array_merge($output['messages'], $audioCacheData['files'], $audioStitcherData['messages']);
            return $this->finalizeJob($output);
        }

        // todo: clear text and audio cache on success?

        return $this->finalizeJob($output);
    }



    private function generateJobID($sessionKey) {
        return uniqid($sessionKey);
    }

    private function finalizeJob($output) {
        $this->working = false;
        return JobStatusWriter::jobCompleted($this->jobID, $output);
    }


    /**
     * Get the status of a job by jobID.
     *
     * @param string $jobID
     * @return array
     */
    public static function getStatus($jobID) {
        $output = [
            'success'   => false,
            'status'    => null,
            'messages'  => [],
        ];

        $statusData = JobStatusWriter::readStatusFile($jobID);

        if(!$statusData) {
            $output['messages'] = "The status of this job could not be determined. File not found.";
        } else {
            $output['success'] = true;
            $output['status']  = $statusData;
        }

        return $output;
    }


    public function isJobInProgress() {
        return $this->working;
    }


    public function writeSingleOutputFile($audioData) {
        // todo: this is for when the request doesn't need to use the AudioSticher
        // actually, the audio stitcher should probabably handle this.
    }



}

//$type = env('TTS_TEXT_TYPE', 'text');
//$outputFormat = env('TTS_OUTPUT_FORMAT', 'mp3');