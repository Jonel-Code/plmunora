<?php
// use require to import the connection
require_once __DIR__ . '/../../public/AppInstance.php';

return function(){
    $app = AppInstance\AppInstance::get_app_instance();
    
    $config = $app->getContainer()->get('mode');
    
    $db_config = require 'db-config.php';
    $_DB_CONFIG = $db_config($config);
    
    $driver     = $_DB_CONFIG['driver'];
    $host       = $_DB_CONFIG['host'];
    $database   = $_DB_CONFIG['database'];
    $port       = $_DB_CONFIG['port'];
    $username   = $_DB_CONFIG['username'];
    $password   = $_DB_CONFIG['password'];
    
    try {
        $conn = new PDO("$driver:host=$host:$port;dbname=$database", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // echo "Connected successfully"; 
        return $conn;
    } catch(PDOException $e) {
        // echo "Connection failed: " . $e->getMessage();
        return NULL;
    }
};
