<?php
require_once __DIR__ . '/../models/resource/tbl-student.php';

return function($app){
    $app->get('/student-login', function ($request, $response, $args) {  
        $un = $request->getParam('username');
        $ps = $request->getParam('password');
        if($un==NULL || $ps==NULL){
            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(401)
            ->write(json_encode([
                'error'=>'fill the required fields'
            ]));            
        }

        $res = SqlTable\TblStudent::get_student($un, $ps);
        if(!$res){
            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(404)
            ->write(json_encode([
                'error'=>'not found'
            ])); 
        }
        $res_1 = $res[0];
        $rv = [
            'name'=>$res_1['name'],
            'id'=>$res_1['sid'],
            'acc_id'=>$res_1['acc_id'],
        ];
        return $response
            ->withHeader('Content-type', 'application/json')
            ->write(json_encode($rv));
    });
};