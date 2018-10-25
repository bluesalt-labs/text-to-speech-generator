<?php

namespace App;

class Response
{
    const VIEW_EXT = '.php';
    const VIEW_404 = '404';
    const VIEW_500 = '500';

    protected $headers;
    protected $body;
    protected $data;
    protected $responseMessages;

    public function __construct() {

    }

    public function __toString() {
        if(array_key_exists('Content-Type', $this->headers) ){
            if($this->headers['Content-Type'] === 'application/json') {
                return $this->toJson();
            }
            else if($this->headers['Content-Type'] === 'text/html') {
                return $this->toHtml();
            }
        }

        return $this->toHtml();
    }

    public function loadView($filepath) {
        $filepath = ltrim($filepath, '/');
        $filepath = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filepath);
        $filepath = VIEW_ROOT.$filepath.'.php';

        try {
            ob_start();

            if(is_file($filepath)) {
                include $filepath;
            } else {
                return $this->return404();
            }

        } catch(\Error $e) {
            ob_end_clean();
            return $this->return500($e->getMessage());
        }

        $this->body = ob_end_flush();
    }

    public function addMessage($message) {
        $this->responseMessages[] = $message;
    }

    public function getMessages() {
        return $this->responseMessages;
    }

    public function toJson() {
        $this->setHeader('Content-Type', 'application/json');

        exit( json_encode($this->data) );
    }

    public function toHtml() {
        $this->setHeader('Content-Type', 'text/html');

        return strval($this->body);
    }

    public function data($data = []) {
        if($data !== []) {
            $this->data = $data;
        }

        return $this->data;
    }

    public function setHeader($key, $value) {
        $this->headers[$key] = $value;
    }

    public function outputHeaders() {
        foreach($this->headers as $header) {
            header($header);
        }
    }

    public function return404($message = null) {
        $this->addMessage($message);
        $this->loadView('404');
        return $this;
    }

    public function return500($message = null) {
        $this->addMessage($message);
        $this->loadView('500');
        return $this;
    }



}