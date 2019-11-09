<?php

require_once __DIR__ . '/../models/resource/tbl-admin.php';

return function ($app) {
    $app->post('/admin/new-admin', function ($request, $response, $args) {
        $name = $request->getParam('name');
        $employee_id = $request->getParam('employeeId');
        $employee_email = $request->getParam('employeeEmail');
        $office = $request->getParam('office');
        try {
            SqlTable\TblAdmin::new_admin($name, $employee_id, 'common', $employee_email, $office);
            return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode([
                    'response' => 'account created'
                ]));
        } catch (\Throwable $th) {
            return $response->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode([
                    'response' => 'error in creating admin'
                ]));
        }
    });

    $app->get('/admin/request-listing', function ($request, $response, $args) {
        $res = SqlTable\TblRequestDetails::get_all_request();
        if ($res == null) {
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404)
                ->write(json_encode([]));
        }
        foreach ($res as $key => $value) {
            $rv[] = [
                'requestId' => $value['req_id'],
                'dateOfRequest' => $value['date_of_request'],
                'titles' => $value['titles'],
                'description' => $value['description'],
                'total' => $value['total'],
                'hash_key' => $value['hash_key'],
                'registrarAccId' => $value['registrar_acc_id'],
                'treasuryAccId' => $value['treasury_acc_id'],
                'studentId' => $value['sid'],
                'date_payed' => $value['date_payed'],
                'or_number' => $value['or_number']
            ];
        }
        return $response
            ->withHeader('Content-type', 'application/json')
            ->write(json_encode($rv));
    });

    $app->post('/admin/approve-request', function ($request, $response, $args) {
        $name = $request->getParam('name');
        $employee_id = $request->getParam('employeeId');
        $request_id = $request->getParam('requestId');
        $or_number = $request->getParam('orNumber');
        try {
            SqlTable\TblRequestDetails::approve($request_id, $name, $employee_id, $or_number);
            return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode([
                    'response' => 'Approve successful'
                ]));
        } catch (\Throwable $th) {
            return $response->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode([
                    'response' => 'error in approval'
                ]));
        }
    });

    $app->post('/admin/approve-request/remove', function ($request, $response, $args) {
        $name = $request->getParam('name');
        $employee_id = $request->getParam('employeeId');
        $request_id = $request->getParam('requestId');
        SqlTable\TblRequestDetails::un_approve($request_id, $name, $employee_id);
        try {

            return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode([
                    'response' => 'Approve successful'
                ]));
        } catch (\Throwable $th) {
            return $response->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode([
                    'response' => 'error in approval'
                ]));
        }
    });
};
