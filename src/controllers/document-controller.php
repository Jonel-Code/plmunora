<?php

require_once __DIR__ . '/../models/resource/tbl-document.php';

function fetchDocumentListing($response)
{
    $rv = SqlTable\TblDocument::get_all_docs();
    return $response
        ->withHeader('Content-type', 'application/json')
        ->write(json_encode($rv));
}

return function ($app) {
    $app->post('/admin/create-document', function ($request, $response, $args) {
        $title = $request->getParam('title');
        $description = $request->getParam('description');
        $price = $request->getParam('price');
        try {
            SqlTable\TblDocument::new_docs($title, $description, $price);
            return fetchDocumentListing($response);
        } catch (\Throwable $th) {
            return $response->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode([
                    'response' => 'error in Adding Document'
                ]));
        }
    });

    $app->post('/admin/create-document/update', function ($request, $response, $args) {
        $document_id = $request->getParam('documentId');
        $title = $request->getParam('title');
        $description = $request->getParam('description');
        $price = $request->getParam('price');
        try {
            SqlTable\TblDocument::update($document_id, $title, $description, $price);
            return fetchDocumentListing($response);
        } catch (\Throwable $th) {
            return $response->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode([
                    'response' => 'error in Adding Document'
                ]));
        }
    });

    $app->post('/admin/create-document/delete', function ($request, $response, $args) {
        $document_id = $request->getParam('documentId');
        try {
            SqlTable\TblDocument::delete($document_id);
            return fetchDocumentListing($response);
        } catch (\Throwable $th) {
            return $response->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode([
                    'response' => 'error in Adding Document'
                ]));
        }
    });
};
