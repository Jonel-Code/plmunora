<?php

namespace SqlTable;

require_once __DIR__ . '/table.php';

class TblStudent extends SqlTable
{
    public static $table_name = 'tblStudent';
    public $acc_id;
    public $name;
    public $sid;
    public $password;
    function __construct($name, $sid, $acc_id = 0, $password = '123')
    {
        $this->name = $name;
        $this->sid = $sid;
        $this->acc_id = $acc_id;
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }


    public static function create_table()
    {
        $tname = self::$table_name;
        try {
            $sql = "CREATE table if not exists $tname(
                acc_id      INT( 11 )       AUTO_INCREMENT PRIMARY KEY,
                name        VARCHAR( 50 )   NOT NULL,         
                sid         VARCHAR( 50 )   NOT NULL UNIQUE,
                password    TEXT            NOT NULL
                );";
            self::exec($sql);
        } catch (PDOException $e) {
            // echo $e->getMessage();
        }
    }

    public static function new_student($name, $sid,  $password = '123')
    {
        $conn = self::get_connection();
        $tname = self::$table_name;
        $sql = "
            INSERT into $tname(name,sid, password)
            values(?,?,?);
        ";
        $conn->prepare($sql)->execute([trim($name), trim($sid), password_hash($password, PASSWORD_BCRYPT)]);
    }

    public static function get_student($name, $sid)
    {
        $conn = self::get_connection();
        $tname = self::$table_name;
        $sql = "
            select * from $tname
            where name=? and sid=?
            ;
        ";
        $ex = $conn->prepare($sql);
        $ex->execute([trim($name), trim($sid)]);
        return $ex->fetchAll(\PDO::FETCH_ASSOC);
    }
}

// TblStudent::create_table();
// TblStudent::new_student('jonel',16118081);
// var_dump(json_encode(TblStudent::get_student('jonel', 16118081)));
