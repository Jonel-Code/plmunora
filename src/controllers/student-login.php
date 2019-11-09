<?php
require_once __DIR__ . '/../models/resource/tbl-student.php';

return function ($app) {
    $app->get('/student-login', function ($request, $response, $args) {
        $un = $request->getParam('username');
        $sid = $request->getParam('sid');
        $ps = $request->getParam('password');
        if ($un == NULL || $ps == NULL  || $sid == NULL) {
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(401)
                ->write(json_encode([
                    'error' => 'fill the required fields'
                ]));
        }

        $res = SqlTable\TblStudent::get_student($un, $sid);
        if (!$res) {
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404)
                ->write(json_encode([
                    'error' => 'not found'
                ]));
        }

        $res_1 = $res[0];
        $verify = password_verify($ps, $res_1['password']);
        if (!$verify) {
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404)
                ->write(json_encode([
                    'error' => 'not found'
                ]));
        }
        $rv = [
            'name' => $res_1['name'],
            'id' => $res_1['sid'],
            'acc_id' => $res_1['acc_id'],
        ];
        return $response
            ->withHeader('Content-type', 'application/json')
            ->write(json_encode($rv));
    });
};
