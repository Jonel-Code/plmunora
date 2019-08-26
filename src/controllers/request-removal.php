<?php
use SqlTable\TblRequestDetails;

require_once __DIR__ . '/../models/resource/tbl-request-detail.php';

return function($app){
    $app->post('/api/request/delete', function ($request, $response, $args) {  
        $un = $request->getParam('reqId');
        if($un==NULL ){
            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(401)
            ->write(json_encode([
                'error'=>'fill the required fields'
            ]));            
        }

        $message = SqlTable\TblRequestDetails::delete_request($un);
        return $response
                ->withHeader('Content-type', 'application/json')
                ->write(json_encode($message));        
        
    });
};