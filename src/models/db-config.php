<?php

return function($config = ''){
    // $config = $app->configMode();
    if($config == 'production'){
        return [
            'driver' => 'mysql', //mysql, ....
            'username' => 'DB_USERNAME',
            'password' => 'DB_PASSWORD',
            'host' => 'DB_HOST',
            'database' => 'DB_DATABASE',
            'port' => '5432'
        ];
    }
    return [
        'driver' => 'mysql',
        'username' => 'root',
        'password' => 'dev',
        'host' => 'localhost',
        'database' => 'plmunora',
        'port' => '3306'
    ];
};