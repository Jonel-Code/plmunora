<?php
require_once __DIR__ . '/../models/resource/tbl-document.php';

return function ($app) {
    $app->get('/document-list', function ($request, $response, $args) {
        $rv = SqlTable\TblDocument::get_all_docs();
        return $response
            ->withHeader('Content-type', 'application/json')
            ->write(json_encode($rv));
    });
};
