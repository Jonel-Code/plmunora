<?php
require_once __DIR__ . '/../models/resource/tbl-student.php';

return function ($app) {
    $app->post('/register-student', function ($request, $response, $args) {
        $un = $request->getParam('name');
        $sid = $request->getParam('sid');
        $ps = $request->getParam('password');
        if ($un == NULL || $ps == NULL || $sid == NULL) {
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(401)
                ->write(json_encode([
                    'error' => 'fill the required fields'
                ]));
        }
        try {
            SqlTable\TblStudent::new_student($un, $sid, $ps);
            return $response
                ->withHeader('Content-type', 'application/json')
                ->write(json_encode([
                    'message' => 'created'
                ]));
        } catch (PDOException $e) {
            echo $e->getMessage();
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(409)
                ->write(json_encode([
                    'error' => 'duplicate'
                ]));
        }
    });
};
