<?php

require_once __DIR__ . '/../models/resource/tbl-request-detail.php';
require_once __DIR__ . '/../models/resource/tbl-student.php';

return function($app, $temp_folder){
    $container = $app->getContainer();

    $app->post('/request-form-submit', function ($request, $response, $args) use ($container, $temp_folder) {        
        // Sample log message        
        $generate_pdf = require '../src/generators/jasper-reports/generate-pdf.php';
        $doc_ids = $request->getParam('doc_ids');
        $name = $request->getParam('name');
        $sid = $request->getParam('sid');

        $req_docs = json_decode($doc_ids);
        if(!is_array($req_docs)){
            return $response->withStatus(500)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode([
                        'message'=>'error in mapping of request documents'
                    ]));
        }
        $stud = SqlTable\TblStudent::get_student($name, $sid);
        if(!$stud){
            return $response->withStatus(404)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode([
                        'message'=>'student not found'
                    ]));
        }
        $curr_stud = $stud[0];
        $req = SqlTable\TblRequestDetails::new_request($curr_stud['acc_id'],$req_docs);
        if(!$req){
            return $response->withStatus(405)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode([
                        'message'=>'Error in saving requests'
                    ]));
        }

        $req_hash_key = $req['hash_key'];

        $output_folder =  $temp_folder . '\\' . $req_hash_key;
        if (!file_exists($output_folder)) {
            mkdir($output_folder, 0777, true);
        }
        $pdf_out = '';
        $jasper_file = 'request-form';
        $pdf_out = $generate_pdf($output_folder, $jasper_file, ['req_id'=>$req['req_id']]);

        $basePath = $request->getUri()->getBaseUrl();
        $z = explode('\\',$temp_folder);
        $base_temp = end($z);
        $pdf_url = "$basePath/$base_temp/$req_hash_key/$jasper_file.pdf";
        // $container->get('logger')->info("Generate PDF route $pdf_url");      
          
        // return $container->get('renderer')->render($response, 'pdf-render.phtml', []);        
        return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode([
                        'pdf_link'=> $pdf_url
                    ]));
    });
};