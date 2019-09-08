<?php

require_once __DIR__ . '/../models/resource/tbl-admin.php';

return function ($app) {
    $app->post('/admin/new-admin', function ($request, $response, $args) {
        $name = $request->getParam('name');
        $employee_id = $request->getParam('employeeId');
        $employee_email = $request->getParam('employeeEmail');
        $office = $request->getParam('office');
        SqlTable\TblAdmin::new_admin($name, $employee_id, 'common', $employee_email, $office);
        try {
            return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode([
                    'response' => 'account created'
                ]));
        } catch (\Throwable $th) {
            return $response->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode([
                    'response' => 'error in Adding Document'
                ]));
        }
    });
};
