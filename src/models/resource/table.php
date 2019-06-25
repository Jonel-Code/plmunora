<?php

namespace SqlTable;

abstract class SqlTable{
    abstract static function create_table();
    protected static function get_connection(){
        $conn = require __DIR__ . '/../mysql-connection.php';
        return $conn();
    }    
    protected static function exec($sql = ''){
        $conn = self::get_connection();   
        $conn->exec($sql);     
    }
}
