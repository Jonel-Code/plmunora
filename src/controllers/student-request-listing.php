<?php
require_once __DIR__ . '/../models/resource/tbl-request-detail.php';

return function($app){
    $app->get('/api/student/request-list', function ($request, $response, $args) {  
        $id = $request->getParam('studentId');
        if($id==NULL){
            return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(401)
            ->write(json_encode([
                'error'=>'fill the required fields'
            ]));            
        }

        $res = SqlTable\TblRequestDetails::student_req_listing($id);       
        if($res==null){
            return $response
            ->withHeader('Content-type', 'application/json')
            ->write(json_encode([])); 
        }
        foreach ($res as $key => $value) {
            $rv[] = [
                'requestId'=>$value['req_id'],
                'dateOfRequest'=>$value['date_of_request'],
                'titles'=>$value['titles'],
                'description'=>$value['description'],
                'total'=>$value['total'],
                'hash_key'=>$value['hash_key'],
                'registrarAccId'=>$value['registrar_acc_id'],
                'treasuryAccId'=>$value['treasury_acc_id'],
            ];
        }
        return $response
            ->withHeader('Content-type', 'application/json')
            ->write(json_encode($rv));
    });
};