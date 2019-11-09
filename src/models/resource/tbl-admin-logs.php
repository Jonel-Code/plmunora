<?php

namespace SqlTable;

require_once __DIR__ . '/table.php';

class TblAdminLogs extends SqlTable
{
    public static $table_name = 'tbladminlogs';
    public $id;
    public $adminid;
    public $recordedtime;
    public $type; // 1 for login and 2 for logout
    function __construct(int $adminid, int $type)
    {
        $this->adminid = $adminid;
        $this->type = $type;
    }
    public static function create_table()
    { }

    public function saveLog()
    {
        $conn = self::get_connection();
        $tname = self::$table_name;
        $sql = "
            INSERT into $tname(adminid, type)
            values(?,?);
        ";
        $conn->prepare($sql)->execute([$this->adminid, $this->type]);
    }
}
