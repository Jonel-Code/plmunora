<?php

use SqlTable\TblAdmin;
use SqlTable\TblAdminLogs;

require_once __DIR__ . '/../models/resource/tbl-admin.php';
require_once __DIR__ . '/../models/resource/tbl-admin-logs.php';

return function ($app) {
    $app->get('/admin-login', function ($request, $response, $args) {
        $un = $request->getParam('username');
        $ps = $request->getParam('password');
        if ($un == NULL || $ps == NULL) {
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(401)
                ->write(json_encode([
                    'message' => 'fill the required fields'
                ]));
        }
        $rv = TblAdmin::get_admin($un, $ps);
        if (!$rv) {
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404)
                ->write(json_encode([
                    'message' => 'not found'
                ]));
        }

        $log = new TblAdminLogs($rv[0]['acc_id'], 1);
        $log->saveLog();

        return $response
            ->withHeader('Content-type', 'application/json')
            ->write(json_encode($rv[0]));
    });

    $app->post('/admin-logout', function ($request, $response, $args) {
        $adminId = $request->getParam('adminId');
        if ($adminId == NULL) {
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(401)
                ->write(json_encode([
                    'message' => 'fill the required fields'
                ]));
        }

        $log = new TblAdminLogs((int) $adminId, 2);
        $log->saveLog();

        return $response
            ->withHeader('Content-type', 'application/json')
            ->write(json_encode([
                'message' => 'admin logout'
            ]));
    });
};
