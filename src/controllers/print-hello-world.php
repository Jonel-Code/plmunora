<?php

return function($app, $temp_folder){
    $container = $app->getContainer();

    $app->get('/pdf-hello-world', function ($request, $response, $args) use ($container, $temp_folder) {
        // Sample log message        
        $generate_pdf = require '../src/generators/jasper-reports/generate-pdf.php';
        $req_id = $request->getParam('id');
        // check wheather the parameters is
        if(!isset($req_id)){
            return $response->withStatus(404)
                            ->withHeader('Content-Type', 'text/html')
                            ->write('Page not found');
        }
        $output_folder =  $temp_folder . '\\' . $req_id;
        if (!file_exists($output_folder)) {
            mkdir($output_folder, 0777, true);
        }
        $pdf_out = '';
        // $jasper_file = 'hl';
        // $pdf_out = $generate_pdf($output_folder, $jasper_file);
        $jasper_file = 'request-form';
        $pdf_out = $generate_pdf($output_folder, $jasper_file, ['req_id'=>6]);

        $basePath = $request->getUri()->getBaseUrl();
        $z = explode('\\',$temp_folder);
        $base_temp = end($z);
        $pdf_url = "$basePath/$base_temp/$req_id/$jasper_file.pdf";
        $container->get('logger')->info("Generate PDF route $pdf_url");      
          
        return $container->get('renderer')->render($response, 'pdf-render.phtml', ['pdf_path'=> $pdf_url]);        
    });
};