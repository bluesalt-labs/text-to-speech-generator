<?php
namespace App\Helpers;

class AudioStitcher
{
    protected $jobID;

    public function __construct($jobID) {
        $this->jobID = $jobID;
        app()->jobStatus->audioCacherInitiated();

        return $this;
    }

    public function stitchAudioFiles($jobID) {

    }
}