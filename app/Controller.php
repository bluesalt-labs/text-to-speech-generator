<?php

namespace App;

class Controller
{
    protected $app;
    protected $response;

    public function __construct() {
        $this->response = new Response();
    }

    public function get_index(Request $request) {
        $this->response->loadView('home');
        return $this->response;
    }

    public function get_404($message = null) {
        return $this->response->return404($message);
    }

    public function get_500($message = null) {
        return $this->response->return500($message);
    }

    public function post_SubmitAudioRequest(Request $request) {
        $output = [
            'success'       => false,
            'audio_path'    => null,
            'audio_name'    => null,
            'messages'      => [],
        ];

        $ttsResponse = null;

        try {
            $text       = $request->attributes('text');
            $voice      = $request->attributes('voice');

            $submitResponse = app()->submitJobRequest($text, $voice);
        } catch (\Error $e) {
            $output['messages'][] = $e->getMessage();
        }

        if($ttsResponse) {
            $output['success'] = $ttsResponse['success'];
            $output['audio_path'] = $ttsResponse['path'];
            $output['audio_name'] = $ttsResponse['name'];
        }

        $this->response->data($output);
        $this->response->toJson();
    }

    public function get_GetRequestStatus(Request $request) {
        $jobID = $request->attributes('job_id');

        $output = app()->getJobStatus($jobID);

        $this->response->data($output);
        $this->response->toJson();
    }

}