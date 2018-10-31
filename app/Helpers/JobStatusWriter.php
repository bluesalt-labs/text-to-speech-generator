<?php
namespace App\Helpers;

class JobStatusWriter
{

    // TODO: OUTPUT ALL RESPONSES TO MASTER LOG FILE.

    const AUTO_WRITE_FILE   = true;
    const OUTPUT_ALL_TO_LOG = false; // todo

    /**
     * Get the contents of the status file as array.
     * Will return a string of the file contents if json is not valid.
     *
     * @param string $jobID
     * @return array|string
     */
    public static function readStatusFile($jobID) {
        $data = null;

        if(file_exists( static::getStatusFilepath($jobID))) {
            $data = file_get_contents(static::getStatusFilepath($jobID));
            $jsonData = json_decode($data, true);

            if(gettype($jsonData) === 'array') {
                return $jsonData;
            }
        }

        return $data;
    }


    //*********** Initiation Events ************//

    /**
     * Event: JobWorker has started.
     * 
     * @param string $jobID
     * @param array $output
     * @return array
     */
    public static function jobStarted($jobID, $output) {
        $data = [
            'success'       => $output['success'],
            'status'        => 'Job Started',
            'progress'      => 1,
            'messages'      => $output['messages'],
        ];

        return static::generateOutput($jobID, $data);
    }

    //*********** Text Cache Events ************//

    /**
     * Event: Text cache directory initiated.
     * 
     * @param string $jobID
     * @param array $output
     * @return array
     */
    public static function textCacheInitiated($jobID, $output) {
        $status = 'Cache directory '.($output['success'] ? 'successfully' : 'could not be').'created.';

        $data = [
            'success'       => $output['success'],
            'status'        => $status,
            'progress'      => 2,
            'cache_dir'     => $output['cache_dir'],
            'messages'      => $output['messages'],
        ];

        return static::generateOutput($jobID, $data);
    }

    /**
     * Event: Text cache file created.
     * 
     * @param string $jobID
     * @param array $output
     * @return array
     */
    public static function textCacheFileCreated($jobID, $output) {
        $status = 'Cache file '.$output['file_num'].($output['success'] ? 'successfully' : 'could not be').'created.';

        $data = [
            'success'       => $output['success'],
            'status'        => $status,
            'progress'      => 0,
            'file_path'     => null,
            'file_name'     => null,
            'messages'      => $output['messages'],
        ];

        return static::generateOutput($jobID, $data);
    }

    /**
     * Event: All text cache files created.
     * 
     * @param string $jobID
     * @param array $output
     * @return array
     */
    public static function textCacheCompleted($jobID, $output) {
        $status = '';

        $data = [
            'success'       => $output['success'],
            'status'        => $status,
            'progress'      => 0,
            'file_path'     => null,
            'file_name'     => null,
            'messages'      => [],
        ];

        return static::generateOutput($jobID, $data);
    }


    //*********** Audio Cache Events ***********//

    /**
     * Event: // todo
     * 
     * @param string $jobID
     * @param bool $success
     * @return array
     */
    public static function audioCacheInitiated($jobID, $success = true) {
        // todo
        // update status after initiating the audio cache directory
        $data = [
            'success'       => $success,
            'status'        => '',
            'progress'      => 0,
            'file_path'     => null,
            'file_name'     => null,
            'messages'      => [],
        ];

        return static::generateOutput($jobID, $data);
    }

    /**
     * Event: Request sent to polly.
     *
     * @param string $jobID
     * @param array $output
     * @return array
     */
    public static function pollyRequestSent($jobID, $output) {
        // todo?
        $data = [
            'success'       => $success,
            'status'        => '',
            'progress'      => 0,
            'file_path'     => null,
            'file_name'     => null,
            'messages'      => [],
        ];

        return static::generateOutput($jobID, $data);
    }

    /**
     * Event: // todo
     * 
     * @param string $jobID
     * @param int $fileNumber
     * @param bool $success
     * @return array
     */
    public static function audioCacheFileCreated($jobID, $fileNumber, $success = true) {
        // todo
        // update status after each audio cache file is created
        $data = [
            'success'       => $success,
            'status'        => '',
            'progress'      => 0,
            'file_path'     => null,
            'file_name'     => null,
            'messages'      => [],
        ];

        return static::generateOutput($jobID, $data);
    }

    /**
     * Event: // todo
     *
     * @param string $jobID
     * @param bool $success
     * @return array
     */
    public static function audioCacheCompleted($jobID, $success = true) {
        // todo
        // update status after all the audio cache files have been created
        $data = [
            'success'       => $success,
            'status'        => '',
            'progress'      => 0,
            'file_path'     => null,
            'file_name'     => null,
            'messages'      => [],
        ];

        return static::generateOutput($jobID, $data);
    }


