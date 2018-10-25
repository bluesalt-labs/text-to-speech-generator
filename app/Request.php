<?php

namespace App;

class Request
{

    protected $type;
    protected $headers;
    protected $uri;
    protected $data;

    public function __construct() {
        $this->data = [];

        $this->parseRequestType();
        $this->parseRequestHeaders();
        $this->parseRequestUri();
        $this->parseRequestData();

        return $this;
    }

    private function parseRequestType() {
        $this->type = strtoupper( $_SERVER['REQUEST_METHOD'] );
    }

    private function parseRequestHeaders() {
        $this->headers = getallheaders();
    }

    private function parseRequestUri() {
        $this->uri = explode('/', $_SERVER['REQUEST_URI']);
    }

    private function parseRequestData() {
        $requestData = [];

        try {
            // todo: this probably only works with JSON requests so... ¯\_(ツ)_/¯
            $requestData = json_decode( file_get_contents('php://input'), true);
        } catch (\Exception $e) {  }

        if(gettype($requestData) === 'array') {
            foreach($requestData as $key => $value) {
                $this->data[$key] = $value;
            }
        } else {
            if( $this->isGet() && (gettype($_GET) === 'array') ) {
                foreach($_GET as $value) {
                    $this->data[] = $value;
                }
            }

            if($this->isPost() && (gettype($_POST) === 'array') ) {
                foreach($_POST as $value) {
                    $this->data[] = $value;
                }
            }
        }
    }

    private function camelToSnake($string) {
        return preg_replace_callback(
            '/[A-Z]/',
            function($subject) { return '_'.strtolower($subject[0]); },
            $string
        );
    }

    private function kebabToCamel($string, $lowerCaseFirst = false) {
        $output = "";
        $parts = explode('-', strtolower($string) );

        foreach($parts as $part) {
            $output .= ucfirst($part);
        }

        return ( $lowerCaseFirst ? $output : ucfirst($output) );
    }

    public function isGet()  { return !!($this->type === 'GET');  }
    public function isPost() { return !!($this->type === 'POST'); }

    public function getControllerAction() {
        $action = strtolower($this->type).'_'.
            $this->kebabToCamel( end($this->uri) );

        return (end($this->uri) ? $action : 'get_index');
    }

    public function attributes($key = null) {

        if($key) {
            if(array_key_exists($key, $this->data)) { return $this->data[$key]; }
        } else { return null; }

        return $this->data;
    }

    public function __get($key) {
        // This doesn't seem to work :/
        return $this->attributes($key);
    }

    public function getType() {
        return $this->type;
    }

}