<?php

if (! function_exists('app')) {
    function app() {
        return $GLOBALS['app'];
    }
}

if (! function_exists('request')) {
    function request() {
        return app()->request;
    }
}

if (! function_exists('resource')) {
    function resource($file, $subDir = null) {
        // if no sub-directory is specified,
        // assume the subdirectory is the same as the file's extension (i.e. js, css, etc.)
        if(!$subDir) {
            $ext = end(explode(".", $file));
            $subDir = ($ext ? $ext.DIRECTORY_SEPARATOR : '');
        }

        // Return the resource file path
        return PUBLIC_ROOT.
            'resources'.DIRECTORY_SEPARATOR.
            $subDir.DIRECTORY_SEPARATOR.
            $file;
    }
}

if (! function_exists('css')) {
    function css($filename) {
        return resource($filename, 'css');
    }
}

if (! function_exists('js')) {
    function js($filename) {
        return resource($filename, 'js');
    }
}

//if (! function_exists('getResponseMessages')) {
//    function getResponseMessages() {
//        return app()->response->getMessages();
//    }
//}

