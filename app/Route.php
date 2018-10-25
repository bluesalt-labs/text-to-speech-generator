<?php

namespace App;

class Route
{
    public $uri;
    public $method;


    public function __construct($uri, $method) {
        $this->uri      = $this->parseUri($uri);
        $this->method   = $this->parseMethod($method);
    }


    private function parseUri($uri) {
        // todo
        return $uri;
    }

    private function parseMethod($method) {
        // todo
        return $method;
    }

    public function getResponseMethod() {
        // todo
        return $this->method;
    }




    //public static function defineRoutes(Router $router) {
    //    // ???
    //}

    //private function request($type, $urn, $method, $data = [], $headers = []) {
    //    $this->routes[$urn] = [
    //        'type'      => $type,
    //        'method'    => $method,
    //        'data'      => $data,
    //        'headers'   => $headers
    //    ];
//
    //    // debug
    //    return $this->routes;
    //}
//
    //public function get($urn, $method, $data = [], $headers = []) {
    //    return $this->request('GET', $urn, $method, $data, $headers);
    //}
//
    //public function post($urn, $method, $data = [], $headers = []) {
    //    return $this->request('POST', $urn, $method, $data, $headers);
    //}

}
