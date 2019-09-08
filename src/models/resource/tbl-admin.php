<?php

namespace SqlTable;

require_once __DIR__ . '/table.php';

class TblAdmin extends SqlTable
{
    public static $table_name = 'tblAdmin';
    public $acc_id;
    public $name;
    public $eid;
    public $account_type; //admin or employee
    public $email;
    public $office; // treasury or registrar
    function __construct($name, $eid, $account_type, $email, $office)
    {
        $this->name = $name;
        $this->eid = $eid;
        $this->account_type = $account_type;
        $this->email = $email;
        $this->office = $office;
    }
    public static function create_table()
    {
        $tname = self::$table_name;
        try {
            $conn = self::get_connection();
            $sql = "CREATE table if not exists $tname(
                acc_id          INT( 11 )       AUTO_INCREMENT PRIMARY KEY,
                name            VARCHAR( 50 )   NOT NULL,         
                eid             VARCHAR( 50 )   NOT NULL UNIQUE,
                account_type    VARCHAR( 50 )   NOT NULL,
                email           VARCHAR( 50 )   NOT NULL UNIQUE,
                office          VARCHAR( 50 )   NOT NULL
                );";
            $conn->exec($sql);
        } catch (PDOException $e) {
            // echo $e->getMessage();
        }
    }

    public static function new_admin($name, $eid, $account_type, $email, $office)
    {
        $conn = self::get_connection();
        $tname = self::$table_name;
        $sql = "
            INSERT into $tname(name,eid, account_type,email,office)
            values(?,?,?,?,?);
        ";
        $conn->prepare($sql)->execute([trim($name), trim($eid), trim($account_type), trim($email), trim($office)]);
    }

    public static function get_admin($name, $sid)
    {
        $conn = self::get_connection();
        $tname = self::$table_name;
        $sql = "
            select * from $tname
            where name=? and eid=?
            ;
        ";
        $ex = $conn->prepare($sql);
        $ex->execute([trim($name), trim($sid)]);
        return $ex->fetchAll(\PDO::FETCH_ASSOC);
    }
}

TblAdmin::create_table();
// var_dump(TblAdmin::get_admin('admin', 1611));
// TblAdmin::new_admin('admin',1611,'admin','mail@webmaster.com','registrar');
