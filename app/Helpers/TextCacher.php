<?php
namespace App\Helpers;

class TextCacher
{
    //protected $jobID;
    //protected $fileCounter;

    //public function __construct($jobID) {
    //    $this->jobID = $jobID;
    //    $this->fileCounter = 0;
    //    app()->jobStatus->textCacherInitiated();
    //    return $this;
    //}

    /**
     * Split up request text and create text cache files.
     *
     * @param $jobID
     * @param $text
     * @return array
     */
    public static function cacheTextFiles($jobID, $text) {
        $output = [
            'success'   => false,
            'files'     => [],
            'messages'  => [],
        ];

        $initiateData = static::initiateJobCache($jobID);

        if(!$initiateData['success']) {
            $output['messages'] = $initiateData['messages'];

            return $output;
        }

        // Clean the input text before processing
        $text = TextToSpeech::cleanString($text);

        $maxChars = TextToSpeech::getMaxCharacters();
        $maxLoops = 10000;
        $fileNum  = 1;

        while(strlen($text) > 0 || $fileNum < $maxLoops) {
            // Make sure we're not trying to get more characters than there are available
            if(strlen($text) < $maxChars) {
                $maxChars = strlen($text);
            }

            // load characters into the text buffer
            $textBuffer = substr($text, 0, $maxChars);

            // Remove characters after the last space character
            // todo: try/catch?
            // Thanks https://stackoverflow.com/a/1530902
            //$parts = preg_split("/\s+(?=\S*+$)/", $textBuffer);
            $parts = preg_split("/.+(?=\S*+$)/", $textBuffer);
            $textBuffer = array_shift($parts);

            // remove count($textBuffer) characters from text input string
            // todo make sure $text is at least this long?
            $text = substr($text, strlen($textBuffer));

            // Write the text buffer to a file.
            // todo: break loop on failure and get message
            $writeFileData = static::writeCacheFile($jobID, $initiateData['cache_dir'], $fileNum, $textBuffer);

            $output['files'][$fileNum] = $writeFileData['file_path'];

            $fileNum++;
        }

        $output['success'] = true;

        JobStatusWriter::textCacheCompleted($jobID, $output);
        return $output;
    }

    // todo: just temporary. should probably be more robust.
    public static function getCacheFileContents($jobID, $fileNum) {
        $filepath = static::getCacheDirPath($jobID).$fileNum.'.txt';

        return file_get_contents($filepath);
    }

    public static function clearJobCache($jobID) {
        // todo
    }

    /**
     * Initiate this job's text cache directory.
     *
     * @param $jobID
     * @return array
     */
    private static function initiateJobCache($jobID) {
        $output = [
            'success'   => false,
            'cache_dir' => null,
            'messages'  => [],
        ];

        $output['cache_dir'] = $cacheDirPath = static::getCacheDirPath($jobID);
        $output['success'] = mkdir($cacheDirPath);

        JobStatusWriter::textCacheInitiated($jobID, $output);
        return $output;
    }

    private static function writeCacheFile($jobID, $basePath, $fileNum, $text) {
        $output = [
            'success'   => false,
            'file_path' => null,
            'file_num'  => $fileNum,
            'messages'  => [],
        ];

        $output['file_path'] = $filePath = $basePath.$fileNum.'.txt';
        $output['success'] = file_put_contents($filePath, $text);

        JobStatusWriter::textCacheFileCreated($jobID, $output);
        return $output;
    }

    private static function getCacheDirPath($jobID) {
        $basePath = DOC_ROOT.env('CACHE_PATH_TEXT', 'cache/text/');

        return $basePath.$jobID;
    }



}