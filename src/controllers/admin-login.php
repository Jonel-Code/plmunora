<?php
require_once __DIR__ . '/../models/resource/tbl-admin.php';
return function($app){
    $app->get('/admin-login', function ($request, $response, $args) {  
        $un = $request->getParam('username');
        $ps = $request->getParam('password');
        if($un==NULL || $ps==NULL){
            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(401)
            ->write(json_encode([
                'message'=>'fill the required fields'
            ]));            
        }
        $rv = SqlTable\TblAdmin::get_admin($un, $ps);
        if(!$rv){
            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(404)
            ->write(json_encode([
                'message'=>'not found'
            ]));            
        }
        return $response
            ->withHeader('Content-type', 'application/json')
            ->write(json_encode($rv[0]));
    });
};