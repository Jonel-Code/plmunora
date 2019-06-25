<?php

require __DIR__ . '/../../../vendor/autoload.php';
use PHPJasper\PHPJasper;

return function($folder_name, $jasper_file, $params=[]){    

    $input = __DIR__ . "\\jasper-files\\$jasper_file.jasper";  
    $output = $folder_name;    
    $db_conf = require __DIR__ . '/../../models/db-config.php';
    $options = [ 
        'format' => ['pdf'],
    ];
    $options['db_connection'] = $db_conf();
    $options['params'] = $params;

    $jasper = new PHPJasper;

    // $out = $jasper->process(
    //     $input,
    //     $output,
    //     $options
    // )->output();
    // echo $out;

    $jasper->process(
        $input,
        $output,
        $options
    )->execute();
    return $output . "\\$jasper_file.pdf";
};