<?php

return function($app){
    $app->get('/test-call', function ($request, $response, $args) {    
        return $response
            ->withHeader('Content-type', 'application/json')
            // ->withHeader('Access-Control-Allow-Origin', '*')
            ->write(json_encode([
                'message'=>'okay'
            ]));
    });
};