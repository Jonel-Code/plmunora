<?php

require_once __DIR__ . '/../models/resource/tbl-request-detail.php';
require_once __DIR__ . '/../models/resource/tbl-student.php';

return function($app, $temp_folder){
    $container = $app->getContainer();

    $app->get('/request-form-get', function ($request, $response, $args) use ($container, $temp_folder) {        
        
        $generate_pdf = require '../src/generators/jasper-reports/generate-pdf.php';
        $hash_key = $request->getParam('hash_key');
        $stud_acc_id = $request->getParam('stud_acc_id');

        $req = SqlTable\TblRequestDetails::get_request_details($stud_acc_id,$hash_key);
        if(!$req){
            return $response->withStatus(404)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode([
                        'message'=>'not found'
                    ]));
        }

        $req_id = $req[0]['req_id'];

        $output_folder =  $temp_folder . '\\' . $hash_key;
        if (!file_exists($output_folder)) {
            mkdir($output_folder, 0777, true);
        }

        $pdf_out = '';
        $jasper_file = 'request-form';
        $pdf_out = $generate_pdf($output_folder, $jasper_file, ['req_id'=>$req_id]);

        $basePath = $request->getUri()->getBaseUrl();
        $z = explode('\\',$temp_folder);
        $base_temp = end($z);
        $pdf_url = "$basePath/$base_temp/$hash_key/$jasper_file.pdf";  
        return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode([
                        'pdf_link'=> $pdf_url
                    ]));
    });
};