    //*********** Audio Stitcher Events ********//

    /**
     * Event: // todo
     *
     * @param string $jobID
     * @param bool $success
     * @return array
     */
    public static function audioSticherInitiated($jobID, $success = true) {
        // todo
        // update status after initiating the audio stitcher
        $data = [
            'success'       => $success,
            'status'        => '',
            'progress'      => 0,
            'file_path'     => null,
            'file_name'     => null,
            'messages'      => [],
        ];

        return static::generateOutput($jobID, $data);
    }

    /**
     * Event: // todo
     *
     * @param string $jobID
     * @param $fileNumber
     * @param bool $success
     * @return array
     */
    public static function audioFileStiched($jobID, $fileNumber, $success = true) {
        // todo?
        // update status after an audio file has been stitched?
        // this might just be a shell command so we may not have a status
        $data = [
            'success'       => $success,
            'status'        => '',
            'progress'      => 0,
            'file_path'     => null,
            'file_name'     => null,
            'messages'      => [],
        ];

        return static::generateOutput($jobID, $data);
    }

    /**
     * Event: // todo
     *
     * @param $jobID
     * @param bool $success
     * @return array
     */
    public static function audioSticherCompleted($jobID, $success = true) {
        // todo
        // update status after all the audio cache files are stiched together
        // (and the output file is created?)
        // maybe audioOutputFileCreated() {} ?
        $data = [
            'success'       => $success,
            'status'        => '',
            'progress'      => 0,
            'file_path'     => null,
            'file_name'     => null,
            'messages'      => [],
        ];

        return static::generateOutput($jobID, $data);
    }


    //*********** Finalization Events **********//

    /**
     * Event: JobWorker has completed.
     *
     * @param string $jobID
     * @param array $output
     * @return array
     */
    public static function jobCompleted($jobID, $output) {
        $status = 'Job '.(!!$output['success'] ? 'completed successfully.': 'failed.' );

        $data = [
            'success'       => $output['success'],
            'status'        => '',
            'progress'      => 100,
            'file_path'     => null,
            'file_name'     => null,
            'messages'      => $output['messages'],
        ];

        return static::generateOutput($jobID, $data);
    }


    //*********** Internal Functions ***********//

    /**
     * Run this at the end of every function above to write and output job status.
     *
     * @param string $jobID
     * @param $statusData
     * @param bool|null $writeFile
     * @return array
     */
    private static function generateOutput($jobID, $statusData, $writeFile = null) {
        if($writeFile === null) { $writeFile = !!static::AUTO_WRITE_FILE; }

        $statusData = static::returnStatus($statusData);

        if($writeFile) { static::writeStatusFile($jobID, $statusData); }

        return $statusData;
    }

    /**
     * Accept parameters and return in consistent format.
     *
     * @param $statusData
     * @return array
     */
    private static function returnStatus($statusData) {
        $output = [
            'success'       => false,
            'status'        => '',
            'progress'      => 0,
            'data'          => [],
            'messages'      => [],
        ];
        
        foreach($output as $key => $value) {
            try {
                $output[$key] = $statusData[$key];
            } catch(\Exception $e) {
                $output[$key] = $value;
            }    
        }
        
        return $output;
    }

    /**
     * Write the status to the output file in json format.
     *
     * @param string $jobID
     * @param $statusData
     * @return array
     */
    private static function writeStatusFile($jobID, $statusData) {
        $output = [
            'success'   => false,
            'messsages' => [],
        ];

        try {
            $fh = fopen(static::getStatusFilepath($jobID), 'w');
            $writes = fwrite($fh, json_encode($statusData, JSON_PRETTY_PRINT));
            fclose($fh);
        } catch (\Exception $e) {
            $output['success'] = false;
            $output['messsages'][] = "Could not write to job status file: ". $e->getMessage();
        }

        if($writes) {
            $output['success'] = true;
            $output['messsages'][] = "Data file updated successfully.";
        } else {
            $output['success'] = true;
            $output['messsages'][] = "Data could not be written to the file.";
        }

        return $output;
    }

    /**
     * Get the full path of the file to output to.
     *
     * @param string $jobID
     * @return string
     */
    private static function getStatusFilepath($jobID) {
        $basePath = DOC_ROOT.env('CACHE_PATH_STATUS', 'cache/status/');

        return $basePath.$jobID.'.json';
    }
    

}
